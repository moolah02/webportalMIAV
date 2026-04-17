$utf8NoBom = New-Object System.Text.UTF8Encoding $false
$base = 'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views\employees'

# ─── show.blade.php ──────────────────────────────────────────────────────────
$show = @'
@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="page-title">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
            <p class="page-subtitle mt-1">Employee Profile</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('employees.index') }}" class="btn-secondary btn-sm">&#8592; Back to Employees</a>
            <a href="{{ route('employees.edit', $employee) }}" class="btn-primary btn-sm">Edit Profile</a>
            <button onclick="sendEmail('{{ $employee->email }}')" class="btn-sm px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-600 text-white hover:bg-purple-700 transition-colors cursor-pointer">Send Email</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Left: Profile card --}}
        <div class="ui-card h-fit">
            <div class="ui-card-body flex flex-col items-center text-center">
                <div class="w-24 h-24 rounded-full bg-[#1a3a5c] flex items-center justify-center text-white text-3xl font-bold mb-4">
                    {{ substr($employee->first_name ?? 'N', 0, 1) }}{{ substr($employee->last_name ?? 'A', 0, 1) }}
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                <p class="text-sm text-gray-500 mb-3">
                    @php
                        try { $role = \App\Models\Role::find($employee->role_id); echo $role ? ucfirst(str_replace('_', ' ', $role->name)) : 'No Role Assigned'; } catch (\Exception $e) { echo 'No Role Assigned'; }
                    @endphp
                </p>
                @php
                    $empStatusClass = match($employee->status ?? 'pending') {
                        'active'  => 'badge-green',
                        'pending' => 'badge-yellow',
                        default   => 'badge-gray',
                    };
                @endphp
                <span class="badge {{ $empStatusClass }}">{{ ucfirst($employee->status ?? 'Pending') }}</span>
            </div>
            <div class="border-t border-gray-100">
                <div class="ui-card-body flex flex-col divide-y divide-gray-100">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Employee ID</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->employee_number ?? $employee->id }}</span>
                    </div>
                    <div class="flex items-start justify-between py-2 gap-2">
                        <span class="text-xs text-gray-500 uppercase tracking-wide flex-shrink-0">Email</span>
                        <a href="mailto:{{ $employee->email }}" class="text-sm text-[#1a3a5c] hover:underline truncate">{{ $employee->email }}</a>
                    </div>
                    @if($employee->phone)
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Phone</span>
                        <span class="text-sm text-gray-900">{{ $employee->phone }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Hire Date</span>
                        <span class="text-sm text-gray-900">{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'Not set' }}</span>
                    </div>
                    @if($employee->last_login_at)
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Last Login</span>
                        <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($employee->last_login_at)->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Details (2/3) --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Role & Permissions --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Role &amp; Permissions</h4>
                </div>
                <div class="ui-card-body">
                    @php
                        try { $role = \App\Models\Role::find($employee->role_id); } catch (\Exception $e) { $role = null; }
                    @endphp
                    @if($role)
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="badge badge-blue">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                            @if(is_array($role->permissions) && in_array('all', $role->permissions))
                                <span class="badge badge-red">Super Admin</span>
                            @endif
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500 uppercase tracking-wide mb-2">Permissions</div>
                            <div class="flex flex-wrap gap-1.5">
                                @if(is_array($role->permissions) && !empty($role->permissions))
                                    @foreach($role->permissions as $permission)
                                    @php
                                        $permClass = match($permission) {
                                            'all'            => 'badge-red',
                                            'manage_team'    => 'badge-purple',
                                            'manage_assets'  => 'badge-green',
                                            'view_dashboard' => 'badge-blue',
                                            'view_clients'   => 'badge-orange',
                                            default          => 'badge-gray',
                                        };
                                    @endphp
                                    <span class="badge {{ $permClass }}">{{ ucwords(str_replace('_', ' ', $permission)) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-sm text-gray-400 italic">No permissions assigned</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-red-50 text-red-700 p-4 rounded-lg text-sm text-center">
                            <strong>No Role Assigned</strong>
                            <p class="mt-1 text-xs text-red-500">This employee needs a role to access the system.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Department & Reporting --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Department &amp; Reporting</h4>
                </div>
                <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="ui-label">Department</div>
                        <div class="text-sm text-gray-900">@php try { echo $employee->department->name ?? 'No Department'; } catch (\Exception $e) { echo 'No Department'; } @endphp</div>
                    </div>
                    <div>
                        <div class="ui-label">Reports To</div>
                        <div class="text-sm text-gray-900">@php try { echo $employee->manager ? $employee->manager->first_name . ' ' . $employee->manager->last_name : 'No Manager'; } catch (\Exception $e) { echo 'No Manager'; } @endphp</div>
                    </div>
                </div>
            </div>

            {{-- Additional Information --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Additional Information</h4>
                </div>
                <div class="ui-card-body grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <div class="ui-label">Time Zone</div>
                        <div class="text-sm text-gray-900">{{ $employee->time_zone ?? 'UTC' }}</div>
                    </div>
                    <div>
                        <div class="ui-label">Language</div>
                        <div class="text-sm text-gray-900">{{ strtoupper($employee->language ?? 'EN') }}</div>
                    </div>
                    <div>
                        <div class="ui-label">Two Factor Auth</div>
                        <div class="text-sm">
                            @if($employee->two_factor_enabled)
                                <span class="badge badge-green">Enabled</span>
                            @else
                                <span class="badge badge-gray">Disabled</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="ui-label">Account Created</div>
                        <div class="text-sm text-gray-900">{{ $employee->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendEmail(email) { window.location.href = 'mailto:' + email; }
</script>
@endsection
'@

# ─── create.blade.php ────────────────────────────────────────────────────────
$create = @'
@extends('layouts.app')

@section('content')
<div>
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="page-title">Employee Onboarding</h2>
            <p class="page-subtitle mt-1">Add a new employee to the company</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn-secondary btn-sm">&#8592; Back to Employees</a>
    </div>

    @if($errors->any())
    <div class="flash-error mb-5">
        <strong>Please fix the following errors:</strong>
        <ul class="mt-2 ml-4 list-disc text-sm">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('employees.store') }}" method="POST" id="employeeForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Main Form (2/3) --}}
            <div class="lg:col-span-2 flex flex-col gap-5">

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Personal Information</h4></div>
                    <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="John" class="ui-input w-full">
                            @error('first_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Smith" class="ui-input w-full">
                            @error('last_name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="john.smith@company.com" class="ui-input w-full">
                            @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 123-4567" class="ui-input w-full">
                            @error('phone')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required placeholder="Secure password" class="ui-input w-full">
                            @error('password')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required placeholder="Confirm password" class="ui-input w-full">
                        </div>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Employment Information</h4></div>
                    <div class="ui-card-body flex flex-col gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="ui-label">Department <span class="text-red-500">*</span></label>
                                <select name="department_id" required class="ui-select w-full">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="ui-label">Primary Role <span class="text-red-500">*</span></label>
                                <select name="role_id" required class="ui-select w-full" onchange="showRolePermissions(this)">
                                    <option value="">Select Primary Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" data-permissions="{{ json_encode($role->permissions) }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="ui-label">Hire Date <span class="text-red-500">*</span></label>
                                <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required class="ui-input w-full">
                                @error('hire_date')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="ui-label">Manager</label>
                                <select name="manager_id" class="ui-select w-full">
                                    <option value="">Select Manager (Optional)</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->full_name }} &mdash; {{ $manager->role->name ?? 'No Role' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="ui-label">Time Zone</label>
                                <select name="time_zone" class="ui-select w-full">
                                    <option value="UTC" {{ old('time_zone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ old('time_zone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                    <option value="America/Chicago" {{ old('time_zone') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                    <option value="America/Denver" {{ old('time_zone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                    <option value="America/Los_Angeles" {{ old('time_zone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                    <option value="Europe/London" {{ old('time_zone') == 'Europe/London' ? 'selected' : '' }}>London</option>
                                    <option value="Asia/Tokyo" {{ old('time_zone') == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                                </select>
                            </div>
                            <div>
                                <label class="ui-label">Language</label>
                                <select name="language" class="ui-select w-full">
                                    <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>German</option>
                                    <option value="ja" {{ old('language') == 'ja' ? 'selected' : '' }}>Japanese</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="ui-label mb-2">Additional Roles <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <p class="text-xs text-gray-500 mb-3">Select additional roles to supplement the primary role.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($roles as $role)
                                    <label class="flex items-center gap-2.5 px-3 py-2 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-[#1a3a5c] hover:bg-blue-50 transition-all">
                                        <input type="checkbox" name="additional_roles[]" value="{{ $role->id }}"
                                               {{ is_array(old('additional_roles')) && in_array($role->id, old('additional_roles')) ? 'checked' : '' }}
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
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Additional Information</h4></div>
                    <div class="ui-card-body flex flex-col gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="ui-label">Position / Title</label>
                                <input type="text" name="position" value="{{ old('position') }}" placeholder="e.g., Senior Developer" class="ui-input w-full">
                            </div>
                            <div>
                                <label class="ui-label">Annual Salary</label>
                                <input type="number" name="salary" value="{{ old('salary') }}" step="0.01" min="0" placeholder="50000.00" class="ui-input w-full">
                            </div>
                        </div>
                        <div>
                            <label class="ui-label">Address</label>
                            <textarea name="address" rows="2" placeholder="123 Main Street" class="ui-input w-full">{{ old('address') }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div><label class="ui-label">City</label><input type="text" name="city" value="{{ old('city') }}" placeholder="New York" class="ui-input w-full"></div>
                            <div><label class="ui-label">State</label><input type="text" name="state" value="{{ old('state') }}" placeholder="NY" class="ui-input w-full"></div>
                            <div><label class="ui-label">Country</label><input type="text" name="country" value="{{ old('country') }}" placeholder="USA" class="ui-input w-full"></div>
                            <div><label class="ui-label">Postal Code</label><input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="10001" class="ui-input w-full"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="ui-label">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Jane Smith" class="ui-input w-full">
                            </div>
                            <div>
                                <label class="ui-label">Emergency Contact Phone</label>
                                <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="+1 (555) 987-6543" class="ui-input w-full">
                            </div>
                            <div>
                                <label class="ui-label">Relationship</label>
                                <select name="emergency_contact_relationship" class="ui-select w-full">
                                    <option value="">Select Relationship</option>
                                    @foreach(['spouse','parent','sibling','child','friend','other'] as $rel)
                                    <option value="{{ $rel }}" {{ old('emergency_contact_relationship') == $rel ? 'selected' : '' }}>{{ ucfirst($rel) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="ui-label">Skills / Certifications</label>
                                <textarea name="skills" rows="2" placeholder="CompTIA A+, Network Config..." class="ui-input w-full">{{ old('skills') }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Comma-separated list</p>
                            </div>
                            <div>
                                <label class="ui-label">Work Location</label>
                                <input type="text" name="work_location" value="{{ old('work_location') }}" placeholder="Head Office, Remote..." class="ui-input w-full">
                            </div>
                        </div>
                        <div>
                            <label class="ui-label">Profile Photo</label>
                            <input type="file" name="avatar" accept="image/*" class="ui-input w-full">
                            <p class="text-xs text-gray-400 mt-1">Optional</p>
                        </div>
                        <div>
                            <label class="ui-label">Notes</label>
                            <textarea name="notes" rows="3" placeholder="Any additional notes..." class="ui-input w-full">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar (1/3) --}}
            <div class="flex flex-col gap-5">
                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Employee Status</h4></div>
                    <div class="ui-card-body flex flex-col gap-3">
                        <div>
                            <label class="ui-label">Status <span class="text-red-500">*</span></label>
                            <select name="status" required class="ui-select w-full">
                                <option value="active"   {{ old('status', 'active') == 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="pending"  {{ old('status') == 'pending'  ? 'selected' : '' }}>Pending Onboarding</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="two_factor_enabled" value="1" {{ old('two_factor_enabled') ? 'checked' : '' }} class="w-4 h-4">
                            <span class="text-sm text-gray-700">Enable Two-Factor Auth</span>
                        </label>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Role Permissions</h4></div>
                    <div class="ui-card-body">
                        <div id="role-permissions" class="text-sm text-gray-400 italic">Select a role to see permissions</div>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-header"><h4 class="text-sm font-semibold text-gray-800 m-0">Employee Number</h4></div>
                    <div class="ui-card-body">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-xs text-gray-500 mb-1">Will be auto-generated</div>
                            <div id="employee-number-preview" class="text-sm font-semibold text-[#1a3a5c]">Select department first</div>
                            <div class="text-xs text-gray-400 mt-1">Based on department + year</div>
                        </div>
                    </div>
                </div>

                <div class="ui-card">
                    <div class="ui-card-body flex flex-col gap-2.5">
                        <button type="submit" id="submitBtn" class="btn-primary w-full justify-center">Complete Onboarding</button>
                        <a href="{{ route('employees.index') }}" class="btn-secondary w-full text-center">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function showRolePermissions(select) {
    const option = select.selectedOptions[0];
    const container = document.getElementById('role-permissions');
    if (!option || !option.dataset.permissions) {
        container.innerHTML = '<span class="text-sm text-gray-400 italic">Select a role to see permissions</span>';
        return;
    }
    try {
        const perms = JSON.parse(option.dataset.permissions);
        if (!perms || perms.length === 0) { container.innerHTML = '<span class="text-sm text-gray-400 italic">No permissions assigned</span>'; return; }
        container.innerHTML = perms.map(p => `<span style="display:inline-block;background:#f1f5f9;color:#475569;font-size:11px;padding:2px 8px;border-radius:9999px;margin:2px;">${p.replace(/_/g,' ')}</span>`).join('');
    } catch(e) { container.innerHTML = '<span class="text-sm text-gray-400 italic">Could not load permissions</span>'; }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('employeeForm').addEventListener('submit', function(e) {
        const required = ['first_name','last_name','email','password','password_confirmation','department_id','role_id','hire_date'];
        const missing = required.filter(f => { const el = document.querySelector(`[name="${f}"]`); return !el || !el.value.trim(); });
        if (missing.length > 0) { e.preventDefault(); alert('Missing required fields: ' + missing.join(', ')); return; }
        if (document.querySelector('[name="password"]').value !== document.querySelector('[name="password_confirmation"]').value) {
            e.preventDefault(); alert('Passwords do not match!'); return;
        }
        const btn = document.getElementById('submitBtn');
        btn.textContent = 'Creating Employee...';
        btn.disabled = true;
    });
});
</script>
@endsection
'@

# ─── edit.blade.php ──────────────────────────────────────────────────────────
$edit = @'
@extends('layouts.app')

@section('content')
<div>
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="page-title">Edit {{ $employee->first_name }} {{ $employee->last_name }}</h2>
            <p class="page-subtitle mt-1">Update employee information and permissions</p>
        </div>
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
'@

[IO.File]::WriteAllText("$base\show.blade.php",   $show,   $utf8NoBom)
[IO.File]::WriteAllText("$base\create.blade.php", $create, $utf8NoBom)
[IO.File]::WriteAllText("$base\edit.blade.php",   $edit,   $utf8NoBom)

Write-Host "show:   $((Get-Content "$base\show.blade.php").Count) lines"
Write-Host "create: $((Get-Content "$base\create.blade.php").Count) lines"
Write-Host "edit:   $((Get-Content "$base\edit.blade.php").Count) lines"
