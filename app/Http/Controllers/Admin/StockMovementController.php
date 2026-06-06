<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:stock-list')->only(['index', 'balances']);
        $this->middleware('permission:stock-movement-create')->only(['create', 'store']);
    }

    /**
     * قائمة حركات المخزون
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'warehouse', 'user'])->orderByDesc('movement_date')->orderByDesc('id');

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('movement_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('movement_date', '<=', $request->to_date);
        }

        $movements = $query->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.stock.partials.movements-table-rows', compact('movements'))->render(),
                'pagination' => view('admin.pages.stock.partials.pagination', ['paginator' => $movements])->render(),
            ]);
        }

        return view('admin.pages.stock.movements-index', compact('movements', 'warehouses'));
    }

    /**
     * نموذج إدخال حركة يدوية (إدخال / صرف / تسوية)
     */
    public function create(Request $request)
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $selectedProduct = old('product_id')
            ? Product::find(old('product_id'))
            : null;

        return view('admin.pages.stock.movement-create', compact('warehouses', 'selectedProduct'));
    }

    /**
     * حفظ حركة جديدة وتحديث الرصيد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.0001',
            'movement_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $balance = StockBalance::firstOrCreate(
            [
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['warehouse_id'],
            ],
            ['quantity' => 0]
        );

        if ($validated['type'] === 'out' && (float) $balance->quantity < (float) $validated['quantity']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'الرصيد الحالي (' . number_format($balance->quantity, 2) . ') أقل من الكمية المطلوبة.');
        }

        StockMovement::record([
            'type' => $validated['type'],
            'product_id' => $validated['product_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'quantity' => $validated['quantity'],
            'movement_date' => $validated['movement_date'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.stock.movements.index')
            ->with('success', 'تم تسجيل الحركة وتحديث الرصيد بنجاح.');
    }

    /**
     * صفحة أرصدة المخزون (حسب منتج/مخزن)
     */
    public function balances(Request $request)
    {
        $query = StockBalance::with(['product', 'warehouse'])->orderBy('warehouse_id')->orderBy('product_id');

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('low_stock') && $request->boolean('low_stock')) {
            $query->whereHas('product', fn ($q) => $q->where('min_stock_alert', '>', 0))
                ->whereRaw('stock_balances.quantity <= (SELECT min_stock_alert FROM products WHERE products.id = stock_balances.product_id)');
        }

        $balances = $query->paginate(25)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.stock.partials.balances-table-rows', compact('balances'))->render(),
                'pagination' => view('admin.pages.stock.partials.pagination', ['paginator' => $balances])->render(),
            ]);
        }

        return view('admin.pages.stock.balances-index', compact('balances', 'warehouses'));
    }
}
