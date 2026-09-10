@extends('layouts.app')
@section('title', 'Projects')

@section('header-actions')
<a href="{{ route('projects.create') }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> New Project</a>
@endsection

@push('styles')
<style>
.pj-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
@media (max-width: 900px) { .pj-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.pj-muted { color: var(--mv-muted); font-size: 12.5px; }
.pj-name { font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; font-size: 13.5px; line-height: 1.35; display: block; }
.pj-name:hover { text-decoration: underline; }
.pj-code { font-family: var(--mv-mono); font-size: 11.5px; color: var(--mv-muted); margin-top: 2px; display: inline-block; }
.pj-type { font-size: 12px; padding: 2px 8px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); text-transform: capitalize; white-space: nowrap; }
.pj-progress { display: flex; align-items: center; gap: 8px; }
.pj-bar { flex: 1; height: 6px; background: var(--mv-line); border-radius: 3px; overflow: hidden; min-width: 70px; }
.pj-bar > span { display: block; height: 100%; background: var(--mv-accent); border-radius: 3px; }
.pj-bar > span.is-done { background: var(--mv-good); }
.pj-pct { font-size: 12px; font-weight: 600; color: var(--mv-ink-2); width: 36px; text-align: right; font-variant-numeric: tabular-nums; }
.pj-dates { font-size: 12.5px; color: var(--mv-ink-2); white-space: nowrap; font-variant-numeric: tabular-nums; line-height: 1.5; }
.pj-dates .pj-muted { font-size: 12px; }
</style>
@endpush

@section('content')

<div class="pj-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-folder"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Projects</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-activity"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['active'] }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['completed'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-pause"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['paused'] }}</div>
            <div class="stat-label">Paused / On Hold</div>
        </div>
    </div>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group" style="flex:1;min-width:200px">
        <label class="ui-label">Search</label>
        <input type="text" name="search" placeholder="Search projects…" value="{{ request('search') }}" class="ui-input" style="width:100%">
    </div>
    <div class="filter-group">
        <label class="ui-label">Client</label>
        <select name="client_id" class="ui-select" style="min-width:160px">
            <option value="">All Clients</option>
            @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select">
            <option value="">All Status</option>
            <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="paused"    {{ request('status') == 'paused'    ? 'selected' : '' }}>Paused</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <div class="filter-group">
        <label class="ui-label">Type</label>
        <select name="project_type" class="ui-select">
            <option value="">All Types</option>
            <option value="discovery"    {{ request('project_type') == 'discovery'    ? 'selected' : '' }}>Discovery</option>
            <option value="servicing"    {{ request('project_type') == 'servicing'    ? 'selected' : '' }}>Servicing</option>
            <option value="support"      {{ request('project_type') == 'support'      ? 'selected' : '' }}>Support</option>
            <option value="maintenance"  {{ request('project_type') == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
            <option value="installation" {{ request('project_type') == 'installation' ? 'selected' : '' }}>Installation</option>
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['client_id','status','project_type','search']))
        <a href="{{ route('projects.index') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <h3>All Projects</h3>
        <span class="pj-muted">{{ $projects->total() }} {{ Str::plural('project', $projects->total()) }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th style="min-width:150px">Progress</th>
                    <th>Timeline</th>
                    <th>Manager</th>
                    <th style="width:90px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                @php
                    $pct = $project->completion_percentage ?? 0;
                    $sc = match($project->status) {
                        'active'    => 'badge-green',
                        'completed' => 'badge-blue',
                        'paused'    => 'badge-yellow',
                        'cancelled' => 'badge-red',
                        default     => 'badge-gray',
                    };
                @endphp
                <tr>
                    <td style="max-width:260px">
                        <a href="{{ route('projects.show', $project) }}" class="pj-name">{{ $project->project_name }}</a>
                        <span class="pj-code">{{ $project->project_code }}</span>
                    </td>
                    <td>{{ $project->client->company_name }}</td>
                    <td><span class="pj-type">{{ $project->project_type }}</span></td>
                    <td><span class="badge {{ $sc }}" style="text-transform:capitalize">{{ $project->status }}</span></td>
                    <td>
                        @if($project->job_assignments_count > 0)
                            <div class="pj-progress">
                                <div class="pj-bar"><span class="{{ $pct >= 100 ? 'is-done' : '' }}" style="width:{{ min(100, $pct) }}%"></span></div>
                                <span class="pj-pct">{{ number_format($pct, 0) }}%</span>
                            </div>
                            <div class="pj-muted" style="font-size:12px;margin-top:2px">{{ $project->terminals_count ?? 0 }} terminals</div>
                        @else
                            <span class="pj-muted">No assignments</span>
                        @endif
                    </td>
                    <td>
                        <div class="pj-dates">
                            <div>{{ $project->start_date ? $project->start_date->format('M j, Y') : '—' }}</div>
                            @if($project->end_date)
                            <div class="pj-muted">to {{ $project->end_date->format('M j, Y') }}</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($project->projectManager)
                            {{ $project->projectManager->full_name }}
                        @else
                            <span class="pj-muted">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('projects.show', $project) }}" class="action-btn" title="View"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a>
                            <a href="{{ route('projects.edit', $project) }}" class="action-btn" title="Edit"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-folder"/></svg></div>
                            <div class="empty-state-msg">No projects match your current filters.</div>
                            @if(request()->hasAny(['client_id','status','project_type','search']))
                            <a href="{{ route('projects.index') }}" class="btn-secondary" style="margin-top:12px">Clear</a>
                            @endif
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
