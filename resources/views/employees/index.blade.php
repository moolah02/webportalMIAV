{{--
==============================================
EMPLOYEE MANAGEMENT TABLE VIEW
File: resources/views/employees/index.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 24px; font-weight: 600;">👥 Employee Management</h2>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">Manage employee onboarding and permissions</p>
        </div>
        <button onclick="location.href='{{ route('employees.create') }}'" class="btn btn-primary">+ Onboard New Employee</button>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
        <div class="metric-card">
            <div class="metric-icon" style="background: #E3F2FD;">
                <span style="color: #1976D2; font-size: 24px;">👥</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">
                    @php
                        try {
                            $totalEmployees = \App\Models\Employee::count();
                        } catch (\Exception $e) {
                            $totalEmployees = 0;
                        }
                    @endphp
                    {{ $totalEmployees }}
                </div>
                <div class="metric-label">TOTAL EMPLOYEES</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #E8F5E8;">
                <span style="color: #388E3C; font-size: 24px;">✅</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">
                    @php
                        try {
                            $activeEmployees = \App\Models\Employee::where('status', 'active')->count();
                        } catch (\Exception $e) {
                            $activeEmployees = 0;
                        }
                    @endphp
                    {{ $activeEmployees }}
                </div>
                <div class="metric-label">ACTIVE EMPLOYEES</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #FFF3E0;">
                <span style="color: #F57C00; font-size: 24px;">🆕</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">
                    @php
                        try {
                            $newThisMonth = \App\Models\Employee::whereMonth('hire_date', now()->month)->count();
                        } catch (\Exception $e) {
                            $newThisMonth = 0;
                        }
                    @endphp
                    {{ $newThisMonth }}
                </div>
                <div class="metric-label">NEW THIS MONTH</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #F3E5F5;">
                <span style="color: #7B1FA2; font-size: 24px;">⏳</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">
                    @php
                        try {
                            $pendingOnboarding = \App\Models\Employee::where('status', 'pending')->count();
                        } catch (\Exception $e) {
                            $pendingOnboarding = 0;
                        }
                    @endphp
                    {{ $pendingOnboarding }}
                </div>
                <div class="metric-label">PENDING ONBOARDING</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="content-card" style="margin-block-end: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search employees..."
                   style="flex: 1; min-inline-size: 250px; padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">

            <select name="department" style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                <option value="">All Departments</option>
                <option value="engineering" {{ request('department') == 'engineering' ? 'selected' : '' }}>Engineering</option>
                <option value="sales" {{ request('department') == 'sales' ? 'selected' : '' }}>Sales</option>
                <option value="hr" {{ request('department') == 'hr' ? 'selected' : '' }}>HR</option>
            </select>

            <select name="status" style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>

            <button type="submit" class="btn">Filter</button>

            @if(request()->hasAny(['search', 'status', 'department']))
            <a href="{{ route('employees.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Employee Table -->
    <div class="content-card">
        @php
            try {
                if (!isset($employees)) {
                    $employees = \App\Models\Employee::latest()->get();
                }
            } catch (\Exception $e) {
                $employees = collect();
            }
        @endphp

        @if($employees->count() > 0)
        <div class="table-container">
            <table class="employee-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" style="cursor: pointer;">
                        </th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Hire Date</th>
                        <th>Contact</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>
                            <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" style="cursor: pointer;">
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="employee-avatar">
                                    {{ substr($employee->first_name ?? 'N', 0, 1) }}{{ substr($employee->last_name ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: #333; margin-bottom: 2px;">
                                        {{ $employee->first_name ?? 'Unknown' }} {{ $employee->last_name ?? 'User' }}
                                    </div>
                                    <div style="font-size: 12px; color: #999;">
                                        ID: {{ $employee->employee_number ?? $employee->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="color: #666; font-size: 14px;">
                                @php
                                    try {
                                        $department = $employee->department->name ?? 'No Department';
                                    } catch (\Exception $e) {
                                        $department = 'No Department';
                                    }
                                @endphp
                                {{ $department }}
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $employee->status ?? 'pending' }}">
                                {{ ucfirst($employee->status ?? 'Pending') }}
                            </span>
                        </td>
                        <td>
                            @php
                                try {
                                    $role = null;
                                    if ($employee->role_id) {
                                        $role = \App\Models\Role::find($employee->role_id);
                                    }
                                } catch (\Exception $e) {
                                    $role = null;
                                }
                            @endphp

                            @if($role)
                                <span class="role-badge">
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </span>
                            @else
                                <span class="role-badge" style="background: #FFEBEE; color: #D32F2F;">
                                    No Role
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="color: #666; font-size: 14px;">
                                {{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 14px;">
                                <div style="margin-bottom: 4px;">
                                    <a href="mailto:{{ $employee->email }}" style="color: #1976D2; text-decoration: none; font-size: 13px;">
                                        {{ $employee->email }}
                                    </a>
                                </div>
                                @if($employee->phone)
                                <div style="color: #666; font-size: 13px;">
                                    {{ $employee->phone }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <button onclick="location.href='{{ route('employees.show', $employee) }}'" class="action-btn view-btn" title="View">
                                    👁️
                                </button>
                                <button onclick="location.href='{{ route('employees.edit', $employee) }}'" class="action-btn edit-btn" title="Edit">
                                    ✏️
                                </button>
                                <button onclick="quickActions({{ $employee->id }}, '{{ $employee->first_name }} {{ $employee->last_name }}', '{{ $employee->email }}')" class="action-btn menu-btn" title="More Actions">
                                    ⚙️
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 60px; color: #666;">
            <div style="font-size: 64px; margin-block-end: 20px;">👥</div>
            <h3 style="margin: 0 0 10px 0; color: #333;">No employees found</h3>
            <p style="margin: 0 0 20px 0;">Start by onboarding your first employee.</p>
            <button onclick="location.href='{{ route('employees.create') }}'" class="btn btn-primary">Onboard First Employee</button>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(isset($employees) && method_exists($employees, 'hasPages') && $employees->hasPages())
    <div style="margin-block-start: 30px; display: flex; justify-content: center;">
        {{ $employees->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Quick Actions Modal -->
<div id="quickActionsModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 400px; inline-size: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <!-- Modal Header -->
        <div style="background: #F8F9FA; padding: 20px; border-radius: 12px 12px 0 0; border-bottom: 1px solid #E0E0E0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #333;">
                <span>⚙️</span>
                <span id="modalEmployeeName">Quick Actions</span>
            </h3>
            <button onclick="closeQuickActions()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: #666; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 20px;">
            <div style="display: grid; gap: 12px;">
                <!-- View Profile -->
                <button id="viewProfileBtn" class="modal-action-btn" style="background: #E3F2FD; color: #1976D2;">
                    <span style="font-size: 20px;">👤</span>
                    <div>
                        <div style="font-weight: bold;">View Profile</div>
                        <div style="font-size: 12px; opacity: 0.8;">See complete employee details</div>
                    </div>
                </button>

                <!-- Edit Employee -->
                <button id="editEmployeeBtn" class="modal-action-btn" style="background: #E8F5E8; color: #388E3C;">
                    <span style="font-size: 20px;">✏️</span>
                    <div>
                        <div style="font-weight: bold;">Edit Employee</div>
                        <div style="font-size: 12px; opacity: 0.8;">Update employee information</div>
                    </div>
                </button>

                <!-- Change Role -->

                <!-- Send Email -->
                <button onclick="sendEmail()" class="modal-action-btn" style="background: #F3E5F5; color: #7B1FA2;">
                    <span style="font-size: 20px;">📧</span>
                    <div>
                        <div style="font-weight: bold;">Send Email</div>
                        <div style="font-size: 12px; opacity: 0.8;">Contact this employee</div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Metric Cards */
.metric-card {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #F0F0F0;
    display: flex;
    align-items: center;
    gap: 16px;
}

.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.metric-content {
    flex: 1;
}

.metric-number {
    font-size: 32px;
    font-weight: bold;
    color: #333;
    line-height: 1;
    margin-bottom: 4px;
}

.metric-label {
    font-size: 12px;
    color: #666;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* Content Card */
.content-card {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #F0F0F0;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
}

.employee-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.employee-table th {
    background: #F8F9FA;
    padding: 16px 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #E0E0E0;
}

.employee-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #F0F0F0;
    vertical-align: middle;
}

.employee-table tr:hover {
    background: #FAFAFA;
}

.employee-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    font-weight: bold;
    flex-shrink: 0;
}

/* Status Badges */
.status-badge {
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
    text-transform: capitalize;
}

.status-active { background: #E8F5E8; color: #2E7D32; }
.status-pending { background: #FFF3E0; color: #F57C00; }
.status-inactive { background: #F5F5F5; color: #666; }

.role-badge {
    background: #E3F2FD;
    color: #1976D2;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

/* Action Buttons */
.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #F8F9FA;
    color: #666;
}

.action-btn:hover {
    background: #E0E0E0;
    transform: translateY(-1px);
}

/* Buttons */
.btn {
    padding: 10px 16px;
    border: 1px solid #E0E0E0;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #1976D2;
    color: #1976D2;
}

.btn-primary {
    background: #1976D2;
    color: white;
    border-color: #1976D2;
}

.btn-primary:hover {
    background: #1565C0;
    border-color: #1565C0;
    color: white;
}

/* Modal */
.modal-action-btn {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: start;
    inline-size: 100%;
}

.modal-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<script>
let currentEmployeeId = null;
let currentEmployeeEmail = null;

function quickActions(employeeId, employeeName, employeeEmail) {
    currentEmployeeId = employeeId;
    currentEmployeeEmail = employeeEmail;

    document.getElementById('modalEmployeeName').textContent = `Actions for ${employeeName}`;

    // Update button actions
    document.getElementById('viewProfileBtn').onclick = () => {
        closeQuickActions();
        window.location.href = `/employees/${employeeId}`;
    };

    document.getElementById('editEmployeeBtn').onclick = () => {
        closeQuickActions();
        window.location.href = `/employees/${employeeId}/edit`;
    };

    document.getElementById('quickActionsModal').style.display = 'flex';
}

function closeQuickActions() {
    document.getElementById('quickActionsModal').style.display = 'none';
}

function changeRole() {
    closeQuickActions();
    const newRole = prompt("Enter the new role ID for employee #" + currentEmployeeId);
    if (!newRole) return;

    fetch(`/employees/${currentEmployeeId}/role`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ role_id: newRole })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message || 'Role updated');
        location.reload();
    })
    .catch(e => alert('Error: ' + e.message));
}

function sendEmail() {
    closeQuickActions();
    if (currentEmployeeEmail) {
        window.location.href = `mailto:${currentEmployeeEmail}`;
    } else {
        alert('Email address not available');
    }
}

// Select All functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="employee_ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('quickActionsModal');
        if (event.target === modal) {
            closeQuickActions();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeQuickActions();
        }
    });
});
</script>
@endsection
