<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationItem;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use App\Services\Accounting\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:bank-reconciliation-list')->only('index');
        $this->middleware('permission:bank-reconciliation-create')->only(['create', 'store']);
        $this->middleware('permission:bank-reconciliation-show')->only('show');
    }

    public function index(Request $request)
    {
        $reconciliations = BankReconciliation::with(['bankAccount', 'user'])
            ->orderByDesc('statement_date')
            ->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.finance.bank-reconciliations.partials.table-rows', compact('reconciliations'))->render(),
                'pagination' => view('admin.pages.finance.bank-reconciliations.partials.pagination', compact('reconciliations'))->render(),
            ]);
        }

        return view('admin.pages.finance.bank-reconciliations.index', compact('reconciliations'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.finance.bank-reconciliations.create', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_date' => 'required|date',
            'statement_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $cashAccount = ChartOfAccount::findByCode(AccountingService::CODE_CASH);
        $bookBalance = 0;
        if ($cashAccount) {
            $bookBalance = (float) JournalEntryLine::query()
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entry_lines.account_id', $cashAccount->id)
                ->where('journal_entries.is_posted', true)
                ->where('journal_entries.entry_date', '<=', $validated['statement_date'])
                ->selectRaw('SUM(debit) - SUM(credit) as balance')
                ->value('balance') ?? 0;
        }

        $reconciliation = BankReconciliation::create([
            'bank_account_id' => $validated['bank_account_id'],
            'statement_date' => $validated['statement_date'],
            'statement_balance' => $validated['statement_balance'],
            'book_balance' => $bookBalance,
            'difference' => (float) $validated['statement_balance'] - $bookBalance,
            'status' => BankReconciliation::STATUS_DRAFT,
            'user_id' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.bank-reconciliations.show', $reconciliation)
            ->with('success', 'تم إنشاء التسوية البنكية.');
    }

    public function show(BankReconciliation $bankReconciliation)
    {
        $bankReconciliation->load(['bankAccount', 'user', 'items']);
        return view('admin.pages.finance.bank-reconciliations.show', compact('bankReconciliation'));
    }

    public function addItem(Request $request, BankReconciliation $bankReconciliation)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric',
        ]);

        BankReconciliationItem::create([
            'bank_reconciliation_id' => $bankReconciliation->id,
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
        ]);

        return back()->with('success', 'تم إضافة البند.');
    }

    public function toggleItem(BankReconciliation $bankReconciliation, BankReconciliationItem $item)
    {
        if ($item->bank_reconciliation_id !== $bankReconciliation->id) {
            abort(404);
        }
        $item->update(['is_cleared' => !$item->is_cleared]);
        return back();
    }

    public function finalize(BankReconciliation $bankReconciliation)
    {
        $bankReconciliation->update([
            'status' => BankReconciliation::STATUS_RECONCILED,
            'reconciled_at' => now(),
        ]);

        return back()->with('success', 'تم إقفال التسوية.');
    }
}
