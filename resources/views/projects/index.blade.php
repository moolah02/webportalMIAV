@extends('layouts.app')
@section('title', 'Projects')

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Projects</h2>
        <p class="text-sm text-gray-500 mt-0.5">Manage all active and completed projects</p>
    </div>
    <a href="{{ route('projects.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors">
        ➕ New Project
    </a>
</div>

{{-- Filters --}}
<div class="bg-white border border-gray-200 rounded-xl p-5 mb-6 shadow-sm">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Client</label>
            <select name="client_id"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm min-w-[160px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Clients</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                    {{ $client->company_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</label>
            <select name="status"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm min-w-[140px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="paused"    {{ request('status') == 'paused'    ? 'selected' : '' }}>Paused</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Type</label>
            <select name="project_type"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm min-w-[140px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Types</option>
                <option value="discovery"    {{ request('project_type') == 'discovery'    ? 'selected' : '' }}>Discovery</option>
                <option value="servicing"    {{ request('project_type') == 'servicing'    ? 'selected' : '' }}>Servicing</option>
                <option value="support"      {{ request('project_type') == 'support'      ? 'selected' : '' }}>Support</option>
                <option value="maintenance"  {{ request('project_type') == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
                <option value="installation" {{ request('project_type') == 'installation' ? 'selected' : '' }}>Installation</option>
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Search</label>
            <input type="text" name="search" placeholder="Search projects…" value="{{ request('search') }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm min-w-[200px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <button type="submit"
                class="px-5 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-lg transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['client_id','status','project_type','search']))
        <a href="{{ route('projects.index') }}"
           class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Project</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Client</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Progress</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Timeline</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Manager</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($projects as $project)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 align-top">
                    <div class="font-semibold text-gray-900">{{ $project->project_name }}</div>
                    <code class="text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                        {{ $project->project_code }}
                    </code>
                </td>
                <td class="px-4 py-3 align-top text-gray-700">
                    {{ $project->client->company_name }}
                </td>
                <td class="px-4 py-3 align-top">
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-medium capitalize">
                        {{ $project->project_type }}
                    </span>
                </td>
                <td class="px-4 py-3 align-top">
                    @php
                        $statusClasses = [
                            'active'    => 'bg-green-100 text-green-800',
                            'completed' => 'bg-blue-100 text-blue-800',
                            'paused'    => 'bg-yellow-100 text-yellow-700',
                            'cancelled' => 'bg-red-100 text-red-600',
                        ];
                        $sc = $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize {{ $sc }}">
                        {{ $project->status }}
                    </span>
                </td>
                <td class="px-4 py-3 align-top text-gray-500">
                    @if($project->job_assignments_count > 0)
                        <div>{{ $project->terminals_count ?? 0 }} terminals</div>
                        <div>{{ number_format($project->completion_percentage ?? 0, 1) }}% complete</div>
                    @else
                        <span class="italic">No assignments</span>
                    @endif
                </td>
                <td class="px-4 py-3 align-top text-gray-700 text-xs leading-relaxed">
                    <div><span class="font-medium">Start:</span> {{ $project->start_date ? $project->start_date->format('M j, Y') : 'Not set' }}</div>
                    <div><span class="font-medium">End:</span> {{ $project->end_date ? $project->end_date->format('M j, Y') : 'Not set' }}</div>
                </td>
                <td class="px-4 py-3 align-top">
                    @if($project->projectManager)
                        <span class="font-medium text-gray-700">{{ $project->projectManager->full_name }}</span>
                    @else
                        <span class="text-gray-400 italic text-xs">Unassigned</span>
                    @endif
                </td>
                <td class="px-4 py-3 align-top">
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('projects.show', $project) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white text-xs font-medium rounded-md transition-colors no-underline">
                            👁 View
                        </a>
                        <a href="{{ route('projects.edit', $project) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 text-xs font-medium rounded-md transition-colors no-underline">
                            ✏️ Edit
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-16 text-center text-gray-400">
                    <div class="text-4xl mb-3">📋</div>
                    <div class="font-medium text-gray-500 mb-1">No projects found</div>
                    <div class="text-sm">No projects match your current filters.</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($projects->hasPages())
<div class="mt-5">
    {{ $projects->appends(request()->query())->links() }}
</div>
@endif

@endsection
