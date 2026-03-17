<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductPrice;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LargeMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        try {
            // Truncate in dependency-safe order
            ProductBarcode::truncate();
            ProductPrice::truncate();
            Product::truncate();
            Warehouse::truncate();
            Branch::truncate();
            Category::truncate();
            Unit::truncate();
            Tax::truncate();
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $faker = \Faker\Factory::create('ar_SA');

        // ===== Taxes =====
        $taxes = collect([
            ['name' => 'بدون ضريبة', 'type' => 'percent', 'rate' => 0, 'is_active' => true],
            ['name' => 'ضريبة 5%', 'type' => 'percent', 'rate' => 5, 'is_active' => true],
            ['name' => 'ضريبة 15%', 'type' => 'percent', 'rate' => 15, 'is_active' => true],
        ])->map(fn ($t) => Tax::create($t));

        // ===== Branches + Warehouses =====
        $branchCount = 8;
        $warehousesPerBranch = 4;

        $branches = collect();
        for ($i = 1; $i <= $branchCount; $i++) {
            $branch = Branch::create([
                'name' => 'فرع ' . $i . ' - ' . $faker->city(),
                'code' => 'BR' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'phone' => $faker->phoneNumber(),
                'email' => 'branch' . $i . '@example.com',
                'address' => $faker->address(),
                'settings' => [
                    'currency' => 'SAR',
                ],
                'is_active' => true,
            ]);
            $branches->push($branch);

            for ($w = 1; $w <= $warehousesPerBranch; $w++) {
                Warehouse::create([
                    'branch_id' => $branch->id,
                    'name' => ($w === 1 ? 'المخزن الرئيسي' : 'مخزن ' . $w) . ' - ' . $branch->code,
                    'code' => $branch->code . '-WH' . $w,
                    'address' => $faker->address(),
                    'is_default' => $w === 1,
                    'is_active' => true,
                ]);
            }
        }

        // ===== Units (base + sub-units) =====
        $baseUnits = collect([
            ['name' => 'قطعة', 'symbol' => 'pcs'],
            ['name' => 'كيلوجرام', 'symbol' => 'kg'],
            ['name' => 'لتر', 'symbol' => 'L'],
            ['name' => 'علبة', 'symbol' => 'box'],
        ])->map(fn ($u) => Unit::create([
            'name' => $u['name'],
            'symbol' => $u['symbol'],
            'base_unit_id' => null,
            'conversion_factor' => 1,
            'is_active' => true,
        ]));

        // Sub-units for "قطعة" و "علبة"
        $pieceUnit = $baseUnits->firstWhere('name', 'قطعة');
        $boxUnit = $baseUnits->firstWhere('name', 'علبة');

        if ($pieceUnit) {
            Unit::create([
                'name' => 'دزينة (12 قطعة)',
                'symbol' => 'doz',
                'base_unit_id' => $pieceUnit->id,
                'conversion_factor' => 12,
                'is_active' => true,
            ]);
            Unit::create([
                'name' => 'كرتون (24 قطعة)',
                'symbol' => 'ctn',
                'base_unit_id' => $pieceUnit->id,
                'conversion_factor' => 24,
                'is_active' => true,
            ]);
        }

        if ($boxUnit) {
            Unit::create([
                'name' => 'باك (6 علب)',
                'symbol' => 'pack',
                'base_unit_id' => $boxUnit->id,
                'conversion_factor' => 6,
                'is_active' => true,
            ]);
        }

        $units = Unit::where('is_active', true)->get();

        // ===== Categories (parents + children) =====
        $parentNames = [
            'مواد غذائية',
            'مشروبات',
            'منظفات',
            'أدوات منزلية',
            'إلكترونيات',
            'قرطاسية',
            'مستحضرات تجميل',
            'ملابس',
            'رياضة',
            'ألعاب',
            'أدوية OTC',
            'مخبوزات',
        ];

        $parents = collect();
        foreach ($parentNames as $idx => $name) {
            // ensure unique slug/name
            $uniqueName = $name . ' ' . ($idx + 1);
            $parents->push(Category::create([
                'parent_id' => null,
                'name' => $uniqueName,
                'description' => $faker->sentence(8),
                'order' => $idx + 1,
                'is_active' => true,
            ]));
        }

        $children = collect();
        foreach ($parents as $p) {
            $childCount = random_int(3, 7);
            for ($c = 1; $c <= $childCount; $c++) {
                $children->push(Category::create([
                    'parent_id' => $p->id,
                    'name' => $p->name . ' - ' . $c,
                    'description' => $faker->sentence(10),
                    'order' => $c,
                    'is_active' => true,
                ]));
            }
        }

        $allCategories = $parents->merge($children);

        // ===== Products (large count) =====
        $productsCount = 600;

        $usedProductBarcodes = [];
        $usedExtraBarcodes = [];

        for ($i = 1; $i <= $productsCount; $i++) {
            $category = $allCategories->random();
            $unit = $units->random();
            $tax = $taxes->random();

            $name = $faker->words(random_int(2, 4), true) . ' #' . $i;
            $basePrice = $faker->randomFloat(2, 2, 500);
            $costPrice = max(0.01, round($basePrice * $faker->randomFloat(2, 0.4, 0.85), 2));

            $barcode = null;
            if (random_int(1, 100) <= 70) {
                // make mostly unique
                do {
                    $candidate = (string) $faker->ean13();
                } while (isset($usedProductBarcodes[$candidate]));
                $usedProductBarcodes[$candidate] = true;
                $barcode = $candidate;
            }

            $product = Product::create([
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'tax_id' => $tax->id,
                'name' => $name,
                'barcode' => $barcode,
                'description' => $faker->sentence(12),
                'base_price' => $basePrice,
                'cost_price' => $costPrice,
                'min_stock_alert' => random_int(0, 20),
                'reorder_level' => random_int(5, 30),
                'max_level' => random_int(50, 300),
                'is_active' => true,
            ]);

            // Default prices (null branch) retail/wholesale
            ProductPrice::create([
                'product_id' => $product->id,
                'branch_id' => null,
                'price_type' => 'retail',
                'value' => $basePrice,
            ]);
            ProductPrice::create([
                'product_id' => $product->id,
                'branch_id' => null,
                'price_type' => 'wholesale',
                'value' => max(0.01, round($basePrice * 0.92, 2)),
            ]);

            // Branch overrides for some products
            if (random_int(1, 100) <= 25) {
                $targetBranches = $branches->random(random_int(1, min(3, $branches->count())));
                foreach (collect($targetBranches) as $b) {
                    ProductPrice::create([
                        'product_id' => $product->id,
                        'branch_id' => $b->id,
                        'price_type' => 'retail',
                        'value' => max(0.01, round($basePrice * $faker->randomFloat(2, 0.95, 1.10), 2)),
                    ]);
                }
            }

            // Extra barcodes for some products (unique globally)
            $extraCount = random_int(0, 2);
            for ($x = 1; $x <= $extraCount; $x++) {
                do {
                    $extra = (string) $faker->ean13();
                } while (isset($usedExtraBarcodes[$extra]) || isset($usedProductBarcodes[$extra]));
                $usedExtraBarcodes[$extra] = true;

                ProductBarcode::create([
                    'product_id' => $product->id,
                    'barcode' => $extra,
                    'description' => $x === 1 ? 'باركود بديل' : 'باركود إضافي ' . $x,
                    'is_primary' => false,
                ]);
            }
        }

        $this->command?->info('LargeMasterDataSeeder: تم إنشاء بيانات كبيرة (فروع/مخازن/تصنيفات/وحدات/منتجات) بنجاح.');
    }
}

