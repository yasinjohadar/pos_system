<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\Reports\PartnerReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $customers = Customer::orderBy('name')->get();
        $selectedCustomerId = $request->input('customer_id');
        $aging = null;

        if ($selectedCustomerId) {
            $customer = Customer::findOrFail($selectedCustomerId);
            $aging = $service->getCustomerAging($customer, $asOfDate);
        }

        return view('admin.pages.reports.customers.aging', compact('customers', 'selectedCustomerId', 'aging', 'asOfDate'));
    }

    public function suppliersAging(Request $request, PartnerReportService $service)
    {
        $asOfDate = $request->filled('as_of_date')
            ? Carbon::parse($request->input('as_of_date'))
            : Carbon::today();

        $suppliers = Supplier::orderBy('name')->get();
        $selectedSupplierId = $request->input('supplier_id');
        $aging = null;

        if ($selectedSupplierId) {
            $supplier = Supplier::findOrFail($selectedSupplierId);
            $aging = $service->getSupplierAging($supplier, $asOfDate);
        }

        return view('admin.pages.reports.suppliers.aging', compact('suppliers', 'selectedSupplierId', 'aging', 'asOfDate'));
    }
}

