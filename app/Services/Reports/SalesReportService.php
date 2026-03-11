<?php

namespace App\Services\Reports;

use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use Carbon\Carbon;

class SalesReportService
{
    /**
     * ملخص مبيعات ليوم واحد.
     *
     * @return array{
     *  date: string,
     *  invoices_count: int,
     *  total_sales: float,
     *  total_returns: float,
     *  net_sales: float,
     *  tax_amount: float,
     *  discount_amount: float
     * }
     */
    public function getDailySummary(Carbon $date, ?int $branchId = null): array
    {
        $query = SaleInvoice::query()
            ->whereDate('invoice_date', $date->toDateString())
            ->where('status', SaleInvoice::STATUS_CONFIRMED);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $invoices = $query->get();

        $invoicesCount = $invoices->count();
        $totalSales = (float) $invoices->sum('total');
        $taxAmount = (float) $invoices->sum('tax_amount');
        $discountAmount = (float) $invoices->sum('discount_amount');

        $returnsQuery = SaleReturn::query()
            ->whereDate('return_date', $date->toDateString())
            ->where('status', SaleReturn::STATUS_COMPLETED);

        if ($branchId) {
            $returnsQuery->whereHas('saleInvoice', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $totalReturns = (float) $returnsQuery->sum('total_refund');

        $netSales = $totalSales - $totalReturns;

        return [
            'date' => $date->toDateString(),
            'invoices_count' => $invoicesCount,
            'total_sales' => $totalSales,
            'total_returns' => $totalReturns,
            'net_sales' => $netSales,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * ملخص مبيعات شهري (قيمة لكل يوم في الشهر).
     *
     * @return array{labels: array<int,string>, totals: array<int,float>}
     */
    public function getMonthlySummary(int $year, int $month, ?int $branchId = null): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = (clone $start)->endOfMonth();

        $query = SaleInvoice::query()
            ->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', SaleInvoice::STATUS_CONFIRMED);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $grouped = $query
            ->selectRaw('DATE(invoice_date) as d, SUM(total) as total')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $labels = [];
        $totals = [];

        $cursor = $start->copy();
        while ($cursor->lessThanOrEqualTo($end)) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('Y-m-d');
            $totals[] = isset($grouped[$key]) ? (float) $grouped[$key]->total : 0.0;
            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
        ];
    }
}

