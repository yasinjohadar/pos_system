<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'unit_id',
        'tax_id',
        'name',
        'slug',
        'barcode',
        'description',
        'base_price',
        'cost_price',
        'image',
        'min_stock_alert',
        'reorder_level',
        'max_level',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'min_stock_alert' => 'integer',
        'reorder_level' => 'decimal:4',
        'max_level' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function stockBalances()
    {
        return $this->hasMany(StockBalance::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get effective price for a branch and type (or base_price if no override).
     */
    public function getPriceForBranch(?int $branchId = null, string $priceType = 'retail'): float
    {
        $price = $this->prices()
            ->where('price_type', $priceType)
            ->when($branchId !== null, fn ($q) => $q->where('branch_id', $branchId))
            ->when($branchId === null, fn ($q) => $q->whereNull('branch_id'))
            ->value('value');

        if ($price !== null) {
            return (float) $price;
        }

        if ($branchId !== null) {
            $defaultPrice = $this->prices()
                ->where('price_type', $priceType)
                ->whereNull('branch_id')
                ->value('value');
            if ($defaultPrice !== null) {
                return (float) $defaultPrice;
            }
        }

        return (float) $this->base_price;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
