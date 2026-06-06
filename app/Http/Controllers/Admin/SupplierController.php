<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\MergesPhoneInput;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use MergesPhoneInput;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:supplier-list')->only('index');
        $this->middleware('permission:supplier-create')->only(['create', 'store']);
        $this->middleware('permission:supplier-edit')->only(['edit', 'update']);
        $this->middleware('permission:supplier-delete')->only('destroy');
        $this->middleware('permission:supplier-show')->only(['show', 'statement']);
        $this->middleware('permission:supplier-list|purchase-invoice-create|purchase-invoice-edit')->only('searchSelect');
    }

    /**
     * بحث الموردين لقوائم الاختيار (Select2 AJAX).
     */
    public function searchSelect(Request $request)
    {
        $search = trim((string) $request->input('search', $request->input('q', '')));

        $query = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }

        $suppliers = $query->limit(25)->get(['id', 'name', 'phone']);

        return response()->json([
            'results' => $suppliers->map(fn ($s) => [
                'id' => $s->id,
                'text' => $s->name . ($s->phone ? ' (' . $s->phone . ')' : ''),
            ]),
        ]);
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

        $suppliers = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.purchases.suppliers.partials.table-rows', compact('suppliers'))->render(),
                'pagination' => view('admin.pages.purchases.suppliers.partials.pagination', compact('suppliers'))->render(),
            ]);
        }

        return view('admin.pages.purchases.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.pages.purchases.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequestWithPhone($request, array_merge([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ], $this->phoneRules('phone')), [
            'phone.regex' => 'رقم الهاتف غير صحيح — أدخل الرقم بدون صفر في البداية',
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
        $validated = $this->validateRequestWithPhone($request, array_merge([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ], $this->phoneRules('phone')), [
            'phone.regex' => 'رقم الهاتف غير صحيح — أدخل الرقم بدون صفر في البداية',
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
