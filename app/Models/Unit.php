<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'base_unit_id',
        'conversion_factor',
        'is_active',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function subUnits()
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
