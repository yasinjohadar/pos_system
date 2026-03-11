<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency_id',
        'rate',
        'valid_from',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'valid_from' => 'date',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public static function getRateForDate(int $currencyId, Carbon $date): float
    {
        $record = static::where('currency_id', $currencyId)
            ->where('valid_from', '<=', $date->toDateString())
            ->orderByDesc('valid_from')
            ->first();

        return $record ? (float) $record->rate : 1.0;
    }
}

