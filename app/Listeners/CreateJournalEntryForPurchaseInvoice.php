<?php

namespace App\Listeners;

use App\Events\PurchaseInvoiceConfirmed;
use App\Services\Accounting\AccountingService;

class CreateJournalEntryForPurchaseInvoice
{
    public function __construct(
        protected AccountingService $accounting
    ) {}

    public function handle(PurchaseInvoiceConfirmed $event): void
    {
        $this->accounting->createPurchaseInvoiceEntry($event->purchaseInvoice);
    }
}
