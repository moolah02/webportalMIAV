{{-- 
==============================================
ROLE SHOW VIEW
File: resources/views/roles/show.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">
                🔑 {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                @if(in_array('all', $role->permissions ?? []))
                    <span style="color: #f44336; font-size: 16px; margin-left: 10px;">⚡ Super Admin</span>
                @endif
            </h2>
            <p style="color: #666; margin: 5px 0 0 0;">Role details and assigned employees</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">✏️ Edit Role</a>
            <a href="{{ route('roles.index') }}" class="btn">← Back to Roles</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Content -->
        <div>
            <!-- Role Information -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 20px; color: #333;">📋 Role Information</h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="margin-block-end: 15px;">
                            <div style="font-weight: 600; color: #333; margin-block-end: 5px;">Role Name</div>
                            <div style="color: #666;">{{ $role->name }}</div>
                        </div>
                        
                        <div style="margin-block-end: 15px;">
                            <div style="font-weight: 600; color: #333; margin-block-end: 5px;">Display Name</div>
                            <div style="color: #666;">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</div>
                        </div>
                    </div>
                    
                    <div>
                        <div style="margin-block-end: 15px;">
                            <div style="font-weight: 600; color: #333; margin-block-end: 5px;">Assigned Employees</div>
                            <div style="color: #666; font-size: 24px; font-weight: bold;">{{ $role->employees->count() }}</div>
                        </div>
                        
                        <div style="margin-block-end: 15px;">
                            <div style="font-weight: 600; color: #333; margin-block-end: 5px;">Total Permissions</div>
                            <div style="color: #666; font-size: 24px; font-weight: bold;">{{ count($role->permissions ?? []) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Details -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 20px; color: #333;">🔐 Permissions</h4>
                
                @if(empty($role->permissions))
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <div style="font-size: 48px; margin-block-end: 15px;">🔒</div>
                        <div>No permissions assigned to this role</div>
                    </div>
                @else
                    @php
                        $permissions = $role->permissions ?? [];
                        $groupedPermissions = collect($allPermissions)->filter(function($permission, $key) use ($permissions) {
                            return in_array($key, $permissions);
                        })->groupBy('category');
                    @endphp
                    
                    @foreach($groupedPermissions as $category => $categoryPermissions)
                    <div style="margin-block-end: 25px; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px;">
                        <h6 style="color: #333; margin-block-end: 15px; text-transform: capitalize; display: flex; align-items: center; gap: 8px;">
                            @switch($category)
                                @case('admin')
                                    <span style="color: #f44336;">⚡</span> Admin
                                    @break
                                @case('general')
                                    <span style="color: #2196f3;">👤</span> General
                                    @break
                                @case('assets')
                                    <span style="color: #4caf50;">📦</span> Assets
                                    @break
                                @case('clients')
                                    <span style="color: #ff9800;">🏢</span> Clients
                                    @break
                                @case('management')
                                    <span style="color: #9c27b0;">👥</span> Management
                                    @break
                                @case('technical')
                                    <span style="color: #00bcd4;">🔧</span> Technical
                                    @break
                                @default
                                    <span>📋</span> {{ ucfirst($category) }}
                            @endswitch
                        </h6>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 10px;">
                            @foreach($categoryPermissions as $key => $permission)
                            <div style="border: 1px solid #ddd; border-radius: 6px; padding: 12px; background: #f8f9fa;">
                                <div style="font-weight: 500; margin-block-end: 4px; display: flex; align-items: center; gap: 8px;">
                                    {{ $permission['name'] }}
                                    @if(isset($permission['danger']) && $permission['danger'])
                                        <span style="background: #ffebee; color: #d32f2f; padding: 2px 6px; border-radius: 8px; font-size: 10px;">
                                            DANGER
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size: 12px; color: #666;">{{ $permission['description'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Assigned Employees -->
            @if($role->employees->count() > 0)
            <div class="content-card">
                <h4 style="margin-block-end: 20px; color: #333;">👥 Assigned Employees</h4>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                    @foreach($role->employees as $employee)
                    <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #f8f9fa;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-block-end: 10px;">
                            <div style="inline-size: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold;">
                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 500; color: #333;">{{ $employee->full_name }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $employee->email }}</div>
                            </div>
                        </div>
                        
                        @if($employee->department)
                        <div style="font-size: 12px; color: #666; margin-block-end: 5px;">
                            🏢 {{ $employee->department->name }}
                        </div>
                        @endif
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="status-badge status-{{ $employee->status }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                            <a href="{{ route('employees.show', $employee) }}" style="color: #2196f3; text-decoration: none; font-size: 12px;">
                                View →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">⚡ Quick Actions</h4>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary" style="inline-size: 100%; text-align: center;">
                        ✏️ Edit Role
                    </a>
                    
                    <button onclick="cloneRole({{ $role->id }})" class="btn" style="inline-size: 100%;">
                        📋 Clone Role
                    </button>
                    
                    @if(!in_array($role->name, ['super_admin', 'admin', 'manager', 'employee', 'technician']))
                    <button onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')" class="btn" style="inline-size: 100%; color: #f44336; border-color: #f44336;">
                        🗑️ Delete Role
                    </button>
                    @endif
                </div>
            </div>

            <!-- Role Statistics -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">📊 Statistics</h4>
                
                <div style="margin-block-end: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-block-end: 8px;">
                        <span style="color: #666;">Assigned Employees:</span>
                        <span style="font-weight: bold;">{{ $role->employees->count() }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-block-end: 8px;">
                        <span style="color: #666;">Active Employees:</span>
                        <span style="font-weight: bold;">{{ $role->employees->where('status', 'active')->count() }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-block-end: 8px;">
                        <span style="color: #666;">Total Permissions:</span>
                        <span style="font-weight: bold;">{{ count($role->permissions ?? []) }}</span>
                    </div>
                </div>
                
                <!-- Permission Level Indicator -->
                @php
                    $permissionCount = count($role->permissions ?? []);
                    $hasAll = in_array('all', $role->permissions ?? []);
                    $hasManageTeam = in_array('manage_team', $role->permissions ?? []);
                    $hasManageAssets = in_array('manage_assets', $role->permissions ?? []);
                    
                    if ($hasAll) {
                        $level = 'Super Admin';
                        $color = '#f44336';
                    } elseif ($hasManageTeam || $hasManageAssets) {
                        $level = 'Admin';
                        $color = '#ff9800';
                    } elseif ($permissionCount > 3) {
                        $level = 'Elevated';
                        $color = '#2196f3';
                    } else {
                        $level = 'Standard';
                        $color = '#4caf50';
                    }
                @endphp
                
                <div style="background: {{ $color }}20; color: {{ $color }}; padding: 10px; border-radius: 6px; text-align: center;">
                    <div style="font-weight: bold;">{{ $level }}</div>
                    <div style="font-size: 12px; opacity: 0.8;">Permission Level</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">📈 Recent Activity</h4>
                
                <div style="color: #666; font-style: italic; text-align: center; padding: 20px;">
                    Activity tracking coming soon
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

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

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-inactive { background: #f5f5f5; color: #666; }
</style>

<script>
function cloneRole(roleId) {
    if (confirm('Clone this role? A copy will be created that you can edit.')) {
        fetch(`/roles/${roleId}/clone`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `/roles/${data.newRoleId}/edit`;
            } else {
                alert('Failed to clone role');
            }
        })
        .catch(error => {
            alert('Failed to clone role');
        });
    }
}

function deleteRole(roleId, roleName) {
    if (confirm(`Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/roles/${roleId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection