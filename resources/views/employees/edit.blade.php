@extends('layouts.app')
@section('title', 'Edit Employee')

@section('header-actions')
<a href="{{ route('employees.show', $employee) }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Profile</a>
@endsection

@push('styles')
<style>
.ef-modal { position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(22, 32, 44, .45); }
.ef-modal.hidden { display: none; }
.ef-modal-box { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; width: 92%; max-width: 380px; padding: 20px; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
.ef-modal-box h3 { margin: 0 0 4px; font-size: 15px; font-weight: 600; color: var(--mv-ink); display: flex; align-items: center; gap: 8px; }
.ef-modal-box p { font-size: 13px; color: var(--mv-muted); margin: 0 0 14px; }
.ef-pass { font-family: var(--mv-mono); font-size: 18px; font-weight: 600; letter-spacing: .12em; color: var(--mv-ink); background: var(--mv-surface-2); border: 1px solid var(--mv-line-strong); border-radius: 8px; padding: 10px 12px; text-align: center; margin-bottom: 14px; user-select: all; }
.ef-modal-actions { display: flex; gap: 8px; justify-content: flex-end; }
</style>
@endpush

@section('content')
@include('employees.partials.form-styles')

@php
    try { $roles = \App\Models\Role::all(); $employeeRoleIds = $employee->roles->pluck('id')->toArray(); }
    catch (\Exception $e) { $roles = collect(); $employeeRoleIds = []; }
    try { $departments = \App\Models\Department::all(); }
    catch (\Exception $e) { $departments = collect([(object)['id' => 1, 'name' => 'IT']]); }
    try { $currentRole = \App\Models\Role::find($employee->role_id); } catch (\Exception $e) { $currentRole = null; }
@endphp

<form method="POST" action="{{ route('employees.update', $employee) }}" id="editForm">
    @csrf
    @method('PUT')
    <div class="ef-layout">
        <div class="ef-col">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Personal Information</h3></div>
                <div class="ef-grid">
                    <div>
                        <label class="ui-label">First Name <span class="ef-req">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="ui-input">
                        @error('first_name')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Last Name <span class="ef-req">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="ui-input">
                        @error('last_name')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Email Address <span class="ef-req">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $employee->email) }}" required class="ui-input">
                        @error('email')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="+1 (555) 123-4567" class="ui-input">
                        @error('phone')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Role &amp; Department</h3></div>
                <div class="ef-grid">
                    <div>
                        <label class="ui-label">Primary Role <span class="ef-req">*</span></label>
                        <select name="role_id" required class="ui-select">
                            <option value="">Select Primary Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}{{ is_array($role->permissions) && in_array('all', $role->permissions) ? ' (Super Admin)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('role_id')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Department</label>
                        <select name="department_id" class="ui-select">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ui-label">Status <span class="ef-req">*</span></label>
                        <select name="status" required class="ui-select">
                            <option value="active"   {{ old('status', $employee->status) == 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="pending"  {{ old('status', $employee->status) == 'pending'  ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="ui-label">Hire Date</label>
                        <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '') }}" class="ui-input">
                    </div>
                    <div class="ef-full">
                        <label class="ui-label">Additional Roles <span class="ef-opt">(Optional)</span></label>
                        <div class="ef-roles">
                            <div class="ef-roles-note">Current: <strong>{{ $employee->roles->count() > 0 ? $employee->roles->pluck('name')->join(', ') : 'None' }}</strong></div>
                            <div class="ef-roles-grid" style="margin:0 -1px -1px 0">
                                @foreach($roles as $role)
                                <label class="ef-role">
                                    <input type="checkbox" name="additional_roles[]" value="{{ $role->id }}"
                                           {{ in_array($role->id, old('additional_roles', $employeeRoleIds)) ? 'checked' : '' }}>
                                    <span>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Preferences</h3></div>
                <div class="ef-grid">
                    <div>
                        <label class="ui-label">Time Zone</label>
                        <select name="time_zone" class="ui-select">
                            <option value="UTC"                 {{ old('time_zone', $employee->time_zone) == 'UTC'                 ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York"    {{ old('time_zone', $employee->time_zone) == 'America/New_York'    ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago"     {{ old('time_zone', $employee->time_zone) == 'America/Chicago'     ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver"      {{ old('time_zone', $employee->time_zone) == 'America/Denver'      ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ old('time_zone', $employee->time_zone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                        </select>
                    </div>
                    <div>
                        <label class="ui-label">Language</label>
                        <select name="language" class="ui-select">
                            <option value="en" {{ old('language', $employee->language) == 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ old('language', $employee->language) == 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="fr" {{ old('language', $employee->language) == 'fr' ? 'selected' : '' }}>French</option>
                        </select>
                    </div>
                    <div class="ef-full">
                        <label class="ef-check">
                            <input type="checkbox" name="two_factor_enabled" value="1" {{ old('two_factor_enabled', $employee->two_factor_enabled) ? 'checked' : '' }}>
                            <span>Enable Two-Factor Authentication</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="ef-col">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Current Role</h3></div>
                <div class="ef-body">
                    @if($currentRole)
                        <div class="ef-chips">
                            <span class="badge badge-blue">{{ ucfirst(str_replace('_', ' ', $currentRole->name)) }}</span>
                            @if(is_array($currentRole->permissions) && in_array('all', $currentRole->permissions))
                                <span class="badge badge-red">Super Admin</span>
                            @endif
                        </div>
                        <div class="ef-hint">{{ is_array($currentRole->permissions) ? count($currentRole->permissions) : 0 }} permission(s)</div>
                    @else
                        <span class="badge badge-red">No Role Assigned</span>
                    @endif
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Actions</h3></div>
                <div class="ef-actions" style="justify-content:stretch;flex-direction:column">
                    <button type="submit" class="btn-primary" style="justify-content:center"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg> Save Changes</button>
                    <a href="{{ route('employees.show', $employee) }}" class="btn-secondary" style="justify-content:center;text-align:center">Cancel</a>
                    <button type="button" onclick="resetPassword()" class="btn-warning" style="justify-content:center"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-key"/></svg> Reset Password</button>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Employee Stats</h3></div>
                <div class="ef-rows">
                    <div class="ef-row"><span>Employee ID</span><strong style="font-family:var(--mv-mono);font-size:12.5px">{{ $employee->employee_number ?? $employee->id }}</strong></div>
                    <div class="ef-row"><span>Member Since</span><strong>{{ $employee->created_at->format('M Y') }}</strong></div>
                    <div class="ef-row"><span>Last Updated</span><strong>{{ $employee->updated_at->diffForHumans() }}</strong></div>
                    @if($employee->last_login_at)
                    <div class="ef-row"><span>Last Login</span><strong>{{ \Carbon\Carbon::parse($employee->last_login_at)->diffForHumans() }}</strong></div>
                    @endif
                </div>
            </div>

            <div class="ui-card">
                <div class="ef-note">
                    <div><strong>Role changes</strong> take effect immediately</div>
                    <div><strong>Email changes</strong> require verification</div>
                    <div><strong>Status changes</strong> affect system access</div>
                    <div>Use <strong>Reset Password</strong> for login issues</div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Temp password reveal modal --}}
<div id="tempPasswordModal" class="ef-modal hidden">
    <div class="ef-modal-box">
        <h3><svg class="mv-i mv-i-sm" aria-hidden="true" style="color:var(--mv-muted)"><use href="#i-key"/></svg> Password Reset</h3>
        <p>Share this temporary password with <strong id="resetEmpName"></strong>. It is only shown once.</p>
        <div class="ef-pass" id="tempPasswordDisplay"></div>
        <div class="ef-modal-actions">
            <button type="button" onclick="copyTempPassword(event)" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-copy"/></svg> Copy Password</button>
            <button type="button" onclick="document.getElementById('tempPasswordModal').classList.add('hidden')" class="btn-primary">Done</button>
        </div>
    </div>
</div>

<script>
async function resetPassword() {
    if (!confirm("Reset this employee's password? A new temporary password will be generated.")) return;
    try {
        const res = await fetch("{{ route('employees.reset-password', $employee) }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('tempPasswordDisplay').textContent = data.temp_password;
            document.getElementById('resetEmpName').textContent = data.employee_name;
            document.getElementById('tempPasswordModal').classList.remove('hidden');
        } else {
            alert(data.message || 'Failed to reset password.');
        }
    } catch (e) {
        alert('Error resetting password. Please try again.');
    }
}
function copyTempPassword(e) {
    const btn = e ? e.currentTarget : null;
    const pwd = document.getElementById('tempPasswordDisplay').textContent;
    navigator.clipboard.writeText(pwd).then(() => {
        if (!btn) return;
        const original = btn.innerHTML;
        btn.textContent = 'Copied!';
        setTimeout(() => btn.innerHTML = original, 2000);
    });
}
let formDirty = false;
document.getElementById('editForm').addEventListener('change', function() { formDirty = true; });
document.getElementById('editForm').addEventListener('submit', function() { formDirty = false; });
window.addEventListener('beforeunload', function(e) { if (formDirty) { e.preventDefault(); e.returnValue = ''; } });
</script>
@endsection
