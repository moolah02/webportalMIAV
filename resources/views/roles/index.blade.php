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
            <div class="stat-icon stat-icon-blue">&#x1F511;</div>
            <div>
                <div class="stat-number">{{ $stats['total_roles'] ?? 0 }}</div>
                <div class="stat-label">Total Roles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-red">&#x26A1;</div>
            <div>
                <div class="stat-number">{{ $stats['roles_with_admin'] ?? 0 }}</div>
                <div class="stat-label">Admin Roles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">&#x1F3A8;</div>
            <div>
                <div class="stat-number">{{ $stats['custom_roles'] ?? 0 }}</div>
                <div class="stat-label">Custom Roles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">&#x1F465;</div>
            <div>
                <div class="stat-number">{{ $stats['employees_assigned'] ?? 0 }}</div>
                <div class="stat-label">Employees With Roles</div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" class="filter-bar">
        <div class="filter-group" style="flex:1;min-width:180px">
            <label class="ui-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search roles…" class="ui-input">
        </div>
        <div class="filter-group">
            <label class="ui-label">Permission Level</label>
            <select name="permission_level" class="ui-select">
                <option value="">All Levels</option>
                <option value="super_admin" {{ request('permission_level') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="manager"     {{ request('permission_level') == 'manager'     ? 'selected' : '' }}>Manager</option>
                <option value="user"        {{ request('permission_level') == 'user'        ? 'selected' : '' }}>User / Limited</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply</button>
            @if(request()->hasAny(['search', 'permission_level']))
            <a href="{{ route('roles.index') }}" class="btn-secondary">Clear</a>
            @endif
        </div>
    </form>

    <!-- Roles Table -->
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <span class="text-sm font-semibold text-gray-800">Roles</span>
            <span class="badge badge-gray">{{ $roles->count() }} roles</span>
        </div>
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
                            <div class="flex items-center gap-3">
                                <span class="text-base">
                                    @if($rolePerms->contains('name', 'all')) &#x26A1;
                                    @elseif($rolePerms->contains('name', 'manage_team')) &#x1F451;
                                    @else &#x1F464;
                                    @endif
                                </span>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</div>
                                    <div class="text-xs text-gray-400">ID: {{ $role->id }}</div>
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
                                <div class="text-sm font-semibold text-gray-900">{{ $employeeCount }}</div>
                                <div class="text-xs text-gray-400">assigned</div>
                            </div>
                        </td>
                        <td class="text-sm text-gray-600">
                            {{ $role->created_at ? $role->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('roles.show', $role) }}" class="action-btn action-view" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('roles.edit', $role) }}" class="action-btn action-edit" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button onclick="showQuickActions({{ $role->id }}, '{{ addslashes($role->name) }}')" class="action-btn action-view" title="More Actions">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">&#x1F511;</div>
            <p class="empty-state-msg">No roles found</p>
            <p class="text-sm text-gray-400 mt-1 mb-4">Create your first role to get started.</p>
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
