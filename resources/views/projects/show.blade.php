{{-- resources/views/projects/show.blade.php --}}
@extends('layouts.app')

@section('title', $project->project_name)

@section('header-actions')
<a href="{{ route('projects.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> All Projects</a>
<a href="{{ route('projects.edit', $project) }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit</a>
@if($project->status === 'active')
<a href="{{ route('projects.closure-wizard', $project) }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-lock"/></svg> Close Project</a>
@endif
@if($project->report_path)
<a href="{{ route('projects.download-report', $project) }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Download Report</a>
@endif
@endsection

@push('styles')
<style>
.ps-head { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; padding: 16px 18px; margin-bottom: 16px; }
.ps-head-main { flex: 1; min-width: 260px; }
.ps-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 6px; }
.ps-title { font-size: 17px; font-weight: 600; color: var(--mv-ink); margin: 0; line-height: 1.3; }
.ps-meta { display: flex; flex-wrap: wrap; gap: 6px 18px; margin-top: 6px; font-size: 12.5px; color: var(--mv-muted); }
.ps-meta strong { color: var(--mv-ink-2); font-weight: 500; }
.ps-code { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 0 6px; }
.ps-type { font-size: 12px; padding: 2px 8px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); text-transform: capitalize; }
.ps-pct { width: 180px; }
.ps-pct-v { font-size: 24px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; line-height: 1.1; }
.ps-pct-l { font-size: 12px; color: var(--mv-muted); margin: 2px 0 6px; }
.ps-bar { height: 6px; background: var(--mv-line); border-radius: 3px; overflow: hidden; }
.ps-bar > span { display: block; height: 100%; background: var(--mv-accent); border-radius: 3px; }
.ps-bar > span.is-done { background: var(--mv-good); }
.ps-bar.is-lg { height: 8px; }
.ps-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
@media (max-width: 900px) { .ps-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.ps-stack { display: flex; flex-direction: column; gap: 16px; margin-bottom: 16px; }
.ps-body { padding: 16px 18px; }
.ps-text { font-size: 13px; color: var(--mv-ink-2); margin: 0 0 12px; }
.ps-row-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.ps-sub { font-size: 11.5px; font-weight: 600; color: var(--mv-muted); letter-spacing: .05em; text-transform: uppercase; margin: 0 0 8px; }
.ps-chip { font-size: 12px; padding: 2px 8px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); text-transform: capitalize; font-variant-numeric: tabular-nums; }
.ps-two { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
@media (max-width: 760px) { .ps-two { grid-template-columns: 1fr; } }
.ps-check { display: flex; align-items: center; gap: 10px; padding: 9px 0; }
.ps-check + .ps-check { border-top: 1px solid var(--mv-line); }
.ps-check .mv-i { flex-shrink: 0; }
.ps-check.is-ok .mv-i { color: var(--mv-good); }
.ps-check.is-no .mv-i { color: var(--mv-crit); }
.ps-check-t { font-size: 13px; font-weight: 500; color: var(--mv-ink); }
.ps-check-s { font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.ps-callout { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; font-size: 13px; border: 1px solid; border-radius: 8px; line-height: 1.45; }
.ps-callout .mv-i { flex-shrink: 0; margin-top: 1px; }
.ps-status-form { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 12px 18px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); }
.ps-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 16px; align-items: start; }
@media (max-width: 1000px) { .ps-layout { grid-template-columns: 1fr; } }
.ps-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.ps-dl { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 20px; padding: 16px 18px; }
.ps-dl .v { font-size: 13.5px; color: var(--mv-ink); margin-top: 2px; font-variant-numeric: tabular-nums; }
.ps-notes { padding: 0 18px 16px; }
.ps-notes p { font-size: 13px; color: var(--mv-ink-2); margin: 0; }
.ps-rows { padding: 2px 18px; }
.ps-kv { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 9px 0; font-size: 13px; }
.ps-kv + .ps-kv { border-top: 1px solid var(--mv-line); }
.ps-kv > span:first-child { color: var(--mv-muted); }
.ps-kv > :last-child { color: var(--mv-ink); text-align: right; font-variant-numeric: tabular-nums; max-width: 60%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ps-activity { display: flex; gap: 10px; padding: 9px 0; }
.ps-activity + .ps-activity { border-top: 1px solid var(--mv-line); }
.ps-activity-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--mv-line-strong); margin-top: 6px; flex-shrink: 0; }
.ps-activity p { margin: 0; font-size: 13px; color: var(--mv-ink-2); }
.ps-activity small { font-size: 12px; color: var(--mv-muted); }
.ps-insight { padding: 12px 18px; }
.ps-insight + .ps-insight { border-top: 1px solid var(--mv-line); }
</style>
@endpush

@section('content')
@php
    $statusBadge = match($project->status) {
        'active'    => 'badge-green',
        'completed' => 'badge-blue',
        'paused'    => 'badge-yellow',
        'cancelled' => 'badge-red',
        default     => 'badge-gray',
    };
    $pct = $progressData['completion_percentage'] ?? 0;
    $priorityBadge = match($project->priority ?? 'normal') { 'high', 'emergency' => 'badge-red', 'low' => 'badge-gray', default => 'badge-blue' };
@endphp

<div class="ui-card ps-head">
    <div class="ps-head-main">
        <div class="ps-chips">
            <span class="badge {{ $statusBadge }}" style="text-transform:capitalize">{{ $project->status }}</span>
            <span class="ps-type">{{ $project->project_type }}</span>
            @if($project->priority && $project->priority !== 'normal')
            <span class="badge {{ $priorityBadge }}" style="text-transform:capitalize">{{ $project->priority }} priority</span>
            @endif
        </div>
        <h2 class="ps-title">{{ $project->project_name }}</h2>
        <div class="ps-meta">
            <span class="ps-code">{{ $project->project_code }}</span>
            @if($project->client)<span>Client: <strong>{{ $project->client->company_name }}</strong></span>@endif
            @if($project->projectManager)<span>PM: <strong>{{ $project->projectManager->full_name }}</strong></span>@endif
            <span>Created {{ $project->created_at->format('M j, Y') }}</span>
        </div>
    </div>
    <div class="ps-pct">
        <div class="ps-pct-v">{{ number_format($pct, 0) }}%</div>
        <div class="ps-pct-l">Overall Completion</div>
        <div class="ps-bar"><span class="{{ $pct >= 100 ? 'is-done' : '' }}" style="width:{{ min(100, max(0, $pct)) }}%"></span></div>
    </div>
</div>

<div class="ps-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-clipboard"/></svg></div>
        <div>
            <div class="stat-number">{{ $progressData['total_assignments'] ?? 0 }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ $progressData['completed_visits'] ?? 0 }}</div>
            <div class="stat-label">Completed Visits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-monitor"/></svg></div>
        <div>
            <div class="stat-number">{{ $progressData['total_terminals'] ?? 0 }}</div>
            <div class="stat-label">Total Terminals</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-chart"/></svg></div>
        <div>
            <div class="stat-number">{{ number_format($pct, 1) }}%</div>
            <div class="stat-label">Complete</div>
        </div>
    </div>
</div>

<div class="ps-stack">
    @if($project->status === 'active')
    <div class="ui-card">
        <div class="ui-card-header"><h3>Terminal Assignment</h3></div>
        <div class="ps-body">
            <p class="ps-text">
                @if(($progressData['total_terminals'] ?? 0) > 0)
                    This project has <strong>{{ $progressData['total_terminals'] }}</strong> terminals assigned.
                    You can modify assignments or add more terminals via the deployment page.
                @else
                    No terminals assigned yet. Use the deployment page to assign terminals and technicians.
                @endif
            </p>
            <div class="ps-row-actions">
                <a href="{{ route('deployment.index', ['project_id' => $project->id, 'client_id' => $project->client_id]) }}" class="btn-primary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-map"/></svg> {{ ($progressData['total_terminals'] ?? 0) > 0 ? 'Manage Terminal Assignments' : 'Assign Terminals to Project' }}
                </a>
                @if(($progressData['total_terminals'] ?? 0) > 0)
                <a href="{{ route('jobs.index', ['project_id' => $project->id]) }}" class="btn-secondary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-clipboard"/></svg> View Job Assignments
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="ui-card">
        <div class="ui-card-header">
            <h3>Project Progress</h3>
            <span class="badge {{ $statusBadge }}" style="text-transform:capitalize">{{ $project->status }}</span>
        </div>
        <div class="ps-body">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <span class="ps-check-s">Overall Completion</span>
                <strong style="font-variant-numeric:tabular-nums;color:var(--mv-ink)">{{ number_format($pct, 1) }}%</strong>
            </div>
            <div class="ps-bar is-lg" style="margin-bottom:16px"><span class="{{ $pct >= 100 ? 'is-done' : '' }}" style="width:{{ min(100, max(0, $pct)) }}%"></span></div>

            <div class="ps-two">
                @if(isset($progressData['assignments_by_status']) && $progressData['assignments_by_status']->count() > 0)
                <div>
                    <p class="ps-sub">Assignment Status</p>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        @foreach($progressData['assignments_by_status'] as $status => $count)
                        <span class="ps-chip">{{ str_replace('_', ' ', $status) }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if(isset($progressData['terminals_by_status']) && $progressData['terminals_by_status']->count() > 0)
                <div>
                    <p class="ps-sub">Terminal Status</p>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        @foreach($progressData['terminals_by_status'] as $status => $count)
                        <span class="ps-chip">{{ $status }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($project->status === 'active')
    @php
        $totalTerminals     = $progressData['total_terminals'] ?? 0;
        $completedVisits    = $progressData['completed_visits'] ?? 0;
        $completedCount     = $progressData['assignments_by_status']['completed'] ?? 0;
        $pendingAssignments = ($progressData['total_assignments'] ?? 0) - $completedCount;
        $canComplete        = $totalTerminals > 0 && $completedVisits >= $totalTerminals && $pendingAssignments == 0;
        $checks = [
            ['ok' => $totalTerminals > 0, 't' => 'Terminals Assigned', 's' => $totalTerminals . ' terminal' . ($totalTerminals !== 1 ? 's' : '')],
            ['ok' => $completedVisits >= $totalTerminals && $totalTerminals > 0, 't' => 'All Terminals Visited', 's' => $completedVisits . '/' . $totalTerminals . ' completed'],
            ['ok' => $pendingAssignments == 0, 't' => 'No Pending Assignments', 's' => $pendingAssignments . ' remaining'],
        ];
    @endphp
    <div class="ui-card">
        <div class="ui-card-header">
            <h3>Completion Readiness</h3>
            @if($canComplete)
            <span class="badge badge-green">Ready</span>
            @else
            <span class="badge badge-yellow">Not Ready</span>
            @endif
        </div>
        <div class="ps-body">
            <div class="ps-two" style="align-items:center">
                <div>
                    @foreach($checks as $c)
                    <div class="ps-check {{ $c['ok'] ? 'is-ok' : 'is-no' }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-{{ $c['ok'] ? 'check-circle' : 'x-circle' }}"/></svg>
                        <div>
                            <div class="ps-check-t">{{ $c['t'] }}</div>
                            <div class="ps-check-s">{{ $c['s'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div>
                    @if($canComplete)
                    <div class="alert-success ps-callout">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-circle"/></svg>
                        <div><strong>Ready for Completion!</strong><br>All requirements met. You can now complete this project.</div>
                    </div>
                    @else
                    <div class="alert-warning ps-callout">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-alert-triangle"/></svg>
                        <div><strong>Not Ready for Completion</strong><br>Complete all checklist items to close this project.</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('projects.update', $project) }}" class="ps-status-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="project_name" value="{{ $project->project_name }}">
            <input type="hidden" name="client_id" value="{{ $project->client_id }}">
            <input type="hidden" name="start_date" value="{{ $project->start_date }}">
            <input type="hidden" name="description" value="{{ $project->description }}">
            <label class="ui-label" for="quick_status" style="margin:0;white-space:nowrap">Update Status:</label>
            <select name="status" id="quick_status" class="ui-select" style="width:auto;min-width:160px">
                @foreach(['planning','active','on_hold','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ $project->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg> Save Status</button>
        </form>
    </div>
    @endif
</div>

<div class="ps-layout">
    <div class="ps-col">
        <div class="ui-card">
            <div class="ui-card-header"><h3>Project Information</h3></div>
            <div class="ps-dl">
                <div><div class="ui-label">Type</div><div class="v"><span class="ps-type">{{ $project->project_type }}</span></div></div>
                <div><div class="ui-label">Priority</div><div class="v"><span class="badge {{ $priorityBadge }}" style="text-transform:capitalize">{{ $project->priority ?? 'normal' }}</span></div></div>
                <div><div class="ui-label">Start Date</div><div class="v">{{ $project->start_date ? $project->start_date->format('M j, Y') : '—' }}</div></div>
                <div><div class="ui-label">End Date</div><div class="v">{{ $project->end_date ? $project->end_date->format('M j, Y') : '—' }}</div></div>
                @if($project->projectManager)
                <div><div class="ui-label">Manager</div><div class="v">{{ $project->projectManager->full_name }}</div></div>
                @endif
                @if($project->budget)
                <div><div class="ui-label">Budget</div><div class="v">${{ number_format($project->budget, 2) }}</div></div>
                @endif
                <div><div class="ui-label">Created</div><div class="v">{{ $project->created_at->format('M j, Y') }}</div></div>
                <div><div class="ui-label">Created By</div><div class="v">{{ $project->createdBy->full_name ?? 'Unknown' }}</div></div>
                @if($project->completed_at)
                <div><div class="ui-label">Completed At</div><div class="v">{{ $project->completed_at->format('M j, Y g:i A') }}</div></div>
                @endif
            </div>
            @if($project->notes)
            <div class="ps-notes">
                <div class="ui-label">Notes</div>
                <p>{{ $project->notes }}</p>
            </div>
            @endif
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Reports &amp; Documentation</h3></div>
            <div class="ps-body">
                @if($project->status === 'completed' && $project->report_path)
                <div class="alert-success ps-callout" style="align-items:center">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-file-check"/></svg>
                    <div style="flex:1"><strong>Completion Report Available</strong><br>Generated {{ $project->report_generated_at?->format('M j, Y g:i A') }}</div>
                    <a href="{{ route('projects.download-report', $project) }}" class="btn-primary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Download</a>
                </div>
                @elseif($project->status === 'completed')
                <div class="alert-warning ps-callout">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-alert-triangle"/></svg>
                    <span>Project completed but no report available. Contact administrator.</span>
                </div>
                @else
                <p class="ps-text" style="margin:0">Reports will be generated automatically when the project is completed.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="ps-col">
        <div class="ui-card">
            <div class="ui-card-header"><h3>Quick Reference</h3></div>
            <div class="ps-rows">
                <div class="ps-kv"><span>Code</span><span style="font-family:var(--mv-mono);font-size:12.5px">{{ $project->project_code }}</span></div>
                <div class="ps-kv"><span>Client</span><span>{{ $project->client->company_name }}</span></div>
                <div class="ps-kv"><span>Start</span><span>{{ $project->start_date ? $project->start_date->format('M j, Y') : '—' }}</span></div>
                <div class="ps-kv"><span>End</span><span>{{ $project->end_date ? $project->end_date->format('M j, Y') : '—' }}</span></div>
                @if($project->budget)
                <div class="ps-kv"><span>Budget</span><span>${{ number_format($project->budget, 0) }}</span></div>
                @endif
                <div class="ps-kv"><span>Created by</span><span>{{ $project->createdBy->full_name ?? '—' }}</span></div>
                @if($project->completed_at)
                <div class="ps-kv"><span>Closed</span><span style="color:var(--mv-good)">{{ $project->completed_at->format('M j, Y') }}</span></div>
                @endif
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Recent Activities</h3></div>
            @if(isset($recentActivities) && count($recentActivities) > 0)
            <div class="ps-rows">
                @foreach($recentActivities as $activity)
                <div class="ps-activity">
                    <span class="ps-activity-dot"></span>
                    <div>
                        <p>{{ $activity['message'] }}</p>
                        <small>{{ $activity['date']->diffForHumans() }}</small>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state" style="padding:28px 16px">
                <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-inbox"/></svg></div>
                <div class="empty-state-msg">No recent activities.</div>
            </div>
            @endif
        </div>
    </div>
</div>

@if(isset($previousProjects) && $previousProjects->count() > 0)
<div class="ui-card" style="margin-top:16px">
    <div class="ui-card-header"><h3>Insights from Previous Projects</h3></div>
    @foreach($previousProjects as $insight)
    <div class="ps-insight">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
            <div style="min-width:0">
                <div style="font-weight:500;color:var(--mv-ink)">{{ $insight['project']->project_name }}</div>
                <div class="ps-check-s" style="margin-top:2px">
                    <span class="ps-code">{{ $insight['project']->project_code }}</span>
                    · Completed {{ $insight['project']->end_date ? $insight['project']->end_date->diffForHumans() : 'recently' }}
                </div>
            </div>
            <span class="badge badge-blue" style="flex-shrink:0">{{ number_format($insight['completion_data']['completion_percentage'], 0) }}%</span>
        </div>
        @if($insight['project']->notes)
        <p class="ps-text" style="margin:6px 0 0">{{ \Illuminate\Support\Str::limit($insight['project']->notes, 200) }}</p>
        @endif
    </div>
    @endforeach
</div>
@endif

@endsection
