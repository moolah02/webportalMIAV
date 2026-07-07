@extends('layouts.app')
@section('title', 'Database Backups')

@push('styles')
<style>
    .card { background:#fff; border-radius:12px; padding:24px 28px; box-shadow:0 2px 10px rgba(0,0,0,.05); margin-bottom:24px; }
    .form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
    .form-input  { padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13.5px; color:#111827; }
    .form-input:focus { outline:none; border-color:#1a3a5c; }
    .form-hint   { font-size:11.5px; color:#9ca3af; margin-top:4px; }
    .btn-primary { background:#1a3a5c; color:#fff; border:none; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-primary:hover { background:#162f4a; }
    .btn-success { background:#059669; color:#fff; border:none; padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-success:hover { background:#047857; }
    .btn-sm-link { background:none; border:none; padding:4px 8px; border-radius:6px; font-size:12px; cursor:pointer; font-weight:600; }
    .btn-dl  { color:#1a3a5c; background:#eff6ff; }
    .btn-dl:hover  { background:#dbeafe; }
    .btn-del { color:#dc2626; background:#fef2f2; }
    .btn-del:hover { background:#fee2e2; }
    .backup-table { width:100%; border-collapse:collapse; font-size:13px; }
    .backup-table th { text-align:left; padding:10px 12px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; border-bottom:2px solid #f1f5f9; }
    .backup-table td { padding:11px 12px; border-bottom:1px solid #f8fafc; vertical-align:middle; }
    .backup-table tr:last-child td { border-bottom:none; }
    .backup-table tr:hover td { background:#fafafa; }
    .alert-success { background:#dcfce7; border:1px solid #86efac; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
    .alert-error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
    .empty-state   { padding:40px 0; text-align:center; color:#9ca3af; font-size:13px; }
</style>
@endpush

@section('content')

<div style="margin-bottom:20px;">
    <a href="{{ route('settings.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;">← Back to Settings</a>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="margin:0;font-size:20px;font-weight:700;color:#1a3a5c;">💾 Database Backups</h1>
        <p style="margin:6px 0 0;color:#6b7280;font-size:13px;">Scheduled and on-demand MySQL backups — stored on the server</p>
    </div>
    <form method="POST" action="{{ route('settings.backups.store') }}">
        @csrf
        <button type="submit" class="btn-success">⚡ Run Backup Now</button>
    </form>
</div>

@if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-error">❌ {{ session('error') }}</div>
@endif

{{-- Schedule settings --}}
<div class="card">
    <h3 style="margin:0 0 16px;font-size:15px;font-weight:700;color:#1a3a5c;">⏰ Backup Schedule</h3>
    <form method="POST" action="{{ route('settings.backups.settings') }}" style="display:flex;align-items:flex-end;gap:20px;flex-wrap:wrap;">
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
                value="{{ $settings->get('backup_retention_days')?->value ?? 14 }}" style="width:100px;">
        </div>
        <button type="submit" class="btn-primary">💾 Save</button>
    </form>
    <p class="form-hint" style="margin-top:12px;">
        ⚠️ The server cron must be running for scheduled backups to work:
        <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:11px;">* * * * * php /var/www/html/revival_production/artisan schedule:run >> /dev/null 2>&1</code>
    </p>
</div>

{{-- Backup list --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h3 style="margin:0;font-size:15px;font-weight:700;color:#1a3a5c;">📂 Existing Backups</h3>
        <span style="font-size:12px;color:#6b7280;">{{ $files->count() }} file(s) · Stored at <code style="background:#f1f5f9;padding:2px 5px;border-radius:4px;">storage/app/backups/</code></span>
    </div>

    @if($files->isEmpty())
        <div class="empty-state">No backups yet. Click <strong>Run Backup Now</strong> to create the first one.</div>
    @else
        <table class="backup-table">
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
                    <td style="font-family:monospace;font-size:12px;color:#374151;">{{ $file['name'] }}</td>
                    <td style="color:#6b7280;">{{ $file['size'] }}</td>
                    <td style="color:#6b7280;">{{ $file['created'] }}</td>
                    <td style="text-align:right;">
                        <a href="{{ route('settings.backups.download', $file['name']) }}" class="btn-sm-link btn-dl">⬇ Download</a>
                        <form method="POST" action="{{ route('settings.backups.destroy', $file['name']) }}" style="display:inline;" onsubmit="return confirm('Delete this backup?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm-link btn-del">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
