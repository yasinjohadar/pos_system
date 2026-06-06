<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosShift extends Model
{
    protected $fillable = [
        'user_id', 'treasury_id', 'branch_id', 'opening_cash', 'closing_cash',
        'expected_cash', 'cash_difference', 'opened_at', 'closed_at', 'status', 'notes',
    ];

    protected $casts = [
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    public function user() { return $this->belongsTo(User::class); }
    public function treasury() { return $this->belongsTo(Treasury::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function heldSales() { return $this->hasMany(PosHeldSale::class); }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }
}
