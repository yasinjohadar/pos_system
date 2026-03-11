<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-list')->only('index');
        $this->middleware('permission:customer-create')->only(['create', 'store']);
        $this->middleware('permission:customer-edit')->only(['edit', 'update']);
        $this->middleware('permission:customer-delete')->only('destroy');
        $this->middleware('permission:customer-show')->only(['show', 'statement']);
    }

    public function index(Request $request)
    {
        $query = Customer::query()->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $customers = $query->paginate(15);

        return view('admin.pages.sales.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.pages.sales.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        Customer::create($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    public function show(Customer $customer)
    {
        $customer->load(['saleInvoices' => fn ($q) => $q->latest()->limit(20)]);
        return view('admin.pages.sales.customers.show', compact('customer'));
    }

    /**
     * كشف حساب العميل: حركات (فواتير، مرتجعات، دفعات) ورصيد حتى تاريخ محدد.
     */
    public function statement(Request $request, Customer $customer)
    {
        $asOfDate = $request->filled('as_of_date')
            ? \Carbon\Carbon::parse($request->input('as_of_date'))
            : null;

        $entries = $customer->getStatementEntries($asOfDate);

        return view('admin.pages.sales.customers.statement', compact('customer', 'entries', 'asOfDate'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.pages.sales.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'تم تحديث العميل بنجاح');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->saleInvoices()->exists()) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'لا يمكن حذف العميل لأنه مرتبط بفواتير. يمكنك تعطيله بدلاً من الحذف.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }
}
