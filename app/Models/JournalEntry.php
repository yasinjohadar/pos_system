<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_number',
        'entry_date',
        'description',
        'reference_type',
        'reference_id',
        'is_posted',
        'source',
        'reversed_entry_id',
        'fiscal_year_id',
        'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_posted' => 'boolean',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public const SOURCE_AUTO = 'auto';
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_CLOSING = 'closing';
    public const SOURCE_REVERSAL = 'reversal';

    public function reversedEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_entry_id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function totalDebit(): float
    {
        return (float) $this->lines->sum('debit');
    }

    public function totalCredit(): float
    {
        return (float) $this->lines->sum('credit');
    }

    public function isBalanced(): bool
    {
        return abs($this->totalDebit() - $this->totalCredit()) < 0.01;
    }

    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    public static function generateEntryNumber(): string
    {
        $prefix = 'JE-' . date('Ymd') . '-';
        $last = static::where('entry_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('entry_number');
        $seq = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
