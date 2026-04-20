@extends('layouts.app')
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
