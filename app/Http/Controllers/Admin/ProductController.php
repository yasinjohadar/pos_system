<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\ProductPrice;
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
        $this->middleware('permission:product-edit')->only(['edit', 'update']);
        $this->middleware('permission:product-delete')->only('destroy');
        $this->middleware('permission:product-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit'])->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%")
                    ->orWhere('slug', 'like', "%$search%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $products = $query->paginate(15);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.products.create', compact('categories', 'units'));
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
        $product->load('prices.branch');
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.products.edit', compact('product', 'categories', 'units', 'branches'));
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
}
