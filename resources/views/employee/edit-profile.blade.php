@extends('layouts.app')
@section('title', 'Edit Profile')

@section('header-actions')
<a href="{{ route('employee.profile') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Profile</a>
@endsection

@push('styles')
<style>
    .ep { display: grid; gap: 24px; max-width: 1080px; }
    .ep .mv-i { width: 16px; height: 16px; }
    .ep-errors { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border: 1px solid; font-size: 13.5px; }
    .ep-errors .mv-i { margin-top: 2px; flex-shrink: 0; }
    .ep-errors ul { margin: 4px 0 0; padding-left: 18px; }

    .ep-id { display: flex; align-items: center; gap: 14px; }
    .ep-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--mv-accent-soft); color: var(--mv-accent-ink); display: grid; place-items: center; font-size: 15px; font-weight: 600; flex-shrink: 0; }
    .ep-name { font-size: 15px; font-weight: 600; color: var(--mv-ink); }
    .ep-line { font-size: 13px; color: var(--mv-muted); }
    .ep-line .mv-mono { color: var(--mv-ink-2); }
    .ep-chips { display: flex; gap: 6px; margin-left: auto; }

    .ep-section { display: grid; grid-template-columns: 240px minmax(0, 1fr); gap: 28px; padding-top: 24px; border-top: 1px solid var(--mv-line); }
    .ep-section h2 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
    .ep-section p.ep-desc { margin: 4px 0 0; font-size: 13px; line-height: 1.5; color: var(--mv-muted); }
    .ep-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 18px 20px; }
    .ep-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .ep .ui-input, .ep .ui-select { width: 100%; height: 36px; padding: 0 11px; font-size: 13.5px; }
    .ep .ui-input:disabled { background: var(--mv-surface-2) !important; color: var(--mv-muted); cursor: not-allowed; }
    .ep-hint { font-size: 12px; color: var(--mv-muted); margin-top: 4px; }
    .ep-err { font-size: 12px; color: var(--mv-crit); margin-top: 4px; }
    .ep .is-invalid { border-color: var(--mv-crit) !important; }
    .ep-req { color: var(--mv-crit); }

    .ep-facts { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1px; background: var(--mv-line); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .ep-facts > div { background: var(--mv-surface); padding: 12px 16px; }
    .ep-facts dt { font-size: 12px; color: var(--mv-muted); }
    .ep-facts dd { margin: 2px 0 0; font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }

    .ep-foot { display: flex; justify-content: flex-end; gap: 8px; padding-top: 20px; border-top: 1px solid var(--mv-line); }
    .ep-foot button { display: inline-flex; align-items: center; gap: 6px; }

    @media (max-width: 900px) { .ep-section { grid-template-columns: minmax(0, 1fr); gap: 12px; } .ep-grid, .ep-facts { grid-template-columns: minmax(0, 1fr); } }
</style>
@endpush

@section('content')
<div class="ep">

    @if($errors->any())
    <div class="alert alert-danger ep-errors">
        <svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg>
        <div><strong>Please fix the following errors:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    </div>
    @endif

    {{-- Who --}}
    <div class="ep-id">
        <div class="ep-avatar">{{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}</div>
        <div>
            <div class="ep-name">{{ $employee->full_name }}</div>
            <div class="ep-line"><span class="mv-mono">{{ $employee->employee_number }}</span> &middot; {{ $employee->department->name ?? 'No Department' }}</div>
        </div>
        <div class="ep-chips">
            <span class="badge {{ $employee->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($employee->status) }}</span>
            @if($employee->role)<span class="badge badge-blue">{{ $employee->role->name }}</span>@endif
        </div>
    </div>

    <form method="POST" action="{{ route('employee.update-profile') }}" style="display:grid;gap:24px;">
        @csrf @method('PATCH')

        {{-- Personal Information --}}
        <section class="ep-section">
            <div>
                <h2>Personal Information</h2>
                <p class="ep-desc">Your name and how colleagues can reach you.</p>
            </div>
            <div class="ep-card">
                <div class="ep-grid">
                    <div>
                        <label class="ui-label" for="first_name">First Name <span class="ep-req">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="ui-input @error('first_name') is-invalid @enderror" value="{{ old('first_name', $employee->first_name) }}" required>
                        @error('first_name')<p class="ep-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label" for="last_name">Last Name <span class="ep-req">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="ui-input @error('last_name') is-invalid @enderror" value="{{ old('last_name', $employee->last_name) }}" required>
                        @error('last_name')<p class="ep-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Email Address</label>
                        <input type="email" class="ui-input" value="{{ $employee->email }}" disabled>
                        <p class="ep-hint">Contact IT to change your email</p>
                    </div>
                    <div>
                        <label class="ui-label" for="phone">Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="ui-input @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}">
                        @error('phone')<p class="ep-err">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </section>

        {{-- System Preferences --}}
        <section class="ep-section">
            <div>
                <h2>System Preferences</h2>
                <p class="ep-desc">Used for dates, times and language across the portal.</p>
            </div>
            <div class="ep-card">
                <div class="ep-grid">
                    <div>
                        <label class="ui-label" for="time_zone">Time Zone <span class="ep-req">*</span></label>
                        <select name="time_zone" id="time_zone" class="ui-select @error('time_zone') is-invalid @enderror" required>
                            <option value="">Select Time Zone</option>
                            @php $timezones = ['UTC'=>'UTC (Coordinated Universal Time)','America/New_York'=>'Eastern Time (US & Canada)','America/Chicago'=>'Central Time (US & Canada)','America/Denver'=>'Mountain Time (US & Canada)','America/Los_Angeles'=>'Pacific Time (US & Canada)','Europe/London'=>'London, Edinburgh, Dublin','Europe/Paris'=>'Paris, Berlin, Madrid','Africa/Harare'=>'Harare, Zimbabwe','Africa/Johannesburg'=>'Johannesburg, South Africa','Africa/Cairo'=>'Cairo, Egypt','Asia/Tokyo'=>'Tokyo, Osaka, Sapporo','Asia/Shanghai'=>'Beijing, Shanghai','Australia/Sydney'=>'Sydney, Melbourne']; @endphp
                            @foreach($timezones as $value => $label)
                            <option value="{{ $value }}" {{ old('time_zone', $employee->time_zone) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('time_zone')<p class="ep-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label" for="language">Language <span class="ep-req">*</span></label>
                        <select name="language" id="language" class="ui-select @error('language') is-invalid @enderror" required>
                            @php $languages = ['en'=>'English','es'=>'Spanish','fr'=>'French','de'=>'German','it'=>'Italian','pt'=>'Portuguese','zh'=>'Chinese','ja'=>'Japanese','ko'=>'Korean','ar'=>'Arabic']; @endphp
                            @foreach($languages as $value => $label)
                            <option value="{{ $value }}" {{ old('language', $employee->language) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('language')<p class="ep-err">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </section>

        {{-- Read-only system info --}}
        <section class="ep-section">
            <div>
                <h2>System Information</h2>
                <p class="ep-desc">Managed by administrator.</p>
            </div>
            <dl class="ep-facts" style="margin:0;">
                <div><dt>Employee #</dt><dd class="mv-mono">{{ $employee->employee_number }}</dd></div>
                <div><dt>Hire Date</dt><dd>{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'Not specified' }}</dd></div>
                <div><dt>Department</dt><dd>{{ $employee->department->name ?? 'Not assigned' }}</dd></div>
                <div><dt>Role</dt><dd>{{ $employee->role->name ?? 'Not assigned' }}</dd></div>
                <div><dt>Status</dt><dd><span class="badge {{ $employee->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($employee->status) }}</span></dd></div>
                <div><dt>Last Login</dt><dd>{{ $employee->last_login_at ? $employee->last_login_at->diffForHumans() : 'Never' }}</dd></div>
            </dl>
        </section>

        <div class="ep-foot">
            <a href="{{ route('employee.profile') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary"><svg class="mv-i" aria-hidden="true"><use href="#i-save"/></svg> Save Changes</button>
        </div>
    </form>
</div>
@endsection
