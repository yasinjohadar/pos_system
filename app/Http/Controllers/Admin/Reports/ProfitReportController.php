<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\ProfitReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfitReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-profit')->only('index');
    }

    public function index(Request $request, ProfitReportService $service)
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->input('from_date'))
            : Carbon::today()->startOfMonth();

        $to = $request->filled('to_date')
            ? Carbon::parse($request->input('to_date'))
            : Carbon::today();

        $branchId = $request->input('branch_id');

        $summary = $service->getProfitSummary($from, $to, $branchId ? (int) $branchId : null);

        return view('admin.pages.reports.profit.index', compact('summary', 'from', 'to', 'branchId'));
    }
}

