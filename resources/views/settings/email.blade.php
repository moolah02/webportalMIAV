@extends('layouts.app')
@section('title', 'Email Settings')

@push('styles')
<style>
    .settings-form-card { background:#fff; border-radius:12px; padding:28px 32px; box-shadow:0 2px 10px rgba(0,0,0,.05); max-width:680px; }
    .form-group { margin-bottom:20px; }
    .form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
    .form-hint  { font-size:11.5px; color:#9ca3af; margin-top:4px; }
    .form-input { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13.5px; color:#111827; transition:border-color .15s; }
    .form-input:focus { outline:none; border-color:#1a3a5c; box-shadow:0 0 0 3px rgba(26,58,92,.08); }
    .form-row  { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .btn-primary  { background:#1a3a5c; color:#fff; border:none; padding:10px 22px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-primary:hover { background:#162f4a; }
    .btn-outline  { background:#fff; color:#1a3a5c; border:1px solid #1a3a5c; padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-outline:hover { background:#f0f4f8; }
    .alert-success { background:#dcfce7; border:1px solid #86efac; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
    .alert-error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
    .section-divider { border:none; border-top:1px solid #f1f5f9; margin:28px 0; }
</style>
@endpush

@section('content')

<div style="margin-bottom:20px;">
    <a href="{{ route('settings.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;">← Back to Settings</a>
</div>

<div style="margin-bottom:24px;">
    <h1 style="margin:0;font-size:20px;font-weight:700;color:#1a3a5c;">📧 Email Settings</h1>
    <p style="margin:6px 0 0;color:#6b7280;font-size:13px;">Configure SMTP for outgoing emails (notifications, alerts, ticket updates)</p>
</div>

@if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-error">❌ {{ session('error') }}</div>
@endif

<div class="settings-form-card">
    <form method="POST" action="{{ route('settings.email.update') }}">
        @csrf
        @method('POST')

        <div class="form-row">
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

        <div class="form-row">
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

        <div class="form-row">
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

        <div style="display:flex;gap:12px;align-items:center;margin-top:8px;">
            <button type="submit" class="btn-primary">💾 Save Settings</button>
        </div>
    </form>

    <hr class="section-divider">

    <h3 style="font-size:14px;font-weight:700;color:#1a3a5c;margin:0 0 14px;">🔬 Send Test Email</h3>
    <form method="POST" action="{{ route('settings.email.test') }}" style="display:flex;gap:12px;align-items:flex-end;">
        @csrf
        <div class="form-group" style="flex:1;margin-bottom:0;">
            <label class="form-label">Send test to</label>
            <input class="form-input" type="email" name="test_email" placeholder="recipient@example.com">
        </div>
        <button type="submit" class="btn-outline" style="white-space:nowrap;">📨 Send Test</button>
    </form>
</div>

@endsection
