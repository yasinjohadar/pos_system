<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrialBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view')->only('index');
    }

    /**
     * ميزان المراجعة: أرصدة الحسابات من القيود (مدين / دائن).
     */
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $lines = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereBetween('journal_entries.entry_date', [$from, $to])
            ->where('journal_entries.is_posted', true)
            ->select([
                'journal_entry_lines.account_id',
                DB::raw('SUM(journal_entry_lines.debit) as total_debit'),
                DB::raw('SUM(journal_entry_lines.credit) as total_credit'),
            ])
            ->groupBy('journal_entry_lines.account_id')
            ->get();

        $accountIds = $lines->pluck('account_id')->unique()->filter();
        $accounts = ChartOfAccount::whereIn('id', $accountIds)->get()->keyBy('id');

        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($lines as $line) {
            $account = $accounts->get($line->account_id);
            if (!$account) {
                continue;
            }
            $debit = (float) $line->total_debit;
            $credit = (float) $line->total_credit;
            $balance = $debit - $credit;
            $totalDebit += $debit;
            $totalCredit += $credit;
            $rows[] = (object) [
                'account_code' => $account->code,
                'account_name' => $account->name,
                'account_type' => $account->type,
                'debit' => $debit,
                'credit' => $credit,
                'balance_debit' => $balance > 0 ? $balance : 0,
                'balance_credit' => $balance < 0 ? -$balance : 0,
            ];
        }

        if ($request->input('format') === 'csv') {
            $filename = 'trial-balance-' . $from . '-to-' . $to . '.csv';
            return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['كود الحساب', 'اسم الحساب', 'نوع الحساب', 'مدين', 'دائن', 'رصيد مدين', 'رصيد دائن']);
                foreach ($rows as $row) {
                    fputcsv($out, [$row->account_code, $row->account_name, $row->account_type, $row->debit, $row->credit, $row->balance_debit, $row->balance_credit]);
                }
                fclose($out);
            }, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return view('admin.pages.reports.trial-balance.index', compact('rows', 'from', 'to', 'totalDebit', 'totalCredit'));
    }
}
