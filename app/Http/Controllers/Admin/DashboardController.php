<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Dashboard\DashboardWidgetsService;
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
        ProfitReportService $profitReportService,
        DashboardWidgetsService $widgetsService
    ) {
        $today = Carbon::today();
        $salesToday = $salesReportService->getDailySummary($today, null);
        $purchasesToday = $purchaseReportService->getDailySummary($today, null);

        $from = $today->copy()->startOfMonth();
        $profitSummary = $profitReportService->getProfitSummary($from, $today, null);

        $widgets = $widgetsService->getAllWidgets();

        return view('admin.dashboard', [
            'salesToday' => $salesToday,
            'purchasesToday' => $purchasesToday,
            'profitSummary' => $profitSummary,
            'customersCount' => Customer::count(),
            'customersBalance' => $widgets['customers_balance'],
            'suppliersBalance' => $widgets['suppliers_balance'],
            'stockAlertsCount' => $widgets['stock_alerts_count'],
            'dueChecks' => $widgets['due_checks'],
            'topProducts' => $widgets['top_products'],
            'topCustomers' => $widgets['top_customers'],
        ]);
    }
}

