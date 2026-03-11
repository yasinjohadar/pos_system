<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CashVoucher extends Model
{
    protected $fillable = [
        'type',
        'voucher_number',
        'date',
        'treasury_id',
        'bank_account_id',
        'amount',
        'currency',
        'category',
        'description',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public const TYPE_RECEIPT = 'receipt';
    public const TYPE_PAYMENT = 'payment';

    public function treasury()
    {
        return $this->belongsTo(Treasury::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateNumber(): string
    {
        $prefix = 'CV-' . date('Ymd') . '-';
        $last = static::where('voucher_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('voucher_number');

        $seq = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    protected static function booted()
    {
        static::creating(function (CashVoucher $voucher) {
            if (!$voucher->date) {
                $voucher->date = Carbon::today();
            }
            if (!$voucher->fiscal_year_id && $voucher->date) {
                $fy = FiscalYear::forDate(Carbon::parse($voucher->date));
                if ($fy) {
                    $voucher->fiscal_year_id = $fy->id;
                }
            }
        });
    }
}

