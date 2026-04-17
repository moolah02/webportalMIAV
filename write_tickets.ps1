$scriptsSection = [IO.File]::ReadAllText('c:\xampp4\htdocs\dashboard\Revival_Technologies\tickets_scripts_backup.txt', [Text.Encoding]::UTF8)
$utf8NoBom = New-Object System.Text.UTF8Encoding $false

$html = @'
@extends('layouts.app')

@push('styles')
<style>
    /* ── Modals ─────────────────────────────────────────────── */
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

    /* ── Badges — referenced by JS-generated HTML ────────────── */
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

    /* ── Table cell helpers — referenced by JS filter queries ── */
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
        <h1 class="page-title">🎫 Support Tickets</h1>
        <p class="page-subtitle">Manage and track all support requests</p>
    </div>
    <button class="btn-primary" onclick="openCreateTicketModal()">➕ New Ticket</button>
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
                            <button class="btn-secondary btn-sm" onclick="viewTicketDetails({{ $ticket->id }})">👁 View</button>
                            <button class="btn-primary btn-sm" onclick="editTicketFromTable({{ $ticket->id }})">✏️ Edit</button>
                            @if($ticket->status === 'open')
                                <button class="btn-success btn-sm" onclick="updateTicketStatus({{ $ticket->id }}, 'resolved')">✅ Resolve</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">🎫</div>
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

'@

$content = $html + $scriptsSection
[IO.File]::WriteAllText('c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views\tickets\index.blade.php', $content, $utf8NoBom)
Write-Host "Written. Lines: $(($content -split "`n").Count)"
