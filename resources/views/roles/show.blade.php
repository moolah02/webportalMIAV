{{-- resources/views/roles/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Role Details')

@section('header-actions')
<a href="{{ route('roles.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Roles</a>
<a href="{{ route('roles.edit', $role) }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit Role</a>
@endsection

@push('styles')
<style>
.rs-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 16px; align-items: start; }
@media (max-width: 1100px) { .rs-layout { grid-template-columns: 1fr; } }
.rs-main, .rs-side { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.rs-head { display: flex; align-items: center; gap: 14px; padding: 16px 18px; flex-wrap: wrap; }
.rs-mark { width: 40px; height: 40px; border-radius: 9px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rs-mark.is-admin { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.rs-title { font-size: 16px; font-weight: 600; color: var(--mv-ink); margin: 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.rs-sub { font-size: 12.5px; color: var(--mv-muted); margin-top: 2px; }
.rs-kpis { display: flex; gap: 28px; margin-left: auto; }
.rs-kpi span { display: block; font-size: 11.5px; color: var(--mv-muted); }
.rs-kpi strong { font-size: 18px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.rs-body { padding: 16px 18px; }
.rs-mono { font-family: var(--mv-mono); font-size: 12.5px; color: var(--mv-ink-2); }
.rs-group { border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
.rs-group + .rs-group { margin-top: 12px; }
.rs-group-head { display: flex; align-items: center; gap: 10px; padding: 9px 14px; background: var(--mv-surface-2); border-bottom: 1px solid var(--mv-line); }
.rs-group-head .mv-i { color: var(--mv-ink-2); }
.rs-group-title { font-size: 13px; font-weight: 600; color: var(--mv-ink); }
.rs-group-count { margin-left: auto; font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.rs-perms { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); margin-bottom: -1px; }
@media (max-width: 760px) { .rs-perms { grid-template-columns: 1fr; } }
.rs-perm { padding: 9px 14px; border-bottom: 1px solid var(--mv-line); }
@media (min-width: 761px) { .rs-perms .rs-perm:nth-child(odd) { border-right: 1px solid var(--mv-line); } }
.rs-perm-name { font-size: 13px; font-weight: 500; color: var(--mv-ink); }
.rs-perm-desc { font-size: 12px; color: var(--mv-muted); margin-top: 1px; line-height: 1.4; }
.rs-danger { display: inline-block; font-size: 10.5px; font-weight: 600; letter-spacing: .03em; color: var(--mv-crit); background: var(--mv-crit-soft); border: 1px solid #F2CACA; border-radius: 5px; padding: 0 5px; margin-left: 6px; vertical-align: 1px; }
.rs-person { display: flex; align-items: center; gap: 10px; }
.rs-avatar { width: 30px; height: 30px; border-radius: 50%; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; flex-shrink: 0; }
.rs-muted { color: var(--mv-muted); font-size: 12.5px; }
.rs-empty { padding: 28px 18px; text-align: center; color: var(--mv-muted); font-size: 13px; }
.rs-actions { display: flex; flex-direction: column; gap: 8px; padding: 14px 18px; }
.rs-actions > * { width: 100%; justify-content: center; }
.rs-list { padding: 4px 18px; }
.rs-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; font-size: 13px; color: var(--mv-ink-2); }
.rs-row + .rs-row { border-top: 1px solid var(--mv-line); }
.rs-row strong { color: var(--mv-ink); font-weight: 600; font-variant-numeric: tabular-nums; }
</style>
@endpush

@section('content')
@php
    $rolePermNames = $role->rolePermissions->pluck('name')->toArray();
    $hasAll = in_array('all', $rolePermNames);
    $employees = $role->employees;
    $displayName = ucfirst(str_replace('_', ' ', $role->name));

    $permissionCount = count($rolePermNames);
    if ($hasAll) {
        $level = ['Super Admin', 'badge-red'];
    } elseif (in_array('manage_team', $rolePermNames) || in_array('manage_assets', $rolePermNames)) {
        $level = ['Admin', 'badge-yellow'];
    } elseif ($permissionCount > 3) {
        $level = ['Elevated', 'badge-blue'];
    } else {
        $level = ['Standard', 'badge-green'];
    }

    $categoryConfig = [
        'admin'      => ['name' => 'System Administration', 'icon' => 'settings'],
        'dashboard'  => ['name' => 'Dashboard Access',      'icon' => 'grid'],
        'assets'     => ['name' => 'Asset Management',      'icon' => 'box'],
        'operations' => ['name' => 'Field Operations',      'icon' => 'wrench'],
        'clients'    => ['name' => 'Client Management',     'icon' => 'building'],
        'management' => ['name' => 'Employee Management',   'icon' => 'users'],
        'technician' => ['name' => 'Technician Portal',     'icon' => 'user-check'],
        'reports'    => ['name' => 'Reports & Analytics',   'icon' => 'chart'],
        'special'    => ['name' => 'Special Operations',    'icon' => 'zap'],
    ];
    $groupedPermissions = collect($allPermissions)
        ->filter(fn ($permission, $key) => in_array($key, $rolePermNames))
        ->groupBy('category', true);
    $knownKeys = array_keys($allPermissions);
    $otherPerms = array_values(array_diff($rolePermNames, $knownKeys));
@endphp

<div class="ui-card" style="margin-bottom:16px">
    <div class="rs-head">
        <span class="rs-mark {{ $hasAll ? 'is-admin' : '' }}"><svg class="mv-i" aria-hidden="true"><use href="#i-{{ $hasAll ? 'shield' : 'key' }}"/></svg></span>
        <div>
            <h2 class="rs-title">{{ $displayName }} @if($hasAll)<span class="badge badge-red">Super Admin</span>@endif</h2>
            <div class="rs-sub">Role details and assigned employees</div>
        </div>
        <div class="rs-kpis">
            <div class="rs-kpi"><span>Assigned Employees</span><strong>{{ $employees->count() }}</strong></div>
            <div class="rs-kpi"><span>Total Permissions</span><strong>{{ $permissionCount }}</strong></div>
        </div>
    </div>
</div>

<div class="rs-layout">
    <div class="rs-main">
        <div class="ui-card">
            <div class="ui-card-header"><h3>Role Information</h3></div>
            <div class="rs-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px">
                <div><div class="ui-label">Role Name</div><div class="rs-mono">{{ $role->name }}</div></div>
                <div><div class="ui-label">Display Name</div><div>{{ $displayName }}</div></div>
                <div><div class="ui-label">Created</div><div>{{ $role->created_at ? $role->created_at->format('M d, Y') : '—' }}</div></div>
                <div><div class="ui-label">Permission Level</div><span class="badge {{ $level[1] }}">{{ $level[0] }}</span></div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Permissions</h3><span class="rs-muted">{{ $permissionCount }} granted</span></div>
            <div class="rs-body">
                @if(empty($rolePermNames))
                    <div class="rs-empty">No permissions assigned to this role</div>
                @else
                    @foreach($groupedPermissions as $category => $categoryPermissions)
                    @php $cfg = $categoryConfig[$category] ?? ['name' => ucfirst($category), 'icon' => 'layers']; @endphp
                    <div class="rs-group">
                        <div class="rs-group-head">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-{{ $cfg['icon'] }}"/></svg>
                            <span class="rs-group-title">{{ $cfg['name'] }}</span>
                            <span class="rs-group-count">{{ count($categoryPermissions) }}</span>
                        </div>
                        <div class="rs-perms">
                            @foreach($categoryPermissions as $key => $permission)
                            <div class="rs-perm">
                                <div class="rs-perm-name">{{ $permission['name'] }}@if(!empty($permission['danger']))<span class="rs-danger">DANGER</span>@endif</div>
                                <div class="rs-perm-desc">{{ $permission['description'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if(count($otherPerms))
                    <div class="rs-group">
                        <div class="rs-group-head">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-layers"/></svg>
                            <span class="rs-group-title">Other</span>
                            <span class="rs-group-count">{{ count($otherPerms) }}</span>
                        </div>
                        <div class="rs-perms">
                            @foreach($otherPerms as $name)
                            <div class="rs-perm"><div class="rs-perm-name rs-mono">{{ $name }}</div></div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="ui-card overflow-hidden">
            <div class="ui-card-header"><h3>Assigned Employees</h3><span class="rs-muted">{{ $employees->count() }}</span></div>
            @if($employees->count() > 0)
            <div class="overflow-x-auto">
                <table class="ui-table w-full">
                    <thead>
                        <tr><th>Employee</th><th>Department</th><th>Status</th><th style="width:60px"></th></tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        @php
                            // employees has a legacy `department` text column that shadows the relation
                            $dept = $employee->getRelationValue('department');
                            $deptName = $dept->name ?? ($employee->getAttributes()['department'] ?? null);
                            $sc = ['active' => 'badge-green', 'pending' => 'badge-yellow', 'inactive' => 'badge-gray'][$employee->status] ?? 'badge-gray';
                        @endphp
                        <tr>
                            <td>
                                <div class="rs-person">
                                    <span class="rs-avatar">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                                    <div>
                                        <div style="font-weight:500;color:var(--mv-ink)">{{ $employee->full_name }}</div>
                                        <div class="rs-muted">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $deptName ?: '—' }}</td>
                            <td><span class="badge {{ $sc }}">{{ ucfirst($employee->status) }}</span></td>
                            <td><a href="{{ route('employees.show', $employee) }}" class="action-btn" title="View"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="rs-empty">No employees have this role yet.</div>
            @endif
        </div>
    </div>

    <aside class="rs-side">
        <div class="ui-card">
            <div class="ui-card-header"><h3>Quick Actions</h3></div>
            <div class="rs-actions">
                <a href="{{ route('roles.edit', $role) }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit Role</a>
                <button type="button" onclick="cloneRole({{ $role->id }})" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-copy"/></svg> Clone Role</button>
                @if(!in_array($role->name, ['super_admin', 'admin', 'manager', 'employee', 'technician']))
                <button type="button" onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')" class="btn-danger"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-trash"/></svg> Delete Role</button>
                @endif
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Statistics</h3></div>
            <div class="rs-list">
                <div class="rs-row"><span>Assigned Employees</span><strong>{{ $employees->count() }}</strong></div>
                <div class="rs-row"><span>Active Employees</span><strong>{{ $employees->where('status', 'active')->count() }}</strong></div>
                <div class="rs-row"><span>Total Permissions</span><strong>{{ $permissionCount }}</strong></div>
                <div class="rs-row"><span>Permission Level</span><span class="badge {{ $level[1] }}">{{ $level[0] }}</span></div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Recent Activity</h3></div>
            <div class="rs-empty">Activity tracking coming soon</div>
        </div>
    </aside>
</div>

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
        .catch(() => alert('Failed to clone role'));
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
