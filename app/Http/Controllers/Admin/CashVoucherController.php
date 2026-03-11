<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CashVoucher;
use App\Models\Treasury;
use Illuminate\Http\Request;

class CashVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:cash-voucher-list')->only('index');
        $this->middleware('permission:cash-voucher-create')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $query = CashVoucher::with(['treasury', 'bankAccount', 'user'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('treasury_id')) {
            $query->where('treasury_id', $request->input('treasury_id'));
        }
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->input('bank_account_id'));
        }

        $vouchers = $query->paginate(20);
        $treasuries = Treasury::getActiveForSelect();
        $bankAccounts = BankAccount::getActiveForSelect();

        return view('admin.pages.finance.cash-vouchers.index', compact('vouchers', 'treasuries', 'bankAccounts'));
    }

    public function create()
    {
        $treasuries = Treasury::getActiveForSelect();
        $bankAccounts = BankAccount::getActiveForSelect();

        return view('admin.pages.finance.cash-vouchers.create', compact('treasuries', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:receipt,payment',
            'date' => 'required|date',
            'treasury_id' => 'nullable|exists:treasuries,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|max:10',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if (!$validated['treasury_id'] && !$validated['bank_account_id']) {
            return back()->withInput()->withErrors(['treasury_id' => 'يجب اختيار خزنة أو حساب بنكي.']);
        }

        $voucher = CashVoucher::create([
            'type' => $validated['type'],
            'voucher_number' => CashVoucher::generateNumber(),
            'date' => $validated['date'],
            'treasury_id' => $validated['treasury_id'] ?? null,
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? null,
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'user_id' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.cash-vouchers.index')
            ->with('success', 'تم تسجيل السند رقم ' . $voucher->voucher_number);
    }
}

