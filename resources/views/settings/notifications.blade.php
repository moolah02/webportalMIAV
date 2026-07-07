@extends('layouts.app')
@section('title', 'Notification Settings')

@push('styles')
<style>
    .settings-form-card { background:#fff; border-radius:12px; padding:28px 32px; box-shadow:0 2px 10px rgba(0,0,0,.05); max-width:680px; }
    .toggle-row { display:flex; align-items:flex-start; justify-content:space-between; padding:14px 0; border-top:1px solid #f1f5f9; gap:24px; }
    .toggle-row:first-of-type { border-top:none; padding-top:0; }
    .toggle-info h4 { font-size:13.5px; font-weight:600; color:#111827; margin:0 0 3px; }
    .toggle-info p  { font-size:12px; color:#9ca3af; margin:0; }
    .toggle-switch  { position:relative; display:inline-block; width:44px; height:24px; flex-shrink:0; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; inset:0; background:#d1d5db; border-radius:24px; cursor:pointer; transition:.2s; }
    .toggle-slider:before { position:absolute; content:''; height:18px; width:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.2s; }
    input:checked + .toggle-slider { background:#1a3a5c; }
    input:checked + .toggle-slider:before { transform:translateX(20px); }
    .form-group { margin-bottom:20px; }
    .form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
    .form-input { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13.5px; color:#111827; }
    .form-input:focus { outline:none; border-color:#1a3a5c; box-shadow:0 0 0 3px rgba(26,58,92,.08); }
    .form-hint  { font-size:11.5px; color:#9ca3af; margin-top:4px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .btn-primary { background:#1a3a5c; color:#fff; border:none; padding:10px 22px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-primary:hover { background:#162f4a; }
    .alert-success { background:#dcfce7; border:1px solid #86efac; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
    .callout-info  { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; padding:12px 16px; border-radius:8px; margin-bottom:24px; font-size:13px; }
    .section-title { font-size:13px; font-weight:700; color:#1a3a5c; margin:28px 0 4px; }
    .section-divider { border:none; border-top:1px solid #f1f5f9; margin:4px 0 20px; }
</style>
@endpush

@section('content')

<div style="margin-bottom:20px;">
    <a href="{{ route('settings.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;">← Back to Settings</a>
</div>

<div style="margin-bottom:24px;">
    <h1 style="margin:0;font-size:20px;font-weight:700;color:#1a3a5c;">🔔 Notification Settings</h1>
    <p style="margin:6px 0 0;color:#6b7280;font-size:13px;">Control which events trigger in-app and email notifications</p>
</div>

@if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
@endif

@php
    $mailConfigured = !empty(\App\Models\SystemSetting::get('mail_from_address'));
@endphp

@if(!$mailConfigured)
<div class="callout-info">
    ℹ️ Email notifications require SMTP to be configured first.
    <a href="{{ route('settings.email') }}" style="font-weight:600;color:#1e40af;">Configure Email →</a>
</div>
@endif

<div class="settings-form-card">
    <form method="POST" action="{{ route('settings.notifications.update') }}">
        @csrf

        <p class="section-title">Ticket Notifications</p>
        <hr class="section-divider">

        <div class="toggle-row">
            <div class="toggle-info">
                <h4>New Ticket Created</h4>
                <p>Email admins &amp; the assigned technician when a ticket is opened</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_new_ticket" value="1" {{ $settings->get('notify_new_ticket')?->value ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="toggle-row">
            <div class="toggle-info">
                <h4>Ticket Status Changed</h4>
                <p>Email the ticket creator when a status update is made</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_ticket_status" value="1" {{ $settings->get('notify_ticket_status')?->value ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="toggle-row">
            <div class="toggle-info">
                <h4>Ticket Overdue Alert</h4>
                <p>Email admins when a ticket has been open past the threshold below</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_ticket_overdue" value="1" {{ $settings->get('notify_ticket_overdue')?->value ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group" style="margin-top:12px;padding-left:4px;">
            <label class="form-label">Overdue Threshold (hours)</label>
            <input class="form-input" type="number" name="notify_overdue_hours" min="1" max="720"
                value="{{ $settings->get('notify_overdue_hours')?->value ?? 24 }}" style="max-width:160px;">
            <div class="form-hint">Hours after creation before a ticket is considered overdue</div>
        </div>

        <p class="section-title" style="margin-top:24px;">License Notifications</p>
        <hr class="section-divider">

        <div class="toggle-row">
            <div class="toggle-info">
                <h4>License Expiry Warning</h4>
                <p>Email admins before a business license expires</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_license_expiry" value="1" {{ $settings->get('notify_license_expiry')?->value ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group" style="margin-top:12px;padding-left:4px;">
            <label class="form-label">Expiry Warning (days before)</label>
            <input class="form-input" type="number" name="notify_license_expiry_days" min="1" max="365"
                value="{{ $settings->get('notify_license_expiry_days')?->value ?? 30 }}" style="max-width:160px;">
            <div class="form-hint">Send the warning this many days before the license expiry date</div>
        </div>

        <p class="section-title" style="margin-top:24px;">Admin Recipients</p>
        <hr class="section-divider">

        <div class="form-group">
            <label class="form-label">Admin Role Names</label>
            <input class="form-input" type="text" name="notify_admin_roles"
                value="{{ $settings->get('notify_admin_roles')?->value ?? 'admin,manager' }}">
            <div class="form-hint">Comma-separated role names that receive admin-level alerts (e.g. admin,manager)</div>
        </div>

        <button type="submit" class="btn-primary">💾 Save Notification Settings</button>
    </form>
</div>

@endsection
