@extends('layouts.app')
@section('title', 'Employee Details')

@section('header-actions')
<a href="{{ route('employees.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Employees</a>
<button type="button" onclick="sendEmail('{{ $employee->email }}')" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-mail"/></svg> Send Email</button>
<a href="{{ route('employees.edit', $employee) }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit Profile</a>
@endsection

@push('styles')
<style>
.es-layout { display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 16px; align-items: start; }
@media (max-width: 1000px) { .es-layout { grid-template-columns: 1fr; } }
.es-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.es-profile { padding: 20px 18px 16px; display: flex; align-items: center; gap: 14px; }
.es-avatar { width: 52px; height: 52px; border-radius: 50%; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); display: inline-flex; align-items: center; justify-content: center; font-size: 17px; font-weight: 600; flex-shrink: 0; }
.es-name { font-size: 15.5px; font-weight: 600; color: var(--mv-ink); }
.es-role { font-size: 12.5px; color: var(--mv-muted); margin: 2px 0 6px; }
.es-rows { padding: 2px 18px 6px; border-top: 1px solid var(--mv-line); }
.es-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 9px 0; font-size: 13px; }
.es-row + .es-row { border-top: 1px solid var(--mv-line); }
.es-row > span:first-child { color: var(--mv-muted); flex-shrink: 0; }
.es-row > :last-child { color: var(--mv-ink); text-align: right; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.es-mono { font-family: var(--mv-mono); font-size: 12.5px; }
.es-link { color: var(--mv-accent-ink); text-decoration: none; }
.es-link:hover { text-decoration: underline; }
.es-body { padding: 16px 18px; }
.es-dl { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 20px; padding: 16px 18px; }
.es-dl-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
@media (max-width: 760px) { .es-dl, .es-dl-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.es-dl .v { font-size: 13.5px; color: var(--mv-ink); margin-top: 2px; }
.es-chips { display: flex; flex-wrap: wrap; gap: 5px; }
.es-chip { font-size: 11.5px; padding: 2px 7px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); }
.es-chip.is-crit { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.es-sub { font-size: 11.5px; font-weight: 600; color: var(--mv-muted); letter-spacing: .05em; text-transform: uppercase; margin: 14px 0 8px; }
.es-muted { color: var(--mv-muted); font-size: 12.5px; }
</style>
@endpush

@section('content')
@php
    try { $role = \App\Models\Role::find($employee->role_id); } catch (\Exception $e) { $role = null; }
    $roleName = $role ? ucfirst(str_replace('_', ' ', $role->name)) : 'No Role Assigned';
    $empStatusClass = match($employee->status ?? 'pending') {
        'active'  => 'badge-green',
        'pending' => 'badge-yellow',
        default   => 'badge-gray',
    };
    try {
        // employees has a legacy `department` text column that shadows the relation
        $dept = $employee->getRelationValue('department');
        $departmentName = $dept->name ?? ($employee->getAttributes()['department'] ?? null) ?: 'No Department';
    } catch (\Throwable $e) { $departmentName = 'No Department'; }
    try { $managerName = $employee->manager ? $employee->manager->first_name . ' ' . $employee->manager->last_name : 'No Manager'; } catch (\Throwable $e) { $managerName = 'No Manager'; }
    $rolePerms = ($role && is_array($role->permissions)) ? $role->permissions : [];
@endphp

<div class="es-layout">
    <div class="es-col">
        <div class="ui-card">
            <div class="es-profile">
                <span class="es-avatar">{{ substr($employee->first_name ?? 'N', 0, 1) }}{{ substr($employee->last_name ?? 'A', 0, 1) }}</span>
                <div style="min-width:0">
                    <div class="es-name">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                    <div class="es-role">{{ $roleName }}</div>
                    <span class="badge {{ $empStatusClass }}">{{ ucfirst($employee->status ?? 'Pending') }}</span>
                </div>
            </div>
            <div class="es-rows">
                <div class="es-row"><span>Employee ID</span><span class="es-mono">{{ $employee->employee_number ?? $employee->id }}</span></div>
                <div class="es-row"><span>Email</span><a href="mailto:{{ $employee->email }}" class="es-link">{{ $employee->email }}</a></div>
                @if($employee->phone)
                <div class="es-row"><span>Phone</span><span>{{ $employee->phone }}</span></div>
                @endif
                <div class="es-row"><span>Hire Date</span><span>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'Not set' }}</span></div>
                @if($employee->last_login_at)
                <div class="es-row"><span>Last Login</span><span>{{ \Carbon\Carbon::parse($employee->last_login_at)->diffForHumans() }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <div class="es-col">
        <div class="ui-card">
            <div class="ui-card-header"><h3>Role &amp; Permissions</h3></div>
            <div class="es-body">
                @if($role)
                    <div class="es-chips">
                        <span class="badge badge-blue">{{ $roleName }}</span>
                        @if(in_array('all', $rolePerms))
                            <span class="badge badge-red">Super Admin</span>
                        @endif
                    </div>
                    <div class="es-sub">Permissions</div>
                    <div class="es-chips">
                        @forelse($rolePerms as $permission)
                            <span class="es-chip {{ $permission === 'all' ? 'is-crit' : '' }}">{{ ucwords(str_replace('_', ' ', $permission)) }}</span>
                        @empty
                            <span class="es-muted">No permissions assigned</span>
                        @endforelse
                    </div>
                @else
                    <div class="alert-danger" style="padding:11px 14px;font-size:13px;border:1px solid">
                        <strong>No Role Assigned</strong> — this employee needs a role to access the system.
                    </div>
                @endif
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Department &amp; Reporting</h3></div>
            <div class="es-dl">
                <div><div class="ui-label">Department</div><div class="v">{{ $departmentName }}</div></div>
                <div><div class="ui-label">Reports To</div><div class="v">{{ $managerName }}</div></div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Additional Information</h3></div>
            <div class="es-dl es-dl-4">
                <div><div class="ui-label">Time Zone</div><div class="v">{{ $employee->time_zone ?? 'UTC' }}</div></div>
                <div><div class="ui-label">Language</div><div class="v">{{ strtoupper($employee->language ?? 'EN') }}</div></div>
                <div>
                    <div class="ui-label">Two Factor Auth</div>
                    <div class="v">
                        @if($employee->two_factor_enabled)
                            <span class="badge badge-green">Enabled</span>
                        @else
                            <span class="badge badge-gray">Disabled</span>
                        @endif
                    </div>
                </div>
                <div><div class="ui-label">Account Created</div><div class="v">{{ $employee->created_at->format('M d, Y') }}</div></div>
            </div>
        </div>
    </div>
</div>

<script>
function sendEmail(email) { window.location.href = 'mailto:' + email; }
</script>
@endsection
