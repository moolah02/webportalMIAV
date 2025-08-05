@extends('layouts.app')

@section('content')
<div class="deployment-planning">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-info">
                <h4>🗺️ Deployment Planning</h4>
                <p>Create and manage deployment templates for efficient technician assignments</p>
            </div>
            <div class="header-actions">
                <button onclick="showAnalytics()" class="btn btn-secondary">
                    📊 Analytics
                </button>
                <button onclick="exportTemplates()" class="btn btn-success">
                    📄 Export Templates
                </button>
                <button onclick="refreshData()" class="btn btn-primary">
                    🔄 Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['total_templates'] }}</div>
                <div class="stat-label">Total Templates</div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['active_templates'] }}</div>
                <div class="stat-label">Active Templates</div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon">📍</div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['regions_with_templates'] }}/{{ $stats['total_regions'] }}</div>
                <div class="stat-label">Regions Covered</div>
            </div>
        </div>
        <div class="stat-card purple">
            <div class="stat-icon">🖥️</div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['deployed_terminals'] }}/{{ $stats['total_terminals'] }}</div>
                <div class="stat-label">Terminals Deployed</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="main-grid">
        <!-- Create Template Form -->
        <div class="content-card form-card">
            <div class="card-header">
                <h3>📝 Create Deployment Template</h3>
            </div>
            <div class="card-body">
                <!-- REPLACE the form section in your view with this updated structure: -->

<form id="deploymentTemplateForm">
    @csrf
    <input type="hidden" name="pos_terminals" id="pos_terminals_json">
    
    <!-- Client Selection -->
    <div class="form-group">
        <label for="clientFilter" class="form-label required">Client</label>
        <select name="client_id" id="clientFilter" class="form-select" required>
            <option value="">Select Client</option>
            @foreach($clients as $client)
            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Region Selection (NOW SEPARATE AND REQUIRED) -->
    <div class="form-group">
        <label for="regionFilterInput" class="form-label required">Region</label>
        <select id="regionFilterInput" name="region_id" class="form-select" required>
            <option value="">Select Region</option>
            @foreach($regions as $region)
            <option value="{{ $region->id }}">{{ $region->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Grouping Method -->
    <div class="form-group">
        <label for="groupBy" class="form-label required">🔀 Group Terminals By</label>
        <select name="group_by" id="groupBy" class="form-select" required>
            <option value="">Select grouping method</option>
            <option value="city">City</option>
            <option value="province">Province</option>
            <option value="area">Area</option>
            <option value="address">Physical Address</option>
        </select>
    </div>

    <!-- Dynamic Filter Sections -->
    <div id="citySelect" class="form-group filter-section" style="display: none;">
        <label for="cityFilterInput" class="form-label required">City</label>
        <select id="cityFilterInput" name="city_filter" class="form-select">
            <option value="">Select City</option>
        </select>
    </div>

    <div id="provinceSelect" class="form-group filter-section" style="display: none;">
        <label for="provinceFilterInput" class="form-label required">Province</label>
        <select id="provinceFilterInput" name="province_filter" class="form-select">
            <option value="">Select Province</option>
        </select>
    </div>

    <div id="areaSelect" class="form-group filter-section" style="display: none;">
        <label for="areaFilterInput" class="form-label required">Area</label>
        <select id="areaFilterInput" name="area_filter" class="form-select">
            <option value="">Select Area</option>
        </select>
    </div>

    <div id="addressSelect" class="form-group filter-section" style="display: none;">
        <label for="addressFilterInput" class="form-label required">Physical Address</label>
        <select id="addressFilterInput" name="address_filter" class="form-select">
            <option value="">Select Address</option>
        </select>
    </div>

    <!-- Template Name -->
    <div class="form-group">
        <label for="templateName" class="form-label required">Template Name</label>
        <input type="text" name="template_name" id="templateName" class="form-input" 
               placeholder="e.g., Harare City Maintenance Q1" required>
    </div>
    
    <!-- Rest of the form remains the same -->
    <div class="form-group">
        <label class="form-label required">POS Terminals</label>
        <div class="terminals-container">
            <div id="terminalsContainer">
                <div class="empty-state">
                    <div class="empty-icon">🗺️</div>
                    <div class="empty-text">Select client, region, and grouping to view terminals</div>
                </div>
            </div>
        </div>
        <div class="terminals-actions">
            <span id="selectedCount" class="selected-count">0 terminals selected</span>
            <div class="action-links">
                <a href="#" onclick="selectAllTerminals()" class="action-link">Select All</a>
                <a href="#" onclick="clearAllTerminals()" class="action-link danger">Clear All</a>
            </div>
        </div>
    </div>
    
    <!-- Service Details -->
    <div class="form-row">
        <div class="form-group">
            <label for="serviceType" class="form-label required">Service Type</label>
            <select name="service_type" id="serviceType" class="form-select" required>
                <option value="">Select Service Type</option>
                @foreach($serviceTypes as $serviceType)
                <option value="{{ $serviceType->slug }}" 
                        data-icon="{{ $serviceType->icon }}" 
                        data-color="{{ $serviceType->color }}">
                    {!! $serviceType->icon !!} {{ $serviceType->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="priority" class="form-label required">Priority</label>
            <select name="priority" id="priority" class="form-select" required>
                <option value="normal">🔵 Normal</option>
                <option value="high">🟡 High</option>
                <option value="low">⚪ Low</option>
                <option value="emergency">🔴 Emergency</option>
            </select>
        </div>
    </div>
    
    <!-- Additional Details -->
    <div class="form-group">
        <label for="estimatedDuration" class="form-label">Estimated Duration per Terminal (hours)</label>
        <input type="number" name="estimated_duration_hours" id="estimatedDuration"
               class="form-input" step="0.5" min="0.5" max="12" placeholder="2.0">
        <small class="form-help">Total estimated time will be calculated based on selected terminals</small>
    </div>
    
    <div class="form-group">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-textarea" rows="2"
                  placeholder="Brief description of this deployment template..."></textarea>
    </div>
    
    <div class="form-group">
        <label for="notes" class="form-label">Notes & Instructions</label>
        <textarea name="notes" id="notes" class="form-textarea" rows="3"
                  placeholder="Special instructions, requirements, or notes for technicians..."></textarea>
    </div>
    
    <div class="form-group">
        <label for="tags" class="form-label">Tags (comma-separated)</label>
        <input type="text" name="tags" id="tags" class="form-input"
               placeholder="harare, maintenance, quarterly, etc.">
    </div>
    
    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-large">
            ➕ Create Template
        </button>
        <button type="button" onclick="resetTemplateForm()" class="btn btn-secondary">
            🔄 Reset
        </button>
    </div>
</form>
            </div>
        </div>

        <!-- Templates List -->
        <div class="content-card list-card">
            <div class="card-header">
                <h3>📋 Deployment Templates</h3>
                <div class="header-filters">
                    <select id="regionFilter" class="filter-select">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                    <select id="statusFilter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="templates-list" id="templatesList">
                    @forelse($templates as $template)
                    <div class="template-item" data-region="{{ $template->region_id }}" 
                         data-status="{{ $template->is_active ? 'active' : 'inactive' }}">
                        <div class="template-header">
                            <div class="template-title">{{ $template->template_name }}</div>
                            <div class="template-meta">
                                {{ $template->region->name ?? 'N/A' }} • {{ $template->creator->first_name ?? 'Unknown' }} {{ $template->creator->last_name ?? '' }}
                            </div>
                        </div>
                        
                        <div class="template-details">
                            <div class="template-badges">
                                <span class="badge badge-info">
                                    {{ $template->terminals_count }} terminals
                                </span>
                                @if($template->estimated_duration_hours)
                                <span class="badge badge-purple">
                                    {{ $template->estimated_duration_hours }}h/terminal
                                </span>
                                @endif
                                <span class="template-date">{{ $template->created_at->format('M d, Y') }}</span>
                            </div>
                            <span class="priority-badge priority-{{ $template->priority }}">
                                {{ ucfirst($template->priority) }}
                            </span>
                        </div>

                        <div class="template-service">
                            <span class="service-badge">
                                {{ $template->service_type_display }}
                            </span>
                        </div>

                        @if($template->tags)
                        <div class="template-tags">
                            @foreach($template->tags as $tag)
                                <span class="tag">🏷️ {{ $tag }}</span>
                            @endforeach
                        </div>
                        @endif

                        <div class="template-actions">
                            <button onclick="viewTemplate({{ $template->id }})" class="btn-small btn-outline">
                                👁️ View
                            </button>
                            @if($template->is_active)
                                <button onclick="deployTemplate({{ $template->id }})" class="btn-small btn-success">
                                    🚀 Deploy
                                </button>
                                <button onclick="editTemplate({{ $template->id }})" class="btn-small btn-outline">
                                    ✏️ Edit
                                </button>
                            @endif
                            <button onclick="deleteTemplate({{ $template->id }})" class="btn-small btn-danger">
                                🗑️ Delete
                            </button>
                        </div>

                        <div class="template-status">
                            <span class="status-badge status-{{ $template->is_active ? 'active' : 'inactive' }}">
                                {{ $template->is_active ? '✅ Active' : '❌ Inactive' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state-large">
                        <div class="empty-icon">📋</div>
                        <h3>No deployment templates found</h3>
                        <p>Create your first deployment template to get started with organized technician assignments.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <div class="spinner"></div>
        <div class="loading-text">Loading...</div>
    </div>
</div>

<!-- Template Details Modal -->
<div id="templateModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><span>🗺️</span><span id="modalTitle">Template Details</span></h3>
            <button onclick="closeModal()" class="modal-close">×</button>
        </div>
        <div id="modalContent" class="modal-body"></div>
    </div>
</div>

<!-- Deploy Template Modal -->
<div id="deployModal" class="modal">
    <div class="modal-content deploy-modal">
        <div class="modal-header deploy-header">
            <h3><span>🚀</span><span>Deploy Template</span></h3>
            <button onclick="closeDeployModal()" class="modal-close">×</button>
        </div>
        <div class="modal-body">
            <form id="deployForm">
                @csrf
                <div class="form-group">
                    <label for="deployTechnician" class="form-label required">Technician</label>
                    <select name="technician_id" id="deployTechnician" class="form-select" required>
                        <option value="">Select Technician</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">
                                {{ $technician->name }} – {{ $technician->specialization }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="deployDate" class="form-label required">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="deployDate" class="form-input" 
                           required min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="deployNotes" class="form-label">Additional Notes</label>
                    <textarea name="additional_notes" id="deployNotes" class="form-textarea" rows="3"
                              placeholder="Any additional instructions or notes..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">🚀 Deploy Template</button>
                    <button type="button" onclick="closeDeployModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Modern Design System */
.deployment-planning {
    padding: 0;
    max-width: 100%;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    margin-bottom: 2rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-info h2 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.header-info p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card.primary::before { background: linear-gradient(90deg, #2196f3, #1976d2); }
.stat-card.success::before { background: linear-gradient(90deg, #4caf50, #388e3c); }
.stat-card.warning::before { background: linear-gradient(90deg, #ff9800, #f57c00); }
.stat-card.purple::before { background: linear-gradient(90deg, #9c27b0, #7b1fa2); }

.stat-icon {
    font-size: 3rem;
    opacity: 0.9;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: #333;
}

.stat-label {
    font-size: 1rem;
    color: #666;
    font-weight: 500;
}

/* Main Grid Layout */
.main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    min-height: 600px;
}

@media (max-width: 1200px) {
    .main-grid {
        grid-template-columns: 1fr;
    }
}

/* Cards */
.content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.content-card:hover {
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}

.card-header {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e0e7ff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1565c0;
}

.card-body {
    padding: 2rem;
}

/* Form Styles */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.form-label.required::after {
    content: ' *';
    color: #ef4444;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-help {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.25rem;
    display: block;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

/* Filter Sections */
.filter-section {
    transition: all 0.3s ease;
}

/* Terminals Container */
.terminals-container {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    max-height: 300px;
    overflow-y: auto;
    background: #f9fafb;
}

.terminals-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

.selected-count {
    color: #6b7280;
    font-weight: 500;
}

.action-links {
    display: flex;
    gap: 1rem;
}

.action-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.action-link:hover {
    color: #1d4ed8;
}

.action-link.danger {
    color: #ef4444;
}

.action-link.danger:hover {
    color: #dc2626;
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-state-large {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-text {
    font-size: 1rem;
    margin-top: 1rem;
}

/* Buttons */
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669, #047857);
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1rem;
}

.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

.btn-outline {
    background: transparent;
    border: 2px solid #e5e7eb;
    color: #374151;
}

.btn-outline:hover {
    border-color: #3b82f6;
    color: #3b82f6;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

/* Filter Controls */
.header-filters {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.85rem;
    background: white;
}

/* Template Items */
.templates-list {
    max-height: 600px;
    overflow-y: auto;
}

.template-item {
    background: white;
    border: 2px solid #f3f4f6;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    position: relative;
}

.template-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.template-header {
    margin-bottom: 1rem;
}

.template-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.template-meta {
    font-size: 0.85rem;
    color: #6b7280;
}

.template-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.template-badges {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.template-date {
    font-size: 0.75rem;
    color: #9ca3af;
}

.template-service {
    margin-bottom: 1rem;
}

.template-tags {
    margin-bottom: 1rem;
}

.template-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.template-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

/* Badges */
.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.badge-purple {
    background: #e9d5ff;
    color: #7c3aed;
}

.priority-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.priority-emergency {
    background: #fee2e2;
    color: #dc2626;
}

.priority-high {
    background: #fef3c7;
    color: #d97706;
}

.priority-normal {
    background: #dbeafe;
    color: #2563eb;
}

.priority-low {
    background: #f3f4f6;
    color: #6b7280;
}

.service-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    background: #f3f4f6;
    color: #374151;
}

.tag {
    background: #f9fafb;
    color: #6b7280;
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    font-size: 0.7rem;
    margin-right: 0.25rem;
    display: inline-block;
    margin-bottom: 0.25rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

/* Modals */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(4px);
}

.modal-content {
    background: white;
    border-radius: 16px;
    padding: 0;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem 2rem;
    position: relative;
}

.deploy-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.modal-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
}

.modal-close {
    position: absolute;
    top: 1rem;
    right: 1.5rem;
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

.modal-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 2rem;
    max-height: 60vh;
    overflow-y: auto;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(4px);
}

.loading-spinner {
    text-align: center;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f4f6;
    border-top: 4px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: #6b7280;
    font-weight: 500;
}

/* Terminal Checkboxes */
.terminal-checkbox {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
}

.terminal-checkbox:hover {
    background: #f0f9ff;
    border-color: #3b82f6;
}

.terminal-checkbox input {
    cursor: pointer;
}

.terminal-info {
    flex: 1;
}

.terminal-name {
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.25rem;
}

.terminal-details {
    font-size: 0.8rem;
    color: #6b7280;
}

/* City Groups */
.city-group {
    margin-bottom: 1rem;
}

.city-header {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #e2e8f0;
}

.city-terminals {
    padding-left: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .header-actions {
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .main-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .template-item {
        padding: 1rem;
    }
    
    .template-actions {
        justify-content: center;
    }
    
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .stat-card {
        padding: 1.5rem;
    }
    
    .stat-icon {
        font-size: 2.5rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .template-badges {
        justify-content: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Global variables
let selectedTerminals = [];
let currentTemplateId = null;
let terminalsData = {};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    setupCSRF();
});

function setupEventListeners() {
    // Form change handlers
    document.getElementById('clientFilter').addEventListener('change', handleClientChange);
    document.getElementById('groupBy').addEventListener('change', handleGroupByChange);
    document.getElementById('regionFilterInput').addEventListener('change', handleFilterChange);
    document.getElementById('cityFilterInput').addEventListener('change', handleFilterChange);
    document.getElementById('addressFilterInput').addEventListener('change', handleFilterChange);
    
    // Form submission
    document.getElementById('deploymentTemplateForm').addEventListener('submit', handleFormSubmit);
    
    // Deploy form submission
    document.getElementById('deployForm').addEventListener('submit', handleDeploySubmit);
    
    // Filter handlers
    document.getElementById('regionFilter').addEventListener('change', filterTemplates);
    document.getElementById('statusFilter').addEventListener('change', filterTemplates);
    
    // Modal close handlers
    document.addEventListener('click', function(e) {
        if (e.target.matches('#templateModal') || e.target.matches('#deployModal')) {
            closeModal();
            closeDeployModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeDeployModal();
        }
    });
}

function setupCSRF() {
    // Set up CSRF token for all AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (token) {
        window.csrfToken = token;
    }
}

// Handle client selection change
function handleClientChange() {
    const clientId = document.getElementById('clientFilter').value;
    const groupBy = document.getElementById('groupBy').value;
    
    // Reset dependent fields
    clearDependentSelects();
    clearTerminalsContainer();
    
    if (clientId && groupBy) {
        loadOptionsForGrouping(clientId, groupBy);
    }
}

// Handle grouping method change
function handleGroupByChange() {
    const groupBy = document.getElementById('groupBy').value;
    const clientId = document.getElementById('clientFilter').value;
    
    // Show/hide appropriate filter sections
    document.getElementById('regionSelect').style.display = 'none';
    document.getElementById('citySelect').style.display = 'none';
    document.getElementById('addressSelect').style.display = 'none';
    
    if (groupBy === 'region') {
        document.getElementById('regionSelect').style.display = 'block';
    } else if (groupBy === 'city') {
        document.getElementById('citySelect').style.display = 'block';
    } else if (groupBy === 'address') {
        document.getElementById('addressSelect').style.display = 'block';
    }
    
    // Clear dependent selects and terminals
    clearDependentSelects();
    clearTerminalsContainer();
    
    // Load options if client is selected
    if (clientId && groupBy) {
        loadOptionsForGrouping(clientId, groupBy);
    }
}

// Handle filter selection change
function handleFilterChange() {
    loadTerminalsForSelection();
}

// Load options based on grouping method
function loadOptionsForGrouping(clientId, groupBy) {
    if (groupBy === 'city') {
        loadCitiesForClient(clientId);
    } else if (groupBy === 'address') {
        loadAddressesForClient(clientId);
    }
    // Region options are already loaded from the server
}

// Load cities for client
function loadCitiesForClient(clientId) {
    showLoading(true);
    
    fetch(`/api/clients/${clientId}/cities`, {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(cities => {
        const citySelect = document.getElementById('cityFilterInput');
        citySelect.innerHTML = '<option value="">Select City</option>';
        
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading cities:', error);
        showNotification('Error loading cities', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Load addresses for client
function loadAddressesForClient(clientId) {
    showLoading(true);
    
    fetch(`/api/clients/${clientId}/addresses`, {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(addresses => {
        const addressSelect = document.getElementById('addressFilterInput');
        addressSelect.innerHTML = '<option value="">Select Address</option>';
        
        addresses.forEach(address => {
            const option = document.createElement('option');
            option.value = address;
            option.textContent = address;
            addressSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading addresses:', error);
        showNotification('Error loading addresses', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Load terminals based on current selection
function loadTerminalsForSelection() {
    const clientId = document.getElementById('clientFilter').value;
    const groupBy = document.getElementById('groupBy').value;
    
    if (!clientId || !groupBy) {
        clearTerminalsContainer();
        return;
    }
    
    let filterValue = '';
    if (groupBy === 'region') {
        filterValue = document.getElementById('regionFilterInput').value;
    } else if (groupBy === 'city') {
        filterValue = document.getElementById('cityFilterInput').value;
    } else if (groupBy === 'address') {
        filterValue = document.getElementById('addressFilterInput').value;
    }
    
    if (!filterValue) {
        clearTerminalsContainer();
        return;
    }
    
    // Show loading state
    showTerminalsLoading();
    
    // Build URL parameters
    const params = new URLSearchParams({
        client_id: clientId,
        group_by: groupBy,
        filter_value: filterValue
    });
    
    fetch(`/api/terminals?${params}`, {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.terminals && data.terminals.length > 0) {
            renderTerminals(data.terminals, groupBy);
        } else {
            showEmptyTerminals();
        }
    })
    .catch(error => {
        console.error('Error loading terminals:', error);
        showTerminalsError();
        showNotification('Error loading terminals', 'error');
    });
}

// Render terminals in the container
function renderTerminals(terminals, groupBy) {
    const container = document.getElementById('terminalsContainer');
    
    if (groupBy === 'city') {
        // Group terminals by city
        const terminalsByCity = groupTerminalsByCity(terminals);
        let html = '';
        
        Object.keys(terminalsByCity).forEach(city => {
            html += `
                <div class="city-group">
                    <div class="city-header">
                        <span>${city}</span>
                        <span>${terminalsByCity[city].length} terminals</span>
                    </div>
                    <div class="city-terminals">
                        ${terminalsByCity[city].map(terminal => createTerminalCheckbox(terminal)).join('')}
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } else {
        // Simple list for region or address grouping
        container.innerHTML = terminals.map(terminal => createTerminalCheckbox(terminal)).join('');
    }
    
    updateSelectedCount();
}

// Group terminals by city
function groupTerminalsByCity(terminals) {
    return terminals.reduce((groups, terminal) => {
        const city = terminal.city || 'Unknown City';
        if (!groups[city]) {
            groups[city] = [];
        }
        groups[city].push(terminal);
        return groups;
    }, {});
}

// Create terminal checkbox HTML
function createTerminalCheckbox(terminal) {
    const statusColors = {
        'active': '#10b981',
        'offline': '#ef4444',
        'maintenance': '#f59e0b',
        'faulty': '#f97316'
    };
    
    const statusColor = statusColors[terminal.status] || '#6b7280';
    
    return `
        <div class="terminal-checkbox">
            <input type="checkbox" 
                   id="terminal_${terminal.id}" 
                   value="${terminal.id}"
                   onchange="toggleTerminal(${terminal.id})">
            <label for="terminal_${terminal.id}" class="terminal-info">
                <div class="terminal-name">${terminal.terminal_id || 'N/A'}</div>
                <div class="terminal-details">
                    ${terminal.merchant_name || 'Unknown Merchant'} • 
                    ${terminal.physical_address || 'No address'} • 
                    <span style="color: ${statusColor};">
                        ${terminal.status || 'Unknown'}
                    </span>
                </div>
            </label>
        </div>
    `;
}

// Toggle terminal selection
function toggleTerminal(terminalId) {
    const checkbox = document.getElementById(`terminal_${terminalId}`);
    if (checkbox.checked) {
        if (!selectedTerminals.includes(terminalId)) {
            selectedTerminals.push(terminalId);
        }
    } else {
        selectedTerminals = selectedTerminals.filter(id => id !== terminalId);
    }
    updateSelectedCount();
}

// Select all terminals
function selectAllTerminals() {
    const checkboxes = document.querySelectorAll('#terminalsContainer input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        const terminalId = parseInt(checkbox.value);
        if (!selectedTerminals.includes(terminalId)) {
            selectedTerminals.push(terminalId);
        }
    });
    updateSelectedCount();
}

// Clear all terminals
function clearAllTerminals() {
    const checkboxes = document.querySelectorAll('#terminalsContainer input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedTerminals = [];
    updateSelectedCount();
}

// Update selected count display
function updateSelectedCount() {
    const count = selectedTerminals.length;
    const countText = count === 1 ? '1 terminal selected' : `${count} terminals selected`;
    document.getElementById('selectedCount').textContent = countText;
}

// Clear dependent selects
function clearDependentSelects() {
    document.getElementById('cityFilterInput').innerHTML = '<option value="">Select City</option>';
    document.getElementById('addressFilterInput').innerHTML = '<option value="">Select Address</option>';
}

// Clear terminals container
function clearTerminalsContainer() {
    document.getElementById('terminalsContainer').innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">🗺️</div>
            <div class="empty-text">Select a client and grouping to view terminals</div>
        </div>
    `;
    selectedTerminals = [];
    updateSelectedCount();
}

// Show terminals loading state
function showTerminalsLoading() {
    document.getElementById('terminalsContainer').innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">⏳</div>
            <div class="empty-text">Loading terminals...</div>
        </div>
    `;
}

// Show empty terminals state
function showEmptyTerminals() {
    document.getElementById('terminalsContainer').innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">📍</div>
            <div class="empty-text">No terminals found for selected criteria</div>
        </div>
    `;
}

// Show terminals error state
function showTerminalsError() {
    document.getElementById('terminalsContainer').innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">⚠️</div>
            <div class="empty-text">Error loading terminals</div>
        </div>
    `;
}

// Handle form submission
function handleFormSubmit(e) {
    e.preventDefault();
    
    if (selectedTerminals.length === 0) {
        showNotification('Please select at least one terminal', 'error');
        return;
    }
    
    // Set selected terminals in hidden field
    document.getElementById('pos_terminals_json').value = JSON.stringify(selectedTerminals);
    
    const formData = new FormData(document.getElementById('deploymentTemplateForm'));
    
    showLoading(true);
    
    fetch('/deployment', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Template created successfully!', 'success');
            resetTemplateForm();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Error creating template', 'error');
        }
    })
    .catch(error => {
        console.error('Error creating template:', error);
        showNotification('Error creating template', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Reset template form
function resetTemplateForm() {
    document.getElementById('deploymentTemplateForm').reset();
    selectedTerminals = [];
    clearTerminalsContainer();
    
    // Hide all filter sections
    document.getElementById('regionSelect').style.display = 'none';
    document.getElementById('citySelect').style.display = 'none';
    document.getElementById('addressSelect').style.display = 'none';
    
    updateSelectedCount();
}

// Filter templates
function filterTemplates() {
    const regionFilter = document.getElementById('regionFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const templateItems = document.querySelectorAll('.template-item');
    
    templateItems.forEach(item => {
        const itemRegion = item.getAttribute('data-region');
        const itemStatus = item.getAttribute('data-status');
        
        let showItem = true;
        
        if (regionFilter && itemRegion !== regionFilter) {
            showItem = false;
        }
        
        if (statusFilter && itemStatus !== statusFilter) {
            showItem = false;
        }
        
        item.style.display = showItem ? 'block' : 'none';
    });
}

// View template details
function viewTemplate(templateId) {
    currentTemplateId = templateId;
    showLoading(true);
    
    fetch(`/deployment/${templateId}`, {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayTemplateDetails(data);
            document.getElementById('templateModal').style.display = 'flex';
        } else {
            showNotification(data.message || 'Error loading template', 'error');
        }
    })
    .catch(error => {
        console.error('Error loading template:', error);
        showNotification('Error loading template details', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Display template details in modal
function displayTemplateDetails(data) {
    const template = data.template;
    const terminals = data.terminals || [];
    
    document.getElementById('modalTitle').textContent = `Template: ${template.template_name}`;
    
    let terminalsHtml = '';
    if (terminals.length > 0) {
        if (data.terminals_by_city && Object.keys(data.terminals_by_city).length > 1) {
            // Group by city display
            Object.keys(data.terminals_by_city).forEach(city => {
                terminalsHtml += `
                    <div style="margin-bottom: 1rem;">
                        <h5 style="color: #374151; margin-bottom: 0.5rem;">${city} (${data.terminals_by_city[city].length} terminals)</h5>
                        <div style="padding-left: 1rem;">
                            ${data.terminals_by_city[city].map(terminal => `
                                <div style="padding: 0.5rem; border: 1px solid #e5e7eb; margin-bottom: 0.25rem; border-radius: 4px;">
                                    <strong>${terminal.terminal_id}</strong><br>
                                    <small style="color: #6b7280;">${terminal.merchant_name} • ${terminal.physical_address}</small>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            });
        } else {
            // Simple list display
            terminalsHtml = terminals.map(terminal => `
                <div style="padding: 0.5rem; border: 1px solid #e5e7eb; margin-bottom: 0.5rem; border-radius: 4px;">
                    <strong>${terminal.terminal_id}</strong><br>
                    <small style="color: #6b7280;">${terminal.merchant_name} • ${terminal.physical_address}</small>
                </div>
            `).join('');
        }
    } else {
        terminalsHtml = '<p style="color: #6b7280;">No terminals found</p>';
    }
    
    document.getElementById('modalContent').innerHTML = `
        <div style="margin-bottom: 2rem;">
            <h4 style="color: #111827; margin-bottom: 1rem;">Template Information</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div><strong>Region:</strong> ${template.region?.name || 'N/A'}</div>
                <div><strong>Service Type:</strong> ${template.service_type_display || template.service_type}</div>
                <div><strong>Priority:</strong> <span class="priority-badge priority-${template.priority}">${template.priority}</span></div>
                <div><strong>Created:</strong> ${new Date(template.created_at).toLocaleDateString()}</div>
            </div>
            ${template.description ? `<div style="margin-bottom: 1rem;"><strong>Description:</strong> ${template.description}</div>` : ''}
            ${template.notes ? `<div style="margin-bottom: 1rem;"><strong>Notes:</strong> ${template.notes}</div>` : ''}
            ${template.estimated_duration_hours ? `<div><strong>Estimated Duration:</strong> ${template.estimated_duration_hours} hours per terminal</div>` : ''}
        </div>
        <div>
            <h4 style="color: #111827; margin-bottom: 1rem;">Terminals (${terminals.length})</h4>
            <div style="max-height: 300px; overflow-y: auto;">
                ${terminalsHtml}
            </div>
        </div>
    `;
}

// Deploy template
function deployTemplate(templateId) {
    currentTemplateId = templateId;
    document.getElementById('deployModal').style.display = 'flex';
}

// Handle deploy form submission
function handleDeploySubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(document.getElementById('deployForm'));
    
    showLoading(true);
    
    fetch(`/deployment/${currentTemplateId}/deploy`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Template deployed successfully!', 'success');
            closeDeployModal();
        } else {
            showNotification(data.message || 'Error deploying template', 'error');
        }
    })
    .catch(error => {
        console.error('Error deploying template:', error);
        showNotification('Error deploying template', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Edit template (placeholder)
function editTemplate(templateId) {
    showNotification('Edit functionality coming soon', 'info');
}

// Delete template
function deleteTemplate(templateId) {
    if (!confirm('Are you sure you want to delete this template?')) {
        return;
    }
    
    showLoading(true);
    
    fetch(`/deployment/${templateId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Template deleted successfully!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Error deleting template', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting template:', error);
        showNotification('Error deleting template', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Close template modal
function closeModal() {
    document.getElementById('templateModal').style.display = 'none';
    currentTemplateId = null;
}

// Close deploy modal
function closeDeployModal() {
    document.getElementById('deployModal').style.display = 'none';
    document.getElementById('deployForm').reset();
    currentTemplateId = null;
}

// Show/hide loading overlay
function showLoading(show) {
    document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        z-index: 10000;
        max-width: 350px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        font-weight: 500;
        animation: slideIn 0.3s ease;
    `;
    
    // Set background color based on type
    const colors = {
        'success': '#10b981',
        'error': '#ef4444',
        'warning': '#f59e0b',
        'info': '#3b82f6'
    };
    
    notification.style.background = colors[type] || colors.info;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// Header action functions
function showAnalytics() {
    fetch('/deployment/analytics', {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Display analytics in a modal or new page
            showNotification('Analytics loaded successfully', 'success');
            console.log('Analytics data:', data.analytics);
        } else {
            showNotification('Error loading analytics', 'error');
        }
    })
    .catch(error => {
        console.error('Error loading analytics:', error);
        showNotification('Error loading analytics', 'error');
    });
}

function exportTemplates() {
    window.open('/deployment/export', '_blank');
}

function refreshData() {
    window.location.reload();
}

// Add CSS animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
@endsection