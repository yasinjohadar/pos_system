<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\CustomerPerformanceService;
use Illuminate\Http\Request;

class CustomerPerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view')->only(['index', 'top', 'inactive']);
    }

    public function index(Request $request, CustomerPerformanceService $service)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $rows = $service->getCustomerPerformance($from, $to);
        return view('admin.pages.reports.customer-performance.index', compact('rows', 'from', 'to'));
    }

    public function top(Request $request, CustomerPerformanceService $service)
    {
        $limit = (int) $request->input('limit', 10);
        $from = $request->input('from');
        $to = $request->input('to');
        $rows = $service->getTopCustomers($limit, $from, $to);
        return view('admin.pages.reports.customer-performance.top', compact('rows', 'limit', 'from', 'to'));
    }

    public function inactive(Request $request, CustomerPerformanceService $service)
    {
        $days = (int) $request->input('days', 90);
        $rows = $service->getInactiveCustomers($days);
        return view('admin.pages.reports.customer-performance.inactive', compact('rows', 'days'));
    }
}
