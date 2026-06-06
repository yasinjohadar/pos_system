<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\Reports\PurchaseReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        if ($request->input('format') === 'csv') {
            return $this->dailyCsv($summary, $date);
        }

        if ($request->ajax()) {
            return response()->json([
                'summary' => view('admin.pages.reports.purchases.partials.daily-summary', compact('summary', 'date'))->render(),
            ]);
        }

        return view('admin.pages.reports.purchases.daily', compact('summary', 'date', 'branchId', 'branches'));
    }

    private function dailyCsv(array $summary, Carbon $date): StreamedResponse
    {
        $filename = 'purchases-daily-' . $date->format('Y-m-d') . '.csv';

        return new StreamedResponse(function () use ($summary, $date) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['التاريخ', 'عدد الفواتير', 'إجمالي المشتريات', 'المرتجعات', 'صافي المشتريات', 'الضريبة', 'الخصم']);
            fputcsv($out, [
                $date->format('Y-m-d'),
                $summary['invoices_count'],
                $summary['total_purchases'],
                $summary['total_returns'],
                $summary['net_purchases'],
                $summary['tax_amount'],
                $summary['discount_amount'],
            ]);
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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
