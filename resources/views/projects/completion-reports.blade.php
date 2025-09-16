{{-- resources/views/projects/completion-reports.blade.php --}}
@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    {{-- Page Header --}}
    <h2 class="page-title mb-4">Project Completion & Reports</h2>
    <p class="page-subtitle">Manage project completions and generate reports</p>

    {{-- Statistics Cards --}}
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $activeProjects->count() }}</div>
                    <div class="stat-label">Active Projects</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $completedProjects->count() }}</div>
                    <div class="stat-label">Completed Projects</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-pending">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $completedProjects->where('report_path')->count() }}</div>
                    <div class="stat-label">Reports Generated</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-progress">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($activeProjects->avg('completion_percentage') ?? 0, 1) }}%</div>
                    <div class="stat-label">Avg. Progress</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">Project Management</h6>
        </div>
        <div class="card-body">
            <div class="nav-buttons">
                <button class="btn btn-primary active" onclick="showTab('active-projects')">
                    <i class="fas fa-play-circle"></i> Active Projects
                </button>
                <button class="btn btn-secondary" onclick="showTab('completed-projects')">
                    <i class="fas fa-check-circle"></i> Completed Projects
                </button>
                <button class="btn btn-secondary" onclick="showTab('analytics')">
                    <i class="fas fa-chart-bar"></i> Analytics
                </button>
                <button class="btn btn-secondary" onclick="showTab('reports')">
                    <i class="fas fa-file-pdf"></i> Reports
                </button>
                <button class="btn btn-secondary" onclick="showTab('manual-reports')">
                    <i class="fas fa-cogs"></i> Generate Reports
                </button>
            </div>
        </div>
    </div>

    {{-- Active Projects Tab --}}
    <div id="active-projects" class="tab-content active">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Active Projects</h6>
                <span class="badge">{{ $activeProjects->count() }}</span>
            </div>

            @if($activeProjects->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Timeline</th>
                                <th>Manager</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeProjects as $project)
                            <tr>
                                <td>
                                    <div class="item-title">{{ $project->project_name }}</div>
                                    <div class="item-subtitle">{{ $project->project_code }}</div>
                                </td>
                                <td>{{ $project->client->company_name }}</td>
                                <td>
                                    <span class="badge badge-status-{{ $project->status }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($project->job_assignments_count > 0)
                                    <div class="progress-info">
                                        {{ $project->terminals_count ?? 0 }} terminals<br>
                                        <small class="text-muted">{{ number_format($project->completion_percentage ?? 0, 1) }}% complete</small>
                                    </div>
                                    @else
                                    <span class="text-muted">No assignments</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="timeline-info">
                                        <div><strong>Start:</strong> {{ $project->start_date ? $project->start_date->format('M j, Y') : 'Not set' }}</div>
                                        <div><strong>End:</strong> {{ $project->end_date ? $project->end_date->format('M j, Y') : 'Not set' }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($project->projectManager)
                                        {{ $project->projectManager->full_name }}
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="action-buttons">
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('projects.completion-wizard', $project) }}" class="btn btn-primary">
                                            <i class="fas fa-flag-checkered"></i> Complete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <h5>No active projects</h5>
                    <p>All projects are completed or there are no projects to show.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Completed Projects Tab --}}
    <div id="completed-projects" class="tab-content">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Completed Projects</h6>
                <span class="badge">{{ $completedProjects->count() }}</span>
            </div>

            @if($completedProjects->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Completion Date</th>
                                <th>Duration</th>
                                <th>Quality Rating</th>
                                <th>Report Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedProjects as $project)
                            <tr>
                                <td>
                                    <div class="item-title">{{ $project->project_name }}</div>
                                    <div class="item-subtitle">{{ $project->project_code }}</div>
                                </td>
                                <td>{{ $project->client->company_name }}</td>
                                <td>
                                    <div class="completion-date">
                                        {{ $project->completed_at ? $project->completed_at->format('M j, Y') : 'N/A' }}
                                    </div>
                                    <div class="completion-relative">{{ $project->completed_at?->diffForHumans() }}</div>
                                </td>
                                <td>
                                    @if($project->start_date && $project->completed_at)
                                        {{ $project->start_date->diffInDays($project->completed_at) }} days
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($project->completion)
                                    <div class="rating-display">
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $project->completion->quality_score ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted">{{ $project->completion->quality_score }}/5</small>
                                    </div>
                                    @else
                                    <span class="text-muted">Not rated</span>
                                    @endif
                                </td>
                                <td>
                                    @if($project->report_path)
                                        <span class="badge badge-status-completed">Generated</span>
                                    @else
                                        <span class="badge badge-status-assigned">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="action-buttons">
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($project->completion)
                                        <button class="btn btn-secondary" onclick="showCompletionDetails('{{ $project->id }}')">
                                            <i class="fas fa-info-circle"></i> Details
                                        </button>
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
                    <h5>No completed projects</h5>
                    <p>No projects have been completed yet.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Analytics Tab --}}
    <div id="analytics" class="tab-content">
        <div class="stats-grid mb-4">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon stat-icon-progress">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ number_format($activeProjects->avg('completion_percentage') ?? 0, 1) }}%</div>
                        <div class="stat-label">Avg. Progress</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon stat-icon-success">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $completedProjects->where('completion')->avg('completion.quality_score') ? number_format($completedProjects->where('completion')->avg('completion.quality_score'), 1) : '0' }}</div>
                        <div class="stat-label">Avg. Quality Score</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon stat-icon-pending">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $completedProjects->where('completion')->avg('completion.client_satisfaction') ? number_format($completedProjects->where('completion')->avg('completion.client_satisfaction'), 1) : '0' }}</div>
                        <div class="stat-label">Avg. Client Satisfaction</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            @php
                            $avgDuration = $completedProjects->filter(function($project) {
                                return $project->start_date && $project->completed_at;
                            })->map(function($project) {
                                return $project->start_date->diffInDays($project->completed_at);
                            })->avg();
                            @endphp
                            {{ $avgDuration ? number_format($avgDuration, 0) : '0' }}
                        </div>
                        <div class="stat-label">Avg. Duration (days)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reports Tab --}}
    <div id="reports" class="tab-content">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Generated Reports</h6>
                <span class="badge">{{ $completedProjects->where('report_path')->count() }}</span>
            </div>

            @if($completedProjects->where('report_path')->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Report Generated</th>
                                <th>File Size</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedProjects->where('report_path') as $project)
                            <tr>
                                <td>
                                    <div class="item-title">{{ $project->project_name }}</div>
                                    <div class="item-subtitle">{{ $project->project_code }}</div>
                                </td>
                                <td>{{ $project->client->company_name }}</td>
                                <td>{{ $project->completed_at ? $project->completed_at->format('M j, Y') : 'N/A' }}</td>
                                <td>
                                    @if($project->report_path && file_exists(storage_path('app/public/' . $project->report_path)))
                                        {{ number_format(filesize(storage_path('app/public/' . $project->report_path)) / 1024, 1) }} KB
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($project->report_path)
                                    <a href="{{ route('projects.download-report', $project) }}" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <h5>No reports generated</h5>
                    <p>Complete projects to generate reports.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Manual Reports Tab --}}
    <div id="manual-reports" class="tab-content">
        @if($completedProjects->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title">Select Project for Report Generation</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <select id="projectSelector" class="form-control">
                            <option value="">Choose a completed project...</option>
                            @foreach($completedProjects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }} - {{ $project->client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div id="reportGeneratorContainer" style="display: none;">
                {{-- This will be populated via AJAX when project is selected --}}
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <h5>No completed projects</h5>
                        <p>Complete some projects first to generate reports.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
/* Base Styling */
.page-title {
    color: #374151;
    font-weight: 600;
    margin: 0;
}

.page-subtitle {
    color: #6b7280;
    margin-bottom: 2rem;
    font-size: 1rem;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.stat-card-body {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    background: #f9fafb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-icon-success { background: #f0fdf4; color: #16a34a; }
.stat-icon-pending { background: #fef3c7; color: #d97706; }
.stat-icon-progress { background: #dbeafe; color: #2563eb; }

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Card Styling */
.card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
}

.card-header {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    color: #374151;
    font-weight: 600;
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

/* Navigation Buttons */
.nav-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Tab Content */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Button Styling */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 6px;
    border: 1px solid transparent;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
    gap: 0.5rem;
}

.btn-primary {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
}

.btn-primary:hover {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    text-decoration: none;
}

.btn-primary.active {
    background: #2563eb;
    border-color: #2563eb;
}

.btn-secondary {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #374151;
}

.btn-secondary:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
    color: #374151;
    text-decoration: none;
}

.btn-outline {
    background: transparent;
    border-color: #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
    text-decoration: none;
}

/* Table Styling */
.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.data-table th {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

.data-table tbody tr:hover {
    background: #f9fafb;
}

/* Table Content */
.item-title {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.item-subtitle {
    color: #6b7280;
    font-size: 0.8125rem;
    font-family: monospace;
    background: #f3f4f6;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    display: inline-block;
}

.completion-date {
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.25rem;
}

.completion-relative {
    color: #6b7280;
    font-size: 0.8125rem;
}

.progress-info, .timeline-info {
    font-size: 0.875rem;
    line-height: 1.4;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.rating-stars {
    display: flex;
    gap: 0.125rem;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.badge-status-active {
    background: #dcfce7;
    color: #166534;
}

.badge-status-completed {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-status-assigned {
    background: #f3f4f6;
    color: #374151;
}

/* Form Elements */
.form-group {
    margin-bottom: 1rem;
}

.form-control {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    color: #111827;
    background: #ffffff;
    width: 100%;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Empty State */
.empty-state {
    padding: 3rem;
    text-align: center;
}

.empty-state h5 {
    color: #374151;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6b7280;
    margin: 0;
}

/* Utility Classes */
.text-end {
    text-align: right;
}

.text-muted {
    color: #6b7280;
}

.text-warning {
    color: #f59e0b;
}

.table-responsive {
    overflow-x: auto;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .nav-buttons {
        flex-direction: column;
    }

    .action-buttons {
        flex-direction: column;
    }

    .card-body {
        padding: 1rem;
    }

    .data-table {
        font-size: 0.8125rem;
    }

    .data-table th,
    .data-table td {
        padding: 0.5rem;
    }

    .stat-card-body {
        padding: 1rem;
    }

    .stat-number {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // Remove active class from all buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.classList.remove('active');
        if (button.classList.contains('btn-primary')) {
            button.classList.remove('btn-primary');
            button.classList.add('btn-secondary');
        }
    });

    // Show selected tab content
    document.getElementById(tabName).classList.add('active');

    // Add active class to clicked button
    event.target.classList.remove('btn-secondary');
    event.target.classList.add('btn-primary', 'active');
}

function showCompletionDetails(projectId) {
    window.location.href = `/projects/${projectId}/completion-details`;
}

// Project selector for manual reports
document.addEventListener('DOMContentLoaded', function() {
    const projectSelector = document.getElementById('projectSelector');
    if (projectSelector) {
        projectSelector.addEventListener('change', function() {
            const projectId = this.value;
            const container = document.getElementById('reportGeneratorContainer');

            if (projectId) {
                // Show loading state
                container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading report generator...</div>';
                container.style.display = 'block';

                // Load the report generator for selected project
                fetch(`/projects/${projectId}/report-generator`)
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        container.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        container.innerHTML = '<div class="alert alert-danger">Error loading report generator.</div>';
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
