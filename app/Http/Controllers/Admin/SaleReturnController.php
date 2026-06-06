<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:sale-return-list')->only('index');
        $this->middleware('permission:sale-return-create')->only(['create', 'store', 'searchInvoices', 'invoiceReturnData']);
        $this->middleware('permission:sale-return-show')->only('show');
        $this->middleware('permission:sale-return-complete')->only('complete');
    }

    public function index(Request $request)
    {
        $query = SaleReturn::with(['saleInvoice', 'warehouse', 'user'])
            ->orderByDesc('return_date')
            ->orderByDesc('id');

        if ($request->filled('return_number')) {
            $query->where('return_number', 'like', '%' . $request->input('return_number') . '%');
        }
        if ($request->filled('sale_invoice_id')) {
            $query->where('sale_invoice_id', $request->input('sale_invoice_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $returns = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.sales.returns.partials.table-rows', compact('returns'))->render(),
                'pagination' => view('admin.pages.sales.returns.partials.pagination', compact('returns'))->render(),
            ]);
        }

        return view('admin.pages.sales.returns.index', compact('returns'));
    }

    /**
     * إنشاء مرتجع من فاتورة معينة (يُستدعى عادة من صفحة الفاتورة).
     */
    public function create(Request $request)
    {
        $saleInvoice = null;
        if ($request->filled('sale_invoice_id')) {
            $saleInvoice = SaleInvoice::with(['items.product', 'warehouse', 'branch'])
                ->where('status', SaleInvoice::STATUS_CONFIRMED)
                ->findOrFail($request->input('sale_invoice_id'));
        }

        $warehouses = \App\Models\Warehouse::where('is_active', true)->with('branch')->orderBy('name')->get();

        return view('admin.pages.sales.returns.create', compact('saleInvoice', 'warehouses'));
    }

    /**
     * بحث الفواتير المؤكدة لنموذج المرتجع (Select2 AJAX).
     */
    public function searchInvoices(Request $request)
    {
        $search = trim((string) $request->input('search', $request->input('q', '')));

        $query = SaleInvoice::query()
            ->where('status', SaleInvoice::STATUS_CONFIRMED)
            ->with(['branch', 'customer'])
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }

        $invoices = $query->limit(25)->get();

        return response()->json([
            'results' => $invoices->map(fn ($inv) => [
                'id' => $inv->id,
                'text' => $inv->number
                    . ' — ' . ($inv->branch->name ?? '—')
                    . ' — ' . $inv->invoice_date->format('Y-m-d')
                    . ($inv->customer ? ' (' . $inv->customer->name . ')' : ''),
            ])->values(),
        ]);
    }

    /**
     * بيانات الفاتورة لملء بنود المرتجع (AJAX).
     */
    public function invoiceReturnData(SaleInvoice $saleInvoice)
    {
        if ($saleInvoice->status !== SaleInvoice::STATUS_CONFIRMED) {
            return response()->json(['message' => 'الفاتورة غير مؤكدة.'], 422);
        }

        $saleInvoice->load(['items.product']);

        return response()->json([
            'warehouse_id' => $saleInvoice->warehouse_id,
            'invoice_number' => $saleInvoice->number,
            'items' => $saleInvoice->items->map(fn ($item) => [
                'sale_invoice_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name ?? '—',
                'quantity_remaining' => (float) $item->quantity_remaining,
                'unit_price' => (float) $item->unit_price,
            ])->values(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_invoice_id' => 'required|exists:sale_invoices,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sale_invoice_item_id' => 'nullable|exists:sale_invoice_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = SaleInvoice::where('id', $validated['sale_invoice_id'])
            ->where('status', SaleInvoice::STATUS_CONFIRMED)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $return = SaleReturn::create([
                'return_number' => SaleReturn::generateReturnNumber(),
                'sale_invoice_id' => $invoice->id,
                'return_date' => $validated['return_date'],
                'warehouse_id' => $validated['warehouse_id'],
                'subtotal_refund' => 0,
                'tax_refund' => 0,
                'total_refund' => 0,
                'status' => SaleReturn::STATUS_PENDING,
                'user_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $items = array_values(array_filter($validated['items'], fn ($row) => (float) $row['quantity'] > 0));
            if ($items === []) {
                return back()->withInput()->withErrors(['items' => 'أدخل كمية مرتجعة واحدة على الأقل.']);
            }

            $subtotal = 0;
            foreach ($items as $row) {
                $total = round((float) $row['quantity'] * (float) $row['unit_price'], 2);
                $return->items()->create([
                    'sale_invoice_item_id' => $row['sale_invoice_item_id'] ?? null,
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

        return redirect()->route('admin.sale-returns.show', $return)
            ->with('success', 'تم إنشاء المرتجع. يمكنك إكماله لإدخال الكميات إلى المخزون.');
    }

    public function show(SaleReturn $saleReturn)
    {
        $saleReturn->load(['saleInvoice.branch', 'saleInvoice.customer', 'warehouse', 'user', 'items.product']);
        return view('admin.pages.sales.returns.show', compact('saleReturn'));
    }

    /**
     * إكمال المرتجع (إدخال مخزون).
     */
    public function complete(SaleReturn $saleReturn)
    {
        if ($saleReturn->status !== SaleReturn::STATUS_PENDING) {
            return redirect()->route('admin.sale-returns.show', $saleReturn)
                ->with('error', 'المرتجع مكتمل أو ملغى مسبقاً.');
        }

        try {
            $saleReturn->complete();
        } catch (\Throwable $e) {
            return redirect()->route('admin.sale-returns.show', $saleReturn)
                ->with('error', 'فشل إكمال المرتجع: ' . $e->getMessage());
        }

        return redirect()->route('admin.sale-returns.show', $saleReturn)
            ->with('success', 'تم إكمال المرتجع وإدخال الكميات إلى المخزون.');
    }
}
