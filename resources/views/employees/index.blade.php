{{-- resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Employee Management')

@section('header-actions')
<a href="{{ route('employees.create') }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-user-plus"/></svg> Onboard New Employee</a>
@endsection

@push('styles')
<style>
.em-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
@media (max-width: 900px) { .em-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.em-muted { color: var(--mv-muted); font-size: 12.5px; }
.em-person { display: flex; align-items: center; gap: 10px; }
.em-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); display: inline-flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 600; flex-shrink: 0; letter-spacing: .02em; }
.em-name { font-weight: 500; color: var(--mv-ink); font-size: 13.5px; }
.em-id { font-family: var(--mv-mono); font-size: 11.5px; color: var(--mv-muted); }
.em-link { color: var(--mv-accent-ink); text-decoration: none; }
.em-link:hover { text-decoration: underline; }
.em-roles { display: flex; flex-wrap: wrap; gap: 4px; }
.em-role { font-size: 11.5px; padding: 2px 7px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); white-space: nowrap; }

.em-modal { display: none; position: fixed; inset: 0; background: rgba(22, 32, 44, .45); z-index: 1000; justify-content: center; align-items: center; }
.em-modal-box { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; width: 92%; max-width: 380px; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); overflow: hidden; }
.em-modal-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--mv-line); }
.em-modal-head span { font-size: 14.5px; font-weight: 600; color: var(--mv-ink); flex: 1; }
.em-modal-close { background: none; border: 0; color: var(--mv-muted); cursor: pointer; padding: 4px; border-radius: 6px; display: inline-flex; }
.em-modal-close:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.em-modal-body { padding: 8px; display: grid; gap: 2px; }
.em-action { display: flex; align-items: center; gap: 12px; width: 100%; text-align: left; background: transparent; border: 0; border-radius: 8px; padding: 10px 12px; cursor: pointer; color: var(--mv-ink); }
.em-action:hover { background: var(--mv-surface-2); }
.em-action .mv-i { color: var(--mv-ink-2); }
.em-action .t { font-size: 13.5px; font-weight: 500; }
.em-action .d { font-size: 12px; color: var(--mv-muted); }
.em-action.is-danger, .em-action.is-danger .mv-i { color: var(--mv-crit); }
.em-action.is-danger:hover { background: var(--mv-crit-soft); }
</style>
@endpush

@section('content')
@php
    try { $totalEmployees = \App\Models\Employee::count(); } catch (\Exception $e) { $totalEmployees = 0; }
    try { $activeEmployees = \App\Models\Employee::where('status','active')->count(); } catch (\Exception $e) { $activeEmployees = 0; }
    try { $newThisMonth = \App\Models\Employee::whereMonth('hire_date', now()->month)->count(); } catch (\Exception $e) { $newThisMonth = 0; }
    try { $pendingOnboarding = \App\Models\Employee::where('status','pending')->count(); } catch (\Exception $e) { $pendingOnboarding = 0; }
@endphp
<div class="em-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg></div>
        <div>
            <div class="stat-number">{{ $totalEmployees }}</div>
            <div class="stat-label">Total Employees</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ $activeEmployees }}</div>
            <div class="stat-label">Active Employees</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg></div>
        <div>
            <div class="stat-number">{{ $newThisMonth }}</div>
            <div class="stat-label">New This Month</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg></div>
        <div>
            <div class="stat-number">{{ $pendingOnboarding }}</div>
            <div class="stat-label">Pending Onboarding</div>
        </div>
    </div>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group" style="flex:1;min-width:200px">
        <label class="ui-label">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..." class="ui-input" style="width:100%">
    </div>
    <div class="filter-group">
        <label class="ui-label">Department</label>
        <select name="department_id" class="ui-select">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','status','department_id']))
            <a href="{{ route('employees.index') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

@php
    try {
        if (!isset($employees)) { $employees = \App\Models\Employee::latest()->get(); }
    } catch (\Exception $e) { $employees = collect(); }
@endphp
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <h3>Employees</h3>
        <span class="em-muted">{{ $employees->count() }} shown</span>
    </div>
    @if($employees->count() > 0)
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th style="width:40px"><input type="checkbox" id="selectAll" style="cursor:pointer"></th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Hire Date</th>
                    <th>Contact</th>
                    <th style="width:150px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td><input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" style="cursor:pointer"></td>
                    <td>
                        <div class="em-person">
                            <span class="em-avatar">{{ substr($employee->first_name ?? 'N', 0, 1) }}{{ substr($employee->last_name ?? 'A', 0, 1) }}</span>
                            <div>
                                <div class="em-name">{{ $employee->first_name ?? 'Unknown' }} {{ $employee->last_name ?? 'User' }}</div>
                                <div class="em-id">{{ $employee->employee_number ?? $employee->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php try { $department = $employee->department->name ?? 'No Department'; } catch (\Exception $e) { $department = 'No Department'; } @endphp
                        {{ $department }}
                    </td>
                    <td>
                        @php $sc = ['active' => 'badge-green', 'pending' => 'badge-yellow', 'inactive' => 'badge-gray']; @endphp
                        <span class="badge {{ $sc[$employee->status ?? 'pending'] ?? 'badge-gray' }}">{{ ucfirst($employee->status ?? 'Pending') }}</span>
                    </td>
                    <td>
                        @php try { $roles = $employee->roles; } catch (\Exception $e) { $roles = collect(); } @endphp
                        <div class="em-roles">
                            @forelse($roles as $role)
                                <span class="em-role">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                            @empty
                                <span class="badge badge-red">No Role</span>
                            @endforelse
                        </div>
                    </td>
                    <td style="white-space:nowrap">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        <a href="mailto:{{ $employee->email }}" class="em-link">{{ $employee->email }}</a>
                        @if($employee->phone)<div class="em-muted" style="margin-top:1px">{{ $employee->phone }}</div>@endif
                    </td>
                    <td>
                        <div class="action-group">
                            <button type="button" onclick="location.href='{{ route('employees.show', $employee) }}'" class="action-btn" title="View"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></button>
                            <button type="button" onclick="location.href='{{ route('employees.edit', $employee) }}'" class="action-btn" title="Edit"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg></button>
                            <button type="button" onclick="quickActions({{ $employee->id }}, '{{ $employee->first_name }} {{ $employee->last_name }}', '{{ $employee->email }}')" class="action-btn" title="More Actions"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-more"/></svg></button>
                            <button type="button" onclick="confirmDelete({{ $employee->id }}, '{{ $employee->first_name }} {{ $employee->last_name }}')" class="action-btn action-delete" title="Delete"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-trash"/></svg></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg></div>
        <p class="empty-state-msg">No employees found.</p>
        <a href="{{ route('employees.create') }}" class="btn-primary" style="margin-top:12px">Onboard your first employee</a>
    </div>
    @endif
</div>

@if(isset($employees) && method_exists($employees,'hasPages') && $employees->hasPages())
<div class="mt-5 flex justify-center">
    {{ $employees->appends(request()->query())->links() }}
</div>
@endif

{{-- Quick Actions Modal (IDs used by JS: quickActionsModal, modalEmployeeName, viewProfileBtn, editEmployeeBtn, deleteEmployeeBtn) --}}
<div id="quickActionsModal" class="em-modal">
    <div class="em-modal-box">
        <div class="em-modal-head">
            <svg class="mv-i mv-i-sm" aria-hidden="true" style="color:var(--mv-muted)"><use href="#i-user"/></svg>
            <span id="modalEmployeeName">Quick Actions</span>
            <button type="button" onclick="closeQuickActions()" class="em-modal-close" aria-label="Close"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="em-modal-body">
            <button type="button" id="viewProfileBtn" class="em-action">
                <svg class="mv-i" aria-hidden="true"><use href="#i-user"/></svg>
                <div><div class="t">View Profile</div><div class="d">See complete employee details</div></div>
            </button>
            <button type="button" id="editEmployeeBtn" class="em-action">
                <svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg>
                <div><div class="t">Edit Employee</div><div class="d">Update employee information</div></div>
            </button>
            <button type="button" onclick="sendEmail()" class="em-action">
                <svg class="mv-i" aria-hidden="true"><use href="#i-mail"/></svg>
                <div><div class="t">Send Email</div><div class="d">Contact this employee</div></div>
            </button>
            <button type="button" id="deleteEmployeeBtn" class="em-action is-danger">
                <svg class="mv-i" aria-hidden="true"><use href="#i-trash"/></svg>
                <div><div class="t">Delete Employee</div><div class="d">Permanently remove this employee</div></div>
            </button>
        </div>
    </div>
</div>

{{-- Hidden delete form --}}
<form id="deleteEmployeeForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
<script>
let currentEmployeeId = null;
let currentEmployeeEmail = null;

function quickActions(employeeId, employeeName, employeeEmail) {
    currentEmployeeId = employeeId;
    currentEmployeeEmail = employeeEmail;

    document.getElementById('modalEmployeeName').textContent = `Actions for ${employeeName}`;

    document.getElementById('viewProfileBtn').onclick = () => {
        closeQuickActions();
        window.location.href = `/employees/${employeeId}`;
    };

    document.getElementById('editEmployeeBtn').onclick = () => {
        closeQuickActions();
        window.location.href = `/employees/${employeeId}/edit`;
    };

    document.getElementById('deleteEmployeeBtn').onclick = () => {
        closeQuickActions();
        confirmDelete(employeeId, employeeName);
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

function confirmDelete(employeeId, employeeName) {
    if (!confirm(`Delete "${employeeName}"?\n\nThis action cannot be undone.`)) return;
    const form = document.getElementById('deleteEmployeeForm');
    form.action = `/employees/${employeeId}`;
    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('input[name="employee_ids[]"]').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('quickActionsModal');
        if (event.target === modal) {
            closeQuickActions();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeQuickActions();
        }
    });
});
</script>
@endsection
