{{-- resources/views/reports/technician-visits.blade.php --}}
@extends('layouts.app')

@section('title', 'Technician Visit Reports')

@section('content')
<style>
.date-range-custom { display: none; }
.date-range-custom.show { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
</style>

<div>
    <!-- Header actions -->
    <div class="flex justify-end gap-2 mb-6">
        <button class="btn-secondary" onclick="refreshData()">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
        <button class="btn-secondary" onclick="exportReports()">
            <i class="fas fa-download"></i> Export
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📅</div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" id="stat-today">{{ $stats['today_visits'] ?? 0 }}</div>
                <div class="stat-label">Today's Visits</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">✅</div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" id="stat-working">{{ $stats['working_terminals'] ?? 0 }}</div>
                <div class="stat-label">Working Terminals</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">⚠️</div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" id="stat-issues">{{ $stats['issues_found'] ?? 0 }}</div>
                <div class="stat-label">Issues Found</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">🔴</div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" id="stat-not-seen">{{ $stats['not_seen'] ?? 0 }}</div>
                <div class="stat-label">Not Seen</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar mb-5">
        <form id="filterForm" class="flex flex-wrap gap-3 items-end w-full">
            <div class="flex flex-col">
                <label class="ui-label">Date Range</label>
                <select class="ui-select w-auto" id="dateRange" name="date_range">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last_7_days" selected>Last 7 Days</option>
                    <option value="last_30_days">Last 30 Days</option>
                    <option value="this_month">This Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div class="flex flex-col">
                <label class="ui-label">Technician</label>
                <select class="ui-select w-auto" id="technicianFilter" name="technician_id">
                    <option value="">All Technicians</option>
                    @foreach($technicians as $technician)
                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label class="ui-label">Region</label>
                <select class="ui-select w-auto" id="regionFilter" name="region_id">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label class="ui-label">Status</label>
                <select class="ui-select w-auto" id="statusFilter" name="terminal_status">
                    <option value="">All Status</option>
                    <option value="seen_working">Working</option>
                    <option value="seen_issues">Issues</option>
                    <option value="not_seen">Not Seen</option>
                    <option value="relocated">Relocated</option>
                    <option value="missing">Missing</option>
                </select>
            </div>
            <div class="flex flex-col">
                <label class="ui-label">Client</label>
                <select class="ui-select w-auto" id="clientFilter" name="client_id">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col flex-1 min-w-48">
                <label class="ui-label">Search</label>
                <input type="text" class="ui-input" id="searchTerm"
                       placeholder="Terminal ID, Merchant, Visit ID..." name="search">
            </div>
            <div class="flex gap-2 items-end">
                <button type="submit" class="btn-secondary">Apply</button>
                <button type="button" class="btn-secondary" onclick="resetFilters()">Reset</button>
            </div>

            <!-- Custom Date Range (Hidden by default) -->
            <div class="date-range-custom w-full" id="customDateInputs">
                <div class="flex flex-col">
                    <label class="ui-label">From Date</label>
                    <input type="date" class="ui-input" id="startDate" name="start_date">
                </div>
                <div class="flex flex-col">
                    <label class="ui-label">To Date</label>
                    <input type="date" class="ui-input" id="endDate" name="end_date">
                </div>
            </div>
        </form>
    </div>

    <!-- Data Section -->
    <div class="ui-card overflow-hidden" id="dataSection">
        <div class="ui-card-header">
            <span class="text-sm font-semibold text-gray-800">Visit Reports <span id="visitCount">(7)</span></span>
            <div class="flex items-center gap-3">
                <button class="btn-secondary btn-sm" onclick="toggleView()">
                    <i class="fas fa-th" id="viewToggleIcon"></i>
                    <span id="viewToggleText">Card View</span>
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div id="tableView" class="overflow-x-auto">
            <table class="ui-table" id="visitsTable">
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
                            <div style="font-size: 0.75rem; color: #4b5563;">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">John Mavhu</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">+263771234567</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14, 25</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">2 terminals</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">ACME Stores</div>
                            <div style="font-size: 0.75rem; color: #4b5563; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                            <span style="color: #4b5563; font-size: 0.75rem;">2h 30m</span>
                        </td>
                        <td>
                            <span class="status-badge badge-warning">1</span>
                        </td>
                        <td>
                            <a href="/visits/2" class="btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="3">
                        <td>
                            <strong>VIS-20250826-003</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14, 25</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">2 terminals</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">ACME Stores</div>
                            <div style="font-size: 0.75rem; color: #4b5563; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                            <span style="color: #4b5563; font-size: 0.75rem;">1h 45m</span>
                        </td>
                        <td>
                            <span class="status-badge badge-warning">1</span>
                        </td>
                        <td>
                            <a href="/visits/3" class="btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="4">
                        <td>
                            <strong>VIS-20250826-004</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">TM Pick n' Pay</div>
                            <div style="font-size: 0.75rem; color: #4b5563; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                            <span style="color: #4b5563; font-size: 0.75rem;">1h 15m</span>
                        </td>
                        <td>
                            <span style="color: #4b5563; font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/4" class="btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="5">
                        <td>
                            <strong>VIS-20250826-005</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">TM Pick n' Pay</div>
                            <div style="font-size: 0.75rem; color: #4b5563; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                            <span style="color: #4b5563; font-size: 0.75rem;">0h 45m</span>
                        </td>
                        <td>
                            <span style="color: #4b5563; font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/5" class="btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="6">
                        <td>
                            <strong>VIS-20250826-006</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 26</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Mike Chikwanha</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">+263785123456</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">14</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">TM Pick n' Pay</div>
                            <div style="font-size: 0.75rem; color: #4b5563; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                            <span style="color: #4b5563; font-size: 0.75rem;">1h 00m</span>
                        </td>
                        <td>
                            <span style="color: #4b5563; font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/6" class="btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="7">
                        <td>
                            <strong>VIS-20250827-007</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 27</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">88203567</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">CITY GROCERS LTD</div>
                            <div style="font-size: 0.75rem; color: #4b5563; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                            <span style="color: #4b5563; font-size: 0.75rem;">1h 20m</span>
                        </td>
                        <td>
                            <span style="color: #4b5563; font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/7" class="btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    <tr data-visit-id="8">
                        <td>
                            <strong>VIS-20250827-008</strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Aug 27</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">12:57</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">Sarah Mutendi</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">+263712345678</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">88203567</div>
                            <div style="font-size: 0.75rem; color: #4b5563;">1 terminal</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">CITY GROCERS LTD</div>
                            <div style="font-size: 0.75rem; color: #4b5563; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                            <span style="color: #4b5563; font-size: 0.75rem;">1h 10m</span>
                        </td>
                        <td>
                            <span style="color: #4b5563; font-size: 0.75rem;">None</span>
                        </td>
                        <td>
                            <a href="/visits/8" class="btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-5 py-4 border-t border-gray-100 flex justify-center">
                {{ $visits->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Card View (Initially Hidden) -->
        <div id="cardView" style="display: none;">
            <div class="ui-card-grid p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="cardContainer">
                <!-- Cards populated via JavaScript -->
            </div>
        </div>
    </div>
</div>
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
                    <div style="font-size: 0.75rem; color: #4b5563;">${new Date(visit.visit_date).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</div>
                </td>
                <td>
                    <div style="font-weight: 500;">${visit.technician.name}</div>
                    <div style="font-size: 0.75rem; color: #4b5563;">${visit.technician.phone || ''}</div>
                </td>
                <td>
                    <div style="font-weight: 500;">${visit.pos_terminal.terminal_id}</div>
                    <div style="font-size: 0.75rem; color: #4b5563;">${visit.asset_id || 'N/A'}</div>
                </td>
                <td>
                    <div style="font-weight: 500;">${visit.pos_terminal.merchant_name}</div>
                    <div style="font-size: 0.75rem; color: #4b5563; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                        '<span style="color: #4b5563; font-size: 0.75rem;">N/A</span>'}
                </td>
                <td>
                    ${issuesCount > 0 ?
                        `<span class="status-badge badge-warning">${issuesCount}</span>` :
                        '<span style="color: #4b5563; font-size: 0.75rem;">None</span>'}
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
    document.getElementById('stat-today').textContent = stats.today_visits || 0;
    document.getElementById('stat-working').textContent = stats.working_terminals || 0;
    document.getElementById('stat-issues').textContent = stats.issues_found || 0;
    document.getElementById('stat-not-seen').textContent = stats.not_seen || 0;
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
                let photosHtml = '<div class="grid grid-cols-1 md:grid-cols-2 gap-5">';
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
                <div class="ui-card-header-compact">
                    <small class="text-muted">${visit.visit_id}</small>
                    <span class="status-badge ${statusConfig.class}">${statusConfig.text}</span>
                </div>
                <div class="ui-card-body-compact">
                    <h6 style="margin-bottom: 0.5rem; font-weight: 600;">${visit.pos_terminal.terminal_id}</h6>
                    <p style="margin-bottom: 0.5rem; font-size: 0.875rem;">
                        <strong>Merchant:</strong> ${visit.pos_terminal.merchant_name}<br>
                        <strong>Technician:</strong> ${visit.technician.name}<br>
                        <strong>Date:</strong> ${new Date(visit.visit_date).toLocaleDateString()}
                    </p>
                    ${visit.technician_feedback ?
                        `<p style="margin: 0; font-size: 0.75rem; color: #4b5563;">${visit.technician_feedback.substring(0, 100)}...</p>` : ''}
                </div>
                <div class="ui-card-footer-compact">
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
