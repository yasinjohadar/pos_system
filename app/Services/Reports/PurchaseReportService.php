<?php

namespace App\Services\Reports;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use Carbon\Carbon;

class PurchaseReportService
{
    /**
     * ملخص مشتريات ليوم واحد.
     *
     * @return array{
     *  date: string,
     *  invoices_count: int,
     *  total_purchases: float,
     *  total_returns: float,
     *  net_purchases: float,
     *  tax_amount: float,
     *  discount_amount: float
     * }
     */
    public function getDailySummary(Carbon $date, ?int $branchId = null): array
    {
        $query = PurchaseInvoice::query()
            ->whereDate('invoice_date', $date->toDateString())
            ->where('status', PurchaseInvoice::STATUS_CONFIRMED);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $invoices = $query->get();

        $invoicesCount = $invoices->count();
        $totalPurchases = (float) $invoices->sum('total');
        $taxAmount = (float) $invoices->sum('tax_amount');
        $discountAmount = (float) $invoices->sum('discount_amount');

        $returnsQuery = PurchaseReturn::query()
            ->whereDate('return_date', $date->toDateString())
            ->where('status', PurchaseReturn::STATUS_COMPLETED);

        if ($branchId) {
            $returnsQuery->whereHas('purchaseInvoice', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $totalReturns = (float) $returnsQuery->sum('total_refund');
        $netPurchases = $totalPurchases - $totalReturns;

        return [
            'date' => $date->toDateString(),
            'invoices_count' => $invoicesCount,
            'total_purchases' => $totalPurchases,
            'total_returns' => $totalReturns,
            'net_purchases' => $netPurchases,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * ملخص مشتريات شهري (قيمة لكل يوم في الشهر).
     *
     * @return array{labels: array<int,string>, totals: array<int,float>}
     */
    public function getMonthlySummary(int $year, int $month, ?int $branchId = null): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = (clone $start)->endOfMonth();

        $query = PurchaseInvoice::query()
            ->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', PurchaseInvoice::STATUS_CONFIRMED);

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

