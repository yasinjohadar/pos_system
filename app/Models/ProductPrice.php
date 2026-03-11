<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id',
        'branch_id',
        'price_type',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public const PRICE_TYPES = [
        'retail' => 'تجزئة',
        'wholesale' => 'جملة',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
