@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div x-data="reportBuilder()" x-init="init()">

    <!-- Inline notifications (replaces alert() calls) -->
    <div x-show="errorMessage" x-transition
         style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;color:#b91c1c;font-size:14px;">
        <span x-text="errorMessage"></span>
        <button @click="errorMessage=''" style="background:none;border:none;color:#b91c1c;cursor:pointer;font-weight:bold;font-size:18px;line-height:1;padding:0 0 0 12px;">×</button>
    </div>
    <div x-show="successMessage" x-transition
         style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;color:#166534;font-size:14px;">
        <span x-text="successMessage"></span>
        <button @click="successMessage=''" style="background:none;border:none;color:#166534;cursor:pointer;font-weight:bold;font-size:18px;line-height:1;padding:0 0 0 12px;">×</button>
    </div>

    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="margin:0;color:#111827;font-weight:700;letter-spacing:-.02em;">Report Builder</h2>
            <p style="margin:6px 0 0;color:#6b7280;font-size:14px;">Drag fields from the left panel into the drop zones, then click Run Report</p>
        </div>

        <!-- Toolbar -->
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button @click="runReport()"
                    :disabled="loading || !hasValidConfig()"
                    class="btn btn-secondary">
                <span x-show="!loading">&#x1F504; Run Report</span>
                <span x-show="loading">&#x23F3; Running...</span>
            </button>

            @if($canManageTemplates)
            <button @click="showSaveModal = true"
                    :disabled="!hasValidConfig()"
                    class="btn btn-secondary">
                &#x1F4BE; Save Template
            </button>
            @endif

            <div style="position:relative;" x-data="{ open: false }">
                <button @click="open = !open"
                        :disabled="!reportData || reportData.length === 0"
                        class="btn btn-outline">
                    &#x1F4CA; Export &#x25BE;
                </button>
                <div x-show="open" @click.outside="open = false"
                     style="position:absolute;right:0;top:100%;margin-top:4px;background:#fff;border:1px solid #e5e7eb;border-radius:6px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);z-index:50;min-width:150px;">
                    <button @click="exportReport('csv'); open = false"
                            style="display:block;width:100%;text-align:left;padding:8px 12px;border:none;background:none;color:#374151;font-size:14px;cursor:pointer;">
                        CSV Format
                    </button>
                </div>
            </div>

            <button @click="openTemplateModal()" class="btn btn-outline">
                &#x1F4C2; Load Template
            </button>
        </div>
    </div>

    <!-- Main 3-panel layout -->
    <div style="display:grid;grid-template-columns:280px 1fr 260px;gap:16px;height:calc(100vh - 210px);">

        <!-- ===== LEFT PANEL — Available Fields ===== -->
        <div class="content-card" style="overflow-y:auto;padding:16px;">
            <h4 class="card-title">Available Fields</h4>

            <div style="margin-bottom:12px;">
                <input type="text" x-model="fieldSearch" placeholder="Search fields..."
                       style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;box-sizing:border-box;">
            </div>

            <template x-for="(table, tableName) in availableFields" :key="tableName">
                <div style="margin-bottom:18px;" x-show="shouldShowTable(table)">
                    <div style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;padding-bottom:4px;border-bottom:1px solid #f3f4f6;"
                         x-text="table.label"></div>

                    <!-- Dimensions -->
                    <template x-if="table.fields.filter(f => f.category === 'dimensions' && matchesSearch(f.label)).length > 0">
                        <div style="margin-bottom:8px;">
                            <div class="field-category-label">&#x1F4CF; Dimensions</div>
                            <template x-for="field in table.fields.filter(f => f.category === 'dimensions')" :key="field.expression">
                                <div x-show="matchesSearch(field.label)"
                                     class="field-item dimension-field"
                                     draggable="true"
                                     @dragstart="startDrag($event, field, 'dimension')"
                                     :title="field.expression"
                                     x-text="field.label">
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Measures -->
                    <template x-if="table.fields.filter(f => f.category === 'measures' && matchesSearch(f.label)).length > 0">
                        <div>
                            <div class="field-category-label">&#x1F4CA; Measures</div>
                            <template x-for="field in table.fields.filter(f => f.category === 'measures')" :key="field.expression">
                                <div x-show="matchesSearch(field.label)"
                                     class="field-item measure-field"
                                     draggable="true"
                                     @dragstart="startDrag($event, field, 'measure')"
                                     :title="field.expression"
                                     x-text="field.label">
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- ===== CENTER PANEL — Drop Zones + Results ===== -->
        <div style="display:flex;flex-direction:column;gap:16px;overflow:hidden;">

            <!-- Drop Zones -->
            <div class="content-card" style="padding:16px;flex-shrink:0;">
                <h4 class="card-title">Report Configuration</h4>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">

                    <!-- Columns -->
                    <div>
                        <div class="zone-label">&#x1F4CA; Columns</div>
                        <div class="drop-zone"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave="handleDragLeave($event)"
                             @drop="handleDrop($event, 'columns')">
                            <template x-for="(field, index) in reportConfig.columns" :key="index">
                                <div class="field-pill dimension-pill">
                                    <span x-text="field.label"></span>
                                    <button @click="removeField('columns', index)" class="remove-btn">×</button>
                                </div>
                            </template>
                            <div x-show="reportConfig.columns.length === 0" class="drop-hint">Drop dimensions here</div>
                        </div>
                    </div>

                    <!-- Rows / Groups -->
                    <div>
                        <div class="zone-label">&#x1F4CB; Rows / Groups</div>
                        <div class="drop-zone"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave="handleDragLeave($event)"
                             @drop="handleDrop($event, 'rows')">
                            <template x-for="(field, index) in reportConfig.rows" :key="index">
                                <div class="field-pill dimension-pill">
                                    <span x-text="field.label"></span>
                                    <button @click="removeField('rows', index)" class="remove-btn">×</button>
                                </div>
                            </template>
                            <div x-show="reportConfig.rows.length === 0" class="drop-hint">Drop dimensions here</div>
                        </div>
                    </div>

                    <!-- Values / Aggregations -->
                    <div>
                        <div class="zone-label">&#x1F522; Values / Aggregations</div>
                        <div class="drop-zone"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave="handleDragLeave($event)"
                             @drop="handleDrop($event, 'values')">
                            <template x-for="(field, index) in reportConfig.values" :key="index">
                                <div class="field-pill measure-pill" style="flex-wrap:wrap;">
                                    <span x-text="field.label"></span>
                                    <select x-model="field.aggregate"
                                            style="margin-left:4px;border:none;background:transparent;font-size:11px;color:#92400e;">
                                        <option value="COUNT">COUNT</option>
                                        <option value="SUM">SUM</option>
                                        <option value="AVG">AVG</option>
                                        <option value="MIN">MIN</option>
                                        <option value="MAX">MAX</option>
                                    </select>
                                    <button @click="removeField('values', index)" class="remove-btn">×</button>
                                </div>
                            </template>
                            <div x-show="reportConfig.values.length === 0" class="drop-hint">Drop measures here</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="content-card" style="flex:1;overflow:hidden;display:flex;flex-direction:column;min-height:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <h4 class="card-title" style="margin:0;">&#x1F4C8; Results</h4>
                    <span x-show="reportData" style="font-size:13px;color:#6b7280;"
                          x-text="(reportData?.length || 0) + ' row' + (reportData?.length === 1 ? '' : 's')"></span>
                </div>

                <!-- Empty state -->
                <div x-show="!reportData && !loading"
                     style="flex:1;display:flex;align-items:center;justify-content:center;color:#9ca3af;text-align:center;">
                    <div>
                        <div style="font-size:40px;margin-bottom:12px;">&#x1F4CA;</div>
                        <p style="margin:0;font-size:14px;">Add fields and click <strong>Run Report</strong></p>
                    </div>
                </div>

                <!-- Loading -->
                <div x-show="loading"
                     style="flex:1;display:flex;align-items:center;justify-content:center;color:#6b7280;">
                    <div style="text-align:center;">
                        <div class="spinner"></div>
                        <p style="margin-top:12px;font-size:14px;">Generating report...</p>
                    </div>
                </div>

                <!-- No results -->
                <div x-show="reportData && reportData.length === 0 && !loading"
                     style="flex:1;display:flex;align-items:center;justify-content:center;color:#9ca3af;text-align:center;">
                    <div>
                        <div style="font-size:40px;margin-bottom:12px;">&#x1F50D;</div>
                        <p style="margin:0;font-size:14px;">No data found for this configuration</p>
                    </div>
                </div>

                <!-- Data table -->
                <div x-show="reportData && reportData.length > 0" style="flex:1;overflow:auto;min-height:0;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead style="background:#f9fafb;position:sticky;top:0;z-index:1;">
                            <tr>
                                <template x-for="column in reportColumns" :key="column">
                                    <th style="padding:10px 8px;text-align:left;font-weight:600;font-size:11px;color:#374151;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e5e7eb;white-space:nowrap;"
                                        x-text="column"></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in reportData" :key="index">
                                <tr style="border-bottom:1px solid #f3f4f6;"
                                    @mouseenter="$el.style.background='#f9fafb'"
                                    @mouseleave="$el.style.background='transparent'">
                                    <template x-for="column in reportColumns" :key="column">
                                        <td style="padding:8px;color:#111827;" x-text="row[column] ?? '—'"></td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT PANEL — Filters & Options ===== -->
        <div class="content-card" style="overflow-y:auto;padding:16px;">
            <h4 class="card-title">&#x2699;&#xFE0F; Filters &amp; Options</h4>

            <!-- Base Table -->
            <div style="margin-bottom:16px;">
                <div class="label">Base Table</div>
                <select x-model="reportConfig.baseTable"
                        @change="clearConfig()"
                        style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <template x-for="(tableInfo, tableName) in availableFields" :key="tableName">
                        <option :value="tableName" x-text="tableInfo.label"></option>
                    </template>
                </select>
                <div style="font-size:11px;color:#9ca3af;margin-top:3px;">Changing base table clears all fields</div>
            </div>

            <!-- Region Filter -->
            <div style="margin-bottom:16px;">
                <div class="label">Region</div>
                <select x-model="reportConfig.regionId"
                        style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <option value="">All Regions</option>
                    <template x-for="(name, id) in availableFilters.regions" :key="id">
                        <option :value="id" x-text="name"></option>
                    </template>
                </select>
            </div>

            <!-- Client Filter -->
            <div style="margin-bottom:16px;">
                <div class="label">Client</div>
                <select x-model="reportConfig.clientId"
                        style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <option value="">All Clients</option>
                    <template x-for="(name, id) in availableFilters.clients" :key="id">
                        <option :value="id" x-text="name"></option>
                    </template>
                </select>
            </div>

            <!-- Date Filter -->
            <div style="margin-bottom:16px;">
                <div class="label">Date Column</div>
                <select x-model="reportConfig.dateColumn"
                        style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <option value="">No date filter</option>
                    <template x-for="(tableInfo, tableName) in availableFields" :key="tableName">
                        <template x-for="field in tableInfo.fields.filter(f => f.type === 'date')" :key="field.expression">
                            <option :value="field.expression" x-text="tableInfo.label + ' › ' + field.label"></option>
                        </template>
                    </template>
                </select>
            </div>
            <div x-show="reportConfig.dateColumn" style="margin-bottom:12px;">
                <div class="label">From</div>
                <input type="date" x-model="reportConfig.dateFrom"
                       style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;box-sizing:border-box;">
            </div>
            <div x-show="reportConfig.dateColumn" style="margin-bottom:16px;">
                <div class="label">To</div>
                <input type="date" x-model="reportConfig.dateTo"
                       style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;box-sizing:border-box;">
            </div>

            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 16px;">

            <!-- Result Limit -->
            <div style="margin-bottom:12px;">
                <div class="label">Row Limit (preview)</div>
                <input type="number" x-model.number="reportConfig.limit" min="1" max="10000"
                       style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;box-sizing:border-box;">
            </div>

            <!-- Download All -->
            <div style="margin-bottom:16px;">
                <label style="display:flex;align-items:center;cursor:pointer;gap:8px;">
                    <input type="checkbox" x-model="reportConfig.downloadAll">
                    <span style="font-size:13px;color:#374151;">Export all rows (bypass limit)</span>
                </label>
            </div>

            <!-- Quick Clear -->
            <button @click="clearConfig()" class="btn btn-outline" style="width:100%;font-size:13px;">
                &#x1F5D1; Clear All Fields
            </button>
        </div>
    </div>

    <!-- ===== SAVE TEMPLATE MODAL ===== -->
    <div x-show="showSaveModal"
         style="position:fixed;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:200;">
        <div class="content-card" style="width:420px;max-width:92vw;" @click.stop>
            <h4 class="card-title">&#x1F4BE; Save Report Template</h4>
            <div style="margin-bottom:14px;">
                <div class="label">Template Name *</div>
                <input type="text" x-model="templateForm.name" placeholder="e.g. Monthly Terminal Summary"
                       style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <div class="label">Description</div>
                <textarea x-model="templateForm.description" rows="2" placeholder="Optional description..."
                          style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;resize:vertical;box-sizing:border-box;"></textarea>
            </div>
            @if($canManageTemplates)
            <div style="margin-bottom:16px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" x-model="templateForm.isGlobal">
                    <span style="font-size:14px;color:#374151;">Make global (visible to all users)</span>
                </label>
            </div>
            @endif
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button @click="showSaveModal = false; templateForm = {name:'',description:'',isGlobal:false}" class="btn btn-outline">Cancel</button>
                <button @click="saveTemplate()" :disabled="!templateForm.name" class="btn btn-secondary">Save</button>
            </div>
        </div>
    </div>

    <!-- ===== LOAD TEMPLATE MODAL ===== -->
    <div x-show="showTemplateModal"
         style="position:fixed;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:200;">
        <div class="content-card" style="width:640px;max-width:92vw;max-height:80vh;display:flex;flex-direction:column;" @click.stop>
            <h4 class="card-title">&#x1F4C2; Load Report Template</h4>

            <!-- Loading state -->
            <div x-show="templatesLoading" style="text-align:center;padding:24px;color:#6b7280;font-size:14px;">
                Loading templates...
            </div>

            <!-- No templates -->
            <div x-show="!templatesLoading && availableTemplates.length === 0"
                 style="text-align:center;padding:24px;color:#9ca3af;font-size:14px;">
                No saved templates found.
            </div>

            <!-- Template list -->
            <div x-show="!templatesLoading && availableTemplates.length > 0"
                 style="flex:1;overflow-y:auto;margin-bottom:16px;">
                <template x-for="template in availableTemplates" :key="template.id">
                    <div style="border:1px solid #e5e7eb;border-radius:6px;padding:12px;margin-bottom:8px;cursor:pointer;transition:background .15s;"
                         @click="loadTemplate(template)"
                         @mouseenter="$el.style.background='#f9fafb'"
                         @mouseleave="$el.style.background='transparent'">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <span style="font-weight:600;color:#111827;font-size:14px;" x-text="template.name"></span>
                            <span x-show="template.is_global"
                                  style="font-size:10px;font-weight:700;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:4px;padding:2px 6px;text-transform:uppercase;">
                                Global
                            </span>
                        </div>
                        <div style="font-size:13px;color:#6b7280;" x-text="template.description || 'No description'"></div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:4px;"
                             x-text="'By ' + (template.creator?.first_name || 'Unknown') + ' ' + (template.creator?.last_name || '')"></div>
                    </div>
                </template>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button @click="showTemplateModal = false" class="btn btn-outline">Close</button>
            </div>
        </div>
    </div>

</div><!-- /x-data -->

<style>
.content-card  { background:#fff; padding:20px; border-radius:10px; border:1px solid #e5e7eb; }
.card-title    { margin:0 0 12px; color:#111827; font-weight:700; font-size:15px; }
.label         { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; font-weight:600; }
.zone-label    { font-size:12px; color:#374151; font-weight:600; margin-bottom:6px; }

.btn           { padding:9px 14px; border:1px solid #d1d5db; border-radius:6px; background:#fff; color:#374151; cursor:pointer; font-weight:500; font-size:14px; transition:all .15s; line-height:1; }
.btn:hover     { border-color:#9ca3af; background:#f9fafb; }
.btn:disabled  { opacity:.45; cursor:not-allowed; }
.btn-secondary { background:#f3f4f6; border-color:#d1d5db; }
.btn-secondary:hover:not(:disabled) { background:#e5e7eb; border-color:#9ca3af; }
.btn-outline   { background:transparent; }
.btn-outline:hover:not(:disabled) { background:#f9fafb; border-color:#9ca3af; }

.field-category-label { font-size:10px; color:#9ca3af; font-weight:700; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px; }

.field-item    { padding:5px 8px; margin-bottom:3px; border-radius:4px; font-size:12px; cursor:grab; transition:all .15s; border:1px solid transparent; user-select:none; }
.field-item:hover { opacity:.85; }
.field-item:active { cursor:grabbing; }

.dimension-field { background:#ecfdf5; color:#065f46; border-color:#bbf7d0; }
.dimension-field:hover { background:#d1fae5; border-color:#86efac; }
.measure-field   { background:#fef3c7; color:#92400e; border-color:#fde68a; }
.measure-field:hover { background:#fef08a; border-color:#facc15; }

.drop-zone {
    min-height:64px; padding:10px; border:2px dashed #d1d5db; border-radius:6px;
    background:#f9fafb; transition:all .2s ease;
}
.drop-zone.drag-over { border-color:#3b82f6; background:#dbeafe; }

.drop-hint { color:#9ca3af; font-size:12px; text-align:center; font-style:italic; padding-top:8px; }

.field-pill  { display:inline-flex; align-items:center; padding:3px 8px; margin:2px; border-radius:12px; font-size:12px; font-weight:500; }
.dimension-pill { background:#ecfdf5; color:#065f46; border:1px solid #bbf7d0; }
.measure-pill   { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
.remove-btn  { margin-left:5px; background:none; border:none; color:#ef4444; cursor:pointer; font-weight:bold; font-size:14px; padding:0; line-height:1; }
.remove-btn:hover { color:#b91c1c; }

.spinner { width:28px; height:28px; border:3px solid #f3f3f3; border-top:3px solid #3b82f6; border-radius:50%; animation:spin 1s linear infinite; margin:0 auto; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('reportBuilder', () => ({

    // ── Server-provided data ──────────────────────────────────────────────────
    availableFields:    @json($fields),
    availableFilters:   @json($filters),
    canManageTemplates: @json($canManageTemplates),

    // ── UI state ─────────────────────────────────────────────────────────────
    loading:          false,
    templatesLoading: false,
    fieldSearch:      '',
    errorMessage:     '',
    successMessage:   '',
    showSaveModal:    false,
    showTemplateModal:false,
    availableTemplates: [],

    // ── Report configuration ─────────────────────────────────────────────────
    reportConfig: {
      baseTable:   'pos_terminals',
      columns:     [],
      rows:        [],
      values:      [],
      regionId:    '',
      clientId:    '',
      dateColumn:  '',
      dateFrom:    '',
      dateTo:      '',
      limit:       100,
      downloadAll: false,
    },

    // ── Template save form ───────────────────────────────────────────────────
    templateForm: { name: '', description: '', isGlobal: false },

    // ── Results ──────────────────────────────────────────────────────────────
    reportData:    null,
    reportColumns: [],

    // ── Lifecycle ────────────────────────────────────────────────────────────
    init() {
      this.loadTemplates();
    },

    // ── Field panel helpers ───────────────────────────────────────────────────
    shouldShowTable(table) {
      if (!this.fieldSearch) return true;
      return table.fields.some(f => this.matchesSearch(f.label));
    },

    matchesSearch(label) {
      if (!this.fieldSearch) return true;
      return label.toLowerCase().includes(this.fieldSearch.toLowerCase());
    },

    hasValidConfig() {
      return this.reportConfig.columns.length > 0
          || this.reportConfig.rows.length > 0
          || this.reportConfig.values.length > 0;
    },

    clearConfig() {
      this.reportConfig.columns    = [];
      this.reportConfig.rows       = [];
      this.reportConfig.values     = [];
      this.reportData              = null;
      this.reportColumns           = [];
      this.errorMessage            = '';
      this.successMessage          = '';
    },

    // ── Drag & drop ───────────────────────────────────────────────────────────
    startDrag(event, field, type) {
      event.dataTransfer.setData('application/json', JSON.stringify({ field, type }));
    },

    handleDragOver(event) {
      event.preventDefault();
      event.currentTarget.classList.add('drag-over');
    },

    handleDragLeave(event) {
      event.currentTarget.classList.remove('drag-over');
    },

    handleDrop(event, zone) {
      event.preventDefault();
      event.currentTarget.classList.remove('drag-over');

      try {
        const { field, type } = JSON.parse(event.dataTransfer.getData('application/json'));

        if ((zone === 'columns' || zone === 'rows') && type === 'measure') {
          this.errorMessage = 'Measures cannot be used as dimensions. Drop them in the Values zone instead.';
          return;
        }

        const entry = { ...field, aggregate: zone === 'values' ? 'COUNT' : undefined };
        this.reportConfig[zone].push(entry);
      } catch (e) {
        this.errorMessage = 'Failed to add field: ' + e.message;
      }
    },

    removeField(zone, index) {
      this.reportConfig[zone].splice(index, 1);
    },

    // ── Query config builder ─────────────────────────────────────────────────
    buildQueryConfig() {
      const cfg = {
        base:         { table: this.reportConfig.baseTable },
        select:       [],
        joins:        [],       // backend auto-detects from referenced tables
        group_by:     [],
        where:        [],
        limit:        this.reportConfig.downloadAll ? null : this.reportConfig.limit,
        download_all: this.reportConfig.downloadAll,
      };

      // SELECT + GROUP BY from columns and rows
      [...this.reportConfig.columns, ...this.reportConfig.rows].forEach(field => {
        cfg.select.push({ expr: field.expression, as: field.label });
        cfg.group_by.push(field.expression);
      });

      // SELECT for values (aggregates)
      this.reportConfig.values.forEach(field => {
        cfg.select.push({ expr: field.expression, as: field.label, aggregate: field.aggregate });
      });

      // --- WHERE filters ---

      // Region filter: use baseTable.region_id if the base table has that column,
      // otherwise fall back to pos_terminals.region_id (auto-join handles the rest)
      if (this.reportConfig.regionId) {
        const baseFields  = this.availableFields[this.reportConfig.baseTable]?.fields || [];
        const hasRegionId = baseFields.some(f => f.name === 'region_id');
        const regionCol   = hasRegionId
          ? this.reportConfig.baseTable + '.region_id'
          : 'pos_terminals.region_id';
        cfg.where.push({ column: regionCol, operator: '=', value: this.reportConfig.regionId });
      }

      // Client filter: same dynamic column detection
      if (this.reportConfig.clientId) {
        const baseFields   = this.availableFields[this.reportConfig.baseTable]?.fields || [];
        const hasClientId  = baseFields.some(f => f.name === 'client_id');
        const clientCol    = hasClientId
          ? this.reportConfig.baseTable + '.client_id'
          : 'pos_terminals.client_id';
        cfg.where.push({ column: clientCol, operator: '=', value: this.reportConfig.clientId });
      }

      // Date range filter
      if (this.reportConfig.dateColumn && (this.reportConfig.dateFrom || this.reportConfig.dateTo)) {
        cfg.where.push({
          column:   this.reportConfig.dateColumn,
          operator: 'between_dates',
          value:    { from: this.reportConfig.dateFrom || null, to: this.reportConfig.dateTo || null },
        });
      }

      return cfg;
    },

    // ── API: run report ───────────────────────────────────────────────────────
    async runReport() {
      if (!this.hasValidConfig()) {
        this.errorMessage = 'Please add at least one field to your report.';
        return;
      }

      this.loading       = true;
      this.errorMessage  = '';
      this.successMessage= '';

      try {
        const response = await fetch('/api/report/preview', {
          method:      'POST',
          credentials: 'same-origin',
          headers: {
            'Accept':           'application/json',
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(this.buildQueryConfig()),
        });

        const text = await response.text();
        if (text.trim().startsWith('<!')) {
          this.errorMessage = 'Session expired or server error. Please refresh and try again.';
          return;
        }

        const result = JSON.parse(text);
        if (result.success) {
          this.reportData    = result.data;
          this.reportColumns = result.data.length > 0 ? Object.keys(result.data[0]) : [];
        } else {
          this.errorMessage = result.error || 'Unknown error generating report.';
        }
      } catch (error) {
        this.errorMessage = 'Run report failed: ' + error.message;
      } finally {
        this.loading = false;
      }
    },

    // ── API: export ───────────────────────────────────────────────────────────
    async exportReport(format) {
      this.errorMessage = '';

      try {
        const cfg     = this.buildQueryConfig();
        cfg.format    = format;
        cfg.filename  = 'report_' + new Date().toISOString().slice(0, 10);

        const response = await fetch('/api/report/export', {
          method:      'POST',
          credentials: 'same-origin',
          headers: {
            'Accept':           'application/json',
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(cfg),
        });

        if (response.ok) {
          const blob = await response.blob();
          const url  = window.URL.createObjectURL(blob);
          const a    = document.createElement('a');
          a.href     = url;
          a.download = cfg.filename + '.' + format;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
        } else {
          const result = await response.json().catch(() => ({}));
          this.errorMessage = 'Export failed: ' + (result.error || response.statusText);
        }
      } catch (error) {
        this.errorMessage = 'Export failed: ' + error.message;
      }
    },

    // ── API: templates ────────────────────────────────────────────────────────
    async loadTemplates() {
      this.templatesLoading = true;
      try {
        const response = await fetch('/api/report/templates', {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });

        const text = await response.text();
        if (text.trim().startsWith('<!')) {
          console.warn('Could not load templates — HTML response (session?)');
          return;
        }

        const result = JSON.parse(text);
        if (result.success) {
          this.availableTemplates = Array.isArray(result.data)
            ? result.data
            : (result.data?.data || []);
        }
      } catch (error) {
        console.error('Failed to load templates:', error);
      } finally {
        this.templatesLoading = false;
      }
    },

    openTemplateModal() {
      this.showTemplateModal = true;
      this.loadTemplates();
    },

    loadTemplate(template) {
      try {
        const p = template.payload;
        if (!p || typeof p !== 'object') {
          this.errorMessage = 'Invalid template data — cannot load.';
          return;
        }

        this.reportConfig = {
          baseTable:   p.baseTable   || 'pos_terminals',
          columns:     Array.isArray(p.columns) ? p.columns : [],
          rows:        Array.isArray(p.rows)    ? p.rows    : [],
          values:      Array.isArray(p.values)  ? p.values  : [],
          regionId:    p.regionId    || '',
          clientId:    p.clientId    || '',
          dateColumn:  p.dateColumn  || '',
          dateFrom:    p.dateFrom    || '',
          dateTo:      p.dateTo      || '',
          limit:       p.limit       || 100,
          downloadAll: p.downloadAll || false,
        };

        this.reportData         = null;
        this.reportColumns      = [];
        this.showTemplateModal  = false;
        this.successMessage     = `Template "${template.name}" loaded. Click Run Report to execute.`;
      } catch (e) {
        this.errorMessage = 'Failed to load template: ' + e.message;
      }
    },

    async saveTemplate() {
      if (!this.templateForm.name) {
        this.errorMessage = 'Please enter a template name.';
        return;
      }

      try {
        const response = await fetch('/api/report/templates', {
          method:      'POST',
          credentials: 'same-origin',
          headers: {
            'Accept':           'application/json',
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({
            name:        this.templateForm.name,
            description: this.templateForm.description,
            is_global:   this.templateForm.isGlobal,
            payload:     this.reportConfig,
          }),
        });

        const text = await response.text();
        if (text.trim().startsWith('<!')) {
          this.errorMessage = 'Save failed — session expired. Please refresh.';
          return;
        }

        const result = JSON.parse(text);
        if (result.success) {
          this.showSaveModal  = false;
          this.templateForm   = { name: '', description: '', isGlobal: false };
          this.successMessage = 'Template saved successfully!';
          this.loadTemplates();
        } else {
          this.errorMessage = 'Error: ' + (result.error || 'Unknown error');
        }
      } catch (error) {
        this.errorMessage = 'Failed to save template: ' + error.message;
      }
    },

  }));
});
</script>
@endpush

@endsection
