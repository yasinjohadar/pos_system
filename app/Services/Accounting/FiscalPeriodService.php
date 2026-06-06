<?php

namespace App\Services\Accounting;

use App\Models\FiscalYear;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class FiscalPeriodService
{
    public function assertDateOpen(Carbon $date): void
    {
        $year = FiscalYear::where('start_date', '<=', $date->toDateString())
            ->where('end_date', '>=', $date->toDateString())
            ->where('is_closed', true)
            ->first();

        if ($year) {
            throw ValidationException::withMessages([
                'entry_date' => 'الفترة المحاسبية مغلقة (' . $year->name . ').',
            ]);
        }
    }

    public function fiscalYearForDate(Carbon $date): ?FiscalYear
    {
        return FiscalYear::forDate($date);
    }
}
