@extends('layouts.app')
@section('title', 'Technician Visit Reports')

@push('styles')
<style>
    .tv { display: grid; gap: 16px; }
    .tv .mv-i { width: 16px; height: 16px; }
    .tv-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .tv-kpi { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 14px 16px; }
    .tv-kpi-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12.5px; font-weight: 500; color: var(--mv-muted); }
    .tv-kpi-top .mv-i { color: var(--mv-muted); }
    .tv-kpi-value { margin-top: 6px; font-size: 24px; font-weight: 600; letter-spacing: -.02em; line-height: 1.15; color: var(--mv-ink); font-variant-numeric: tabular-nums; }

    .tv-filters { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 12px 14px; }
    .tv-filters form { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px; }
    .tv-field { display: flex; flex-direction: column; gap: 4px; }
    .tv-field .ui-label { margin: 0; }
    .tv-field .ui-input, .tv-field .ui-select { height: 36px; font-size: 13.5px; }
    .tv-field-grow { flex: 1; min-width: 220px; }
    .tv-actions { display: flex; gap: 8px; margin-left: auto; }
    .tv-actions a, .tv-actions button { display: inline-flex; align-items: center; gap: 6px; height: 36px; white-space: nowrap; }

    .tv-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .tv-card-head { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-bottom: 1px solid var(--mv-line); font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .tv-count { font-size: 12px; font-weight: 500; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 6px; padding: 0 7px; font-variant-numeric: tabular-nums; }
    .tv .ui-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .tv .ui-table th { text-align: left; white-space: nowrap; }
    .tv-id { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 6px; white-space: nowrap; }
    .tv-code { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-accent-ink); background: var(--mv-accent-soft); border-radius: 5px; padding: 1px 6px; white-space: nowrap; }
    .tv-main { color: var(--mv-ink); font-weight: 500; }
    .tv-sub { font-size: 12px; color: var(--mv-muted); }
    .tv-summary { max-width: 340px; color: var(--mv-ink-2); line-height: 1.45; }
    .tv-muted { color: var(--mv-muted); }
    .tv .badge { display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
    .tv .badge .mv-i { width: 13px; height: 13px; }
    .tv-empty { padding: 44px 16px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
    .tv-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); display: block; margin: 0 auto 8px; }
    .tv-pagination { padding: 12px 16px; border-top: 1px solid var(--mv-line); }

    @media (max-width: 900px) { .tv-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } .tv-actions { margin-left: 0; } }
</style>
@endpush

@section('content')
<div class="tv">

    {{-- Figures --}}
    <div class="tv-kpis">
        <div class="tv-kpi">
            <div class="tv-kpi-top">Today's Visits <svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg></div>
            <div class="tv-kpi-value" id="stat-today">{{ $stats['today_visits'] ?? 0 }}</div>
        </div>
        <div class="tv-kpi">
            <div class="tv-kpi-top">Completed <svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
            <div class="tv-kpi-value" id="stat-completed">{{ $stats['completed'] ?? 0 }}</div>
        </div>
        <div class="tv-kpi">
            <div class="tv-kpi-top">Pending <svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg></div>
            <div class="tv-kpi-value" id="stat-pending">{{ $stats['pending'] ?? 0 }}</div>
        </div>
        <div class="tv-kpi">
            <div class="tv-kpi-top">Total Visits <svg class="mv-i" aria-hidden="true"><use href="#i-clipboard"/></svg></div>
            <div class="tv-kpi-value" id="stat-total">{{ $stats['total_visits'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="tv-filters">
        <form id="filterForm" method="GET" action="{{ route('reports.technician-visits') }}">
            <div class="tv-field">
                <label class="ui-label" for="dateRange">Date Range</label>
                <select class="ui-select" name="date_range" id="dateRange" onchange="toggleCustomDates(this.value)">
                    <option value="today"        {{ request('date_range') === 'today'        ? 'selected' : '' }}>Today</option>
                    <option value="yesterday"    {{ request('date_range') === 'yesterday'    ? 'selected' : '' }}>Yesterday</option>
                    <option value="last_7_days"  {{ !request('date_range') || request('date_range') === 'last_7_days'  ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last_30_days" {{ request('date_range') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="this_month"   {{ request('date_range') === 'this_month'   ? 'selected' : '' }}>This Month</option>
                    <option value="custom"       {{ request('date_range') === 'custom'       ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <div class="tv-field" id="customFrom" style="{{ request('date_range') === 'custom' ? '' : 'display:none' }}">
                <label class="ui-label">From</label>
                <input type="date" name="start_date" class="ui-input" value="{{ request('start_date') }}">
            </div>
            <div class="tv-field" id="customTo" style="{{ request('date_range') === 'custom' ? '' : 'display:none' }}">
                <label class="ui-label">To</label>
                <input type="date" name="end_date" class="ui-input" value="{{ request('end_date') }}">
            </div>

            <div class="tv-field">
                <label class="ui-label">Employee</label>
                <select class="ui-select" name="technician_id">
                    <option value="">All Employees</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>
                            {{ $tech->first_name }} {{ $tech->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="tv-field">
                <label class="ui-label">Status</label>
                <select class="ui-select" name="status">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="tv-field tv-field-grow">
                <label class="ui-label">Search</label>
                <input type="text" class="ui-input" name="search"
                       placeholder="Merchant, summary, action points…" value="{{ request('search') }}">
            </div>

            <div class="tv-actions">
                <button type="submit" class="btn-primary">Apply</button>
                <a href="{{ route('reports.technician-visits') }}" class="btn-secondary">Reset</a>
                <a href="{{ route('reports.technician-visits.export') }}?{{ http_build_query(request()->all()) }}" class="btn-secondary">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export CSV
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="tv-card">
        <div class="tv-card-head">
            Visit Reports
            <span class="tv-count">{{ number_format($visits->total()) }}</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date &amp; Time</th>
                        <th>Employee</th>
                        <th>Merchant</th>
                        <th>Assignment</th>
                        <th>Terminal</th>
                        <th>Status</th>
                        <th>Summary</th>
                        <th>Evidence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                    @php
                        $terminal = is_array($visit->terminal) ? $visit->terminal : [];
                        $evidence = is_array($visit->evidence) ? $visit->evidence : [];
                    @endphp
                    <tr>
                        <td><span class="tv-id">{{ $visit->id }}</span></td>
                        <td style="white-space:nowrap;">
                            @if($visit->completed_at)
                                <div class="tv-main">{{ $visit->completed_at->format('M j, Y') }}</div>
                                <div class="tv-sub">{{ $visit->completed_at->format('H:i') }}</div>
                            @else
                                <span class="tv-muted">Not completed</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ optional($visit->employee)->full_name ?? ('Emp #'.$visit->employee_id) }}</td>
                        <td>
                            <div class="tv-main">{{ $visit->merchant_name ?? '—' }}</div>
                            <div class="tv-sub">ID: <span class="mv-mono">{{ $visit->merchant_id }}</span></div>
                        </td>
                        <td class="mv-mono" style="font-size:12px;">{{ $visit->assignment_id ?? '—' }}</td>
                        <td>
                            @if(!empty($terminal['terminal_id']))
                                <span class="tv-code">{{ $terminal['terminal_id'] }}</span>
                                @if(!empty($terminal['status']))
                                    <div class="tv-sub" style="margin-top:2px;">{{ $terminal['status'] }}</div>
                                @endif
                            @else
                                <span class="tv-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($visit->completed_at)
                                <span class="badge badge-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Completed</span>
                            @else
                                <span class="badge badge-yellow"><svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg> Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="tv-summary">{{ \Illuminate\Support\Str::limit($visit->visit_summary, 100) }}</div>
                            @if($visit->action_points)
                                <div class="tv-sub" style="margin-top:2px;">{{ \Illuminate\Support\Str::limit($visit->action_points, 80) }}</div>
                            @endif
                        </td>
                        <td>
                            @if(count($evidence))
                                <span class="badge badge-blue"><svg class="mv-i" aria-hidden="true"><use href="#i-paperclip"/></svg> {{ count($evidence) }}</span>
                            @else
                                <span class="tv-muted">None</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('visits.show', $visit) }}" class="btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="tv-empty">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-clipboard"/></svg>
                                No visits found for the selected filters
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
        <div class="tv-pagination">
            {{ $visits->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCustomDates(val) {
    const show = val === 'custom';
    document.getElementById('customFrom').style.display = show ? '' : 'none';
    document.getElementById('customTo').style.display   = show ? '' : 'none';
}
</script>
@endpush
