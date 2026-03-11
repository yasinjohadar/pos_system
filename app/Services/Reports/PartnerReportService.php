<?php

namespace App\Services\Reports;

use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;

class PartnerReportService
{
    /**
     * تقسيم رصيد العميل إلى أعمار ديون (0-30, 31-60, 61-90, +90).
     */
    public function getCustomerAging(Customer $customer, ?Carbon $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?: Carbon::today();
        $entries = $customer->getStatementEntries($asOfDate);

        $buckets = [
            '0_30' => 0.0,
            '31_60' => 0.0,
            '61_90' => 0.0,
            '90_plus' => 0.0,
        ];

        foreach ($entries as $entry) {
            $days = $entry['date']->diffInDays($asOfDate);
            $balanceDelta = $entry['debit'] - $entry['credit'];

            if ($days <= 30) {
                $buckets['0_30'] += $balanceDelta;
            } elseif ($days <= 60) {
                $buckets['31_60'] += $balanceDelta;
            } elseif ($days <= 90) {
                $buckets['61_90'] += $balanceDelta;
            } else {
                $buckets['90_plus'] += $balanceDelta;
            }
        }

        return $buckets;
    }

    /**
     * تقسيم رصيد المورد إلى أعمار ذمم.
     */
    public function getSupplierAging(Supplier $supplier, ?Carbon $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?: Carbon::today();
        $entries = $supplier->getStatementEntries($asOfDate);

        $buckets = [
            '0_30' => 0.0,
            '31_60' => 0.0,
            '61_90' => 0.0,
            '90_plus' => 0.0,
        ];

        foreach ($entries as $entry) {
            $days = $entry['date']->diffInDays($asOfDate);
            $balanceDelta = $entry['debit'] - $entry['credit'];

            if ($days <= 30) {
                $buckets['0_30'] += $balanceDelta;
            } elseif ($days <= 60) {
                $buckets['31_60'] += $balanceDelta;
            } elseif ($days <= 90) {
                $buckets['61_90'] += $balanceDelta;
            } else {
                $buckets['90_plus'] += $balanceDelta;
            }
        }

        return $buckets;
    }
}

