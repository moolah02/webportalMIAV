@extends('layouts.app')
@section('title', 'Database Backups')

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Settings</a>
@endsection

@push('styles')
<style>
    .bk { display: grid; gap: 20px; max-width: 1080px; }
    .bk .mv-i { width: 15px; height: 15px; }
    .bk-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .bk-toolbar p { margin: 0; font-size: 13px; color: var(--mv-muted); }
    .bk button, .bk a.bk-link { display: inline-flex; align-items: center; gap: 6px; }
    .bk-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
    .bk-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 18px; border-bottom: 1px solid var(--mv-line); flex-wrap: wrap; }
    .bk-card-head h3 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .bk-card-head span { font-size: 12.5px; color: var(--mv-muted); }
    .bk-card-body { padding: 16px 18px; }
    .bk-form { display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; }
    .bk .form-label { display: block; margin-bottom: 5px; }
    .bk .form-input { height: 36px; padding: 0 11px; font-size: 13.5px; }
    .bk-hint { display: flex; gap: 8px; align-items: flex-start; margin: 14px 0 0; font-size: 12.5px; color: var(--mv-muted); line-height: 1.6; }
    .bk-hint .mv-i { color: var(--mv-warn); margin-top: 3px; flex-shrink: 0; }
    .bk code { font-family: var(--mv-mono); font-size: 12px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 6px; color: var(--mv-ink-2); }
    .backup-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .backup-table th { text-align: left; white-space: nowrap; }
    .bk-file { font-family: var(--mv-mono); font-size: 12.5px; color: var(--mv-ink); }
    .bk-muted { color: var(--mv-muted); white-space: nowrap; font-variant-numeric: tabular-nums; }
    .bk-actions { display: inline-flex; gap: 6px; justify-content: flex-end; }
    .bk-empty { padding: 36px 16px; text-align: center; font-size: 13.5px; color: var(--mv-muted); }
    .bk-empty .mv-i { width: 28px; height: 28px; display: block; margin: 0 auto 8px; color: var(--mv-line-strong); }
</style>
@endpush

@section('content')
<div class="bk">

    <div class="bk-toolbar">
        <p>Scheduled and on-demand MySQL backups — stored on the server</p>
        <form method="POST" action="{{ route('settings.backups.store') }}">
            @csrf
            <button type="submit" class="btn-primary"><svg class="mv-i" aria-hidden="true"><use href="#i-database"/></svg> Run Backup Now</button>
        </form>
    </div>

    {{-- Schedule --}}
    <div class="bk-card">
        <div class="bk-card-head"><h3>Backup Schedule</h3></div>
        <div class="bk-card-body">
            <form method="POST" action="{{ route('settings.backups.settings') }}" class="bk-form">
                @csrf
                <div>
                    <label class="form-label">Frequency</label>
                    <select class="form-input" name="backup_frequency">
                        @foreach(['off'=>'Disabled','daily'=>'Daily (2:00 AM)','weekly'=>'Weekly (Sunday 2:00 AM)'] as $val => $label)
                            <option value="{{ $val }}" {{ ($settings->get('backup_frequency')?->value ?? 'daily') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Keep backups for (days)</label>
                    <input class="form-input" type="number" name="backup_retention_days" min="1" max="365"
                        value="{{ $settings->get('backup_retention_days')?->value ?? 14 }}" style="width:110px;">
                </div>
                <button type="submit" class="btn-secondary" style="height:36px;"><svg class="mv-i" aria-hidden="true"><use href="#i-save"/></svg> Save</button>
            </form>
            <p class="bk-hint">
                <svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg>
                <span>The server cron must be running for scheduled backups to work:
                <code>* * * * * php /var/www/html/revival_production/artisan schedule:run >> /dev/null 2>&1</code></span>
            </p>
        </div>
    </div>

    {{-- Existing backups --}}
    <div class="bk-card">
        <div class="bk-card-head">
            <h3>Existing Backups</h3>
            <span>{{ $files->count() }} file(s) · Stored at <code>storage/app/backups/</code></span>
        </div>

        @if($files->isEmpty())
            <div class="bk-empty">
                <svg class="mv-i" aria-hidden="true"><use href="#i-database"/></svg>
                No backups yet. Click <strong>Run Backup Now</strong> to create the first one.
            </div>
        @else
            <div style="overflow-x:auto;">
            <table class="backup-table ui-table">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Size</th>
                        <th>Created</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                    <tr>
                        <td class="bk-file">{{ $file['name'] }}</td>
                        <td class="bk-muted">{{ $file['size'] }}</td>
                        <td class="bk-muted">{{ $file['created'] }}</td>
                        <td style="text-align:right;">
                            <div class="bk-actions">
                                <a href="{{ route('settings.backups.download', $file['name']) }}" class="btn-secondary btn-sm bk-link"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Download</a>
                                <form method="POST" action="{{ route('settings.backups.destroy', $file['name']) }}" style="display:inline;" onsubmit="return confirm('Delete this backup?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-trash"/></svg> Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
</div>
@endsection
