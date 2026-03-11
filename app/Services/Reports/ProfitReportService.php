<?php

namespace App\Services\Reports;

use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\CashVoucher;
use Carbon\Carbon;

class ProfitReportService
{
    /**
     * ملخص أرباح لفترة محددة.
     *
     * @return array{
     *  from: string,
     *  to: string,
     *  sales_total: float,
     *  sales_returns: float,
     *  purchases_total: float,
     *  purchase_returns: float,
     *  vouchers_receipts: float,
     *  vouchers_payments: float,
     *  gross_profit: float
     * }
     */
    public function getProfitSummary(Carbon $from, Carbon $to, ?int $branchId = null): array
    {
        $salesQuery = SaleInvoice::query()
            ->whereBetween('invoice_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', SaleInvoice::STATUS_CONFIRMED);

        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }

        $salesTotal = (float) $salesQuery->sum('total');

        $salesReturnsQuery = SaleReturn::query()
            ->whereBetween('return_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', SaleReturn::STATUS_COMPLETED);

        if ($branchId) {
            $salesReturnsQuery->whereHas('saleInvoice', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $salesReturns = (float) $salesReturnsQuery->sum('total_refund');

        $purchaseQuery = PurchaseInvoice::query()
            ->whereBetween('invoice_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', PurchaseInvoice::STATUS_CONFIRMED);

        if ($branchId) {
            $purchaseQuery->where('branch_id', $branchId);
        }

        $purchasesTotal = (float) $purchaseQuery->sum('total');

        $purchaseReturnsQuery = PurchaseReturn::query()
            ->whereBetween('return_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', PurchaseReturn::STATUS_COMPLETED);

        if ($branchId) {
            $purchaseReturnsQuery->whereHas('purchaseInvoice', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $purchaseReturns = (float) $purchaseReturnsQuery->sum('total_refund');

        $netSales = $salesTotal - $salesReturns;
        $netPurchases = $purchasesTotal - $purchaseReturns;

        $receipts = (float) CashVoucher::where('type', CashVoucher::TYPE_RECEIPT)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');

        $payments = (float) CashVoucher::where('type', CashVoucher::TYPE_PAYMENT)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');

        $grossProfit = $netSales - $netPurchases + $receipts - $payments;

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'sales_total' => $salesTotal,
            'sales_returns' => $salesReturns,
            'purchases_total' => $purchasesTotal,
            'purchase_returns' => $purchaseReturns,
            'vouchers_receipts' => $receipts,
            'vouchers_payments' => $payments,
            'gross_profit' => $grossProfit,
        ];
    }
}

