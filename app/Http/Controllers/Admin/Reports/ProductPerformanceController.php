<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\ProductPerformanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductPerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view')->only(['index', 'top', 'noSales']);
    }

    public function index(Request $request, ProductPerformanceService $service)
    {
        $from = $request->input('from', Carbon::today()->startOfMonth()->toDateString());
        $to = $request->input('to', Carbon::today()->toDateString());
        $rows = $service->getProductPerformance($from, $to);

        if ($request->input('format') === 'csv') {
            return $this->exportPerformanceCsv($rows, $from, $to, 'product-performance');
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.reports.product-performance.partials.table-rows', compact('rows'))->render(),
            ]);
        }

        return view('admin.pages.reports.product-performance.index', compact('rows', 'from', 'to'));
    }

    public function top(Request $request, ProductPerformanceService $service)
    {
        $limit = max(5, min(50, (int) $request->input('limit', 10)));
        $from = $request->input('from', Carbon::today()->startOfMonth()->toDateString());
        $to = $request->input('to', Carbon::today()->toDateString());
        $rows = $service->getTopProducts($limit, $from, $to);

        if ($request->input('format') === 'csv') {
            return $this->exportTopCsv($rows, $from, $to, $limit);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.reports.product-performance.partials.top-rows', compact('rows'))->render(),
            ]);
        }

        return view('admin.pages.reports.product-performance.top', compact('rows', 'limit', 'from', 'to'));
    }

    public function noSales(Request $request, ProductPerformanceService $service)
    {
        $rows = $service->getProductsWithNoSales();

        if ($request->input('format') === 'csv') {
            return $this->exportNoSalesCsv($rows);
        }

        return view('admin.pages.reports.product-performance.no-sales', compact('rows'));
    }

    private function exportPerformanceCsv($rows, string $from, string $to, string $prefix): StreamedResponse
    {
        $filename = $prefix . '-' . $from . '-to-' . $to . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['المنتج', 'التصنيف', 'الكمية', 'الإيرادات', 'الربح', 'الهامش %']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->product_name,
                    $row->category_name,
                    $row->total_qty,
                    $row->total_revenue,
                    $row->profit,
                    $row->margin_percent,
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
        $filename = 'top-products-' . $from . '-to-' . $to . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'المنتج', 'التصنيف', 'الكمية', 'الإيرادات', 'الربح']);
            foreach ($rows as $i => $row) {
                fputcsv($out, [
                    $i + 1,
                    $row->product_name,
                    $row->category_name,
                    $row->total_qty,
                    $row->total_revenue,
                    $row->profit,
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportNoSalesCsv($rows): StreamedResponse
    {
        $filename = 'products-no-sales-' . now()->format('Y-m-d') . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['المنتج', 'التصنيف', 'الباركود']);
            foreach ($rows as $p) {
                fputcsv($out, [
                    $p->name,
                    $p->category->name ?? '—',
                    $p->barcode ?? '—',
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
