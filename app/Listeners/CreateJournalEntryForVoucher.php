<?php

namespace App\Listeners;

use App\Events\CashVoucherCreated;
use App\Services\Accounting\AccountingService;

class CreateJournalEntryForVoucher
{
    public function __construct(
        protected AccountingService $accounting
    ) {}

    public function handle(CashVoucherCreated $event): void
    {
        $voucher = $event->cashVoucher;
        if ($voucher->type === \App\Models\CashVoucher::TYPE_RECEIPT) {
            $this->accounting->createReceiptVoucherEntry($voucher);
        } else {
            $this->accounting->createPaymentVoucherEntry($voucher);
        }
    }
}
