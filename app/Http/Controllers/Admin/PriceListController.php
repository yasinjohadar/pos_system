<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceList;
use App\Models\Product;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:price-list-list')->only('index');
        $this->middleware('permission:price-list-create')->only(['create', 'store']);
        $this->middleware('permission:price-list-edit')->only(['edit', 'update']);
        $this->middleware('permission:price-list-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = PriceList::withCount('items')->orderByDesc('is_active')->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $priceLists = $query->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.sales.price-lists.partials.table-rows', compact('priceLists'))->render(),
                'pagination' => view('admin.pages.sales.price-lists.partials.pagination', compact('priceLists'))->render(),
            ]);
        }

        return view('admin.pages.sales.price-lists.index', compact('priceLists'));
    }

    public function create()
    {
        return view('admin.pages.sales.price-lists.create', [
            'oldProducts' => $this->resolveItemProducts(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        $priceList = PriceList::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (empty($item['product_id']) || $item['price'] === null || $item['price'] === '') {
                    continue;
                }
                $priceList->items()->create([
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                ]);
            }
        }

        return redirect()->route('admin.price-lists.index')
            ->with('success', 'تم إنشاء قائمة الأسعار بنجاح.');
    }

    public function edit(PriceList $priceList)
    {
        $priceList->load('items.product');

        return view('admin.pages.sales.price-lists.edit', [
            'priceList' => $priceList,
            'oldProducts' => $this->resolveItemProducts($priceList),
        ]);
    }

    public function update(Request $request, PriceList $priceList)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        $priceList->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $priceList->items()->delete();
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (empty($item['product_id']) || $item['price'] === null || $item['price'] === '') {
                    continue;
                }
                $priceList->items()->create([
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                ]);
            }
        }

        return redirect()->route('admin.price-lists.index')
            ->with('success', 'تم تحديث قائمة الأسعار بنجاح.');
    }

    public function destroy(PriceList $priceList)
    {
        if ($priceList->customers()->exists()) {
            return redirect()->route('admin.price-lists.index')
                ->with('error', 'لا يمكن حذف قائمة أسعار مرتبطة بعملاء.');
        }

        $priceList->items()->delete();
        $priceList->delete();

        return redirect()->route('admin.price-lists.index')
            ->with('success', 'تم حذف قائمة الأسعار بنجاح.');
    }

    private function resolveItemProducts(?PriceList $priceList = null)
    {
        $ids = collect(old('items', []))->pluck('product_id')->filter()->unique();

        if ($ids->isEmpty() && $priceList) {
            $ids = $priceList->items->pluck('product_id')->unique();
        }

        return $ids->isNotEmpty()
            ? Product::whereIn('id', $ids)->get()->keyBy('id')
            : collect();
    }
}

