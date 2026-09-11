@extends('layouts.app')
@section('title', 'Terminal Deployment')

@section('header-actions')
    <span class="dp-meta"><span class="dp-count">{{ $stats['active_projects'] }}</span> active projects</span>
@endsection

@push('styles')
<style>
/* Terminal Deployment — page styles (tokens from miav-shell.css) */
.dp-page { display: flex; flex-direction: column; gap: 16px; }
.dp-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
.dp-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 13px 18px; border-bottom: 1px solid var(--mv-line); min-height: 52px; }
.dp-card-title { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); display: flex; align-items: center; gap: 8px; }
.dp-card-sub { margin: 2px 0 0; font-size: 12.5px; color: var(--mv-muted); }
.dp-card-body { padding: 16px 18px; }
.dp-muted { color: var(--mv-muted); }
.dp-meta { font-size: 12.5px; color: var(--mv-muted); white-space: nowrap; }
.dp-count { font-size: 12px; font-weight: 500; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 6px; padding: 0 7px; line-height: 1.7; font-variant-numeric: tabular-nums; }

/* Stepper */
.dp-stepper { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); padding: 14px 18px; gap: 0; }
.step-item { position: relative; display: flex; gap: 10px; align-items: flex-start; padding-right: 18px; color: var(--mv-muted); }
.step-item + .step-item { border-left: 1px solid var(--mv-line); padding-left: 16px; }
.step-circle { position: relative; z-index: 1; width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0; display: grid; place-items: center;
    font-size: 12px; font-weight: 600; background: var(--mv-surface); color: var(--mv-muted); border: 1px solid var(--mv-line-strong); box-shadow: 0 0 0 4px var(--mv-surface); }
.dp-step-text { position: relative; z-index: 1; background: var(--mv-surface); padding-right: 10px; min-width: 0; }
.dp-step-text strong { display: block; font-size: 13px; font-weight: 600; color: var(--mv-ink-2); line-height: 24px; }
.dp-step-text span { display: block; font-size: 12px; color: var(--mv-muted); line-height: 1.4; }
.step-item.active .step-circle { background: var(--mv-accent); border-color: var(--mv-accent); color: #FFFFFF; }
.step-item.active .dp-step-text strong { color: var(--mv-accent-ink); }
.step-item.completed .step-circle { background: var(--mv-good-soft); border-color: #C6E6D2; color: var(--mv-good); }
.step-item.completed .dp-step-text strong { color: var(--mv-ink); }

/* Setup */
.dp-setup { position: relative; z-index: 20; }
.dp-setup-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 2fr) minmax(0, 1.2fr); gap: 18px; align-items: start; padding: 16px 18px 18px; }
.dp-label { display: block; margin-bottom: 6px; font-size: 12.5px; font-weight: 500; color: var(--mv-ink-2); }
.dp-req { color: var(--mv-crit); }
.dp-help { color: var(--mv-muted); font-size: 12px; margin-top: 6px; }
.dp-kpi { border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); padding: 7px 12px; display: flex; align-items: baseline; justify-content: space-between; gap: 8px; }
.dp-kpi-number { font-size: 22px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; letter-spacing: -.02em; line-height: 1.2; }
.dp-kpi-label { font-size: 12px; color: var(--mv-muted); }
.dp-stack { display: grid; gap: 8px; margin-top: 8px; }
.dp-stack .btn-primary, .dp-stack .btn-secondary, .dp-full { width: 100%; justify-content: center; }
.dp-input, .deployment-input, .deployment-select { width: 100%; padding: 8px 11px; border: 1px solid var(--mv-line-strong); border-radius: 8px; font: inherit; font-size: 13.5px; background: var(--mv-surface); color: var(--mv-ink); }
.dp-input:focus, .deployment-input:focus, .deployment-select:focus { outline: none; border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
#loadHierarchyBtn:disabled { opacity: .55; cursor: not-allowed; }
#autoLoadIndicator { display: none; font-size: 12px; color: var(--mv-accent-ink); margin-top: 6px; }

/* Custom multi-select dropdowns */
.custom-dropdown { position: relative; width: 100%; }
.dropdown-selected { display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 8px 11px; border: 1px solid var(--mv-line-strong); border-radius: 8px; background: var(--mv-surface); cursor: pointer; font-size: 13.5px; color: var(--mv-ink); }
.dropdown-selected > span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dropdown-selected:hover { border-color: var(--mv-ink-2); }
.dropdown-selected.active { border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
.dropdown-arrow { display: grid; place-items: center; color: var(--mv-muted); transition: transform .15s ease; }
.dropdown-selected.active .dropdown-arrow { transform: rotate(180deg); }
.dropdown-options { display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 1000; max-height: 280px; overflow-y: auto; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 8px; box-shadow: 0 12px 32px rgba(22, 32, 44, .12); }
.dropdown-options.show { display: block; }
.dropdown-search { position: sticky; top: 0; padding: 8px; background: var(--mv-surface); border-bottom: 1px solid var(--mv-line); }
.dropdown-search input { width: 100%; padding: 6px 9px; border: 1px solid var(--mv-line-strong); border-radius: 6px; font: inherit; font-size: 13px; }
.dropdown-search input:focus { outline: none; border-color: var(--mv-accent); }
.dropdown-option { display: flex; align-items: center; gap: 9px; padding: 8px 12px; cursor: pointer; font-size: 13px; color: var(--mv-ink-2); }
.dropdown-option:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.dropdown-option.disabled { color: var(--mv-muted); cursor: default; }
.dropdown-option.disabled:hover { background: transparent; }
.dropdown-option input[type="checkbox"] { accent-color: var(--mv-accent); margin: 0; }
.dropdown-option span { flex: 1; }
.dropdown-option small { color: var(--mv-warn); }

/* Progress strip (display toggled to grid by JS) */
.deployment-progress-stats { display: none; grid-template-columns: repeat(5, minmax(0, 1fr)); background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
.deployment-progress-stat { padding: 12px 18px; }
.deployment-progress-stat + .deployment-progress-stat { border-left: 1px solid var(--mv-line); }
.deployment-progress-label { font-size: 12.5px; color: var(--mv-muted); display: flex; align-items: center; gap: 7px; margin-bottom: 3px; }
.deployment-progress-value { font-size: 20px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; letter-spacing: -.02em; line-height: 1.2; }
.dp-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--mv-line-strong); }
.dp-dot.is-accent { background: var(--mv-accent); }
.dp-dot.is-good { background: var(--mv-good); }
.dp-dot.is-warn { background: #C28A2C; }

/* Main working area (display toggled to block by JS) */
.main-content-section { display: none; }
.dp-work-side { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; align-items: start; margin-top: 16px; }

/* Terminal table */
.dp-table-head { padding: 13px 18px; border-bottom: 1px solid var(--mv-line); }
.dp-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
.dp-toolbar-actions { display: flex; gap: 6px; }
.dp-filter-grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 10px; }
.dp-table-wrap { max-height: 600px; overflow: auto; }
#terminalTable { width: 100%; border-collapse: collapse; font-size: 13px; }
#terminalTable th { position: sticky; top: 0; z-index: 2; background: var(--mv-surface-2); color: var(--mv-muted); font-size: 11.5px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; text-align: left; padding: 9px 12px; border-bottom: 1px solid var(--mv-line); white-space: nowrap; }
#terminalTable th.dp-c, #terminalTable td.dp-c { text-align: center; }
#terminalTable td { padding: 9px 12px; border-bottom: 1px solid var(--mv-line); color: var(--mv-ink-2); vertical-align: middle; }
#terminalTable input[type="checkbox"] { accent-color: var(--mv-accent); cursor: pointer; }
.terminal-row { cursor: pointer; }
.terminal-row:hover { background: var(--mv-surface-2); }
.terminal-row.selected { background: var(--mv-accent-soft); }
.dp-tid { font-family: var(--mv-mono); font-size: 12.5px; color: var(--mv-ink); }
.dp-merchant { color: var(--mv-ink); font-weight: 500; }
.dp-row-btn { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border: 1px solid var(--mv-line-strong); border-radius: 7px; background: var(--mv-surface); color: var(--mv-ink-2); font: inherit; font-size: 12.5px; font-weight: 500; cursor: pointer; }
.dp-row-btn:hover:not(:disabled) { background: var(--mv-accent-soft); border-color: #C9D9EE; color: var(--mv-accent-ink); }
.dp-row-btn:disabled { opacity: .55; cursor: default; }
.dp-row-btn.is-danger { color: var(--mv-crit); border-color: #EBC3C3; }
.dp-row-btn.is-danger:hover { background: var(--mv-crit-soft); color: var(--mv-crit); border-color: #EBC3C3; }
.dp-empty { padding: 44px 20px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
.dp-empty .mv-i { width: 26px; height: 26px; color: var(--mv-line-strong); display: block; margin: 0 auto 10px; }
.dp-empty h5, .dp-empty h6 { margin: 0 0 3px; font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
.dp-empty p { margin: 0; }
.dp-empty ul { list-style: none; margin: 10px 0 0; padding: 0; font-size: 12.5px; }
#tablePagination { display: none; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 16px; border-top: 1px solid var(--mv-line); font-size: 13px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
#paginationButtons { display: flex; gap: 4px; flex-wrap: wrap; }
#paginationButtons .btn, #paginationButtons button { min-width: 32px; height: 30px; padding: 0 10px; border: 1px solid var(--mv-line); border-radius: 7px; background: var(--mv-surface); color: var(--mv-ink-2); font-size: 12.5px; font-weight: 500; box-shadow: none; }
#paginationButtons .btn:hover, #paginationButtons button:hover { background: var(--mv-surface-2); }
#paginationButtons .btn.btn-primary { background: var(--mv-accent) !important; border-color: var(--mv-accent) !important; color: #FFFFFF !important; }

/* Status chips (classes set by JS) */
.status-badge, .assignment-badge, .dp-chip { display: inline-flex; align-items: center; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; line-height: 1.7; white-space: nowrap; text-transform: capitalize;
    background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }
.status-active, .assignment-assigned, .dp-chip.is-good { background: var(--mv-good-soft); color: var(--mv-good); border-color: transparent; }
.status-offline, .status-faulty, .dp-chip.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); border-color: transparent; }
.status-maintenance, .dp-chip.is-warn { background: var(--mv-warn-soft); color: var(--mv-warn); border-color: transparent; }
.dp-chip.is-accent { background: var(--mv-accent-soft); color: var(--mv-accent-ink); border-color: transparent; }
/* status-unknown and assignment-unassigned stay neutral */

/* Side cards */
.dp-field { margin-bottom: 14px; }
.deployment-mode-grid { display: grid; gap: 8px; }
.dp-mode { display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; border: 1px solid var(--mv-line); border-radius: 8px; cursor: pointer; background: var(--mv-surface); }
.dp-mode:hover { background: var(--mv-surface-2); }
.dp-mode:has(input:checked) { border-color: #C9D9EE; background: var(--mv-accent-soft); }
.dp-mode input { accent-color: var(--mv-accent); margin-top: 3px; }
.dp-mode strong { display: block; font-size: 13px; font-weight: 500; color: var(--mv-ink); }
.dp-mode span { display: block; font-size: 12px; color: var(--mv-muted); }
.dp-option-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
.dp-actions { display: grid; gap: 8px; padding-top: 14px; border-top: 1px solid var(--mv-line); }
.dp-actions-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.dp-actions button { justify-content: center; }
.dp-actions button:disabled { opacity: .55; cursor: not-allowed; }
.workload-item { padding: 10px 0; border-bottom: 1px solid var(--mv-line); }
.workload-item:first-child { padding-top: 0; }
.workload-item:last-child { border-bottom: 0; padding-bottom: 0; }
.technician-name { font-size: 13px; font-weight: 500; color: var(--mv-ink); }
.dp-workload-row { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: 3px; font-size: 12px; color: var(--mv-muted); }
.dp-workload-row > span:last-child { display: inline-flex; align-items: center; gap: 8px; font-variant-numeric: tabular-nums; }
.dp-list { max-height: 260px; overflow-y: auto; }
.unassigned-item { padding: 8px 10px; border: 1px solid var(--mv-line); border-radius: 8px; margin-bottom: 6px; font-size: 13px; color: var(--mv-ink); cursor: pointer; }
.unassigned-item:hover { background: var(--mv-surface-2); border-color: var(--mv-line-strong); }
.unassigned-item small { color: var(--mv-muted); }
.unassigned-item .dp-tid { font-size: 12px; color: var(--mv-ink-2); }
.dp-more { text-align: center; padding: 6px; font-size: 12.5px; color: var(--mv-muted); }

/* Success / summary (display toggled to block by JS) */
.assignment-success-section { display: none; }
.dp-success-grid { display: grid; grid-template-columns: minmax(0, 3fr) minmax(0, 1fr); gap: 16px; align-items: start; }
.assignment-table { width: 100%; border-collapse: collapse; }
.assignment-table th { background: var(--mv-surface-2); color: var(--mv-muted); font-size: 11.5px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; text-align: left; padding: 9px 12px; border-bottom: 1px solid var(--mv-line); }
.assignment-table td { padding: 10px 12px; border-bottom: 1px solid var(--mv-line); font-size: 13px; color: var(--mv-ink-2); vertical-align: middle; font-variant-numeric: tabular-nums; }
.assignment-table .dp-c { text-align: center; }
.assignment-table tbody tr:last-child td { border-bottom: 0; }
.assignment-table tbody tr:hover { background: var(--mv-surface-2); }
.dp-table-frame { border: 1px solid var(--mv-line); border-radius: 8px; overflow: hidden; }
.dp-summary { margin-top: 14px; border: 1px solid var(--mv-line); border-radius: 8px; padding: 12px 14px; background: var(--mv-surface-2); }
.dp-summary h6 { margin: 0 0 8px; font-size: 12px; font-weight: 600; color: var(--mv-muted); letter-spacing: .04em; text-transform: uppercase; }
.dp-summary-list { display: grid; gap: 6px; font-size: 13px; }
.dp-summary-list > div { display: flex; justify-content: space-between; gap: 8px; color: var(--mv-ink-2); }
.dp-summary-list strong { color: var(--mv-ink); font-weight: 600; font-variant-numeric: tabular-nums; }

/* Info message inserted by JS (project pre-selected) */
.flash-info-lite { display: flex; gap: 12px; align-items: flex-start; background: var(--mv-accent-soft); border: 1px solid #C9D9EE; color: var(--mv-accent-ink); border-radius: 8px; padding: 11px 14px; font-size: 13.5px; }
.flash-info-lite p { margin: 3px 0 0; color: var(--mv-ink-2); }

/* Loading state */
.loading { opacity: .6; pointer-events: none; }

/* Modals */
.dp-overlay { position: fixed; inset: 0; z-index: 1000; justify-content: center; align-items: center; background: rgba(22, 32, 44, .45); padding: 16px; }
.dp-modal { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
.dp-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
.dp-modal-head h3 { margin: 0; font-size: 15px; font-weight: 600; color: var(--mv-ink); }
.dp-x { width: 32px; height: 32px; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); display: grid; place-items: center; cursor: pointer; }
.dp-x:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.dp-modal-body { padding: 16px 18px; }
.dp-modal-body p { margin: 0 0 14px; font-size: 13.5px; color: var(--mv-ink-2); line-height: 1.5; }
.dp-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); border-radius: 0 0 12px 12px; }
.dp-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.dp-choice { width: 100%; display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface); color: var(--mv-ink); font: inherit; font-size: 13.5px; font-weight: 500; cursor: pointer; text-align: left; }
.dp-choice:hover { background: var(--mv-accent-soft); border-color: #C9D9EE; color: var(--mv-accent-ink); }
.dp-choice .mv-i { color: var(--mv-muted); }
.dp-choice small { margin-left: auto; font-weight: 400; font-size: 12px; color: var(--mv-muted); }
.dp-details { margin-top: 12px; padding: 10px 12px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 8px; font-size: 13px; }
.dp-details summary { cursor: pointer; font-weight: 500; color: var(--mv-ink-2); }
.dp-details pre { margin: 8px 0 0; font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); white-space: pre-wrap; }

@media (max-width: 1280px) {
    .dp-setup-grid { grid-template-columns: 1fr 1fr; }
    .dp-filter-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .dp-work-side { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 900px) {
    .dp-stepper { grid-template-columns: 1fr 1fr; row-gap: 12px; }
    .step-item::after { display: none; }
    .dp-setup-grid, .dp-work-side, .dp-success-grid { grid-template-columns: 1fr; }
    .dp-filter-grid { grid-template-columns: 1fr 1fr; }
    .deployment-progress-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .deployment-progress-stat + .deployment-progress-stat { border-left: 0; }
    .dp-option-grid, .dp-form-row { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="dp-page" id="deploymentPage">
    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Steps -->
    <section class="dp-card">
        <div class="dp-stepper" id="stepProgress">
            <div class="step-item active" id="step1">
                <div class="step-circle">1</div>
                <div class="dp-step-text"><strong>Select Scope</strong><span>Choose clients and projects for this deployment batch.</span></div>
            </div>
            <div class="step-item" id="step2">
                <div class="step-circle">2</div>
                <div class="dp-step-text"><strong>Load Hierarchy</strong><span>Pull terminal hierarchy and filter the working set.</span></div>
            </div>
            <div class="step-item" id="step3">
                <div class="step-circle">3</div>
                <div class="dp-step-text"><strong>Pick Technicians</strong><span>Select technicians and define assignment mode.</span></div>
            </div>
            <div class="step-item" id="step4">
                <div class="step-circle">4</div>
                <div class="dp-step-text"><strong>Assign</strong><span>Create assignments and review the final summary.</span></div>
            </div>
        </div>
    </section>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="flash-success" style="border:1px solid; padding:11px 14px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="flash-error" style="border:1px solid; padding:11px 14px;">{{ session('error') }}</div>
    @endif

    <!-- Page Setup - Filters Section -->
    <section class="dp-card dp-setup">
        <div class="dp-card-head">
            <div>
                <h2 class="dp-card-title">Deployment Setup</h2>
                <p class="dp-card-sub">Define the client scope, target projects, and deployment date before loading terminals.</p>
            </div>
            <span class="dp-meta">Step 1 of 4</span>
        </div>

        <div class="dp-setup-grid">
            <!-- Client Selection -->
            <div>
                <span class="dp-label">Select Clients <span class="dp-req">*</span></span>
                <div class="custom-dropdown" id="clientDropdown">
                    <div class="dropdown-selected" onclick="toggleDropdown('clientDropdown')">
                        <span id="clientSelectedText">Choose clients...</span>
                        <i class="dropdown-arrow"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-chevron-down"/></svg></i>
                    </div>
                    <div class="dropdown-options" id="clientOptions">
                        <div class="dropdown-search">
                            <input type="text" placeholder="Search clients..." onkeyup="filterOptions('clientOptions', this.value)">
                        </div>
                        @foreach($clients as $client)
                            <label class="dropdown-option">
                                <input type="checkbox" value="{{ $client['id'] }}" data-terminals="{{ $client['terminal_count'] }}" data-name="{{ $client['name'] }}" onchange="updateClientSelection()">
                                <span>{{ $client['name'] }} ({{ $client['terminal_count'] }} terminals)</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="dp-help">Select one or more clients to build the deployment scope.</div>
            </div>

            <!-- Total Terminals Display -->
            <div>
                <span class="dp-label">Total Terminals</span>
                <div class="dp-kpi">
                    <div class="dp-kpi-number" id="totalTerminalCount">0</div>
                    <div class="dp-kpi-label">Selected</div>
                </div>
            </div>

            <!-- Project Selection -->
            <div>
                <span class="dp-label">Projects <span class="dp-req">*</span></span>
                <div class="custom-dropdown" id="projectDropdown">
                    <div class="dropdown-selected" onclick="toggleDropdown('projectDropdown')">
                        <span id="projectSelectedText">Select clients first...</span>
                        <i class="dropdown-arrow"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-chevron-down"/></svg></i>
                    </div>
                    <div class="dropdown-options" id="projectOptions">
                        <div class="dropdown-search">
                            <input type="text" placeholder="Search projects..." onkeyup="filterOptions('projectOptions', this.value)">
                        </div>
                        <div id="projectOptionsList">
                            <div class="dropdown-option disabled">Select clients first...</div>
                        </div>
                    </div>
                </div>
                <div class="dp-stack">
                    <button type="button" class="btn-secondary" onclick="createNewProject()">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Create New Project
                    </button>
                </div>
                <div class="dp-help">Need a fresh deployment project? Create it here and continue.</div>
            </div>

            <!-- Start Date -->
            <div>
                <label class="dp-label" for="deploymentDate">Deployment Date</label>
                <input type="date" id="deploymentDate" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="deployment-input">
                <div class="dp-stack">
                    <button type="button" class="btn-primary" onclick="loadHierarchy()" id="loadHierarchyBtn" disabled>
                        Load Client Terminals
                    </button>
                </div>
                <div id="autoLoadIndicator">Loading terminals...</div>
            </div>
        </div>
    </section>

    <!-- Progress Stats - Hidden Initially -->
    <div id="progressStats" class="progress-section deployment-progress-stats">
        <div class="deployment-progress-stat">
            <div class="deployment-progress-label"><span class="dp-dot"></span>Total Terminals</div>
            <div class="deployment-progress-value" id="totalTerminals">0</div>
        </div>
        <div class="deployment-progress-stat">
            <div class="deployment-progress-label"><span class="dp-dot is-good"></span>Assigned</div>
            <div class="deployment-progress-value" id="assignedTerminals">0</div>
        </div>
        <div class="deployment-progress-stat">
            <div class="deployment-progress-label"><span class="dp-dot is-warn"></span>Unassigned</div>
            <div class="deployment-progress-value" id="unassignedTerminals">0</div>
        </div>
        <div class="deployment-progress-stat">
            <div class="deployment-progress-label"><span class="dp-dot is-accent"></span>Selected</div>
            <div class="deployment-progress-value" id="selectedTerminals">0</div>
        </div>
        <div class="deployment-progress-stat">
            <div class="deployment-progress-label"><span class="dp-dot"></span>Technicians</div>
            <div class="deployment-progress-value" id="technicianCount">0</div>
        </div>
    </div>

    <!-- Main Content Area - Hidden Initially -->
    <div id="mainContentArea" class="main-content-section">

        <!-- Terminal Table -->
        <section class="dp-card">
            <div class="dp-table-head">
                <div class="dp-toolbar">
                    <h2 class="dp-card-title">Terminal List <span class="dp-count" id="terminalCount">0</span></h2>
                    <div class="dp-toolbar-actions">
                        <button class="btn-secondary btn-sm" onclick="selectAllVisible()" disabled id="selectAllBtn" title="Select All Visible">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-square"/></svg> Select All
                        </button>
                        <button class="btn-secondary btn-sm" onclick="clearSelections()" disabled id="clearAllBtn" title="Clear Selections">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg> Clear
                        </button>
                        <button class="btn-secondary btn-sm" onclick="exportTableData()" disabled id="exportBtn" title="Export Data">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Export
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="dp-filter-grid">
                    <div>
                        <label class="dp-label" for="provinceFilter">Province</label>
                        <select id="provinceFilter" onchange="applyFilters()" class="deployment-select">
                            <option value="">All Provinces</option>
                        </select>
                    </div>
                    <div>
                        <label class="dp-label" for="cityFilter">City</label>
                        <select id="cityFilter" onchange="applyFilters()" class="deployment-select">
                            <option value="">All Cities</option>
                        </select>
                    </div>
                    <div>
                        <label class="dp-label" for="regionFilter">Region</label>
                        <select id="regionFilter" onchange="applyFilters()" class="deployment-select">
                            <option value="">All Regions</option>
                        </select>
                    </div>
                    <div>
                        <label class="dp-label" for="assignmentFilter">Assignment Status</label>
                        <select id="assignmentFilter" onchange="applyFilters()" class="deployment-select">
                            <option value="">All Terminals</option>
                            <option value="assigned">Assigned</option>
                            <option value="unassigned">Unassigned</option>
                        </select>
                    </div>
                    <div>
                        <label class="dp-label" for="statusFilter">Status</label>
                        <select id="statusFilter" onchange="applyFilters()" class="deployment-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="offline">Offline</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="faulty">Faulty</option>
                        </select>
                    </div>
                    <div>
                        <label class="dp-label" for="searchFilter">Search</label>
                        <input type="text" id="searchFilter" placeholder="Search terminals..." onkeyup="applyFilters()" class="deployment-input">
                    </div>
                </div>
            </div>

            <!-- Terminal Table -->
            <div class="dp-table-wrap">
                <table id="terminalTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" aria-label="Select all on this page"></th>
                            <th>Terminal ID</th>
                            <th>Merchant Name</th>
                            <th>Province</th>
                            <th>City</th>
                            <th>Region</th>
                            <th class="dp-c">Assignment</th>
                            <th class="dp-c">Status</th>
                            <th class="dp-c">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="terminalTableBody">
                        <tr>
                            <td colspan="9">
                                <div class="dp-empty">
                                    <svg class="mv-i" aria-hidden="true"><use href="#i-arrow-up-right"/></svg>
                                    <h5>Step 1: Configure Deployment Setup</h5>
                                    <p>Select clients and projects to load terminals.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="tablePagination">
                <div>
                    Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="paginationTotal">0</span> terminals
                </div>
                <div id="paginationButtons">
                    <!-- Pagination buttons will be inserted here -->
                </div>
            </div>
        </section>

        <!-- Assignment & Tracking -->
        <div class="dp-work-side">

            <!-- Assignment Section -->
            <section class="dp-card" id="assignmentSection">
                <div class="dp-card-head"><h2 class="dp-card-title">Technician Assignment</h2></div>
                <div class="dp-card-body">
                    <!-- Technician Selection -->
                    <div class="dp-field">
                        <span class="dp-label">Select Technicians <span class="dp-req">*</span></span>
                        <div class="custom-dropdown" id="technicianDropdown">
                            <div class="dropdown-selected" onclick="toggleDropdown('technicianDropdown')">
                                <span id="technicianSelectedText">Choose technicians...</span>
                                <i class="dropdown-arrow"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-chevron-down"/></svg></i>
                            </div>
                            <div class="dropdown-options" id="technicianOptions">
                                <div class="dropdown-search">
                                    <input type="text" placeholder="Search technicians..." onkeyup="filterOptions('technicianOptions', this.value)">
                                </div>
                                @foreach($technicians as $tech)
                                    <label class="dropdown-option">
                                        <input type="checkbox"
                                               value="{{ $tech['id'] }}"
                                               data-name="{{ $tech['name'] }}"
                                               data-spec="{{ $tech['specialization'] }}"
                                               data-availability="{{ $tech['availability_status'] }}"
                                               data-workload="{{ $tech['current_workload'] }}"
                                               onchange="updateTechnicianSelection()">
                                        <span>
                                            {{ $tech['name'] }} - {{ $tech['specialization'] }}
                                            @if($tech['availability_status'] !== 'available')
                                                <small>({{ ucfirst($tech['availability_status']) }})</small>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="dp-help">Select multiple technicians to distribute or share assignments.</div>
                    </div>

                    <!-- Assignment Mode -->
                    <div class="dp-field">
                        <span class="dp-label">Assignment Mode</span>
                        <div class="deployment-mode-grid">
                            <label class="dp-mode" onclick="selectAssignmentMode('individual')">
                                <input type="radio" name="assignmentMode" value="individual" checked>
                                <div>
                                    <strong>Individual Assignment</strong>
                                    <span>Distribute terminals among technicians</span>
                                </div>
                            </label>
                            <label class="dp-mode" onclick="selectAssignmentMode('team')">
                                <input type="radio" name="assignmentMode" value="team">
                                <div>
                                    <strong>Team Assignment</strong>
                                    <span>All technicians work together</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Assignment Options -->
                    <div class="dp-option-grid">
                        <div>
                            <label class="dp-label" for="assignmentPriority">Priority</label>
                            <select id="assignmentPriority" class="deployment-select">
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>
                        <div>
                            <label class="dp-label" for="serviceType">Service Type</label>
                            <select id="serviceType" class="deployment-select">
                                <option value="routine_maintenance">Routine Maintenance</option>
                                <option value="emergency_repair">Emergency Repair</option>
                            </select>
                        </div>
                    </div>

                    <!-- Assignment Actions -->
                    <div class="dp-actions">
                        <button type="button" class="btn-primary" onclick="assignSelected()" id="assignSelectedBtn" disabled>
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Assign Selected Terminals
                        </button>
                        <div class="dp-actions-row">
                            <button type="button" class="btn-secondary" onclick="assignAll()" id="assignAllBtn" disabled>
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-clipboard"/></svg> Assign All
                            </button>
                            <button type="button" class="btn-secondary" onclick="clearAssignments()" id="clearAssignmentsBtn" disabled>
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-trash"/></svg> Clear Assignments
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Technician Workload Display -->
            <section class="dp-card">
                <div class="dp-card-head"><h2 class="dp-card-title">Technician Workload</h2></div>
                <div class="dp-card-body">
                    <div id="technicianWorkload">
                        <div class="dp-empty" style="padding: 20px 8px;">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg>
                            <h6>Step 3: Select Technicians</h6>
                            <p>Choose technicians to see workload distribution</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Unassigned Terminals -->
            <section class="dp-card">
                <div class="dp-card-head">
                    <h2 class="dp-card-title">Unassigned Terminals <span class="dp-count" id="unassignedCount">0</span></h2>
                </div>
                <div class="dp-card-body">
                    <div id="unassignedList" class="dp-list">
                        <div class="dp-empty" style="padding: 20px 8px;">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg>
                            <p>Load hierarchy to see unassigned terminals</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Assignment Success Section - Hidden Initially -->
    <section class="assignment-success-section dp-card" id="assignmentSuccessSection">
        <div class="dp-card-head">
            <h2 class="dp-card-title">
                <svg class="mv-i mv-i-sm" aria-hidden="true" style="color: var(--mv-good);"><use href="#i-check-circle"/></svg> Assignment Complete!
            </h2>
        </div>
        <div class="dp-card-body">
            <div class="dp-success-grid">
                <!-- Assignment Summary Table -->
                <div class="dp-table-frame">
                    <table class="assignment-table">
                        <thead>
                            <tr>
                                <th>Technician</th>
                                <th class="dp-c">Terminals</th>
                                <th class="dp-c">Regions</th>
                                <th class="dp-c">Priority</th>
                                <th class="dp-c">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="assignmentSummaryTable">
                            <tr>
                                <td colspan="5" class="dp-empty" style="padding: 28px;">No assignments yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Export Actions -->
                <div>
                    <div class="dp-actions" style="padding-top: 0; border-top: 0;">
                        <button type="button" class="btn-primary" onclick="exportDeployment()" id="exportDeploymentBtn" disabled>
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Export Assignment Data
                        </button>
                        <button type="button" class="btn-secondary" onclick="saveAsDraft()" id="saveDraftBtn" disabled>
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg> Save as Draft
                        </button>
                        <button type="button" class="btn-secondary" onclick="viewAllAssignments()">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg> View All Assignments
                        </button>
                    </div>

                    <!-- Assignment Stats -->
                    <div class="dp-summary">
                        <h6>Assignment Summary</h6>
                        <div class="dp-summary-list">
                            <div><span>Total Technicians</span><strong id="summaryTechnicians">0</strong></div>
                            <div><span>Total Terminals</span><strong id="summaryTerminals">0</strong></div>
                            <div><span>Estimated Time</span><strong id="summaryTime">0 hours</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Create New Project Modal -->
<div id="createProjectModal" class="dp-overlay" style="display: none;">
    <div class="dp-modal" role="dialog" aria-modal="true" aria-labelledby="createProjectTitle">
        <div class="dp-modal-head">
            <h3 id="createProjectTitle">Create New Project</h3>
            <button type="button" onclick="closeProjectModal()" class="dp-x" aria-label="Close">
                <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
            </button>
        </div>

        <form id="createProjectForm">
            <div class="dp-modal-body">
                <div class="dp-field">
                    <label class="dp-label" for="newProjectName">Project Name <span class="dp-req">*</span></label>
                    <input type="text" id="newProjectName" required class="dp-input" placeholder="e.g., Q1 2025 Terminal Maintenance">
                </div>

                <div class="dp-form-row dp-field">
                    <div>
                        <label class="dp-label" for="newProjectType">Project Type <span class="dp-req">*</span></label>
                        <select id="newProjectType" required class="dp-input">
                            <option value="">Select type...</option>
                            <option value="discovery">Discovery</option>
                            <option value="servicing">Servicing</option>
                            <option value="support">Support</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="installation">Installation</option>
                        </select>
                    </div>
                    <div>
                        <label class="dp-label" for="newProjectDuration">Expected Duration</label>
                        <select id="newProjectDuration" class="dp-input">
                            <option value="1">1 Month</option>
                            <option value="3" selected>3 Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">1 Year</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="dp-label" for="newProjectDescription">Project Description</label>
                    <textarea id="newProjectDescription" rows="3" class="dp-input" style="resize: vertical;"
                              placeholder="Describe the project objectives and scope..."></textarea>
                </div>
            </div>

            <div class="dp-modal-foot">
                <button type="button" onclick="closeProjectModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Create Project
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// CSRF token setup for AJAX requests
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// =====================
// GLOBAL STATE (Updated for Table)
// =====================
let deploymentState = {
    selectedClients: new Set(),
    selectedProjects: new Set(),
    selectedTechnicians: new Set(),
    selectedTerminals: new Set(),
    hierarchyData: [],
    allTerminals: new Map(),
    filteredTerminals: [],
    assignedTerminalIds: new Set(), // Track which terminals are assigned
    assignments: {},
    deploymentDate: null,
    isLoading: false,
    filters: {
        province: '',
        city: '',
        region: '',
        status: '',
        assignment: '', // New assignment filter
        search: ''
    },
    pagination: {
        currentPage: 1,
        itemsPerPage: 50,
        totalPages: 1
    }
};

// =====================
// INITIALIZATION
// =====================
document.addEventListener('DOMContentLoaded', function() {
    console.log('Terminal Deployment initialized');
    console.log('Available clients:', @json($clients));
    console.log('Available technicians:', @json($technicians));

    // Handle pre-selections from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const preSelectedProjectId = urlParams.get('project_id') || @json($preSelectedProjectId ?? null);
    const preSelectedClientId = urlParams.get('client_id') || @json($preSelectedClientId ?? null);

    // Pre-select client if provided
    if (preSelectedClientId) {
        const clientCheckbox = document.querySelector(`#clientOptions input[value="${preSelectedClientId}"]`);
        if (clientCheckbox) {
            clientCheckbox.checked = true;
            updateClientSelection();

            // Load projects for this client and then pre-select the project
            setTimeout(() => {
                if (preSelectedProjectId) {
                    loadProjectsAndSelect(preSelectedProjectId);
                }
            }, 500);
        }
    }

    // Show helpful message if coming from project creation
    if (preSelectedProjectId) {
        showProjectSelectionMessage();
    }

    setupEventListeners();
    updateLoadButton();
    updateProgressiveVisibility();
});

function setupEventListeners() {
    // Deployment date
    document.getElementById('deploymentDate').addEventListener('change', function() {
        deploymentState.deploymentDate = this.value;
    });

    // Assignment mode
    document.querySelectorAll('input[name="assignmentMode"]').forEach(radio => {
        radio.addEventListener('change', updateAssignmentMode);
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.custom-dropdown')) {
            closeAllDropdowns();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeProjectModal();
        }
    });
}

function loadProjectsAndSelect(projectId) {
    // Wait for projects to load, then select the specified project
    const checkForProject = setInterval(() => {
        const projectCheckbox = document.querySelector(`#projectOptionsList input[value="${projectId}"]`);
        if (projectCheckbox) {
            clearInterval(checkForProject);
            projectCheckbox.checked = true;
            updateProjectSelection();
            // Auto-load will trigger from updateProjectSelection()
        }
    }, 100);

    // Stop checking after 5 seconds
    setTimeout(() => clearInterval(checkForProject), 5000);
}

function showProjectSelectionMessage() {
    const messageHtml = `
        <div class="flash-info-lite">
            <svg class="mv-i" aria-hidden="true"><use href="#i-info"/></svg>
            <div>
                <strong>Project Ready for Terminal Assignment</strong>
                <p>Your project has been created successfully. Follow the steps below to assign terminals and technicians to this project.</p>
            </div>
        </div>
    `;

    // Insert the message at the top of the page
    const container = document.getElementById('deploymentPage') || document.querySelector('.container-fluid');
    if (container) {
        container.insertAdjacentHTML('afterbegin', messageHtml);
    }
}

// =====================
// PROGRESSIVE DISCLOSURE FUNCTIONS
// =====================
function updateProgressiveVisibility() {
    const hasClients = deploymentState.selectedClients.size > 0;
    const hasProjects = deploymentState.selectedProjects.size > 0;
    const hasHierarchy = deploymentState.hierarchyData.length > 0;
    const hasTechnicians = deploymentState.selectedTechnicians.size > 0;
    const hasAssignments = Object.keys(deploymentState.assignments).length > 0;
    const hasSelections = deploymentState.selectedTerminals.size > 0;

    // Show/hide sections progressively
    toggleSection('progressStats', hasHierarchy);
    toggleSection('mainContentArea', hasClients && hasProjects);
    toggleSection('assignmentSuccessSection', hasAssignments);

    // Update button states
    updateButtonStates(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments, hasSelections);

    // Update step indicators
    updateStepIndicators(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments);

    // Show helpful hints
    updateHelpfulHints(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments);
}

function toggleSection(sectionId, shouldShow) {
    const section = document.getElementById(sectionId);
    if (section) {
        if (shouldShow && section.style.display === 'none') {
            section.style.display = sectionId === 'progressStats' ? 'grid' : 'block';

            // Add smooth animation
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            setTimeout(() => {
                section.style.transition = 'all 0.3s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 10);
        } else if (!shouldShow) {
            section.style.display = 'none';
        }
    }
}

function updateButtonStates(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments, hasSelections) {
    // Load Hierarchy Button
    const loadBtn = document.getElementById('loadHierarchyBtn');
    loadBtn.disabled = !(hasClients && hasProjects);
    loadBtn.textContent = hasClients && hasProjects ? 'Load Client Terminals' : 'Select Clients & Projects First';

    // Assignment Buttons
    document.getElementById('assignSelectedBtn').disabled = !(hasSelections && hasTechnicians);
    document.getElementById('assignAllBtn').disabled = !(hasHierarchy && hasTechnicians);
    document.getElementById('clearAssignmentsBtn').disabled = !hasAssignments;

    // Hierarchy Control Buttons
    ['expandAllBtn', 'collapseAllBtn', 'selectAllBtn', 'clearAllBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.disabled = !hasHierarchy;
    });

    // Export Section Buttons (moved to assignment success section)
    ['exportDeploymentBtn', 'saveDraftBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.disabled = !hasAssignments;
    });
}

function updateStepIndicators(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments) {
    // Step 1: Clients & Projects
    const step1 = document.getElementById('step1');
    if (hasClients && hasProjects) {
        step1.classList.remove('active');
        step1.classList.add('completed');
    } else if (hasClients || hasProjects) {
        step1.classList.add('active');
        step1.classList.remove('completed');
    }

    // Step 2: Hierarchy
    const step2 = document.getElementById('step2');
    if (hasHierarchy) {
        step2.classList.remove('active');
        step2.classList.add('completed');
    } else if (hasClients && hasProjects) {
        step2.classList.add('active');
        step2.classList.remove('completed');
    }

    // Step 3: Technicians
    const step3 = document.getElementById('step3');
    if (hasTechnicians && hasHierarchy) {
        step3.classList.remove('active');
        step3.classList.add('completed');
    } else if (hasHierarchy) {
        step3.classList.add('active');
        step3.classList.remove('completed');
    }

    // Step 4: Assign
    const step4 = document.getElementById('step4');
    if (hasAssignments) {
        step4.classList.add('completed');
    } else if (hasTechnicians) {
        step4.classList.add('active');
    }
}

function updateHelpfulHints(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments) {
    // Update the hierarchy tree placeholder
    const treeContainer = document.getElementById('hierarchyTree');

    if (!hasClients || !hasProjects) {
        if (treeContainer) {
            treeContainer.innerHTML = `
                <div class="dp-empty">
                    <h5>Step 1: Configure Deployment Setup</h5>
                    <p>Select clients and projects to continue</p>
                    <ul>
                        <li>${!hasClients ? 'Choose one or more clients' : 'Clients selected'}</li>
                        <li>${!hasProjects ? 'Select associated projects' : 'Projects selected'}</li>
                    </ul>
                </div>
            `;
        }
    }

    // Update technician workload placeholder
    if (!hasTechnicians && hasHierarchy) {
        document.getElementById('technicianWorkload').innerHTML = `
            <div class="dp-empty" style="padding: 20px 8px;">
                <h6>Step 3: Select Technicians</h6>
                <p>Choose technicians to see workload distribution</p>
            </div>
        `;
    }
}

// =====================
// CUSTOM DROPDOWN FUNCTIONS (FIXED)
// =====================
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const selected = dropdown.querySelector('.dropdown-selected');
    const options = dropdown.querySelector('.dropdown-options');

    // Close other dropdowns first
    closeAllDropdowns();

    // Toggle current dropdown
    if (options.classList.contains('show')) {
        closeDropdown(dropdownId);
    } else {
        openDropdown(dropdownId);
    }
}

function openDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const selected = dropdown.querySelector('.dropdown-selected');
    const options = dropdown.querySelector('.dropdown-options');

    selected.classList.add('active');
    options.classList.add('show');

    // Focus search input if it exists
    const searchInput = options.querySelector('.dropdown-search input');
    if (searchInput) {
        setTimeout(() => searchInput.focus(), 100);
    }
}

function closeDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const selected = dropdown.querySelector('.dropdown-selected');
    const options = dropdown.querySelector('.dropdown-options');

    selected.classList.remove('active');
    options.classList.remove('show');
}

function closeAllDropdowns() {
    document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
        const selected = dropdown.querySelector('.dropdown-selected');
        const options = dropdown.querySelector('.dropdown-options');
        if (selected && options) {
            selected.classList.remove('active');
            options.classList.remove('show');
        }
    });
}

function filterOptions(optionsId, searchValue) {
    const options = document.getElementById(optionsId);
    const dropdownOptions = options.querySelectorAll('.dropdown-option');

    searchValue = searchValue.toLowerCase();

    dropdownOptions.forEach(option => {
        if (option.classList.contains('disabled')) return;

        const text = option.textContent.toLowerCase();
        if (text.includes(searchValue)) {
            option.style.display = 'flex';
        } else {
            option.style.display = 'none';
        }
    });
}

function updateClientSelection() {
    deploymentState.selectedClients.clear();

    const checkboxes = document.querySelectorAll('#clientOptions input[type="checkbox"]:checked');
    const selectedText = document.getElementById('clientSelectedText');

    if (checkboxes.length === 0) {
        selectedText.textContent = 'Choose clients...';
    } else if (checkboxes.length === 1) {
        selectedText.textContent = checkboxes[0].dataset.name;
    } else {
        selectedText.textContent = `${checkboxes.length} clients selected`;
    }

    checkboxes.forEach(checkbox => {
        deploymentState.selectedClients.add(checkbox.value);
    });

    console.log('Selected clients:', Array.from(deploymentState.selectedClients));
    updateTotalTerminalCount();
    loadProjectsForClients();
    updateLoadButton();
    updateProgressiveVisibility();

    // Auto-close dropdown after selection
    setTimeout(() => closeDropdown('clientDropdown'), 300);
}

function updateTechnicianSelection() {
    deploymentState.selectedTechnicians.clear();

    const checkboxes = document.querySelectorAll('#technicianOptions input[type="checkbox"]:checked');
    const selectedText = document.getElementById('technicianSelectedText');

    if (checkboxes.length === 0) {
        selectedText.textContent = 'Choose technicians...';
    } else if (checkboxes.length === 1) {
        selectedText.textContent = checkboxes[0].dataset.name;
    } else {
        selectedText.textContent = `${checkboxes.length} technicians selected`;
    }

    checkboxes.forEach(checkbox => {
        deploymentState.selectedTechnicians.add(checkbox.value);
    });

    updateTechnicianWorkload();
    updateAssignmentButtons();
    updateProgressiveVisibility();

    if (deploymentState.selectedTechnicians.size > 0) {
        showAlert('Step 3 Complete! Select terminals from the hierarchy to assign them.');
    }

    // Auto-close dropdown after selection
    setTimeout(() => closeDropdown('technicianDropdown'), 300);
}

// =====================
// CLIENT & PROJECT MANAGEMENT
// =====================
function updateTotalTerminalCount() {
    const checkboxes = document.querySelectorAll('#clientOptions input[type="checkbox"]:checked');
    let totalTerminals = 0;

    checkboxes.forEach(checkbox => {
        totalTerminals += parseInt(checkbox.dataset.terminals) || 0;
    });

    document.getElementById('totalTerminalCount').textContent = totalTerminals;
}

function loadProjectsForClients() {
    const projectDropdown = document.getElementById('projectDropdown');
    const projectSelectedText = document.getElementById('projectSelectedText');
    const projectOptionsList = document.getElementById('projectOptionsList');

    if (deploymentState.selectedClients.size === 0) {
        projectSelectedText.textContent = 'Select clients first...';
        projectOptionsList.innerHTML = '<div class="dropdown-option disabled">Select clients first...</div>';
        return;
    }

    // Show loading state
    projectSelectedText.textContent = 'Loading projects...';
    projectOptionsList.innerHTML = '<div class="dropdown-option disabled">Loading projects...</div>';

    const clientIds = Array.from(deploymentState.selectedClients);

    fetch('{{ route("deployment.projects") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({
            client_ids: clientIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            projectSelectedText.textContent = 'Choose projects...';
            projectOptionsList.innerHTML = '';

            if (!data.projects || data.projects.length === 0) {
                projectOptionsList.innerHTML = '<div class="dropdown-option disabled">No projects found for selected clients</div>';
            } else {
                data.projects.forEach(project => {
                    const projectOption = document.createElement('label');
                    projectOption.className = 'dropdown-option';
                    projectOption.innerHTML = `
                        <input type="checkbox" value="${project.id}" data-name="${project.display_name}" data-type="${project.project_type}" onchange="updateProjectSelection()">
                        <span>${project.display_name} (${project.project_type})</span>
                    `;
                    projectOptionsList.appendChild(projectOption);
                });
            }
        } else {
            projectSelectedText.textContent = 'Error loading projects';
            projectOptionsList.innerHTML = '<div class="dropdown-option disabled">Error loading projects</div>';
            showErrorModal(
                'Failed to Load Projects',
                'Unable to load projects for the selected clients. Please try again.',
                data.message || 'Unknown error'
            );
        }
    })
    .catch(error => {
        console.error('Error loading projects:', error);
        projectSelectedText.textContent = 'Error loading projects';
        projectOptionsList.innerHTML = '<div class="dropdown-option disabled">Error loading projects</div>';
        showErrorModal(
            'Network Error',
            'Failed to connect to the server while loading projects. Please check your connection and try again.',
            error.toString()
        );
    });
}

function updateProjectSelection() {
    deploymentState.selectedProjects.clear();

    const checkboxes = document.querySelectorAll('#projectOptionsList input[type="checkbox"]:checked');
    const selectedText = document.getElementById('projectSelectedText');

    if (checkboxes.length === 0) {
        selectedText.textContent = 'Choose projects...';
    } else if (checkboxes.length === 1) {
        selectedText.textContent = checkboxes[0].dataset.name;
    } else {
        selectedText.textContent = `${checkboxes.length} projects selected`;
    }

    checkboxes.forEach(checkbox => {
        deploymentState.selectedProjects.add(checkbox.value);
    });

    console.log('Selected projects:', Array.from(deploymentState.selectedProjects));
    updateProgressiveVisibility();
    updateLoadButton();

    // Auto-close dropdown after selection
    setTimeout(() => closeDropdown('projectDropdown'), 300);
}

function autoLoadTerminalsIfReady() {
    const hasClients = deploymentState.selectedClients.size > 0;
    const hasProjects = deploymentState.selectedProjects.size > 0;

    console.log('Auto-load check:', {
        hasClients,
        hasProjects,
        selectedClients: Array.from(deploymentState.selectedClients),
        selectedProjects: Array.from(deploymentState.selectedProjects)
    });

    // If both clients and projects are selected, automatically load terminals
    if (hasClients && hasProjects) {
        console.log('Auto-loading terminals...');

        // Show loading indicator
        const indicator = document.getElementById('autoLoadIndicator');
        if (indicator) {
            indicator.style.display = 'block';
            console.log('Loading indicator shown');
        }

        // Small delay to show the indicator and close dropdowns
        setTimeout(() => {
            console.log('Calling loadHierarchy()');
            loadHierarchy();
        }, 500);
    } else {
        console.log('Auto-load conditions not met');
    }
}

// Update the load button state
function updateLoadButton() {
    const loadBtn = document.getElementById('loadHierarchyBtn');
    if (!loadBtn) return;

    const hasClients = deploymentState.selectedClients.size > 0;
    const hasProjects = deploymentState.selectedProjects.size > 0;

    if (hasClients && hasProjects) {
        loadBtn.disabled = false;
        loadBtn.textContent = 'Load Client Terminals';
        loadBtn.style.opacity = '1';
        loadBtn.style.cursor = 'pointer';
    } else {
        loadBtn.disabled = true;
        loadBtn.textContent = 'Select Clients & Projects First';
        loadBtn.style.opacity = '0.6';
        loadBtn.style.cursor = 'not-allowed';
    }
}

// =====================
// HIERARCHY MANAGEMENT
// =====================
function loadHierarchy() {
    if (deploymentState.selectedClients.size === 0) {
        showAlert('Please select at least one client', 'danger');
        return;
    }

    if (deploymentState.selectedProjects.size === 0) {
        showAlert('Please select at least one project', 'danger');
        return;
    }

    const tableBody = document.getElementById('terminalTableBody');
    setLoading(tableBody, true);

    tableBody.innerHTML = `
        <tr>
            <td colspan="9">
                <div class="dp-empty"><p>Loading terminals...</p></div>
            </td>
        </tr>
    `;

    const requestData = {
        client_ids: Array.from(deploymentState.selectedClients),
        project_ids: Array.from(deploymentState.selectedProjects)
    };

    fetch('{{ route("deployment.terminals") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Hierarchy loaded successfully:', data);
            deploymentState.hierarchyData = data.hierarchy;
            buildTerminalMap(data.hierarchy);

            setLoading(tableBody, false);
            renderHierarchy();
            updateProgressStats();

            updateProgressiveVisibility();

            // Hide auto-load indicator
            const indicator = document.getElementById('autoLoadIndicator');
            if (indicator) {
                indicator.style.display = 'none';
            }

            showAlert('Step 2 Complete! Now select technicians to start assigning terminals.');
        } else {
            setLoading(tableBody, false);

            // Hide auto-load indicator even on failure
            const indicator = document.getElementById('autoLoadIndicator');
            if (indicator) {
                indicator.style.display = 'none';
            }
            showErrorModal(
                'Failed to Load Terminals',
                'Unable to load terminal hierarchy. Please check your selections and try again.',
                data.message || 'Unknown error'
            );
        }
    })
    .catch(error => {
        console.error('Error loading terminals:', error);
        setLoading(tableBody, false);

        // Hide auto-load indicator on error
        const indicator = document.getElementById('autoLoadIndicator');
        if (indicator) {
            indicator.style.display = 'none';
        }

        showErrorModal(
            'Network Error',
            'Failed to connect to the server while loading terminals. Please check your connection and try again.',
            error.toString()
        );
    });
}

function buildTerminalMap(hierarchy) {
    console.log('Building terminal map from hierarchy:', hierarchy);
    deploymentState.allTerminals.clear();

    const terminals = [];

    const processNode = (node) => {
        if (node.type === 'terminal') {
            const terminalId = node.id.replace('terminal-', '');
            deploymentState.allTerminals.set(terminalId, node);
            terminals.push(node);
            return;
        }

        if (node.children) {
            node.children.forEach(processNode);
        }

        if (node.terminals) {
            node.terminals.forEach(processNode);
        }
    };

    hierarchy.forEach(processNode);

    // Get assigned terminal IDs
    refreshAssignedTerminals().then(() => {
        // Populate filter options
        populateFilterOptions(terminals);

        // Initialize filtered terminals
        deploymentState.filteredTerminals = terminals;

        console.log('Final terminal map size:', deploymentState.allTerminals.size);
    });
}

function refreshAssignedTerminals() {
    // Scope to the currently selected project so only project-relevant assignments affect the display
    const projectId = deploymentState.selectedProjects.size > 0
        ? Array.from(deploymentState.selectedProjects)[0]
        : null;

    const url = projectId
        ? `{{ route("deployment.assigned-terminals") }}?project_id=${encodeURIComponent(projectId)}`
        : '{{ route("deployment.assigned-terminals") }}';

    return fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Normalize to strings and filter to only terminals in the current project view
            const currentTerminalIds = new Set(
                Array.from(deploymentState.allTerminals.keys()).map(String)
            );
            deploymentState.assignedTerminalIds = new Set(
                (data.assigned_terminal_ids || [])
                    .map(String)
                    .filter(id => currentTerminalIds.size === 0 || currentTerminalIds.has(id))
            );
        }
    })
    .catch(error => {
        console.error('Error loading assigned terminals:', error);
        deploymentState.assignedTerminalIds = new Set();
    });
}

function populateFilterOptions(terminals) {
    const provinces = new Set();
    const cities = new Set();
    const regions = new Set();

    terminals.forEach(terminal => {
        if (terminal.province) provinces.add(terminal.province);
        if (terminal.city) cities.add(terminal.city);
        if (terminal.area) regions.add(terminal.area);
    });

    // Populate Province filter
    const provinceFilter = document.getElementById('provinceFilter');
    provinceFilter.innerHTML = '<option value="">All Provinces</option>';
    Array.from(provinces).sort().forEach(province => {
        provinceFilter.innerHTML += `<option value="${province}">${province}</option>`;
    });

    // Populate City filter
    const cityFilter = document.getElementById('cityFilter');
    cityFilter.innerHTML = '<option value="">All Cities</option>';
    Array.from(cities).sort().forEach(city => {
        cityFilter.innerHTML += `<option value="${city}">${city}</option>`;
    });

    // Populate Region filter
    const regionFilter = document.getElementById('regionFilter');
    regionFilter.innerHTML = '<option value="">All Regions</option>';
    Array.from(regions).sort().forEach(region => {
        regionFilter.innerHTML += `<option value="${region}">${region}</option>`;
    });
}

function applyFilters() {
    // Get filter values
    deploymentState.filters.province = document.getElementById('provinceFilter').value;
    deploymentState.filters.city = document.getElementById('cityFilter').value;
    deploymentState.filters.region = document.getElementById('regionFilter').value;
    deploymentState.filters.status = document.getElementById('statusFilter').value;
    deploymentState.filters.assignment = document.getElementById('assignmentFilter').value;
    deploymentState.filters.search = document.getElementById('searchFilter').value.toLowerCase();

    // Update dependent filters
    updateDependentFilters();

    // Filter terminals
    const allTerminals = Array.from(deploymentState.allTerminals.values());
    deploymentState.filteredTerminals = allTerminals.filter(terminal => {
        const terminalId = terminal.id.replace('terminal-', '');

        // Province filter
        if (deploymentState.filters.province && terminal.province !== deploymentState.filters.province) {
            return false;
        }

        // City filter
        if (deploymentState.filters.city && terminal.city !== deploymentState.filters.city) {
            return false;
        }

        // Region filter
        if (deploymentState.filters.region && terminal.area !== deploymentState.filters.region) {
            return false;
        }

        // Status filter
        if (deploymentState.filters.status && terminal.status !== deploymentState.filters.status) {
            return false;
        }

        // Assignment filter
        if (deploymentState.filters.assignment) {
            const isAssigned = deploymentState.assignedTerminalIds.has(terminalId);
            if (deploymentState.filters.assignment === 'assigned' && !isAssigned) {
                return false;
            }
            if (deploymentState.filters.assignment === 'unassigned' && isAssigned) {
                return false;
            }
        }

        // Search filter
        if (deploymentState.filters.search) {
            const searchText = (
                terminal.terminal_id + ' ' +
                terminal.merchant_name + ' ' +
                (terminal.province || '') + ' ' +
                (terminal.city || '') + ' ' +
                (terminal.area || '')
            ).toLowerCase();

            if (!searchText.includes(deploymentState.filters.search)) {
                return false;
            }
        }

        return true;
    });

    // Reset pagination
    deploymentState.pagination.currentPage = 1;
    deploymentState.pagination.totalPages = Math.ceil(deploymentState.filteredTerminals.length / deploymentState.pagination.itemsPerPage);

    // Render table
    renderTerminalTable();
    updatePagination();
}

function updateDependentFilters() {
    const selectedProvince = deploymentState.filters.province;
    const selectedCity = deploymentState.filters.city;

    // Update City filter based on Province selection
    if (selectedProvince) {
        const cityFilter = document.getElementById('cityFilter');
        const citiesInProvince = new Set();

        Array.from(deploymentState.allTerminals.values()).forEach(terminal => {
            if (terminal.province === selectedProvince && terminal.city) {
                citiesInProvince.add(terminal.city);
            }
        });

        const currentCity = cityFilter.value;
        cityFilter.innerHTML = '<option value="">All Cities</option>';
        Array.from(citiesInProvince).sort().forEach(city => {
            const selected = city === currentCity ? 'selected' : '';
            cityFilter.innerHTML += `<option value="${city}" ${selected}>${city}</option>`;
        });

        // Reset city if it's not in the new list
        if (currentCity && !citiesInProvince.has(currentCity)) {
            deploymentState.filters.city = '';
        }
    }

    // Update Region filter based on Province and City selection
    const regionFilter = document.getElementById('regionFilter');
    const regionsFiltered = new Set();

    Array.from(deploymentState.allTerminals.values()).forEach(terminal => {
        const matchesProvince = !selectedProvince || terminal.province === selectedProvince;
        const matchesCity = !deploymentState.filters.city || terminal.city === deploymentState.filters.city;

        if (matchesProvince && matchesCity && terminal.area) {
            regionsFiltered.add(terminal.area);
        }
    });

    const currentRegion = regionFilter.value;
    regionFilter.innerHTML = '<option value="">All Regions</option>';
    Array.from(regionsFiltered).sort().forEach(region => {
        const selected = region === currentRegion ? 'selected' : '';
        regionFilter.innerHTML += `<option value="${region}" ${selected}>${region}</option>`;
    });

    // Reset region if it's not in the new list
    if (currentRegion && !regionsFiltered.has(currentRegion)) {
        deploymentState.filters.region = '';
    }
}

function renderTerminalTable() {
    const tableBody = document.getElementById('terminalTableBody');
    const startIndex = (deploymentState.pagination.currentPage - 1) * deploymentState.pagination.itemsPerPage;
    const endIndex = Math.min(startIndex + deploymentState.pagination.itemsPerPage, deploymentState.filteredTerminals.length);
    const pageTerminals = deploymentState.filteredTerminals.slice(startIndex, endIndex);

    if (pageTerminals.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="dp-empty">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-search"/></svg>
                        <h5>No terminals found</h5>
                        <p>Try adjusting your filters or search criteria</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    pageTerminals.forEach(terminal => {
        const terminalId = terminal.id.replace('terminal-', '');
        const isSelected = deploymentState.selectedTerminals.has(terminalId);
        const isAssigned = deploymentState.assignedTerminalIds.has(terminalId);
        const statusClass = getStatusClass(terminal.status);
        const assignmentClass = isAssigned ? 'assignment-assigned' : 'assignment-unassigned';

        html += `
            <tr class="terminal-row ${isSelected ? 'selected' : ''}" data-terminal-id="${terminalId}">
                <td>
                    <input type="checkbox" ${isSelected ? 'checked' : ''}
                           onchange="toggleTerminalSelection('${terminalId}')"
                           aria-label="Select terminal ${terminal.terminal_id}">
                </td>
                <td><span class="dp-tid">${terminal.terminal_id}</span></td>
                <td class="dp-merchant">${terminal.merchant_name}</td>
                <td>${terminal.province || '<span class="dp-muted">Unknown</span>'}</td>
                <td>${terminal.city || '<span class="dp-muted">Unknown</span>'}</td>
                <td>${terminal.area || '<span class="dp-muted">Unknown</span>'}</td>
                <td class="dp-c">
                    <span class="assignment-badge ${assignmentClass}">
                        ${isAssigned ? 'Assigned' : 'Unassigned'}
                    </span>
                </td>
                <td class="dp-c">
                    <span class="status-badge ${statusClass}">${terminal.status || 'unknown'}</span>
                </td>
                <td class="dp-c">
                    <button type="button" class="dp-row-btn" onclick="assignSingleTerminal('${terminalId}')"
                            ${isAssigned ? 'disabled title="Already assigned"' : ''}>
                        ${isAssigned ? 'Assigned' : 'Assign'}
                    </button>
                </td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;

    // Update counter
    document.getElementById('terminalCount').textContent = deploymentState.filteredTerminals.length;

    // Enable/disable controls
    const hasTerminals = deploymentState.filteredTerminals.length > 0;
    ['selectAllBtn', 'clearAllBtn', 'exportBtn'].forEach(id => {
        document.getElementById(id).disabled = !hasTerminals;
    });
}

function getStatusClass(status) {
    const statusClasses = {
        'active': 'status-active',
        'offline': 'status-offline',
        'maintenance': 'status-maintenance',
        'faulty': 'status-faulty'
    };
    return statusClasses[status] || 'status-unknown';
}

function renderHierarchy() {
    console.log('renderHierarchy called with data:', deploymentState.hierarchyData);

    if (!deploymentState.hierarchyData || deploymentState.hierarchyData.length === 0) {
        console.log('No hierarchy data, showing empty table');
        showEmptyTable();
        return;
    }

    // Apply initial filters (no filters)
    applyFilters();
}

function showEmptyTable() {
    const tableBody = document.getElementById('terminalTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="9">
                <div class="dp-empty">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-arrow-up-right"/></svg>
                    <h5>Step 1: Configure Deployment Setup</h5>
                    <p>Select clients and projects to load terminals</p>
                </div>
            </td>
        </tr>
    `;
}

function assignSingleTerminal(terminalId) {
    // Quick assign single terminal
    if (deploymentState.selectedTechnicians.size === 0) {
        showAlert('Please select a technician first', 'danger');
        return;
    }

    deploymentState.selectedTerminals.clear();
    deploymentState.selectedTerminals.add(terminalId);
    assignSelected();
}

function assignAll() {
    deploymentState.allTerminals.forEach((terminal, id) => {
        deploymentState.selectedTerminals.add(id);
    });
    assignSelected();
}

// =====================
// TECHNICIAN MANAGEMENT
// =====================
function updateTechnicianWorkload() {
    const container = document.getElementById('technicianWorkload');

    if (deploymentState.selectedTechnicians.size === 0) {
        container.innerHTML = `
            <div class="dp-empty" style="padding: 20px 8px;">
                <h6>Step 3: Select Technicians</h6>
                <p>Choose technicians to see workload distribution</p>
            </div>
        `;
        return;
    }

    let html = '';
    const checkboxes = document.querySelectorAll('#technicianOptions input[type="checkbox"]:checked');

    checkboxes.forEach(checkbox => {
        const workload = checkbox.dataset.workload || 0;
        const availability = checkbox.dataset.availability || 'available';
        const spec = checkbox.dataset.spec || 'General';

        const availabilityTone = {
            'available': 'is-good',
            'busy': 'is-warn',
            'very_busy': 'is-crit',
            'overloaded': 'is-crit'
        }[availability] || '';

        html += `
            <div class="workload-item">
                <div class="technician-name">${checkbox.dataset.name}</div>
                <div class="dp-workload-row">
                    <span>${spec}</span>
                    <span>
                        Current: ${workload} jobs
                        <span class="dp-chip ${availabilityTone}">${availability.replace('_', ' ')}</span>
                    </span>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// =====================
// ASSIGNMENT MANAGEMENT
// =====================
function assignSelected() {
    if (deploymentState.selectedTechnicians.size === 0) {
        showAlert('Please select at least one technician', 'danger');
        return;
    }

    if (deploymentState.selectedTerminals.size === 0) {
        showAlert('Please select terminals to assign', 'danger');
        return;
    }

    const assignmentMode = document.querySelector('input[name="assignmentMode"]:checked').value;
    const selectedTerminalIds = Array.from(deploymentState.selectedTerminals);
    const selectedTechnicians = getSelectedTechnicianData();

    const assignmentData = {
        selected_terminals: selectedTerminalIds,
        scheduled_date: deploymentState.deploymentDate || document.getElementById('deploymentDate').value,
        service_type: document.getElementById('serviceType').value,
        priority: document.getElementById('assignmentPriority').value,
        assignment_type: assignmentMode,
        notes: `Deployment assignment - ${assignmentMode} mode`
    };

    if (deploymentState.selectedProjects.size > 0) {
        assignmentData.project_id = Array.from(deploymentState.selectedProjects)[0];
    }

    if (assignmentMode === 'team') {
        selectedTechnicians.forEach(tech => {
            createAssignment({...assignmentData, technician_id: tech.id}, tech);
        });
    } else {
        distributeTerminalsAmongTechnicians(selectedTechnicians, selectedTerminalIds, assignmentData);
    }

    deploymentState.selectedTerminals.clear();
    renderHierarchy();
    updateProgressStats();
}

function getSelectedTechnicianData() {
    const checkboxes = document.querySelectorAll('#technicianOptions input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(checkbox => ({
        id: checkbox.value,
        name: checkbox.dataset.name,
        specialization: checkbox.dataset.spec,
        availability: checkbox.dataset.availability,
        workload: parseInt(checkbox.dataset.workload) || 0
    }));
}

function distributeTerminalsAmongTechnicians(technicians, terminalIds, baseAssignmentData) {
    const terminalsPerTech = Math.ceil(terminalIds.length / technicians.length);

    technicians.forEach((tech, index) => {
        const startIndex = index * terminalsPerTech;
        const endIndex = Math.min(startIndex + terminalsPerTech, terminalIds.length);
        const techTerminals = terminalIds.slice(startIndex, endIndex);

        if (techTerminals.length > 0) {
            createAssignment({
                ...baseAssignmentData,
                technician_id: tech.id,
                selected_terminals: techTerminals
            }, tech);
        }
    });
}

function createAssignment(assignmentData, technician) {
    fetch('{{ route("deployment.assign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(assignmentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addToLocalAssignments(technician.id, technician, assignmentData.selected_terminals);
            showAlert(`Assignment created for ${technician.name}!`);
            updateAssignmentSummary();
            updateUnassignedList();
            updateProgressiveVisibility();

            // Refresh assigned terminals and re-render table
            refreshAssignedTerminals().then(() => {
                renderTerminalTable();
            });
        } else {
            showAlert('Error creating assignment: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error creating assignment:', error);
        showAlert('Error creating assignment', 'danger');
    });
}

function addToLocalAssignments(technicianId, technician, terminalIds) {
    if (!deploymentState.assignments[technicianId]) {
        deploymentState.assignments[technicianId] = {
            technician: technician,
            terminals: [],
            regions: new Set(),
            priority: document.getElementById('assignmentPriority').value
        };
    }

    terminalIds.forEach(terminalId => {
        if (!deploymentState.assignments[technicianId].terminals.includes(terminalId)) {
            deploymentState.assignments[technicianId].terminals.push(terminalId);

            // Add region info
            const terminal = deploymentState.allTerminals.get(terminalId);
            if (terminal) {
                deploymentState.assignments[technicianId].regions.add(terminal.city || 'Unknown');
            }
        }
    });

    updateProgressStats();
    showAssignmentSuccessSection();
}

// =====================
// UI UPDATES
// =====================
function updateProgressStats() {
    const totalTerminals = deploymentState.allTerminals.size;
    let assignedCount = 0;

    Object.values(deploymentState.assignments).forEach(assignment => {
        assignedCount += assignment.terminals.length;
    });

    const selectedCount = deploymentState.selectedTerminals.size;
    const unassignedCount = totalTerminals - assignedCount;
    const technicianCount = Object.keys(deploymentState.assignments).length;

    document.getElementById('totalTerminals').textContent = totalTerminals;
    document.getElementById('assignedTerminals').textContent = assignedCount;
    document.getElementById('unassignedTerminals').textContent = unassignedCount;
    document.getElementById('selectedTerminals').textContent = selectedCount;
    document.getElementById('technicianCount').textContent = technicianCount;
    document.getElementById('unassignedCount').textContent = unassignedCount;

    updateUnassignedList();
}

function updateAssignmentButtons() {
    const hasSelections = deploymentState.selectedTerminals.size > 0;
    const hasTechnicians = deploymentState.selectedTechnicians.size > 0;
    const hasAssignments = Object.keys(deploymentState.assignments).length > 0;

    document.getElementById('assignSelectedBtn').disabled = !(hasSelections && hasTechnicians);
    document.getElementById('assignAllBtn').disabled = !(deploymentState.allTerminals.size > 0 && hasTechnicians);
    document.getElementById('clearAssignmentsBtn').disabled = !hasAssignments;
}

function showAssignmentSuccessSection() {
    updateAssignmentSummary();
    updateProgressiveVisibility();
}

function updateAssignmentSummary() {
    const tableBody = document.getElementById('assignmentSummaryTable');

    if (Object.keys(deploymentState.assignments).length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="dp-empty" style="padding: 28px;">
                    No assignments yet
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    Object.values(deploymentState.assignments).forEach((assignment, index) => {
        const regionList = Array.from(assignment.regions).join(', ');
        const priorityClass = {
            'normal': '',
            'high': 'is-warn',
            'emergency': 'is-crit'
        }[assignment.priority] || '';

        html += `
            <tr>
                <td>
                    <div class="dp-merchant">${assignment.technician.name}</div>
                    <div class="dp-muted" style="font-size: 12px;">${assignment.technician.specialization}</div>
                </td>
                <td class="dp-c"><strong>${assignment.terminals.length}</strong></td>
                <td class="dp-c">${regionList || 'Various'}</td>
                <td class="dp-c"><span class="dp-chip ${priorityClass}">${assignment.priority}</span></td>
                <td class="dp-c">
                    <button type="button" class="dp-row-btn is-danger" onclick="removeAssignment('${assignment.technician.id}')">
                        Remove
                    </button>
                </td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;

    // Update summary stats
    const totalTechnicians = Object.keys(deploymentState.assignments).length;
    const totalTerminals = Object.values(deploymentState.assignments).reduce((sum, a) => sum + a.terminals.length, 0);
    const estimatedTime = totalTerminals * 1.5; // 1.5 hours per terminal

    document.getElementById('summaryTechnicians').textContent = totalTechnicians;
    document.getElementById('summaryTerminals').textContent = totalTerminals;
    document.getElementById('summaryTime').textContent = `${estimatedTime} hours`;

    // Enable export buttons
    ['exportDeploymentBtn', 'saveDraftBtn'].forEach(id => {
        document.getElementById(id).disabled = totalTechnicians === 0;
    });
}

function updateUnassignedList() {
    const container = document.getElementById('unassignedList');
    const assignedTerminalIds = new Set();

    Object.values(deploymentState.assignments).forEach(assignment => {
        assignment.terminals.forEach(terminalId => {
            assignedTerminalIds.add(terminalId);
        });
    });

    const unassignedTerminals = [];
    deploymentState.allTerminals.forEach((terminal, id) => {
        if (!assignedTerminalIds.has(id)) {
            unassignedTerminals.push(terminal);
        }
    });

    if (unassignedTerminals.length === 0 && deploymentState.allTerminals.size > 0) {
        container.innerHTML = `
            <div class="dp-empty" style="padding: 20px 8px;">
                <p>All terminals assigned!</p>
            </div>
        `;
        return;
    }

    if (deploymentState.allTerminals.size === 0) {
        container.innerHTML = `
            <div class="dp-empty" style="padding: 20px 8px;">
                <p>Load hierarchy to see unassigned terminals</p>
            </div>
        `;
        return;
    }

    let html = '';
    unassignedTerminals.slice(0, 10).forEach(terminal => { // Show max 10
        html += `
            <div class="unassigned-item" onclick="selectUnassignedTerminal('${terminal.id.replace('terminal-', '')}')">
                <div><strong style="font-weight: 500;">${terminal.merchant_name}</strong> <span class="dp-tid">${terminal.terminal_id}</span></div>
                <small>${terminal.city || 'Unknown City'}</small>
            </div>
        `;
    });

    if (unassignedTerminals.length > 10) {
        html += `<div class="dp-more">and ${unassignedTerminals.length - 10} more</div>`;
    }

    container.innerHTML = html;
}

// =====================
// PROJECT MODAL
// =====================
function createNewProject() {
    if (deploymentState.selectedClients.size === 0) {
        showAlert('Please select clients first before creating a project', 'danger');
        return;
    }
    document.getElementById('createProjectModal').style.display = 'flex';
}

function closeProjectModal() {
    document.getElementById('createProjectModal').style.display = 'none';
    document.getElementById('createProjectForm').reset();
}

// Create project form submission
document.addEventListener('DOMContentLoaded', function() {
    const createProjectForm = document.getElementById('createProjectForm');
    if (createProjectForm) {
        createProjectForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (deploymentState.selectedClients.size === 0) {
                showAlert('Please select clients first', 'danger');
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Creating Project...';
            submitBtn.disabled = true;

            const formData = {
                name: document.getElementById('newProjectName').value.trim(),
                type: document.getElementById('newProjectType').value,
                duration: document.getElementById('newProjectDuration').value,
                description: document.getElementById('newProjectDescription').value.trim(),
                client_ids: Array.from(deploymentState.selectedClients)
            };

            // Validate required fields
            if (!formData.name || !formData.type) {
                showAlert('Please fill in all required fields', 'danger');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }

            // Make API call to create project
            fetch('{{ route("deployment.projects.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeProjectModal();
                    showAlert(`Project "${formData.name}" created successfully!`);

                    // Add the new project to the dropdown
                    const projectOptionsList = document.getElementById('projectOptionsList');
                    const newProjectOption = document.createElement('label');
                    newProjectOption.className = 'dropdown-option';
                    newProjectOption.innerHTML = `
                        <input type="checkbox" value="${data.project.id}" data-name="${data.project.name}" data-type="${data.project.type}" onchange="updateProjectSelection()">
                        <span>${data.project.name} (${data.project.type})</span>
                    `;

                    // Remove "no projects" message if it exists
                    const noProjectsMsg = projectOptionsList.querySelector('.disabled');
                    if (noProjectsMsg) {
                        noProjectsMsg.remove();
                    }

                    projectOptionsList.appendChild(newProjectOption);

                    // Auto-select the new project
                    const newCheckbox = newProjectOption.querySelector('input[type="checkbox"]');
                    newCheckbox.checked = true;
                    updateProjectSelection();
                } else {
                    showAlert('Error creating project: ' + (data.message || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error creating project:', error);
                showErrorModal(
                    'Project Creation Failed',
                    'There was an error creating your project. Please check the details below and try again.',
                    error.toString()
                );
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// =====================
// UTILITY FUNCTIONS
// =====================
function updatePagination() {
    const pagination = document.getElementById('tablePagination');
    const startIndex = (deploymentState.pagination.currentPage - 1) * deploymentState.pagination.itemsPerPage + 1;
    const endIndex = Math.min(deploymentState.pagination.currentPage * deploymentState.pagination.itemsPerPage, deploymentState.filteredTerminals.length);

    if (deploymentState.filteredTerminals.length === 0) {
        pagination.style.display = 'none';
        return;
    }

    pagination.style.display = 'flex';

    // Update showing text
    document.getElementById('showingFrom').textContent = startIndex;
    document.getElementById('showingTo').textContent = endIndex;
    document.getElementById('paginationTotal').textContent = deploymentState.filteredTerminals.length;

    // Generate pagination buttons
    const buttonsContainer = document.getElementById('paginationButtons');
    let buttonsHtml = '';

    // Previous button
    if (deploymentState.pagination.currentPage > 1) {
        buttonsHtml += `<button class="btn-secondary btn-sm" onclick="changePage(${deploymentState.pagination.currentPage - 1})">← Previous</button>`;
    }

    // Page numbers
    const totalPages = deploymentState.pagination.totalPages;
    const currentPage = deploymentState.pagination.currentPage;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (endPage - startPage < 4) {
        if (startPage === 1) {
            endPage = Math.min(totalPages, startPage + 4);
        } else {
            startPage = Math.max(1, endPage - 4);
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const active = i === currentPage ? 'btn-primary' : '';
        buttonsHtml += `<button class="btn btn-small ${active}" onclick="changePage(${i})">${i}</button>`;
    }

    // Next button
    if (deploymentState.pagination.currentPage < deploymentState.pagination.totalPages) {
        buttonsHtml += `<button class="btn-secondary btn-sm" onclick="changePage(${deploymentState.pagination.currentPage + 1})">Next →</button>`;
    }

    buttonsContainer.innerHTML = buttonsHtml;
}

function changePage(page) {
    deploymentState.pagination.currentPage = page;
    renderTerminalTable();
    updatePagination();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const isChecked = selectAllCheckbox.checked;

    if (isChecked) {
        selectAllVisible();
    } else {
        clearSelections();
    }
}

function selectAllVisible() {
    const startIndex = (deploymentState.pagination.currentPage - 1) * deploymentState.pagination.itemsPerPage;
    const endIndex = Math.min(startIndex + deploymentState.pagination.itemsPerPage, deploymentState.filteredTerminals.length);
    const pageTerminals = deploymentState.filteredTerminals.slice(startIndex, endIndex);

    pageTerminals.forEach(terminal => {
        const terminalId = terminal.id.replace('terminal-', '');
        deploymentState.selectedTerminals.add(terminalId);
    });

    renderTerminalTable();
    updateProgressStats();
    updateAssignmentButtons();
}

function selectAll() {
    deploymentState.filteredTerminals.forEach(terminal => {
        const terminalId = terminal.id.replace('terminal-', '');
        deploymentState.selectedTerminals.add(terminalId);
    });

    renderTerminalTable();
    updateProgressStats();
    updateAssignmentButtons();
}

function clearSelections() {
    deploymentState.selectedTerminals.clear();
    document.getElementById('selectAllCheckbox').checked = false;
    renderTerminalTable();
    updateProgressStats();
    updateAssignmentButtons();
}

function exportTableData() {
    const data = deploymentState.filteredTerminals.map(terminal => ({
        'Terminal ID': terminal.terminal_id,
        'Merchant Name': terminal.merchant_name,
        'Province': terminal.province || 'Unknown',
        'City': terminal.city || 'Unknown',
        'Region': terminal.area || 'Unknown',
        'Status': terminal.status || 'unknown',
        'Address': terminal.address || '',
        'Phone': terminal.phone || ''
    }));

    // Convert to CSV
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header]}"`).join(','))
    ].join('\n');

    // Download
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `terminals_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Update existing functions to work with table
function toggleTerminalSelection(terminalId) {
    if (deploymentState.selectedTerminals.has(terminalId)) {
        deploymentState.selectedTerminals.delete(terminalId);
    } else {
        deploymentState.selectedTerminals.add(terminalId);
    }
    updateProgressStats();
    updateAssignmentButtons();
    renderTerminalTable();
}

function showEmptyHierarchy() {
    showEmptyTable();
}

function showAlert(message, type = 'success') {
    // Use the portal's toast when available
    if (typeof window.showNotification === 'function') {
        window.showNotification(type === 'danger' ? 'error' : (type === 'success' ? 'success' : 'info'), message);
        return;
    }

    // Fallback toast
    const alertId = 'alert-' + Date.now();
    const alertClass = type === 'success' ? 'alert-success' : type === 'danger' ? 'alert-danger' : 'alert-info';
    const icon = type === 'success' ? '' : type === 'danger' ? '' : '';

    const alertHtml = `
        <div id="${alertId}" class="alert ${alertClass}" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; animation: slideIn 0.3s ease;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">${icon}</span>
                <span style="flex: 1;">${message}</span>
                <button type="button" onclick="removeAlert('${alertId}')" style="background: none; border: none; font-size: 18px; cursor: pointer; padding: 0; margin-left: 10px;">×</button>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', alertHtml);

    // Auto-remove after 5 seconds
    setTimeout(() => removeAlert(alertId), 5000);
}

function removeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }
}

function showErrorModal(title, message, details = null) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background: rgba(0,0,0,0.5); z-index: 10000;
        display: flex; justify-content: center; align-items: center;
    `;

    modal.innerHTML = `
        <div class="dp-modal" role="alertdialog" aria-modal="true">
            <div class="dp-modal-head">
                <h3 style="display: flex; align-items: center; gap: 8px;">
                    <svg class="mv-i" aria-hidden="true" style="color: var(--mv-crit);"><use href="#i-alert-circle"/></svg>
                    <span>${title}</span>
                </h3>
                <button type="button" class="dp-x" aria-label="Close" onclick="this.closest('[style*=\"position: fixed\"]').remove()"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
            </div>
            <div class="dp-modal-body">
                <p>${message}</p>
                ${details ? `<details class="dp-details">
                    <summary>Technical Details</summary>
                    <pre>${details}</pre>
                </details>` : ''}
            </div>
            <div class="dp-modal-foot">
                <button type="button" onclick="this.closest('[style*=\"position: fixed\"]').remove()" class="btn-primary">Close</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function setLoading(element, isLoading) {
    if (isLoading) {
        element.classList.add('loading');
    } else {
        element.classList.remove('loading');
    }
}

// =====================
// ACTION FUNCTIONS (Updated for Table)
// =====================
function clearAssignments() {
    deploymentState.assignments = {};
    updateProgressStats();
    updateAssignmentSummary();
    updateUnassignedList();
    updateAssignmentButtons();
    updateProgressiveVisibility();
}

function removeAssignment(technicianId) {
    delete deploymentState.assignments[technicianId];
    updateProgressStats();
    updateAssignmentSummary();
    updateUnassignedList();
    updateAssignmentButtons();
    updateProgressiveVisibility();
}

function selectUnassignedTerminal(terminalId) {
    deploymentState.selectedTerminals.add(terminalId);
    updateProgressStats();
    updateAssignmentButtons();
    renderHierarchy();
}

function selectAssignmentMode(mode) {
    document.querySelector(`input[name="assignmentMode"][value="${mode}"]`).checked = true;
}

function updateAssignmentMode() {
    // Handle assignment mode changes
    updateAssignmentButtons();
}

function exportDeployment() {
    if (Object.keys(deploymentState.assignments).length === 0) {
        showAlert('No assignments to export', 'danger');
        return;
    }

    // Show export options modal
    showExportModal();
}

function showExportModal() {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background: rgba(0,0,0,0.5); z-index: 10000;
        display: flex; justify-content: center; align-items: center;
    `;

    modal.innerHTML = `
        <div class="dp-modal" style="max-width: 420px;" role="dialog" aria-modal="true">
            <div class="dp-modal-head">
                <h3>Export Assignment Data</h3>
                <button type="button" class="dp-x" aria-label="Close" onclick="closeModal(this)"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
            </div>
            <div class="dp-modal-body">
                <p>Choose export format for your assignment data:</p>
                <div style="display: grid; gap: 8px;">
                    <button type="button" onclick="exportAssignments('csv')" class="dp-choice">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-table"/></svg>
                        <span>CSV Spreadsheet</span><small>.csv</small>
                    </button>
                    <button type="button" onclick="exportAssignments('excel')" class="dp-choice">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg>
                        <span>Excel Workbook</span><small>.xlsx</small>
                    </button>
                    <button type="button" onclick="exportAssignments('pdf')" class="dp-choice">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-printer"/></svg>
                        <span>PDF Report</span><small>.pdf</small>
                    </button>
                    <button type="button" onclick="exportAssignments('mobile')" class="dp-choice">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-phone"/></svg>
                        <span>Mobile Sync JSON</span><small>.json</small>
                    </button>
                </div>
            </div>
            <div class="dp-modal-foot">
                <button type="button" onclick="closeModal(this)" class="btn-secondary">Cancel</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function exportAssignments(format) {
    // Close the modal
    document.querySelector('[style*="position: fixed"]')?.remove();

    // Show loading
    showAlert('Preparing export...', 'info');

    // Collect assignment data to send
    const exportData = {
        format: format,
        client_ids: Array.from(deploymentState.selectedClients),
        project_ids: Array.from(deploymentState.selectedProjects),
        assignments: Object.values(deploymentState.assignments).map(assignment => ({
            technician_id: assignment.technician.id,
            technician_name: assignment.technician.name,
            terminal_ids: assignment.terminals,
            regions: Array.from(assignment.regions),
            priority: assignment.priority
        }))
    };

    fetch('{{ route("deployment.export-assignments") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(exportData)
    })
    .then(response => {
        if (response.ok) {
            // Check if it's a file download
            const contentType = response.headers.get('content-type');
            if (contentType && (contentType.includes('application/') || contentType.includes('text/csv'))) {
                return response.blob().then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `assignment_${format}_${new Date().toISOString().split('T')[0]}.${format === 'csv' ? 'csv' : format === 'excel' ? 'xlsx' : format === 'pdf' ? 'pdf' : 'json'}`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    showAlert(`Export completed! File downloaded as ${a.download}`);
                });
            } else {
                return response.json();
            }
        } else {
            throw new Error(`Export failed: ${response.status}`);
        }
    })
    .then(data => {
        if (data && data.success) {
            showAlert(data.message || 'Export completed successfully!');
        }
    })
    .catch(error => {
        console.error('Export error:', error);
        showAlert('Export failed: ' + error.message, 'danger');
    });
}

function saveAsDraft() {
    if (Object.keys(deploymentState.assignments).length === 0) {
        showAlert('No assignments to save', 'danger');
        return;
    }

    showAlert('Saving assignment as draft...', 'info');

    const draftData = {
        name: `Assignment Draft - ${new Date().toLocaleDateString()}`,
        client_ids: Array.from(deploymentState.selectedClients),
        project_ids: Array.from(deploymentState.selectedProjects),
        scheduled_date: deploymentState.deploymentDate || document.getElementById('deploymentDate').value,
        assignments: Object.values(deploymentState.assignments).map(assignment => ({
            technician_id: assignment.technician.id,
            terminal_ids: assignment.terminals,
            priority: assignment.priority
        })),
        deployment_status: 'draft'
    };

    fetch('{{ route("deployment.drafts.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(draftData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Draft saved successfully! Draft ID: ${data.draft_id}`);
        } else {
            showAlert('Failed to save draft: ' + (data.message || 'Unknown error'), 'danger');
        }
    })
    .catch(error => {
        console.error('Save draft error:', error);
        showAlert('Error saving draft: ' + error.message, 'danger');
    });
}

function viewAllAssignments() {
    window.location.href = '{{ route("jobs.index") }}';
}

// Universal modal close function
function closeModal(button) {
    // Try different ways to find the modal
    let modal = null;

    if (button && typeof button.closest === 'function') {
        modal = button.closest('[style*="position: fixed"]');
    }

    if (!modal) {
        modal = document.querySelector('[style*="position: fixed"]');
    }

    if (modal) {
        modal.remove();
    }
}

// Override the existing modal close handlers
document.addEventListener('click', function(event) {
    // Close any modal when clicking the backdrop
    if (event.target.style && event.target.style.position === 'fixed') {
        event.target.remove();
    }

    // Close project modal specifically
    const projectModal = document.getElementById('createProjectModal');

    if (event.target === projectModal) {
        closeProjectModal();
    }
});
</script>

@endsection
