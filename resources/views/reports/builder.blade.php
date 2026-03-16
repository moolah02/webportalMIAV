@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div x-data="reportBuilder()" x-init="init()">

    <!-- Notifications -->
    <div x-show="errorMessage" x-transition
         style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;color:#b91c1c;font-size:14px;">
        <span x-text="errorMessage"></span>
        <button @click="errorMessage=''" style="background:none;border:none;color:#b91c1c;cursor:pointer;font-size:20px;line-height:1;padding:0 0 0 12px;">&#x00D7;</button>
    </div>
    <div x-show="successMessage" x-transition
         style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;color:#166534;font-size:14px;">
        <span x-text="successMessage"></span>
        <button @click="successMessage=''" style="background:none;border:none;color:#166534;cursor:pointer;font-size:20px;line-height:1;padding:0 0 0 12px;">&#x00D7;</button>
    </div>

    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="margin:0;color:#111827;font-weight:700;font-size:22px;">Report Builder</h2>
            <p style="margin:5px 0 0;color:#6b7280;font-size:14px;">
                Drag fields from the left panel into the report area, then click <strong>Run Report</strong>
            </p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button @click="runReport()" :disabled="loading || fields.length === 0" class="rb-btn rb-btn-primary">
                <span x-show="!loading">&#9654; Run Report</span>
                <span x-show="loading">&#9203; Running...</span>
            </button>

            <div style="position:relative;" x-data="{ open: false }">
                <button @click="open = !open" :disabled="!reportData || reportData.length === 0" class="rb-btn rb-btn-outline">
                    &#11015; Export &#9662;
                </button>
                <div x-show="open" @click.outside="open = false"
                     style="position:absolute;right:0;top:calc(100% + 4px);background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 8px 16px rgba(0,0,0,.08);z-index:50;min-width:140px;overflow:hidden;">
                    <button @click="exportReport('csv'); open = false"
                            style="display:block;width:100%;text-align:left;padding:10px 14px;border:none;background:none;color:#374151;font-size:13px;cursor:pointer;"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='none'">
                        &#128196; CSV
                    </button>
                </div>
            </div>

            @if($canManageTemplates)
            <button @click="showSaveModal = true" :disabled="fields.length === 0" class="rb-btn rb-btn-outline">
                &#128190; Save
            </button>
            @endif

            <button @click="openTemplateModal()" class="rb-btn rb-btn-outline">
                &#128193; Templates
            </button>

            <button @click="clearAll()" x-show="fields.length > 0" class="rb-btn rb-btn-ghost">
                &#128465; Clear
            </button>
        </div>
    </div>

    <!-- Main layout: left panel + main area -->
    <div style="display:grid;grid-template-columns:260px 1fr;gap:16px;height:calc(100vh - 200px);min-height:500px;">

        <!-- ===== LEFT: Available Fields ===== -->
        <div class="rb-card" style="overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:0;">
            <div style="font-weight:700;color:#111827;font-size:14px;margin-bottom:10px;">Available Fields</div>

            <input type="text" x-model="fieldSearch" placeholder="&#128269; Search fields..."
                   style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;color:#374151;background:#f9fafb;margin-bottom:12px;box-sizing:border-box;"
                   @input="null">

            <template x-for="(table, tableName) in availableFields" :key="tableName">
                <div x-show="tableVisible(table)" style="margin-bottom:16px;">
                    <div style="font-size:10px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;padding-bottom:5px;border-bottom:1px solid #f3f4f6;"
                         x-text="table.label"></div>
                    <template x-for="field in table.fields.filter(f => fieldVisible(f))" :key="field.expression">
                        <div class="rb-field-item"
                             :class="field.category === 'measures' ? 'rb-measure' : 'rb-dimension'"
                             draggable="true"
                             @dragstart="startDrag($event, field)"
                             :title="'Drag to add: ' + field.expression"
                             x-text="field.label">
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- ===== RIGHT: Drop zone + Results ===== -->
        <div style="display:flex;flex-direction:column;gap:14px;overflow:hidden;min-height:0;">

            <!-- Drop zone row -->
            <div class="rb-card" style="padding:16px;flex-shrink:0;max-height:220px;overflow-y:auto;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <span style="font-weight:700;color:#111827;font-size:14px;">&#128202; Report Fields</span>
                </div>

                <!-- Filter bar -->
                <div style="display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;margin-bottom:10px;padding:10px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
                    <div style="display:flex;flex-direction:column;gap:3px;">
                        <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Data Source</label>
                        <select x-model="config.baseTable" @change="clearAll()"
                                style="padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#374151;background:#fff;min-width:130px;">
                            <template x-for="(t, tn) in availableFields" :key="tn">
                                <option :value="tn" x-text="t.label"></option>
                            </template>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:3px;">
                        <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Region</label>
                        <select x-model="config.regionId"
                                style="padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#374151;background:#fff;min-width:120px;">
                            <option value="">All Regions</option>
                            <template x-for="(name, id) in availableFilters.regions" :key="id">
                                <option :value="id" x-text="name"></option>
                            </template>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:3px;">
                        <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Client</label>
                        <select x-model="config.clientId"
                                style="padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#374151;background:#fff;min-width:130px;">
                            <option value="">All Clients</option>
                            <template x-for="(name, id) in availableFilters.clients" :key="id">
                                <option :value="id" x-text="name"></option>
                            </template>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:3px;">
                        <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Date Field</label>
                        <select x-model="config.dateColumn"
                                style="padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#374151;background:#fff;min-width:140px;">
                            <option value="">No date filter</option>
                            <template x-for="(t, tn) in availableFields" :key="tn">
                                <template x-for="f in t.fields.filter(f => f.type === 'date')" :key="f.expression">
                                    <option :value="f.expression" x-text="t.label + ' › ' + f.label"></option>
                                </template>
                            </template>
                        </select>
                    </div>

                    <template x-if="config.dateColumn">
                        <div style="display:flex;flex-direction:column;gap:3px;">
                            <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">From</label>
                            <input type="date" x-model="config.dateFrom"
                                   style="padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#374151;">
                        </div>
                    </template>
                    <template x-if="config.dateColumn">
                        <div style="display:flex;flex-direction:column;gap:3px;">
                            <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">To</label>
                            <input type="date" x-model="config.dateTo"
                                   style="padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#374151;">
                        </div>
                    </template>

                    <div style="display:flex;flex-direction:column;gap:3px;">
                        <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Row Limit</label>
                        <input type="number" x-model.number="config.limit" min="1" max="10000"
                               style="width:75px;padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#374151;">
                    </div>
                </div>

                <!-- The drop zone -->
                <div class="rb-dropzone"
                     :class="{ 'rb-dropzone-over': draggingOver }"
                     @dragover.prevent="draggingOver = true"
                     @dragleave="draggingOver = false"
                     @drop="onDrop($event)">

                    <template x-if="fields.length === 0">
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#9ca3af;">
                            <div style="text-align:center;">
                                <div style="font-size:28px;margin-bottom:6px;">&#8592;</div>
                                <div style="font-size:13px;">Drag fields here to build your report</div>
                            </div>
                        </div>
                    </template>

                    <div style="display:flex;flex-wrap:wrap;gap:6px;align-content:flex-start;">
                        <template x-for="(field, i) in fields" :key="i">
                            <div class="rb-pill" :class="field.category === 'measures' ? 'rb-pill-measure' : 'rb-pill-dim'">
                                <span x-text="field.label" style="font-size:12px;font-weight:500;"></span>

                                <!-- Aggregate selector for measures -->
                                <template x-if="field.category === 'measures'">
                                    <select x-model="field.aggregate"
                                            style="margin-left:4px;border:none;background:transparent;font-size:11px;cursor:pointer;color:inherit;outline:none;">
                                        <option value="">Raw</option>
                                        <option value="COUNT">COUNT</option>
                                        <option value="SUM">SUM</option>
                                        <option value="AVG">AVG</option>
                                        <option value="MIN">MIN</option>
                                        <option value="MAX">MAX</option>
                                    </select>
                                </template>

                                <button @click="removeField(i)"
                                        style="margin-left:5px;background:none;border:none;cursor:pointer;font-size:14px;line-height:1;padding:0;opacity:.6;"
                                        onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.6">&#x00D7;</button>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="fields.length > 0" style="margin-top:8px;font-size:11px;color:#9ca3af;">
                    <span x-text="fields.length + ' field' + (fields.length === 1 ? '' : 's') + ' selected'"></span>
                    <span x-show="hasAggregates()" style="margin-left:8px;">&#8226; Aggregated fields will GROUP BY all other columns</span>
                </div>
            </div>

            <!-- Results panel -->
            <div class="rb-card" style="flex:1;overflow:hidden;display:flex;flex-direction:column;min-height:0;padding:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-shrink:0;">
                    <span style="font-weight:700;color:#111827;font-size:14px;">Results</span>
                    <span x-show="reportData" style="font-size:12px;color:#6b7280;"
                          x-text="(reportData?.length || 0) + ' row' + ((reportData?.length ?? 0) === 1 ? '' : 's')"></span>
                </div>

                <!-- States -->
                <div x-show="!reportData && !loading" style="flex:1;display:flex;align-items:center;justify-content:center;color:#9ca3af;text-align:center;">
                    <div>
                        <div style="font-size:36px;margin-bottom:10px;">&#128202;</div>
                        <p style="margin:0;font-size:13px;">Drag fields above and click <strong style="color:#374151;">Run Report</strong></p>
                    </div>
                </div>

                <div x-show="loading" style="flex:1;display:flex;align-items:center;justify-content:center;color:#6b7280;">
                    <div style="text-align:center;">
                        <div class="rb-spinner"></div>
                        <p style="margin-top:10px;font-size:13px;">Running report...</p>
                    </div>
                </div>

                <div x-show="reportData && reportData.length === 0 && !loading"
                     style="flex:1;display:flex;align-items:center;justify-content:center;color:#9ca3af;text-align:center;">
                    <div>
                        <div style="font-size:36px;margin-bottom:10px;">&#128269;</div>
                        <p style="margin:0;font-size:13px;">No data matches your filters</p>
                    </div>
                </div>

                <div x-show="reportData && reportData.length > 0" style="flex:1;overflow:auto;min-height:0;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead style="position:sticky;top:0;z-index:1;background:#f9fafb;">
                            <tr>
                                <template x-for="col in reportColumns" :key="col">
                                    <th style="padding:10px 12px;text-align:left;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e5e7eb;white-space:nowrap;"
                                        x-text="col"></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, i) in reportData" :key="i">
                                <tr style="border-bottom:1px solid #f3f4f6;transition:background .1s;"
                                    @mouseenter="$el.style.background='#f9fafb'"
                                    @mouseleave="$el.style.background=''">
                                    <template x-for="col in reportColumns" :key="col">
                                        <td style="padding:9px 12px;color:#111827;" x-text="row[col] ?? '—'"></td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SAVE TEMPLATE MODAL -->
    <div x-show="showSaveModal"
         style="position:fixed;inset:0;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;z-index:300;">
        <div class="rb-card" style="width:400px;max-width:92vw;" @click.stop>
            <div style="font-weight:700;color:#111827;font-size:15px;margin-bottom:16px;">&#128190; Save Template</div>
            <div style="margin-bottom:12px;">
                <div class="rb-label">Name *</div>
                <input type="text" x-model="saveForm.name" placeholder="e.g. Monthly Terminal Report"
                       style="width:100%;padding:9px 11px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <div class="rb-label">Description</div>
                <textarea x-model="saveForm.description" rows="2"
                          style="width:100%;padding:9px 11px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;resize:vertical;box-sizing:border-box;"></textarea>
            </div>
            @if($canManageTemplates)
            <div style="margin-bottom:16px;">
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

    <!-- LOAD TEMPLATE MODAL -->
    <div x-show="showTemplateModal"
         style="position:fixed;inset:0;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;z-index:300;">
        <div class="rb-card" style="width:580px;max-width:92vw;max-height:75vh;display:flex;flex-direction:column;" @click.stop>
            <div style="font-weight:700;color:#111827;font-size:15px;margin-bottom:14px;">&#128193; Load Template</div>

            <div x-show="templatesLoading" style="text-align:center;padding:24px;color:#6b7280;font-size:14px;">Loading...</div>

            <div x-show="!templatesLoading && availableTemplates.length === 0"
                 style="text-align:center;padding:24px;color:#9ca3af;font-size:14px;">No saved templates yet.</div>

            <div x-show="!templatesLoading && availableTemplates.length > 0" style="flex:1;overflow-y:auto;margin-bottom:14px;">
                <template x-for="tpl in availableTemplates" :key="tpl.id">
                    <div style="border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;margin-bottom:8px;cursor:pointer;transition:all .15s;"
                         @click="applyTemplate(tpl)"
                         @mouseenter="$el.style.borderColor='#3b82f6'; $el.style.background='#eff6ff'"
                         @mouseleave="$el.style.borderColor='#e5e7eb'; $el.style.background=''">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                            <span style="font-weight:600;color:#111827;font-size:14px;" x-text="tpl.name"></span>
                            <span x-show="tpl.is_global"
                                  style="font-size:10px;font-weight:700;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:4px;padding:2px 6px;">GLOBAL</span>
                        </div>
                        <div style="font-size:12px;color:#6b7280;" x-text="tpl.description || 'No description'"></div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;"
                             x-text="'By ' + (tpl.creator?.first_name || 'Unknown') + ' ' + (tpl.creator?.last_name || '')"></div>
                    </div>
                </template>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button @click="showTemplateModal = false" class="rb-btn rb-btn-outline">Close</button>
            </div>
        </div>
    </div>

</div><!-- /x-data -->

<style>
/* Base card */
.rb-card { background:#fff; border-radius:10px; border:1px solid #e5e7eb; }

/* Buttons */
.rb-btn { padding:8px 14px; border-radius:7px; font-size:13px; font-weight:600; cursor:pointer; border:1px solid transparent; transition:all .15s; line-height:1.4; white-space:nowrap; }
.rb-btn:disabled { opacity:.4; cursor:not-allowed; }
.rb-btn-primary { background:#111827; color:#fff; border-color:#111827; }
.rb-btn-primary:hover:not(:disabled) { background:#374151; }
.rb-btn-outline { background:#fff; color:#374151; border-color:#d1d5db; }
.rb-btn-outline:hover:not(:disabled) { background:#f9fafb; border-color:#9ca3af; }
.rb-btn-ghost { background:transparent; color:#6b7280; border-color:transparent; }
.rb-btn-ghost:hover:not(:disabled) { background:#f3f4f6; color:#374151; }

/* Field items in left panel */
.rb-field-item {
    padding:6px 10px; margin-bottom:3px; border-radius:5px;
    font-size:12px; font-weight:500; cursor:grab;
    transition:transform .1s, box-shadow .1s;
    user-select:none; border:1px solid transparent;
}
.rb-field-item:hover { transform:translateX(2px); box-shadow:0 2px 6px rgba(0,0,0,.06); }
.rb-field-item:active { cursor:grabbing; }
.rb-dimension { background:#f0fdf4; color:#166534; border-color:#bbf7d0; }
.rb-dimension:hover { background:#dcfce7; }
.rb-measure   { background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
.rb-measure:hover { background:#ffedd5; }

/* Drop zone */
.rb-dropzone {
    min-height:72px; padding:10px 12px;
    border:2px dashed #d1d5db; border-radius:8px;
    background:#fafafa; transition:all .2s;
}
.rb-dropzone-over { border-color:#3b82f6; background:#eff6ff; }

/* Pills in drop zone */
.rb-pill {
    display:inline-flex; align-items:center; padding:5px 10px;
    border-radius:20px; font-size:12px; font-weight:500;
    border:1px solid transparent;
}
.rb-pill-dim     { background:#f0fdf4; color:#166534; border-color:#86efac; }
.rb-pill-measure { background:#fff7ed; color:#9a3412; border-color:#fdba74; }

/* Table */
table { border-collapse:collapse; }
thead th { font-size:11px !important; }

/* Spinner */
.rb-spinner { width:26px; height:26px; border:3px solid #e5e7eb; border-top-color:#3b82f6; border-radius:50%; animation:rb-spin 1s linear infinite; margin:0 auto; }
@keyframes rb-spin { to { transform:rotate(360deg); } }

/* Label in modal */
.rb-label { font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; }
</style>

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('reportBuilder', () => ({

    // ── Server data ──────────────────────────────────────────────────────────
    availableFields:    @json($fields),
    availableFilters:   @json($filters),
    canManageTemplates: @json($canManageTemplates),

    // ── State ────────────────────────────────────────────────────────────────
    loading:          false,
    templatesLoading: false,
    fieldSearch:      '',
    draggingOver:     false,
    errorMessage:     '',
    successMessage:   '',
    showSaveModal:    false,
    showTemplateModal:false,
    availableTemplates: [],

    // ── The selected fields (single flat array) ───────────────────────────────
    fields: [],   // each: { label, expression, type, category, aggregate: '' }

    // ── Config / filters ─────────────────────────────────────────────────────
    config: {
      baseTable:  'pos_terminals',
      regionId:   '',
      clientId:   '',
      dateColumn: '',
      dateFrom:   '',
      dateTo:     '',
      limit:      100,
    },

    // ── Save form ─────────────────────────────────────────────────────────────
    saveForm: { name: '', description: '', isGlobal: false },

    // ── Results ──────────────────────────────────────────────────────────────
    reportData:    null,
    reportColumns: [],

    // ── Init ─────────────────────────────────────────────────────────────────
    init() {
      this.loadTemplates();
    },

    // ── Left panel helpers ────────────────────────────────────────────────────
    tableVisible(table) {
      if (!this.fieldSearch) return true;
      return table.fields.some(f => this.fieldVisible(f));
    },
    fieldVisible(field) {
      if (!this.fieldSearch) return true;
      return field.label.toLowerCase().includes(this.fieldSearch.toLowerCase());
    },

    // ── Drag ─────────────────────────────────────────────────────────────────
    startDrag(event, field) {
      event.dataTransfer.setData('application/json', JSON.stringify(field));
      event.dataTransfer.effectAllowed = 'copy';
    },

    onDrop(event) {
      event.preventDefault();
      this.draggingOver = false;
      try {
        const field = JSON.parse(event.dataTransfer.getData('application/json'));
        // Prevent duplicates
        if (this.fields.some(f => f.expression === field.expression)) {
          this.errorMessage = `"${field.label}" is already in the report.`;
          return;
        }
        this.fields.push({ ...field, aggregate: '' });
        this.errorMessage = '';
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

    clearAll() {
      this.fields        = [];
      this.reportData    = null;
      this.reportColumns = [];
      this.errorMessage  = '';
      this.successMessage= '';
    },

    hasAggregates() {
      return this.fields.some(f => f.aggregate && f.aggregate !== '');
    },

    // ── Build query payload ───────────────────────────────────────────────────
    buildPayload() {
      const withAgg = this.hasAggregates();

      const select   = [];
      const group_by = [];

      this.fields.forEach(f => {
        if (f.aggregate && f.aggregate !== '') {
          // Aggregated column
          select.push({ expr: f.expression, as: f.aggregate + '(' + f.label + ')', aggregate: f.aggregate });
        } else {
          select.push({ expr: f.expression, as: f.label });
          if (withAgg) group_by.push(f.expression);
        }
      });

      const where = [];

      // Region filter
      if (this.config.regionId) {
        const baseFields  = this.availableFields[this.config.baseTable]?.fields || [];
        const hasRegionId = baseFields.some(f => f.name === 'region_id');
        where.push({
          column:   hasRegionId ? this.config.baseTable + '.region_id' : 'pos_terminals.region_id',
          operator: '=',
          value:    this.config.regionId,
        });
      }

      // Client filter
      if (this.config.clientId) {
        const baseFields   = this.availableFields[this.config.baseTable]?.fields || [];
        const hasClientId  = baseFields.some(f => f.name === 'client_id');
        where.push({
          column:   hasClientId ? this.config.baseTable + '.client_id' : 'pos_terminals.client_id',
          operator: '=',
          value:    this.config.clientId,
        });
      }

      // Date filter
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

    // ── Run report ────────────────────────────────────────────────────────────
    async runReport() {
      if (this.fields.length === 0) {
        this.errorMessage = 'Drag at least one field into the report area first.';
        return;
      }

      this.loading       = true;
      this.errorMessage  = '';
      this.successMessage= '';

      try {
        const res = await fetch('/api/report/preview', {
          method:      'POST',
          credentials: 'same-origin',
          headers: {
            'Accept':           'application/json',
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(this.buildPayload()),
        });

        const text = await res.text();
        if (text.trim().startsWith('<!')) {
          this.errorMessage = 'Session expired. Please refresh and try again.';
          return;
        }

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

    // ── Export ────────────────────────────────────────────────────────────────
    async exportReport(format) {
      this.errorMessage = '';
      try {
        const payload    = this.buildPayload();
        payload.format   = format;
        payload.filename = 'report_' + new Date().toISOString().slice(0, 10);
        payload.download_all = true;

        const res = await fetch('/api/report/export', {
          method:      'POST',
          credentials: 'same-origin',
          headers: {
            'Accept':           'application/json',
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
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

    // ── Templates ─────────────────────────────────────────────────────────────
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

        // Restore fields array
        this.fields = Array.isArray(p.fields) ? p.fields : [];

        // Restore config
        this.config = {
          baseTable:  p.baseTable  || 'pos_terminals',
          regionId:   p.regionId   || '',
          clientId:   p.clientId   || '',
          dateColumn: p.dateColumn || '',
          dateFrom:   p.dateFrom   || '',
          dateTo:     p.dateTo     || '',
          limit:      p.limit      || 100,
        };

        this.reportData         = null;
        this.reportColumns      = [];
        this.showTemplateModal  = false;
        this.successMessage     = `"${tpl.name}" loaded — click Run Report to execute.`;
      } catch (e) {
        this.errorMessage = 'Failed to load template: ' + e.message;
      }
    },

    async saveTemplate() {
      if (!this.saveForm.name) { this.errorMessage = 'Enter a template name.'; return; }
      try {
        const res = await fetch('/api/report/templates', {
          method:      'POST',
          credentials: 'same-origin',
          headers: {
            'Accept':           'application/json',
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({
            name:        this.saveForm.name,
            description: this.saveForm.description,
            is_global:   this.saveForm.isGlobal,
            payload: {
              fields:     this.fields,
              baseTable:  this.config.baseTable,
              regionId:   this.config.regionId,
              clientId:   this.config.clientId,
              dateColumn: this.config.dateColumn,
              dateFrom:   this.config.dateFrom,
              dateTo:     this.config.dateTo,
              limit:      this.config.limit,
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
