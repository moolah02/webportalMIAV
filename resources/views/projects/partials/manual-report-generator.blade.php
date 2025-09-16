{{-- resources/views/projects/partials/manual-report-generator.blade.php --}}
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Generate Project Reports</h6>
    </div>
    <div class="card-body">
        @if($project->status !== 'completed')
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                This project must be completed before reports can be generated.
            </div>
        @else
            <form action="{{ route('projects.generate-reports', $project) }}" method="POST" id="reportGenerationForm">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label">Select Report Types</label>
                            <div class="report-types">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="report_types[]" value="executive" id="exec_report">
                                    <label class="form-check-label" for="exec_report">
                                        <strong>Executive Summary</strong>
                                        <small class="text-muted d-block">High-level overview for management (2-3 pages)</small>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="report_types[]" value="detailed" id="detailed_report">
                                    <label class="form-check-label" for="detailed_report">
                                        <strong>Detailed Technical Report</strong>
                                        <small class="text-muted d-block">Comprehensive analysis with metrics and data</small>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="report_types[]" value="client" id="client_report">
                                    <label class="form-check-label" for="client_report">
                                        <strong>Client Presentation</strong>
                                        <small class="text-muted d-block">Professional slides for client presentation</small>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="report_types[]" value="simple" id="simple_report">
                                    <label class="form-check-label" for="simple_report">
                                        <strong>Simple Text Report</strong>
                                        <small class="text-muted d-block">Basic completion summary (TXT format)</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="custom_notes" class="form-label">Additional Notes for Reports</label>
                            <textarea class="form-control" id="custom_notes" name="custom_notes" rows="3"
                                      placeholder="Any specific information to include in the reports..."></textarea>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_raw_data" id="include_data">
                                <label class="form-check-label" for="include_data">
                                    Include raw terminal and visit data as appendix
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Project Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="summary-item">
                                    <span class="summary-label">Project:</span>
                                    <span class="summary-value">{{ $project->project_name }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Client:</span>
                                    <span class="summary-value">{{ $project->client->company_name }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Completed:</span>
                                    <span class="summary-value">{{ $project->completed_at?->format('M j, Y') }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Duration:</span>
                                    <span class="summary-value">
                                        @if($project->start_date && $project->completed_at)
                                            {{ $project->start_date->diffInDays($project->completed_at) }} days
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>

                                @if($project->report_path)
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle"></i>
                                        <small>Previous reports exist and will be replaced by new ones.</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary w-100" id="generateBtn">
                                <i class="fas fa-file-pdf"></i> Generate Reports
                            </button>

                            @if($project->report_path)
                                <a href="{{ route('projects.download-report', $project) }}" class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="fas fa-download"></i> Download Existing Report
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Loading Modal --}}
<div class="modal fade" id="reportLoadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Generating Reports...</h5>
                <p class="text-muted">This may take a few moments. Please don't close this window.</p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.report-types .form-check {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.report-types .form-check:hover {
    background: #f9fafb;
    border-color: #3b82f6;
}

.report-types .form-check-input:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-label {
    font-weight: 500;
    color: #6b7280;
}

.summary-value {
    color: #111827;
    font-weight: 500;
}

.card.bg-light {
    background-color: #f9fafb !important;
}
</style>

<script>
document.getElementById('reportGenerationForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('input[name="report_types[]"]:checked');

    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one report type.');
        return;
    }

    // Show loading modal
    const modal = new bootstrap.Modal(document.getElementById('reportLoadingModal'));
    modal.show();

    // Disable form elements
    const generateBtn = document.getElementById('generateBtn');
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    generateBtn.disabled = true;

    // Form will submit normally
});

// Auto-hide modal on page load (in case of redirect back)
document.addEventListener('DOMContentLoaded', function() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('reportLoadingModal'));
    if (modal) {
        modal.hide();
    }
});
</script>
