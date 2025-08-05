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

    <form action="{{ route('employees.store') }}" method="POST" id="employeeForm">
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
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Role *</label>
                            <select name="role_id" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;" onchange="showRolePermissions(this.value)">
                                <option value="">Select Role</option>
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
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
// Show role permissions when role is selected
function showRolePermissions(roleId) {
    const roleSelect = document.querySelector('select[name="role_id"]');
    const permissionsDiv = document.getElementById('role-permissions');
    
    if (!roleId) {
        permissionsDiv.innerHTML = 'Select a role to see permissions';
        return;
    }
    
    const selectedOption = roleSelect.querySelector(`option[value="${roleId}"]`);
    const permissions = JSON.parse(selectedOption.dataset.permissions || '[]');
    
    if (permissions.length === 0) {
        permissionsDiv.innerHTML = '<span style="color: #f44336;">No permissions assigned</span>';
        return;
    }
    
    let permissionList = permissions.map(permission => {
        const displayName = permission.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const color = permission === 'all' ? '#f44336' : '#4caf50';
        return `<span style="background: ${color}20; color: ${color}; padding: 2px 6px; border-radius: 8px; font-size: 11px; margin: 2px; display: inline-block;">${displayName}</span>`;
    }).join('');
    
    permissionsDiv.innerHTML = permissionList;
}

// Update employee number preview when department changes
document.querySelector('select[name="department_id"]').addEventListener('change', function() {
    const preview = document.getElementById('employee-number-preview');
    const departmentSelect = this;
    const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
    
    if (departmentSelect.value) {
        const departmentName = selectedOption.text;
        const prefix = departmentName.substring(0, 3).toUpperCase();
        const year = new Date().getFullYear().toString().slice(-2);
        preview.textContent = `${prefix}${year}XXXX`;
    } else {
        preview.textContent = 'Select department first';
    }
});

// Form validation and UX improvements
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('employeeForm');
    
    // Auto-generate preview on page load if department is selected
    const departmentSelect = document.querySelector('select[name="department_id"]');
    if (departmentSelect.value) {
        departmentSelect.dispatchEvent(new Event('change'));
    }
    
    // Auto-show permissions on page load if role is selected
    const roleSelect = document.querySelector('select[name="role_id"]');
    if (roleSelect.value) {
        showRolePermissions(roleSelect.value);
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const firstName = document.querySelector('input[name="first_name"]').value;
        const lastName = document.querySelector('input[name="last_name"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const departmentId = document.querySelector('select[name="department_id"]').value;
        const roleId = document.querySelector('select[name="role_id"]').value;
        
        if (!firstName || !lastName || !email || !departmentId || !roleId) {
            e.preventDefault();
            alert('Please fill in all required fields (marked with *)');
            return;
        }
        
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '⏳ Creating Employee...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection