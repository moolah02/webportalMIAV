{{-- resources/views/reports/technician-visits.blade.php --}}
@extends('layouts.app')

@section('title', 'Technician Visit Reports')

@section('content')
<style>
/* Compact Dashboard Styling */
:root {
    --primary-color: #4f46e5;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
    --border-radius: 8px;
    --compact-spacing: 0.75rem;
}

.dashboard-container {
    padding: 1rem;
    background: var(--gray-50);
    min-height: 100vh;
}

/* Compact Header */
.dashboard-header {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
    display: flex;
    justify-content: between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
}

.page-subtitle {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0.25rem 0 0 0;
}

/* Compact Stats Row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    border-left: 4px solid;
}

.stat-card.primary { border-left-color: var(--primary-color); }
.stat-card.success { border-left-color: var(--success-color); }
.stat-card.warning { border-left-color: var(--warning-color); }
.stat-card.danger { border-left-color: var(--danger-color); }

.stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.stat-icon.primary { background: var(--primary-color); }
.stat-icon.success { background: var(--success-color); }
.stat-icon.warning { background: var(--warning-color); }
.stat-icon.danger { background: var(--danger-color); }

.stat-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
    line-height: 1;
}

.stat-content p {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0.25rem 0 0 0;
}

/* Compact Filter Section */
.filter-section {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1rem;
    margin-bottom: 1rem;
}

.filter-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.filter-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 0.25rem;
}

.filter-input {
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.filter-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Compact Action Buttons */
.btn-compact {
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: #4338ca;
    border-color: #4338ca;
    color: white;
}

.btn-outline {
    background: white;
    color: var(--gray-600);
    border-color: var(--gray-200);
}

.btn-outline:hover {
    background: var(--gray-50);
    color: var(--gray-700);
    border-color: var(--gray-300);
}

/* Data Section */
.data-section {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.data-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.data-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.data-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Compact Table */
.table-container {
    overflow-x: auto;
}

.compact-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.compact-table thead th {
    background: var(--gray-50);
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    color: var(--gray-700);
    border-bottom: 2px solid var(--gray-200);
    white-space: nowrap;
}

.compact-table tbody td {
    padding: 0.75rem;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}

.compact-table tbody tr:hover {
    background: var(--gray-50);
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-info { background: #e0f2fe; color: #0c4a6e; }
.badge-dark { background: #f3f4f6; color: #374151; }

/* Action Buttons */
.action-group {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
}

.action-btn {
    padding: 0.5rem;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    background: white;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
}

.action-btn:hover {
    background: var(--gray-50);
    color: var(--gray-700);
    border-color: var(--gray-300);
}

.action-btn i {
    font-size: 0.875rem;
}

/* Card Grid View */
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    padding: 1rem;
}

.visit-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: all 0.2s ease;
}

.visit-card:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.card-header-compact {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: between;
    align-items: center;
}

.card-body-compact {
    padding: 1rem;
}

.card-footer-compact {
    padding: 1rem;
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 0.5rem;
    }

    .filter-grid {
        grid-template-columns: 1fr;
    }

    .stats-row {
        grid-template-columns: 1fr;
    }

    .data-actions {
        width: 100%;
        justify-content: stretch;
    }

    .data-actions .btn-compact {
        flex: 1;
        justify-content: center;
    }
}

/* Loading States */
.loading-overlay {
    position: relative;
}

.loading-overlay.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner {
    width: 2rem;
    height: 2rem;
    border: 2px solid var(--gray-200);
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Hide custom date inputs by default */
.date-range-custom {
    display: none;
    grid-column: span 2;
}

.date-range-custom.show {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* Search with buttons group */
.search-with-buttons {
    grid-column: span 2;
}

.search-button-group {
    display: flex;
    gap: 0.5rem;
    align-items: stretch;
}

.search-input {
    flex: 1;
    min-width: 0;
}

.search-button-group .btn-compact {
    white-space: nowrap;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .search-with-buttons {
        grid-column: span 1;
    }

    .search-button-group {
        flex-direction: column;
    }
}
</style>

<div class="dashboard-container">
    <!-- Compact Header -->
    <div class="dashboard-header">
        <div>
            <h1 class="page-title">Technician Visit Reports</h1>
            <p class="page-subtitle">Real-time field visit tracking and analytics</p>
        </div>
        <div class="data-actions">
            <button class="btn-compact btn-outline" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="btn-compact btn-outline" onclick="exportReports()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Compact Stats -->
    <div class="stats-row">
        <div class="stat-card primary">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <h3>7</h3>
                <p>Total Visits</p>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>5</h3>
                <p>Working Terminals</p>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3>2</h3>
                <p>Issues Found</p>
            </div>
        </div>
        <div class="stat-card danger">
            <div class="stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h3>1</h3>
                <p>Down Terminals</p>
            </div>
        </div>
    </div>

    <!-- Compact Filters -->
    <div class="filter-section">
        <div class="filter-header">
            <h2 class="filter-title">Filters & Search</h2>
        </div>

        <form id="filterForm" class="filter-grid">
            <div class="filter-group">
                <label class="filter-label" for="dateRange">Date Range</label>
                <select class="filter-input" id="dateRange" name="date_range">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last_7_days" selected>Last 7 Days</option>
                    <option value="last_30_days">Last 30 Days</option>
                    <option value="this_month">This Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="technicianFilter">Technician</label>
                <select class="filter-input" id="technicianFilter" name="technician_id">
                    <option value="">All Technicians</option>
                    @foreach($technicians as $technician)
                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="regionFilter">Region</label>
                <select class="filter-input" id="regionFilter" name="region_id">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="statusFilter">Status</label>
                <select class="filter-input" id="statusFilter" name="terminal_status">
                    <option value="">All Status</option>
                    <option value="seen_working">Working</option>
                    <option value="seen_issues">Issues</option>
                    <option value="not_seen">Not Seen</option>
                    <option value="relocated">Relocated</option>
                    <option value="missing">Missing</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="clientFilter">Client</label>
                <select class="filter-input" id="clientFilter" name="client_id">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search with buttons group -->
            <div class="filter-group search-with-buttons">
                <label class="filter-label" for="searchTerm">Search</label>
                <div class="search-button-group">
                    <input type="text" class="filter-input search-input" id="searchTerm"
                           placeholder="Terminal ID, Merchant, Visit ID..." name="search">
                    <button type="submit" class="btn-compact btn-outline">
                        <i class="fas fa-search"></i> Apply
                    </button>
                    <button type="button" class="btn-compact btn-outline" onclick="resetFilters()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Custom Date Range (Hidden by default) -->
            <div class="date-range-custom" id="customDateInputs">
                <div class="filter-group">
                    <label class="filter-label" for="startDate">From Date</label>
                    <input type="date" class="filter-input" id="startDate" name="start_date">
                </div>
                <div class="filter-group">
                    <label class="filter-label" for="endDate">To Date</label>
                    <input type="date" class="filter-input" id="endDate" name="end_date">
                </div>
            </div>
        </form>
    </div>

    <!-- Data Section -->
    <div class="data-section loading-overlay" id="dataSection">
        <div class="data-header">
            <h2 class="data-title">Visit Reports <span id="visitCount">(7)</span></h2>
            <div class="data-actions">
                <button class="btn-compact btn-outline" onclick="toggleView()">
                    <i class="fas fa-th" id="viewToggleIcon"></i>
                    <span id="viewToggleText">Card View</span>
                </button>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
                    Showing 1 to 7 of 7
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div id="tableView" class="table-container">
            <table class="compact-table" id="visitsTable">
                <thead>
                    <tr>
                        <th>Visit ID</th>
                        <th>Date</th>
                        <th>Technician</th>
                        <th>Terminal</th>
                        <th>Merchant</th>
                        <th>Region</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Issues</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="visitsTableBody">
                    <tr data-visit-id="2">
                        <td>
                            <strong>VIS-20250826-002</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">John Mavhu</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">+263771234567</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14, 25</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">2 terminals</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">ACME Stores</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Harare Central Business District
                            </div>
                        </td>
                        <td>
                            <span class="status-badge badge-info">Central Region</span>
                        </td>
                        <td>
                            <span class="status-badge badge-warning">Issues</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">2h 30m</span>
                        </td>
                        <td>
                            <span class="status-badge badge-warning">1</span>
                        </td>
                        <td>
                            <a href="/visits/2" class="btn-compact btn-outline">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="3">
                        <td>
                            <strong>VIS-20250826-003</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14, 25</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">2 terminals</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">ACME Stores</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Westgate Shopping Centre
                            </div>
                        </td>
                        <td>
                            <span class="status-badge badge-info">West Region</span>
                        </td>
                        <td>
                            <span class="status-badge badge-warning">Issues</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">1h 45m</span>
                        </td>
                        <td>
                            <span class="status-badge badge-warning">1</span>
                        </td>
                        <td>
                            <a href="/visits/3" class="btn-compact btn-outline">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="4">
                        <td>
                            <strong>VIS-20250826-004</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">TM Pick n' Pay</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Sam Levy's Village
                            </div>
                        </td>
                        <td>
                            <span class="status-badge badge-info">North Region</span>
                        </td>
                        <td>
                            <span class="status-badge badge-success">Working</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">1h 15m</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/4" class="btn-compact btn-outline">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="5">
                        <td>
                            <strong>VIS-20250826-005</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">TM Pick n' Pay</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Eastgate Mall
                            </div>
                        </td>
                        <td>
                            <span class="status-badge badge-info">East Region</span>
                        </td>
                        <td>
                            <span class="status-badge badge-success">Working</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">0h 45m</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/5" class="btn-compact btn-outline">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="6">
                        <td>
                            <strong>VIS-20250826-006</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Mike Chikwanha</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">+263785123456</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">TM Pick n' Pay</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Borrowdale Village
                            </div>
                        </td>
                        <td>
                            <span class="status-badge badge-info">North Region</span>
                        </td>
                        <td>
                            <span class="status-badge badge-success">Working</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">1h 00m</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/6" class="btn-compact btn-outline">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="7">
                        <td>
                            <strong>VIS-20250827-007</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 27</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">88203567</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">CITY GROCERS LTD</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Avondale Shopping Centre
                            </div>
                        </td>
                        <td>
                            <span class="status-badge badge-info">Central Region</span>
                        </td>
                        <td>
                            <span class="status-badge badge-success">Working</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">1h 20m</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/7" class="btn-compact btn-outline">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="8">
                        <td>
                            <strong>VIS-20250827-008</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 27</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">88203567</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">CITY GROCERS LTD</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Highlands Shopping Centre
                            </div>
                        </td>
                        <td>
                            <span class="status-badge badge-info">South Region</span>
                        </td>
                        <td>
                            <span class="status-badge badge-success">Working</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">1h 10m</span>
                        </td>
                        <td>
                            <span style="color: var(--gray-600); font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/8" class="btn-compact btn-outline">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: center;">
                {{ $visits->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Card View (Initially Hidden) -->
        <div id="cardView" style="display: none;">
            <div class="card-grid" id="cardContainer">
                <!-- Cards populated via JavaScript -->
            </div>
        </div>
</div>

<!-- Visit Details Modal -->
<div class="modal fade" id="visitDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Visit Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="visitDetailsBody">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Photos Modal -->
<div class="modal fade" id="photosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Visit Photos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="photosBody">
                <!-- Photos loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentView = 'table';

// Handle date range selection
document.getElementById('dateRange').addEventListener('change', function() {
    const customInputs = document.getElementById('customDateInputs');
    if (this.value === 'custom') {
        customInputs.classList.add('show');
    } else {
        customInputs.classList.remove('show');
    }
});

// Handle filter form submission
document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    loadFilteredData();
});

// Load filtered data
function loadFilteredData() {
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);

    showLoading(true);

    fetch(`/reports/technician-visits/filter?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTable(data.visits);
                updateStats(data.stats);
                updateVisitCount(data.total || 0);
                if (currentView === 'card') {
                    updateCardView(data.visits);
                }
            } else {
                showAlert('Error loading filtered data', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading filtered data', 'error');
        })
        .finally(() => {
            showLoading(false);
        });
}

// Update table with new data
function updateTable(visits) {
    const tbody = document.getElementById('visitsTableBody');
    tbody.innerHTML = '';

    visits.forEach(visit => {
        const statusConfig = getStatusConfig(visit.terminal_status);
        const issuesCount = visit.issues_found ? JSON.parse(visit.issues_found).length : 0;
        const photosCount = visit.photos ? JSON.parse(visit.photos).length : 0;

        const row = `
            <tr data-visit-id="${visit.id}">
                <td>
                    <strong>${visit.visit_id}</strong>
                    ${photosCount > 0 ? '<i class="fas fa-camera text-info ms-1" title="Has photos" style="font-size: 0.75rem;"></i>' : ''}
                </td>
                <td>
                    <div style="font-weight: 500;">${new Date(visit.visit_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}</div>
                    <div style="font-size: 0.75rem; color: var(--gray-600);">${new Date(visit.visit_date).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</div>
                </td>
                <td>
                    <div style="font-weight: 500;">${visit.technician.name}</div>
                    <div style="font-size: 0.75rem; color: var(--gray-600);">${visit.technician.phone || ''}</div>
                </td>
                <td>
                    <div style="font-weight: 500;">${visit.pos_terminal.terminal_id}</div>
                    <div style="font-size: 0.75rem; color: var(--gray-600);">${visit.asset_id || 'N/A'}</div>
                </td>
                <td>
                    <div style="font-weight: 500;">${visit.pos_terminal.merchant_name}</div>
                    <div style="font-size: 0.75rem; color: var(--gray-600); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        ${visit.pos_terminal.physical_address || ''}
                    </div>
                </td>
                <td>
                    <span class="status-badge badge-info">
                        ${visit.pos_terminal.region ? visit.pos_terminal.region.name : 'N/A'}
                    </span>
                </td>
                <td>
                    <span class="status-badge ${statusConfig.class}">${statusConfig.text}</span>
                </td>
                <td>
                    ${visit.duration_minutes ?
                        `${Math.floor(visit.duration_minutes / 60)}h ${visit.duration_minutes % 60}m` :
                        '<span style="color: var(--gray-600); font-size: 0.75rem;">N/A</span>'}
                </td>
                <td>
                    ${issuesCount > 0 ?
                        `<span class="status-badge badge-warning">${issuesCount}</span>` :
                        '<span style="color: var(--gray-600); font-size: 0.75rem;">None</span>'}
                </td>
                <td>
                    <div class="action-group">
                        <button class="action-btn" onclick="viewVisitDetails(${visit.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${photosCount > 0 ?
                            `<button class="action-btn" onclick="viewPhotos(${visit.id})" title="View Photos">
                                <i class="fas fa-images"></i>
                            </button>` : ''}
                        <button class="action-btn" onclick="generateReport(${visit.id})" title="Generate Report">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Update stats cards
function updateStats(stats) {
    document.querySelector('.stat-card.primary .stat-content h3').textContent = stats.today_visits || 0;
    document.querySelector('.stat-card.success .stat-content h3').textContent = stats.working_terminals || 0;
    document.querySelector('.stat-card.warning .stat-content h3').textContent = stats.issues_found || 0;
    document.querySelector('.stat-card.danger .stat-content h3').textContent = stats.not_seen || 0;
}

// Update visit count
function updateVisitCount(count) {
    document.getElementById('visitCount').textContent = `(${count})`;
}

// Get status configuration
function getStatusConfig(status) {
    const configs = {
        'seen_working': { class: 'badge-success', text: 'Working' },
        'seen_issues': { class: 'badge-warning', text: 'Issues' },
        'not_seen': { class: 'badge-danger', text: 'Not Seen' },
        'relocated': { class: 'badge-info', text: 'Relocated' },
        'missing': { class: 'badge-dark', text: 'Missing' }
    };
    return configs[status] || { class: 'badge-dark', text: status };
}

// Toggle between table and card view
function toggleView() {
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const toggleIcon = document.getElementById('viewToggleIcon');
    const toggleText = document.getElementById('viewToggleText');

    if (currentView === 'table') {
        tableView.style.display = 'none';
        cardView.style.display = 'block';
        toggleIcon.className = 'fas fa-table';
        toggleText.textContent = 'Table View';
        currentView = 'card';
        updateCardView();
    } else {
        tableView.style.display = 'block';
        cardView.style.display = 'none';
        toggleIcon.className = 'fas fa-th';
        toggleText.textContent = 'Card View';
        currentView = 'table';
    }
}

// Show/hide loading indicator
function showLoading(show) {
    const dataSection = document.getElementById('dataSection');
    if (show) {
        dataSection.classList.add('loading');
        dataSection.insertAdjacentHTML('beforeend', '<div class="spinner"></div>');
    } else {
        dataSection.classList.remove('loading');
        const spinner = dataSection.querySelector('.spinner');
        if (spinner) spinner.remove();
    }
}

// Utility functions
function viewVisitDetails(visitId) {
    showLoading(true);

    fetch(`/reports/technician-visits/${visitId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('visitDetailsBody').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('visitDetailsModal')).show();
            } else {
                showAlert('Error loading visit details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading visit details', 'error');
        })
        .finally(() => {
            showLoading(false);
        });
}

function viewPhotos(visitId) {
    fetch(`/reports/technician-visits/${visitId}/photos`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let photosHtml = '<div class="row">';
                data.photos.forEach(photo => {
                    photosHtml += `
                        <div class="col-md-6 mb-3">
                            <img src="${photo.url}" class="img-fluid rounded" alt="Visit Photo">
                            ${photo.caption ? `<p class="mt-2"><small class="text-muted">${photo.caption}</small></p>` : ''}
                        </div>
                    `;
                });
                photosHtml += '</div>';

                document.getElementById('photosBody').innerHTML = photosHtml;
                new bootstrap.Modal(document.getElementById('photosModal')).show();
            } else {
                showAlert('Error loading photos', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading photos', 'error');
        });
}

function generateReport(visitId) {
    window.open(`/reports/technician-visits/${visitId}/pdf`, '_blank');
}

function exportReports() {
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);
    window.open(`/reports/technician-visits/export?${params}`, '_blank');
}

function updateCardView(visits) {
    const container = document.getElementById('cardContainer');
    if (!container) return;

    container.innerHTML = '';

    if (!visits || visits.length === 0) {
        container.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted">No visits found</p></div>';
        return;
    }

    visits.forEach(visit => {
        const statusConfig = getStatusConfig(visit.terminal_status);
        const photosCount = visit.photos ? JSON.parse(visit.photos).length : 0;

        const card = `
            <div class="visit-card">
                <div class="card-header-compact">
                    <small class="text-muted">${visit.visit_id}</small>
                    <span class="status-badge ${statusConfig.class}">${statusConfig.text}</span>
                </div>
                <div class="card-body-compact">
                    <h6 style="margin-bottom: 0.5rem; font-weight: 600;">${visit.pos_terminal.terminal_id}</h6>
                    <p style="margin-bottom: 0.5rem; font-size: 0.875rem;">
                        <strong>Merchant:</strong> ${visit.pos_terminal.merchant_name}<br>
                        <strong>Technician:</strong> ${visit.technician.name}<br>
                        <strong>Date:</strong> ${new Date(visit.visit_date).toLocaleDateString()}
                    </p>
                    ${visit.technician_feedback ?
                        `<p style="margin: 0; font-size: 0.75rem; color: var(--gray-600);">${visit.technician_feedback.substring(0, 100)}...</p>` : ''}
                </div>
                <div class="card-footer-compact">
                    <div class="action-group" style="justify-content: center;">
                        <button class="action-btn" onclick="viewVisitDetails(${visit.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${photosCount > 0 ?
                            `<button class="action-btn" onclick="viewPhotos(${visit.id})" title="View Photos">
                                <i class="fas fa-images"></i>
                            </button>` : ''}
                        <button class="action-btn" onclick="generateReport(${visit.id})" title="Generate Report">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', card);
    });
}

function refreshData() {
    loadFilteredData();
}

function resetFilters() {
    document.getElementById('filterForm').reset();
    document.getElementById('dateRange').value = 'last_7_days';
    document.getElementById('customDateInputs').classList.remove('show');
    loadFilteredData();
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.maxWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 7);

    document.getElementById('endDate').valueAsDate = endDate;
    document.getElementById('startDate').valueAsDate = startDate;
});
</script>
@endsection
