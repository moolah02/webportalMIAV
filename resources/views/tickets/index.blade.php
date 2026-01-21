@extends('layouts.app')

@push('styles')
<style>
    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        background: white;
        padding: 24px 32px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #2c3e50;
        color: white;
    }

    .btn-primary:hover {
        background: #34495e;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    /* Filters Section */
    .filters-section {
        background: white;
        padding: 24px 32px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        margin-bottom: 24px;
    }

    .filters-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #2c3e50;
        box-shadow: 0 0 0 2px rgba(44, 62, 80, 0.1);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        padding: 24px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border-left: 4px solid;
    }

    .stat-card.open { border-left-color: #007bff; }
    .stat-card.in-progress { border-left-color: #ffc107; }
    .stat-card.resolved { border-left-color: #28a745; }
    .stat-card.critical { border-left-color: #dc3545; }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stat-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 600;
    }

    /* Tickets Table */
    .tickets-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .section-header {
        padding: 20px 32px;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #495057;
    }

    .table-container {
        overflow-x: auto;
    }

    .tickets-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tickets-table th {
        background: #f8f9fa;
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tickets-table td {
        padding: 16px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: top;
    }

    .tickets-table tbody tr:hover {
        background: #f8f9fa;
    }

    .ticket-id {
        font-family: monospace;
        font-weight: 600;
        color: #2c3e50;
    }

    .ticket-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 4px;
    }

    .ticket-description {
        color: #6c757d;
        font-size: 13px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Status Badges */
    .status-badge, .priority-badge, .issue-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    /* Status Colors */
    .status-open { background: #cfe2ff; color: #004085; }
    .status-in-progress { background: #fff3cd; color: #664d03; }
    .status-resolved { background: #d1edff; color: #0c5460; }
    .status-closed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    /* Priority Colors */
    .priority-critical { background: #f8d7da; color: #721c24; }
    .priority-high { background: #fff3cd; color: #664d03; }
    .priority-medium { background: #cfe2ff; color: #004085; }
    .priority-low { background: #d4edda; color: #155724; }

    /* Issue Type Colors */
    .issue-hardware_malfunction { background: #f8d7da; color: #721c24; }
    .issue-software_issue { background: #cfe2ff; color: #004085; }
    .issue-network_connectivity { background: #fff3cd; color: #664d03; }
    .issue-user_training { background: #d1ecf1; color: #0c5460; }
    .issue-maintenance_required { background: #ffeaa7; color: #6c5ce7; }
    .issue-replacement_needed { background: #fab1a0; color: #e17055; }
    .issue-other { background: #e9ecef; color: #495057; }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    .modal-header {
        padding: 24px 32px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #6c757d;
        cursor: pointer;
        padding: 4px;
    }

    .modal-body {
        padding: 32px;
    }

    .modal-footer {
        padding: 24px 32px;
        border-top: 1px solid #dee2e6;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group-full {
        grid-column: 1 / -1;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 16px;
        }

        .page-header {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }

        .filters-row {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .tickets-table {
            font-size: 14px;
        }

        .tickets-table th,
        .tickets-table td {
            padding: 12px 8px;
        }
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .ticket-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .technician-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .technician-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #2c3e50;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">🎫 Support Tickets</h1>
        <button class="btn btn-primary" onclick="openCreateTicketModal()">
            ➕ Create New Ticket
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card open">
            <div class="stat-number" id="openTickets">{{ $stats['open'] ?? 0 }}</div>
            <div class="stat-label">Open Tickets</div>
        </div>
        <div class="stat-card in-progress">
            <div class="stat-number" id="inProgressTickets">{{ $stats['in_progress'] ?? 0 }}</div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-card resolved">
            <div class="stat-number" id="resolvedTickets">{{ $stats['resolved'] ?? 0 }}</div>
            <div class="stat-label">Resolved This Month</div>
        </div>
        <div class="stat-card critical">
            <div class="stat-number" id="criticalTickets">{{ $stats['critical'] ?? 0 }}</div>
            <div class="stat-label">Critical Priority</div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-row">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-control" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Priority</label>
                <select class="form-control" id="priorityFilter">
                    <option value="">All Priorities</option>
                    <option value="critical">Critical</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Issue Type</label>
                <select class="form-control" id="issueTypeFilter">
                    <option value="">All Types</option>
                    <option value="hardware_malfunction">Hardware Malfunction</option>
                    <option value="software_issue">Software Issue</option>
                    <option value="network_connectivity">Network Connectivity</option>
                    <option value="user_training">User Training</option>
                    <option value="maintenance_required">Maintenance Required</option>
                    <option value="replacement_needed">Replacement Needed</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" id="searchInput" placeholder="Search tickets...">
            </div>
            <div class="form-group">
                <button class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="tickets-section">
        <div class="section-header">
            <h2 class="section-title">All Tickets</h2>
        </div>
        <div class="table-container">
            <table class="tickets-table" id="ticketsTable">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Title & Description</th>
                        <th>Issue Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Technician</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ticketsTableBody">
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>
                                <span class="ticket-id">{{ $ticket->ticket_id }}</span>
                            </td>
                            <td>
                                <div class="ticket-title">{{ $ticket->title }}</div>
                                <div class="ticket-description">{{ Str::limit($ticket->description, 100) }}</div>
                                @if($ticket->posTerminal)
                                    <div class="ticket-meta">Terminal: {{ $ticket->posTerminal->terminal_id }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="issue-badge issue-{{ $ticket->issue_type }}">
                                    {{ ucwords(str_replace('_', ' ', $ticket->issue_type)) }}
                                </span>
                            </td>
                            <td>
                                <span class="priority-badge priority-{{ $ticket->priority }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ str_replace('_', '-', $ticket->status) }}">
                                    {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($ticket->assignedTo)
                                    <div class="technician-info">
                                        <div class="technician-avatar">{{ substr($ticket->assignedTo->first_name, 0, 1) }}</div>
                                        <span>{{ $ticket->assignedTo->first_name }} {{ $ticket->assignedTo->last_name }}</span>
                                    </div>
                                @else
                                    <span style="color: #6c757d;">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $ticket->created_at->format('M j, Y') }}</div>
                                <div class="ticket-meta">{{ $ticket->created_at->format('g:i A') }}</div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-secondary" onclick="viewTicketDetails({{ $ticket->id }})">
                                        👁️ View
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editTicketFromTable({{ $ticket->id }})">
                                        ✏️ Edit
                                    </button>
                                    @if($ticket->status === 'open')
                                        <button class="btn btn-sm btn-success" onclick="updateTicketStatus({{ $ticket->id }}, 'resolved')">
                                            ✅ Resolve
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div class="empty-state-icon">🎫</div>
                                <div>No tickets found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Ticket Modal -->
<div id="ticketModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="ticketModalTitle">Create New Ticket</h3>
            <button class="modal-close" onclick="closeTicketModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="ticketForm">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" id="ticketTitle" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority *</label>
                        <select class="form-control" id="ticketPriority" required>
                            <option value="">Select Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ticket Type *</label>
                        <select class="form-control" id="ticketType" required>
                            <option value="">Select Type</option>
                            <option value="pos_terminal">POS Terminal</option>
                            <option value="internal">Internal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Assignment Type *</label>
                        <select class="form-control" id="ticketAssignmentType" required>
                            <option value="">Select Assignment</option>
                            <option value="public">Public (Any Employee)</option>
                            <option value="direct">Direct (Specific Employee)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Issue Type *</label>
                        <select class="form-control" id="ticketIssueType" required>
                            <option value="">Select Issue Type</option>
                            <option value="hardware_malfunction">Hardware Malfunction</option>
                            <option value="software_issue">Software Issue</option>
                            <option value="network_connectivity">Network Connectivity</option>
                            <option value="user_training">User Training</option>
                            <option value="maintenance_required">Maintenance Required</option>
                            <option value="replacement_needed">Replacement Needed</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group" id="posTerminalField">
                        <label class="form-label">POS Terminal *</label>
                        <select class="form-control" id="ticketPosTerminal" required>
                            <option value="">Select Terminal</option>
                            @foreach($posTerminals as $terminal)
                                <option value="{{ $terminal->id }}">{{ $terminal->terminal_id }} - {{ $terminal->merchant_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="assignedToField" style="display: none;">
                        <label class="form-label">Assigned To *</label>
                        <select class="form-control" id="ticketAssignedTo" required>
                            <option value="">Select Employee</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->first_name }} {{ $technician->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Est. Resolution Time (minutes)</label>
                        <input type="number" class="form-control" id="ticketEstimatedTime" min="0">
                    </div>
                </div>
                <div class="form-group form-group-full">
                    <label class="form-label">Description *</label>
                    <textarea class="form-control" id="ticketDescription" required placeholder="Please provide a detailed description of the issue..."></textarea>
                </div>
                <div class="form-group form-group-full" id="resolutionGroup" style="display: none;">
                    <label class="form-label">Resolution</label>
                    <textarea class="form-control" id="ticketResolution" placeholder="Describe how the issue was resolved..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeTicketModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveTicket()">Save Ticket</button>
        </div>
    </div>
</div>

<!-- View Ticket Details Modal -->
<div id="ticketDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="ticketDetailsTitle">Ticket Details</h3>
            <button class="modal-close" onclick="closeTicketDetailsModal()">&times;</button>
        </div>
        <div class="modal-body" id="ticketDetailsBody">
            <!-- Dynamic content will be inserted here -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeTicketDetailsModal()">Close</button>
            <button type="button" class="btn btn-primary" id="editTicketBtn" onclick="editTicket()">Edit Ticket</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pass Laravel data to JavaScript - simplified approach
    const ticketsData = @json($tickets);

    const routes = {
        store: '{{ route("tickets.store") }}',
        update: (id) => `{{ route("tickets.update", ":id") }}`.replace(':id', id),
        show: (id) => `{{ route("tickets.show", ":id") }}`.replace(':id', id),
        updateStatus: (id) => `{{ route("tickets.updateStatus", ":id") }}`.replace(':id', id)
    };

    let currentEditingTicket = null;

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
    });

    function setupEventListeners() {
        // Filter event listeners
        document.getElementById('statusFilter').addEventListener('change', filterTickets);
        document.getElementById('priorityFilter').addEventListener('change', filterTickets);
        document.getElementById('issueTypeFilter').addEventListener('change', filterTickets);
        document.getElementById('searchInput').addEventListener('input', filterTickets);

        // Ticket form field visibility
        document.getElementById('ticketType')?.addEventListener('change', updateFormFields);
        document.getElementById('ticketAssignmentType')?.addEventListener('change', updateFormFields);

        // Modal close on outside click
        window.addEventListener('click', function(event) {
            const ticketModal = document.getElementById('ticketModal');
            const detailsModal = document.getElementById('ticketDetailsModal');
            if (event.target === ticketModal) {
                closeTicketModal();
            }
            if (event.target === detailsModal) {
                closeTicketDetailsModal();
            }
        });
    }

    function updateFormFields() {
        const ticketType = document.getElementById('ticketType')?.value;
        const assignmentType = document.getElementById('ticketAssignmentType')?.value;

        // Show/hide POS Terminal field based on ticket type
        const posTerminalField = document.getElementById('posTerminalField');
        if (posTerminalField) {
            if (ticketType === 'pos_terminal') {
                posTerminalField.style.display = 'block';
                document.getElementById('ticketPosTerminal').required = true;
            } else {
                posTerminalField.style.display = 'none';
                document.getElementById('ticketPosTerminal').required = false;
                document.getElementById('ticketPosTerminal').value = '';
            }
        }

        // Show/hide Assigned To field based on assignment type
        const assignedToField = document.getElementById('assignedToField');
        if (assignedToField) {
            if (assignmentType === 'direct') {
                assignedToField.style.display = 'block';
                document.getElementById('ticketAssignedTo').required = true;
            } else {
                assignedToField.style.display = 'none';
                document.getElementById('ticketAssignedTo').required = false;
                document.getElementById('ticketAssignedTo').value = '';
            }
        }
    }

    function filterTickets() {
        const statusFilter = document.getElementById('statusFilter').value;
        const priorityFilter = document.getElementById('priorityFilter').value;
        const issueTypeFilter = document.getElementById('issueTypeFilter').value;
        const searchInput = document.getElementById('searchInput').value.toLowerCase();

        const rows = document.querySelectorAll('#ticketsTableBody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 8) return; // Skip empty state row

            const ticketId = cells[0].textContent.trim();
            const title = cells[1].querySelector('.ticket-title')?.textContent || '';
            const description = cells[1].querySelector('.ticket-description')?.textContent || '';
            const issueType = cells[2].querySelector('.issue-badge')?.className.includes(`issue-${issueTypeFilter}`) || !issueTypeFilter;
            const priority = cells[3].querySelector('.priority-badge')?.className.includes(`priority-${priorityFilter}`) || !priorityFilter;
            const status = cells[4].querySelector('.status-badge')?.className.includes(`status-${statusFilter.replace('_', '-')}`) || !statusFilter;

            const matchesSearch = !searchInput ||
                title.toLowerCase().includes(searchInput) ||
                description.toLowerCase().includes(searchInput) ||
                ticketId.toLowerCase().includes(searchInput);

            const shouldShow = issueType && priority && status && matchesSearch;
            row.style.display = shouldShow ? '' : 'none';
        });
    }

    function clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('priorityFilter').value = '';
        document.getElementById('issueTypeFilter').value = '';
        document.getElementById('searchInput').value = '';

        // Show all rows
        document.querySelectorAll('#ticketsTableBody tr').forEach(row => {
            row.style.display = '';
        });
    }

    function openCreateTicketModal() {
        currentEditingTicket = null;
        document.getElementById('ticketModalTitle').textContent = 'Create New Ticket';
        document.getElementById('ticketForm').reset();
        document.getElementById('resolutionGroup').style.display = 'none';
        updateFormFields(); // Update field visibility based on form defaults
        document.getElementById('ticketModal').classList.add('show');
    }

    function closeTicketModal() {
        document.getElementById('ticketModal').classList.remove('show');
    }

    function editTicketFromTable(ticketId) {
        const ticket = ticketsData.find(t => t.id === ticketId);
        if (!ticket) return;

        currentEditingTicket = ticket;
        document.getElementById('ticketModalTitle').textContent = 'Edit Ticket';

        // Populate form
        document.getElementById('ticketTitle').value = ticket.title;
        document.getElementById('ticketPriority').value = ticket.priority;
        document.getElementById('ticketType').value = ticket.ticket_type || 'pos_terminal';
        document.getElementById('ticketAssignmentType').value = ticket.assignment_type || 'public';
        document.getElementById('ticketIssueType').value = ticket.issue_type;
        document.getElementById('ticketPosTerminal').value = ticket.pos_terminal_id || '';
        document.getElementById('ticketAssignedTo').value = ticket.assigned_to || '';
        document.getElementById('ticketEstimatedTime').value = ticket.estimated_resolution_time || '';
        document.getElementById('ticketDescription').value = ticket.description;
        document.getElementById('ticketResolution').value = ticket.resolution || '';

        // Update form fields visibility based on values
        updateFormFields();

        // Show resolution field if ticket is resolved
        if (ticket.status === 'resolved' || ticket.status === 'closed') {
            document.getElementById('resolutionGroup').style.display = 'block';
        }

        document.getElementById('ticketModal').classList.add('show');
    }

    async function saveTicket() {
        const form = document.getElementById('ticketForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = {
            title: document.getElementById('ticketTitle').value,
            priority: document.getElementById('ticketPriority').value,
            ticket_type: document.getElementById('ticketType').value,
            assignment_type: document.getElementById('ticketAssignmentType').value,
            issue_type: document.getElementById('ticketIssueType').value,
            pos_terminal_id: document.getElementById('ticketPosTerminal').value || null,
            assigned_to: document.getElementById('ticketAssignedTo').value || null,
            estimated_resolution_time: document.getElementById('ticketEstimatedTime').value || null,
            description: document.getElementById('ticketDescription').value,
            resolution: document.getElementById('ticketResolution').value || null
        };

        try {
            const url = currentEditingTicket ? routes.update(currentEditingTicket.id) : routes.store;
            const method = currentEditingTicket ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                closeTicketModal();
                window.location.reload(); // Refresh page to show updated data
            } else {
                const errorData = await response.json();
                alert('Error saving ticket: ' + (errorData.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error saving ticket. Please try again.');
        }
    }

    async function updateTicketStatus(ticketId, newStatus) {
        try {
            const response = await fetch(routes.updateStatus(ticketId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            });

            if (response.ok) {
                window.location.reload(); // Refresh page to show updated data
            } else {
                alert('Error updating ticket status');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error updating ticket status. Please try again.');
        }
    }

    function viewTicketDetails(ticketId) {
        const ticket = ticketsData.find(t => t.id === ticketId);
        if (!ticket) return;

        document.getElementById('ticketDetailsTitle').textContent = `Ticket ${ticket.ticket_id}`;

        const detailsBody = document.getElementById('ticketDetailsBody');
        detailsBody.innerHTML = `
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div>
                    <strong>Title:</strong><br>
                    ${ticket.title}
                </div>
                <div>
                    <strong>Status:</strong><br>
                    <span class="status-badge status-${ticket.status.replace('_', '-')}">${ticket.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                </div>
                <div>
                    <strong>Priority:</strong><br>
                    <span class="priority-badge priority-${ticket.priority}">${ticket.priority}</span>
                </div>
                <div>
                    <strong>Issue Type:</strong><br>
                    <span class="issue-badge issue-${ticket.issue_type}">${ticket.issue_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                </div>
                <div>
                    <strong>Assigned To:</strong><br>
                    ${ticket.assigned_to ? (ticket.assigned_to.first_name + ' ' + ticket.assigned_to.last_name) : 'Unassigned'}
                </div>
                <div>
                    <strong>Created:</strong><br>
                    ${new Date(ticket.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })} ${new Date(ticket.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}
                </div>
                ${ticket.pos_terminal ? `
                    <div>
                        <strong>POS Terminal:</strong><br>
                        ${ticket.pos_terminal.terminal_id}
                    </div>
                ` : ''}
                ${ticket.estimated_resolution_time ? `
                    <div>
                        <strong>Est. Resolution:</strong><br>
                        ${ticket.estimated_resolution_time} minutes
                    </div>
                ` : ''}
            </div>
            <div style="margin-bottom: 20px;">
                <strong>Description:</strong><br>
                <div style="background: #f8f9fa; padding: 16px; border-radius: 6px; margin-top: 8px;">
                    ${ticket.description}
                </div>
            </div>
            ${ticket.resolution ? `
                <div>
                    <strong>Resolution:</strong><br>
                    <div style="background: #d4edda; padding: 16px; border-radius: 6px; margin-top: 8px; border-left: 4px solid #28a745;">
                        ${ticket.resolution}
                    </div>
                </div>
            ` : ''}
        `;

        document.getElementById('editTicketBtn').onclick = () => {
            closeTicketDetailsModal();
            editTicketFromTable(ticketId);
        };

        document.getElementById('ticketDetailsModal').classList.add('show');
    }

    function closeTicketDetailsModal() {
        document.getElementById('ticketDetailsModal').classList.remove('show');
    }
</script>
@endpush
