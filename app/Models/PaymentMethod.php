<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'code', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public const CODE_CASH = 'cash';
    public const CODE_CARD = 'card';
    public const CODE_TRANSFER = 'transfer';
    public const CODE_CREDIT = 'credit';

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class, 'payment_method_id');
    }

    public static function getActiveForSelect()
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }
}
