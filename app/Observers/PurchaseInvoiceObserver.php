<?php

namespace App\Observers;

use App\Models\PurchaseInvoice;
use App\Services\Audit\AuditService;
use App\Events\PurchaseInvoiceConfirmed;

class PurchaseInvoiceObserver
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function created(PurchaseInvoice $purchaseInvoice): void
    {
        $this->audit->logCreate($purchaseInvoice);
    }

    public function updated(PurchaseInvoice $purchaseInvoice): void
    {
        $oldStatus = $purchaseInvoice->getOriginal('status');
        $newStatus = $purchaseInvoice->status;

        if ($oldStatus === PurchaseInvoice::STATUS_DRAFT && $newStatus === PurchaseInvoice::STATUS_CONFIRMED) {
            $this->audit->logConfirm($purchaseInvoice, $this->snapshot($purchaseInvoice->getOriginal()));
            PurchaseInvoiceConfirmed::dispatch($purchaseInvoice);
            return;
        }

        if ($oldStatus !== $newStatus && $newStatus === PurchaseInvoice::STATUS_CANCELLED) {
            $this->audit->logCancel($purchaseInvoice, $this->snapshot($purchaseInvoice->getOriginal()));
            return;
        }

        $old = $this->snapshot($purchaseInvoice->getOriginal());
        $new = $this->snapshot($purchaseInvoice->getAttributes());
        if ($old !== $new) {
            $this->audit->logUpdate($purchaseInvoice, $old, $new);
        }
    }

    public function deleted(PurchaseInvoice $purchaseInvoice): void
    {
        $this->audit->logDelete($purchaseInvoice);
    }

    private function snapshot(array $attrs): array
    {
        $keys = ['number', 'invoice_date', 'branch_id', 'supplier_id', 'warehouse_id', 'total', 'status', 'payment_status'];
        return array_intersect_key($attrs, array_flip($keys));
    }
}
