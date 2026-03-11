<?php

namespace App\Services\Reports;

use App\Models\PurchaseInvoice;
use App\Models\SaleInvoice;
use Carbon\Carbon;

class TaxReportService
{
    public function getSalesTaxReport(Carbon $from, Carbon $to, ?int $branchId = null): float
    {
        $query = SaleInvoice::query()
            ->whereBetween('invoice_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', SaleInvoice::STATUS_CONFIRMED);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return (float) $query->sum('tax_amount');
    }

    public function getPurchaseTaxReport(Carbon $from, Carbon $to, ?int $branchId = null): float
    {
        $query = PurchaseInvoice::query()
            ->whereBetween('invoice_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', PurchaseInvoice::STATUS_CONFIRMED);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return (float) $query->sum('tax_amount');
    }
}

