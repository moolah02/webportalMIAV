@extends('layouts.app')
@section('title', 'Add Employee')

@section('header-actions')
<a href="{{ route('employees.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Employees</a>
@endsection

@section('content')
@include('employees.partials.form-styles')

@if($errors->any())
<div class="alert-danger ef-alert">
    <strong>Please fix the following errors:</strong>
    <ul>
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form action="{{ route('employees.store') }}" method="POST" id="employeeForm" enctype="multipart/form-data">
    @csrf
    <div class="ef-layout">
        <div class="ef-col">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Personal Information</h3></div>
                <div class="ef-grid">
                    <div>
                        <label class="ui-label">First Name <span class="ef-req">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="John" class="ui-input">
                        @error('first_name')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Last Name <span class="ef-req">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Smith" class="ui-input">
                        @error('last_name')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Email Address <span class="ef-req">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="john.smith@company.com" class="ui-input">
                        @error('email')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 123-4567" class="ui-input">
                        @error('phone')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Password <span class="ef-req">*</span></label>
                        <input type="password" name="password" required placeholder="Secure password" class="ui-input">
                        @error('password')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Confirm Password <span class="ef-req">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="Confirm password" class="ui-input">
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Employment Information</h3></div>
                <div class="ef-grid">
                    <div>
                        <label class="ui-label">Department <span class="ef-req">*</span></label>
                        <select name="department_id" required class="ui-select">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Primary Role <span class="ef-req">*</span></label>
                        <select name="role_id" required class="ui-select" onchange="showRolePermissions(this)">
                            <option value="">Select Primary Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" data-permissions="{{ json_encode($role->permissions) }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Hire Date <span class="ef-req">*</span></label>
                        <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required class="ui-input">
                        @error('hire_date')<div class="ef-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Manager</label>
                        <select name="manager_id" class="ui-select">
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
                        <select name="time_zone" class="ui-select">
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
                        <select name="language" class="ui-select">
                            <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>French</option>
                            <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>German</option>
                            <option value="ja" {{ old('language') == 'ja' ? 'selected' : '' }}>Japanese</option>
                        </select>
                    </div>
                    <div class="ef-full">
                        <label class="ui-label">Additional Roles <span class="ef-opt">(Optional)</span></label>
                        <div class="ef-roles">
                            <div class="ef-roles-note">Select additional roles to supplement the primary role.</div>
                            <div class="ef-roles-grid" style="margin:0 -1px -1px 0">
                                @foreach($roles as $role)
                                <label class="ef-role">
                                    <input type="checkbox" name="additional_roles[]" value="{{ $role->id }}"
                                           {{ is_array(old('additional_roles')) && in_array($role->id, old('additional_roles')) ? 'checked' : '' }}>
                                    <span>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Additional Information</h3></div>
                <div class="ef-grid">
                    <div>
                        <label class="ui-label">Position / Title</label>
                        <input type="text" name="position" value="{{ old('position') }}" placeholder="e.g., Senior Developer" class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Annual Salary</label>
                        <input type="number" name="salary" value="{{ old('salary') }}" step="0.01" min="0" placeholder="50000.00" class="ui-input">
                    </div>
                    <div class="ef-full">
                        <label class="ui-label">Address</label>
                        <textarea name="address" rows="2" placeholder="123 Main Street" class="ui-input">{{ old('address') }}</textarea>
                    </div>
                </div>
                <div class="ef-grid ef-grid-4" style="padding-top:0">
                    <div><label class="ui-label">City</label><input type="text" name="city" value="{{ old('city') }}" placeholder="New York" class="ui-input"></div>
                    <div><label class="ui-label">State</label><input type="text" name="state" value="{{ old('state') }}" placeholder="NY" class="ui-input"></div>
                    <div><label class="ui-label">Country</label><input type="text" name="country" value="{{ old('country') }}" placeholder="USA" class="ui-input"></div>
                    <div><label class="ui-label">Postal Code</label><input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="10001" class="ui-input"></div>
                </div>
                <div class="ef-grid ef-grid-3" style="padding-top:0">
                    <div>
                        <label class="ui-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Jane Smith" class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="+1 (555) 987-6543" class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Relationship</label>
                        <select name="emergency_contact_relationship" class="ui-select">
                            <option value="">Select Relationship</option>
                            @foreach(['spouse','parent','sibling','child','friend','other'] as $rel)
                            <option value="{{ $rel }}" {{ old('emergency_contact_relationship') == $rel ? 'selected' : '' }}>{{ ucfirst($rel) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ef-grid" style="padding-top:0">
                    <div>
                        <label class="ui-label">Skills / Certifications</label>
                        <textarea name="skills" rows="2" placeholder="CompTIA A+, Network Config..." class="ui-input">{{ old('skills') }}</textarea>
                        <div class="ef-hint">Comma-separated list</div>
                    </div>
                    <div>
                        <label class="ui-label">Work Location</label>
                        <input type="text" name="work_location" value="{{ old('work_location') }}" placeholder="Head Office, Remote..." class="ui-input">
                    </div>
                    <div class="ef-full">
                        <label class="ui-label">Profile Photo <span class="ef-opt">(Optional)</span></label>
                        <input type="file" name="avatar" accept="image/*" class="ui-input" style="padding:6px">
                    </div>
                    <div class="ef-full">
                        <label class="ui-label">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Any additional notes..." class="ui-input">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="ef-col">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Employee Status</h3></div>
                <div class="ef-body" style="display:flex;flex-direction:column;gap:12px">
                    <div>
                        <label class="ui-label">Status <span class="ef-req">*</span></label>
                        <select name="status" required class="ui-select">
                            <option value="active"   {{ old('status', 'active') == 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="pending"  {{ old('status') == 'pending'  ? 'selected' : '' }}>Pending Onboarding</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <label class="ef-check">
                        <input type="checkbox" name="two_factor_enabled" value="1" {{ old('two_factor_enabled') ? 'checked' : '' }}>
                        <span>Enable Two-Factor Auth</span>
                    </label>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Role Permissions</h3></div>
                <div class="ef-body">
                    <div id="role-permissions" class="ef-muted">Select a role to see permissions</div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Employee Number</h3></div>
                <div class="ef-body">
                    <div class="ef-code">
                        <div class="ef-hint" style="margin:0">Will be auto-generated</div>
                        <div id="employee-number-preview" class="ef-code-v">Select department first</div>
                        <div class="ef-hint" style="margin:0">Based on department + year</div>
                    </div>
                </div>
                <div class="ui-card-footer ef-actions">
                    <a href="{{ route('employees.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" id="submitBtn" class="btn-primary">Complete Onboarding</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function showRolePermissions(select) {
    const option = select.selectedOptions[0];
    const container = document.getElementById('role-permissions');
    if (!option || !option.dataset.permissions) {
        container.innerHTML = '<span class="ef-muted">Select a role to see permissions</span>';
        return;
    }
    try {
        const perms = JSON.parse(option.dataset.permissions);
        if (!perms || perms.length === 0) { container.innerHTML = '<span class="ef-muted">No permissions assigned</span>'; return; }
        container.innerHTML = '<div class="ef-chips">' + perms.map(p => `<span class="ef-chip">${String(p).replace(/_/g, ' ')}</span>`).join('') + '</div>';
    } catch (e) { container.innerHTML = '<span class="ef-muted">Could not load permissions</span>'; }
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
