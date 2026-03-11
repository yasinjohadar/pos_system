<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:payment-method-list')->only('index');
        $this->middleware('permission:payment-method-create')->only(['create', 'store']);
        $this->middleware('permission:payment-method-edit')->only(['edit', 'update']);
        $this->middleware('permission:payment-method-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = PaymentMethod::query()->orderBy('sort_order')->orderBy('name');

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $paymentMethods = $query->paginate(15);

        return view('admin.pages.sales.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.pages.sales.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'تم إضافة طريقة الدفع بنجاح');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.pages.sales.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $paymentMethod->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'تم تحديث طريقة الدفع بنجاح');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->salePayments()->exists()) {
            return redirect()->route('admin.payment-methods.index')
                ->with('error', 'لا يمكن حذف طريقة الدفع لأنها مرتبطة بدفعات.');
        }

        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'تم حذف طريقة الدفع بنجاح');
    }
}
