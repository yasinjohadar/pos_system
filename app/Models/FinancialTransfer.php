<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransfer extends Model
{
    protected $fillable = [
        'from_treasury_id',
        'from_bank_account_id',
        'to_treasury_id',
        'to_bank_account_id',
        'amount',
        'transfer_date',
        'reference',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transfer_date' => 'date',
    ];

    public function fromTreasury()
    {
        return $this->belongsTo(Treasury::class, 'from_treasury_id');
    }

    public function fromBankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'from_bank_account_id');
    }

    public function toTreasury()
    {
        return $this->belongsTo(Treasury::class, 'to_treasury_id');
    }

    public function toBankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'to_bank_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** مصدر التحويل (خزنة أو حساب بنكي) */
    public function getFromSourceNameAttribute(): string
    {
        if ($this->from_treasury_id) {
            return ($this->fromTreasury->name ?? '—') . ' (خزنة/بنك)';
        }
        if ($this->from_bank_account_id) {
            return $this->fromBankAccount->name ?? '—';
        }
        return '—';
    }

    /** وجهة التحويل */
    public function getToTargetNameAttribute(): string
    {
        if ($this->to_treasury_id) {
            return ($this->toTreasury->name ?? '—') . ' (خزنة/بنك)';
        }
        if ($this->to_bank_account_id) {
            return $this->toBankAccount->name ?? '—';
        }
        return '—';
    }
}
