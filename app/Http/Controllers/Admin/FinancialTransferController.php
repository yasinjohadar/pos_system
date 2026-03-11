<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\FinancialTransfer;
use App\Models\Treasury;
use Illuminate\Http\Request;

class FinancialTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:financial-transfer-list')->only('index');
        $this->middleware('permission:financial-transfer-create')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $query = FinancialTransfer::query()
            ->with(['fromTreasury', 'fromBankAccount', 'toTreasury', 'toBankAccount', 'user'])
            ->orderByDesc('transfer_date')
            ->orderByDesc('id');

        if ($request->filled('from_date')) {
            $query->where('transfer_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->where('transfer_date', '<=', $request->input('to_date'));
        }

        $transfers = $query->paginate(15);

        return view('admin.pages.sales.financial-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $treasuries = Treasury::getActiveForSelect();
        $bankAccounts = BankAccount::getActiveForSelect();
        return view('admin.pages.sales.financial-transfers.create', compact('treasuries', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_source' => 'required|in:treasury,bank_account',
            'from_treasury_id' => 'required_if:from_source,treasury|nullable|exists:treasuries,id',
            'from_bank_account_id' => 'required_if:from_source,bank_account|nullable|exists:bank_accounts,id',
            'to_source' => 'required|in:treasury,bank_account',
            'to_treasury_id' => 'required_if:to_source,treasury|nullable|exists:treasuries,id',
            'to_bank_account_id' => 'required_if:to_source,bank_account|nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $fromTreasuryId = $validated['from_source'] === 'treasury' ? ($validated['from_treasury_id'] ?? null) : null;
        $fromBankAccountId = $validated['from_source'] === 'bank_account' ? ($validated['from_bank_account_id'] ?? null) : null;
        $toTreasuryId = $validated['to_source'] === 'treasury' ? ($validated['to_treasury_id'] ?? null) : null;
        $toBankAccountId = $validated['to_source'] === 'bank_account' ? ($validated['to_bank_account_id'] ?? null) : null;

        if (!$fromTreasuryId && !$fromBankAccountId) {
            return redirect()->back()->withInput()->withErrors(['from_source' => 'يجب اختيار مصدر التحويل (خزنة أو حساب بنكي).']);
        }
        if ($fromTreasuryId && $fromBankAccountId) {
            return redirect()->back()->withInput()->withErrors(['from_source' => 'اختر مصدراً واحداً فقط.']);
        }
        if (!$toTreasuryId && !$toBankAccountId) {
            return redirect()->back()->withInput()->withErrors(['to_source' => 'يجب اختيار وجهة التحويل (خزنة أو حساب بنكي).']);
        }
        if ($toTreasuryId && $toBankAccountId) {
            return redirect()->back()->withInput()->withErrors(['to_source' => 'اختر وجهة واحدة فقط.']);
        }

        FinancialTransfer::create([
            'from_treasury_id' => $fromTreasuryId,
            'from_bank_account_id' => $fromBankAccountId,
            'to_treasury_id' => $toTreasuryId,
            'to_bank_account_id' => $toBankAccountId,
            'amount' => $validated['amount'],
            'transfer_date' => $validated['transfer_date'],
            'reference' => $validated['reference'] ?? null,
            'user_id' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.financial-transfers.index')
            ->with('success', 'تم تسجيل التحويل المالي بنجاح.');
    }
}
