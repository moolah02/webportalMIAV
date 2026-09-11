{{-- File: resources/views/business-licenses/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Business License')

@push('styles')
<style>
    /* Shared with edit.blade.php — keep both blocks identical */
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
<div class="bl-form-page">
    <!-- Header -->
    <div class="bl-toolbar">
        <p class="bl-sub">
            {{ $direction === 'company_held' ? 'Register an internal license for compliance tracking' : 'Register a license issued to a customer' }}
        </p>
        <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Licenses
        </a>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="bl-alert" role="alert">
            <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bl-layout">
        <!-- Main Form -->
        <div class="bl-main">
            <form action="{{ route('business-licenses.store') }}" method="POST" enctype="multipart/form-data" class="bl-form">
                @csrf
                <input type="hidden" name="license_direction" value="{{ $direction }}">

                <!-- Basic Information -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Basic Information</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div class="bl-span-2">
                            <label class="ui-label">License Name <span class="bl-req">*</span></label>
                            <input type="text" name="license_name" value="{{ old('license_name') }}" required
                                   class="ui-input" placeholder="e.g., Business Operating License">
                            @error('license_name') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="ui-label">License Number <span class="bl-req">*</span></label>
                            <input type="text" name="license_number" value="{{ old('license_number') }}" required
                                   class="ui-input mv-mono" placeholder="e.g., BL-2024-001234">
                            @error('license_number') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="ui-label">License Type <span class="bl-req">*</span></label>
                            <input type="text" name="license_type" value="{{ old('license_type') }}" required
                                   class="ui-input" placeholder="e.g., Trading License">
                            @error('license_type') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="ui-label">Status <span class="bl-req">*</span></label>
                            <select name="status" required class="ui-select">
                                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ old('status', 'active') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="ui-label">Issuing Authority <span class="bl-req">*</span></label>
                            <input type="text" name="issuing_authority" value="{{ old('issuing_authority') }}" required
                                   class="ui-input" placeholder="e.g., Department of Commerce">
                            @error('issuing_authority') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="bl-span-all">
                            <label class="ui-label">Description</label>
                            <textarea name="description" rows="3" class="ui-textarea"
                                      placeholder="Brief description of the license and its purpose...">{{ old('description') }}</textarea>
                            @error('description') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </section>

                <!-- Dates -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Dates</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div>
                            <label class="ui-label">Issue Date <span class="bl-req">*</span></label>
                            <input type="date" name="issue_date" value="{{ old('issue_date') }}" required class="ui-input">
                            @error('issue_date') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="ui-label">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="ui-input">
                            <p class="ui-hint">Leave blank if the license does not expire</p>
                            @error('expiry_date') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </section>

                @if($direction === 'company_held')
                    <!-- Company-Held Specific Fields -->
                    <section class="ui-card">
                        <div class="ui-card-header"><h3 class="bl-card-title">Company Details</h3></div>
                        <div class="ui-card-body bl-fields">
                            <div>
                                <label class="ui-label">Department <span class="bl-req">*</span></label>
                                <div class="bl-inline">
                                    <select name="department_id" id="department_select" required class="ui-select">
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="openNewDeptModal()"
                                        title="Add new department" aria-label="Add new department" class="bl-icon-btn">
                                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg>
                                    </button>
                                </div>
                                @error('department_id') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Priority Level <span class="bl-req">*</span></label>
                                <select name="priority_level" required class="ui-select">
                                    <option value="">-- Select Priority --</option>
                                    @foreach(\App\Models\BusinessLicense::PRIORITY_LEVELS as $key => $label)
                                    <option value="{{ $key }}" {{ old('priority_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('priority_level') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Responsible Employee</label>
                                <select name="responsible_employee_id" class="ui-select">
                                    <option value="">-- None --</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('responsible_employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('responsible_employee_id') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Location</label>
                                <input type="text" name="location" value="{{ old('location') }}" class="ui-input" placeholder="e.g., Head Office">
                                @error('location') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Initial Cost ($)</label>
                                <input type="number" name="cost" value="{{ old('cost') }}" step="0.01" min="0" class="ui-input" placeholder="0.00">
                                @error('cost') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Renewal Cost ($)</label>
                                <input type="number" name="renewal_cost" value="{{ old('renewal_cost') }}" step="0.01" min="0" class="ui-input" placeholder="0.00">
                                @error('renewal_cost') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="bl-span-2">
                                <label class="ui-label">Regulatory Body</label>
                                <input type="text" name="regulatory_body" value="{{ old('regulatory_body') }}" class="ui-input" placeholder="e.g., Financial Services Authority">
                                @error('regulatory_body') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="bl-span-all">
                                <label class="ui-label">Business Impact</label>
                                <textarea name="business_impact" rows="2" class="ui-textarea" placeholder="What business operations does this license enable?">{{ old('business_impact') }}</textarea>
                                @error('business_impact') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="bl-span-all">
                                <label class="ui-label">License Conditions</label>
                                <textarea name="license_conditions" rows="2" class="ui-textarea" placeholder="Any conditions or restrictions on this license...">{{ old('license_conditions') }}</textarea>
                                @error('license_conditions') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="bl-span-all">
                                <label class="ui-label">Compliance Notes</label>
                                <textarea name="compliance_notes" rows="2" class="ui-textarea" placeholder="Internal compliance notes...">{{ old('compliance_notes') }}</textarea>
                                @error('compliance_notes') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </section>
                @else
                    <!-- Customer-Issued Specific Fields -->
                    <section class="ui-card">
                        <div class="ui-card-header"><h3 class="bl-card-title">Customer Information</h3></div>
                        <div class="ui-card-body bl-fields cols-2">
                            <div>
                                <label class="ui-label">Customer Name <span class="bl-req">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="ui-input">
                                @error('customer_name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Customer Email <span class="bl-req">*</span></label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="ui-input">
                                @error('customer_email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Customer Company</label>
                                <input type="text" name="customer_company" value="{{ old('customer_company') }}" class="ui-input">
                                @error('customer_company') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Customer Phone</label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="ui-input">
                                @error('customer_phone') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="bl-span-all">
                                <label class="ui-label">Customer Address</label>
                                <textarea name="customer_address" rows="2" class="ui-textarea">{{ old('customer_address') }}</textarea>
                                @error('customer_address') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="ui-card">
                        <div class="ui-card-header"><h3 class="bl-card-title">License &amp; Billing</h3></div>
                        <div class="ui-card-body bl-fields">
                            <div>
                                <label class="ui-label">Revenue Amount <span class="bl-req">*</span></label>
                                <input type="number" name="revenue_amount" value="{{ old('revenue_amount') }}" required step="0.01" min="0" class="ui-input" placeholder="0.00">
                                @error('revenue_amount') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Billing Cycle <span class="bl-req">*</span></label>
                                <select name="billing_cycle" required class="ui-select">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Models\BusinessLicense::BILLING_CYCLES as $key => $label)
                                    <option value="{{ $key }}" {{ old('billing_cycle') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('billing_cycle') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Support Level <span class="bl-req">*</span></label>
                                <select name="support_level" required class="ui-select">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Models\BusinessLicense::SUPPORT_LEVELS as $key => $label)
                                    <option value="{{ $key }}" {{ old('support_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('support_level') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">License Quantity</label>
                                <input type="number" name="license_quantity" value="{{ old('license_quantity', 1) }}" min="1" class="ui-input">
                                @error('license_quantity') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Service Start Date</label>
                                <input type="date" name="service_start_date" value="{{ old('service_start_date') }}" class="ui-input">
                                @error('service_start_date') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="ui-label">Customer Reference</label>
                                <input type="text" name="customer_reference" value="{{ old('customer_reference') }}" class="ui-input">
                                @error('customer_reference') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="bl-span-2">
                                <label class="ui-label">Usage Limit</label>
                                <input type="text" name="usage_limit" value="{{ old('usage_limit') }}" class="ui-input" placeholder="e.g., 5 users, unlimited">
                                @error('usage_limit') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="bl-span-all">
                                <label class="ui-label">License Terms</label>
                                <textarea name="license_terms" rows="3" class="ui-textarea" placeholder="Terms and conditions for the license...">{{ old('license_terms') }}</textarea>
                                @error('license_terms') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Attachments & Reminders -->
                <section class="ui-card">
                    <div class="ui-card-header"><h3 class="bl-card-title">Attachments &amp; Reminders</h3></div>
                    <div class="ui-card-body bl-fields">
                        <div class="bl-span-2">
                            <label class="ui-label">License Document</label>
                            <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="ui-input">
                            <p class="ui-hint">PDF, DOC, DOCX, JPG, PNG — max 2MB</p>
                            @error('document') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="ui-label">Renewal Reminder (Days Before Expiry)</label>
                            <input type="number" name="renewal_reminder_days" value="{{ old('renewal_reminder_days', 15) }}" min="1" max="365" class="ui-input">
                            @error('renewal_reminder_days') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        @if($direction === 'company_held')
                        <div class="bl-span-all">
                            <label class="bl-check">
                                <input type="checkbox" name="auto_renewal" value="1" {{ old('auto_renewal') ? 'checked' : '' }}>
                                <span>Enable Auto-Renewal Notifications</span>
                            </label>
                            @error('auto_renewal') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        @else
                        <div class="bl-span-all">
                            <label class="bl-check">
                                <input type="checkbox" name="auto_renewal_customer" value="1" {{ old('auto_renewal_customer') ? 'checked' : '' }}>
                                <span>Enable Auto-Renewal</span>
                            </label>
                            @error('auto_renewal_customer') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        @endif
                    </div>
                </section>

                <!-- Submit Buttons -->
                <div class="bl-formbar">
                    <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Create License</button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <aside class="bl-side">
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Tips</h3></div>
                <div class="ui-card-body">
                    <ul class="bl-tips">
                        @if($direction === 'company_held')
                            <li>Ensure license numbers are unique and identifiable</li>
                            <li>Assign a department and responsible employee</li>
                            <li>Set priority level for compliance tracking</li>
                            <li>Upload a clear copy of the license document</li>
                            <li>Set renewal reminders to avoid expiry lapses</li>
                        @else
                            <li>Ensure customer details are accurate for communication</li>
                            <li>Set the correct billing cycle and revenue amount</li>
                            <li>Define support level clearly for SLA tracking</li>
                            <li>Document license terms and usage limits</li>
                            <li>Enable auto-renewal to avoid service interruptions</li>
                        @endif
                    </ul>
                </div>
            </section>

            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">License Type</h3></div>
                <div class="ui-card-body">
                    <p class="bl-text">
                        @if($direction === 'company_held')
                            You are adding a <strong>Company-Held</strong> license owned by the company.
                        @else
                            You are adding a <strong>Customer-Issued</strong> license for a customer.
                        @endif
                    </p>
                    <a href="{{ route('business-licenses.create', ['direction' => $direction === 'company_held' ? 'customer_issued' : 'company_held']) }}" class="bl-link">
                        Switch to {{ $direction === 'company_held' ? 'Customer License' : 'Company License' }}
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                    </a>
                </div>
            </section>
        </aside>
    </div>

    {{-- Quick Add Department Modal --}}
    <div id="newDeptModal" class="hidden fixed inset-0 flex items-center justify-center p-4 bl-modal">
        <div class="ui-card w-full max-w-sm bl-modal-card" role="dialog" aria-modal="true" aria-labelledby="newDeptTitle">
            <div class="ui-card-header">
                <h3 id="newDeptTitle" class="bl-card-title">Add New Department</h3>
                <button type="button" onclick="closeNewDeptModal()" class="bl-close" aria-label="Close">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg>
                </button>
            </div>
            <div class="ui-card-body">
                <label class="ui-label">Department Name <span class="bl-req">*</span></label>
                <input type="text" id="newDeptName" class="ui-input" placeholder="e.g., Legal & Compliance" autofocus>
                <p id="newDeptError" class="form-error hidden"></p>
            </div>
            <div class="ui-card-footer">
                <button type="button" onclick="closeNewDeptModal()" class="btn-secondary">Cancel</button>
                <button type="button" onclick="saveNewDept()" id="newDeptSaveBtn" class="btn-primary">Save Department</button>
            </div>
        </div>
    </div>
</div>

<script>
function openNewDeptModal() {
    document.getElementById('newDeptName').value = '';
    document.getElementById('newDeptError').classList.add('hidden');
    document.getElementById('newDeptModal').classList.remove('hidden');
    setTimeout(() => document.getElementById('newDeptName').focus(), 50);
}

function closeNewDeptModal() {
    document.getElementById('newDeptModal').classList.add('hidden');
}

function saveNewDept() {
    const name = document.getElementById('newDeptName').value.trim();
    const errEl = document.getElementById('newDeptError');
    const btn = document.getElementById('newDeptSaveBtn');

    if (!name) {
        errEl.textContent = 'Department name is required.';
        errEl.classList.remove('hidden');
        return;
    }

    btn.textContent = 'Saving…';
    btn.disabled = true;
    errEl.classList.add('hidden');

    fetch('{{ route("departments.quick-create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                         || '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name })
    })
    .then(r => r.json())
    .then(data => {
        if (data.errors) {
            errEl.textContent = Object.values(data.errors)[0][0];
            errEl.classList.remove('hidden');
            return;
        }
        const sel = document.getElementById('department_select');
        const opt = document.createElement('option');
        opt.value = data.id;
        opt.textContent = data.name;
        opt.selected = true;
        sel.appendChild(opt);
        closeNewDeptModal();
    })
    .catch(() => {
        errEl.textContent = 'Failed to save department. Please try again.';
        errEl.classList.remove('hidden');
    })
    .finally(() => {
        btn.textContent = 'Save Department';
        btn.disabled = false;
    });
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeNewDeptModal();
    if (e.key === 'Enter' && !document.getElementById('newDeptModal').classList.contains('hidden')) {
        e.preventDefault();
        saveNewDept();
    }
});
document.getElementById('newDeptModal').addEventListener('click', e => {
    if (e.target === document.getElementById('newDeptModal')) closeNewDeptModal();
});
</script>

@endsection
