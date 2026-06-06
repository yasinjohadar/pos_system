<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use App\Services\Accounting\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view');
    }

    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $cashAccount = ChartOfAccount::findByCode(AccountingService::CODE_CASH);
        $operating = 0;
        $investing = 0;
        $financing = 0;

        if ($cashAccount) {
            $lines = JournalEntryLine::query()
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entry_lines.account_id', $cashAccount->id)
                ->where('journal_entries.is_posted', true)
                ->whereBetween('journal_entries.entry_date', [$from, $to])
                ->select([
                    'journal_entries.description',
                    DB::raw('SUM(journal_entry_lines.debit) - SUM(journal_entry_lines.credit) as net'),
                ])
                ->groupBy('journal_entries.id', 'journal_entries.description')
                ->get();

            foreach ($lines as $line) {
                $net = (float) $line->net;
                $operating += $net;
            }
        }

        $netChange = $operating + $investing + $financing;

        return view('admin.pages.reports.cash-flow.index', compact(
            'from', 'to', 'operating', 'investing', 'financing', 'netChange'
        ));
    }
}
