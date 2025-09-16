{{-- resources/views/projects/show.blade.php --}}
@extends('layouts.app')

@section('title', $project->project_name)

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <h2 class="page-title mb-4">{{ $project->project_name }}</h2>
    <p class="page-subtitle">{{ $project->project_code }} • {{ $project->client->company_name }}</p>

    {{-- Statistics Cards --}}
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $progressData['total_assignments'] ?? 0 }}</div>
                    <div class="stat-label">Total Assignments</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $progressData['completed_visits'] ?? 0 }}</div>
                    <div class="stat-label">Completed Visits</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-pending">
                    <i class="fas fa-desktop"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $progressData['total_terminals'] ?? 0 }}</div>
                    <div class="stat-label">Total Terminals</div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-progress">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%</div>
                    <div class="stat-label">Complete</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Project Actions --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">Project Actions</h6>
            <span class="badge badge-status-{{ $project->status }}">{{ ucfirst($project->status) }}</span>
        </div>
        <div class="card-body">
            <div class="nav-buttons">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline">
                    <i class="fas fa-edit"></i> Edit Project
                </a>

                @if ($project->status === 'active')
                    <a href="{{ route('deployment.index') }}?project_id={{ $project->id }}" class="btn btn-primary">
                        <i class="fas fa-tasks"></i> Assign Terminals
                    </a>
                    <a href="{{ route('projects.completion-wizard', $project) }}" class="btn btn-secondary">
                        <i class="fas fa-magic"></i> Complete Project
                    </a>
                @endif

                @if ($project->report_path)
                    <a href="{{ route('projects.download-report', $project) }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download Report
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Project Progress --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">Project Progress & Statistics</h6>
        </div>
        <div class="card-body">
            {{-- Progress Bar --}}
            <div class="progress mb-3" style="height: 20px;">
                <div class="progress-bar bg-success" style="width: {{ $progressData['completion_percentage'] ?? 0 }}%">
                    {{ number_format($progressData['completion_percentage'] ?? 0, 1) }}% Complete
                </div>
            </div>

            {{-- Assignment Status Breakdown --}}
            @if(isset($progressData['assignments_by_status']) && $progressData['assignments_by_status']->count() > 0)
                <div class="status-badges mb-3">
                    <strong>Assignment Status:</strong>
                    @foreach($progressData['assignments_by_status'] as $status => $count)
                        <span class="badge badge-status-{{ $status }}">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Terminal Status Breakdown --}}
            @if(isset($progressData['terminals_by_status']) && $progressData['terminals_by_status']->count() > 0)
                <div class="status-badges">
                    <strong>Terminal Status:</strong>
                    @foreach($progressData['terminals_by_status'] as $status => $count)
                        <span class="badge badge-priority-normal">
                            {{ ucfirst($status) }}: {{ $count }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Project Completion Readiness Check --}}
    @if($project->status === 'active')
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title">Project Completion Readiness</h6>
            </div>
            <div class="card-body">
                @php
                    $totalTerminals    = $progressData['total_terminals'] ?? 0;
                    $completedVisits   = $progressData['completed_visits'] ?? 0;
                    $completedCount    = $progressData['assignments_by_status']['completed'] ?? 0;
                    $pendingAssignments = ($progressData['total_assignments'] ?? 0) - $completedCount;
                    $canComplete       = $totalTerminals > 0 && $completedVisits >= $totalTerminals && $pendingAssignments == 0;
                @endphp

                <div class="readiness-grid">
                    <div class="readiness-checks">
                        <div class="readiness-item">
                            <i class="fas fa-{{ $totalTerminals > 0 ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                            <span>Terminals Assigned: {{ $totalTerminals }}</span>
                        </div>
                        <div class="readiness-item">
                            <i class="fas fa-{{ $completedVisits >= $totalTerminals && $totalTerminals > 0 ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                            <span>All Terminals Visited: {{ $completedVisits }}/{{ $totalTerminals }}</span>
                        </div>
                        <div class="readiness-item">
                            <i class="fas fa-{{ $pendingAssignments == 0 ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                            <span>Pending Assignments: {{ $pendingAssignments }}</span>
                        </div>
                    </div>
                    <div class="readiness-status">
                        @if($canComplete)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Ready for Completion!</strong><br>
                                All requirements met. You can now complete this project.
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Not Ready for Completion</strong><br>
                                Complete all requirements above to finish this project.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Two Column Layout --}}
    <div class="row">
        {{-- Left Column - Project Information --}}
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title">Project Information</h6>
                </div>
                <div class="card-body">
                    <div class="project-details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Type:</span>
                            <span class="badge badge-priority-normal">{{ ucfirst($project->project_type) }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Priority:</span>
                            <span class="badge badge-priority-{{ $project->priority }}">
                                {{ ucfirst($project->priority) }}
                            </span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Start Date:</span>
                            <span class="detail-value">{{ $project->start_date ? $project->start_date->format('M j, Y') : 'Not set' }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">End Date:</span>
                            <span class="detail-value">{{ $project->end_date ? $project->end_date->format('M j, Y') : 'Not set' }}</span>
                        </div>

                        @if($project->projectManager)
                            <div class="detail-item">
                                <span class="detail-label">Manager:</span>
                                <span class="detail-value">{{ $project->projectManager->full_name }}</span>
                            </div>
                        @endif

                        @if($project->budget)
                            <div class="detail-item">
                                <span class="detail-label">Budget:</span>
                                <span class="detail-value">${{ number_format($project->budget, 2) }}</span>
                            </div>
                        @endif

                        <div class="detail-item">
                            <span class="detail-label">Created:</span>
                            <span class="detail-value">{{ $project->created_at->format('M j, Y') }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Created By:</span>
                            <span class="detail-value">{{ $project->createdBy->full_name ?? 'Unknown' }}</span>
                        </div>

                        @if($project->completed_at)
                            <div class="detail-item">
                                <span class="detail-label">Completed:</span>
                                <span class="detail-value">{{ $project->completed_at->format('M j, Y g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Project Reports Section --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title">Project Reports & Documentation</h6>
                </div>
                <div class="card-body">
                    @if($project->status === 'completed' && $project->report_path)
                        <div class="alert alert-success">
                            <i class="fas fa-file-pdf"></i>
                            <strong>Completion Report Available</strong><br>
                            Generated on {{ $project->report_generated_at?->format('M j, Y g:i A') }}
                            <a href="{{ route('projects.download-report', $project) }}" class="btn btn-primary btn-sm ms-2">
                                <i class="fas fa-download"></i> Download Report
                            </a>
                        </div>
                    @elseif($project->status === 'completed')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Project completed but no report available. Contact administrator.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Project reports will be generated automatically when the project is completed.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column - Recent Activities --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title">Recent Activities</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentActivities) && count($recentActivities) > 0)
                        @foreach($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-message">{{ $activity['message'] }}</div>
                                    <div class="activity-time">{{ $activity['date']->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <p class="text-muted">No recent activities to display.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Previous Project Insights --}}
    @if(isset($previousProjects) && $previousProjects->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Insights from Previous Projects</h6>
            </div>
            <div class="card-body">
                @foreach($previousProjects as $insight)
                    <div class="insight-item">
                        <div class="insight-header">
                            <div>
                                <div class="insight-title">{{ $insight['project']->project_name }}</div>
                                <div class="insight-meta">
                                    {{ $insight['project']->project_code }} •
                                    Completed {{ $insight['project']->end_date ? $insight['project']->end_date->diffForHumans() : 'recently' }}
                                </div>
                            </div>
                            <span class="badge badge-status-completed">
                                {{ number_format($insight['completion_data']['completion_percentage'], 1) }}% Complete
                            </span>
                        </div>

                        @if($insight['project']->notes)
                            <div class="insight-description">
                                {{ \Illuminate\Support\Str::limit($insight['project']->notes, 200) }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
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
}

.btn-secondary {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #374151;
}

.btn-secondary:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.btn-outline {
    background: transparent;
    border-color: #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Progress Bar */
.progress {
    background-color: #f3f4f6;
    border-radius: 8px;
    height: 20px;
    overflow: hidden;
}

.progress-bar {
    background-color: #16a34a;
    color: white;
    text-align: center;
    line-height: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: width 0.3s ease;
}

.bg-success {
    background-color: #16a34a !important;
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

/* Status Badges */
.badge-status-active {
    background: #dcfce7;
    color: #166534;
}

.badge-status-completed {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-status-in_progress {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-status-assigned {
    background: #f3f4f6;
    color: #374151;
}

.badge-status-cancelled {
    background: #fecaca;
    color: #991b1b;
}

.badge-status-inactive {
    background: #f3f4f6;
    color: #6b7280;
}

/* Priority Badges */
.badge-priority-emergency {
    background: #fecaca;
    color: #991b1b;
}

.badge-priority-high {
    background: #fed7aa;
    color: #c2410c;
}

.badge-priority-normal {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-priority-low {
    background: #f3f4f6;
    color: #6b7280;
}

/* Status Badges Layout */
.status-badges {
    margin-bottom: 1rem;
}

.status-badges strong {
    display: inline-block;
    margin-right: 0.5rem;
    color: #374151;
}

.status-badges .badge {
    margin-right: 0.5rem;
    margin-bottom: 0.25rem;
}

/* Readiness Grid */
.readiness-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: start;
}

.readiness-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.readiness-item i {
    margin-right: 0.5rem;
}

/* Project Details Grid */
.project-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: #6b7280;
}

.detail-value {
    color: #111827;
    font-weight: 500;
}

/* Activities */
.activity-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-message {
    font-size: 0.875rem;
    color: #111827;
    margin-bottom: 0.25rem;
}

.activity-time {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Insights */
.insight-item {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.insight-item:last-child {
    margin-bottom: 0;
}

.insight-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.insight-title {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.insight-meta {
    font-size: 0.875rem;
    color: #6b7280;
}

.insight-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.5;
}

/* Alert Boxes */
.alert {
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.alert-warning {
    background: #fffbeb;
    border: 1px solid #fed7aa;
    color: #92400e;
}

.alert-info {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    color: #0c4a6e;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 1rem;
}

/* Utility Classes */
.text-muted {
    color: #6b7280;
}

.text-success {
    color: #16a34a;
}

.text-danger {
    color: #dc2626;
}

.mb-3 {
    margin-bottom: 0.75rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.ms-2 {
    margin-left: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .readiness-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .project-details-grid {
        grid-template-columns: 1fr;
    }

    .nav-buttons {
        flex-direction: column;
    }

    .card-body {
        padding: 1rem;
    }

    .insight-header {
        flex-direction: column;
        gap: 0.5rem;
    }

    .stat-card-body {
        padding: 1rem;
    }

    .stat-number {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .container-fluid {
        padding: 1rem;
    }
}
</style>
@endpush
