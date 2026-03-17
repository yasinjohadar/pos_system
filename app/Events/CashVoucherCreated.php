<?php

namespace App\Events;

use App\Models\CashVoucher;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashVoucherCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CashVoucher $cashVoucher
    ) {}
}
