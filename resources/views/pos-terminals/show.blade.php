@extends('layouts.app')
@section('title', 'Terminal — ' . $posTerminal->terminal_id)

@push('styles')
<style>
/* ── POS terminals · show ──────────────────────────────── */
.pt-show .pt-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
.pt-show .pt-head-main { display: flex; align-items: center; gap: 12px; min-width: 0; }
.pt-show .pt-id-chip { display: inline-flex; align-items: center; height: 28px; padding: 0 10px; border-radius: 6px; background: var(--mv-surface); border: 1px solid var(--mv-line-strong); font-size: 13px; font-weight: 500; color: var(--mv-ink); white-space: nowrap; flex-shrink: 0; }
.pt-show .pt-head-name { font-size: 15px; font-weight: 600; color: var(--mv-ink); line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pt-show .pt-head-sub { font-size: 12.5px; color: var(--mv-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pt-show .pt-head-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.pt-show .pt-btn { height: 34px; padding: 0 12px; font-size: 13px; }
.pt-show .pt-btn-sm { height: 30px; padding: 0 10px; font-size: 12.5px; }
.pt-show .pt-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }

.pt-show .ui-card-header { padding: 12px 18px; }
.pt-show .ui-card-header h2 { font-size: 14px; font-weight: 600; margin: 0; }

/* Key / value grid */
.pt-show .pt-kv { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px 24px; padding: 16px 18px 18px; margin: 0; }
.pt-show .pt-kv dt, .pt-show .pt-k { font-size: 12px; font-weight: 400; color: var(--mv-muted); margin: 0 0 3px; }
.pt-show .pt-kv dd, .pt-show .pt-v { font-size: 13.5px; color: var(--mv-ink); margin: 0; overflow-wrap: anywhere; }
.pt-show .pt-kv-wide { grid-column: 1 / -1; padding-top: 14px; border-top: 1px solid var(--mv-line); }
.pt-show .pt-empty-val { color: var(--mv-muted); }
.pt-show .pt-link { color: var(--mv-accent-ink); text-decoration: none; }
.pt-show .pt-link:hover { text-decoration: underline; }
@media (max-width: 640px) { .pt-show .pt-kv { grid-template-columns: repeat(2, minmax(0, 1fr)); } }

.pt-show .pt-stack > * + * { margin-top: 16px; }
.pt-show .pt-v-sub { font-size: 12px; color: var(--mv-muted); margin-top: 2px; }
.pt-show .pt-v-row { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
.pt-show .pt-v-crit { color: var(--mv-crit); font-weight: 600; }
.pt-show .pt-v-warn { color: var(--mv-warn); font-weight: 600; }
.pt-show .pt-text { margin: 0; font-size: 13.5px; color: var(--mv-ink-2); white-space: pre-line; }

/* Status chips */
.pt-show .status-badge { gap: 6px; padding: 2px 8px; font-size: 12px; line-height: 18px; }
.pt-show .status-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

/* Quick actions */
.pt-show .pt-qa { display: flex; flex-direction: column; padding: 6px; }
.pt-show .pt-qa button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 10px; border: 0; border-radius: 7px; background: transparent; font: inherit; font-size: 13.5px; color: var(--mv-ink); text-align: left; cursor: pointer; }
.pt-show .pt-qa button:hover { background: var(--mv-surface-2); }
.pt-show .pt-qa button > .mv-i:first-child { color: var(--mv-muted); }
.pt-show .pt-qa span { flex: 1; }
.pt-show .pt-qa .pt-qa-chev { width: 15px; height: 15px; color: var(--mv-line-strong); }

/* Statistics list */
.pt-show .pt-stat-list { margin: 0; }
.pt-show .pt-stat-list > div { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 18px; font-size: 13px; }
.pt-show .pt-stat-list > div + div { border-top: 1px solid var(--mv-line); }
.pt-show .pt-stat-list dt { font-weight: 400; color: var(--mv-ink-2); margin: 0; }
.pt-show .pt-stat-list dd { margin: 0; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }

/* Empty state */
.pt-show .pt-empty { padding: 32px 16px; text-align: center; color: var(--mv-muted); font-size: 13px; }
.pt-show .pt-empty .mv-i { display: block; width: 32px; height: 32px; margin: 0 auto 8px; color: var(--mv-line-strong); }

/* Modals */
.pt-show .pt-modal { position: fixed; inset: 0; z-index: 1100; display: flex; align-items: center; justify-content: center; padding: 24px 16px; background: rgba(22, 32, 44, .45); }
.pt-show .pt-modal-card { width: 100%; max-width: 520px; max-height: calc(100vh - 48px); display: flex; flex-direction: column; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 16px 40px rgba(22, 32, 44, .14); overflow: hidden; }
.pt-show .pt-modal-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
.pt-show .pt-modal-head h3 { margin: 0; font-size: 14.5px; font-weight: 600; }
.pt-show .pt-modal-form { display: flex; flex-direction: column; flex: 1; min-height: 0; margin: 0; }
.pt-show .pt-modal-body { padding: 18px; overflow-y: auto; }
.pt-show .pt-modal-body > * + * { margin-top: 14px; }
.pt-show .pt-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid var(--mv-line); }
.pt-show .pt-icon-btn { width: 30px; height: 30px; display: grid; place-items: center; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); cursor: pointer; }
.pt-show .pt-icon-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.pt-show .pt-req { color: var(--mv-crit); }
.pt-show .ui-input { height: 38px; padding: 0 12px; font-size: 13.5px; }
.pt-show .ui-select { height: 38px; padding: 0 32px 0 12px; font-size: 13.5px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236A7686' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; }
.pt-show .ui-textarea { padding: 9px 12px; font-size: 13.5px; }
.pt-show .pt-check { display: flex; align-items: center; gap: 8px; margin: 0; font-size: 13px; color: var(--mv-ink-2); cursor: pointer; }
.pt-show .pt-check input { width: 16px; height: 16px; margin: 0; accent-color: var(--mv-accent); }
</style>
@endpush

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

<div class="pt-show">

{{-- Header --}}
<div class="pt-head">
    <div class="pt-head-main">
        <span class="pt-id-chip mv-mono">{{ $posTerminal->terminal_id }}</span>
        <div class="min-w-0">
            <div class="pt-head-name">{{ $posTerminal->merchant_name ?? '—' }}</div>
            <div class="pt-head-sub">{{ $posTerminal->client->company_name ?? '—' }}</div>
        </div>
        <span class="status-badge {{ $statusBadge }}">{{ ucfirst($posTerminal->status ?? 'unknown') }}</span>
    </div>
    <div class="pt-head-actions">
        <a href="{{ route('pos-terminals.index') }}" class="btn-secondary pt-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back
        </a>
        <button type="button" onclick="confirmDelete()" class="btn-danger pt-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-trash"/></svg> Delete
        </button>
        <a href="{{ route('pos-terminals.edit', $posTerminal) }}" class="btn-primary pt-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit
        </a>
    </div>
</div>

{{-- Main grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

    {{-- ===== LEFT / MAIN (2 cols) ===== --}}
    <div class="lg:col-span-2 pt-col">

        {{-- Terminal Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2>Terminal Information</h2>
            </div>
            <dl class="pt-kv">
                <div>
                    <dt>Terminal ID</dt>
                    <dd class="mv-mono">{{ $posTerminal->terminal_id }}</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd><span class="status-badge {{ $statusBadge }}">{{ ucfirst($posTerminal->status ?? 'unknown') }}</span></dd>
                </div>
                <div>
                    <dt>Model</dt>
                    <dd>{{ $posTerminal->terminal_model ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Serial Number</dt>
                    <dd class="mv-mono">{{ $posTerminal->serial_number ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Installation Date</dt>
                    <dd>{{ $posTerminal->installation_date ? $posTerminal->installation_date->format('M d, Y') : '—' }}</dd>
                </div>
                <div>
                    <dt>Last Service</dt>
                    <dd>{{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never' }}</dd>
                </div>
                @if($posTerminal->physical_address)
                <div class="pt-kv-wide">
                    <dt>Physical Address</dt>
                    <dd>{{ $posTerminal->physical_address }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Merchant Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2>Merchant Information</h2>
            </div>
            <dl class="pt-kv">
                <div>
                    <dt>Business Name</dt>
                    <dd>{{ $posTerminal->merchant_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt>Contact Person</dt>
                    <dd>{{ $posTerminal->merchant_contact_person ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Phone</dt>
                    <dd>
                        @if($posTerminal->merchant_phone)
                            <a href="tel:{{ $posTerminal->merchant_phone }}" class="pt-link">{{ $posTerminal->merchant_phone }}</a>
                        @else —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Email</dt>
                    <dd>
                        @if($posTerminal->merchant_email)
                            <a href="mailto:{{ $posTerminal->merchant_email }}" class="pt-link">{{ $posTerminal->merchant_email }}</a>
                        @else —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Business Type</dt>
                    <dd>{{ $posTerminal->business_type ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Region</dt>
                    <dd>{{ $posTerminal->region ?: '—' }}</dd>
                </div>
                @if($posTerminal->city || $posTerminal->province)
                <div>
                    <dt>City / Province</dt>
                    <dd>{{ $posTerminal->city ?? '' }}@if($posTerminal->city && $posTerminal->province), @endif{{ $posTerminal->province ?? '' }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Client Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2>Client Information</h2>
            </div>
            <dl class="pt-kv">
                <div>
                    <dt>Bank / Client</dt>
                    <dd>
                        <a href="{{ route('clients.show', $posTerminal->client) }}" class="pt-link">{{ $posTerminal->client->company_name }}</a>
                    </dd>
                </div>
                <div>
                    <dt>Client Code</dt>
                    <dd class="mv-mono">{{ $posTerminal->client->client_code ?? '—' }}</dd>
                </div>
                <div>
                    <dt>Contact Person</dt>
                    <dd>{{ $posTerminal->client->contact_person ?? '—' }}</dd>
                </div>
                <div>
                    <dt>Client Status</dt>
                    <dd><span class="status-badge {{ ($posTerminal->client->status ?? 'active') === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($posTerminal->client->status ?? 'active') }}</span></dd>
                </div>
            </dl>
        </div>

        @if($posTerminal->contract_details)
        {{-- Contract Details --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2>Contract Details</h2>
            </div>
            <div class="ui-card-body">
                <p class="pt-text">{{ $posTerminal->contract_details }}</p>
            </div>
        </div>
        @endif

        {{-- Service History --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2>Service History</h2>
                <button type="button" onclick="openServiceModal()" class="btn-secondary pt-btn-sm">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Schedule Service
                </button>
            </div>
            <div id="service-history-list" class="pt-empty">
                <svg class="mv-i" aria-hidden="true"><use href="#i-history"/></svg>
                No service records found.
            </div>
        </div>

    </div>

    {{-- ===== RIGHT SIDEBAR ===== --}}
    <div class="pt-col">

        {{-- Quick Actions --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2>Quick Actions</h2>
            </div>
            <div class="pt-qa">
                <button type="button" onclick="openTicketModal()">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-ticket"/></svg>
                    <span>Create Ticket</span>
                    <svg class="mv-i pt-qa-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                </button>
                <button type="button" onclick="openServiceModal()">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg>
                    <span>Schedule Service</span>
                    <svg class="mv-i pt-qa-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                </button>
                <button type="button" onclick="openNotesModal()">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg>
                    <span>Add Notes</span>
                    <svg class="mv-i pt-qa-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                </button>
            </div>
        </div>

        {{-- Service Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2>Service Information</h2>
            </div>
            <div class="ui-card-body pt-stack">
                <div>
                    <div class="pt-k">Last Service</div>
                    <div class="pt-v">
                        {{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never serviced' }}
                    </div>
                    @if($posTerminal->last_service_date)
                        <div class="pt-v-sub">{{ $posTerminal->last_service_date->diffForHumans() }}</div>
                    @endif
                </div>
                <div>
                    <div class="pt-k">Next Service Due</div>
                    @if($posTerminal->next_service_due)
                        @php
                        $isOverdue = $posTerminal->next_service_due <= now();
                        $isDueSoon = !$isOverdue && $posTerminal->next_service_due <= now()->addDays(7);
                        @endphp
                        <div class="pt-v pt-v-row">
                            <span class="{{ $isOverdue ? 'pt-v-crit' : ($isDueSoon ? 'pt-v-warn' : '') }}">{{ $posTerminal->next_service_due->format('M d, Y') }}</span>
                            @if($isOverdue)
                                <span class="status-badge badge-red">Overdue</span>
                            @elseif($isDueSoon)
                                <span class="status-badge badge-yellow">Due Soon</span>
                            @endif
                        </div>
                    @else
                        <div class="pt-v pt-empty-val">Not scheduled</div>
                    @endif
                </div>
                <button type="button" onclick="openServiceModal()" class="btn-secondary pt-btn w-full justify-center">Schedule Service</button>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2>Statistics</h2>
            </div>
            <dl class="pt-stat-list">
                <div>
                    <dt>Total Jobs</dt>
                    <dd id="total-jobs">—</dd>
                </div>
                <div>
                    <dt>Service Reports</dt>
                    <dd id="service-reports">—</dd>
                </div>
                <div>
                    <dt>Open Tickets</dt>
                    <dd id="open-tickets">—</dd>
                </div>
                <div>
                    <dt>Days Since Last Service</dt>
                    <dd>{{ $posTerminal->last_service_date ? (int) $posTerminal->last_service_date->diffInDays(now()) : '—' }}</dd>
                </div>
            </dl>
        </div>

    </div>
</div>

{{-- ===== MODALS ===== --}}

{{-- Create Ticket --}}
<div id="ticketModal" class="pt-modal hidden" role="dialog" aria-modal="true" aria-labelledby="ticketModalHeading">
    <div class="pt-modal-card">
        <div class="pt-modal-head">
            <h3 id="ticketModalHeading">Create Support Ticket</h3>
            <button type="button" onclick="closeModal('ticketModal')" class="pt-icon-btn" title="Close" aria-label="Close">
                <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
            </button>
        </div>
        <form id="ticketForm" onsubmit="submitTicket(event)" class="pt-modal-form">
            @csrf
            <div class="pt-modal-body">
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
                    <label class="ui-label">Issue Description <span class="pt-req">*</span></label>
                    <textarea name="description" rows="4" required class="ui-textarea" placeholder="Describe the issue in detail…"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ui-label">Reported By</label>
                        <input type="text" name="reported_by" class="ui-input" value="{{ $posTerminal->merchant_contact_person }}">
                    </div>
                    <div>
                        <label class="ui-label">Contact Number</label>
                        <input type="tel" name="contact_number" class="ui-input" value="{{ $posTerminal->merchant_phone }}">
                    </div>
                </div>
            </div>
            <div class="pt-modal-foot">
                <button type="button" onclick="closeModal('ticketModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Ticket</button>
            </div>
        </form>
    </div>
</div>

{{-- Schedule Service --}}
<div id="serviceModal" class="pt-modal hidden" role="dialog" aria-modal="true" aria-labelledby="serviceModalHeading">
    <div class="pt-modal-card">
        <div class="pt-modal-head">
            <h3 id="serviceModalHeading">Schedule Service</h3>
            <button type="button" onclick="closeModal('serviceModal')" class="pt-icon-btn" title="Close" aria-label="Close">
                <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
            </button>
        </div>
        <form id="serviceForm" onsubmit="submitService(event)" class="pt-modal-form">
            @csrf
            <div class="pt-modal-body">
                <div>
                    <label class="ui-label">Service Type <span class="pt-req">*</span></label>
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
                        <label class="ui-label">Scheduled Date <span class="pt-req">*</span></label>
                        <input type="date" name="scheduled_date" required class="ui-input" min="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="ui-label">Scheduled Time <span class="pt-req">*</span></label>
                        <input type="time" name="scheduled_time" required class="ui-input">
                    </div>
                </div>
                <div>
                    <label class="ui-label">Service Notes</label>
                    <textarea name="notes" rows="3" class="ui-textarea" placeholder="Special instructions or notes…"></textarea>
                </div>
            </div>
            <div class="pt-modal-foot">
                <button type="button" onclick="closeModal('serviceModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Schedule Service</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Notes --}}
<div id="notesModal" class="pt-modal hidden" role="dialog" aria-modal="true" aria-labelledby="notesModalHeading">
    <div class="pt-modal-card">
        <div class="pt-modal-head">
            <h3 id="notesModalHeading">Add Notes</h3>
            <button type="button" onclick="closeModal('notesModal')" class="pt-icon-btn" title="Close" aria-label="Close">
                <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
            </button>
        </div>
        <form id="notesForm" onsubmit="submitNotes(event)" class="pt-modal-form">
            @csrf
            <div class="pt-modal-body">
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
                    <label class="ui-label">Notes <span class="pt-req">*</span></label>
                    <textarea name="notes" rows="5" required class="ui-textarea" placeholder="Enter your notes here…"></textarea>
                </div>
                <label class="pt-check">
                    <input type="checkbox" name="is_important">
                    Mark as Important
                </label>
            </div>
            <div class="pt-modal-foot">
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

</div>

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
