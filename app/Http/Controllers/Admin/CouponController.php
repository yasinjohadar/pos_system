<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:coupon-list')->only('index');
        $this->middleware('permission:coupon-create')->only(['create', 'store']);
        $this->middleware('permission:coupon-edit')->only(['edit', 'update']);
        $this->middleware('permission:coupon-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Coupon::query()->orderByDesc('id');

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->input('code') . '%');
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        $coupons = $query->paginate(15)->withQueryString();

        return view('admin.pages.sales.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.pages.sales.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:coupons,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validated['type'] === 'percent' && (float) $validated['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'نسبة الخصم لا يجب أن تتجاوز 100%.']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم إنشاء الكوبون بنجاح.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.pages.sales.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validated['valid_from'] && $validated['valid_until'] && $validated['valid_until'] < $validated['valid_from']) {
            return back()->withInput()->withErrors(['valid_until' => 'تاريخ انتهاء الصلاحية يجب أن يكون بعد تاريخ البدء.']);
        }

        if ($validated['type'] === 'percent' && (float) $validated['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'نسبة الخصم لا يجب أن تتجاوز 100%.']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم تحديث الكوبون بنجاح.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم حذف الكوبون بنجاح.');
    }

    /**
     * التحقق من كود الخصم وإرجاع قيمة الخصم (للاستخدام من واجهة الفاتورة).
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $result = Coupon::validateAndGetDiscount(
            $request->input('code'),
            (float) $request->input('subtotal')
        );

        return response()->json($result);
    }
}
