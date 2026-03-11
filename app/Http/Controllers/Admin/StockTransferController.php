<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\StockMovement;
use App\Models\StockBalance;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:stock-list')->only(['index', 'show']);
        $this->middleware('permission:stock-transfer-create')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'user'])->orderByDesc('transfer_date')->orderByDesc('id');

        if ($request->filled('from_warehouse_id')) {
            $query->where('from_warehouse_id', $request->from_warehouse_id);
        }
        if ($request->filled('to_warehouse_id')) {
            $query->where('to_warehouse_id', $request->to_warehouse_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transfers = $query->paginate(15);
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.stock.transfers-index', compact('transfers', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.stock.transfer-create', compact('warehouses', 'products'));
    }

    /**
     * إنشاء تحويل: حركة صرف من المخزن المصدر + حركة إدخال للمخزن الهدف، مرتبطتان بنفس stock_transfer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        $fromId = (int) $validated['from_warehouse_id'];
        $toId = (int) $validated['to_warehouse_id'];

        foreach ($validated['items'] as $item) {
            $productId = (int) $item['product_id'];
            $qty = (float) $item['quantity'];
            $balance = StockBalance::firstOrCreate(
                ['product_id' => $productId, 'warehouse_id' => $fromId],
                ['quantity' => 0]
            );
            if ((float) $balance->quantity < $qty) {
                $product = Product::find($productId);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'رصيد المنتج «' . ($product->name ?? $productId) . '» في المخزن المصدر غير كافٍ.');
            }
        }

        DB::beginTransaction();
        try {
            $transfer = StockTransfer::create([
                'from_warehouse_id' => $fromId,
                'to_warehouse_id' => $toId,
                'transfer_date' => $validated['transfer_date'],
                'user_id' => auth()->id(),
                'status' => StockTransfer::STATUS_COMPLETED,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $productId = (int) $item['product_id'];
                $qty = (float) $item['quantity'];

                StockMovement::record([
                    'type' => 'transfer_out',
                    'product_id' => $productId,
                    'warehouse_id' => $fromId,
                    'quantity' => $qty,
                    'movement_date' => $transfer->transfer_date,
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $transfer->id,
                    'stock_transfer_id' => $transfer->id,
                    'notes' => 'تحويل إلى مخزن: ' . Warehouse::find($toId)->name,
                ]);

                StockMovement::record([
                    'type' => 'transfer_in',
                    'product_id' => $productId,
                    'warehouse_id' => $toId,
                    'quantity' => $qty,
                    'movement_date' => $transfer->transfer_date,
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $transfer->id,
                    'stock_transfer_id' => $transfer->id,
                    'notes' => 'تحويل من مخزن: ' . Warehouse::find($fromId)->name,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }

        return redirect()->route('admin.stock.transfers.index')
            ->with('success', 'تم تنفيذ التحويل بنجاح.');
    }

    public function show(StockTransfer $transfer)
    {
        $transfer->load(['fromWarehouse', 'toWarehouse', 'user', 'movements.product']);
        return view('admin.pages.stock.transfer-show', compact('transfer'));
    }
}
