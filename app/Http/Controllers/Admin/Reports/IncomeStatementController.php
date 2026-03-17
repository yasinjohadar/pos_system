<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeStatementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view')->only('index');
    }

    /**
     * قائمة الدخل المبسطة: إيرادات - مصروفات = صافي الدخل.
     */
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $revenue = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.id')
            ->whereBetween('journal_entries.entry_date', [$from, $to])
            ->where('journal_entries.is_posted', true)
            ->where('chart_of_accounts.type', ChartOfAccount::TYPE_REVENUE)
            ->select(DB::raw('COALESCE(SUM(journal_entry_lines.credit - journal_entry_lines.debit), 0) as total'))
            ->value('total');
        $revenue = (float) $revenue;

        $expense = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.id')
            ->whereBetween('journal_entries.entry_date', [$from, $to])
            ->where('journal_entries.is_posted', true)
            ->where('chart_of_accounts.type', ChartOfAccount::TYPE_EXPENSE)
            ->select(DB::raw('COALESCE(SUM(journal_entry_lines.debit - journal_entry_lines.credit), 0) as total'))
            ->value('total');
        $expense = (float) $expense;

        $netIncome = $revenue - $expense;

        if ($request->input('format') === 'csv') {
            $filename = 'income-statement-' . $from . '-to-' . $to . '.csv';
            return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($revenue, $expense, $netIncome) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['البند', 'المبلغ']);
                fputcsv($out, ['الإيرادات', $revenue]);
                fputcsv($out, ['المصروفات', $expense]);
                fputcsv($out, ['صافي الدخل', $netIncome]);
                fclose($out);
            }, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return view('admin.pages.reports.income-statement.index', compact('from', 'to', 'revenue', 'expense', 'netIncome'));
    }
}
