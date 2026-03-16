@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
/* ── No-flash fix ───────────────────────────────────────── */
[x-cloak] { display: none !important; }

/* ── Layout ─────────────────────────────────────────────── */
.rb-wrap   { display:flex; flex-direction:column; gap:0; height:calc(100vh - 160px); min-height:560px; }
.rb-body   { display:grid; grid-template-columns:240px 1fr; gap:14px; flex:1; min-height:0; overflow:hidden; }

/* ── Cards ───────────────────────────────────────────────── */
.rb-card   { background:#fff; border-radius:12px; border:1px solid #e5e7eb; box-shadow:0 1px 4px rgba(0,0,0,.04); }
.rb-card-header {
    padding:14px 16px 0;
    font-size:12px; font-weight:700; color:#6b7280;
    text-transform:uppercase; letter-spacing:.07em;
    border-bottom:1px solid #f3f4f6; padding-bottom:10px; margin-bottom:0;
}

/* ── Header bar ──────────────────────────────────────────── */
.rb-topbar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
    padding:0 0 14px;
    border-bottom:1px solid #e5e7eb;
    margin-bottom:14px;
    flex-shrink:0;
}
.rb-topbar h2 { margin:0; font-size:20px; font-weight:700; color:#111827; }
.rb-topbar p  { margin:4px 0 0; font-size:13px; color:#6b7280; }
.rb-actions   { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }

/* ── Buttons ─────────────────────────────────────────────── */
.rb-btn { padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:1px solid transparent; transition:all .15s; line-height:1.4; white-space:nowrap; display:inline-flex; align-items:center; gap:5px; }
.rb-btn:disabled { opacity:.4; cursor:not-allowed; }
.rb-btn-primary  { background:#1d4ed8; color:#fff; border-color:#1d4ed8; }
.rb-btn-primary:hover:not(:disabled)  { background:#1e40af; }
.rb-btn-success  { background:#059669; color:#fff; border-color:#059669; }
.rb-btn-success:hover:not(:disabled)  { background:#047857; }
.rb-btn-outline  { background:#fff; color:#374151; border-color:#d1d5db; }
.rb-btn-outline:hover:not(:disabled)  { background:#f9fafb; border-color:#9ca3af; }
.rb-btn-ghost    { background:transparent; color:#6b7280; border-color:transparent; }
.rb-btn-ghost:hover:not(:disabled)    { background:#f3f4f6; color:#374151; }
.rb-btn-danger   { background:#fff; color:#dc2626; border-color:#fecaca; }
.rb-btn-danger:hover:not(:disabled)   { background:#fef2f2; }

/* ── Filters card ────────────────────────────────────────── */
.rb-filters {
    padding:14px 16px;
    flex-shrink:0;
    border-bottom:2px solid #f3f4f6;
}
.rb-filters-grid {
    display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;
}
.rb-filter-group { display:flex; flex-direction:column; gap:4px; flex:1; min-width:120px; }
.rb-filter-group label { font-size:10px; font-weight:800; color:#9ca3af; text-transform:uppercase; letter-spacing:.07em; }
.rb-filter-group select,
.rb-filter-group input {
    padding:7px 10px;
    border:1px solid #e5e7eb;
    border-radius:7px;
    font-size:13px;
    color:#374151;
    background:#f9fafb;
    transition:border-color .15s;
    width:100%;
    box-sizing:border-box;
}
.rb-filter-group select:focus,
.rb-filter-group input:focus {
    outline:none;
    border-color:#3b82f6;
    background:#fff;
    box-shadow:0 0 0 3px rgba(59,130,246,.1);
}
.rb-filter-divider {
    width:1px; height:32px; background:#e5e7eb;
    align-self:flex-end; flex-shrink:0; margin:0 2px 4px;
}

/* ── Left panel ──────────────────────────────────────────── */
.rb-left {
    overflow-y:auto;
    display:flex;
    flex-direction:column;
}
.rb-left-search {
    padding:12px 12px 8px;
    position:sticky; top:0;
    background:#fff;
    z-index:2;
    border-bottom:1px solid #f3f4f6;
}
.rb-left-search input {
    width:100%; padding:8px 12px;
    border:1px solid #e5e7eb; border-radius:7px;
    font-size:12px; color:#374151; background:#f9fafb;
    box-sizing:border-box;
}
.rb-left-search input:focus { outline:none; border-color:#3b82f6; background:#fff; }
.rb-left-body  { padding:10px 12px; flex:1; }
.rb-table-label {
    font-size:10px; font-weight:800; color:#9ca3af;
    text-transform:uppercase; letter-spacing:.08em;
    margin:10px 0 5px; padding-bottom:4px;
    border-bottom:1px solid #f3f4f6;
}
.rb-field-item {
    padding:6px 10px; margin-bottom:3px; border-radius:6px;
    font-size:12px; font-weight:500; cursor:grab;
    transition:transform .1s, box-shadow .1s;
    user-select:none; border:1px solid transparent;
}
.rb-field-item:hover  { transform:translateX(2px); box-shadow:0 2px 6px rgba(0,0,0,.06); }
.rb-field-item:active { cursor:grabbing; }
.rb-dimension { background:#f0fdf4; color:#166534; border-color:#bbf7d0; }
.rb-dimension:hover { background:#dcfce7; }
.rb-measure   { background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
.rb-measure:hover { background:#ffedd5; }

/* ── Right panel ─────────────────────────────────────────── */
.rb-right {
    display:flex; flex-direction:column;
    overflow:hidden; min-height:0;
}
.rb-right-top {
    flex-shrink:0;
    border-bottom:1px solid #f3f4f6;
}

/* ── Drop zone ───────────────────────────────────────────── */
.rb-dropzone-wrap { padding:12px 16px; }
.rb-dropzone {
    min-height:64px; padding:10px 12px;
    border:2px dashed #d1d5db; border-radius:8px;
    background:#fafafa; transition:all .2s;
}
.rb-dropzone-over  { border-color:#3b82f6; background:#eff6ff; }
.rb-dropzone-empty { display:flex; align-items:center; justify-content:center; height:64px; color:#9ca3af; font-size:13px; }

/* ── Pills ───────────────────────────────────────────────── */
.rb-pill {
    display:inline-flex; align-items:center; padding:5px 10px;
    border-radius:20px; font-size:12px; font-weight:500;
    border:1px solid transparent;
}
.rb-pill-dim     { background:#f0fdf4; color:#166534; border-color:#86efac; }
.rb-pill-measure { background:#fff7ed; color:#9a3412; border-color:#fdba74; }
.rb-pill-remove  { margin-left:5px; background:none; border:none; cursor:pointer; font-size:14px; line-height:1; padding:0; opacity:.5; }
.rb-pill-remove:hover { opacity:1; }

/* ── Results ─────────────────────────────────────────────── */
.rb-results {
    flex:1; overflow:hidden; display:flex; flex-direction:column;
    min-height:0;
}
.rb-results-header {
    padding:12px 16px 10px;
    display:flex; align-items:center; justify-content:space-between;
    flex-shrink:0;
    border-bottom:1px solid #f3f4f6;
}
.rb-results-body { flex:1; overflow:auto; min-height:0; }
.rb-results-empty {
    flex:1; display:flex; align-items:center; justify-content:center;
    color:#9ca3af; text-align:center; padding:40px;
}

/* ── Table ───────────────────────────────────────────────── */
.rb-table { width:100%; border-collapse:collapse; font-size:13px; }
.rb-table thead { position:sticky; top:0; z-index:1; background:#f9fafb; }
.rb-table th {
    padding:10px 14px; text-align:left;
    font-size:11px; font-weight:700; color:#374151;
    text-transform:uppercase; letter-spacing:.05em;
    border-bottom:2px solid #e5e7eb; white-space:nowrap;
}
.rb-table td { padding:9px 14px; color:#111827; border-bottom:1px solid #f3f4f6; }
.rb-table tbody tr:hover td { background:#f9fafb; }

/* ── Notifications ───────────────────────────────────────── */
.rb-alert {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px; border-radius:8px; font-size:13px;
    margin-bottom:12px; flex-shrink:0;
}
.rb-alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; }
.rb-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.rb-alert button  { background:none; border:none; cursor:pointer; font-size:18px; line-height:1; padding:0 0 0 10px; color:inherit; opacity:.7; }
.rb-alert button:hover { opacity:1; }

/* ── Spinner ─────────────────────────────────────────────── */
.rb-spinner { width:28px; height:28px; border:3px solid #e5e7eb; border-top-color:#3b82f6; border-radius:50%; animation:rb-spin .8s linear infinite; margin:0 auto; }
@keyframes rb-spin { to { transform:rotate(360deg); } }

/* ── Modal ───────────────────────────────────────────────── */
.rb-modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.45); display:flex; align-items:center; justify-content:center; z-index:300; }
.rb-modal { padding:24px; width:440px; max-width:92vw; }
.rb-modal-lg { padding:24px; width:600px; max-width:92vw; max-height:78vh; display:flex; flex-direction:column; }
.rb-modal h3 { margin:0 0 16px; font-size:16px; font-weight:700; color:#111827; }
.rb-field-label { font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; }
.rb-input {
    width:100%; padding:9px 11px; border:1px solid #d1d5db;
    border-radius:7px; font-size:14px; box-sizing:border-box;
    transition:border-color .15s;
}
.rb-input:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }

/* ── Badge ───────────────────────────────────────────────── */
.rb-badge { font-size:10px; font-weight:700; border-radius:4px; padding:2px 7px; border:1px solid transparent; }
.rb-badge-global { background:#dcfce7; color:#166534; border-color:#bbf7d0; }

/* ── Field count hint ────────────────────────────────────── */
.rb-hint { font-size:11px; color:#9ca3af; margin-top:8px; }

/* ── Dropdown menu ───────────────────────────────────────── */
.rb-menu {
    position:absolute; right:0; top:calc(100% + 4px);
    background:#fff; border:1px solid #e5e7eb;
    border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.1);
    z-index:50; min-width:150px; overflow:hidden;
}
.rb-menu button {
    display:block; width:100%; text-align:left;
    padding:10px 14px; border:none; background:none;
    color:#374151; font-size:13px; cursor:pointer;
}
.rb-menu button:hover { background:#f9fafb; }
</style>

<div x-data="reportBuilder()" x-cloak>

    {{-- ── Notifications ─────────────────────────────────── --}}
    <div x-show="errorMessage" x-transition class="rb-alert rb-alert-error">
        <span x-text="errorMessage"></span>
        <button @click="errorMessage=''">&times;</button>
    </div>
    <div x-show="successMessage" x-transition class="rb-alert rb-alert-success">
        <span x-text="successMessage"></span>
        <button @click="successMessage=''">&times;</button>
    </div>

    <div class="rb-wrap">

        {{-- ── Top bar ────────────────────────────────────── --}}
        <div class="rb-topbar">
            <div>
                <h2>Report Builder</h2>
                <p>Choose a data source, set filters, drag columns into your report, then run it.</p>
            </div>
            <div class="rb-actions">
                <button @click="runReport()" :disabled="loading || fields.length === 0" class="rb-btn rb-btn-primary">
                    <span x-show="!loading">&#9654; Run Report</span>
                    <span x-show="loading" x-cloak>&#9203; Running…</span>
                </button>

                <div style="position:relative;" x-data="{ open: false }">
                    <button @click="open = !open" :disabled="!reportData || reportData.length === 0" class="rb-btn rb-btn-success">
                        &#11015; Export &#9662;
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="rb-menu">
                        <button @click="exportReport('csv'); open = false">&#128196;&nbsp; CSV</button>
                    </div>
                </div>

                @if($canManageTemplates)
                <button @click="showSaveModal = true" :disabled="fields.length === 0" class="rb-btn rb-btn-outline">
                    &#128190; Save Template
                </button>
                @endif

                <button @click="openTemplateModal()" class="rb-btn rb-btn-outline">
                    &#128193; Templates
                </button>

                <button @click="clearAll()" x-show="fields.length > 0" class="rb-btn rb-btn-danger">
                    &#128465; Clear
                </button>
            </div>
        </div>

        {{-- ── Body ──────────────────────────────────────── --}}
        <div class="rb-body">

            {{-- ── LEFT: Field browser ─────────────────── --}}
            <div class="rb-card rb-left">
                <div class="rb-left-search">
                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">Available Fields</div>
                    <input type="text" x-model="fieldSearch" placeholder="&#128269; Search fields…">
                    <div style="display:flex;gap:8px;margin-top:8px;">
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:#166534;">
                            <span style="width:8px;height:8px;background:#bbf7d0;border-radius:50%;display:inline-block;"></span> Dimension
                        </span>
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:#9a3412;">
                            <span style="width:8px;height:8px;background:#fed7aa;border-radius:50%;display:inline-block;"></span> Measure
                        </span>
                    </div>
                </div>
                <div class="rb-left-body">
                    <template x-for="(table, tableName) in availableFields" :key="tableName">
                        <div x-show="tableVisible(table)">
                            <div class="rb-table-label" x-text="table.label"></div>
                            <template x-for="field in table.fields.filter(f => fieldVisible(f))" :key="field.expression">
                                <div class="rb-field-item"
                                     :class="field.category === 'measures' ? 'rb-measure' : 'rb-dimension'"
                                     draggable="true"
                                     @dragstart="startDrag($event, field)"
                                     @dblclick="addField(field)"
                                     :title="'Click &amp; drag — or double-click to add'"
                                     x-text="field.label">
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ── RIGHT: Filters + Drop zone + Results ── --}}
            <div class="rb-card rb-right">

                {{-- ── Filters ─────────────────────────── --}}
                <div class="rb-right-top">
                    <div class="rb-filters">
                        <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">
                            &#128270; Filters &amp; Options
                        </div>
                        <div class="rb-filters-grid">

                            <div class="rb-filter-group" style="min-width:150px;max-width:180px;">
                                <label>Data Source</label>
                                <select x-model="config.baseTable" @change="onDataSourceChange()">
                                    <template x-for="(t, tn) in availableFields" :key="tn">
                                        <option :value="tn" x-text="t.label"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="rb-filter-divider"></div>

                            <div class="rb-filter-group">
                                <label>Region</label>
                                <select x-model="config.regionId">
                                    <option value="">All Regions</option>
                                    <template x-for="(name, id) in availableFilters.regions" :key="id">
                                        <option :value="id" x-text="name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="rb-filter-group">
                                <label>Client</label>
                                <select x-model="config.clientId">
                                    <option value="">All Clients</option>
                                    <template x-for="(name, id) in availableFilters.clients" :key="id">
                                        <option :value="id" x-text="name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="rb-filter-divider"></div>

                            <div class="rb-filter-group" style="min-width:150px;">
                                <label>Date Column</label>
                                <select x-model="config.dateColumn">
                                    <option value="">No date filter</option>
                                    <template x-for="(t, tn) in availableFields" :key="tn">
                                        <template x-for="f in t.fields.filter(f => f.type === 'date')" :key="f.expression">
                                            <option :value="f.expression" x-text="t.label + ' › ' + f.label"></option>
                                        </template>
                                    </template>
                                </select>
                            </div>

                            <div class="rb-filter-group" style="max-width:140px;">
                                <label>From Date</label>
                                <input type="date" x-model="config.dateFrom" :disabled="!config.dateColumn"
                                       style="cursor:pointer;" :style="!config.dateColumn ? 'opacity:.4' : ''">
                            </div>

                            <div class="rb-filter-group" style="max-width:140px;">
                                <label>To Date</label>
                                <input type="date" x-model="config.dateTo" :disabled="!config.dateColumn"
                                       style="cursor:pointer;" :style="!config.dateColumn ? 'opacity:.4' : ''">
                            </div>

                            <div class="rb-filter-divider"></div>

                            <div class="rb-filter-group" style="max-width:90px;">
                                <label>Row Limit</label>
                                <input type="number" x-model.number="config.limit" min="1" max="10000"
                                       style="text-align:center;">
                            </div>

                        </div>
                    </div>

                    {{-- ── Drop zone ────────────────────── --}}
                    <div class="rb-dropzone-wrap">
                        <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">
                            &#9776; Report Columns
                            <span x-show="fields.length > 0" style="margin-left:8px;font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;"
                                  x-text="'(' + fields.length + ' selected)'"></span>
                        </div>

                        <div class="rb-dropzone"
                             :class="{ 'rb-dropzone-over': draggingOver }"
                             @dragover.prevent="draggingOver = true"
                             @dragleave="draggingOver = false"
                             @drop="onDrop($event)">

                            <div x-show="fields.length === 0" class="rb-dropzone-empty">
                                <span>&#8592; Drag fields here — or double-click them in the left panel</span>
                            </div>

                            <div style="display:flex;flex-wrap:wrap;gap:6px;align-content:flex-start;">
                                <template x-for="(field, i) in fields" :key="i">
                                    <div class="rb-pill" :class="field.category === 'measures' ? 'rb-pill-measure' : 'rb-pill-dim'">
                                        <span x-text="field.label" style="font-size:12px;font-weight:500;"></span>
                                        <template x-if="field.category === 'measures'">
                                            <select x-model="field.aggregate"
                                                    style="margin-left:6px;border:none;background:transparent;font-size:11px;cursor:pointer;color:inherit;outline:none;">
                                                <option value="">Raw</option>
                                                <option value="COUNT">COUNT</option>
                                                <option value="SUM">SUM</option>
                                                <option value="AVG">AVG</option>
                                                <option value="MIN">MIN</option>
                                                <option value="MAX">MAX</option>
                                            </select>
                                        </template>
                                        <button @click="removeField(i)" class="rb-pill-remove">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="hasAggregates()" class="rb-hint">
                            &#9432; Aggregated fields will GROUP BY all non-aggregated columns
                        </div>
                    </div>
                </div>

                {{-- ── Results ──────────────────────────── --}}
                <div class="rb-results">
                    <div class="rb-results-header">
                        <span style="font-size:13px;font-weight:700;color:#111827;">Results</span>
                        <span x-show="reportData && reportData.length > 0"
                              style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 10px;border-radius:20px;"
                              x-text="(reportData?.length || 0) + ' row' + ((reportData?.length ?? 0) === 1 ? '' : 's')"></span>
                    </div>

                    {{-- Empty state --}}
                    <div x-show="!reportData && !loading" class="rb-results-empty">
                        <div>
                            <div style="font-size:40px;margin-bottom:10px;">&#128202;</div>
                            <p style="margin:0;font-size:14px;font-weight:600;color:#374151;">No report run yet</p>
                            <p style="margin:6px 0 0;font-size:13px;color:#9ca3af;">Select columns above and click <strong>Run Report</strong></p>
                        </div>
                    </div>

                    {{-- Loading --}}
                    <div x-show="loading" class="rb-results-empty">
                        <div>
                            <div class="rb-spinner"></div>
                            <p style="margin-top:12px;font-size:13px;color:#6b7280;">Running report…</p>
                        </div>
                    </div>

                    {{-- No rows --}}
                    <div x-show="reportData && reportData.length === 0 && !loading" class="rb-results-empty">
                        <div>
                            <div style="font-size:36px;margin-bottom:10px;">&#128269;</div>
                            <p style="margin:0;font-size:14px;font-weight:600;color:#374151;">No results</p>
                            <p style="margin:6px 0 0;font-size:13px;color:#9ca3af;">Try adjusting your filters or columns</p>
                        </div>
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
                                <template x-for="(row, i) in reportData" :key="i">
                                    <tr>
                                        <template x-for="col in reportColumns" :key="col">
                                            <td x-text="row[col] ?? '—'"></td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>{{-- /rb-right --}}
        </div>{{-- /rb-body --}}
    </div>{{-- /rb-wrap --}}


    {{-- ── SAVE TEMPLATE MODAL ─────────────────────────────── --}}
    <div x-show="showSaveModal" x-transition class="rb-modal-backdrop" @click.self="showSaveModal = false">
        <div class="rb-card rb-modal">
            <h3>&#128190; Save Template</h3>
            <div style="margin-bottom:12px;">
                <div class="rb-field-label">Name *</div>
                <input type="text" x-model="saveForm.name" placeholder="e.g. Monthly Terminal Report"
                       class="rb-input" @keydown.enter="saveTemplate()">
            </div>
            <div style="margin-bottom:14px;">
                <div class="rb-field-label">Description</div>
                <textarea x-model="saveForm.description" rows="2" class="rb-input" style="resize:vertical;"></textarea>
            </div>
            @if($canManageTemplates)
            <div style="margin-bottom:18px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151;">
                    <input type="checkbox" x-model="saveForm.isGlobal"> Make visible to all users
                </label>
            </div>
            @endif
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button @click="showSaveModal = false" class="rb-btn rb-btn-outline">Cancel</button>
                <button @click="saveTemplate()" :disabled="!saveForm.name" class="rb-btn rb-btn-primary">Save</button>
            </div>
        </div>
    </div>

    {{-- ── LOAD TEMPLATE MODAL ─────────────────────────────── --}}
    <div x-show="showTemplateModal" x-transition class="rb-modal-backdrop" @click.self="showTemplateModal = false">
        <div class="rb-card rb-modal-lg">
            <h3>&#128193; Load Template</h3>

            <div x-show="templatesLoading" style="text-align:center;padding:24px;color:#6b7280;font-size:14px;">
                <div class="rb-spinner" style="margin-bottom:10px;"></div>
                Loading templates…
            </div>

            <div x-show="!templatesLoading && availableTemplates.length === 0"
                 style="text-align:center;padding:40px;color:#9ca3af;font-size:14px;">
                <div style="font-size:36px;margin-bottom:10px;">&#128193;</div>
                No saved templates yet.
            </div>

            <div x-show="!templatesLoading && availableTemplates.length > 0"
                 style="flex:1;overflow-y:auto;margin-bottom:14px;display:flex;flex-direction:column;gap:8px;">
                <template x-for="tpl in availableTemplates" :key="tpl.id">
                    <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;cursor:pointer;transition:all .15s;"
                         @click="applyTemplate(tpl)"
                         @mouseenter="$el.style.borderColor='#3b82f6'; $el.style.background='#eff6ff'"
                         @mouseleave="$el.style.borderColor='#e5e7eb'; $el.style.background=''">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                            <span style="font-weight:600;color:#111827;font-size:14px;" x-text="tpl.name"></span>
                            <span x-show="tpl.is_global" class="rb-badge rb-badge-global">GLOBAL</span>
                        </div>
                        <div style="font-size:12px;color:#6b7280;" x-text="tpl.description || 'No description'"></div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:5px;"
                             x-text="'Created by ' + (tpl.creator?.first_name || 'Unknown') + ' ' + (tpl.creator?.last_name || '')"></div>
                    </div>
                </template>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button @click="showTemplateModal = false" class="rb-btn rb-btn-outline">Close</button>
            </div>
        </div>
    </div>

    {{-- ── DATA SOURCE CHANGE CONFIRM ───────────────────────── --}}
    <div x-show="showSourceConfirm" x-transition class="rb-modal-backdrop">
        <div class="rb-card rb-modal">
            <h3>&#9888; Change Data Source?</h3>
            <p style="font-size:14px;color:#374151;margin:0 0 20px;">
                Changing the data source will clear all your current column selections. Are you sure?
            </p>
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button @click="cancelDataSourceChange()" class="rb-btn rb-btn-outline">Cancel</button>
                <button @click="confirmDataSourceChange()" class="rb-btn rb-btn-danger">Yes, Change It</button>
            </div>
        </div>
    </div>

</div><!-- /x-data -->


@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('reportBuilder', () => ({

    // ── Server data ────────────────────────────────────────────────
    availableFields:    @json($fields),
    availableFilters:   @json($filters),
    canManageTemplates: @json($canManageTemplates),

    // ── State ─────────────────────────────────────────────────────
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

    // ── Fields ────────────────────────────────────────────────────
    fields: [],

    // ── Config ────────────────────────────────────────────────────
    config: {
      baseTable:  'pos_terminals',
      regionId:   '',
      clientId:   '',
      dateColumn: '',
      dateFrom:   '',
      dateTo:     '',
      limit:      100,
    },

    // ── Save form ─────────────────────────────────────────────────
    saveForm: { name: '', description: '', isGlobal: false },

    // ── Results ───────────────────────────────────────────────────
    reportData:    null,
    reportColumns: [],

    // ── Init ─────────────────────────────────────────────────────
    init() {
      // Templates loaded lazily when modal opens — no startup flicker
    },

    // ── Left panel helpers ────────────────────────────────────────
    tableVisible(table) {
      if (!this.fieldSearch) return true;
      return table.fields.some(f => this.fieldVisible(f));
    },
    fieldVisible(field) {
      if (!this.fieldSearch) return true;
      return field.label.toLowerCase().includes(this.fieldSearch.toLowerCase());
    },

    // ── Double-click to add ───────────────────────────────────────
    addField(field) {
      if (this.fields.some(f => f.expression === field.expression)) {
        this.errorMessage = `"${field.label}" is already added.`;
        return;
      }
      this.fields.push({ ...field, aggregate: '' });
      this.errorMessage = '';
    },

    // ── Drag ─────────────────────────────────────────────────────
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
      } catch (e) {
        this.errorMessage = 'Drop failed: ' + e.message;
      }
    },

    removeField(index) {
      this.fields.splice(index, 1);
      if (this.fields.length === 0) {
        this.reportData    = null;
        this.reportColumns = [];
      }
    },

    // ── Data source change (with confirm if fields exist) ─────────
    onDataSourceChange() {
      if (this.fields.length > 0) {
        this.pendingBaseTable  = this.config.baseTable;
        // revert immediately, show confirm
        this.config.baseTable  = this.pendingBaseTable;
        this.showSourceConfirm = true;
      }
    },
    confirmDataSourceChange() {
      this.clearAll();
      this.showSourceConfirm = false;
    },
    cancelDataSourceChange() {
      // restore previous value
      const keys = Object.keys(this.availableFields);
      const prev = keys.find(k => k !== this.config.baseTable) || keys[0];
      this.config.baseTable  = prev;
      this.showSourceConfirm = false;
    },

    clearAll() {
      this.fields         = [];
      this.reportData     = null;
      this.reportColumns  = [];
      this.errorMessage   = '';
      this.successMessage = '';
    },

    hasAggregates() {
      return this.fields.some(f => f.aggregate && f.aggregate !== '');
    },

    // ── Build query payload ───────────────────────────────────────
    buildPayload() {
      const withAgg  = this.hasAggregates();
      const select   = [];
      const group_by = [];

      this.fields.forEach(f => {
        if (f.aggregate && f.aggregate !== '') {
          select.push({ expr: f.expression, as: f.aggregate + '(' + f.label + ')', aggregate: f.aggregate });
        } else {
          select.push({ expr: f.expression, as: f.label });
          if (withAgg) group_by.push(f.expression);
        }
      });

      const where = [];

      if (this.config.regionId) {
        const baseFields = this.availableFields[this.config.baseTable]?.fields || [];
        const hasRegion  = baseFields.some(f => f.name === 'region_id');
        where.push({
          column:   hasRegion ? this.config.baseTable + '.region_id' : 'pos_terminals.region_id',
          operator: '=',
          value:    this.config.regionId,
        });
      }

      if (this.config.clientId) {
        const baseFields = this.availableFields[this.config.baseTable]?.fields || [];
        const hasClient  = baseFields.some(f => f.name === 'client_id');
        where.push({
          column:   hasClient ? this.config.baseTable + '.client_id' : 'pos_terminals.client_id',
          operator: '=',
          value:    this.config.clientId,
        });
      }

      if (this.config.dateColumn && (this.config.dateFrom || this.config.dateTo)) {
        where.push({
          column:   this.config.dateColumn,
          operator: 'between_dates',
          value:    { from: this.config.dateFrom || null, to: this.config.dateTo || null },
        });
      }

      return {
        base:    { table: this.config.baseTable },
        select,
        joins:   [],
        group_by,
        where,
        limit:   this.config.limit,
      };
    },

    // ── Run report ────────────────────────────────────────────────
    async runReport() {
      if (this.fields.length === 0) {
        this.errorMessage = 'Add at least one column to your report first.';
        return;
      }
      this.loading       = true;
      this.errorMessage  = '';
      this.successMessage= '';
      try {
        const res  = await fetch('/api/report/preview', {
          method: 'POST', credentials: 'same-origin',
          headers: {
            'Accept': 'application/json', 'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(this.buildPayload()),
        });
        const text = await res.text();
        if (text.trim().startsWith('<!')) { this.errorMessage = 'Session expired — please refresh.'; return; }
        const result = JSON.parse(text);
        if (result.success) {
          this.reportData    = result.data;
          this.reportColumns = result.data.length > 0 ? Object.keys(result.data[0]) : [];
        } else {
          this.errorMessage = result.error || 'Unknown error';
        }
      } catch (err) {
        this.errorMessage = 'Request failed: ' + err.message;
      } finally {
        this.loading = false;
      }
    },

    // ── Export ────────────────────────────────────────────────────
    async exportReport(format) {
      this.errorMessage = '';
      try {
        const payload      = this.buildPayload();
        payload.format     = format;
        payload.filename   = 'report_' + new Date().toISOString().slice(0, 10);
        payload.download_all = true;
        const res = await fetch('/api/report/export', {
          method: 'POST', credentials: 'same-origin',
          headers: {
            'Accept': 'application/json', 'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(payload),
        });
        if (res.ok) {
          const blob = await res.blob();
          const url  = URL.createObjectURL(blob);
          const a    = Object.assign(document.createElement('a'), { href: url, download: payload.filename + '.' + format });
          document.body.appendChild(a); a.click(); document.body.removeChild(a);
          URL.revokeObjectURL(url);
        } else {
          const r = await res.json().catch(() => ({}));
          this.errorMessage = 'Export failed: ' + (r.error || res.statusText);
        }
      } catch (err) {
        this.errorMessage = 'Export failed: ' + err.message;
      }
    },

    // ── Templates ─────────────────────────────────────────────────
    async loadTemplates() {
      this.templatesLoading = true;
      try {
        const res  = await fetch('/api/report/templates', {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const text = await res.text();
        if (text.trim().startsWith('<!')) return;
        const r = JSON.parse(text);
        if (r.success) this.availableTemplates = Array.isArray(r.data) ? r.data : (r.data?.data || []);
      } catch (e) {
        console.error('Template load error:', e);
      } finally {
        this.templatesLoading = false;
      }
    },

    openTemplateModal() {
      this.showTemplateModal = true;
      this.loadTemplates();
    },

    applyTemplate(tpl) {
      try {
        const p = tpl.payload;
        if (!p) { this.errorMessage = 'Template has no payload.'; return; }
        this.fields = Array.isArray(p.fields) ? p.fields : [];
        this.config = {
          baseTable:  p.baseTable  || 'pos_terminals',
          regionId:   p.regionId   || '',
          clientId:   p.clientId   || '',
          dateColumn: p.dateColumn || '',
          dateFrom:   p.dateFrom   || '',
          dateTo:     p.dateTo     || '',
          limit:      p.limit      || 100,
        };
        this.reportData        = null;
        this.reportColumns     = [];
        this.showTemplateModal = false;
        this.successMessage    = `"${tpl.name}" loaded — click Run Report to execute.`;
      } catch (e) {
        this.errorMessage = 'Failed to load template: ' + e.message;
      }
    },

    async saveTemplate() {
      if (!this.saveForm.name) { this.errorMessage = 'Enter a template name.'; return; }
      try {
        const res = await fetch('/api/report/templates', {
          method: 'POST', credentials: 'same-origin',
          headers: {
            'Accept': 'application/json', 'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({
            name: this.saveForm.name, description: this.saveForm.description,
            is_global: this.saveForm.isGlobal,
            payload: {
              fields: this.fields, baseTable: this.config.baseTable,
              regionId: this.config.regionId, clientId: this.config.clientId,
              dateColumn: this.config.dateColumn, dateFrom: this.config.dateFrom,
              dateTo: this.config.dateTo, limit: this.config.limit,
            },
          }),
        });
        const text = await res.text();
        if (text.trim().startsWith('<!')) { this.errorMessage = 'Session expired.'; return; }
        const r = JSON.parse(text);
        if (r.success) {
          this.showSaveModal  = false;
          this.saveForm       = { name: '', description: '', isGlobal: false };
          this.successMessage = 'Template saved!';
          this.loadTemplates();
        } else {
          this.errorMessage = r.error || 'Save failed';
        }
      } catch (e) {
        this.errorMessage = 'Save failed: ' + e.message;
      }
    },

  }));
});
</script>
@endpush

@endsection
