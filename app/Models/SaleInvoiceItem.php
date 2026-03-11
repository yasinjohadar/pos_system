<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleInvoiceItem extends Model
{
    protected $fillable = [
        'sale_invoice_id',
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

    public function saleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class, 'sale_invoice_id');
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
        return $this->hasMany(SaleReturnItem::class, 'sale_invoice_item_id');
    }

    /**
     * الكمية المُرجعة من هذا البند.
     */
    public function getQuantityReturnedAttribute(): float
    {
        return (float) $this->returnItems()->whereHas('saleReturn', fn ($q) => $q->where('status', SaleReturn::STATUS_COMPLETED))->sum('quantity');
    }

    /**
     * الكمية المتبقية القابلة للمرتجع.
     */
    public function getQuantityRemainingAttribute(): float
    {
        return (float) $this->quantity - $this->quantity_returned;
    }
}
