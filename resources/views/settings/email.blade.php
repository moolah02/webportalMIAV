@extends('layouts.app')
@section('title', 'Email Settings')

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
    .ss-card-body { padding: 18px 20px; display: grid; gap: 16px; }
    .ss-card-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 20px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); border-radius: 0 0 10px 10px; }
    .ss-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .ss .form-group { margin: 0; }
    .ss .form-label { display: block; margin-bottom: 5px; }
    .ss .form-input { width: 100%; height: 36px; padding: 0 11px; font-size: 13.5px; }
    .ss select.form-input { padding-right: 28px; }
    .ss .form-hint { margin-top: 4px; font-size: 12px; color: var(--mv-muted); }
    .ss .form-hint:empty { display: none; }
    .ss-toggle { display: flex; align-items: center; justify-content: space-between; gap: 24px; cursor: pointer; }
    .ss-toggle + .ss-toggle { padding-top: 14px; border-top: 1px solid var(--mv-line); }
    .ss-toggle-title { font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
    .ss-toggle-desc { font-size: 12.5px; color: var(--mv-muted); margin-top: 2px; }
    .ss-switch { position: relative; width: 38px; height: 22px; flex-shrink: 0; }
    .ss-switch input { position: absolute; inset: 0; opacity: 0; margin: 0; cursor: pointer; z-index: 1; }
    .ss-switch span { position: absolute; inset: 0; border-radius: 11px; background: var(--mv-line-strong); transition: background .15s; }
    .ss-switch span::before { content: ""; position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(22,32,44,.2); transition: transform .15s; }
    .ss-switch input:checked + span { background: var(--mv-accent); }
    .ss-switch input:checked + span::before { transform: translateX(16px); }
    .ss-switch input:focus-visible + span { outline: 2px solid var(--mv-accent); outline-offset: 2px; }
    .ss-inline { display: flex; gap: 10px; align-items: flex-end; }
    .ss-inline .form-group { flex: 1; }
    .ss button .mv-i, .ss a .mv-i { width: 15px; height: 15px; }
    .ss button { display: inline-flex; align-items: center; gap: 6px; }
    @media (max-width: 900px) { .ss-section { grid-template-columns: minmax(0, 1fr); gap: 12px; } .ss-row { grid-template-columns: minmax(0, 1fr); } }
</style>
@endpush

@section('content')
<div class="ss">

    <p class="ss-intro">Configure SMTP for outgoing emails (notifications, alerts, ticket updates)</p>

    {{-- SMTP --}}
    <section class="ss-section is-first">
        <div>
            <h2>SMTP Server</h2>
            <p class="ss-desc">The mail server used to send every email from the portal. Ask your email provider for these values.</p>
        </div>
        <form method="POST" action="{{ route('settings.email.update') }}" class="ss-card">
            @csrf
            @method('POST')
            <div class="ss-card-body">
                <div class="ss-row">
                    <div class="form-group">
                        <label class="form-label">SMTP Host</label>
                        <input class="form-input" type="text" name="mail_host"
                            value="{{ $settings->get('mail_host')?->value ?? '' }}"
                            placeholder="smtp.gmail.com">
                        <div class="form-hint">{{ $settings->get('mail_host')?->description }}</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP Port</label>
                        <input class="form-input" type="number" name="mail_port"
                            value="{{ $settings->get('mail_port')?->value ?? 587 }}"
                            placeholder="587">
                        <div class="form-hint">{{ $settings->get('mail_port')?->description }}</div>
                    </div>
                </div>

                <div class="ss-row">
                    <div class="form-group">
                        <label class="form-label">SMTP Username</label>
                        <input class="form-input" type="text" name="mail_username"
                            value="{{ $settings->get('mail_username')?->value ?? '' }}"
                            placeholder="you@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP Password</label>
                        <input class="form-input" type="password" name="mail_password"
                            placeholder="{{ $settings->get('mail_password')?->value ? '••••••••' : 'Enter password' }}">
                        <div class="form-hint">Leave blank to keep existing password</div>
                    </div>
                </div>

                <div class="ss-row">
                    <div class="form-group">
                        <label class="form-label">Encryption</label>
                        <select class="form-input" name="mail_encryption">
                            @foreach(['tls'=>'TLS (recommended)','ssl'=>'SSL','none'=>'None'] as $val => $label)
                                <option value="{{ $val }}" {{ ($settings->get('mail_encryption')?->value ?? 'tls') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">From Address</label>
                        <input class="form-input" type="email" name="mail_from_address"
                            value="{{ $settings->get('mail_from_address')?->value ?? '' }}"
                            placeholder="noreply@yourcompany.com">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">From Name</label>
                    <input class="form-input" type="text" name="mail_from_name"
                        value="{{ $settings->get('mail_from_name')?->value ?? 'Revival Technologies' }}"
                        placeholder="Revival Technologies">
                </div>
            </div>
            <div class="ss-card-foot">
                <button type="submit" class="btn-primary"><svg class="mv-i" aria-hidden="true"><use href="#i-save"/></svg> Save Settings</button>
            </div>
        </form>
    </section>

    {{-- Welcome email --}}
    <section class="ss-section">
        <div>
            <h2>New Employee Welcome Email</h2>
            <p class="ss-desc">What happens when an administrator creates a new employee account.</p>
        </div>
        <form method="POST" action="{{ route('settings.email.update') }}" class="ss-card">
            @csrf
            <div class="ss-card-body">
                <label class="ss-toggle">
                    <div>
                        <div class="ss-toggle-title">Send welcome email when employee is created</div>
                        <div class="ss-toggle-desc">Emails login credentials to the new employee (requires SMTP above)</div>
                    </div>
                    <input type="hidden" name="welcome_email_enabled" value="0">
                    <span class="ss-switch">
                        <input type="checkbox" name="welcome_email_enabled" value="1"
                            {{ \App\Models\SystemSetting::get('welcome_email_enabled') ? 'checked' : '' }}>
                        <span></span>
                    </span>
                </label>
                <label class="ss-toggle">
                    <div>
                        <div class="ss-toggle-title">Require password change on first login</div>
                        <div class="ss-toggle-desc">Employee must set a new password immediately after their first login</div>
                    </div>
                    <input type="hidden" name="welcome_email_force_reset" value="0">
                    <span class="ss-switch">
                        <input type="checkbox" name="welcome_email_force_reset" value="1"
                            {{ \App\Models\SystemSetting::get('welcome_email_force_reset') ? 'checked' : '' }}>
                        <span></span>
                    </span>
                </label>
            </div>
            <div class="ss-card-foot">
                <button type="submit" class="btn-primary"><svg class="mv-i" aria-hidden="true"><use href="#i-save"/></svg> Save</button>
            </div>
        </form>
    </section>

    {{-- Test --}}
    <section class="ss-section">
        <div>
            <h2>Send Test Email</h2>
            <p class="ss-desc">Check the saved SMTP settings by sending a message to any address.</p>
        </div>
        <form method="POST" action="{{ route('settings.email.test') }}" class="ss-card">
            @csrf
            <div class="ss-card-body">
                <div class="ss-inline">
                    <div class="form-group">
                        <label class="form-label">Send test to</label>
                        <input class="form-input" type="email" name="test_email" placeholder="recipient@example.com">
                    </div>
                    <button type="submit" class="btn-secondary" style="height:36px;white-space:nowrap;"><svg class="mv-i" aria-hidden="true"><use href="#i-send"/></svg> Send Test</button>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection
