<?php

namespace Database\Seeders;

use App\Models\Treasury;
use Illuminate\Database\Seeder;

class TreasurySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'خزنة رئيسية', 'type' => Treasury::TYPE_CASHBOX],
            ['name' => 'بنك رئيسي', 'type' => Treasury::TYPE_BANK],
        ];

        foreach ($items as $item) {
            Treasury::firstOrCreate(
                ['name' => $item['name']],
                ['type' => $item['type'], 'is_active' => true]
            );
        }
    }
}
