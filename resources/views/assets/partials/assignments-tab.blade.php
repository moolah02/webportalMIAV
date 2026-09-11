{{-- Current Assignments tab (styles live in assets/index.blade.php) --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="app-base-url" content="{{ url('/') }}">

@php
    $condBadge = ['new' => 'badge-green', 'good' => 'badge-green', 'fair' => 'badge-yellow', 'poor' => 'badge-red'];
@endphp

{{-- Assignment Statistics --}}
<div class="as-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-user-check"/></svg></div>
        <div class="min-w-0">
            <div class="stat-number">{{ $assignmentStats['active_assignments'] ?? 0 }}</div>
            <div class="stat-label">Active Assignments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ ($assignmentStats['overdue_assignments'] ?? 0) > 0 ? 'stat-icon-red' : '' }}"><svg class="mv-i" aria-hidden="true"><use href="#i-clock"/></svg></div>
        <div class="min-w-0">
            <div class="stat-number">{{ $assignmentStats['overdue_assignments'] ?? 0 }}</div>
            <div class="stat-label">Overdue Returns</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-undo"/></svg></div>
        <div class="min-w-0">
            <div class="stat-number">{{ $assignmentStats['returned_this_month'] ?? 0 }}</div>
            <div class="stat-label">Returned This Month</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-layers"/></svg></div>
        <div class="min-w-0">
            <div class="stat-number">{{ $assignmentStats['total_assignments'] ?? 0 }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <input type="hidden" name="tab" value="assignments">

    <div class="filter-group as-grow">
        <label class="ui-label" for="asg_employee_search">Search Employee</label>
        <input type="text" name="employee_search" id="asg_employee_search" value="{{ request('employee_search') }}"
               placeholder="Search by employee name or number..." class="ui-input">
    </div>

    <div class="filter-group as-grow">
        <label class="ui-label" for="asg_asset_search">Search Asset</label>
        <input type="text" name="asset_search" id="asg_asset_search" value="{{ request('asset_search') }}"
               placeholder="Search by asset name or SKU..." class="ui-input">
    </div>

    <div class="filter-group">
        <label class="ui-label" for="asg_department">Department</label>
        <select name="department" id="asg_department" class="ui-select">
            <option value="">All Departments</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="as-check" for="overdue_filter">
            <input type="checkbox" name="overdue_only" value="1" {{ request('overdue_only') ? 'checked' : '' }} id="overdue_filter">
            Overdue Only
        </label>
    </div>

    <div class="filter-actions">
        @if(request()->hasAny(['employee_search', 'asset_search', 'department', 'overdue_only']))
        <a href="{{ route('assets.index', ['tab' => 'assignments']) }}" class="btn-secondary">Clear</a>
        @endif
        <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-filter"/></svg> Filter</button>
    </div>
</form>

{{-- Current Assignments Table --}}
@if($assignments->count() > 0)
    <div class="ui-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="assignment-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Asset</th>
                        <th class="as-right">Quantity</th>
                        <th>Assigned Date</th>
                        <th>Expected Return</th>
                        <th class="as-right">Days Assigned</th>
                        <th>Condition</th>
                        <th class="as-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    <tr class="{{ $assignment->is_overdue ? 'overdue-row' : '' }}">
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
                            <div class="cell-sub">by {{ $assignment->assignedBy->full_name ?? 'System' }}</div>
                        </td>
                        <td>
                            @if($assignment->expected_return_date)
                                <div class="as-v {{ $assignment->is_overdue ? 'as-late' : '' }}">
                                    {{ $assignment->expected_return_date->format('M d, Y') }}
                                </div>
                                @if($assignment->is_overdue)
                                    <span class="badge badge-red mt-1">{{ $assignment->days_overdue }} days overdue</span>
                                @endif
                            @else
                                <span class="as-muted">No due date</span>
                            @endif
                        </td>
                        <td class="as-right"><span class="as-num">{{ (int)$assignment->days_assigned }} days</span></td>
                        <td>
                            <span class="badge {{ $condBadge[$assignment->condition_when_assigned] ?? 'badge-gray' }}">
                                {{ ucfirst($assignment->condition_when_assigned) }}
                            </span>
                        </td>
                        <td class="as-right">
                            <div class="action-group">
                                <button type="button" onclick="openReturnModal({{ $assignment->id }})"
                                        class="action-btn" title="Return Asset" aria-label="Return Asset">
                                    <svg class="mv-i" aria-hidden="true"><use href="#i-undo"/></svg>
                                </button>
                                <button type="button" onclick="openTransferModal({{ $assignment->id }})"
                                        class="action-btn" title="Transfer Asset" aria-label="Transfer Asset">
                                    <svg class="mv-i" aria-hidden="true"><use href="#i-refresh"/></svg>
                                </button>
                                <button type="button" onclick="viewAssignmentDetails({{ $assignment->id }})"
                                        class="action-btn" title="View Details" aria-label="View Details">
                                    <svg class="mv-i" aria-hidden="true"><use href="#i-eye"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
        <div class="ui-card-footer as-pager">
            {{ $assignments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
@else
    <div class="ui-card">
        <div class="empty-state">
            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-user-check"/></svg></div>
            <h3>No Active Assignments</h3>
            <p class="empty-state-msg">No assets are currently assigned to employees.</p>
            <a href="{{ route('assets.index', ['tab' => 'assign']) }}" class="btn-primary btn-sm">Assign First Asset</a>
        </div>
    </div>
@endif

{{-- MODALS --}}

{{-- Return Asset Modal --}}
<div id="returnAssetModal" class="as-overlay" style="display: none;" role="dialog" aria-modal="true">
    <div class="as-modal">
        <div class="as-modal-head">
            <h3>Return Asset</h3>
            <button type="button" onclick="closeReturnModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="as-modal-body">
            <div id="returnAssignmentInfo" class="as-summary">
                <h4 class="as-sec">Assignment Details</h4>
                <div class="as-kv">
                    <div><div class="as-k">Asset</div><div class="as-v is-strong" id="return_asset_name">Loading...</div></div>
                    <div><div class="as-k">Employee</div><div class="as-v is-strong" id="return_employee_name">Loading...</div></div>
                </div>
                <div class="as-kv is-3" style="margin-top:12px;">
                    <div><div class="as-k">Assigned Date</div><div class="as-v" id="return_assigned_date">Loading...</div></div>
                    <div><div class="as-k">Days Assigned</div><div class="as-v" id="return_days_assigned">0 days</div></div>
                    <div><div class="as-k">Quantity</div><div class="as-v" id="return_quantity">1</div></div>
                </div>

                {{-- Overdue Warning --}}
                <div id="overdue_warning" class="as-alert" style="display: none;"></div>
            </div>

            <form id="returnAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="return_assignment_id" name="assignment_id">

                <div class="as-stack">
                    <div class="as-grid">
                        <div>
                            <label class="ui-label" for="return_date">Return Date <span class="as-req">*</span></label>
                            <input type="date" name="return_date" id="return_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label" for="return_condition">Condition When Returned <span class="as-req">*</span></label>
                            <select name="condition_when_returned" id="return_condition" required class="ui-select w-full">
                                <option value="">Select condition...</option>
                                <option value="new">New - Like brand new</option>
                                <option value="good">Good - Minor wear, fully functional</option>
                                <option value="fair">Fair - Noticeable wear, some issues</option>
                                <option value="poor">Poor - Significant damage/issues</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="ui-label">Return Notes</label>
                        <textarea name="return_notes" rows="3" class="ui-textarea w-full"
                                  placeholder="Optional notes about the return (e.g., reason for return, any issues)..."></textarea>
                    </div>

                    <div>
                        <span class="ui-label">Update Asset Status</span>
                        <div class="as-choices">
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="available" checked>
                                <svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg> Available
                            </label>
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="maintenance">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-wrench"/></svg> Maintenance
                            </label>
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="damaged">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg> Damaged
                            </label>
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="retired">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-ban"/></svg> Retired
                            </label>
                        </div>
                    </div>
                </div>

                <div class="as-modal-actions">
                    <button type="button" onclick="closeReturnModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-undo"/></svg> Process Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Transfer Asset Modal --}}
<div id="transferAssetModal" class="as-overlay" style="display: none;" role="dialog" aria-modal="true">
    <div class="as-modal">
        <div class="as-modal-head">
            <h3>Transfer Asset</h3>
            <button type="button" onclick="closeTransferModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="as-modal-body">
            <div id="transferAssignmentInfo" class="as-summary">
                <h4 class="as-sec">Current Assignment</h4>
                <div class="as-kv">
                    <div><div class="as-k">Asset</div><div class="as-v is-strong" id="transfer_asset_name">Loading...</div></div>
                    <div><div class="as-k">Current Employee</div><div class="as-v is-strong" id="transfer_current_employee">Loading...</div></div>
                </div>
                <div class="as-kv is-3" style="margin-top:12px;">
                    <div><div class="as-k">Assigned Since</div><div class="as-v" id="transfer_assigned_date">Loading...</div></div>
                    <div><div class="as-k">Duration</div><div class="as-v" id="transfer_days_assigned">0 days</div></div>
                    <div><div class="as-k">Quantity</div><div class="as-v" id="transfer_quantity">1</div></div>
                </div>
            </div>

            <form id="transferAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="transfer_assignment_id" name="assignment_id">

                <div class="as-stack">
                    <div>
                        <label class="ui-label" for="transfer_new_employee_id">Transfer To <span class="as-req">*</span></label>
                        <select name="new_employee_id" id="transfer_new_employee_id" required class="ui-select w-full">
                            <option value="">Loading employees...</option>
                        </select>
                    </div>

                    <div class="as-grid">
                        <div>
                            <label class="ui-label" for="transfer_date">Transfer Date <span class="as-req">*</span></label>
                            <input type="date" name="transfer_date" id="transfer_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label" for="transfer_reason">Transfer Reason <span class="as-req">*</span></label>
                            <select name="transfer_reason" id="transfer_reason" required class="ui-select w-full">
                                <option value="">Select reason...</option>
                                <option value="Employee departure">Employee Departure</option>
                                <option value="Role change">Role Change</option>
                                <option value="Department transfer">Department Transfer</option>
                                <option value="Project completion">Project Completion</option>
                                <option value="Equipment upgrade">Equipment Upgrade</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="ui-label">Transfer Notes</label>
                        <textarea name="transfer_notes" rows="3" class="ui-textarea w-full"
                                  placeholder="Additional notes about this transfer..."></textarea>
                    </div>
                </div>

                <div class="as-modal-actions">
                    <button type="button" onclick="closeTransferModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg> Process Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Assignment Details Modal --}}
<div id="assignmentDetailsModal" class="as-overlay" style="display: none;" role="dialog" aria-modal="true">
    <div class="as-modal is-wide">
        <div class="as-modal-head">
            <h3 id="detailsModalTitle">Assignment Details</h3>
            <button type="button" onclick="closeDetailsModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div id="detailsModalBody" class="as-modal-body">
            {{-- Content will be loaded here --}}
        </div>
    </div>
</div>

<script>

    const BASE = document.querySelector('meta[name="app-base-url"]').content.replace(/\/$/, '');
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Global variables
let currentAssignmentForReturn = null;
let currentAssignmentForTransfer = null;
let currentAssignmentForDetails = null;

// Return Asset Modal Functions
function openReturnModal(assignmentId) {
    currentAssignmentForReturn = assignmentId;

    // Reset form
    document.getElementById('returnAssetForm').reset();

    // Show loading state
    document.getElementById('return_asset_name').textContent = 'Loading...';
    document.getElementById('return_employee_name').textContent = 'Loading...';
    document.getElementById('return_assigned_date').textContent = 'Loading...';
    document.getElementById('return_days_assigned').textContent = 'Loading...';
    document.getElementById('return_quantity').textContent = 'Loading...';

    // Show modal
    document.getElementById('returnAssetModal').style.display = 'flex';

    // Fetch assignment details
   fetch(`${BASE}/asset-assignments/${assignmentId}/data`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const assignment = data.assignment;

            // Populate assignment info
            document.getElementById('return_assignment_id').value = assignment.id;
            document.getElementById('return_asset_name').textContent = assignment.asset.name;
            document.getElementById('return_employee_name').textContent =
                `${assignment.employee.first_name} ${assignment.employee.last_name}`;
            document.getElementById('return_assigned_date').textContent =
                new Date(assignment.assignment_date).toLocaleDateString();
            document.getElementById('return_days_assigned').textContent = `${data.days_assigned || 0} days`;
            document.getElementById('return_quantity').textContent = assignment.quantity_assigned;

            // Show overdue warning if applicable
            const overdueWarning = document.getElementById('overdue_warning');
            if (data.is_overdue) {
                overdueWarning.style.display = 'block';
                overdueWarning.innerHTML = `<strong>This asset is ${data.days_overdue} days overdue!</strong>`;
            } else {
                overdueWarning.style.display = 'none';
            }
        }
    })
    /*.catch(error => {
        console.error('Error:', error);
        alert('Failed to load assignment details');
        closeReturnModal();
    });*/
}

function closeReturnModal() {
    document.getElementById('returnAssetModal').style.display = 'none';
    document.getElementById('returnAssetForm').reset();
    currentAssignmentForReturn = null;
}

// Transfer Asset Modal Functions
function openTransferModal(assignmentId) {
    currentAssignmentForTransfer = assignmentId;

    // Reset form
    document.getElementById('transferAssetForm').reset();

    // Show loading state
    document.getElementById('transfer_asset_name').textContent = 'Loading...';
    document.getElementById('transfer_current_employee').textContent = 'Loading...';
    document.getElementById('transfer_assigned_date').textContent = 'Loading...';
    document.getElementById('transfer_days_assigned').textContent = 'Loading...';
    document.getElementById('transfer_quantity').textContent = 'Loading...';

    // Show modal
    document.getElementById('transferAssetModal').style.display = 'flex';

    // Fetch assignment details
    fetch(`${BASE}/asset-assignments/${assignmentId}/data`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const assignment = data.assignment;

            // Populate assignment info
            document.getElementById('transfer_assignment_id').value = assignment.id;
            document.getElementById('transfer_asset_name').textContent = assignment.asset.name;
            document.getElementById('transfer_current_employee').textContent =
                `${assignment.employee.first_name} ${assignment.employee.last_name}`;
            document.getElementById('transfer_current_employee').setAttribute('data-employee-id', assignment.employee.id);
            document.getElementById('transfer_assigned_date').textContent =
                new Date(assignment.assignment_date).toLocaleDateString();
            document.getElementById('transfer_days_assigned').textContent = `${data.days_assigned || 0} days`;
            document.getElementById('transfer_quantity').textContent = assignment.quantity_assigned;

            // Load available employees
            loadEmployeesForTransfer(assignment.employee.id);
        }
    })
    /*.catch(error => {
        console.error('Error:', error);
        alert('Failed to load assignment details');
        closeTransferModal();
    });*/
}

function closeTransferModal() {
    document.getElementById('transferAssetModal').style.display = 'none';
    document.getElementById('transferAssetForm').reset();
    currentAssignmentForTransfer = null;
}

function loadEmployeesForTransfer(currentEmployeeId) {
  fetch(`${BASE}/employees/available`, {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
  .then(response => response.json())
  .then(employees => {
    const select = document.getElementById('transfer_new_employee_id');
    select.innerHTML = '<option value="">Choose new employee...</option>';

    employees
      .filter(employee => employee.id != currentEmployeeId)
      .forEach(employee => {
        const option = document.createElement('option');
        option.value = employee.id;
        option.textContent = `${employee.name} (${employee.employee_number}) - ${employee.department}`;
        select.appendChild(option);
      });
  })
  .catch(error => {
    console.error('Error loading employees:', error);
    document.getElementById('transfer_new_employee_id').innerHTML =
      '<option value="">Failed to load employees</option>';
  });
}

// View Assignment Details Functions
function viewAssignmentDetails(assignmentId) {
    currentAssignmentForDetails = assignmentId;

    // Show loading state
    document.getElementById('detailsModalBody').innerHTML = '<p class="as-muted" style="margin:0;">Loading...</p>';
    document.getElementById('assignmentDetailsModal').style.display = 'flex';

   fetch(`${BASE}/asset-assignments/${assignmentId}/data`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const assignment = data.assignment;

            document.getElementById('detailsModalTitle').textContent =
                `${assignment.asset.name} → ${assignment.employee.first_name} ${assignment.employee.last_name}`;

            const modalBody = document.getElementById('detailsModalBody');
            modalBody.innerHTML = `
                <div class="as-detail">
                    <section class="as-kv">
                        <div>
                            <h4 class="as-sec">Employee Details</h4>
                            <div class="as-k">Name</div><div class="as-v is-strong">${assignment.employee.first_name} ${assignment.employee.last_name}</div>
                            <div class="as-k" style="margin-top:8px">Number</div><div class="as-v mv-mono">${assignment.employee.employee_number}</div>
                            <div class="as-k" style="margin-top:8px">Department</div><div class="as-v">${assignment.employee.department?.name || 'Not assigned'}</div>
                        </div>
                        <div>
                            <h4 class="as-sec">Asset Details</h4>
                            <div class="as-k">Name</div><div class="as-v is-strong">${assignment.asset.name}</div>
                            <div class="as-k" style="margin-top:8px">Category</div><div class="as-v">${assignment.asset.category}</div>
                            <div class="as-k" style="margin-top:8px">SKU</div><div class="as-v mv-mono">${assignment.asset.sku || 'Not assigned'}</div>
                        </div>
                    </section>

                    <section>
                        <h4 class="as-sec">Assignment Timeline</h4>
                        <div class="as-kv">
                            <div><div class="as-k">Assigned Date</div><div class="as-v">${new Date(assignment.assignment_date).toLocaleDateString()}</div></div>
                            <div><div class="as-k">Expected Return</div><div class="as-v">${assignment.expected_return_date ? new Date(assignment.expected_return_date).toLocaleDateString() : 'Not set'}</div></div>
                            <div><div class="as-k">Quantity</div><div class="as-v">${assignment.quantity_assigned}</div></div>
                            <div><div class="as-k">Days Assigned</div><div class="as-v">${data.days_assigned || 0} days</div></div>
                        </div>
                    </section>

                    ${assignment.assignment_notes ? `
                    <section>
                        <h4 class="as-sec">Notes</h4>
                        <p class="as-note">${assignment.assignment_notes}</p>
                    </section>
                    ` : ''}
                </div>
            `;
        }
    })
    /*.catch(error => {
        console.error('Error:', error);
        document.getElementById('detailsModalBody').innerHTML =
            '<p style="color: #f44336;">Failed to load assignment details</p>';
    });*/
}

function closeDetailsModal() {
    document.getElementById('assignmentDetailsModal').style.display = 'none';
    currentAssignmentForDetails = null;
}

// Form Submissions
document.getElementById('returnAssetForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!currentAssignmentForReturn) {
        alert('No assignment selected');
        return;
    }

    const formData = new FormData(this);
    const jsonData = {};
    formData.forEach((value, key) => {
        if (key !== '_token' && key !== '_method') {
            jsonData[key] = value;
        }
    });

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Processing...';
    submitBtn.disabled = true;

    fetch(`${BASE}/asset-assignments/${currentAssignmentForReturn}/return`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Asset returned successfully!');
            closeReturnModal();
            window.location.reload();
        } else {
            alert('Failed to return asset: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to process return');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

document.getElementById('transferAssetForm').addEventListener('submit', function (e) {
  e.preventDefault();

  if (!currentAssignmentForTransfer) {
    alert('No assignment selected');
    return;
  }

  const select = document.getElementById('transfer_new_employee_id');
  const chosen = select?.value || '';

  if (!chosen) {
    alert('Choose who to transfer to');
    return;
  }

  const formData = new FormData(this);
  // Force the value into the payload so Laravel definitely sees it
  formData.set('new_employee_id', chosen);

  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = 'Processing...';
  submitBtn.disabled = true;

 fetch(`${BASE}/asset-assignments/${currentAssignmentForTransfer}/transfer`, {
  method: 'POST',
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  body: formData        // keep FormData (do NOT set Content-Type)
})

  .then(async (response) => {
    const raw = await response.text();
    let data = null; try { data = raw ? JSON.parse(raw) : null; } catch {}
    if (!response.ok) {
      const msg = (data && (data.message || JSON.stringify(data.errors || data))) || raw || `HTTP ${response.status}`;
      throw new Error(msg);
    }
    return data || {};
  })
  .then((data) => {
    if (data.success) {
      alert('Asset transferred successfully!');
      closeTransferModal();
      window.location.reload();
    } else {
      alert('Failed to transfer asset: ' + (data.message || 'Unknown error'));
    }
  })
  .catch((err) => {
    console.error(err);
    alert('Failed to process transfer: ' + err.message);
  })
  .finally(() => {
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
  });
});

// Auto-update asset status based on condition
document.getElementById('return_condition')?.addEventListener('change', function() {
    const statusRadios = document.querySelectorAll('input[name="update_asset_status"]');

    switch(this.value) {
        case 'poor':
            statusRadios.forEach(radio => {
                if (radio.value === 'damaged') radio.checked = true;
            });
            break;
        case 'fair':
            statusRadios.forEach(radio => {
                if (radio.value === 'maintenance') radio.checked = true;
            });
            break;
        case 'good':
        case 'new':
            statusRadios.forEach(radio => {
                if (radio.value === 'available') radio.checked = true;
            });
            break;
    }
});

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const returnModal = document.getElementById('returnAssetModal');
    const detailsModal = document.getElementById('assignmentDetailsModal');
    const transferModal = document.getElementById('transferAssetModal');

    if (event.target === returnModal) {
        closeReturnModal();
    }
    if (event.target === detailsModal) {
        closeDetailsModal();
    }
    if (event.target === transferModal) {
        closeTransferModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if (document.getElementById('returnAssetModal').style.display === 'flex') {
            closeReturnModal();
        }
        if (document.getElementById('assignmentDetailsModal').style.display === 'flex') {
            closeDetailsModal();
        }
        if (document.getElementById('transferAssetModal')?.style.display === 'flex') {
            closeTransferModal();
        }
    }
});
</script>
