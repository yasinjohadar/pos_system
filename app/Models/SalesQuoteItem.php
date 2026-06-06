<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesQuoteItem extends Model
{
    protected $fillable = ['sales_quote_id', 'product_id', 'quantity', 'unit_price', 'total'];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function quote() { return $this->belongsTo(SalesQuote::class, 'sales_quote_id'); }
}
