<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\Reports\PartnerReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PartnerReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-partners')->only(['customersAging', 'suppliersAging']);
    }

    public function customersAging(Request $request, PartnerReportService $service)
    {
        $asOfDate = $request->filled('as_of_date')
            ? Carbon::parse($request->input('as_of_date'))
            : Carbon::today();

        $selectedCustomerId = $request->input('customer_id');
        $customer = null;
        $aging = null;

        if ($selectedCustomerId) {
            $customer = Customer::findOrFail($selectedCustomerId);
            $aging = $service->getCustomerAging($customer, $asOfDate);
        }

        if ($request->input('format') === 'csv') {
            if (! $customer || ! $aging) {
                abort(422, 'يرجى اختيار عميل أولاً');
            }

            return $this->exportCustomerAgingCsv($customer, $aging, $asOfDate);
        }

        if ($request->ajax()) {
            return response()->json([
                'summary' => view('admin.pages.reports.customers.partials.aging-summary', compact('customer', 'aging', 'asOfDate'))->render(),
            ]);
        }

        return view('admin.pages.reports.customers.aging', compact('customer', 'selectedCustomerId', 'aging', 'asOfDate'));
    }

    public function suppliersAging(Request $request, PartnerReportService $service)
    {
        $asOfDate = $request->filled('as_of_date')
            ? Carbon::parse($request->input('as_of_date'))
            : Carbon::today();

        $selectedSupplierId = $request->input('supplier_id');
        $supplier = null;
        $aging = null;

        if ($selectedSupplierId) {
            $supplier = Supplier::findOrFail($selectedSupplierId);
            $aging = $service->getSupplierAging($supplier, $asOfDate);
        }

        if ($request->input('format') === 'csv') {
            if (! $supplier || ! $aging) {
                abort(422, 'يرجى اختيار مورد أولاً');
            }

            return $this->exportSupplierAgingCsv($supplier, $aging, $asOfDate);
        }

        if ($request->ajax()) {
            return response()->json([
                'summary' => view('admin.pages.reports.suppliers.partials.aging-summary', compact('supplier', 'aging', 'asOfDate'))->render(),
            ]);
        }

        return view('admin.pages.reports.suppliers.aging', compact('supplier', 'selectedSupplierId', 'aging', 'asOfDate'));
    }

    private function exportSupplierAgingCsv(Supplier $supplier, array $aging, Carbon $asOfDate): StreamedResponse
    {
        $filename = 'supplier-aging-' . $supplier->id . '-' . $asOfDate->format('Y-m-d') . '.csv';
        $total = array_sum($aging);

        return new StreamedResponse(function () use ($supplier, $aging, $asOfDate, $total) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['المورد', 'حتى تاريخ', '0-30 يوم', '31-60 يوم', '61-90 يوم', 'أكثر من 90 يوم', 'الإجمالي']);
            fputcsv($out, [
                $supplier->name,
                $asOfDate->format('Y-m-d'),
                $aging['0_30'],
                $aging['31_60'],
                $aging['61_90'],
                $aging['90_plus'],
                $total,
            ]);
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportCustomerAgingCsv(Customer $customer, array $aging, Carbon $asOfDate): StreamedResponse
    {
        $filename = 'customer-aging-' . $customer->id . '-' . $asOfDate->format('Y-m-d') . '.csv';
        $total = array_sum($aging);

        return new StreamedResponse(function () use ($customer, $aging, $asOfDate, $total) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['العميل', 'حتى تاريخ', '0-30 يوم', '31-60 يوم', '61-90 يوم', 'أكثر من 90 يوم', 'الإجمالي']);
            fputcsv($out, [
                $customer->name,
                $asOfDate->format('Y-m-d'),
                $aging['0_30'],
                $aging['31_60'],
                $aging['61_90'],
                $aging['90_plus'],
                $total,
            ]);
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
