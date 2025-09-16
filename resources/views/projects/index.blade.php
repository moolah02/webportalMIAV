@extends('layouts.app')

@section('content')
<style>
    .projects-container {
        padding: 2rem;
        max-width: 100%;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .page-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .btn-new {
        background: #3b82f6;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s;
    }

    .btn-new:hover {
        background: #2563eb;
        color: white;
        text-decoration: none;
    }

    .filters {
        background: #f9fafb;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .filter-group label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .filter-group select,
    .filter-group input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        min-width: 150px;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .btn-filter {
        background: #374151;
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        height: fit-content;
    }

    .btn-filter:hover {
        background: #1f2937;
    }

    .projects-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .projects-table th {
        background: #f3f4f6;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .projects-table td {
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }

    .projects-table tbody tr:hover {
        background: #f9fafb;
    }

    .project-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .project-code {
        font-size: 0.75rem;
        color: #6b7280;
        font-family: monospace;
        background: #f3f4f6;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .status-active { background: #dcfce7; color: #166534; }
    .status-completed { background: #dbeafe; color: #1e40af; }
    .status-paused { background: #fef3c7; color: #d97706; }
    .status-cancelled { background: #fee2e2; color: #dc2626; }

    .type-badge {
        padding: 0.25rem 0.5rem;
        background: #e5e7eb;
        color: #374151;
        border-radius: 4px;
        font-size: 0.75rem;
        text-transform: capitalize;
    }

    .progress-info {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .date-info {
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .date-label {
        font-weight: 500;
        color: #374151;
    }

    .manager-name {
        font-weight: 500;
        color: #374151;
    }

    .unassigned {
        color: #9ca3af;
        font-style: italic;
    }

    .btn-view, .btn-edit {
        padding: 0.375rem 0.75rem;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-right: 0.5rem;
    }

    .btn-view {
        background: #374151;
        color: white;
    }

    .btn-view:hover {
        background: #1f2937;
        color: white;
        text-decoration: none;
    }

    .btn-edit {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-edit:hover {
        background: #e5e7eb;
        color: #374151;
        text-decoration: none;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #d1d5db;
    }

    @media (max-width: 768px) {
        .projects-container {
            padding: 1rem;
        }

        .filters {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
        }

        .projects-table {
            font-size: 0.875rem;
        }
    }
</style>

<div class="projects-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Projects</h1>
    </div>

    <!-- Filters -->
    <div class="filters">
        <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end; width: 100%;">
            <div class="filter-group">
                <label>Client</label>
                <select name="client_id">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->company_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Type</label>
                <select name="project_type">
                    <option value="">All Types</option>
                    <option value="discovery" {{ request('project_type') == 'discovery' ? 'selected' : '' }}>Discovery</option>
                    <option value="servicing" {{ request('project_type') == 'servicing' ? 'selected' : '' }}>Servicing</option>
                    <option value="support" {{ request('project_type') == 'support' ? 'selected' : '' }}>Support</option>
                    <option value="maintenance" {{ request('project_type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="installation" {{ request('project_type') == 'installation' ? 'selected' : '' }}>Installation</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="search" placeholder="Search projects..." value="{{ request('search') }}">
            </div>

            <button type="submit" class="btn-filter">Filter</button>
        </form>
    </div>

    <!-- Projects Table -->
    <table class="projects-table">
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
                    <div class="project-name">{{ $project->project_name }}</div>
                    <span class="project-code">{{ $project->project_code }}</span>
                </td>
                <td>{{ $project->client->company_name }}</td>
                <td>
                    <span class="type-badge">{{ $project->project_type }}</span>
                </td>
                <td>
                    <span class="status-badge status-{{ $project->status }}">
                        {{ $project->status }}
                    </span>
                </td>
                <td>
                    @if($project->job_assignments_count > 0)
                    <div class="progress-info">
                        {{ $project->terminals_count ?? 0 }} terminals<br>
                        {{ number_format($project->completion_percentage ?? 0, 1) }}% complete
                    </div>
                    @else
                    <span class="progress-info">No assignments</span>
                    @endif
                </td>
                <td>
                    <div class="date-info">
                        <div><span class="date-label">Start:</span> {{ $project->start_date ? $project->start_date->format('M j, Y') : 'Not set' }}</div>
                        <div><span class="date-label">End:</span> {{ $project->end_date ? $project->end_date->format('M j, Y') : 'Not set' }}</div>
                    </div>
                </td>
                <td>
                    @if($project->projectManager)
                    <span class="manager-name">{{ $project->projectManager->full_name }}</span>
                    @else
                    <span class="unassigned">Unassigned</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('projects.show', $project) }}" class="btn-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('projects.edit', $project) }}" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="empty-state">
                    <i class="fas fa-project-diagram"></i>
                    <h3>No projects found</h3>
                    <p>No projects match your current filters.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($projects->hasPages())
    <div style="margin-top: 2rem;">
        {{ $projects->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
