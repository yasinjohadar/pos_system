<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled Commands for Backup System
Schedule::command('backup:run-scheduled')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('backup:cleanup-expired')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping();

// تقارير وملخصات
Schedule::command('inventory:reorder-alert')->daily()->at('07:00');
Schedule::command('reports:daily-summary')->dailyAt('08:00');
Schedule::command('reports:weekly-summary')->weeklyOn(0, '09:00'); // Sunday 09:00
