<?php

namespace App\Observers;

use App\Models\SaleInvoice;
use App\Services\Audit\AuditService;
use App\Events\SaleInvoiceConfirmed;

class SaleInvoiceObserver
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function created(SaleInvoice $saleInvoice): void
    {
        $this->audit->logCreate($saleInvoice);
    }

    public function updated(SaleInvoice $saleInvoice): void
    {
        $oldStatus = $saleInvoice->getOriginal('status');
        $newStatus = $saleInvoice->status;

        if ($oldStatus === SaleInvoice::STATUS_DRAFT && $newStatus === SaleInvoice::STATUS_CONFIRMED) {
            $this->audit->logConfirm($saleInvoice, $this->snapshot($saleInvoice->getOriginal()));
            SaleInvoiceConfirmed::dispatch($saleInvoice);
            return;
        }

        if ($oldStatus !== $newStatus && $newStatus === SaleInvoice::STATUS_CANCELLED) {
            $this->audit->logCancel($saleInvoice, $this->snapshot($saleInvoice->getOriginal()));
            return;
        }

        $old = $this->snapshot($saleInvoice->getOriginal());
        $new = $this->snapshot($saleInvoice->getAttributes());
        if ($old !== $new) {
            $this->audit->logUpdate($saleInvoice, $old, $new);
        }
    }

    public function deleted(SaleInvoice $saleInvoice): void
    {
        $this->audit->logDelete($saleInvoice);
    }

    private function snapshot(array $attrs): array
    {
        $keys = ['number', 'invoice_date', 'branch_id', 'customer_id', 'warehouse_id', 'total', 'status', 'payment_status'];
        return array_intersect_key($attrs, array_flip($keys));
    }
}
