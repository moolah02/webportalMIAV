{{-- resources/views/projects/partials/manual-report-generator.blade.php
     Loaded into Closure Reports > Generate Reports via fetch(), and served on its own at /projects/{id}/report-generator. --}}
<div class="ui-card mrg">
    <div class="ui-card-header">
        <h3>Generate Project Reports</h3>
    </div>
    <div class="mrg-body">
        @if($project->status !== 'completed')
            <div class="alert-warning mrg-alert">
                This project must be completed before reports can be generated.
            </div>
        @else
            <form action="{{ route('projects.generate-reports', $project) }}" method="POST" id="reportGenerationForm">
                @csrf

                <div class="mrg-grid">
                    <div>
                        <div class="mrg-label">Select Report Types</div>
                        <div class="mrg-types">
                            <label class="mrg-type" for="exec_report">
                                <input type="checkbox" name="report_types[]" value="executive" id="exec_report">
                                <span><strong>Executive Summary</strong><small>High-level overview for management (2-3 pages)</small></span>
                            </label>
                            <label class="mrg-type" for="detailed_report">
                                <input type="checkbox" name="report_types[]" value="detailed" id="detailed_report">
                                <span><strong>Detailed Technical Report</strong><small>Comprehensive analysis with metrics and data</small></span>
                            </label>
                            <label class="mrg-type" for="client_report">
                                <input type="checkbox" name="report_types[]" value="client" id="client_report">
                                <span><strong>Client Presentation</strong><small>Professional slides for client presentation</small></span>
                            </label>
                            <label class="mrg-type" for="simple_report">
                                <input type="checkbox" name="report_types[]" value="simple" id="simple_report">
                                <span><strong>Simple Text Report</strong><small>Basic completion summary (TXT format)</small></span>
                            </label>
                        </div>

                        <label for="custom_notes" class="mrg-label" style="margin-top:14px">Additional Notes for Reports</label>
                        <textarea class="ui-input mrg-input" id="custom_notes" name="custom_notes" rows="3"
                                  placeholder="Any specific information to include in the reports..."></textarea>

                        <label class="mrg-check" for="include_data">
                            <input type="checkbox" name="include_raw_data" id="include_data">
                            <span>Include raw terminal and visit data as appendix</span>
                        </label>
                    </div>

                    <div>
                        <div class="mrg-summary">
                            <div class="mrg-summary-title">Project Summary</div>
                            <div class="summary-item"><span class="summary-label">Project</span><span class="summary-value">{{ $project->project_name }}</span></div>
                            <div class="summary-item"><span class="summary-label">Client</span><span class="summary-value">{{ $project->client->company_name }}</span></div>
                            <div class="summary-item"><span class="summary-label">Completed</span><span class="summary-value">{{ $project->completed_at?->format('M j, Y') }}</span></div>
                            <div class="summary-item">
                                <span class="summary-label">Duration</span>
                                <span class="summary-value">
                                    @if($project->start_date && $project->completed_at)
                                        {{ (int) $project->start_date->diffInDays($project->completed_at) }} days
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            @if($project->report_path)
                                <div class="alert-info mrg-alert" style="margin-top:10px">Previous reports exist and will be replaced by new ones.</div>
                            @endif
                        </div>

                        <div class="mrg-actions">
                            @if($project->report_path)
                                <a href="{{ route('projects.download-report', $project) }}" class="btn-secondary">Download Existing Report</a>
                            @endif
                            <button type="submit" class="btn-primary" id="generateBtn">Generate Reports</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Loading overlay --}}
<div id="reportLoadingModal" class="mrg-overlay" style="display:none">
    <div class="mrg-overlay-box">
        <div class="mrg-spinner" role="status" aria-label="Loading"></div>
        <div style="font-weight:600;color:var(--mv-ink, #16202C);margin-top:10px">Generating Reports...</div>
        <div style="font-size:12.5px;color:var(--mv-muted, #6A7686);margin-top:4px">This may take a few moments. Please don't close this window.</div>
    </div>
</div>

<style>
.mrg-body { padding: 16px 18px; }
.mrg-grid { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: 20px; }
@media (max-width: 800px) { .mrg-grid { grid-template-columns: 1fr; } }
.mrg-label { display: block; font-size: 12.5px; font-weight: 500; color: var(--mv-ink-2, #445162); margin-bottom: 6px; }
.mrg-types { border: 1px solid var(--mv-line, #E1E6EC); border-radius: 8px; overflow: hidden; }
.mrg-type { display: flex; gap: 10px; align-items: flex-start; padding: 10px 12px; cursor: pointer; margin: 0; }
.mrg-type + .mrg-type { border-top: 1px solid var(--mv-line, #E1E6EC); }
.mrg-type:hover { background: var(--mv-surface-2, #F7F9FB); }
.mrg-type input, .mrg-check input { width: 15px; height: 15px; margin-top: 2px; accent-color: var(--mv-accent, #2B64A8); flex-shrink: 0; }
.mrg-type strong { display: block; font-size: 13px; font-weight: 500; color: var(--mv-ink, #16202C); }
.mrg-type small { display: block; font-size: 12px; color: var(--mv-muted, #6A7686); margin-top: 1px; }
.mrg-input { width: 100%; }
.mrg-check { display: flex; gap: 9px; align-items: center; font-size: 13px; color: var(--mv-ink, #16202C); margin-top: 12px; cursor: pointer; }
.mrg-summary { border: 1px solid var(--mv-line, #E1E6EC); border-radius: 8px; background: var(--mv-surface-2, #F7F9FB); padding: 12px 14px; }
.mrg-summary-title { font-size: 12.5px; font-weight: 600; color: var(--mv-ink, #16202C); margin-bottom: 4px; }
.mrg .summary-item { display: flex; justify-content: space-between; gap: 12px; padding: 7px 0; font-size: 13px; }
.mrg .summary-item + .summary-item { border-top: 1px solid var(--mv-line, #E1E6EC); }
.mrg .summary-label { color: var(--mv-muted, #6A7686); }
.mrg .summary-value { color: var(--mv-ink, #16202C); font-weight: 500; text-align: right; }
.mrg-alert { padding: 10px 12px; font-size: 12.5px; border: 1px solid; border-radius: 8px; }
.mrg-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 14px; flex-wrap: wrap; }
.mrg-overlay { position: fixed; inset: 0; z-index: 1050; background: rgba(22, 32, 44, .45); align-items: center; justify-content: center; }
.mrg-overlay-box { background: #fff; border: 1px solid var(--mv-line, #E1E6EC); border-radius: 12px; padding: 22px 26px; text-align: center; max-width: 340px; }
.mrg-spinner { width: 26px; height: 26px; margin: 0 auto; border-radius: 50%; border: 3px solid var(--mv-line, #E1E6EC); border-top-color: var(--mv-accent, #2B64A8); animation: mrg-spin .8s linear infinite; }
@keyframes mrg-spin { to { transform: rotate(360deg); } }
</style>

<script>
(function () {
    const form = document.getElementById('reportGenerationForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        const checkedBoxes = form.querySelectorAll('input[name="report_types[]"]:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one report type.');
            return;
        }
        const overlay = document.getElementById('reportLoadingModal');
        if (overlay) overlay.style.display = 'flex';
        const generateBtn = document.getElementById('generateBtn');
        generateBtn.textContent = 'Generating...';
        generateBtn.disabled = true;
    });
    window.addEventListener('pageshow', function () {
        const overlay = document.getElementById('reportLoadingModal');
        if (overlay) overlay.style.display = 'none';
    });
})();
</script>
