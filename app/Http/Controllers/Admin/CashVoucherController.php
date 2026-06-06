<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashVoucher;
use App\Models\JournalEntry;
use App\Services\Accounting\AccountingService;
use Illuminate\Http\Request;

class CashVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:cash-voucher-list')->only('index');
        $this->middleware('permission:cash-voucher-create')->only(['create', 'store']);
        $this->middleware('permission:cash-voucher-show')->only(['show', 'print']);
        $this->middleware('permission:cancel_financial_transaction')->only('cancel');
    }

    public function index(Request $request)
    {
        $query = CashVoucher::with(['treasury', 'bankAccount', 'user'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('treasury_id')) {
            $query->where('treasury_id', $request->input('treasury_id'));
        }
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->input('bank_account_id'));
        }

        $vouchers = $query->paginate(20)->withQueryString();
        $treasuries = \App\Models\Treasury::getActiveForSelect();
        $bankAccounts = \App\Models\BankAccount::getActiveForSelect();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.finance.cash-vouchers.partials.table-rows', compact('vouchers'))->render(),
                'pagination' => view('admin.pages.finance.cash-vouchers.partials.pagination', compact('vouchers'))->render(),
            ]);
        }

        return view('admin.pages.finance.cash-vouchers.index', compact('vouchers', 'treasuries', 'bankAccounts'));
    }

    public function create()
    {
        $treasuries = \App\Models\Treasury::getActiveForSelect();
        $bankAccounts = \App\Models\BankAccount::getActiveForSelect();

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
            'status' => CashVoucher::STATUS_ACTIVE,
        ]);

        return redirect()->route('admin.cash-vouchers.show', $voucher)
            ->with('success', 'تم تسجيل السند رقم ' . $voucher->voucher_number);
    }

    public function show(CashVoucher $cashVoucher)
    {
        $cashVoucher->load(['treasury', 'bankAccount', 'user', 'cancelledBy']);
        $journalEntry = JournalEntry::where('reference_type', CashVoucher::class)
            ->where('reference_id', $cashVoucher->id)
            ->where('source', '!=', JournalEntry::SOURCE_REVERSAL)
            ->with('lines.account')
            ->first();

        return view('admin.pages.finance.cash-vouchers.show', compact('cashVoucher', 'journalEntry'));
    }

    public function print(CashVoucher $cashVoucher)
    {
        $cashVoucher->load(['treasury', 'bankAccount', 'user']);
        $companySettings = app(\App\Services\Settings\CompanySettingsService::class)->getSettings();

        return view('admin.pages.finance.cash-vouchers.print', compact('cashVoucher', 'companySettings'));
    }

    public function cancel(CashVoucher $cashVoucher, AccountingService $accounting)
    {
        if ($cashVoucher->isCancelled()) {
            return back()->with('error', 'السند ملغى مسبقاً.');
        }

        $journalEntry = JournalEntry::where('reference_type', CashVoucher::class)
            ->where('reference_id', $cashVoucher->id)
            ->where('source', JournalEntry::SOURCE_AUTO)
            ->first();

        if ($journalEntry) {
            $accounting->createReversalEntry($journalEntry);
        }

        $cashVoucher->update([
            'status' => CashVoucher::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        return redirect()->route('admin.cash-vouchers.show', $cashVoucher)
            ->with('success', 'تم إلغاء السند وإنشاء قيد عكسي.');
    }
}
