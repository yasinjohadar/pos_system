<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id', 'product_id', 'quantity', 'unit_price', 'total'];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function order() { return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id'); }
}
