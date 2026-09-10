@extends('layouts.app')
@section('title', 'My Profile')

@section('header-actions')
<a href="{{ route('employee.edit-profile') }}" class="btn-primary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit Profile</a>
@endsection

@push('styles')
<style>
    .pf { display: grid; gap: 16px; }
    .pf .mv-i { width: 16px; height: 16px; }

    /* Identity + figures */
    .pf-head { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: stretch; overflow: hidden; }
    .pf-id { display: flex; align-items: center; gap: 16px; padding: 18px 20px; min-width: 0; }
    .pf-avatar { width: 52px; height: 52px; border-radius: 50%; background: var(--mv-accent-soft); color: var(--mv-accent-ink); display: grid; place-items: center; font-size: 17px; font-weight: 600; letter-spacing: .02em; flex-shrink: 0; }
    .pf-name { font-size: 17px; font-weight: 600; color: var(--mv-ink); letter-spacing: -.01em; }
    .pf-line { margin-top: 2px; font-size: 13px; color: var(--mv-muted); }
    .pf-line .mv-mono { color: var(--mv-ink-2); }
    .pf-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .pf-figs { display: grid; grid-template-columns: repeat(4, minmax(110px, 1fr)); border-left: 1px solid var(--mv-line); }
    .pf-fig { padding: 16px 18px; display: flex; flex-direction: column; justify-content: center; }
    .pf-fig + .pf-fig { border-left: 1px solid var(--mv-line); }
    .pf-fig .stat-number { font-size: 22px; font-weight: 600; letter-spacing: -.02em; color: var(--mv-ink); line-height: 1.15; font-variant-numeric: tabular-nums; }
    .pf-fig .stat-label { font-size: 12.5px; color: var(--mv-muted); margin-top: 2px; }

    /* Cards */
    .pf-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; align-items: start; }
    .pf-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
    .pf-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 18px; border-bottom: 1px solid var(--mv-line); }
    .pf-card-head h3 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .pf-card-body { padding: 6px 18px 16px; }
    .pf-sub { font-size: 12px; font-weight: 600; color: var(--mv-muted); letter-spacing: .04em; margin: 14px 0 4px; }
    .pf-dl { margin: 0; }
    .pf-dl > div { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 9px 0; border-top: 1px solid var(--mv-line); font-size: 13.5px; }
    .pf-dl > div:first-child { border-top: 0; }
    .pf-dl dt { color: var(--mv-muted); font-weight: 400; }
    .pf-dl dd { margin: 0; color: var(--mv-ink); text-align: right; display: flex; flex-wrap: wrap; gap: 4px; justify-content: flex-end; }
    .pf-foot { padding-top: 14px; }
    .pf-foot button { display: inline-flex; align-items: center; gap: 6px; }

    .pf-list > div { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 10px 0; border-top: 1px solid var(--mv-line); }
    .pf-list > div:first-child { border-top: 0; }
    .pf-list-title { font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
    .pf-list-sub { font-size: 12.5px; color: var(--mv-muted); margin-top: 1px; }
    .pf-list-side { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
    .pf-person { display: flex; align-items: center; gap: 10px; }
    .pf-person-av { width: 30px; height: 30px; border-radius: 50%; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); display: grid; place-items: center; font-size: 11.5px; font-weight: 600; flex-shrink: 0; }
    .pf-more { font-size: 12.5px; color: var(--mv-muted); padding-top: 8px; }
    .pf-empty { padding: 32px 0 16px; text-align: center; font-size: 13.5px; color: var(--mv-muted); }
    .pf-empty .mv-i { width: 26px; height: 26px; color: var(--mv-line-strong); display: block; margin: 0 auto 8px; }

    /* Change password modal */
    .pf-modal { position: fixed; inset: 0; z-index: 1100; background: rgba(22,32,44,.45); display: flex; align-items: center; justify-content: center; padding: 16px; }
    .pf-modal-box { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 20px 48px rgba(22,32,44,.18); width: 100%; max-width: 420px; }
    .pf-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid var(--mv-line); }
    .pf-modal-head h3 { margin: 0; font-size: 15px; font-weight: 600; color: var(--mv-ink); }
    .pf-modal-close { background: none; border: 0; font-size: 22px; line-height: 1; color: var(--mv-muted); cursor: pointer; padding: 0 2px; }
    .pf-modal-close:hover { color: var(--mv-ink); }
    .pf-modal-body { padding: 18px 20px; display: grid; gap: 14px; }
    .pf-modal-body .ui-input { width: 100%; height: 36px; padding: 0 11px; font-size: 13.5px; }
    .pf-err { color: var(--mv-crit); font-size: 12px; margin-top: 4px; }
    .pf-req { color: var(--mv-crit); }
    .pf-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 20px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); border-radius: 0 0 12px 12px; }

    @media (max-width: 1100px) { .pf-head { grid-template-columns: minmax(0, 1fr); } .pf-figs { border-left: 0; border-top: 1px solid var(--mv-line); } }
    @media (max-width: 900px) { .pf-grid { grid-template-columns: minmax(0, 1fr); } .pf-figs { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
@endpush

@section('content')
<div class="pf">

    {{-- Identity + figures --}}
    <div class="pf-head">
        <div class="pf-id">
            <div class="pf-avatar">{{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}</div>
            <div style="min-width:0;">
                <div class="pf-name">{{ $employee->full_name }}</div>
                <div class="pf-line"><span class="mv-mono">{{ $employee->employee_number }}</span> &middot; {{ $employee->department->name ?? 'No Department' }}</div>
                <div class="pf-chips">
                    <span class="badge {{ $employee->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($employee->status) }}</span>
                    @foreach($employee->roles as $role)
                    <span class="badge badge-blue">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="pf-figs">
            <div class="pf-fig"><div class="stat-number">{{ $stats['total_asset_requests'] }}</div><div class="stat-label">Total Requests</div></div>
            <div class="pf-fig"><div class="stat-number">{{ $stats['pending_requests'] }}</div><div class="stat-label">Pending</div></div>
            <div class="pf-fig"><div class="stat-number">{{ $stats['assigned_assets_count'] }}</div><div class="stat-label">Assigned Assets</div></div>
            <div class="pf-fig"><div class="stat-number">{{ $stats['subordinates_count'] }}</div><div class="stat-label">Team Members</div></div>
        </div>
    </div>

    <div class="pf-grid">

        {{-- Personal Information --}}
        <div class="pf-card">
            <div class="pf-card-head"><h3>Personal Information</h3></div>
            <div class="pf-card-body">
                <dl class="pf-dl">
                    <div><dt>Email</dt><dd>{{ $employee->email }}</dd></div>
                    <div><dt>Phone</dt><dd>{{ $employee->phone ?: 'Not provided' }}</dd></div>
                    <div><dt>Hire Date</dt><dd>{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'Not specified' }}</dd></div>
                    @if($employee->position)
                    <div><dt>Position</dt><dd>{{ $employee->position }}</dd></div>
                    @endif
                    <div><dt>Time Zone</dt><dd>{{ $employee->time_zone }}</dd></div>
                    <div><dt>Language</dt><dd>{{ strtoupper($employee->language) }}</dd></div>
                </dl>

                <div class="pf-sub">Organisation</div>
                <dl class="pf-dl">
                    <div><dt>Department</dt><dd>{{ $employee->department->name ?? 'Not assigned' }}</dd></div>
                    <div>
                        <dt>Roles</dt>
                        <dd>
                            @forelse($employee->roles as $role)
                            <span class="badge badge-blue">{{ $role->name }}</span>
                            @empty
                            <span style="color:var(--mv-muted);">Not assigned</span>
                            @endforelse
                        </dd>
                    </div>
                    <div><dt>Manager</dt><dd>{{ $employee->manager->full_name ?? 'No manager assigned' }}</dd></div>
                    <div>
                        <dt>2FA Status</dt>
                        <dd><span class="badge {{ $employee->two_factor_enabled ? 'badge-green' : 'badge-yellow' }}">{{ $employee->two_factor_enabled ? 'Enabled' : 'Disabled' }}</span></dd>
                    </div>
                    <div><dt>Last Login</dt><dd>{{ $employee->last_login_at ? $employee->last_login_at->diffForHumans() : 'Never' }}</dd></div>
                </dl>

                <div class="pf-foot">
                    <button type="button" onclick="document.getElementById('changePwModal').classList.remove('hidden')" class="btn-secondary btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-lock"/></svg> Change Password</button>
                </div>
            </div>
        </div>

        {{-- Assets & Requests --}}
        <div class="pf-card">
            <div class="pf-card-head"><h3>Assets &amp; Requests</h3></div>
            <div class="pf-card-body">

                @if($employee->currentAssetAssignments->count() > 0)
                <div class="pf-sub">Assigned Assets ({{ $employee->currentAssetAssignments->count() }})</div>
                <div class="pf-list">
                    @foreach($employee->currentAssetAssignments->take(8) as $assignment)
                    <div>
                        <div style="min-width:0;">
                            <div class="pf-list-title">{{ $assignment->asset->name }}</div>
                            <div class="pf-list-sub">@if($assignment->asset->brand){{ $assignment->asset->brand }} &middot; @endif{{ $assignment->asset->category }}@if($assignment->asset->sku) &middot; SKU: <span class="mv-mono">{{ $assignment->asset->sku }}</span>@endif</div>
                        </div>
                        <div class="pf-list-side">
                            <span class="badge {{ $assignment->condition_when_assigned === 'new' ? 'badge-green' : ($assignment->condition_when_assigned === 'good' ? 'badge-blue' : 'badge-yellow') }}">{{ ucfirst($assignment->condition_when_assigned) }}</span>
                            @if($assignment->isOverdue())<span class="badge badge-red">{{ $assignment->days_overdue }}d overdue</span>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($employee->currentAssetAssignments->count() > 8)<div class="pf-more">And {{ $employee->currentAssetAssignments->count() - 8 }} more&hellip;</div>@endif
                @endif

                @if($employee->assetRequests->count() > 0)
                <div class="pf-sub">Recent Requests ({{ $employee->assetRequests->count() }})</div>
                <div class="pf-list">
                    @foreach($employee->assetRequests->take(8) as $request)
                    <div>
                        <div style="min-width:0;">
                            <div class="pf-list-title mv-mono" style="font-size:13px;">{{ $request->request_number }}</div>
                            <div class="pf-list-sub">@if($request->business_justification){{ Str::limit($request->business_justification, 40) }}@endif @if($request->total_estimated_cost) &middot; ${{ number_format($request->total_estimated_cost, 0) }}@endif</div>
                        </div>
                        <div class="pf-list-side">
                            @php $rBadge = match($request->status) { 'approved' => 'badge-green', 'rejected' => 'badge-red', 'pending' => 'badge-yellow', 'fulfilled' => 'badge-blue', default => 'badge-gray' }; @endphp
                            <span class="badge {{ $rBadge }}">{{ ucfirst($request->status) }}</span>
                            @if(in_array($request->priority, ['urgent','high']))<span class="badge badge-red">{{ ucfirst($request->priority) }}</span>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($employee->subordinates->count() > 0)
                <div class="pf-sub">Team Members ({{ $employee->subordinates->count() }})</div>
                <div class="pf-list">
                    @foreach($employee->subordinates->take(6) as $sub)
                    <div>
                        <div class="pf-person">
                            <div class="pf-person-av">{{ strtoupper(substr($sub->first_name,0,1).substr($sub->last_name,0,1)) }}</div>
                            <div>
                                <div class="pf-list-title">{{ $sub->full_name }}</div>
                                <div class="pf-list-sub">{{ $sub->role?->name ?? 'No role' }}</div>
                            </div>
                        </div>
                        @if($sub->isFieldTechnician())<span class="badge badge-gray">Technician</span>@endif
                    </div>
                    @endforeach
                </div>
                @if($employee->subordinates->count() > 6)<div class="pf-more">And {{ $employee->subordinates->count() - 6 }} more&hellip;</div>@endif
                @endif

                @if($employee->currentAssetAssignments->count() === 0 && $employee->assetRequests->count() === 0 && $employee->subordinates->count() === 0)
                <div class="pf-empty">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg>
                    No activity yet. Assets, requests and team info will appear here.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Change Password Modal --}}
<div id="changePwModal" class="hidden pf-modal">
    <div class="pf-modal-box">
        <div class="pf-modal-head">
            <h3>Change Password</h3>
            <button onclick="document.getElementById('changePwModal').classList.add('hidden')" class="pf-modal-close" aria-label="Close">&times;</button>
        </div>
        <form method="POST" action="{{ route('employee.update-password') }}">
            @csrf @method('PATCH')
            <div class="pf-modal-body">
                <div>
                    <label class="ui-label" for="cp_current">Current Password <span class="pf-req">*</span></label>
                    <input type="password" name="current_password" id="cp_current" class="ui-input" required>
                    @error('current_password')<p class="pf-err">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label" for="cp_new">New Password <span class="pf-req">*</span></label>
                    <input type="password" name="password" id="cp_new" class="ui-input" required minlength="8">
                    @error('password')<p class="pf-err">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label" for="cp_confirm">Confirm Password <span class="pf-req">*</span></label>
                    <input type="password" name="password_confirmation" id="cp_confirm" class="ui-input" required minlength="8">
                    @error('password_confirmation')<p class="pf-err">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="pf-modal-foot">
                <button type="button" onclick="document.getElementById('changePwModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Update Password</button>
            </div>
        </form>
    </div>
</div>

@endsection
