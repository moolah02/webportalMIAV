import os

BASE = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'

# ================================================
# employee/profile.blade.php
# ================================================
profile = r"""@extends('layouts.app')
@section('title', 'My Profile')

@section('header-actions')
<a href="{{ route('employee.edit-profile') }}" class="btn-primary btn-sm">&#x270F;&#xFE0F; Edit Profile</a>
@endsection

@section('content')

{{-- Flash --}}
@if(session('success'))
<div class="flash-success"><span>&#x2705;</span> {{ session('success') }}</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-xl flex-shrink-0">&#x1F4CB;</div>
        <div><div class="stat-number">{{ $stats['total_asset_requests'] }}</div><div class="stat-label">Total Requests</div></div>
    </div>
    <div class="stat-card">
        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-xl flex-shrink-0">&#x23F1;&#xFE0F;</div>
        <div><div class="stat-number">{{ $stats['pending_requests'] }}</div><div class="stat-label">Pending</div></div>
    </div>
    <div class="stat-card">
        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-xl flex-shrink-0">&#x1F4E6;</div>
        <div><div class="stat-number">{{ $stats['assigned_assets_count'] }}</div><div class="stat-label">Assigned Assets</div></div>
    </div>
    <div class="stat-card">
        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-xl flex-shrink-0">&#x1F465;</div>
        <div><div class="stat-number">{{ $stats['subordinates_count'] }}</div><div class="stat-label">Team Members</div></div>
    </div>
</div>

{{-- Main 2-col grid --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Personal Information --}}
    <div class="ui-card">
        <div class="ui-card-header">
            <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F464; Personal Information</h3>
        </div>
        <div class="ui-card-body">
            <div class="flex items-center gap-4 mb-5 pb-4 border-b border-gray-100">
                <div class="w-14 h-14 rounded-full bg-[#1a3a5c] flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-base font-bold text-gray-900">{{ $employee->full_name }}</div>
                    <div class="text-sm text-gray-500">{{ $employee->employee_number }} &middot; {{ $employee->department->name ?? 'No Department' }}</div>
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <span class="badge {{ $employee->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($employee->status) }}</span>
                        @foreach($employee->roles as $role)
                        <span class="badge badge-blue">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-50 text-sm mb-5">
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Email</span><span class="text-gray-800">{{ $employee->email }}</span></div>
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Phone</span><span class="text-gray-800">{{ $employee->phone ?: 'Not provided' }}</span></div>
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Hire Date</span><span class="text-gray-800">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'Not specified' }}</span></div>
                @if($employee->position)
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Position</span><span class="text-gray-800">{{ $employee->position }}</span></div>
                @endif
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Time Zone</span><span class="text-gray-800">{{ $employee->time_zone }}</span></div>
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Language</span><span class="text-gray-800">{{ strtoupper($employee->language) }}</span></div>
            </div>

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">&#x1F3E2; Organisation</div>
            <div class="divide-y divide-gray-50 text-sm mb-5">
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Department</span><span class="text-gray-800">{{ $employee->department->name ?? 'Not assigned' }}</span></div>
                <div class="flex justify-between items-start py-2">
                    <span class="font-medium text-gray-500">Roles</span>
                    <span class="flex flex-wrap gap-1 justify-end">
                        @forelse($employee->roles as $role)
                        <span class="badge badge-blue">{{ $role->name }}</span>
                        @empty
                        <span class="text-gray-400">Not assigned</span>
                        @endforelse
                    </span>
                </div>
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Manager</span><span class="text-gray-800">{{ $employee->manager->full_name ?? 'No manager assigned' }}</span></div>
                <div class="flex justify-between py-2">
                    <span class="font-medium text-gray-500">2FA Status</span>
                    <span class="badge {{ $employee->two_factor_enabled ? 'badge-green' : 'badge-yellow' }}">{{ $employee->two_factor_enabled ? 'Enabled' : 'Disabled' }}</span>
                </div>
                <div class="flex justify-between py-2"><span class="font-medium text-gray-500">Last Login</span><span class="text-gray-800">{{ $employee->last_login_at ? $employee->last_login_at->diffForHumans() : 'Never' }}</span></div>
            </div>

            <button type="button" onclick="document.getElementById('changePwModal').classList.remove('hidden')" class="btn-secondary btn-sm">&#x1F510; Change Password</button>
        </div>
    </div>

    {{-- Assets & Requests --}}
    <div class="ui-card">
        <div class="ui-card-header">
            <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CB; Assets &amp; Requests</h3>
        </div>
        <div class="ui-card-body">

            @if($employee->currentAssetAssignments->count() > 0)
            <div class="mb-5">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">&#x1F5A5;&#xFE0F; Assigned Assets ({{ $employee->currentAssetAssignments->count() }})</div>
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    @foreach($employee->currentAssetAssignments->take(8) as $assignment)
                    <div class="flex items-start justify-between p-2.5 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-800">{{ $assignment->asset->name }}</div>
                            <div class="text-xs text-gray-500">@if($assignment->asset->brand){{ $assignment->asset->brand }} &middot; @endif{{ $assignment->asset->category }}@if($assignment->asset->sku) &middot; SKU: {{ $assignment->asset->sku }}@endif</div>
                        </div>
                        <div class="flex flex-col gap-1 items-end ml-2 flex-shrink-0">
                            <span class="badge {{ $assignment->condition_when_assigned === 'new' ? 'badge-green' : ($assignment->condition_when_assigned === 'good' ? 'badge-blue' : 'badge-yellow') }}">{{ ucfirst($assignment->condition_when_assigned) }}</span>
                            @if($assignment->isOverdue())<span class="badge badge-red">{{ $assignment->days_overdue }}d overdue</span>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($employee->currentAssetAssignments->count() > 8)<p class="text-xs text-gray-400 text-center mt-1 italic">And {{ $employee->currentAssetAssignments->count() - 8 }} more&hellip;</p>@endif
            </div>
            @endif

            @if($employee->assetRequests->count() > 0)
            <div class="mb-5">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">&#x1F4DD; Recent Requests ({{ $employee->assetRequests->count() }})</div>
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    @foreach($employee->assetRequests->take(8) as $request)
                    <div class="flex items-start justify-between p-2.5 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-800">{{ $request->request_number }}</div>
                            <div class="text-xs text-gray-500">@if($request->business_justification){{ Str::limit($request->business_justification, 40) }}@endif@if($request->total_estimated_cost) &middot; ${{ number_format($request->total_estimated_cost, 0) }}@endif</div>
                        </div>
                        <div class="flex flex-col gap-1 items-end ml-2 flex-shrink-0">
                            @php $rBadge = match($request->status) { 'approved' => 'badge-green', 'rejected' => 'badge-red', 'pending' => 'badge-yellow', 'fulfilled' => 'badge-blue', default => 'badge-gray' }; @endphp
                            <span class="badge {{ $rBadge }}">{{ ucfirst($request->status) }}</span>
                            @if(in_array($request->priority, ['urgent','high']))<span class="badge badge-red">{{ ucfirst($request->priority) }}</span>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($employee->subordinates->count() > 0)
            <div class="mb-5">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">&#x1F465; Team Members ({{ $employee->subordinates->count() }})</div>
                <div class="space-y-2">
                    @foreach($employee->subordinates->take(6) as $sub)
                    <div class="flex items-center gap-3 p-2.5 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">{{ strtoupper(substr($sub->first_name,0,1).substr($sub->last_name,0,1)) }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-800">{{ $sub->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $sub->role->name ?? 'No role' }}</div>
                        </div>
                        @if($sub->isFieldTechnician())<span class="badge badge-yellow">&#x1F527;</span>@endif
                    </div>
                    @endforeach
                    @if($employee->subordinates->count() > 6)<p class="text-xs text-gray-400 text-center italic">And {{ $employee->subordinates->count() - 6 }} more&hellip;</p>@endif
                </div>
            </div>
            @endif

            @if($employee->currentAssetAssignments->count() === 0 && $employee->assetRequests->count() === 0 && $employee->subordinates->count() === 0)
            <div class="empty-state"><div class="empty-state-icon">&#x1F4CB;</div><div class="empty-state-msg">No activity yet. Assets, requests and team info will appear here.</div></div>
            @endif
        </div>
    </div>
</div>

{{-- Change Password Modal --}}
<div id="changePwModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="ui-card w-full max-w-md">
        <div class="ui-card-header" style="background:#1a3a5c;">
            <h3 class="text-sm font-semibold text-white m-0">&#x1F510; Change Password</h3>
            <button onclick="document.getElementById('changePwModal').classList.add('hidden')" class="text-white/70 hover:text-white text-xl leading-none border-0 bg-transparent cursor-pointer">&times;</button>
        </div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('employee.update-password') }}">
                @csrf @method('PATCH')
                <div class="mb-4">
                    <label class="ui-label" for="cp_current">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" id="cp_current" class="ui-input" required>
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="ui-label" for="cp_new">New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="cp_new" class="ui-input" required minlength="8">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-5">
                    <label class="ui-label" for="cp_confirm">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="cp_confirm" class="ui-input" required minlength="8">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">&#x1F510; Update Password</button>
                    <button type="button" onclick="document.getElementById('changePwModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
"""

# ================================================
# employee/edit-profile.blade.php
# ================================================
edit_profile = r"""@extends('layouts.app')
@section('title', 'Edit Profile')

@section('header-actions')
<a href="{{ route('employee.profile') }}" class="btn-secondary btn-sm">&larr; Back to Profile</a>
@endsection

@section('content')

{{-- Errors --}}
@if($errors->any())
<div class="flash-error mb-5">
    <div><span class="font-semibold">&#x26A0;&#xFE0F; Please fix the following errors:</span>
    <ul class="list-disc pl-5 mt-1 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
</div>
@endif

<div class="max-w-2xl mx-auto">
    <div class="ui-card">
        <div class="ui-card-header">
            <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F464; Profile Information</h3>
        </div>
        <div class="ui-card-body">
            {{-- Profile summary --}}
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                <div class="w-14 h-14 rounded-full bg-[#1a3a5c] flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-base font-bold text-gray-900">{{ $employee->full_name }}</div>
                    <div class="text-sm text-gray-500">{{ $employee->employee_number }} &middot; {{ $employee->department->name ?? 'No Department' }}</div>
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <span class="badge {{ $employee->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($employee->status) }}</span>
                        @if($employee->role)<span class="badge badge-blue">{{ $employee->role->name }}</span>@endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('employee.update-profile') }}">
                @csrf @method('PATCH')

                {{-- Personal Information --}}
                <div class="mb-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">&#x1F4DD; Personal Information</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label" for="first_name">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" id="first_name" class="ui-input @error('first_name') border-red-400 @enderror" value="{{ old('first_name', $employee->first_name) }}" required>
                            @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label" for="last_name">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" id="last_name" class="ui-input @error('last_name') border-red-400 @enderror" value="{{ old('last_name', $employee->last_name) }}" required>
                            @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Email Address</label>
                            <input type="email" class="ui-input bg-gray-50 text-gray-400 cursor-not-allowed" value="{{ $employee->email }}" disabled>
                            <p class="text-xs text-gray-400 mt-1">Contact IT to change your email</p>
                        </div>
                        <div>
                            <label class="ui-label" for="phone">Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="ui-input @error('phone') border-red-400 @enderror" value="{{ old('phone', $employee->phone) }}">
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- System Preferences --}}
                <div class="mb-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">&#x2699;&#xFE0F; System Preferences</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label" for="time_zone">Time Zone <span class="text-red-500">*</span></label>
                            <select name="time_zone" id="time_zone" class="ui-select @error('time_zone') border-red-400 @enderror" required>
                                <option value="">Select Time Zone</option>
                                @php $timezones = ['UTC'=>'UTC (Coordinated Universal Time)','America/New_York'=>'Eastern Time (US & Canada)','America/Chicago'=>'Central Time (US & Canada)','America/Denver'=>'Mountain Time (US & Canada)','America/Los_Angeles'=>'Pacific Time (US & Canada)','Europe/London'=>'London, Edinburgh, Dublin','Europe/Paris'=>'Paris, Berlin, Madrid','Africa/Harare'=>'Harare, Zimbabwe','Africa/Johannesburg'=>'Johannesburg, South Africa','Africa/Cairo'=>'Cairo, Egypt','Asia/Tokyo'=>'Tokyo, Osaka, Sapporo','Asia/Shanghai'=>'Beijing, Shanghai','Australia/Sydney'=>'Sydney, Melbourne']; @endphp
                                @foreach($timezones as $value => $label)
                                <option value="{{ $value }}" {{ old('time_zone', $employee->time_zone) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('time_zone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label" for="language">Language <span class="text-red-500">*</span></label>
                            <select name="language" id="language" class="ui-select @error('language') border-red-400 @enderror" required>
                                @php $languages = ['en'=>'English','es'=>'Spanish','fr'=>'French','de'=>'German','it'=>'Italian','pt'=>'Portuguese','zh'=>'Chinese','ja'=>'Japanese','ko'=>'Korean','ar'=>'Arabic']; @endphp
                                @foreach($languages as $value => $label)
                                <option value="{{ $value }}" {{ old('language', $employee->language) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('language')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Read-only system info --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">&#x1F4CB; System Information <span class="normal-case font-normal">(managed by administrator)</span></div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                        <div><div class="text-xs text-gray-400 mb-0.5">Employee #</div><div class="font-medium text-gray-700">{{ $employee->employee_number }}</div></div>
                        <div><div class="text-xs text-gray-400 mb-0.5">Hire Date</div><div class="font-medium text-gray-700">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'Not specified' }}</div></div>
                        <div><div class="text-xs text-gray-400 mb-0.5">Department</div><div class="font-medium text-gray-700">{{ $employee->department->name ?? 'Not assigned' }}</div></div>
                        <div><div class="text-xs text-gray-400 mb-0.5">Role</div><div class="font-medium text-gray-700">{{ $employee->role->name ?? 'Not assigned' }}</div></div>
                        <div><div class="text-xs text-gray-400 mb-0.5">Status</div><span class="badge {{ $employee->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($employee->status) }}</span></div>
                        <div><div class="text-xs text-gray-400 mb-0.5">Last Login</div><div class="font-medium text-gray-700">{{ $employee->last_login_at ? $employee->last_login_at->diffForHumans() : 'Never' }}</div></div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">&#x1F4BE; Save Changes</button>
                    <a href="{{ route('employee.profile') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
"""

files = {
    r'employee\profile.blade.php': profile,
    r'employee\edit-profile.blade.php': edit_profile,
}

for rel, content in files.items():
    path = os.path.join(BASE, rel)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content.lstrip('\n'))
    print(f'Written: {rel}')
