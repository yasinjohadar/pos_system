<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductBatch;

class StockMovement extends Model
{
    protected $fillable = [
        'type',
        'product_id',
        'batch_id',
        'warehouse_id',
        'quantity',
        'reference_type',
        'reference_id',
        'stock_transfer_id',
        'movement_date',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'movement_date' => 'date',
    ];

    /** أنواع الحركة التي تزيد الرصيد */
    public const TYPES_IN = ['in', 'transfer_in', 'adjustment', 'inventory_count', 'return_sale'];

    /** أنواع الحركة التي تنقص الرصيد */
    public const TYPES_OUT = ['out', 'transfer_out', 'return_purchase'];

    /** أنواع تسمح بكمية موجبة أو سالبة (دلتا) */
    public const TYPES_DELTA = ['adjustment', 'inventory_count'];

    public const TYPE_LABELS = [
        'in' => 'إدخال',
        'out' => 'صرف',
        'transfer_out' => 'تحويل (صرف)',
        'transfer_in' => 'تحويل (استلام)',
        'adjustment' => 'تسوية',
        'inventory_count' => 'جرد',
        'return_sale' => 'مرتجع بيع',
        'return_purchase' => 'مرتجع شراء',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class, 'stock_transfer_id');
    }

    /**
     * هل الحركة تزيد الرصيد؟
     */
    public function isIn(): bool
    {
        return in_array($this->type, self::TYPES_IN);
    }

    /**
     * إنشاء حركة وتحديث الرصيد.
     */
    public static function record(array $data): self
    {
        $quantity = (float) $data['quantity'];
        $type = $data['type'];

        if (in_array($type, self::TYPES_DELTA)) {
            // كما هو (موجب أو سالب)
        } elseif (in_array($type, self::TYPES_OUT)) {
            $quantity = -abs($quantity);
        } else {
            $quantity = abs($quantity);
        }

        $data['quantity'] = $quantity;
        $data['user_id'] = $data['user_id'] ?? auth()->id();

        $movement = self::create($data);

        StockBalance::updateBalance(
            (int) $movement->product_id,
            (int) $movement->warehouse_id,
            $movement->quantity
        );

        if ($movement->batch_id && in_array($type, self::TYPES_OUT)) {
            $batch = ProductBatch::find($movement->batch_id);
            if ($batch) {
                $batch->decrement('current_quantity', abs($movement->quantity));
            }
        }

        return $movement;
    }
}
