<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GeneralLedgerService
{
    public function getLedger(?int $accountId, string $from, string $to): Collection
    {
        $query = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.entry_date', [$from, $to])
            ->select([
                'journal_entry_lines.*',
                'journal_entries.entry_number',
                'journal_entries.entry_date',
                'journal_entries.description as entry_description',
            ])
            ->orderBy('journal_entries.entry_date')
            ->orderBy('journal_entries.id');

        if ($accountId) {
            $query->where('journal_entry_lines.account_id', $accountId);
        }

        return $query->get();
    }

    public function getAccountStatement(int $accountId, string $from, string $to): array
    {
        $account = ChartOfAccount::findOrFail($accountId);

        $openingDebit = (float) JournalEntryLine::query()
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entries.is_posted', true)
            ->where('journal_entries.entry_date', '<', $from)
            ->sum('journal_entry_lines.debit');

        $openingCredit = (float) JournalEntryLine::query()
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entries.is_posted', true)
            ->where('journal_entries.entry_date', '<', $from)
            ->sum('journal_entry_lines.credit');

        $openingBalance = $openingDebit - $openingCredit;

        $lines = $this->getLedger($accountId, $from, $to);
        $running = $openingBalance;
        $rows = [];

        foreach ($lines as $line) {
            $debit = (float) $line->debit;
            $credit = (float) $line->credit;
            $running += $debit - $credit;
            $rows[] = (object) [
                'entry_number' => $line->entry_number,
                'entry_date' => $line->entry_date,
                'description' => $line->description ?: $line->entry_description,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $running,
            ];
        }

        return [
            'account' => $account,
            'opening_balance' => $openingBalance,
            'rows' => $rows,
            'closing_balance' => $running,
        ];
    }

    public function getBalancesByType(string $asOfDate): array
    {
        $lines = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_entry_lines.account_id', '=', 'chart_of_accounts.id')
            ->where('journal_entries.is_posted', true)
            ->where('journal_entries.entry_date', '<=', $asOfDate)
            ->select([
                'chart_of_accounts.id',
                'chart_of_accounts.code',
                'chart_of_accounts.name',
                'chart_of_accounts.type',
                DB::raw('SUM(journal_entry_lines.debit) as total_debit'),
                DB::raw('SUM(journal_entry_lines.credit) as total_credit'),
            ])
            ->groupBy('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.type')
            ->get();

        $grouped = [];
        foreach ($lines as $line) {
            $balance = (float) $line->total_debit - (float) $line->total_credit;
            $grouped[$line->type][] = (object) [
                'code' => $line->code,
                'name' => $line->name,
                'balance' => abs($balance),
                'side' => $balance >= 0 ? 'debit' : 'credit',
            ];
        }

        return $grouped;
    }
}
