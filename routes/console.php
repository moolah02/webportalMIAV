<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Backup: runs daily; the command skips if frequency='off' or weekly+not Sunday
Schedule::command('backup:run')->dailyAt('02:00');

// Ticket & license alerts
Schedule::command('tickets:check-overdue')->hourly();
Schedule::command('licenses:check-expiry')->dailyAt('08:00');
