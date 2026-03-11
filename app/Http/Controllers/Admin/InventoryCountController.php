<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryCountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:stock-list')->only('index');
        $this->middleware('permission:stock-count')->only('store');
    }

    /**
     * اختيار مخزن وعرض نموذج الجرد (الرصيد الحالي + إدخال الرصيد الفعلي)
     */
    public function index(Request $request)
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $warehouseId = $request->get('warehouse_id');

        if (!$warehouseId) {
            return view('admin.pages.stock.inventory-count-index', compact('warehouses'));
        }

        $warehouse = Warehouse::find($warehouseId);
        if (!$warehouse) {
            return redirect()->route('admin.stock.inventory-count.index')->with('error', 'المخزن غير موجود.');
        }

        $balances = StockBalance::where('warehouse_id', $warehouseId)
            ->with('product')
            ->orderBy('product_id')
            ->get();

        return view('admin.pages.stock.inventory-count-form', compact('warehouses', 'warehouse', 'balances'));
    }

    /**
     * تنفيذ الجرد: إنشاء حركات تسوية (inventory_count) للفرق بين الرصيد المحسب والفعلي
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'count_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.actual_quantity' => 'required|numeric|min:0',
        ]);

        $warehouseId = (int) $validated['warehouse_id'];
        $countDate = $validated['count_date'];

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $item) {
                $productId = (int) $item['product_id'];
                $actualQty = (float) $item['actual_quantity'];

                $balance = StockBalance::firstOrCreate(
                    ['product_id' => $productId, 'warehouse_id' => $warehouseId],
                    ['quantity' => 0]
                );

                $currentQty = (float) $balance->quantity;
                $diff = $actualQty - $currentQty;

                if (abs($diff) < 0.0001) {
                    continue;
                }

                StockMovement::record([
                    'type' => 'inventory_count',
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $diff,
                    'movement_date' => $countDate,
                    'reference_type' => 'inventory_count',
                    'notes' => 'جرد: الرصيد الفعلي ' . $actualQty . ' (المحسب كان ' . $currentQty . ')',
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }

        return redirect()->route('admin.stock.inventory-count.index')
            ->with('success', 'تم تنفيذ الجرد وتحديث الأرصدة بنجاح.');
    }
}
