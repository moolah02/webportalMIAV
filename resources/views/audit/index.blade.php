@extends('layouts.app')
@section('title', 'Audit Trail')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="m-0 text-gray-900 text-2xl font-semibold">&#x1F50D; Audit Trail</h1>
            <p class="text-gray-500 text-sm mt-1">Complete history of all system actions, categorised by area</p>
        </div>
    </div>

    {{-- Top stats --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">📋</div>
            <div>
                <div class="stat-number">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total Events Logged</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">📅</div>
            <div>
                <div class="stat-number">{{ number_format($stats['today']) }}</div>
                <div class="stat-label">Events Today</div>
            </div>
        </div>
    </div>

    {{-- Category breakdown --}}
    @php
    $catColors = [
        'Assets'            => ['border' => 'border-blue-400',   'text' => 'text-blue-700',   'bg' => 'bg-blue-50'],
        'Tickets'           => ['border' => 'border-orange-400', 'text' => 'text-orange-700', 'bg' => 'bg-orange-50'],
        'Employees'         => ['border' => 'border-purple-400', 'text' => 'text-purple-700', 'bg' => 'bg-purple-50'],
        'Job Assignments'   => ['border' => 'border-green-400',  'text' => 'text-green-700',  'bg' => 'bg-green-50'],
        'Site Visits'       => ['border' => 'border-teal-400',   'text' => 'text-teal-700',   'bg' => 'bg-teal-50'],
        'Clients'           => ['border' => 'border-yellow-400', 'text' => 'text-yellow-700', 'bg' => 'bg-yellow-50'],
        'Terminals'         => ['border' => 'border-gray-400',   'text' => 'text-gray-700',   'bg' => 'bg-gray-100'],
        'Business Licenses' => ['border' => 'border-red-400',    'text' => 'text-red-700',    'bg' => 'bg-red-50'],
        'Settings'          => ['border' => 'border-slate-400',  'text' => 'text-slate-700',  'bg' => 'bg-slate-100'],
        'System'            => ['border' => 'border-zinc-400',   'text' => 'text-zinc-700',   'bg' => 'bg-zinc-100'],
        'Other'             => ['border' => 'border-gray-300',   'text' => 'text-gray-600',   'bg' => 'bg-gray-50'],
    ];
    @endphp
    <div class="grid grid-cols-5 gap-3 mb-6">
        @foreach($stats['byCategory'] as $cat => $count)
            @if($count > 0)
            @php $cc = $catColors[$cat] ?? ['border' => 'border-gray-300', 'text' => 'text-gray-600', 'bg' => 'bg-gray-50']; @endphp
            <a href="?category={{ urlencode($cat) }}"
               class="bg-white rounded-xl border-l-4 {{ $cc['border'] }} border border-gray-200 p-3 no-underline hover:shadow-sm transition {{ request('category') === $cat ? 'ring-2 ring-offset-1 ring-blue-400' : '' }}">
                <div class="text-xl font-bold {{ $cc['text'] }}">{{ number_format($count) }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $cat }}</div>
            </a>
            @endif
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="filter-bar">
        <div class="filter-group">
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
    <div class="ui-card overflow-hidden">
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
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    @php
                    $cc = $catColors[$log->category] ?? ['border' => 'border-gray-300', 'text' => 'text-gray-600', 'bg' => 'bg-gray-50'];
                    $actionColors = [
                        'approved'       => 'bg-green-100 text-green-700',
                        'rejected'       => 'bg-red-100 text-red-700',
                        'created'        => 'bg-blue-100 text-blue-700',
                        'updated'        => 'bg-yellow-100 text-yellow-700',
                        'deleted'        => 'bg-red-200 text-red-800',
                        'status_changed' => 'bg-indigo-100 text-indigo-700',
                        'completed'      => 'bg-green-100 text-green-800',
                        'cancelled'      => 'bg-gray-200 text-gray-600',
                    ];
                    $actionColor = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs font-medium text-gray-800">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($log->employee)
                                <div class="text-xs font-medium text-gray-800">{{ $log->employee->first_name }} {{ $log->employee->last_name }}</div>
                                <div class="text-xs text-gray-400">{{ $log->employee->employee_number ?? '' }}</div>
                            @else
                                <span class="text-xs text-gray-400">System</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $cc['bg'] }} {{ $cc['text'] }}">
                                {{ $log->category }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $actionColor }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($log->model_type)
                                <div class="text-xs font-medium text-gray-600">{{ $log->model_type }}</div>
                                @if($log->model_id)
                                    <div class="text-xs text-gray-400">#{{ $log->model_id }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">&#x2014;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <span class="text-gray-700 text-xs">{{ $log->description }}</span>
                            @if($log->old_values || $log->new_values)
                                <button onclick="toggleChanges({{ $log->id }})"
                                        class="ml-2 text-xs text-blue-500 hover:text-blue-700 underline">
                                    View changes
                                </button>
                                <div id="changes-{{ $log->id }}" class="hidden mt-2">
                                    @if($log->old_values)
                                        <div class="bg-red-50 rounded p-2 mb-1 text-xs font-mono">
                                            <strong class="text-red-600">Before:</strong>
                                            @foreach($log->old_values as $k => $v)
                                                <div>{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($log->new_values)
                                        <div class="bg-green-50 rounded p-2 text-xs font-mono">
                                            <strong class="text-green-600">After:</strong>
                                            @foreach($log->new_values as $k => $v)
                                                <div>{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $log->ip_address ?? '&#x2014;' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <div class="text-4xl mb-3">&#x1F4CB;</div>
                            <div class="font-medium">No audit log entries found</div>
                            <div class="text-sm mt-1">Activity will appear here as users perform actions.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
            <div class="ui-card-footer justify-center">
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
