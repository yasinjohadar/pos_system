<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ProductBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:product-batch-list')->only('index');
        $this->middleware('permission:product-batch-create')->only(['create', 'store']);
        $this->middleware('permission:product-batch-edit')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $query = ProductBatch::with(['product', 'warehouse'])->orderByDesc('received_date');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $batches = $query->paginate(20)->withQueryString();
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.inventory.batches.partials.table-rows', compact('batches'))->render(),
                'pagination' => view('admin.pages.inventory.batches.partials.pagination', compact('batches'))->render(),
            ]);
        }

        return view('admin.pages.inventory.batches.index', compact('batches', 'products', 'warehouses'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.inventory.batches.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'batch_number' => 'required|string|max:100',
            'expiry_date' => 'nullable|date',
            'initial_quantity' => 'required|numeric|min:0.0001',
            'cost_price' => 'nullable|numeric|min:0',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        ProductBatch::create([
            ...$validated,
            'current_quantity' => $validated['initial_quantity'],
        ]);

        return redirect()->route('admin.product-batches.index')->with('success', 'تم إضافة الدفعة.');
    }

    public function edit(ProductBatch $productBatch)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.inventory.batches.edit', compact('productBatch', 'products', 'warehouses'));
    }

    public function update(Request $request, ProductBatch $productBatch)
    {
        $validated = $request->validate([
            'batch_number' => 'required|string|max:100',
            'expiry_date' => 'nullable|date',
            'cost_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $productBatch->update($validated);

        return redirect()->route('admin.product-batches.index')->with('success', 'تم تحديث الدفعة.');
    }
}
