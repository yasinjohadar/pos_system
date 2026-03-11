<?php

namespace App\Services\Reports;

use App\Models\StockBalance;
use Illuminate\Support\Collection;

class InventoryReportService
{
    /**
     * إرجاع أرصدة المخزون الحالية حسب المنتج (مع إمكانية فلترة بالمخزن أو التصنيف).
     */
    public function getCurrentStock(?int $warehouseId = null, ?int $categoryId = null): Collection
    {
        $query = StockBalance::query()
            ->with(['product.category', 'warehouse'])
            ->orderBy('product_id');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($categoryId) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        return $query->get();
    }

    /**
     * المنتجات التي تحتاج إلى إعادة طلب بناءً على رصيد المخزون وحقول إعادة الطلب في المنتج.
     */
    public function getReorderSuggestions(): Collection
    {
        $balances = StockBalance::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->get();

        return $balances->filter(function ($row) {
            $product = $row->product;
            if (!$product) {
                return false;
            }
            if ($product->reorder_level === null) {
                return false;
            }
            return (float) $row->total_qty <= (float) $product->reorder_level;
        });
    }
}

