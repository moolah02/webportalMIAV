{{-- 
==============================================
EMPLOYEE EDIT VIEW
File: resources/views/employees/edit.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="{{ route('employees.show', $employee) }}" class="btn" style="text-decoration: none;">
                    ← Back to Profile
                </a>
                <div>
                    <h2 style="margin: 0; color: #333;">Edit {{ $employee->first_name }} {{ $employee->last_name }}</h2>
                    <p style="color: #666; margin: 5px 0 0 0;">Update employee information and permissions</p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('employees.update', $employee) }}">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px;">
            <!-- Main Form -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- Personal Information -->
                <div class="content-card">
                    <h4 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 8px;">
                        👤 Personal Information
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label for="first_name" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">First Name *</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;" required>
                            @error('first_name')
                                <div style="color: #d32f2f; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="last_name" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;" required>
                            @error('last_name')
                                <div style="color: #d32f2f; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="email" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;" required>
                            @error('email')
                                <div style="color: #d32f2f; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="phone" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;" 
                                   placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <div style="color: #d32f2f; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Role & Department -->
                <div class="content-card">
                    <h4 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 8px;">
                        🔑 Role & Department
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label for="role_id" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Role *</label>
                            <select id="role_id" name="role_id" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;" required>
                                <option value="">Select Role</option>
                                @php
                                    try {
                                        $roles = \App\Models\Role::all();
                                    } catch (\Exception $e) {
                                        $roles = collect();
                                    }
                                @endphp
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        @if(is_array($role->permissions) && in_array('all', $role->permissions))
                                            (Super Admin)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div style="color: #d32f2f; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="department_id" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Department</label>
                            <select id="department_id" name="department_id" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
                                <option value="">Select Department</option>
                                @php
                                    try {
                                        $departments = \App\Models\Department::all();
                                    } catch (\Exception $e) {
                                        $departments = collect([
                                            (object)['id' => 1, 'name' => 'IT'],
                                            (object)['id' => 2, 'name' => 'Operations'],
                                            (object)['id' => 3, 'name' => 'Management'],
                                        ]);
                                    }
                                @endphp
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div style="color: #d32f2f; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="status" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Status *</label>
                            <select id="status" name="status" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;" required>
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="pending" {{ old('status', $employee->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                            @error('status')
                                <div style="color: #d32f2f; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="hire_date" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Hire Date</label>
                            <input type="date" id="hire_date" name="hire_date" 
                                   value="{{ old('hire_date', $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '') }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
                            @error('hire_date')
                                <div style="color: #d32f2f; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Preferences -->
                <div class="content-card">
                    <h4 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 8px;">
                        ⚙️ Preferences
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="time_zone" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Time Zone</label>
                            <select id="time_zone" name="time_zone" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
                                <option value="UTC" {{ old('time_zone', $employee->time_zone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ old('time_zone', $employee->time_zone) == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="America/Chicago" {{ old('time_zone', $employee->time_zone) == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                <option value="America/Denver" {{ old('time_zone', $employee->time_zone) == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                <option value="America/Los_Angeles" {{ old('time_zone', $employee->time_zone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="language" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Language</label>
                            <select id="language" name="language" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
                                <option value="en" {{ old('language', $employee->language) == 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ old('language', $employee->language) == 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ old('language', $employee->language) == 'fr' ? 'selected' : '' }}>French</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="two_factor_enabled" value="1" 
                                   {{ old('two_factor_enabled', $employee->two_factor_enabled) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px;">
                            <span style="font-weight: 500; color: #333;">Enable Two-Factor Authentication</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- Current Role Info -->
                <div class="content-card">
                    <h4 style="margin: 0 0 15px 0; color: #333;">Current Role</h4>
                    @php
                        try {
                            $currentRole = \App\Models\Role::find($employee->role_id);
                        } catch (\Exception $e) {
                            $currentRole = null;
                        }
                    @endphp
                    
                    @if($currentRole)
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                    {{ ucfirst(str_replace('_', ' ', $currentRole->name)) }}
                                </span>
                                @if(is_array($currentRole->permissions) && in_array('all', $currentRole->permissions))
                                    <span style="background: #ffebee; color: #d32f2f; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                        Super Admin
                                    </span>
                                @endif
                            </div>
                            <div style="font-size: 12px; color: #666;">
                                Permissions: {{ is_array($currentRole->permissions) ? count($currentRole->permissions) : 0 }}
                            </div>
                        </div>
                    @else
                        <div style="background: #ffebee; color: #d32f2f; padding: 15px; border-radius: 8px; text-align: center;">
                            <strong>No Role Assigned</strong>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="content-card">
                    <h4 style="margin: 0 0 15px 0; color: #333;">Actions</h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                            ✅ Save Changes
                        </button>
                        
                        <a href="{{ route('employees.show', $employee) }}" class="btn" style="width: 100%; text-align: center; text-decoration: none;">
                            ❌ Cancel
                        </a>
                        
                        <button type="button" onclick="resetPassword()" class="btn" style="width: 100%; background: #ff9800; color: white; border-color: #ff9800;">
                            🔑 Reset Password
                        </button>
                    </div>
                </div>

                <!-- Employee Stats -->
                <div class="content-card">
                    <h4 style="margin: 0 0 15px 0; color: #333;">Employee Stats</h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                            <span style="font-size: 12px; color: #666;">Employee ID</span>
                            <span style="font-size: 14px; color: #333; font-weight: 500;">{{ $employee->employee_number ?? $employee->id }}</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                            <span style="font-size: 12px; color: #666;">Member Since</span>
                            <span style="font-size: 14px; color: #333; font-weight: 500;">{{ $employee->created_at->format('M Y') }}</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                            <span style="font-size: 12px; color: #666;">Last Updated</span>
                            <span style="font-size: 14px; color: #333; font-weight: 500;">{{ $employee->updated_at->diffForHumans() }}</span>
                        </div>
                        
                        @if($employee->last_login_at)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                            <span style="font-size: 12px; color: #666;">Last Login</span>
                            <span style="font-size: 14px; color: #333; font-weight: 500;">{{ \Carbon\Carbon::parse($employee->last_login_at)->diffForHumans() }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="content-card" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);">
                    <h4 style="margin: 0 0 15px 0; color: #333;">💡 Quick Tips</h4>
                    <div style="font-size: 14px; line-height: 1.5; color: #666;">
                        <p style="margin: 0 0 10px;">• <strong>Role changes</strong> take effect immediately</p>
                        <p style="margin: 0 0 10px;">• <strong>Email changes</strong> require verification</p>
                        <p style="margin: 0 0 10px;">• <strong>Status changes</strong> affect system access</p>
                        <p style="margin: 0;">• Use <strong>Reset Password</strong> for login issues</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.content-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn {
    padding: 12px 20px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
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

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #2196f3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

input[type="checkbox"] {
    accent-color: #2196f3;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function resetPassword() {
    if (confirm('Are you sure you want to reset this employee\'s password? They will receive an email with instructions.')) {
        // For now, just show an alert - you can implement actual password reset later
        alert('Password reset functionality will be implemented soon. For now, you can manually update their password in the database.');
    }
}

// Auto-save draft functionality (optional)
let formData = {};
const form = document.querySelector('form');

if (form) {
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            formData[this.name] = this.value;
            // You could save to localStorage or send to server here
        });
    });
}

// Warn about unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (Object.keys(formData).length > 0) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Clear form data when form is submitted
if (form) {
    form.addEventListener('submit', function() {
        formData = {};
    });
}
</script>
@endsection