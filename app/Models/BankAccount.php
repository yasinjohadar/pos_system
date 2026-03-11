<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'name',
        'account_number',
        'branch_id',
        'opening_balance',
        'currency',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getActiveForSelect()
    {
        return static::where('is_active', true)->orderBy('name')->get();
    }
}
