<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model
{
    protected $fillable = [
        'bank_account_id', 'statement_date', 'statement_balance', 'book_balance',
        'difference', 'status', 'user_id', 'reconciled_at', 'notes',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'statement_balance' => 'decimal:2',
        'book_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_RECONCILED = 'reconciled';

    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(BankReconciliationItem::class); }
}
