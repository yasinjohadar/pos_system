<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Collection;

class BatchService
{
    public function createBatch(Product $product, Warehouse $warehouse, array $data): ProductBatch
    {
        $batch = ProductBatch::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'batch_number' => $data['batch_number'],
            'expiry_date' => isset($data['expiry_date']) ? $data['expiry_date'] : null,
            'initial_quantity' => (float) ($data['initial_quantity'] ?? 0),
            'current_quantity' => (float) ($data['initial_quantity'] ?? 0),
            'cost_price' => isset($data['cost_price']) ? (float) $data['cost_price'] : null,
            'received_date' => $data['received_date'] ?? now()->toDateString(),
            'notes' => $data['notes'] ?? null,
        ]);

        if ($batch->current_quantity > 0) {
            StockMovement::record([
                'type' => 'in',
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'batch_id' => $batch->id,
                'quantity' => $batch->current_quantity,
                'movement_date' => $batch->received_date,
                'reference_type' => 'product_batch',
                'reference_id' => $batch->id,
                'notes' => 'استلام دفعة: ' . $batch->batch_number,
            ]);
        }

        return $batch;
    }

    /**
     * @return Collection|ProductBatch[]
     */
    public function getAvailableBatches(Product $product, Warehouse $warehouse): Collection
    {
        return ProductBatch::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('current_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->orderBy('received_date')
            ->get();
    }

    public function getFIFOBatch(Product $product, Warehouse $warehouse, float $quantity): ?ProductBatch
    {
        $batches = $this->getAvailableBatches($product, $warehouse);
        foreach ($batches as $batch) {
            if ((float) $batch->current_quantity >= $quantity) {
                return $batch;
            }
        }
        return null;
    }

    public function deductFromBatch(ProductBatch $batch, float $quantity): void
    {
        $qty = min((float) $batch->current_quantity, abs($quantity));
        if ($qty <= 0) {
            return;
        }
        $batch->decrement('current_quantity', $qty);
    }

    /**
     * @return Collection|ProductBatch[]
     */
    public function getExpiringBatches(int $daysThreshold = 30): Collection
    {
        return ProductBatch::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($daysThreshold))
            ->where('current_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->with(['product', 'warehouse'])
            ->get();
    }
}
