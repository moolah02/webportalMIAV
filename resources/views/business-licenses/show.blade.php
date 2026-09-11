{{-- File: resources/views/business-licenses/show.blade.php --}}
@extends('layouts.app')
@section('title', 'License Details')

@push('styles')
<style>
    .bl-show { display: flex; flex-direction: column; gap: 16px; }

    /* Header row */
    .bl-show .bl-head { display: flex; align-items: center; justify-content: space-between; gap: 12px 16px; flex-wrap: wrap; }
    .bl-show .bl-head-main { min-width: 0; }
    .bl-show .bl-head-line { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .bl-show .bl-title { margin: 0; font-size: 17px; font-weight: 600; line-height: 1.3; color: var(--mv-ink); }
    .bl-show .bl-sub { margin: 3px 0 0; font-size: 13px; color: var(--mv-muted); }
    .bl-show .code-chip { padding: 2px 7px; font-size: 12px; }
    .bl-show .bl-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* Layout + cards */
    .bl-show .bl-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 16px; align-items: start; }
    .bl-show .bl-main,
    .bl-show .bl-side { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
    .bl-show .ui-card-header { padding: 12px 18px; gap: 12px; flex-wrap: wrap; }
    .bl-show .bl-card-title { margin: 0; font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
    .bl-show .ui-card-body { padding: 16px 18px; }
    .bl-show .bl-side .ui-card-body { padding: 14px 16px; }
    .bl-show .bl-chips { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .bl-show .badge { gap: 6px; white-space: nowrap; }
    .bl-show .bl-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

    /* Expiry banner */
    .bl-show .bl-banner { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 16px; padding: 10px 12px; border: 1px solid; border-radius: 8px; font-size: 13px; }
    .bl-show .bl-banner.is-crit { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
    .bl-show .bl-banner.is-warn { background: var(--mv-warn-soft); border-color: #F0DDB6; color: var(--mv-warn); }
    .bl-show .bl-banner .mv-i { margin-top: 1px; }
    .bl-show .bl-banner-title { font-weight: 600; }
    .bl-show .bl-banner-text { color: var(--mv-ink-2); font-variant-numeric: tabular-nums; }

    /* Key / value */
    .bl-show .bl-kv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px 24px; margin: 0; }
    .bl-show .bl-span-all { grid-column: 1 / -1; }
    .bl-show dt { margin-bottom: 3px; font-size: 12px; font-weight: 400; color: var(--mv-muted); }
    .bl-show dd { margin: 0; font-size: 13.5px; color: var(--mv-ink); overflow-wrap: anywhere; }
    .bl-show dd.is-warn { color: var(--mv-warn); }
    .bl-show dd.is-crit { color: var(--mv-crit); }
    .bl-show .bl-rel,
    .bl-show .bl-by { display: block; margin-top: 1px; font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
    .bl-show dd.is-warn .bl-rel,
    .bl-show dd.is-crit .bl-rel { color: inherit; }
    .bl-show .bl-money { font-size: 15px; font-weight: 600; font-variant-numeric: tabular-nums; }
    .bl-show .bl-prose { margin: 0; font-size: 13.5px; line-height: 1.6; color: var(--mv-ink-2); white-space: pre-line; }
    .bl-show .bl-link { color: var(--mv-accent-ink); text-decoration: none; }
    .bl-show .bl-link:hover { color: var(--mv-accent-ink); text-decoration: underline; }

    /* Side lists */
    .bl-show .bl-list { margin: 0; }
    .bl-show .bl-list > div + div { margin-top: 12px; }
    .bl-show .bl-person { display: flex; align-items: center; gap: 10px; }
    .bl-show .bl-avatar { width: 28px; height: 28px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--mv-line); border-radius: 7px; background: var(--mv-surface-2); color: var(--mv-ink-2); font-size: 12px; font-weight: 600; }
    .bl-show .bl-with-icon { display: flex; align-items: center; gap: 6px; }
    .bl-show .bl-with-icon .mv-i { color: var(--mv-muted); }
    .bl-show .bl-quick { display: flex; flex-direction: column; gap: 8px; }
    .bl-show .bl-quick > a,
    .bl-show .bl-quick > button { justify-content: center; width: 100%; }

    @media (max-width: 1100px) { .bl-show .bl-layout { grid-template-columns: minmax(0, 1fr); } }
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
    $blNeedsRenewal = $businessLicense->is_expired || $businessLicense->is_expiring_soon;
    $blExpTone = $businessLicense->is_expired ? 'is-crit' : ($businessLicense->is_expiring_soon ? 'is-warn' : '');
@endphp
<div class="bl-show">
    <!-- Header -->
    <div class="bl-head">
        <div class="bl-head-main">
            <div class="bl-head-line">
                <span class="code-chip" title="License number">{{ $businessLicense->license_number }}</span>
                <h2 class="bl-title">{{ $businessLicense->license_name }}</h2>
                <span class="badge {{ $blStatusBadge($businessLicense->status) }}"><span class="bl-dot"></span>{{ $businessLicense->status_name }}</span>
            </div>
            <p class="bl-sub">{{ $businessLicense->license_direction_name }}</p>
        </div>
        <div class="bl-actions">
            <a href="{{ route('business-licenses.index', ['direction' => $businessLicense->license_direction]) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back
            </a>
            @if($businessLicense->document_path)
            <a href="{{ route('business-licenses.download', $businessLicense) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg>Download
            </a>
            @endif
            <a href="{{ route('business-licenses.edit', $businessLicense) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg>Edit
            </a>
            @if($businessLicense->canRenew())
            <a href="{{ route('business-licenses.renew', $businessLicense) }}" class="{{ $blNeedsRenewal ? 'btn-primary' : 'btn-secondary' }}">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg>Renew
            </a>
            @endif
        </div>
    </div>

    <div class="bl-layout">
        <!-- Main Content -->
        <div class="bl-main">
            <!-- License Status & Overview -->
            <section class="ui-card">
                <div class="ui-card-header">
                    <h3 class="bl-card-title">License Overview</h3>
                    <div class="bl-chips">
                        <span class="badge badge-gray">{{ $businessLicense->license_direction_name }}</span>
                        @if($businessLicense->isCompanyHeld())
                        <span class="badge {{ $blPriorityBadge($businessLicense->priority_level) }}">{{ $businessLicense->priority_level_name }} Priority</span>
                        @else
                        <span class="badge badge-gray">{{ $businessLicense->support_level_name }}</span>
                        @endif
                    </div>
                </div>
                <div class="ui-card-body">
                    @if($blNeedsRenewal)
                    <div class="bl-banner {{ $businessLicense->is_expired ? 'is-crit' : 'is-warn' }}" role="status">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-{{ $businessLicense->is_expired ? 'alert-circle' : 'alert-triangle' }}"/></svg>
                        <div>
                            <div class="bl-banner-title">
                                {{ $businessLicense->is_expired ? 'License Expired' : 'License Expiring Soon' }}
                            </div>
                            <div class="bl-banner-text">
                                @if($businessLicense->is_expired)
                                    Expired {{ abs($businessLicense->days_until_expiry) }} days ago
                                @else
                                    Expires in {{ $businessLicense->days_until_expiry }} days
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <dl class="bl-kv-grid">
                        <div>
                            <dt>License Type</dt>
                            <dd>{{ $businessLicense->license_type_name }}</dd>
                        </div>
                        <div>
                            <dt>Issuing Authority</dt>
                            <dd>{{ $businessLicense->issuing_authority }}</dd>
                        </div>
                        <div>
                            <dt>Issue Date</dt>
                            <dd>
                                {{ $businessLicense->issue_date ? $businessLicense->issue_date->format('M d, Y') : 'N/A' }}
                                @if($businessLicense->issue_date)<span class="bl-rel">{{ $businessLicense->issue_date->diffForHumans() }}</span>@endif
                            </dd>
                        </div>
                        <div>
                            <dt>Expiry Date</dt>
                            <dd class="{{ $blExpTone }}">
                                {{ $businessLicense->expiry_date ? $businessLicense->expiry_date->format('M d, Y') : 'N/A' }}
                                @if($blRel)<span class="bl-rel">{{ $blRel }}</span>@endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            @if($businessLicense->isCustomerIssued())
            <!-- Customer Information -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Customer Information</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-kv-grid">
                        <div>
                            <dt>Customer Name</dt>
                            <dd>{{ $businessLicense->customer_name }}</dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd><a href="mailto:{{ $businessLicense->customer_email }}" class="bl-link">{{ $businessLicense->customer_email }}</a></dd>
                        </div>
                        @if($businessLicense->customer_company)
                        <div>
                            <dt>Company</dt>
                            <dd>{{ $businessLicense->customer_company }}</dd>
                        </div>
                        @endif
                        @if($businessLicense->customer_phone)
                        <div>
                            <dt>Phone</dt>
                            <dd><a href="tel:{{ $businessLicense->customer_phone }}" class="bl-link">{{ $businessLicense->customer_phone }}</a></dd>
                        </div>
                        @endif
                        @if($businessLicense->customer_reference)
                        <div>
                            <dt>Customer Reference</dt>
                            <dd class="mv-mono">{{ $businessLicense->customer_reference }}</dd>
                        </div>
                        @endif
                        @if($businessLicense->customer_address)
                        <div class="bl-span-all">
                            <dt>Address</dt>
                            <dd class="bl-prose">{{ $businessLicense->customer_address }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>
            @endif

            <!-- Description & Details -->
            @if($businessLicense->description)
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Description</h3></div>
                <div class="ui-card-body">
                    <p class="bl-prose">{{ $businessLicense->description }}</p>
                </div>
            </section>
            @endif

            <!-- Financial Information -->
            @if($businessLicense->isCompanyHeld() && ($businessLicense->cost || $businessLicense->renewal_cost))
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Financial Information</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-kv-grid">
                        @if($businessLicense->cost)
                        <div>
                            <dt>Initial Cost</dt>
                            <dd class="bl-money">${{ number_format($businessLicense->cost, 2) }}</dd>
                        </div>
                        @endif
                        @if($businessLicense->renewal_cost)
                        <div>
                            <dt>Renewal Cost</dt>
                            <dd class="bl-money">${{ number_format($businessLicense->renewal_cost, 2) }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>
            @elseif($businessLicense->isCustomerIssued())
            <!-- Revenue Information -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Revenue Information</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-kv-grid">
                        <div>
                            <dt>Revenue Amount</dt>
                            <dd class="bl-money">${{ number_format($businessLicense->revenue_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt>Billing Cycle</dt>
                            <dd>{{ $businessLicense->billing_cycle_name }}</dd>
                        </div>
                        <div>
                            <dt>Annual Revenue</dt>
                            <dd class="bl-money">${{ number_format($businessLicense->annual_revenue, 2) }}</dd>
                        </div>
                        @if($businessLicense->license_quantity)
                        <div>
                            <dt>License Quantity</dt>
                            <dd>{{ $businessLicense->license_quantity }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>
            @endif

            @if($businessLicense->isCustomerIssued() && ($businessLicense->usage_limit || $businessLicense->service_start_date))
            <!-- License Details -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">License Details</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-kv-grid">
                        @if($businessLicense->usage_limit)
                        <div>
                            <dt>Usage Limit</dt>
                            <dd>{{ $businessLicense->usage_limit }}</dd>
                        </div>
                        @endif
                        @if($businessLicense->service_start_date)
                        <div>
                            <dt>Service Start Date</dt>
                            <dd>
                                {{ $businessLicense->service_start_date->format('M d, Y') }}
                                <span class="bl-rel">{{ $businessLicense->service_start_date->diffForHumans() }}</span>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>
            @endif

            @if($businessLicense->isCompanyHeld() && $businessLicense->business_impact)
            <!-- Business Impact -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Business Impact</h3></div>
                <div class="ui-card-body">
                    <p class="bl-prose">{{ $businessLicense->business_impact }}</p>
                </div>
            </section>
            @endif

            <!-- License Conditions / Terms -->
            @if($businessLicense->license_conditions || $businessLicense->license_terms)
            <section class="ui-card">
                <div class="ui-card-header">
                    <h3 class="bl-card-title">{{ $businessLicense->isCompanyHeld() ? 'License Conditions' : 'License Terms' }}</h3>
                </div>
                <div class="ui-card-body">
                    <p class="bl-prose">{{ $businessLicense->isCompanyHeld() ? $businessLicense->license_conditions : $businessLicense->license_terms }}</p>
                </div>
            </section>
            @endif

            @if($businessLicense->isCompanyHeld() && $businessLicense->compliance_notes)
            <!-- Compliance Notes -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Compliance Notes</h3></div>
                <div class="ui-card-body">
                    <p class="bl-prose">{{ $businessLicense->compliance_notes }}</p>
                </div>
            </section>
            @endif
        </div>

        <!-- Sidebar -->
        <aside class="bl-side">
            @if($businessLicense->isCompanyHeld())
            <!-- Assignment Information -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Assignment</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-list">
                        <div>
                            <dt>Department</dt>
                            <dd>{{ $businessLicense->department->name ?? 'Not Assigned' }}</dd>
                        </div>

                        @if($businessLicense->responsibleEmployee)
                        <div>
                            <dt>Responsible Employee</dt>
                            <dd class="bl-person">
                                <span class="bl-avatar" aria-hidden="true">{{ substr($businessLicense->responsibleEmployee->full_name, 0, 1) }}</span>
                                <span>
                                    {{ $businessLicense->responsibleEmployee->full_name }}
                                    @if($businessLicense->responsibleEmployee->email)
                                    <span class="bl-by">{{ $businessLicense->responsibleEmployee->email }}</span>
                                    @endif
                                </span>
                            </dd>
                        </div>
                        @endif

                        @if($businessLicense->location)
                        <div>
                            <dt>Location</dt>
                            <dd class="bl-with-icon"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-pin"/></svg>{{ $businessLicense->location }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>

            <!-- Renewal Settings -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Renewal Settings</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-list">
                        <div>
                            <dt>Reminder Days</dt>
                            <dd>{{ $businessLicense->renewal_reminder_days }} days before expiry</dd>
                        </div>
                        <div>
                            <dt>Auto Renewal</dt>
                            <dd><span class="badge {{ $businessLicense->auto_renewal ? 'badge-green' : 'badge-gray' }}"><span class="bl-dot"></span>{{ $businessLicense->auto_renewal ? 'Enabled' : 'Disabled' }}</span></dd>
                        </div>
                        @if($businessLicense->renewal_date)
                        <div>
                            <dt>Last Renewed</dt>
                            <dd>
                                {{ $businessLicense->renewal_date->format('M d, Y') }}
                                <span class="bl-rel">{{ $businessLicense->renewal_date->diffForHumans() }}</span>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>
            @else
            <!-- Customer License Settings -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">License Settings</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-list">
                        <div>
                            <dt>Support Level</dt>
                            <dd>{{ $businessLicense->support_level_name }}</dd>
                        </div>
                        <div>
                            <dt>Auto Renewal</dt>
                            <dd><span class="badge {{ $businessLicense->auto_renewal_customer ? 'badge-green' : 'badge-gray' }}"><span class="bl-dot"></span>{{ $businessLicense->auto_renewal_customer ? 'Enabled' : 'Disabled' }}</span></dd>
                        </div>
                        @if($businessLicense->renewal_date)
                        <div>
                            <dt>Last Renewed</dt>
                            <dd>
                                {{ $businessLicense->renewal_date->format('M d, Y') }}
                                <span class="bl-rel">{{ $businessLicense->renewal_date->diffForHumans() }}</span>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </section>
            @endif

            <!-- Additional Information -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Additional Info</h3></div>
                <div class="ui-card-body">
                    <dl class="bl-list">
                        @if($businessLicense->isCompanyHeld() && $businessLicense->regulatory_body)
                        <div>
                            <dt>Regulatory Body</dt>
                            <dd>{{ $businessLicense->regulatory_body }}</dd>
                        </div>
                        @endif

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
                    </dl>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="ui-card">
                <div class="ui-card-header"><h3 class="bl-card-title">Quick Actions</h3></div>
                <div class="ui-card-body bl-quick">
                    @if($businessLicense->canRenew() && ($businessLicense->is_expired || $businessLicense->is_expiring_soon))
                    <a href="{{ route('business-licenses.renew', $businessLicense) }}" class="btn-secondary">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg>Renew License
                    </a>
                    @endif

                    <a href="{{ route('business-licenses.edit', $businessLicense) }}" class="btn-secondary">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg>Edit License
                    </a>

                    @if($businessLicense->document_path)
                    <a href="{{ route('business-licenses.download', $businessLicense) }}" class="btn-secondary">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg>Download Document
                    </a>
                    @endif

                    @if($businessLicense->isCustomerIssued() && $businessLicense->customer_email)
                    <a href="mailto:{{ $businessLicense->customer_email }}?subject=Regarding License {{ $businessLicense->license_number }}" class="btn-secondary">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-mail"/></svg>Email Customer
                    </a>
                    @endif

                    <button type="button" onclick="if(confirm('Are you sure you want to delete this license?')) { document.getElementById('delete-form').submit(); }"
                            class="btn-danger">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-trash"/></svg>Delete License
                    </button>
                </div>
            </section>
        </aside>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" action="{{ route('business-licenses.destroy', $businessLicense) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection
