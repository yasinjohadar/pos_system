<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use Illuminate\Database\Seeder;

class FiscalYearSeeder extends Seeder
{
    public function run(): void
    {
        if (FiscalYear::exists()) {
            $this->command?->info('FiscalYearSeeder: سنوات موجودة — تخطي.');

            return;
        }

        $years = [
            [
                'name' => 'السنة المالية 2024',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'is_active' => false,
                'is_closed' => true,
            ],
            [
                'name' => 'السنة المالية 2025',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'is_active' => false,
                'is_closed' => true,
            ],
            [
                'name' => 'السنة المالية 2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'is_active' => true,
                'is_closed' => false,
            ],
        ];

        foreach ($years as $year) {
            FiscalYear::create($year);
        }

        $this->command?->info('FiscalYearSeeder: تم إنشاء ' . count($years) . ' سنوات مالية.');
    }
}
