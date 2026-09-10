@extends('layouts.app')
@section('title', 'Support Tickets')

@push('styles')
<style>
    /* ── Stats strip ── */
    .tx-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 16px; }
    .tx-stat { padding: 14px 18px; }
    .tx-stat + .tx-stat { border-left: 1px solid var(--mv-line); }
    .tx-stat-label { font-size: 12.5px; color: var(--mv-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 7px; }
    .tx-stat-value { font-size: 22px; font-weight: 600; color: var(--mv-ink); letter-spacing: -.02em; font-variant-numeric: tabular-nums; line-height: 1.15; }
    .tx-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--mv-line-strong); }
    .tx-dot.is-accent { background: var(--mv-accent); }
    .tx-dot.is-warn { background: #C28A2C; }
    .tx-dot.is-good { background: var(--mv-good); }
    .tx-dot.is-crit { background: var(--mv-crit); }

    /* ── Toolbar / filters ── */
    .tx-toolbar { display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap; margin-bottom: 12px; }
    .tx-toolbar .tx-f { display: flex; flex-direction: column; gap: 5px; min-width: 150px; }
    .tx-toolbar .tx-f-search { flex: 1; min-width: 220px; max-width: 360px; }
    .tx-toolbar .ui-label { margin: 0; }
    .tx-toolbar .ui-input, .tx-toolbar .ui-select { width: 100%; }
    .tx-toolbar .tx-spacer { flex: 1; }
    .tx-searchbox { display: flex; align-items: center; gap: 6px; border: 1px solid var(--mv-line-strong); border-radius: 8px; padding: 0 10px; background: var(--mv-surface); color: var(--mv-muted); }
    .tx-searchbox:focus-within { border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
    .tx-searchbox input { border: 0 !important; box-shadow: none !important; outline: 0; padding: 8px 0; font: inherit; font-size: 13.5px; width: 100%; background: transparent; }

    /* ── Table ── */
    .tx-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
    .tx-card-head { display: flex; align-items: center; justify-content: space-between; padding: 13px 18px; border-bottom: 1px solid var(--mv-line); }
    .tx-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .tx-count { font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
    .tx-table { width: 100%; border-collapse: collapse; }
    .tx-table th { background: var(--mv-surface-2); color: var(--mv-muted); font-size: 11.5px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; text-align: left; padding: 9px 14px; border-bottom: 1px solid var(--mv-line); white-space: nowrap; }
    .tx-table td { padding: 11px 14px; border-bottom: 1px solid var(--mv-line); vertical-align: middle; font-size: 13px; color: var(--mv-ink-2); }
    .tx-table tbody tr:last-child td { border-bottom: 0; }
    .tx-table tbody tr:hover { background: var(--mv-surface-2); }

    /* Table cell helpers — referenced by JS filter queries */
    .ticket-id { font-family: var(--mv-mono); font-weight: 500; font-size: 12.5px; color: var(--mv-ink); white-space: nowrap; text-decoration: none; }
    a.ticket-id:hover { color: var(--mv-accent-ink); text-decoration: underline; }
    .ticket-title { font-weight: 500; color: var(--mv-ink); font-size: 13.5px; margin-bottom: 2px; }
    .ticket-description { color: var(--mv-muted); font-size: 12.5px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; max-width: 460px; }
    .ticket-meta { font-size: 12px; color: var(--mv-muted); margin-top: 2px; }
    .ticket-meta .mv-mono { color: var(--mv-ink-2); }
    .technician-avatar { width: 24px; height: 24px; border-radius: 50%; background: var(--mv-accent-soft); color: var(--mv-accent-ink); display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; flex-shrink: 0; }
    .tx-person { display: flex; align-items: center; gap: 8px; white-space: nowrap; color: var(--mv-ink); }
    .tx-nowrap { white-space: nowrap; }

    /* Chips — class names are read by the JS filters */
    .status-badge, .priority-badge, .issue-badge { display: inline-flex; align-items: center; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; line-height: 1.7; white-space: nowrap;
        background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }
    .status-open, .priority-medium { background: var(--mv-accent-soft); color: var(--mv-accent-ink); border-color: transparent; }
    .status-in-progress, .status-pending, .priority-high { background: var(--mv-warn-soft); color: var(--mv-warn); border-color: transparent; }
    .status-resolved { background: var(--mv-good-soft); color: var(--mv-good); border-color: transparent; }
    .status-cancelled, .priority-critical { background: var(--mv-crit-soft); color: var(--mv-crit); border-color: transparent; }
    /* issue types, closed / on hold and low priority stay neutral */

    /* Row actions */
    .tx-actions { display: flex; gap: 6px; justify-content: flex-end; align-items: center; }
    .tx-icon-btn { width: 30px; height: 30px; border: 1px solid var(--mv-line); border-radius: 7px; background: var(--mv-surface); color: var(--mv-ink-2); display: grid; place-items: center; cursor: pointer; padding: 0; }
    .tx-icon-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); border-color: var(--mv-line-strong); }
    .tx-status-btn { height: 30px; display: inline-flex; align-items: center; gap: 4px; padding: 0 8px 0 10px; border: 1px solid var(--mv-line); border-radius: 7px; background: var(--mv-surface); color: var(--mv-ink-2); font: inherit; font-size: 12.5px; font-weight: 500; cursor: pointer; white-space: nowrap; }
    .tx-status-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); border-color: var(--mv-line-strong); }

    /* Status change dropdown */
    .status-drop { position: relative; display: inline-block; }
    .status-drop-menu { display: none; position: absolute; right: 0; top: calc(100% + 4px); background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 8px; box-shadow: 0 12px 32px rgba(22, 32, 44, .12); z-index: 200; min-width: 160px; padding: 4px; }
    .status-drop-menu button { display: block; width: 100%; padding: 7px 10px; text-align: left; background: none; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; color: var(--mv-ink-2); white-space: nowrap; }
    .status-drop-menu button:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
    .status-drop.open .status-drop-menu { display: block; }

    .tx-empty { padding: 40px 20px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
    .tx-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); display: block; margin: 0 auto 10px; }

    /* ── Modals ── */
    .modal { display: none; position: fixed; z-index: 1000; inset: 0; background: rgba(22, 32, 44, .45); padding: 16px; }
    .modal.show { display: flex; align-items: center; justify-content: center; }
    .modal-content { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; max-width: 760px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
    .modal-header { padding: 14px 18px; border-bottom: 1px solid var(--mv-line); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: var(--mv-surface); z-index: 1; }
    .modal-title { font-size: 15px; font-weight: 600; color: var(--mv-ink); margin: 0; }
    .modal-close { width: 32px; height: 32px; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); display: grid; place-items: center; cursor: pointer; padding: 0; }
    .modal-close:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
    .modal-body { padding: 18px; }
    .modal-footer { padding: 12px 18px; border-top: 1px solid var(--mv-line); display: flex; gap: 8px; justify-content: flex-end; background: var(--mv-surface-2); border-radius: 0 0 12px 12px; }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; margin-bottom: 14px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group-full { grid-column: 1 / -1; }
    .form-group .ui-label { margin: 0; }
    textarea.ui-input { min-height: 90px; resize: vertical; }
    .tx-section-title { font-size: 13px; font-weight: 600; color: var(--mv-ink); margin: 0 0 10px; }

    /* JS-generated details / steps */
    .tx-dl { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px 20px; margin: 0 0 18px; }
    .tx-dl dt { font-size: 12px; color: var(--mv-muted); margin-bottom: 3px; font-weight: 400; }
    .tx-dl dd { margin: 0; font-size: 13.5px; color: var(--mv-ink); }
    .tx-block { background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 8px; padding: 12px 14px; font-size: 13.5px; color: var(--mv-ink-2); white-space: pre-wrap; line-height: 1.55; }
    .tx-block-label { font-size: 12px; color: var(--mv-muted); margin: 0 0 6px; }
    .tx-steps { list-style: none; margin: 0; padding: 0; }
    .tx-step { display: grid; grid-template-columns: 28px minmax(0, 1fr); gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--mv-line); }
    .tx-step:first-child { padding-top: 0; }
    .tx-step-no { width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--mv-line-strong); display: grid; place-items: center; font-size: 12px; font-weight: 600; color: var(--mv-ink-2); }
    .tx-step-head { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; font-size: 13px; color: var(--mv-ink); font-weight: 500; }
    .tx-step-when { font-size: 12px; color: var(--mv-muted); font-weight: 400; }
    .tx-step p { margin: 4px 0 0; font-size: 13px; color: var(--mv-ink-2); }
    .tx-step .tx-note { font-size: 12.5px; color: var(--mv-muted); }
    .tx-step .tx-transfer { font-size: 12.5px; color: var(--mv-warn); }
    .tx-add-step { margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--mv-line); }

    @media (max-width: 900px) {
        .tx-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .tx-stat:nth-child(3) { border-left: 0; }
        .tx-stat:nth-child(n+3) { border-top: 1px solid var(--mv-line); }
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('header-actions')
    <button type="button" class="btn-secondary btn-sm" onclick="openCreateTicketModal()">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> New Ticket
    </button>
@endsection

@section('content')

{{-- Stats --}}
<div class="tx-stats">
    <div class="tx-stat">
        <div class="tx-stat-label"><span class="tx-dot is-accent"></span>Open Tickets</div>
        <div class="tx-stat-value" id="openTickets">{{ $stats['open'] ?? 0 }}</div>
    </div>
    <div class="tx-stat">
        <div class="tx-stat-label"><span class="tx-dot is-warn"></span>In Progress</div>
        <div class="tx-stat-value" id="inProgressTickets">{{ $stats['in_progress'] ?? 0 }}</div>
    </div>
    <div class="tx-stat">
        <div class="tx-stat-label"><span class="tx-dot is-good"></span>Resolved This Month</div>
        <div class="tx-stat-value" id="resolvedTickets">{{ $stats['resolved'] ?? 0 }}</div>
    </div>
    <div class="tx-stat">
        <div class="tx-stat-label"><span class="tx-dot is-crit"></span>Critical Priority</div>
        <div class="tx-stat-value" id="criticalTickets">{{ $stats['critical'] ?? 0 }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="tx-toolbar">
    <div class="tx-f tx-f-search">
        <label class="ui-label" for="searchInput">Search</label>
        <div class="tx-searchbox">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-search"/></svg>
            <input type="text" id="searchInput" placeholder="Search tickets...">
        </div>
    </div>
    <div class="tx-f">
        <label class="ui-label" for="statusFilter">Status</label>
        <select class="ui-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="open">Open</option>
            <option value="in_progress">In Progress</option>
            <option value="on_hold">On Hold</option>
            <option value="resolved">Resolved</option>
            <option value="closed">Closed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
    <div class="tx-f">
        <label class="ui-label" for="priorityFilter">Priority</label>
        <select class="ui-select" id="priorityFilter">
            <option value="">All Priorities</option>
            <option value="critical">Critical</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
        </select>
    </div>
    <div class="tx-f">
        <label class="ui-label" for="issueTypeFilter">Issue Type</label>
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
    <button type="button" class="btn-secondary" onclick="clearFilters()">Clear</button>
</div>

{{-- Tickets table --}}
<div class="tx-card">
    <div class="tx-card-head">
        <h2>All Tickets</h2>
        <span class="tx-count">{{ $tickets->count() }} {{ \Illuminate\Support\Str::plural('ticket', $tickets->count()) }}</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="tx-table" id="ticketsTable">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Title &amp; Description</th>
                    <th>Issue Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Technician</th>
                    <th>Created</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="ticketsTableBody">
                @forelse($tickets as $ticket)
                <tr>
                    <td><a href="{{ route('tickets.show', $ticket) }}" class="ticket-id">{{ $ticket->ticket_id }}</a></td>
                    <td>
                        <div class="ticket-title">{{ $ticket->title }}</div>
                        <div class="ticket-description">{{ Str::limit($ticket->description, 100) }}</div>
                        @if($ticket->posTerminal)
                            <div class="ticket-meta">Terminal <span class="mv-mono">{{ $ticket->posTerminal->terminal_id }}</span></div>
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
                            <div class="tx-person">
                                <span class="technician-avatar">{{ substr($ticket->assignedTo->first_name, 0, 1) }}</span>
                                <span>{{ $ticket->assignedTo->first_name }} {{ $ticket->assignedTo->last_name }}</span>
                            </div>
                        @else
                            <span class="ticket-meta">Unassigned</span>
                        @endif
                    </td>
                    <td class="tx-nowrap">
                        <div>{{ $ticket->created_at->format('M j, Y') }}</div>
                        <div class="ticket-meta">{{ $ticket->created_at->format('g:i A') }}</div>
                    </td>
                    <td>
                        <div class="tx-actions">
                            <button type="button" class="tx-icon-btn" onclick="viewTicketDetails({{ $ticket->id }})" title="View" aria-label="View ticket {{ $ticket->ticket_id }}">
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>
                            </button>
                            <button type="button" class="tx-icon-btn" onclick="editTicketFromTable({{ $ticket->id }})" title="Edit" aria-label="Edit ticket {{ $ticket->ticket_id }}">
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg>
                            </button>
                            @php
                                $transitions = [
                                    'open'        => ['in_progress'=>'Mark In Progress','on_hold'=>'Put On Hold','resolved'=>'Resolve','cancelled'=>'Cancel'],
                                    'in_progress' => ['on_hold'=>'Put On Hold','resolved'=>'Resolve','open'=>'Back to Open','cancelled'=>'Cancel'],
                                    'on_hold'     => ['in_progress'=>'Resume','open'=>'Back to Open','cancelled'=>'Cancel'],
                                    'resolved'    => ['open'=>'Re-open','closed'=>'Close'],
                                ];
                                $nextStatuses = $transitions[$ticket->status] ?? [];
                            @endphp
                            @if(!empty($nextStatuses))
                            <div class="status-drop" id="sdrop-{{ $ticket->id }}">
                                <button type="button" class="tx-status-btn" onclick="toggleStatusDrop({{ $ticket->id }})" aria-haspopup="true">
                                    Status <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-chevron-down"/></svg>
                                </button>
                                <div class="status-drop-menu">
                                    @foreach($nextStatuses as $status => $label)
                                        <button type="button" onclick="updateTicketStatus({{ $ticket->id }},'{{ $status }}',this);closeAllStatusDrops()">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="tx-empty">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-ticket"/></svg>
                            No tickets found.
                            <div style="margin-top:12px;"><button type="button" class="btn-secondary btn-sm" onclick="openCreateTicketModal()">New Ticket</button></div>
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
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="ticketModalTitle">
        <div class="modal-header">
            <h3 class="modal-title" id="ticketModalTitle">Create New Ticket</h3>
            <button type="button" class="modal-close" onclick="closeTicketModal()" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="modal-body">
            <form id="ticketForm">
                @csrf
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label class="ui-label" for="ticketTitle">Title *</label>
                        <input type="text" class="ui-input" id="ticketTitle" required>
                    </div>
                    <div class="form-group">
                        <label class="ui-label" for="ticketPriority">Priority *</label>
                        <select class="ui-select" id="ticketPriority" required>
                            <option value="">Select Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="ui-label" for="ticketIssueType">Issue Type *</label>
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
                    <div class="form-group">
                        <label class="ui-label" for="ticketType">Ticket Type *</label>
                        <select class="ui-select" id="ticketType" required>
                            <option value="">Select Type</option>
                            <option value="pos_terminal">POS Terminal</option>
                            <option value="internal">Internal</option>
                        </select>
                    </div>
                    <div class="form-group" id="posTerminalField">
                        <label class="ui-label" for="ticketPosTerminal">POS Terminal *</label>
                        <select class="ui-select" id="ticketPosTerminal" required>
                            <option value="">Select Terminal</option>
                            @foreach($posTerminals as $terminal)
                                <option value="{{ $terminal->id }}">{{ $terminal->terminal_id }} - {{ $terminal->merchant_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="ui-label" for="ticketAssignmentType">Assignment Type *</label>
                        <select class="ui-select" id="ticketAssignmentType" required>
                            <option value="">Select Assignment</option>
                            <option value="public">Public (Any Employee)</option>
                            <option value="direct">Direct (Specific Employee)</option>
                        </select>
                    </div>
                    <div class="form-group" id="assignedToField" style="display: none;">
                        <label class="ui-label" for="ticketAssignedTo">Assigned To *</label>
                        <select class="ui-select" id="ticketAssignedTo" required>
                            <option value="">Select Employee</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->first_name }} {{ $technician->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="ui-label" for="ticketEstimatedDays">Est. Resolution Time (Days)</label>
                        <input type="number" class="ui-input" id="ticketEstimatedDays" min="0">
                    </div>
                </div>
                <div class="form-group form-group-full">
                    <label class="ui-label" for="ticketDescription">Description *</label>
                    <textarea class="ui-input" id="ticketDescription" required placeholder="Please provide a detailed description of the issue..."></textarea>
                </div>
                <div class="form-group form-group-full" id="resolutionGroup" style="display: none; margin-top: 14px;">
                    <label class="ui-label" for="ticketResolution">Resolution</label>
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
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="ticketDetailsTitle">
        <div class="modal-header">
            <h3 class="modal-title" id="ticketDetailsTitle">Ticket Details</h3>
            <button type="button" class="modal-close" onclick="closeTicketDetailsModal()" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
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
    <div class="modal-content" style="max-width: 680px;" role="dialog" aria-modal="true" aria-labelledby="ticketStepsTitle">
        <div class="modal-header">
            <h3 class="modal-title" id="ticketStepsTitle">Ticket Steps &amp; Audit Trail</h3>
            <button type="button" class="modal-close" onclick="closeTicketStepsModal()" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="modal-body">
            <div id="ticketStepsContainer"></div>
            <div class="tx-add-step">
                <h4 class="tx-section-title">Add Work Step</h4>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="ui-label" for="stepDescription">Description *</label>
                    <input type="text" class="ui-input" id="stepDescription" placeholder="What work was done?">
                </div>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="ui-label" for="stepNotes">Notes</label>
                    <textarea class="ui-input" id="stepNotes" placeholder="Additional notes..." rows="3"></textarea>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn-primary btn-sm" onclick="addTicketStep()">Add Step</button>
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
    <div class="modal-content" style="max-width: 480px;" role="dialog" aria-modal="true" aria-labelledby="transferTicketTitle">
        <div class="modal-header">
            <h3 class="modal-title" id="transferTicketTitle">Transfer Ticket</h3>
            <button type="button" class="modal-close" onclick="closeTransferModal()" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="modal-body">
            <div class="form-group" style="margin-bottom: 12px;">
                <label class="ui-label" for="transferToEmployee">Transfer To *</label>
                <select class="ui-select" id="transferToEmployee">
                    <option value="">Select Employee</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->first_name }} {{ $tech->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label class="ui-label" for="transferReason">Reason for Transfer *</label>
                <textarea class="ui-input" id="transferReason" placeholder="Why are you transferring this ticket?" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="ui-label" for="transferNotes">Work Completed</label>
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
        update: (id) => `{{ route("tickets.update", ["TICKET_ID_PLACEHOLDER"]) }}`.replace('TICKET_ID_PLACEHOLDER', id),
        show: (id) => `{{ route("tickets.show", ["TICKET_ID_PLACEHOLDER"]) }}`.replace('TICKET_ID_PLACEHOLDER', id),
        updateStatus: (id) => `{{ route("tickets.updateStatus", ["TICKET_ID_PLACEHOLDER"]) }}`.replace('TICKET_ID_PLACEHOLDER', id),
        addStep: (id) => `{{ route("tickets.addStep", ["TICKET_ID_PLACEHOLDER"]) }}`.replace('TICKET_ID_PLACEHOLDER', id),
        completeStep: (id, stepId) => `{{ route("tickets.completeStep", ["TICKET_ID_PLACEHOLDER", "STEP_ID_PLACEHOLDER"]) }}`.replace('TICKET_ID_PLACEHOLDER', id).replace('STEP_ID_PLACEHOLDER', stepId),
        transferStep: (id, stepId) => `{{ route("tickets.transferStep", ["TICKET_ID_PLACEHOLDER", "STEP_ID_PLACEHOLDER"]) }}`.replace('TICKET_ID_PLACEHOLDER', id).replace('STEP_ID_PLACEHOLDER', stepId),
        resolveTicket: (id) => `{{ route("tickets.resolve", ["TICKET_ID_PLACEHOLDER"]) }}`.replace('TICKET_ID_PLACEHOLDER', id),
        auditTrail: (id) => `{{ route("tickets.auditTrail", ["TICKET_ID_PLACEHOLDER"]) }}`.replace('TICKET_ID_PLACEHOLDER', id)
    };

    let currentEditingTicket = null;
    let currentViewingTicketId = null;

    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const titleCase = (v) => String(v ?? '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('statusFilter').addEventListener('change', filterTickets);
        document.getElementById('priorityFilter').addEventListener('change', filterTickets);
        document.getElementById('issueTypeFilter').addEventListener('change', filterTickets);
        document.getElementById('searchInput').addEventListener('input', filterTickets);

        document.getElementById('ticketType')?.addEventListener('change', updateFormFields);
        document.getElementById('ticketAssignmentType')?.addEventListener('change', updateFormFields);

        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('ticketModal')) closeTicketModal();
            if (event.target === document.getElementById('ticketDetailsModal')) closeTicketDetailsModal();
        });
    }

    function updateFormFields() {
        const ticketType = document.getElementById('ticketType')?.value;
        const assignmentType = document.getElementById('ticketAssignmentType')?.value;

        const posTerminalField = document.getElementById('posTerminalField');
        if (posTerminalField) {
            if (ticketType === 'pos_terminal') {
                posTerminalField.style.display = '';
                document.getElementById('ticketPosTerminal').required = true;
            } else {
                posTerminalField.style.display = 'none';
                document.getElementById('ticketPosTerminal').required = false;
                document.getElementById('ticketPosTerminal').value = '';
            }
        }

        const assignedToField = document.getElementById('assignedToField');
        if (assignedToField) {
            if (assignmentType === 'direct') {
                assignedToField.style.display = '';
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

        document.querySelectorAll('#ticketsTableBody tr').forEach(row => {
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

            row.style.display = (issueType && priority && status && matchesSearch) ? '' : 'none';
        });
    }

    function clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('priorityFilter').value = '';
        document.getElementById('issueTypeFilter').value = '';
        document.getElementById('searchInput').value = '';
        document.querySelectorAll('#ticketsTableBody tr').forEach(row => { row.style.display = ''; });
    }

    function openCreateTicketModal() {
        currentEditingTicket = null;
        document.getElementById('ticketModalTitle').textContent = 'Create New Ticket';
        document.getElementById('ticketForm').reset();
        document.getElementById('resolutionGroup').style.display = 'none';
        updateFormFields();
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

        document.getElementById('ticketTitle').value = ticket.title;
        document.getElementById('ticketPriority').value = ticket.priority;
        document.getElementById('ticketType').value = ticket.ticket_type || 'pos_terminal';
        document.getElementById('ticketAssignmentType').value = ticket.assignment_type || 'public';
        document.getElementById('ticketIssueType').value = ticket.issue_type;
        document.getElementById('ticketPosTerminal').value = ticket.pos_terminal_id || '';
        document.getElementById('ticketAssignedTo').value = ticket.assigned_to || '';
        document.getElementById('ticketEstimatedDays').value = ticket.estimated_resolution_days || ticket.estimated_resolution_time || '';
        document.getElementById('ticketDescription').value = ticket.description;
        document.getElementById('ticketResolution').value = ticket.resolution || '';

        updateFormFields();

        if (ticket.status === 'resolved' || ticket.status === 'closed') {
            document.getElementById('resolutionGroup').style.display = '';
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
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                closeTicketModal();
                window.location.reload();
            } else {
                let message = 'Unknown error';
                try {
                    const errorData = await response.json();
                    message = errorData.errors ? Object.values(errorData.errors).flat().join('\n') : (errorData.message || message);
                } catch (_) {}
                showNotification('error', 'Error saving ticket: ' + esc(message));
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Error saving ticket. Please try again.');
        }
    }

    function toggleStatusDrop(ticketId) {
        const el = document.getElementById('sdrop-' + ticketId);
        if (!el) return;
        const isOpen = el.classList.contains('open');
        closeAllStatusDrops();
        if (!isOpen) el.classList.add('open');
    }

    function closeAllStatusDrops() {
        document.querySelectorAll('.status-drop.open').forEach(el => el.classList.remove('open'));
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.status-drop')) closeAllStatusDrops();
    });

    async function updateTicketStatus(ticketId, newStatus, btn) {
        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }
        try {
            const response = await fetch(routes.updateStatus(ticketId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            });

            const text = await response.text();
            let data = null;
            try { data = JSON.parse(text); } catch (_) {}

            if (response.ok) {
                showNotification('success', data?.message || 'Ticket status updated successfully');
                setTimeout(() => window.location.reload(), 900);
                return;
            }

            showNotification('error', data?.message || data?.error || `Could not update ticket status (HTTP ${response.status})`);
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Could not update ticket status. Please try again.');
        } finally {
            if (btn) { btn.disabled = false; btn.style.opacity = ''; }
        }
    }

    // Toasts: showNotification(type, message) comes from the portal layout.

    function viewTicketDetails(ticketId) {
        const ticket = ticketsData.find(t => t.id === ticketId);
        if (!ticket) return;

        currentViewingTicketId = ticketId;
        document.getElementById('ticketDetailsTitle').textContent = `Ticket ${ticket.ticket_id}`;

        const created = new Date(ticket.created_at);
        const estDays = ticket.estimated_resolution_days || ticket.estimated_resolution_time;

        document.getElementById('ticketDetailsBody').innerHTML = `
            <dl class="tx-dl">
                <div style="grid-column: 1 / -1;"><dt>Title</dt><dd>${esc(ticket.title)}</dd></div>
                <div><dt>Status</dt><dd><span class="status-badge status-${esc(ticket.status.replace('_', '-'))}">${esc(titleCase(ticket.status))}</span></dd></div>
                <div><dt>Priority</dt><dd><span class="priority-badge priority-${esc(ticket.priority)}">${esc(titleCase(ticket.priority))}</span></dd></div>
                <div><dt>Issue Type</dt><dd><span class="issue-badge issue-${esc(ticket.issue_type)}">${esc(titleCase(ticket.issue_type))}</span></dd></div>
                <div><dt>Assigned To</dt><dd>${ticket.assigned_to ? esc(ticket.assigned_to.first_name + ' ' + ticket.assigned_to.last_name) : 'Unassigned'}</dd></div>
                <div><dt>Created</dt><dd>${created.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })} ${created.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</dd></div>
                ${ticket.pos_terminal ? `<div><dt>POS Terminal</dt><dd class="mv-mono">${esc(ticket.pos_terminal.terminal_id)}</dd></div>` : ''}
                ${estDays ? `<div><dt>Est. Resolution</dt><dd>${esc(estDays)} days</dd></div>` : ''}
            </dl>
            <p class="tx-block-label">Description</p>
            <div class="tx-block">${esc(ticket.description)}</div>
            ${ticket.resolution ? `
                <p class="tx-block-label" style="margin-top: 16px;">Resolution</p>
                <div class="tx-block">${esc(ticket.resolution)}</div>
            ` : ''}
            <p style="margin: 16px 0 0; font-size: 13px;"><a href="${routes.show(ticket.id)}" style="color: var(--mv-accent-ink);">Open full ticket page</a></p>
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
            showNotification('error', 'Error loading ticket steps');
        }
    }

    function displayTicketSteps(steps) {
        const container = document.getElementById('ticketStepsContainer');

        if (steps.length === 0) {
            container.innerHTML = '<p style="margin: 0; font-size: 13px; color: var(--mv-muted);">No steps recorded yet</p>';
            return;
        }

        container.innerHTML = '<ol class="tx-steps">' + steps.map(step => `
            <li class="tx-step">
                <span class="tx-step-no">${esc(step.step_number)}</span>
                <div>
                    <div class="tx-step-head">
                        ${esc(step.employee_name)}
                        <span class="status-badge ${getStepColor(step.status)}">${esc(titleCase(step.status))}</span>
                        ${step.completed_at ? `<span class="tx-step-when">Completed ${new Date(step.completed_at).toLocaleString()}</span>` : ''}
                    </div>
                    <p>${esc(step.description)}</p>
                    ${step.resolution_notes ? `<p><strong>Resolution:</strong> ${esc(step.resolution_notes)}</p>` : ''}
                    ${step.notes ? `<p class="tx-note">Notes: ${esc(step.notes)}</p>` : ''}
                    ${step.transferred_reason ? `<p class="tx-transfer">Transferred to ${esc(step.transferred_to_name)}: ${esc(step.transferred_reason)}</p>` : ''}
                </div>
            </li>
        `).join('') + '</ol>';
    }

    // step status → chip tone (classes defined above)
    function getStepColor(status) {
        const tones = {
            'in_progress': 'status-in-progress',
            'completed': 'status-resolved',
            'transferred': 'status-open',
            'resolved': 'status-resolved'
        };
        return tones[status] || '';
    }

    async function addTicketStep() {
        const description = document.getElementById('stepDescription').value;
        const notes = document.getElementById('stepNotes').value;

        if (!description.trim()) {
            showNotification('error', 'Please enter a description');
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
                body: JSON.stringify({ description, notes: notes || null })
            });

            if (!response.ok) throw new Error('Failed to add step');

            document.getElementById('stepDescription').value = '';
            document.getElementById('stepNotes').value = '';
            await viewTicketSteps();
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Error adding step');
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
            showNotification('error', 'Please select an employee and provide a reason');
            return;
        }

        try {
            const auditResponse = await fetch(routes.auditTrail(currentViewingTicketId));
            const auditData = await auditResponse.json();
            const currentStep = auditData.steps?.find(s => s.status === 'in_progress');

            if (!currentStep) {
                showNotification('error', 'No active step to transfer');
                return;
            }

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

            showNotification('success', 'Ticket transferred successfully');
            closeTransferModal();
            closeTicketStepsModal();
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Error transferring ticket');
        }
    }

    function closeTicketStepsModal() {
        document.getElementById('ticketStepsModal').classList.remove('show');
    }
</script>
@endpush
