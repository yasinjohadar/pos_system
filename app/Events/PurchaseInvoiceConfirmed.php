<?php

namespace App\Events;

use App\Models\PurchaseInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseInvoiceConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PurchaseInvoice $purchaseInvoice
    ) {}
}
