@extends('layouts.app')
@section('title', 'System Settings')

@push('styles')
<style>
    .st { display: grid; gap: 24px; max-width: 1180px; }
    .st .mv-i { width: 16px; height: 16px; }

    /* Summary strip */
    .st-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1px; background: var(--mv-line); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .st-summary > div { background: var(--mv-surface); padding: 14px 18px; }
    .st-summary .stat-number { font-size: 22px; font-weight: 600; letter-spacing: -.02em; color: var(--mv-ink); line-height: 1.15; font-variant-numeric: tabular-nums; }
    .st-summary .stat-label { font-size: 12.5px; color: var(--mv-muted); margin-top: 2px; }

    /* Two-column sections */
    .st-section { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 28px; padding-top: 24px; border-top: 1px solid var(--mv-line); }
    .st-section:first-of-type { border-top: 0; padding-top: 0; }
    .st-section-title { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
    .st-section-desc { margin: 4px 0 0; font-size: 13px; line-height: 1.5; color: var(--mv-muted); }

    .st-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .st-group + .st-group { border-top: 1px solid var(--mv-line); }
    .st-group-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; padding: 12px 16px 4px; }
    .st-group-title { font-size: 13px; font-weight: 600; color: var(--mv-ink); }
    .st-group-note { font-size: 12.5px; color: var(--mv-muted); }

    .st-links { list-style: none; margin: 0; padding: 4px 8px 8px; }
    .st-links a {
        display: flex; align-items: center; gap: 10px; padding: 8px 8px; border-radius: 7px;
        color: var(--mv-ink-2); font-size: 13.5px; text-decoration: none;
    }
    .st-links a:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
    .st-links a > .mv-i:first-child { color: var(--mv-muted); }
    .st-links .st-label { flex: 1; min-width: 0; }
    .st-links .st-chev { color: var(--mv-line-strong); }
    .st-links a:hover .st-chev { color: var(--mv-muted); }
    .badge-count { min-width: 26px; text-align: center; font-size: 12px; font-weight: 500; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 6px; padding: 0 6px; font-variant-numeric: tabular-nums; }
    .badge-manage { font-size: 12px; font-weight: 500; color: var(--mv-accent-ink); }

    .st-ref { padding: 6px 16px 14px; }
    .st-ref-label { font-size: 12px; font-weight: 500; color: var(--mv-muted); margin: 6px 0 6px; }
    .ref-chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .ref-chips .badge { font-size: 12px; padding: 2px 8px; }

    .st-all { columns: 2; column-gap: 8px; }
    .st-all li { break-inside: avoid; }

    @media (max-width: 1000px) {
        .st-section { grid-template-columns: minmax(0, 1fr); gap: 12px; }
        .st-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .st-all { columns: 1; }
    }
</style>
@endpush

@section('content')
@php
    $count = fn ($type) => $categories->get($type, collect())->count();
@endphp
<div class="st">

    {{-- Summary --}}
    <div class="st-summary">
        <div><div class="stat-number">{{ $stats['total_categories'] }}</div><div class="stat-label">Total Categories</div></div>
        <div><div class="stat-number">{{ $stats['asset_categories'] }}</div><div class="stat-label">Asset Categories</div></div>
        <div><div class="stat-number">{{ $stats['total_departments'] }}</div><div class="stat-label">Departments</div></div>
        <div><div class="stat-number">{{ $stats['total_roles'] }}</div><div class="stat-label">Roles</div></div>
    </div>

    {{-- SECTION 1: Assets & Terminals --}}
    <section class="st-section">
        <div>
            <h2 class="st-section-title">Assets &amp; Terminals</h2>
            <p class="st-section-desc">Asset categories, statuses and custom fields; POS terminal statuses and device models; job and field service types.</p>
        </div>
        <div class="st-card">
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Asset Configuration</span><span class="st-group-note">Asset categories, statuses and custom fields</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.category.manage', 'asset_category') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-tag"/></svg><span class="st-label">Asset Categories</span>
                        <span class="badge-count">{{ $count('asset_category') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                    <li><a href="{{ route('settings.category.manage', 'asset_status') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-activity"/></svg><span class="st-label">Asset Statuses</span>
                        <span class="badge-count">{{ $count('asset_status') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                    <li><a href="{{ route('settings.asset-category-fields.index', $categories->get('asset_category', collect())->first()?->id ?? 1) }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-sliders"/></svg><span class="st-label">Category Custom Fields</span>
                        <span class="badge-manage">Manage</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Terminal Configuration</span><span class="st-group-note">POS terminal statuses and device models</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.category.manage', 'terminal_status') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-wifi"/></svg><span class="st-label">Terminal Statuses</span>
                        <span class="badge-count">{{ $count('terminal_status') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                    <li><a href="{{ route('settings.category.manage', 'terminal_model') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-monitor"/></svg><span class="st-label">Terminal Models / Brands</span>
                        <span class="badge-count">{{ $count('terminal_model') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Service Configuration</span><span class="st-group-note">Job and field service type definitions</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.category.manage', 'service_type') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-wrench"/></svg><span class="st-label">Service Types</span>
                        <span class="badge-count">{{ $count('service_type') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                    <li><a href="{{ route('settings.category.manage', 'visit_purpose') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-pin"/></svg><span class="st-label">Visit Purposes</span>
                        <span class="badge-count">{{ $count('visit_purpose') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
        </div>
    </section>

    {{-- SECTION 2: Support Tickets --}}
    <section class="st-section">
        <div>
            <h2 class="st-section-title">Support Tickets</h2>
            <p class="st-section-desc">Issue types raised against terminals or internally. Ticket statuses and priorities are system-defined.</p>
        </div>
        <div class="st-card">
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Ticket Categories</span><span class="st-group-note">Issue types raised against terminals or internally</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.category.manage', 'ticket_issue_type') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-ticket"/></svg><span class="st-label">Issue Types</span>
                        <span class="badge-count">{{ $count('ticket_issue_type') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Ticket Status &amp; Priority Reference</span><span class="st-group-note">Read-only — these values are system-defined and control ticket workflows</span></div>
                <div class="st-ref">
                    <div class="st-ref-label">Statuses</div>
                    <div class="ref-chips">
                        <span class="badge badge-blue">Open</span>
                        <span class="badge badge-yellow">In Progress</span>
                        <span class="badge badge-gray">On Hold</span>
                        <span class="badge badge-yellow">Pending</span>
                        <span class="badge badge-green">Resolved</span>
                        <span class="badge badge-gray">Closed</span>
                        <span class="badge badge-red">Cancelled</span>
                    </div>
                    <div class="st-ref-label" style="margin-top:12px;">Priorities</div>
                    <div class="ref-chips">
                        <span class="badge badge-gray">Low</span>
                        <span class="badge badge-blue">Medium</span>
                        <span class="badge badge-yellow">High</span>
                        <span class="badge badge-red">Critical</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 3: People & HR --}}
    <section class="st-section">
        <div>
            <h2 class="st-section-title">People &amp; HR</h2>
            <p class="st-section-desc">Organisational units, access-control roles and job titles.</p>
        </div>
        <div class="st-card">
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Departments</span><span class="st-group-note">Organisational units and teams</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.departments.manage') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg><span class="st-label">All Departments</span>
                        <span class="badge-count">{{ $stats['total_departments'] }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Roles &amp; Permissions</span><span class="st-group-note">Access control roles and their permission sets</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.roles.manage') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-shield"/></svg><span class="st-label">Manage Roles</span>
                        <span class="badge-count">{{ $stats['total_roles'] }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Employee Positions</span><span class="st-group-note">Job titles and position classifications</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.category.manage', 'employee_position') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-briefcase"/></svg><span class="st-label">Positions / Job Titles</span>
                        <span class="badge-count">{{ $count('employee_position') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
        </div>
    </section>

    {{-- SECTION 4: Clients & Projects --}}
    <section class="st-section">
        <div>
            <h2 class="st-section-title">Clients &amp; Projects</h2>
            <p class="st-section-desc">Industry tags, merchant/business types and project classifications.</p>
        </div>
        <div class="st-card">
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Client Classification</span><span class="st-group-note">Industry tags and merchant/business types</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.category.manage', 'client_industry') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg><span class="st-label">Client Industries</span>
                        <span class="badge-count">{{ $count('client_industry') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                    <li><a href="{{ route('settings.category.manage', 'business_type') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-cart"/></svg><span class="st-label">Business / Merchant Types</span>
                        <span class="badge-count">{{ $count('business_type') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Project Configuration</span><span class="st-group-note">Project type classifications</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.category.manage', 'project_type') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-folder"/></svg><span class="st-label">Project Types</span>
                        <span class="badge-count">{{ $count('project_type') }}</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Project Status Reference</span><span class="st-group-note">Read-only system-defined project lifecycle states</span></div>
                <div class="st-ref">
                    <div class="ref-chips">
                        <span class="badge badge-gray">Planning</span>
                        <span class="badge badge-blue">Active</span>
                        <span class="badge badge-yellow">On Hold</span>
                        <span class="badge badge-yellow">Paused</span>
                        <span class="badge badge-green">Completed</span>
                        <span class="badge badge-gray">Closed</span>
                        <span class="badge badge-red">Cancelled</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 5: System --}}
    <section class="st-section">
        <div>
            <h2 class="st-section-title">System</h2>
            <p class="st-section-desc">Email, notifications and backups, every lookup list in one place, and the audit trail.</p>
        </div>
        <div class="st-card">
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">General Settings</span><span class="st-group-note">Email, notifications and backup configuration</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('settings.email') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-mail"/></svg><span class="st-label">Email Settings</span>
                        <span class="badge-manage">Configure</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                    <li><a href="{{ route('settings.notifications') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-bell"/></svg><span class="st-label">Notification Rules</span>
                        <span class="badge-manage">Configure</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                    <li><a href="{{ route('settings.backups') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-database"/></svg><span class="st-label">Database Backups</span>
                        <span class="badge-manage">Manage</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">Audit Trail</span><span class="st-group-note">System activity logs and audit records</span></div>
                <ul class="st-links">
                    <li><a href="{{ route('audit-trail.index') }}">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-history"/></svg><span class="st-label">View Audit Trail</span>
                        <span class="badge-manage">Open</span><svg class="mv-i st-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a></li>
                </ul>
            </div>
            <div class="st-group">
                <div class="st-group-head"><span class="st-group-title">System Categories (All)</span><span class="st-group-note">Full list of every lookup / category type</span></div>
                <ul class="st-links st-all">
                    @foreach($categoryTypes as $type => $label)
                    <li><a href="{{ route('settings.category.manage', $type) }}">
                        <span class="st-label">{{ $label }}</span>
                        <span class="badge-count">{{ $count($type) }}</span>
                    </a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
</div>
@endsection
