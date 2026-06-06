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

        $salesMonthly = $salesReportService->getMonthlySummary($today->year, $today->month, null);
        $purchasesMonthly = $purchaseReportService->getMonthlySummary($today->year, $today->month, null);

        $chartLabels = collect($salesMonthly['labels'])
            ->map(fn (string $date) => Carbon::parse($date)->format('d/m'))
            ->values()
            ->all();

        $topProducts = $widgets['top_products'];
        $topCustomers = $widgets['top_customers'];

        return view('admin.dashboard', [
            'salesToday' => $salesToday,
            'purchasesToday' => $purchasesToday,
            'profitSummary' => $profitSummary,
            'customersCount' => Customer::count(),
            'customersBalance' => $widgets['customers_balance'],
            'suppliersBalance' => $widgets['suppliers_balance'],
            'stockAlertsCount' => $widgets['stock_alerts_count'],
            'dueChecks' => $widgets['due_checks'],
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
            'todayFormatted' => $today->locale('ar')->translatedFormat('l، j F Y'),
            'salesChart' => [
                'labels' => $chartLabels,
                'totals' => $salesMonthly['totals'],
            ],
            'purchasesChart' => [
                'labels' => $chartLabels,
                'totals' => $purchasesMonthly['totals'],
            ],
            'topProductsChart' => [
                'labels' => $topProducts->pluck('product_name')->values()->all(),
                'values' => $topProducts->pluck('total_revenue')->values()->all(),
            ],
            'topCustomersChart' => [
                'labels' => $topCustomers->pluck('customer_name')->values()->all(),
                'values' => $topCustomers->pluck('total_sales')->values()->all(),
            ],
        ]);
    }
}

