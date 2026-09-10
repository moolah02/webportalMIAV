{{-- resources/views/roles/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Role Management')

@section('header-actions')
<a href="{{ route('roles.create') }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Create Role</a>
@endsection

@push('styles')
<style>
.rl-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
@media (max-width: 900px) { .rl-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.rl-name { display: flex; align-items: center; gap: 10px; }
.rl-name-mark { width: 30px; height: 30px; border-radius: 8px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rl-name-mark.is-admin { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.rl-name-title { font-weight: 500; color: var(--mv-ink); font-size: 13.5px; }
.rl-name-id { font-family: var(--mv-mono); font-size: 11.5px; color: var(--mv-muted); }
.rl-perms { display: flex; flex-wrap: wrap; gap: 4px; max-width: 320px; }
.rl-perm { font-size: 11.5px; padding: 2px 7px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); white-space: nowrap; }
.rl-perm.is-all { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.rl-count { text-align: right; font-variant-numeric: tabular-nums; }
.rl-count > div:first-child { font-weight: 600; color: var(--mv-ink); font-size: 13.5px; }
.rl-count > div + div { font-size: 11.5px; color: var(--mv-muted); }
.rl-muted { color: var(--mv-muted); font-size: 12.5px; }

/* Quick actions modal */
.rl-modal { display: none; position: fixed; inset: 0; background: rgba(22, 32, 44, .45); z-index: 1000; justify-content: center; align-items: center; }
.rl-modal-box { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; width: 92%; max-width: 380px; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); overflow: hidden; }
.rl-modal-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--mv-line); }
.rl-modal-head h3 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); flex: 1; }
.rl-modal-close { background: none; border: 0; color: var(--mv-muted); cursor: pointer; padding: 4px; border-radius: 6px; display: inline-flex; }
.rl-modal-close:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.rl-modal-body { padding: 8px; display: grid; gap: 2px; }
.modal-action-btn { display: flex; align-items: center; gap: 12px; width: 100%; text-align: left; background: transparent; border: 0; border-radius: 8px; padding: 10px 12px; cursor: pointer; color: var(--mv-ink); }
.modal-action-btn:hover { background: var(--mv-surface-2); }
.modal-action-btn .mv-i { color: var(--mv-ink-2); }
.modal-action-btn .t { font-size: 13.5px; font-weight: 500; }
.modal-action-btn .d { font-size: 12px; color: var(--mv-muted); }
.modal-action-btn.is-danger, .modal-action-btn.is-danger .mv-i { color: var(--mv-crit); }
.modal-action-btn.is-danger:hover { background: var(--mv-crit-soft); }
</style>
@endpush

@section('content')
<div>
    <div class="rl-stats">
        <div class="stat-card">
            <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-key"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['total_roles'] ?? 0 }}</div>
                <div class="stat-label">Total Roles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-shield"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['roles_with_admin'] ?? 0 }}</div>
                <div class="stat-label">Admin Roles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-sliders"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['custom_roles'] ?? 0 }}</div>
                <div class="stat-label">Custom Roles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['employees_assigned'] ?? 0 }}</div>
                <div class="stat-label">Employees With Roles</div>
            </div>
        </div>
    </div>

    <form method="GET" class="filter-bar">
        <div class="filter-group" style="flex:1;min-width:200px">
            <label class="ui-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search roles…" class="ui-input" style="width:100%">
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

    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <h3>Roles</h3>
            <span class="rl-muted">{{ $roles->count() }} roles</span>
        </div>
        @if($roles->count() > 0)
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll" style="cursor: pointer;"></th>
                        <th>Role Name</th>
                        <th>Permission Level</th>
                        <th>Key Permissions</th>
                        <th style="width: 110px; text-align:right;">Employees</th>
                        <th>Created</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    @php
                        $rolePerms = $role->rolePermissions;
                        $isAll = $rolePerms->contains('name', 'all');
                    @endphp
                    <tr>
                        <td><input type="checkbox" name="role_ids[]" value="{{ $role->id }}" style="cursor: pointer;"></td>
                        <td>
                            <div class="rl-name">
                                <span class="rl-name-mark {{ $isAll ? 'is-admin' : '' }}">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-{{ $isAll ? 'shield' : ($rolePerms->contains('name', 'manage_team') ? 'users' : 'user') }}"/></svg>
                                </span>
                                <div>
                                    <div class="rl-name-title">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</div>
                                    <div class="rl-name-id">ID {{ $role->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($isAll)
                                <span class="badge badge-red">Super Admin</span>
                            @elseif($rolePerms->contains('name', 'manage_team'))
                                <span class="badge badge-blue">Manager</span>
                            @elseif($rolePerms->whereIn('name', ['view_dashboard', 'view_jobs'])->isNotEmpty())
                                <span class="badge badge-gray">User</span>
                            @elseif($rolePerms->isNotEmpty())
                                <span class="badge badge-gray">Limited Admin</span>
                            @else
                                <span class="badge badge-gray">Limited</span>
                            @endif
                        </td>
                        <td>
                            <div class="rl-perms">
                                @if($rolePerms->isEmpty())
                                    <span class="rl-muted">No permissions</span>
                                @else
                                    @foreach($rolePerms->take(3) as $perm)
                                        <span class="rl-perm {{ $perm->name === 'all' ? 'is-all' : '' }}">{{ ucwords(str_replace('_', ' ', $perm->name)) }}</span>
                                    @endforeach
                                    @if($rolePerms->count() > 3)
                                        <span class="rl-perm">+{{ $rolePerms->count() - 3 }} more</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="rl-count">
                                @php
                                    try {
                                        $employeeCount = \App\Models\Employee::where('role_id', $role->id)->count();
                                    } catch (\Exception $e) {
                                        $employeeCount = 0;
                                    }
                                @endphp
                                <div>{{ $employeeCount }}</div>
                                <div>assigned</div>
                            </div>
                        </td>
                        <td class="rl-muted">{{ $role->created_at ? $role->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('roles.show', $role) }}" class="action-btn" title="View"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a>
                                <a href="{{ route('roles.edit', $role) }}" class="action-btn" title="Edit"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg></a>
                                <button type="button" onclick="showQuickActions({{ $role->id }}, '{{ addslashes($role->name) }}')" class="action-btn" title="More Actions"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-more"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-key"/></svg></div>
            <p class="empty-state-msg">No roles found. Create your first role to get started.</p>
            <a href="{{ route('roles.create') }}" class="btn-primary" style="margin-top:12px">Create First Role</a>
        </div>
        @endif
    </div>

    @if(method_exists($roles, 'links'))
    <div class="mt-5 flex justify-center">
        {{ $roles->appends(request()->query())->links() }}
    </div>
    @endif
</div>

{{-- Quick actions modal (IDs used by the script below) --}}
<div id="quickActionsModal" class="rl-modal">
    <div class="rl-modal-box">
        <div class="rl-modal-head">
            <svg class="mv-i mv-i-sm" aria-hidden="true" style="color:var(--mv-muted)"><use href="#i-key"/></svg>
            <h3 id="modalRoleName">Role Actions</h3>
            <button type="button" onclick="closeQuickActions()" class="rl-modal-close" aria-label="Close"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="rl-modal-body">
            <button type="button" id="viewRoleBtn" class="modal-action-btn">
                <svg class="mv-i" aria-hidden="true"><use href="#i-eye"/></svg>
                <div><div class="t">View Details</div><div class="d">See complete role information</div></div>
            </button>
            <button type="button" id="editRoleBtn" class="modal-action-btn">
                <svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg>
                <div><div class="t">Edit Permissions</div><div class="d">Modify role permissions</div></div>
            </button>
            <button type="button" id="cloneRoleBtn" class="modal-action-btn">
                <svg class="mv-i" aria-hidden="true"><use href="#i-copy"/></svg>
                <div><div class="t">Clone Role</div><div class="d">Create a copy of this role</div></div>
            </button>
            <button type="button" id="deleteRoleBtn" class="modal-action-btn is-danger">
                <svg class="mv-i" aria-hidden="true"><use href="#i-trash"/></svg>
                <div><div class="t">Delete Role</div><div class="d">Remove this role permanently</div></div>
            </button>
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

    // Employee count lives in the 5th column's first div
    const row = document.querySelector(`tr:has(input[value="${currentRoleId}"])`);
    const employeeCountElement = row ? row.querySelector('td:nth-child(5) div div:first-child') : null;
    const employeeCount = employeeCountElement ? parseInt(employeeCountElement.textContent) : 0;

    if (employeeCount > 0) {
        alert(`Cannot delete role "${currentRoleName}" because it is assigned to ${employeeCount} employee(s). Please reassign these employees first.`);
        return;
    }

    if (!confirm(`Are you sure you want to delete the role "${currentRoleName}"? This action cannot be undone.`)) {
        return;
    }

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

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('input[name="role_ids[]"]').forEach(checkbox => {
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
