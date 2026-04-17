@extends('layouts.app')
@section('title', 'Support Tickets')

@push('styles')
<style>
    /* &#x2500;&#x2500; Modals &#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500; */
    .modal { display: none; position: fixed; z-index: 1000; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
    .modal.show { display: flex; align-items: center; justify-content: center; }
    .modal-content { background: white; border-radius: 12px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
    .modal-title { font-size: 17px; font-weight: 700; color: #111827; }
    .modal-close { background: none; border: none; font-size: 22px; color: #6b7280; cursor: pointer; line-height: 1; padding: 0; }
    .modal-body { padding: 24px; }
    .modal-footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 16px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group-full { grid-column: 1 / -1; }
    textarea.ui-input { min-height: 90px; resize: vertical; }

    /* &#x2500;&#x2500; Badges &#x2014; referenced by JS-generated HTML &#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500;&#x2500; */
    .status-badge, .priority-badge, .issue-badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
    .status-open { background: #dbeafe; color: #1e40af; }
    .status-in-progress { background: #fef3c7; color: #92400e; }
    .status-resolved { background: #d1fae5; color: #065f46; }
    .status-closed { background: #dcfce7; color: #166534; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .priority-critical { background: #fee2e2; color: #991b1b; }
    .priority-high { background: #fef3c7; color: #92400e; }
    .priority-medium { background: #dbeafe; color: #1e40af; }
    .priority-low { background: #d1fae5; color: #065f46; }
    .issue-hardware_malfunction { background: #fee2e2; color: #991b1b; }
    .issue-software_issue { background: #dbeafe; color: #1e40af; }
    .issue-network_connectivity { background: #fef3c7; color: #92400e; }
    .issue-user_training { background: #cffafe; color: #164e63; }
    .issue-maintenance_required { background: #fef9c3; color: #713f12; }
    .issue-replacement_needed { background: #ffe4e6; color: #9f1239; }
    .issue-other { background: #f3f4f6; color: #374151; }

    /* &#x2500;&#x2500; Table cell helpers &#x2014; referenced by JS filter queries &#x2500;&#x2500; */
    .ticket-id { font-family: monospace; font-weight: 600; color: #1a3a5c; font-size: 13px; }
    .ticket-title { font-weight: 600; color: #111827; font-size: 14px; margin-bottom: 2px; }
    .ticket-description { color: #6b7280; font-size: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .ticket-meta { font-size: 11px; color: #9ca3af; margin-top: 2px; }
    .technician-avatar { width: 24px; height: 24px; border-radius: 50%; background: #1a3a5c; color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; flex-shrink: 0; }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title">&#x1F3AB; Support Tickets</h1>
        <p class="page-subtitle">Manage and track all support requests</p>
    </div>
    <button class="btn-primary" onclick="openCreateTicketModal()">&#x2795; New Ticket</button>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card border-l-4 border-blue-500">
        <div>
            <div class="stat-number text-blue-600" id="openTickets">{{ $stats['open'] ?? 0 }}</div>
            <div class="stat-label">Open Tickets</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-yellow-400">
        <div>
            <div class="stat-number text-yellow-600" id="inProgressTickets">{{ $stats['in_progress'] ?? 0 }}</div>
            <div class="stat-label">In Progress</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-green-500">
        <div>
            <div class="stat-number text-green-600" id="resolvedTickets">{{ $stats['resolved'] ?? 0 }}</div>
            <div class="stat-label">Resolved This Month</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-red-500">
        <div>
            <div class="stat-number text-red-600" id="criticalTickets">{{ $stats['critical'] ?? 0 }}</div>
            <div class="stat-label">Critical Priority</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="filter-bar">
    <div class="flex-1 min-w-[140px]">
        <label class="ui-label">Status</label>
        <select class="ui-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="open">Open</option>
            <option value="in_progress">In Progress</option>
            <option value="resolved">Resolved</option>
            <option value="closed">Closed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
    <div class="flex-1 min-w-[140px]">
        <label class="ui-label">Priority</label>
        <select class="ui-select" id="priorityFilter">
            <option value="">All Priorities</option>
            <option value="critical">Critical</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
        </select>
    </div>
    <div class="flex-1 min-w-[160px]">
        <label class="ui-label">Issue Type</label>
        <select class="ui-select" id="issueTypeFilter">
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
    <div class="flex-1 min-w-[160px]">
        <label class="ui-label">Search</label>
        <input type="text" class="ui-input" id="searchInput" placeholder="Search tickets...">
    </div>
    <div class="flex items-end">
        <button class="btn-secondary" onclick="clearFilters()">Clear</button>
    </div>
</div>

{{-- Tickets table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800 text-sm">All Tickets</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table" id="ticketsTable">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Title &amp; Description</th>
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
                    <td><span class="ticket-id">{{ $ticket->ticket_id }}</span></td>
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
                            <div class="flex items-center gap-2">
                                <span class="technician-avatar">{{ substr($ticket->assignedTo->first_name, 0, 1) }}</span>
                                <span class="text-sm text-gray-700">{{ $ticket->assignedTo->first_name }} {{ $ticket->assignedTo->last_name }}</span>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        <div class="text-sm text-gray-700">{{ $ticket->created_at->format('M j, Y') }}</div>
                        <div class="ticket-meta">{{ $ticket->created_at->format('g:i A') }}</div>
                    </td>
                    <td>
                        <div class="flex gap-1.5 flex-wrap">
                            <button class="btn-secondary btn-sm" onclick="viewTicketDetails({{ $ticket->id }})">ðŸ‘ View</button>
                            <button class="btn-primary btn-sm" onclick="editTicketFromTable({{ $ticket->id }})">âœï¸ Edit</button>
                            @if($ticket->status === 'open')
                                <button class="btn-success btn-sm" onclick="updateTicketStatus({{ $ticket->id }}, 'resolved')">&#x2705; Resolve</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">&#x1F3AB;</div>
                            <div class="empty-state-msg">No tickets found</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Create / Edit Ticket Modal --}}
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
                        <label class="ui-label">Title *</label>
                        <input type="text" class="ui-input" id="ticketTitle" required>
                    </div>
                    <div class="form-group">
                        <label class="ui-label">Priority *</label>
                        <select class="ui-select" id="ticketPriority" required>
                            <option value="">Select Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="ui-label">Ticket Type *</label>
                        <select class="ui-select" id="ticketType" required>
                            <option value="">Select Type</option>
                            <option value="pos_terminal">POS Terminal</option>
                            <option value="internal">Internal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="ui-label">Assignment Type *</label>
                        <select class="ui-select" id="ticketAssignmentType" required>
                            <option value="">Select Assignment</option>
                            <option value="public">Public (Any Employee)</option>
                            <option value="direct">Direct (Specific Employee)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="ui-label">Issue Type *</label>
                        <select class="ui-select" id="ticketIssueType" required>
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
                        <label class="ui-label">POS Terminal *</label>
                        <select class="ui-select" id="ticketPosTerminal" required>
                            <option value="">Select Terminal</option>
                            @foreach($posTerminals as $terminal)
                                <option value="{{ $terminal->id }}">{{ $terminal->terminal_id }} - {{ $terminal->merchant_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="assignedToField" style="display: none;">
                        <label class="ui-label">Assigned To *</label>
                        <select class="ui-select" id="ticketAssignedTo" required>
                            <option value="">Select Employee</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->first_name }} {{ $technician->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="ui-label">Est. Resolution Time (Days)</label>
                        <input type="number" class="ui-input" id="ticketEstimatedDays" min="0">
                    </div>
                </div>
                <div class="form-group form-group-full">
                    <label class="ui-label">Description *</label>
                    <textarea class="ui-input" id="ticketDescription" required placeholder="Please provide a detailed description of the issue..."></textarea>
                </div>
                <div class="form-group form-group-full" id="resolutionGroup" style="display: none;">
                    <label class="ui-label">Resolution</label>
                    <textarea class="ui-input" id="ticketResolution" placeholder="Describe how the issue was resolved..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeTicketModal()">Cancel</button>
            <button type="button" class="btn-primary" onclick="saveTicket()">Save Ticket</button>
        </div>
    </div>
</div>

{{-- View Ticket Details Modal --}}
<div id="ticketDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="ticketDetailsTitle">Ticket Details</h3>
            <button class="modal-close" onclick="closeTicketDetailsModal()">&times;</button>
        </div>
        <div class="modal-body" id="ticketDetailsBody"></div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeTicketDetailsModal()">Close</button>
            <button type="button" class="btn-secondary" id="viewStepsBtn" onclick="viewTicketSteps()">View Steps</button>
            <button type="button" class="btn-primary" id="editTicketBtn" onclick="editTicket()">Edit Ticket</button>
        </div>
    </div>
</div>

{{-- Ticket Steps / Audit Trail Modal --}}
<div id="ticketStepsModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 class="modal-title" id="ticketStepsTitle">Ticket Steps &amp; Audit Trail</h3>
            <button class="modal-close" onclick="closeTicketStepsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="ticketStepsContainer"></div>
            <div class="mt-5 pt-5 border-t border-gray-100">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Add Work Step</h4>
                <div class="form-group mb-3">
                    <label class="ui-label">Description *</label>
                    <input type="text" class="ui-input" id="stepDescription" placeholder="What work was done?">
                </div>
                <div class="form-group mb-3">
                    <label class="ui-label">Notes</label>
                    <textarea class="ui-input" id="stepNotes" placeholder="Additional notes..." rows="3"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="btn-success btn-sm" onclick="addTicketStep()">Add Step</button>
                    <button type="button" class="btn-secondary btn-sm" onclick="completeAndTransfer()">Complete &amp; Transfer</button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeTicketStepsModal()">Close</button>
        </div>
    </div>
</div>

{{-- Transfer Ticket Modal --}}
<div id="transferModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">Transfer Ticket</h3>
            <button class="modal-close" onclick="closeTransferModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group mb-3">
                <label class="ui-label">Transfer To *</label>
                <select class="ui-select" id="transferToEmployee">
                    <option value="">Select Employee</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->first_name }} {{ $tech->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3">
                <label class="ui-label">Reason for Transfer *</label>
                <textarea class="ui-input" id="transferReason" placeholder="Why are you transferring this ticket?" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="ui-label">Work Completed</label>
                <textarea class="ui-input" id="transferNotes" placeholder="What have you accomplished so far?" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeTransferModal()">Cancel</button>
            <button type="button" class="btn-primary" onclick="submitTransfer()">Transfer</button>
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
        updateStatus: (id) => `{{ route("tickets.updateStatus", ":id") }}`.replace(':id', id),
        addStep: (id) => `{{ route("tickets.addStep", ":id") }}`.replace(':id', id),
        completeStep: (id, stepId) => `{{ route("tickets.completeStep", [":id", ":step"]) }}`.replace(':id', id).replace(':step', stepId),
        transferStep: (id, stepId) => `{{ route("tickets.transferStep", [":id", ":step"]) }}`.replace(':id', id).replace(':step', stepId),
        resolveTicket: (id) => `{{ route("tickets.resolve", ":id") }}`.replace(':id', id),
        auditTrail: (id) => `{{ route("tickets.auditTrail", ":id") }}`.replace(':id', id)
    };

    let currentEditingTicket = null;
    let currentViewingTicketId = null;

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
        document.getElementById('ticketEstimatedDays').value = ticket.estimated_resolution_days || '';
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
            estimated_resolution_days: document.getElementById('ticketEstimatedDays').value || null,
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

        currentViewingTicketId = ticketId;

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
                ${ticket.estimated_resolution_days ? `
                    <div>
                        <strong>Est. Resolution:</strong><br>
                        ${ticket.estimated_resolution_days} days
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

    async function viewTicketSteps() {
        if (!currentViewingTicketId) return;

        try {
            const response = await fetch(routes.auditTrail(currentViewingTicketId), {
                headers: {
                    'Authorization': `Bearer ${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch steps');

            const data = await response.json();
            displayTicketSteps(data.steps || []);

            closeTicketDetailsModal();
            document.getElementById('ticketStepsModal').classList.add('show');
        } catch (error) {
            console.error('Error:', error);
            alert('Error loading ticket steps');
        }
    }

    function displayTicketSteps(steps) {
        const container = document.getElementById('ticketStepsContainer');

        if (steps.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #999;">No steps recorded yet</p>';
            return;
        }

        container.innerHTML = steps.map((step, index) => `
            <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid ${getStepColor(step.status)};">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <strong>Step ${step.step_number}</strong> - ${step.status.toUpperCase()}
                        <div style="font-size: 12px; color: #666; margin-top: 4px;">
                            ${step.employee_name}
                            ${step.completed_at ? ` • Completed: ${new Date(step.completed_at).toLocaleString()}` : ''}
                        </div>
                    </div>
                </div>
                <p style="margin: 10px 0; color: #333;">${step.description}</p>
                ${step.resolution_notes ? `<p style="margin: 10px 0; padding: 10px; background: white; border-radius: 4px;"><strong>Resolution:</strong> ${step.resolution_notes}</p>` : ''}
                ${step.notes ? `<p style="margin: 10px 0; font-size: 12px; color: #666;"><strong>Notes:</strong> ${step.notes}</p>` : ''}
                ${step.transferred_reason ? `<p style="margin: 10px 0; font-size: 12px; color: #d9534f;"><strong>Transferred to ${step.transferred_to_name}:</strong> ${step.transferred_reason}</p>` : ''}
            </div>
        `).join('');
    }

    function getStepColor(status) {
        const colors = {
            'in_progress': '#3498db',
            'completed': '#27ae60',
            'transferred': '#f39c12',
            'resolved': '#2ecc71'
        };
        return colors[status] || '#95a5a6';
    }

    async function addTicketStep() {
        const description = document.getElementById('stepDescription').value;
        const notes = document.getElementById('stepNotes').value;

        if (!description.trim()) {
            alert('Please enter a description');
            return;
        }

        try {
            const response = await fetch(routes.addStep(currentViewingTicketId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    description,
                    notes: notes || null
                })
            });

            if (!response.ok) throw new Error('Failed to add step');

            // Clear form and reload steps
            document.getElementById('stepDescription').value = '';
            document.getElementById('stepNotes').value = '';

            // Reload steps display
            await viewTicketSteps();
        } catch (error) {
            console.error('Error:', error);
            alert('Error adding step');
        }
    }

    function completeAndTransfer() {
        document.getElementById('transferModal').classList.add('show');
    }

    function closeTransferModal() {
        document.getElementById('transferModal').classList.remove('show');
        document.getElementById('transferToEmployee').value = '';
        document.getElementById('transferReason').value = '';
        document.getElementById('transferNotes').value = '';
    }

    async function submitTransfer() {
        const transferTo = document.getElementById('transferToEmployee').value;
        const reason = document.getElementById('transferReason').value;
        const notes = document.getElementById('transferNotes').value;

        if (!transferTo || !reason.trim()) {
            alert('Please select an employee and provide a reason');
            return;
        }

        try {
            // First get current step
            const auditResponse = await fetch(routes.auditTrail(currentViewingTicketId));
            const auditData = await auditResponse.json();
            const currentStep = auditData.steps?.find(s => s.status === 'in_progress');

            if (!currentStep) {
                alert('No active step to transfer');
                return;
            }

            // Transfer the step
            const transferResponse = await fetch(
                routes.transferStep(currentViewingTicketId, currentStep.id),
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        transferred_to: transferTo,
                        transferred_reason: reason,
                        notes: notes || null
                    })
                }
            );

            if (!transferResponse.ok) throw new Error('Failed to transfer');

            alert('Ticket transferred successfully');
            closeTransferModal();
            closeTicketStepsModal();
        } catch (error) {
            console.error('Error:', error);
            alert('Error transferring ticket');
        }
    }

    function closeTicketStepsModal() {
        document.getElementById('ticketStepsModal').classList.remove('show');
    }
</script>
@endpush
