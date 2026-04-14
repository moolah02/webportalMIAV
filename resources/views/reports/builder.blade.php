@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
:root {
    --rb-accent:   #4f46e5;
    --rb-accent-h: #4338ca;
    --rb-dim-bg:   #eff6ff;
    --rb-dim-fg:   #1e40af;
    --rb-dim-bd:   #bfdbfe;
    --rb-mes-bg:   #fdf4ff;
    --rb-mes-fg:   #7e22ce;
    --rb-mes-bd:   #e9d5ff;
    --rb-surface:  #ffffff;
    --rb-muted:    #f8fafc;
    --rb-border:   #e2e8f0;
    --rb-text:     #0f172a;
    --rb-sub:      #64748b;
    --rb-radius:   12px;
    --rb-shadow:   0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
}

[x-cloak] { display:none !important; }

/* ─── Page shell ─────────────────────────────────────────────── */
.rb-page {
    display: grid;
    grid-template-rows: auto auto 1fr;
    height: calc(100vh - 150px);
    min-height: 600px;
    gap: 0;
}

/* ─── Top bar ────────────────────────────────────────────────── */
.rb-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 0 16px;
    border-bottom: 1px solid var(--rb-border);
    margin-bottom: 0;
    gap: 12px;
    flex-wrap: wrap;
}
.rb-topbar-left { display:flex; align-items:center; gap:12px; }
.rb-topbar-left h2 { margin:0; font-size:18px; font-weight:700; color:var(--rb-text); }
.rb-topbar-left p  { margin:0; font-size:12px; color:var(--rb-sub); }
.rb-topbar-right { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }

/* ─── Filter strip ───────────────────────────────────────────── */
.rb-strip {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    padding: 12px 16px;
    background: var(--rb-muted);
    border: 1px solid var(--rb-border);
    border-radius: var(--rb-radius);
    margin: 12px 0;
    flex-wrap: wrap;
}
.rb-strip-group { display:flex; flex-direction:column; gap:3px; }
.rb-strip-group label {
    font-size:10px; font-weight:700; color:var(--rb-sub);
    text-transform:uppercase; letter-spacing:.07em;
    display:flex; align-items:center; gap:4px;
}
.rb-strip-group label span { font-size:11px; }
.rb-strip-select,
.rb-strip-input {
    height:34px;
    padding: 0 10px;
    border: 1px solid var(--rb-border);
    border-radius: 8px;
    font-size: 13px;
    color: var(--rb-text);
    background: var(--rb-surface);
    cursor: pointer;
    min-width: 120px;
    transition: border-color .15s, box-shadow .15s;
}
.rb-strip-select:focus,
.rb-strip-input:focus {
    outline: none;
    border-color: var(--rb-accent);
    box-shadow: 0 0 0 3px rgba(79,70,229,.12);
}
.rb-strip-input[disabled] { opacity:.35; cursor:default; }
.rb-strip-sep {
    width:1px; height:34px; background:var(--rb-border);
    margin: 0 4px; align-self: flex-end;
}
.rb-strip-input[type="date"] { min-width:130px; }
.rb-strip-input[type="number"] { min-width:70px; max-width:80px; }

/* ─── Main body ──────────────────────────────────────────────── */
.rb-body {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 12px;
    overflow: hidden;
    min-height: 0;
}

/* ─── Field panel ────────────────────────────────────────────── */
.rb-fields-panel {
    background: var(--rb-surface);
    border: 1px solid var(--rb-border);
    border-radius: var(--rb-radius);
    box-shadow: var(--rb-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.rb-fields-head {
    padding: 12px 14px 10px;
    border-bottom: 1px solid var(--rb-border);
    background: var(--rb-muted);
    flex-shrink: 0;
}
.rb-fields-head h4 {
    margin:0 0 8px;
    font-size:11px; font-weight:700;
    color:var(--rb-sub);
    text-transform:uppercase; letter-spacing:.07em;
}
.rb-search {
    width:100%;
    height:32px;
    padding:0 10px;
    border:1px solid var(--rb-border);
    border-radius:7px;
    font-size:12px;
    color:var(--rb-text);
    background:var(--rb-surface);
    box-sizing:border-box;
    transition:border-color .15s;
}
.rb-search:focus { outline:none; border-color:var(--rb-accent); box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.rb-fields-body { flex:1; overflow-y:auto; padding:10px 10px 14px; }
.rb-cat-label {
    font-size:10px; font-weight:700;
    color:var(--rb-sub);
    text-transform:uppercase; letter-spacing:.07em;
    padding:8px 6px 4px;
    display:flex; align-items:center; gap:5px;
}
.rb-cat-label::before {
    content:'';
    flex:1; height:1px; background:var(--rb-border);
}
.rb-chip {
    display:block; width:100%;
    padding:6px 10px; margin-bottom:2px;
    border:1px solid transparent;
    border-radius:7px;
    font-size:12px; font-weight:500;
    cursor:grab; user-select:none;
    transition:all .12s;
    text-align:left; background:none;
}
.rb-chip:active { cursor:grabbing; }
.rb-chip-dim { background:var(--rb-dim-bg); color:var(--rb-dim-fg); border-color:var(--rb-dim-bd); }
.rb-chip-dim:hover { background:#dbeafe; transform:translateX(2px); }
.rb-chip-mes { background:var(--rb-mes-bg); color:var(--rb-mes-fg); border-color:var(--rb-mes-bd); }
.rb-chip-mes:hover { background:#f3e8ff; transform:translateX(2px); }
.rb-legend { display:flex; gap:10px; padding:6px 14px 10px; border-top:1px solid var(--rb-border); flex-shrink:0; }
.rb-legend span { font-size:10px; font-weight:600; display:flex; align-items:center; gap:4px; }
.rb-dot { width:7px; height:7px; border-radius:50%; display:inline-block; }
.rb-dot-dim { background:var(--rb-accent); }
.rb-dot-mes { background:#9333ea; }

/* ─── Right panel ────────────────────────────────────────────── */
.rb-right {
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow: hidden;
    min-height: 0;
}

/* ─── Columns bar ────────────────────────────────────────────── */
.rb-columns-bar {
    background: var(--rb-surface);
    border: 1px solid var(--rb-border);
    border-radius: var(--rb-radius);
    box-shadow: var(--rb-shadow);
    flex-shrink: 0;
    overflow: hidden;
}
.rb-columns-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px;
    border-bottom:1px solid var(--rb-border);
    background:var(--rb-muted);
}
.rb-columns-head-left {
    display:flex; align-items:center; gap:8px;
    font-size:12px; font-weight:700; color:var(--rb-sub);
    text-transform:uppercase; letter-spacing:.06em;
}
.rb-col-count {
    background:var(--rb-accent); color:#fff;
    font-size:10px; font-weight:700;
    padding:1px 7px; border-radius:20px;
}
.rb-columns-body {
    padding:10px 12px;
    min-height:50px;
}
.rb-dropzone {
    min-height:44px;
    border:2px dashed var(--rb-border);
    border-radius:8px;
    padding:8px 10px;
    background:var(--rb-muted);
    transition:all .2s;
    display:flex; flex-wrap:wrap; gap:6px; align-items:center;
}
.rb-dropzone.over { border-color:var(--rb-accent); background:rgba(79,70,229,.04); }
.rb-dz-hint { color:#94a3b8; font-size:12px; }
.rb-pill {
    display:inline-flex; align-items:center;
    padding:4px 8px 4px 10px;
    border-radius:20px; font-size:12px; font-weight:500;
    border:1px solid transparent;
    gap:4px;
}
.rb-pill-dim { background:var(--rb-dim-bg); color:var(--rb-dim-fg); border-color:var(--rb-dim-bd); }
.rb-pill-mes { background:var(--rb-mes-bg); color:var(--rb-mes-fg); border-color:var(--rb-mes-bd); }
.rb-pill select {
    border:none; background:transparent;
    font-size:10px; color:inherit; cursor:pointer;
    outline:none; padding:0;
}
.rb-pill-x {
    background:none; border:none; cursor:pointer;
    font-size:13px; line-height:1; padding:0;
    opacity:.5; color:inherit;
}
.rb-pill-x:hover { opacity:1; }
.rb-agg-hint { font-size:10px; color:var(--rb-sub); padding:0 4px; }

/* ─── Results ────────────────────────────────────────────────── */
.rb-results {
    flex:1;
    background:var(--rb-surface);
    border:1px solid var(--rb-border);
    border-radius:var(--rb-radius);
    box-shadow:var(--rb-shadow);
    display:flex; flex-direction:column;
    overflow:hidden; min-height:0;
}
.rb-results-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px;
    border-bottom:1px solid var(--rb-border);
    background:var(--rb-muted);
    flex-shrink:0;
}
.rb-results-title {
    font-size:12px; font-weight:700; color:var(--rb-sub);
    text-transform:uppercase; letter-spacing:.06em;
    display:flex; align-items:center; gap:8px;
}
.rb-row-badge {
    background:#dcfce7; color:#15803d;
    font-size:11px; font-weight:700;
    padding:2px 9px; border-radius:20px;
    border:1px solid #86efac;
}
.rb-results-body { flex:1; overflow:auto; min-height:0; }
.rb-results-empty {
    flex:1; display:flex; align-items:center; justify-content:center;
    flex-direction:column; gap:10px;
    color:#94a3b8; padding:50px 20px; text-align:center;
}
.rb-results-empty-icon { font-size:44px; }
.rb-results-empty-title { font-size:15px; font-weight:600; color:#475569; margin:0; }
.rb-results-empty-sub { font-size:13px; margin:0; color:#94a3b8; }

/* ─── Table ──────────────────────────────────────────────────── */
.rb-table { width:100%; border-collapse:collapse; font-size:13px; }
.rb-table thead { position:sticky; top:0; z-index:2; }
.rb-table th {
    padding:10px 14px;
    background:#f1f5f9;
    border-bottom:2px solid var(--rb-border);
    text-align:left;
    font-size:11px; font-weight:700;
    color:#374151;
    text-transform:uppercase; letter-spacing:.06em;
    white-space:nowrap;
}
.rb-table td {
    padding:9px 14px;
    border-bottom:1px solid #f1f5f9;
    color:var(--rb-text);
    font-size:13px;
}
.rb-table tbody tr:hover td { background:#fafbfc; }

/* ─── Buttons ────────────────────────────────────────────────── */
.rb-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 16px;
    border-radius:8px; border:1px solid transparent;
    font-size:13px; font-weight:600; cursor:pointer;
    transition:all .15s; white-space:nowrap;
    line-height:1.4;
}
.rb-btn:disabled { opacity:.4; cursor:not-allowed; }
.rb-btn-primary  { background:var(--rb-accent); color:#fff; border-color:var(--rb-accent); }
.rb-btn-primary:hover:not(:disabled)  { background:var(--rb-accent-h); }
/* Run Report gets extra size so it's immediately obvious */
.rb-btn-run {
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: .02em;
    box-shadow: 0 2px 6px rgba(79,70,229,.35);
}
.rb-btn-run:disabled {
    opacity: 1;
    background: #c7d2fe;
    border-color: #c7d2fe;
    color: #6366f1;
    cursor: not-allowed;
    box-shadow: none;
}
.rb-btn-export   { background:#059669; color:#fff; border-color:#059669; }
.rb-btn-export:hover:not(:disabled)   { background:#047857; }
.rb-btn-outline  { background:#fff; color:#374151; border-color:var(--rb-border); }
.rb-btn-outline:hover:not(:disabled)  { background:var(--rb-muted); border-color:#cbd5e1; }
.rb-btn-ghost    { background:transparent; color:var(--rb-sub); border-color:transparent; }
.rb-btn-ghost:hover:not(:disabled)    { background:var(--rb-muted); color:var(--rb-text); }
.rb-btn-danger   { background:#fff5f5; color:#dc2626; border-color:#fecaca; }
.rb-btn-danger:hover:not(:disabled)   { background:#fee2e2; }

/* ─── Spinner ────────────────────────────────────────────────── */
.rb-spinner {
    width:32px; height:32px;
    border:3px solid var(--rb-border);
    border-top-color:var(--rb-accent);
    border-radius:50%;
    animation:rb-spin .7s linear infinite;
    margin:0 auto 10px;
}
@keyframes rb-spin { to { transform:rotate(360deg); } }

/* ─── Alerts ─────────────────────────────────────────────────── */
.rb-alert {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px; border-radius:9px;
    font-size:13px; margin-bottom:10px;
}
.rb-alert-err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; }
.rb-alert-ok  { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.rb-alert button { background:none; border:none; cursor:pointer; font-size:17px; opacity:.6; color:inherit; padding:0 0 0 10px; }
.rb-alert button:hover { opacity:1; }

/* ─── Dropdown menu ──────────────────────────────────────────── */
.rb-menu {
    position:absolute; right:0; top:calc(100% + 5px);
    background:#fff;
    border:1px solid var(--rb-border);
    border-radius:10px;
    box-shadow:0 10px 30px rgba(0,0,0,.1);
    z-index:99; min-width:160px; overflow:hidden;
}
.rb-menu button {
    display:flex; align-items:center; gap:8px;
    width:100%; padding:10px 14px;
    border:none; background:none;
    font-size:13px; color:var(--rb-text);
    cursor:pointer; text-align:left;
}
.rb-menu button:hover { background:var(--rb-muted); }

/* ─── Modal ──────────────────────────────────────────────────── */
.rb-backdrop {
    position:fixed; inset:0;
    background:rgba(15,23,42,.5);
    backdrop-filter:blur(2px);
    display:flex; align-items:center; justify-content:center;
    z-index:500;
}
.rb-modal {
    background:var(--rb-surface);
    border-radius:14px;
    box-shadow:0 20px 60px rgba(0,0,0,.2);
    padding:24px;
    width:460px; max-width:92vw;
}
.rb-modal-lg {
    background:var(--rb-surface);
    border-radius:14px;
    box-shadow:0 20px 60px rgba(0,0,0,.2);
    padding:24px;
    width:580px; max-width:92vw;
    max-height:78vh;
    display:flex; flex-direction:column;
}
.rb-modal h3 { margin:0 0 18px; font-size:16px; font-weight:700; color:var(--rb-text); }
.rb-label { font-size:10px; font-weight:700; color:var(--rb-sub); text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; display:block; }
.rb-input {
    width:100%; padding:9px 12px;
    border:1px solid var(--rb-border); border-radius:8px;
    font-size:14px; box-sizing:border-box;
    transition:border-color .15s;
}
.rb-input:focus { outline:none; border-color:var(--rb-accent); box-shadow:0 0 0 3px rgba(79,70,229,.1); }

/* ─── Template card ──────────────────────────────────────────── */
.rb-tpl {
    border:1px solid var(--rb-border);
    border-radius:10px; padding:14px;
    cursor:pointer; transition:all .15s;
    margin-bottom:8px;
}
.rb-tpl:hover { border-color:var(--rb-accent); background:rgba(79,70,229,.03); }
.rb-tpl-name { font-size:14px; font-weight:600; color:var(--rb-text); }
.rb-tpl-desc { font-size:12px; color:var(--rb-sub); margin-top:3px; }
.rb-tpl-meta { font-size:11px; color:#94a3b8; margin-top:5px; }
.rb-badge-global { background:#f0fdf4; color:#15803d; border:1px solid #86efac; border-radius:4px; font-size:9px; font-weight:700; padding:2px 6px; text-transform:uppercase; letter-spacing:.05em; }

/* ─── Confirm dialog ─────────────────────────────────────────── */
.rb-confirm { max-width:380px; }
.rb-confirm-icon { font-size:36px; text-align:center; margin-bottom:12px; }
.rb-confirm p { font-size:14px; color:var(--rb-sub); margin:0 0 20px; line-height:1.6; }
</style>

<div x-data="reportBuilder()" x-cloak>

    {{-- Alerts --}}
    <template x-if="errorMessage">
        <div class="rb-alert rb-alert-err" x-transition>
            <span x-text="errorMessage"></span>
            <button @click="errorMessage=''">&times;</button>
        </div>
    </template>
    <template x-if="successMessage">
        <div class="rb-alert rb-alert-ok" x-transition>
            <span x-text="successMessage"></span>
            <button @click="successMessage=''">&times;</button>
        </div>
    </template>

    <div class="rb-page">

        {{-- ═══════════════════════════════════════════════
             TOP BAR
        ════════════════════════════════════════════════ --}}
        <div class="rb-topbar">
            <div class="rb-topbar-left">
                <div>
                    <h2>&#128202; Report Builder</h2>
                    <p>Pick a data source, add columns, apply filters — then run your report.</p>
                </div>
            </div>
            <div class="rb-topbar-right">

                <span :title="fields.length === 0 ? 'Add at least one column from the left panel first' : 'Run your report'">
                <button @click="runReport()" :disabled="loading || fields.length === 0" class="rb-btn rb-btn-primary rb-btn-run">
                    <span x-show="!loading">&#9654;&nbsp;Run Report</span>
                    <span x-show="loading" style="display:flex;align-items:center;gap:6px;">
                        <span style="width:13px;height:13px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:rb-spin .7s linear infinite;display:inline-block;"></span>
                        Running…
                    </span>
                </button>
                </span>

                <div style="position:relative;" x-data="{ open:false }">
                    <button @click="open=!open"
                            :disabled="!reportData || reportData.length===0"
                            class="rb-btn rb-btn-export">
                        &#11015;&nbsp;Export&nbsp;&#9662;
                    </button>
                    <div x-show="open" @click.outside="open=false" x-transition class="rb-menu">
                        <button @click="exportReport('csv');open=false">
                            <span>&#128196;</span> Download CSV
                        </button>
                        <button @click="exportReport('pdf');open=false">
                            <span>&#128240;</span> Download PDF
                        </button>
                    </div>
                </div>

                @if($canManageTemplates)
                <button @click="showSaveModal=true" :disabled="fields.length===0" class="rb-btn rb-btn-outline">
                    &#128190;&nbsp;Save
                </button>
                @endif

                <button @click="openTemplateModal()" class="rb-btn rb-btn-outline">
                    &#128193;&nbsp;Templates
                </button>

                <a href="{{ route('reports.history') }}" class="rb-btn rb-btn-outline">
                    &#128221;&nbsp;History
                </a>

                <button x-show="fields.length > 0" @click="clearAll()" class="rb-btn rb-btn-danger">
                    &#128465;&nbsp;Clear
                </button>

            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             FILTER STRIP
        ════════════════════════════════════════════════ --}}
        <div class="rb-strip">

            <div class="rb-strip-group">
                <label><span>&#128200;</span> Data Source</label>
                <select class="rb-strip-select" x-model="config.baseTable" @change="onDataSourceChange()" style="min-width:160px;">
                    <template x-for="(t,tn) in availableFields" :key="tn">
                        <option :value="tn" x-text="t.label"></option>
                    </template>
                </select>
            </div>

            <div class="rb-strip-sep"></div>

            <div class="rb-strip-group">
                <label><span>&#127759;</span> Region</label>
                <select class="rb-strip-select" x-model="config.regionId">
                    <option value="">All Regions</option>
                    <template x-for="(name,id) in availableFilters.regions" :key="id">
                        <option :value="id" x-text="name"></option>
                    </template>
                </select>
            </div>

            <div class="rb-strip-group">
                <label><span>&#128100;</span> Client</label>
                <select class="rb-strip-select" x-model="config.clientId">
                    <option value="">All Clients</option>
                    <template x-for="(name,id) in availableFilters.clients" :key="id">
                        <option :value="id" x-text="name"></option>
                    </template>
                </select>
            </div>

            <div class="rb-strip-sep"></div>

            <div class="rb-strip-group">
                <label><span>&#128197;</span> Date Column</label>
                <select class="rb-strip-select" x-model="config.dateColumn" style="min-width:160px;">
                    <option value="">No date filter</option>
                    <template x-for="(t,tn) in availableFields" :key="tn">
                        <template x-for="f in t.fields.filter(f=>f.type==='date')" :key="f.expression">
                            <option :value="f.expression" x-text="t.label+' › '+f.label"></option>
                        </template>
                    </template>
                </select>
            </div>

            <div class="rb-strip-group">
                <label>From</label>
                <input type="date" class="rb-strip-input" x-model="config.dateFrom" :disabled="!config.dateColumn">
            </div>

            <div class="rb-strip-group">
                <label>To</label>
                <input type="date" class="rb-strip-input" x-model="config.dateTo" :disabled="!config.dateColumn">
            </div>

            <div class="rb-strip-sep"></div>

            <div class="rb-strip-group">
                <label>&#9776; Rows</label>
                <input type="number" class="rb-strip-input" x-model.number="config.limit" min="1" max="10000" style="text-align:center;">
            </div>

        </div>

        {{-- ═══════════════════════════════════════════════
             MAIN BODY
        ════════════════════════════════════════════════ --}}
        <div class="rb-body">

            {{-- ─── LEFT: Field browser ─────────────────── --}}
            <div class="rb-fields-panel">
                <div class="rb-fields-head">
                    <h4>Available Fields</h4>
                    <input type="text" class="rb-search" x-model="fieldSearch" placeholder="&#128269;  Search fields…">
                </div>
                <div class="rb-fields-body">
                    <template x-for="(table, tableName) in availableFields" :key="tableName">
                        <div x-show="tableVisible(table)">
                            <div class="rb-cat-label" x-text="table.label"></div>
                            <template x-for="field in table.fields.filter(f => fieldVisible(f))" :key="field.expression">
                                <button class="rb-chip"
                                        :class="field.category==='measures' ? 'rb-chip-mes' : 'rb-chip-dim'"
                                        draggable="true"
                                        @dragstart="startDrag($event, field)"
                                        @dblclick="addField(field)"
                                        :title="'Double-click or drag to add'"
                                        x-text="field.label">
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="rb-legend">
                    <span style="color:var(--rb-dim-fg);">
                        <span class="rb-dot rb-dot-dim"></span> Dimension
                    </span>
                    <span style="color:var(--rb-mes-fg);">
                        <span class="rb-dot rb-dot-mes"></span> Measure
                    </span>
                </div>
            </div>

            {{-- ─── RIGHT ───────────────────────────────── --}}
            <div class="rb-right">

                {{-- Columns bar --}}
                <div class="rb-columns-bar">
                    <div class="rb-columns-head">
                        <div class="rb-columns-head-left">
                            &#9776; Report Columns
                            <span x-show="fields.length > 0" class="rb-col-count" x-text="fields.length"></span>
                        </div>
                        <span x-show="hasAggregates()" class="rb-agg-hint">&#9432; Non-aggregated fields will be grouped</span>
                    </div>
                    <div class="rb-columns-body">
                        <div class="rb-dropzone"
                             :class="{ over: draggingOver }"
                             @dragover.prevent="draggingOver=true"
                             @dragleave="draggingOver=false"
                             @drop="onDrop($event)">

                            <span x-show="fields.length===0" class="rb-dz-hint">
                                &#8592; Drag columns here, or double-click them in the panel
                            </span>

                            <template x-for="(field, i) in fields" :key="i">
                                <div class="rb-pill" :class="field.category==='measures' ? 'rb-pill-mes' : 'rb-pill-dim'">
                                    <span x-text="field.label"></span>
                                    <template x-if="field.category==='measures'">
                                        <select x-model="field.aggregate" class="rb-pill select">
                                            <option value="">Raw</option>
                                            <option value="COUNT">COUNT</option>
                                            <option value="SUM">SUM</option>
                                            <option value="AVG">AVG</option>
                                            <option value="MIN">MIN</option>
                                            <option value="MAX">MAX</option>
                                        </select>
                                    </template>
                                    <button class="rb-pill-x" @click="removeField(i)">&times;</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Results --}}
                <div class="rb-results">
                    <div class="rb-results-head">
                        <div class="rb-results-title">
                            Results
                            <span x-show="reportData && reportData.length > 0"
                                  class="rb-row-badge"
                                  x-text="(reportData?.length||0)+' row'+((reportData?.length??0)===1?'':'s')">
                            </span>
                        </div>
                        <span x-show="reportData && reportData.length > 0"
                              style="font-size:11px;color:var(--rb-sub);"
                              x-text="fields.length + ' column' + (fields.length===1?'':'s')">
                        </span>
                    </div>

                    {{-- Idle --}}
                    <div x-show="!reportData && !loading" class="rb-results-empty">
                        <div class="rb-results-empty-icon">&#128202;</div>
                        <p class="rb-results-empty-title">Your report will appear here</p>
                        <p class="rb-results-empty-sub">Add columns from the left panel, set filters above, then hit <strong>Run Report</strong></p>
                    </div>

                    {{-- Loading --}}
                    <div x-show="loading" class="rb-results-empty">
                        <div class="rb-spinner"></div>
                        <p class="rb-results-empty-title">Fetching data…</p>
                        <p class="rb-results-empty-sub">Hang tight</p>
                    </div>

                    {{-- No rows --}}
                    <div x-show="reportData && reportData.length===0 && !loading" class="rb-results-empty">
                        <div class="rb-results-empty-icon">&#128269;</div>
                        <p class="rb-results-empty-title">No results found</p>
                        <p class="rb-results-empty-sub">Try broadening your filters or choosing a different data source</p>
                    </div>

                    {{-- Table --}}
                    <div x-show="reportData && reportData.length > 0" class="rb-results-body">
                        <table class="rb-table">
                            <thead>
                                <tr>
                                    <template x-for="col in reportColumns" :key="col">
                                        <th x-text="col"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row,i) in reportData" :key="i">
                                    <tr>
                                        <template x-for="col in reportColumns" :key="col">
                                            <td x-text="row[col] ?? '—'"></td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                </div>{{-- /rb-results --}}
            </div>{{-- /rb-right --}}
        </div>{{-- /rb-body --}}
    </div>{{-- /rb-page --}}


    {{-- ══════════════════ MODALS ══════════════════ --}}

    {{-- Save template --}}
    <div x-show="showSaveModal" x-transition class="rb-backdrop" @click.self="showSaveModal=false">
        <div class="rb-modal">
            <h3>&#128190; Save as Template</h3>
            <div style="margin-bottom:12px;">
                <label class="rb-label">Name *</label>
                <input type="text" x-model="saveForm.name" class="rb-input"
                       placeholder="e.g. Monthly Terminal Report"
                       @keydown.enter="saveTemplate()">
            </div>
            <div style="margin-bottom:14px;">
                <label class="rb-label">Description</label>
                <textarea x-model="saveForm.description" rows="2" class="rb-input" style="resize:vertical;height:60px;"></textarea>
            </div>
            @if($canManageTemplates)
            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--rb-text);">
                    <input type="checkbox" x-model="saveForm.isGlobal"> Make visible to all users
                </label>
            </div>
            @endif
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button @click="showSaveModal=false" class="rb-btn rb-btn-outline">Cancel</button>
                <button @click="saveTemplate()" :disabled="!saveForm.name" class="rb-btn rb-btn-primary">Save Template</button>
            </div>
        </div>
    </div>

    {{-- Load template --}}
    <div x-show="showTemplateModal" x-transition class="rb-backdrop" @click.self="showTemplateModal=false">
        <div class="rb-modal-lg">
            <h3>&#128193; Load a Template</h3>

            <div x-show="templatesLoading" style="text-align:center;padding:30px;">
                <div class="rb-spinner"></div>
                <p style="color:var(--rb-sub);font-size:13px;margin:0;">Loading templates…</p>
            </div>

            <div x-show="!templatesLoading && availableTemplates.length===0"
                 style="text-align:center;padding:40px;color:#94a3b8;">
                <div style="font-size:40px;margin-bottom:12px;">&#128193;</div>
                <p style="font-size:14px;font-weight:600;color:#475569;margin:0 0 4px;">No templates yet</p>
                <p style="font-size:13px;margin:0;">Save a report configuration above to create one.</p>
            </div>

            <div x-show="!templatesLoading && availableTemplates.length > 0"
                 style="flex:1;overflow-y:auto;margin-bottom:14px;">
                <template x-for="tpl in availableTemplates" :key="tpl.id">
                    <div class="rb-tpl" @click="applyTemplate(tpl)">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:4px;">
                            <span class="rb-tpl-name" x-text="tpl.name"></span>
                            <span x-show="tpl.is_global" class="rb-badge-global">Global</span>
                        </div>
                        <div class="rb-tpl-desc" x-text="tpl.description || 'No description'"></div>
                        <div class="rb-tpl-meta"
                             x-text="'Created by ' + (tpl.creator?.first_name || 'Unknown') + ' ' + (tpl.creator?.last_name || '')"></div>
                    </div>
                </template>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button @click="showTemplateModal=false" class="rb-btn rb-btn-outline">Close</button>
            </div>
        </div>
    </div>

    {{-- Data source confirm --}}
    <div x-show="showSourceConfirm" x-transition class="rb-backdrop">
        <div class="rb-modal rb-confirm">
            <div class="rb-confirm-icon">&#9888;&#65039;</div>
            <h3 style="text-align:center;">Change Data Source?</h3>
            <p>Switching data sources will clear your current column selection. This can't be undone.</p>
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button @click="cancelDataSourceChange()" class="rb-btn rb-btn-outline">Cancel</button>
                <button @click="confirmDataSourceChange()" class="rb-btn rb-btn-danger">Yes, Switch It</button>
            </div>
        </div>
    </div>

</div>


@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('reportBuilder', () => ({

    availableFields:    @json($fields),
    availableFilters:   @json($filters),
    canManageTemplates: @json($canManageTemplates),

    loading:            false,
    templatesLoading:   false,
    fieldSearch:        '',
    draggingOver:       false,
    errorMessage:       '',
    successMessage:     '',
    showSaveModal:      false,
    showTemplateModal:  false,
    showSourceConfirm:  false,
    pendingBaseTable:   '',
    availableTemplates: [],

    fields: [],

    config: {
      baseTable:  'pos_terminals',
      regionId:   '',
      clientId:   '',
      dateColumn: '',
      dateFrom:   '',
      dateTo:     '',
      limit:      100,
    },

    saveForm: { name:'', description:'', isGlobal:false },
    reportData:    null,
    reportColumns: [],

    init() { /* templates load lazily */ },

    tableVisible(table) {
      if (!this.fieldSearch) return true;
      return table.fields.some(f => this.fieldVisible(f));
    },
    fieldVisible(field) {
      if (!this.fieldSearch) return true;
      return field.label.toLowerCase().includes(this.fieldSearch.toLowerCase());
    },

    addField(field) {
      if (this.fields.some(f => f.expression === field.expression)) {
        this.errorMessage = `"${field.label}" is already in the report.`;
        return;
      }
      this.fields.push({ ...field, aggregate:'' });
      this.errorMessage = '';
    },

    startDrag(event, field) {
      event.dataTransfer.setData('application/json', JSON.stringify(field));
      event.dataTransfer.effectAllowed = 'copy';
    },
    onDrop(event) {
      event.preventDefault();
      this.draggingOver = false;
      try {
        const field = JSON.parse(event.dataTransfer.getData('application/json'));
        this.addField(field);
      } catch(e) {
        this.errorMessage = 'Drop failed: ' + e.message;
      }
    },

    removeField(i) {
      this.fields.splice(i, 1);
      if (!this.fields.length) { this.reportData = null; this.reportColumns = []; }
    },

    onDataSourceChange() {
      if (this.fields.length > 0) {
        this.pendingBaseTable  = this.config.baseTable;
        this.showSourceConfirm = true;
      }
    },
    confirmDataSourceChange() { this.clearAll(); this.showSourceConfirm = false; },
    cancelDataSourceChange() {
      const keys = Object.keys(this.availableFields);
      this.config.baseTable  = keys.find(k => k !== this.config.baseTable) || keys[0];
      this.showSourceConfirm = false;
    },

    clearAll() {
      this.fields = []; this.reportData = null; this.reportColumns = [];
      this.errorMessage = ''; this.successMessage = '';
    },

    hasAggregates() {
      return this.fields.some(f => f.aggregate && f.aggregate !== '');
    },

    buildPayload() {
      const withAgg = this.hasAggregates();
      const select = [], group_by = [];
      this.fields.forEach(f => {
        if (f.aggregate && f.aggregate !== '') {
          select.push({ expr:f.expression, as:f.aggregate+'('+f.label+')', aggregate:f.aggregate });
        } else {
          select.push({ expr:f.expression, as:f.label });
          if (withAgg) group_by.push(f.expression);
        }
      });
      const where = [];
      if (this.config.regionId) {
        const bf = this.availableFields[this.config.baseTable]?.fields || [];
        if (bf.some(f => f.name === 'region_id')) {
          // Table has FK region_id — filter via regions.name (auto-join handles the JOIN)
          where.push({ column: 'regions.name', operator: '=', value: this.config.regionId });
        } else {
          // Table stores region as a plain string column (e.g. pos_terminals.region)
          where.push({ column: this.config.baseTable + '.region', operator: '=', value: this.config.regionId });
        }
      }
      if (this.config.clientId) {
        const bf = this.availableFields[this.config.baseTable]?.fields || [];
        where.push({ column: bf.some(f=>f.name==='client_id') ? this.config.baseTable+'.client_id' : 'pos_terminals.client_id', operator:'=', value:this.config.clientId });
      }
      if (this.config.dateColumn && (this.config.dateFrom || this.config.dateTo)) {
        where.push({ column:this.config.dateColumn, operator:'between_dates', value:{from:this.config.dateFrom||null, to:this.config.dateTo||null} });
      }
      return { base:{table:this.config.baseTable}, select, joins:[], group_by, where, limit:this.config.limit };
    },

    async runReport() {
      if (!this.fields.length) { this.errorMessage = 'Add at least one column first.'; return; }
      this.loading = true; this.errorMessage = ''; this.successMessage = '';
      try {
        const res  = await fetch('/api/report/preview', {
          method:'POST', credentials:'same-origin',
          headers:{ 'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest' },
          body: JSON.stringify(this.buildPayload())
        });
        const text = await res.text();
        if (text.trim().startsWith('<!')) { this.errorMessage = 'Session expired — please refresh.'; return; }
        const r = JSON.parse(text);
        if (r.success) { this.reportData = r.data; this.reportColumns = r.data.length ? Object.keys(r.data[0]) : []; }
        else this.errorMessage = r.error || 'Unknown error';
      } catch(e) { this.errorMessage = 'Request failed: '+e.message; }
      finally { this.loading = false; }
    },

    async exportReport(format) {
      this.errorMessage = '';
      try {
        const payload = this.buildPayload();
        payload.format = format; payload.filename = 'report_'+new Date().toISOString().slice(0,10); payload.download_all = true;
        const res = await fetch('/api/report/export', {
          method:'POST', credentials:'same-origin',
          headers:{ 'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest' },
          body: JSON.stringify(payload)
        });
        if (res.ok) {
          const blob = await res.blob();
          const url  = URL.createObjectURL(blob);
          const a    = Object.assign(document.createElement('a'),{href:url,download:payload.filename+'.'+format});
          document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
        } else {
          const r = await res.json().catch(()=>({}));
          this.errorMessage = 'Export failed: '+(r.error||res.statusText);
        }
      } catch(e) { this.errorMessage = 'Export failed: '+e.message; }
    },

    async loadTemplates() {
      this.templatesLoading = true;
      try {
        const res  = await fetch('/api/report/templates', { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
        const text = await res.text();
        if (text.trim().startsWith('<!')) return;
        const r = JSON.parse(text);
        if (r.success) this.availableTemplates = Array.isArray(r.data) ? r.data : (r.data?.data || []);
      } catch(e) { console.error(e); }
      finally { this.templatesLoading = false; }
    },

    openTemplateModal() { this.showTemplateModal = true; this.loadTemplates(); },

    applyTemplate(tpl) {
      try {
        const p = tpl.payload;
        if (!p) { this.errorMessage='Template has no payload.'; return; }
        this.fields = Array.isArray(p.fields) ? p.fields : [];
        this.config = { baseTable:p.baseTable||'pos_terminals', regionId:p.regionId||'', clientId:p.clientId||'', dateColumn:p.dateColumn||'', dateFrom:p.dateFrom||'', dateTo:p.dateTo||'', limit:p.limit||100 };
        this.reportData = null; this.reportColumns = [];
        this.showTemplateModal = false;
        this.successMessage = `"${tpl.name}" loaded — click Run Report.`;
      } catch(e) { this.errorMessage='Failed to load template: '+e.message; }
    },

    async saveTemplate() {
      if (!this.saveForm.name) { this.errorMessage='Enter a template name.'; return; }
      try {
        const res = await fetch('/api/report/templates', {
          method:'POST', credentials:'same-origin',
          headers:{ 'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest' },
          body: JSON.stringify({ name:this.saveForm.name, description:this.saveForm.description, is_global:this.saveForm.isGlobal, payload:{fields:this.fields,baseTable:this.config.baseTable,regionId:this.config.regionId,clientId:this.config.clientId,dateColumn:this.config.dateColumn,dateFrom:this.config.dateFrom,dateTo:this.config.dateTo,limit:this.config.limit} })
        });
        const text = await res.text();
        if (text.trim().startsWith('<!')) { this.errorMessage='Session expired.'; return; }
        const r = JSON.parse(text);
        if (r.success) { this.showSaveModal=false; this.saveForm={name:'',description:'',isGlobal:false}; this.successMessage='Template saved!'; this.loadTemplates(); }
        else this.errorMessage = r.error||'Save failed';
      } catch(e) { this.errorMessage='Save failed: '+e.message; }
    },

  }));
});
</script>
@endpush

@endsection
