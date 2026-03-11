<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    protected $table = 'stock_balances';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * تحديث الرصيد من كمية معينة (موجبة أو سالبة).
     */
    public static function updateBalance(int $productId, int $warehouseId, float $quantityDelta): self
    {
        $balance = self::firstOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
            ],
            ['quantity' => 0]
        );

        $balance->increment('quantity', $quantityDelta);

        return $balance->fresh();
    }

    /**
     * تعيين الرصيد مباشرة (مثل بعد الجرد).
     */
    public static function setBalance(int $productId, int $warehouseId, float $quantity): self
    {
        $balance = self::firstOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
            ],
            ['quantity' => 0]
        );

        $balance->update(['quantity' => $quantity]);

        return $balance->fresh();
    }
}
