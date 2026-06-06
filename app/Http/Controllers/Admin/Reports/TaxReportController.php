<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\Reports\TaxReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        $salesTax = $service->getSalesTaxReport($from, $to, $branchId ? (int) $branchId : null);
        $purchaseTax = $service->getPurchaseTaxReport($from, $to, $branchId ? (int) $branchId : null);

        if ($request->input('format') === 'csv') {
            return $this->exportCsv($from, $to, $salesTax, $purchaseTax);
        }

        if ($request->ajax()) {
            return response()->json([
                'summary' => view('admin.pages.reports.taxes.partials.summary', compact('from', 'to', 'salesTax', 'purchaseTax'))->render(),
            ]);
        }

        return view('admin.pages.reports.taxes.index', compact('from', 'to', 'salesTax', 'purchaseTax', 'branchId', 'branches'));
    }

    private function exportCsv(Carbon $from, Carbon $to, float $salesTax, float $purchaseTax): StreamedResponse
    {
        $filename = 'tax-report-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.csv';
        $netTax = $salesTax - $purchaseTax;

        return new StreamedResponse(function () use ($from, $to, $salesTax, $purchaseTax, $netTax) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['من', 'إلى', 'ضريبة المخرجات', 'ضريبة المدخلات', 'صافي الضريبة']);
            fputcsv($out, [
                $from->format('Y-m-d'),
                $to->format('Y-m-d'),
                $salesTax,
                $purchaseTax,
                $netTax,
            ]);
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
