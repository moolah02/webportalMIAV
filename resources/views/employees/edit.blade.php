@extends('layouts.app')
@section('title', 'Edit Employee')

@section('content')
<div>
    <div class="flex items-start justify-between mb-5">
        <a href="{{ route('employees.show', $employee) }}" class="btn-secondary btn-sm">&#8592; Back to Profile</a>
    </div>

    <form method="POST" action="{{ route('employees.update', $employee) }}" id="editForm">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Main Form (2/3) --}}
            <div class="lg:col-span-2 flex flex-col gap-5">

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Personal Information</h4></div>
                    <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="ui-input w-full">
                            @error('first_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="ui-input w-full">
                            @error('last_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" required class="ui-input w-full">
                            @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="+1 (555) 123-4567" class="ui-input w-full">
                            @error('phone')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Role &amp; Department</h4></div>
                    <div class="ui-card-body flex flex-col gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="ui-label">Primary Role <span class="text-red-500">*</span></label>
                                <select name="role_id" required class="ui-select w-full">
                                    <option value="">Select Primary Role</option>
                                    @php
                                        try { $roles = \App\Models\Role::all(); $employeeRoleIds = $employee->roles->pluck('id')->toArray(); }
                                        catch (\Exception $e) { $roles = collect(); $employeeRoleIds = []; }
                                    @endphp
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}{{ is_array($role->permissions) && in_array('all', $role->permissions) ? ' (Super Admin)' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="ui-label">Department</label>
                                <select name="department_id" class="ui-select w-full">
                                    <option value="">Select Department</option>
                                    @php
                                        try { $departments = \App\Models\Department::all(); }
                                        catch (\Exception $e) { $departments = collect([(object)['id'=>1,'name'=>'IT']]); }
                                    @endphp
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="ui-label">Status <span class="text-red-500">*</span></label>
                                <select name="status" required class="ui-select w-full">
                                    <option value="active"   {{ old('status', $employee->status) == 'active'   ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="pending"  {{ old('status', $employee->status) == 'pending'  ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <div>
                                <label class="ui-label">Hire Date</label>
                                <input type="date" name="hire_date"
                                       value="{{ old('hire_date', $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '') }}"
                                       class="ui-input w-full">
                            </div>
                        </div>
                        <div>
                            <label class="ui-label mb-2">Additional Roles <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <p class="text-xs text-gray-500 mb-3">Current: <strong>{{ $employee->roles->count() > 0 ? $employee->roles->pluck('name')->join(', ') : 'None' }}</strong></p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($roles as $role)
                                    <label class="flex items-center gap-2.5 px-3 py-2 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-[#1a3a5c] hover:bg-blue-50 transition-all">
                                        <input type="checkbox" name="additional_roles[]" value="{{ $role->id }}"
                                               {{ in_array($role->id, old('additional_roles', $employeeRoleIds)) ? 'checked' : '' }}
                                               class="w-4 h-4 cursor-pointer">
                                        <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Preferences</h4></div>
                    <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">Time Zone</label>
                            <select name="time_zone" class="ui-select w-full">
                                <option value="UTC"                 {{ old('time_zone', $employee->time_zone) == 'UTC'                 ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York"   {{ old('time_zone', $employee->time_zone) == 'America/New_York'   ? 'selected' : '' }}>Eastern Time</option>
                                <option value="America/Chicago"    {{ old('time_zone', $employee->time_zone) == 'America/Chicago'    ? 'selected' : '' }}>Central Time</option>
                                <option value="America/Denver"     {{ old('time_zone', $employee->time_zone) == 'America/Denver'     ? 'selected' : '' }}>Mountain Time</option>
                                <option value="America/Los_Angeles"{{ old('time_zone', $employee->time_zone) == 'America/Los_Angeles'? 'selected' : '' }}>Pacific Time</option>
                            </select>
                        </div>
                        <div>
                            <label class="ui-label">Language</label>
                            <select name="language" class="ui-select w-full">
                                <option value="en" {{ old('language', $employee->language) == 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ old('language', $employee->language) == 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ old('language', $employee->language) == 'fr' ? 'selected' : '' }}>French</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="two_factor_enabled" value="1"
                                       {{ old('two_factor_enabled', $employee->two_factor_enabled) ? 'checked' : '' }} class="w-4 h-4">
                                <span class="text-sm font-medium text-gray-700">Enable Two-Factor Authentication</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar (1/3) --}}
            <div class="flex flex-col gap-5">

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Current Role</h4></div>
                    <div class="ui-card-body">
                        @php try { $currentRole = \App\Models\Role::find($employee->role_id); } catch (\Exception $e) { $currentRole = null; } @endphp
                        @if($currentRole)
                            <div class="bg-gray-50 rounded-lg p-3 flex flex-wrap gap-2 items-center">
                                <span class="badge badge-blue">{{ ucfirst(str_replace('_', ' ', $currentRole->name)) }}</span>
                                @if(is_array($currentRole->permissions) && in_array('all', $currentRole->permissions))
                                    <span class="badge badge-red">Super Admin</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mt-2">{{ is_array($currentRole->permissions) ? count($currentRole->permissions) : 0 }} permission(s)</p>
                        @else
                            <div class="bg-red-50 text-red-700 text-sm p-3 rounded-lg text-center">No Role Assigned</div>
                        @endif
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Actions</h4></div>
                    <div class="ui-card-body flex flex-col gap-2.5">
                        <button type="submit" class="btn-primary w-full justify-center">Save Changes</button>
                        <a href="{{ route('employees.show', $employee) }}" class="btn-secondary w-full text-center">Cancel</a>
                        <button type="button" onclick="resetPassword()" class="w-full px-3 py-2 rounded-lg text-sm font-medium bg-amber-500 text-white hover:bg-amber-600 transition-colors cursor-pointer">Reset Password</button>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Employee Stats</h4></div>
                    <div class="ui-card-body flex flex-col divide-y divide-gray-100">
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs text-gray-500">Employee ID</span>
                            <span class="text-sm font-medium text-gray-900">{{ $employee->employee_number ?? $employee->id }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs text-gray-500">Member Since</span>
                            <span class="text-sm font-medium text-gray-900">{{ $employee->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs text-gray-500">Last Updated</span>
                            <span class="text-sm font-medium text-gray-900">{{ $employee->updated_at->diffForHumans() }}</span>
                        </div>
                        @if($employee->last_login_at)
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs text-gray-500">Last Login</span>
                            <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($employee->last_login_at)->diffForHumans() }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="ui-card bg-blue-50 border-blue-200">
                    <div class="ui-card-body text-sm text-gray-600 space-y-1.5">
                        <p><strong class="text-gray-800">Role changes</strong> take effect immediately</p>
                        <p><strong class="text-gray-800">Email changes</strong> require verification</p>
                        <p><strong class="text-gray-800">Status changes</strong> affect system access</p>
                        <p>Use <strong class="text-gray-800">Reset Password</strong> for login issues</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function resetPassword() {
    if (confirm('Reset this employee\'s password?')) {
        alert('Password reset functionality will be implemented soon.');
    }
}
let formDirty = false;
document.getElementById('editForm').addEventListener('change', function() { formDirty = true; });
document.getElementById('editForm').addEventListener('submit', function() { formDirty = false; });
window.addEventListener('beforeunload', function(e) { if (formDirty) { e.preventDefault(); e.returnValue = ''; } });
</script>
@endsection