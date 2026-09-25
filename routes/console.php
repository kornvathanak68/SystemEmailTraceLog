<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Schedule::command('report:fetch')
    ->monthlyOn(2, '08:00')
    ->timezone(config('delivery.timezone', 'Asia/Phnom_Penh'))
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('Scheduled report:fetch failed'));

// Guardrail — alert if no file arrived by the 5th of the month
Schedule::call(function () {
    $period = now()->format('Y-m');
    $exists = \App\Models\ReportRun::where('period', $period)->exists();

    if (!$exists) {
        Log::warning("No report file received for period {$period} by day 5");
        // TODO: Mail::to(config('delivery.alert_admin_email'))->send(new AdminAlert(...));
    }
})->monthlyOn(config('delivery.alert_if_no_file_by_day', 5), '09:00')
  ->timezone(config('delivery.timezone', 'Asia/Phnom_Penh'))
  ->name('report:alert-no-file')
  ->withoutOverlapping();
