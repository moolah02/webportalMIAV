@extends('layouts.app')
@section('title', 'Completed Visits')

@section('content')

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-800">Completed Visits</h2>
        <p class="text-sm text-gray-500 mt-0.5">All closed and completed site visits</p>
    </div>
    <a href="{{ route('site_visits.createManual') }}" class="btn-primary">+ Log Visit</a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">✅</div>
        <div>
            <div class="stat-number">{{ number_format($stats['total']) }}</div>
            <div class="stat-label">Total Completed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📅</div>
        <div>
            <div class="stat-number">{{ number_format($stats['this_month']) }}</div>
            <div class="stat-label">This Month</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">⏱</div>
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
        <div class="stat-icon stat-icon-navy">🎯</div>
        <div>
            <div class="stat-number">{{ $stats['outcomes']['completed'] ?? 0 }}</div>
            <div class="stat-label">Outcome: Completed</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="filter-bar mb-5">
    <form method="GET" action="{{ route('site_visits.completed') }}" class="flex flex-wrap gap-3 items-end w-full">
        <div class="flex flex-col">
            <label class="ui-label">From Date</label>
            <input type="date" name="date_from" class="ui-input" value="{{ request('date_from') }}">
        </div>
        <div class="flex flex-col">
            <label class="ui-label">To Date</label>
            <input type="date" name="date_to" class="ui-input" value="{{ request('date_to') }}">
        </div>
        <div class="flex flex-col">
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
        <div class="flex flex-col">
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
        <div class="flex flex-col flex-1 min-w-48">
            <label class="ui-label">Search</label>
            <input type="text" name="search" class="ui-input" placeholder="Visit ID, terminal, merchant, technician…" value="{{ request('search') }}">
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('site_visits.completed') }}" class="btn-secondary">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">
            Visits
            <span class="text-gray-400 font-normal">({{ $visits->total() }})</span>
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $visit)
                <tr>
                    <td>
                        <span class="font-mono text-xs font-semibold" style="color:#1a3a5c">
                            {{ $visit->visit_id ?? '#'.$visit->id }}
                        </span>
                    </td>
                    <td>
                        @if($visit->started_at)
                            <div class="text-sm font-medium">{{ $visit->started_at->format('M j, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $visit->started_at->format('g:i A') }}</div>
                        @elseif($visit->visit_date)
                            <div class="text-sm font-medium">{{ $visit->visit_date->format('M j, Y') }}</div>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        @if($visit->technician)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" style="background:#1a3a5c">
                                    {{ substr($visit->technician->first_name,0,1) }}{{ substr($visit->technician->last_name,0,1) }}
                                </div>
                                <span class="text-sm text-gray-800">{{ $visit->technician->first_name }} {{ $visit->technician->last_name }}</span>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="text-sm font-medium">{{ $visit->posTerminal?->terminal_id ?? '—' }}</div>
                    </td>
                    <td>
                        <div class="text-sm font-medium">{{ $visit->posTerminal?->merchant_name ?? '—' }}</div>
                        @if($visit->posTerminal?->client)
                            <div class="text-xs text-gray-500">{{ $visit->posTerminal->client->company_name }}</div>
                        @endif
                    </td>
                    <td>
                        @if($visit->duration_minutes)
                            <span class="text-sm text-gray-700">{{ floor($visit->duration_minutes/60) }}h {{ $visit->duration_minutes % 60 }}m</span>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
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
                    <td>
                        <a href="{{ route('site_visits.show', $visit) }}" class="btn-secondary btn-sm">View Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-state-icon">✅</div>
                            <div class="empty-state-msg">No completed visits found</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($visits->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 flex justify-center">
        {{ $visits->links() }}
    </div>
    @endif
</div>

@endsection
