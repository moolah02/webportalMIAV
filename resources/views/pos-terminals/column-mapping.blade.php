@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">⚙️ Column Mapping Management</h1>
            <p class="page-description">Create and manage CSV column mappings for different bank formats</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('pos-terminals.index') }}" class="btn btn-outline">
                ← Back to Terminals
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <!-- Create New Mapping Card -->
    <div class="main-card">
        <h3 class="section-title">📝 Create New Column Mapping</h3>
        <p class="section-description">Configure how CSV columns map to database fields for easier imports</p>

        <form action="{{ route('pos-terminals.store-mapping') }}" method="POST" class="mapping-form">
            @csrf

            <!-- Basic Information -->
            <div class="form-row">
                <div class="form-group">
                    <label for="mapping_name" class="form-label">Mapping Name *</label>
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

                <div class="form-group">
                    <label for="client_id" class="form-label">Associated Client (Optional)</label>
                    <select name="client_id" id="client_id" class="form-select">
                        <option value="">General Mapping (All Clients)</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description"
                          id="description"
                          placeholder="Describe when to use this mapping..."
                          rows="2"
                          class="form-textarea"></textarea>
            </div>

            <!-- Column Mapping Configuration -->
            <div class="mapping-config">
                <h4 class="config-title">🗂️ Column Mapping Configuration</h4>
                <p class="config-description">Map CSV columns (0-based index) to database fields. Leave blank to skip a field.</p>

                <div class="mapping-grid">
                    <!-- Terminal Information -->
                    <div class="mapping-section">
                        <h5 class="section-header">📟 Terminal Information</h5>
                        <div class="field-mappings">
                            <div class="field-mapping">
                                <label class="field-label">Terminal ID *</label>
                                <input type="number"
                                       name="column_mappings[terminal_id]"
                                       placeholder="Column index (e.g., 1)"
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
                        <h5 class="section-header">🏪 Merchant Information</h5>
                        <div class="field-mappings">
                            <div class="field-mapping">
                                <label class="field-label">Merchant Name *</label>
                                <input type="number"
                                       name="column_mappings[merchant_name]"
                                       placeholder="Column index (e.g., 4)"
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
                        <h5 class="section-header">📍 Location Information</h5>
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
                        <h5 class="section-header">📋 Additional Fields</h5>
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

                    <!-- NEW: Custom Fields Section -->
                    <div class="mapping-section">
                        <h5 class="section-header">🔧 Custom Fields</h5>
                        <p class="section-description">Add mappings for additional columns in your CSV that aren't covered above</p>
                        <div class="field-mappings" id="customFieldsContainer">
                            <!-- Dynamic custom field inputs will be added here -->
                        </div>
                        <button type="button" class="btn btn-outline btn-small" onclick="addCustomField()">
                            + Add Custom Field
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="resetMappingForm()">Reset Form</button>
                <button type="button" class="btn btn-outline" onclick="loadDefaultMapping()">Load Default Values</button>
                <button type="submit" class="btn btn-primary">
                    <span class="btn-icon">💾</span>
                    Save Column Mapping
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Mappings -->
    @if($mappings->count() > 0)
    <div class="main-card">
        <h3 class="section-title">📚 Existing Column Mappings</h3>
        <p class="section-description">Manage your saved column mappings</p>

        <div class="mappings-table-container">
            <table class="mappings-table">
                <thead>
                    <tr>
                        <th>Mapping Name</th>
                        <th>Client</th>
                        <th>Description</th>
                        <th>Fields Mapped</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mappings as $mapping)
                    <tr>
                        <td>
                            <div class="mapping-name">{{ $mapping->mapping_name }}</div>
                        </td>
                        <td>
                            <div class="client-name">
                                {{ $mapping->client ? $mapping->client->company_name : 'General' }}
                            </div>
                        </td>
                        <td>
                            <div class="mapping-description">
                                {{ Str::limit($mapping->description, 50) ?: 'No description' }}
                            </div>
                        </td>
                        <td>
                            <div class="fields-count">
                                {{ count(array_filter($mapping->column_mappings ?? [])) }} fields
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $mapping->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $mapping->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="created-date">{{ $mapping->created_at->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action" onclick="editMapping({{ $mapping->id }})">Edit</button>
                                <button class="btn-action" onclick="toggleMapping({{ $mapping->id }})">
                                    {{ $mapping->is_active ? 'Disable' : 'Enable' }}
                                </button>
                                <button class="btn-action danger" onclick="deleteMapping({{ $mapping->id }})">Delete</button>
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
    <div class="main-card">
        <h3 class="section-title">📖 Column Mapping Guide</h3>
        <div class="guide-content">
            <div class="guide-section">
                <h4>🎯 How Column Mapping Works</h4>
                <ul class="guide-list">
                    <li><strong>Column Index:</strong> Enter the column number (starting from 0) where each field is located in your CSV</li>
                    <li><strong>Required Fields:</strong> Terminal ID and Merchant Name are required for successful imports</li>
                    <li><strong>Optional Fields:</strong> Leave blank if your CSV doesn't have that information</li>
                    <li><strong>Custom Fields:</strong> Use the "Add Custom Field" button for columns not covered in standard fields</li>
                    <li><strong>Multiple Mappings:</strong> Create different mappings for different bank CSV formats</li>
                </ul>
            </div>

            <div class="guide-section">
                <h4>📝 Example CSV Structure</h4>
                <div class="csv-example">
                    <div class="csv-header">Column 0 | Column 1 | Column 2 | Column 3 | Column 4 | Column 5</div>
                    <div class="csv-row">Merchant ID | Terminal ID | Type | Legal Name | Business Name | Address</div>
                </div>
                <p class="csv-note">In this example: Terminal ID = Column 1, Merchant Name = Column 4</p>
            </div>

            <div class="guide-section">
                <h4>💡 Best Practices</h4>
                <ul class="guide-list">
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

<style>
/* Existing styles... */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-block-end: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #dee2e6;
}

.header-content h1 {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 28px;
}

.header-content p {
    margin: 0;
    color: #666;
    font-size: 16px;
}

.header-actions {
    flex-shrink: 0;
}

.main-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 30px;
    margin-block-end: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-title {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 20px;
}

.section-description {
    margin: 0 0 30px 0;
    color: #666;
    font-size: 14px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-block-end: 20px;
}

.form-group {
    margin-block-end: 24px;
}

.form-label {
    display: block;
    margin-block-end: 8px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s ease;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #007bff;
}

.form-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 4px;
}

.mapping-config {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #dee2e6;
}

.config-title {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 18px;
}

.config-description {
    margin: 0 0 30px 0;
    color: #666;
    font-size: 14px;
}

.mapping-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.mapping-section {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
}

.section-header {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 16px;
    font-weight: 600;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.field-mappings {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.field-mapping {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.field-label {
    font-weight: 500;
    color: #333;
    font-size: 13px;
}

.column-input {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 13px;
    transition: border-color 0.2s ease;
}

.column-input:focus {
    outline: none;
    border-color: #007bff;
}

.field-help {
    color: #666;
    font-size: 11px;
    font-style: italic;
}

/* NEW: Custom field styles */
.custom-field-row {
    display: grid;
    grid-template-columns: 1fr auto auto auto;
    gap: 8px;
    align-items: center;
    padding: 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
}

.custom-field-name {
    padding: 6px 10px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 12px;
}

.btn-remove {
    padding: 4px 8px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 3px;
    font-size: 11px;
    cursor: pointer;
}

.btn-remove:hover {
    background: #c82333;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    border: 1px solid;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-primary {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.btn-outline {
    background: white;
    border-color: #dee2e6;
    color: #333;
}

.btn-outline:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.btn-small {
    padding: 8px 16px;
    font-size: 12px;
    min-width: auto;
}

.btn-action {
    padding: 6px 12px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 12px;
    color: #333;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.btn-action.danger:hover {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

.btn-icon {
    margin-right: 6px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}

/* Alert styles */
.alert {
    padding: 12px 16px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}

/* Table styles and other existing styles remain the same... */
.mappings-table-container {
    overflow-x: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.mappings-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.mappings-table th {
    background: #f8f9fa;
    padding: 16px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #333;
    border-bottom: 2px solid #dee2e6;
}

.mappings-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: top;
}

.mappings-table tbody tr:hover {
    background: #f8f9fa;
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.guide-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.guide-section h4 {
    margin: 0 0 16px 0;
    color: #333;
    font-size: 16px;
}

.guide-list {
    margin: 0;
    padding-left: 20px;
    color: #666;
}

.guide-list li {
    margin-block-end: 8px;
    line-height: 1.5;
}

.csv-example {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 12px;
    font-family: monospace;
    font-size: 12px;
    margin: 16px 0;
}

.csv-header {
    font-weight: bold;
    color: #333;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 4px;
    margin-block-end: 4px;
}

.csv-row {
    color: #666;
}

.csv-note {
    font-size: 12px;
    color: #666;
    font-style: italic;
    margin-top: 8px;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .mapping-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .action-buttons {
        flex-direction: column;
    }

    .custom-field-row {
        grid-template-columns: 1fr;
        gap: 8px;
    }
}
</style>

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
