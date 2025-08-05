{{-- 
==============================================
SUPER SAFE ROLES INDEX VIEW
File: resources/views/roles/index.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">🔑 Role Management</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage user roles and permissions</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Create New Role</a>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">🔑</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['total_roles'] ?? 0 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Roles</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⚡</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['roles_with_admin'] ?? 0 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Admin Roles</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">🎨</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['custom_roles'] ?? 0 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Custom Roles</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">👥</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['employees_assigned'] ?? 0 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Employees with Roles</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="content-card" style="margin-block-end: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search roles..." 
                   style="flex: 1; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
            <button type="submit" class="btn">Search</button>
            @if(request('search'))
            <a href="{{ route('roles.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Roles List -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
        @forelse($roles as $role)
        <div class="role-card">
            <!-- Role Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 15px;">
                <div>
                    <h4 style="margin: 0; color: #333; display: flex; align-items: center; gap: 8px;">
                        @if(is_array($role->permissions) && in_array('all', $role->permissions))
                            <span style="color: #f44336;">⚡</span>
                        @endif
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </h4>
                    <div style="font-size: 12px; color: #999;">
                        @php
                            try {
                                $employeeCount = \App\Models\Employee::where('role_id', $role->id)->count();
                            } catch (\Exception $e) {
                                $employeeCount = 0;
                            }
                        @endphp
                        {{ $employeeCount }} employees assigned
                    </div>
                </div>
                
                @if(is_array($role->permissions) && in_array('all', $role->permissions))
                <span style="background: #ffebee; color: #d32f2f; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                    Super Admin
                </span>
                @endif
            </div>

            <!-- Permissions Preview -->
            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-block-end: 15px; min-height: 100px;">
                <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 8px;">Permissions</div>
                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                    @if(!is_array($role->permissions) || empty($role->permissions))
                        <span style="color: #999; font-style: italic;">No permissions assigned</span>
                    @else
                        @foreach($role->permissions as $permission)
                            @php
                                $colors = [
                                    'all' => ['bg' => '#ffebee', 'text' => '#d32f2f'],
                                    'manage_team' => ['bg' => '#f3e5f5', 'text' => '#7b1fa2'],
                                    'manage_assets' => ['bg' => '#e8f5e8', 'text' => '#388e3c'],
                                    'view_dashboard' => ['bg' => '#e3f2fd', 'text' => '#1976d2'],
                                    'view_clients' => ['bg' => '#fff3e0', 'text' => '#f57c00'],
                                    'view_jobs' => ['bg' => '#e0f2f1', 'text' => '#00796b'],
                                ];
                                $color = $colors[$permission] ?? ['bg' => '#f5f5f5', 'text' => '#666'];
                                $displayName = ucwords(str_replace('_', ' ', $permission));
                            @endphp
                            <span style="background: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                                {{ $displayName }}
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
                <a href="{{ route('roles.show', $role) }}" class="btn-small" style="text-align: center;">
                    View
                </a>
                <a href="{{ route('roles.edit', $role) }}" class="btn-small" style="text-align: center;">
                    Edit
                </a>
                <button onclick="showQuickActions({{ $role->id }}, '{{ $role->name }}')" class="btn-small" style="inline-size: 100%;">
                    More
                </button>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
            <div style="font-size: 64px; margin-block-end: 20px;">🔑</div>
            <h3>No roles found</h3>
            <p>Create your first role to get started.</p>
            <a href="{{ route('roles.create') }}" class="btn btn-primary" style="margin-block-start: 15px;">Create First Role</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($roles, 'links'))
    <div style="margin-block-start: 30px; display: flex; justify-content: center;">
        {{ $roles->appends(request()->query())->links() }}
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

.role-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.role-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
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

.btn-small {
    padding: 6px 12px;
    font-size: 14px;
}
</style>

<script>
function showQuickActions(roleId, roleName) {
    const action = prompt(`Quick actions for "${roleName}":\n\n1. View Details\n2. Edit Permissions\n3. Delete Role\n\nEnter 1, 2, or 3:`);
    
    switch(action) {
        case '1':
            window.location.href = `/roles/${roleId}`;
            break;
        case '2':
            window.location.href = `/roles/${roleId}/edit`;
            break;
        case '3':
            if (confirm(`Delete role "${roleName}"?`)) {
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
            break;
    }
}
</script>
@endsection