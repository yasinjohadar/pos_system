<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'return_number',
        'purchase_invoice_id',
        'return_date',
        'warehouse_id',
        'subtotal_refund',
        'tax_refund',
        'total_refund',
        'status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'subtotal_refund' => 'decimal:2',
        'tax_refund' => 'decimal:2',
        'total_refund' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id');
    }

    public static function generateReturnNumber(): string
    {
        $prefix = 'PRET-' . date('Ymd') . '-';
        $last = static::where('return_number', 'like', $prefix . '%')->orderByDesc('id')->value('return_number');
        $seq = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /** إكمال المرتجع: إنشاء حركات صرف مخزون (return_purchase) */
    public function complete(): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                StockMovement::record([
                    'type' => 'return_purchase',
                    'product_id' => $item->product_id,
                    'warehouse_id' => $this->warehouse_id,
                    'quantity' => $item->quantity,
                    'movement_date' => $this->return_date,
                    'reference_type' => 'purchase_return',
                    'reference_id' => $this->id,
                    'notes' => 'مرتجع شراء #' . $this->return_number,
                ]);
            }
            $this->update(['status' => self::STATUS_COMPLETED]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
