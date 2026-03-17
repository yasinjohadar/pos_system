<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    public function saleInvoices()
    {
        return $this->hasMany(SaleInvoice::class, 'coupon_id');
    }

    /**
     * التحقق من صلاحية الكوبون وحساب قيمة الخصم.
     *
     * @return array{valid: bool, discount: float, message: string}
     */
    public static function validateAndGetDiscount(string $code, float $subtotal): array
    {
        $coupon = static::where('code', $code)->where('is_active', true)->first();
        if (!$coupon) {
            return ['valid' => false, 'discount' => 0.0, 'message' => 'كود الخصم غير صالح أو منتهي.'];
        }
        if ($coupon->valid_from && today()->lt($coupon->valid_from)) {
            return ['valid' => false, 'discount' => 0.0, 'message' => 'الكوبون غير مفعّل حتى ' . $coupon->valid_from->format('Y-m-d')];
        }
        if ($coupon->valid_until && today()->gt($coupon->valid_until)) {
            return ['valid' => false, 'discount' => 0.0, 'message' => 'انتهت صلاحية الكوبون.'];
        }
        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
            return ['valid' => false, 'discount' => 0.0, 'message' => 'تم استنفاد عدد استخدامات هذا الكوبون.'];
        }
        if ($coupon->min_purchase !== null && $subtotal < (float) $coupon->min_purchase) {
            return ['valid' => false, 'discount' => 0.0, 'message' => 'الحد الأدنى للطلب لاستخدام هذا الكوبون: ' . number_format($coupon->min_purchase, 2)];
        }
        $discount = 0.0;
        if ($coupon->type === self::TYPE_PERCENT) {
            $discount = round($subtotal * (float) $coupon->value / 100, 2);
        } else {
            $discount = min((float) $coupon->value, $subtotal);
        }
        return ['valid' => true, 'discount' => $discount, 'coupon_id' => $coupon->id, 'message' => 'تم تطبيق الخصم: ' . number_format($discount, 2)];
    }
}
