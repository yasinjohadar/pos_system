<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\SalesReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-sales')->only(['daily', 'monthly']);
    }

    public function daily(Request $request, SalesReportService $service)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        $branchId = $request->input('branch_id');

        $summary = $service->getDailySummary($date, $branchId ? (int) $branchId : null);

        if ($request->input('format') === 'csv') {
            return $this->dailyCsv($summary, $date);
        }

        return view('admin.pages.reports.sales.daily', compact('summary', 'date', 'branchId'));
    }

    private function dailyCsv(array $summary, Carbon $date): StreamedResponse
    {
        $filename = 'sales-daily-' . $date->format('Y-m-d') . '.csv';
        return new StreamedResponse(function () use ($summary, $date) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['التاريخ', 'عدد الفواتير', 'إجمالي المبيعات', 'المرتجعات', 'صافي المبيعات', 'الضريبة', 'الخصم']);
            fputcsv($out, [
                $date->format('Y-m-d'),
                $summary['invoices_count'],
                $summary['total_sales'],
                $summary['total_returns'],
                $summary['net_sales'],
                $summary['tax_amount'],
                $summary['discount_amount'],
            ]);
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function monthly(Request $request, SalesReportService $service)
    {
        $year = (int) ($request->input('year') ?: date('Y'));
        $month = (int) ($request->input('month') ?: date('m'));
        $branchId = $request->input('branch_id');

        $chart = $service->getMonthlySummary($year, $month, $branchId ? (int) $branchId : null);

        if ($request->input('format') === 'csv') {
            return $this->monthlyCsv($chart, $year, $month);
        }

        return view('admin.pages.reports.sales.monthly', compact('chart', 'year', 'month', 'branchId'));
    }

    private function monthlyCsv(array $chart, int $year, int $month): StreamedResponse
    {
        $filename = "sales-monthly-{$year}-{$month}.csv";
        return new StreamedResponse(function () use ($chart) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['التاريخ', 'إجمالي المبيعات']);
            foreach ($chart['labels'] ?? [] as $i => $label) {
                $total = $chart['totals'][$i] ?? 0;
                fputcsv($out, [$label, $total]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

