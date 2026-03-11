<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'number',
        'invoice_date',
        'branch_id',
        'supplier_id',
        'warehouse_id',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total',
        'payment_status',
        'status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PARTIAL = 'partial';
    public const PAYMENT_STATUS_PAID = 'paid';

    public const DISCOUNT_TYPE_FIXED = 'fixed';
    public const DISCOUNT_TYPE_PERCENT = 'percent';

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
        return $this->hasMany(PurchaseInvoiceItem::class, 'purchase_invoice_id');
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class, 'purchase_invoice_id');
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class, 'purchase_invoice_id');
    }

    /** إجمالي المدفوعات المرتبطة بهذه الفاتورة */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->supplierPayments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->total - $this->total_paid;
    }

    public static function generateNumber(int $branchId): string
    {
        $prefix = 'PUR-' . date('Ymd') . '-';
        $last = static::where('branch_id', $branchId)->where('number', 'like', $prefix . '%')->orderByDesc('id')->value('number');
        $seq = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    protected static function booted()
    {
        static::creating(function (PurchaseInvoice $invoice) {
            if (!$invoice->invoice_date) {
                $invoice->invoice_date = Carbon::today();
            }
            if (!$invoice->fiscal_year_id && $invoice->invoice_date) {
                $fy = FiscalYear::forDate(Carbon::parse($invoice->invoice_date));
                if ($fy) {
                    $invoice->fiscal_year_id = $fy->id;
                }
            }
        });
    }

    /** إعادة حساب الإجماليات من البنود */
    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('total');
        $discountAmount = 0;
        if ($this->discount_type === self::DISCOUNT_TYPE_PERCENT && $this->discount_value > 0) {
            $discountAmount = round($subtotal * (float) $this->discount_value / 100, 2);
        } elseif ($this->discount_type === self::DISCOUNT_TYPE_FIXED && $this->discount_value > 0) {
            $discountAmount = min((float) $this->discount_value, $subtotal);
        }
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = round($afterDiscount * (float) $this->tax_rate / 100, 2);
        $total = $afterDiscount + $taxAmount;

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->tax_amount = $taxAmount;
        $this->total = $total;
        $this->saveQuietly();
    }

    /** اعتماد الفاتورة: إنشاء حركات إدخال مخزون (in) */
    public function confirm(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                $warehouseId = $item->warehouse_id ?? $this->warehouse_id;
                StockMovement::record([
                    'type' => 'in',
                    'product_id' => $item->product_id,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $item->quantity,
                    'movement_date' => $this->invoice_date,
                    'reference_type' => 'purchase_invoice',
                    'reference_id' => $this->id,
                    'notes' => 'فاتورة شراء #' . $this->number,
                ]);
            }
            $this->update(['status' => self::STATUS_CONFIRMED]);
            $this->updatePaymentStatus();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updatePaymentStatus(): void
    {
        $total = (float) $this->total;
        $paid = (float) $this->supplierPayments()->sum('amount');
        $status = $total <= 0 ? self::PAYMENT_STATUS_PAID : ($paid >= $total ? self::PAYMENT_STATUS_PAID : ($paid > 0 ? self::PAYMENT_STATUS_PARTIAL : self::PAYMENT_STATUS_PENDING));
        $this->update(['payment_status' => $status]);
    }
}
