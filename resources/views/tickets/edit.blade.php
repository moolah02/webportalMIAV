{{-- resources/views/tickets/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Ticket')

@section('header-actions')
    <a href="{{ route('tickets.show', $ticket) }}" class="btn-secondary btn-sm">View ticket</a>
@endsection

@push('styles')
<style>
    .te-wrap { max-width: 980px; }
    .te-crumb { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--mv-muted); text-decoration: none; margin-bottom: 14px; }
    .te-crumb:hover { color: var(--mv-ink); }
    .te-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 16px; }
    .te-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 13px 18px; border-bottom: 1px solid var(--mv-line); }
    .te-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .te-card-body { padding: 18px; }
    .te-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 18px; }
    .te-full { grid-column: 1 / -1; }
    .te-field .ui-label { display: block; margin-bottom: 6px; }
    .te-field .ui-input, .te-field .ui-select { width: 100%; }
    .te-field textarea { min-height: 110px; resize: vertical; }
    .te-err { font-size: 12px; color: var(--mv-crit); margin-top: 5px; }
    .te-req { color: var(--mv-crit); }
    .te-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 18px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); border-radius: 0 0 10px 10px; }
    @media (max-width: 720px) { .te-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $issueTypes = [
        'hardware_malfunction' => 'Hardware Malfunction', 'software_issue' => 'Software Issue',
        'network_connectivity' => 'Network Connectivity', 'user_training' => 'User Training',
        'maintenance_required' => 'Maintenance Required', 'replacement_needed' => 'Replacement Needed', 'other' => 'Other',
    ];
    $statuses = ['open' => 'Open', 'in_progress' => 'In Progress', 'on_hold' => 'On Hold', 'resolved' => 'Resolved', 'closed' => 'Closed', 'cancelled' => 'Cancelled'];
    $ticketType = old('ticket_type', $ticket->ticket_type ?: 'pos_terminal');
    $assignmentType = old('assignment_type', $ticket->assignment_type ?: 'public');
@endphp
<div class="te-wrap">
    <a href="{{ route('tickets.index') }}" class="te-crumb">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Support Tickets
    </a>

    @if($errors->any())
        <div class="alert-danger" style="border:1px solid; padding:11px 14px; margin-bottom:16px; font-size:13.5px;">
            <strong>Please check the form.</strong>
            <ul style="margin:6px 0 0 18px; padding:0;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tickets.update', $ticket) }}">
        @csrf
        @method('PUT')

        <div class="te-card">
            <div class="te-card-head">
                <h2>Ticket <span class="mv-mono" style="font-weight:500;">{{ $ticket->ticket_id }}</span></h2>
            </div>
            <div class="te-card-body">
                <div class="te-grid">
                    <div class="te-field te-full">
                        <label class="ui-label" for="title">Title <span class="te-req">*</span></label>
                        <input type="text" id="title" name="title" class="ui-input" required maxlength="255" value="{{ old('title', $ticket->title) }}">
                        @error('title')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="te-field">
                        <label class="ui-label" for="priority">Priority <span class="te-req">*</span></label>
                        <select id="priority" name="priority" class="ui-select" required>
                            @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $val => $label)
                                <option value="{{ $val }}" {{ old('priority', $ticket->priority) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="te-field">
                        <label class="ui-label" for="issue_type">Issue Type <span class="te-req">*</span></label>
                        <select id="issue_type" name="issue_type" class="ui-select" required>
                            @foreach($issueTypes as $val => $label)
                                <option value="{{ $val }}" {{ old('issue_type', $ticket->issue_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('issue_type')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="te-field te-full">
                        <label class="ui-label" for="description">Description <span class="te-req">*</span></label>
                        <textarea id="description" name="description" class="ui-input" required>{{ old('description', $ticket->description) }}</textarea>
                        @error('description')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="te-card">
            <div class="te-card-head"><h2>Assignment</h2></div>
            <div class="te-card-body">
                <div class="te-grid">
                    <div class="te-field">
                        <label class="ui-label" for="ticket_type">Ticket Type <span class="te-req">*</span></label>
                        <select id="ticket_type" name="ticket_type" class="ui-select" required>
                            <option value="pos_terminal" {{ $ticketType === 'pos_terminal' ? 'selected' : '' }}>POS Terminal</option>
                            <option value="internal" {{ $ticketType === 'internal' ? 'selected' : '' }}>Internal</option>
                        </select>
                        @error('ticket_type')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="te-field" id="posTerminalField">
                        <label class="ui-label" for="pos_terminal_id">POS Terminal <span class="te-req">*</span></label>
                        <select id="pos_terminal_id" name="pos_terminal_id" class="ui-select">
                            <option value="">Select Terminal</option>
                            @foreach($posTerminals as $terminal)
                                <option value="{{ $terminal->id }}" {{ (string) old('pos_terminal_id', $ticket->pos_terminal_id) === (string) $terminal->id ? 'selected' : '' }}>{{ $terminal->terminal_id }} - {{ $terminal->merchant_name }}</option>
                            @endforeach
                        </select>
                        @error('pos_terminal_id')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="te-field">
                        <label class="ui-label" for="assignment_type">Assignment Type <span class="te-req">*</span></label>
                        <select id="assignment_type" name="assignment_type" class="ui-select" required>
                            <option value="public" {{ $assignmentType === 'public' ? 'selected' : '' }}>Public (Any Employee)</option>
                            <option value="direct" {{ $assignmentType === 'direct' ? 'selected' : '' }}>Direct (Specific Employee)</option>
                        </select>
                        @error('assignment_type')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="te-field" id="assignedToField">
                        <label class="ui-label" for="assigned_to">Assigned To <span class="te-req">*</span></label>
                        <select id="assigned_to" name="assigned_to" class="ui-select">
                            <option value="">Select Employee</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}" {{ (string) old('assigned_to', $ticket->assigned_to) === (string) $technician->id ? 'selected' : '' }}>{{ $technician->first_name }} {{ $technician->last_name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="te-field">
                        <label class="ui-label" for="estimated_resolution_days">Est. Resolution Time (Days)</label>
                        <input type="number" id="estimated_resolution_days" name="estimated_resolution_days" class="ui-input" min="1"
                               value="{{ old('estimated_resolution_days', $ticket->estimated_resolution_time) }}">
                        @error('estimated_resolution_days')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="te-card">
            <div class="te-card-head"><h2>Status &amp; Resolution</h2></div>
            <div class="te-card-body">
                <div class="te-grid">
                    <div class="te-field">
                        <label class="ui-label" for="status">Status</label>
                        <select id="status" name="status" class="ui-select">
                            @foreach($statuses as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $ticket->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="te-field te-full">
                        <label class="ui-label" for="resolution">Resolution</label>
                        <textarea id="resolution" name="resolution" class="ui-input" placeholder="Describe how the issue was resolved...">{{ old('resolution', $ticket->resolution) }}</textarea>
                        @error('resolution')<div class="te-err">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="te-foot">
                <a href="{{ route('tickets.show', $ticket) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg> Save Ticket
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const type = document.getElementById('ticket_type');
    const assign = document.getElementById('assignment_type');
    function sync() {
        const pos = document.getElementById('posTerminalField');
        const to = document.getElementById('assignedToField');
        pos.style.display = type.value === 'pos_terminal' ? '' : 'none';
        document.getElementById('pos_terminal_id').required = type.value === 'pos_terminal';
        to.style.display = assign.value === 'direct' ? '' : 'none';
        document.getElementById('assigned_to').required = assign.value === 'direct';
    }
    type.addEventListener('change', sync);
    assign.addEventListener('change', sync);
    sync();
})();
</script>
@endpush
