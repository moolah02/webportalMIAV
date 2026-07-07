@extends('layouts.app')
@section('title', 'System Settings')

@push('styles')
<style>
    /* ── Stats row ───────────────────────────────────────── */
    .settings-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-left: 4px solid #1a3a5c;
    }
    .stat-number { font-size: 30px; font-weight: 700; color: #1a3a5c; line-height: 1; }
    .stat-label  { font-size: 13px; color: #6b7280; margin-top: 6px; }

    /* ── Section group ───────────────────────────────────── */
    .settings-section { margin-bottom: 32px; }
    .settings-section-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 12px;
        padding-left: 2px;
    }
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
    }
    .settings-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px 22px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .settings-header {
        font-size: 14px;
        font-weight: 700;
        color: #1a3a5c;
        margin-bottom: 4px;
    }
    .settings-desc { font-size: 12px; color: #9ca3af; margin-bottom: 14px; }
    .settings-list { list-style: none; padding: 0; margin: 0; }
    .settings-list li { border-top: 1px solid #f1f5f9; }
    .settings-list li:first-child { border-top: none; }
    .settings-list li a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        color: #374151;
        text-decoration: none;
        font-size: 13px;
        transition: color .15s;
    }
    .settings-list li a:hover { color: #1a3a5c; }
    .badge-count {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: #f1f5f9;
        color: #64748b;
        min-width: 24px;
        text-align: center;
    }
    .badge-soon {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        background: #fef3c7;
        color: #92400e;
    }
    .badge-manage {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        background: #e0f2fe;
        color: #0369a1;
    }

    /* ── Reference panel ─────────────────────────────────── */
    .ref-panel {
        background: #fff;
        border-radius: 12px;
        padding: 22px 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .ref-panel-title { font-size: 14px; font-weight: 700; color: #1a3a5c; margin-bottom: 4px; }
    .ref-panel-desc  { font-size: 12px; color: #9ca3af; margin-bottom: 14px; }
    .ref-chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .ref-chip {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #374151;
    }
    .ref-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

    @media (max-width: 1100px) {
        .settings-stats  { grid-template-columns: repeat(2, 1fr); }
        .settings-grid   { grid-template-columns: repeat(2, 1fr); }
        .ref-grid        { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .settings-stats  { grid-template-columns: 1fr; }
        .settings-grid   { grid-template-columns: 1fr; }
        .ref-grid        { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div style="background:#fff;padding:22px 26px;border-radius:12px;margin-bottom:24px;box-shadow:0 2px 10px rgba(0,0,0,.05);">
    <h1 style="margin:0;color:#1a3a5c;font-weight:700;font-size:20px;">⚙️ Settings &amp; Configuration</h1>
    <p style="margin:6px 0 0;color:#6b7280;font-size:13px;">Manage system categories, lookup values, and configurations</p>
</div>

{{-- Stats --}}
<div class="settings-stats">
    <div class="stat-card">
        <div class="stat-number">{{ $stats['total_categories'] }}</div>
        <div class="stat-label">Total Categories</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $stats['asset_categories'] }}</div>
        <div class="stat-label">Asset Categories</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $stats['total_departments'] }}</div>
        <div class="stat-label">Departments</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $stats['total_roles'] }}</div>
        <div class="stat-label">Roles</div>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────── --}}
{{-- SECTION 1: Assets & Terminals                          --}}
{{-- ─────────────────────────────────────────────────────── --}}
<div class="settings-section">
    <div class="settings-section-title">Assets &amp; Terminals</div>
    <div class="settings-grid">

        <div class="settings-card">
            <div class="settings-header">📦 Asset Configuration</div>
            <div class="settings-desc">Asset categories, statuses and custom fields</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.category.manage', 'asset_category') }}">
                    <span>🏷️ Asset Categories</span>
                    <span class="badge-count">{{ $categories->get('asset_category', collect())->count() }}</span>
                </a></li>
                <li><a href="{{ route('settings.category.manage', 'asset_status') }}">
                    <span>📊 Asset Statuses</span>
                    <span class="badge-count">{{ $categories->get('asset_status', collect())->count() }}</span>
                </a></li>
                <li><a href="{{ route('settings.asset-category-fields.index', $categories->get('asset_category', collect())->first()?->id ?? 1) }}">
                    <span>⚙️ Category Custom Fields</span>
                    <span class="badge-manage">Manage</span>
                </a></li>
            </ul>
        </div>

        <div class="settings-card">
            <div class="settings-header">🖥️ Terminal Configuration</div>
            <div class="settings-desc">POS terminal statuses and device models</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.category.manage', 'terminal_status') }}">
                    <span>📶 Terminal Statuses</span>
                    <span class="badge-count">{{ $categories->get('terminal_status', collect())->count() }}</span>
                </a></li>
                <li><a href="{{ route('settings.category.manage', 'terminal_model') }}">
                    <span>🖥️ Terminal Models / Brands</span>
                    <span class="badge-count">{{ $categories->get('terminal_model', collect())->count() }}</span>
                </a></li>
            </ul>
        </div>

        <div class="settings-card">
            <div class="settings-header">🔧 Service Configuration</div>
            <div class="settings-desc">Job and field service type definitions</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.category.manage', 'service_type') }}">
                    <span>⚙️ Service Types</span>
                    <span class="badge-count">{{ $categories->get('service_type', collect())->count() }}</span>
                </a></li>
                <li><a href="{{ route('settings.category.manage', 'visit_purpose') }}">
                    <span>🗺️ Visit Purposes</span>
                    <span class="badge-count">{{ $categories->get('visit_purpose', collect())->count() }}</span>
                </a></li>
            </ul>
        </div>

    </div>
</div>

{{-- ─────────────────────────────────────────────────────── --}}
{{-- SECTION 2: Support Tickets                             --}}
{{-- ─────────────────────────────────────────────────────── --}}
<div class="settings-section">
    <div class="settings-section-title">Support Tickets</div>
    <div class="settings-grid">

        <div class="settings-card">
            <div class="settings-header">🎫 Ticket Categories</div>
            <div class="settings-desc">Issue types raised against terminals or internally</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.category.manage', 'ticket_issue_type') }}">
                    <span>🔍 Issue Types</span>
                    <span class="badge-count">{{ $categories->get('ticket_issue_type', collect())->count() }}</span>
                </a></li>
            </ul>
        </div>

        <div class="settings-card" style="grid-column: span 2;">
            <div class="settings-header">📌 Ticket Status &amp; Priority Reference</div>
            <div class="settings-desc">Read-only — these values are system-defined and control ticket workflows</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:4px;">
                <div>
                    <div style="font-size:11px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em;">Statuses</div>
                    <div class="ref-chips">
                        <span class="ref-chip" style="background:#fef9c3;border-color:#fde047;color:#713f12;">Open</span>
                        <span class="ref-chip" style="background:#dbeafe;border-color:#93c5fd;color:#1e40af;">In Progress</span>
                        <span class="ref-chip" style="background:#fef3c7;border-color:#fcd34d;color:#92400e;">On Hold</span>
                        <span class="ref-chip" style="background:#f3e8ff;border-color:#d8b4fe;color:#6b21a8;">Pending</span>
                        <span class="ref-chip" style="background:#dcfce7;border-color:#86efac;color:#166534;">Resolved</span>
                        <span class="ref-chip" style="background:#f1f5f9;border-color:#cbd5e1;color:#475569;">Closed</span>
                        <span class="ref-chip" style="background:#fee2e2;border-color:#fca5a5;color:#991b1b;">Cancelled</span>
                    </div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em;">Priorities</div>
                    <div class="ref-chips">
                        <span class="ref-chip" style="background:#f0fdf4;border-color:#bbf7d0;color:#15803d;">Low</span>
                        <span class="ref-chip" style="background:#fefce8;border-color:#fef08a;color:#a16207;">Medium</span>
                        <span class="ref-chip" style="background:#fff7ed;border-color:#fed7aa;color:#c2410c;">High</span>
                        <span class="ref-chip" style="background:#fef2f2;border-color:#fecaca;color:#b91c1c;">Critical</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ─────────────────────────────────────────────────────── --}}
{{-- SECTION 3: People & HR                                 --}}
{{-- ─────────────────────────────────────────────────────── --}}
<div class="settings-section">
    <div class="settings-section-title">People &amp; HR</div>
    <div class="settings-grid">

        <div class="settings-card">
            <div class="settings-header">🏢 Departments</div>
            <div class="settings-desc">Organisational units and teams</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.departments.manage') }}">
                    <span>📂 All Departments</span>
                    <span class="badge-count">{{ $stats['total_departments'] }}</span>
                </a></li>
            </ul>
        </div>

        <div class="settings-card">
            <div class="settings-header">🛡️ Roles &amp; Permissions</div>
            <div class="settings-desc">Access control roles and their permission sets</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.roles.manage') }}">
                    <span>🔑 Manage Roles</span>
                    <span class="badge-count">{{ $stats['total_roles'] }}</span>
                </a></li>
            </ul>
        </div>

        <div class="settings-card">
            <div class="settings-header">👤 Employee Positions</div>
            <div class="settings-desc">Job titles and position classifications</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.category.manage', 'employee_position') }}">
                    <span>📋 Positions / Job Titles</span>
                    <span class="badge-count">{{ $categories->get('employee_position', collect())->count() }}</span>
                </a></li>
            </ul>
        </div>

    </div>
</div>

{{-- ─────────────────────────────────────────────────────── --}}
{{-- SECTION 4: Clients & Projects                          --}}
{{-- ─────────────────────────────────────────────────────── --}}
<div class="settings-section">
    <div class="settings-section-title">Clients &amp; Projects</div>
    <div class="settings-grid">

        <div class="settings-card">
            <div class="settings-header">🤝 Client Classification</div>
            <div class="settings-desc">Industry tags and merchant/business types</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.category.manage', 'client_industry') }}">
                    <span>🏭 Client Industries</span>
                    <span class="badge-count">{{ $categories->get('client_industry', collect())->count() }}</span>
                </a></li>
                <li><a href="{{ route('settings.category.manage', 'business_type') }}">
                    <span>🏪 Business / Merchant Types</span>
                    <span class="badge-count">{{ $categories->get('business_type', collect())->count() }}</span>
                </a></li>
            </ul>
        </div>

        <div class="settings-card">
            <div class="settings-header">📁 Project Configuration</div>
            <div class="settings-desc">Project type classifications</div>
            <ul class="settings-list">
                <li><a href="{{ route('settings.category.manage', 'project_type') }}">
                    <span>📌 Project Types</span>
                    <span class="badge-count">{{ $categories->get('project_type', collect())->count() }}</span>
                </a></li>
            </ul>
        </div>

        <div class="settings-card">
            <div class="settings-header">📌 Project Status Reference</div>
            <div class="settings-desc">Read-only system-defined project lifecycle states</div>
            <div class="ref-chips" style="margin-top:4px;">
                <span class="ref-chip">Planning</span>
                <span class="ref-chip" style="background:#dbeafe;border-color:#93c5fd;color:#1e40af;">Active</span>
                <span class="ref-chip" style="background:#fef3c7;border-color:#fcd34d;color:#92400e;">On Hold</span>
                <span class="ref-chip">Paused</span>
                <span class="ref-chip" style="background:#dcfce7;border-color:#86efac;color:#166534;">Completed</span>
                <span class="ref-chip" style="background:#f1f5f9;border-color:#cbd5e1;color:#475569;">Closed</span>
                <span class="ref-chip" style="background:#fee2e2;border-color:#fca5a5;color:#991b1b;">Cancelled</span>
            </div>
        </div>

    </div>
</div>

{{-- ─────────────────────────────────────────────────────── --}}
{{-- SECTION 5: System / General                            --}}
{{-- ─────────────────────────────────────────────────────── --}}
<div class="settings-section">
    <div class="settings-section-title">System</div>
    <div class="settings-grid">

        <div class="settings-card">
            <div class="settings-header">📋 System Categories (All)</div>
            <div class="settings-desc">Full list of every lookup / category type</div>
            <ul class="settings-list">
                @foreach($categoryTypes as $type => $label)
                <li><a href="{{ route('settings.category.manage', $type) }}">
                    <span>{{ $label }}</span>
                    <span class="badge-count">{{ $categories->get($type, collect())->count() }}</span>
                </a></li>
                @endforeach
            </ul>
        </div>

        <div class="settings-card">
            <div class="settings-header">⚙️ General Settings</div>
            <div class="settings-desc">Email, notifications and backup configuration</div>
            <ul class="settings-list">
                <li><a href="#" onclick="alert('Coming Soon!')">
                    <span>📧 Email Settings</span>
                    <span class="badge-soon">Soon</span>
                </a></li>
                <li><a href="#" onclick="alert('Coming Soon!')">
                    <span>🔔 Notification Rules</span>
                    <span class="badge-soon">Soon</span>
                </a></li>
                <li><a href="#" onclick="alert('Coming Soon!')">
                    <span>💾 Backup Settings</span>
                    <span class="badge-soon">Soon</span>
                </a></li>
            </ul>
        </div>

        <div class="settings-card">
            <div class="settings-header">🔍 Audit Trail</div>
            <div class="settings-desc">System activity logs and audit records</div>
            <ul class="settings-list">
                <li><a href="{{ route('audit-trail.index') }}">
                    <span>📜 View Audit Trail</span>
                    <span class="badge-manage">Open</span>
                </a></li>
            </ul>
        </div>

    </div>
</div>

@endsection
