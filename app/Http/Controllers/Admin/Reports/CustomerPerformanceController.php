<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\CustomerPerformanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerPerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view')->only(['index', 'top', 'inactive']);
    }

    public function index(Request $request, CustomerPerformanceService $service)
    {
        $from = $request->input('from', Carbon::today()->startOfMonth()->toDateString());
        $to = $request->input('to', Carbon::today()->toDateString());
        $rows = $service->getCustomerPerformance($from, $to);

        if ($request->input('format') === 'csv') {
            return $this->exportPerformanceCsv($rows, $from, $to);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.reports.customer-performance.partials.table-rows', compact('rows'))->render(),
            ]);
        }

        return view('admin.pages.reports.customer-performance.index', compact('rows', 'from', 'to'));
    }

    public function top(Request $request, CustomerPerformanceService $service)
    {
        $limit = max(5, min(50, (int) $request->input('limit', 10)));
        $from = $request->input('from', Carbon::today()->startOfMonth()->toDateString());
        $to = $request->input('to', Carbon::today()->toDateString());
        $rows = $service->getTopCustomers($limit, $from, $to);

        if ($request->input('format') === 'csv') {
            return $this->exportTopCsv($rows, $from, $to, $limit);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.reports.customer-performance.partials.top-rows', compact('rows'))->render(),
            ]);
        }

        return view('admin.pages.reports.customer-performance.top', compact('rows', 'limit', 'from', 'to'));
    }

    public function inactive(Request $request, CustomerPerformanceService $service)
    {
        $days = max(1, min(365, (int) $request->input('days', 90)));
        $rows = $service->getInactiveCustomers($days);

        if ($request->input('format') === 'csv') {
            return $this->exportInactiveCsv($rows, $days);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.reports.customer-performance.partials.inactive-rows', compact('rows'))->render(),
            ]);
        }

        return view('admin.pages.reports.customer-performance.inactive', compact('rows', 'days'));
    }

    private function exportPerformanceCsv($rows, string $from, string $to): StreamedResponse
    {
        $filename = 'customer-performance-' . $from . '-to-' . $to . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['العميل', 'عدد الفواتير', 'إجمالي المبيعات', 'متوسط الفاتورة', 'آخر شراء']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->customer_name,
                    $row->invoice_count,
                    $row->total_sales,
                    $row->avg_invoice_value,
                    $row->last_invoice_date,
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportTopCsv($rows, string $from, string $to, int $limit): StreamedResponse
    {
        $filename = 'top-customers-' . $from . '-to-' . $to . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'العميل', 'عدد الفواتير', 'إجمالي المبيعات', 'متوسط الفاتورة']);
            foreach ($rows as $i => $row) {
                fputcsv($out, [
                    $i + 1,
                    $row->customer_name,
                    $row->invoice_count,
                    $row->total_sales,
                    $row->avg_invoice_value,
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportInactiveCsv($rows, int $days): StreamedResponse
    {
        $filename = 'inactive-customers-' . $days . 'days-' . now()->format('Y-m-d') . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['العميل', 'الهاتف', 'البريد']);
            foreach ($rows as $c) {
                fputcsv($out, [$c->name, $c->phone ?? '—', $c->email ?? '—']);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
