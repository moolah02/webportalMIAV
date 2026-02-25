@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>›</span> System Manual
</div>

<h1>System Manual</h1>
<p class="subtitle">
    Complete guide to the MIAV Dashboard — for Admins, Managers, and all staff.
    <span style="float:right;" class="badge badge-blue">Version 1.0 · Jan 2026</span>
</p>

{{-- TOC --}}
<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:20px 24px;margin-bottom:32px;">
    <div style="font-weight:700;font-size:13px;margin-bottom:12px;color:#1a3a5c;">📑 Table of Contents</div>
    <ol style="columns:2;column-gap:32px;list-style-position:inside;font-size:13.5px;">
        <li><a href="#getting-started" style="color:#2563a8;text-decoration:none;">Getting Started</a></li>
        <li><a href="#company-dashboard" style="color:#2563a8;text-decoration:none;">Company Dashboard</a></li>
        <li><a href="#employee-dashboard" style="color:#2563a8;text-decoration:none;">Employee Dashboard</a></li>
        <li><a href="#assets" style="color:#2563a8;text-decoration:none;">Assets Management</a></li>
        <li><a href="#field-ops" style="color:#2563a8;text-decoration:none;">Field Operations</a></li>
        <li><a href="#projects" style="color:#2563a8;text-decoration:none;">Project Management</a></li>
        <li><a href="#clients" style="color:#2563a8;text-decoration:none;">Client Management</a></li>
        <li><a href="#employees" style="color:#2563a8;text-decoration:none;">Employee Management</a></li>
        <li><a href="#technician" style="color:#2563a8;text-decoration:none;">Technician Portal</a></li>
        <li><a href="#reports" style="color:#2563a8;text-decoration:none;">Reports & Analytics</a></li>
        <li><a href="#admin" style="color:#2563a8;text-decoration:none;">Administration</a></li>
        <li><a href="#roles" style="color:#2563a8;text-decoration:none;">User Roles & Permissions</a></li>
    </ol>
</div>

<h2 id="getting-started">1. Getting Started</h2>

<h3>1.1 Logging In</h3>
<ol>
    <li>Navigate to the system URL in your browser.</li>
    <li>Enter your email address and password.</li>
    <li>Click <strong>Login</strong>.</li>
</ol>
<p>If you have forgotten your password, contact your system administrator to have it reset.</p>

<h3>1.2 Navigation</h3>
<p>The system uses a left sidebar for navigation. Each section can be expanded or collapsed. The sidebar contains:</p>
<ul>
    <li><strong>Company Dashboard</strong> — System overview and metrics</li>
    <li><strong>Employee Dashboard</strong> — Personal workspace</li>
    <li><strong>Assets Management</strong> — Asset inventory, POS terminals, business licenses</li>
    <li><strong>Field Operations</strong> — Deployment, jobs, visits, tickets</li>
    <li><strong>Project Management</strong> — Projects, closure, reports</li>
    <li><strong>Client Management</strong> — Client records and dashboards</li>
    <li><strong>Employee Management</strong> — Staff and roles</li>
    <li><strong>Technician Portal</strong> — Technician-specific views</li>
    <li><strong>Administration</strong> — System settings</li>
    <li><strong>Reports &amp; Analytics</strong> — Reporting tools</li>
    <li><strong>My Account</strong> — Profile and sign out</li>
</ul>

<h3>1.3 System Conventions</h3>
<ul>
    <li><strong>Required fields</strong> are marked with a red asterisk (*).</li>
    <li><strong>Status badges</strong> are color-coded: green (active/complete), yellow (pending), red (urgent/overdue), grey (inactive).</li>
    <li><strong>Search and filter</strong> controls appear at the top of list pages.</li>
    <li><strong>Export</strong> options (CSV, PDF) are available on most list pages.</li>
</ul>

<h2 id="company-dashboard">2. Company Dashboard</h2>

<p>The Company Dashboard is the main overview screen available to Admins and Managers. It displays:</p>
<ul>
    <li>Total active employees</li>
    <li>Open support tickets (POS Terminal & Internal)</li>
    <li>Assets count and status summary</li>
    <li>Recent activities timeline</li>
    <li>Quick-access buttons to common actions</li>
</ul>
<div class="callout">
    <strong>Note:</strong> The metrics shown on the Company Dashboard are refreshed in real time on each page load.
</div>

<h2 id="employee-dashboard">3. Employee Dashboard</h2>

<p>The Employee Dashboard is a personal workspace showing:</p>
<ul>
    <li>Assigned tickets and their statuses</li>
    <li>Upcoming job assignments</li>
    <li>Recent field visits</li>
    <li>Personal notifications</li>
</ul>

<h2 id="assets">4. Assets Management</h2>

<h3>4.1 Asset Inventory</h3>
<p>Track all company assets by category, serial number, assigned employee, and status.</p>

<h3>4.2 POS Terminals</h3>
<p>Manage POS terminal inventory including:</p>
<ul>
    <li>Terminal details (serial, model, merchant)</li>
    <li>Deployment status and history</li>
    <li>Import terminals from CSV/Excel</li>
    <li>Terminal-linked support tickets</li>
</ul>

<h3>4.3 Business Licenses</h3>
<p>Track all company business licenses, expiry dates, and renewal alerts.</p>

<h2 id="field-ops">5. Field Operations</h2>

<h3>5.1 Terminal Deployments</h3>
<p>Log and track POS terminal deployments to client sites.</p>

<h3>5.2 Job Assignments</h3>
<p>Create and assign field jobs to technicians with priority levels and deadlines.</p>

<h3>5.3 Site Visits</h3>
<p>Log technician site visits with notes, photos, and outcome records.</p>

<h3>5.4 Support Tickets</h3>
<p>Create and manage support tickets. Two types:</p>
<ul>
    <li><span class="badge badge-blue">POS Terminal</span> — Linked to a specific terminal</li>
    <li><span class="badge badge-purple">Internal</span> — General internal support requests</li>
</ul>
<p>Two assignment types:</p>
<ul>
    <li><span class="badge badge-green">Public</span> — Any eligible employee can pick up</li>
    <li><span class="badge badge-yellow">Direct</span> — Assigned to a specific employee</li>
</ul>

<h2 id="projects">6. Project Management</h2>
<p>Create, track, and close projects. Attach reports and milestones. Generate project closure summaries.</p>

<h2 id="clients">7. Client Management</h2>
<p>Manage all client records including contact details, assigned terminals, SLA tiers, and client-facing dashboards.</p>

<h2 id="employees">8. Employee Management</h2>

<h3>8.1 Onboarding a New Employee</h3>
<ol>
    <li>Navigate to <strong>Employee Management → Employees → Onboard New Employee</strong>.</li>
    <li>Fill in: First Name, Last Name, Email, Password, Primary Role, Department, Hire Date, Status.</li>
    <li>Optionally assign Additional Roles.</li>
    <li>Click <strong>Create Employee</strong>.</li>
</ol>

<h3>8.2 Roles &amp; Permissions</h3>
<p>See <a href="#roles">Section 12</a> for full role breakdown.</p>

<h2 id="technician">9. Technician Portal</h2>
<p>A dedicated view for technicians showing assigned jobs, active tickets, and field visit history. Accessible from the sidebar under <em>Technician Portal</em>.</p>

<h2 id="reports">10. Reports &amp; Analytics</h2>
<p>The reporting module provides:</p>
<ul>
    <li>Predefined system reports (employee activity, ticket summary, asset audit)</li>
    <li>Custom Report Builder with column selector and filters</li>
    <li>Export to PDF and CSV</li>
</ul>

<h2 id="admin">11. Administration</h2>
<ul>
    <li><strong>Settings</strong> — System-wide configuration</li>
    <li><strong>Roles Management</strong> — Create and edit permission roles</li>
    <li><strong>Audit Log</strong> — View all system-level events</li>
</ul>

<h2 id="roles">12. User Roles &amp; Permissions</h2>

<table>
    <thead>
        <tr>
            <th>Role</th>
            <th>Badge</th>
            <th>Access Level</th>
            <th>Key Permissions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Super Admin</td>
            <td><span class="badge badge-red">Super Admin</span></td>
            <td>Full system</td>
            <td>All modules, user management, roles, settings</td>
        </tr>
        <tr>
            <td>Administrator</td>
            <td><span class="badge badge-green">Admin</span></td>
            <td>High</td>
            <td>Most modules, employee management, reports</td>
        </tr>
        <tr>
            <td>Manager</td>
            <td><span class="badge badge-blue">Manager</span></td>
            <td>Medium-High</td>
            <td>Dashboard, jobs, visits, projects, tickets</td>
        </tr>
        <tr>
            <td>Supervisor</td>
            <td><span class="badge badge-yellow">Supervisor</span></td>
            <td>Medium</td>
            <td>Field ops, ticket management, view reports</td>
        </tr>
        <tr>
            <td>Technician</td>
            <td><span class="badge badge-blue">Technician</span></td>
            <td>Limited</td>
            <td>Assigned jobs, tickets, own visits</td>
        </tr>
        <tr>
            <td>Employee</td>
            <td><span class="badge">Employee</span></td>
            <td>Basic</td>
            <td>Personal dashboard, own assigned work</td>
        </tr>
    </tbody>
</table>

@endsection
