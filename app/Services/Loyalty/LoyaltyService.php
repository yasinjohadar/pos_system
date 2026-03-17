<?php

namespace App\Services\Loyalty;

use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Models\SaleInvoice;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    /** عدد النقاط لكل وحدة عملة (مثلاً نقطة واحدة) */
    private int $pointsPerCurrency = 1;

    /** المبلغ المطلوب بالعملة للحصول على نقطة واحدة */
    private int $currencyAmount = 100;

    public function earnPoints(SaleInvoice $invoice): void
    {
        $customer = $invoice->customer;
        if (!$customer) {
            return;
        }

        $total = (float) $invoice->total;
        $points = (int) floor($total / $this->currencyAmount) * $this->pointsPerCurrency;
        if ($points <= 0) {
            return;
        }

        $balanceBefore = (int) $customer->loyalty_points;
        $balanceAfter = $balanceBefore + $points;

        LoyaltyTransaction::create([
            'customer_id' => $customer->id,
            'type' => LoyaltyTransaction::TYPE_EARN,
            'points' => $points,
            'reference_type' => SaleInvoice::class,
            'reference_id' => $invoice->id,
            'description' => 'نقاط من فاتورة مبيعات #' . $invoice->number,
            'balance_after' => $balanceAfter,
        ]);

        $customer->update(['loyalty_points' => $balanceAfter]);
    }

    /**
     * استبدال النقاط بخصم على الفاتورة.
     *
     * @return float المبلغ المخصوم (بالعملة)
     */
    public function redeemPoints(Customer $customer, int $points, SaleInvoice $invoice): float
    {
        $balance = (int) $customer->loyalty_points;
        if ($points <= 0 || $points > $balance) {
            return 0.0;
        }

        $balanceAfter = $balance - $points;
        $discountAmount = $points * $this->currencyAmount; // تحويل النقاط لقيمة خصم

        DB::transaction(function () use ($customer, $points, $invoice, $balanceAfter, $discountAmount) {
            LoyaltyTransaction::create([
                'customer_id' => $customer->id,
                'type' => LoyaltyTransaction::TYPE_REDEEM,
                'points' => -$points,
                'reference_type' => SaleInvoice::class,
                'reference_id' => $invoice->id,
                'description' => 'استبدال نقاط على فاتورة #' . $invoice->number,
                'balance_after' => $balanceAfter,
            ]);
            $customer->update(['loyalty_points' => $balanceAfter]);
        });

        return (float) $discountAmount;
    }

    public function getCustomerBalance(Customer $customer): int
    {
        return (int) $customer->loyalty_points;
    }

    public function adjustPoints(Customer $customer, int $points, string $reason): void
    {
        $balance = (int) $customer->loyalty_points;
        $balanceAfter = $balance + $points;
        $balanceAfter = max(0, $balanceAfter);

        LoyaltyTransaction::create([
            'customer_id' => $customer->id,
            'type' => LoyaltyTransaction::TYPE_ADJUSTMENT,
            'points' => $points,
            'reference_type' => null,
            'reference_id' => null,
            'description' => $reason,
            'balance_after' => $balanceAfter,
        ]);

        $customer->update(['loyalty_points' => $balanceAfter]);
    }

    public function expirePoints(Customer $customer, int $points): void
    {
        $balance = (int) $customer->loyalty_points;
        $deduct = min($points, $balance);
        if ($deduct <= 0) {
            return;
        }

        $balanceAfter = $balance - $deduct;

        LoyaltyTransaction::create([
            'customer_id' => $customer->id,
            'type' => LoyaltyTransaction::TYPE_EXPIRE,
            'points' => -$deduct,
            'reference_type' => null,
            'reference_id' => null,
            'description' => 'انتهاء صلاحية نقاط',
            'balance_after' => $balanceAfter,
        ]);

        $customer->update(['loyalty_points' => $balanceAfter]);
    }
}
