@extends('layouts.app')
@section('title', 'Projects')

@section('header-actions')
<a href="{{ route('projects.create') }}" class="btn-primary">➕ New Project</a>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card">
        <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-xl flex-shrink-0">📋</div>
        <div>
            <div class="stat-number">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Projects</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-xl flex-shrink-0">🟢</div>
        <div>
            <div class="stat-number text-green-600">{{ $stats['active'] }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center text-xl flex-shrink-0">✅</div>
        <div>
            <div class="stat-number text-blue-600">{{ $stats['completed'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="w-11 h-11 rounded-xl bg-yellow-100 flex items-center justify-center text-xl flex-shrink-0">⏸️</div>
        <div>
            <div class="stat-number text-yellow-600">{{ $stats['paused'] }}</div>
            <div class="stat-label">Paused / On Hold</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div class="flex flex-col gap-1">
        <label class="ui-label">Client</label>
        <select name="client_id" class="ui-select" style="min-width:160px">
            <option value="">All Clients</option>
            @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                {{ $client->company_name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-col gap-1">
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select" style="min-width:140px">
            <option value="">All Status</option>
            <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="paused"    {{ request('status') == 'paused'    ? 'selected' : '' }}>Paused</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    <div class="flex flex-col gap-1">
        <label class="ui-label">Type</label>
        <select name="project_type" class="ui-select" style="min-width:140px">
            <option value="">All Types</option>
            <option value="discovery"    {{ request('project_type') == 'discovery'    ? 'selected' : '' }}>Discovery</option>
            <option value="servicing"    {{ request('project_type') == 'servicing'    ? 'selected' : '' }}>Servicing</option>
            <option value="support"      {{ request('project_type') == 'support'      ? 'selected' : '' }}>Support</option>
            <option value="maintenance"  {{ request('project_type') == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
            <option value="installation" {{ request('project_type') == 'installation' ? 'selected' : '' }}>Installation</option>
        </select>
    </div>

    <div class="flex flex-col gap-1">
        <label class="ui-label">Search</label>
        <input type="text" name="search" placeholder="Search projects…"
               value="{{ request('search') }}" class="ui-input" style="min-width:200px">
    </div>

    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['client_id','status','project_type','search']))
        <a href="{{ route('projects.index') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

{{-- Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">All Projects</span>
        <span class="text-xs text-gray-400 ml-auto">{{ $projects->total() }} {{ Str::plural('project', $projects->total()) }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Timeline</th>
                    <th>Manager</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                @php
                    $pct = $project->completion_percentage ?? 0;
                    $barColor = $pct >= 100 ? 'bg-blue-500' : ($pct >= 60 ? 'bg-green-500' : ($pct >= 30 ? 'bg-yellow-400' : 'bg-gray-300'));
                    $sc = match($project->status) {
                        'active'    => 'badge-green',
                        'completed' => 'badge-blue',
                        'paused'    => 'badge-yellow',
                        'cancelled' => 'badge-red',
                        default     => 'badge-gray',
                    };
                    $typeIcon = match($project->project_type) {
                        'maintenance'  => '🔧',
                        'installation' => '📦',
                        'support'      => '💬',
                        'discovery'    => '🔍',
                        'servicing'    => '⚙️',
                        default        => '📝',
                    };
                @endphp
                <tr>
                    <td style="max-width:220px">
                        <a href="{{ route('projects.show', $project) }}"
                           class="font-semibold text-[#1a3a5c] hover:underline leading-snug block">
                            {{ $project->project_name }}
                        </a>
                        <code class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                            {{ $project->project_code }}
                        </code>
                    </td>
                    <td class="text-sm text-gray-700">{{ $project->client->company_name }}</td>
                    <td>
                        <span class="badge badge-gray capitalize">{{ $typeIcon }} {{ $project->project_type }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $sc }} capitalize">{{ $project->status }}</span>
                    </td>
                    <td style="min-width:130px">
                        @if($project->job_assignments_count > 0)
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $barColor }} h-1.5 rounded-full"
                                         style="width:{{ min(100, $pct) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-600 w-10 text-right">{{ number_format($pct, 0) }}%</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $project->terminals_count ?? 0 }} terminals</div>
                        @else
                            <span class="text-xs italic text-gray-400">No assignments</span>
                        @endif
                    </td>
                    <td class="text-xs text-gray-600" style="min-width:120px">
                        <div>{{ $project->start_date ? $project->start_date->format('M j, Y') : '—' }}</div>
                        @if($project->end_date)
                        <div class="text-gray-400">→ {{ $project->end_date->format('M j, Y') }}</div>
                        @endif
                    </td>
                    <td class="text-sm">
                        @if($project->projectManager)
                            <span class="font-medium text-gray-700">{{ $project->projectManager->full_name }}</span>
                        @else
                            <span class="text-gray-400 italic text-xs">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('projects.show', $project) }}" class="btn-secondary btn-sm">View</a>
                            <a href="{{ route('projects.edit', $project) }}" class="btn-secondary btn-sm">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <div class="empty-state-msg">No projects match your current filters.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($projects->hasPages())
<div class="mt-5">
    {{ $projects->appends(request()->query())->links() }}
</div>
@endif

@endsection
