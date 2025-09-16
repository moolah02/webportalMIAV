{{-- resources/views/projects/partials/report-generation-tab.blade.php --}}

<div class="mb-4">
    <h5 class="text-dark mb-2">Report Generation & Management</h5>
    <p class="text-muted">Generate, customize, and manage project completion reports.</p>
</div>

<!-- Report Generation Cards -->
<div class="row g-4">
    @if($completedProjects->count() > 0)
        @foreach($completedProjects as $project)
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100 report-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title mb-1 text-white">{{ $project->project_name }}</h6>
                            <small class="text-white-50">{{ $project->project_code }}</small>
                        </div>
                        <span class="badge bg-white text-primary">
                            {{ $project->completed_at?->format('M j, Y') }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="project-info mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-building text-primary me-2"></i>
                            <span class="text-muted">{{ $project->client->company_name }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user text-info me-2"></i>
                            <span class="text-muted">{{ $project->projectManager?->full_name ?? 'Unassigned' }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-chart-line text-success me-2"></i>
                            <span class="text-muted">{{ $project->completion_percentage ?? 100 }}% Complete</span>
                        </div>
                        @if(isset($project->completion))
                            <div class="d-flex align-items-center">
                                <i class="fas fa-star text-warning me-2"></i>
                                <span class="text-muted">Quality: {{ $project->completion->quality_score }}/5</span>
                            </div>
                        @endif
                    </div>

                    <!-- Report Status -->
                    <div class="report-status mb-3">
                        @if($project->report_path)
                            <div class="alert alert-success border-0 py-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Reports Available</strong>
                                <small class="d-block text-muted mt-1">
                                    Generated: {{ $project->report_generated_at?->format('M j, Y g:i A') ?? 'Unknown' }}
                                </small>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 py-2">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>No Reports Generated</strong>
                                <small class="d-block text-muted mt-1">Click generate to create reports</small>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary generate-report-btn"
                                data-project-id="{{ $project->id }}"
                                data-project-name="{{ $project->project_name }}"
                                data-client-name="{{ $project->client->company_name }}">
                            <i class="fas fa-file-pdf me-2"></i>
                            {{ $project->report_path ? 'Regenerate Reports' : 'Generate Reports' }}
                        </button>

                        @if($project->report_path)
                            <div class="btn-group">
                                <a href="{{ route('projects.download-report', $project) }}"
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                                <button type="button" class="btn btn-outline-info btn-sm email-report-btn"
                                        data-project-id="{{ $project->id }}"
                                        data-client-email="{{ $project->client->email ?? '' }}">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm preview-report-btn"
                                        data-project-id="{{ $project->id }}"
                                        data-project="{{ json_encode($project) }}"
                                        data-completion="{{ isset($project->completion) ? json_encode($project->completion) : '{}' }}">
                                    <i class="fas fa-eye me-1"></i>Preview
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-file-pdf text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h4 class="text-muted mb-2">No Completed Projects</h4>
                    <p class="text-muted">Reports can only be generated for completed projects.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Report Generation Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title text-dark">Generate Project Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="reportGenerationForm" method="POST">
                @csrf
                <input type="hidden" name="project_id" id="reportProjectId">

                <div class="modal-body">
                    <div class="alert alert-info border-0 bg-light mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-info me-3"></i>
                            <div>
                                <div><strong>Project:</strong> <span id="reportProjectName"></span></div>
                                <div><strong>Client:</strong> <span id="reportClientName"></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-8">
                            <h6 class="fw-semibold mb-3">Report Types</h6>
                            <div class="report-types">
                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="checkbox" name="report_types[]" value="executive" id="exec_report" checked>
                                    <label class="form-check-label" for="exec_report">
                                        <strong>Executive Summary Report</strong>
                                        <small class="d-block text-muted">High-level overview perfect for management and stakeholders</small>
                                    </label>
                                </div>
                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="checkbox" name="report_types[]" value="detailed" id="detail_report" checked>
                                    <label class="form-check-label" for="detail_report">
                                        <strong>Detailed Technical Report</strong>
                                        <small class="d-block text-muted">Comprehensive technical analysis with all metrics and data</small>
                                    </label>
                                </div>
                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="checkbox" name="report_types[]" value="client" id="client_report" checked>
                                    <label class="form-check-label" for="client_report">
                                        <strong>Client Presentation</strong>
                                        <small class="d-block text-muted">Professional presentation format for client meetings</small>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="report_notes" class="form-label fw-semibold">Additional Report Notes</label>
                                <textarea class="form-control" id="report_notes" name="report_notes" rows="3"
                                          placeholder="Add any specific instructions or additional information for the reports..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0 fw-semibold">Report Options</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_charts" name="include_charts" checked>
                                        <label class="form-check-label" for="include_charts">
                                            Include Charts & Graphs
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_photos" name="include_photos">
                                        <label class="form-check-label" for="include_photos">
                                            Include Site Photos
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_recommendations" name="include_recommendations" checked>
                                        <label class="form-check-label" for="include_recommendations">
                                            Include Recommendations
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="branded_template" name="branded_template" checked>
                                        <label class="form-check-label" for="branded_template">
                                            Use Branded Template
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 text-center">
                                <div class="text-muted mb-2">
                                    <i class="fas fa-clock me-1"></i>
                                    Estimated Generation Time
                                </div>
                                <div class="badge bg-info">2-3 minutes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cog me-2"></i>Generate Reports
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email Report Modal -->
<div class="modal fade" id="emailReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title text-dark">Email Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="emailReportForm" method="POST">
                @csrf
                <input type="hidden" name="project_id" id="emailProjectId">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipient_email" class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" id="recipient_email" name="recipient_email" required>
                    </div>

                    <div class="mb-3">
                        <label for="email_subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="email_subject" name="email_subject"
                               value="Project Completion Report - [PROJECT_NAME]" required>
                    </div>

                    <div class="mb-3">
                        <label for="email_message" class="form-label">Message</label>
                        <textarea class="form-control" id="email_message" name="email_message" rows="4" required>Dear Client,

Please find attached the completion report for your recent project. The report contains a comprehensive overview of all work completed, findings, and recommendations.

If you have any questions about the report or require additional information, please don't hesitate to contact us.

Best regards,
[YOUR_NAME]</textarea>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.report-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 16px;
    overflow: hidden;
}

.report-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.report-types .form-check {
    transition: all 0.2s ease;
}

.report-types .form-check:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    flex: 1;
}

.empty-state {
    padding: 4rem 2rem;
}

.alert {
    border-radius: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate Report Modal
    document.querySelectorAll('.generate-report-btn').forEach(button => {
        button.addEventListener('click', function() {
            const projectId = this.getAttribute('data-project-id');
            const projectName = this.getAttribute('data-project-name');
            const clientName = this.getAttribute('data-client-name');

            document.getElementById('reportProjectId').value = projectId;
            document.getElementById('reportProjectName').textContent = projectName;
            document.getElementById('reportClientName').textContent = clientName;

            const modal = new bootstrap.Modal(document.getElementById('generateReportModal'));
            modal.show();
        });
    });

    // Email Report Modal
    document.querySelectorAll('.email-report-btn').forEach(button => {
        button.addEventListener('click', function() {
            const projectId = this.getAttribute('data-project-id');
            const clientEmail = this.getAttribute('data-client-email');

            document.getElementById('emailProjectId').value = projectId;
            document.getElementById('recipient_email').value = clientEmail;

            const modal = new bootstrap.Modal(document.getElementById('emailReportModal'));
            modal.show();
        });
    });

    // Report Generation Form Submit
    document.getElementById('reportGenerationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
        submitBtn.disabled = true;

        // Here you would make an AJAX call to generate reports
        // For now, just simulate the process
        setTimeout(function() {
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Generated!';
            setTimeout(function() {
                location.reload(); // Refresh to show new reports
            }, 1000);
        }, 3000);
    });

    // Email Form Submit
    document.getElementById('emailReportForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;

        // Simulate email sending
        setTimeout(function() {
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Sent!';
            setTimeout(function() {
                bootstrap.Modal.getInstance(document.getElementById('emailReportModal')).hide();
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Email';
                submitBtn.disabled = false;
            }, 1000);
        }, 2000);
    });
});
</script>
