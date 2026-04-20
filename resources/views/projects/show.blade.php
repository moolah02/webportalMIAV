{{-- resources/views/projects/show.blade.php --}}
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

@php
    $statusColor = match($project->status) {
        'active'    => 'bg-green-500',
        'completed' => 'bg-blue-500',
        'paused'    => 'bg-yellow-400',
        'cancelled' => 'bg-red-500',
        default     => 'bg-gray-400',
    };
    $statusBadge = match($project->status) {
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

{{-- Project Hero Banner --}}
<div class="ui-card mb-5 overflow-hidden">
    <div class="h-1.5 w-full {{ $statusColor }}"></div>
    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-[#1a3a5c]/10 flex items-center justify-center text-2xl flex-shrink-0">
            {{ $typeIcon }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="badge {{ $statusBadge }} capitalize">{{ $project->status }}</span>
                <span class="badge badge-gray capitalize">{{ $project->project_type }}</span>
                @if($project->priority && $project->priority !== 'normal')
                @php $pc = match($project->priority) { 'high','emergency' => 'badge-red', 'low' => 'badge-gray', default => 'badge-blue' }; @endphp
                <span class="badge {{ $pc }} capitalize">{{ $project->priority }} priority</span>
                @endif
            </div>
            <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $project->project_name }}</h2>
            <div class="flex flex-wrap gap-4 mt-1.5 text-xs text-gray-500">
                <span><code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $project->project_code }}</code></span>
                @if($project->client)<span>Client: <strong class="text-gray-700">{{ $project->client->company_name }}</strong></span>@endif
                @if($project->projectManager)<span>PM: <strong class="text-gray-700">{{ $project->projectManager->full_name }}</strong></span>@endif
                <span>Created {{ $project->created_at->format('M j, Y') }}</span>
            </div>
        </div>
        @php
            $pct    = $progressData['completion_percentage'] ?? 0;
            $barCol = $pct >= 100 ? 'bg-blue-500' : ($pct >= 60 ? 'bg-green-500' : ($pct >= 30 ? 'bg-yellow-400' : 'bg-gray-200'));
        @endphp
        <div class="sm:text-right sm:min-w-[110px]">
            <div class="text-3xl font-bold text-[#1a3a5c]">{{ number_format($pct, 0) }}%</div>
            <div class="text-xs text-gray-400 mb-1.5">Overall Completion</div>
            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                <div class="{{ $barCol }} h-2 rounded-full transition-all" style="width:{{ min(100,$pct) }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center text-xl flex-shrink-0">📋</div>
        <div>
            <div class="stat-number">{{ $progressData['total_assignments'] ?? 0 }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-xl flex-shrink-0">✅</div>
        <div>
            <div class="stat-number text-green-600">{{ $progressData['completed_visits'] ?? 0 }}</div>
            <div class="stat-label">Completed Visits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="w-11 h-11 rounded-xl bg-yellow-100 flex items-center justify-center text-xl flex-shrink-0">🖥️</div>
        <div>
            <div class="stat-number text-yellow-600">{{ $progressData['total_terminals'] ?? 0 }}</div>
            <div class="stat-label">Total Terminals</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="w-11 h-11 rounded-xl bg-purple-100 flex items-center justify-center text-xl flex-shrink-0">📊</div>
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
        <span class="badge {{ $statusBadge }} capitalize ml-auto">{{ $project->status }}</span>
    </div>
    <div class="ui-card-body">
        {{-- Progress bar --}}
        <div class="mb-4">
            <div class="flex justify-between items-center mb-1.5">
                <span class="text-xs text-gray-500 font-medium">Overall Completion</span>
                <span class="text-sm font-bold text-[#1a3a5c]">{{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="{{ $barCol }} h-3 rounded-full transition-all"
                     style="width: {{ min(100, max(0, $progressData['completion_percentage'] ?? 0)) }}%"></div>
            </div>
        </div>

        {{-- Assignment & Terminal Status --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if(isset($progressData['assignments_by_status']) && $progressData['assignments_by_status']->count() > 0)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Assignment Status</p>
                <div class="flex gap-2 flex-wrap">
                    @foreach($progressData['assignments_by_status'] as $status => $count)
                    <span class="badge badge-gray capitalize">{{ str_replace('_',' ', $status) }}: {{ $count }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($progressData['terminals_by_status']) && $progressData['terminals_by_status']->count() > 0)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Terminal Status</p>
                <div class="flex gap-2 flex-wrap">
                    @foreach($progressData['terminals_by_status'] as $status => $count)
                    <span class="badge badge-blue capitalize">{{ $status }}: {{ $count }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
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
        @if($canComplete)
        <span class="badge badge-green ml-auto">Ready</span>
        @else
        <span class="badge badge-yellow ml-auto">Not Ready</span>
        @endif
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-2.5 rounded-lg {{ $totalTerminals > 0 ? 'bg-green-50' : 'bg-red-50' }}">
                    <span class="text-lg">{{ $totalTerminals > 0 ? '✅' : '❌' }}</span>
                    <div>
                        <div class="text-sm font-medium text-gray-700">Terminals Assigned</div>
                        <div class="text-xs text-gray-500">{{ $totalTerminals }} terminal{{ $totalTerminals !== 1 ? 's' : '' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-2.5 rounded-lg {{ $completedVisits >= $totalTerminals && $totalTerminals > 0 ? 'bg-green-50' : 'bg-red-50' }}">
                    <span class="text-lg">{{ $completedVisits >= $totalTerminals && $totalTerminals > 0 ? '✅' : '❌' }}</span>
                    <div>
                        <div class="text-sm font-medium text-gray-700">All Terminals Visited</div>
                        <div class="text-xs text-gray-500">{{ $completedVisits }}/{{ $totalTerminals }} completed</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-2.5 rounded-lg {{ $pendingAssignments == 0 ? 'bg-green-50' : 'bg-red-50' }}">
                    <span class="text-lg">{{ $pendingAssignments == 0 ? '✅' : '❌' }}</span>
                    <div>
                        <div class="text-sm font-medium text-gray-700">No Pending Assignments</div>
                        <div class="text-xs text-gray-500">{{ $pendingAssignments }} remaining</div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col justify-center">
                @if($canComplete)
                <div class="flash-success">
                    <span>✅</span>
                    <div><strong>Ready for Completion!</strong><br>All requirements met. You can now complete this project.</div>
                </div>
                @else
                <div class="flash-warning">
                    <span>⚠️</span>
                    <div><strong>Not Ready for Completion</strong><br>Complete all checklist items to close this project.</div>
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
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Type</dt>
                        <dd><span class="badge badge-gray capitalize">{{ $typeIcon }} {{ $project->project_type }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Priority</dt>
                        <dd>
                            @php $pc = match($project->priority ?? 'normal') { 'high','emergency' => 'badge-red', 'low' => 'badge-gray', default => 'badge-blue' }; @endphp
                            <span class="badge {{ $pc }} capitalize">{{ $project->priority ?? 'normal' }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Start Date</dt>
                        <dd class="text-gray-700 font-medium">{{ $project->start_date ? $project->start_date->format('M j, Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">End Date</dt>
                        <dd class="text-gray-700 font-medium">{{ $project->end_date ? $project->end_date->format('M j, Y') : '—' }}</dd>
                    </div>
                    @if($project->projectManager)
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Manager</dt>
                        <dd class="text-gray-700 font-medium">{{ $project->projectManager->full_name }}</dd>
                    </div>
                    @endif
                    @if($project->budget)
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Budget</dt>
                        <dd class="text-gray-700 font-medium">${{ number_format($project->budget, 2) }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Created</dt>
                        <dd class="text-gray-700">{{ $project->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Created By</dt>
                        <dd class="text-gray-700">{{ $project->createdBy->full_name ?? 'Unknown' }}</dd>
                    </div>
                    @if($project->completed_at)
                    <div class="col-span-2">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Completed At</dt>
                        <dd class="text-gray-700">{{ $project->completed_at->format('M j, Y g:i A') }}</dd>
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

    {{-- Right: Recent Activities + Quick Info --}}
    <div class="space-y-5">

        {{-- Quick Info Panel --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="font-semibold text-gray-800">Quick Reference</span>
            </div>
            <div class="ui-card-body p-0">
                <div class="divide-y divide-gray-100 text-sm">
                    <div class="flex justify-between items-center px-4 py-3">
                        <span class="text-gray-500">Code</span>
                        <code class="bg-gray-100 px-2 py-0.5 rounded text-gray-700 text-xs">{{ $project->project_code }}</code>
                    </div>
                    <div class="flex justify-between items-center px-4 py-3">
                        <span class="text-gray-500">Client</span>
                        <span class="font-medium text-gray-700 text-right max-w-[140px] truncate">{{ $project->client->company_name }}</span>
                    </div>
                    <div class="flex justify-between items-center px-4 py-3">
                        <span class="text-gray-500">Start</span>
                        <span class="text-gray-700">{{ $project->start_date ? $project->start_date->format('M j, Y') : '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center px-4 py-3">
                        <span class="text-gray-500">End</span>
                        <span class="text-gray-700">{{ $project->end_date ? $project->end_date->format('M j, Y') : '—' }}</span>
                    </div>
                    @if($project->budget)
                    <div class="flex justify-between items-center px-4 py-3">
                        <span class="text-gray-500">Budget</span>
                        <span class="font-semibold text-[#1a3a5c]">${{ number_format($project->budget, 0) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center px-4 py-3">
                        <span class="text-gray-500">Created by</span>
                        <span class="text-gray-700">{{ $project->createdBy->full_name ?? '—' }}</span>
                    </div>
                    @if($project->completed_at)
                    <div class="flex justify-between items-center px-4 py-3">
                        <span class="text-gray-500">Closed</span>
                        <span class="text-green-600 font-medium">{{ $project->completed_at->format('M j, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header">
                <span class="font-semibold text-gray-800">Recent Activities</span>
            </div>
            <div class="ui-card-body">
                @if(isset($recentActivities) && count($recentActivities) > 0)
                <div class="space-y-3">
                    @foreach($recentActivities as $activity)
                    <div class="flex gap-3 items-start">
                        <div class="w-2 h-2 rounded-full bg-[#1a3a5c]/30 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm text-gray-700 leading-snug">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $activity['date']->diffForHumans() }}</p>
                        </div>
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
        <div class="border border-gray-100 rounded-lg p-4 hover:border-gray-200 transition-colors">
            <div class="flex justify-between items-start gap-3">
                <div class="min-w-0">
                    <div class="font-semibold text-gray-900 truncate">{{ $insight['project']->project_name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">
                        <code class="bg-gray-100 px-1 rounded">{{ $insight['project']->project_code }}</code>
                        · Completed {{ $insight['project']->end_date ? $insight['project']->end_date->diffForHumans() : 'recently' }}
                    </div>
                </div>
                <span class="badge badge-blue flex-shrink-0">{{ number_format($insight['completion_data']['completion_percentage'], 0) }}%</span>
            </div>
            @if($insight['project']->notes)
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ \Illuminate\Support\Str::limit($insight['project']->notes, 200) }}</p>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
