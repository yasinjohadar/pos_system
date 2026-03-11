<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $fillable = [
        'sale_invoice_id',
        'amount',
        'payment_method_id',
        'treasury_id',
        'payment_date',
        'reference',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function saleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class, 'sale_invoice_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function treasury()
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::saved(fn (SalePayment $p) => $p->saleInvoice?->updatePaymentStatus());
        static::deleted(fn (SalePayment $p) => $p->saleInvoice?->updatePaymentStatus());
    }
}
