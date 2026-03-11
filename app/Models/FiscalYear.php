<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'is_closed',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_closed', false);
    }

    public function containsDate(Carbon $date): bool
    {
        return $date->betweenIncluded($this->start_date, $this->end_date);
    }

    public static function forDate(Carbon $date): ?self
    {
        return static::active()
            ->where('start_date', '<=', $date->toDateString())
            ->where('end_date', '>=', $date->toDateString())
            ->orderByDesc('start_date')
            ->first();
    }
}

