<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'batch_number',
        'expiry_date',
        'initial_quantity',
        'current_quantity',
        'cost_price',
        'received_date',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_date' => 'date',
        'initial_quantity' => 'decimal:4',
        'current_quantity' => 'decimal:4',
        'cost_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'batch_id');
    }

    public function hasQuantity(float $quantity = 0): bool
    {
        return (float) $this->current_quantity >= $quantity;
    }
}
