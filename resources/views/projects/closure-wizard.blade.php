{{-- resources/views/projects/closure-wizard.blade.php --}}
@extends('layouts.app')

@section('title', 'Close Project - ' . $project->project_name)

@section('header-actions')
<a href="{{ route('projects.show', $project) }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Project</a>
@endsection

@push('styles')
<style>
.cw-head { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 18px; }
.cw-name { font-size: 16px; font-weight: 600; color: var(--mv-ink); }
.cw-code { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 7px; }
.cw-muted { color: var(--mv-muted); font-size: 12.5px; }
.cw-steps { display: flex; align-items: center; padding: 14px 18px; border-top: 1px solid var(--mv-line); overflow-x: auto; }
.step-item { display: flex; align-items: center; gap: 8px; flex-shrink: 0; font-size: 13px; color: var(--mv-muted); }
.step-circle { width: 24px; height: 24px; border-radius: 50%; border: 1px solid var(--mv-line-strong); background: var(--mv-surface); color: var(--mv-muted); font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; font-variant-numeric: tabular-nums; }
.step-item.active { color: var(--mv-accent-ink); font-weight: 600; }
.step-item.active .step-circle { background: var(--mv-accent); border-color: var(--mv-accent); color: #fff; }
.step-item.completed { color: var(--mv-good); }
.step-item.completed .step-circle { background: var(--mv-good-soft); border-color: #C6E6D2; color: var(--mv-good); }
.cw-line { flex: 1; min-width: 28px; height: 1px; background: var(--mv-line); margin: 0 12px; }
.wizard-step { display: none; }
.wizard-step.active { display: block; }
.cw-stack { display: flex; flex-direction: column; gap: 16px; margin-top: 16px; }
.cw-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
@media (max-width: 900px) { .cw-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.cw-two { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; align-items: start; }
@media (max-width: 900px) { .cw-two { grid-template-columns: 1fr; } }
.cw-body { padding: 16px 18px; }
.cw-field { margin-bottom: 14px; }
.cw-field:last-child { margin-bottom: 0; }
.cw-field .ui-input, .cw-field .ui-select { width: 100%; }
.cw-req { color: var(--mv-crit); }
.cw-rows { padding: 2px 18px; }
.cw-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 0; font-size: 13px; }
.cw-row + .cw-row { border-top: 1px solid var(--mv-line); }
.cw-row > span:first-child { color: var(--mv-ink-2); }
.cw-row strong { font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.cw-status { display: flex; align-items: center; gap: 9px; padding: 8px 0; font-size: 13px; color: var(--mv-ink-2); font-variant-numeric: tabular-nums; }
.cw-status + .cw-status { border-top: 1px solid var(--mv-line); }
.cw-status .mv-i { color: var(--mv-muted); flex-shrink: 0; }
.cw-status.is-ok .mv-i { color: var(--mv-good); }
.cw-chip { font-size: 12px; padding: 2px 8px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); font-variant-numeric: tabular-nums; }
.cw-num { text-align: right; font-variant-numeric: tabular-nums; }
.cw-callout { padding: 12px 14px; font-size: 13px; border: 1px solid; border-radius: 8px; line-height: 1.5; margin-bottom: 14px; }
.cw-callout ul { margin: 6px 0 0 18px; list-style: disc; }
.closure-checklist { border-top: 1px solid var(--mv-line); padding-top: 12px; margin-top: 4px; }
.closure-checklist h6 { font-size: 12.5px; font-weight: 600; color: var(--mv-ink); margin: 0 0 8px; }
.cw-check { display: flex; align-items: flex-start; gap: 9px; padding: 5px 0; font-size: 13px; color: var(--mv-ink); cursor: pointer; }
.cw-check input { width: 15px; height: 15px; margin-top: 2px; accent-color: var(--mv-accent); flex-shrink: 0; }
.navigation { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: 16px; padding: 12px 18px; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
.navigation .cw-nav-right { display: flex; gap: 8px; }
</style>
@endpush

@section('content')
<div class="ui-card">
    <div class="cw-head">
        <span class="cw-name">{{ $project->project_name }}</span>
        <span class="cw-code">{{ $project->project_code }}</span>
        @if($project->client)<span class="cw-muted">{{ $project->client->company_name }}</span>@endif
    </div>
    <div class="cw-steps" aria-label="Closure Progress">
        <div class="step-item active" data-step="1"><span class="step-circle">1</span><span class="step-label">Status Review</span></div>
        <div class="cw-line"></div>
        <div class="step-item" data-step="2"><span class="step-circle">2</span><span class="step-label">Summary</span></div>
        <div class="cw-line"></div>
        <div class="step-item" data-step="3"><span class="step-circle">3</span><span class="step-label">Analytics</span></div>
        <div class="cw-line"></div>
        <div class="step-item" data-step="4"><span class="step-circle">4</span><span class="step-label">Closure</span></div>
    </div>
</div>

<form action="{{ route('projects.close', $project) }}" method="POST" id="closureWizard">
    @csrf

    {{-- Step 1: Project Status Review --}}
    <div class="wizard-step active" id="step1">
        <div class="cw-stack">
            <div class="cw-stats">
                <div class="stat-card">
                    <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-monitor"/></svg></div>
                    <div>
                        <div class="stat-number">{{ $progressData['total_terminals'] ?? 0 }}</div>
                        <div class="stat-label">Total Assigned</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
                    <div>
                        <div class="stat-number">{{ $progressData['completed_visits'] ?? 0 }}</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-clock"/></svg></div>
                    <div>
                        <div class="stat-number">{{ ($progressData['total_terminals'] ?? 0) - ($progressData['completed_visits'] ?? 0) }}</div>
                        <div class="stat-label">Remaining</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-chart"/></svg></div>
                    <div>
                        <div class="stat-number">{{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%</div>
                        <div class="stat-label">Progress</div>
                    </div>
                </div>
            </div>

            <div class="cw-two">
                <div class="ui-card">
                    <div class="ui-card-header"><h3>Geographic Distribution</h3></div>
                    <div class="cw-rows">
                        @if(isset($progressData['regional_performance']) && $progressData['regional_performance']->count() > 0)
                            @php $totalTerminals = $progressData['regional_performance']->sum('total_terminals'); @endphp
                            @foreach($progressData['regional_performance']->take(3) as $region)
                            <div class="cw-row">
                                <span>{{ $region->region }}</span>
                                <span class="cw-chip">{{ $totalTerminals > 0 ? round(($region->total_terminals / $totalTerminals) * 100, 1) : 0 }}%</span>
                            </div>
                            @endforeach
                        @else
                            <div class="cw-row"><span>Main Region</span><span class="cw-chip">100%</span></div>
                        @endif
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h3>Closure Reason</h3></div>
                    <div class="cw-body">
                        <div class="cw-field">
                            <label class="ui-label">Why are you closing this project? <span class="cw-req">*</span></label>
                            <select class="ui-select" name="closure_reason" required>
                                <option value="">Select closure reason...</option>
                                <option value="completed">Project Completed Successfully</option>
                                <option value="cancelled">Project Cancelled</option>
                                <option value="on_hold">Project On Hold</option>
                                <option value="client_request">Closed at Client Request</option>
                            </select>
                        </div>
                        <div class="cw-status">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-info"/></svg>
                            <span>Current Progress: {{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%</span>
                        </div>
                        <div class="cw-status {{ ($progressData['total_terminals'] ?? 0) > 0 ? 'is-ok' : '' }}">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-{{ ($progressData['total_terminals'] ?? 0) > 0 ? 'check-circle' : 'info' }}"/></svg>
                            <span>Terminals Assigned: {{ $progressData['total_terminals'] ?? 0 }}</span>
                        </div>
                        <div class="cw-status {{ ($progressData['completed_visits'] ?? 0) > 0 ? 'is-ok' : '' }}">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-{{ ($progressData['completed_visits'] ?? 0) > 0 ? 'check-circle' : 'info' }}"/></svg>
                            <span>Terminals Visited: {{ $progressData['completed_visits'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Project Summary --}}
    <div class="wizard-step" id="step2">
        <div class="cw-stack">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Project Summary &amp; Outcomes</h3></div>
                <div class="cw-body">
                    <div class="cw-field">
                        <label for="executive_summary" class="ui-label">Executive Summary <span class="cw-req">*</span></label>
                        <textarea class="ui-input" id="executive_summary" name="executive_summary" rows="4" required
                                  placeholder="Provide a summary of what was accomplished, current status, and any important outcomes..."></textarea>
                    </div>
                    <div class="cw-field">
                        <label for="key_achievements" class="ui-label">Key Achievements <span class="cw-req">*</span></label>
                        <textarea class="ui-input" id="key_achievements" name="key_achievements" rows="3" required
                                  placeholder="List the major accomplishments and milestones reached during this project..."></textarea>
                    </div>
                    <div class="cw-two" style="gap:14px">
                        <div class="cw-field">
                            <label for="challenges_overcome" class="ui-label">Challenges &amp; Solutions</label>
                            <textarea class="ui-input" id="challenges_overcome" name="challenges_overcome" rows="3"
                                      placeholder="Describe any significant challenges encountered and how they were addressed..."></textarea>
                        </div>
                        <div class="cw-field">
                            <label for="lessons_learned" class="ui-label">Lessons Learned</label>
                            <textarea class="ui-input" id="lessons_learned" name="lessons_learned" rows="3"
                                      placeholder="What insights or lessons can be applied to future projects?"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 3: Analytics --}}
    <div class="wizard-step" id="step3">
        <div class="cw-stack">
            <div class="cw-two">
                <div class="ui-card overflow-hidden">
                    <div class="ui-card-header"><h3>Regional Performance Analysis</h3></div>
                    <div class="overflow-x-auto">
                        <table class="ui-table w-full">
                            <thead>
                                <tr>
                                    <th>Region</th>
                                    <th style="text-align:right">Terminals</th>
                                    <th style="text-align:right">Progress</th>
                                    <th style="text-align:right">Avg Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($progressData['regional_performance']) && $progressData['regional_performance']->count() > 0)
                                    @foreach($progressData['regional_performance'] as $region)
                                    @php $rate = $region->completion_rate; @endphp
                                    <tr>
                                        <td>{{ $region->region ?? 'Unknown' }}</td>
                                        <td class="cw-num">{{ $region->total_terminals }}</td>
                                        <td class="cw-num"><span class="badge {{ $rate >= 95 ? 'badge-green' : ($rate >= 80 ? 'badge-blue' : 'badge-gray') }}">{{ $rate }}%</span></td>
                                        <td class="cw-num">{{ number_format($region->avg_duration / 60, 1) }} hrs</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4" class="cw-muted" style="text-align:center">Regional data calculated from terminal locations</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h3>Team Performance Metrics</h3></div>
                    @if(isset($progressData['team_metrics']))
                    @php $teamRate = $progressData['team_metrics']['completion_rate'] ?? 0; @endphp
                    <div class="cw-rows">
                        <div class="cw-row"><span>Total Assignments</span><strong>{{ $progressData['team_metrics']['total_assignments'] ?? 0 }}</strong></div>
                        <div class="cw-row"><span>Completed</span><strong>{{ $progressData['team_metrics']['completed_assignments'] ?? 0 }}</strong></div>
                        <div class="cw-row"><span>Team Progress Rate</span><span class="badge {{ $teamRate >= 90 ? 'badge-green' : 'badge-blue' }}">{{ $teamRate }}%</span></div>
                        <div class="cw-row"><span>Unique Technicians</span><strong>{{ $progressData['team_metrics']['unique_technicians'] ?? 0 }} technicians</strong></div>
                    </div>
                    @else
                    <div class="cw-body cw-muted">Team metrics calculated from job assignments and visit data.</div>
                    @endif
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Issues &amp; Recommendations</h3></div>
                <div class="cw-body">
                    <div class="cw-two" style="gap:14px">
                        <div class="cw-field">
                            <label for="issues_found" class="ui-label">Technical Issues Discovered</label>
                            <textarea class="ui-input" id="issues_found" name="issues_found" rows="3"
                                      placeholder="Summarize any technical issues found during terminal visits..."></textarea>
                        </div>
                        <div class="cw-field">
                            <label for="recommendations" class="ui-label">Recommendations for Client</label>
                            <textarea class="ui-input" id="recommendations" name="recommendations" rows="3"
                                      placeholder="Provide recommendations for terminal maintenance, upgrades, or operational improvements..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 4: Closure Finalization --}}
    <div class="wizard-step" id="step4">
        <div class="cw-stack">
            <div class="cw-two">
                <div class="ui-card">
                    <div class="ui-card-header"><h3>Closure Confirmation</h3></div>
                    <div class="cw-body">
                        <div class="alert-info cw-callout">
                            <strong>Project Closure</strong><br>
                            You are about to close this project. This action will:
                            <ul>
                                <li>Change the project status based on your selected reason</li>
                                <li>Generate a closure report with your summary and findings</li>
                                <li>Archive the project for future reference</li>
                                <li>Preserve all assignment and visit data</li>
                            </ul>
                        </div>
                        <div class="cw-field">
                            <label for="additional_notes" class="ui-label">Additional Notes for Report</label>
                            <textarea class="ui-input" id="additional_notes" name="additional_notes" rows="3"
                                      placeholder="Any additional information to include in the closure documentation..."></textarea>
                        </div>
                        <div class="closure-checklist">
                            <h6>Please confirm:</h6>
                            <label class="cw-check" for="confirm1"><input type="checkbox" id="confirm1" required> I have reviewed the project status and outcomes</label>
                            <label class="cw-check" for="confirm2"><input type="checkbox" id="confirm2" required> All relevant information has been documented</label>
                            <label class="cw-check" for="confirm3"><input type="checkbox" id="confirm3" required> I understand this action will close the project</label>
                        </div>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h3>Project Closure Summary</h3></div>
                    <div class="cw-rows">
                        <div class="cw-row"><span>Project Duration</span><strong>{{ $project->start_date ? (int) $project->start_date->diffInDays(now()) : 'N/A' }} days</strong></div>
                        <div class="cw-row"><span>Total Terminals</span><strong>{{ $progressData['total_terminals'] ?? 0 }}</strong></div>
                        <div class="cw-row"><span>Progress Made</span><strong>{{ number_format($progressData['completion_percentage'] ?? 0, 1) }}%</strong></div>
                        <div class="cw-row"><span>Total Assignments</span><strong>{{ $progressData['total_assignments'] ?? 0 }}</strong></div>
                        <div class="cw-row"><span>Final Status</span><span class="badge badge-blue">Ready for Closure</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="navigation">
        <button type="button" class="btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Previous
        </button>
        <span></span>
        <div class="cw-nav-right">
            <button type="button" class="btn-primary" id="nextBtn" onclick="changeStep(1)">
                Next <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-right"/></svg>
            </button>
            <button type="submit" class="btn-danger" id="submitBtn" style="display: none;">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-lock"/></svg> Close Project
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 4;

function changeStep(direction) {
    document.getElementById(`step${currentStep}`).classList.remove('active');
    const stepItems = document.querySelectorAll('.step-item');
    stepItems[currentStep - 1].classList.remove('active');

    currentStep += direction;

    document.getElementById(`step${currentStep}`).classList.add('active');
    stepItems[currentStep - 1].classList.add('active');

    document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-flex';
    document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-flex';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-flex' : 'none';

    stepItems.forEach((item, index) => {
        item.classList.toggle('completed', index < currentStep - 1);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('closureWizard').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    if (!submitBtn) return;
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;
});
</script>
@endpush
