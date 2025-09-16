@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Success Header -->
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">
                    <i class="fas fa-check-circle"></i> Project Completed Successfully!
                </h4>
                <p class="mb-0">
                    <strong>{{ $project->project_name }}</strong> has been marked as completed.
                    All reports have been generated and are ready for review.
                </p>
            </div>

            <!-- Project Summary Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-flag-checkered"></i>
                        Project Completion Summary
                    </h3>
                    <p class="mb-0">{{ $project->project_code }} • {{ $project->client->company_name }}</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Executive Summary</h5>
                            <p>{{ $completionData['completion_summary']['executive_summary'] }}</p>

                            <h6>Key Achievements</h6>
                            <p>{{ $completionData['completion_summary']['key_achievements'] }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Final Statistics</h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-primary">{{ $completionData['performance_metrics']['total_terminals'] }}</h4>
                                            <small>Terminals</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">{{ number_format($completionData['performance_metrics']['completion_percentage'], 1) }}%</h4>
                                            <small>Complete</small>
                                        </div>
                                    </div>
                                    <div class="row text-center mt-2">
                                        <div class="col-6">
                                            <h4 class="text-info">{{ $completionData['performance_metrics']['project_duration_days'] ?? 'N/A' }}</h4>
                                            <small>Days</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-warning">{{ $completionData['performance_metrics']['quality_score'] }}/5</h4>
                                            <small>Quality</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Preview and Actions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-file-alt"></i>
                        Generated Reports
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Report Preview -->
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Report Preview</h6>
                                </div>
                                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Executive Summary Preview -->
                                    <div class="mb-4">
                                        <h5 class="text-primary">PROJECT COMPLETION REPORT</h5>
                                        <hr>
                                        <p><strong>Project:</strong> {{ $project->project_name }}</p>
                                        <p><strong>Client:</strong> {{ $project->client->company_name }}</p>
                                        <p><strong>Completed:</strong> {{ $project->completed_at->format('F j, Y g:i A') }}</p>
                                        <p><strong>Duration:</strong> {{ $completionData['performance_metrics']['project_duration_days'] ?? 'N/A' }} days</p>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-secondary">EXECUTIVE SUMMARY</h6>
                                        <p style="text-align: justify;">{{ $completionData['completion_summary']['executive_summary'] }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-secondary">KEY PERFORMANCE METRICS</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Total Terminals Processed:</td>
                                                <td><strong>{{ $completionData['performance_metrics']['total_terminals'] }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Completion Rate:</td>
                                                <td><strong>{{ number_format($completionData['performance_metrics']['completion_percentage'], 1) }}%</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Quality Score:</td>
                                                <td><strong>{{ $completionData['performance_metrics']['quality_score'] }}/5</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Client Satisfaction:</td>
                                                <td><strong>{{ $completionData['performance_metrics']['client_satisfaction'] }}/5</strong></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-secondary">KEY ACHIEVEMENTS</h6>
                                        <p style="text-align: justify;">{{ $completionData['completion_summary']['key_achievements'] }}</p>
                                    </div>

                                    @if($completionData['completion_summary']['challenges_overcome'])
                                    <div class="mb-4">
                                        <h6 class="text-secondary">CHALLENGES & SOLUTIONS</h6>
                                        <p style="text-align: justify;">{{ $completionData['completion_summary']['challenges_overcome'] }}</p>
                                    </div>
                                    @endif

                                    @if($completionData['technical_analysis']['recommendations'])
                                    <div class="mb-4">
                                        <h6 class="text-secondary">RECOMMENDATIONS</h6>
                                        <p style="text-align: justify;">{{ $completionData['technical_analysis']['recommendations'] }}</p>
                                    </div>
                                    @endif

                                    <div class="text-center mt-4">
                                        <small class="text-muted">--- End of Preview ---</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Panel -->
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Report Actions</h6>
                                </div>
                                <div class="card-body">
                                    <h6>Available Reports:</h6>
                                    <div class="list-group mb-3">
                                        @foreach($completionData['generated_reports'] as $reportType)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                {{ ucfirst(str_replace('_', ' ', $reportType)) }} Report
                                            </span>
                                            <span class="badge bg-success">Ready</span>
                                        </div>
                                        @endforeach
                                    </div>

                                    <!-- Download Options -->
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('projects.download-report', $project) }}" class="btn btn-success">
                                            <i class="fas fa-download"></i> Download All Reports
                                        </a>

                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal">
                                            <i class="fas fa-eye"></i> Full Preview
                                        </button>

                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal">
                                            <i class="fas fa-edit"></i> Edit & Regenerate
                                        </button>

                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-arrow-left"></i> Back to Project
                                        </a>
                                    </div>

                                    <!-- Email Options -->
                                    <div class="mt-3">
                                        <h6>Share Reports:</h6>
                                        <button type="button" class="btn btn-outline-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#emailModal">
                                            <i class="fas fa-envelope"></i> Email to Client
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Feedback -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Project Rating</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <span class="text-muted">Quality Score:</span><br>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $completionData['performance_metrics']['quality_score'] ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                    <div>
                                        <span class="text-muted">Client Satisfaction:</span><br>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $completionData['performance_metrics']['client_satisfaction'] ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Report Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="height: 70vh; overflow-y: auto;">
                <embed src="{{ route('projects.download-report', $project) }}" type="application/pdf" width="100%" height="100%">
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Report Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('projects.regenerate-report', $project) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Executive Summary</label>
                        <textarea class="form-control" name="executive_summary" rows="3">{{ $completionData['completion_summary']['executive_summary'] }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Key Achievements</label>
                        <textarea class="form-control" name="key_achievements" rows="3">{{ $completionData['completion_summary']['key_achievements'] }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recommendations</label>
                        <textarea class="form-control" name="recommendations" rows="3">{{ $completionData['technical_analysis']['recommendations'] }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Regenerate Reports</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Reports to Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('projects.email-report', $project) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" name="recipient_email"
                               value="{{ $project->client->email ?? '' }}" required>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject"
                               value="Project Completion Report - {{ $project->project_name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="4">Dear {{ $project->client->company_name }},

We are pleased to inform you that {{ $project->project_name }} has been successfully completed. Please find the attached completion report with detailed analysis and recommendations.

Thank you for choosing our services.

Best regards,
{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
