<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\PurchaseReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-purchases')->only(['daily', 'monthly']);
    }

    public function daily(Request $request, PurchaseReportService $service)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        $branchId = $request->input('branch_id');

        $summary = $service->getDailySummary($date, $branchId ? (int) $branchId : null);

        return view('admin.pages.reports.purchases.daily', compact('summary', 'date', 'branchId'));
    }

    public function monthly(Request $request, PurchaseReportService $service)
    {
        $year = (int) ($request->input('year') ?: date('Y'));
        $month = (int) ($request->input('month') ?: date('m'));
        $branchId = $request->input('branch_id');

        $chart = $service->getMonthlySummary($year, $month, $branchId ? (int) $branchId : null);

        return view('admin.pages.reports.purchases.monthly', compact('chart', 'year', 'month', 'branchId'));
    }
}

