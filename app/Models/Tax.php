<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'type',
        'rate',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function calculate(float $base): float
    {
        if ($this->type === 'fixed') {
            return min((float) $this->rate, $base);
        }

        return round($base * (float) $this->rate / 100, 2);
    }
}

