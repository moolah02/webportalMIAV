{{-- resources/views/projects/closure-reports.blade.php --}}
@extends('layouts.app')
@section('title', 'Closure Reports')

@push('styles')
<style>
.cr-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
@media (max-width: 900px) { .cr-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.cr-tabs { margin-bottom: 16px; border-radius: 10px 10px 0 0; }
.cr-tabs .tab-btn { background: transparent; border-top: 0; border-left: 0; border-right: 0; }
.cr-tabs .tab-btn .mv-i { color: currentColor; }
.tab-content { display: none; }
.tab-content.active { display: block; }
.cr-muted { color: var(--mv-muted); font-size: 12.5px; }
.cr-title { font-weight: 500; color: var(--mv-ink); font-size: 13.5px; }
.cr-code { font-family: var(--mv-mono); font-size: 11.5px; color: var(--mv-muted); }
.cr-dates { font-size: 12.5px; color: var(--mv-ink-2); line-height: 1.55; white-space: nowrap; font-variant-numeric: tabular-nums; }
.cr-dates span { color: var(--mv-muted); }
.cr-progress { display: flex; align-items: center; gap: 8px; min-width: 130px; }
.cr-bar { flex: 1; height: 6px; background: var(--mv-line); border-radius: 3px; overflow: hidden; }
.cr-bar > span { display: block; height: 100%; background: var(--mv-accent); border-radius: 3px; }
.cr-pct { font-size: 12px; font-weight: 600; color: var(--mv-ink-2); font-variant-numeric: tabular-nums; width: 42px; text-align: right; }
.cr-actions { display: flex; gap: 6px; justify-content: flex-end; }
.cr-num { text-align: right; font-variant-numeric: tabular-nums; }
.cr-body { padding: 16px 18px; }
.cr-select { width: 100%; max-width: 520px; }
.cr-loading { padding: 24px; text-align: center; color: var(--mv-muted); font-size: 13px; }
</style>
@endpush

@section('content')
@php
    $reportsCount = $closedProjects->where('report_path')->count();
    $avgProgress = number_format($activeProjects->avg('completion_percentage') ?? 0, 1);
@endphp

<div class="cr-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-activity"/></svg></div>
        <div>
            <div class="stat-number">{{ $activeProjects->count() }}</div>
            <div class="stat-label">Active Projects</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ $closedProjects->count() }}</div>
            <div class="stat-label">Closed Projects</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-file-check"/></svg></div>
        <div>
            <div class="stat-number">{{ $reportsCount }}</div>
            <div class="stat-label">Reports Generated</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-trending-up"/></svg></div>
        <div>
            <div class="stat-number">{{ $avgProgress }}%</div>
            <div class="stat-label">Avg. Progress</div>
        </div>
    </div>
</div>

<div class="ui-card overflow-hidden" style="margin-bottom:16px">
    <div class="tab-nav cr-tabs" role="tablist" style="margin:0">
        <button type="button" class="tab-btn active" data-tab="active-projects" onclick="showTab('active-projects', this)"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-activity"/></svg> Active Projects</button>
        <button type="button" class="tab-btn" data-tab="closed-projects" onclick="showTab('closed-projects', this)"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-circle"/></svg> Closed Projects</button>
        <button type="button" class="tab-btn" data-tab="analytics" onclick="showTab('analytics', this)"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-chart"/></svg> Analytics</button>
        <button type="button" class="tab-btn" data-tab="reports" onclick="showTab('reports', this)"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-file"/></svg> Reports</button>
        <button type="button" class="tab-btn" data-tab="manual-reports" onclick="showTab('manual-reports', this)"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-settings"/></svg> Generate Reports</button>
    </div>
</div>

{{-- Active Projects --}}
<div id="active-projects" class="tab-content active">
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header"><h3>Active Projects</h3><span class="cr-muted">{{ $activeProjects->count() }}</span></div>
        @if($activeProjects->count() > 0)
        <div class="overflow-x-auto">
            <table class="ui-table w-full">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Timeline</th>
                        <th>Manager</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeProjects as $project)
                    @php $pct = $project->completion_percentage ?? 0; @endphp
                    <tr>
                        <td>
                            <div class="cr-title">{{ $project->project_name }}</div>
                            <div class="cr-code">{{ $project->project_code }}</div>
                        </td>
                        <td>{{ $project->client->company_name }}</td>
                        <td><span class="badge {{ $project->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($project->status) }}</span></td>
                        <td>
                            @if($project->job_assignments_count > 0)
                            <div class="cr-progress">
                                <div class="cr-bar"><span style="width:{{ min(100, $pct) }}%"></span></div>
                                <span class="cr-pct">{{ number_format($pct, 1) }}%</span>
                            </div>
                            <div class="cr-muted" style="font-size:12px;margin-top:2px">{{ $project->terminals_count ?? 0 }} terminals</div>
                            @else
                            <span class="cr-muted">No assignments</span>
                            @endif
                        </td>
                        <td>
                            <div class="cr-dates">
                                <div><span>Start</span> {{ $project->start_date ? $project->start_date->format('M j, Y') : 'Not set' }}</div>
                                <div><span>End</span> {{ $project->end_date ? $project->end_date->format('M j, Y') : 'Not set' }}</div>
                            </div>
                        </td>
                        <td>
                            @if($project->projectManager)
                                {{ $project->projectManager->full_name }}
                            @else
                                <span class="cr-muted">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="cr-actions">
                                <a href="{{ route('projects.show', $project) }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg> View</a>
                                <a href="{{ route('projects.closure-wizard', $project) }}" class="btn-primary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-lock"/></svg> Close</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-folder"/></svg></div>
            <p class="empty-state-msg">No active projects. All projects are closed or there are no projects to show.</p>
        </div>
        @endif
    </div>
</div>

{{-- Closed Projects --}}
<div id="closed-projects" class="tab-content">
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header"><h3>Closed Projects</h3><span class="cr-muted">{{ $closedProjects->count() }}</span></div>
        @if($closedProjects->count() > 0)
        <div class="overflow-x-auto">
            <table class="ui-table w-full">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Closure Date</th>
                        <th style="text-align:right">Duration</th>
                        <th>Report Status</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($closedProjects as $project)
                    <tr>
                        <td>
                            <div class="cr-title">{{ $project->project_name }}</div>
                            <div class="cr-code">{{ $project->project_code }}</div>
                        </td>
                        <td>{{ $project->client->company_name }}</td>
                        <td>
                            <div class="cr-dates">
                                <div>{{ $project->closed_at ? $project->closed_at->format('M j, Y') : 'N/A' }}</div>
                                <div><span>{{ $project->closed_at?->diffForHumans() }}</span></div>
                            </div>
                        </td>
                        <td class="cr-num">
                            @if($project->start_date && $project->closed_at)
                                {{ (int) $project->start_date->diffInDays($project->closed_at) }} days
                            @else
                                <span class="cr-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($project->report_path)
                                <span class="badge badge-green">Generated</span>
                            @else
                                <span class="badge badge-gray">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="cr-actions">
                                <a href="{{ route('projects.show', $project) }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg> View</a>
                                @if($project->closure)
                                <button type="button" class="btn-secondary btn-sm" onclick="showClosureDetails('{{ $project->id }}')"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-info"/></svg> Details</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
            <p class="empty-state-msg">No closed projects. No projects have been closed yet.</p>
        </div>
        @endif
    </div>
</div>

{{-- Analytics --}}
<div id="analytics" class="tab-content">
    @php
        $avgDuration = $closedProjects->filter(function ($project) {
            return $project->start_date && $project->closed_at;
        })->map(function ($project) {
            return $project->start_date->diffInDays($project->closed_at);
        })->avg();
        $avgSatisfaction = $closedProjects->where('closure')->avg('closure.client_satisfaction');
    @endphp
    <div class="cr-stats">
        <div class="stat-card">
            <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-chart"/></svg></div>
            <div>
                <div class="stat-number">{{ $avgProgress }}%</div>
                <div class="stat-label">Avg. Progress</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-star"/></svg></div>
            <div>
                <div class="stat-number">{{ $avgSatisfaction ? number_format($avgSatisfaction, 1) : '0' }}</div>
                <div class="stat-label">Avg. Client Satisfaction</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg></div>
            <div>
                <div class="stat-number">{{ $avgDuration ? number_format($avgDuration, 0) : '0' }}</div>
                <div class="stat-label">Avg. Duration (days)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-folder"/></svg></div>
            <div>
                <div class="stat-number">{{ $closedProjects->count() }}</div>
                <div class="stat-label">Total Closed</div>
            </div>
        </div>
    </div>
</div>

{{-- Reports --}}
<div id="reports" class="tab-content">
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header"><h3>Generated Reports</h3><span class="cr-muted">{{ $reportsCount }}</span></div>
        @if($reportsCount > 0)
        <div class="overflow-x-auto">
            <table class="ui-table w-full">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Report Generated</th>
                        <th style="text-align:right">File Size</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($closedProjects->where('report_path') as $project)
                    <tr>
                        <td>
                            <div class="cr-title">{{ $project->project_name }}</div>
                            <div class="cr-code">{{ $project->project_code }}</div>
                        </td>
                        <td>{{ $project->client->company_name }}</td>
                        <td class="cr-dates">{{ $project->closed_at ? $project->closed_at->format('M j, Y') : 'N/A' }}</td>
                        <td class="cr-num">
                            @if($project->report_path && file_exists(storage_path('app/public/' . $project->report_path)))
                                {{ number_format(filesize(storage_path('app/public/' . $project->report_path)) / 1024, 1) }} KB
                            @else
                                <span class="cr-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="cr-actions">
                                @if($project->report_path)
                                <a href="{{ route('projects.download-report', $project) }}" class="btn-primary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Download</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg></div>
            <p class="empty-state-msg">No reports generated. Close projects to generate reports.</p>
        </div>
        @endif
    </div>
</div>

{{-- Generate Reports --}}
<div id="manual-reports" class="tab-content">
    @if($closedProjects->count() > 0)
    <div class="ui-card" style="margin-bottom:16px">
        <div class="ui-card-header"><h3>Select Project for Report Generation</h3></div>
        <div class="cr-body">
            <label class="ui-label" for="projectSelector">Closed project</label>
            <select id="projectSelector" class="ui-select cr-select">
                <option value="">Choose a closed project...</option>
                @foreach($closedProjects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_name }} - {{ $project->client->company_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div id="reportGeneratorContainer" style="display: none;"></div>
    @else
    <div class="ui-card">
        <div class="empty-state">
            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg></div>
            <p class="empty-state-msg">No closed projects. Close some projects first to generate reports.</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function showTab(tabName, btn) {
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    document.querySelectorAll('.cr-tabs .tab-btn').forEach(b => b.classList.remove('active'));

    const panel = document.getElementById(tabName);
    if (panel) panel.classList.add('active');

    const trigger = btn || (window.event && window.event.target && window.event.target.closest('.tab-btn'))
        || document.querySelector(`.cr-tabs .tab-btn[data-tab="${tabName}"]`);
    if (trigger) trigger.classList.add('active');
}

function showClosureDetails(projectId) {
    window.location.href = `/projects/${projectId}/closure-details`;
}

document.addEventListener('DOMContentLoaded', function() {
    const projectSelector = document.getElementById('projectSelector');
    if (projectSelector) {
        projectSelector.addEventListener('change', function() {
            const projectId = this.value;
            const container = document.getElementById('reportGeneratorContainer');

            if (projectId) {
                container.innerHTML = '<div class="ui-card"><div class="cr-loading">Loading report generator...</div></div>';
                container.style.display = 'block';

                fetch(`/projects/${projectId}/report-generator`)
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        container.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        container.innerHTML = '<div class="alert-danger" style="padding:11px 14px;border:1px solid">Error loading report generator.</div>';
                        container.style.display = 'block';
                    });
            } else {
                container.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
