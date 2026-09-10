{{-- File: resources/views/business-licenses/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Business License')

@push('styles')
<style>
    /* Shared with create.blade.php — keep both blocks identical */
    .bl-form-page { display: flex; flex-direction: column; gap: 16px; }
    .bl-form-page .bl-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .bl-form-page .bl-sub { margin: 0; font-size: 13px; color: var(--mv-muted); }
    .bl-form-page .bl-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .bl-form-page .bl-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 16px; align-items: start; }
    .bl-form-page .bl-main,
    .bl-form-page .bl-side,
    .bl-form-page .bl-form { display: flex; flex-direction: column; gap: 16px; min-width: 0; margin: 0; }

    /* Section cards */
    .bl-form-page .ui-card-header { padding: 12px 18px; }
    .bl-form-page .bl-card-title { margin: 0; font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
    .bl-form-page .ui-card-body { padding: 16px 18px 18px; }
    .bl-form-page .bl-side .ui-card-body { padding: 14px 16px; }

    /* Fields */
    .bl-form-page .bl-fields { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px 16px; }
    .bl-form-page .bl-fields.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .bl-form-page .bl-fields > div { min-width: 0; }
    .bl-form-page .bl-span-2 { grid-column: span 2; }
    .bl-form-page .bl-span-all { grid-column: 1 / -1; }
    .bl-form-page .ui-label { margin-bottom: 5px; }
    .bl-form-page .bl-req { color: var(--mv-crit); }
    .bl-form-page .ui-input,
    .bl-form-page .ui-select { height: 36px; padding: 0 11px; font-size: 13.5px; }
    .bl-form-page .ui-select { padding-right: 32px; }
    .bl-form-page .ui-textarea { padding: 8px 11px; font-size: 13.5px; line-height: 1.5; }
    .bl-form-page input[type="file"].ui-input { height: auto; padding: 5px; font-size: 13px; color: var(--mv-ink-2); }
    .bl-form-page input[type="file"]::file-selector-button { margin-right: 10px; padding: 5px 10px; border: 1px solid var(--mv-line-strong); border-radius: 6px; background: var(--mv-surface-2); color: var(--mv-ink); font: inherit; font-size: 12.5px; cursor: pointer; }
    .bl-form-page .ui-hint { margin: 5px 0 0; font-size: 12px; }
    .bl-form-page .form-error { margin: 5px 0 0; font-size: 12px; color: var(--mv-crit); }
    .bl-form-page .bl-check { display: inline-flex; align-items: center; gap: 8px; margin: 0; font-size: 13.5px; color: var(--mv-ink); cursor: pointer; }
    .bl-form-page .bl-check input[type="checkbox"] { width: 16px; height: 16px; margin: 0; border-radius: 4px; border-color: var(--mv-line-strong); color: var(--mv-accent); }
    .bl-form-page .bl-inline { display: flex; align-items: center; gap: 8px; }
    .bl-form-page .bl-inline .ui-select { flex: 1 1 auto; min-width: 0; }
    .bl-form-page .bl-icon-btn { flex-shrink: 0; width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--mv-line-strong); border-radius: 8px; background: var(--mv-surface); color: var(--mv-ink-2); cursor: pointer; }
    .bl-form-page .bl-icon-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
    .bl-form-page .bl-formbar { display: flex; justify-content: flex-end; gap: 8px; }

    /* Read-only bits */
    .bl-form-page .bl-readonly { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .bl-form-page .badge { gap: 6px; white-space: nowrap; }
    .bl-form-page .bl-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .bl-form-page .bl-file { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; padding: 8px 10px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); font-size: 13px; color: var(--mv-ink-2); }
    .bl-form-page .bl-file .mv-i { color: var(--mv-muted); }
    .bl-form-page .bl-file-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .bl-form-page .bl-file a { font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; }
    .bl-form-page .bl-file a:hover { text-decoration: underline; }
    .bl-form-page .bl-kv { display: flex; flex-direction: column; gap: 10px; margin: 0; }
    .bl-form-page .bl-kv > div { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .bl-form-page .bl-kv dt { font-size: 12px; font-weight: 400; color: var(--mv-muted); white-space: nowrap; }
    .bl-form-page .bl-kv dd { margin: 0; font-size: 13.5px; color: var(--mv-ink); text-align: right; font-variant-numeric: tabular-nums; }
    .bl-form-page .bl-kv dd.is-warn { color: var(--mv-warn); }
    .bl-form-page .bl-kv dd.is-crit { color: var(--mv-crit); }
    .bl-form-page .bl-rel,
    .bl-form-page .bl-by { display: block; font-size: 12px; color: var(--mv-muted); }
    .bl-form-page dd.is-warn .bl-rel,
    .bl-form-page dd.is-crit .bl-rel { color: inherit; }

    /* Side notes */
    .bl-form-page .bl-tips { margin: 0; padding-left: 16px; font-size: 13px; line-height: 1.55; color: var(--mv-ink-2); }
    .bl-form-page .bl-tips li + li { margin-top: 4px; }
    .bl-form-page .bl-tips li::marker { color: var(--mv-muted); }
    .bl-form-page .bl-text { margin: 0; font-size: 13px; line-height: 1.55; color: var(--mv-ink-2); }
    .bl-form-page .bl-text strong { color: var(--mv-ink); font-weight: 600; }
    .bl-form-page .bl-link { display: inline-flex; align-items: center; gap: 4px; margin-top: 10px; font-size: 13px; font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; }
    .bl-form-page .bl-link:hover { color: var(--mv-accent-ink); text-decoration: underline; }

    /* Errors */
    .bl-form-page .bl-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border: 1px solid #F2CACA; border-radius: 8px; background: var(--mv-crit-soft); color: var(--mv-crit); font-size: 13px; }
    .bl-form-page .bl-alert ul { margin: 4px 0 0; padding-left: 18px; }

    /* Quick-add department dialog */
    .bl-form-page .bl-modal { z-index: 1100; background: rgba(22, 32, 44, .45); }
    .mv-page .bl-modal-card { box-shadow: 0 12px 32px rgba(22, 32, 44, .16) !important; }
    .bl-form-page .bl-modal .ui-card-header { padding: 12px 16px; }
    .bl-form-page .bl-modal .ui-card-body { padding: 16px; }
    .bl-form-page .bl-modal .ui-card-footer { justify-content: flex-end; gap: 8px; padding: 12px 16px; }
    .bl-form-page .bl-close { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; padding: 0; border: 0; border-radius: 6px; background: transparent; color: var(--mv-muted); cursor: pointer; }
    .bl-form-page .bl-close:hover { background: var(--mv-surface-2); color: var(--mv-ink); }

    @media (max-width: 1100px) { .bl-form-page .bl-layout { grid-template-columns: minmax(0, 1fr); } }
    @media (max-width: 720px) {
        .bl-form-page .bl-fields,
        .bl-form-page .bl-fields.cols-2 { grid-template-columns: minmax(0, 1fr); }
        .bl-form-page .bl-span-2 { grid-column: auto; }
    }
</style>
@endpush

@section('content')
@php
    $blStatusBadge = fn ($s) => match ($s) {
        'active' => 'badge-green',
        'expired', 'suspended' => 'badge-red',
        'pending_renewal', 'under_review' => 'badge-blue',
        default => 'badge-gray',
    };
    $blPriorityBadge = fn ($p) => match ($p) {
        'critical' => 'badge-red',
        'high' => 'badge-yellow',
        default => 'badge-gray',
    };
    $blRel = null;
    if ($businessLicense->expiry_date) {
        $blD = (int) $businessLicense->days_until_expiry;
        $blRel = $blD === 0 ? 'today'
            : ($blD > 0 ? 'in ' . $blD . ' ' . \Illuminate\Support\Str::plural('day', $blD)
                        : abs($blD) . ' ' . \Illuminate\Support\Str::plural('day', abs($blD)) . ' ago');
    }
    $blExpTone = $businessLicense->is_expired ? 'is-crit' : ($businessLicense->is_expiring_soon ? 'is-warn' : '');
@endphp
<div class="bl-form-page">
    <!-- Header -->
    <div class="bl-toolbar">
        <p class="bl-sub">Update license information for {{ $businessLicense->license_name }}</p>
        <div class="bl-actions">
            <a href="{{ route('business-licenses.show', $businessLicense) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>View
            </a>
            <a href="{{ route('business-licenses.index', ['direction' => $businessLicense->license_direction]) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Licenses
            </a>
        </div>
    </div>

    <div class="bl-layout">
        <!-- Main Form -->
        <div class="bl-main">
            <form action="{{ route('business-licenses.update', $businessLicense) }}" method="POST" enctype="multipart/form-data" class="bl-form">
                @csrf
                @method('PUT')

                <!-- License Type Display (Read-only) -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">License Type</h3></div>
                    <div class="ui-card-body bl-readonly">
                        <span class="badge badge-gray">{{ $businessLicense->license_direction_name }}</span>
                        <span class="bl-text">{{ $businessLicense->isCompanyHeld() ? 'License owned by your company' : 'License issued to customer' }}</span>
                    </div>
                </section>

                <!-- Basic Information -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Basic Information</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div class="bl-span-2">
                            <label class="ui-label">License Name <span class="bl-req">*</span></label>
                            <input type="text" name="license_name" value="{{ old('license_name', $businessLicense->license_name) }}" required
                                   class="ui-input">
                            @error('license_name')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">License Number <span class="bl-req">*</span></label>
                            <input type="text" name="license_number" value="{{ old('license_number', $businessLicense->license_number) }}" required
                                   class="ui-input mv-mono">
                            @error('license_number')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">License Type <span class="bl-req">*</span></label>
                            <select name="license_type" required class="ui-select">
                                <option value="">Select License Type</option>
                                @foreach(\App\Models\BusinessLicense::LICENSE_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('license_type', $businessLicense->license_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('license_type')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Status <span class="bl-req">*</span></label>
                            <select name="status" required class="ui-select">
                                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ old('status', $businessLicense->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Issuing Authority <span class="bl-req">*</span></label>
                            <input type="text" name="issuing_authority" value="{{ old('issuing_authority', $businessLicense->issuing_authority) }}" required
                                   class="ui-input">
                            @error('issuing_authority')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bl-span-all">
                            <label class="ui-label">Description</label>
                            <textarea name="description" rows="3" class="ui-textarea">{{ old('description', $businessLicense->description) }}</textarea>
                            @error('description')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Dates Information -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Dates Information</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div>
                            <label class="ui-label">Issue Date <span class="bl-req">*</span></label>
                            <input type="date" name="issue_date" value="{{ old('issue_date', $businessLicense->issue_date?->format('Y-m-d')) }}" required
                                   class="ui-input">
                            @error('issue_date')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Expiry Date <span class="bl-req">*</span></label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date', $businessLicense->expiry_date?->format('Y-m-d')) }}" required
                                   class="ui-input">
                            @error('expiry_date')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($businessLicense->isCustomerIssued())
                        <div>
                            <label class="ui-label">Service Start Date</label>
                            <input type="date" name="service_start_date" value="{{ old('service_start_date', $businessLicense->service_start_date?->format('Y-m-d')) }}"
                                   class="ui-input">
                            @error('service_start_date')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                    </div>
                </section>

                @if($businessLicense->isCompanyHeld())
                <!-- Company-Held License Fields -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Financial Information</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div>
                            <label class="ui-label">Initial Cost ($)</label>
                            <input type="number" name="cost" value="{{ old('cost', $businessLicense->cost) }}" step="0.01" min="0"
                                   class="ui-input">
                            @error('cost')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Renewal Cost ($)</label>
                            <input type="number" name="renewal_cost" value="{{ old('renewal_cost', $businessLicense->renewal_cost) }}" step="0.01" min="0"
                                   class="ui-input">
                            @error('renewal_cost')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Company Additional Information -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Additional Information</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div class="bl-span-2">
                            <label class="ui-label">Regulatory Body</label>
                            <input type="text" name="regulatory_body" value="{{ old('regulatory_body', $businessLicense->regulatory_body) }}"
                                   class="ui-input">
                            @error('regulatory_body')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Renewal Reminder (Days)</label>
                            <input type="number" name="renewal_reminder_days" value="{{ old('renewal_reminder_days', $businessLicense->renewal_reminder_days) }}" min="1" max="365"
                                   class="ui-input">
                            @error('renewal_reminder_days')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bl-span-all">
                            <label class="ui-label">Business Impact</label>
                            <textarea name="business_impact" rows="3" class="ui-textarea">{{ old('business_impact', $businessLicense->business_impact) }}</textarea>
                            @error('business_impact')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bl-span-all">
                            <label class="ui-label">License Conditions</label>
                            <textarea name="license_conditions" rows="3" class="ui-textarea">{{ old('license_conditions', $businessLicense->license_conditions) }}</textarea>
                            @error('license_conditions')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bl-span-all">
                            <label class="ui-label">Compliance Notes</label>
                            <textarea name="compliance_notes" rows="3" class="ui-textarea">{{ old('compliance_notes', $businessLicense->compliance_notes) }}</textarea>
                            @error('compliance_notes')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bl-span-all">
                            <label class="bl-check">
                                <input type="checkbox" name="auto_renewal" value="1" {{ old('auto_renewal', $businessLicense->auto_renewal) ? 'checked' : '' }}>
                                <span>Enable Auto-Renewal Notifications</span>
                            </label>
                            @error('auto_renewal')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                @else
                <!-- Customer-Issued License Fields -->
                <!-- Customer Information -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Customer Information</h3></div>
                    <div class="ui-card-body bl-fields cols-2">
                        <div>
                            <label class="ui-label">Customer Name <span class="bl-req">*</span></label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $businessLicense->customer_name) }}" required
                                   class="ui-input">
                            @error('customer_name')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Customer Email <span class="bl-req">*</span></label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $businessLicense->customer_email) }}" required
                                   class="ui-input">
                            @error('customer_email')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Customer Company</label>
                            <input type="text" name="customer_company" value="{{ old('customer_company', $businessLicense->customer_company) }}"
                                   class="ui-input">
                            @error('customer_company')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Customer Phone</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone', $businessLicense->customer_phone) }}"
                                   class="ui-input">
                            @error('customer_phone')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Customer Reference</label>
                            <input type="text" name="customer_reference" value="{{ old('customer_reference', $businessLicense->customer_reference) }}"
                                   class="ui-input">
                            @error('customer_reference')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bl-span-all">
                            <label class="ui-label">Customer Address</label>
                            <textarea name="customer_address" rows="3" class="ui-textarea">{{ old('customer_address', $businessLicense->customer_address) }}</textarea>
                            @error('customer_address')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- License & Billing Information -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">License &amp; Billing Information</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div>
                            <label class="ui-label">Revenue Amount ($) <span class="bl-req">*</span></label>
                            <input type="number" name="revenue_amount" value="{{ old('revenue_amount', $businessLicense->revenue_amount) }}" required step="0.01" min="0"
                                   class="ui-input">
                            @error('revenue_amount')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Billing Cycle <span class="bl-req">*</span></label>
                            <select name="billing_cycle" required class="ui-select">
                                <option value="">Select Billing Cycle</option>
                                @foreach(\App\Models\BusinessLicense::BILLING_CYCLES as $key => $label)
                                <option value="{{ $key }}" {{ old('billing_cycle', $businessLicense->billing_cycle) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('billing_cycle')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Support Level <span class="bl-req">*</span></label>
                            <select name="support_level" required class="ui-select">
                                <option value="">Select Support Level</option>
                                @foreach(\App\Models\BusinessLicense::SUPPORT_LEVELS as $key => $label)
                                <option value="{{ $key }}" {{ old('support_level', $businessLicense->support_level) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('support_level')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">License Quantity</label>
                            <input type="number" name="license_quantity" value="{{ old('license_quantity', $businessLicense->license_quantity) }}" min="1"
                                   class="ui-input">
                            @error('license_quantity')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bl-span-2">
                            <label class="ui-label">Usage Limit</label>
                            <input type="text" name="usage_limit" value="{{ old('usage_limit', $businessLicense->usage_limit) }}"
                                   class="ui-input">
                            @error('usage_limit')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Customer License Terms -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">License Terms &amp; Conditions</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div class="bl-span-all">
                            <label class="ui-label">License Terms</label>
                            <textarea name="license_terms" rows="4" class="ui-textarea">{{ old('license_terms', $businessLicense->license_terms) }}</textarea>
                            @error('license_terms')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bl-span-all">
                            <label class="bl-check">
                                <input type="checkbox" name="auto_renewal_customer" value="1" {{ old('auto_renewal_customer', $businessLicense->auto_renewal_customer) ? 'checked' : '' }}>
                                <span>Enable Auto-Renewal for Customer</span>
                            </label>
                            @error('auto_renewal_customer')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>
                @endif

                <!-- Document Upload -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Document</h3></div>
                    <div class="ui-card-body">
                        @if($businessLicense->document_path)
                        <div class="bl-file">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-file"/></svg>
                            <span class="bl-file-name">Current document: <span class="mv-mono">{{ basename($businessLicense->document_path) }}</span></span>
                            <a href="{{ route('business-licenses.download', $businessLicense) }}">Download</a>
                        </div>
                        @endif

                        <div>
                            <label class="ui-label">License Document</label>
                            <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="ui-input">
                            <p class="ui-hint">Upload new document to replace existing (PDF, DOC, DOCX, JPG, PNG - Max 2MB)</p>
                            @error('document')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Submit Buttons -->
                <div class="bl-formbar">
                    <a href="{{ route('business-licenses.show', $businessLicense) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Update License</button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <aside class="bl-side">
            <!-- Current Status -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Current Status</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-kv">
                        <div>
                            <dt>Type</dt>
                            <dd><span class="badge badge-gray">{{ $businessLicense->license_direction_name }}</span></dd>
                        </div>
                        <div>
                            <dt>Status</dt>
                            <dd><span class="badge {{ $blStatusBadge($businessLicense->status) }}"><span class="bl-dot"></span>{{ $businessLicense->status_name }}</span></dd>
                        </div>
                        @if($businessLicense->isCompanyHeld())
                        <div>
                            <dt>Priority</dt>
                            <dd><span class="badge {{ $blPriorityBadge($businessLicense->priority_level) }}">{{ $businessLicense->priority_level_name }}</span></dd>
                        </div>
                        @else
                        <div>
                            <dt>Support</dt>
                            <dd><span class="badge badge-gray">{{ $businessLicense->support_level_name }}</span></dd>
                        </div>
                        @endif
                        @if($businessLicense->expiry_date)
                        <div>
                            <dt>Expires</dt>
                            <dd class="{{ $blExpTone }}">
                                {{ $businessLicense->expiry_date->format('M d, Y') }}
                                @if($blRel)<span class="bl-rel">{{ $blRel }}</span>@endif
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>

            <!-- Edit Tips -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Edit Tips</h3></div>
                <div class="ui-card-body">
                    <ul class="bl-tips">
                        @if($businessLicense->isCompanyHeld())
                        <li>Update expiry dates when renewals are processed</li>
                        <li>Change status to reflect current license state</li>
                        <li>Upload new documents when available</li>
                        <li>Adjust reminder days for critical licenses</li>
                        <li>Update responsible employees as needed</li>
                        @else
                        <li>Keep customer information up to date</li>
                        <li>Update billing cycle and revenue as needed</li>
                        <li>Modify support levels based on agreements</li>
                        <li>Update license terms when necessary</li>
                        <li>Track usage and adjust limits</li>
                        @endif
                    </ul>
                </div>
            </section>

            <!-- Change History -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">License History</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-kv">
                        <div>
                            <dt>Created</dt>
                            <dd>
                                {{ $businessLicense->created_at->format('M d, Y') }}
                                @if($businessLicense->creator)
                                <span class="bl-by">by {{ $businessLicense->creator->full_name }}</span>
                                @endif
                            </dd>
                        </div>
                        @if($businessLicense->updated_at != $businessLicense->created_at)
                        <div>
                            <dt>Last Updated</dt>
                            <dd>
                                {{ $businessLicense->updated_at->format('M d, Y') }}
                                @if($businessLicense->updater)
                                <span class="bl-by">by {{ $businessLicense->updater->full_name }}</span>
                                @endif
                            </dd>
                        </div>
                        @endif
                        @if($businessLicense->renewal_date)
                        <div>
                            <dt>Last Renewed</dt>
                            <dd>{{ $businessLicense->renewal_date->format('M d, Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>
        </aside>
    </div>
</div>

@endsection
