<?php

namespace App\Services\Pricing;

use App\Models\Customer;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\Product;
use App\Models\Promotion;
use Carbon\Carbon;

class PricingService
{
    /**
     * حساب سعر بند فاتورة بيع واحد مع الأخذ في الاعتبار:
     * - قائمة أسعار العميل إن وُجدت.
     * - العروض النشطة على المنتج.
     *
     * @return array{unit_price: float, discount_per_unit: float, final_unit_price: float, line_total: float, discount_source: ?string}
     */
    public function calculateItemPrice(Product $product, ?Customer $customer, float $quantity, ?int $branchId = null): array
    {
        $quantity = max($quantity, 0.0);

        $priceList = $this->getCustomerPriceList($customer);
        $baseUnitPrice = $this->getBaseUnitPrice($product, $priceList, $branchId);

        $discountPerUnit = 0.0;
        $discountSource = null;

        // تطبيق خصم من قائمة الأسعار (إن وُجد سعر خاص)
        if ($priceList) {
            $specialPrice = $this->getPriceListUnitPrice($product, $priceList);
            if ($specialPrice !== null && $specialPrice < $baseUnitPrice) {
                $discountPerUnit = $baseUnitPrice - $specialPrice;
                $baseUnitPrice = $specialPrice;
                $discountSource = 'price_list';
            }
        }

        // تطبيق العروض النشطة على المنتج
        $promotionResult = $this->applyPromotion($product, $quantity, $baseUnitPrice);
        if ($promotionResult['discount_per_unit'] > 0) {
            $baseUnitPrice -= $promotionResult['discount_per_unit'];
            $discountPerUnit += $promotionResult['discount_per_unit'];
            $discountSource = $promotionResult['source'];
        }

        $finalUnitPrice = max($baseUnitPrice, 0.0);
        $lineTotal = round($finalUnitPrice * $quantity, 2);

        return [
            'unit_price' => round($finalUnitPrice, 2),
            'discount_per_unit' => round($discountPerUnit, 4),
            'final_unit_price' => round($finalUnitPrice, 2),
            'line_total' => $lineTotal,
            'discount_source' => $discountSource,
        ];
    }

    protected function getCustomerPriceList(?Customer $customer): ?PriceList
    {
        if (!$customer || !$customer->price_list_id) {
            return null;
        }

        $priceList = $customer->priceList;
        if (!$priceList || !$priceList->is_active) {
            return null;
        }

        return $priceList;
    }

    /**
     * السعر الأساسي قبل أي خصومات (من قائمة الأسعار أو من إعدادات المنتج/الفرع).
     */
    protected function getBaseUnitPrice(Product $product, ?PriceList $priceList, ?int $branchId): float
    {
        if ($priceList) {
            $special = $this->getPriceListUnitPrice($product, $priceList);
            if ($special !== null) {
                return $special;
            }
        }

        return (float) $product->getPriceForBranch($branchId, 'retail');
    }

    protected function getPriceListUnitPrice(Product $product, PriceList $priceList): ?float
    {
        /** @var PriceListItem|null $item */
        $item = $priceList->items()
            ->where('product_id', $product->id)
            ->first();

        return $item ? (float) $item->price : null;
    }

    /**
     * تطبيق أول عرض نشط مناسب على المنتج والكمية.
     *
     * @return array{discount_per_unit: float, source: ?string}
     */
    protected function applyPromotion(Product $product, float $quantity, float $currentUnitPrice): array
    {
        $today = Carbon::today();

        /** @var Promotion|null $promotion */
        $promotion = Promotion::where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->orderBy('id')
            ->first();

        if (!$promotion) {
            return ['discount_per_unit' => 0.0, 'source' => null];
        }

        if ($promotion->min_qty !== null && $quantity < (float) $promotion->min_qty) {
            return ['discount_per_unit' => 0.0, 'source' => null];
        }

        $discountPerUnit = 0.0;
        if ($promotion->type === 'percent') {
            $discountPerUnit = $currentUnitPrice * ((float) $promotion->value / 100);
        } elseif ($promotion->type === 'fixed') {
            $discountPerUnit = min((float) $promotion->value, $currentUnitPrice);
        }

        if ($discountPerUnit <= 0) {
            return ['discount_per_unit' => 0.0, 'source' => null];
        }

        return [
            'discount_per_unit' => $discountPerUnit,
            'source' => 'promotion',
        ];
    }
}

