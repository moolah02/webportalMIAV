{{-- resources/views/pos-terminals/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add POS Terminal')

@push('styles')
<style>
/* ── POS terminals · create ────────────────────────────── */
.pt-form { max-width: 920px; margin: 0 auto; }
.pt-form .pt-form-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
.pt-form .pt-btn { height: 34px; padding: 0 12px; font-size: 13px; }
.pt-form .pt-errors { display: flex; align-items: flex-start; gap: 10px; padding: 11px 14px; margin-bottom: 16px; border: 1px solid #F2CACA; border-radius: 8px; background: var(--mv-crit-soft); color: var(--mv-crit); font-size: 13px; }
.pt-form .pt-errors .mv-i { margin-top: 1px; }
.pt-form .pt-section + .pt-section { margin-top: 16px; }
.pt-form .ui-card-header { padding: 12px 18px; }
.pt-form .ui-card-header h2 { font-size: 14px; font-weight: 600; margin: 0; }
.pt-form .ui-card-body { padding: 18px; }
.pt-form .pt-grid { display: grid; gap: 16px 20px; }
.pt-form .pt-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.pt-form .pt-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.pt-form .pt-span-all { grid-column: 1 / -1; }
@media (max-width: 760px) { .pt-form .pt-grid-2, .pt-form .pt-grid-3 { grid-template-columns: 1fr; } }
.pt-form .ui-input { height: 38px; padding: 0 12px; font-size: 13.5px; }
.pt-form .ui-select { height: 38px; padding: 0 32px 0 12px; font-size: 13.5px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236A7686' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; }
.pt-form .ui-textarea { padding: 9px 12px; font-size: 13.5px; }
.pt-form .ui-hint { font-size: 12px; margin: 5px 0 0; }
.pt-form .pt-req { color: var(--mv-crit); }
.pt-form .pt-error { margin: 5px 0 0; font-size: 12px; color: var(--mv-crit); }
.pt-form .pt-readonly { display: flex; align-items: center; gap: 8px; height: 38px; padding: 0 12px; border: 1px dashed var(--mv-line-strong); border-radius: 8px; background: var(--mv-surface-2); color: var(--mv-muted); font-size: 13.5px; cursor: not-allowed; }
.pt-form .pt-form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
</style>
@endpush

@section('content')
<div class="pt-form">
    {{-- Header --}}
    <div class="pt-form-top">
        <a href="{{ route('pos-terminals.index') }}" class="btn-secondary pt-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Terminals
        </a>
    </div>

    @if($errors->any())
    <div class="pt-errors" role="alert">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-alert-circle"/></svg>
        <div>
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('pos-terminals.store') }}" method="POST">
        @csrf

        {{-- Terminal Information --}}
        <section class="ui-card pt-section">
            <div class="ui-card-header"><h2>Terminal Information</h2></div>
            <div class="ui-card-body pt-grid pt-grid-2">
                <div>
                    <label class="ui-label">Terminal ID <span class="pt-req">*</span></label>
                    <input type="text" name="terminal_id" value="{{ old('terminal_id') }}" placeholder="e.g., POS-001" required class="ui-input mv-mono">
                    @error('terminal_id')<p class="pt-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Client/Bank <span class="pt-req">*</span></label>
                    <select name="client_id" required class="ui-select">
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="pt-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Terminal Model</label>
                    <select name="terminal_model" class="ui-select">
                        <option value="">Select Model</option>
                        @foreach(['Ingenico iWL220','Verifone VX520','PAX A920','Ingenico Move 5000'] as $model)
                            <option value="{{ $model }}" {{ old('terminal_model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ui-label">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="e.g., SN123456" class="ui-input mv-mono">
                </div>
                <div>
                    <label class="ui-label">Installation Date</label>
                    <input type="date" name="installation_date" value="{{ old('installation_date') }}" class="ui-input">
                </div>
                <div>
                    <label class="ui-label">Status</label>
                    <div class="pt-readonly">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-lock"/></svg>
                        Set by field visits
                    </div>
                    <p class="ui-hint">Terminal status is recorded by technicians on the mobile app and cannot be set here.</p>
                </div>
            </div>
        </section>

        {{-- Merchant Information --}}
        <section class="ui-card pt-section">
            <div class="ui-card-header"><h2>Merchant Information</h2></div>
            <div class="ui-card-body pt-grid pt-grid-2">
                <div>
                    <label class="ui-label">Merchant Name <span class="pt-req">*</span></label>
                    <input type="text" name="merchant_name" value="{{ old('merchant_name') }}" placeholder="e.g., Green Valley Supermarket" required class="ui-input">
                    @error('merchant_name')<p class="pt-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Contact Person</label>
                    <input type="text" name="merchant_contact_person" value="{{ old('merchant_contact_person') }}" placeholder="e.g., John Doe" class="ui-input">
                </div>
                <div>
                    <label class="ui-label">Phone Number</label>
                    <input type="text" name="merchant_phone" value="{{ old('merchant_phone') }}" placeholder="e.g., +254712345678" class="ui-input">
                </div>
                <div>
                    <label class="ui-label">Email Address</label>
                    <input type="email" name="merchant_email" value="{{ old('merchant_email') }}" placeholder="e.g., merchant@example.com" class="ui-input">
                </div>
                <div>
                    <label class="ui-label">Business Type</label>
                    <select name="business_type" class="ui-select">
                        <option value="">Select Type</option>
                        @foreach(['Retail','Restaurant','Pharmacy','Electronics','Grocery','Other'] as $type)
                            <option value="{{ $type }}" {{ old('business_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- Location Information --}}
        <section class="ui-card pt-section">
            <div class="ui-card-header"><h2>Location Information</h2></div>
            <div class="ui-card-body pt-grid pt-grid-3">
                <div>
                    <label class="ui-label">Region</label>
                    <select name="region" class="ui-select" onchange="document.getElementById('create_province').value=this.value">
                        <option value="">Select Region</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg }}" {{ old('region') == $reg ? 'selected' : '' }}>{{ $reg }}</option>
                        @endforeach
                    </select>
                    @error('region')<p class="pt-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g., Harare" class="ui-input">
                    @error('city')<p class="pt-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Province</label>
                    <input type="text" name="province" id="create_province" value="{{ old('province') }}" placeholder="e.g., Harare Province" class="ui-input">
                    @error('province')<p class="pt-error">{{ $message }}</p>@enderror
                </div>
                <div class="pt-span-all">
                    <label class="ui-label">Physical Address</label>
                    <textarea name="physical_address" rows="3" class="ui-textarea" placeholder="Full address...">{{ old('physical_address') }}</textarea>
                    @error('physical_address')<p class="pt-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- Additional Information --}}
        <section class="ui-card pt-section">
            <div class="ui-card-header"><h2>Additional Information</h2></div>
            <div class="ui-card-body">
                <label class="ui-label">Contract Details</label>
                <textarea name="contract_details" rows="4" class="ui-textarea" placeholder="Any contract or service agreement details...">{{ old('contract_details') }}</textarea>
            </div>
        </section>

        {{-- Actions --}}
        <div class="pt-form-actions">
            <a href="{{ route('pos-terminals.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create Terminal</button>
        </div>
    </form>
</div>
@endsection
