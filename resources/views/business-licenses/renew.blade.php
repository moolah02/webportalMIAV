{{-- File: resources/views/business-licenses/renew.blade.php --}}
@extends('layouts.app')
@section('title', 'Renew License')

@push('styles')
<style>
    .bl-renew { display: flex; flex-direction: column; gap: 16px; }
    .bl-renew .bl-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .bl-renew .bl-sub { margin: 0; font-size: 13px; color: var(--mv-muted); }
    .bl-renew .bl-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .bl-renew .bl-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 16px; align-items: start; }
    .bl-renew .bl-main,
    .bl-renew .bl-side { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
    .bl-renew form { margin: 0; }

    /* Status banner */
    .bl-renew .bl-banner { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border: 1px solid; border-radius: 8px; font-size: 13px; }
    .bl-renew .bl-banner.is-crit { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
    .bl-renew .bl-banner.is-warn { background: var(--mv-warn-soft); border-color: #F0DDB6; color: var(--mv-warn); }
    .bl-renew .bl-banner .mv-i { margin-top: 1px; }
    .bl-renew .bl-banner-title { font-size: 13.5px; font-weight: 600; }
    .bl-renew .bl-banner-text { margin-top: 2px; color: var(--mv-ink-2); }
    .bl-renew .bl-banner-text strong { color: var(--mv-ink); font-weight: 600; }

    /* Cards */
    .bl-renew .ui-card-header { padding: 12px 18px; }
    .bl-renew .bl-card-title { margin: 0; font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
    .bl-renew .ui-card-body { padding: 16px 18px 18px; }
    .bl-renew .bl-side .ui-card-body { padding: 14px 16px; }
    .bl-renew .ui-card-footer.bl-formbar { justify-content: flex-end; gap: 8px; padding: 12px 18px; }

    /* Fields */
    .bl-renew .bl-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; }
    .bl-renew .bl-fields > div { min-width: 0; }
    .bl-renew .bl-span-all { grid-column: 1 / -1; }
    .bl-renew .ui-label { margin-bottom: 5px; }
    .bl-renew .bl-req { color: var(--mv-crit); }
    .bl-renew .ui-input { height: 36px; padding: 0 11px; font-size: 13.5px; }
    .bl-renew .ui-textarea { padding: 8px 11px; font-size: 13.5px; line-height: 1.5; }
    .bl-renew input[type="file"].ui-input { height: auto; padding: 5px; font-size: 13px; color: var(--mv-ink-2); }
    .bl-renew input[type="file"]::file-selector-button { margin-right: 10px; padding: 5px 10px; border: 1px solid var(--mv-line-strong); border-radius: 6px; background: var(--mv-surface-2); color: var(--mv-ink); font: inherit; font-size: 12.5px; cursor: pointer; }
    .bl-renew .ui-hint { margin: 5px 0 0; font-size: 12px; }
    .bl-renew .form-error { margin: 5px 0 0; font-size: 12px; color: var(--mv-crit); }
    .bl-renew .bl-file { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; padding: 8px 10px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); font-size: 13px; color: var(--mv-ink-2); }
    .bl-renew .bl-file .mv-i { color: var(--mv-muted); }
    .bl-renew .bl-file-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .bl-renew .bl-file a { font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; white-space: nowrap; }
    .bl-renew .bl-file a:hover { text-decoration: underline; }

    /* Summary + key/values */
    .bl-renew .badge { gap: 6px; white-space: nowrap; }
    .bl-renew .bl-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .bl-renew .bl-summary { margin-top: 18px; padding: 14px 16px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); }
    .bl-renew .bl-summary-title { margin: 0 0 10px; font-size: 12.5px; font-weight: 600; color: var(--mv-ink); letter-spacing: 0; }
    .bl-renew .bl-kv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 12px 20px; margin: 0; }
    .bl-renew .bl-kv-grid dt { margin-bottom: 2px; font-size: 12px; font-weight: 400; color: var(--mv-muted); }
    .bl-renew .bl-kv-grid dd { margin: 0; font-size: 13.5px; color: var(--mv-ink); overflow-wrap: anywhere; }
    .bl-renew .bl-kv { display: flex; flex-direction: column; gap: 10px; margin: 0; }
    .bl-renew .bl-kv > div { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .bl-renew .bl-kv dt { font-size: 12px; font-weight: 400; color: var(--mv-muted); white-space: nowrap; }
    .bl-renew .bl-kv dd { margin: 0; font-size: 13.5px; color: var(--mv-ink); text-align: right; font-variant-numeric: tabular-nums; }

    /* Side notes */
    .bl-renew .bl-tips { margin: 0; padding-left: 16px; font-size: 13px; line-height: 1.55; color: var(--mv-ink-2); }
    .bl-renew .bl-tips li + li { margin-top: 4px; }
    .bl-renew .bl-tips li::marker { color: var(--mv-muted); }
    .bl-renew .bl-note-block + .bl-note-block { margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--mv-line); }
    .bl-renew .bl-note-label { margin-bottom: 3px; font-size: 12px; font-weight: 500; color: var(--mv-muted); }
    .bl-renew .bl-note-text { font-size: 13px; line-height: 1.55; color: var(--mv-ink-2); white-space: pre-line; }

    @media (max-width: 1100px) { .bl-renew .bl-layout { grid-template-columns: minmax(0, 1fr); } }
    @media (max-width: 720px)  { .bl-renew .bl-fields { grid-template-columns: minmax(0, 1fr); } }
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
@endphp
<div class="bl-renew">
    <!-- Header -->
    <div class="bl-toolbar">
        <p class="bl-sub">Process renewal for {{ $businessLicense->license_name }}</p>
        <div class="bl-actions">
            <a href="{{ route('business-licenses.show', $businessLicense) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>View License
            </a>
            <a href="{{ route('business-licenses.index') }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Licenses
            </a>
        </div>
    </div>

    <!-- License Status Alert -->
    @if($businessLicense->is_expired || $businessLicense->is_expiring_soon)
    <div class="bl-banner {{ $businessLicense->is_expired ? 'is-crit' : 'is-warn' }}" role="status">
        <svg class="mv-i" aria-hidden="true"><use href="#i-{{ $businessLicense->is_expired ? 'alert-circle' : 'alert-triangle' }}"/></svg>
        <div>
            <div class="bl-banner-title">
                {{ $businessLicense->is_expired ? 'License Has Expired' : 'License Expiring Soon' }}
            </div>
            <div class="bl-banner-text">
                @if($businessLicense->is_expired)
                    This license expired {{ abs($businessLicense->days_until_expiry) }} days ago on {{ $businessLicense->expiry_date->format('M d, Y') }}
                @else
                    This license expires in {{ $businessLicense->days_until_expiry }} days on {{ $businessLicense->expiry_date->format('M d, Y') }}
                @endif
            </div>
            @if($businessLicense->business_impact)
            <div class="bl-banner-text">
                <strong>Business Impact:</strong> {{ $businessLicense->business_impact }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="bl-layout">
        <!-- Renewal Form -->
        <div class="bl-main">
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Renewal Information</h3></div>

                <form id="renewForm" action="{{ route('business-licenses.process-renewal', $businessLicense) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="ui-card-body">
                        <div class="bl-fields">
                            <!-- New Expiry Date -->
                            <div>
                                <label class="ui-label">New Expiry Date <span class="bl-req">*</span></label>
                                <input type="date" name="new_expiry_date" value="{{ old('new_expiry_date') }}" required
                                       min="{{ now()->addDay()->format('Y-m-d') }}"
                                       class="ui-input">
                                <p class="ui-hint">
                                    Current expiry: {{ $businessLicense->expiry_date ? $businessLicense->expiry_date->format('M d, Y') : 'Not set' }}
                                </p>
                                @error('new_expiry_date')
                                <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Renewal Cost -->
                            <div>
                                <label class="ui-label">Renewal Cost ($)</label>
                                <input type="number" name="renewal_cost" value="{{ old('renewal_cost', $businessLicense->renewal_cost) }}"
                                       step="0.01" min="0" placeholder="0.00"
                                       class="ui-input">
                                <p class="ui-hint">
                                    Previous renewal cost: ${{ number_format($businessLicense->renewal_cost ?? 0, 2) }}
                                </p>
                                @error('renewal_cost')
                                <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Compliance Notes -->
                            <div class="bl-span-all">
                                <label class="ui-label">Renewal Notes</label>
                                <textarea name="compliance_notes" rows="4" placeholder="Any notes about this renewal process, compliance updates, or changes..."
                                          class="ui-textarea">{{ old('compliance_notes') }}</textarea>
                                <p class="ui-hint">
                                    Document any changes, conditions, or important notes about this renewal
                                </p>
                                @error('compliance_notes')
                                <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Document Upload -->
                            <div class="bl-span-all">
                                <label class="ui-label">Renewed License Document</label>
                                @if($businessLicense->document_path)
                                <div class="bl-file">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-file"/></svg>
                                    <span class="bl-file-name">Current: <span class="mv-mono">{{ basename($businessLicense->document_path) }}</span></span>
                                    <a href="{{ route('business-licenses.download', $businessLicense) }}">View Current</a>
                                </div>
                                @endif
                                <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="ui-input">
                                <p class="ui-hint">
                                    Upload the new renewed license document (PDF, DOC, DOCX, JPG, PNG - Max 2MB)
                                </p>
                                @error('document')
                                <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Renewal Confirmation -->
                        <div class="bl-summary">
                            <h4 class="bl-summary-title">Renewal Summary</h4>
                            <dl class="bl-kv-grid">
                                <div><dt>License</dt><dd>{{ $businessLicense->license_name }}</dd></div>
                                <div><dt>License Number</dt><dd class="mv-mono">{{ $businessLicense->license_number }}</dd></div>
                                <div>
                                    <dt>Current Status</dt>
                                    <dd><span class="badge {{ $blStatusBadge($businessLicense->status) }}"><span class="bl-dot"></span>{{ $businessLicense->status_name }}</span></dd>
                                </div>
                                <div><dt>Current Expiry</dt><dd>{{ $businessLicense->expiry_date ? $businessLicense->expiry_date->format('M d, Y') : 'Not set' }}</dd></div>
                                <div><dt>Issuing Authority</dt><dd>{{ $businessLicense->issuing_authority }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="ui-card-footer bl-formbar">
                        <a href="{{ route('business-licenses.show', $businessLicense) }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg>Process Renewal
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <!-- Sidebar Information -->
        <aside class="bl-side">
            <!-- Current License Info -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Current License Details</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-kv">
                        <div>
                            <dt>Type</dt>
                            <dd>{{ $businessLicense->license_type_name }}</dd>
                        </div>
                        <div>
                            <dt>Department</dt>
                            <dd>{{ $businessLicense->department->name ?? 'N/A' }}</dd>
                        </div>
                        @if($businessLicense->responsibleEmployee)
                        <div>
                            <dt>Responsible</dt>
                            <dd>{{ $businessLicense->responsibleEmployee->full_name }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt>Priority</dt>
                            <dd><span class="badge {{ $blPriorityBadge($businessLicense->priority_level) }}">{{ $businessLicense->priority_level_name }}</span></dd>
                        </div>
                        <div>
                            <dt>Issue Date</dt>
                            <dd>{{ $businessLicense->issue_date ? $businessLicense->issue_date->format('M d, Y') : 'N/A' }}</dd>
                        </div>
                        @if($businessLicense->renewal_reminder_days)
                        <div>
                            <dt>Reminder Days</dt>
                            <dd>{{ $businessLicense->renewal_reminder_days }} days</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>

            <!-- Renewal History -->
            @if($businessLicense->renewal_date)
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Last Renewal</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-kv">
                        <div>
                            <dt>Date</dt>
                            <dd>{{ $businessLicense->renewal_date->format('M d, Y') }}</dd>
                        </div>
                        @if($businessLicense->renewal_cost)
                        <div>
                            <dt>Cost</dt>
                            <dd>${{ number_format($businessLicense->renewal_cost, 2) }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>
            @endif

            <!-- Renewal Tips -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Renewal Tips</h3></div>
                <div class="ui-card-body">
                    <ul class="bl-tips">
                        <li>Set new expiry date based on renewal period</li>
                        <li>Upload the new license document if available</li>
                        <li>Update renewal costs for budget tracking</li>
                        <li>Add notes about any condition changes</li>
                        <li>Verify all information before processing</li>
                    </ul>
                </div>
            </section>

            <!-- Important Notes -->
            @if($businessLicense->license_conditions || $businessLicense->business_impact)
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Important Notes</h3></div>
                <div class="ui-card-body">
                    @if($businessLicense->business_impact)
                    <div class="bl-note-block">
                        <div class="bl-note-label">Business Impact</div>
                        <div class="bl-note-text">{{ $businessLicense->business_impact }}</div>
                    </div>
                    @endif

                    @if($businessLicense->license_conditions)
                    <div class="bl-note-block">
                        <div class="bl-note-label">License Conditions</div>
                        <div class="bl-note-text">{{ $businessLicense->license_conditions }}</div>
                    </div>
                    @endif
                </div>
            </section>
            @endif
        </aside>
    </div>
</div>


<script>
// Auto-calculate common renewal periods
document.addEventListener('DOMContentLoaded', function() {
    const expiryInput = document.querySelector('input[name="new_expiry_date"]');
    const currentExpiry = new Date('{{ $businessLicense->expiry_date ? $businessLicense->expiry_date->format("Y-m-d") : "" }}');

    // Suggest 1 year from current expiry as default
    if (currentExpiry && !isNaN(currentExpiry.getTime())) {
        const oneYearLater = new Date(currentExpiry);
        oneYearLater.setFullYear(oneYearLater.getFullYear() + 1);

        if (!expiryInput.value) {
            expiryInput.value = oneYearLater.toISOString().split('T')[0];
        }
    }

    // Form validation
    const form = document.getElementById('renewForm');
    form.addEventListener('submit', function(e) {
        const newExpiryDate = new Date(expiryInput.value);
        const currentDate = new Date();

        if (newExpiryDate <= currentDate) {
            e.preventDefault();
            alert('New expiry date must be in the future.');
            expiryInput.focus();
            return false;
        }

        // Confirm renewal
        const confirmMessage = `Are you sure you want to renew this license?\n\nNew expiry date: ${newExpiryDate.toLocaleDateString()}\n\nThis will update the license status to "active".`;
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection
