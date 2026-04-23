{{-- resources/views/jobs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Job Assignments')

@section('content')
{{-- Actions --}}
<div class="flex justify-end items-center mb-6">
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card">
        <div class="stat-icon stat-icon-gray">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <div class="stat-number">{{ $assignments->total() ?? 0 }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-number">{{ $assignments->where('status', 'assigned')->count() }}</div>
            <div class="stat-label">Assigned</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-number">{{ $assignments->where('status', 'completed')->count() }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <div>
            <div class="stat-number">{{ $assignments->where('status', 'in_progress')->count() }}</div>
            <div class="stat-label">In Progress</div>
        </div>
    </div>
</div>

{{-- Nav Toggle --}}
<div class="ui-card mb-4">
    <div class="ui-card-body flex gap-2">
        <a href="{{ route('jobs.mine') }}"
           class="{{ ($scope ?? 'all') === 'mine' ? 'btn-primary' : 'btn-secondary' }}">
            My Assignments
        </a>
        <a href="{{ route('jobs.index') }}"
           class="{{ ($scope ?? 'all') === 'all' ? 'btn-primary' : 'btn-secondary' }}">
            All Assignments
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar flex-wrap">
    <div>
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select w-36">
            <option value="">All Status</option>
            @foreach (['assigned'=>'Assigned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $val => $lbl)
                <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ui-label">From Date</label>
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="ui-input w-36">
    </div>
    <div>
        <label class="ui-label">To Date</label>
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="ui-input w-36">
    </div>
    @if(($scope ?? 'all') === 'all')
    <div>
        <label class="ui-label">Priority</label>
        <select name="priority" class="ui-select w-32">
            <option value="">All Priority</option>
            @foreach (['low'=>'Low','normal'=>'Normal','high'=>'High','emergency'=>'Emergency'] as $pval => $plbl)
                <option value="{{ $pval }}" {{ ($filters['priority'] ?? '') === $pval ? 'selected' : '' }}>{{ $plbl }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ui-label">Technician</label>
        <select name="technician_id" class="ui-select w-40">
            <option value="">All Technicians</option>
            @foreach ($technicians as $t)
                <option value="{{ $t->id }}" {{ ($filters['technician_id'] ?? '') == $t->id ? 'selected' : '' }}>{{ $t->first_name }} {{ $t->last_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ui-label">Client</label>
        <select name="client_id" class="ui-select w-40">
            <option value="">All Clients</option>
            @foreach ($clients as $c)
                <option value="{{ $c->id }}" {{ ($filters['client_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div>
        <label class="ui-label">Search</label>
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="ui-input w-48" placeholder="Assignment ID or notes...">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Apply</button>
        <a href="{{ (($scope ?? 'all') === 'all') ? route('jobs.index') : route('jobs.mine') }}" class="btn-secondary">Reset</a>
    </div>
</form>

{{-- Table Card --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">Assignments</span>
        <span class="badge badge-gray">{{ $assignments->count() }}</span>
    </div>

    @if($assignments->count() > 0)
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Details</th>
                    @if(($scope ?? 'all') === 'all') <th>Technician</th> @endif
                    <th>Scheduled</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($assignments as $a)
                <tr>
                    <td>
                        <div class="text-sm font-semibold text-gray-900">{{ $a->assignment_id }}</div>
                        <div class="text-xs text-gray-400">Created {{ $a->created_at?->diffForHumans() }}</div>
                    </td>
                    <td>
                        <div class="text-sm font-medium text-gray-900">
                            {{ $a->list_title ?? implode(' • ', array_filter([
                                $a->project->project_name ?? null,
                                $a->client->company_name ?? null,
                                $a->region->name ?? null,
                            ])) ?: '—' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            Service: {{ \Illuminate\Support\Str::headline($a->service_type) }}
                            • Terminals: {{ is_array($a->pos_terminals) ? count($a->pos_terminals) : ($a->terminal_count ?? 0) }}
                            @if(!empty($a->terminal_merchant_preview))
                                • e.g. {{ $a->terminal_merchant_preview }}@if(($a->terminal_count ?? 0) > 3) + more @endif
                            @endif
                        </div>
                    </td>
                    @if(($scope ?? 'all') === 'all')
                    <td>
                        @if($a->technician)
                            <div class="text-sm font-medium text-gray-900">{{ $a->technician->first_name }} {{ $a->technician->last_name }}</div>
                            <div class="text-xs text-gray-500">Technician</div>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    @endif
                    <td>
                        <div class="text-sm font-medium text-gray-900">{{ optional($a->scheduled_date)->format('M j, Y') ?? '—' }}</div>
                        <div class="text-xs text-gray-400">{{ optional($a->scheduled_date)?->diffForHumans() }}</div>
                    </td>
                    <td>
                        @php
                            $pc = ['emergency'=>'badge badge-red','high'=>'badge badge-orange','normal'=>'badge badge-blue','low'=>'badge badge-gray'];
                        @endphp
                        <span class="{{ $pc[$a->priority] ?? 'badge badge-gray' }}">{{ \Illuminate\Support\Str::headline($a->priority) }}</span>
                    </td>
                    <td>
                        @php
                            $stc = ['completed'=>'badge badge-green','in_progress'=>'badge badge-blue','assigned'=>'badge badge-gray','cancelled'=>'badge badge-red'];
                        @endphp
                        <span class="{{ $stc[$a->status] ?? 'badge badge-gray' }}">{{ \Illuminate\Support\Str::headline($a->status) }}</span>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('jobs.show', $a->id) }}" class="btn-secondary btn-sm">View</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if($assignments->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $assignments->links() }}
    </div>
    @endif
    @else
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <p class="empty-state-msg">No assignments found. Try adjusting your filters.</p>
    </div>
    @endif
</div>
@endsection