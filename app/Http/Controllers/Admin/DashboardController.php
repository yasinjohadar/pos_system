<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Reports\SalesReportService;
use App\Services\Reports\PurchaseReportService;
use App\Services\Reports\ProfitReportService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(
        SalesReportService $salesReportService,
        PurchaseReportService $purchaseReportService,
        ProfitReportService $profitReportService
    ) {
        $today = Carbon::today();
        $salesToday = $salesReportService->getDailySummary($today, null);
        $purchasesToday = $purchaseReportService->getDailySummary($today, null);

        $from = $today->copy()->startOfMonth();
        $profitSummary = $profitReportService->getProfitSummary($from, $today, null);

        return view('admin.dashboard', [
            'salesToday' => $salesToday,
            'purchasesToday' => $purchasesToday,
            'profitSummary' => $profitSummary,
        ]);
    }
}

