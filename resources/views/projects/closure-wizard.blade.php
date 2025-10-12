{{-- resources/views/projects/closure-wizard.blade.php --}}
@extends('layouts.app')

@section('title', 'Close Project - ' . $project->project_name)

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <h2 class="page-title mb-4">
        <i class="fas fa-archive"></i>
        Project Closure Wizard - {{ $project->project_name }}
    </h2>
    <p class="page-subtitle">{{ $project->project_code }} • {{ $project->client->company_name }}</p>

    {{-- Progress Steps --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">Closure Progress</h6>
        </div>
        <div class="card-body">
            <div class="progress-steps">
                <div class="step-item active" data-step="1">
                    <div class="step-circle">1</div>
                    <span class="step-label">Status Review</span>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-circle">2</div>
                    <span class="step-label">Summary</span>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-circle">3</div>
                    <span class="step-label">Analytics</span>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-circle">4</div>
                    <span class="step-label">Closure</span>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('projects.close', $project) }}" method="POST" id="closureWizard">
        @csrf

        {{-- Step 1: Project Status Review --}}
        <div class="wizard-step active" id="step1">
            {{-- Statistics Cards --}}
            <div class="stats-grid mb-4">
                <div class="stat-card">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $progressData['total_terminals'] ?? 0 }}</div>
                            <div class="stat-label">Total Assigned</div>
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
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-body">
                        <div class="stat-icon stat-icon-pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ ($progressData['total_terminals'] ?? 0) - ($progressData['completed_visits'] ?? 0) }}</div>
                            <div class="stat-label">Remaining</div>
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
                            <div class="stat-label">Progress</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Regional Breakdown --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Geographic Distribution</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($progressData['regional_performance']) && $progressData['regional_performance']->count() > 0)
                                @php $totalTerminals = $progressData['regional_performance']->sum('total_terminals'); @endphp
                                @foreach($progressData['regional_performance']->take(3) as $region)
                                <div class="region-item">
                                    <span class="region-name">{{ $region->region }}</span>
                                    <span class="badge badge-priority-normal">{{ $totalTerminals > 0 ? round(($region->total_terminals / $totalTerminals) * 100, 1) : 0 }}%</span>
                                </div>
                                @endforeach
                            @else
                                <div class="region-item">
                                    <span class="region-name">Main Region</span>
                                    <span class="badge badge-priority-normal">100%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Closure Reason Selection --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Closure Reason</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Why are you closing this project? *</label>
                                <select class="form-control" name="closure_reason" required>
                                    <option value="">Select closure reason...</option>
                                    <option value="completed">Project Completed Successfully</option>
                                    <option value="cancelled">Project Cancelled</option>
                                    <option value="on_hold">Project On Hold</option>
                                    <option value="client_request">Closed at Client Request</option>
                                </select>
                            </div>

                            {{-- Status indicators --}}
                            <div class="status-item">
                                <i class="fas fa-info-circle text-info"></i>
                                <span>Current Progress: {{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="status-item">
                                <i class="fas fa-{{ ($progressData['total_terminals'] ?? 0) > 0 ? 'check-circle text-success' : 'info-circle text-muted' }}"></i>
                                <span>Terminals Assigned: {{ $progressData['total_terminals'] ?? 0 }}</span>
                            </div>
                            <div class="status-item">
                                <i class="fas fa-{{ ($progressData['completed_visits'] ?? 0) > 0 ? 'check-circle text-success' : 'info-circle text-muted' }}"></i>
                                <span>Terminals Visited: {{ $progressData['completed_visits'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: Project Summary --}}
        <div class="wizard-step" id="step2">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Project Summary & Outcomes</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="executive_summary" class="form-label">Executive Summary *</label>
                                <textarea class="form-control" id="executive_summary" name="executive_summary" rows="4" required
                                          placeholder="Provide a summary of what was accomplished, current status, and any important outcomes..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="key_achievements" class="form-label">Key Achievements *</label>
                                <textarea class="form-control" id="key_achievements" name="key_achievements" rows="3" required
                                          placeholder="List the major accomplishments and milestones reached during this project..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="challenges_overcome" class="form-label">Challenges & Solutions</label>
                                <textarea class="form-control" id="challenges_overcome" name="challenges_overcome" rows="3"
                                          placeholder="Describe any significant challenges encountered and how they were addressed..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="lessons_learned" class="form-label">Lessons Learned</label>
                                <textarea class="form-control" id="lessons_learned" name="lessons_learned" rows="3"
                                          placeholder="What insights or lessons can be applied to future projects?"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Analytics --}}
        <div class="wizard-step" id="step3">
            <div class="row">
                {{-- Regional Performance Analysis --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Regional Performance Analysis</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Region</th>
                                            <th>Terminals</th>
                                            <th>Progress</th>
                                            <th>Avg Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($progressData['regional_performance']) && $progressData['regional_performance']->count() > 0)
                                            @foreach($progressData['regional_performance'] as $region)
                                            <tr>
                                                <td>{{ $region->region ?? 'Unknown' }}</td>
                                                <td>{{ $region->total_terminals }}</td>
                                                <td>
                                                    <span class="badge badge-status-{{ $region->completion_rate >= 95 ? 'completed' : ($region->completion_rate >= 80 ? 'in_progress' : 'assigned') }}">
                                                        {{ $region->completion_rate }}%
                                                    </span>
                                                </td>
                                                <td>{{ number_format($region->avg_duration / 60, 1) }} hrs</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <i class="fas fa-info-circle"></i> Regional data calculated from terminal locations
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Team Performance --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Team Performance Metrics</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($progressData['team_metrics']))
                            <div class="metric-item">
                                <span class="metric-label">Total Assignments</span>
                                <span class="metric-value">{{ $progressData['team_metrics']['total_assignments'] ?? 0 }}</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Completed</span>
                                <span class="metric-value">{{ $progressData['team_metrics']['completed_assignments'] ?? 0 }}</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Team Progress Rate</span>
                                <span class="badge badge-status-{{ ($progressData['team_metrics']['completion_rate'] ?? 0) >= 90 ? 'completed' : 'in_progress' }}">
                                    {{ $progressData['team_metrics']['completion_rate'] ?? 0 }}%
                                </span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Unique Technicians</span>
                                <span class="metric-value">{{ $progressData['team_metrics']['unique_technicians'] ?? 0 }} technicians</span>
                            </div>
                            @else
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Team metrics calculated from job assignments and visit data.
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Issues & Recommendations --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title">Issues & Recommendations</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="issues_found" class="form-label">Technical Issues Discovered</label>
                        <textarea class="form-control" id="issues_found" name="issues_found" rows="2"
                                  placeholder="Summarize any technical issues found during terminal visits..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="recommendations" class="form-label">Recommendations for Client</label>
                        <textarea class="form-control" id="recommendations" name="recommendations" rows="2"
                                  placeholder="Provide recommendations for terminal maintenance, upgrades, or operational improvements..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4: Closure Finalization --}}
        <div class="wizard-step" id="step4">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Closure Confirmation</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Project Closure</strong><br>
                                You are about to close this project. This action will:
                                <ul class="mt-2 mb-0">
                                    <li>Change the project status based on your selected reason</li>
                                    <li>Generate a closure report with your summary and findings</li>
                                    <li>Archive the project for future reference</li>
                                    <li>Preserve all assignment and visit data</li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <label for="additional_notes" class="form-label">Additional Notes for Report</label>
                                <textarea class="form-control" id="additional_notes" name="additional_notes" rows="3"
                                          placeholder="Any additional information to include in the closure documentation..."></textarea>
                            </div>

                            <div class="closure-checklist">
                                <h6>Please confirm:</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm1" required>
                                    <label class="form-check-label" for="confirm1">
                                        I have reviewed the project status and outcomes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm2" required>
                                    <label class="form-check-label" for="confirm2">
                                        All relevant information has been documented
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm3" required>
                                    <label class="form-check-label" for="confirm3">
                                        I understand this action will close the project
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Project Closure Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="metric-item">
                                <span class="metric-label">Project Duration</span>
                                <span class="metric-value">{{ $project->start_date ? $project->start_date->diffInDays(now()) : 'N/A' }} days</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Total Terminals</span>
                                <span class="metric-value">{{ $progressData['total_terminals'] ?? 0 }}</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Progress Made</span>
                                <span class="metric-value">{{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Total Assignments</span>
                                <span class="metric-value">{{ $progressData['total_assignments'] ?? 0 }}</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Final Status</span>
                                <span class="badge badge-status-in_progress">Ready for Closure</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="navigation">
            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                <i class="fas fa-arrow-left"></i> Previous
            </button>
            <div></div>
            <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                Next <i class="fas fa-arrow-right"></i>
            </button>
            <button type="submit" class="btn btn-danger" id="submitBtn" style="display: none;">
                <i class="fas fa-archive"></i> Close Project
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
/* Base Styling */
.page-title {
    color: #374151;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
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

/* Progress Steps */
.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    padding: 1rem 0;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.step-item.active .step-circle {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.step-item.completed .step-circle {
    background: #16a34a;
    border-color: #16a34a;
    color: white;
}

.step-label {
    font-weight: 500;
    color: #6b7280;
    text-align: center;
    font-size: 0.875rem;
}

.step-item.active .step-label {
    color: #3b82f6;
    font-weight: 600;
}

.step-item.completed .step-label {
    color: #16a34a;
    font-weight: 600;
}

/* Wizard Steps */
.wizard-step {
    display: none;
}

.wizard-step.active {
    display: block;
}

/* Form Elements */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
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

/* Checkboxes */
.form-check {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    gap: 0.75rem;
}

.form-check-input {
    width: 16px;
    height: 16px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: white;
    margin-top: 2px;
    flex-shrink: 0;
}

.form-check-input:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.form-check-label {
    color: #374151;
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Status Items */
.status-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
}

.status-item:last-child {
    border-bottom: none;
}

/* Regional Items */
.region-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.region-item:last-child {
    border-bottom: none;
}

.region-name {
    font-weight: 500;
    color: #374151;
}

/* Metric Items */
.metric-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.metric-item:last-child {
    border-bottom: none;
}

.metric-label {
    font-weight: 500;
    color: #6b7280;
}

.metric-value {
    color: #111827;
    font-weight: 500;
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

.badge-status-completed {
    background: #dcfce7;
    color: #166534;
}

.badge-status-in_progress {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-status-assigned {
    background: #f3f4f6;
    color: #374151;
}

.badge-priority-normal {
    background: #dbeafe;
    color: #1d4ed8;
}

/* Data Table */
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
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
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

.btn-danger {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}

.btn-danger:hover {
    background: #b91c1c;
    border-color: #b91c1c;
}

/* Navigation */
.navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

/* Alert */
.alert {
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid;
    margin-bottom: 1rem;
}

.alert-info {
    background: #f0f9ff;
    border-left-color: #3b82f6;
    color: #1e40af;
}

/* Closure Checklist */
.closure-checklist {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.closure-checklist h6 {
    color: #374151;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Utility Classes */
.text-center { text-align: center; }
.text-muted { color: #6b7280; }
.text-info { color: #3b82f6; }
.text-success { color: #16a34a; }
.table-responsive { overflow-x: auto; }

/* Responsive Design */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .progress-steps {
        flex-wrap: wrap;
        gap: 1rem;
    }

    .navigation {
        flex-direction: column;
        gap: 1rem;
    }

    .card-body {
        padding: 1rem;
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
// Initialize variables
let currentStep = 1;
const totalSteps = 4;

function changeStep(direction) {
    // Hide current step
    document.getElementById(`step${currentStep}`).classList.remove('active');

    // Update step indicators
    const stepItems = document.querySelectorAll('.step-item');
    stepItems[currentStep - 1].classList.remove('active');

    // Update step number
    currentStep += direction;

    // Show new step
    document.getElementById(`step${currentStep}`).classList.add('active');
    stepItems[currentStep - 1].classList.add('active');

    // Update buttons
    document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-flex';
    document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-flex';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-flex' : 'none';

    // Mark completed steps
    stepItems.forEach((item, index) => {
        if (index < currentStep - 1) {
            item.classList.add('completed');
        } else {
            item.classList.remove('completed');
        }
    });

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Form submission
document.getElementById('closureWizard').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    if (!submitBtn) return;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;
});
</script>
@endpush
