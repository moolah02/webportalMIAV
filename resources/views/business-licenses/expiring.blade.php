@extends('layouts.app')
@section('title', 'Expiring Licenses')

@push('styles')
<style>
    .bl-report { display: flex; flex-direction: column; gap: 16px; }
    .bl-report .bl-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .bl-report .bl-heading { margin: 0; font-size: 15px; font-weight: 600; color: var(--mv-ink); }
    .bl-report .bl-sub { margin: 2px 0 0; font-size: 13px; color: var(--mv-muted); }
    .bl-report .bl-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* Summary tiles */
    .bl-report .bl-tiles { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .bl-report .stat-card { padding: 14px 16px; gap: 12px; min-width: 0; }
    .bl-report .stat-icon { width: 34px; height: 34px; border-radius: 8px; }
    .bl-report .stat-icon .mv-i { width: 17px; height: 17px; }
    .bl-report .stat-number { font-size: 20px; }
    .bl-report .stat-label { margin-top: 3px; font-size: 12px; }

    /* Days window switch */
    .bl-report .bl-seg { display: inline-flex; gap: 2px; padding: 3px; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 9px; }
    .bl-report .bl-seg a { padding: 4px 10px; border-radius: 6px; font-size: 12.5px; font-weight: 500; line-height: 1.4; color: var(--mv-ink-2); text-decoration: none; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .bl-report .bl-seg a:hover { color: var(--mv-ink); background: var(--mv-surface-2); }
    .bl-report .bl-seg a.is-active { color: var(--mv-accent-ink); background: var(--mv-accent-soft); font-weight: 600; }

    /* Table card */
    .bl-report .bl-card { overflow: hidden; }
    .bl-report .bl-card .ui-card-header { padding: 10px 16px; gap: 12px; flex-wrap: wrap; }
    .bl-report .bl-card-head-left { display: flex; align-items: center; gap: 10px; }
    .bl-report .bl-card-title { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
    .bl-report .ui-table thead th { white-space: nowrap; }
    .bl-report .ui-table tbody td { vertical-align: top; }
    .bl-report .bl-name { color: var(--mv-ink); font-weight: 500; line-height: 1.35; }
    .bl-report .bl-num { display: block; margin-top: 2px; font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); }
    .bl-report .bl-meta { display: block; font-size: 12px; line-height: 1.5; color: var(--mv-muted); }
    .bl-report .bl-strong { color: var(--mv-ink); font-weight: 500; }
    .bl-report .badge { gap: 6px; white-space: nowrap; }
    .bl-report .bl-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .bl-report .bl-days { font-size: 13px; font-weight: 500; color: var(--mv-ink-2); white-space: nowrap; font-variant-numeric: tabular-nums; }
    .bl-report .bl-days.is-warn { color: var(--mv-warn); }
    .bl-report .bl-days.is-crit { color: var(--mv-crit); }
    .bl-report .bl-col-actions { width: 1%; text-align: right; }
    .bl-report .action-group { justify-content: flex-end; gap: 4px; }
    .bl-report .action-btn { width: 30px; height: 30px; background: var(--mv-surface); }
    .bl-report .bl-card-foot { padding: 10px 16px; border-top: 1px solid var(--mv-line); }
    .bl-report .bl-empty { padding: 44px 16px; }
    .bl-report .empty-state-icon .mv-i { display: block; margin: 0 auto; width: 28px; height: 28px; }
    .bl-report .bl-empty .empty-state-msg { margin: 0 0 14px; }

    @media (max-width: 760px) { .bl-report .bl-tiles { grid-template-columns: minmax(0, 1fr); } }
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
<div class="bl-report">

    <div class="bl-toolbar">
        <div>
            <h2 class="bl-heading">Expiring {{ $direction === 'company_held' ? 'Internal' : 'Customer' }} Licenses</h2>
            <p class="bl-sub">Licenses expiring within {{ $days }} days</p>
        </div>
        <div class="bl-actions">
            <a href="{{ route('business-licenses.compliance', ['direction' => $direction]) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-shield"/></svg>Compliance Report
            </a>
            <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Licenses
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="bl-tiles">
        <div class="stat-card">
            <div class="stat-icon stat-icon-red"><svg class="mv-i" aria-hidden="true"><use href="#i-x-circle"/></svg></div>
            <div>
                <div class="stat-number">{{ $licenses->where('is_expired', true)->count() }}</div>
                <div class="stat-label">Already Expired</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow"><svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg></div>
            <div>
                <div class="stat-number">{{ $licenses->where('is_expired', false)->count() }}</div>
                <div class="stat-label">Expiring Soon</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg></div>
            <div>
                <div class="stat-number">{{ $licenses->total() }}</div>
                <div class="stat-label">Total Affected</div>
            </div>
        </div>
    </div>

    <div class="ui-card bl-card">
        <div class="ui-card-header">
            <div class="bl-card-head-left">
                <h3 class="bl-card-title">Expiring Licenses</h3>
                <span class="badge {{ $licenses->total() > 0 ? 'badge-yellow' : 'badge-gray' }}">{{ $licenses->total() }} licenses</span>
            </div>

            {{-- Days filter --}}
            <nav class="bl-seg" aria-label="Expiry window">
                @foreach([15, 30, 60, 90] as $d)
                <a href="{{ route('business-licenses.expiring', ['direction' => $direction, 'days' => $d]) }}"
                   class="{{ $days == $d ? 'is-active' : '' }}">{{ $d }} days</a>
                @endforeach
            </nav>
        </div>

        @if($licenses->count() > 0)
        <div style="overflow-x:auto;">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>License</th>
                        @if($direction === 'company_held')
                        <th>Department</th>
                        <th>Priority</th>
                        @else
                        <th>Customer</th>
                        <th>Revenue</th>
                        @endif
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                        <th>Status</th>
                        <th class="bl-col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($licenses as $license)
                    @php $days_left = (int) $license->days_until_expiry; @endphp
                    <tr>
                        <td>
                            <div class="bl-name">{{ $license->license_name }}</div>
                            <span class="bl-num">{{ $license->license_number }}</span>
                            <span class="bl-meta">{{ $license->license_type_name }}</span>
                        </td>
                        @if($direction === 'company_held')
                        <td>
                            <div class="bl-strong">{{ $license->department->name ?? '—' }}</div>
                            <span class="bl-meta">{{ $license->responsibleEmployee->full_name ?? '' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $blPriorityBadge($license->priority_level) }}">{{ $license->priority_level_name }}</span>
                        </td>
                        @else
                        <td>
                            <div class="bl-strong">{{ $license->customer_display_name }}</div>
                            <span class="bl-meta">{{ $license->customer_email }}</span>
                        </td>
                        <td>
                            <div class="bl-strong">${{ number_format($license->revenue_amount ?? 0, 0) }}</div>
                            <span class="bl-meta">{{ $license->billing_cycle_name }}</span>
                        </td>
                        @endif
                        <td>
                            {{ $license->expiry_date ? $license->expiry_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td>
                            @if($license->is_expired)
                                <span class="bl-days is-crit">{{ abs($days_left) }}d overdue</span>
                            @elseif($days_left <= 7)
                                <span class="bl-days is-crit">{{ $days_left }}d left</span>
                            @elseif($days_left <= 30)
                                <span class="bl-days is-warn">{{ $days_left }}d left</span>
                            @else
                                <span class="bl-days">{{ $days_left }}d left</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $blStatusBadge($license->status) }}"><span class="bl-dot"></span>{{ $license->status_name }}</span>
                        </td>
                        <td class="bl-col-actions">
                            <div class="action-group">
                                <a href="{{ route('business-licenses.show', $license) }}" class="action-btn" title="View" aria-label="View">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>
                                </a>
                                @if($license->canRenew())
                                <a href="{{ route('business-licenses.renew', $license) }}" class="action-btn" title="Renew" aria-label="Renew">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($licenses->hasPages())
        <div class="bl-card-foot">
            {{ $licenses->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <div class="empty-state bl-empty">
            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
            <p class="empty-state-msg">No licenses expiring within {{ $days }} days.</p>
            <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary">Back to Licenses</a>
        </div>
        @endif
    </div>
</div>

@endsection
