<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\TaxReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaxReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-taxes')->only('index');
    }

    public function index(Request $request, TaxReportService $service)
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->input('from_date'))
            : Carbon::today()->startOfMonth();

        $to = $request->filled('to_date')
            ? Carbon::parse($request->input('to_date'))
            : Carbon::today();

        $branchId = $request->input('branch_id');

        $salesTax = $service->getSalesTaxReport($from, $to, $branchId ? (int) $branchId : null);
        $purchaseTax = $service->getPurchaseTaxReport($from, $to, $branchId ? (int) $branchId : null);

        return view('admin.pages.reports.taxes.index', compact('from', 'to', 'salesTax', 'purchaseTax', 'branchId'));
    }
}

