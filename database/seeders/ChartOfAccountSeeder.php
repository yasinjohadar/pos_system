<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * حسابات افتراضية للقيود التلقائية (يجب أن تبقى الأكواد كما هي للخدمة).
     */
    public function run(): void
    {
        $accounts = [
            ['code' => '1100', 'name' => 'الصندوق والبنوك', 'type' => 'asset', 'level' => 1],
            ['code' => '1200', 'name' => 'العملاء', 'type' => 'asset', 'level' => 1],
            ['code' => '2100', 'name' => 'الموردون', 'type' => 'liability', 'level' => 1],
            ['code' => '2200', 'name' => 'ضريبة المبيعات', 'type' => 'liability', 'level' => 1],
            ['code' => '3100', 'name' => 'رأس المال', 'type' => 'equity', 'level' => 1],
            ['code' => '4100', 'name' => 'المبيعات', 'type' => 'revenue', 'level' => 1],
            ['code' => '5100', 'name' => 'المشتريات', 'type' => 'expense', 'level' => 1],
            ['code' => '5200', 'name' => 'مصروفات عامة', 'type' => 'expense', 'level' => 1],
        ];

        foreach ($accounts as $row) {
            ChartOfAccount::firstOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'level' => $row['level'],
                    'is_active' => true,
                ]
            );
        }
    }
}
