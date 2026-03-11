<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Branch;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:bank-account-list')->only('index');
        $this->middleware('permission:bank-account-create')->only(['create', 'store']);
        $this->middleware('permission:bank-account-edit')->only(['edit', 'update']);
        $this->middleware('permission:bank-account-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = BankAccount::query()->with('branch')->orderBy('name');

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $bankAccounts = $query->paginate(15);

        return view('admin.pages.sales.bank-accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.sales.bank-accounts.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'opening_balance' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        BankAccount::create($validated);

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'تم إضافة الحساب البنكي بنجاح');
    }

    public function edit(BankAccount $bankAccount)
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.sales.bank-accounts.edit', compact('bankAccount', 'branches'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'opening_balance' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        $bankAccount->update($validated);

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'تم تحديث الحساب البنكي بنجاح');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'تم حذف الحساب البنكي بنجاح');
    }
}
