<?php

namespace App\Services\Reports;

use App\Models\Customer;
use App\Models\SaleInvoice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerPerformanceService
{
    /**
     * أداء العملاء: إجمالي مبيعات، عدد فواتير، متوسط قيمة فاتورة، آخر شراء.
     */
    public function getCustomerPerformance(?string $fromDate = null, ?string $toDate = null): Collection
    {
        $query = SaleInvoice::query()
            ->where('status', 'confirmed')
            ->whereNotNull('customer_id')
            ->select([
                'customer_id',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('MAX(invoice_date) as last_invoice_date'),
            ])
            ->groupBy('customer_id');

        if ($fromDate) {
            $query->whereDate('invoice_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('invoice_date', '<=', $toDate);
        }

        $rows = $query->get();
        $customers = Customer::whereIn('id', $rows->pluck('customer_id'))->get()->keyBy('id');

        return $rows->map(function ($row) use ($customers) {
            $customer = $customers->get($row->customer_id);
            $totalSales = (float) $row->total_sales;
            $count = (int) $row->invoice_count;
            $avgInvoice = $count > 0 ? $totalSales / $count : 0;
            return (object) [
                'customer_id' => $row->customer_id,
                'customer_name' => $customer ? $customer->name : '—',
                'invoice_count' => $count,
                'total_sales' => $totalSales,
                'avg_invoice_value' => round($avgInvoice, 2),
                'last_invoice_date' => $row->last_invoice_date,
            ];
        })->sortByDesc('total_sales')->values();
    }

    /**
     * أفضل العملاء حسب المبيعات.
     */
    public function getTopCustomers(int $limit = 10, ?string $from = null, ?string $to = null): Collection
    {
        return $this->getCustomerPerformance($from, $to)->take($limit);
    }

    /**
     * عملاء غير نشطين (لم يشتروا منذ X يوم).
     */
    public function getInactiveCustomers(int $daysInactive = 90): Collection
    {
        $cutoff = now()->subDays($daysInactive)->toDateString();
        return Customer::where('is_active', true)
            ->whereDoesntHave('saleInvoices', function ($q) use ($cutoff) {
                $q->where('status', 'confirmed')->where('invoice_date', '>=', $cutoff);
            })
            ->orderBy('name')
            ->get();
    }
}
