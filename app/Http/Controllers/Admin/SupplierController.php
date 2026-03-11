<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:supplier-list')->only('index');
        $this->middleware('permission:supplier-create')->only(['create', 'store']);
        $this->middleware('permission:supplier-edit')->only(['edit', 'update']);
        $this->middleware('permission:supplier-delete')->only('destroy');
        $this->middleware('permission:supplier-show')->only(['show', 'statement']);
    }

    public function index(Request $request)
    {
        $query = Supplier::query()->orderBy('name');

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

        $suppliers = $query->paginate(15);

        return view('admin.pages.purchases.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.pages.purchases.suppliers.create');
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

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['purchaseInvoices' => fn ($q) => $q->latest()->limit(20)]);
        return view('admin.pages.purchases.suppliers.show', compact('supplier'));
    }

    /**
     * كشف حساب المورد: حركات (فواتير، مرتجعات، دفعات) ورصيد حتى تاريخ محدد.
     */
    public function statement(Request $request, Supplier $supplier)
    {
        $asOfDate = $request->filled('as_of_date')
            ? \Carbon\Carbon::parse($request->input('as_of_date'))
            : null;

        $entries = $supplier->getStatementEntries($asOfDate);

        return view('admin.pages.purchases.suppliers.statement', compact('supplier', 'entries', 'asOfDate'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.pages.purchases.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
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

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'تم تحديث المورد بنجاح');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseInvoices()->exists()) {
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'لا يمكن حذف المورد لأنه مرتبط بفواتير. يمكنك تعطيله بدلاً من الحذف.');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }
}
