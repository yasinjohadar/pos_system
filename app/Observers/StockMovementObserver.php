<?php

namespace App\Observers;

use App\Models\StockMovement;
use App\Services\Audit\AuditService;

class StockMovementObserver
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function created(StockMovement $stockMovement): void
    {
        $this->audit->logCreate($stockMovement);
    }

    public function deleted(StockMovement $stockMovement): void
    {
        $this->audit->logDelete($stockMovement);
    }
}
