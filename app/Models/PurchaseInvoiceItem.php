<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $fillable = [
        'purchase_invoice_id',
        'product_id',
        'warehouse_id',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function returnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_invoice_item_id');
    }

    public function getQuantityReturnedAttribute(): float
    {
        return (float) $this->returnItems()->whereHas('purchaseReturn', fn ($q) => $q->where('status', PurchaseReturn::STATUS_COMPLETED))->sum('quantity');
    }

    public function getQuantityRemainingAttribute(): float
    {
        return (float) $this->quantity - $this->quantity_returned;
    }
}
