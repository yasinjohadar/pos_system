<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankReconciliationItem extends Model
{
    protected $fillable = [
        'bank_reconciliation_id', 'journal_entry_line_id', 'transaction_date',
        'description', 'amount', 'is_cleared',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'is_cleared' => 'boolean',
    ];

    public function reconciliation() { return $this->belongsTo(BankReconciliation::class, 'bank_reconciliation_id'); }
    public function journalEntryLine() { return $this->belongsTo(JournalEntryLine::class); }
}
