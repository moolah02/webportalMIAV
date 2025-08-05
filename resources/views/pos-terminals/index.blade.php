@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_terminals'] }}</div>
            <div class="stat-label">Total Terminals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['active_terminals'] }}</div>
            <div class="stat-label">Active Terminals</div>
        </div>
        <div class="stat-card alert">
            <div class="stat-number">{{ $stats['faulty_terminals'] }}</div>
            <div class="stat-label">Need Attention</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['offline_terminals'] }}</div>
            <div class="stat-label">Offline</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tab-navigation">
        <button class="tab-btn active" onclick="switchTab('overview')">
            Terminal Overview
        </button>
        <button class="tab-btn" onclick="switchTab('import')">
            Import Bank Data
        </button>
        <button class="tab-btn" onclick="switchTab('field')">
            Field Updates
        </button>
    </div>

    <!-- Terminal Overview Tab -->
    <div id="overview-tab" class="tab-content active">
        <div class="main-card">
            <!-- Filters Section -->
            <form method="GET" action="{{ route('pos-terminals.index') }}" class="filters-form">
                <!-- Search and Actions Row -->
                <div class="search-actions-row">
                    <div class="search-container">
                        <input type="text" 
                               name="search" 
                               placeholder="Search terminals..." 
                               value="{{ request('search') }}"
                               class="search-input">
                    </div>
                    
                    <div class="actions-container">
                        <button type="submit" class="btn btn-secondary">Search</button>
                        <a href="{{ route('pos-terminals.index') }}" class="btn btn-outline">Clear</a>
                        <a href="{{ route('pos-terminals.create') }}" class="btn btn-primary">Add Terminal</a>
                        <a href="{{ route('pos-terminals.export', request()->query()) }}" class="btn btn-outline">Export</a>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="filters-row">
                    <select name="client" class="filter-select">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="filter-select">
                        <option value="">All Status</option>
                        @if(is_array($statusOptions) || is_object($statusOptions))
                            @foreach($statusOptions as $slug => $name)
                                <option value="{{ $slug }}" {{ request('status') == $slug ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        @else
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="faulty" {{ request('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                        @endif
                    </select>

                    <select name="region" class="filter-select">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                                {{ $region }}
                            </option>
                        @endforeach
                    </select>

                    <select name="city" class="filter-select">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>

                    <select name="province" class="filter-select">
                        <option value="">All Provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Terminals Table -->
            <div class="table-container">
                <table class="terminals-table">
                    <thead>
                        <tr>
                            <th>Terminal ID</th>
                            <th>Client/Bank</th>
                            <th>Merchant</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Last Service</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terminals as $terminal)
                        <tr>
                            <td>
                                <div class="terminal-id">{{ $terminal->terminal_id }}</div>
                            </td>
                            <td>
                                <div class="client-name">{{ $terminal->client->company_name }}</div>
                            </td>
                            <td>
                                <div class="merchant-name">{{ $terminal->merchant_name }}</div>
                                @if($terminal->business_type)
                                <div class="business-type">{{ $terminal->business_type }}</div>
                                @endif
                            </td>
                            <td>
                                @if($terminal->merchant_contact_person)
                                <div class="contact-name">{{ $terminal->merchant_contact_person }}</div>
                                @endif
                                @if($terminal->merchant_phone)
                                <div class="contact-phone">{{ $terminal->merchant_phone }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="location-primary">{{ $terminal->region ?: 'No region' }}</div>
                                @if($terminal->city)
                                <div class="location-secondary">{{ $terminal->city }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ $terminal->status }}">
                                    {{ ucfirst($terminal->status) }}
                                </span>
                            </td>
                            <td>
                                @if($terminal->last_service_date)
                                <div class="service-date">{{ $terminal->last_service_date->format('M d, Y') }}</div>
                                <div class="service-ago">{{ $terminal->last_service_date->diffForHumans() }}</div>
                                @else
                                <div class="no-service">Never serviced</div>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('pos-terminals.show', $terminal) }}" class="btn-action">View</a>
                                    <a href="{{ route('pos-terminals.edit', $terminal) }}" class="btn-action">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div class="empty-message">
                                    <h4>No terminals found</h4>
                                    <p>Try adjusting your filters or <a href="{{ route('pos-terminals.create') }}">add your first terminal</a></p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($terminals->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $terminals->firstItem() ?? 0 }} to {{ $terminals->lastItem() ?? 0 }} of {{ $terminals->total() }} terminals
                </div>
                <div class="pagination-links">
                    {{ $terminals->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Import Tab -->
    <div id="import-tab" class="tab-content">
        <div class="main-card">
            <h3 class="section-title">📤 Import Terminal Data</h3>
            <p class="section-description">Bulk import terminal data from bank or client CSV files with flexible column mapping</p>

            <form action="{{ route('pos-terminals.import') }}" method="POST" enctype="multipart/form-data" class="import-form">
                @csrf
                
                <!-- Client Selection -->
                <div class="form-group">
                    <label for="client_id" class="form-label">Select Client/Bank *</label>
                    <select name="client_id" id="client_id" required class="form-select">
                        <option value="">Choose the client for these terminals...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Column Mapping Selection -->
                <div class="form-group">
                    <label for="mapping_id" class="form-label">Column Mapping (Optional)</label>
                    <div class="mapping-container">
                        <select name="mapping_id" id="mapping_id" class="form-select">
                            <option value="">Use Default Mapping</option>
                            @if(isset($mappings) && $mappings->count() > 0)
                                @foreach($mappings as $mapping)
                                    <option value="{{ $mapping->id }}" data-description="{{ $mapping->description }}">
                                        {{ $mapping->mapping_name }}
                                        @if($mapping->client)
                                            ({{ $mapping->client->company_name }})
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="mapping-actions">
                            <a href="{{ route('pos-terminals.column-mapping') }}" class="btn btn-outline btn-small" target="_blank">
                                Create New Mapping
                            </a>
                            <a href="{{ route('pos-terminals.column-mapping') }}" class="btn btn-outline btn-small" target="_blank">
                                Manage Mappings
                            </a>
                        </div>
                    </div>
                    <small class="help-text">Select a pre-configured mapping for this bank's specific CSV format, or use the default mapping</small>
                    <div id="mappingDescription" class="mapping-description" style="display: none;"></div>
                </div>

                <!-- File Upload -->
                <div class="file-upload-area">
                    <div class="upload-icon">📁</div>
                    <h4>Upload Your CSV File</h4>
                    <p>Select your terminal data CSV file with bank information</p>
                    
                    <input type="file" 
                           name="file" 
                           id="csvFile"
                           accept=".csv" 
                           required
                           class="file-input">
                    
                    @error('file')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    
                    <div id="fileName" class="file-name"></div>
                    <div class="file-info">
                        Supported formats: CSV only • Max size: 10MB<br>
                        <span class="file-format-note">💡 Excel files: Save as CSV (Comma delimited) before uploading</span>
                    </div>
                </div>

                <!-- Import Options -->
                <div class="form-group">
                    <label class="form-label">Import Options</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="options[]" value="skip_duplicates" checked>
                            <span class="checkmark"></span>
                            Skip duplicate terminal IDs
                            <small>Existing terminals with same ID will be ignored</small>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="options[]" value="update_existing">
                            <span class="checkmark"></span>
                            Update existing terminals with new data
                            <small>Override existing terminal data with imported values</small>
                        </label>
                    </div>
                </div>

                <!-- Preview Button -->
                <div class="form-group">
                    <button type="button" id="previewBtn" class="btn btn-outline" disabled>
                        <span class="btn-icon">👁️</span>
                        Preview Import Data
                    </button>
                    <small class="help-text">Upload a file to enable preview</small>
                </div>

                <!-- Data Preview -->
                <div id="dataPreview" class="data-preview" style="display: none;">
                    <h4>📊 Data Preview</h4>
                    <div class="preview-content">
                        <div class="preview-stats">
                            <span class="stat">Mapping: <strong id="mappingName">Default</strong></span>
                            <span class="stat">Rows: <strong id="rowCount">0</strong></span>
                            <span class="stat">Columns: <strong id="columnCount">0</strong></span>
                        </div>
                        <div class="preview-table-container">
                            <table id="previewTable" class="preview-table"></table>
                        </div>
                        <div class="preview-actions">
                            <button type="button" class="btn btn-outline btn-small" onclick="closePreview()">Close Preview</button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="resetForm()">Reset Form</button>
                    <a href="{{ route('pos-terminals.download-template') }}" class="btn btn-outline">
                        <span class="btn-icon">📥</span>
                        Download Template
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-icon">⚡</span>
                        Process Import
                    </button>
                </div>
            </form>

            <!-- Import Tips -->
            <div class="import-tips">
                <h4>💡 Import Tips</h4>
                <div class="tips-grid">
                    <div class="tip-card">
                        <div class="tip-icon">📋</div>
                        <div class="tip-content">
                            <strong>CSV Format</strong>
                            <p>Ensure your file is saved as CSV (Comma delimited) format</p>
                        </div>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">🎯</div>
                        <div class="tip-content">
                            <strong>Required Fields</strong>
                            <p>Terminal ID and Merchant Name are required for each row</p>
                        </div>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">🔄</div>
                        <div class="tip-content">
                            <strong>Dynamic Updates</strong>
                            <p>Technicians can update imported terminals via mobile app</p>
                        </div>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">⚙️</div>
                        <div class="tip-content">
                            <strong>Column Mapping</strong>
                            <p>Create custom mappings for different bank CSV formats</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Field Updates Tab -->
    <div id="field-tab" class="tab-content">
        <div class="main-card">
            <h3 class="section-title">🔧 Technician Field Updates</h3>
            <p class="section-description">Update terminal status and service information after field visits</p>

            <form class="field-update-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Terminal ID</label>
                        <input type="text" placeholder="Enter Terminal ID" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Service Type</label>
                        <select class="form-select">
                            <option>Maintenance</option>
                            <option>Installation</option>
                            <option>Repair</option>
                            <option>Inspection</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Current Status</label>
                        <select class="form-select">
                            <option>Active</option>
                            <option>Offline</option>
                            <option>Maintenance</option>
                            <option>Faulty</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Visit Date</label>
                        <input type="datetime-local" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Service Notes</label>
                    <textarea placeholder="Describe the work performed..." rows="4" class="form-textarea"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Terminal</button>
                    <button type="reset" class="btn btn-outline">Clear Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Main Layout */
* { box-sizing: border-box; }

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-block-end: 30px;
}

.stat-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-card.alert {
    border-left: 4px solid #dc3545;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin-block-end: 8px;
}

.stat-label {
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

/* Tab Navigation */
.tab-navigation {
    display: flex;
    border-bottom: 2px solid #dee2e6;
    margin-block-end: 30px;
    background: white;
    border-radius: 8px 8px 0 0;
    overflow: hidden;
}

.tab-btn {
    padding: 16px 24px;
    background: #f8f9fa;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 500;
    color: #666;
    transition: all 0.2s ease;
    flex: 1;
}

.tab-btn:hover {
    background: #e9ecef;
    color: #333;
}

.tab-btn.active {
    background: white;
    color: #007bff;
    border-bottom-color: #007bff;
}

/* Main Card */
.main-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Tab Content */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Filters */
.filters-form {
    margin-block-end: 30px;
}

.search-actions-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-block-end: 20px;
    gap: 20px;
}

.search-container {
    flex: 1;
    max-width: 400px;
}

.search-input {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: #007bff;
}

.actions-container {
    display: flex;
    gap: 10px;
}

.filters-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
}

.filter-select {
    padding: 10px 16px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    background: white;
    font-size: 14px;
    cursor: pointer;
    transition: border-color 0.2s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #007bff;
}

/* Buttons */
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
    text-decoration: none;
}

.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    border-color: #545b62;
    text-decoration: none;
}

.btn-outline {
    background: white;
    border-color: #dee2e6;
    color: #333;
}

.btn-outline:hover {
    background: #f8f9fa;
    border-color: #007bff;
    text-decoration: none;
}

.btn-action {
    display: inline-block;
    padding: 6px 12px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 12px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease;
}

.btn-action:hover {
    background: #f8f9fa;
    border-color: #007bff;
    text-decoration: none;
}

.btn-small {
    padding: 8px 16px;
    font-size: 12px;
    min-width: auto;
}

.btn-icon {
    margin-right: 6px;
}

/* Table */
.table-container {
    overflow-x: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.terminals-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.terminals-table th {
    background: #f8f9fa;
    padding: 16px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #333;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
}

.terminals-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: top;
}

.terminals-table tbody tr:hover {
    background: #f8f9fa;
}

/* Table Content */
.terminal-id {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.client-name {
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.merchant-name {
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.business-type {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
}

.contact-name {
    font-weight: 500;
    color: #333;
    font-size: 13px;
}

.contact-phone {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
}

.location-primary {
    font-weight: 500;
    color: #333;
    font-size: 13px;
}

.location-secondary {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
}

.service-date {
    font-weight: 500;
    color: #333;
    font-size: 13px;
}

.service-ago {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
}

.no-service {
    font-size: 13px;
    color: #999;
    font-style: italic;
}

.action-buttons {
    display: flex;
    gap: 6px;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-offline {
    background: #fff3cd;
    color: #856404;
}

.status-maintenance {
    background: #d1ecf1;
    color: #0c5460;
}

.status-faulty {
    background: #f8d7da;
    color: #721c24;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-message h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.empty-message p {
    margin: 0;
    color: #666;
}

.empty-message a {
    color: #007bff;
    text-decoration: none;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}

.pagination-info {
    color: #666;
    font-size: 14px;
}

/* Forms */
.section-title {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 24px;
}

.section-description {
    margin: 0 0 30px 0;
    color: #666;
    font-size: 16px;
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

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 4px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-block-end: 24px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}

/* File Upload */
.file-upload-area {
    border: 3px dashed #dee2e6;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
    background: #f8f9fa;
    margin-block-end: 24px;
    transition: border-color 0.2s ease;
}

.file-upload-area:hover {
    border-color: #007bff;
}

.upload-icon {
    font-size: 48px;
    margin-block-end: 16px;
}

.file-upload-area h4 {
    margin: 0 0 8px 0;
    color: #333;
}

.file-upload-area p {
    margin: 0 0 20px 0;
    color: #666;
}

.file-input {
    margin: 0 auto 16px auto;
    display: block;
    padding: 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
}

.file-name {
    font-weight: 500;
    color: #28a745;
    margin-block-end: 8px;
}

.file-info {
    font-size: 12px;
    color: #666;
}

.file-format-note {
    color: #007bff;
    font-weight: 500;
}

/* Column Mapping Styles */
.mapping-container {
    display: flex;
    gap: 12px;
    align-items: flex-end;
}

.mapping-container .form-select {
    flex: 1;
}

.mapping-actions {
    display: flex;
    gap: 8px;
}

.help-text {
    color: #666;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

.mapping-description {
    margin-top: 8px;
    padding: 8px 12px;
    background: #f8f9ff;
    border: 1px solid #e3f2fd;
    border-radius: 4px;
    font-size: 12px;
    color: #666;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
    padding: 12px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.checkbox-label:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

.checkbox-label input[type="checkbox"] {
    margin: 0;
    width: 16px;
    height: 16px;
    accent-color: #007bff;
}

.checkbox-label small {
    color: #666;
    font-size: 12px;
    margin-top: 2px;
    display: block;
}

/* Data Preview Styles */
.data-preview {
    margin-top: 24px;
    padding: 20px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
}

.data-preview h4 {
    margin: 0 0 16px 0;
    color: #333;
}

.preview-stats {
    display: flex;
    gap: 20px;
    margin-block-end: 16px;
    flex-wrap: wrap;
}

.preview-stats .stat {
    padding: 6px 12px;
    background: white;
    border-radius: 4px;
    font-size: 12px;
}

.preview-table-container {
    max-height: 300px;
    overflow: auto;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
    margin-block-end: 16px;
}

.preview-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.preview-table th,
.preview-table td {
    padding: 8px;
    border: 1px solid #dee2e6;
    text-align: left;
}

.preview-table th {
    background: #f8f9fa;
    font-weight: 600;
    position: sticky;
    top: 0;
}

.preview-actions {
    text-align: right;
}

/* Import Tips */
.import-tips {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #dee2e6;
}

.import-tips h4 {
    margin: 0 0 20px 0;
    color: #333;
}

.tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.tip-card {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #f8f9ff;
    border: 1px solid #e3f2fd;
    border-radius: 8px;
}

.tip-icon {
    font-size: 20px;
    flex-shrink: 0;
}

.tip-content strong {
    display: block;
    margin-block-end: 4px;
    color: #333;
}

.tip-content p {
    margin: 0;
    font-size: 12px;
    color: #666;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 768px) {
    .search-actions-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .actions-container {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .filters-row {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .pagination-container {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .tab-navigation {
        flex-direction: column;
    }
    
    .mapping-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .mapping-actions {
        justify-content: stretch;
    }
    
    .mapping-actions .btn {
        flex: 1;
    }
    
    .tips-grid {
        grid-template-columns: 1fr;
    }
    
    .preview-stats {
        flex-direction: column;
        gap: 8px;
    }
}
</style>

<script>
// Tab Switching
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');

    // Mark button as active
    event.target.classList.add('active');
}

// Enhanced file upload feedback and preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('csvFile');
    const fileName = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    const previewBtn = document.getElementById('previewBtn');
    const dataPreview = document.getElementById('dataPreview');
    const mappingSelect = document.getElementById('mapping_id');
    const mappingDescription = document.getElementById('mappingDescription');
    
    // File input change handler
    if (fileInput && fileName) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileName.innerHTML = `
                    <div style="color: #28a745; font-weight: 500;">
                        ✅ Selected: ${file.name}
                    </div>
                    <div style="color: #666; font-size: 12px; margin-top: 2px;">
                        Size: ${(file.size / 1024).toFixed(1)} KB
                    </div>
                `;
                if (previewBtn) {
                    previewBtn.disabled = false;
                    const helpText = previewBtn.nextElementSibling;
                    if (helpText) {
                        helpText.textContent = 'Click to preview how your data will be imported';
                    }
                }
            } else {
                fileName.textContent = '';
                if (previewBtn) {
                    previewBtn.disabled = true;
                    const helpText = previewBtn.nextElementSibling;
                    if (helpText) {
                        helpText.textContent = 'Upload a file to enable preview';
                    }
                }
                if (dataPreview) {
                    dataPreview.style.display = 'none';
                }
            }
        });
    }
    
    // Mapping selection change handler
    if (mappingSelect && mappingDescription) {
        mappingSelect.addEventListener('change', function(e) {
            const selectedOption = e.target.selectedOptions[0];
            const description = selectedOption.getAttribute('data-description');
            
            if (description) {
                mappingDescription.textContent = description;
                mappingDescription.style.display = 'block';
            } else {
                mappingDescription.style.display = 'none';
            }
        });
    }
    
    // Preview button click handler
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            const file = fileInput.files[0];
            const mappingId = mappingSelect ? mappingSelect.value : '';
            
            if (!file) {
                alert('Please select a CSV file first.');
                return;
            }
            
            previewBtn.innerHTML = '<span class="btn-icon">⏳</span> Loading Preview...';
            previewBtn.disabled = true;
            
            // Create FormData for preview request
            const formData = new FormData();
            formData.append('file', file);
            formData.append('mapping_id', mappingId);
            formData.append('preview_rows', 5);
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }
            
            // Send preview request
            fetch('/pos-terminals/preview-import', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPreview(data);
                } else {
                    alert('Preview failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Preview error:', error);
                alert('Preview failed. Please check your file format.');
            })
            .finally(() => {
                previewBtn.innerHTML = '<span class="btn-icon">👁️</span> Preview Import Data';
                previewBtn.disabled = false;
            });
        });
    }
    
    // Form submission handler
    const importForm = document.querySelector('.import-form');
    if (importForm && submitBtn) {
        importForm.addEventListener('submit', function(e) {
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Please select a CSV file before submitting.');
                return false;
            }
            
            submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Processing...';
            submitBtn.disabled = true;
        });
    }
});

function displayPreview(data) {
    const mappingName = document.getElementById('mappingName');
    const rowCount = document.getElementById('rowCount');
    const columnCount = document.getElementById('columnCount');
    const previewTable = document.getElementById('previewTable');
    const dataPreview = document.getElementById('dataPreview');
    
    if (!mappingName || !rowCount || !columnCount || !previewTable || !dataPreview) {
        console.error('Preview elements not found');
        return;
    }
    
    // Update stats
    mappingName.textContent = data.mapping_name || 'Default';
    rowCount.textContent = data.preview_data ? data.preview_data.length : 0;
    columnCount.textContent = data.headers ? data.headers.length : 0;
    
    // Build table
    let tableHTML = '<thead><tr>';
    
    // Add headers
    const displayHeaders = ['Row', 'Terminal ID', 'Merchant Name', 'City', 'Region', 'Status', 'Mapped Data'];
    displayHeaders.forEach(header => {
        tableHTML += `<th>${header}</th>`;
    });
    tableHTML += '</tr></thead><tbody>';
    
    // Add preview rows
    if (data.preview_data && data.preview_data.length > 0) {
        data.preview_data.forEach(row => {
            tableHTML += '<tr>';
            tableHTML += `<td>${row.row_number || 'N/A'}</td>`;
            tableHTML += `<td>${row.mapped_data && row.mapped_data.terminal_id || 'N/A'}</td>`;
            tableHTML += `<td>${row.mapped_data && row.mapped_data.merchant_name || 'N/A'}</td>`;
            tableHTML += `<td>${row.mapped_data && row.mapped_data.city || 'N/A'}</td>`;
            tableHTML += `<td>${row.mapped_data && row.mapped_data.region || 'N/A'}</td>`;
            const status = row.mapped_data && row.mapped_data.status || 'active';
            tableHTML += `<td><span class="status-badge status-${status}">${status}</span></td>`;
            const fieldCount = row.mapped_data ? Object.keys(row.mapped_data).length : 0;
            tableHTML += `<td><small>✅ ${fieldCount} fields mapped</small></td>`;
            tableHTML += '</tr>';
        });
    } else {
        tableHTML += '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #666;">No data to preview</td></tr>';
    }
    
    tableHTML += '</tbody>';
    previewTable.innerHTML = tableHTML;
    dataPreview.style.display = 'block';
    
    // Scroll to preview
    dataPreview.scrollIntoView({ behavior: 'smooth' });
}

function closePreview() {
    const dataPreview = document.getElementById('dataPreview');
    if (dataPreview) {
        dataPreview.style.display = 'none';
    }
}

function resetForm() {
    const importForm = document.querySelector('.import-form');
    if (importForm) {
        importForm.reset();
    }
    
    const fileName = document.getElementById('fileName');
    if (fileName) {
        fileName.textContent = '';
    }
    
    const dataPreview = document.getElementById('dataPreview');
    if (dataPreview) {
        dataPreview.style.display = 'none';
    }
    
    const previewBtn = document.getElementById('previewBtn');
    if (previewBtn) {
        previewBtn.disabled = true;
        const helpText = previewBtn.nextElementSibling;
        if (helpText) {
            helpText.textContent = 'Upload a file to enable preview';
        }
    }
    
    const mappingDescription = document.getElementById('mappingDescription');
    if (mappingDescription) {
        mappingDescription.style.display = 'none';
    }
}
</script>
@endsection