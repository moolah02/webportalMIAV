@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div x-data="reportBuilder()" x-init="init()">
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-block-end:24px;">
        <div>
            <h2 style="margin:0;color:#111827;font-weight:700;letter-spacing:-.02em;">
                Report Builder
            </h2>
            <p style="margin:6px 0 0;color:#6b7280;">Create custom reports with drag-and-drop interface</p>
        </div>

        <!-- Toolbar -->
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button @click="runReport()"
                    :disabled="!canPreview || loading || !hasValidConfig()"
                    class="btn btn-secondary">
                <span x-show="!loading">🔄 Run Report</span>
                <span x-show="loading">⏳ Running...</span>
            </button>

            @if($canManageTemplates)
            <button @click="showSaveModal = true"
                    :disabled="!hasValidConfig()"
                    class="btn btn-secondary">
                💾 Save Template
            </button>
            @endif

            <div class="dropdown" style="position:relative;" x-data="{ open: false }">
                <button @click="open = !open"
                        :disabled="!canExport || !reportData || reportData.length === 0"
                        class="btn btn-outline">
                    📊 Export ▾
                </button>
                <div x-show="open" @click.outside="open = false"
                     style="position:absolute;right:0;top:100%;margin-top:4px;background:#fff;border:1px solid #e5e7eb;border-radius:6px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);z-index:50;min-width:150px;">
                    <button @click="exportReport('csv'); open = false"
                            style="display:block;width:100%;text-align:left;padding:8px 12px;border:none;background:none;color:#374151;font-size:14px;cursor:pointer;">
                        CSV Format
                    </button>
                </div>
            </div>

            <button @click="showTemplateModal = true" class="btn btn-outline">
                📂 Load Template
            </button>
        </div>
    </div>

    <!-- Main Layout -->
    <div style="display:grid;grid-template-columns:300px 1fr 280px;gap:16px;height:calc(100vh - 200px);">

        <!-- Left Panel - Fields -->
        <div class="content-card" style="overflow-y:auto;padding:16px;">
            <h4 class="card-title">Available Fields</h4>

            <!-- Search Fields -->
            <div style="margin-bottom:16px;">
                <input type="text" x-model="fieldSearch" placeholder="Search fields..."
                       style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;">
            </div>

            <!-- Field Categories -->
            <template x-for="(table, tableName) in availableFields" :key="tableName">
                <div style="margin-bottom:20px;" x-show="shouldShowTable(table, tableName)">
                    <h5 style="margin:0 0 8px;color:#111827;font-weight:600;font-size:14px;" x-text="table.label"></h5>

                    <!-- Dimensions -->
                    <div style="margin-bottom:12px;">
                        <div class="field-category-label">📏 Dimensions</div>
                        <template x-for="field in table.fields.filter(f => f.category === 'dimensions')" :key="field.expression">
                            <div x-show="matchesSearch(field.label)"
                                 class="field-item dimension-field"
                                 draggable="true"
                                 @dragstart="startDrag($event, field, 'dimension')"
                                 x-text="field.label">
                            </div>
                        </template>
                    </div>

                    <!-- Measures -->
                    <div>
                        <div class="field-category-label">📊 Measures</div>
                        <template x-for="field in table.fields.filter(f => f.category === 'measures')" :key="field.expression">
                            <div x-show="matchesSearch(field.label)"
                                 class="field-item measure-field"
                                 draggable="true"
                                 @dragstart="startDrag($event, field, 'measure')"
                                 x-text="field.label">
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Center Panel - Canvas & Results -->
        <div style="display:flex;flex-direction:column;gap:16px;">

            <!-- Drop Zones -->
            <div class="content-card" style="padding:16px;">
                <h4 class="card-title">Report Configuration</h4>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">

                    <!-- Columns Drop Zone -->
                    <div>
                        <div class="label" style="margin-bottom:8px;">📊 Columns</div>
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
                            <div x-show="reportConfig.columns.length === 0" class="drop-hint">
                                Drop dimensions here
                            </div>
                        </div>
                    </div>

                    <!-- Rows Drop Zone -->
                    <div>
                        <div class="label" style="margin-bottom:8px;">📋 Rows/Groups</div>
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
                            <div x-show="reportConfig.rows.length === 0" class="drop-hint">
                                Drop dimensions here
                            </div>
                        </div>
                    </div>

                    <!-- Values Drop Zone -->
                    <div>
                        <div class="label" style="margin-bottom:8px;">🔢 Values/Aggregations</div>
                        <div class="drop-zone"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave="handleDragLeave($event)"
                             @drop="handleDrop($event, 'values')">
                            <template x-for="(field, index) in reportConfig.values" :key="index">
                                <div class="field-pill measure-pill">
                                    <span x-text="field.label"></span>
                                    <select x-model="field.aggregate" @change="updateField('values', index, 'aggregate', $event.target.value)"
                                            style="margin-left:4px;border:none;background:transparent;font-size:11px;">
                                        <option value="COUNT">COUNT</option>
                                        <option value="SUM">SUM</option>
                                        <option value="AVG">AVG</option>
                                        <option value="MIN">MIN</option>
                                        <option value="MAX">MAX</option>
                                    </select>
                                    <button @click="removeField('values', index)" class="remove-btn">×</button>
                                </div>
                            </template>
                            <div x-show="reportConfig.values.length === 0" class="drop-hint">
                                Drop measures here
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="content-card" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
                <h4 class="card-title">📈 Results</h4>

                <div x-show="!reportData && !loading" style="display:flex;align-items:center;justify-content:center;flex:1;color:#6b7280;text-align:center;">
                    <div>
                        <div style="font-size:48px;margin-bottom:16px;">📊</div>
                        <p>Configure your report fields and click "Run Report" to see results</p>
                    </div>
                </div>

                <div x-show="loading" style="display:flex;align-items:center;justify-content:center;flex:1;color:#6b7280;">
                    <div style="text-align:center;">
                        <div class="spinner"></div>
                        <p style="margin-top:12px;">Generating report...</p>
                    </div>
                </div>

                <div x-show="reportData && reportData.length > 0" style="flex:1;overflow:auto;">
                    <div style="margin-bottom:12px;color:#6b7280;font-size:14px;">
                        <span x-text="`${reportData?.length || 0} rows`"></span>
                    </div>
                    <div style="overflow:auto;border:1px solid #e5e7eb;border-radius:6px;">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                                <tr>
                                    <template x-for="column in reportColumns" :key="column">
                                        <th style="padding:12px 8px;text-align:left;font-weight:600;font-size:12px;color:#374151;text-transform:uppercase;letter-spacing:0.05em;"
                                            x-text="column">
                                        </th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in reportData" :key="index">
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <template x-for="column in reportColumns" :key="column">
                                            <td style="padding:8px;font-size:14px;color:#111827;"
                                                x-text="row[column]">
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="reportData && reportData.length === 0 && !loading" style="display:flex;align-items:center;justify-content:center;flex:1;color:#6b7280;text-align:center;">
                    <div>
                        <div style="font-size:48px;margin-bottom:16px;">🔍</div>
                        <p>No data found for your report criteria</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Filters -->
        <div class="content-card" style="overflow-y:auto;padding:16px;">
            <h4 class="card-title">⚙️ Filters & Options</h4>

            <!-- Region Filter -->
            <div style="margin-bottom:16px;">
                <div class="label">Region</div>
                <select x-model="reportConfig.regionId" style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;">
                    <option value="">All Regions</option>
                    <template x-for="(name, id) in availableFilters.regions" :key="id">
                        <option :value="id" x-text="name"></option>
                    </template>
                </select>
            </div>

            <!-- Client Filter -->
            <div style="margin-bottom:16px;">
                <div class="label">Client</div>
                <select x-model="reportConfig.clientId" style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;">
                    <option value="">All Clients</option>
                    <template x-for="(name, id) in availableFilters.clients" :key="id">
                        <option :value="id" x-text="name"></option>
                    </template>
                </select>
            </div>

            <!-- Limit -->
            <div style="margin-bottom:16px;">
                <div class="label">Result Limit</div>
                <input type="number" x-model.number="reportConfig.limit" min="1" max="10000"
                       style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;">
            </div>

            <!-- Download All -->
            <div style="margin-bottom:16px;">
                <label style="display:flex;align-items:center;cursor:pointer;">
                    <input type="checkbox" x-model="reportConfig.downloadAll" style="margin-right:8px;">
                    <span style="font-size:14px;color:#374151;">Download All (bypass limit)</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Save Template Modal -->
    <div x-show="showSaveModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:100;">
        <div class="content-card" style="width:400px;max-width:90vw;">
            <h4 class="card-title">💾 Save Report Template</h4>
            <div style="margin-bottom:16px;">
                <div class="label">Template Name</div>
                <input type="text" x-model="templateForm.name"
                       style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;">
            </div>
            <div style="margin-bottom:16px;">
                <div class="label">Description</div>
                <textarea x-model="templateForm.description" rows="3"
                          style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;resize:vertical;"></textarea>
            </div>
            @if($canManageTemplates)
            <div style="margin-bottom:16px;">
                <label style="display:flex;align-items:center;cursor:pointer;">
                    <input type="checkbox" x-model="templateForm.isGlobal" style="margin-right:8px;">
                    <span style="font-size:14px;color:#374151;">Make this template global (visible to all users)</span>
                </label>
            </div>
            @endif
            <div style="display:flex;justify-content:end;gap:8px;">
                <button @click="showSaveModal = false" class="btn btn-outline">Cancel</button>
                <button @click="saveTemplate()" class="btn btn-secondary">Save</button>
            </div>
        </div>
    </div>

    <!-- Load Template Modal -->
    <div x-show="showTemplateModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:100;">
        <div class="content-card" style="width:800px;max-width:90vw;max-height:80vh;overflow:hidden;display:flex;flex-direction:column;">
            <h4 class="card-title">📂 Load Report Template</h4>
            <div style="flex:1;overflow-y:auto;margin-bottom:16px;">
                <template x-for="template in availableTemplates" :key="template.id">
                    <div style="border:1px solid #e5e7eb;border-radius:6px;padding:12px;margin-bottom:8px;cursor:pointer;transition:all 0.2s ease;"
                         @click="loadTemplate(template)"
                         @mouseenter="$el.style.backgroundColor='#f9fafb'"
                         @mouseleave="$el.style.backgroundColor='transparent'">
                        <div style="font-weight:600;color:#111827;margin-bottom:4px;" x-text="template.name"></div>
                        <div style="font-size:14px;color:#6b7280;margin-bottom:8px;" x-text="template.description || 'No description'"></div>
                        <div style="display:flex;align-items:center;justify-content:between;">
                            <span x-show="template.is_global" class="status-badge status-active">Global</span>
                            <div style="font-size:12px;color:#9ca3af;" x-text="'By ' + (template.creator?.first_name || 'Unknown') + ' ' + (template.creator?.last_name || '')"></div>
                        </div>
                    </div>
                </template>
            </div>
            <div style="display:flex;justify-content:end;">
                <button @click="showTemplateModal = false" class="btn btn-outline">Cancel</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Use your existing styles */
.content-card{background:#fff;padding:20px;border-radius:10px;border:1px solid #e5e7eb}
.card-title{margin:0 0 12px;color:#111827;font-weight:700}
.label{font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px}
.value{color:#111827}

.btn{padding:10px 16px;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#374151;text-decoration:none;cursor:pointer;font-weight:500;font-size:14px;transition:all .2s ease;display:inline-block;line-height:1}
.btn:hover{border-color:#9ca3af;background:#f9fafb}
.btn:disabled{opacity:0.5;cursor:not-allowed}
.btn-secondary{background:#f3f4f6;color:#374151;border-color:#d1d5db}
.btn-secondary:hover:not(:disabled){background:#e5e7eb;border-color:#9ca3af}
.btn-outline{background:transparent;color:#374151;border-color:#d1d5db}
.btn-outline:hover:not(:disabled){background:#f9fafb;color:#111827;border-color:#9ca3af}

.status-badge{padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em}
.status-active{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}

/* Report Builder Specific Styles */
.field-category-label{font-size:11px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px}

.field-item{
    padding:6px 8px;
    margin-bottom:4px;
    border-radius:4px;
    font-size:13px;
    cursor:grab;
    transition:all 0.2s ease;
    border:1px solid transparent;
}

.dimension-field{
    background:#ecfdf5;
    color:#065f46;
    border-color:#bbf7d0;
}
.dimension-field:hover{
    background:#d1fae5;
    border-color:#86efac;
}

.measure-field{
    background:#fef3c7;
    color:#92400e;
    border-color:#fde68a;
}
.measure-field:hover{
    background:#fde047;
    border-color:#facc15;
}

.drop-zone{
    min-height:60px;
    padding:12px;
    border:2px dashed #d1d5db;
    border-radius:6px;
    background:#f9fafb;
    transition:all 0.3s ease;
}

.drop-zone.drag-over{
    border-color:#3b82f6;
    background:#dbeafe;
}

.drop-hint{
    color:#9ca3af;
    font-size:13px;
    text-align:center;
    font-style:italic;
}

.field-pill{
    display:inline-flex;
    align-items:center;
    padding:4px 8px;
    margin:2px;
    border-radius:12px;
    font-size:12px;
    font-weight:500;
}

.dimension-pill{
    background:#ecfdf5;
    color:#065f46;
    border:1px solid #bbf7d0;
}

.measure-pill{
    background:#fef3c7;
    color:#92400e;
    border:1px solid #fde68a;
}

.remove-btn{
    margin-left:6px;
    background:none;
    border:none;
    color:#ef4444;
    cursor:pointer;
    font-weight:bold;
    font-size:14px;
    padding:0;
    width:16px;
    height:16px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
}

.remove-btn:hover{
    background:#fee2e2;
}

.spinner{
    width:32px;
    height:32px;
    border:3px solid #f3f3f3;
    border-top:3px solid #3b82f6;
    border-radius:50%;
    animation:spin 1s linear infinite;
    margin:0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('reportBuilder', () => ({
    availableFields: @json($fields),
    availableFilters: @json($filters),
    canPreview: @json($canPreviewReports),
    canExport: @json($canExportReports),
    canManageTemplates: @json($canManageTemplates),

    loading: false,
    fieldSearch: '',
    showSaveModal: false,
    showTemplateModal: false,
    availableTemplates: [],

    reportConfig: {
      baseTable: 'pos_terminals',
      columns: [],
      rows: [],
      values: [],
      regionId: '',
      clientId: '',
      limit: 100,
      downloadAll: false
    },

    templateForm: {
      name: '',
      description: '',
      isGlobal: false
    },

    reportData: null,
    reportColumns: [],

    init() {
      this.loadTemplates();
    },

    shouldShowTable(table, tableName) {
      if (!this.fieldSearch) return true;
      return table.fields.some(field => this.matchesSearch(field.label));
    },

    matchesSearch(fieldLabel) {
      if (!this.fieldSearch) return true;
      return fieldLabel.toLowerCase().includes(this.fieldSearch.toLowerCase());
    },

    hasValidConfig() {
      return this.reportConfig.columns.length > 0 ||
             this.reportConfig.rows.length > 0 ||
             this.reportConfig.values.length > 0;
    },

    startDrag(event, field, type) {
      event.dataTransfer.setData('application/json', JSON.stringify({
        field: field,
        type: type
      }));
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
        const data = JSON.parse(event.dataTransfer.getData('application/json'));
        const field = {
          ...data.field,
          aggregate: zone === 'values' ? 'COUNT' : undefined
        };

        if (zone === 'values' && data.type === 'dimension') {
          field.aggregate = 'COUNT';
        } else if ((zone === 'columns' || zone === 'rows') && data.type === 'measure') {
          alert('Measures cannot be used as dimensions. Try dropping in the Values zone instead.');
          return;
        }

        this.reportConfig[zone].push(field);
      } catch (e) {
        console.error('Failed to handle drop:', e);
      }
    },

    removeField(zone, index) {
      this.reportConfig[zone].splice(index, 1);
    },

    updateField(zone, index, property, value) {
      this.reportConfig[zone][index][property] = value;
    },

    buildQueryConfig() {
      const config = {
        base: { table: this.reportConfig.baseTable },
        select: [],
        joins: [],
        group_by: [],
        limit: this.reportConfig.downloadAll ? null : this.reportConfig.limit,
        download_all: this.reportConfig.downloadAll
      };

      // Add select fields
      [...this.reportConfig.columns, ...this.reportConfig.rows].forEach(field => {
        config.select.push({
          expr: field.expression,
          as: field.label
        });
        config.group_by.push(field.expression);
      });

      this.reportConfig.values.forEach(field => {
        config.select.push({
          expr: field.expression,
          as: field.label,
          aggregate: field.aggregate
        });
      });

      return config;
    },

    // ----------------- NETWORK METHODS -----------------

    async runReport() {
      if (!this.hasValidConfig()) {
        alert('Please add at least one field to your report.');
        return;
      }

      this.loading = true;

      try {
        const response = await fetch('/api/report/preview', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(this.buildQueryConfig())
        });

        const text = await response.text();
        const looksHtml = text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html');
        if (looksHtml) {
          console.error('Received HTML (probably login or exception page):', text);
          alert('Auth/CSRF or server error — check Network tab and storage/logs/laravel.log');
          return;
        }

        const result = JSON.parse(text);
        if (result.success) {
          this.reportData = result.data;
          this.reportColumns = result.data.length > 0 ? Object.keys(result.data[0]) : [];
        } else {
          alert('Error: ' + (result.error || 'Unknown error'));
        }
      } catch (error) {
        alert('Run report failed: ' + error.message);
      } finally {
        this.loading = false;
      }
    },

    async exportReport(format) {
      const config = this.buildQueryConfig();
      config.format = format;

      try {
        const response = await fetch('/api/report/export', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(config)
        });

        if (response.ok) {
          const blob = await response.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `report.${format}`;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
        } else {
          alert('Export failed');
        }
      } catch (error) {
        alert('Export failed: ' + error.message);
      }
    },

    async loadTemplates() {
      try {
        const response = await fetch('/api/report/templates', {
          method: 'GET',
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const text = await response.text();
        if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
          console.error('HTML response when loading templates:', text);
          alert('Failed to load templates — server returned HTML (probably a login or error page). Check logs.');
          return;
        }

        const result = JSON.parse(text);
        if (result.success) {
          this.availableTemplates = Array.isArray(result.data)
            ? result.data
            : result.data.data || [];
        } else {
          alert('Error loading templates: ' + (result.error || 'Unknown error'));
        }
      } catch (error) {
        console.error('Failed to load templates:', error);
      }
    },

    async saveTemplate() {
      try {
        const response = await fetch('/api/report/templates', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            name: this.templateForm.name,
            description: this.templateForm.description,
            is_global: this.templateForm.isGlobal,
            payload: this.reportConfig
          })
        });

        const text = await response.text();
        if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
          console.error('HTML response when saving template:', text);
          alert('Save failed — server returned an HTML page. Check logs.');
          return;
        }

        const result = JSON.parse(text);
        if (result.success) {
          this.showSaveModal = false;
          this.templateForm = { name: '', description: '', isGlobal: false };
          this.loadTemplates();
          alert('Template saved successfully!');
        } else {
          alert('Error: ' + (result.error || 'Unknown error'));
        }
      } catch (error) {
        alert('Failed to save template: ' + error.message);
      }
    }
  }));
});
</script>

@endsection
