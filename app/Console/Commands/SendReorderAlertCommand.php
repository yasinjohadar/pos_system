<?php

namespace App\Console\Commands;

use App\Services\Reports\InventoryReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReorderAlertCommand extends Command
{
    protected $signature = 'inventory:reorder-alert';

    protected $description = 'تنبيه إعادة الطلب: عرض المنتجات التي وصل رصيدها لمستوى إعادة الطلب أو أقل';

    public function handle(InventoryReportService $inventoryReportService): int
    {
        $rows = $inventoryReportService->getReorderSuggestions();

        if ($rows->isEmpty()) {
            $this->info('لا توجد منتجات تحتاج إلى إعادة طلب حالياً.');
            return Command::SUCCESS;
        }

        $tableData = $rows->map(function ($row) {
            $product = $row->product;
            return [
                $product->id,
                $product->name,
                $product->reorder_level,
                (float) $row->total_qty,
            ];
        })->toArray();

        $this->table(['#', 'المنتج', 'حد إعادة الطلب', 'الرصيد الحالي'], $tableData);
        $this->warn('عدد المنتجات التي تحتاج إعادة طلب: ' . count($tableData));

        Log::channel('single')->info('تنبيه إعادة الطلب', [
            'count' => count($tableData),
            'products' => $tableData,
        ]);

        return Command::SUCCESS;
    }
}
