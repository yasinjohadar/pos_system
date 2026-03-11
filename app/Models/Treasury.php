<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treasury extends Model
{
    protected $fillable = ['name', 'type', 'branch_id', 'opening_balance', 'currency', 'is_active', 'notes'];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
    ];

    public const TYPE_CASHBOX = 'cashbox';
    public const TYPE_BANK = 'bank';

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class, 'treasury_id');
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class, 'treasury_id');
    }

    public static function getActiveForSelect()
    {
        return static::where('is_active', true)->orderBy('type')->orderBy('name')->get();
    }
}
