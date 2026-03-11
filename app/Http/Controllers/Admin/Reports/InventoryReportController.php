<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Warehouse;
use App\Services\Reports\InventoryReportService;
use Illuminate\Http\Request;

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

        return view('admin.pages.reports.inventory.index', compact('rows', 'warehouses', 'categories', 'warehouseId', 'categoryId'));
    }

    public function reorder(InventoryReportService $service)
    {
        $rows = $service->getReorderSuggestions();

        return view('admin.pages.reports.inventory.reorder', compact('rows'));
    }
}

