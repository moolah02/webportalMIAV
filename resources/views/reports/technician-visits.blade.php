{{-- resources/views/reports/technician-visits.blade.php --}}
@extends('layouts.app')

@section('title', 'Technician Visit Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Technician Visit Reports</h1>
                <p class="page-subtitle">View and manage field visit data submitted by technicians</p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['today_visits'] }}</h3>
                            <p class="mb-0">Today's Visits</p>
                        </div>
                        <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['working_terminals'] }}</h3>
                            <p class="mb-0">Terminals Working</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['issues_found'] }}</h3>
                            <p class="mb-0">Issues Found</p>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['not_seen'] }}</h3>
                            <p class="mb-0">Not Seen</p>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Filters & Search</h3>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="dateRange" class="form-label">Date Range</label>
                    <select class="form-select" id="dateRange" name="date_range">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="last_7_days" selected>Last 7 Days</option>
                        <option value="last_30_days">Last 30 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-2" id="customDateRange" style="display: none;">
                    <label for="startDate" class="form-label">From</label>
                    <input type="date" class="form-control" id="startDate" name="start_date">
                </div>
                <div class="col-md-2" id="customDateRange2" style="display: none;">
                    <label for="endDate" class="form-label">To</label>
                    <input type="date" class="form-control" id="endDate" name="end_date">
                </div>
                <div class="col-md-3">
                    <label for="technicianFilter" class="form-label">Technician</label>
                    <select class="form-select" id="technicianFilter" name="technician_id">
                        <option value="">All Technicians</option>
                        @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="regionFilter" class="form-label">Region</label>
                    <select class="form-select" id="regionFilter" name="region_id">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Terminal Status</label>
                    <select class="form-select" id="statusFilter" name="terminal_status">
                        <option value="">All Status</option>
                        <option value="seen_working">Seen - Working</option>
                        <option value="seen_issues">Seen - Issues</option>
                        <option value="not_seen">Not Seen</option>
                        <option value="relocated">Relocated</option>
                        <option value="missing">Missing</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="clientFilter" class="form-label">Client</label>
                    <select class="form-select" id="clientFilter" name="client_id">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchTerm" class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchTerm" 
                           placeholder="Search by Terminal ID, Merchant name, or Visit ID..." name="search">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Visit Reports Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Visit Reports</h3>
            <div class="btn-group">
                <button class="btn btn-outline-primary" onclick="exportReports()">
                    <i class="fas fa-download"></i> Export
                </button>
                <button class="btn btn-outline-info" onclick="toggleView()">
                    <i class="fas fa-th" id="viewToggleIcon"></i> <span id="viewToggleText">Card View</span>
                </button>
                <button class="btn btn-outline-success" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading visit reports...</p>
            </div>

            <!-- Table View -->
            <div id="tableView">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="visitsTable">
                        <thead>
                            <tr>
                                <th>Visit ID</th>
                                <th>Date & Time</th>
                                <th>Technician</th>
                                <th>Terminal ID</th>
                                <th>Merchant</th>
                                <th>Region</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Issues</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="visitsTableBody">
                            @foreach($visits as $visit)
                            <tr data-visit-id="{{ $visit->id }}">
                                <td>
                                    <strong>{{ $visit->visit_id }}</strong>
                                    @if($visit->photos && count(json_decode($visit->photos)) > 0)
                                        <i class="fas fa-camera text-info ms-1" title="Has photos"></i>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ date('M j, Y', strtotime($visit->visit_date)) }}</div>
                                    <small class="text-muted">{{ date('H:i', strtotime($visit->visit_date)) }}</small>
                                </td>
                                <td>
                                    <div>{{ $visit->technician->name }}</div>
                                    <small class="text-muted">{{ $visit->technician->phone }}</small>
                                </td>
                                <td>
                                    <strong>{{ $visit->posTerminal->terminal_id }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $visit->asset_id ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div>{{ $visit->posTerminal->merchant_name }}</div>
                                    <small class="text-muted">{{ $visit->posTerminal->physical_address }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $visit->posTerminal->region->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'seen_working' => ['class' => 'bg-success', 'text' => 'Working'],
                                            'seen_issues' => ['class' => 'bg-warning', 'text' => 'Issues Found'],
                                            'not_seen' => ['class' => 'bg-danger', 'text' => 'Not Seen'],
                                            'relocated' => ['class' => 'bg-info', 'text' => 'Relocated'],
                                            'missing' => ['class' => 'bg-dark', 'text' => 'Missing']
                                        ];
                                        $config = $statusConfig[$visit->terminal_status] ?? ['class' => 'bg-secondary', 'text' => ucfirst($visit->terminal_status)];
                                    @endphp
                                    <span class="badge {{ $config['class'] }}">{{ $config['text'] }}</span>
                                </td>
                                <td>
                                    @if($visit->duration_minutes)
                                        {{ floor($visit->duration_minutes / 60) }}h {{ $visit->duration_minutes % 60 }}m
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($visit->issues_found && count(json_decode($visit->issues_found)) > 0)
                                        <span class="badge bg-warning">{{ count(json_decode($visit->issues_found)) }} issues</span>
                                    @else
                                        <span class="text-muted">None</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" 
                                                onclick="viewVisitDetails({{ $visit->id }})" 
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($visit->photos && count(json_decode($visit->photos)) > 0)
                                        <button class="btn btn-outline-info" 
                                                onclick="viewPhotos({{ $visit->id }})" 
                                                title="View Photos">
                                            <i class="fas fa-images"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-outline-success" 
                                                onclick="generateReport({{ $visit->id }})" 
                                                title="Generate Report">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $visits->firstItem() ?? 0 }} to {{ $visits->lastItem() ?? 0 }} 
                        of {{ $visits->total() ?? 0 }} visits
                    </div>
                    {{ $visits->appends(request()->query())->links() }}
                </div>
            </div>

            <!-- Card View (Initially Hidden) -->
            <div id="cardView" style="display: none;">
                <div class="row" id="cardContainer">
                    <!-- Cards will be populated via JavaScript -->
                </div>
            </div>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printVisitDetails()">
                    <i class="fas fa-print"></i> Print
                </button>
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
    const customRangeElements = document.querySelectorAll('#customDateRange, #customDateRange2');
    
    if (this.value === 'custom') {
        customRangeElements.forEach(el => el.style.display = 'block');
    } else {
        customRangeElements.forEach(el => el.style.display = 'none');
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
                    ${photosCount > 0 ? '<i class="fas fa-camera text-info ms-1" title="Has photos"></i>' : ''}
                </td>
                <td>
                    <div>${new Date(visit.visit_date).toLocaleDateString()}</div>
                    <small class="text-muted">${new Date(visit.visit_date).toLocaleTimeString()}</small>
                </td>
                <td>
                    <div>${visit.technician.name}</div>
                    <small class="text-muted">${visit.technician.phone || ''}</small>
                </td>
                <td>
                    <strong>${visit.pos_terminal.terminal_id}</strong>
                    <br>
                    <small class="text-muted">${visit.asset_id || 'N/A'}</small>
                </td>
                <td>
                    <div>${visit.pos_terminal.merchant_name}</div>
                    <small class="text-muted">${visit.pos_terminal.physical_address || ''}</small>
                </td>
                <td>
                    <span class="badge bg-info">
                        ${visit.pos_terminal.region ? visit.pos_terminal.region.name : 'N/A'}
                    </span>
                </td>
                <td>
                    <span class="badge ${statusConfig.class}">${statusConfig.text}</span>
                </td>
                <td>
                    ${visit.duration_minutes ? 
                        `${Math.floor(visit.duration_minutes / 60)}h ${visit.duration_minutes % 60}m` : 
                        '<span class="text-muted">N/A</span>'}
                </td>
                <td>
                    ${issuesCount > 0 ? 
                        `<span class="badge bg-warning">${issuesCount} issues</span>` : 
                        '<span class="text-muted">None</span>'}
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" 
                                onclick="viewVisitDetails(${visit.id})" 
                                title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${photosCount > 0 ? 
                            `<button class="btn btn-outline-info" 
                                    onclick="viewPhotos(${visit.id})" 
                                    title="View Photos">
                                <i class="fas fa-images"></i>
                            </button>` : ''}
                        <button class="btn btn-outline-success" 
                                onclick="generateReport(${visit.id})" 
                                title="Generate Report">
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
    document.querySelector('.bg-primary .h3').textContent = stats.today_visits || 0;
    document.querySelector('.bg-success .h3').textContent = stats.working_terminals || 0;
    document.querySelector('.bg-warning .h3').textContent = stats.issues_found || 0;
    document.querySelector('.bg-danger .h3').textContent = stats.not_seen || 0;
}

// Get status configuration
function getStatusConfig(status) {
    const configs = {
        'seen_working': { class: 'bg-success', text: 'Working' },
        'seen_issues': { class: 'bg-warning', text: 'Issues Found' },
        'not_seen': { class: 'bg-danger', text: 'Not Seen' },
        'relocated': { class: 'bg-info', text: 'Relocated' },
        'missing': { class: 'bg-dark', text: 'Missing' }
    };
    return configs[status] || { class: 'bg-secondary', text: status };
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

// Update card view
function updateCardView(visits = null) {
    if (!visits) {
        // Get visits from table data
        visits = Array.from(document.querySelectorAll('#visitsTableBody tr')).map(row => {
            return {
                id: row.dataset.visitId,
                // Extract data from table cells - simplified for demo
            };
        });
    }
    
    const container = document.getElementById('cardContainer');
    container.innerHTML = '';
    
    visits.forEach(visit => {
        const statusConfig = getStatusConfig(visit.terminal_status);
        const card = `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <small class="text-muted">${visit.visit_id}</small>
                        <span class="badge ${statusConfig.class}">${statusConfig.text}</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">${visit.pos_terminal.terminal_id}</h6>
                        <p class="card-text">
                            <strong>Merchant:</strong> ${visit.pos_terminal.merchant_name}<br>
                            <strong>Technician:</strong> ${visit.technician.name}<br>
                            <strong>Date:</strong> ${new Date(visit.visit_date).toLocaleDateString()}
                        </p>
                        ${visit.technician_feedback ? 
                            `<p class="card-text"><small class="text-muted">${visit.technician_feedback.substring(0, 100)}...</small></p>` : ''}
                    </div>
                    <div class="card-footer">
                        <div class="btn-group btn-group-sm w-100">
                            <button class="btn btn-outline-primary" onclick="viewVisitDetails(${visit.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-outline-success" onclick="generateReport(${visit.id})">
                                <i class="fas fa-file-pdf"></i> Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', card);
    });
}

// View visit details
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

// View photos
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

// Generate report
function generateReport(visitId) {
    window.open(`/reports/technician-visits/${visitId}/pdf`, '_blank');
}

// Export reports
function exportReports() {
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);
    window.open(`/reports/technician-visits/export?${params}`, '_blank');
}

// Reset filters
function resetFilters() {
    document.getElementById('filterForm').reset();
    document.getElementById('dateRange').value = 'last_7_days';
    document.querySelectorAll('#customDateRange, #customDateRange2').forEach(el => {
        el.style.display = 'none';
    });
    loadFilteredData();
}

// Refresh data
function refreshData() {
    loadFilteredData();
}

// Print visit details
function printVisitDetails() {
    const content = document.getElementById('visitDetailsBody').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Visit Details</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                ${content}
                <script>window.print();</script>
            </body>
        </html>
    `);
}

// Show/hide loading indicator
function showLoading(show) {
    const indicator = document.getElementById('loadingIndicator');
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    
    if (show) {
        indicator.style.display = 'block';
        tableView.style.display = 'none';
        cardView.style.display = 'none';
    } else {
        indicator.style.display = 'none';
        if (currentView === 'table') {
            tableView.style.display = 'block';
        } else {
            cardView.style.display = 'block';
        }
    }
}

// Utility function
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set default date range
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 7);
    
    document.getElementById('endDate').valueAsDate = endDate;
    document.getElementById('startDate').valueAsDate = startDate;
});
</script>
@endsection