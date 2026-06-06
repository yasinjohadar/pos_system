<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:purchase-order-list')->only('index');
        $this->middleware('permission:purchase-order-create')->only(['create', 'store']);
        $this->middleware('permission:purchase-order-show')->only('show');
        $this->middleware('permission:purchase-order-convert')->only('convert');
    }

    public function index(Request $request)
    {
        $orders = PurchaseOrder::with(['supplier', 'branch'])
            ->orderByDesc('order_date')
            ->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.purchases.orders.partials.table-rows', compact('orders'))->render(),
                'pagination' => view('admin.pages.purchases.orders.partials.pagination', compact('orders'))->render(),
            ]);
        }

        return view('admin.pages.purchases.orders.index', compact('orders'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.purchases.orders.create', compact('branches', 'warehouses', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = PurchaseOrder::create([
                'number' => PurchaseOrder::generateNumber(),
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'branch_id' => $validated['branch_id'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'status' => PurchaseOrder::STATUS_DRAFT,
                'user_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => round((float) $item['quantity'] * (float) $item['unit_price'], 2),
                ]);
            }

            $order->recalculateTotals();
            return $order;
        });

        return redirect()->route('admin.purchase-orders.show', $order)->with('success', 'تم إنشاء أمر الشراء.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.product', 'supplier', 'branch', 'warehouse']);
        return view('admin.pages.purchases.orders.show', compact('purchaseOrder'));
    }

    public function convert(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === PurchaseOrder::STATUS_CONVERTED) {
            return back()->with('error', 'تم تحويل الأمر مسبقاً.');
        }

        $purchaseOrder->load('items');

        $invoice = DB::transaction(function () use ($purchaseOrder) {
            $invoice = PurchaseInvoice::create([
                'number' => PurchaseInvoice::generateNumber($purchaseOrder->branch_id),
                'invoice_date' => now()->toDateString(),
                'branch_id' => $purchaseOrder->branch_id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'subtotal' => $purchaseOrder->subtotal,
                'tax_rate' => $purchaseOrder->tax_rate,
                'tax_amount' => $purchaseOrder->tax_amount,
                'total' => $purchaseOrder->total,
                'payment_status' => PurchaseInvoice::PAYMENT_STATUS_PENDING,
                'status' => PurchaseInvoice::STATUS_DRAFT,
                'user_id' => auth()->id(),
                'notes' => 'من أمر شراء #' . $purchaseOrder->number,
            ]);

            foreach ($purchaseOrder->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ]);
            }

            $purchaseOrder->update([
                'status' => PurchaseOrder::STATUS_CONVERTED,
                'converted_invoice_id' => $invoice->id,
            ]);

            return $invoice;
        });

        return redirect()->route('admin.purchase-invoices.show', $invoice)
            ->with('success', 'تم تحويل أمر الشراء إلى فاتورة.');
    }
}
