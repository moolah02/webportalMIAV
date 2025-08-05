{{-- 
==============================================
EMPLOYEE SHOW VIEW
File: resources/views/employees/show.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="{{ route('employees.index') }}" class="btn" style="text-decoration: none;">
                    ← Back to Employees
                </a>
                <div>
                    <h2 style="margin: 0; color: #333;">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                    <p style="color: #666; margin: 5px 0 0 0;">Employee Profile</p>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">
                ✏️ Edit Profile
            </a>
            <button onclick="sendEmail('{{ $employee->email }}')" class="btn" style="background: #9c27b0; color: white; border-color: #9c27b0;">
                📧 Send Email
            </button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 30px;">
        <!-- Left Column - Profile Card -->
        <div class="profile-card">
            <!-- Avatar -->
            <div style="text-align: center; margin-block-end: 25px;">
                <div style="inline-size: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold; margin: 0 auto 15px;">
                    {{ substr($employee->first_name ?? 'N', 0, 1) }}{{ substr($employee->last_name ?? 'A', 0, 1) }}
                </div>
                <h3 style="margin: 0; color: #333;">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                <p style="color: #666; margin: 5px 0;">
                    @php
                        try {
                            $role = \App\Models\Role::find($employee->role_id);
                            echo $role ? ucfirst(str_replace('_', ' ', $role->name)) : 'No Role Assigned';
                        } catch (\Exception $e) {
                            echo 'No Role Assigned';
                        }
                    @endphp
                </p>
                <span class="status-badge status-{{ $employee->status ?? 'pending' }}">
                    {{ ucfirst($employee->status ?? 'Pending') }}
                </span>
            </div>

            <!-- Quick Info -->
            <div style="border-block-start: 1px solid #eee; padding-top: 20px;">
                <div class="info-item">
                    <strong>Employee ID:</strong>
                    <span>{{ $employee->employee_number ?? $employee->id }}</span>
                </div>
                <div class="info-item">
                    <strong>Email:</strong>
                    <a href="mailto:{{ $employee->email }}" style="color: #2196f3;">{{ $employee->email }}</a>
                </div>
                @if($employee->phone)
                <div class="info-item">
                    <strong>Phone:</strong>
                    <span>{{ $employee->phone }}</span>
                </div>
                @endif
                <div class="info-item">
                    <strong>Hire Date:</strong>
                    <span>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'Not set' }}</span>
                </div>
                @if($employee->last_login_at)
                <div class="info-item">
                    <strong>Last Login:</strong>
                    <span>{{ \Carbon\Carbon::parse($employee->last_login_at)->diffForHumans() }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Details -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Role & Permissions -->
            <div class="content-card">
                <h4 style="margin: 0 0 15px 0; color: #333; display: flex; align-items: center; gap: 8px;">
                    🔑 Role & Permissions
                </h4>
                @php
                    try {
                        $role = \App\Models\Role::find($employee->role_id);
                    } catch (\Exception $e) {
                        $role = null;
                    }
                @endphp
                
                @if($role)
                    <div style="margin-block-end: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-block-end: 10px;">
                            <span style="background: #e3f2fd; color: #1976d2; padding: 5px 12px; border-radius: 15px; font-size: 14px; font-weight: 500;">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </span>
                            @if(is_array($role->permissions) && in_array('all', $role->permissions))
                                <span style="background: #ffebee; color: #d32f2f; padding: 5px 12px; border-radius: 15px; font-size: 14px; font-weight: 500;">
                                    Super Admin
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 10px;">Permissions</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @if(is_array($role->permissions) && !empty($role->permissions))
                                @foreach($role->permissions as $permission)
                                    @php
                                        $colors = [
                                            'all' => ['bg' => '#ffebee', 'text' => '#d32f2f'],
                                            'manage_team' => ['bg' => '#f3e5f5', 'text' => '#7b1fa2'],
                                            'manage_assets' => ['bg' => '#e8f5e8', 'text' => '#388e3c'],
                                            'view_dashboard' => ['bg' => '#e3f2fd', 'text' => '#1976d2'],
                                            'view_clients' => ['bg' => '#fff3e0', 'text' => '#f57c00'],
                                            'view_jobs' => ['bg' => '#e0f2f1', 'text' => '#00796b'],
                                        ];
                                        $color = $colors[$permission] ?? ['bg' => '#f5f5f5', 'text' => '#666'];
                                        $displayName = ucwords(str_replace('_', ' ', $permission));
                                    @endphp
                                    <span style="background: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                        {{ $displayName }}
                                    </span>
                                @endforeach
                            @else
                                <span style="color: #999; font-style: italic;">No permissions assigned</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div style="background: #ffebee; color: #d32f2f; padding: 15px; border-radius: 8px; text-align: center;">
                        <strong>⚠️ No Role Assigned</strong>
                        <p style="margin: 5px 0 0; font-size: 14px;">This employee needs a role to access the system.</p>
                    </div>
                @endif
            </div>

            <!-- Department & Reporting -->
            <div class="content-card">
                <h4 style="margin: 0 0 15px 0; color: #333; display: flex; align-items: center; gap: 8px;">
                    🏢 Department & Reporting
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px;">Department</div>
                        <div style="font-size: 16px; color: #333;">
                            @php
                                try {
                                    $department = $employee->department->name ?? 'No Department';
                                } catch (\Exception $e) {
                                    $department = 'No Department';
                                }
                            @endphp
                            {{ $department }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px;">Reports To</div>
                        <div style="font-size: 16px; color: #333;">
                            @php
                                try {
                                    $manager = $employee->manager ? $employee->manager->first_name . ' ' . $employee->manager->last_name : 'No Manager';
                                } catch (\Exception $e) {
                                    $manager = 'No Manager';
                                }
                            @endphp
                            {{ $manager }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="content-card">
                <h4 style="margin: 0 0 15px 0; color: #333; display: flex; align-items: center; gap: 8px;">
                    📋 Additional Information
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px;">Time Zone</div>
                        <div style="font-size: 16px; color: #333;">{{ $employee->time_zone ?? 'UTC' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px;">Language</div>
                        <div style="font-size: 16px; color: #333;">{{ strtoupper($employee->language ?? 'EN') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px;">Two Factor Auth</div>
                        <div style="font-size: 16px; color: #333;">
                            @if($employee->two_factor_enabled)
                                <span style="color: #4caf50;">✅ Enabled</span>
                            @else
                                <span style="color: #ff9800;">❌ Disabled</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px;">Account Created</div>
                        <div style="font-size: 16px; color: #333;">{{ $employee->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    height: fit-content;
}

.content-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-block-end: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-block-end: none;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-inactive { background: #f5f5f5; color: #666; }

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
function sendEmail(email) {
    window.location.href = `mailto:${email}`;
}
</script>
@endsection