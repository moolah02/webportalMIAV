{{-- resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Employee Management')

@section('content')
{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title">&#x1F465; Employee Management</h1>
        <p class="page-subtitle">Manage employee onboarding and permissions</p>
    </div>
    <a href="{{ route('employees.create') }}" class="btn-primary">+ Onboard New Employee</a>
</div>

{{-- Stats --}}
@php
    try { $totalEmployees = \App\Models\Employee::count(); } catch (\Exception $e) { $totalEmployees = 0; }
    try { $activeEmployees = \App\Models\Employee::where('status','active')->count(); } catch (\Exception $e) { $activeEmployees = 0; }
    try { $newThisMonth = \App\Models\Employee::whereMonth('hire_date', now()->month)->count(); } catch (\Exception $e) { $newThisMonth = 0; }
    try { $pendingOnboarding = \App\Models\Employee::where('status','pending')->count(); } catch (\Exception $e) { $pendingOnboarding = 0; }
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card border-l-4 border-blue-500">
        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 text-xl leading-none">&#x1F465;</div>
        <div>
            <div class="stat-number text-blue-600">{{ $totalEmployees }}</div>
            <div class="stat-label">Total Employees</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-green-500">
        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 text-xl leading-none">&#x2705;</div>
        <div>
            <div class="stat-number text-green-600">{{ $activeEmployees }}</div>
            <div class="stat-label">Active Employees</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-orange-400">
        <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0 text-xl leading-none">&#x1F195;</div>
        <div>
            <div class="stat-number text-orange-500">{{ $newThisMonth }}</div>
            <div class="stat-label">New This Month</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-purple-500">
        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0 text-xl leading-none">â³</div>
        <div>
            <div class="stat-number text-purple-600">{{ $pendingOnboarding }}</div>
            <div class="stat-label">Pending Onboarding</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div>
        <label class="ui-label">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..." class="ui-input w-52">
    </div>
    <div>
        <label class="ui-label">Department</label>
        <select name="department_id" class="ui-select w-44">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select w-36">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','status','department_id']))
            <a href="{{ route('employees.index') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

{{-- Table --}}
@php
    try {
        if (!isset($employees)) { $employees = \App\Models\Employee::latest()->get(); }
    } catch (\Exception $e) { $employees = collect(); }
@endphp
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">Employees</span>
        <span class="badge badge-gray">{{ $employees->count() }}</span>
    </div>
    @if($employees->count() > 0)
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="cursor-pointer rounded"></th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Hire Date</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td><input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" class="cursor-pointer rounded"></td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ substr($employee->first_name ?? 'N', 0, 1) }}{{ substr($employee->last_name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $employee->first_name ?? 'Unknown' }} {{ $employee->last_name ?? 'User' }}</div>
                                <div class="text-xs text-gray-400">ID: {{ $employee->employee_number ?? $employee->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-sm text-gray-600">
                        @php try { $department = $employee->department->name ?? 'No Department'; } catch (\Exception $e) { $department = 'No Department'; } @endphp
                        {{ $department }}
                    </td>
                    <td>
                        @php $sc = ['active'=>'badge badge-green','pending'=>'badge badge-yellow','inactive'=>'badge badge-gray']; @endphp
                        <span class="{{ $sc[$employee->status ?? 'pending'] ?? 'badge badge-gray' }}">{{ ucfirst($employee->status ?? 'Pending') }}</span>
                    </td>
                    <td>
                        @php
                            try { $roles = $employee->roles; } catch (\Exception $e) { $roles = collect(); }
                            $roleColors = [
                                'admin'      => ['bg'=>'#E8F5E9','color'=>'#2E7D32'],
                                'supervisor' => ['bg'=>'#FFF3E0','color'=>'#F57C00'],
                                'technician' => ['bg'=>'#E3F2FD','color'=>'#1976D2'],
                                'manager'    => ['bg'=>'#F3E5F5','color'=>'#7B1FA2'],
                                'default'    => ['bg'=>'#F5F5F5','color'=>'#666'],
                            ];
                        @endphp
                        <div class="flex flex-wrap gap-1">
                            @if($roles->count() > 0)
                                @foreach($roles as $role)
                                    @php $c = $roleColors[strtolower($role->name)] ?? $roleColors['default']; @endphp
                                    <span class="badge" style="background:{{ $c['bg'] }};color:{{ $c['color'] }};">{{ ucfirst(str_replace('_',' ',$role->name)) }}</span>
                                @endforeach
                            @else
                                <span class="badge badge-red">No Role</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-sm text-gray-600">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        <a href="mailto:{{ $employee->email }}" class="text-sm text-[#1a3a5c] hover:underline">{{ $employee->email }}</a>
                        @if($employee->phone)<div class="text-xs text-gray-500 mt-0.5">{{ $employee->phone }}</div>@endif
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button onclick="location.href='{{ route('employees.show', $employee) }}'" class="btn-secondary btn-sm" title="View">ðŸ‘ï¸</button>
                            <button onclick="location.href='{{ route('employees.edit', $employee) }}'" class="btn-secondary btn-sm" title="Edit">âœï¸</button>
                            <button onclick="quickActions({{ $employee->id }}, '{{ $employee->first_name }} {{ $employee->last_name }}', '{{ $employee->email }}')" class="btn-secondary btn-sm" title="More Actions">&#x2699;ï¸</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">&#x1F465;</div>
        <p class="empty-state-msg">No employees found. <a href="{{ route('employees.create') }}" class="text-[#1a3a5c] underline">Onboard your first employee</a>.</p>
    </div>
    @endif
</div>

{{-- Pagination --}}
@if(isset($employees) && method_exists($employees,'hasPages') && $employees->hasPages())
<div class="mt-5 flex justify-center">
    {{ $employees->appends(request()->query())->links() }}
</div>
@endif

{{-- Quick Actions Modal (IDs used by JS: quickActionsModal, modalEmployeeName, viewProfileBtn, editEmployeeBtn) --}}
<div id="quickActionsModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm relative">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 rounded-t-xl flex items-center gap-3">
            <span>&#x2699;ï¸</span>
            <span id="modalEmployeeName" class="text-sm font-semibold text-gray-900">Quick Actions</span>
            <button onclick="closeQuickActions()" class="ml-auto text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5 grid gap-3">
            <button id="viewProfileBtn" class="flex items-center gap-3 p-4 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition text-left w-full">
                <span class="text-xl">&#x1F464;</span>
                <div><div class="text-sm font-semibold">View Profile</div><div class="text-xs opacity-75">See complete employee details</div></div>
            </button>
            <button id="editEmployeeBtn" class="flex items-center gap-3 p-4 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition text-left w-full">
                <span class="text-xl">âœï¸</span>
                <div><div class="text-sm font-semibold">Edit Employee</div><div class="text-xs opacity-75">Update employee information</div></div>
            </button>
            <button onclick="sendEmail()" class="flex items-center gap-3 p-4 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition text-left w-full">
                <span class="text-xl">&#x1F4E7;</span>
                <div><div class="text-sm font-semibold">Send Email</div><div class="text-xs opacity-75">Contact this employee</div></div>
            </button>
        </div>
    </div>
</div>
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