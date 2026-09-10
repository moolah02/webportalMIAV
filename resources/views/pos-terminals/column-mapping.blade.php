@extends('layouts.app')
@section('title', 'Column Mapping')

@push('styles')
<style>
/* ── POS terminals · column mapping ────────────────────── */
.pt-map .pt-map-top { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
.pt-map .pt-map-desc { margin: 0; font-size: 13.5px; color: var(--mv-muted); }
.pt-map .pt-btn { height: 34px; padding: 0 12px; font-size: 13px; }
.pt-map .pt-btn-sm { height: 30px; padding: 0 10px; font-size: 12.5px; }
.pt-map .pt-card + .pt-card { margin-top: 16px; }
.pt-map .ui-card-header { padding: 12px 18px; }
.pt-map .ui-card-header h2 { font-size: 14px; font-weight: 600; margin: 0; }
.pt-map .ui-card-body { padding: 18px; }
.pt-map .pt-sub { margin: 2px 0 0; font-size: 12.5px; color: var(--mv-muted); }
.pt-map .pt-errors { display: flex; align-items: flex-start; gap: 10px; padding: 11px 14px; margin-bottom: 16px; border: 1px solid #F2CACA; border-radius: 8px; background: var(--mv-crit-soft); color: var(--mv-crit); font-size: 13px; }
.pt-map .pt-errors ul { margin: 0; padding-left: 16px; }
.pt-map .pt-errors .mv-i { margin-top: 1px; }

/* Basic fields */
.pt-map .pt-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 20px; }
.pt-map .pt-span-all { grid-column: 1 / -1; }
.pt-map .form-label { display: block; margin-bottom: 6px; }
.pt-map .pt-req { color: var(--mv-crit); }
.pt-map .form-input { width: 100%; height: 38px; padding: 0 12px; font-size: 13.5px; }
.pt-map .ui-select { height: 38px; padding: 0 32px 0 12px; font-size: 13.5px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236A7686' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; }
.pt-map .ui-textarea { padding: 9px 12px; font-size: 13.5px; }
.pt-map .form-error { margin-top: 5px; font-size: 12px; color: var(--mv-crit); }

/* Mapping configuration */
.pt-map .pt-config { margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--mv-line); }
.pt-map .pt-config-title { margin: 0; font-size: 13.5px; font-weight: 600; }
.pt-map .mapping-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 14px; margin-top: 14px; align-items: start; }
.pt-map .mapping-section { border: 1px solid var(--mv-line); border-radius: 10px; background: var(--mv-surface); overflow: hidden; }
.pt-map .section-header { margin: 0; padding: 10px 14px; font-size: 13px; font-weight: 600; color: var(--mv-ink); background: var(--mv-surface-2); border-bottom: 1px solid var(--mv-line); }
.pt-map .section-note { margin: 0; padding: 10px 14px 0; font-size: 12px; color: var(--mv-muted); }
.pt-map .field-mappings { display: flex; flex-direction: column; }
.pt-map .field-mappings:empty { display: none; }
.pt-map .field-mapping { display: grid; grid-template-columns: minmax(0, 1fr) 120px; column-gap: 12px; align-items: center; padding: 9px 14px; }
.pt-map .field-mapping + .field-mapping { border-top: 1px solid var(--mv-line); }
.pt-map .field-label { grid-column: 1; grid-row: 1; margin: 0; font-size: 13px; font-weight: 500; color: var(--mv-ink); }
.pt-map .field-help { grid-column: 1; grid-row: 2; font-size: 12px; color: var(--mv-muted); line-height: 1.4; }
.pt-map .field-mapping .column-input { grid-column: 2; grid-row: 1 / span 2; }
.pt-map .column-input, .pt-map .custom-field-name { width: 100%; height: 32px; padding: 0 10px; border: 1px solid var(--mv-line-strong); border-radius: 7px; background: var(--mv-surface); font-size: 13px; color: var(--mv-ink); outline: none; }
.pt-map .column-input { font-family: var(--mv-mono); font-variant-numeric: tabular-nums; }
.pt-map .column-input::placeholder, .pt-map .custom-field-name::placeholder { font-family: var(--mv-sans); color: var(--mv-muted); }
.pt-map .column-input:focus, .pt-map .custom-field-name:focus { border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
.pt-map #customFieldsContainer { gap: 8px; padding: 10px 14px 0; }
.pt-map .custom-field-row { display: grid; grid-template-columns: minmax(0, 1fr) 96px auto; gap: 8px; align-items: center; }
.pt-map .btn-remove { height: 32px; padding: 0 10px; border: 1px solid #EBC3C3; border-radius: 7px; background: var(--mv-surface); color: var(--mv-crit); font-size: 12.5px; font-weight: 500; cursor: pointer; }
.pt-map .btn-remove:hover { background: var(--mv-crit-soft); }
.pt-map .section-foot { padding: 10px 14px 12px; }
.pt-map .pt-footer-end { justify-content: flex-end; flex-wrap: wrap; gap: 8px; padding: 12px 18px; }

/* Existing mappings */
.pt-map .ui-table tbody td { font-size: 13px; }
.pt-map .pt-muted { color: var(--mv-muted); }
.pt-map .status-badge { gap: 6px; padding: 2px 8px; font-size: 12px; line-height: 18px; }
.pt-map .status-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
.pt-map .action-group { gap: 4px; justify-content: flex-end; }
.pt-map .action-btn { width: 30px; height: 30px; }
.pt-map th.pt-th-actions, .pt-map td.pt-td-actions { text-align: right; width: 1%; }

/* Guide */
.pt-map .pt-guide { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; }
.pt-map .pt-guide h3 { margin: 0 0 8px; font-size: 13.5px; font-weight: 600; }
.pt-map .pt-guide-list { margin: 0; padding-left: 18px; font-size: 13px; color: var(--mv-ink-2); }
.pt-map .pt-guide-list li { margin-bottom: 6px; line-height: 1.5; }
.pt-map .pt-guide-list strong { font-weight: 500; color: var(--mv-ink); }
.pt-map .pt-csv { overflow-x: auto; border: 1px solid var(--mv-line); border-radius: 8px; }
.pt-map .pt-csv table { width: 100%; border-collapse: collapse; background: var(--mv-surface); font-family: var(--mv-mono); font-size: 12px; }
.pt-map .pt-csv th, .pt-map .pt-csv td { padding: 7px 10px; text-align: left; white-space: nowrap; border: 0; font-size: 12px; }
.pt-map .pt-csv th { background: var(--mv-surface-2); color: var(--mv-muted); font-weight: 500; border-bottom: 1px solid var(--mv-line); }
.pt-map .pt-csv td { color: var(--mv-ink-2); }
.pt-map .pt-csv tr:hover { background: transparent; }
.pt-map .pt-csv .is-key { background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
.pt-map .pt-csv-note { margin: 8px 0 0; font-size: 12.5px; color: var(--mv-muted); }

@media (max-width: 760px) {
  .pt-map .pt-grid { grid-template-columns: 1fr; }
  .pt-map .mapping-grid { grid-template-columns: 1fr; }
  .pt-map .custom-field-row { grid-template-columns: 1fr 96px; }
  .pt-map .custom-field-row .btn-remove { grid-column: 1 / -1; }
}
</style>
@endpush

@section('content')
<div class="pt-map">
    <!-- Header -->
    <div class="pt-map-top">
        <p class="pt-map-desc">Create and manage CSV column mappings for different bank formats</p>
        <a href="{{ route('pos-terminals.index') }}" class="btn-secondary pt-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Terminals
        </a>
    </div>

    <!-- Validation messages (success / error flashes are shown by the layout) -->
    @if($errors->any())
        <div class="pt-errors" role="alert">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-alert-circle"/></svg>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <!-- Create New Mapping Card -->
    <div class="ui-card pt-card">
        <div class="ui-card-header">
            <div>
                <h2>Create New Column Mapping</h2>
                <p class="pt-sub">Configure how CSV columns map to database fields for easier imports</p>
            </div>
        </div>

        <form action="{{ route('pos-terminals.store-mapping') }}" method="POST" class="mapping-form">
            @csrf

            <div class="ui-card-body">
                <!-- Basic Information -->
                <div class="pt-grid">
                    <div>
                        <label for="mapping_name" class="form-label">Mapping Name <span class="pt-req">*</span></label>
                        <input type="text"
                               name="mapping_name"
                               id="mapping_name"
                               placeholder="e.g., Standard Bank Format, CBZ CSV Layout"
                               required
                               class="form-input">
                        @error('mapping_name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="client_id" class="form-label">Associated Client (Optional)</label>
                        <select name="client_id" id="client_id" class="ui-select">
                            <option value="">General Mapping (All Clients)</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-span-all">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description"
                                  id="description"
                                  placeholder="Describe when to use this mapping..."
                                  rows="2"
                                  class="ui-textarea"></textarea>
                    </div>
                </div>

                <!-- Column Mapping Configuration -->
                <div class="pt-config">
                    <h3 class="pt-config-title">Column Mapping Configuration</h3>
                    <p class="pt-sub">Map CSV columns (0-based index) to database fields. Leave blank to skip a field.</p>

                    <div class="mapping-grid">
                        <!-- Terminal Information -->
                        <div class="mapping-section">
                            <h4 class="section-header">Terminal Information</h4>
                            <div class="field-mappings">
                                <div class="field-mapping">
                                    <label class="field-label">Terminal ID <span class="pt-req">*</span></label>
                                    <input type="number"
                                           name="column_mappings[terminal_id]"
                                           placeholder="e.g. 1"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Required field - CSV column containing terminal IDs</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Terminal Model</label>
                                    <input type="number"
                                           name="column_mappings[terminal_model]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Device type/model (e.g., VX-520)</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Serial Number</label>
                                    <input type="number"
                                           name="column_mappings[serial_number]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Device serial number</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Status</label>
                                    <input type="number"
                                           name="column_mappings[status]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Terminal status (active, offline, etc.)</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Installation Date</label>
                                    <input type="number"
                                           name="column_mappings[installation_date]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">When terminal was installed</small>
                                </div>
                            </div>
                        </div>

                        <!-- Merchant Information -->
                        <div class="mapping-section">
                            <h4 class="section-header">Merchant Information</h4>
                            <div class="field-mappings">
                                <div class="field-mapping">
                                    <label class="field-label">Merchant Name <span class="pt-req">*</span></label>
                                    <input type="number"
                                           name="column_mappings[merchant_name]"
                                           placeholder="e.g. 4"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Required field - Business/merchant name</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Contact Person</label>
                                    <input type="number"
                                           name="column_mappings[merchant_contact_person]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Primary contact person</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Phone Number</label>
                                    <input type="number"
                                           name="column_mappings[merchant_phone]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Merchant phone number</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Business Type</label>
                                    <input type="number"
                                           name="column_mappings[business_type]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Type of business (retail, restaurant, etc.)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="mapping-section">
                            <h4 class="section-header">Location Information</h4>
                            <div class="field-mappings">
                                <div class="field-mapping">
                                    <label class="field-label">Physical Address</label>
                                    <input type="number"
                                           name="column_mappings[physical_address]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Street address</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">City</label>
                                    <input type="number"
                                           name="column_mappings[city]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">City or town</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Province</label>
                                    <input type="number"
                                           name="column_mappings[province]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Province or state</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Region</label>
                                    <input type="number"
                                           name="column_mappings[region]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Service region</small>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Fields -->
                        <div class="mapping-section">
                            <h4 class="section-header">Additional Fields</h4>
                            <div class="field-mappings">
                                <div class="field-mapping">
                                    <label class="field-label">Condition</label>
                                    <input type="number"
                                           name="column_mappings[condition]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Terminal condition notes</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Issues</label>
                                    <input type="number"
                                           name="column_mappings[issues]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Known issues or problems</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Comments</label>
                                    <input type="number"
                                           name="column_mappings[comments]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">General comments</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Corrective Action</label>
                                    <input type="number"
                                           name="column_mappings[corrective_action]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">Actions taken or needed</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Site Contact</label>
                                    <input type="number"
                                           name="column_mappings[site_contact]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">On-site contact person</small>
                                </div>

                                <div class="field-mapping">
                                    <label class="field-label">Site Phone</label>
                                    <input type="number"
                                           name="column_mappings[site_phone]"
                                           placeholder="Column index"
                                           min="0" max="50"
                                           class="column-input">
                                    <small class="field-help">On-site contact phone</small>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Fields -->
                        <div class="mapping-section">
                            <h4 class="section-header">Custom Fields</h4>
                            <p class="section-note">Add mappings for additional columns in your CSV that aren't covered above</p>
                            <div class="field-mappings" id="customFieldsContainer"></div>
                            <div class="section-foot">
                                <button type="button" class="btn-secondary pt-btn-sm" onclick="addCustomField()">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add Custom Field
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="ui-card-footer pt-footer-end">
                <button type="button" class="btn-secondary" onclick="resetMappingForm()">Reset Form</button>
                <button type="button" class="btn-secondary" onclick="loadDefaultMapping()">Load Default Values</button>
                <button type="submit" class="btn-primary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg>
                    Save Column Mapping
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Mappings -->
    @if($mappings->count() > 0)
    <div class="ui-card pt-card overflow-hidden">
        <div class="ui-card-header">
            <div>
                <h2>Existing Column Mappings</h2>
                <p class="pt-sub">Manage your saved column mappings</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Mapping Name</th>
                        <th>Client</th>
                        <th>Description</th>
                        <th>Fields Mapped</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="pt-th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mappings as $mapping)
                    <tr>
                        <td>
                            <div class="cell-primary">{{ $mapping->mapping_name }}</div>
                        </td>
                        <td>
                            @if($mapping->client)
                                {{ $mapping->client->company_name }}
                            @else
                                <span class="pt-muted">General</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $mapping->description ? '' : 'pt-muted' }}">{{ Str::limit($mapping->description, 50) ?: 'No description' }}</span>
                        </td>
                        <td>
                            {{ count(array_filter($mapping->column_mappings ?? [])) }} fields
                        </td>
                        <td>
                            <span class="status-badge {{ $mapping->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $mapping->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <span class="cell-sub">{{ $mapping->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="pt-td-actions">
                            <div class="action-group">
                                <button type="button" class="action-btn" onclick="editMapping({{ $mapping->id }})" title="Edit" aria-label="Edit">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg>
                                </button>
                                <button type="button" class="action-btn" onclick="toggleMapping({{ $mapping->id }})" title="{{ $mapping->is_active ? 'Disable' : 'Enable' }}" aria-label="{{ $mapping->is_active ? 'Disable' : 'Enable' }}">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-{{ $mapping->is_active ? 'pause' : 'play' }}"/></svg>
                                </button>
                                <button type="button" class="action-btn action-delete" onclick="deleteMapping({{ $mapping->id }})" title="Delete" aria-label="Delete">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-trash"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Mapping Guide -->
    <div class="ui-card pt-card">
        <div class="ui-card-header">
            <h2>Column Mapping Guide</h2>
        </div>
        <div class="ui-card-body pt-guide">
            <div>
                <h3>How Column Mapping Works</h3>
                <ul class="pt-guide-list">
                    <li><strong>Column Index:</strong> Enter the column number (starting from 0) where each field is located in your CSV</li>
                    <li><strong>Required Fields:</strong> Terminal ID and Merchant Name are required for successful imports</li>
                    <li><strong>Optional Fields:</strong> Leave blank if your CSV doesn't have that information</li>
                    <li><strong>Custom Fields:</strong> Use the "Add Custom Field" button for columns not covered in standard fields</li>
                    <li><strong>Multiple Mappings:</strong> Create different mappings for different bank CSV formats</li>
                </ul>
            </div>

            <div>
                <h3>Example CSV Structure</h3>
                <div class="pt-csv">
                    <table>
                        <thead>
                            <tr><th>Column 0</th><th>Column 1</th><th>Column 2</th><th>Column 3</th><th>Column 4</th><th>Column 5</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Merchant ID</td><td class="is-key">Terminal ID</td><td>Type</td><td>Legal Name</td><td class="is-key">Business Name</td><td>Address</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="pt-csv-note">In this example: Terminal ID = Column 1, Merchant Name = Column 4</p>
            </div>

            <div>
                <h3>Best Practices</h3>
                <ul class="pt-guide-list">
                    <li>Create client-specific mappings for different bank formats</li>
                    <li>Use descriptive names like "Standard Bank Format" or "CBZ Monthly Export"</li>
                    <li>Test mappings with preview before processing large imports</li>
                    <li>Keep mappings updated when CSV formats change</li>
                    <li>Use custom fields for any extra columns your CSV might have</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
let customFieldCount = 0;

// Add a custom field mapping
function addCustomField() {
    customFieldCount++;
    const container = document.getElementById('customFieldsContainer');
    const fieldHtml = `
        <div class="custom-field-row" id="customField_${customFieldCount}">
            <input type="text"
                   placeholder="Field name (e.g., Bank Reference, Special Notes)"
                   class="custom-field-name"
                   onchange="updateCustomFieldName(${customFieldCount}, this.value)">
            <input type="number"
                   name="column_mappings[custom_field_${customFieldCount}]"
                   placeholder="Column index"
                   min="0" max="50"
                   class="column-input">
            <button type="button" onclick="removeCustomField(${customFieldCount})" class="btn-remove">Remove</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', fieldHtml);
}

// Update the name attribute when field name changes
function updateCustomFieldName(id, name) {
    const input = document.querySelector(`#customField_${id} input[type="number"]`);
    if (input && name.trim()) {
        const fieldName = name.trim().toLowerCase().replace(/[^a-z0-9]/g, '_');
        input.name = `column_mappings[${fieldName}]`;
    }
}

// Remove a custom field
function removeCustomField(id) {
    const element = document.getElementById(`customField_${id}`);
    if (element) {
        element.remove();
    }
}

// Load default mapping values
function loadDefaultMapping() {
    const defaultMappings = {
        'terminal_id': 1,
        'business_type': 2,
        'merchant_name': 4,
        'physical_address': 5,
        'city': 6,
        'province': 7,
        'merchant_phone': 8,
        'region': 9,
        'installation_date': 10,
        'merchant_contact_person': 11,
        'terminal_model': 12,
        'serial_number': 13,
        'status': 14,
        'condition': 15,
        'issues': 16,
        'comments': 17,
        'corrective_action': 18,
        'site_contact': 19,
        'site_phone': 20
    };

    Object.keys(defaultMappings).forEach(field => {
        const input = document.querySelector(`input[name="column_mappings[${field}]"]`);
        if (input) {
            input.value = defaultMappings[field];
        }
    });

    document.getElementById('mapping_name').value = 'Default Bank CSV Format';
    document.getElementById('description').value = 'Standard mapping for bank CSV exports with terminal information';

    alert('Default mapping values loaded! You can modify them as needed.');
}

// Reset form
function resetMappingForm() {
    if (confirm('Are you sure you want to reset the form?')) {
        document.querySelector('.mapping-form').reset();
        // Clear custom fields
        document.getElementById('customFieldsContainer').innerHTML = '';
        customFieldCount = 0;
    }
}

// Placeholder functions for table actions
function editMapping(mappingId) {
    alert(`Edit mapping functionality for ID ${mappingId} - Coming soon!`);
}

function toggleMapping(mappingId) {
    if (confirm('Are you sure you want to toggle this mapping status?')) {
        window.location.href = `/pos-terminals/column-mapping/${mappingId}/toggle`;
    }
}

function deleteMapping(mappingId) {
    if (confirm('Are you sure you want to delete this mapping? This action cannot be undone.')) {
        // Create a form and submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pos-terminals/column-mapping/${mappingId}`;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.mapping-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const mappingName = document.getElementById('mapping_name').value.trim();
            const terminalIdColumn = document.querySelector('input[name="column_mappings[terminal_id]"]').value;
            const merchantNameColumn = document.querySelector('input[name="column_mappings[merchant_name]"]').value;

            if (!mappingName) {
                alert('Please enter a mapping name.');
                e.preventDefault();
                return;
            }

            if (!terminalIdColumn || !merchantNameColumn) {
                alert('Terminal ID and Merchant Name column mappings are required.');
                e.preventDefault();
                return;
            }
        });
    }
});
</script>
@endsection
