@extends('layouts.app')
@section('title', 'Audit Trail')

@push('styles')
<style>
    .au { display: grid; gap: 16px; }
    .au .mv-i { width: 16px; height: 16px; }
    .au-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .au-toolbar p { margin: 0; font-size: 13px; color: var(--mv-muted); }
    .au-toolbar a { display: inline-flex; align-items: center; gap: 6px; }

    .au-summary { display: grid; grid-template-columns: 200px 200px minmax(0, 1fr); gap: 1px; background: var(--mv-line); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .au-summary > div { background: var(--mv-surface); padding: 14px 18px; min-width: 0; }
    .au-fig-value { font-size: 22px; font-weight: 600; letter-spacing: -.02em; color: var(--mv-ink); line-height: 1.15; font-variant-numeric: tabular-nums; }
    .au-fig-label { font-size: 12.5px; color: var(--mv-muted); margin-top: 2px; }
    .au-areas-label { font-size: 12.5px; color: var(--mv-muted); margin-bottom: 8px; }
    .au-areas { display: flex; flex-wrap: wrap; gap: 6px; }
    .au-area { display: inline-flex; align-items: center; gap: 6px; height: 28px; padding: 0 10px; border-radius: 7px; border: 1px solid var(--mv-line); background: var(--mv-surface); color: var(--mv-ink-2); font-size: 12.5px; text-decoration: none; }
    .au-area b { font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
    .au-area:hover { border-color: var(--mv-line-strong); background: var(--mv-surface-2); color: var(--mv-ink); }
    .au-area.is-on { border-color: #C9D9EE; background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
    .au-area.is-on b { color: var(--mv-accent-ink); }

    .au-filters { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 12px 14px; display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px; }
    .au-filters .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .au-filters .filter-group-grow { flex: 1; min-width: 200px; }
    .au-filters .ui-label { margin: 0; }
    .au-filters .ui-input, .au-filters .ui-select { height: 36px; font-size: 13.5px; padding-top: 0 !important; padding-bottom: 0 !important; line-height: 34px; }
    .au-filters .filter-actions { display: flex; gap: 8px; margin-left: auto; }

    .au-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .au .ui-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .au .ui-table th { text-align: left; white-space: nowrap; }
    .au-when { white-space: nowrap; font-variant-numeric: tabular-nums; }
    .au-when div:first-child { color: var(--mv-ink); }
    .au-sub { font-size: 12px; color: var(--mv-muted); }
    .au-who { color: var(--mv-ink); font-weight: 500; white-space: nowrap; }
    .au-mono { font-family: var(--mv-mono); font-size: 12px; }
    .au-desc { max-width: 420px; color: var(--mv-ink-2); line-height: 1.45; }
    .au-toggle { margin-left: 6px; background: none; border: 0; padding: 0; font: inherit; font-size: 12.5px; color: var(--mv-accent-ink); cursor: pointer; }
    .au-toggle:hover { text-decoration: underline; }
    .au-diff { margin-top: 8px; display: grid; gap: 6px; }
    .au-diff > div { font-family: var(--mv-mono); font-size: 12px; line-height: 1.5; border-radius: 6px; padding: 8px 10px; border: 1px solid var(--mv-line); background: var(--mv-surface-2); color: var(--mv-ink-2); word-break: break-word; }
    .au-diff strong { display: block; font-family: var(--mv-sans); font-size: 12px; font-weight: 600; margin-bottom: 2px; }
    .au-diff .is-before strong { color: var(--mv-crit); }
    .au-diff .is-after strong { color: var(--mv-good); }
    .au .badge { white-space: nowrap; }
    .au-empty { padding: 48px 16px; text-align: center; }
    .au-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); display: block; margin: 0 auto 8px; }
    .au-empty-title { font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .au-empty-sub { font-size: 13px; color: var(--mv-muted); margin-top: 4px; }
    .au-pagination { padding: 12px 16px; border-top: 1px solid var(--mv-line); }

    @media (max-width: 1000px) { .au-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); } .au-summary > div:last-child { grid-column: 1 / -1; } }
</style>
@endpush

@section('content')
@php
    $actionBadges = [
        'approved'       => 'badge-green',
        'completed'      => 'badge-green',
        'created'        => 'badge-blue',
        'status_changed' => 'badge-blue',
        'updated'        => 'badge-gray',
        'rejected'       => 'badge-red',
        'deleted'        => 'badge-red',
        'cancelled'      => 'badge-gray',
    ];
@endphp
<div class="au">

    <div class="au-toolbar">
        <p>Complete history of all system actions, categorised by area</p>
        <a href="{{ route('audit-trail.export-analysis', request()->only(['date_from','date_to'])) }}" class="btn-primary">
            <svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Analysis PDF
        </a>
    </div>

    {{-- Summary --}}
    <div class="au-summary">
        <div>
            <div class="au-fig-value">{{ number_format($stats['total']) }}</div>
            <div class="au-fig-label">Total Events Logged</div>
        </div>
        <div>
            <div class="au-fig-value">{{ number_format($stats['today']) }}</div>
            <div class="au-fig-label">Events Today</div>
        </div>
        <div>
            <div class="au-areas-label">By area</div>
            <div class="au-areas">
                @foreach($stats['byCategory'] as $cat => $count)
                    @if($count > 0)
                    <a href="?category={{ urlencode($cat) }}" class="au-area {{ request('category') === $cat ? 'is-on' : '' }}">
                        {{ $cat }} <b>{{ number_format($count) }}</b>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="au-filters">
        <div class="filter-group filter-group-grow">
            <label class="ui-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description…" class="ui-input">
        </div>
        <div class="filter-group">
            <label class="ui-label">Category</label>
            <select name="category" class="ui-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label">Action</label>
            <select name="action" class="ui-select">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $action)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label">Employee</label>
            <select name="employee_id" class="ui-select">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->first_name }} {{ $emp->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="ui-input">
        </div>
        <div class="filter-group">
            <label class="ui-label">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="ui-input">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply</button>
            @if(request()->hasAny(['search','action','category','employee_id','date_from','date_to']))
                <a href="{{ route('audit-trail.index') }}" class="btn-secondary">Clear</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="au-card">
        <div style="overflow-x:auto;">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Who</th>
                    <th>Category</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>Description</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="au-when">
                            <div>{{ $log->created_at->format('d M Y') }}</div>
                            <div class="au-sub">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            @if($log->employee)
                                <div class="au-who">{{ $log->employee->first_name }} {{ $log->employee->last_name }}</div>
                                <div class="au-sub au-mono">{{ $log->employee->employee_number ?? '' }}</div>
                            @else
                                <span class="au-sub">System</span>
                            @endif
                        </td>
                        <td><span class="badge badge-gray">{{ $log->category }}</span></td>
                        <td>
                            <span class="badge {{ $actionBadges[$log->action] ?? 'badge-gray' }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td>
                            @if($log->model_type)
                                <div style="color:var(--mv-ink-2);">{{ $log->model_type }}</div>
                                @if($log->model_id)
                                    <div class="au-sub au-mono">#{{ $log->model_id }}</div>
                                @endif
                            @else
                                <span class="au-sub">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="au-desc">
                                {{ $log->description }}
                                @if($log->old_values || $log->new_values)
                                    <button type="button" onclick="toggleChanges({{ $log->id }})" class="au-toggle">View changes</button>
                                @endif
                            </div>
                            @if($log->old_values || $log->new_values)
                                <div id="changes-{{ $log->id }}" class="hidden au-diff">
                                    @if($log->old_values)
                                        <div class="is-before">
                                            <strong>Before:</strong>
                                            @foreach($log->old_values as $k => $v)
                                                <div>{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($log->new_values)
                                        <div class="is-after">
                                            <strong>After:</strong>
                                            @foreach($log->new_values as $k => $v)
                                                <div>{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="au-mono au-sub" style="white-space:nowrap;">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="au-empty">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-history"/></svg>
                                <div class="au-empty-title">No audit log entries found</div>
                                <div class="au-empty-sub">Activity will appear here as users perform actions.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($logs->hasPages())
            <div class="au-pagination">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleChanges(id) {
    const el = document.getElementById('changes-' + id);
    el.classList.toggle('hidden');
}
</script>
@endpush
@endsection
