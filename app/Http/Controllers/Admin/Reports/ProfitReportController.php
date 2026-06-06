<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\Reports\ProfitReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        if ($request->input('format') === 'csv') {
            return $this->exportCsv($summary, $from, $to);
        }

        if ($request->ajax()) {
            return response()->json([
                'summary' => view('admin.pages.reports.profit.partials.summary', compact('summary', 'from', 'to'))->render(),
            ]);
        }

        return view('admin.pages.reports.profit.index', compact('summary', 'from', 'to', 'branchId', 'branches'));
    }

    private function exportCsv(array $summary, Carbon $from, Carbon $to): StreamedResponse
    {
        $filename = 'profit-report-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.csv';
        $netSales = $summary['sales_total'] - $summary['sales_returns'];
        $netPurchases = $summary['purchases_total'] - $summary['purchase_returns'];

        return new StreamedResponse(function () use ($summary, $from, $to, $netSales, $netPurchases) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['من', 'إلى', 'صافي المبيعات', 'صافي المشتريات', 'سندات قبض', 'سندات صرف', 'الربح الإجمالي']);
            fputcsv($out, [
                $from->format('Y-m-d'),
                $to->format('Y-m-d'),
                $netSales,
                $netPurchases,
                $summary['vouchers_receipts'],
                $summary['vouchers_payments'],
                $summary['gross_profit'],
            ]);
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
