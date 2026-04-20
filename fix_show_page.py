"""Rewrite projects/show.blade.php to use design system"""
import os

BASE = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'

content = r"""{{-- resources/views/projects/show.blade.php --}}
@extends('layouts.app')

@section('title', $project->project_name)

@section('header-actions')
<a href="{{ route('projects.index') }}" class="btn-secondary">← All Projects</a>
<a href="{{ route('projects.edit', $project) }}" class="btn-secondary">✏️ Edit</a>
@if($project->status === 'active')
<a href="{{ route('projects.closure-wizard', $project) }}" class="btn-secondary">🔒 Close Project</a>
@endif
@if($project->report_path)
<a href="{{ route('projects.download-report', $project) }}" class="btn-primary">⬇️ Download Report</a>
@endif
@endsection

@section('content')

{{-- Statistics --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card border-l-4 border-blue-500">
        <div>
            <div class="stat-number">{{ $progressData['total_assignments'] ?? 0 }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-green-500">
        <div>
            <div class="stat-number text-green-600">{{ $progressData['completed_visits'] ?? 0 }}</div>
            <div class="stat-label">Completed Visits</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-yellow-400">
        <div>
            <div class="stat-number text-yellow-600">{{ $progressData['total_terminals'] ?? 0 }}</div>
            <div class="stat-label">Total Terminals</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-purple-500">
        <div>
            <div class="stat-number text-purple-600">{{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%</div>
            <div class="stat-label">Complete</div>
        </div>
    </div>
</div>

{{-- Terminal Assignment (active projects only) --}}
@if($project->status === 'active')
<div class="ui-card mb-5 border-l-4 border-blue-400">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">🖥️ Terminal Assignment</span>
    </div>
    <div class="ui-card-body">
        <p class="text-gray-600 text-sm mb-4">
            @if(($progressData['total_terminals'] ?? 0) > 0)
                This project has <strong>{{ $progressData['total_terminals'] }}</strong> terminals assigned.
                You can modify assignments or add more terminals via the deployment page.
            @else
                No terminals assigned yet. Use the deployment page to assign terminals and technicians.
            @endif
        </p>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('deployment.index', ['project_id' => $project->id, 'client_id' => $project->client_id]) }}"
               class="btn-primary">
                🗺️ {{ ($progressData['total_terminals'] ?? 0) > 0 ? 'Manage Terminal Assignments' : 'Assign Terminals to Project' }}
            </a>
            @if(($progressData['total_terminals'] ?? 0) > 0)
            <a href="{{ route('jobs.index', ['project_id' => $project->id]) }}" class="btn-secondary">
                📋 View Job Assignments
            </a>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Progress --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Project Progress</span>
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
    </div>
    <div class="ui-card-body">
        {{-- Progress bar --}}
        <div class="w-full bg-gray-200 rounded-full h-4 mb-1 overflow-hidden">
            <div class="bg-green-500 h-4 rounded-full flex items-center justify-center text-xs text-white font-semibold"
                 style="width: {{ max(5, $progressData['completion_percentage'] ?? 0) }}%; min-width: 2rem">
                {{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%
            </div>
        </div>
        <p class="text-xs text-gray-500 mb-4">{{ number_format($progressData['completion_percentage'] ?? 0, 1) }}% overall completion</p>

        {{-- Assignment Status --}}
        @if(isset($progressData['assignments_by_status']) && $progressData['assignments_by_status']->count() > 0)
        <div class="mb-3">
            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Assignment Status:</span>
            <div class="flex gap-2 mt-1 flex-wrap">
                @foreach($progressData['assignments_by_status'] as $status => $count)
                <span class="badge badge-gray capitalize">{{ str_replace('_',' ', $status) }}: {{ $count }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Terminal Status --}}
        @if(isset($progressData['terminals_by_status']) && $progressData['terminals_by_status']->count() > 0)
        <div>
            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Terminal Status:</span>
            <div class="flex gap-2 mt-1 flex-wrap">
                @foreach($progressData['terminals_by_status'] as $status => $count)
                <span class="badge badge-blue capitalize">{{ $status }}: {{ $count }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Quick Status Update + Readiness Check --}}
@if($project->status === 'active')
@php
    $totalTerminals     = $progressData['total_terminals'] ?? 0;
    $completedVisits    = $progressData['completed_visits'] ?? 0;
    $completedCount     = $progressData['assignments_by_status']['completed'] ?? 0;
    $pendingAssignments = ($progressData['total_assignments'] ?? 0) - $completedCount;
    $canComplete        = $totalTerminals > 0 && $completedVisits >= $totalTerminals && $pendingAssignments == 0;
@endphp
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Completion Readiness</span>
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-sm">
                    <span class="{{ $totalTerminals > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $totalTerminals > 0 ? '✅' : '❌' }}
                    </span>
                    <span class="text-gray-700">Terminals Assigned: <strong>{{ $totalTerminals }}</strong></span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="{{ $completedVisits >= $totalTerminals && $totalTerminals > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $completedVisits >= $totalTerminals && $totalTerminals > 0 ? '✅' : '❌' }}
                    </span>
                    <span class="text-gray-700">All Terminals Visited: <strong>{{ $completedVisits }}/{{ $totalTerminals }}</strong></span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="{{ $pendingAssignments == 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $pendingAssignments == 0 ? '✅' : '❌' }}
                    </span>
                    <span class="text-gray-700">Pending Assignments: <strong>{{ $pendingAssignments }}</strong></span>
                </div>
            </div>
            <div>
                @if($canComplete)
                <div class="flash-success">
                    <span>✅</span>
                    <div><strong>Ready for Completion!</strong><br>All requirements met. You can now complete this project.</div>
                </div>
                @else
                <div class="flash-warning">
                    <span>⚠️</span>
                    <div><strong>Not Ready for Completion</strong><br>Complete all requirements to finish this project.</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Status Update --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <form method="POST" action="{{ route('projects.update', $project) }}" class="flex items-center gap-3 flex-wrap">
                @csrf
                @method('PUT')
                <input type="hidden" name="project_name" value="{{ $project->project_name }}">
                <input type="hidden" name="client_id" value="{{ $project->client_id }}">
                <input type="hidden" name="start_date" value="{{ $project->start_date }}">
                <input type="hidden" name="description" value="{{ $project->description }}">
                <label class="ui-label mb-0 whitespace-nowrap">Update Status:</label>
                <select name="status" id="quick_status" class="ui-select" style="width:auto;min-width:160px">
                    @foreach(['planning','active','on_hold','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ $project->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary btn-sm">💾 Save Status</button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Two column layout --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Project Info + Reports --}}
    <div class="lg:col-span-2 space-y-5">

        <div class="ui-card">
            <div class="ui-card-header">
                <span class="font-semibold text-gray-800">Project Information</span>
            </div>
            <div class="ui-card-body">
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</dt>
                        <dd class="mt-1"><span class="badge badge-gray capitalize">{{ $project->project_type }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Priority</dt>
                        <dd class="mt-1">
                            @php $pc = match($project->priority ?? 'normal') { 'high','emergency' => 'badge-red', 'low' => 'badge-gray', default => 'badge-blue' }; @endphp
                            <span class="badge {{ $pc }} capitalize">{{ $project->priority ?? 'normal' }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Start Date</dt>
                        <dd class="mt-1 text-gray-700">{{ $project->start_date ? $project->start_date->format('M j, Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">End Date</dt>
                        <dd class="mt-1 text-gray-700">{{ $project->end_date ? $project->end_date->format('M j, Y') : '—' }}</dd>
                    </div>
                    @if($project->projectManager)
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Manager</dt>
                        <dd class="mt-1 text-gray-700 font-medium">{{ $project->projectManager->full_name }}</dd>
                    </div>
                    @endif
                    @if($project->budget)
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Budget</dt>
                        <dd class="mt-1 text-gray-700">${{ number_format($project->budget, 2) }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Created</dt>
                        <dd class="mt-1 text-gray-700">{{ $project->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Created By</dt>
                        <dd class="mt-1 text-gray-700">{{ $project->createdBy->full_name ?? 'Unknown' }}</dd>
                    </div>
                    @if($project->completed_at)
                    <div class="col-span-2">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Completed At</dt>
                        <dd class="mt-1 text-gray-700">{{ $project->completed_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @endif
                </dl>

                @if($project->notes)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Notes</dt>
                    <p class="text-sm text-gray-600">{{ $project->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Reports --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="font-semibold text-gray-800">Reports &amp; Documentation</span>
            </div>
            <div class="ui-card-body">
                @if($project->status === 'completed' && $project->report_path)
                <div class="flash-success">
                    <span>📄</span>
                    <div>
                        <strong>Completion Report Available</strong><br>
                        Generated {{ $project->report_generated_at?->format('M j, Y g:i A') }}
                        <a href="{{ route('projects.download-report', $project) }}" class="btn-primary btn-sm ml-2">⬇️ Download</a>
                    </div>
                </div>
                @elseif($project->status === 'completed')
                <div class="flash-warning">
                    <span>⚠️</span>
                    <span>Project completed but no report available. Contact administrator.</span>
                </div>
                @else
                <p class="text-sm text-gray-500">Reports will be generated automatically when the project is completed.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- Right: Recent Activities --}}
    <div class="space-y-5">
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="font-semibold text-gray-800">Recent Activities</span>
            </div>
            <div class="ui-card-body">
                @if(isset($recentActivities) && count($recentActivities) > 0)
                <div class="space-y-3">
                    @foreach($recentActivities as $activity)
                    <div class="border-l-2 border-gray-200 pl-3">
                        <p class="text-sm text-gray-700 mb-0">{{ $activity['message'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $activity['date']->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">📬</div>
                    <div class="empty-state-msg">No recent activities.</div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Previous Project Insights --}}
@if(isset($previousProjects) && $previousProjects->count() > 0)
<div class="ui-card mt-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Insights from Previous Projects</span>
    </div>
    <div class="ui-card-body space-y-4">
        @foreach($previousProjects as $insight)
        <div class="border border-gray-100 rounded-lg p-4">
            <div class="flex justify-between items-start">
                <div>
                    <div class="font-semibold text-gray-900">{{ $insight['project']->project_name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">
                        {{ $insight['project']->project_code }} •
                        Completed {{ $insight['project']->end_date ? $insight['project']->end_date->diffForHumans() : 'recently' }}
                    </div>
                </div>
                <span class="badge badge-blue">{{ number_format($insight['completion_data']['completion_percentage'], 1) }}% Complete</span>
            </div>
            @if($insight['project']->notes)
            <p class="text-sm text-gray-600 mt-2">{{ \Illuminate\Support\Str::limit($insight['project']->notes, 200) }}</p>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
"""

path = os.path.join(BASE, 'projects', 'show.blade.php')
with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print('WRITTEN: projects/show.blade.php')
