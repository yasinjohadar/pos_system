<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        if (StockMovement::exists()) {
            $this->command?->info('StockSeeder: حركات المخزون موجودة مسبقاً — تخطي.');

            return;
        }

        $user = User::query()->first();
        $warehouses = Warehouse::where('is_active', true)->orderBy('id')->get();
        $products = Product::where('is_active', true)->orderBy('id')->limit(40)->get();

        if ($warehouses->isEmpty() || $products->isEmpty() || ! $user) {
            $this->command?->warn('StockSeeder: يتطلب مستخدماً وفروعاً/مخازن ومنتجات نشطة.');

            return;
        }

        DB::transaction(function () use ($user, $warehouses, $products) {
            $warehouseIds = $warehouses->pluck('id')->all();

            foreach ($products as $index => $product) {
                $warehouse = $warehouses[$index % $warehouses->count()];
                $qty = random_int(20, 200);

                StockMovement::record([
                    'type' => 'in',
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity' => $qty,
                    'movement_date' => now()->subDays(random_int(15, 45))->toDateString(),
                    'notes' => 'إدخال أولي — بيانات تجريبية',
                    'user_id' => $user->id,
                ]);

                if ($index % 3 === 0) {
                    StockMovement::record([
                        'type' => 'out',
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'quantity' => random_int(5, min(15, $qty)),
                        'movement_date' => now()->subDays(random_int(5, 14))->toDateString(),
                        'notes' => 'صرف تجريبي',
                        'user_id' => $user->id,
                    ]);
                }

                if ($index % 7 === 0) {
                    StockMovement::record([
                        'type' => 'adjustment',
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'quantity' => random_int(-3, 5),
                        'movement_date' => now()->subDays(random_int(1, 7))->toDateString(),
                        'notes' => 'تسوية مخزون تجريبية',
                        'user_id' => $user->id,
                    ]);
                }
            }

            if (count($warehouseIds) >= 2) {
                $from = $warehouses[0];
                $to = $warehouses[1];
                $transferProducts = $products->take(5);

                $transfer = StockTransfer::create([
                    'from_warehouse_id' => $from->id,
                    'to_warehouse_id' => $to->id,
                    'transfer_date' => now()->subDays(3)->toDateString(),
                    'user_id' => $user->id,
                    'status' => StockTransfer::STATUS_COMPLETED,
                    'notes' => 'تحويل تجريبي بين المخازن',
                ]);

                foreach ($transferProducts as $product) {
                    $qty = random_int(3, 12);

                    StockMovement::record([
                        'type' => 'transfer_out',
                        'product_id' => $product->id,
                        'warehouse_id' => $from->id,
                        'quantity' => $qty,
                        'movement_date' => $transfer->transfer_date,
                        'reference_type' => 'stock_transfer',
                        'reference_id' => $transfer->id,
                        'stock_transfer_id' => $transfer->id,
                        'notes' => 'تحويل إلى: ' . $to->name,
                        'user_id' => $user->id,
                    ]);

                    StockMovement::record([
                        'type' => 'transfer_in',
                        'product_id' => $product->id,
                        'warehouse_id' => $to->id,
                        'quantity' => $qty,
                        'movement_date' => $transfer->transfer_date,
                        'reference_type' => 'stock_transfer',
                        'reference_id' => $transfer->id,
                        'stock_transfer_id' => $transfer->id,
                        'notes' => 'تحويل من: ' . $from->name,
                        'user_id' => $user->id,
                    ]);
                }
            }
        });

        $this->command?->info('StockSeeder: تم إنشاء حركات وأرصدة وتحويلات تجريبية.');
    }
}
