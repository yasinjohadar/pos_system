<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\CashVoucher;
use App\Models\Treasury;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CashVoucherSeeder extends Seeder
{
    public function run(): void
    {
        if (CashVoucher::exists()) {
            $this->command?->info('CashVoucherSeeder: سندات موجودة — تخطي.');

            return;
        }

        $user = User::query()->first();
        $treasuries = Treasury::where('is_active', true)->orderBy('id')->get();
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('id')->get();

        if (! $user || ($treasuries->isEmpty() && $bankAccounts->isEmpty())) {
            $this->command?->warn('CashVoucherSeeder: يتطلب مستخدماً وخزنة أو حساب بنكي.');

            return;
        }

        $scenarios = [
            ['type' => CashVoucher::TYPE_RECEIPT, 'days' => 1, 'amount' => 1500, 'category' => 'إيرادات أخرى', 'description' => 'قبض نقدي من عميل', 'source' => 'treasury'],
            ['type' => CashVoucher::TYPE_RECEIPT, 'days' => 3, 'amount' => 3200.50, 'category' => 'مبيعات', 'description' => 'تحصيل مبيعات نقدية', 'source' => 'treasury'],
            ['type' => CashVoucher::TYPE_RECEIPT, 'days' => 5, 'amount' => 8750, 'category' => 'إيرادات أخرى', 'description' => 'إيداع بنكي', 'source' => 'bank'],
            ['type' => CashVoucher::TYPE_PAYMENT, 'days' => 2, 'amount' => 450, 'category' => 'مصروفات تشغيل', 'description' => 'مصروفات مكتبية', 'source' => 'treasury'],
            ['type' => CashVoucher::TYPE_PAYMENT, 'days' => 4, 'amount' => 1200, 'category' => 'رواتب', 'description' => 'سلفة موظف', 'source' => 'treasury'],
            ['type' => CashVoucher::TYPE_PAYMENT, 'days' => 6, 'amount' => 5600, 'category' => 'موردين', 'description' => 'دفعة مورد', 'source' => 'bank'],
            ['type' => CashVoucher::TYPE_RECEIPT, 'days' => 8, 'amount' => 2100, 'category' => 'مبيعات', 'description' => 'قبض فاتورة نقدية', 'source' => 'treasury'],
            ['type' => CashVoucher::TYPE_PAYMENT, 'days' => 10, 'amount' => 980, 'category' => 'مصروفات تشغيل', 'description' => 'فاتورة كهرباء', 'source' => 'treasury'],
            ['type' => CashVoucher::TYPE_RECEIPT, 'days' => 12, 'amount' => 15000, 'category' => 'إيرادات أخرى', 'description' => 'تحويل وارد من البنك', 'source' => 'bank'],
            ['type' => CashVoucher::TYPE_PAYMENT, 'days' => 14, 'amount' => 3400, 'category' => 'إيجار', 'description' => 'إيجار المحل', 'source' => 'bank'],
            ['type' => CashVoucher::TYPE_RECEIPT, 'days' => 0, 'amount' => 750, 'category' => 'مبيعات', 'description' => 'قبض يومي', 'source' => 'treasury'],
            ['type' => CashVoucher::TYPE_PAYMENT, 'days' => 0, 'amount' => 200, 'category' => 'مصروفات تشغيل', 'description' => 'مصروف نثرية', 'source' => 'treasury'],
        ];

        foreach ($scenarios as $index => $scenario) {
            $date = Carbon::today()->subDays($scenario['days']);
            $treasury = null;
            $bankAccount = null;

            if ($scenario['source'] === 'treasury' && $treasuries->isNotEmpty()) {
                $treasury = $treasuries[$index % $treasuries->count()];
            } elseif ($scenario['source'] === 'bank' && $bankAccounts->isNotEmpty()) {
                $bankAccount = $bankAccounts[$index % $bankAccounts->count()];
            }

            if (! $treasury && ! $bankAccount) {
                if ($treasuries->isNotEmpty()) {
                    $treasury = $treasuries->first();
                } elseif ($bankAccounts->isNotEmpty()) {
                    $bankAccount = $bankAccounts->first();
                } else {
                    continue;
                }
            }

            $dateKey = $date->format('Ymd');
            $seq = str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            CashVoucher::create([
                'type' => $scenario['type'],
                'voucher_number' => 'CV-' . $dateKey . '-' . $seq,
                'date' => $date,
                'treasury_id' => $treasury?->id,
                'bank_account_id' => $bankAccount?->id,
                'amount' => $scenario['amount'],
                'currency' => 'SAR',
                'category' => $scenario['category'],
                'description' => $scenario['description'],
                'user_id' => $user->id,
                'notes' => 'بيانات تجريبية من CashVoucherSeeder',
            ]);
        }

        $this->command?->info('CashVoucherSeeder: تم إنشاء ' . count($scenarios) . ' سند قبض/صرف.');
    }
}
