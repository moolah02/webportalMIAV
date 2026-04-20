"""
Batch fix for Bootstrap → design-system class replacements.
Handles:
  - profile/profile.blade.php (full rewrite)
  - Targeted replacements across all Bootstrap pages
"""
import os, re, glob

BASE = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'

# ============================================================
# FULL REWRITE: profile/profile.blade.php
# ============================================================
PROFILE_PROFILE = r"""@extends('layouts.app')
@section('title', 'My Profile')

@section('header-actions')
<a href="{{ route('employee.edit-profile') }}" class="btn-primary btn-sm">&#x270F;&#xFE0F; Edit Profile</a>
<button type="button" onclick="document.getElementById('changePwModal2').classList.remove('hidden')" class="btn-secondary btn-sm">&#x1F510; Change Password</button>
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

{{-- Main grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Personal + Org --}}
    <div class="flex flex-col gap-5">
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F464; Personal Information</h3>
            </div>
            <div class="ui-card-body">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                    <div class="w-12 h-12 rounded-full bg-[#1a3a5c] flex items-center justify-center text-white font-bold text-base flex-shrink-0">
                        {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">{{ $employee->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $employee->employee_number }}@if($employee->department) &middot; {{ $employee->department->name }}@endif</div>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span class="badge {{ $employee->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($employee->status) }}</span>
                            @if($employee->isFieldTechnician())<span class="badge badge-yellow">&#x1F527; Technician</span>@endif
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-gray-50 text-sm">
                    <div class="flex justify-between py-2"><span class="text-gray-500">Email</span><span class="text-gray-800">{{ $employee->email }}</span></div>
                    <div class="flex justify-between py-2"><span class="text-gray-500">Phone</span><span class="text-gray-800">{{ $employee->phone ?: 'Not provided' }}</span></div>
                    <div class="flex justify-between py-2"><span class="text-gray-500">Hire Date</span><span class="text-gray-800">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'Not specified' }}</span></div>
                    @if($employee->position)<div class="flex justify-between py-2"><span class="text-gray-500">Position</span><span class="text-gray-800">{{ $employee->position }}</span></div>@endif
                    <div class="flex justify-between py-2"><span class="text-gray-500">Time Zone</span><span class="text-gray-800">{{ $employee->time_zone }}</span></div>
                    <div class="flex justify-between py-2"><span class="text-gray-500">Language</span><span class="text-gray-800">{{ strtoupper($employee->language) }}</span></div>
                </div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F3E2; Organisation</h3>
            </div>
            <div class="ui-card-body">
                <div class="divide-y divide-gray-50 text-sm">
                    <div class="flex justify-between py-2"><span class="text-gray-500">Department</span><span class="text-gray-800">{{ $employee->department->name ?? 'Not assigned' }}</span></div>
                    <div class="flex justify-between py-2"><span class="text-gray-500">Role</span><span class="text-gray-800">{{ $employee->role->name ?? 'Not assigned' }}@if($employee->isFieldTechnician()) &#x1F527;@endif</span></div>
                    <div class="flex justify-between py-2"><span class="text-gray-500">Manager</span><span class="text-gray-800">{{ $employee->manager->full_name ?? 'None' }}</span></div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">2FA</span>
                        <span class="badge {{ $employee->two_factor_enabled ? 'badge-green' : 'badge-yellow' }}">{{ $employee->two_factor_enabled ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="flex justify-between py-2"><span class="text-gray-500">Last Login</span><span class="text-gray-800">{{ $employee->last_login_at ? $employee->last_login_at->diffForHumans() : 'Never' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Middle: Assigned Assets --}}
    <div class="flex flex-col gap-5">
        @if($employee->currentAssetAssignments->count() > 0)
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F5A5;&#xFE0F; Assigned Assets</h3>
                <span class="badge badge-gray">{{ $employee->currentAssetAssignments->count() }}</span>
            </div>
            <div class="ui-card-body p-0">
                <table class="ui-table">
                    <thead><tr><th>Asset</th><th>Condition</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($employee->currentAssetAssignments->take(8) as $assignment)
                    <tr>
                        <td>
                            <div class="font-medium text-gray-800">{{ $assignment->asset->name }}</div>
                            @if($assignment->asset->brand)<div class="text-xs text-gray-500">{{ $assignment->asset->brand }}</div>@endif
                        </td>
                        <td><span class="badge {{ $assignment->condition_when_assigned === 'new' ? 'badge-green' : ($assignment->condition_when_assigned === 'good' ? 'badge-blue' : 'badge-yellow') }}">{{ ucfirst($assignment->condition_when_assigned) }}</span></td>
                        <td>
                            <span class="badge {{ $assignment->isOverdue() ? 'badge-red' : 'badge-green' }}">{{ $assignment->isOverdue() ? $assignment->days_overdue.'d overdue' : 'OK' }}</span>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($employee->subordinates->count() > 0)
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F465; Team Members ({{ $employee->subordinates->count() }})</h3>
            </div>
            <div class="ui-card-body">
                <div class="space-y-2">
                    @foreach($employee->subordinates->take(6) as $sub)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">{{ strtoupper(substr($sub->first_name,0,1).substr($sub->last_name,0,1)) }}</div>
                        <div>
                            <div class="text-sm font-medium text-gray-800">{{ $sub->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $sub->role->name ?? 'No role' }}@if($sub->isFieldTechnician()) &#x1F527;@endif</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Right: Recent Requests --}}
    <div>
        @if($employee->assetRequests->count() > 0)
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F4DC; Recent Asset Requests</h3>
            </div>
            <div class="ui-card-body p-0">
                <table class="ui-table">
                    <thead><tr><th>Request #</th><th>Status</th><th>Cost</th><th>Date</th></tr></thead>
                    <tbody>
                    @foreach($employee->assetRequests->take(8) as $request)
                    <tr>
                        <td>
                            <div class="font-medium text-gray-800">{{ $request->request_number }}</div>
                            @if($request->business_justification)<div class="text-xs text-gray-500">{{ Str::limit($request->business_justification, 25) }}</div>@endif
                        </td>
                        <td>
                            @php $rBadge = match($request->status) { 'approved' => 'badge-green', 'rejected' => 'badge-red', 'pending' => 'badge-yellow', 'fulfilled' => 'badge-blue', default => 'badge-gray' }; @endphp
                            <span class="badge {{ $rBadge }}">{{ ucfirst($request->status) }}</span>
                            @if(in_array($request->priority, ['urgent','high']))<div class="text-xs text-red-500 mt-0.5">{{ ucfirst($request->priority) }}</div>@endif
                        </td>
                        <td class="text-gray-700">${{ number_format($request->total_estimated_cost, 0) }}</td>
                        <td class="text-gray-500">{{ $request->created_at->format('M d') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Change Password Modal --}}
<div id="changePwModal2" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="ui-card w-full max-w-sm">
        <div class="ui-card-header" style="background:#1a3a5c;">
            <h3 class="text-sm font-semibold text-white m-0">&#x1F510; Change Password</h3>
            <button onclick="document.getElementById('changePwModal2').classList.add('hidden')" class="text-white/70 hover:text-white text-xl leading-none border-0 bg-transparent cursor-pointer">&times;</button>
        </div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('employee.update-password') }}">
                @csrf @method('PATCH')
                <div class="mb-4">
                    <label class="ui-label" for="cp2_current">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" id="cp2_current" class="ui-input" required>
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="ui-label" for="cp2_new">New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="cp2_new" class="ui-input" required minlength="8">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-5">
                    <label class="ui-label" for="cp2_confirm">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="cp2_confirm" class="ui-input" required minlength="8">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">&#x1F510; Update Password</button>
                    <button type="button" onclick="document.getElementById('changePwModal2').classList.add('hidden')" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
"""

def write_file(rel, content):
    path = os.path.join(BASE, rel)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content.lstrip('\n'))
    print(f'Written: {rel}')

write_file(r'profile\profile.blade.php', PROFILE_PROFILE)

# ============================================================
# TARGETED REPLACEMENTS: Bootstrap → Design System
# ============================================================

# Patterns applied inside class="" attributes only
class_patterns = [
    # Cards — order matters (specific first)
    (r'\bcard-header\b', 'ui-card-header'),
    (r'\bcard-body\b',   'ui-card-body'),
    (r'(?<!["\'\w-])card\b', 'ui-card'),   # standalone 'card' not preceded by word chars
    # Buttons — most specific first
    (r'btn btn-outline-primary\b',    'btn-secondary'),
    (r'btn btn-outline-secondary\b',  'btn-secondary'),
    (r'btn btn-outline-danger\b',     'btn-danger'),
    (r'btn btn-outline-success\b',    'btn-success'),
    (r'btn btn-outline\b',            'btn-secondary'),
    (r'btn btn-primary\b',            'btn-primary'),
    (r'btn btn-secondary\b',          'btn-secondary'),
    (r'btn btn-danger\b',             'btn-danger'),
    (r'btn btn-success\b',            'btn-success'),
    (r'btn btn-warning\b',            'btn-secondary'),
    (r'btn btn-info\b',               'btn-secondary'),
    (r'btn btn-dark\b',               'btn-primary'),
    (r'btn btn-light\b',              'btn-secondary'),
    (r'btn btn-back\b',               'btn-secondary'),
    # Form fields
    (r'\bform-control\b',   'ui-input'),
    (r'\bform-select\b',    'ui-select'),
    (r'\bform-group\b',     'mb-4'),
    # Layout (safe to simplify)
    (r'\bcontainer-fluid\b', ''),
]

# Remove body{} style override (inside style blocks only)
BODY_OVERRIDE_RE = re.compile(
    r'(body\s*\{[^}]*\})',
    re.DOTALL
)

TARGET_FILES = [
    r'deployment\site-visit.blade.php',
    r'projects\closure-wizard.blade.php',
    r'projects\completion-wizard.blade.php',
    r'projects\closure-reports.blade.php',
    r'projects\completion-reports.blade.php',
    r'projects\completion-success.blade.php',
    r'projects\create-improved.blade.php',
    r'pos-terminals\import.blade.php',
    r'pos-terminals\column-mapping.blade.php',
    r'pos-terminals\show.blade.php',
    r'roles\show.blade.php',
    r'settings\asset-category-fields\index.blade.php',
    r'settings\manage-category.blade.php',
    r'settings\manage-role.blade.php',
    r'settings\index.blade.php',
    r'reports\history.blade.php',
    r'reports\system-dashboard.blade.php',
    r'reports\index.blade.php',
    r'asset-requests\catalog.blade.php',
    r'asset-requests\index.blade.php',
    r'visits\index.blade.php',
    r'reports\technician-visits.blade.php',
    r'client-dashboards\index.blade.php',
    r'assets\partials\assets-tab.blade.php',
    r'assets\partials\assets-table.blade.php',
    r'assets\partials\assign-tab.blade.php',
    r'assets\partials\assignments-tab.blade.php',
    r'assets\partials\existing-modals.blade.php',
    r'assets\partials\history-tab.blade.php',
    r'assets\modals\assign-asset.blade.php',
    r'assets\modals\return-asset.blade.php',
    r'assets\modals\transfer-asset.blade.php',
    r'projects\partials\active-projects-tab.blade.php',
    r'projects\partials\completed-projects-tab.blade.php',
    r'projects\partials\terminal-list-modal.blade.php',
    r'projects\partials\terminal-preview-modal.blade.php',
    r'projects\partials\terminal-upload-section.blade.php',
    r'projects\partials\manual-report-generator.blade.php',
    r'projects\partials\report-generation-tab.blade.php',
]

def apply_class_patches(txt):
    # Apply inside class attribute values only (between quotes after class=)
    def patch_class_value(m):
        val = m.group(1)
        for pat, repl in class_patterns:
            val = re.sub(pat, repl, val)
        # Clean up multiple spaces
        val = re.sub(r'  +', ' ', val).strip()
        return f'class="{val}"'

    txt = re.sub(r'class="([^"]*)"', patch_class_value, txt)
    # Same for class='...'
    def patch_class_value_sq(m):
        val = m.group(1)
        for pat, repl in class_patterns:
            val = re.sub(pat, repl, val)
        val = re.sub(r'  +', ' ', val).strip()
        return f"class='{val}'"
    txt = re.sub(r"class='([^']*)'", patch_class_value_sq, txt)
    return txt

changed = 0
for rel in TARGET_FILES:
    path = os.path.join(BASE, rel)
    if not os.path.exists(path):
        print(f'SKIP (not found): {rel}')
        continue
    try:
        original = open(path, encoding='utf-8').read()
    except Exception as e:
        print(f'ERROR reading {rel}: {e}')
        continue

    patched = apply_class_patches(original)

    if patched != original:
        with open(path, 'w', encoding='utf-8') as f:
            f.write(patched)
        changed += 1
        print(f'Patched: {rel}')
    else:
        print(f'No changes: {rel}')

print(f'\nDone. {changed} file(s) updated.')
