@extends('layouts.app')
@section('title', 'POS Terminals')

@push('styles')
<style>
/* ── POS terminals · index ─────────────────────────────── */
.pt-index .pt-sr { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }

/* Stat tiles */
.pt-index .pt-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px; }
.pt-index .stat-card { padding: 14px 16px; gap: 12px; }
.pt-index .stat-icon { width: 36px; height: 36px; border-radius: 8px; }
.pt-index .stat-icon .mv-i { width: 18px; height: 18px; }
.pt-index .stat-number { font-size: 22px; }
.pt-index .stat-label { margin-top: 3px; }
@media (max-width: 900px) { .pt-index .pt-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }

/* Tabs */
.pt-index .tab-nav { margin-bottom: 16px; gap: 2px; }
.pt-index .tab-btn { padding: 10px 12px; font-size: 13.5px; gap: 8px; margin-bottom: -1px; border-bottom-width: 2px; }
.pt-index .tab-btn .mv-i { width: 16px; height: 16px; }
.pt-index .pt-count { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 18px; padding: 0 6px; border-radius: 9px; background: var(--mv-warn-soft); color: var(--mv-warn); font-size: 11.5px; font-weight: 600; font-variant-numeric: tabular-nums; }

/* Toolbar (filter bar) */
.pt-index .filter-bar { padding: 10px 12px; gap: 8px; align-items: center; margin-bottom: 12px; }
.pt-index .filter-bar .ui-input, .pt-index .filter-bar .ui-select { height: 34px; padding-top: 0; padding-bottom: 0; font-size: 13px; min-width: 0; }
.pt-index .filter-bar .ui-select { width: 150px; }
.pt-index .filter-actions { align-items: center; gap: 6px; }
.pt-index .pt-toolbar-end { margin-left: auto; display: flex; align-items: center; gap: 6px; }
.pt-index .pt-search { position: relative; }
.pt-index .pt-search .mv-i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--mv-muted); pointer-events: none; }
.pt-index .pt-search .ui-input { width: 240px; padding-left: 32px; }
.pt-index .ui-select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236A7686' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; padding-right: 32px;
}
.pt-index .pt-btn { height: 34px; padding: 0 12px; font-size: 13px; }

/* Table card */
.pt-index .ui-card-header { padding: 12px 16px; }
.pt-index .pt-card-title { display: flex; align-items: baseline; gap: 10px; }
.pt-index .pt-card-title h2 { font-size: 14px; font-weight: 600; margin: 0; }
.pt-index .pt-card-meta { font-size: 12.5px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.pt-index .ui-table tbody td { font-size: 13px; }
.pt-index .cell-sub { font-size: 12px; color: var(--mv-muted); margin-top: 1px; }
.pt-index .pt-id { color: var(--mv-ink); font-size: 12.5px; font-weight: 500; text-decoration: none; white-space: nowrap; }
.pt-index .pt-id:hover { color: var(--mv-accent-ink); text-decoration: underline; }
.pt-index .pt-none { color: var(--mv-muted); font-size: 12.5px; }
.pt-index .action-group { gap: 4px; justify-content: flex-end; }
.pt-index .action-btn { width: 30px; height: 30px; }
.pt-index th.pt-th-actions, .pt-index td.pt-td-actions { text-align: right; width: 1%; }
.pt-index .status-badge { gap: 6px; padding: 2px 8px; font-size: 12px; line-height: 18px; }
.pt-index .status-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
.pt-index .ui-card-footer { padding: 10px 16px; gap: 12px; flex-wrap: wrap; }
.pt-index .pt-foot-meta { font-size: 12.5px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }

/* Empty state */
.pt-index .ui-table tbody tr.pt-empty-row:hover { background: transparent; }
.pt-index .pt-empty { padding: 44px 16px; text-align: center; color: var(--mv-muted); font-size: 13px; }
.pt-index .pt-empty .mv-i { display: block; width: 32px; height: 32px; margin: 0 auto 10px; color: var(--mv-line-strong); }
.pt-index .pt-empty a { color: var(--mv-accent-ink); font-weight: 500; }

/* Discoveries note */
.pt-index .pt-note { display: flex; align-items: flex-start; gap: 10px; padding: 10px 14px; margin-bottom: 12px; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; font-size: 13px; color: var(--mv-ink-2); }
.pt-index .pt-note .mv-i { color: var(--mv-warn); margin-top: 1px; }

/* Smart import */
.pt-index .pt-sub { margin: 2px 0 0; font-size: 12.5px; color: var(--mv-muted); }
.pt-index .pt-stack > * + * { margin-top: 18px; }
.pt-index .pt-req { color: var(--mv-crit); }
.pt-index .pt-opt { color: var(--mv-muted); font-weight: 400; }
.pt-index .pt-error { margin: 6px 0 0; font-size: 12px; color: var(--mv-crit); }
.pt-index .ui-hint { font-size: 12px; margin: 5px 0 0; }
.pt-index .pt-stack .ui-select { height: 38px; padding-top: 0; padding-bottom: 0; font-size: 13.5px; }
.pt-index .pt-stack .pt-btn-field { height: 38px; }
.pt-index .pt-drop { border: 1px dashed var(--mv-line-strong); border-radius: 10px; background: var(--mv-surface-2); padding: 26px 20px; text-align: center; cursor: pointer; transition: border-color .15s ease; }
.pt-index .pt-drop:hover { border-color: var(--mv-muted); }
.pt-index .pt-drop.bg-blue-50 { background: var(--mv-surface); border-color: var(--mv-accent); }
.pt-index .pt-drop-icon { width: 36px; height: 36px; margin: 0 auto 10px; border-radius: 9px; background: var(--mv-surface); border: 1px solid var(--mv-line); display: grid; place-items: center; color: var(--mv-ink-2); }
.pt-index .pt-drop-title { margin: 0; font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
.pt-index .pt-drop-sub { margin: 3px 0 14px; font-size: 12.5px; color: var(--mv-muted); }
.pt-index .pt-drop-actions { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; cursor: default; }
.pt-index .pt-file { display: flex; align-items: center; gap: 10px; max-width: 460px; margin: 14px auto 0; padding: 9px 12px; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 8px; text-align: left; }
.pt-index .pt-file .mv-i { color: var(--mv-good); }
.pt-index .pt-file-name { font-size: 13px; font-weight: 500; color: var(--mv-ink); word-break: break-all; }
.pt-index .pt-file-meta { font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.pt-index .pt-options { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
.pt-index .pt-option { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; margin: 0; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface); cursor: pointer; transition: border-color .15s ease; }
.pt-index .pt-option:hover { border-color: var(--mv-line-strong); }
.pt-index .pt-option input { width: 16px; height: 16px; margin-top: 2px; accent-color: var(--mv-accent); flex-shrink: 0; }
.pt-index .pt-option-title { display: block; font-size: 13px; font-weight: 500; color: var(--mv-ink); }
.pt-index .pt-option-sub { display: block; font-size: 12px; color: var(--mv-muted); margin-top: 2px; }
@media (max-width: 700px) { .pt-index .pt-options { grid-template-columns: 1fr; } }
.pt-index .pt-footer-end { justify-content: flex-end; gap: 8px; padding: 12px 16px; }
.pt-index .pt-side { display: flex; flex-direction: column; gap: 16px; }
.pt-index .pt-side h3 { font-size: 13.5px; font-weight: 600; margin: 0 0 10px; }
.pt-index .pt-side-label { margin: 0 0 6px; font-size: 12px; font-weight: 500; color: var(--mv-muted); }
.pt-index .pt-list { list-style: none; margin: 0 0 14px; padding: 0; }
.pt-index .pt-list:last-child { margin-bottom: 0; }
.pt-index .pt-list li { display: flex; align-items: center; gap: 8px; padding: 3px 0; font-size: 13px; color: var(--mv-ink-2); }
.pt-index .pt-list .mv-i { width: 14px; height: 14px; color: var(--mv-muted); }
.pt-index .pt-side-text { margin: 0; font-size: 12.5px; line-height: 1.55; color: var(--mv-muted); }
.pt-index button:disabled { cursor: not-allowed; }

/* Modals */
.pt-index .pt-modal { position: fixed; inset: 0; z-index: 1100; display: flex; align-items: center; justify-content: center; padding: 24px 16px; overflow-y: auto; background: rgba(22, 32, 44, .45); }
.pt-index .pt-modal-card { width: 100%; max-height: calc(100vh - 48px); display: flex; flex-direction: column; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 16px 40px rgba(22, 32, 44, .14); overflow: hidden; }
.pt-index .pt-modal-lg { max-width: 880px; }
.pt-index .pt-modal-sm { max-width: 400px; }
.pt-index .pt-modal-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
.pt-index .pt-modal-head h3 { margin: 0; font-size: 14.5px; font-weight: 600; }
.pt-index .pt-modal-body { padding: 18px; overflow-y: auto; }
.pt-index .pt-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid var(--mv-line); }
.pt-index .pt-icon-btn { width: 30px; height: 30px; display: grid; place-items: center; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); cursor: pointer; }
.pt-index .pt-icon-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.pt-index .pt-loading { padding: 48px 0; text-align: center; color: var(--mv-muted); font-size: 13px; }
.pt-index .pt-loading .mv-i { display: block; width: 22px; height: 22px; margin: 0 auto 10px; }
.pt-index .pt-spin { animation: pt-spin 1s linear infinite; }
@keyframes pt-spin { to { transform: rotate(360deg); } }

/* Preview results (rendered by JS) */
.pt-index .pt-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
.pt-index .pt-summary-box { padding: 12px 14px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); }
.pt-index .pt-summary-box.is-good { border-color: #C6E6D2; background: var(--mv-good-soft); }
.pt-index .pt-summary-box.is-crit { border-color: #F2CACA; background: var(--mv-crit-soft); }
.pt-index .pt-summary-title { margin: 0; font-size: 13px; font-weight: 600; color: var(--mv-ink); }
.pt-index .pt-summary-text { margin: 3px 0 0; font-size: 12.5px; color: var(--mv-ink-2); font-variant-numeric: tabular-nums; }
.pt-index .pt-subcard { border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; margin-bottom: 16px; }
.pt-index .pt-subcard-head { padding: 10px 14px; font-size: 13px; font-weight: 600; color: var(--mv-ink); border-bottom: 1px solid var(--mv-line); }
.pt-index .pt-subcard .ui-table thead th { padding: 8px 14px; }
.pt-index .pt-subcard .ui-table tbody td { padding: 8px 14px; }
.pt-index .pt-code { display: inline-block; padding: 1px 6px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink); }
.pt-index .pt-row-error { display: block; margin-top: 3px; font-size: 12px; color: var(--mv-crit); }
.pt-index .pt-result { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 8px; border: 1px solid var(--mv-line); }
.pt-index .pt-result.is-good { background: var(--mv-good-soft); border-color: #C6E6D2; color: var(--mv-good); }
.pt-index .pt-result.is-crit { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.pt-index .pt-result .mv-i { margin-top: 1px; }
.pt-index .pt-fail { padding: 28px 8px 8px; text-align: center; }
.pt-index .pt-fail > .mv-i { display: block; width: 28px; height: 28px; margin: 0 auto 10px; color: var(--mv-crit); }
.pt-index .pt-fail-title { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.pt-index .pt-fail-msg { margin: 4px 0 18px; font-size: 13px; color: var(--mv-muted); }
.pt-index .pt-hint-box { text-align: left; padding: 12px 14px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); font-size: 12.5px; color: var(--mv-ink-2); }
.pt-index .pt-hint-box ul { margin: 6px 0 0; padding-left: 18px; }
@media (max-width: 700px) { .pt-index .pt-summary { grid-template-columns: 1fr; } }

/* Processing */
.pt-index .pt-processing { padding: 28px 24px 24px; text-align: center; }
.pt-index .pt-processing > .mv-i { display: block; width: 22px; height: 22px; margin: 0 auto 12px; color: var(--mv-accent); }
.pt-index .pt-processing h4 { margin: 0 0 6px; font-size: 14.5px; font-weight: 600; }
.pt-index .pt-processing p { margin: 0 0 18px; font-size: 13px; color: var(--mv-muted); }
.pt-index .pt-bar { position: relative; height: 4px; border-radius: 4px; background: var(--mv-line); overflow: hidden; }
.pt-index .pt-bar span { position: absolute; top: 0; bottom: 0; left: -35%; width: 35%; border-radius: 4px; background: var(--mv-accent); animation: pt-slide 1.4s ease-in-out infinite; }
@keyframes pt-slide { to { left: 100%; } }
@media (prefers-reduced-motion: reduce) { .pt-index .pt-spin, .pt-index .pt-bar span { animation: none; } }
</style>
@endpush

@section('content')
<div class="pt-index">

{{-- ── Stats ──────────────────────────────────────────────── --}}
<div class="pt-stats">
    <div class="stat-card">
        <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-card"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['total_terminals'] ?? 0 }}</div>
            <div class="stat-label">Total Terminals</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['active_terminals'] ?? 0 }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-orange"><svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['faulty_terminals'] ?? 0 }}</div>
            <div class="stat-label">Need Attention</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-gray"><svg class="mv-i" aria-hidden="true"><use href="#i-x-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['offline_terminals'] ?? 0 }}</div>
            <div class="stat-label">Offline</div>
        </div>
    </div>
</div>

{{-- ── Tab Navigation ──────────────────────────────────────── --}}
<div class="tab-nav">
    <a href="{{ route('pos-terminals.index') }}" class="tab-btn {{ request('tab') !== 'discoveries' ? 'active' : '' }}">
        <svg class="mv-i" aria-hidden="true"><use href="#i-card"/></svg> Terminal Overview
    </a>
    <a href="{{ route('pos-terminals.index', ['tab' => 'discoveries']) }}" class="tab-btn {{ request('tab') === 'discoveries' ? 'active' : '' }}">
        <svg class="mv-i" aria-hidden="true"><use href="#i-search"/></svg> Field Discoveries
        @if(($fieldDiscoveryCount ?? 0) > 0)
            <span class="pt-count">{{ $fieldDiscoveryCount }}</span>
        @endif
    </a>
    <button type="button" class="tab-btn" onclick="switchTab('import', this)">
        <svg class="mv-i" aria-hidden="true"><use href="#i-upload"/></svg> Smart Import
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════
     TAB 1 — TERMINAL OVERVIEW  |  TAB 2 — FIELD DISCOVERIES
     (server-rendered based on ?tab= URL param)
════════════════════════════════════════════════════════════ --}}
@if(request('tab') === 'discoveries')
{{-- FIELD DISCOVERIES TAB --}}
<div id="discoveries-tab" class="tab-content">
    <div class="pt-note">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-info"/></svg>
        <span>These terminals were found by technicians during site visits and are not yet in the main inventory. Review each one and promote to the main list once verified.</span>
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('pos-terminals.index') }}" class="filter-bar">
        <input type="hidden" name="tab" value="discoveries">
        <div class="filter-group pt-search">
            <label class="ui-label pt-sr" for="discovery-search">Search</label>
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-search"/></svg>
            <input type="text" name="search" id="discovery-search" placeholder="Search discovered terminals…"
                   value="{{ request('search') }}" class="ui-input"
                   onkeydown="if(event.key==='Enter'){this.form.submit();}">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-primary pt-btn">Apply</button>
            <a href="{{ route('pos-terminals.index', ['tab' => 'discoveries']) }}" class="btn-secondary pt-btn">Reset</a>
        </div>
    </form>

    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <div class="pt-card-title">
                <h2>Discovered on Site</h2>
                <span class="pt-card-meta">{{ number_format($discoveries->total()) }} terminals</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Terminal ID</th>
                        <th>Merchant</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Model / Serial</th>
                        <th>Status</th>
                        <th>Discovered</th>
                        <th class="pt-th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discoveries as $terminal)
                    <tr>
                        <td><a href="{{ route('pos-terminals.show', $terminal) }}" class="pt-id mv-mono">{{ $terminal->terminal_id }}</a></td>
                        <td>
                            <div class="cell-primary">{{ $terminal->merchant_name }}</div>
                            @if($terminal->business_type)
                            <div class="cell-sub">{{ $terminal->business_type }}</div>
                            @endif
                        </td>
                        <td>
                            @if($terminal->merchant_contact_person)
                            <div class="cell-primary">{{ $terminal->merchant_contact_person }}</div>
                            @endif
                            @if($terminal->merchant_phone)
                            <div class="cell-sub">{{ $terminal->merchant_phone }}</div>
                            @endif
                            @if(!$terminal->merchant_contact_person && !$terminal->merchant_phone)
                            <span class="pt-none">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="cell-primary">{{ $terminal->region ?: '—' }}</div>
                            @if($terminal->city)
                            <div class="cell-sub">{{ $terminal->city }}</div>
                            @endif
                        </td>
                        <td>
                            @if($terminal->terminal_model)
                            <div class="cell-primary">{{ $terminal->terminal_model }}</div>
                            @endif
                            @if($terminal->serial_number)
                            <div class="cell-sub mv-mono">{{ $terminal->serial_number }}</div>
                            @endif
                            @if(!$terminal->terminal_model && !$terminal->serial_number)
                            <span class="pt-none">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = match($terminal->status) {
                                    'active'      => 'badge-green',
                                    'offline'     => 'badge-gray',
                                    'maintenance' => 'badge-yellow',
                                    'faulty'      => 'badge-red',
                                    default       => 'badge-gray',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ ucfirst($terminal->status) }}</span>
                        </td>
                        <td>
                            <div class="cell-sub">{{ $terminal->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="pt-td-actions">
                            <div class="action-group">
                                <a href="{{ route('pos-terminals.show', $terminal) }}" class="action-btn action-view" title="View" aria-label="View">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>
                                </a>
                                <a href="{{ route('pos-terminals.edit', $terminal) }}" class="action-btn action-edit" title="Edit / Promote" aria-label="Edit / Promote">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="pt-empty-row">
                        <td colspan="8">
                            <div class="pt-empty">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-search"/></svg>
                                No field-discovered terminals yet.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($discoveries->hasPages())
        <div class="ui-card-footer">
            <span class="pt-foot-meta">
                Showing {{ $discoveries->firstItem() ?? 0 }}–{{ $discoveries->lastItem() ?? 0 }}
                of {{ number_format($discoveries->total()) }} terminals
            </span>
            {{ $discoveries->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@else
{{-- TERMINAL OVERVIEW TAB --}}
<div id="overview-tab" class="tab-content">

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('pos-terminals.index') }}" class="filter-bar" id="filter-form">
        <div class="filter-group pt-search">
            <label class="ui-label pt-sr" for="search-input">Search</label>
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-search"/></svg>
            <input type="text" name="search" id="search-input"
                   placeholder="Search terminals…"
                   value="{{ request('search') }}"
                   class="ui-input"
                   onkeydown="if(event.key==='Enter'){this.form.submit();}">
        </div>
        <div class="filter-group">
            <label class="ui-label pt-sr" for="filter-client">Client</label>
            <select name="client" id="filter-client" class="ui-select" onchange="this.form.submit()">
                <option value="">All Clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                        {{ $client->company_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label pt-sr" for="filter-status">Status</label>
            <select name="status" id="filter-status" class="ui-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active"       {{ request('status') == 'active'       ? 'selected' : '' }}>Active</option>
                <option value="offline"      {{ request('status') == 'offline'      ? 'selected' : '' }}>Offline</option>
                <option value="faulty"       {{ request('status') == 'faulty'       ? 'selected' : '' }}>Faulty</option>
                <option value="maintenance"  {{ request('status') == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label pt-sr" for="filter-region">Region</label>
            <select name="region" id="filter-region" class="ui-select" onchange="this.form.submit()">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label pt-sr" for="filter-city">City</label>
            <select name="city" id="filter-city" class="ui-select" onchange="this.form.submit()">
                <option value="">All Cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-primary pt-btn">Apply</button>
            <a href="{{ route('pos-terminals.index') }}" class="btn-secondary pt-btn">Reset</a>
        </div>
        <div class="pt-toolbar-end">
            <a href="{{ route('pos-terminals.export', request()->query()) }}" class="btn-secondary pt-btn">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg>
                Export
            </a>
            <a href="{{ route('pos-terminals.create') }}" class="btn-primary pt-btn">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg>
                Add Terminal
            </a>
        </div>
    </form>

    {{-- Terminals table --}}
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <div class="pt-card-title">
                <h2>Terminal Inventory</h2>
                <span class="pt-card-meta">{{ number_format($terminals->total()) }} terminals</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Terminal ID</th>
                        <th>Client / Bank</th>
                        <th>Merchant</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Last Service</th>
                        <th class="pt-th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terminals as $terminal)
                    <tr>
                        <td>
                            <a href="{{ route('pos-terminals.show', $terminal) }}" class="pt-id mv-mono">{{ $terminal->terminal_id }}</a>
                        </td>
                        <td>
                            <div class="cell-primary">{{ $terminal->client->company_name }}</div>
                        </td>
                        <td>
                            <div class="cell-primary">{{ $terminal->merchant_name }}</div>
                            @if($terminal->business_type)
                            <div class="cell-sub">{{ $terminal->business_type }}</div>
                            @endif
                        </td>
                        <td>
                            @if($terminal->merchant_contact_person)
                            <div class="cell-primary">{{ $terminal->merchant_contact_person }}</div>
                            @endif
                            @if($terminal->merchant_phone)
                            <div class="cell-sub">{{ $terminal->merchant_phone }}</div>
                            @endif
                            @if(!$terminal->merchant_contact_person && !$terminal->merchant_phone)
                            <span class="pt-none">—</span>
                            @endif
                        </td>
                        <td>
                            @if($terminal->region)
                            <div class="cell-primary">{{ $terminal->region }}</div>
                            @else
                            <div class="pt-none">No region</div>
                            @endif
                            @if($terminal->city)
                            <div class="cell-sub">{{ $terminal->city }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = match($terminal->status) {
                                    'active'      => 'badge-green',
                                    'offline'     => 'badge-gray',
                                    'maintenance' => 'badge-yellow',
                                    'faulty'      => 'badge-red',
                                    default       => 'badge-gray',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ ucfirst($terminal->status) }}</span>
                        </td>
                        <td>
                            @if($terminal->last_service_date)
                            <div class="cell-primary">{{ $terminal->last_service_date->format('M d, Y') }}</div>
                            <div class="cell-sub">{{ $terminal->last_service_date->diffForHumans() }}</div>
                            @else
                            <span class="pt-none">Never serviced</span>
                            @endif
                        </td>
                        <td class="pt-td-actions">
                            <div class="action-group">
                                <a href="{{ route('pos-terminals.show', $terminal) }}" class="action-btn action-view" title="View" aria-label="View">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>
                                </a>
                                <a href="{{ route('pos-terminals.edit', $terminal) }}" class="action-btn action-edit" title="Edit" aria-label="Edit">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="pt-empty-row">
                        <td colspan="8">
                            <div class="pt-empty">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-card"/></svg>
                                No terminals found. Try adjusting your filters or
                                <a href="{{ route('pos-terminals.create') }}">add your first terminal</a>.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($terminals->hasPages())
        <div class="ui-card-footer">
            <span class="pt-foot-meta">
                Showing {{ $terminals->firstItem() ?? 0 }}–{{ $terminals->lastItem() ?? 0 }}
                of {{ number_format($terminals->total()) }} terminals
            </span>
            {{ $terminals->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     TAB 3 — SMART IMPORT
════════════════════════════════════════════════════════════ --}}
<div id="import-tab" class="tab-content hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

        {{-- Import form --}}
        <form id="smart-import-form" action="{{ route('pos-terminals.import') }}" method="POST"
              enctype="multipart/form-data" class="ui-card lg:col-span-2">
            @csrf

            <div class="ui-card-header">
                <div>
                    <h2>Terminal Data Import</h2>
                    <p class="pt-sub">Import terminals from Excel, CSV, or TXT files with smart column detection</p>
                </div>
                <a href="{{ route('pos-terminals.download-template') }}" class="btn-secondary pt-btn">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg>
                    Download Template
                </a>
            </div>

            <div class="ui-card-body pt-stack">
                {{-- Client + mapping row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="ui-label" for="client_id">Client / Bank <span class="pt-req">*</span></label>
                        <select name="client_id" id="client_id" required class="ui-select">
                            <option value="">Choose the client for these terminals…</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="pt-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="ui-label" for="mapping_id">Column Mapping <span class="pt-opt">(optional)</span></label>
                        <div class="flex gap-2">
                            <select name="mapping_id" id="mapping_id" class="ui-select flex-1">
                                <option value="">Auto-detect columns</option>
                                @if(isset($mappings) && $mappings->count() > 0)
                                    @foreach($mappings as $mapping)
                                        <option value="{{ $mapping->id }}">
                                            {{ $mapping->mapping_name }}
                                            @if($mapping->client) ({{ $mapping->client->company_name }}) @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <a href="{{ route('pos-terminals.column-mapping') }}" target="_blank" class="btn-secondary pt-btn-field">
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-settings"/></svg> Manage
                            </a>
                        </div>
                        <p class="ui-hint">Leave blank for automatic header detection</p>
                    </div>
                </div>

                {{-- File drop zone --}}
                <div>
                    <label class="ui-label">Upload Data File <span class="pt-req">*</span></label>
                    <div id="drop-zone" class="pt-drop"
                         onclick="document.getElementById('smart-file-input').click()">
                        <div class="pt-drop-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-upload"/></svg></div>
                        <p class="pt-drop-title">Drop your file here or click to browse</p>
                        <p class="pt-drop-sub">Supports Excel (.xlsx, .xls), CSV, and TXT files up to 50 MB</p>
                        <div class="pt-drop-actions" onclick="event.stopPropagation()">
                            <input type="file" name="file" id="smart-file-input"
                                   accept=".csv,.xlsx,.xls,.txt" required class="hidden">
                            <button type="button" class="btn-secondary pt-btn"
                                    onclick="document.getElementById('smart-file-input').click()">
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-folder"/></svg> Choose File
                            </button>
                            <button type="button" id="preview-btn" disabled class="btn-secondary pt-btn opacity-50 cursor-not-allowed">
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg> Preview & Analyze
                            </button>
                        </div>
                        @error('file')
                            <p class="pt-error">{{ $message }}</p>
                        @enderror
                        <div id="file-info" class="hidden pt-file">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg>
                            <div class="min-w-0">
                                <div id="file-name" class="pt-file-name"></div>
                                <div id="file-details" class="pt-file-meta"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Import options --}}
                <div>
                    <p class="ui-label">Import Options</p>
                    <div class="pt-options">
                        <label class="pt-option">
                            <input type="checkbox" name="options[]" value="skip_duplicates" checked>
                            <span>
                                <span class="pt-option-title">Skip Duplicate Terminal IDs</span>
                                <span class="pt-option-sub">Existing terminals with the same ID will be ignored during import</span>
                            </span>
                        </label>
                        <label class="pt-option">
                            <input type="checkbox" name="options[]" value="update_existing">
                            <span>
                                <span class="pt-option-title">Update Existing Records</span>
                                <span class="pt-option-sub">Override existing terminal data with new imported values</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="ui-card-footer pt-footer-end">
                <button type="button" onclick="resetImportForm()" class="btn-secondary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg> Reset Form
                </button>
                <button type="submit" id="import-submit-btn" disabled
                        class="btn-primary opacity-50 cursor-not-allowed">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-upload"/></svg> Start Smart Import
                </button>
            </div>
        </form>

        {{-- Info cards --}}
        <div class="pt-side">
            <div class="ui-card ui-card-body">
                <h3>Required Fields</h3>
                <p class="pt-side-label">Required</p>
                <ul class="pt-list">
                    <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Terminal ID</li>
                    <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Merchant Name</li>
                </ul>
                <p class="pt-side-label">Optional</p>
                <p class="pt-side-text">
                    Contact Person, Phone, Email, Address, City, Province, Region,
                    Business Type, Terminal Model, Serial Number, Installation Date, Status, etc.
                </p>
            </div>
            <div class="ui-card ui-card-body">
                <h3>Smart Features</h3>
                <ul class="pt-list">
                    <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Auto-detects column headers</li>
                    <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Processes any column order</li>
                    <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Supports CSV, XLSX, XLS, TXT</li>
                    <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Handles files up to 50 MB</li>
                    <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Preview before importing</li>
                    <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Duplicate detection</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ── Preview Modal ───────────────────────────────────────── --}}
<div id="preview-modal" class="hidden pt-modal" role="dialog" aria-modal="true" aria-labelledby="preview-modal-title">
    <div class="pt-modal-card pt-modal-lg">
        <div class="pt-modal-head">
            <h3 id="preview-modal-title">Smart Import Preview & Analysis</h3>
            <button type="button" onclick="closePreviewModal()" class="pt-icon-btn" title="Close" aria-label="Close">
                <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
            </button>
        </div>
        <div id="preview-content" class="pt-modal-body">
            <div class="pt-loading">
                <svg class="mv-i pt-spin" aria-hidden="true"><use href="#i-refresh"/></svg>
                Analyzing your file…
            </div>
        </div>
        <div class="pt-modal-foot">
            <button type="button" onclick="closePreviewModal()" class="btn-secondary">Close Preview</button>
            <button type="button" id="proceed-import-btn" onclick="proceedWithImport()" disabled
                    class="btn-primary opacity-50 cursor-not-allowed">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg> Looks Good — Proceed with Import
            </button>
        </div>
    </div>
</div>

{{-- ── Processing Modal ────────────────────────────────────── --}}
<div id="processing-modal" class="hidden pt-modal" style="z-index:1110" role="dialog" aria-modal="true" aria-live="polite">
    <div class="pt-modal-card pt-modal-sm pt-processing">
        <svg class="mv-i pt-spin" aria-hidden="true"><use href="#i-refresh"/></svg>
        <h4>Processing Your Smart Import</h4>
        <p>Large files are processed in chunks automatically. This may take a few minutes…</p>
        <div class="pt-bar"><span></span></div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
// ── Tab switching ────────────────────────────────────────────
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const tab = document.getElementById(tabName + '-tab');
    if (tab) tab.classList.remove('hidden');
    if (btn) btn.classList.add('active');
}

// ── Filter helpers ───────────────────────────────────────────
function applyFilters() {
    document.getElementById('filter-form')?.submit();
}

// ── File upload ──────────────────────────────────────────────
window.importData = { currentFile: null, previewData: null, isProcessing: false };

document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('smart-file-input');
    const dropZone  = document.getElementById('drop-zone');
    const previewBtn = document.getElementById('preview-btn');
    const submitBtn  = document.getElementById('import-submit-btn');

    if (!fileInput) return;

    fileInput.addEventListener('change', e => { if (e.target.files[0]) handleFileSelection(e.target.files[0]); });

    if (dropZone) {
        dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('bg-blue-50'); });
        dropZone.addEventListener('dragleave', e => { e.preventDefault(); dropZone.classList.remove('bg-blue-50'); });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('bg-blue-50');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                handleFileSelection(file);
            }
        });
    }

    function handleFileSelection(file) {
        const allowed = ['.csv', '.xlsx', '.xls', '.txt'];
        const ext = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) { alert('Please select a valid file type: CSV, XLSX, XLS, or TXT'); return; }
        if (file.size > 50 * 1024 * 1024) { alert('File size exceeds 50 MB limit.'); return; }

        window.importData.currentFile = file;

        const nameEl    = document.getElementById('file-name');
        const detailEl  = document.getElementById('file-details');
        const infoEl    = document.getElementById('file-info');
        if (nameEl)   nameEl.textContent   = file.name;
        if (detailEl) detailEl.textContent = `${formatBytes(file.size)} · ${ext.toUpperCase()} · Modified ${new Date(file.lastModified).toLocaleDateString()}`;
        if (infoEl)   infoEl.classList.remove('hidden');

        if (previewBtn) { previewBtn.disabled = false; previewBtn.classList.remove('opacity-50', 'cursor-not-allowed'); }
        if (submitBtn)  { submitBtn.disabled  = false; submitBtn.classList.remove('opacity-50', 'cursor-not-allowed'); }
    }

    function formatBytes(b) {
        if (b === 0) return '0 B';
        const k = 1024, s = ['B','KB','MB','GB'], i = Math.floor(Math.log(b)/Math.log(k));
        return (b/Math.pow(k,i)).toFixed(2)+' '+s[i];
    }

    if (previewBtn) {
        previewBtn.addEventListener('click', function () {
            if (window.importData.isProcessing) return;
            const clientId = document.getElementById('client_id')?.value;
            if (!fileInput.files[0]) { alert('Please select a file first'); return; }
            if (!clientId) { alert('Please select a client first'); return; }
            startPreview(fileInput.files[0], clientId, document.getElementById('mapping_id')?.value);
        });
    }
});

function startPreview(file, clientId, mappingId) {
    window.importData.isProcessing = true;
    showPreviewModal();

    const formData = new FormData();
    formData.append('file', file);
    formData.append('client_id', clientId);
    if (mappingId) formData.append('mapping_id', mappingId);
    formData.append('preview_rows', '5');

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const ctrl = new AbortController();
    const tid  = setTimeout(() => { ctrl.abort(); displayPreviewError('Request timed out. Try a smaller file or split into chunks.'); }, 120000);

    fetch('/pos-terminals/preview-import', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        signal: ctrl.signal
    })
    .then(r => { clearTimeout(tid); if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
    .then(data => { data.success ? displayPreviewData(data) : displayPreviewError(data.message || 'Preview failed'); })
    .catch(err  => { clearTimeout(tid); displayPreviewError(err.name === 'AbortError' ? 'Timed out. Try a smaller file.' : err.message); })
    .finally(() => { window.importData.isProcessing = false; });
}

function showPreviewModal() {
    document.getElementById('preview-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreviewModal() {
    document.getElementById('preview-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function displayPreviewData(data) {
    const mapped   = data.column_mapping_info?.mapped_fields   || [];
    const missing  = data.column_mapping_info?.missing_required || [];
    const hasErrors = data.preview_data.some(r => r.validation_status !== 'valid');
    const canImport = !hasErrors && missing.length === 0;

    const content = document.getElementById('preview-content');
    content.innerHTML = `
        <div class="pt-summary">
            <div class="pt-summary-box">
                <p class="pt-summary-title">File Analysis</p>
                <p class="pt-summary-text">Mapping: ${data.mapping_name} · ${data.headers.length} columns · ${data.preview_data.length} preview rows</p>
            </div>
            <div class="pt-summary-box ${missing.length ? 'is-crit' : 'is-good'}">
                <p class="pt-summary-title">Column Mapping</p>
                <p class="pt-summary-text">${mapped.length} mapped · ${missing.length} missing required · ${missing.length === 0 ? 'All required fields found' : 'Missing: ' + missing.join(', ')}</p>
            </div>
        </div>
        <div class="pt-subcard">
            <div class="pt-subcard-head">Detected Columns</div>
            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead><tr><th>#</th><th>Column Header</th><th>Status</th></tr></thead>
                    <tbody>
                        ${data.headers.map((h, i) => {
                            const cls = mapped.includes(h.toLowerCase().replace(/\s+/g,'_')) ? 'badge-green' : 'badge-yellow';
                            return `<tr><td>${i+1}</td><td><span class="pt-code">${h}</span></td><td><span class="status-badge ${cls}">${cls === 'badge-green' ? 'Mapped' : 'Unmapped'}</span></td></tr>`;
                        }).join('')}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-subcard">
            <div class="pt-subcard-head">Data Preview</div>
            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead><tr><th>Row</th><th>Terminal ID</th><th>Merchant</th><th>Status</th><th>Validation</th></tr></thead>
                    <tbody>
                        ${data.preview_data.map(r => `
                            <tr>
                                <td>${r.row_number}</td>
                                <td><span class="pt-code">${r.mapped_data.terminal_id||'N/A'}</span></td>
                                <td>${r.mapped_data.merchant_name||'N/A'}</td>
                                <td>${r.mapped_data.status||'active'}</td>
                                <td><span class="status-badge ${r.validation_status==='valid'?'badge-green':'badge-red'}">${r.validation_status==='valid'?'Valid':'Error'}</span>${r.validation_status!=='valid'?'<span class="pt-row-error">'+r.validation_message+'</span>':''}</td>
                            </tr>`).join('')}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-result ${canImport ? 'is-good' : 'is-crit'}">
            <svg class="mv-i" aria-hidden="true"><use href="#i-${canImport ? 'check-circle' : 'alert-circle'}"/></svg>
            <div>
                <p class="pt-summary-title">${canImport ? 'Ready for Import' : 'Issues Detected'}</p>
                <p class="pt-summary-text">${canImport ? 'All required fields present and validation passed.' : 'Resolve issues before importing.'}</p>
            </div>
        </div>
    `;

    const proceedBtn = document.getElementById('proceed-import-btn');
    if (proceedBtn) {
        proceedBtn.disabled = !canImport;
        proceedBtn.classList.toggle('opacity-50', !canImport);
        proceedBtn.classList.toggle('cursor-not-allowed', !canImport);
    }
}

function displayPreviewError(msg) {
    document.getElementById('preview-content').innerHTML = `
        <div class="pt-fail">
            <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
            <p class="pt-fail-title">Preview Failed</p>
            <p class="pt-fail-msg">${msg}</p>
            <div class="pt-hint-box">
                <strong>Troubleshooting:</strong>
                <ul>
                    <li>Ensure the file is valid CSV, XLSX, XLS, or TXT</li>
                    <li>Check it contains Terminal ID and Merchant Name columns</li>
                    <li>Verify the file is not corrupted or password-protected</li>
                </ul>
            </div>
        </div>
    `;
}

function proceedWithImport() {
    closePreviewModal();
    document.getElementById('processing-modal').classList.remove('hidden');
    document.getElementById('smart-import-form')?.submit();
}

function resetImportForm() {
    document.getElementById('smart-import-form')?.reset();
    document.getElementById('file-info')?.classList.add('hidden');
    const pBtn = document.getElementById('preview-btn');
    const sBtn = document.getElementById('import-submit-btn');
    if (pBtn) { pBtn.disabled = true; pBtn.classList.add('opacity-50', 'cursor-not-allowed'); }
    if (sBtn) { sBtn.disabled = true; sBtn.classList.add('opacity-50', 'cursor-not-allowed'); }
    window.importData = { currentFile: null, previewData: null, isProcessing: false };
}

// Close modal on backdrop click or Escape
document.addEventListener('click', e => {
    if (e.target.id === 'preview-modal') closePreviewModal();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePreviewModal();
});
</script>
@endpush
