<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
    protected $fillable = [
        'sale_return_id',
        'sale_invoice_item_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id');
    }

    public function saleInvoiceItem()
    {
        return $this->belongsTo(SaleInvoiceItem::class, 'sale_invoice_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
