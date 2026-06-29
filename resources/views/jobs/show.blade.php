{{-- resources/views/jobs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Assignment '.$assignment->assignment_id)

@section('content')
@php
$sBadge = [
    'completed'   => 'badge-green',
    'in_progress' => 'badge-blue',
    'assigned'    => 'badge-gray',
    'cancelled'   => 'badge-red',
    'approved'    => 'badge-green',
    'pending'     => 'badge-yellow',
][$assignment->status] ?? 'badge-gray';
$pBadge = [
    'emergency' => 'badge-red',
    'high'      => 'badge-orange',
    'normal'    => 'badge-blue',
    'low'       => 'badge-gray',
][$assignment->priority] ?? 'badge-gray';
@endphp
<div>
    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('jobs.index') }}" class="btn-secondary">&#x2190; Back to Assignments</a>
    </div>

    {{-- Assignment Header --}}
    <div class="ui-card mb-4">
        <div class="ui-card-body">
            {{-- Title / Status / Actions row --}}
            <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                <div class="flex-1 min-w-[280px]">
                    <div class="flex gap-2 flex-wrap">
                        <span class="badge {{ $sBadge }}">{{ \Illuminate\Support\Str::headline($assignment->status) }}</span>
                        <span class="badge {{ $pBadge }}">{{ \Illuminate\Support\Str::headline($assignment->priority) }}</span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="flex gap-3 items-center flex-wrap">
                        {{-- Register Terminal (always available to managers/technicians on this job) --}}
                        @if(auth()->user()->hasPermission('manage_terminals') || auth()->user()->hasPermission('all') || auth()->user()->hasPermission('manage_jobs') || (auth()->user()->id == $assignment->technician_id))
                        <button type="button" class="btn-secondary" onclick="document.getElementById('registerTerminalModal').classList.remove('hidden')" style="border-color:#6366f1;color:#6366f1;">
                            &#x2B; Register Terminal
                        </button>
                        @endif

                        {{-- Transfer button (only when transferrable) --}}
                        @if(!in_array($assignment->status, ['completed','cancelled','reassigned']) && (auth()->user()->hasPermission('manage_jobs') || auth()->user()->hasPermission('all') || auth()->user()->id == $assignment->technician_id))
                        <button type="button" class="btn-secondary" onclick="document.getElementById('transferModal').classList.remove('hidden')" style="border-color:#f59e0b;color:#b45309;">
                            &#8644; Transfer
                        </button>
                        @endif

                        {{-- Status action --}}
                        @if(auth()->user()->can('update', $assignment) || auth()->user()->hasPermission('manage_jobs') || auth()->user()->hasPermission('all') || (auth()->user()->id == $assignment->technician_id))
                        @if($assignment->status === 'assigned')
                            <button type="button" class="btn-primary" onclick="updateStatus({{ $assignment->id }}, 'in_progress', this)">
                                &#x25B6; Start Assignment
                            </button>
                        @elseif($assignment->status === 'in_progress')
                            <button type="button" class="btn-success" onclick="updateStatus({{ $assignment->id }}, 'completed', this)">
                                &#x2713; Mark Complete
                            </button>
                        @elseif($assignment->status === 'completed')
                            <span class="text-green-600 font-semibold text-sm flex items-center gap-1">
                                &#x2705; Assignment Completed
                            </span>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Assignment Details --}}
                <div>
                    <h6 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 pb-2 border-b border-gray-100">Assignment Details</h6>
                    <div class="space-y-0">
                        <div class="flex justify-between items-start py-2.5 border-b border-gray-50">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Technician</span>
                            <div class="text-sm text-gray-800 text-right flex-1">
                                @if($assignment->technician)
                                <div class="flex items-center gap-2 justify-end">
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-xs">
                                        {{ substr($assignment->technician->first_name,0,1) }}{{ substr($assignment->technician->last_name,0,1) }}
                                    </div>
                                    <span>{{ $assignment->technician->first_name }} {{ $assignment->technician->last_name }}</span>
                                </div>
                                @else
                                <span class="text-gray-400">Unassigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-start py-2.5 border-b border-gray-50">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Client</span>
                            <span class="text-sm text-gray-800 text-right">{{ $assignment->client->company_name ?? '&#x2014;' }}</span>
                        </div>
                        @if($assignment->project)
                        <div class="flex justify-between items-start py-2.5">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Project</span>
                            <span class="text-sm text-gray-800 text-right">{{ $assignment->project->project_name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Service Information --}}
                <div>
                    <h6 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 pb-2 border-b border-gray-100">Service Information</h6>
                    <div class="space-y-0">
                        <div class="flex justify-between items-start py-2.5 border-b border-gray-50">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Scheduled Date</span>
                            <div class="text-sm text-gray-800 text-right">
                                @if($assignment->scheduled_date)
                                <div>{{ $assignment->scheduled_date->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $assignment->scheduled_date->diffForHumans() }}</div>
                                @else
                                <span class="text-gray-400">Not scheduled</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-start py-2.5 border-b border-gray-50">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Service Type</span>
                            <span class="text-sm text-gray-800 text-right">{{ \Illuminate\Support\Str::headline($assignment->service_type) }}</span>
                        </div>
                        <div class="flex justify-between items-start py-2.5">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Created</span>
                            <span class="text-sm text-gray-800 text-right">{{ $assignment->created_at?->format('M j, Y') ?? '&#x2014;' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Terminals Section --}}
    @if($terminals->isEmpty())
    <div class="ui-card mb-4">
        <div class="ui-card-body">
            <div class="empty-state">
                <div class="empty-state-icon">&#x1F5A5;</div>
                <p class="empty-state-msg">No terminals assigned</p>
                <p class="text-sm text-gray-400 mt-1">This assignment doesn't have any terminals associated with it.</p>
            </div>
        </div>
    </div>
    @else
    <div class="ui-card mb-4">
        <div class="ui-card-header">
            <h6 class="text-sm font-semibold text-gray-700 m-0">Terminals</h6>
            <div class="flex items-center gap-3">
                <span class="badge badge-gray">{{ $terminals->count() }} {{ \Illuminate\Support\Str::plural('terminal', $terminals->count()) }}</span>
                <div class="relative">
                    <input type="text" id="terminalSearch" placeholder="Search terminals..."
                           class="ui-input !py-1.5 !text-xs pr-7 min-w-[200px]">
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Merchant</th>
                        <th>Terminal ID</th>
                        <th>Address</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="terminalsTableBody">
                @foreach($terminals as $t)
                @php
                $ts = strtolower($t->current_status ?? $t->status ?? 'unknown');
                $tb = $ts === 'active' ? 'badge-green' : ($ts === 'inactive' ? 'badge-red' : 'badge-gray');
                @endphp
                <tr class="terminal-row" data-searchable="{{ strtolower($t->merchant_name . ' ' . $t->terminal_id . ' ' . ($t->physical_address ?? $t->address ?? '') . ' ' . ($t->city ?? '') . ' ' . ($t->province ?? '')) }}">
                    <td>
                        <div class="font-semibold text-gray-800 text-sm">{{ $t->merchant_name ?? '&#x2014;' }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $t->client->company_name ?? '&#x2014;' }}</div>
                    </td>
                    <td>
                        <code class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded text-xs border border-gray-200 font-mono">{{ $t->terminal_id }}</code>
                    </td>
                    <td class="text-sm text-gray-700">{{ $t->physical_address ?? $t->address ?? '&#x2014;' }}</td>
                    <td class="text-sm text-gray-700">
                        {{ $t->city ?? '&#x2014;' }}@if($t->province)<span class="text-gray-400">, {{ $t->province }}</span>@endif
                    </td>
                    <td><span class="badge {{ $tb }}">{{ \Illuminate\Support\Str::headline($t->current_status ?? $t->status ?? 'unknown') }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div id="noResults" class="hidden text-center py-6 border-t border-gray-100">
            <p class="text-sm text-gray-400">No terminals match your search criteria</p>
        </div>
    </div>
    @endif

    {{-- Live Site Visits --}}
    <div class="ui-card mb-4" id="liveSiteVisitsCard">
        <div class="ui-card-header">
            <h6 class="text-sm font-semibold text-gray-700 m-0">Live Site Visits</h6>
            <span class="badge badge-gray" id="liveVisitCount">0</span>
        </div>
        <div class="ui-card-body">
            <div id="liveVisitsList" class="max-h-[420px] overflow-y-auto">
                <div class="text-sm text-gray-400">Waiting for updates...</div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($assignment->notes)
    <div class="ui-card">
        <div class="ui-card-header">
            <h6 class="text-sm font-semibold text-gray-700 m-0">Notes</h6>
        </div>
        <div class="ui-card-body">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $assignment->notes }}</div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- TRANSFER MODAL --}}
    {{-- ======================================================== --}}
    <div id="transferModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,.5);">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">&#8644; Transfer Assignment</h3>
                <button onclick="document.getElementById('transferModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="ui-label">Transfer to <span class="text-red-500">*</span></label>
                    <select id="transferTechnicianId" class="ui-select">
                        <option value="">— Select technician —</option>
                        @foreach($technicians as $tech)
                            @if($tech->id !== $assignment->technician_id)
                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ui-label">Reason <span class="text-red-500">*</span></label>
                    <textarea id="transferReason" rows="3" class="ui-input resize-y" placeholder="Why is this job being transferred?"></textarea>
                </div>
                <div>
                    <label class="ui-label">Notes for receiving technician</label>
                    <textarea id="transferNotes" rows="2" class="ui-input resize-y" placeholder="Any additional handover notes…"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button onclick="document.getElementById('transferModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
                <button id="transferSubmitBtn" onclick="submitTransfer()" class="btn-primary" style="background:#f59e0b;border-color:#f59e0b;">Transfer Job</button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- REGISTER TERMINAL MODAL --}}
    {{-- ======================================================== --}}
    <div id="registerTerminalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,.5);">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4" style="max-height:90vh;overflow-y:auto;">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="text-base font-semibold text-gray-800">&#x2B; Register On-Site Terminal</h3>
                <button onclick="document.getElementById('registerTerminalModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="ui-label">Terminal ID (TID) <span class="text-red-500">*</span></label>
                        <input type="text" id="regTerminalId" class="ui-input" placeholder="e.g. TID-00123">
                    </div>
                    <div>
                        <label class="ui-label">Merchant Name <span class="text-red-500">*</span></label>
                        <input type="text" id="regMerchantName" class="ui-input" placeholder="e.g. Joe's Butchery">
                    </div>
                    <div>
                        <label class="ui-label">Contact Person</label>
                        <input type="text" id="regContactPerson" class="ui-input" placeholder="e.g. Joe Moyo">
                    </div>
                    <div>
                        <label class="ui-label">Phone</label>
                        <input type="text" id="regPhone" class="ui-input" placeholder="e.g. 0771234567">
                    </div>
                    <div>
                        <label class="ui-label">City</label>
                        <input type="text" id="regCity" class="ui-input" placeholder="e.g. Harare">
                    </div>
                    <div>
                        <label class="ui-label">Client / Bank</label>
                        <select id="regClientId" class="ui-select">
                            <option value="">— Select client —</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="ui-label">Physical Address</label>
                    <input type="text" id="regAddress" class="ui-input" placeholder="Street address">
                </div>
                <div>
                    <label class="ui-label">Field Notes</label>
                    <textarea id="regNotes" rows="2" class="ui-input resize-y" placeholder="Any observations about this terminal…"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 sticky bottom-0 bg-white">
                <button onclick="document.getElementById('registerTerminalModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
                <button id="regSubmitBtn" onclick="submitRegisterTerminal()" class="btn-primary" style="background:#6366f1;border-color:#6366f1;">Register Terminal</button>
            </div>
        </div>
    </div>

    {{-- Transfer History (populated when a job is transferred) --}}
    @php $history = $assignment->assignment_history ?? []; @endphp
    @if(count($history) > 0)
    <div class="ui-card" style="border-left:3px solid #f59e0b;">
        <div class="ui-card-header" style="background:#fffbeb;">
            <h6 class="text-sm font-semibold m-0" style="color:#92400e;">
                &#8644; Transfer History
                <span style="margin-left:8px;background:#f59e0b;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">{{ count($history) }}</span>
            </h6>
        </div>
        <div class="ui-card-body" style="padding:0;">
            @foreach($history as $i => $entry)
            <div style="padding:14px 18px;{{ !$loop->last ? 'border-bottom:1px solid #fef3c7;' : '' }}display:flex;gap:14px;align-items:flex-start;">
                <div style="width:30px;height:30px;border-radius:50%;background:#fef3c7;color:#92400e;display:grid;place-items:center;font-size:12px;font-weight:700;flex-shrink:0;">{{ $i+1 }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:4px;">
                        {{ $entry['from_technician_name'] ?? '—' }}
                        <span style="color:#9ca3af;font-weight:400;margin:0 6px;">&#8594;</span>
                        {{ $entry['to_technician_name'] ?? '—' }}
                    </div>
                    <div style="font-size:12px;color:#374151;margin-bottom:3px;">
                        <strong>Reason:</strong> {{ $entry['reason'] ?? '—' }}
                    </div>
                    @if(!empty($entry['notes']))
                    <div style="font-size:12px;color:#6b7280;margin-bottom:3px;">{{ $entry['notes'] }}</div>
                    @endif
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;display:flex;gap:12px;flex-wrap:wrap;">
                        <span>By: {{ $entry['transferred_by_name'] ?? '—' }}</span>
                        <span>{{ isset($entry['transferred_at']) ? \Carbon\Carbon::parse($entry['transferred_at'])->format('M j, Y H:i') : '' }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Classes used only in JS-generated live visits HTML */
.lv-item  { padding:10px 0; border-bottom:1px solid #f1f5f9; }
.lv-item:last-child { border-bottom:none; }
.lv-hdr   { display:flex; justify-content:space-between; align-items:center; }
.lv-name  { font-weight:600; font-size:0.875rem; color:#111827; }
.lv-tid   { color:#6b7280; font-weight:400; }
.lv-meta  { color:#6b7280; font-size:0.75rem; margin-top:4px; }
.lv-det   { margin-top:6px; font-size:0.8125rem; color:#374151; }
.lv-cmt   { color:#374151; margin-top:4px; }
</style>
@endpush

@push('scripts')
<script>
// ==============================
// Terminal Search
// ==============================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('terminalSearch');
    const tableBody   = document.getElementById('terminalsTableBody');
    const noResults   = document.getElementById('noResults');

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            let visible = 0;
            tableBody.querySelectorAll('.terminal-row').forEach(function(row) {
                const match = row.getAttribute('data-searchable').includes(term);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (noResults) noResults.classList.toggle('hidden', visible > 0 || term === '');
        });
    }
});

// ==============================
// Transfer Assignment
// ==============================
async function submitTransfer() {
    const technicianId = document.getElementById('transferTechnicianId').value;
    const reason       = document.getElementById('transferReason').value.trim();
    const notes        = document.getElementById('transferNotes').value.trim();

    if (!technicianId) { showNotification('error', 'Please select a technician.'); return; }
    if (!reason)        { showNotification('error', 'Please enter a reason for the transfer.'); return; }

    const btn = document.getElementById('transferSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Transferring…';

    try {
        const res = await fetch(`/api/jobs/{{ $assignment->id }}/transfer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ to_technician_id: parseInt(technicianId), reason, notes: notes || undefined })
        });
        const data = await res.json();
        if (res.ok && data.success) {
            document.getElementById('transferModal').classList.add('hidden');
            showNotification('success', data.message || 'Job transferred successfully.');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            showNotification('error', data.message || `Transfer failed (HTTP ${res.status})`);
        }
    } catch (e) {
        showNotification('error', 'Network error — could not complete transfer.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Transfer Job';
    }
}

// ==============================
// Register On-Site Terminal
// ==============================
async function submitRegisterTerminal() {
    const terminalId   = document.getElementById('regTerminalId').value.trim();
    const merchantName = document.getElementById('regMerchantName').value.trim();

    if (!terminalId)   { showNotification('error', 'Terminal ID is required.'); return; }
    if (!merchantName) { showNotification('error', 'Merchant name is required.'); return; }

    const btn = document.getElementById('regSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Registering…';

    const payload = { terminal_id: terminalId, merchant_name: merchantName };
    const contactPerson = document.getElementById('regContactPerson').value.trim();
    const phone         = document.getElementById('regPhone').value.trim();
    const city          = document.getElementById('regCity').value.trim();
    const address       = document.getElementById('regAddress').value.trim();
    const clientId      = document.getElementById('regClientId').value;
    const notes         = document.getElementById('regNotes').value.trim();
    if (contactPerson) payload.merchant_contact_person = contactPerson;
    if (phone)         payload.merchant_phone = phone;
    if (city)          payload.city = city;
    if (address)       payload.physical_address = address;
    if (clientId)      payload.client_id = parseInt(clientId);
    if (notes)         payload.notes = notes;

    try {
        const res = await fetch('/api/pos-terminals', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok && data.success) {
            document.getElementById('registerTerminalModal').classList.add('hidden');
            showNotification('success', `Terminal ${terminalId} registered successfully.`);
            // Clear the form
            ['regTerminalId','regMerchantName','regContactPerson','regPhone','regCity','regAddress','regNotes'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('regClientId').value = '';
        } else {
            const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            showNotification('error', firstError || data.message || `Registration failed (HTTP ${res.status})`);
        }
    } catch (e) {
        showNotification('error', 'Network error — could not register terminal.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Register Terminal';
    }
}

// Close modals on backdrop click
['transferModal','registerTerminalModal'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
});

// ==============================
// Status Update
// ==============================
function updateStatus(assignmentId, newStatus, btn) {
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '&#x23F1; Updating...';

    fetch(`/api/assignments/${assignmentId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'Status updated successfully');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification('error', err.message || 'Failed to update status');
        btn.disabled = false;
        btn.innerHTML = orig;
    });
}

// ==============================
// Toast notification
// ==============================
function showNotification(type, message) {
    const el = document.createElement('div');
    el.style.cssText = 'position:fixed;top:20px;right:20px;min-width:280px;background:#fff;border-radius:8px;box-shadow:0 10px 25px rgba(0,0,0,.15);border:1px solid #e5e7eb;z-index:9999;opacity:0;transition:opacity .3s;';
    el.style.borderLeft = `4px solid ${type === 'success' ? '#10b981' : '#ef4444'}`;
    el.innerHTML = `<div style="padding:12px 16px;font-size:14px;color:#374151;">${message}</div>`;
    document.body.appendChild(el);
    setTimeout(() => el.style.opacity = '1', 50);
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 4000);
}

// ==============================
// Live Site Visits Polling
// ==============================
(function(){
    const assignmentId = {{ (int)$assignment->id }};
    const listEl  = document.getElementById('liveVisitsList');
    const countEl = document.getElementById('liveVisitCount');

    async function fetchVisits() {
        try {
            const res  = await fetch(`{{ route('api.jobs.assignments.visits', $assignment->id) }}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) throw new Error('Feed error');

            countEl.textContent = data.count;
            if (data.count === 0) {
                listEl.innerHTML = '<div class="text-sm text-gray-400">No visits yet for this assignment.</div>';
                return;
            }

            listEl.innerHTML = data.visits.map(v => `
                <div class="lv-item">
                    <div class="lv-hdr">
                        <div class="lv-name">${v.merchant_name ?? '&#x2014;'} <span class="lv-tid">(${v.terminal_id ?? '&#x2014;'})</span></div>
                        <span class="badge badge-gray lv-status">${v.status ? v.status.replace('_',' ') : 'open'}</span>
                    </div>
                    <div class="lv-meta">Tech: ${v.technician ?? '&#x2014;'} &bull; Started: ${v.started_at ?? '&#x2014;'} ${v.ended_at ? '&bull; Ended: '+v.ended_at : ''}</div>
                    <div class="lv-det"><strong>Terminal Status:</strong> ${v.terminal_status ?? '&#x2014;'}${v.comments ? '<div class="lv-cmt">'+v.comments+'</div>' : ''}</div>
                </div>
            `).join('');
        } catch (e) {
            console.error(e);
            listEl.innerHTML = '<div class="text-sm text-red-500">Failed to load live visits.</div>';
        }
    }

    fetchVisits();
    setInterval(fetchVisits, 10000);
})();
</script>
@endpush