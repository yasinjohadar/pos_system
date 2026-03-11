<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'start_date',
        'end_date',
        'min_invoice_amount',
        'min_qty',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'min_invoice_amount' => 'decimal:4',
        'min_qty' => 'decimal:4',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PromotionItem::class);
    }
}

