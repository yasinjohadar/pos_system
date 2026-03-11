<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Treasury;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:purchase-invoice-list')->only('index');
        $this->middleware('permission:purchase-invoice-create')->only(['create', 'store']);
        $this->middleware('permission:purchase-invoice-edit')->only(['edit', 'update']);
        $this->middleware('permission:purchase-invoice-delete')->only('destroy');
        $this->middleware('permission:purchase-invoice-show')->only('show');
        $this->middleware('permission:purchase-invoice-confirm')->only('confirm');
    }

    public function index(Request $request)
    {
        $query = PurchaseInvoice::with(['branch', 'supplier', 'user'])
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');

        if ($request->filled('number')) {
            $query->where('number', 'like', '%' . $request->input('number') . '%');
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $invoices = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.purchases.invoices.index', compact('invoices', 'branches', 'suppliers'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->with('branch')->orderBy('name')->get();

        return view('admin.pages.purchases.invoices.create', compact('branches', 'suppliers', 'products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_date' => 'required|date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        DB::beginTransaction();
        try {
            $number = PurchaseInvoice::generateNumber((int) $validated['branch_id']);
            $invoice = PurchaseInvoice::create([
                'number' => $number,
                'invoice_date' => $validated['invoice_date'],
                'branch_id' => $validated['branch_id'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'],
                'subtotal' => 0,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'tax_amount' => 0,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'discount_amount' => 0,
                'total' => 0,
                'payment_status' => PurchaseInvoice::PAYMENT_STATUS_PENDING,
                'status' => PurchaseInvoice::STATUS_DRAFT,
                'user_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $row) {
                $total = round((float) $row['quantity'] * (float) $row['unit_price'], 2);
                $invoice->items()->create([
                    'product_id' => $row['product_id'],
                    'warehouse_id' => $row['warehouse_id'] ?? null,
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $total,
                ]);
            }

            $invoice->recalculateTotals();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('admin.purchase-invoices.show', $invoice)
            ->with('success', 'تم إنشاء فاتورة الشراء. يمكنك اعتمادها لإدخال المخزون.');
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['branch', 'supplier', 'warehouse', 'user', 'items.product', 'supplierPayments.paymentMethod', 'supplierPayments.treasury']);
        $paymentMethods = PaymentMethod::getActiveForSelect();
        $treasuries = Treasury::getActiveForSelect();

        return view('admin.pages.purchases.invoices.show', compact('purchaseInvoice', 'paymentMethods', 'treasuries'));
    }

    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== PurchaseInvoice::STATUS_DRAFT) {
            return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
                ->with('error', 'لا يمكن تعديل فاتورة معتمدة أو ملغاة.');
        }

        $purchaseInvoice->load('items.product');
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->with('branch')->orderBy('name')->get();

        return view('admin.pages.purchases.invoices.edit', compact('purchaseInvoice', 'branches', 'suppliers', 'products', 'warehouses'));
    }

    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== PurchaseInvoice::STATUS_DRAFT) {
            return redirect()->route('admin.purchase-invoices.index')
                ->with('error', 'لا يمكن تعديل الفاتورة.');
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_date' => 'required|date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        DB::beginTransaction();
        try {
            $purchaseInvoice->update([
                'invoice_date' => $validated['invoice_date'],
                'branch_id' => $validated['branch_id'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            $purchaseInvoice->items()->delete();
            foreach ($validated['items'] as $row) {
                $total = round((float) $row['quantity'] * (float) $row['unit_price'], 2);
                $purchaseInvoice->items()->create([
                    'product_id' => $row['product_id'],
                    'warehouse_id' => $row['warehouse_id'] ?? null,
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $total,
                ]);
            }

            $purchaseInvoice->recalculateTotals();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
            ->with('success', 'تم تحديث الفاتورة بنجاح.');
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== PurchaseInvoice::STATUS_DRAFT) {
            return redirect()->route('admin.purchase-invoices.index')
                ->with('error', 'لا يمكن حذف فاتورة معتمدة أو ملغاة.');
        }

        $purchaseInvoice->items()->delete();
        $purchaseInvoice->supplierPayments()->delete();
        $purchaseInvoice->delete();

        return redirect()->route('admin.purchase-invoices.index')
            ->with('success', 'تم حذف الفاتورة بنجاح.');
    }

    public function confirm(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== PurchaseInvoice::STATUS_DRAFT) {
            return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
                ->with('error', 'الفاتورة معتمدة أو ملغاة مسبقاً.');
        }

        try {
            $purchaseInvoice->confirm();
        } catch (\Throwable $e) {
            return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
                ->with('error', 'فشل الاعتماد: ' . $e->getMessage());
        }

        return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
            ->with('success', 'تم اعتماد الفاتورة وإدخال المخزون.');
    }

    public function addPayment(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== PurchaseInvoice::STATUS_CONFIRMED) {
            return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
                ->with('error', 'يتم تسجيل الدفعات للفواتير المعتمدة فقط.');
        }

        if (!$purchaseInvoice->supplier_id) {
            return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
                ->with('error', 'الفاتورة غير مرتبطة بمورد.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'treasury_id' => 'nullable|exists:treasuries,id',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $remaining = $purchaseInvoice->remaining_amount;
        if ((float) $validated['amount'] > $remaining) {
            return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
                ->with('error', 'المبلغ يتجاوز المتبقي للفاتورة (' . number_format($remaining, 2) . ').');
        }

        SupplierPayment::create([
            'supplier_id' => $purchaseInvoice->supplier_id,
            'amount' => $validated['amount'],
            'payment_method_id' => $validated['payment_method_id'],
            'treasury_id' => $validated['treasury_id'] ?? null,
            'payment_date' => $validated['payment_date'],
            'reference' => $validated['reference'] ?? null,
            'purchase_invoice_id' => $purchaseInvoice->id,
            'user_id' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
            ->with('success', 'تم تسجيل الدفعة بنجاح.');
    }

    public function destroyPayment(PurchaseInvoice $purchaseInvoice, SupplierPayment $payment)
    {
        if ($payment->purchase_invoice_id != $purchaseInvoice->id) {
            abort(404);
        }

        $payment->delete();

        return redirect()->route('admin.purchase-invoices.show', $purchaseInvoice)
            ->with('success', 'تم حذف الدفعة.');
    }
}
