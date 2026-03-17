<?php

namespace App\Listeners;

use App\Events\SaleInvoiceConfirmed;
use App\Services\Accounting\AccountingService;

class CreateJournalEntryForSaleInvoice
{
    public function __construct(
        protected AccountingService $accounting
    ) {}

    public function handle(SaleInvoiceConfirmed $event): void
    {
        $this->accounting->createSaleInvoiceEntry($event->saleInvoice);
    }
}
