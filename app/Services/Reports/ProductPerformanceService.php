<?php

namespace App\Services\Reports;

use App\Models\Product;
use App\Models\SaleInvoiceItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductPerformanceService
{
    /**
     * إيرادات وربح حسب المنتج (فواتير مؤكدة فقط).
     */
    public function getProductPerformance(?string $fromDate = null, ?string $toDate = null): Collection
    {
        $query = SaleInvoiceItem::query()
            ->join('sale_invoices', 'sale_invoice_items.sale_invoice_id', '=', 'sale_invoices.id')
            ->where('sale_invoices.status', 'confirmed')
            ->select([
                'sale_invoice_items.product_id',
                DB::raw('SUM(sale_invoice_items.quantity) as total_qty'),
                DB::raw('SUM(sale_invoice_items.total) as total_revenue'),
            ])
            ->groupBy('sale_invoice_items.product_id');

        if ($fromDate) {
            $query->whereDate('sale_invoices.invoice_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('sale_invoices.invoice_date', '<=', $toDate);
        }

        $rows = $query->get();
        $products = Product::whereIn('id', $rows->pluck('product_id'))->get()->keyBy('id');

        return $rows->map(function ($row) use ($products) {
            $product = $products->get($row->product_id);
            $revenue = (float) $row->total_revenue;
            $costPrice = $product ? (float) $product->cost_price : 0;
            $cost = (float) $row->total_qty * $costPrice;
            $profit = $revenue - $cost;
            $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;
            return (object) [
                'product_id' => $row->product_id,
                'product_name' => $product ? $product->name : '—',
                'category_name' => $product && $product->category ? $product->category->name : '—',
                'total_qty' => (float) $row->total_qty,
                'total_revenue' => $revenue,
                'total_cost' => $cost,
                'profit' => $profit,
                'margin_percent' => $margin,
            ];
        })->sortByDesc('total_revenue')->values();
    }

    /**
     * أفضل المنتجات مبيعاً حسب الإيراد.
     */
    public function getTopProducts(int $limit = 10, ?string $from = null, ?string $to = null): Collection
    {
        return $this->getProductPerformance($from, $to)->take($limit);
    }

    /**
     * منتجات بدون مبيعات (لم تظهر في أي فاتورة مؤكدة).
     */
    public function getProductsWithNoSales(): Collection
    {
        $soldIds = SaleInvoiceItem::query()
            ->join('sale_invoices', 'sale_invoice_items.sale_invoice_id', '=', 'sale_invoices.id')
            ->where('sale_invoices.status', 'confirmed')
            ->distinct()
            ->pluck('sale_invoice_items.product_id');

        return Product::where('is_active', true)
            ->whereNotIn('id', $soldIds)
            ->with('category')
            ->orderBy('name')
            ->get();
    }
}
