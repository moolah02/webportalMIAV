{{--
==============================================
FIXED ROLES INDEX - Working Buttons & Modal
File: resources/views/roles/index.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 24px; font-weight: 600;">🔑 Role Management</h2>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">Manage user roles and permissions</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Create New Role</a>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="metric-card">
            <div class="metric-icon" style="background: #E3F2FD;">
                <span style="color: #1976D2; font-size: 24px;">🔑</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['total_roles'] ?? 0 }}</div>
                <div class="metric-label">TOTAL ROLES</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #FFEBEE;">
                <span style="color: #D32F2F; font-size: 24px;">⚡</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['roles_with_admin'] ?? 0 }}</div>
                <div class="metric-label">ADMIN ROLES</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #F3E5F5;">
                <span style="color: #7B1FA2; font-size: 24px;">🎨</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['custom_roles'] ?? 0 }}</div>
                <div class="metric-label">CUSTOM ROLES</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #FFF3E0;">
                <span style="color: #F57C00; font-size: 24px;">👥</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['employees_assigned'] ?? 0 }}</div>
                <div class="metric-label">EMPLOYEES WITH ROLES</div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="content-card" style="margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search roles..."
                   style="flex: 1; min-width: 250px; padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">

            <select name="permission_level" style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                <option value="">All Permission Levels</option>
                <option value="admin" {{ request('permission_level') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="manager" {{ request('permission_level') == 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="user" {{ request('permission_level') == 'user' ? 'selected' : '' }}>User</option>
            </select>

            <button type="submit" class="btn">Filter</button>

            @if(request()->hasAny(['search', 'permission_level']))
            <a href="{{ route('roles.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Roles Table -->
    <div class="content-card">
        @if($roles->count() > 0)
        <div class="table-container">
            <table class="roles-table">
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
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="role-icon">
                                    @if(is_array($role->permissions) && in_array('all', $role->permissions))
                                        <span style="color: #D32F2F;">⚡</span>
                                    @elseif(is_array($role->permissions) && in_array('manage_team', $role->permissions))
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
                            @if(is_array($role->permissions) && in_array('all', $role->permissions))
                                <span class="permission-badge super-admin">Super Admin</span>
                            @elseif(is_array($role->permissions) && in_array('manage_team', $role->permissions))
                                <span class="permission-badge manager">Manager</span>
                            @elseif(is_array($role->permissions) && (in_array('view_dashboard', $role->permissions) || in_array('view_jobs', $role->permissions)))
                                <span class="permission-badge user">User</span>
                            @else
                                <span class="permission-badge limited">Limited</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 300px;">
                                @if(!is_array($role->permissions) || empty($role->permissions))
                                    <span style="color: #999; font-style: italic; font-size: 12px;">No permissions</span>
                                @else
                                    @php
                                        $displayPermissions = array_slice($role->permissions, 0, 3);
                                        $remainingCount = count($role->permissions) - 3;
                                    @endphp
                                    @foreach($displayPermissions as $permission)
                                        @php
                                            $colors = [
                                                'all' => ['bg' => '#FFEBEE', 'text' => '#D32F2F'],
                                                'manage_team' => ['bg' => '#F3E5F5', 'text' => '#7B1FA2'],
                                                'manage_assets' => ['bg' => '#E8F5E8', 'text' => '#388E3C'],
                                                'view_dashboard' => ['bg' => '#E3F2FD', 'text' => '#1976D2'],
                                                'view_clients' => ['bg' => '#FFF3E0', 'text' => '#F57C00'],
                                                'view_jobs' => ['bg' => '#E0F2F1', 'text' => '#00796B'],
                                            ];
                                            $color = $colors[$permission] ?? ['bg' => '#F5F5F5', 'text' => '#666'];
                                            $displayName = ucwords(str_replace('_', ' ', $permission));
                                        @endphp
                                        <span class="permission-tag" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                            {{ $displayName }}
                                        </span>
                                    @endforeach
                                    @if($remainingCount > 0)
                                        <span class="permission-tag" style="background: #F5F5F5; color: #666;">
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
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Create First Role</a>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(method_exists($roles, 'links'))
    <div style="margin-top: 30px; display: flex; justify-content: center;">
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

.roles-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.roles-table th {
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

.roles-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #F0F0F0;
    vertical-align: middle;
}

.roles-table tr:hover {
    background: #FAFAFA;
}

.role-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #F8F9FA;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

/* Permission Badges */
.permission-badge {
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.permission-badge.super-admin { background: #FFEBEE; color: #D32F2F; }
.permission-badge.manager { background: #F3E5F5; color: #7B1FA2; }
.permission-badge.user { background: #E3F2FD; color: #1976D2; }
.permission-badge.limited { background: #F5F5F5; color: #666; }

.permission-tag {
    padding: 2px 6px;
    border-radius: 8px;
    font-size: 11px;
    white-space: nowrap;
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
    text-align: left;
    width: 100%;
}

.modal-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

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
