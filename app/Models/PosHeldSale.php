<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosHeldSale extends Model
{
    protected $fillable = ['user_id', 'pos_shift_id', 'reference', 'cart_data'];

    protected $casts = ['cart_data' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function posShift() { return $this->belongsTo(PosShift::class); }

    public static function generateReference(): string
    {
        return 'HOLD-' . date('His') . '-' . random_int(100, 999);
    }
}
