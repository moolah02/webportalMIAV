{{-- resources/views/jobs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Job Assignments')

@push('styles')
<style>
    .jl-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 16px; }
    .jl-stat { padding: 14px 18px; }
    .jl-stat + .jl-stat { border-left: 1px solid var(--mv-line); }
    .jl-stat-label { font-size: 12.5px; color: var(--mv-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 7px; }
    .jl-stat-value { font-size: 22px; font-weight: 600; color: var(--mv-ink); letter-spacing: -.02em; font-variant-numeric: tabular-nums; line-height: 1.15; }
    .jl-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--mv-line-strong); }
    .jl-dot.is-accent { background: var(--mv-accent); }
    .jl-dot.is-warn { background: #C28A2C; }
    .jl-dot.is-good { background: var(--mv-good); }

    .jl-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
    .jl-seg { display: inline-flex; background: var(--mv-surface); border: 1px solid var(--mv-line-strong); border-radius: 8px; padding: 2px; }
    .jl-seg a { padding: 5px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; color: var(--mv-ink-2); text-decoration: none; }
    .jl-seg a:hover { color: var(--mv-ink); }
    .jl-seg a.is-on { background: var(--mv-accent-soft); color: var(--mv-accent-ink); }

    .jl-filters { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;
                  display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; align-items: end; }
    .jl-filters .ui-label { display: block; margin-bottom: 5px; }
    .jl-filters .ui-input, .jl-filters .ui-select { width: 100%; }
    .jl-filters .jl-search { grid-column: span 2; }
    .jl-filter-actions { display: flex; gap: 8px; }

    .jl-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .jl-card-head { display: flex; align-items: center; justify-content: space-between; padding: 13px 18px; border-bottom: 1px solid var(--mv-line); }
    .jl-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .jl-count { font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
    .jl-table { width: 100%; border-collapse: collapse; }
    .jl-table th { background: var(--mv-surface-2); color: var(--mv-muted); font-size: 11.5px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; text-align: left; padding: 9px 14px; border-bottom: 1px solid var(--mv-line); white-space: nowrap; }
    .jl-table td { padding: 11px 14px; border-bottom: 1px solid var(--mv-line); vertical-align: middle; font-size: 13px; color: var(--mv-ink-2); }
    .jl-table tbody tr:last-child td { border-bottom: 0; }
    .jl-table tbody tr:hover { background: var(--mv-surface-2); }
    .jl-id { font-family: var(--mv-mono); font-size: 12.5px; font-weight: 500; color: var(--mv-ink); text-decoration: none; }
    .jl-id:hover { color: var(--mv-accent-ink); text-decoration: underline; }
    .jl-sub { font-size: 12px; color: var(--mv-muted); margin-top: 2px; }
    .jl-strong { color: var(--mv-ink); font-weight: 500; }
    .jl-num { font-variant-numeric: tabular-nums; }
    .jl-chip { display: inline-flex; align-items: center; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; line-height: 1.7; white-space: nowrap; background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }
    .jl-chip.is-accent { background: var(--mv-accent-soft); color: var(--mv-accent-ink); border-color: transparent; }
    .jl-chip.is-good { background: var(--mv-good-soft); color: var(--mv-good); border-color: transparent; }
    .jl-chip.is-warn { background: var(--mv-warn-soft); color: var(--mv-warn); border-color: transparent; }
    .jl-chip.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); border-color: transparent; }
    .jl-view { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border: 1px solid var(--mv-line-strong); border-radius: 7px; font-size: 12.5px; font-weight: 500; color: var(--mv-ink-2); text-decoration: none; background: var(--mv-surface); }
    .jl-view:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
    .jl-pager { padding: 10px 16px; border-top: 1px solid var(--mv-line); }
    .jl-empty { padding: 40px 20px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
    .jl-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); display: block; margin: 0 auto 10px; }
    @media (max-width: 900px) { .jl-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .jl-stat:nth-child(3) { border-left: 0; } .jl-stat:nth-child(n+3) { border-top: 1px solid var(--mv-line); } }
</style>
@endpush

@section('content')
@php
    $scopeNow = $scope ?? 'all';
    $statusTone = ['completed' => 'is-good', 'in_progress' => 'is-warn', 'assigned' => 'is-accent', 'cancelled' => 'is-crit'];
    $priorityTone = ['emergency' => 'is-crit', 'high' => 'is-warn'];
@endphp

{{-- Stats --}}
<div class="jl-stats">
    <div class="jl-stat">
        <div class="jl-stat-label"><span class="jl-dot"></span>Total Assignments</div>
        <div class="jl-stat-value">{{ $assignments->total() ?? 0 }}</div>
    </div>
    <div class="jl-stat">
        <div class="jl-stat-label"><span class="jl-dot is-accent"></span>Assigned</div>
        <div class="jl-stat-value">{{ $assignments->where('status', 'assigned')->count() }}</div>
    </div>
    <div class="jl-stat">
        <div class="jl-stat-label"><span class="jl-dot is-good"></span>Completed</div>
        <div class="jl-stat-value">{{ $assignments->where('status', 'completed')->count() }}</div>
    </div>
    <div class="jl-stat">
        <div class="jl-stat-label"><span class="jl-dot is-warn"></span>In Progress</div>
        <div class="jl-stat-value">{{ $assignments->where('status', 'in_progress')->count() }}</div>
    </div>
</div>

{{-- Scope toggle --}}
<div class="jl-toolbar">
    <nav class="jl-seg" aria-label="Assignment scope">
        <a href="{{ route('jobs.mine') }}" class="{{ $scopeNow === 'mine' ? 'is-on' : '' }}">My Assignments</a>
        <a href="{{ route('jobs.index') }}" class="{{ $scopeNow === 'all' ? 'is-on' : '' }}">All Assignments</a>
    </nav>
</div>

{{-- Filters --}}
<form method="GET" class="jl-filters">
    <div>
        <label class="ui-label" for="f-status">Status</label>
        <select id="f-status" name="status" class="ui-select">
            <option value="">All Status</option>
            @foreach (['assigned'=>'Assigned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $val => $lbl)
                <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ui-label" for="f-from">From Date</label>
        <input id="f-from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="ui-input">
    </div>
    <div>
        <label class="ui-label" for="f-to">To Date</label>
        <input id="f-to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="ui-input">
    </div>
    @if($scopeNow === 'all')
    <div>
        <label class="ui-label" for="f-priority">Priority</label>
        <select id="f-priority" name="priority" class="ui-select">
            <option value="">All Priority</option>
            @foreach (['low'=>'Low','normal'=>'Normal','high'=>'High','emergency'=>'Emergency'] as $pval => $plbl)
                <option value="{{ $pval }}" {{ ($filters['priority'] ?? '') === $pval ? 'selected' : '' }}>{{ $plbl }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ui-label" for="f-tech">Technician</label>
        <select id="f-tech" name="technician_id" class="ui-select">
            <option value="">All Technicians</option>
            @foreach ($technicians as $t)
                <option value="{{ $t->id }}" {{ ($filters['technician_id'] ?? '') == $t->id ? 'selected' : '' }}>{{ $t->first_name }} {{ $t->last_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ui-label" for="f-client">Client</label>
        <select id="f-client" name="client_id" class="ui-select">
            <option value="">All Clients</option>
            @foreach ($clients as $c)
                <option value="{{ $c->id }}" {{ ($filters['client_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="jl-search">
        <label class="ui-label" for="f-q">Search</label>
        <input id="f-q" type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="ui-input" placeholder="Assignment ID or notes...">
    </div>
    <div class="jl-filter-actions">
        <button type="submit" class="btn-primary">Apply</button>
        <a href="{{ $scopeNow === 'all' ? route('jobs.index') : route('jobs.mine') }}" class="btn-secondary">Reset</a>
    </div>
</form>

{{-- Table --}}
<div class="jl-card">
    <div class="jl-card-head">
        <h2>Assignments</h2>
        <span class="jl-count">{{ $assignments->count() }} shown</span>
    </div>

    @if($assignments->count() > 0)
    <div style="overflow-x:auto;">
        <table class="jl-table">
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Details</th>
                    @if($scopeNow === 'all') <th>Technician</th> @endif
                    <th>Scheduled</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($assignments as $a)
                <tr>
                    <td>
                        <a href="{{ route('jobs.show', $a->id) }}" class="jl-id">{{ $a->assignment_id }}</a>
                        <div class="jl-sub">Created {{ $a->created_at?->diffForHumans() }}</div>
                    </td>
                    <td>
                        <div class="jl-strong">
                            {{ $a->list_title ?? implode(' · ', array_filter([
                                $a->project->project_name ?? null,
                                $a->client->company_name ?? null,
                                $a->region->name ?? null,
                            ])) ?: '—' }}
                        </div>
                        <div class="jl-sub">
                            {{ \Illuminate\Support\Str::headline($a->service_type) }}
                            · <span class="jl-num">{{ is_array($a->pos_terminals) ? count($a->pos_terminals) : ($a->terminal_count ?? 0) }}</span> terminals
                            @if(!empty($a->terminal_merchant_preview))
                                · {{ $a->terminal_merchant_preview }}@if(($a->terminal_count ?? 0) > 3) + more @endif
                            @endif
                        </div>
                    </td>
                    @if($scopeNow === 'all')
                    <td>
                        @if($a->technician)
                            <div class="jl-strong">{{ $a->technician->first_name }} {{ $a->technician->last_name }}</div>
                        @else
                            <span class="jl-sub">Unassigned</span>
                        @endif
                    </td>
                    @endif
                    <td>
                        <div class="jl-strong jl-num">{{ optional($a->scheduled_date)->format('M j, Y') ?? '—' }}</div>
                        <div class="jl-sub">{{ optional($a->scheduled_date)?->diffForHumans() }}</div>
                    </td>
                    <td><span class="jl-chip {{ $priorityTone[$a->priority] ?? '' }}">{{ \Illuminate\Support\Str::headline($a->priority) }}</span></td>
                    <td><span class="jl-chip {{ $statusTone[$a->status] ?? '' }}">{{ \Illuminate\Support\Str::headline($a->status) }}</span></td>
                    <td style="text-align:right;">
                        <a href="{{ route('jobs.show', $a->id) }}" class="jl-view" title="View assignment">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg> View
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if($assignments->hasPages())
    <div class="jl-pager">
        {{ $assignments->links() }}
    </div>
    @endif
    @else
    <div class="jl-empty">
        <svg class="mv-i" aria-hidden="true"><use href="#i-clipboard"/></svg>
        No assignments found. Try adjusting your filters.
        <div style="margin-top:12px;">
            <a href="{{ $scopeNow === 'all' ? route('jobs.index') : route('jobs.mine') }}" class="btn-secondary btn-sm">Reset filters</a>
        </div>
    </div>
    @endif
</div>
@endsection
