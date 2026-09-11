{{-- ===== Assignment History tab (styles live in assets/index.blade.php) ===== --}}

{{-- Ensure these metas exist in your base layout (only keep here if not already in layout) --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="app-base-url" content="{{ url('/') }}">

@php
  $statusOptions = $statusOptions ?? [
      'assigned'    => 'Currently Assigned',
      'returned'    => 'Returned',
      'transferred' => 'Transferred',
      'lost'        => 'Lost',
      'damaged'     => 'Damaged',
  ];
  $histBadge = ['assigned' => 'badge-blue', 'returned' => 'badge-green', 'transferred' => 'badge-gray', 'lost' => 'badge-red', 'damaged' => 'badge-yellow'];
  $histLabel = ['assigned' => 'Active', 'returned' => 'Returned', 'transferred' => 'Transferred', 'lost' => 'Lost', 'damaged' => 'Damaged'];
  $condBadge = ['new' => 'badge-green', 'good' => 'badge-green', 'fair' => 'badge-yellow', 'poor' => 'badge-red'];
@endphp

{{-- History Filters --}}
<form method="GET" class="filter-bar">
  <input type="hidden" name="tab" value="history">

  <div class="filter-group as-grow">
    <label class="ui-label" for="hist_employee_search">Search Employee</label>
    <input type="text" name="employee_search" id="hist_employee_search" value="{{ request('employee_search') }}"
           placeholder="Search by employee name or number..." class="ui-input">
  </div>

  <div class="filter-group">
    <label class="ui-label" for="hist_status_filter">Status</label>
    <select name="status_filter" id="hist_status_filter" class="ui-select">
      <option value="">All Status</option>
      @foreach($statusOptions as $key => $label)
        <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="filter-group">
    <label class="ui-label" for="hist_date_from">From Date</label>
    <input type="date" name="date_from" id="hist_date_from" value="{{ request('date_from') }}" class="ui-input">
  </div>

  <div class="filter-group">
    <label class="ui-label" for="hist_date_to">To Date</label>
    <input type="date" name="date_to" id="hist_date_to" value="{{ request('date_to') }}" class="ui-input">
  </div>

  <div class="filter-actions">
    @if(request()->hasAny(['employee_search','status_filter','date_from','date_to']))
      <a href="{{ route('assets.index', ['tab' => 'history']) }}" class="btn-secondary">Clear</a>
    @endif
    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-filter"/></svg> Filter</button>
  </div>
</form>

{{-- Assignment History Table --}}
@if($history->count() > 0)
  <div class="ui-card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="assignment-table">
        <thead>
        <tr>
          <th>Employee</th>
          <th>Asset</th>
          <th class="as-right">Quantity</th>
          <th>Assignment Period</th>
          <th class="as-right">Duration</th>
          <th>Status</th>
          <th>Condition</th>
          <th class="as-right">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($history as $assignment)
          <tr>
            <td>
              <div class="as-cell">
                <span class="mv-avatar" aria-hidden="true">{{ strtoupper(substr($assignment->employee->first_name, 0, 1)) }}{{ strtoupper(substr($assignment->employee->last_name, 0, 1)) }}</span>
                <div class="min-w-0">
                  <div class="cell-primary">{{ $assignment->employee->full_name }}</div>
                  <div class="cell-sub"><span class="mv-mono">{{ $assignment->employee->employee_number }}</span> &middot; {{ $assignment->employee->department->name ?? 'No Department' }}</div>
                </div>
              </div>
            </td>

            <td>
              <div class="cell-primary">{{ $assignment->asset->name }}</div>
              <div class="cell-sub">
                {{ $assignment->asset->category }}
                @if($assignment->asset->sku)
                  &middot; SKU <span class="mv-mono">{{ $assignment->asset->sku }}</span>
                @endif
              </div>
            </td>

            <td class="as-right"><span class="as-num">{{ $assignment->quantity_assigned }}</span></td>

            <td>
              <div class="as-v">{{ $assignment->assignment_date->format('M d, Y') }}</div>
              <div class="cell-sub">
                to {{ $assignment->actual_return_date ? $assignment->actual_return_date->format('M d, Y') : 'Present' }}
              </div>
            </td>

            <td class="as-right"><span class="as-num">{{ (int)$assignment->days_assigned }} days</span></td>

            <td>
              <span class="status-badge badge {{ $histBadge[$assignment->status] ?? 'badge-gray' }} {{ $assignment->status_badge }}">
                {{ $histLabel[$assignment->status] ?? ucfirst($assignment->status) }}
              </span>
            </td>

            <td>
              <div class="as-cond">
                <span class="cell-sub">When Assigned:</span>
                <span class="badge {{ $condBadge[$assignment->condition_when_assigned] ?? 'badge-gray' }}">{{ ucfirst($assignment->condition_when_assigned) }}</span>

                @if($assignment->condition_when_returned)
                  <span class="cell-sub">When Returned:</span>
                  <span class="badge {{ $condBadge[$assignment->condition_when_returned] ?? 'badge-gray' }}">{{ ucfirst($assignment->condition_when_returned) }}</span>
                @endif
              </div>
            </td>

            <td class="as-right">
              <div class="action-group">
                <button type="button"
                        onclick="viewAssignmentHistory({{ $assignment->id }})"
                        class="action-btn" title="Details" aria-label="Details">
                  <svg class="mv-i" aria-hidden="true"><use href="#i-eye"/></svg>
                </button>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    @if($history->hasPages())
      <div class="ui-card-footer as-pager">
        {{ $history->appends(request()->query())->links() }}
      </div>
    @endif
  </div>
@else
  <div class="ui-card">
    <div class="empty-state">
      <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-history"/></svg></div>
      <h3>No Assignment History</h3>
      <p class="empty-state-msg">Returned, transferred, lost and damaged assignments will be listed here.</p>
      <a href="{{ route('assets.index', ['tab' => 'assignments']) }}" class="btn-secondary btn-sm">Current Assignments</a>
    </div>
  </div>
@endif

{{-- Assignment History Detail Modal --}}
<div id="assignmentHistoryModal" class="as-overlay" style="display:none;" role="dialog" aria-modal="true">
  <div class="as-modal is-wide">
    <div class="as-modal-head">
      <h3 id="historyModalTitle">Assignment History</h3>
      <button id="historyModalClose" type="button" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
    </div>
    <div id="historyModalBody" class="as-modal-body"></div>
  </div>
</div>

<script>
  const BASE = (document.querySelector('meta[name="app-base-url"]')?.content || '').replace(/\/$/, '');
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // Open + populate modal
  window.viewAssignmentHistory = function (assignmentId) {
    fetch(`${BASE}/asset-assignments/${assignmentId}/data`, {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(async (response) => {
      const raw = await response.text();
      let data = null; try { data = raw ? JSON.parse(raw) : null; } catch {}
      if (!response.ok || !data?.success) {
        const msg = (data && (data.message || JSON.stringify(data.errors || data))) || raw || `HTTP ${response.status}`;
        throw new Error(msg);
      }
      return data;
    })
    .then(({ assignment, days_assigned }) => {
      const fullName = assignment.employee.full_name || `${assignment.employee.first_name} ${assignment.employee.last_name}`.trim();
      const returnedTo = assignment.returnedTo ? (assignment.returnedTo.full_name || `${assignment.returnedTo.first_name ?? ''} ${assignment.returnedTo.last_name ?? ''}`.trim()) : 'Not returned';
      const assignedBy = assignment.assignedBy ? (assignment.assignedBy.full_name || `${assignment.assignedBy.first_name ?? ''} ${assignment.assignedBy.last_name ?? ''}`.trim()) : 'System';

      document.getElementById('historyModalTitle').textContent =
        `${assignment.asset.name} → ${fullName}`;

      document.getElementById('historyModalBody').innerHTML = `
        <div class="as-detail">
          <section class="as-kv">
            <div>
              <h4 class="as-sec">Employee</h4>
              <div class="as-k">Name</div><div class="as-v is-strong">${fullName}</div>
              <div class="as-k" style="margin-top:8px">Number</div><div class="as-v mv-mono">${assignment.employee.employee_number}</div>
              <div class="as-k" style="margin-top:8px">Department</div><div class="as-v">${assignment.employee.department?.name ?? 'Not assigned'}</div>
            </div>
            <div>
              <h4 class="as-sec">Asset</h4>
              <div class="as-k">Name</div><div class="as-v is-strong">${assignment.asset.name}</div>
              <div class="as-k" style="margin-top:8px">Category</div><div class="as-v">${assignment.asset.category}</div>
              <div class="as-k" style="margin-top:8px">SKU</div><div class="as-v mv-mono">${assignment.asset.sku ?? 'Not assigned'}</div>
            </div>
          </section>

          <section>
            <h4 class="as-sec">Timeline</h4>
            <div class="as-kv is-3">
              <div><div class="as-k">Assigned</div><div class="as-v">${new Date(assignment.assignment_date).toLocaleDateString()}</div></div>
              <div><div class="as-k">Expected Return</div><div class="as-v">${assignment.expected_return_date ? new Date(assignment.expected_return_date).toLocaleDateString() : 'Not set'}</div></div>
              <div><div class="as-k">Actual Return</div><div class="as-v">${assignment.actual_return_date ? new Date(assignment.actual_return_date).toLocaleDateString() : 'Not returned'}</div></div>
              <div><div class="as-k">Duration</div><div class="as-v">${(parseInt(days_assigned,10) || 0)} days</div></div>
              <div><div class="as-k">Quantity</div><div class="as-v">${assignment.quantity_assigned}</div></div>
              <div><div class="as-k">Status</div><div class="as-v"><span class="badge badge-gray">${assignment.status}</span></div></div>
            </div>
          </section>

          <section>
            <h4 class="as-sec">Condition</h4>
            <div class="as-kv">
              <div><div class="as-k">When Assigned</div><div class="as-v">${assignment.condition_when_assigned}</div></div>
              <div><div class="as-k">When Returned</div><div class="as-v">${assignment.condition_when_returned ?? 'Not returned yet'}</div></div>
            </div>
          </section>

          ${(assignment.assignment_notes || assignment.return_notes) ? `
            <section>
              <h4 class="as-sec">Notes</h4>
              ${assignment.assignment_notes ? `<div class="as-k">Assignment</div><p class="as-note">${assignment.assignment_notes}</p>` : ''}
              ${assignment.return_notes ? `<div class="as-k" style="margin-top:10px">Return/Transfer</div><p class="as-note">${assignment.return_notes}</p>` : ''}
            </section>` : ''
          }

          <section>
            <h4 class="as-sec">People</h4>
            <div class="as-kv">
              <div><div class="as-k">Assigned By</div><div class="as-v">${assignedBy}</div></div>
              <div><div class="as-k">Returned To</div><div class="as-v">${returnedTo}</div></div>
            </div>
          </section>
        </div>
      `;
      document.getElementById('assignmentHistoryModal').style.display = 'flex';
    })
    .catch(err => {
      console.error(err);
      alert('Failed to load assignment history');
    });
  };

  // Close helpers
  function closeHistoryModal(){
    document.getElementById('assignmentHistoryModal').style.display='none';
  }
  document.getElementById('historyModalClose')?.addEventListener('click', closeHistoryModal);
  document.getElementById('assignmentHistoryModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'assignmentHistoryModal') closeHistoryModal();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.getElementById('assignmentHistoryModal').style.display === 'flex') {
      closeHistoryModal();
    }
  });
</script>
