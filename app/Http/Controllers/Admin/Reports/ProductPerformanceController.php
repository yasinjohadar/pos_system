<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\ProductPerformanceService;
use Illuminate\Http\Request;

class ProductPerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view')->only(['index', 'top', 'noSales']);
    }

    public function index(Request $request, ProductPerformanceService $service)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $rows = $service->getProductPerformance($from, $to);
        return view('admin.pages.reports.product-performance.index', compact('rows', 'from', 'to'));
    }

    public function top(Request $request, ProductPerformanceService $service)
    {
        $limit = (int) $request->input('limit', 10);
        $from = $request->input('from');
        $to = $request->input('to');
        $rows = $service->getTopProducts($limit, $from, $to);
        return view('admin.pages.reports.product-performance.top', compact('rows', 'limit', 'from', 'to'));
    }

    public function noSales(ProductPerformanceService $service)
    {
        $rows = $service->getProductsWithNoSales();
        return view('admin.pages.reports.product-performance.no-sales', compact('rows'));
    }
}
