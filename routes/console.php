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

// Publish a new Android APK for download on the portal's Mobile App page.
// Usage: php artisan miav:publish-apk /tmp/app.apk 1.4.7 19 [--channel=new-design] --notes="What changed" --notes="..."
// Channels: current (the "MIAV" app) and new-design (separate "MIAV New Design" app).
Artisan::command('miav:publish-apk {path} {version} {build} {--channel=current : current or new-design} {--notes=* : Release note lines}', function () {
    $src = $this->argument('path');
    if (!is_file($src)) { $this->error("File not found: $src"); return 1; }
    $channel = (string) $this->option('channel');
    if (!array_key_exists($channel, \App\Services\MobileAppRelease::CHANNELS)) { $this->error("Unknown channel: $channel (use current or new-design)"); return 1; }
    $dir = \App\Services\MobileAppRelease::dir();
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) { $this->error("Cannot create $dir"); return 1; }
    $file = 'man-in-a-van-' . ($channel === 'new-design' ? 'newdesign-' : '') . 'v' . $this->argument('version') . '+' . $this->argument('build') . '.apk';
    if (!copy($src, "$dir/$file")) { $this->error('Copy failed'); return 1; }
    $meta = [
        'version'     => $this->argument('version'),
        'build'       => (int) $this->argument('build'),
        'file'        => $file,
        'size'        => filesize("$dir/$file"),
        'sha256'      => hash_file('sha256', "$dir/$file"),
        'released_at' => now()->toDateTimeString(),
        'notes'       => array_values(array_filter($this->option('notes'))),
        'channel'     => $channel,
    ];
    file_put_contents(\App\Services\MobileAppRelease::metaFile($channel), json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    $this->info("Published [$channel] v{$meta['version']} ({$meta['build']}): {$meta['size']} bytes, sha256 {$meta['sha256']}");
    return 0;
})->purpose('Publish an Android APK for download on the Mobile App page');
