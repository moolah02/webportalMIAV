{{--
==============================================
EMPLOYEE ONBOARDING FORM (For Your System)
File: resources/views/employees/create.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">👥 Employee Onboarding</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Add a new employee to the company</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn">← Back to Employees</a>
    </div>

    <form action="{{ route('employees.store') }}" method="POST" id="employeeForm" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Main Form -->
            <div>
                <!-- Personal Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">👤 Personal Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">First Name *</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                   placeholder="John"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('first_name')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Last Name *</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                   placeholder="Smith"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('last_name')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="john.smith@company.com"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('email')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   placeholder="+1 (555) 123-4567"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('phone')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Password *</label>
                            <input type="password" name="password" required
                                   placeholder="Secure password"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('password')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Confirm Password *</label>
                            <input type="password" name="password_confirmation" required
                                   placeholder="Confirm password"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">🏢 Employment Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Department *</label>
                            <select name="department_id" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Primary Role *</label>
                            <select name="role_id" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;" onchange="showRolePermissions(this.value)">
                                <option value="">Select Primary Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" data-permissions="{{ json_encode($role->permissions) }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 10px; font-weight: 500;">Additional Roles (Optional)</label>
                            <div style="border: 2px solid #ddd; border-radius: 8px; padding: 15px; background: #F8F9FA;">
                                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Select additional roles for this employee. The primary role is required.</p>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                                    @foreach($roles as $role)
                                        <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: white; border-radius: 6px; cursor: pointer; border: 1px solid #E0E0E0; transition: all 0.2s;"
                                               onmouseover="this.style.borderColor='#1976D2'; this.style.background='#F0F7FF';"
                                               onmouseout="this.style.borderColor='#E0E0E0'; this.style.background='white';">
                                            <input type="checkbox"
                                                   name="additional_roles[]"
                                                   value="{{ $role->id }}"
                                                   {{ is_array(old('additional_roles')) && in_array($role->id, old('additional_roles')) ? 'checked' : '' }}
                                                   style="cursor: pointer; width: 16px; height: 16px;">
                                            <span style="font-size: 14px; color: #333;">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @error('additional_roles')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Hire Date *</label>
                            <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('hire_date')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Manager</label>
                            <select name="manager_id" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Manager (Optional)</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->full_name }} - {{ $manager->role->name ?? 'No Role' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Time Zone</label>
                            <select name="time_zone" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="UTC" {{ old('time_zone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ old('time_zone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="America/Chicago" {{ old('time_zone') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                <option value="America/Denver" {{ old('time_zone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                <option value="America/Los_Angeles" {{ old('time_zone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                <option value="Europe/London" {{ old('time_zone') == 'Europe/London' ? 'selected' : '' }}>London</option>
                                <option value="Europe/Berlin" {{ old('time_zone') == 'Europe/Berlin' ? 'selected' : '' }}>Berlin</option>
                                <option value="Asia/Tokyo" {{ old('time_zone') == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Language</label>
                            <select name="language" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>French</option>
                                <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>German</option>
                                <option value="ja" {{ old('language') == 'ja' ? 'selected' : '' }}>Japanese</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="content-card">
                    <h4 style="margin-block-end: 20px; color: #333;">📝 Additional Information</h4>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Position/Title</label>
                        <input type="text" name="position" value="{{ old('position') }}"
                               placeholder="e.g., Senior Developer, Account Manager"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Annual Salary</label>
                        <input type="number" name="salary" value="{{ old('salary') }}" step="0.01" min="0"
                               placeholder="50000.00"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Address</label>
                        <textarea name="address" rows="2" placeholder="123 Main Street, Apt 4B"
                                  style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('address') }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">City</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                   placeholder="New York"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">State</label>
                            <input type="text" name="state" value="{{ old('state') }}"
                                   placeholder="NY"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Country</label>
                            <input type="text" name="country" value="{{ old('country') }}"
                                   placeholder="United States"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                   placeholder="10001"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                   placeholder="Jane Smith"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Emergency Contact Phone</label>
                            <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                   placeholder="+1 (555) 987-6543"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Relationship</label>
                            <select name="emergency_contact_relationship" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Relationship</option>
                                <option value="spouse" {{ old('emergency_contact_relationship') == 'spouse' ? 'selected' : '' }}>Spouse</option>
                                <option value="parent" {{ old('emergency_contact_relationship') == 'parent' ? 'selected' : '' }}>Parent</option>
                                <option value="sibling" {{ old('emergency_contact_relationship') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                <option value="child" {{ old('emergency_contact_relationship') == 'child' ? 'selected' : '' }}>Child</option>
                                <option value="friend" {{ old('emergency_contact_relationship') == 'friend' ? 'selected' : '' }}>Friend</option>
                                <option value="other" {{ old('emergency_contact_relationship') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Skills / Certifications</label>
                            <textarea name="skills" rows="2" placeholder="E.g., POS Terminal Installation, Network Configuration, CompTIA A+..."
                                      style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('skills') }}</textarea>
                            <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Comma-separated list of skills</div>
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Work Location / Office</label>
                            <input type="text" name="work_location" value="{{ old('work_location') }}"
                                   placeholder="Head Office, Remote, Regional Office, etc."
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Primary work location</div>
                        </div>
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Profile Photo</label>
                        <input type="file" name="avatar" accept="image/*"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Upload a profile picture (optional)</div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Any additional notes about this employee..."
                                  style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Settings Sidebar -->
            <div>
                <!-- Employee Status -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">⚙️ Employee Status</h4>

                    <div style="margin-block-end: 15px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Status *</label>
                        <select name="status" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending Onboarding</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Employee's current status</div>
                    </div>

                    <div style="margin-block-end: 15px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="two_factor_enabled" value="1" {{ old('two_factor_enabled') ? 'checked' : '' }}>
                            <span>Enable Two-Factor Authentication</span>
                        </label>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Require 2FA for login</div>
                    </div>
                </div>

                <!-- Role Permissions Preview -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">🔑 Role Permissions</h4>

                    <div id="role-permissions" style="font-size: 14px; color: #666;">
                        Select a role to see permissions
                    </div>
                </div>

                <!-- Employee Number Preview -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">🔖 Employee Number</h4>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; text-align: center;">
                        <div style="font-size: 12px; color: #666; margin-block-end: 5px;">Will be auto-generated</div>
                        <div id="employee-number-preview" style="font-weight: bold; color: #2196f3;">Select department first</div>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Based on department + year</div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="content-card">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="inline-size: 100%; padding: 15px;">
                            🎉 Complete Onboarding
                        </button>
                        <a href="{{ route('employees.index') }}" class="btn" style="inline-size: 100%; text-align: center;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn {
    padding: 8px 16px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
}

.btn-primary {
    background: #2196f3;
    color: white;
    border-color: #2196f3;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}
</style>

<script>
// Enhanced debugging
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('employeeForm');

    console.log('Form loaded:', form);
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);

    form.addEventListener('submit', function(e) {
        console.log('Form submission started');

        // Check required fields
        const requiredFields = ['first_name', 'last_name', 'email', 'password', 'password_confirmation', 'department_id', 'role_id', 'hire_date'];
        const missingFields = [];

        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field || !field.value.trim()) {
                missingFields.push(fieldName);
            }
        });

        if (missingFields.length > 0) {
            e.preventDefault();
            alert('Missing required fields: ' + missingFields.join(', '));
            console.log('Missing fields:', missingFields);
            return false;
        }

        // Check password confirmation
        const password = document.querySelector('[name="password"]').value;
        const passwordConfirm = document.querySelector('[name="password_confirmation"]').value;

        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }

        console.log('All validations passed, submitting form...');

        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '⏳ Creating Employee...';
        submitBtn.disabled = true;

        // Log form data
        const formData = new FormData(form);
        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            if (key !== 'password' && key !== 'password_confirmation') {
                console.log(key + ':', value);
            }
        }
    });
});
</script>
@endsection
