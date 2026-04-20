{{--
==============================================
FIXED ROLES INDEX - Working Buttons & Modal
File: resources/views/roles/index.blade.php
==============================================
--}}
@extends('layouts.app')
@section('title', 'Role Management')

@section('header-actions')
<a href="{{ route('roles.create') }}" class="btn-primary">+ Create Role</a>
@endsection

@section('content')
<div>
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #1976D2; font-size: 24px;">🔑</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number">{{ $stats['total_roles'] ?? 0 }}</div>
                <div class="stat-label uppercase tracking-wide">TOTAL ROLES</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #D32F2F; font-size: 24px;">⚡</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number">{{ $stats['roles_with_admin'] ?? 0 }}</div>
                <div class="stat-label uppercase tracking-wide">ADMIN ROLES</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #7B1FA2; font-size: 24px;">🎨</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number">{{ $stats['custom_roles'] ?? 0 }}</div>
                <div class="stat-label uppercase tracking-wide">CUSTOM ROLES</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #F57C00; font-size: 24px;">👥</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number">{{ $stats['employees_assigned'] ?? 0 }}</div>
                <div class="stat-label uppercase tracking-wide">EMPLOYEES WITH ROLES</div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="ui-card p-4 mb-5">
        <form method="GET" class="flex gap-3 items-center flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search roles..."
                   class="ui-input flex-1 min-w-52">

            <select name="permission_level" class="ui-select w-auto">
                <option value="">All Permission Levels</option>
                <option value="super_admin" {{ request('permission_level') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="manager" {{ request('permission_level') == 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="user" {{ request('permission_level') == 'user' ? 'selected' : '' }}>User / Limited</option>
            </select>

            <button type="submit" class="btn-secondary">Filter</button>

            @if(request()->hasAny(['search', 'permission_level']))
            <a href="{{ route('roles.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    <!-- Roles Table -->
    <div class="ui-card p-6">
        @if($roles->count() > 0)
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" style="cursor: pointer;">
                        </th>
                        <th>Role Name</th>
                        <th>Permission Level</th>
                        <th>Key Permissions</th>
                        <th style="width: 120px;">Employees</th>
                        <th>Created</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>
                            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" style="cursor: pointer;">
                        </td>
                        <td>
                            @php $rolePerms = $role->rolePermissions; @endphp
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="role-icon">
                                    @if($rolePerms->contains('name', 'all'))
                                        <span style="color: #D32F2F;">⚡</span>
                                    @elseif($rolePerms->contains('name', 'manage_team'))
                                        <span style="color: #7B1FA2;">👑</span>
                                    @else
                                        <span style="color: #1976D2;">👤</span>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: #333; margin-bottom: 2px;">
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </div>
                                    <div style="font-size: 12px; color: #999;">
                                        ID: {{ $role->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($rolePerms->contains('name', 'all'))
                                <span class="badge badge-red">Super Admin</span>
                            @elseif($rolePerms->contains('name', 'manage_team'))
                                <span class="badge badge-purple">Manager</span>
                            @elseif($rolePerms->whereIn('name', ['view_dashboard', 'view_jobs'])->isNotEmpty())
                                <span class="badge badge-blue">User</span>
                            @elseif($rolePerms->isNotEmpty())
                                <span class="badge badge-blue">Limited Admin</span>
                            @else
                                <span class="badge badge-gray">Limited</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 300px;">
                                @if($rolePerms->isEmpty())
                                    <span style="color: #999; font-style: italic; font-size: 12px;">No permissions</span>
                                @else
                                    @php
                                        $displayPerms = $rolePerms->take(3);
                                        $remainingCount = $rolePerms->count() - 3;
                                        $colors = [
                                            'all'            => ['bg' => '#FFEBEE', 'text' => '#D32F2F'],
                                            'manage_team'    => ['bg' => '#F3E5F5', 'text' => '#7B1FA2'],
                                            'manage_assets'  => ['bg' => '#E8F5E8', 'text' => '#388E3C'],
                                            'view_dashboard' => ['bg' => '#E3F2FD', 'text' => '#1976D2'],
                                            'view_clients'   => ['bg' => '#FFF3E0', 'text' => '#F57C00'],
                                            'view_jobs'      => ['bg' => '#E0F2F1', 'text' => '#00796B'],
                                        ];
                                    @endphp
                                    @foreach($displayPerms as $perm)
                                        @php
                                            $color = $colors[$perm->name] ?? ['bg' => '#F5F5F5', 'text' => '#666'];
                                        @endphp
                                        <span class="badge badge-gray" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                            {{ ucwords(str_replace('_', ' ', $perm->name)) }}
                                        </span>
                                    @endforeach
                                    @if($remainingCount > 0)
                                        <span class="badge badge-gray" style="background: #F5F5F5; color: #666;">
                                            +{{ $remainingCount }} more
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="text-align: center;">
                                @php
                                    try {
                                        $employeeCount = \App\Models\Employee::where('role_id', $role->id)->count();
                                    } catch (\Exception $e) {
                                        $employeeCount = 0;
                                    }
                                @endphp
                                <div style="font-weight: 500; color: #333; font-size: 16px;">{{ $employeeCount }}</div>
                                <div style="font-size: 11px; color: #999;">assigned</div>
                            </div>
                        </td>
                        <td>
                            <div style="color: #666; font-size: 14px;">
                                {{ $role->created_at ? $role->created_at->format('M d, Y') : 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <button onclick="window.location.href='{{ route('roles.show', $role) }}'" class="action-btn view-btn" title="View">
                                    👁️
                                </button>
                                <button onclick="window.location.href='{{ route('roles.edit', $role) }}'" class="action-btn edit-btn" title="Edit">
                                    ✏️
                                </button>
                                <button onclick="showQuickActions({{ $role->id }}, '{{ addslashes($role->name) }}')" class="action-btn menu-btn" title="More Actions">
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
            <div style="font-size: 64px; margin-bottom: 20px;">🔑</div>
            <h3 style="margin: 0 0 10px 0; color: #333;">No roles found</h3>
            <p style="margin: 0 0 20px 0;">Create your first role to get started.</p>
            <a href="{{ route('roles.create') }}" class="btn-primary">Create First Role</a>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(method_exists($roles, 'links'))
    <div class="mt-6 flex justify-center">
        {{ $roles->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Quick Actions Modal - FIXED -->
<div id="quickActionsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 400px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: #F8F9FA; padding: 20px; border-radius: 12px 12px 0 0; border-bottom: 1px solid #E0E0E0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #333;">
                <span>🔑</span>
                <span id="modalRoleName">Role Actions</span>
            </h3>
            <button onclick="closeQuickActions()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: #666; font-size: 24px; cursor: pointer; padding: 5px; line-height: 1;">×</button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 20px;">
            <div style="display: grid; gap: 12px;">
                <!-- View Details -->
                <button id="viewRoleBtn" class="modal-action-btn" style="background: #E3F2FD; color: #1976D2;">
                    <span style="font-size: 20px;">👁️</span>
                    <div>
                        <div style="font-weight: bold;">View Details</div>
                        <div style="font-size: 12px; opacity: 0.8;">See complete role information</div>
                    </div>
                </button>

                <!-- Edit Permissions -->
                <button id="editRoleBtn" class="modal-action-btn" style="background: #E8F5E8; color: #388E3C;">
                    <span style="font-size: 20px;">✏️</span>
                    <div>
                        <div style="font-weight: bold;">Edit Permissions</div>
                        <div style="font-size: 12px; opacity: 0.8;">Modify role permissions</div>
                    </div>
                </button>

                <!-- Clone Role -->
                <button id="cloneRoleBtn" class="modal-action-btn" style="background: #FFF3E0; color: #F57C00;">
                    <span style="font-size: 20px;">📋</span>
                    <div>
                        <div style="font-weight: bold;">Clone Role</div>
                        <div style="font-size: 12px; opacity: 0.8;">Create a copy of this role</div>
                    </div>
                </button>

                <!-- Delete Role -->
                <button id="deleteRoleBtn" class="modal-action-btn" style="background: #FFEBEE; color: #D32F2F;">
                    <span style="font-size: 20px;">🗑️</span>
                    <div>
                        <div style="font-weight: bold;">Delete Role</div>
                        <div style="font-size: 12px; opacity: 0.8;">Remove this role permanently</div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
let currentRoleId = null;
let currentRoleName = null;

function showQuickActions(roleId, roleName) {
    currentRoleId = roleId;
    currentRoleName = roleName;

    document.getElementById('modalRoleName').textContent = `Actions for ${roleName.replace(/_/g, ' ')}`;

    // Update button actions - FIXED EVENT HANDLERS
    document.getElementById('viewRoleBtn').onclick = function() {
        closeQuickActions();
        window.location.href = `/roles/${roleId}`;
    };

    document.getElementById('editRoleBtn').onclick = function() {
        closeQuickActions();
        window.location.href = `/roles/${roleId}/edit`;
    };

    document.getElementById('cloneRoleBtn').onclick = function() {
        closeQuickActions();
        cloneRole();
    };

    document.getElementById('deleteRoleBtn').onclick = function() {
        closeQuickActions();
        deleteRole();
    };

    document.getElementById('quickActionsModal').style.display = 'flex';
}

function closeQuickActions() {
    document.getElementById('quickActionsModal').style.display = 'none';
}

function cloneRole() {
    if (!currentRoleId || !currentRoleName) return;

    const newName = prompt(`Enter a name for the cloned role (original: ${currentRoleName}):`);
    if (!newName) return;

    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        alert('CSRF token not found. Please refresh the page.');
        return;
    }

    fetch(`/roles/${currentRoleId}/clone`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token.getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ name: newName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Role cloned successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to clone role'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
}

function deleteRole() {
    if (!currentRoleId || !currentRoleName) return;

    // Check employee count from the table
    const row = document.querySelector(`tr:has(input[value="${currentRoleId}"])`);
    const employeeCountElement = row ? row.querySelector('td:nth-child(5) div:first-child') : null;
    const employeeCount = employeeCountElement ? parseInt(employeeCountElement.textContent) : 0;

    if (employeeCount > 0) {
        alert(`Cannot delete role "${currentRoleName}" because it is assigned to ${employeeCount} employee(s). Please reassign these employees first.`);
        return;
    }

    if (!confirm(`Are you sure you want to delete the role "${currentRoleName}"? This action cannot be undone.`)) {
        return;
    }

    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        alert('CSRF token not found. Please refresh the page.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/roles/${currentRoleId}`;

    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = token.getAttribute('content');

    form.appendChild(methodInput);
    form.appendChild(tokenInput);
    document.body.appendChild(form);
    form.submit();
}

// Select All functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="role_ids[]"]');
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
