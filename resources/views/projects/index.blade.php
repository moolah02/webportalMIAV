@extends('layouts.app')
@section('title', 'Projects')

@section('header-actions')
<a href="{{ route('projects.create') }}" class="btn-primary">➕ New Project</a>
@endsection

@section('content')

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
                <tr>
                    <td>
                        <div class="font-semibold text-gray-900">{{ $project->project_name }}</div>
                        <code class="text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                            {{ $project->project_code }}
                        </code>
                    </td>
                    <td class="text-gray-700">{{ $project->client->company_name }}</td>
                    <td>
                        <span class="badge badge-gray capitalize">{{ $project->project_type }}</span>
                    </td>
                    <td>
                        @php
                            $sc = match($project->status) {
                                'active'    => 'badge-green',
                                'completed' => 'badge-blue',
                                'paused'    => 'badge-yellow',
                                'cancelled' => 'badge-red',
                                default     => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $sc }} capitalize">{{ $project->status }}</span>
                    </td>
                    <td class="text-gray-500 text-xs leading-relaxed">
                        @if($project->job_assignments_count > 0)
                            <div>{{ $project->terminals_count ?? 0 }} terminals</div>
                            <div>{{ number_format($project->completion_percentage ?? 0, 1) }}% complete</div>
                        @else
                            <span class="italic text-gray-400">No assignments</span>
                        @endif
                    </td>
                    <td class="text-xs leading-relaxed text-gray-700">
                        <div><span class="font-medium">Start:</span> {{ $project->start_date ? $project->start_date->format('M j, Y') : 'Not set' }}</div>
                        <div><span class="font-medium">End:</span> {{ $project->end_date ? $project->end_date->format('M j, Y') : 'Not set' }}</div>
                    </td>
                    <td>
                        @if($project->projectManager)
                            <span class="font-medium text-gray-700">{{ $project->projectManager->full_name }}</span>
                        @else
                            <span class="text-gray-400 italic text-xs">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('projects.show', $project) }}" class="btn-secondary btn-sm">👁 View</a>
                            <a href="{{ route('projects.edit', $project) }}" class="btn-secondary btn-sm">✏️ Edit</a>
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
