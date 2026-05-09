@extends('layouts.app')
@section('title', 'Technician Visit Reports')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📅</div>
        <div>
            <div class="stat-number" id="stat-today">{{ $stats['today_visits'] ?? 0 }}</div>
            <div class="stat-label">Today's Visits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">✅</div>
        <div>
            <div class="stat-number" id="stat-completed">{{ $stats['completed'] ?? 0 }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">⏳</div>
        <div>
            <div class="stat-number" id="stat-pending">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-navy">📋</div>
        <div>
            <div class="stat-number" id="stat-total">{{ $stats['total_visits'] ?? 0 }}</div>
            <div class="stat-label">Total Visits</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="filter-bar mb-5">
    <form id="filterForm" method="GET" action="{{ route('reports.technician-visits') }}" class="flex flex-wrap gap-3 items-end w-full">
        <div class="flex flex-col">
            <label class="ui-label">Date Range</label>
            <select class="ui-select" name="date_range" id="dateRange" onchange="toggleCustomDates(this.value)">
                <option value="today"        {{ request('date_range') === 'today'        ? 'selected' : '' }}>Today</option>
                <option value="yesterday"    {{ request('date_range') === 'yesterday'    ? 'selected' : '' }}>Yesterday</option>
                <option value="last_7_days"  {{ !request('date_range') || request('date_range') === 'last_7_days'  ? 'selected' : '' }}>Last 7 Days</option>
                <option value="last_30_days" {{ request('date_range') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="this_month"   {{ request('date_range') === 'this_month'   ? 'selected' : '' }}>This Month</option>
                <option value="custom"       {{ request('date_range') === 'custom'       ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>

        <div class="flex flex-col" id="customFrom" style="{{ request('date_range') === 'custom' ? '' : 'display:none' }}">
            <label class="ui-label">From</label>
            <input type="date" name="start_date" class="ui-input" value="{{ request('start_date') }}">
        </div>
        <div class="flex flex-col" id="customTo" style="{{ request('date_range') === 'custom' ? '' : 'display:none' }}">
            <label class="ui-label">To</label>
            <input type="date" name="end_date" class="ui-input" value="{{ request('end_date') }}">
        </div>

        <div class="flex flex-col">
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

        <div class="flex flex-col">
            <label class="ui-label">Status</label>
            <select class="ui-select" name="status">
                <option value="">All Status</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
            </select>
        </div>

        <div class="flex flex-col flex-1 min-w-48">
            <label class="ui-label">Search</label>
            <input type="text" class="ui-input" name="search"
                   placeholder="Merchant, summary, action points…" value="{{ request('search') }}">
        </div>

        <div class="flex gap-2 items-end">
            <button type="submit" class="btn-primary">Apply</button>
            <a href="{{ route('reports.technician-visits') }}" class="btn-secondary">Reset</a>
            <a href="{{ route('reports.technician-visits.export') }}?{{ http_build_query(request()->all()) }}" class="btn-secondary">
                ↓ Export CSV
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">
            Visit Reports
            <span class="text-gray-400 font-normal">({{ $visits->total() }})</span>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date & Time</th>
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
                    <td>
                        <span class="inline-block px-2 py-0.5 rounded-md text-white text-xs font-semibold" style="background:#1a3a5c">
                            {{ $visit->id }}
                        </span>
                    </td>
                    <td>
                        @if($visit->completed_at)
                            <div class="text-sm font-medium">{{ $visit->completed_at->format('M j, Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $visit->completed_at->format('H:i') }}</div>
                        @else
                            <span class="text-xs text-gray-400">Not completed</span>
                        @endif
                    </td>
                    <td class="text-sm text-gray-700">
                        {{ optional($visit->employee)->full_name ?? ('Emp #'.$visit->employee_id) }}
                    </td>
                    <td>
                        <div class="text-sm font-semibold text-gray-900">{{ $visit->merchant_name ?? '—' }}</div>
                        <div class="text-xs text-gray-400">ID: {{ $visit->merchant_id }}</div>
                    </td>
                    <td class="text-sm text-gray-600">{{ $visit->assignment_id ?? '—' }}</td>
                    <td>
                        @if(!empty($terminal['terminal_id']))
                            <span class="badge badge-green text-xs">{{ $terminal['terminal_id'] }}</span>
                            @if(!empty($terminal['status']))
                                <div class="text-xs text-gray-400 mt-0.5">{{ $terminal['status'] }}</div>
                            @endif
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td>
                        @if($visit->completed_at)
                            <span class="badge badge-green">✓ Completed</span>
                        @else
                            <span class="badge badge-yellow">⏳ Pending</span>
                        @endif
                    </td>
                    <td class="max-w-xs">
                        <div class="text-sm text-gray-700 leading-snug">
                            {{ \Illuminate\Support\Str::limit($visit->visit_summary, 100) }}
                        </div>
                        @if($visit->action_points)
                            <div class="text-xs text-gray-400 mt-1">
                                {{ \Illuminate\Support\Str::limit($visit->action_points, 80) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if(count($evidence))
                            <span class="badge badge-blue">📎 {{ count($evidence) }}</span>
                        @else
                            <span class="text-xs text-gray-400">None</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('visits.show', $visit) }}" class="btn-secondary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <div class="empty-state-msg">No visits found for the selected filters</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($visits->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 flex justify-center">
        {{ $visits->links() }}
    </div>
    @endif
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
