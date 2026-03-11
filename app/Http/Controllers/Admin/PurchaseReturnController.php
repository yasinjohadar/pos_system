<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:purchase-return-list')->only('index');
        $this->middleware('permission:purchase-return-create')->only(['create', 'store']);
        $this->middleware('permission:purchase-return-show')->only('show');
        $this->middleware('permission:purchase-return-complete')->only('complete');
    }

    public function index(Request $request)
    {
        $query = PurchaseReturn::with(['purchaseInvoice', 'warehouse', 'user'])
            ->orderByDesc('return_date')
            ->orderByDesc('id');

        if ($request->filled('return_number')) {
            $query->where('return_number', 'like', '%' . $request->input('return_number') . '%');
        }
        if ($request->filled('purchase_invoice_id')) {
            $query->where('purchase_invoice_id', $request->input('purchase_invoice_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $returns = $query->paginate(15)->withQueryString();

        return view('admin.pages.purchases.returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $purchaseInvoice = null;
        if ($request->filled('purchase_invoice_id')) {
            $purchaseInvoice = PurchaseInvoice::with(['items.product', 'warehouse', 'branch'])
                ->where('status', PurchaseInvoice::STATUS_CONFIRMED)
                ->findOrFail($request->input('purchase_invoice_id'));
        }

        $invoices = PurchaseInvoice::where('status', PurchaseInvoice::STATUS_CONFIRMED)
            ->with('branch')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $warehouses = \App\Models\Warehouse::where('is_active', true)->with('branch')->orderBy('name')->get();

        return view('admin.pages.purchases.returns.create', compact('purchaseInvoice', 'invoices', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.purchase_invoice_item_id' => 'nullable|exists:purchase_invoice_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = PurchaseInvoice::where('id', $validated['purchase_invoice_id'])
            ->where('status', PurchaseInvoice::STATUS_CONFIRMED)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $return = PurchaseReturn::create([
                'return_number' => PurchaseReturn::generateReturnNumber(),
                'purchase_invoice_id' => $invoice->id,
                'return_date' => $validated['return_date'],
                'warehouse_id' => $validated['warehouse_id'],
                'subtotal_refund' => 0,
                'tax_refund' => 0,
                'total_refund' => 0,
                'status' => PurchaseReturn::STATUS_PENDING,
                'user_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $subtotal = 0;
            foreach ($validated['items'] as $row) {
                $total = round((float) $row['quantity'] * (float) $row['unit_price'], 2);
                $return->items()->create([
                    'purchase_invoice_item_id' => $row['purchase_invoice_item_id'] ?? null,
                    'product_id' => $row['product_id'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $total,
                ]);
                $subtotal += $total;
            }

            $taxRate = (float) $invoice->tax_rate;
            $taxRefund = round($subtotal * $taxRate / 100, 2);
            $return->update([
                'subtotal_refund' => $subtotal,
                'tax_refund' => $taxRefund,
                'total_refund' => $subtotal + $taxRefund,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('admin.purchase-returns.show', $return)
            ->with('success', 'تم إنشاء المرتجع. يمكنك إكماله لصرف الكميات من المخزون.');
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['purchaseInvoice.branch', 'purchaseInvoice.supplier', 'warehouse', 'user', 'items.product']);
        return view('admin.pages.purchases.returns.show', compact('purchaseReturn'));
    }

    public function complete(PurchaseReturn $purchaseReturn)
    {
        if ($purchaseReturn->status !== PurchaseReturn::STATUS_PENDING) {
            return redirect()->route('admin.purchase-returns.show', $purchaseReturn)
                ->with('error', 'المرتجع مكتمل أو ملغى مسبقاً.');
        }

        try {
            $purchaseReturn->complete();
        } catch (\Throwable $e) {
            return redirect()->route('admin.purchase-returns.show', $purchaseReturn)
                ->with('error', 'فشل إكمال المرتجع: ' . $e->getMessage());
        }

        return redirect()->route('admin.purchase-returns.show', $purchaseReturn)
            ->with('success', 'تم إكمال المرتجع وصرف الكميات من المخزون.');
    }
}
