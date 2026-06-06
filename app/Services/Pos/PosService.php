<?php

namespace App\Services\Pos;

use App\Models\Branch;
use App\Models\PosHeldSale;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceItem;
use App\Models\SalePayment;
use App\Models\Treasury;
use App\Models\Warehouse;
use App\Services\Settings\CompanySettingsService;
use Illuminate\Support\Facades\DB;

class PosService
{
    public function getOpenShift(int $userId): ?PosShift
    {
        return PosShift::where('user_id', $userId)
            ->where('status', PosShift::STATUS_OPEN)
            ->with(['treasury', 'branch'])
            ->first();
    }

    public function openShift(int $userId, int $treasuryId, float $openingCash, ?int $branchId = null): PosShift
    {
        $existing = $this->getOpenShift($userId);
        if ($existing) {
            return $existing;
        }

        return PosShift::create([
            'user_id' => $userId,
            'treasury_id' => $treasuryId,
            'branch_id' => $branchId,
            'opening_cash' => $openingCash,
            'opened_at' => now(),
            'status' => PosShift::STATUS_OPEN,
        ]);
    }

    public function closeShift(PosShift $shift, float $closingCash, ?string $notes = null): PosShift
    {
        $shift->update([
            'closing_cash' => $closingCash,
            'expected_cash' => (float) $shift->opening_cash,
            'cash_difference' => $closingCash - (float) $shift->opening_cash,
            'closed_at' => now(),
            'status' => PosShift::STATUS_CLOSED,
            'notes' => $notes,
        ]);

        return $shift;
    }

    public function holdSale(int $userId, ?int $shiftId, array $cart): PosHeldSale
    {
        return PosHeldSale::create([
            'user_id' => $userId,
            'pos_shift_id' => $shiftId,
            'reference' => PosHeldSale::generateReference(),
            'cart_data' => $cart,
        ]);
    }

    public function checkout(array $data, int $userId): SaleInvoice
    {
        $settings = app(CompanySettingsService::class)->getSettings();
        $defaultTaxRate = 0;
        if (!empty($settings['default_tax_id'])) {
            $tax = \App\Models\Tax::find($settings['default_tax_id']);
            $defaultTaxRate = $tax && $tax->type === 'percent' ? (float) $tax->rate : 0;
        }

        return DB::transaction(function () use ($data, $userId, $defaultTaxRate) {
            $branchId = $data['branch_id'];
            $warehouseId = $data['warehouse_id'];

            $invoice = SaleInvoice::create([
                'number' => SaleInvoice::generateNumber((int) $branchId),
                'invoice_date' => now()->toDateString(),
                'branch_id' => $branchId,
                'customer_id' => $data['customer_id'] ?? null,
                'warehouse_id' => $warehouseId,
                'subtotal' => 0,
                'tax_rate' => $data['tax_rate'] ?? $defaultTaxRate,
                'tax_amount' => 0,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_value' => $data['discount_value'] ?? 0,
                'discount_amount' => 0,
                'total' => 0,
                'payment_status' => SaleInvoice::PAYMENT_STATUS_PAID,
                'status' => SaleInvoice::STATUS_DRAFT,
                'user_id' => $userId,
                'notes' => 'POS',
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                SaleInvoiceItem::create([
                    'sale_invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total' => round($qty * $price, 2),
                ]);
            }

            $invoice->recalculateTotals();
            $invoice->confirm();

            foreach ($data['payments'] ?? [] as $payment) {
                SalePayment::create([
                    'sale_invoice_id' => $invoice->id,
                    'payment_method_id' => $payment['payment_method_id'],
                    'treasury_id' => $payment['treasury_id'] ?? null,
                    'amount' => $payment['amount'],
                    'payment_date' => now()->toDateString(),
                    'reference' => $payment['reference'] ?? null,
                    'user_id' => $userId,
                ]);
            }

            if (empty($data['payments'])) {
                SalePayment::create([
                    'sale_invoice_id' => $invoice->id,
                    'payment_method_id' => $data['payment_method_id'],
                    'treasury_id' => $data['treasury_id'] ?? null,
                    'amount' => $invoice->total,
                    'payment_date' => now()->toDateString(),
                    'user_id' => $userId,
                ]);
            }

            return $invoice->fresh(['items.product']);
        });
    }
}
