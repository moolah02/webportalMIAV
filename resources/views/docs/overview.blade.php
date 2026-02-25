@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>›</span> Business Overview
</div>

<h1>Business Overview</h1>
<p class="subtitle">
    System summary, module features, and high-level workflows for the MIAV Dashboard.
    <span style="float:right;" class="badge badge-green">All Roles</span>
</p>

<h2>What is MIAV?</h2>
<p>
    <strong>MIAV</strong> is the central operations management platform for <strong>Revival Technologies</strong>.
    It consolidates employee management, asset tracking, field operations, client management, support ticketing,
    and business reporting into a single, role-based web portal.
</p>

<h2>Technology Stack</h2>
<table>
    <thead>
        <tr><th>Layer</th><th>Technology</th><th>Version</th></tr>
    </thead>
    <tbody>
        <tr><td>Framework</td><td>Laravel</td><td>12.x</td></tr>
        <tr><td>Language</td><td>PHP</td><td>8.2+</td></tr>
        <tr><td>Database</td><td>MySQL</td><td>8.0</td></tr>
        <tr><td>Authentication</td><td>Laravel Sanctum + Session</td><td>4.x</td></tr>
        <tr><td>Permissions</td><td>Spatie Laravel Permission</td><td>6.x</td></tr>
        <tr><td>UI Framework</td><td>Bootstrap + AdminLTE</td><td>4.6 / 3.x</td></tr>
        <tr><td>CSS Tooling</td><td>Tailwind CSS + Vite</td><td>3.x</td></tr>
        <tr><td>PDF Generation</td><td>barryvdh/laravel-dompdf</td><td>3.x</td></tr>
        <tr><td>Excel Export</td><td>Maatwebsite Excel / PhpSpreadsheet</td><td>5.x</td></tr>
        <tr><td>Server</td><td>Apache / Nginx on Ubuntu</td><td>—</td></tr>
    </tbody>
</table>

<h2>System Modules</h2>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin:16px 0 32px;">
    @php
    $modules = [
        ['icon'=>'👥','title'=>'Employee Management','desc'=>'Onboard, manage, and deactivate staff. Assign roles and departments.'],
        ['icon'=>'📦','title'=>'Asset Management','desc'=>'Track all company assets by category, serial, and assigned employee.'],
        ['icon'=>'💳','title'=>'POS Terminals','desc'=>'Full terminal lifecycle: import, deploy, track, and link to support tickets.'],
        ['icon'=>'📋','title'=>'Business Licenses','desc'=>'Track license expiry, renewal dates, and compliance status.'],
        ['icon'=>'🔧','title'=>'Field Operations','desc'=>'Job assignments, site visits, terminal deployments — tracked end-to-end.'],
        ['icon'=>'🎫','title'=>'Support Tickets','desc'=>'POS terminal and internal tickets with staged resolution and audit trail.'],
        ['icon'=>'🏗️','title'=>'Project Management','desc'=>'Create, track, and close projects with milestones and documentation.'],
        ['icon'=>'🤝','title'=>'Client Management','desc'=>'Client records, assigned terminals, SLA tiers, and dashboards.'],
        ['icon'=>'📊','title'=>'Reports & Analytics','desc'=>'System reports, custom report builder, PDF and CSV export.'],
        ['icon'=>'⚙️','title'=>'Administration','desc'=>'System settings, role management, and audit log.'],
    ];
    @endphp
    @foreach($modules as $mod)
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:18px;">
        <div style="font-size:24px;margin-bottom:8px;">{{ $mod['icon'] }}</div>
        <div style="font-weight:600;font-size:14px;color:#1e293b;margin-bottom:4px;">{{ $mod['title'] }}</div>
        <div style="font-size:13px;color:#64748b;line-height:1.5;">{{ $mod['desc'] }}</div>
    </div>
    @endforeach
</div>

<h2>User Roles Summary</h2>

<table>
    <thead>
        <tr><th>Role</th><th>Primary Users</th><th>Key Access</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-red">Super Admin</span></td>
            <td>System owner / IT director</td>
            <td>Everything — full system control</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">Admin</span></td>
            <td>Operations managers</td>
            <td>All modules except system-level config</td>
        </tr>
        <tr>
            <td><span class="badge badge-blue">Manager</span></td>
            <td>Team leads</td>
            <td>Dashboard, ops, projects, clients, reports</td>
        </tr>
        <tr>
            <td><span class="badge badge-yellow">Supervisor</span></td>
            <td>Senior technicians</td>
            <td>Field ops, ticket management, view reports</td>
        </tr>
        <tr>
            <td><span class="badge">Technician</span></td>
            <td>Field technicians</td>
            <td>Assigned jobs and tickets, own portal</td>
        </tr>
        <tr>
            <td><span class="badge">Employee</span></td>
            <td>General staff</td>
            <td>Personal dashboard and assigned tasks only</td>
        </tr>
    </tbody>
</table>

<h2>High-Level Data Flow</h2>
<pre><code>Client Onboarded
    └─▶ POS Terminals Assigned to Client
            └─▶ Terminal Deployed (Deployment Record)
                    └─▶ Terminal Issue Raised (Support Ticket)
                            └─▶ Staged Resolution Steps
                                    └─▶ Ticket Resolved ──▶ Audit Trail


Employee Hired
    └─▶ Roles Assigned (Permissions granted)
            └─▶ Jobs Assigned
                    └─▶ Site Visits Logged
                            └─▶ Reports Generated</code></pre>

<h2>Environments</h2>
<table>
    <thead>
        <tr><th>Environment</th><th>URL</th><th>Database</th><th>Purpose</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-red">Production</span></td>
            <td><code>http://51.21.252.67</code></td>
            <td><code>miav_system</code></td>
            <td>Live system — real data</td>
        </tr>
        <tr>
            <td><span class="badge badge-blue">Development</span></td>
            <td><code>http://51.21.252.67:8080</code></td>
            <td><code>miav_system_dev</code></td>
            <td>Testing &amp; new features</td>
        </tr>
    </tbody>
</table>

@endsection
