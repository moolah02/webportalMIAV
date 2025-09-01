{{-- ===== Assignment History (drop-in replacement) ===== --}}

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
@endphp

<!-- History Filters -->
<div class="content-card" style="margin-block-end: 20px;">
  <form method="GET" style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto auto;gap:15px;align-items:end;">
    <input type="hidden" name="tab" value="history">

    <div>
      <label>Search Employee</label>
      <input type="text" name="employee_search" value="{{ request('employee_search') }}"
             placeholder="Search by employee name or number..."
             style="width:100%;padding:10px;border:2px solid #ddd;border-radius:6px;">
    </div>

    <div>
      <label>Status</label>
      <select name="status_filter" style="width:100%;padding:10px;border:2px solid #ddd;border-radius:6px;">
        <option value="">All Status</option>
        @foreach($statusOptions as $key => $label)
          <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label>From Date</label>
      <input type="date" name="date_from" value="{{ request('date_from') }}"
             style="width:100%;padding:10px;border:2px solid #ddd;border-radius:6px;">
    </div>

    <div>
      <label>To Date</label>
      <input type="date" name="date_to" value="{{ request('date_to') }}"
             style="width:100%;padding:10px;border:2px solid #ddd;border-radius:6px;">
    </div>

    <button type="submit" class="btn btn-primary">Filter</button>

    @if(request()->hasAny(['employee_search','status_filter','date_from','date_to']))
      <a href="{{ route('assets.index', ['tab' => 'history']) }}" class="btn">Clear</a>
    @endif
  </form>
</div>

<!-- Assignment History Table -->
@if($history->count() > 0)
  <div class="content-card">
    <div style="overflow-x:auto;">
      <table class="assignment-table">
        <thead>
        <tr>
          <th>Employee</th>
          <th>Asset</th>
          <th>Quantity</th>
          <th>Assignment Period</th>
          <th>Duration</th>
          <th>Status</th>
          <th>Condition</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($history as $assignment)
          <tr>
            <td>
              <div class="employee-info">
                <div class="employee-avatar">
                  {{ strtoupper(substr($assignment->employee->first_name, 0, 1)) }}{{ strtoupper(substr($assignment->employee->last_name, 0, 1)) }}
                </div>
                <div>
                  <div style="font-weight:600;color:#333;">{{ $assignment->employee->full_name }}</div>
                  <div style="font-size:12px;color:#666;">{{ $assignment->employee->employee_number }}</div>
                  <div style="font-size:12px;color:#666;">{{ $assignment->employee->department->name ?? 'No Department' }}</div>
                </div>
              </div>
            </td>

            <td>
              <div class="asset-info">
                <div class="asset-icon">📦</div>
                <div>
                  <div style="font-weight:600;color:#333;">{{ $assignment->asset->name }}</div>
                  <div style="font-size:12px;color:#666;">{{ $assignment->asset->category }}</div>
                  @if($assignment->asset->sku)
                    <div style="font-size:12px;color:#999;">SKU: {{ $assignment->asset->sku }}</div>
                  @endif
                </div>
              </div>
            </td>

            <td>
              <span style="background:#e3f2fd;color:#1976d2;padding:4px 8px;border-radius:12px;font-weight:600;">
                {{ $assignment->quantity_assigned }}
              </span>
            </td>

            <td>
              <div>
                <div style="font-weight:500;color:#333;">{{ $assignment->assignment_date->format('M d, Y') }}</div>
                <div style="font-size:12px;color:#666;">
                  to {{ $assignment->actual_return_date ? $assignment->actual_return_date->format('M d, Y') : 'Present' }}
                </div>
              </div>
            </td>

            <td>
              <span style="font-weight:600;color:#666;">{{ (int)$assignment->days_assigned }} days</span>
            </td>

            <td>
              <span class="status-badge {{ $assignment->status_badge }}">
                @switch($assignment->status)
                  @case('assigned')    🟢 Active @break
                  @case('returned')    ✅ Returned @break
                  @case('transferred') 🔄 Transferred @break
                  @case('lost')        ❌ Lost @break
                  @case('damaged')     ⚠️ Damaged @break
                  @default             {{ ucfirst($assignment->status) }}
                @endswitch
              </span>
            </td>

            <td>
              <div>
                <div style="font-size:12px;color:#666;">When Assigned:</div>
                <span class="status-badge">{{ ucfirst($assignment->condition_when_assigned) }}</span>

                @if($assignment->condition_when_returned)
                  <div style="font-size:12px;color:#666;margin-top:4px;">When Returned:</div>
                  <span class="status-badge">{{ ucfirst($assignment->condition_when_returned) }}</span>
                @endif
              </div>
            </td>

            <td>
              <div style="display:flex;gap:5px;">
                <button type="button"
                        onclick="viewAssignmentHistory({{ $assignment->id }})"
                        class="btn-small"
                        style="background:#f0f8ff;color:#1976d2;border:1px solid #1976d2;padding:6px 12px;border-radius:4px;cursor:pointer;font-size:12px;">
                  📋 Details
                </button>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    @if($history->hasPages())
      <div style="margin-top:20px;display:flex;justify-content:center;">
        {{ $history->appends(request()->query())->links() }}
      </div>
    @endif
  </div>
@else
  <div class="content-card" style="text-align:center;padding:60px;color:#666;">
    <div style="font-size:64px;margin-block-end:20px;">📋</div>
    <h3>No Assignment History</h3>
  </div>
@endif

<!-- Assignment History Detail Modal -->
<div id="assignmentHistoryModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100vh;background:rgba(0,0,0,0.5);z-index:2000;justify-content:center;align-items:center;">
  <div style="background:white;border-radius:12px;max-width:800px;width:95%;max-height:90vh;overflow-y:auto;box-shadow:0 10px 30px rgba(0,0,0,0.3);position:relative;padding:0;">
    <div style="background:linear-gradient(135deg,#2196f3 0%,#1976d2 100%);color:white;padding:16px 20px;border-radius:12px 12px 0 0;display:flex;align-items:center;justify-content:space-between;">
      <h3 style="margin:0;display:flex;align-items:center;gap:10px;font-size:16px;">
        <span>📋</span>
        <span id="historyModalTitle">Assignment History</span>
      </h3>
      <button id="historyModalClose" type="button" style="background:none;border:none;color:white;font-size:24px;cursor:pointer;line-height:1;">×</button>
    </div>
    <div id="historyModalBody" style="padding:20px;"></div>
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
        <div style="display:grid;gap:20px;">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
              <h4 style="margin:0 0 10px;color:#333;">👤 Employee</h4>
              <div style="background:#f8f9fa;padding:15px;border-radius:8px;">
                <div><strong>Name:</strong> ${fullName}</div>
                <div><strong>Number:</strong> ${assignment.employee.employee_number}</div>
                <div><strong>Department:</strong> ${assignment.employee.department?.name ?? 'Not assigned'}</div>
              </div>
            </div>
            <div>
              <h4 style="margin:0 0 10px;color:#333;">📦 Asset</h4>
              <div style="background:#f8f9fa;padding:15px;border-radius:8px;">
                <div><strong>Name:</strong> ${assignment.asset.name}</div>
                <div><strong>Category:</strong> ${assignment.asset.category}</div>
                <div><strong>SKU:</strong> ${assignment.asset.sku ?? 'Not assigned'}</div>
              </div>
            </div>
          </div>

          <div>
            <h4 style="margin:0 0 10px;color:#333;">📋 Timeline</h4>
            <div style="background:#f8f9fa;padding:15px;border-radius:8px;">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div><strong>Assigned:</strong> ${new Date(assignment.assignment_date).toLocaleDateString()}</div>
                <div><strong>Expected Return:</strong> ${assignment.expected_return_date ? new Date(assignment.expected_return_date).toLocaleDateString() : 'Not set'}</div>
                <div><strong>Actual Return:</strong> ${assignment.actual_return_date ? new Date(assignment.actual_return_date).toLocaleDateString() : 'Not returned'}</div>
                <div><strong>Duration:</strong> ${(parseInt(days_assigned,10) || 0)} days</div>
                <div><strong>Quantity:</strong> ${assignment.quantity_assigned}</div>
                <div><strong>Status:</strong> ${assignment.status}</div>
              </div>
            </div>
          </div>

          <div>
            <h4 style="margin:0 0 10px;color:#333;">🔧 Condition</h4>
            <div style="background:#f8f9fa;padding:15px;border-radius:8px;">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div><strong>When Assigned:</strong> ${assignment.condition_when_assigned}</div>
                <div><strong>When Returned:</strong> ${assignment.condition_when_returned ?? 'Not returned yet'}</div>
              </div>
            </div>
          </div>

          ${(assignment.assignment_notes || assignment.return_notes) ? `
            <div>
              <h4 style="margin:0 0 10px;color:#333;">📝 Notes</h4>
              <div style="background:#f8f9fa;padding:15px;border-radius:8px;">
                ${assignment.assignment_notes ? `<div style="margin-bottom:10px;"><strong>Assignment:</strong><br>${assignment.assignment_notes}</div>` : ''}
                ${assignment.return_notes ? `<div><strong>Return/Transfer:</strong><br>${assignment.return_notes}</div>` : ''}
              </div>
            </div>` : ''
          }

          <div>
            <h4 style="margin:0 0 10px;color:#333;">👥 People</h4>
            <div style="background:#f8f9fa;padding:15px;border-radius:8px;">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div><strong>Assigned By:</strong> ${assignedBy}</div>
                <div><strong>Returned To:</strong> ${returnedTo}</div>
              </div>
            </div>
          </div>
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
