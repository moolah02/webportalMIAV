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

// Mints a token that can read the reporting feed and do nothing else.
//   php artisan miav:bi-token "Mbonisi - Power BI"
//   php artisan miav:bi-token --list
//   php artisan miav:bi-token --revoke=7
Artisan::command('miav:bi-token {name? : Who the token is for} {--list : Show the tokens already issued} {--revoke= : Withdraw a token by id}', function () {
    $owner = \App\Models\Employee::orderBy('id')->first();
    if (! $owner) {
        $this->error('There are no employees, so there is nobody to issue a token against.');
        return 1;
    }

    if ($id = $this->option('revoke')) {
        $token = \Laravel\Sanctum\PersonalAccessToken::find($id);
        if (! $token) {
            $this->error("No token with id {$id}.");
            return 1;
        }
        $this->warn("Withdrawing «{$token->name}» (id {$token->id}) — it stops working immediately.");
        $token->delete();
        $this->info('Withdrawn.');
        return 0;
    }

    if ($this->option('list')) {
        $rows = \Laravel\Sanctum\PersonalAccessToken::query()
            ->get(['id', 'name', 'abilities', 'last_used_at', 'created_at'])
            ->map(fn ($t) => [
                $t->id,
                $t->name,
                implode(',', (array) $t->abilities),
                $t->last_used_at?->diffForHumans() ?? 'never used',
                $t->created_at?->toDateString(),
            ]);
        $this->table(['id', 'name', 'can do', 'last used', 'issued'], $rows);
        return 0;
    }

    $name = $this->argument('name');
    if (! $name) {
        $this->error('Say who the token is for, e.g. php artisan miav:bi-token "Mbonisi - Power BI"');
        return 1;
    }

    // `bi:read` and nothing else: this token cannot file a visit, change a
    // terminal or sign in to the portal.
    $plain = $owner->createToken($name, ['bi:read'])->plainTextToken;

    $this->newLine();
    $this->info("Token for «{$name}»:");
    $this->line($plain);
    $this->newLine();
    $this->warn('This is shown once and never again. Send it over something private — not WhatsApp.');
    $this->line('It can read '.url('/api/bi').' and nothing else.');
    $this->line('Withdraw it later with: php artisan miav:bi-token --revoke=<id>');

    return 0;
})->purpose('Issue, list or withdraw a read-only token for the reporting feed');
