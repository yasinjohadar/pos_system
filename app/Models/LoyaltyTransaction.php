<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    public const TYPE_EARN = 'earn';
    public const TYPE_REDEEM = 'redeem';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_EXPIRE = 'expire';

    protected $fillable = [
        'customer_id',
        'type',
        'points',
        'reference_type',
        'reference_id',
        'description',
        'balance_after',
    ];

    protected $casts = [
        'points' => 'integer',
        'balance_after' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
