{{-- resources/views/deployment/site-visit.blade.php --}}
@extends('layouts.app')

@section('title', $prefillAssignment
    ? 'Site Visits — '.$prefillAssignment->project->project_name.' ('.$prefillAssignment->client->company_name.')'
    : 'Site Visits')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
$a = $prefillAssignment; // alias for cleaner checks
@endphp

<div class="container-fluid py-4">

  {{-- Back --}}
  <div class="mb-3">
    <a href="{{ url()->previous() }}" class="btn btn-back">← Back</a>
  </div>

  {{-- ====== JOB DETAILS ====== --}}
  <div class="card mb-4">
    <div class="card-header">
      <h6 class="card-title">Job Details</h6>
    </div>
    <div class="card-body">
      <div class="details-grid">

        {{-- Technician Section --}}
        <div class="detail-section">
          <h6 class="section-title">Technician</h6>
          <div class="detail-item">
            <span class="detail-label">Technician *</span>
            <span class="detail-value">
              @if($a && $a->technician)
                {{ $a->technician->first_name }} {{ $a->technician->last_name }}
              @else
                <span class="text-muted">—</span>
              @endif
            </span>
          </div>
        </div>

        {{-- Job Assignment Section --}}
        <div class="detail-section">
          <h6 class="section-title">Job Assignment</h6>
          <div class="detail-item">
            <span class="detail-label">Project & Client</span>
            <span class="detail-value">
              @if($a && $a->project && $a->client)
                {{ $a->project->project_name }} - {{ $a->client->company_name }}
              @else
                <span class="text-muted">—</span>
              @endif
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Scheduled Date</span>
            <span class="detail-value">
              @if($a && $a->scheduled_date)
                {{ $a->scheduled_date->format('M j, Y') }}
              @else
                <span class="text-muted">—</span>
              @endif
            </span>
          </div>
        </div>

        {{-- Team Members Section (only show if team assignment exists) --}}
        @if($a && isset($a->team_members) && !empty($a->team_members))
        <div class="detail-section">
          <h6 class="section-title">Team Members</h6>
          @foreach($a->team_members as $member)
          <div class="detail-item">
            <span class="detail-label">Member {{ $loop->iteration }}</span>
            <span class="detail-value">{{ $member['name'] ?? $member }}</span>
          </div>
          @endforeach
        </div>
        @endif

      </div>
    </div>
  </div>

  {{-- ====== TERMINALS LIST ====== --}}
  <div class="card">
    <div class="card-header">
      <h6 class="card-title">Terminals</h6>

      <div class="header-actions">
        {{-- Search --}}
        <div class="search-box">
          <input type="text"
                 id="terminalSearch"
                 placeholder="Search terminals..."
                 class="form-control">
          <i class="fas fa-search search-icon"></i>
        </div>

        {{-- Add terminal not in assignment --}}
        <div class="input-group add-terminal-group">
          <input type="text"
                 id="addTerminalInput"
                 class="form-control"
                 placeholder="Add terminal found onsite (by ID)">
          <button id="btnAddTerminal" class="btn btn-secondary">Add</button>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="terminals-table">
          <thead>
            <tr>
              <th>Terminal ID</th>
              <th>Merchant</th>
              <th>Device</th>
              <th>Serial</th>
              <th>Address</th>
              <th>Location</th>
              <th>Visit Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="terminalsTableBody">

            @php
            // Build terminal list + latest visit cache
            $termsList = isset($terminals) && $terminals instanceof \Illuminate\Support\Collection
                ? $terminals
                : collect();

            $visitsByTerminal = collect();

            if ($a && $termsList->count()) {
                $visitsByTerminal = \App\Models\TechnicianVisit::query()
                    ->where('job_assignment_id', $a->id)
                    ->whereIn('pos_terminal_id', $termsList->pluck('id'))
                    ->latest('started_at')
                    ->get()
                    ->groupBy('pos_terminal_id');
            }
            @endphp

            {{-- Existing assignment terminals --}}
            @forelse ($terminals as $t)
              @php
              $latestVisit = optional($visitsByTerminal[$t->id] ?? collect())->first();

              $searchableData = implode(' ', array_filter([
                  $t->merchant_name,
                  $t->terminal_id,
                  $t->terminal_model ?? '',
                  $t->serial_number ?? '',
                  $t->physical_address ?? $t->address ?? '',
                  $t->city ?? '',
                  $t->province ?? ''
              ]));
              @endphp

              <tr class="terminal-row"
                  data-terminal-id="{{ $t->id }}"
                  data-searchable="{{ strtolower($searchableData) }}">

                <td>
                  <code class="terminal-id">{{ $t->terminal_id }}</code>
                </td>

                <td>{{ $t->merchant_name ?? '—' }}</td>
                <td>{{ $t->terminal_model ?? '—' }}</td>
                <td>{{ $t->serial_number ?? '—' }}</td>
                <td>{{ $t->physical_address ?? $t->address ?? '—' }}</td>

                <td>
                  {{ $t->city ?? '—' }}
                  @if($t->province)
                    , <span class="province">{{ $t->province }}</span>
                  @endif
                </td>

                <td>
                  @if($latestVisit)
                    @php
                    $statusText = $latestVisit->terminal_status_during_visit
                        ?? $latestVisit->status;
                    @endphp
                    <span class="badge badge-status-{{ $statusText }}">
                      {{ \Illuminate\Support\Str::headline($statusText) }}
                    </span>
                  @else
                    <span class="text-muted">Not visited</span>
                  @endif
                </td>

                <td class="text-end">
                  {{-- Edit - goes to detailed form page --}}
                  @if($a)
                    <a class="btn btn-primary btn-sm"
                       href="{{ route('site_visits.edit_terminal', [
                         'assignment_id' => $a->id,
                         'terminal_id' => $t->id
                       ]) }}">
                      Edit
                    </a>
                  @endif

                  {{-- View More - shows reports/history --}}
                  <a class="btn btn-outline btn-sm"
                     href="{{ route('reports.technician-visits') }}?terminal_id={{ $t->id }}">
                    View More
                  </a>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">
                  No terminals assigned to this job.
                </td>
              </tr>
            @endforelse

            {{-- Placeholder container for terminals added from onsite input --}}
            <tr id="extraTerminalsAnchor" style="display:none;"></tr>

          </tbody>
        </table>
      </div>

      {{-- No search results message --}}
      <div id="noResults" class="no-results" style="display:none;">
        <div class="text-center py-4">
          <i class="fas fa-search text-muted mb-2"></i>
          <p class="text-muted mb-0">No terminals match your search.</p>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* Card Styles */
.card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}

.card-header {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
}

.card-title {
    margin: 0;
    font-weight: 600;
    color: #374151;
}

.card-body {
    padding: 1.25rem;
}

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .5rem .75rem;
    border-radius: 6px;
    border: 1px solid transparent;
    text-decoration: none;
    font-size: .875rem;
    cursor: pointer;
}

.btn-back {
    border-color: #d1d5db;
    color: #374151;
    background: #fff;
}

.btn-back:hover {
    background: #f9fafb;
}

.btn-primary {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #fff;
}

.btn-primary:hover {
    background: #2563eb;
    border-color: #2563eb;
}

.btn-secondary {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #374151;
}

.btn-outline {
    background: transparent;
    border-color: #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
}

.btn-sm {
    padding: .35rem .55rem;
    font-size: .8125rem;
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.section-title {
    font-size: .75rem;
    text-transform: uppercase;
    color: #6b7280;
    font-weight: 700;
    letter-spacing: .05em;
    margin: 0 0 .75rem;
}

.detail-section .detail-item {
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding: .5rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.detail-section .detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    color: #6b7280;
    font-weight: 500;
    flex-shrink: 0;
    margin-right: 1rem;
}

.detail-value {
    color: #111827;
    text-align: right;
}

/* Header Actions */
.header-actions {
    display: flex;
    gap: .75rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    min-width: 240px;
}

.search-box .form-control {
    padding-right: 2.5rem;
}

.search-icon {
    position: absolute;
    right: .7rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.add-terminal-group {
    min-width: 340px;
    display: flex;
}

.add-terminal-group input {
    flex: 1;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.add-terminal-group button {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-left: none;
}

/* Table Styles */
.terminals-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .875rem;
}

.terminals-table th {
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
    padding: .75rem .8rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
}

.terminals-table td {
    padding: .75rem .8rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.terminals-table tbody tr:hover {
    background: #f8fafc;
}

/* No Results */
.no-results {
    border-top: 1px solid #f3f4f6;
}

/* Badge Styles */
.badge {
    display: inline-block;
    padding: .25rem .6rem;
    border-radius: 999px;
    background: #f3f4f6;
    color: #374151;
    font-size: .75rem;
    font-weight: 500;
}

.badge-status-working {
    background: #dcfce7;
    color: #166534;
}

.badge-status-not_working {
    background: #fecaca;
    color: #991b1b;
}

.badge-status-needs_maintenance {
    background: #fed7aa;
    color: #c2410c;
}

.badge-status-not_found {
    background: #f3f4f6;
    color: #6b7280;
}

.badge-status-open,
.badge-status-in_progress {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-status-closed,
.badge-status-completed {
    background: #dcfce7;
    color: #166534;
}

/* Form Controls */
.form-control {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: .5rem .7rem;
    font-size: .875rem;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
}

/* Utility Classes */
.text-end {
    text-align: right;
}

.text-muted {
    color: #6b7280;
}

code.terminal-id {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: .2rem .4rem;
    font-family: 'SF Mono', Monaco, monospace;
    font-size: .8rem;
}

.province {
    color: #6b7280;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-actions {
        width: 100%;
        flex-direction: column;
    }

    .search-box,
    .add-terminal-group {
        min-width: unset;
        width: 100%;
    }

    .details-grid {
        grid-template-columns: 1fr;
    }

    .detail-item {
        flex-direction: column;
        align-items: start;
        gap: .25rem;
    }

    .detail-value {
        text-align: left;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // DOM Elements
  const tableBody = document.getElementById('terminalsTableBody');
  const noResults = document.getElementById('noResults');
  const searchEl = document.getElementById('terminalSearch');
  const addInput = document.getElementById('addTerminalInput');
  const addBtn = document.getElementById('btnAddTerminal');
  const anchorRow = document.getElementById('extraTerminalsAnchor');

  // Current assignment id (0 if none)
  const ASSIGNMENT_ID = {{ $a?->id ?? 0 }};

  // 1) Search Functionality
  if (searchEl) {
    searchEl.addEventListener('input', () => {
      const needle = searchEl.value.toLowerCase().trim();
      const rows = tableBody.querySelectorAll('.terminal-row');
      let visible = 0;

      rows.forEach(row => {
        const haystack = row.getAttribute('data-searchable') || '';
        const shouldShow = !needle || haystack.includes(needle);
        row.style.display = shouldShow ? '' : 'none';
        if (shouldShow) visible++;
      });

      // Show no results message if search term exists but no results
      const hasSearchTerm = needle.length > 0;
      const hasResults = visible > 0;
      noResults.style.display = (hasSearchTerm && !hasResults) ? 'block' : 'none';
    });
  }

  // 2) Add Terminal Functionality (for terminals found onsite)
  async function addExtraTerminal() {
    const rawInput = (addInput.value || '').trim();
    if (!rawInput) return;

    const terminalId = parseInt(rawInput, 10);
    if (!terminalId || isNaN(terminalId)) {
      alert('Please enter a valid numeric terminal ID');
      return;
    }

    // Check if terminal already exists in table
    if (tableBody.querySelector(`tr[data-terminal-id="${terminalId}"]`)) {
      alert('This terminal is already in the list');
      addInput.value = '';
      return;
    }

    try {
      const lookupUrl = "{{ url('/site-visits/lookup/terminal') }}/" + terminalId;
      const response = await fetch(lookupUrl, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });

      if (!response.ok) {
        if (response.status === 404) {
          throw new Error('Terminal not found');
        }
        throw new Error(`Server error: ${response.status}`);
      }

      const terminal = await response.json();

      // Build edit and report links
      const editUrl = ASSIGNMENT_ID
        ? "{{ route('site_visits.edit_terminal', ['assignment_id' => '__ASSIGN__', 'terminal_id' => '__TERM__']) }}"
            .replace('__ASSIGN__', ASSIGNMENT_ID)
            .replace('__TERM__', terminal.id)
        : '#';

      const viewUrl = "{{ route('reports.technician-visits') }}" +
                      '?terminal_id=' + terminal.id;

      // Create new table row
      const newRow = document.createElement('tr');
      newRow.className = 'terminal-row';
      newRow.setAttribute('data-terminal-id', terminal.id);

      const searchableData = [
        terminal.merchant_name || '',
        terminal.terminal_id || '',
        terminal.terminal_model || '',
        terminal.serial_number || '',
        terminal.address || '',
        terminal.city || '',
        terminal.province || ''
      ].join(' ').toLowerCase();

      newRow.setAttribute('data-searchable', searchableData);

      newRow.innerHTML = `
        <td>
          <code class="terminal-id">${terminal.terminal_id ?? '—'}</code>
        </td>
        <td>${terminal.merchant_name ?? '—'}</td>
        <td>${terminal.terminal_model ?? '—'}</td>
        <td>${terminal.serial_number ?? '—'}</td>
        <td>${terminal.address ?? '—'}</td>
        <td>
          ${terminal.city ?? '—'}${
            terminal.province ?
            ', <span class="province">' + terminal.province + '</span>' :
            ''
          }
        </td>
        <td><span class="text-muted">Not visited</span></td>
        <td class="text-end">
          <a class="btn btn-primary btn-sm" href="${editUrl}">Edit</a>
          <a class="btn btn-outline btn-sm" href="${viewUrl}">View More</a>
        </td>
      `;

      // Show anchor row and append new terminal
      anchorRow.style.display = '';
      tableBody.appendChild(newRow);

      // Clear inputs and reset search
      addInput.value = '';
      if (searchEl) {
        searchEl.value = '';
        searchEl.dispatchEvent(new Event('input'));
      }

      // Show success message
      const successMsg = document.createElement('div');
      successMsg.className = 'alert alert-success';
      successMsg.style.cssText = 'position:fixed;top:20px;right:20px;z-index:1000;padding:10px 15px;background:#dcfce7;border:1px solid #166534;color:#166534;border-radius:6px;';
      successMsg.textContent = `Terminal ${terminal.terminal_id} added successfully`;
      document.body.appendChild(successMsg);

      setTimeout(() => successMsg.remove(), 3000);

    } catch (error) {
      console.error('Error adding terminal:', error);
      alert(error.message || 'Failed to find terminal. Please check the ID and try again.');
    }
  }

  // Event Listeners
  if (addBtn) {
    addBtn.addEventListener('click', addExtraTerminal);
  }

  if (addInput) {
    addInput.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault();
        addExtraTerminal();
      }
    });
  }
});
</script>
@endpush
