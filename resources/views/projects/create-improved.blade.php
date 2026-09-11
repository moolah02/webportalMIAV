@extends('layouts.app')
@section('title', 'New Project')

@section('header-actions')
<a href="{{ route('projects.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Projects</a>
@endsection

@push('styles')
<style>
[x-cloak] { display: none !important; }
.np-steps { display: flex; align-items: center; gap: 0; padding: 14px 20px; border-bottom: 1px solid var(--mv-line); overflow-x: auto; }
.np-step { display: flex; align-items: center; gap: 8px; flex-shrink: 0; font-size: 13px; color: var(--mv-muted); }
.np-step b { width: 22px; height: 22px; border-radius: 50%; border: 1px solid var(--mv-line-strong); background: var(--mv-surface); color: var(--mv-muted); font-size: 11.5px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; }
.np-step.is-current { color: var(--mv-accent-ink); font-weight: 600; }
.np-step.is-current b { background: var(--mv-accent); border-color: var(--mv-accent); color: #fff; }
.np-step-line { flex: 1; min-width: 24px; height: 1px; background: var(--mv-line); margin: 0 12px; }
.np-section { padding: 18px 20px; border-bottom: 1px solid var(--mv-line); }
.np-section-title { font-size: 11.5px; font-weight: 600; color: var(--mv-muted); letter-spacing: .06em; text-transform: uppercase; margin: 0 0 12px; }
.np-field { margin-bottom: 14px; }
.np-field:last-child { margin-bottom: 0; }
.np-field .ui-input, .np-field .ui-select, .np-grid .ui-input, .np-grid .ui-select { width: 100%; }
.np-hint { font-size: 12px; color: var(--mv-muted); margin-top: 5px; }
.np-req { color: var(--mv-crit); }
.np-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
.np-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
.np-span-2 { grid-column: span 2; }
@media (max-width: 760px) { .np-grid, .np-grid-2 { grid-template-columns: 1fr; } .np-span-2 { grid-column: auto; } }
.np-client-info { margin-top: 10px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; padding: 12px 14px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); }
.np-client-info .v { font-size: 16px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.np-client-info .v.is-good { color: var(--mv-good); }
.np-client-info .l { font-size: 12px; color: var(--mv-muted); }
.np-types { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
@media (max-width: 900px) { .np-types { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.project-type-card { padding: 12px 14px; border: 1px solid var(--mv-line-strong); border-radius: 9px; cursor: pointer; background: var(--mv-surface); transition: border-color .12s ease, background-color .12s ease; }
.project-type-card:hover { border-color: var(--mv-accent); }
.project-type-card.selected { border-color: var(--mv-accent); background: var(--mv-accent-soft); box-shadow: inset 0 0 0 1px var(--mv-accent); }
.project-type-card .mv-i { color: var(--mv-ink-2); margin-bottom: 8px; }
.project-type-card.selected .mv-i { color: var(--mv-accent-ink); }
.np-type-title { font-size: 13px; font-weight: 600; color: var(--mv-ink); }
.np-type-sub { font-size: 12px; color: var(--mv-muted); margin-top: 1px; }
.np-optional > summary { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; cursor: pointer; list-style: none; user-select: none; }
.np-optional > summary::-webkit-details-marker { display: none; }
.np-optional > summary:hover { background: var(--mv-surface-2); }
.np-optional > summary .np-section-title { margin: 0; }
.np-optional-meta { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--mv-muted); }
.np-optional-meta .mv-i { transition: transform .2s ease; }
.np-optional[open] .np-optional-meta .mv-i { transform: rotate(180deg); }
.np-optional { border-bottom: 1px solid var(--mv-line); }
.np-optional-body { padding: 4px 20px 18px; }
.np-budget-presets { display: flex; gap: 6px; margin-bottom: 8px; }
.np-footer { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; }
.np-errors { margin: 16px 20px 0; padding: 11px 14px; font-size: 13px; border: 1px solid; }
.np-errors ul { margin: 6px 0 0 18px; list-style: disc; }
</style>
@endpush

@section('content')
<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@php $preselectedClient = (string) old('client_id', request('client_id', '')); @endphp

<div
     x-data="{
         step: 1,
         selectedClient: '{{ $preselectedClient }}',
         clientInfo: null,
         selectedProjectType: '',
         otherProjectType: '',
         startDate: '{{ date('Y-m-d') }}',
         durationDays: 30,
         budget: null,
         budgetTemplate: '',

         clientInfoMap: @js($clientInfo ?? []),

         loadClientInfo(clientId) {
             // Figures come with the page (see ProjectController::createImproved).
             this.clientInfo = clientId ? (this.clientInfoMap[clientId] || null) : null;
         },

         selectProjectType(type) {
             this.selectedProjectType = type;
         },

         applyBudgetTemplate(amount) {
             this.budget = amount;
             this.budgetTemplate = amount;
         },

         calculateEndDate() {
             if (!this.startDate || !this.durationDays) return '';
             const start = new Date(this.startDate);
             start.setDate(start.getDate() + parseInt(this.durationDays));
             return start.toISOString().split('T')[0];
         },

         dummy: null
     }"
     x-init="if (selectedClient) loadClientInfo(selectedClient)">

    <div class="ui-card">
        <div class="np-steps" aria-label="Project workflow">
            <div class="np-step is-current"><b>1</b> Basic Setup</div>
            <div class="np-step-line"></div>
            <div class="np-step"><b>2</b> Assign Technicians</div>
            <div class="np-step-line"></div>
            <div class="np-step"><b>3</b> Track Progress</div>
            <div class="np-step-line"></div>
            <div class="np-step"><b>4</b> Close Project</div>
        </div>

        <div class="ui-card-header">
            <div>
                <h3>Create New Project</h3>
                <div class="np-hint" style="margin-top:2px">Quick setup — only 3 required fields</div>
            </div>
        </div>

        <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if ($errors->any())
            <div class="alert-danger np-errors">
                <strong>Please fix these errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Section 1: Project Basics --}}
            <div class="np-section">
                <p class="np-section-title">Project Basics</p>
                <div class="np-field">
                    <label class="ui-label">Project Name <span class="np-req">*</span></label>
                    <input type="text" class="ui-input" name="project_name" value="{{ old('project_name') }}"
                           placeholder="e.g., Q1 2026 Terminal Maintenance - Harare" required style="max-width:620px">
                    <div class="np-hint">Include period, type, and location for clarity</div>
                </div>
                <div class="np-field">
                    <label class="ui-label">Client <span class="np-req">*</span></label>
                    <select class="ui-select" id="client_id" name="client_id" style="max-width:620px"
                            x-model="selectedClient" @change="loadClientInfo($event.target.value)" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $preselectedClient === (string) $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }} ({{ $client->pos_terminals_count }} terminals)
                        </option>
                        @endforeach
                    </select>
                    <div x-show="clientInfo" x-cloak class="np-client-info" style="max-width:620px">
                        <div><div class="v" x-text="clientInfo?.total_terminals || 0"></div><div class="l">Total Terminals</div></div>
                        <div><div class="v is-good" x-text="clientInfo?.active_terminals || 0"></div><div class="l">Active</div></div>
                        <div><div class="v" x-text="clientInfo?.primary_region || '--'"></div><div class="l">Primary Region</div></div>
                    </div>
                </div>
                <div class="np-field">
                    <label class="ui-label">Project Type <span class="np-req">*</span></label>
                    <div class="np-types">
                        <div @click="selectProjectType('maintenance')" class="project-type-card"
                             :class="selectedProjectType === 'maintenance' ? 'selected' : ''">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-wrench"/></svg>
                            <div class="np-type-title">Maintenance & Repairs</div>
                            <div class="np-type-sub">Regular upkeep and servicing</div>
                            <input type="radio" name="_project_type_radio" value="maintenance" :checked="selectedProjectType === 'maintenance'" hidden>
                        </div>
                        <div @click="selectProjectType('installation')" class="project-type-card"
                             :class="selectedProjectType === 'installation' ? 'selected' : ''">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg>
                            <div class="np-type-title">Installation & Setup</div>
                            <div class="np-type-sub">New terminal deployment</div>
                            <input type="radio" name="_project_type_radio" value="installation" :checked="selectedProjectType === 'installation'" hidden>
                        </div>
                        <div @click="selectProjectType('support')" class="project-type-card"
                             :class="selectedProjectType === 'support' ? 'selected' : ''">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-message"/></svg>
                            <div class="np-type-title">Support & Troubleshooting</div>
                            <div class="np-type-sub">Issue resolution</div>
                            <input type="radio" name="_project_type_radio" value="support" :checked="selectedProjectType === 'support'" hidden>
                        </div>
                        <div @click="selectProjectType('other')" class="project-type-card"
                             :class="selectedProjectType === 'other' ? 'selected' : ''">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg>
                            <div class="np-type-title">Other</div>
                            <div class="np-type-sub">Specify below</div>
                            <input type="radio" name="_project_type_radio" value="other" :checked="selectedProjectType === 'other'" hidden>
                        </div>
                    </div>
                    <div style="margin-top:10px;max-width:620px" x-show="selectedProjectType === 'other'" x-cloak>
                        <input type="text" class="ui-input" style="width:100%" placeholder="Describe the project type…"
                               x-model="otherProjectType" maxlength="100">
                    </div>
                    <input type="hidden" name="project_type"
                           :value="selectedProjectType === 'other' ? otherProjectType : selectedProjectType">
                </div>
            </div>

            {{-- Section 2: Timeline --}}
            <div class="np-section">
                <p class="np-section-title">Timeline</p>
                <div class="np-grid">
                    <div>
                        <label class="ui-label">Start Date</label>
                        <input type="date" class="ui-input" name="start_date" x-model="startDate"
                               :min="new Date().toISOString().split('T')[0]" value="{{ date('Y-m-d') }}">
                        <div class="np-hint">Pre-filled with today's date</div>
                    </div>
                    <div>
                        <label class="ui-label">Duration (Days)</label>
                        <input type="number" class="ui-input" name="duration_days"
                               x-model="durationDays" min="1" placeholder="e.g. 30">
                        <input type="hidden" name="end_date" :value="calculateEndDate()">
                    </div>
                    <div>
                        <label class="ui-label">End Date</label>
                        <input type="text" class="ui-input" style="background:var(--mv-surface-2)" readonly :value="calculateEndDate()">
                        <div class="np-hint">Auto-calculated</div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Optional Fields (collapsible) --}}
            <details class="np-optional">
                <summary>
                    <p class="np-section-title">Optional Fields</p>
                    <span class="np-optional-meta">
                        Budget · Manager · Priority · Notes
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-chevron-down"/></svg>
                    </span>
                </summary>
                <div class="np-optional-body np-grid-2">
                    <div>
                        <label class="ui-label">Budget (USD)</label>
                        <div class="np-budget-presets">
                            <button type="button" class="btn-secondary btn-sm" @click="applyBudgetTemplate(5000)">~$5K</button>
                            <button type="button" class="btn-secondary btn-sm" @click="applyBudgetTemplate(15000)">~$15K</button>
                            <button type="button" class="btn-secondary btn-sm" @click="applyBudgetTemplate(50000)">~$50K</button>
                        </div>
                        <input type="number" class="ui-input" name="budget" x-model="budget" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div>
                        <label class="ui-label">Project Manager</label>
                        <select class="ui-select" name="project_manager_id">
                            <option value="">Assign Later (Optional)</option>
                            @foreach($projectManagers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ui-label">Priority Level</label>
                        <select class="ui-select" name="priority">
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                    <div class="np-span-2">
                        <label class="ui-label">Project Description</label>
                        <textarea class="ui-input" style="width:100%" name="description" rows="2"
                                  placeholder="Objectives, scope, deliverables…">{{ old('description') }}</textarea>
                    </div>
                    <div class="np-span-2">
                        <label class="ui-label">Additional Notes</label>
                        <textarea class="ui-input" style="width:100%" name="notes" rows="2"
                                  placeholder="Special requirements or constraints…">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </details>

            {{-- Section 4: Terminal Assignment --}}
            <div class="np-section">
                @include('projects.partials.terminal-upload-section')
            </div>

            <div class="np-footer">
                <a href="{{ route('projects.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Project & Continue <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-right"/></svg></button>
            </div>
        </form>
    </div>
</div>

@endsection
