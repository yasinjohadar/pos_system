<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Services\Accounting\AccountingService;
use App\Services\Accounting\FiscalPeriodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:journal-entry-list')->only('index');
        $this->middleware('permission:journal-entry-show')->only('show');
        $this->middleware('permission:journal-entry-create')->only(['create', 'store']);
        $this->middleware('permission:journal-entry-post')->only('post');
        $this->middleware('permission:journal-entry-reverse')->only('reverse');
    }

    public function index(Request $request)
    {
        $query = JournalEntry::with(['createdBy', 'lines.account'])->orderByDesc('entry_date')->orderByDesc('id');
        if ($request->filled('from')) {
            $query->whereDate('entry_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('entry_date', '<=', $request->to);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }
        $entries = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.journal-entries.partials.table-rows', compact('entries'))->render(),
                'pagination' => view('admin.pages.journal-entries.partials.pagination', compact('entries'))->render(),
            ]);
        }

        return view('admin.pages.journal-entries.index', compact('entries'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        return view('admin.pages.journal-entries.create', compact('accounts'));
    }

    public function store(Request $request, FiscalPeriodService $fiscalPeriod)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:255',
            'post_now' => 'boolean',
        ]);

        $fiscalPeriod->assertDateOpen(Carbon::parse($validated['entry_date']));

        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($validated['lines'] as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }
        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->withErrors(['lines' => 'مجموع المدين يجب أن يساوي مجموع الدائن.']);
        }

        $postNow = $request->boolean('post_now');
        $fy = FiscalYear::forDate(Carbon::parse($validated['entry_date']));

        $entry = DB::transaction(function () use ($validated, $postNow, $fy) {
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'entry_date' => $validated['entry_date'],
                'description' => $validated['description'] ?? null,
                'is_posted' => $postNow,
                'source' => JournalEntry::SOURCE_MANUAL,
                'fiscal_year_id' => $fy?->id,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['lines'] as $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);
                if ($debit > 0 || $credit > 0) {
                    $entry->lines()->create([
                        'account_id' => $line['account_id'],
                        'debit' => $debit,
                        'credit' => $credit,
                        'description' => $line['description'] ?? null,
                    ]);
                }
            }

            return $entry;
        });

        return redirect()->route('admin.journal-entries.show', $entry)
            ->with('success', $postNow ? 'تم إنشاء وترحيل القيد.' : 'تم حفظ القيد كمسودة.');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.account', 'createdBy', 'reference', 'reversedEntry', 'fiscalYear']);
        return view('admin.pages.journal-entries.show', compact('journalEntry'));
    }

    public function post(JournalEntry $journalEntry, FiscalPeriodService $fiscalPeriod)
    {
        if ($journalEntry->is_posted) {
            return back()->with('error', 'القيد مرحّل مسبقاً.');
        }
        $journalEntry->load('lines');
        if (!$journalEntry->isBalanced()) {
            return back()->with('error', 'القيد غير متوازن.');
        }

        $fiscalPeriod->assertDateOpen(Carbon::parse($journalEntry->entry_date));
        $journalEntry->update(['is_posted' => true]);

        return back()->with('success', 'تم ترحيل القيد.');
    }

    public function reverse(JournalEntry $journalEntry, AccountingService $accounting, FiscalPeriodService $fiscalPeriod)
    {
        if (!$journalEntry->is_posted) {
            return back()->with('error', 'لا يمكن عكس قيد غير مرحّل.');
        }
        if ($journalEntry->source === JournalEntry::SOURCE_REVERSAL) {
            return back()->with('error', 'لا يمكن عكس قيد عكسي.');
        }

        $fiscalPeriod->assertDateOpen(Carbon::today());
        $accounting->createReversalEntry($journalEntry);

        return back()->with('success', 'تم إنشاء قيد عكسي.');
    }
}
