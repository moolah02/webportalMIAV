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

// Rebuild technician_visits (report builder "Technician Visits (Detail)") from
// mobile visits. Safe to re-run; --dry-run previews without writing.
Artisan::command('miav:sync-technician-visits {--dry-run : Show what would change without writing}', function () {
    $mirror = app(\App\Services\TechnicianVisitMirror::class);
    $dry    = (bool) $this->option('dry-run');
    $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0];

    \App\Models\Visit::with('visitTerminal')->chunkById(200, function ($visits) use ($mirror, $dry, &$counts) {
        foreach ($visits as $visit) {
            if (!$dry) {
                $counts[$mirror->sync($visit)]++;
                continue;
            }
            $exists = \App\Models\TechnicianVisit::where('visit_id', (string) $visit->id)->exists();
            $key    = !$visit->visitTerminal ? 'skipped' : ($exists ? 'updated' : 'created');
            $counts[$key]++;
            if ($key === 'created') {
                $this->line("  would create mirror for visit #{$visit->id} ({$visit->merchant_name})");
            }
        }
    });

    $this->info(($dry ? '[dry run] ' : '') . json_encode($counts));

    // Mirror rows whose visit no longer exists (deleted before the delete hook existed).
    $orphans = \Illuminate\Support\Facades\DB::table('technician_visits as tv')
        ->leftJoin('visits as v', \Illuminate\Support\Facades\DB::raw('v.id'), '=', \Illuminate\Support\Facades\DB::raw('tv.visit_id'))
        ->whereNotNull('tv.visit_id')
        ->whereNull('v.id')
        ->pluck('tv.id');
    if ($orphans->isNotEmpty()) {
        $this->warn('Orphan technician_visits rows (visit deleted), not touched: ' . $orphans->implode(', '));
    }
})->purpose('Sync technician_visits report rows from mobile visits');
