<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Check;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:check-list')->only('index');
        $this->middleware('permission:check-create')->only(['create', 'store']);
        $this->middleware('permission:check-edit')->only(['edit', 'update']);
        $this->middleware('permission:check-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Check::query()->with(['bankAccount', 'salePayment', 'supplierPayment'])->orderByDesc('due_date')->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('from_date')) {
            $query->where('due_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->where('due_date', '<=', $request->input('to_date'));
        }

        $checks = $query->paginate(15);

        return view('admin.pages.sales.checks.index', compact('checks'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::getActiveForSelect();
        return view('admin.pages.sales.checks.create', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'check_number' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'bank_name' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = Check::STATUS_UNDER_COLLECTION;

        Check::create($validated);

        return redirect()->route('admin.checks.index')
            ->with('success', 'تم إضافة الشيك بنجاح.');
    }

    public function show(Check $check)
    {
        $check->load(['bankAccount', 'salePayment.saleInvoice', 'supplierPayment.purchaseInvoice']);
        return view('admin.pages.sales.checks.show', compact('check'));
    }

    public function updateStatus(Request $request, Check $check)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . Check::STATUS_COLLECTED . ',' . Check::STATUS_RETURNED,
        ]);

        $check->update(['status' => $validated['status']]);

        return redirect()->route('admin.checks.show', $check)
            ->with('success', 'تم تحديث حالة الشيك بنجاح.');
    }
}
