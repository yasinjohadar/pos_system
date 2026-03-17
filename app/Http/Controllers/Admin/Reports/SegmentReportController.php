<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\CustomerSegment;
use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SegmentReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view')->only('index');
    }

    /**
     * تقرير الشرائح: عدد العملاء، إجمالي المبيعات، متوسط الرصيد، متوسط قيمة الفاتورة.
     */
    public function index(Request $request)
    {
        $segments = CustomerSegment::where('is_active', true)->orderBy('name')->get();
        $rows = [];

        foreach ($segments as $segment) {
            $customerIds = $segment->customers()->pluck('id');
            $customerCount = $customerIds->count();

            if ($customerCount === 0) {
                $rows[] = (object) [
                    'segment_id' => $segment->id,
                    'segment_name' => $segment->name,
                    'customer_count' => 0,
                    'total_sales' => 0,
                    'avg_balance' => 0,
                    'invoice_count' => 0,
                    'avg_invoice_value' => 0,
                ];
                continue;
            }

            $invoiceIds = SaleInvoice::where('status', SaleInvoice::STATUS_CONFIRMED)
                ->whereIn('customer_id', $customerIds)
                ->pluck('id');

            $totalSales = SaleInvoice::where('status', SaleInvoice::STATUS_CONFIRMED)
                ->whereIn('customer_id', $customerIds)
                ->sum('total');

            $totalReturns = SaleReturn::whereIn('sale_invoice_id', $invoiceIds)
                ->where('status', SaleReturn::STATUS_COMPLETED)
                ->sum('total_refund');

            $totalPaid = SalePayment::whereIn('sale_invoice_id', $invoiceIds)->sum('amount');

            $openingBalance = DB::table('customers')->whereIn('id', $customerIds)->sum('opening_balance');

            $balance = $totalSales - $totalReturns - $totalPaid + (float) $openingBalance;
            $invoiceCount = $invoiceIds->count();

            $rows[] = (object) [
                'segment_id' => $segment->id,
                'segment_name' => $segment->name,
                'customer_count' => $customerCount,
                'total_sales' => (float) $totalSales,
                'avg_balance' => $customerCount > 0 ? round($balance / $customerCount, 2) : 0,
                'invoice_count' => $invoiceCount,
                'avg_invoice_value' => $invoiceCount > 0 ? round($totalSales / $invoiceCount, 2) : 0,
            ];
        }

        if ($request->input('format') === 'csv') {
            return $this->csvResponse($rows);
        }

        return view('admin.pages.reports.segments.index', compact('rows'));
    }

    private function csvResponse(array $rows): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'segments-report-' . now()->format('Y-m-d') . '.csv';
        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['الشريحة', 'عدد العملاء', 'إجمالي المبيعات', 'متوسط الرصيد', 'عدد الفواتير', 'متوسط قيمة الفاتورة']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->segment_name,
                    $row->customer_count,
                    $row->total_sales,
                    $row->avg_balance,
                    $row->invoice_count,
                    $row->avg_invoice_value,
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
