<?php

namespace App\Events;

use App\Models\SaleInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleInvoiceConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public SaleInvoice $saleInvoice
    ) {}
}
