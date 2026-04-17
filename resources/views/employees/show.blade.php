@extends('layouts.app')
@section('title', 'Employee Details')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
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