<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'number', 'order_date', 'expected_date', 'branch_id', 'supplier_id', 'warehouse_id',
        'subtotal', 'tax_rate', 'tax_amount', 'total', 'status',
        'user_id', 'converted_invoice_id', 'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_CANCELLED = 'cancelled';

    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function convertedInvoice() { return $this->belongsTo(PurchaseInvoice::class, 'converted_invoice_id'); }

    public static function generateNumber(): string
    {
        $prefix = 'PO-' . date('Y') . '-';
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
            'total' => $subtotal + $taxAmount,
        ]);
    }
}
