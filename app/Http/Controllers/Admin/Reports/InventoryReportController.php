<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Warehouse;
use App\Services\Reports\InventoryReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-inventory')->only(['index', 'reorder']);
    }

    public function index(Request $request, InventoryReportService $service)
    {
        $warehouseId = $request->input('warehouse_id');
        $categoryId = $request->input('category_id');

        $rows = $service->getCurrentStock(
            $warehouseId ? (int) $warehouseId : null,
            $categoryId ? (int) $categoryId : null
        );

        $warehouses = Warehouse::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        if ($request->input('format') === 'csv') {
            return $this->exportCurrentStockCsv($rows);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.reports.inventory.partials.table-rows', compact('rows'))->render(),
            ]);
        }

        return view('admin.pages.reports.inventory.index', compact('rows', 'warehouses', 'categories', 'warehouseId', 'categoryId'));
    }

    public function reorder(InventoryReportService $service)
    {
        $rows = $service->getReorderSuggestions();

        return view('admin.pages.reports.inventory.reorder', compact('rows'));
    }

    private function exportCurrentStockCsv(Collection $rows): StreamedResponse
    {
        $filename = 'inventory-report-' . now()->format('Y-m-d') . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['المنتج', 'التصنيف', 'المخزن', 'الكمية']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->product->name ?? '—',
                    $row->product->category->name ?? '—',
                    $row->warehouse->name ?? '—',
                    (float) $row->quantity,
                ]);
            }

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
