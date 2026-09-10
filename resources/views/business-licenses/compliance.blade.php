@extends('layouts.app')
@section('title', 'Compliance Report')

@push('styles')
<style>
    .bl-report { display: flex; flex-direction: column; gap: 16px; }
    .bl-report .bl-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .bl-report .bl-heading { margin: 0; font-size: 15px; font-weight: 600; color: var(--mv-ink); }
    .bl-report .bl-sub { margin: 2px 0 0; font-size: 13px; color: var(--mv-muted); }
    .bl-report .bl-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* Summary tiles */
    .bl-report .bl-tiles { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .bl-report .stat-card { padding: 14px 16px; gap: 12px; min-width: 0; }
    .bl-report .stat-icon { width: 34px; height: 34px; border-radius: 8px; }
    .bl-report .stat-icon .mv-i { width: 17px; height: 17px; }
    .bl-report .stat-number { font-size: 20px; }
    .bl-report .stat-label { margin-top: 3px; font-size: 12px; }
    .bl-report .bl-score { display: flex; flex-direction: column; justify-content: center; gap: 7px; padding: 14px 16px; min-width: 0; }
    .bl-report .bl-score-top { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; }
    .bl-report .bl-score-label { font-size: 12px; font-weight: 500; color: var(--mv-muted); }
    .bl-report .bl-score-value { font-size: 20px; font-weight: 600; line-height: 1.1; letter-spacing: -.02em; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
    .bl-report .bl-bar { height: 6px; border-radius: 3px; background: var(--mv-surface-2); box-shadow: inset 0 0 0 1px var(--mv-line); overflow: hidden; }
    .bl-report .bl-bar > span { display: block; height: 100%; border-radius: 3px; }
    .bl-report .bl-bar > .is-good { background: var(--mv-good); }
    .bl-report .bl-bar > .is-warn { background: #C28A2C; }
    .bl-report .bl-bar > .is-crit { background: var(--mv-crit); }
    .bl-report .bl-score-note { font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }

    /* Table card */
    .bl-report .bl-card { overflow: hidden; }
    .bl-report .bl-card .ui-card-header { padding: 12px 16px; gap: 12px; }
    .bl-report .bl-card-title { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
    .bl-report .ui-table thead th { white-space: nowrap; }
    .bl-report .ui-table tbody td { vertical-align: top; }
    .bl-report .bl-name { color: var(--mv-ink); font-weight: 500; line-height: 1.35; }
    .bl-report .bl-num { display: block; margin-top: 2px; font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); }
    .bl-report .bl-meta { display: block; font-size: 12px; line-height: 1.5; color: var(--mv-muted); }
    .bl-report .bl-strong { color: var(--mv-ink); font-weight: 500; }
    .bl-report .badge { gap: 5px; white-space: nowrap; }
    .bl-report .badge .mv-i { width: 13px; height: 13px; }
    .bl-report .bl-days { display: block; margin-top: 5px; font-size: 12px; font-weight: 500; font-variant-numeric: tabular-nums; }
    .bl-report .bl-days.is-warn { color: var(--mv-warn); }
    .bl-report .bl-days.is-crit { color: var(--mv-crit); }
    .bl-report .bl-col-actions { width: 1%; text-align: right; }
    .bl-report .action-group { justify-content: flex-end; gap: 4px; }
    .bl-report .action-btn { width: 30px; height: 30px; background: var(--mv-surface); }
    .bl-report .bl-card-foot { padding: 10px 16px; border-top: 1px solid var(--mv-line); }
    .bl-report .bl-empty { padding: 44px 16px; }
    .bl-report .empty-state-icon .mv-i { width: 28px; height: 28px; }
    .bl-report .bl-empty .empty-state-msg { margin: 0 0 14px; }

    @media (max-width: 1100px) { .bl-report .bl-tiles { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 560px)  { .bl-report .bl-tiles { grid-template-columns: minmax(0, 1fr); } }
</style>
@endpush

@section('content')
@php
    $total = $stats['compliant'] + $stats['warning'] + $stats['nonCompliant'];
    $score = $total > 0 ? round(($stats['compliant'] / $total) * 100) : 100;
    $scoreTone = $score >= 80 ? 'is-good' : ($score >= 60 ? 'is-warn' : 'is-crit');
    $blPriorityBadge = fn ($p) => match ($p) {
        'critical' => 'badge-red',
        'high' => 'badge-yellow',
        default => 'badge-gray',
    };
@endphp
<div class="bl-report">

    <div class="bl-toolbar">
        <div>
            <h2 class="bl-heading">{{ $direction === 'company_held' ? 'Internal Licenses' : 'Customer Licenses' }} — Compliance Report</h2>
            <p class="bl-sub">Licenses requiring attention (expiring within 30 days or already expired)</p>
        </div>
        <div class="bl-actions">
            <a href="{{ route('business-licenses.expiring', ['direction' => $direction]) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-hourglass"/></svg>Expiring Soon
            </a>
            <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Licenses
            </a>
        </div>
    </div>

    {{-- Compliance Summary --}}
    <div class="bl-tiles">
        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['compliant'] }}</div>
                <div class="stat-label">Compliant</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow"><svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['warning'] }}</div>
                <div class="stat-label">Warning (expiring soon)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-red"><svg class="mv-i" aria-hidden="true"><use href="#i-x-circle"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['nonCompliant'] }}</div>
                <div class="stat-label">Non-Compliant (expired)</div>
            </div>
        </div>

        {{-- Compliance Score --}}
        <div class="ui-card bl-score">
            <div class="bl-score-top">
                <span class="bl-score-label">Overall Compliance Score</span>
                <span class="bl-score-value">{{ $score }}%</span>
            </div>
            <div class="bl-bar" role="img" aria-label="Compliance score {{ $score }}%">
                <span class="{{ $scoreTone }}" style="width:{{ $score }}%"></span>
            </div>
            <div class="bl-score-note">{{ $stats['compliant'] }} of {{ $total }} licenses fully compliant</div>
        </div>
    </div>

    {{-- Licenses Needing Attention --}}
    <div class="ui-card bl-card">
        <div class="ui-card-header">
            <h3 class="bl-card-title">Licenses Requiring Action</h3>
            <span class="badge {{ $licenses->total() > 0 ? 'badge-red' : 'badge-gray' }}">{{ $licenses->total() }} items</span>
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
                        <th>Expiry</th>
                        <th>Compliance</th>
                        <th class="bl-col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($licenses as $license)
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
                            @if($license->compliance_status === 'non_compliant')
                                <span class="badge badge-red"><svg class="mv-i" aria-hidden="true"><use href="#i-x-circle"/></svg>Non-Compliant</span>
                                <span class="bl-days is-crit">{{ abs((int)$license->days_until_expiry) }}d overdue</span>
                            @elseif($license->compliance_status === 'warning')
                                <span class="badge badge-yellow"><svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg>Warning</span>
                                <span class="bl-days is-warn">{{ (int)$license->days_until_expiry }}d remaining</span>
                            @else
                                <span class="badge badge-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg>Compliant</span>
                            @endif
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
            <p class="empty-state-msg">All licenses are compliant! No immediate action required.</p>
            <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary">View All Licenses</a>
        </div>
        @endif
    </div>
</div>

@endsection
