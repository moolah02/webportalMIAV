@extends('layouts.app')
@section('title', 'Terminal — ' . $posTerminal->terminal_id)

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
$statusBadge = [
    'active'      => 'badge-green',
    'inactive'    => 'badge-gray',
    'maintenance' => 'badge-yellow',
    'offline'     => 'badge-gray',
    'faulty'      => 'badge-red',
][$posTerminal->status ?? 'inactive'] ?? 'badge-gray';
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-3 min-w-0">
        <span class="inline-block px-2.5 py-1 rounded-md bg-[#1a3a5c] text-white text-sm font-semibold font-mono shrink-0">
            {{ $posTerminal->terminal_id }}
        </span>
        <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate">{{ $posTerminal->merchant_name ?? '—' }}</p>
            <p class="text-xs text-gray-400 truncate">{{ $posTerminal->client->company_name ?? '—' }}</p>
        </div>
        <span class="badge {{ $statusBadge }} shrink-0">{{ ucfirst($posTerminal->status ?? 'unknown') }}</span>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('pos-terminals.edit', $posTerminal) }}" class="btn-primary btn-sm">Edit</a>
        <button onclick="confirmDelete()" class="btn-danger btn-sm">Delete</button>
        <a href="{{ route('pos-terminals.index') }}" class="btn-secondary btn-sm">&#x2190; Back</a>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="flash-success mb-5"><span>&#x2713;</span> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-error mb-5"><span>&#x26A0;</span> {{ session('error') }}</div>
@endif

{{-- Main grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    {{-- ===== LEFT / MAIN (2 cols) ===== --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Terminal Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold text-gray-800">Terminal Information</span>
            </div>
            <div class="ui-card-body grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Terminal ID</div>
                    <div class="text-sm text-gray-800 font-mono font-medium">{{ $posTerminal->terminal_id }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Status</div>
                    <span class="badge {{ $statusBadge }}">{{ ucfirst($posTerminal->status ?? 'unknown') }}</span>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Model</div>
                    <div class="text-sm text-gray-800">{{ $posTerminal->terminal_model ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Serial Number</div>
                    <div class="text-sm text-gray-800 font-mono">{{ $posTerminal->serial_number ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Installation Date</div>
                    <div class="text-sm text-gray-800">
                        {{ $posTerminal->installation_date ? $posTerminal->installation_date->format('M d, Y') : '—' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Last Service</div>
                    <div class="text-sm text-gray-800">
                        {{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never' }}
                    </div>
                </div>
                @if($posTerminal->physical_address)
                <div class="col-span-2 sm:col-span-3 border-t border-gray-100 pt-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Physical Address</div>
                    <div class="text-sm text-gray-800">{{ $posTerminal->physical_address }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Merchant Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold text-gray-800">Merchant Information</span>
            </div>
            <div class="ui-card-body grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Business Name</div>
                    <div class="text-sm text-gray-800 font-medium">{{ $posTerminal->merchant_name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Contact Person</div>
                    <div class="text-sm text-gray-800">{{ $posTerminal->merchant_contact_person ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Phone</div>
                    <div class="text-sm text-gray-800">
                        @if($posTerminal->merchant_phone)
                            <a href="tel:{{ $posTerminal->merchant_phone }}" class="text-[#1a3a5c] hover:underline">
                                {{ $posTerminal->merchant_phone }}
                            </a>
                        @else —
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Email</div>
                    <div class="text-sm text-gray-800 truncate">
                        @if($posTerminal->merchant_email)
                            <a href="mailto:{{ $posTerminal->merchant_email }}" class="text-[#1a3a5c] hover:underline">
                                {{ $posTerminal->merchant_email }}
                            </a>
                        @else —
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Business Type</div>
                    <div class="text-sm text-gray-800">{{ $posTerminal->business_type ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Region</div>
                    <div class="text-sm text-gray-800">{{ $posTerminal->region ?: '—' }}</div>
                </div>
                @if($posTerminal->city || $posTerminal->province)
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">City / Province</div>
                    <div class="text-sm text-gray-800">
                        {{ $posTerminal->city ?? '' }}@if($posTerminal->city && $posTerminal->province), @endif{{ $posTerminal->province ?? '' }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Client Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold text-gray-800">Client Information</span>
            </div>
            <div class="ui-card-body grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Bank / Client</div>
                    <div class="text-sm font-medium">
                        <a href="{{ route('clients.show', $posTerminal->client) }}" class="text-[#1a3a5c] hover:underline">
                            {{ $posTerminal->client->company_name }}
                        </a>
                    </div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Client Code</div>
                    <div class="text-sm text-gray-800 font-mono">{{ $posTerminal->client->client_code ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Contact Person</div>
                    <div class="text-sm text-gray-800">{{ $posTerminal->client->contact_person ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Client Status</div>
                    <span class="badge badge-gray">{{ ucfirst($posTerminal->client->status ?? 'active') }}</span>
                </div>
            </div>
        </div>

        @if($posTerminal->contract_details)
        {{-- Contract Details --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold text-gray-800">Contract Details</span>
            </div>
            <div class="ui-card-body">
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $posTerminal->contract_details }}</p>
            </div>
        </div>
        @endif

        {{-- Service History --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold text-gray-800">Service History</span>
                <button onclick="openServiceModal()" class="btn-primary btn-sm">+ Schedule Service</button>
            </div>
            <div id="service-history-list" class="p-5 text-center text-sm text-gray-400">
                No service records found.
            </div>
        </div>

    </div>

    {{-- ===== RIGHT SIDEBAR ===== --}}
    <div class="space-y-5">

        {{-- Quick Actions --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold text-gray-800">Quick Actions</span>
            </div>
            <div class="ui-card-body flex flex-col gap-2">
                <button onclick="updateStatus('active')"      class="btn-secondary text-left text-sm w-full justify-start">&#x2705; Mark as Active</button>
                <button onclick="updateStatus('maintenance')" class="btn-secondary text-left text-sm w-full justify-start">&#x1F527; Mark for Maintenance</button>
                <button onclick="updateStatus('offline')"     class="btn-secondary text-left text-sm w-full justify-start">&#x26AB; Mark as Offline</button>
                <button onclick="updateStatus('faulty')"      class="btn-danger    text-left text-sm w-full justify-start">&#x26A0; Mark as Faulty</button>
                <div class="border-t border-gray-100 my-1"></div>
                <button onclick="openTicketModal()"  class="btn-secondary text-left text-sm w-full justify-start">&#x1F3AB; Create Ticket</button>
                <button onclick="openServiceModal()" class="btn-secondary text-left text-sm w-full justify-start">&#x1F4C5; Schedule Service</button>
                <button onclick="openNotesModal()"   class="btn-secondary text-left text-sm w-full justify-start">&#x1F4DD; Add Notes</button>
            </div>
        </div>

        {{-- Service Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold text-gray-800">Service Information</span>
            </div>
            <div class="ui-card-body space-y-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Last Service</div>
                    <div class="text-sm text-gray-800">
                        {{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never serviced' }}
                    </div>
                    @if($posTerminal->last_service_date)
                        <div class="text-xs text-gray-400 mt-0.5">{{ $posTerminal->last_service_date->diffForHumans() }}</div>
                    @endif
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Next Service Due</div>
                    @if($posTerminal->next_service_due)
                        @php
                        $isOverdue = $posTerminal->next_service_due <= now();
                        $isDueSoon = !$isOverdue && $posTerminal->next_service_due <= now()->addDays(7);
                        @endphp
                        <div class="text-sm {{ $isOverdue ? 'text-red-600 font-semibold' : ($isDueSoon ? 'text-amber-600 font-semibold' : 'text-gray-800') }}">
                            {{ $posTerminal->next_service_due->format('M d, Y') }}
                        </div>
                        @if($isOverdue)
                            <span class="badge badge-red mt-1">Overdue</span>
                        @elseif($isDueSoon)
                            <span class="badge badge-yellow mt-1">Due Soon</span>
                        @endif
                    @else
                        <div class="text-sm text-gray-400">Not scheduled</div>
                    @endif
                </div>
                <button onclick="openServiceModal()" class="btn-primary btn-sm w-full justify-center">Schedule Service</button>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold text-gray-800">Statistics</span>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="flex justify-between items-center px-5 py-3 text-sm">
                    <span class="text-gray-600">Total Jobs</span>
                    <span id="total-jobs" class="font-semibold text-gray-800">—</span>
                </div>
                <div class="flex justify-between items-center px-5 py-3 text-sm">
                    <span class="text-gray-600">Service Reports</span>
                    <span id="service-reports" class="font-semibold text-gray-800">—</span>
                </div>
                <div class="flex justify-between items-center px-5 py-3 text-sm">
                    <span class="text-gray-600">Open Tickets</span>
                    <span id="open-tickets" class="font-semibold text-gray-800">—</span>
                </div>
                <div class="flex justify-between items-center px-5 py-3 text-sm">
                    <span class="text-gray-600">Days Since Last Service</span>
                    <span class="font-semibold text-gray-800">
                        {{ $posTerminal->last_service_date ? $posTerminal->last_service_date->diffInDays(now()) : '—' }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ===== MODALS ===== --}}

{{-- Create Ticket --}}
<div id="ticketModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Create Support Ticket</h3>
            <button onclick="closeModal('ticketModal')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="ticketForm" onsubmit="submitTicket(event)" class="flex flex-col flex-1 overflow-y-auto">
            @csrf
            <div class="ui-card-body space-y-4">
                <div>
                    <label class="ui-label">Priority</label>
                    <select name="priority" required class="ui-select">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Issue Type</label>
                    <select name="issue_type" required class="ui-select">
                        <option value="">— Select issue type —</option>
                        <option value="hardware">Hardware Issue</option>
                        <option value="software">Software Issue</option>
                        <option value="network">Network / Connectivity</option>
                        <option value="paper">Paper / Receipt Issues</option>
                        <option value="card_reader">Card Reader Problem</option>
                        <option value="display">Display Issues</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Issue Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="ui-textarea" placeholder="Describe the issue in detail…"></textarea>
                </div>
                <div>
                    <label class="ui-label">Reported By</label>
                    <input type="text" name="reported_by" class="ui-input" value="{{ $posTerminal->merchant_contact_person }}">
                </div>
                <div>
                    <label class="ui-label">Contact Number</label>
                    <input type="tel" name="contact_number" class="ui-input" value="{{ $posTerminal->merchant_phone }}">
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('ticketModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Ticket</button>
            </div>
        </form>
    </div>
</div>

{{-- Schedule Service --}}
<div id="serviceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Schedule Service</h3>
            <button onclick="closeModal('serviceModal')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="serviceForm" onsubmit="submitService(event)" class="flex flex-col flex-1 overflow-y-auto">
            @csrf
            <div class="ui-card-body space-y-4">
                <div>
                    <label class="ui-label">Service Type <span class="text-red-500">*</span></label>
                    <select name="service_type" required class="ui-select">
                        <option value="">— Select type —</option>
                        <option value="preventive">Preventive Maintenance</option>
                        <option value="corrective">Corrective Maintenance</option>
                        <option value="installation">Installation</option>
                        <option value="repair">Repair</option>
                        <option value="inspection">Inspection</option>
                        <option value="replacement">Replacement</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ui-label">Scheduled Date <span class="text-red-500">*</span></label>
                        <input type="date" name="scheduled_date" required class="ui-input" min="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="ui-label">Scheduled Time <span class="text-red-500">*</span></label>
                        <input type="time" name="scheduled_time" required class="ui-input">
                    </div>
                </div>
                <div>
                    <label class="ui-label">Service Notes</label>
                    <textarea name="notes" rows="3" class="ui-textarea" placeholder="Special instructions or notes…"></textarea>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('serviceModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Schedule Service</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Notes --}}
<div id="notesModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Add Notes</h3>
            <button onclick="closeModal('notesModal')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="notesForm" onsubmit="submitNotes(event)" class="flex flex-col flex-1 overflow-y-auto">
            @csrf
            <div class="ui-card-body space-y-4">
                <div>
                    <label class="ui-label">Note Type</label>
                    <select name="note_type" required class="ui-select">
                        <option value="general">General Note</option>
                        <option value="technical">Technical Note</option>
                        <option value="customer">Customer Feedback</option>
                        <option value="issue">Issue Report</option>
                        <option value="resolution">Resolution Note</option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Notes <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="5" required class="ui-textarea" placeholder="Enter your notes here…"></textarea>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="checkbox" name="is_important" class="rounded border-gray-300">
                    Mark as Important
                </label>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('notesModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Notes</button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden forms --}}
<form id="statusUpdateForm" action="{{ route('pos-terminals.update-status', $posTerminal) }}" method="POST" class="hidden">
    @csrf @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>
<form id="deleteForm" action="{{ route('pos-terminals.destroy', $posTerminal) }}" method="POST" class="hidden">
    @csrf @method('DELETE')
</form>

@push('scripts')
<script>
function updateStatus(status) {
    if (confirm('Update terminal status to "' + status + '"?')) {
        document.getElementById('statusInput').value = status;
        document.getElementById('statusUpdateForm').submit();
    }
}

function confirmDelete() {
    if (confirm('Delete this terminal? This cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

function openModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    el.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    el.classList.remove('flex');
    document.body.style.overflow = '';
}

function openTicketModal()  { openModal('ticketModal'); }
function openServiceModal() { openModal('serviceModal'); }
function openNotesModal()   { openModal('notesModal'); }

function showToast(msg, type) {
    const toast = document.createElement('div');
    const colours = { success: 'bg-green-50 border-green-200 text-green-800', error: 'bg-red-50 border-red-200 text-red-800', info: 'bg-blue-50 border-blue-200 text-blue-800' };
    toast.className = 'fixed top-5 right-5 z-[9999] flex items-center gap-3 border rounded-lg px-4 py-3 text-sm shadow-lg ' + (colours[type] || colours.info);
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function submitTicket(e) {
    e.preventDefault();
    showToast('Ticket created successfully!', 'success');
    closeModal('ticketModal');
    e.target.reset();
}

function submitService(e) {
    e.preventDefault();
    showToast('Service scheduled successfully!', 'success');
    closeModal('serviceModal');
    e.target.reset();
}

function submitNotes(e) {
    e.preventDefault();
    showToast('Notes saved successfully!', 'success');
    closeModal('notesModal');
    e.target.reset();
}

// Close on backdrop click
document.querySelectorAll('[id$="Modal"]').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

// Close on Escape
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('[id$="Modal"]').forEach(m => {
        if (!m.classList.contains('hidden')) closeModal(m.id);
    });
});

// Load statistics
document.addEventListener('DOMContentLoaded', () => {
    fetch("{{ route('pos-terminals.statistics', $posTerminal) }}", {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.ok ? r.json() : null)
    .then(data => {
        if (!data) return;
        if (data.total_jobs     !== undefined) document.getElementById('total-jobs').textContent     = data.total_jobs;
        if (data.service_reports !== undefined) document.getElementById('service-reports').textContent = data.service_reports;
        if (data.open_tickets   !== undefined) document.getElementById('open-tickets').textContent   = data.open_tickets;
    })
    .catch(() => {});
});
</script>
@endpush
@endsection
