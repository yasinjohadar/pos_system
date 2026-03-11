<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\SalePayment;
use App\Models\Warehouse;
use App\Models\Treasury;
use App\Services\Pricing\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:sale-invoice-list')->only('index');
        $this->middleware('permission:sale-invoice-create')->only(['create', 'store']);
        $this->middleware('permission:sale-invoice-edit')->only(['edit', 'update']);
        $this->middleware('permission:sale-invoice-delete')->only('destroy');
        $this->middleware('permission:sale-invoice-show')->only('show');
        $this->middleware('permission:sale-invoice-confirm')->only('confirm');
    }

    public function index(Request $request)
    {
        $query = SaleInvoice::with(['branch', 'customer', 'user'])
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');

        if ($request->filled('number')) {
            $query->where('number', 'like', '%' . $request->input('number') . '%');
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $invoices = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.sales.invoices.index', compact('invoices', 'branches', 'customers'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->with('branch')->orderBy('name')->get();

        return view('admin.pages.sales.invoices.create', compact('branches', 'customers', 'products', 'warehouses'));
    }

    public function store(Request $request, PricingService $pricingService)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
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
            $number = SaleInvoice::generateNumber((int) $validated['branch_id']);
            $invoice = SaleInvoice::create([
                'number' => $number,
                'invoice_date' => $validated['invoice_date'],
                'branch_id' => $validated['branch_id'],
                'customer_id' => $validated['customer_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'],
                'subtotal' => 0,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'tax_amount' => 0,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'discount_amount' => 0,
                'total' => 0,
                'payment_status' => SaleInvoice::PAYMENT_STATUS_PENDING,
                'status' => SaleInvoice::STATUS_DRAFT,
                'user_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
            }

            foreach ($validated['items'] as $row) {
                $product = Product::find($row['product_id']);
                $quantity = (float) $row['quantity'];
                $pricing = $pricingService->calculateItemPrice(
                    $product,
                    $customer,
                    $quantity,
                    (int) $validated['branch_id']
                );

                $invoice->items()->create([
                    'product_id' => $row['product_id'],
                    'warehouse_id' => $row['warehouse_id'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $pricing['unit_price'],
                    'total' => $pricing['line_total'],
                ]);
            }

            $invoice->recalculateTotals();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('admin.sale-invoices.show', $invoice)
            ->with('success', 'تم إنشاء الفاتورة بنجاح. يمكنك تأكيدها لصرف المخزون.');
    }

    public function show(SaleInvoice $saleInvoice)
    {
        $saleInvoice->load(['branch', 'customer', 'warehouse', 'user', 'items.product', 'payments.paymentMethod', 'payments.treasury']);
        $paymentMethods = PaymentMethod::getActiveForSelect();
        $treasuries = Treasury::getActiveForSelect();

        return view('admin.pages.sales.invoices.show', compact('saleInvoice', 'paymentMethods', 'treasuries'));
    }

    public function edit(SaleInvoice $saleInvoice)
    {
        if ($saleInvoice->status !== SaleInvoice::STATUS_DRAFT) {
            return redirect()->route('admin.sale-invoices.show', $saleInvoice)
                ->with('error', 'لا يمكن تعديل فاتورة مؤكدة أو ملغاة.');
        }

        $saleInvoice->load('items.product');
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->with('branch')->orderBy('name')->get();

        return view('admin.pages.sales.invoices.edit', compact('saleInvoice', 'branches', 'customers', 'products', 'warehouses'));
    }

    public function update(Request $request, SaleInvoice $saleInvoice, PricingService $pricingService)
    {
        if ($saleInvoice->status !== SaleInvoice::STATUS_DRAFT) {
            return redirect()->route('admin.sale-invoices.index')
                ->with('error', 'لا يمكن تعديل الفاتورة.');
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
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
            $saleInvoice->update([
                'invoice_date' => $validated['invoice_date'],
                'branch_id' => $validated['branch_id'],
                'customer_id' => $validated['customer_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            $saleInvoice->items()->delete();

            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
            }

            foreach ($validated['items'] as $row) {
                $product = Product::find($row['product_id']);
                $quantity = (float) $row['quantity'];
                $pricing = $pricingService->calculateItemPrice(
                    $product,
                    $customer,
                    $quantity,
                    (int) $validated['branch_id']
                );

                $saleInvoice->items()->create([
                    'product_id' => $row['product_id'],
                    'warehouse_id' => $row['warehouse_id'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $pricing['unit_price'],
                    'total' => $pricing['line_total'],
                ]);
            }

            $saleInvoice->recalculateTotals();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('admin.sale-invoices.show', $saleInvoice)
            ->with('success', 'تم تحديث الفاتورة بنجاح.');
    }

    public function destroy(SaleInvoice $saleInvoice)
    {
        if ($saleInvoice->status !== SaleInvoice::STATUS_DRAFT) {
            return redirect()->route('admin.sale-invoices.index')
                ->with('error', 'لا يمكن حذف فاتورة مؤكدة أو ملغاة.');
        }

        $saleInvoice->items()->delete();
        $saleInvoice->payments()->delete();
        $saleInvoice->delete();

        return redirect()->route('admin.sale-invoices.index')
            ->with('success', 'تم حذف الفاتورة بنجاح.');
    }

    /**
     * تأكيد الفاتورة (صرف مخزون).
     */
    public function confirm(SaleInvoice $saleInvoice)
    {
        if ($saleInvoice->status !== SaleInvoice::STATUS_DRAFT) {
            return redirect()->route('admin.sale-invoices.show', $saleInvoice)
                ->with('error', 'الفاتورة مؤكدة أو ملغاة مسبقاً.');
        }

        try {
            $saleInvoice->confirm();
        } catch (\Throwable $e) {
            return redirect()->route('admin.sale-invoices.show', $saleInvoice)
                ->with('error', 'فشل التأكيد: ' . $e->getMessage());
        }

        return redirect()->route('admin.sale-invoices.show', $saleInvoice)
            ->with('success', 'تم تأكيد الفاتورة وصرف المخزون.');
    }

    /**
     * إضافة دفعة للفاتورة.
     */
    public function addPayment(Request $request, SaleInvoice $saleInvoice)
    {
        if ($saleInvoice->status !== SaleInvoice::STATUS_CONFIRMED) {
            return redirect()->route('admin.sale-invoices.show', $saleInvoice)
                ->with('error', 'يتم تسجيل الدفعات للفواتير المؤكدة فقط.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'treasury_id' => 'nullable|exists:treasuries,id',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $remaining = $saleInvoice->remaining_amount;
        if ((float) $validated['amount'] > $remaining) {
            return redirect()->route('admin.sale-invoices.show', $saleInvoice)
                ->with('error', 'المبلغ يتجاوز المتبقي للفاتورة (' . number_format($remaining, 2) . ').');
        }

        SalePayment::create([
            'sale_invoice_id' => $saleInvoice->id,
            'amount' => $validated['amount'],
            'payment_method_id' => $validated['payment_method_id'],
            'treasury_id' => $validated['treasury_id'] ?? null,
            'payment_date' => $validated['payment_date'],
            'reference' => $validated['reference'] ?? null,
            'user_id' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.sale-invoices.show', $saleInvoice)
            ->with('success', 'تم تسجيل الدفعة بنجاح.');
    }

    /**
     * جلب سعر البيع للمنتج حسب الفرع (للاستخدام في نموذج الفاتورة).
     */
    public function getProductPrice(Request $request)
    {
        $productId = $request->input('product_id');
        $branchId = $request->input('branch_id');
        if (!$productId) {
            return response()->json(['price' => 0]);
        }
        $product = Product::find($productId);
        $price = $product ? $product->getPriceForBranch($branchId ? (int) $branchId : null, 'retail') : 0;
        return response()->json(['price' => round($price, 2)]);
    }

    /**
     * حذف دفعة (للمسؤول فقط أو حسب الصلاحية).
     */
    public function destroyPayment(SaleInvoice $saleInvoice, SalePayment $payment)
    {
        if ($payment->sale_invoice_id != $saleInvoice->id) {
            abort(404);
        }

        $payment->delete();

        return redirect()->route('admin.sale-invoices.show', $saleInvoice)
            ->with('success', 'تم حذف الدفعة.');
    }
}
