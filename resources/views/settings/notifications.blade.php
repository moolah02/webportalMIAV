@extends('layouts.app')
@section('title', 'Notification Settings')

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Settings</a>
@endsection

@push('styles')
<style>
    .ss { display: grid; gap: 24px; max-width: 1080px; }
    .ss-intro { margin: 0; font-size: 13px; color: var(--mv-muted); }
    .ss-section { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 28px; padding-top: 24px; border-top: 1px solid var(--mv-line); }
    .ss-section.is-first { border-top: 0; padding-top: 0; }
    .ss-section h2 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
    .ss-section p.ss-desc { margin: 4px 0 0; font-size: 13px; line-height: 1.5; color: var(--mv-muted); }
    .ss-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
    .ss-card-body { padding: 16px 20px; display: grid; gap: 14px; }
    .ss-foot { display: flex; justify-content: flex-end; padding-top: 20px; border-top: 1px solid var(--mv-line); }
    .ss .form-group { margin: 0; }
    .ss .form-label { display: block; margin-bottom: 5px; }
    .ss .form-input { width: 100%; height: 36px; padding: 0 11px; font-size: 13.5px; }
    .ss .form-hint { margin-top: 4px; font-size: 12px; color: var(--mv-muted); }
    .toggle-row { display: flex; align-items: center; justify-content: space-between; gap: 24px; }
    .toggle-row + .toggle-row, .toggle-row + .form-group, .form-group + .toggle-row { padding-top: 14px; border-top: 1px solid var(--mv-line); }
    .toggle-info h4 { margin: 0; font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
    .toggle-info p { margin: 2px 0 0; font-size: 12.5px; color: var(--mv-muted); }
    .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; flex-shrink: 0; }
    .toggle-switch input { position: absolute; inset: 0; opacity: 0; margin: 0; cursor: pointer; z-index: 1; }
    .toggle-slider { position: absolute; inset: 0; border-radius: 11px; background: var(--mv-line-strong); transition: background .15s; }
    .toggle-slider::before { content: ""; position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(22,32,44,.2); transition: transform .15s; }
    .toggle-switch input:checked + .toggle-slider { background: var(--mv-accent); }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(16px); }
    .toggle-switch input:focus-visible + .toggle-slider { outline: 2px solid var(--mv-accent); outline-offset: 2px; }
    .ss-note { display: flex; align-items: center; gap: 10px; }
    .ss-note a { font-weight: 500; color: inherit; text-decoration: underline; }
    .ss button { display: inline-flex; align-items: center; gap: 6px; }
    .ss button .mv-i { width: 15px; height: 15px; }
    @media (max-width: 900px) { .ss-section { grid-template-columns: minmax(0, 1fr); gap: 12px; } }
</style>
@endpush

@section('content')
@php
    $mailConfigured = !empty(\App\Models\SystemSetting::get('mail_from_address'));
@endphp
<div class="ss">

    <p class="ss-intro">Control which events trigger in-app and email notifications</p>

    @if(!$mailConfigured)
    <div class="alert alert-info ss-note" style="padding:11px 14px;border:1px solid;">
        <svg class="mv-i" aria-hidden="true"><use href="#i-info"/></svg>
        <span>Email notifications require SMTP to be configured first. <a href="{{ route('settings.email') }}">Configure Email</a></span>
    </div>
    @endif

    <form method="POST" action="{{ route('settings.notifications.update') }}" style="display:grid;gap:24px;">
        @csrf

        <section class="ss-section is-first">
            <div>
                <h2>Ticket Notifications</h2>
                <p class="ss-desc">Emails sent as tickets are opened, updated and left unresolved.</p>
            </div>
            <div class="ss-card">
                <div class="ss-card-body">
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

                    <div class="form-group">
                        <label class="form-label">Overdue Threshold (hours)</label>
                        <input class="form-input" type="number" name="notify_overdue_hours" min="1" max="720"
                            value="{{ $settings->get('notify_overdue_hours')?->value ?? 24 }}" style="max-width:160px;">
                        <div class="form-hint">Hours after creation before a ticket is considered overdue</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ss-section">
            <div>
                <h2>License Notifications</h2>
                <p class="ss-desc">Warnings before a business license runs out.</p>
            </div>
            <div class="ss-card">
                <div class="ss-card-body">
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

                    <div class="form-group">
                        <label class="form-label">Expiry Warning (days before)</label>
                        <input class="form-input" type="number" name="notify_license_expiry_days" min="1" max="365"
                            value="{{ $settings->get('notify_license_expiry_days')?->value ?? 30 }}" style="max-width:160px;">
                        <div class="form-hint">Send the warning this many days before the license expiry date</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ss-section">
            <div>
                <h2>Admin Recipients</h2>
                <p class="ss-desc">Who receives admin-level alerts.</p>
            </div>
            <div class="ss-card">
                <div class="ss-card-body">
                    <div class="form-group">
                        <label class="form-label">Admin Role Names</label>
                        <input class="form-input" type="text" name="notify_admin_roles"
                            value="{{ $settings->get('notify_admin_roles')?->value ?? 'admin,manager' }}">
                        <div class="form-hint">Comma-separated role names that receive admin-level alerts (e.g. admin,manager)</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="ss-foot">
            <button type="submit" class="btn-primary"><svg class="mv-i" aria-hidden="true"><use href="#i-save"/></svg> Save Notification Settings</button>
        </div>
    </form>
</div>
@endsection
