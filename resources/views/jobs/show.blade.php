{{-- resources/views/jobs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Assignment '.$assignment->assignment_id)

@section('content')
@php
$statusTone = [
    'completed'   => 'is-good',
    'in_progress' => 'is-warn',
    'assigned'    => 'is-accent',
    'cancelled'   => 'is-crit',
    'approved'    => 'is-good',
    'pending'     => 'is-warn',
][$assignment->status] ?? '';
$priorityTone = [
    'emergency' => 'is-crit',
    'high'      => 'is-warn',
][$assignment->priority] ?? '';
$me = auth()->user();
$canManage = $me->hasPermission('manage_jobs') || $me->hasPermission('all');
@endphp
<div class="js-page">
    <a href="{{ route('jobs.index') }}" class="js-crumb">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Assignments
    </a>

    {{-- Assignment header --}}
    <section class="js-card">
        <div class="js-head">
            <div class="js-head-main">
                <div class="js-id">{{ $assignment->assignment_id }}</div>
                <div class="js-chips">
                    <span class="js-chip {{ $statusTone }}">{{ \Illuminate\Support\Str::headline($assignment->status) }}</span>
                    <span class="js-chip {{ $priorityTone }}">{{ \Illuminate\Support\Str::headline($assignment->priority) }} priority</span>
                </div>
            </div>
            <div class="js-head-actions">
                {{-- Register Terminal (always available to managers/technicians on this job) --}}
                @if($me->hasPermission('manage_terminals') || $me->hasPermission('all') || $me->hasPermission('manage_jobs') || ($me->id == $assignment->technician_id))
                <button type="button" class="btn-secondary" onclick="document.getElementById('registerTerminalModal').classList.remove('hidden')">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Register Terminal
                </button>
                @endif

                {{-- Transfer button (only when transferrable) --}}
                @if(!in_array($assignment->status, ['completed','cancelled','reassigned']) && ($canManage || $me->id == $assignment->technician_id))
                <button type="button" class="btn-secondary" onclick="document.getElementById('transferModal').classList.remove('hidden')">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-route"/></svg> Transfer
                </button>
                @endif

                {{-- Status action --}}
                @if($me->can('update', $assignment) || $canManage || ($me->id == $assignment->technician_id))
                @if($assignment->status === 'assigned')
                    <button type="button" class="btn-primary" onclick="updateStatus({{ $assignment->id }}, 'in_progress', this)">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-play"/></svg> Start Assignment
                    </button>
                @elseif($assignment->status === 'in_progress')
                    <button type="button" class="btn-success" onclick="updateStatus({{ $assignment->id }}, 'completed', this)">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg> Mark Complete
                    </button>
                @elseif($assignment->status === 'completed')
                    <span class="js-done">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-circle"/></svg> Assignment Completed
                    </span>
                @endif
                @endif
            </div>
        </div>

        <div class="js-facts">
            <div>
                <h3 class="js-facts-title">Assignment Details</h3>
                <dl class="js-dl">
                    <div>
                        <dt>Technician</dt>
                        <dd>
                            @if($assignment->technician)
                                <span class="js-person">
                                    <span class="js-avatar">{{ substr($assignment->technician->first_name,0,1) }}{{ substr($assignment->technician->last_name,0,1) }}</span>
                                    {{ $assignment->technician->first_name }} {{ $assignment->technician->last_name }}
                                </span>
                            @else
                                <span class="js-muted">Unassigned</span>
                            @endif
                        </dd>
                    </div>
                    <div><dt>Client</dt><dd>{{ $assignment->client->company_name ?? '—' }}</dd></div>
                    @if($assignment->project)
                    <div><dt>Project</dt><dd>{{ $assignment->project->project_name }}</dd></div>
                    @endif
                    @if($assignment->region)
                    <div><dt>Region</dt><dd>{{ $assignment->region->name }}</dd></div>
                    @endif
                </dl>
            </div>
            <div>
                <h3 class="js-facts-title">Service Information</h3>
                <dl class="js-dl">
                    <div>
                        <dt>Scheduled Date</dt>
                        <dd>
                            @if($assignment->scheduled_date)
                                {{ $assignment->scheduled_date->format('M j, Y') }}
                                <span class="js-muted">· {{ $assignment->scheduled_date->diffForHumans() }}</span>
                            @else
                                <span class="js-muted">Not scheduled</span>
                            @endif
                        </dd>
                    </div>
                    <div><dt>Service Type</dt><dd>{{ \Illuminate\Support\Str::headline($assignment->service_type) }}</dd></div>
                    <div><dt>Created</dt><dd>{{ $assignment->created_at?->format('M j, Y') ?? '—' }}</dd></div>
                </dl>
            </div>
        </div>
    </section>

    {{-- Terminals --}}
    @if($terminals->isEmpty())
    <section class="js-card">
        <div class="js-empty">
            <svg class="mv-i" aria-hidden="true"><use href="#i-monitor"/></svg>
            <strong>No terminals assigned</strong>
            This assignment doesn't have any terminals associated with it.
        </div>
    </section>
    @else
    <section class="js-card">
        <div class="js-card-head">
            <h2>Terminals <span class="js-count">{{ $terminals->count() }}</span></h2>
            <label class="js-search">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-search"/></svg>
                <input type="text" id="terminalSearch" placeholder="Search terminals..." aria-label="Search terminals">
            </label>
        </div>
        <div style="overflow-x:auto;">
            <table class="js-table">
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
                $tb = ['active' => 'is-good', 'inactive' => 'is-crit', 'offline' => 'is-crit', 'faulty' => 'is-crit', 'maintenance' => 'is-warn'][$ts] ?? '';
                @endphp
                <tr class="terminal-row" data-searchable="{{ strtolower($t->merchant_name . ' ' . $t->terminal_id . ' ' . ($t->physical_address ?? $t->address ?? '') . ' ' . ($t->city ?? '') . ' ' . ($t->province ?? '')) }}">
                    <td>
                        <div class="js-strong">{{ $t->merchant_name ?? '—' }}</div>
                        <div class="js-sub">{{ $t->client->company_name ?? '—' }}</div>
                    </td>
                    <td><span class="js-mono">{{ $t->terminal_id }}</span></td>
                    <td>{{ $t->physical_address ?? $t->address ?? '—' }}</td>
                    <td>{{ $t->city ?? '—' }}@if($t->province)<span class="js-muted">, {{ $t->province }}</span>@endif</td>
                    <td><span class="js-chip {{ $tb }}">{{ \Illuminate\Support\Str::headline($t->current_status ?? $t->status ?? 'unknown') }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div id="noResults" class="hidden js-noresults">No terminals match your search criteria</div>
    </section>
    @endif

    {{-- Live Site Visits --}}
    <section class="js-card" id="liveSiteVisitsCard">
        <div class="js-card-head">
            <h2>Live Site Visits <span class="js-count" id="liveVisitCount">0</span></h2>
            <span class="js-sub"><span class="js-pulse"></span>Updates every 10 seconds</span>
        </div>
        <div class="js-card-body">
            <div id="liveVisitsList" class="js-visits">
                <div class="js-muted">Waiting for updates...</div>
            </div>
        </div>
    </section>

    {{-- Notes --}}
    @if($assignment->notes)
    <section class="js-card">
        <div class="js-card-head"><h2>Notes</h2></div>
        <div class="js-card-body">
            <p class="js-notes">{{ $assignment->notes }}</p>
        </div>
    </section>
    @endif

    {{-- Transfer History (populated when a job is transferred) --}}
    @php $history = $assignment->assignment_history ?? []; @endphp
    @if(count($history) > 0)
    <section class="js-card">
        <div class="js-card-head"><h2>Transfer History <span class="js-count">{{ count($history) }}</span></h2></div>
        <ol class="js-history">
            @foreach($history as $i => $entry)
            <li>
                <span class="js-step">{{ $i+1 }}</span>
                <div>
                    <div class="js-strong">
                        {{ $entry['from_technician_name'] ?? '—' }}
                        <svg class="mv-i mv-i-sm js-arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                        {{ $entry['to_technician_name'] ?? '—' }}
                    </div>
                    <div class="js-history-line"><span class="js-muted">Reason:</span> {{ $entry['reason'] ?? '—' }}</div>
                    @if(!empty($entry['notes']))
                    <div class="js-history-line">{{ $entry['notes'] }}</div>
                    @endif
                    <div class="js-sub">
                        By {{ $entry['transferred_by_name'] ?? '—' }}
                        @if(isset($entry['transferred_at'])) · {{ \Carbon\Carbon::parse($entry['transferred_at'])->format('M j, Y H:i') }} @endif
                    </div>
                </div>
            </li>
            @endforeach
        </ol>
    </section>
    @endif

    {{-- ======================================================== --}}
    {{-- TRANSFER MODAL --}}
    {{-- ======================================================== --}}
    <div id="transferModal" class="hidden js-overlay">
        <div class="js-modal" role="dialog" aria-modal="true" aria-labelledby="transferTitle">
            <div class="js-modal-head">
                <h3 id="transferTitle">Transfer Assignment</h3>
                <button type="button" class="js-x" aria-label="Close" onclick="document.getElementById('transferModal').classList.add('hidden')">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
                </button>
            </div>
            <div class="js-modal-body">
                <div class="js-field">
                    <label class="ui-label" for="transferTechnicianId">Transfer to <span class="js-req">*</span></label>
                    <select id="transferTechnicianId" class="ui-select">
                        <option value="">— Select technician —</option>
                        @foreach($technicians as $tech)
                            @if($tech->id !== $assignment->technician_id)
                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="js-field">
                    <label class="ui-label" for="transferReason">Reason <span class="js-req">*</span></label>
                    <textarea id="transferReason" rows="3" class="ui-input" placeholder="Why is this job being transferred?"></textarea>
                </div>
                <div class="js-field">
                    <label class="ui-label" for="transferNotes">Notes for receiving technician</label>
                    <textarea id="transferNotes" rows="2" class="ui-input" placeholder="Any additional handover notes…"></textarea>
                </div>
            </div>
            <div class="js-modal-foot">
                <button type="button" onclick="document.getElementById('transferModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
                <button type="button" id="transferSubmitBtn" onclick="submitTransfer()" class="btn-primary">Transfer Job</button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- REGISTER TERMINAL MODAL --}}
    {{-- ======================================================== --}}
    <div id="registerTerminalModal" class="hidden js-overlay">
        <div class="js-modal js-modal-lg" role="dialog" aria-modal="true" aria-labelledby="registerTitle">
            <div class="js-modal-head">
                <h3 id="registerTitle">Register On-Site Terminal</h3>
                <button type="button" class="js-x" aria-label="Close" onclick="document.getElementById('registerTerminalModal').classList.add('hidden')">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
                </button>
            </div>
            <div class="js-modal-body">
                <div class="js-form-grid">
                    <div class="js-field">
                        <label class="ui-label" for="regTerminalId">Terminal ID (TID) <span class="js-req">*</span></label>
                        <input type="text" id="regTerminalId" class="ui-input" placeholder="e.g. TID-00123">
                    </div>
                    <div class="js-field">
                        <label class="ui-label" for="regMerchantName">Merchant Name <span class="js-req">*</span></label>
                        <input type="text" id="regMerchantName" class="ui-input" placeholder="e.g. Joe's Butchery">
                    </div>
                    <div class="js-field">
                        <label class="ui-label" for="regContactPerson">Contact Person</label>
                        <input type="text" id="regContactPerson" class="ui-input" placeholder="e.g. Joe Moyo">
                    </div>
                    <div class="js-field">
                        <label class="ui-label" for="regPhone">Phone</label>
                        <input type="text" id="regPhone" class="ui-input" placeholder="e.g. 0771234567">
                    </div>
                    <div class="js-field">
                        <label class="ui-label" for="regCity">City</label>
                        <input type="text" id="regCity" class="ui-input" placeholder="e.g. Harare">
                    </div>
                    <div class="js-field">
                        <label class="ui-label" for="regClientId">Client / Bank</label>
                        <select id="regClientId" class="ui-select">
                            <option value="">— Select client —</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="js-field js-span">
                        <label class="ui-label" for="regAddress">Physical Address</label>
                        <input type="text" id="regAddress" class="ui-input" placeholder="Street address">
                    </div>
                    <div class="js-field js-span">
                        <label class="ui-label" for="regNotes">Field Notes</label>
                        <textarea id="regNotes" rows="2" class="ui-input" placeholder="Any observations about this terminal…"></textarea>
                    </div>
                </div>
            </div>
            <div class="js-modal-foot">
                <button type="button" onclick="document.getElementById('registerTerminalModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
                <button type="button" id="regSubmitBtn" onclick="submitRegisterTerminal()" class="btn-primary">Register Terminal</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.js-page { display: flex; flex-direction: column; gap: 16px; }
.js-crumb { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--mv-muted); text-decoration: none; align-self: flex-start; }
.js-crumb:hover { color: var(--mv-ink); }
.js-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
.js-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 18px; border-bottom: 1px solid var(--mv-line); min-height: 52px; }
.js-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); display: flex; align-items: center; gap: 8px; }
.js-card-body { padding: 16px 18px; }
.js-count { font-size: 12px; font-weight: 500; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 6px; padding: 0 7px; line-height: 1.7; font-variant-numeric: tabular-nums; }

.js-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px 18px; border-bottom: 1px solid var(--mv-line); flex-wrap: wrap; }
.js-id { font-family: var(--mv-mono); font-size: 15px; font-weight: 500; color: var(--mv-ink); }
.js-head-main { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.js-chips { display: flex; gap: 6px; }
.js-head-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.js-done { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; color: var(--mv-good); }

.js-chip { display: inline-flex; align-items: center; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; line-height: 1.7; white-space: nowrap; background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }
.js-chip.is-accent { background: var(--mv-accent-soft); color: var(--mv-accent-ink); border-color: transparent; }
.js-chip.is-good { background: var(--mv-good-soft); color: var(--mv-good); border-color: transparent; }
.js-chip.is-warn { background: var(--mv-warn-soft); color: var(--mv-warn); border-color: transparent; }
.js-chip.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); border-color: transparent; }

.js-facts { display: grid; grid-template-columns: 1fr 1fr; }
.js-facts > div { padding: 14px 18px 16px; }
.js-facts > div + div { border-left: 1px solid var(--mv-line); }
.js-facts-title { margin: 0 0 6px; font-size: 12px; font-weight: 600; color: var(--mv-muted); letter-spacing: .04em; text-transform: uppercase; }
.js-dl { margin: 0; }
.js-dl > div { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid var(--mv-line); font-size: 13px; }
.js-dl > div:last-child { border-bottom: 0; }
.js-dl dt { color: var(--mv-muted); font-weight: 400; }
.js-dl dd { margin: 0; color: var(--mv-ink); text-align: right; font-variant-numeric: tabular-nums; }
.js-person { display: inline-flex; align-items: center; gap: 8px; }
.js-avatar { width: 26px; height: 26px; border-radius: 50%; background: var(--mv-accent-soft); color: var(--mv-accent-ink); display: grid; place-items: center; font-size: 11px; font-weight: 600; }
.js-muted { color: var(--mv-muted); }

.js-search { display: flex; align-items: center; gap: 6px; border: 1px solid var(--mv-line-strong); border-radius: 8px; padding: 0 10px; background: var(--mv-surface); color: var(--mv-muted); }
.js-search:focus-within { border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
.js-search input { border: 0; outline: 0; padding: 6px 0; font: inherit; font-size: 13px; min-width: 200px; background: transparent; color: var(--mv-ink); }
.js-table { width: 100%; border-collapse: collapse; }
.js-table th { background: var(--mv-surface-2); color: var(--mv-muted); font-size: 11.5px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; text-align: left; padding: 9px 14px; border-bottom: 1px solid var(--mv-line); }
.js-table td { padding: 11px 14px; border-bottom: 1px solid var(--mv-line); vertical-align: middle; font-size: 13px; color: var(--mv-ink-2); }
.js-table tbody tr:last-child td { border-bottom: 0; }
.js-table tbody tr:hover { background: var(--mv-surface-2); }
.js-strong { color: var(--mv-ink); font-weight: 500; font-size: 13px; }
.js-sub { font-size: 12px; color: var(--mv-muted); margin-top: 2px; display: inline-flex; align-items: center; gap: 6px; }
.js-mono { font-family: var(--mv-mono); font-size: 12.5px; color: var(--mv-ink); }
.js-noresults { padding: 18px; text-align: center; font-size: 13px; color: var(--mv-muted); border-top: 1px solid var(--mv-line); }
.js-empty { padding: 36px 20px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
.js-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); display: block; margin: 0 auto 10px; }
.js-empty strong { display: block; color: var(--mv-ink); font-weight: 500; margin-bottom: 2px; }
.js-pulse { width: 7px; height: 7px; border-radius: 50%; background: var(--mv-good); display: inline-block; }
.js-visits { max-height: 420px; overflow-y: auto; font-size: 13px; }
.js-notes { margin: 0; font-size: 13.5px; line-height: 1.6; color: var(--mv-ink-2); white-space: pre-wrap; }

.js-history { list-style: none; margin: 0; padding: 0; }
.js-history li { display: grid; grid-template-columns: 26px minmax(0, 1fr); gap: 12px; padding: 12px 18px; border-bottom: 1px solid var(--mv-line); }
.js-history li:last-child { border-bottom: 0; }
.js-step { width: 26px; height: 26px; border-radius: 50%; border: 1px solid var(--mv-line-strong); display: grid; place-items: center; font-size: 12px; font-weight: 600; color: var(--mv-ink-2); }
.js-arrow { vertical-align: -3px; color: var(--mv-muted); margin: 0 4px; }
.js-history-line { font-size: 13px; color: var(--mv-ink-2); margin-top: 3px; }

.js-overlay { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; background: rgba(22, 32, 44, .45); padding: 16px; }
.js-modal { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; width: 100%; max-width: 460px; max-height: 90vh; overflow-y: auto; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
.js-modal-lg { max-width: 560px; }
.js-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); position: sticky; top: 0; background: var(--mv-surface); }
.js-modal-head h3 { margin: 0; font-size: 15px; font-weight: 600; color: var(--mv-ink); }
.js-x { width: 32px; height: 32px; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); display: grid; place-items: center; cursor: pointer; }
.js-x:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.js-modal-body { padding: 16px 18px; }
.js-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); border-radius: 0 0 12px 12px; position: sticky; bottom: 0; }
.js-field { margin-bottom: 14px; }
.js-field:last-child { margin-bottom: 0; }
.js-field .ui-label { display: block; margin-bottom: 6px; }
.js-field .ui-input, .js-field .ui-select { width: 100%; }
.js-field textarea { resize: vertical; }
.js-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 14px; }
.js-form-grid .js-field { margin-bottom: 14px; }
.js-span { grid-column: 1 / -1; }
.js-req { color: var(--mv-crit); }

/* Classes used only in JS-generated live visits HTML */
.lv-item  { padding: 10px 0; border-bottom: 1px solid var(--mv-line); }
.lv-item:first-child { padding-top: 0; }
.lv-item:last-child { border-bottom: none; }
.lv-hdr   { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
.lv-name  { font-weight: 500; font-size: 13px; color: var(--mv-ink); }
.lv-tid   { color: var(--mv-muted); font-weight: 400; font-family: var(--mv-mono); font-size: 12px; }
.lv-meta  { color: var(--mv-muted); font-size: 12px; margin-top: 4px; }
.lv-det   { margin-top: 6px; font-size: 13px; color: var(--mv-ink-2); }
.lv-cmt   { color: var(--mv-ink-2); margin-top: 4px; }
.lv-err   { color: var(--mv-crit); }

@media (max-width: 900px) {
    .js-facts { grid-template-columns: 1fr; }
    .js-facts > div + div { border-left: 0; border-top: 1px solid var(--mv-line); }
    .js-form-grid { grid-template-columns: 1fr; }
}
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
        const res = await fetch(`/web/assignments/{{ $assignment->id }}/transfer`, {
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
        const res = await fetch('/web/pos-terminals/register', {
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
    btn.textContent = 'Updating...';

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

// Toasts: showNotification(type, message) comes from the portal layout.

// ==============================
// Live Site Visits Polling
// ==============================
(function(){
    const listEl  = document.getElementById('liveVisitsList');
    const countEl = document.getElementById('liveVisitCount');
    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    async function fetchVisits() {
        try {
            const res  = await fetch(`{{ route('api.jobs.assignments.visits', $assignment->id) }}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) throw new Error('Feed error');

            countEl.textContent = data.count;
            if (data.count === 0) {
                listEl.innerHTML = '<div class="js-muted">No visits yet for this assignment.</div>';
                return;
            }

            listEl.innerHTML = data.visits.map(v => `
                <div class="lv-item">
                    <div class="lv-hdr">
                        <div class="lv-name">${esc(v.merchant_name ?? '—')} <span class="lv-tid">${esc(v.terminal_id ?? '—')}</span></div>
                        <span class="js-chip lv-status">${v.status ? esc(v.status.replace('_',' ')) : 'open'}</span>
                    </div>
                    <div class="lv-meta">Tech: ${esc(v.technician ?? '—')} · Started: ${esc(v.started_at ?? '—')} ${v.ended_at ? '· Ended: ' + esc(v.ended_at) : ''}</div>
                    <div class="lv-det"><span class="js-muted">Terminal Status:</span> ${esc(v.terminal_status ?? '—')}${v.comments ? '<div class="lv-cmt">' + esc(v.comments) + '</div>' : ''}</div>
                </div>
            `).join('');
        } catch (e) {
            console.error(e);
            listEl.innerHTML = '<div class="lv-err">Failed to load live visits.</div>';
        }
    }

    fetchVisits();
    setInterval(fetchVisits, 10000);
})();
</script>
@endpush
