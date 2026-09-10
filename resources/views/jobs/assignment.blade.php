@extends('layouts.app')
@section('title', 'Job Assignment')

@section('header-actions')
    <button type="button" onclick="exportAssignments()" class="btn-secondary btn-sm">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Export CSV
    </button>
    <button type="button" onclick="refreshData()" class="btn-secondary btn-sm">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg> Refresh
    </button>
@endsection

@push('styles')
<style>
    .ja-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 16px; }
    .ja-stat { padding: 14px 18px; }
    .ja-stat + .ja-stat { border-left: 1px solid var(--mv-line); }
    .ja-stat-label { font-size: 12.5px; color: var(--mv-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 7px; }
    .ja-stat-value { font-size: 22px; font-weight: 600; color: var(--mv-ink); letter-spacing: -.02em; font-variant-numeric: tabular-nums; line-height: 1.15; }
    .ja-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--mv-line-strong); }
    .ja-dot.is-accent { background: var(--mv-accent); }
    .ja-dot.is-warn { background: #C28A2C; }
    .ja-dot.is-good { background: var(--mv-good); }

    .ja-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 3fr); gap: 16px; align-items: start; }
    .ja-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
    .ja-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 13px 18px; border-bottom: 1px solid var(--mv-line); min-height: 52px; }
    .ja-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .ja-card-sub { font-size: 12.5px; color: var(--mv-muted); margin: 2px 0 0; }
    .ja-card-body { padding: 18px; }

    .ja-field { margin-bottom: 16px; }
    .ja-field label, .ja-label { display: block; margin-bottom: 6px; font-size: 12.5px; font-weight: 500; color: var(--mv-ink-2); }
    .ja-req { color: var(--mv-crit); }
    .ja-control { width: 100%; padding: 8px 11px; border: 1px solid var(--mv-line-strong); border-radius: 8px; background: var(--mv-surface); color: var(--mv-ink); font: inherit; font-size: 13.5px; }
    .ja-control:focus { outline: none; border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
    textarea.ja-control { resize: vertical; }
    select.ja-control { appearance: none; -webkit-appearance: none; padding-right: 32px; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236A7686' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 10px center; background-size: 15px; }
    .ja-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .ja-help { font-size: 12px; color: var(--mv-muted); margin-top: 5px; }
    .ja-err { font-size: 12px; color: var(--mv-crit); margin-top: 5px; }
    .ja-actions { display: flex; justify-content: flex-end; gap: 8px; padding-top: 16px; border-top: 1px solid var(--mv-line); }

    .ja-terminals { border: 1px solid var(--mv-line-strong); border-radius: 8px; max-height: 220px; overflow-y: auto; background: var(--mv-surface-2); }
    .ja-terminals-foot { display: flex; justify-content: space-between; align-items: center; margin-top: 6px; font-size: 12px; color: var(--mv-muted); }
    .ja-link { background: none; border: 0; padding: 0; font: inherit; font-size: 12.5px; color: var(--mv-accent-ink); cursor: pointer; }
    .ja-link:hover { text-decoration: underline; }
    .ja-link + .ja-link { margin-left: 12px; }
    .ja-placeholder { padding: 22px 14px; text-align: center; font-size: 13px; color: var(--mv-muted); }
    .ja-placeholder.is-error { color: var(--mv-crit); }
    .ja-placeholder small { display: block; margin-top: 4px; color: var(--mv-muted); font-size: 12px; }
    .terminal-checkbox { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-bottom: 1px solid var(--mv-line); background: var(--mv-surface); cursor: pointer; }
    .terminal-checkbox:last-child { border-bottom: 0; }
    .terminal-checkbox:hover { background: var(--mv-surface-2); }
    .terminal-checkbox.is-selected { background: var(--mv-accent-soft); }
    .terminal-checkbox input { accent-color: var(--mv-accent); }
    .ja-t-main { flex: 1; min-width: 0; }
    .ja-t-title { font-size: 13px; color: var(--mv-ink); font-weight: 500; }
    .ja-t-title .mv-mono { font-weight: 500; }
    .ja-t-addr { font-size: 12px; color: var(--mv-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .ja-chip { display: inline-flex; align-items: center; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; line-height: 1.7; white-space: nowrap; background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }
    .ja-chip.is-accent { background: var(--mv-accent-soft); color: var(--mv-accent-ink); border-color: transparent; }
    .ja-chip.is-good { background: var(--mv-good-soft); color: var(--mv-good); border-color: transparent; }
    .ja-chip.is-warn { background: var(--mv-warn-soft); color: var(--mv-warn); border-color: transparent; }
    .ja-chip.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); border-color: transparent; }

    .ja-list { max-height: 680px; overflow-y: auto; }
    .assignment-card { padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
    .assignment-card:last-child { border-bottom: 0; }
    .assignment-card:hover { background: var(--mv-surface-2); }
    .ja-a-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
    .ja-a-id { font-family: var(--mv-mono); font-size: 13px; font-weight: 500; color: var(--mv-ink); }
    .ja-a-who { font-size: 13px; color: var(--mv-ink-2); margin-top: 2px; }
    .ja-a-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; margin-top: 8px; font-size: 12.5px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
    .ja-a-meta .mv-i { color: var(--mv-muted); }
    .ja-a-meta > span { display: inline-flex; align-items: center; gap: 5px; }
    .ja-a-actions { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .btn-small { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border: 1px solid var(--mv-line-strong); border-radius: 7px; background: var(--mv-surface); color: var(--mv-ink-2); font: inherit; font-size: 12.5px; font-weight: 500; cursor: pointer; }
    .btn-small:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
    .btn-small.is-danger { color: var(--mv-crit); border-color: #EBC3C3; }
    .btn-small.is-danger:hover { background: var(--mv-crit-soft); }
    .btn-small.is-good { color: var(--mv-good); border-color: #C6E6D2; }
    .btn-small.is-good:hover { background: var(--mv-good-soft); }
    .ja-empty { padding: 44px 20px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
    .ja-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); display: block; margin: 0 auto 10px; }
    .ja-empty strong { display: block; color: var(--mv-ink); font-weight: 500; margin-bottom: 2px; }

    /* Details dialog */
    #assignmentModal { position: fixed; inset: 0; background: rgba(22, 32, 44, .45); z-index: 1000; justify-content: center; align-items: center; }
    .ja-modal { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; max-width: 560px; width: calc(100% - 32px); max-height: 80vh; overflow-y: auto; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
    .ja-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
    .ja-modal-head h3 { margin: 0; font-size: 15px; font-weight: 600; color: var(--mv-ink); }
    .ja-x { width: 32px; height: 32px; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); display: grid; place-items: center; cursor: pointer; }
    .ja-x:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
    #modalContent { padding: 18px; }
    .ja-dl { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; margin: 0 0 18px; }
    .ja-dl dt { font-size: 12px; color: var(--mv-muted); margin-bottom: 2px; font-weight: 400; }
    .ja-dl dd { margin: 0; font-size: 13.5px; color: var(--mv-ink); }
    .ja-dl dd small { display: block; color: var(--mv-muted); font-size: 12px; }
    .ja-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }

    @media (max-width: 1100px) { .ja-grid { grid-template-columns: 1fr; } }
    @media (max-width: 900px) { .ja-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .ja-stat:nth-child(3) { border-left: 0; } .ja-stat:nth-child(n+3) { border-top: 1px solid var(--mv-line); } }
</style>
@endpush

@section('content')
@php
    $statusTone = ['completed' => 'is-good', 'in_progress' => 'is-warn', 'assigned' => 'is-accent', 'cancelled' => 'is-crit'];
    $priorityTone = ['emergency' => 'is-crit', 'high' => 'is-warn'];
@endphp
<div>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Statistics -->
    <div class="ja-stats">
        <div class="ja-stat">
            <div class="ja-stat-label"><span class="ja-dot is-accent"></span>Today's Assignments</div>
            <div class="ja-stat-value" id="todayAssignments">{{ $stats['today_assignments'] ?? 0 }}</div>
        </div>
        <div class="ja-stat">
            <div class="ja-stat-label"><span class="ja-dot"></span>Pending</div>
            <div class="ja-stat-value" id="pendingAssignments">{{ $stats['pending_assignments'] ?? 0 }}</div>
        </div>
        <div class="ja-stat">
            <div class="ja-stat-label"><span class="ja-dot is-warn"></span>In Progress</div>
            <div class="ja-stat-value" id="inProgressAssignments">{{ $stats['in_progress_assignments'] ?? 0 }}</div>
        </div>
        <div class="ja-stat">
            <div class="ja-stat-label"><span class="ja-dot is-good"></span>Completed Today</div>
            <div class="ja-stat-value" id="completedToday">{{ $stats['completed_today'] ?? 0 }}</div>
        </div>
    </div>

    <div class="ja-grid">
        <!-- Assignment Form -->
        <section class="ja-card">
            <div class="ja-card-head">
                <div>
                    <h2>Create New Assignment</h2>
                    <p class="ja-card-sub">Assign technicians to regions and POS terminals for service visits</p>
                </div>
            </div>
            <div class="ja-card-body">
                <form action="{{ route('jobs.assignment.store') }}" method="POST" id="jobAssignmentForm">
                    @csrf

                    <!-- Hidden field for JSON terminals -->
                    <input type="hidden" name="pos_terminals" id="pos_terminals_json" value="">

                    <div class="ja-field">
                        <label for="technician">Technician <span class="ja-req">*</span></label>
                        <select id="technician" name="technician_id" required class="ja-control">
                            <option value="">Select Technician</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}"
                                        data-specialization="{{ $technician->specialization }}"
                                        data-phone="{{ $technician->phone }}">
                                    {{ $technician->name }} - {{ $technician->specialization }}
                                </option>
                            @endforeach
                        </select>
                        <div id="technicianInfo" class="ja-help"></div>
                        @error('technician_id')
                            <div class="ja-err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="ja-row">
                        <div class="ja-field">
                            <label for="region">Region <span class="ja-req">*</span></label>
                            <select id="region" name="region_id" required class="ja-control">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" data-terminals="{{ $region->pos_terminals_count }}">
                                        {{ $region->name }} ({{ $region->pos_terminals_count }})
                                    </option>
                                @endforeach
                            </select>
                            @error('region_id')
                                <div class="ja-err">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="ja-field">
                            <label for="client">Client (Optional)</label>
                            <select id="client" name="client_id" class="ja-control">
                                <option value="">All Clients</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="ja-field">
                        <span class="ja-label">POS Terminals <span class="ja-req">*</span></span>
                        <div class="ja-terminals">
                            <div id="terminalsContainer">
                                <div class="ja-placeholder">Select a region to view terminals</div>
                            </div>
                        </div>
                        <div class="ja-terminals-foot">
                            <span id="selectedCount">0 terminals selected</span>
                            <span>
                                <button type="button" class="ja-link" onclick="selectAllTerminals()">Select All</button>
                                <button type="button" class="ja-link" onclick="clearAllTerminals()">Clear All</button>
                            </span>
                        </div>
                        @error('pos_terminals')
                            <div class="ja-err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="ja-row">
                        <div class="ja-field">
                            <label for="scheduledDate">Scheduled Date <span class="ja-req">*</span></label>
                            <input type="date" id="scheduledDate" name="scheduled_date" class="ja-control"
                                   value="{{ old('scheduled_date', date('Y-m-d')) }}" required>
                            @error('scheduled_date')
                                <div class="ja-err">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="ja-field">
                            <label for="serviceType">Service Type <span class="ja-req">*</span></label>
                            <select id="serviceType" name="service_type" required class="ja-control">
                                <option value="">Select Service</option>
                                @foreach($serviceTypes as $serviceType)
                                    <option value="{{ $serviceType->slug }}" {{ old('service_type') == $serviceType->slug ? 'selected' : '' }}>{{ $serviceType->name }}</option>
                                @endforeach
                            </select>
                            @error('service_type')
                                <div class="ja-err">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="ja-row">
                        <div class="ja-field">
                            <label for="priority">Priority <span class="ja-req">*</span></label>
                            <select id="priority" name="priority" required class="ja-control">
                                <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="emergency" {{ old('priority') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            </select>
                            @error('priority')
                                <div class="ja-err">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="ja-field">
                            <label for="estimatedDuration">Est. Duration (hours)</label>
                            <input type="number" id="estimatedDuration" name="estimated_duration_hours" class="ja-control"
                                   value="{{ old('estimated_duration_hours') }}" step="0.5" min="0.5" max="8" placeholder="2.0">
                            @error('estimated_duration_hours')
                                <div class="ja-err">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="ja-field">
                        <label for="notes">Notes/Instructions</label>
                        <textarea id="notes" name="notes" rows="3" class="ja-control"
                                  placeholder="Special instructions, contact details, or notes for the technician...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="ja-err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="ja-actions">
                        <button type="button" onclick="resetForm()" class="btn-secondary">Reset Form</button>
                        <button type="submit" class="btn-primary">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Create Assignment
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Current Assignments -->
        <section class="ja-card">
            <div class="ja-card-head">
                <h2>Current Assignments</h2>
                <select id="assignmentFilter" class="ja-control" style="width:auto; padding:5px 10px; font-size:13px;" aria-label="Filter by status">
                    <option value="">All Assignments</option>
                    @foreach($statusOptions as $slug => $name)
                        <option value="{{ $slug }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="ja-list">
                <div id="assignmentsList">
                    @forelse($assignments as $assignment)
                    @php
                        $serviceTypeCategory = \App\Models\Category::findBySlugAndType($assignment->service_type, \App\Models\Category::TYPE_SERVICE_TYPE);
                    @endphp
                    <div class="assignment-card" data-status="{{ $assignment->status }}">
                        <div class="ja-a-top">
                            <div>
                                <div class="ja-a-id">{{ $assignment->assignment_number ?? $assignment->assignment_id }}</div>
                                <div class="ja-a-who">{{ $assignment->technician->name ?? 'N/A' }} · {{ $assignment->region->name ?? 'N/A' }}</div>
                            </div>
                            <span class="ja-chip {{ $statusTone[$assignment->status] ?? '' }}">{{ ucfirst(str_replace('_', ' ', $assignment->status)) }}</span>
                        </div>
                        <div class="ja-a-meta">
                            <span><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-monitor"/></svg>{{ count($assignment->pos_terminals ?? []) }} terminals</span>
                            <span><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-calendar"/></svg>{{ optional($assignment->scheduled_date)->format('M d, Y') ?? 'Not scheduled' }}</span>
                            <span><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-wrench"/></svg>{{ $serviceTypeCategory->name ?? \Illuminate\Support\Str::headline((string) $assignment->service_type) }}</span>
                            <span class="ja-chip {{ $priorityTone[$assignment->priority] ?? '' }}">{{ ucfirst($assignment->priority) }}</span>
                        </div>
                        <div class="ja-a-actions">
                            <button type="button" onclick="viewAssignment({{ $assignment->id }})" class="btn-small"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg> View</button>
                            @if($assignment->status == 'assigned')
                                <button type="button" onclick="editAssignment({{ $assignment->id }})" class="btn-small"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit</button>
                                <button type="button" onclick="cancelAssignment({{ $assignment->id }})" class="btn-small is-danger"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x-circle"/></svg> Cancel</button>
                            @elseif($assignment->status == 'in_progress')
                                <button type="button" onclick="completeAssignment({{ $assignment->id }})" class="btn-small is-good"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-circle"/></svg> Complete</button>
                            @elseif($assignment->status == 'completed')
                                <button type="button" onclick="generateReport({{ $assignment->id }})" class="btn-small"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-file"/></svg> Report</button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="ja-empty">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-clipboard"/></svg>
                        <strong>No assignments found</strong>
                        Create your first technician assignment to get started.
                    </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Assignment Details Modal -->
<div id="assignmentModal" style="display: none;">
    <div class="ja-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="ja-modal-head">
            <h3 id="modalTitle">Assignment Details</h3>
            <button type="button" onclick="closeModal()" class="ja-x" aria-label="Close">
                <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
            </button>
        </div>
        <div id="modalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
let selectedTerminals = [];

const jobRoutes = {
    regionTerminals: (id) => `{{ url('/jobs/regions') }}/${id}/terminals`,
    show:            (id) => `{{ url('/jobs/assignment') }}/${id}`,
    edit:            (id) => `{{ url('/jobs/assignment') }}/${id}/edit`,
    cancel:          (id) => `{{ url('/jobs/assignment') }}/${id}/cancel`,
    complete:        (id) => `{{ url('/assignments') }}/${id}/complete`,
    page:            (id) => `{{ url('/jobs/assignments') }}/${id}`,
    export:          `{{ route('jobs.assignment.export') }}`,
};

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();

    if (!document.querySelector('meta[name="csrf-token"]')) {
        const metaTag = document.createElement('meta');
        metaTag.name = 'csrf-token';
        metaTag.content = '{{ csrf_token() }}';
        document.head.appendChild(metaTag);
    }
});

function setupEventListeners() {
    // Technician selection
    document.getElementById('technician').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const info = document.getElementById('technicianInfo');

        if (option.value) {
            const specialization = option.dataset.specialization;
            const phone = option.dataset.phone;
            info.textContent = `Specialization: ${specialization || '—'}  ·  Phone: ${phone || '—'}`;
        } else {
            info.textContent = '';
        }
    });

    document.getElementById('region').addEventListener('change', loadTerminals);
    document.getElementById('client').addEventListener('change', loadTerminals);

    document.getElementById('assignmentFilter').addEventListener('change', filterAssignments);

    document.getElementById('jobAssignmentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (selectedTerminals.length === 0) {
            alert('Please select at least one terminal');
            return false;
        }

        document.getElementById('pos_terminals_json').value = JSON.stringify(selectedTerminals);

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Creating Assignment...';
        submitBtn.disabled = true;

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            return response.json().then(data => {
                throw new Error(data.message || `HTTP ${response.status}`);
            });
        })
        .then(data => {
            alert(data.message || 'Assignment created successfully!');
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating assignment: ' + error.message);
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
}

function setTerminalsPlaceholder(text, isError, detail) {
    document.getElementById('terminalsContainer').innerHTML =
        `<div class="ja-placeholder${isError ? ' is-error' : ''}">${escapeHtml(text)}${detail ? `<small>${escapeHtml(detail)}</small>` : ''}</div>`;
}

function loadTerminals() {
    const regionId = document.getElementById('region').value;
    const clientId = document.getElementById('client').value;

    if (!regionId) {
        setTerminalsPlaceholder('Select a region to view terminals');
        return;
    }

    setTerminalsPlaceholder('Loading terminals...');

    let apiUrl = jobRoutes.regionTerminals(regionId);
    if (clientId) {
        apiUrl += '?' + new URLSearchParams({ client_id: clientId }).toString();
    }

    fetch(apiUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderTerminals(data.terminals || []);
        } else {
            throw new Error(data.message || 'Failed to load terminals');
        }
    })
    .catch(error => {
        console.error('Error loading terminals:', error);
        setTerminalsPlaceholder('Error loading terminals', true, error.message);
    });
}

function renderTerminals(terminals) {
    const container = document.getElementById('terminalsContainer');

    if (!terminals || terminals.length === 0) {
        setTerminalsPlaceholder('No terminals found in this region');
        return;
    }

    let html = '';
    terminals.forEach(terminal => {
        const status = terminal.current_status || terminal.status || '';
        const address = terminal.physical_address || terminal.address || 'No address';
        const merchantName = terminal.merchant_name || 'Unknown Merchant';

        html += `
            <div class="terminal-checkbox" onclick="toggleTerminal(${terminal.id}, this)">
                <input type="checkbox" id="terminal_${terminal.id}" value="${terminal.id}" onclick="event.stopPropagation(); toggleTerminal(${terminal.id}, this.closest('.terminal-checkbox'), true);">
                <div class="ja-t-main">
                    <div class="ja-t-title"><span class="mv-mono">${escapeHtml(terminal.terminal_id)}</span> · ${escapeHtml(merchantName)}</div>
                    <div class="ja-t-addr">${escapeHtml(address)}</div>
                </div>
                ${status ? `<span class="ja-chip ${getStatusTone(status)}">${escapeHtml(status.replace(/_/g, ' '))}</span>` : ''}
            </div>
        `;
    });

    container.innerHTML = html;

    selectedTerminals = [];
    updateSelectedCount();
}

function getStatusTone(status) {
    const tones = {
        'active': 'is-good',
        'offline': 'is-crit',
        'maintenance': 'is-warn',
        'faulty': 'is-crit'
    };
    return tones[String(status).toLowerCase()] || '';
}

function toggleTerminal(terminalId, element, fromCheckbox) {
    const checkbox = element.querySelector('input[type="checkbox"]');

    // clicking the row toggles the box; clicking the box itself already toggled it
    if (!fromCheckbox) {
        checkbox.checked = !checkbox.checked;
    }

    if (checkbox.checked) {
        if (!selectedTerminals.includes(terminalId)) {
            selectedTerminals.push(terminalId);
        }
        element.classList.add('is-selected');
    } else {
        selectedTerminals = selectedTerminals.filter(id => id !== terminalId);
        element.classList.remove('is-selected');
    }

    updateSelectedCount();
}

function selectAllTerminals() {
    document.querySelectorAll('#terminalsContainer input[type="checkbox"]').forEach(checkbox => {
        const terminalId = parseInt(checkbox.value);
        checkbox.checked = true;
        if (!selectedTerminals.includes(terminalId)) {
            selectedTerminals.push(terminalId);
        }
        checkbox.closest('.terminal-checkbox').classList.add('is-selected');
    });
    updateSelectedCount();
}

function clearAllTerminals() {
    document.querySelectorAll('#terminalsContainer input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.closest('.terminal-checkbox').classList.remove('is-selected');
    });
    selectedTerminals = [];
    updateSelectedCount();
}

function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = `${selectedTerminals.length} terminals selected`;
}

function resetForm() {
    document.getElementById('jobAssignmentForm').reset();
    selectedTerminals = [];
    updateSelectedCount();
    setTerminalsPlaceholder('Select a region to view terminals');
    document.getElementById('technicianInfo').textContent = '';
}

function filterAssignments() {
    const filter = document.getElementById('assignmentFilter').value;
    document.querySelectorAll('.assignment-card').forEach(card => {
        card.style.display = (!filter || card.dataset.status === filter) ? 'block' : 'none';
    });
}

function viewAssignment(id) {
    fetch(jobRoutes.show(id), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
    })
    .then(assignment => {
        const tech = assignment.technician;
        document.getElementById('modalTitle').textContent = `Assignment ${assignment.assignment_number || assignment.assignment_id}`;
        document.getElementById('modalContent').innerHTML = `
            <dl class="ja-dl">
                <div>
                    <dt>Technician</dt>
                    <dd>${tech ? escapeHtml(tech.name) : 'N/A'}
                        <small>${tech ? escapeHtml(tech.specialization || '') : ''}</small>
                        <small>${tech ? escapeHtml(tech.phone || '') : ''}</small></dd>
                </div>
                <div>
                    <dt>Location</dt>
                    <dd>${assignment.region ? escapeHtml(assignment.region.name) : 'N/A'}
                        <small>${assignment.terminals_count || (assignment.pos_terminals ? assignment.pos_terminals.length : 0)} terminals assigned</small></dd>
                </div>
                <div><dt>Scheduled</dt><dd>${escapeHtml(assignment.scheduled_date || '—')}</dd></div>
                <div><dt>Service Type</dt><dd>${escapeHtml(assignment.service_type || '—')}</dd></div>
                <div><dt>Priority</dt><dd>${escapeHtml(assignment.priority ? assignment.priority.charAt(0).toUpperCase() + assignment.priority.slice(1) : '—')}</dd></div>
                <div><dt>Status</dt><dd>${escapeHtml((assignment.status || '').replace(/_/g, ' '))}</dd></div>
                ${assignment.notes ? `<div style="grid-column:1/-1;"><dt>Notes</dt><dd style="white-space:pre-wrap;">${escapeHtml(assignment.notes)}</dd></div>` : ''}
            </dl>
            <div class="ja-modal-actions">
                <button type="button" onclick="closeModal()" class="btn-secondary">Close</button>
                <a href="${jobRoutes.page(id)}" class="btn-secondary">Open full page</a>
                ${assignment.status === 'assigned' ? `<button type="button" onclick="editAssignment(${id})" class="btn-primary">Edit Assignment</button>` : ''}
            </div>
        `;
        document.getElementById('assignmentModal').style.display = 'flex';
    })
    .catch(error => {
        console.error('Error loading assignment details:', error);
        alert('Error loading assignment details');
    });
}

function editAssignment(id) {
    window.location.href = jobRoutes.edit(id);
    closeModal();
}

function cancelAssignment(id) {
    if (confirm('Are you sure you want to cancel this assignment?')) {
        fetch(jobRoutes.cancel(id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment cancelled successfully');
                location.reload();
            } else {
                alert('Error cancelling assignment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling assignment');
        });
    }
}

function completeAssignment(id) {
    if (confirm('Mark this assignment as completed?')) {
        fetch(jobRoutes.complete(id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment marked as completed');
                location.reload();
            } else {
                alert('Error completing assignment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error completing assignment');
        });
    }
}

function generateReport(id) {
    // the assignment page carries the full record (terminals, visits, history)
    window.open(jobRoutes.page(id), '_blank');
}

function closeModal() {
    document.getElementById('assignmentModal').style.display = 'none';
}

function exportAssignments() {
    const status = document.getElementById('assignmentFilter').value;
    window.location.href = jobRoutes.export + (status ? `?status=${encodeURIComponent(status)}` : '');
}

function refreshData() {
    location.reload();
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('assignmentModal');
    if (event.target === modal) {
        closeModal();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection
