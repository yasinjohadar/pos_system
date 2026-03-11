<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'نقدي', 'code' => PaymentMethod::CODE_CASH, 'sort_order' => 1],
            ['name' => 'بطاقة', 'code' => PaymentMethod::CODE_CARD, 'sort_order' => 2],
            ['name' => 'تحويل بنكي', 'code' => PaymentMethod::CODE_TRANSFER, 'sort_order' => 3],
            ['name' => 'آجل', 'code' => PaymentMethod::CODE_CREDIT, 'sort_order' => 4],
        ];

        foreach ($methods as $m) {
            PaymentMethod::firstOrCreate(
                ['code' => $m['code']],
                ['name' => $m['name'], 'sort_order' => $m['sort_order'], 'is_active' => true]
            );
        }
    }
}
