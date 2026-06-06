<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesQuote extends Model
{
    protected $fillable = [
        'number', 'quote_date', 'valid_until', 'branch_id', 'customer_id', 'warehouse_id',
        'subtotal', 'tax_rate', 'tax_amount', 'discount_amount', 'total', 'status',
        'user_id', 'converted_invoice_id', 'notes',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_CANCELLED = 'cancelled';

    public function items() { return $this->hasMany(SalesQuoteItem::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function convertedInvoice() { return $this->belongsTo(SaleInvoice::class, 'converted_invoice_id'); }

    public static function generateNumber(): string
    {
        $prefix = 'SQ-' . date('Y') . '-';
        $last = static::where('number', 'like', $prefix . '%')->orderByDesc('id')->value('number');
        $seq = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('total');
        $taxAmount = round($subtotal * (float) $this->tax_rate / 100, 2);
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal - (float) $this->discount_amount + $taxAmount,
        ]);
    }
}
