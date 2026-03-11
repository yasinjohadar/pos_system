<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    protected $fillable = [
        'check_number',
        'amount',
        'bank_account_id',
        'bank_name',
        'due_date',
        'status',
        'sale_payment_id',
        'supplier_payment_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public const STATUS_UNDER_COLLECTION = 'under_collection';
    public const STATUS_COLLECTED = 'collected';
    public const STATUS_RETURNED = 'returned';

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function salePayment()
    {
        return $this->belongsTo(SalePayment::class);
    }

    public function supplierPayment()
    {
        return $this->belongsTo(SupplierPayment::class);
    }

    public function getBankDisplayNameAttribute(): string
    {
        if ($this->bank_account_id && $this->relationLoaded('bankAccount')) {
            return $this->bankAccount->name ?? $this->bank_name ?? '—';
        }
        return $this->bank_name ?? '—';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_UNDER_COLLECTION => 'تحت التحصيل',
            self::STATUS_COLLECTED => 'محصل',
            self::STATUS_RETURNED => 'مرتجع',
            default => $this->status,
        };
    }
}
