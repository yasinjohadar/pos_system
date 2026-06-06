<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\ProductPrice;
use App\Models\ProductBarcode;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:product-list')->only('index');
        $this->middleware('permission:product-create')->only(['create', 'store']);
        $this->middleware('permission:product-edit')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('permission:product-delete')->only('destroy');
        $this->middleware('permission:product-show')->only('show');
        $this->middleware('permission:product-list')->only('searchByBarcode');
        $this->middleware('permission:product-list|stock-movement-create|stock-transfer-create|stock-count|promotion-create|promotion-edit|price-list-create|price-list-edit|sale-invoice-create|sale-invoice-edit')->only('searchSelect');
    }

    /**
     * البحث عن منتج بالباركود (الباركود الرئيسي أو من جدول product_barcodes).
     * للاستخدام في نقطة البيع والفحوصات.
     */
    public function searchByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        if (empty($barcode) || !is_string($barcode)) {
            return response()->json(['product' => null]);
        }
        $barcode = trim($barcode);

        $product = Product::where('barcode', $barcode)->where('is_active', true)->first();
        if ($product) {
            return response()->json([
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'unit_id' => $product->unit_id,
                ],
            ]);
        }

        $productBarcode = ProductBarcode::where('barcode', $barcode)->with('product')->first();
        if ($productBarcode && $productBarcode->product && $productBarcode->product->is_active) {
            $p = $productBarcode->product;
            return response()->json([
                'product' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'barcode' => $p->barcode ?? $barcode,
                    'unit_id' => $p->unit_id,
                ],
            ]);
        }

        return response()->json(['product' => null]);
    }

    /**
     * بحث المنتجات لقوائم الاختيار (Select2 AJAX).
     */
    public function searchSelect(Request $request)
    {
        $search = trim((string) $request->input('search', $request->input('q', '')));

        $query = Product::query()
            ->where('is_active', true)
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('barcodes', fn ($b) => $b->where('barcode', 'like', "%{$search}%"));
            });
        }

        $products = $query->limit(25)->get(['id', 'name', 'barcode']);

        return response()->json([
            'results' => $products->map(fn ($p) => [
                'id' => $p->id,
                'text' => $p->name . ($p->barcode ? ' (' . $p->barcode . ')' : ''),
            ])->values(),
        ]);
    }

    public function index(Request $request)
    {
        $products = $this->buildProductsQuery($request)
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.products.partials.table-rows', compact('products'))->render(),
                'pagination' => view('admin.pages.products.partials.pagination', compact('products'))->render(),
            ]);
        }

        return view('admin.pages.products.index', compact('products', 'categories'));
    }

    private function buildProductsQuery(Request $request)
    {
        $query = Product::with(['category', 'unit'])->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('barcodes', fn ($b) => $b->where('barcode', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (int) $request->input('is_active'));
        }

        return $query;
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $taxes = \App\Models\Tax::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.products.create', compact('categories', 'units', 'taxes'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['min_stock_alert'] = $data['min_stock_alert'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'prices.branch']);
        return view('admin.pages.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['prices.branch', 'barcodes']);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $taxes = \App\Models\Tax::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.products.edit', compact('product', 'categories', 'units', 'branches', 'taxes'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['min_stock_alert'] = $data['min_stock_alert'] ?? 0;

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        // استبدال أسعار إضافية (حسب الفرع ونوع السعر)
        $product->prices()->delete();
        if ($request->has('prices')) {
            foreach ($request->input('prices', []) as $priceRow) {
                if (empty($priceRow['value']) || !is_numeric($priceRow['value'])) {
                    continue;
                }
                $branchId = !empty($priceRow['branch_id']) ? $priceRow['branch_id'] : null;
                $priceType = $priceRow['price_type'] ?? 'retail';

                ProductPrice::create([
                    'product_id' => $product->id,
                    'branch_id' => $branchId,
                    'price_type' => $priceType,
                    'value' => $priceRow['value'],
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function toggleStatus(Request $request, Product $product)
    {
        try {
            $product->update(['is_active' => ! $product->is_active]);
            $product->refresh();

            $status = $product->is_active ? 'نشط' : 'غير نشط';

            return response()->json([
                'success' => true,
                'message' => "تم تحديث حالة المنتج إلى: {$status}",
                'is_active' => (bool) $product->is_active,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling product status', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة المنتج',
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->prices()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }

    /**
     * إضافة باركود إضافي للمنتج.
     */
    public function storeBarcode(Request $request, Product $product)
    {
        $validated = $request->validate([
            'barcode' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);
        $barcode = trim($validated['barcode']);
        if ($barcode === '') {
            return redirect()->route('admin.products.edit', $product)
                ->with('error', 'قيمة الباركود مطلوبة.');
        }
        if (ProductBarcode::where('barcode', $barcode)->where('product_id', '!=', $product->id)->exists()) {
            return redirect()->route('admin.products.edit', $product)
                ->with('error', 'هذا الباركود مستخدم لمنتج آخر.');
        }
        if (ProductBarcode::where('barcode', $barcode)->where('product_id', $product->id)->exists()) {
            return redirect()->route('admin.products.edit', $product)
                ->with('error', 'هذا الباركود مضاف مسبقاً لهذا المنتج.');
        }
        ProductBarcode::create([
            'product_id' => $product->id,
            'barcode' => $barcode,
            'description' => $validated['description'] ?? null,
            'is_primary' => false,
        ]);
        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'تم إضافة الباركود.');
    }

    /**
     * حذف باركود إضافي.
     */
    public function destroyBarcode(ProductBarcode $productBarcode)
    {
        $product = $productBarcode->product;
        $productBarcode->delete();
        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'تم حذف الباركود.');
    }
}
