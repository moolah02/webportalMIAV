@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="m-0 text-gray-900 text-2xl font-semibold">🔍 Audit Trail</h1>
            <p class="text-gray-500 text-sm mt-1">Complete history of system actions and changes</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Events</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['today']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Today</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['approvals']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Approvals Logged</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-red-500">{{ number_format($stats['rejections']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Rejections Logged</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..."
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Action</label>
                <select name="action" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Entity Type</label>
                <select name="model_type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($modelTypes as $type)
                        <option value="{{ $type }}" {{ request('model_type') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-[#1a3a5c] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#152e4a] transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search','action','model_type','date_from','date_to']))
                <a href="{{ route('audit-trail.index') }}" class="text-gray-500 text-sm px-3 py-2 hover:text-gray-700">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">When</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Who</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Entity</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-medium text-gray-800">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($log->employee)
                                <div class="font-medium text-gray-800">{{ $log->employee->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $log->employee->employee_number }}</div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $actionColors = [
                                    'approved'  => 'bg-green-100 text-green-700',
                                    'rejected'  => 'bg-red-100 text-red-700',
                                    'created'   => 'bg-blue-100 text-blue-700',
                                    'updated'   => 'bg-yellow-100 text-yellow-700',
                                    'deleted'   => 'bg-red-100 text-red-700',
                                    'assigned'  => 'bg-purple-100 text-purple-700',
                                ];
                                $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colorClass }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($log->model_type)
                                <div class="text-xs font-medium text-gray-600">{{ $log->model_type }}</div>
                                @if($log->model_id)
                                    <div class="text-xs text-gray-400">#{{ $log->model_id }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 max-w-sm">
                            <span class="text-gray-700">{{ $log->description }}</span>
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
                                                <div>{{ $k }}: {{ $v }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($log->new_values)
                                        <div class="bg-green-50 rounded p-2 text-xs font-mono">
                                            <strong class="text-green-600">After:</strong>
                                            @foreach($log->new_values as $k => $v)
                                                <div>{{ $k }}: {{ $v }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <div class="text-4xl mb-3">📋</div>
                            <div class="font-medium">No audit log entries found</div>
                            <div class="text-sm mt-1">Activity will be logged here as users perform actions.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
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
