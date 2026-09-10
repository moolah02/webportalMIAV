{{-- resources/views/business-licenses/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Business Licenses')

@push('styles')
<style>
    .bl-index { display: flex; flex-direction: column; gap: 16px; }
    .bl-index .bl-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .bl-index .bl-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* Direction switch */
    .bl-index .bl-seg { display: inline-flex; gap: 2px; padding: 3px; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 9px; }
    .bl-index .bl-seg a { padding: 5px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; line-height: 1.4; color: var(--mv-ink-2); text-decoration: none; white-space: nowrap; }
    .bl-index .bl-seg a:hover { color: var(--mv-ink); background: var(--mv-surface-2); }
    .bl-index .bl-seg a.is-active { color: var(--mv-accent-ink); background: var(--mv-accent-soft); font-weight: 600; }

    /* Summary tiles */
    .bl-index .bl-stats { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; transition: opacity .15s ease; }
    .bl-index .stat-card { padding: 13px 15px; gap: 12px; min-width: 0; }
    .bl-index .stat-card > div:last-child { min-width: 0; }
    .bl-index .stat-icon { width: 34px; height: 34px; border-radius: 8px; }
    .bl-index .stat-icon .mv-i { width: 17px; height: 17px; }
    .bl-index .stat-number { font-size: 20px; }
    .bl-index .stat-label { margin-top: 3px; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Register card */
    .bl-index .bl-card { overflow: hidden; }
    .bl-index .bl-card-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; padding: 14px 16px 12px; }
    .bl-index .bl-card-title { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
    .bl-index .bl-card-sub { margin: 2px 0 0; font-size: 12.5px; color: var(--mv-muted); }
    .bl-index .bl-count { font-size: 12.5px; color: var(--mv-muted); white-space: nowrap; }

    /* Filters */
    .bl-index .bl-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: 0; padding: 0 16px 12px; border-bottom: 1px solid var(--mv-line); }
    .bl-index .bl-search { position: relative; flex: 1 1 240px; min-width: 200px; }
    .bl-index .bl-search .mv-i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--mv-muted); pointer-events: none; }
    .bl-index .bl-filters .ui-input,
    .bl-index .bl-filters .ui-select { height: 34px; padding-top: 0; padding-bottom: 0; font-size: 13px; }
    .bl-index .bl-search .ui-input { padding-left: 32px; }
    .bl-index .bl-filters .ui-select { width: auto; min-width: 128px; padding-left: 10px; padding-right: 30px; }
    .bl-index .bl-filters .btn-secondary { height: 34px; padding-top: 0; padding-bottom: 0; font-size: 13px; }
    .bl-index .bl-loading { padding: 7px 16px; font-size: 12px; color: var(--mv-muted); background: var(--mv-surface-2); border-bottom: 1px solid var(--mv-line); }

    /* Table */
    .bl-index .ui-table thead th { white-space: nowrap; }
    .bl-index .ui-table tbody td { vertical-align: top; }
    .bl-index .bl-name { color: var(--mv-ink); font-weight: 500; line-height: 1.35; }
    .bl-index .bl-num { display: block; margin-top: 2px; font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); }
    .bl-index .bl-meta { display: block; font-size: 12px; line-height: 1.5; color: var(--mv-muted); }
    .bl-index .bl-note { max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bl-index .bl-strong { color: var(--mv-ink); font-weight: 500; }
    .bl-index .badge { gap: 6px; white-space: nowrap; }
    .bl-index .badge + .bl-meta { margin-top: 5px; }
    .bl-index .bl-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .bl-index .bl-days { display: block; margin-top: 5px; font-size: 12px; font-weight: 500; font-variant-numeric: tabular-nums; }
    .bl-index .bl-days.is-warn { color: var(--mv-warn); }
    .bl-index .bl-days.is-crit { color: var(--mv-crit); }
    .bl-index .bl-dates { display: grid; grid-template-columns: auto 1fr; gap: 2px 10px; margin: 0; font-size: 12.5px; white-space: nowrap; }
    .bl-index .bl-dates dt { font-weight: 400; color: var(--mv-muted); }
    .bl-index .bl-dates dd { margin: 0; color: var(--mv-ink-2); font-variant-numeric: tabular-nums; }
    .bl-index .bl-col-actions { width: 1%; text-align: right; }
    .bl-index .action-group { justify-content: flex-end; gap: 4px; }
    .bl-index .action-btn { width: 30px; height: 30px; background: var(--mv-surface); }

    /* Empty + footer */
    .bl-index .bl-empty { padding: 44px 16px; }
    .bl-index .empty-state-icon .mv-i { display: block; margin: 0 auto; width: 28px; height: 28px; }
    .bl-index .bl-empty-title { margin: 0 0 4px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .bl-index .bl-empty p { margin: 0 0 14px; }
    .bl-index .bl-card-foot { padding: 10px 16px; border-top: 1px solid var(--mv-line); }

    @media (max-width: 1280px) { .bl-index .bl-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 640px)  { .bl-index .bl-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
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
<div class="bl-index">

    {{-- Toolbar: direction switch left, actions right --}}
    <div class="bl-toolbar">
        <nav class="bl-seg" aria-label="License direction">
            <a href="{{ route('business-licenses.index', ['direction' => 'company_held']) }}"
               class="{{ $direction === 'company_held' ? 'is-active' : '' }}">Internal Licenses</a>
            <a href="{{ route('business-licenses.index', ['direction' => 'customer_issued']) }}"
               class="{{ $direction === 'customer_issued' ? 'is-active' : '' }}">Customer Licenses</a>
            <a href="{{ route('business-licenses.index', ['direction' => 'all']) }}"
               class="{{ $direction === 'all' ? 'is-active' : '' }}">All / History</a>
        </nav>

        @if($direction !== 'all')
        <div class="bl-actions">
            <a href="{{ route('business-licenses.compliance', ['direction' => $direction]) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-shield"/></svg>Compliance
            </a>
            <a href="{{ route('business-licenses.expiring', ['direction' => $direction]) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-hourglass"/></svg>Expiring
            </a>
            <a href="{{ route('business-licenses.create', ['direction' => $direction]) }}" class="btn-primary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg>Add {{ $direction === 'company_held' ? 'Internal License' : 'Customer License' }}
            </a>
        </div>
        @endif
    </div>

    {{-- Stats --}}
    <div id="stats-cards" class="bl-stats">
        @if($direction === 'company_held')
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg></div>
                <div>
                    <div class="stat-number" id="total-count">{{ $stats['total_licenses'] }}</div>
                    <div class="stat-label">Total Licenses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
                <div>
                    <div class="stat-number" id="active-count">{{ $stats['active_licenses'] }}</div>
                    <div class="stat-label">Active Licenses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-yellow"><svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg></div>
                <div>
                    <div class="stat-number" id="expiring-count">{{ $stats['expiring_soon'] }}</div>
                    <div class="stat-label">Expiring Soon</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-red"><svg class="mv-i" aria-hidden="true"><use href="#i-x-circle"/></svg></div>
                <div>
                    <div class="stat-number" id="expired-count">{{ $stats['expired_licenses'] }}</div>
                    <div class="stat-label">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-shield"/></svg></div>
                <div>
                    <div class="stat-number" id="critical-count">{{ $stats['critical_licenses'] }}</div>
                    <div class="stat-label">Critical Priority</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-banknote"/></svg></div>
                <div>
                    <div class="stat-number" id="annual-cost">${{ number_format($stats['total_annual_cost'], 0) }}</div>
                    <div class="stat-label">Annual Cost</div>
                </div>
            </div>
        @elseif($direction === 'customer_issued')
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg></div>
                <div>
                    <div class="stat-number" id="total-count">{{ $stats['total_licenses'] }}</div>
                    <div class="stat-label">Total Licenses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
                <div>
                    <div class="stat-number" id="active-count">{{ $stats['active_licenses'] }}</div>
                    <div class="stat-label">Active Licenses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-yellow"><svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg></div>
                <div>
                    <div class="stat-number" id="expiring-count">{{ $stats['expiring_soon'] }}</div>
                    <div class="stat-label">Expiring Soon</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-red"><svg class="mv-i" aria-hidden="true"><use href="#i-x-circle"/></svg></div>
                <div>
                    <div class="stat-number" id="expired-count">{{ $stats['expired_licenses'] }}</div>
                    <div class="stat-label">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-banknote"/></svg></div>
                <div>
                    <div class="stat-number" id="revenue-amount">${{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="stat-label">Annual Revenue</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg></div>
                <div>
                    <div class="stat-number" id="customers-count">{{ $stats['unique_customers'] }}</div>
                    <div class="stat-label">Unique Customers</div>
                </div>
            </div>
        @else
            {{-- All / History --}}
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg></div>
                <div>
                    <div class="stat-number">{{ $stats['total_licenses'] }}</div>
                    <div class="stat-label">Total Records</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
                <div>
                    <div class="stat-number">{{ $stats['active_licenses'] }}</div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-yellow"><svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg></div>
                <div>
                    <div class="stat-number">{{ $stats['expiring_soon'] }}</div>
                    <div class="stat-label">Expiring Soon</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-red"><svg class="mv-i" aria-hidden="true"><use href="#i-x-circle"/></svg></div>
                <div>
                    <div class="stat-number">{{ $stats['expired_licenses'] }}</div>
                    <div class="stat-label">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg></div>
                <div>
                    <div class="stat-number">{{ $stats['company_held'] }}</div>
                    <div class="stat-label">Internal</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-user"/></svg></div>
                <div>
                    <div class="stat-number">{{ $stats['customer_issued'] }}</div>
                    <div class="stat-label">Customer-Issued</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Register --}}
    <div class="ui-card bl-card">
        <div class="bl-card-head">
            <div>
                <h2 class="bl-card-title">
                    @if($direction === 'company_held') Internal Licenses
                    @elseif($direction === 'customer_issued') Customer Licenses
                    @else All Licenses — History &amp; Lookup
                    @endif
                </h2>
                <p class="bl-card-sub">
                    @if($direction === 'company_held') Internal licenses and compliance records
                    @elseif($direction === 'customer_issued') Repository of customer-issued licenses
                    @else Complete record of all issued and held licenses
                    @endif
                </p>
            </div>
            <span class="bl-count">{{ number_format($licenses->total()) }} {{ \Illuminate\Support\Str::plural('record', $licenses->total()) }}</span>
        </div>

        {{-- Filters --}}
        <form id="filter-form" method="GET" class="bl-filters">
            <input type="hidden" name="direction" value="{{ $direction }}" id="current-direction">

            <div class="bl-search">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-search"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ $direction === 'company_held' ? 'Search licenses…' : 'Search licenses or customers…' }}"
                       class="ui-input" id="search-input">
            </div>

            <select name="status" class="ui-select" id="status-filter">
                <option value="">All Status</option>
                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="license_type" class="ui-select" id="type-filter">
                <option value="">All Types</option>
                @foreach(\App\Models\BusinessLicense::LICENSE_TYPES as $key => $label)
                    <option value="{{ $key }}" {{ request('license_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            @if($direction === 'company_held')
                <select name="priority" class="ui-select" id="priority-filter">
                    <option value="">All Priority</option>
                    @foreach(\App\Models\BusinessLicense::PRIORITY_LEVELS as $key => $label)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="department" class="ui-select" id="department-filter">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            @elseif($direction === 'customer_issued')
                <select name="billing_cycle" class="ui-select" id="billing-filter">
                    <option value="">All Billing</option>
                    @foreach(\App\Models\BusinessLicense::BILLING_CYCLES as $key => $label)
                        <option value="{{ $key }}" {{ request('billing_cycle') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="support_level" class="ui-select" id="support-filter">
                    <option value="">All Support</option>
                    @foreach(\App\Models\BusinessLicense::SUPPORT_LEVELS as $key => $label)
                        <option value="{{ $key }}" {{ request('support_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-filter"/></svg>Apply
            </button>
            @if(request()->hasAny(['search', 'status', 'license_type', 'priority', 'department', 'billing_cycle', 'support_level']))
                <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary" id="clear-filters">Clear</a>
            @endif
        </form>

        {{-- Loading (no spinner/animation) --}}
        <div id="loading-indicator" class="bl-loading" style="display:none;">Updating statistics…</div>

        @if($licenses->count() > 0)
            <div style="overflow-x:auto;">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>License Details</th>
                            @if($direction === 'company_held')
                                <th>Department</th>
                                <th>Responsible</th>
                                <th>Priority</th>
                            @elseif($direction === 'customer_issued')
                                <th>Customer</th>
                                <th>Revenue</th>
                                <th>Support</th>
                            @else
                                <th>Type</th>
                                <th>Dept / Customer</th>
                                <th>Issuing Authority</th>
                            @endif
                            <th>Status</th>
                            <th>Dates</th>
                            <th class="bl-col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($licenses as $license)
                            <tr>
                                {{-- License details --}}
                                <td>
                                    <div class="bl-name">{{ $license->license_name }}</div>
                                    <span class="bl-num">{{ $license->license_number }}</span>
                                    <span class="bl-meta">{{ $license->license_type_name }}</span>
                                    @if($license->description)
                                        <span class="bl-meta bl-note" title="{{ $license->description }}">{{ Str::limit($license->description, 100) }}</span>
                                    @endif
                                </td>

                                @if($direction === 'all')
                                    <td>
                                        <span class="badge badge-gray">{{ $license->isCompanyHeld() ? 'Company' : 'Customer' }}</span>
                                    </td>
                                    <td>
                                        @if($license->isCompanyHeld())
                                            <div class="bl-strong">{{ $license->department->name ?? 'Unassigned' }}</div>
                                            <span class="bl-meta">{{ $license->responsibleEmployee->full_name ?? '' }}</span>
                                        @else
                                            <div class="bl-strong">{{ $license->customer_display_name }}</div>
                                            <span class="bl-meta">{{ $license->customer_email }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $license->issuing_authority }}</td>
                                @elseif($license->isCompanyHeld())
                                    <td>
                                        <div class="bl-strong">{{ $license->department->name ?? 'Unassigned' }}</div>
                                        @if($license->location)
                                            <span class="bl-meta">{{ $license->location }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="bl-strong">{{ $license->responsibleEmployee->full_name ?? 'Unassigned' }}</div>
                                        @if($license->responsibleEmployee?->email)
                                            <span class="bl-meta">{{ $license->responsibleEmployee->email }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $blPriorityBadge($license->priority_level) }}">{{ $license->priority_level_name }}</span>
                                        @if($license->renewal_cost)
                                            <span class="bl-meta">${{ number_format($license->renewal_cost, 0) }}/yr</span>
                                        @endif
                                    </td>
                                @else
                                    <td>
                                        <div class="bl-strong">{{ $license->customer_display_name }}</div>
                                        <span class="bl-meta">{{ $license->customer_email }}</span>
                                        @if($license->license_quantity)
                                            <span class="bl-meta">Qty: {{ $license->license_quantity }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="bl-strong">${{ number_format($license->revenue_amount ?? 0, 0) }}</div>
                                        <span class="bl-meta">{{ $license->billing_cycle_name }}</span>
                                        @if($license->annual_revenue)
                                            <span class="bl-meta">${{ number_format($license->annual_revenue, 0) }}/yr</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-gray">{{ $license->support_level_name }}</span>
                                        @if($license->usage_limit)
                                            <span class="bl-meta">{{ Str::limit($license->usage_limit, 30) }}</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- Status --}}
                                <td>
                                    <span class="badge {{ $blStatusBadge($license->status) }}"><span class="bl-dot"></span>{{ $license->status_name }}</span>
                                    @if($license->is_expired)
                                        <span class="bl-days is-crit">{{ abs((int)$license->days_until_expiry) }} days overdue</span>
                                    @elseif($license->is_expiring_soon)
                                        <span class="bl-days is-warn">{{ (int)$license->days_until_expiry }} days left</span>
                                    @endif
                                </td>

                                {{-- Dates --}}
                                <td>
                                    <dl class="bl-dates">
                                        <dt>Issued</dt>
                                        <dd>{{ $license->issue_date ? $license->issue_date->format('M d, Y') : 'N/A' }}</dd>
                                        <dt>Expires</dt>
                                        <dd>{{ $license->expiry_date ? $license->expiry_date->format('M d, Y') : 'N/A' }}</dd>
                                        @if($license->renewal_date)
                                            <dt>Renewed</dt>
                                            <dd>{{ $license->renewal_date->format('M d, Y') }}</dd>
                                        @endif
                                    </dl>
                                </td>

                                {{-- Actions --}}
                                <td class="bl-col-actions">
                                    <div class="action-group">
                                        <a href="{{ route('business-licenses.show', $license) }}" class="action-btn" title="View" aria-label="View">
                                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>
                                        </a>

                                        @if($license->canRenew() && ($license->is_expired || $license->is_expiring_soon))
                                            <a href="{{ route('business-licenses.renew', $license) }}" class="action-btn" title="Renew" aria-label="Renew">
                                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('business-licenses.edit', $license) }}" class="action-btn" title="Edit" aria-label="Edit">
                                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg>
                                        </a>

                                        @if($license->document_path)
                                            <a href="{{ route('business-licenses.download', $license) }}" class="action-btn" title="Document" aria-label="Document">
                                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state bl-empty">
                <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg></div>
                <h3 class="bl-empty-title">No records found</h3>
                <p class="empty-state-msg">Start by adding {{ $direction === 'company_held' ? 'an internal license' : 'a customer license' }}.</p>
                <a href="{{ route('business-licenses.create', ['direction' => $direction]) }}" class="btn-primary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg>Add {{ $direction === 'company_held' ? 'Internal License' : 'Customer License' }}
                </a>
            </div>
        @endif

        {{-- Pagination --}}
        @if($licenses->hasPages())
            <div class="bl-card-foot">
                {{ $licenses->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterInputs = document.querySelectorAll('#filter-form select, #filter-form input[type="text"]');
    const loadingIndicator = document.getElementById('loading-indicator');
    const statsCards = document.getElementById('stats-cards');

    filterInputs.forEach(input => {
        input.addEventListener('change', updateStats);
        if (input.type === 'text') {
            let timeout;
            input.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(updateStats, 500);
            });
        }
    });

    function updateStats() {
        loadingIndicator.style.display = 'block';
        statsCards.style.opacity = '0.7';

        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams(formData);

        fetch(`{{ route('business-licenses.filtered-stats') }}?${params}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) updateStatsCards(data.stats, data.direction);
            })
            .catch(console.error)
            .finally(() => {
                loadingIndicator.style.display = 'none';
                statsCards.style.opacity = '1';
            });
    }

    function updateStatsCards(stats, direction) {
        document.getElementById('active-count').textContent = stats.active_licenses;
        document.getElementById('expiring-count').textContent = stats.expiring_soon;
        document.getElementById('expired-count').textContent = stats.expired_licenses;
        document.getElementById('total-count').textContent = stats.total_licenses;

        if (direction === 'company_held') {
            document.getElementById('annual-cost').textContent = '$' + numberFormat(stats.total_annual_cost);
            document.getElementById('critical-count').textContent = stats.critical_licenses;
        } else {
            document.getElementById('revenue-amount').textContent = '$' + numberFormat(stats.total_revenue);
            document.getElementById('customers-count').textContent = stats.unique_customers;
        }
    }

    function numberFormat(num) {
        return new Intl.NumberFormat().format(num);
    }
});
</script>
@endsection
