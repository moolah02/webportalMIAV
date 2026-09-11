@extends('layouts.app')
@section('title', 'Completed Visits')

@push('styles')
<style>
.cv-bar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.cv-bar p{margin:0;font-size:13px;color:var(--mv-muted)}
.cv-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:16px}
@media (max-width:1000px){.cv-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
.mv-page .cv-stats .stat-card{padding:14px 16px}
.cv-filters.filter-bar{padding:14px 16px;margin-bottom:16px}
.cv-form{display:flex;flex-wrap:wrap;align-items:flex-end;gap:12px;width:100%}
.cv-f{display:flex;flex-direction:column;min-width:0}
.cv-f .ui-label{margin-bottom:4px}
.cv-filters .ui-input,.cv-filters .ui-select{padding-top:8px;padding-bottom:8px;font-size:13px}
.cv-f-grow{flex:1 1 14rem}
.cv-f-grow .ui-input{width:100%}
.cv-f-actions{display:flex;align-items:flex-end;gap:8px}
.cv-count{font-size:12px;color:var(--mv-muted);font-variant-numeric:tabular-nums}
.mv-page .badge{display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.cv-id{font-size:12.5px;font-weight:500;color:var(--mv-ink)}
.cv-strong{font-weight:500;color:var(--mv-ink)}
.cv-sub{margin-top:2px;font-size:12px;color:var(--mv-muted)}
.cv-none{color:var(--mv-muted)}
.cv-nowrap{white-space:nowrap}
.cv-tech{display:flex;align-items:center;gap:8px;white-space:nowrap}
.cv-avatar{width:26px;height:26px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;background:var(--mv-accent-soft);color:var(--mv-accent-ink);font-size:10.5px;font-weight:600;letter-spacing:.02em}
.cv-num{font-variant-numeric:tabular-nums;white-space:nowrap}
.cv-actions{width:1%;text-align:right}
.cv-pager{display:flex;justify-content:center;padding:12px 16px;border-top:1px solid var(--mv-line)}
.cv-empty{padding:40px 16px;text-align:center}
.cv-empty .empty-state-icon .mv-i{display:block;margin:0 auto}
.cv-empty p{margin:0;font-size:13.5px;color:var(--mv-ink-2)}
</style>
@endpush

@section('content')

{{-- Toolbar --}}
<div class="cv-bar">
    <p>All closed and completed site visits</p>
    <a href="{{ route('site_visits.createManual') }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg>Log Visit</a>
</div>

{{-- Stats --}}
<div class="cv-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ number_format($stats['total']) }}</div>
            <div class="stat-label">Total Completed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg></div>
        <div>
            <div class="stat-number">{{ number_format($stats['this_month']) }}</div>
            <div class="stat-label">This Month</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-clock"/></svg></div>
        <div>
            <div class="stat-number">
                @if($stats['avg_duration'])
                    {{ floor($stats['avg_duration'] / 60) }}h {{ $stats['avg_duration'] % 60 }}m
                @else
                    —
                @endif
            </div>
            <div class="stat-label">Avg Duration</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-target"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['outcomes']['completed'] ?? 0 }}</div>
            <div class="stat-label">Outcome: Completed</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="filter-bar cv-filters">
    <form method="GET" action="{{ route('site_visits.completed') }}" class="cv-form">
        <div class="cv-f">
            <label class="ui-label">From Date</label>
            <input type="date" name="date_from" class="ui-input" value="{{ request('date_from') }}">
        </div>
        <div class="cv-f">
            <label class="ui-label">To Date</label>
            <input type="date" name="date_to" class="ui-input" value="{{ request('date_to') }}">
        </div>
        <div class="cv-f">
            <label class="ui-label">Technician</label>
            <select name="technician_id" class="ui-select">
                <option value="">All Technicians</option>
                @foreach($technicians as $tech)
                    <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>
                        {{ $tech->first_name }} {{ $tech->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="cv-f">
            <label class="ui-label">Outcome</label>
            <select name="outcome" class="ui-select">
                <option value="">All Outcomes</option>
                <option value="completed" {{ request('outcome') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="could_not_access_site" {{ request('outcome') === 'could_not_access_site' ? 'selected' : '' }}>Could Not Access</option>
                <option value="parts_required" {{ request('outcome') === 'parts_required' ? 'selected' : '' }}>Parts Required</option>
                <option value="reschedule" {{ request('outcome') === 'reschedule' ? 'selected' : '' }}>Rescheduled</option>
                <option value="terminal_not_found" {{ request('outcome') === 'terminal_not_found' ? 'selected' : '' }}>Terminal Not Found</option>
                <option value="terminal_relocated" {{ request('outcome') === 'terminal_relocated' ? 'selected' : '' }}>Terminal Relocated</option>
            </select>
        </div>
        <div class="cv-f cv-f-grow">
            <label class="ui-label">Search</label>
            <input type="text" name="search" class="ui-input" placeholder="Visit ID, terminal, merchant, technician…" value="{{ request('search') }}">
        </div>
        <div class="cv-f-actions">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('site_visits.completed') }}" class="btn-secondary">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <h3>Visits</h3>
        <span class="cv-count">{{ number_format($visits->total()) }} total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>Visit ID</th>
                    <th>Date</th>
                    <th>Technician</th>
                    <th>Terminal</th>
                    <th>Merchant</th>
                    <th>Duration</th>
                    <th>Terminal Status</th>
                    <th>Outcome</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $visit)
                <tr>
                    <td><span class="mv-mono cv-id">{{ $visit->visit_id ?? '#'.$visit->id }}</span></td>
                    <td class="cv-nowrap">
                        @if($visit->started_at)
                            <div class="cv-strong">{{ $visit->started_at->format('M j, Y') }}</div>
                            <div class="cv-sub">{{ $visit->started_at->format('g:i A') }}</div>
                        @elseif($visit->visit_date)
                            <div class="cv-strong">{{ $visit->visit_date->format('M j, Y') }}</div>
                        @else
                            <span class="cv-none">—</span>
                        @endif
                    </td>
                    <td>
                        @if($visit->technician)
                            <div class="cv-tech">
                                <span class="cv-avatar" aria-hidden="true">{{ substr($visit->technician->first_name,0,1) }}{{ substr($visit->technician->last_name,0,1) }}</span>
                                <span>{{ $visit->technician->first_name }} {{ $visit->technician->last_name }}</span>
                            </div>
                        @else
                            <span class="cv-none">—</span>
                        @endif
                    </td>
                    <td>
                        @if($visit->posTerminal?->terminal_id)
                            <span class="mv-mono cv-strong">{{ $visit->posTerminal->terminal_id }}</span>
                        @else
                            <span class="cv-none">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="cv-strong">{{ $visit->posTerminal?->merchant_name ?? '—' }}</div>
                        @if($visit->posTerminal?->client)
                            <div class="cv-sub">{{ $visit->posTerminal->client->company_name }}</div>
                        @endif
                    </td>
                    <td>
                        @if($visit->duration_minutes)
                            <span class="cv-num">{{ floor($visit->duration_minutes/60) }}h {{ $visit->duration_minutes % 60 }}m</span>
                        @else
                            <span class="cv-none">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $ts = $visit->terminal_status_during_visit ?? $visit->terminal_status;
                            $tsMap = [
                                'working'           => ['badge-green',  'Working'],
                                'not_working'       => ['badge-red',    'Not Working'],
                                'needs_maintenance' => ['badge-yellow', 'Needs Maint.'],
                                'not_found'         => ['badge-gray',   'Not Found'],
                            ];
                            [$tsCls, $tsLbl] = $tsMap[$ts] ?? ['badge-gray', ucwords(str_replace('_',' ',$ts ?? 'Unknown'))];
                        @endphp
                        <span class="badge {{ $tsCls }}">{{ $tsLbl }}</span>
                    </td>
                    <td>
                        @php
                            $outMap = [
                                'completed'             => ['badge-green',  'Completed'],
                                'could_not_access_site' => ['badge-red',    'No Access'],
                                'parts_required'        => ['badge-yellow', 'Parts Needed'],
                                'reschedule'            => ['badge-yellow', 'Rescheduled'],
                                'terminal_not_found'    => ['badge-gray',   'Not Found'],
                                'terminal_relocated'    => ['badge-blue',   'Relocated'],
                            ];
                            [$outCls, $outLbl] = $outMap[$visit->outcome ?? ''] ?? ['badge-gray', $visit->status === 'closed' ? 'Closed' : '—'];
                        @endphp
                        <span class="badge {{ $outCls }}">{{ $outLbl }}</span>
                    </td>
                    <td class="cv-actions">
                        <a href="{{ route('site_visits.show', $visit) }}" class="action-btn" title="View Details" aria-label="View details for visit {{ $visit->visit_id ?? $visit->id }}"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="cv-empty">
                            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
                            <p>No completed visits found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($visits->hasPages())
    <div class="cv-pager">
        {{ $visits->links() }}
    </div>
    @endif
</div>

@endsection
