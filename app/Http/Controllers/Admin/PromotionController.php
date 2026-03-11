<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:promotion-list')->only('index');
        $this->middleware('permission:promotion-create')->only(['create', 'store']);
        $this->middleware('permission:promotion-edit')->only(['edit', 'update']);
        $this->middleware('permission:promotion-delete')->only('destroy');
    }

    public function index()
    {
        $promotions = Promotion::withCount('items')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.pages.sales.promotions.index', compact('promotions'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.sales.promotions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'min_invoice_amount' => 'nullable|numeric|min:0',
            'min_qty' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.max_qty' => 'nullable|numeric|min:0',
        ]);

        $promotion = Promotion::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'min_invoice_amount' => $validated['min_invoice_amount'] ?? null,
            'min_qty' => $validated['min_qty'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (empty($item['product_id'])) {
                    continue;
                }
                $promotion->items()->create([
                    'product_id' => $item['product_id'],
                    'max_qty' => $item['max_qty'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.promotions.index')
            ->with('success', 'تم إنشاء العرض بنجاح.');
    }

    public function edit(Promotion $promotion)
    {
        $promotion->load('items.product');
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.sales.promotions.edit', compact('promotion', 'products'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'min_invoice_amount' => 'nullable|numeric|min:0',
            'min_qty' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.max_qty' => 'nullable|numeric|min:0',
        ]);

        $promotion->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'min_invoice_amount' => $validated['min_invoice_amount'] ?? null,
            'min_qty' => $validated['min_qty'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $promotion->items()->delete();
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (empty($item['product_id'])) {
                    continue;
                }
                $promotion->items()->create([
                    'product_id' => $item['product_id'],
                    'max_qty' => $item['max_qty'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.promotions.index')
            ->with('success', 'تم تحديث العرض بنجاح.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->items()->delete();
        $promotion->delete();

        return redirect()->route('admin.promotions.index')
            ->with('success', 'تم حذف العرض بنجاح.');
    }
}

