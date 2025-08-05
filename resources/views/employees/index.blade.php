{{-- 
==============================================
SAFE EMPLOYEES INDEX VIEW
File: resources/views/employees/index.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">👥 Employee Management</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage employee onboarding and permissions</p>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">+ Onboard New Employee</a>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">👥</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">
                        @php
                            try {
                                $totalEmployees = \App\Models\Employee::count();
                            } catch (\Exception $e) {
                                $totalEmployees = 0;
                            }
                        @endphp
                        {{ $totalEmployees }}
                    </div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Employees</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">✅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">
                        @php
                            try {
                                $activeEmployees = \App\Models\Employee::where('status', 'active')->count();
                            } catch (\Exception $e) {
                                $activeEmployees = 0;
                            }
                        @endphp
                        {{ $activeEmployees }}
                    </div>
                    <div style="font-size: 14px; opacity: 0.9;">Active Employees</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">🆕</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">
                        @php
                            try {
                                $newThisMonth = \App\Models\Employee::whereMonth('hire_date', now()->month)->count();
                            } catch (\Exception $e) {
                                $newThisMonth = 0;
                            }
                        @endphp
                        {{ $newThisMonth }}
                    </div>
                    <div style="font-size: 14px; opacity: 0.9;">New This Month</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⏳</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">
                        @php
                            try {
                                $pendingOnboarding = \App\Models\Employee::where('status', 'pending')->count();
                            } catch (\Exception $e) {
                                $pendingOnboarding = 0;
                            }
                        @endphp
                        {{ $pendingOnboarding }}
                    </div>
                    <div style="font-size: 14px; opacity: 0.9;">Pending Onboarding</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="content-card" style="margin-block-end: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search employees..." 
                   style="flex: 1; min-inline-size: 250px; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
            
            <select name="status" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
            
            <button type="submit" class="btn">Filter</button>
            
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('employees.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Employee Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
        @php
            try {
                if (!isset($employees)) {
                    $employees = \App\Models\Employee::latest()->get();
                }
            } catch (\Exception $e) {
                $employees = collect();
            }
        @endphp
        
        @forelse($employees as $employee)
        <div class="employee-card">
            <!-- Employee Header -->
            <div style="display: flex; justify-content: space-between; align-items: start; margin-block-end: 15px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="inline-size: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; font-weight: bold;">
                        {{ substr($employee->first_name ?? 'N', 0, 1) }}{{ substr($employee->last_name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <h4 style="margin: 0; color: #333;">{{ $employee->first_name ?? 'Unknown' }} {{ $employee->last_name ?? 'User' }}</h4>
                        <div style="font-size: 14px; color: #666;">
                            @php
                                try {
                                    $department = $employee->department->name ?? 'No Department';
                                } catch (\Exception $e) {
                                    $department = 'No Department';
                                }
                            @endphp
                            {{ $department }}
                        </div>
                        <div style="font-size: 12px; color: #999;">ID: {{ $employee->employee_number ?? $employee->id }}</div>
                    </div>
                </div>
                <span class="status-badge status-{{ $employee->status ?? 'pending' }}">
                    {{ ucfirst($employee->status ?? 'Pending') }}
                </span>
            </div>

            <!-- Employee Info -->
            <div style="margin-block-end: 15px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-block-end: 5px;">
                    <span style="color: #666;">📧</span>
                    <a href="mailto:{{ $employee->email }}" style="color: #2196f3; text-decoration: none;">{{ $employee->email }}</a>
                </div>
                
                @if($employee->phone)
                <div style="display: flex; align-items: center; gap: 8px; margin-block-end: 5px;">
                    <span style="color: #666;">📞</span>
                    <span style="color: #333;">{{ $employee->phone }}</span>
                </div>
                @endif

                @php
                    try {
                        $manager = $employee->manager->full_name ?? null;
                    } catch (\Exception $e) {
                        $manager = null;
                    }
                @endphp
                @if($manager)
                <div style="display: flex; align-items: center; gap: 8px; margin-block-end: 5px;">
                    <span style="color: #666;">👤</span>
                    <span style="color: #666;">Reports to: {{ $manager }}</span>
                </div>
                @endif
            </div>

            <!-- Role/Permissions Summary -->
            <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-block-end: 15px;">
                <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px;">Role & Permissions</div>
                <div style="display: flex; flex-wrap: wrap; gap: 5px;">
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
                        <span style="background: #e3f2fd; color: #1976d2; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                        
                        {{-- Show some key permissions --}}
                        @if(is_array($role->permissions))
                            @if(in_array('all', $role->permissions))
                                <span style="background: #ffebee; color: #d32f2f; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                                    Super Admin
                                </span>
                            @elseif(in_array('manage_team', $role->permissions))
                                <span style="background: #f3e5f5; color: #7b1fa2; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                                    Manager
                                </span>
                            @elseif(in_array('view_jobs', $role->permissions))
                                <span style="background: #e0f2f1; color: #00796b; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                                    Technician
                                </span>
                            @endif
                        @endif
                    @else
                        <span style="background: #ffebee; color: #d32f2f; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                            No Role Assigned
                        </span>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 8px;">
                <button onclick="quickActions({{ $employee->id }})" class="btn-small" style="flex: 1; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; font-weight: bold;">
                    ⚡ Quick Actions
                </button>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
            <div style="font-size: 64px; margin-block-end: 20px;">👥</div>
            <h3>No employees found</h3>
            <p>Start by onboarding your first employee.</p>
            <a href="{{ route('employees.create') }}" class="btn btn-primary" style="margin-block-start: 15px;">Onboard First Employee</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($employees) && method_exists($employees, 'hasPages') && $employees->hasPages())
    <div style="margin-block-start: 30px; display: flex; justify-content: center;">
        {{ $employees->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.employee-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.employee-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-inactive { background: #f5f5f5; color: #666; }

.btn {
    padding: 8px 16px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
}

.btn-primary {
    background: #2196f3;
    color: white;
    border-color: #2196f3;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}

.btn-small {
    padding: 6px 12px;
    font-size: 14px;
}
</style>

<!-- Quick Actions Modal -->
<div id="quickActionsModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 400px; inline-size: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>⚡</span>
                <span id="modalEmployeeName">Quick Actions</span>
            </h3>
            <button onclick="closeQuickActions()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <div style="display: grid; gap: 12px;">
                <button onclick="viewEmployee()" class="modal-action-btn" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
                    <span style="font-size: 20px;">👤</span>
                    <div>
                        <div style="font-weight: bold;">View Profile</div>
                        <div style="font-size: 12px; opacity: 0.9;">See complete employee details</div>
                    </div>
                </button>
                
                <button onclick="editEmployee()" class="modal-action-btn" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
                    <span style="font-size: 20px;">✏️</span>
                    <div>
                        <div style="font-weight: bold;">Edit Employee</div>
                        <div style="font-size: 12px; opacity: 0.9;">Update employee information</div>
                    </div>
                </button>
                
                <button onclick="changeRole()" class="modal-action-btn" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
                    <span style="font-size: 20px;">🔑</span>
                    <div>
                        <div style="font-weight: bold;">Change Role</div>
                        <div style="font-size: 12px; opacity: 0.9;">Assign different role & permissions</div>
                    </div>
                </button>
                
                <button onclick="sendEmail()" class="modal-action-btn" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
                    <span style="font-size: 20px;">📧</span>
                    <div>
                        <div style="font-weight: bold;">Send Email</div>
                        <div style="font-size: 12px; opacity: 0.9;">Contact this employee</div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-action-btn {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: start;
    inline-size: 100%;
}

.modal-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-action-btn:active {
    transform: translateY(0);
}
</style>

<script>
let currentEmployeeId = null;
let currentEmployeeEmail = null;

function quickActions(employeeId) {
    currentEmployeeId = employeeId;
    
    // Get employee name from the card
    const employeeCard = event.target.closest('.employee-card');
    const employeeName = employeeCard.querySelector('h4').textContent;
    const employeeEmailElement = employeeCard.querySelector('a[href^="mailto:"]');
    currentEmployeeEmail = employeeEmailElement ? employeeEmailElement.textContent : '';
    
    document.getElementById('modalEmployeeName').textContent = `Actions for ${employeeName}`;
    document.getElementById('quickActionsModal').style.display = 'flex';
}

function closeQuickActions() {
    document.getElementById('quickActionsModal').style.display = 'none';
}

function viewEmployee() {
    closeQuickActions();
    window.location.href = `/employees/${currentEmployeeId}`;
}

function editEmployee() {
    closeQuickActions();
    window.location.href = `/employees/${currentEmployeeId}/edit`;
}

function changeRole() {
    closeQuickActions();
    
    // For now, redirect to edit page - you can implement role changing later
    setTimeout(() => {
        window.location.href = `/employees/${currentEmployeeId}/edit`;
    }, 300);
}

function sendEmail() {
    closeQuickActions();
    
    if (currentEmployeeEmail) {
        setTimeout(() => {
            window.location.href = `mailto:${currentEmployeeEmail}`;
        }, 300);
    } else {
        alert('Email address not available');
    }
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

// Prevent event bubbling on modal content
document.addEventListener('DOMContentLoaded', function() {
    const modalContent = document.querySelector('#quickActionsModal > div');
    if (modalContent) {
        modalContent.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});
</script>
@endsection