<?php

namespace App\Observers;

use App\Models\CashVoucher;
use App\Services\Audit\AuditService;
use App\Events\CashVoucherCreated;

class CashVoucherObserver
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function created(CashVoucher $cashVoucher): void
    {
        $this->audit->logCreate($cashVoucher);
        CashVoucherCreated::dispatch($cashVoucher);
    }

    public function updated(CashVoucher $cashVoucher): void
    {
        $old = $this->snapshot($cashVoucher->getOriginal());
        $new = $this->snapshot($cashVoucher->getAttributes());
        if ($old !== $new) {
            $this->audit->logUpdate($cashVoucher, $old, $new);
        }
    }

    public function deleted(CashVoucher $cashVoucher): void
    {
        $this->audit->logDelete($cashVoucher);
    }

    private function snapshot(array $attrs): array
    {
        $keys = ['type', 'voucher_number', 'date', 'treasury_id', 'amount', 'category', 'description'];
        return array_intersect_key($attrs, array_flip($keys));
    }
}
