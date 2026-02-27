@extends('docs.layout')
@section('content')
@if(!empty(trim($page->content ?? '')))
    {!! $page->content !!}
@else

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>&rsaquo;</span> System Manual
</div>

<h1>System Manual</h1>
<p class="subtitle">
    Complete guide to the MIAV Dashboard &mdash; for all staff, managers, and admins.
    <span style="float:right;" class="badge badge-blue">Version 1.0 &middot; Feb 2026</span>
</p>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:20px 24px;margin-bottom:32px;">
    <div style="font-weight:700;font-size:13px;margin-bottom:12px;color:#0c4a6e;">&#128209; Table of Contents</div>
    <ol style="columns:2;column-gap:32px;list-style-position:inside;font-size:13.5px;color:#0369a1;">
        <li><a href="#getting-started" style="color:#0369a1;text-decoration:none;">Getting Started</a></li>
        <li><a href="#company-dashboard" style="color:#0369a1;text-decoration:none;">Company Dashboard</a></li>
        <li><a href="#employee-dashboard" style="color:#0369a1;text-decoration:none;">Employee Dashboard</a></li>
        <li><a href="#assets" style="color:#0369a1;text-decoration:none;">Assets Management</a></li>
        <li><a href="#field-ops" style="color:#0369a1;text-decoration:none;">Field Operations</a></li>
        <li><a href="#projects" style="color:#0369a1;text-decoration:none;">Project Management</a></li>
        <li><a href="#clients" style="color:#0369a1;text-decoration:none;">Client Management</a></li>
        <li><a href="#employees" style="color:#0369a1;text-decoration:none;">Employee Management</a></li>
        <li><a href="#technician" style="color:#0369a1;text-decoration:none;">Technician Portal</a></li>
        <li><a href="#tickets" style="color:#0369a1;text-decoration:none;">Ticket System</a></li>
        <li><a href="#staged" style="color:#0369a1;text-decoration:none;">Staged Resolution</a></li>
        <li><a href="#reports" style="color:#0369a1;text-decoration:none;">Reports &amp; Analytics</a></li>
        <li><a href="#admin" style="color:#0369a1;text-decoration:none;">Administration</a></li>
        <li><a href="#roles" style="color:#0369a1;text-decoration:none;">User Roles &amp; Permissions</a></li>
    </ol>
</div>

<h2 id="getting-started">1. Getting Started</h2>

<h3>1.1 Logging In</h3>
<ol>
    <li>Navigate to the system URL in your browser: <code>http://51.21.252.67</code></li>
    <li>Enter your assigned email address and password.</li>
    <li>Click <strong>Login</strong>.</li>
</ol>
<p>If you have forgotten your password, contact your system administrator to have it reset. Passwords are managed by the admin &mdash; there is no self-service reset on this system.</p>

<h3>1.2 Navigation</h3>
<p>The system uses a left sidebar for navigation. Each section can be expanded or collapsed:</p>
<ul>
    <li><strong>Company Dashboard</strong> &mdash; System overview and metrics</li>
    <li><strong>Employee Dashboard</strong> &mdash; Personal workspace</li>
    <li><strong>Assets Management</strong> &mdash; Asset inventory, POS terminals, business licenses</li>
    <li><strong>Field Operations</strong> &mdash; Deployments, jobs, visits, tickets</li>
    <li><strong>Project Management</strong> &mdash; Projects, closure, reports</li>
    <li><strong>Client Management</strong> &mdash; Client records and dashboards</li>
    <li><strong>Employee Management</strong> &mdash; Staff and roles</li>
    <li><strong>Technician Portal</strong> &mdash; Technician-specific views</li>
    <li><strong>Administration</strong> &mdash; System settings</li>
    <li><strong>Reports &amp; Analytics</strong> &mdash; Reporting tools</li>
    <li><strong>My Account</strong> &mdash; Profile and sign out</li>
</ul>

<h3>1.3 System Conventions</h3>
<ul>
    <li><strong>Required fields</strong> are marked with a red asterisk (*).</li>
    <li><strong>Status badges</strong> are colour-coded: green (active/complete), yellow (pending), red (urgent/overdue), grey (inactive).</li>
    <li><strong>Search and filter</strong> controls appear at the top of list pages.</li>
    <li><strong>Export</strong> options (CSV, PDF) are available on most list pages.</li>
</ul>

<h2 id="company-dashboard">2. Company Dashboard</h2>

<p>The Company Dashboard is the main overview screen available to Admins and Managers. It displays:</p>
<ul>
    <li>Total active employees</li>
    <li>Open support tickets (POS Terminal &amp; Internal)</li>
    <li>Assets count and status summary</li>
    <li>Recent activities timeline</li>
    <li>Quick-access buttons to common actions</li>
</ul>
<div class="callout">
    <strong>Note:</strong> Dashboard metrics are refreshed in real time on each page load.
</div>

<h2 id="employee-dashboard">3. Employee Dashboard</h2>

<p>The Employee Dashboard is each staff member&rsquo;s personal workspace showing:</p>
<ul>
    <li>Assigned tickets and their current statuses</li>
    <li>Upcoming job assignments</li>
    <li>Recent field visits</li>
    <li>Personal notifications</li>
</ul>

<h2 id="assets">4. Assets Management</h2>

<h3>4.1 Asset Inventory</h3>
<p>Track all company assets by category, serial number, assigned employee, and status. Assets can be bulk-imported via Excel.</p>

<h3>4.2 POS Terminals</h3>
<p>Manage POS terminal inventory including:</p>
<ul>
    <li>Terminal details (serial number, model, merchant name)</li>
    <li>Deployment status and full deployment history</li>
    <li>Import terminals from CSV/Excel</li>
    <li>Terminal-linked support tickets</li>
</ul>

<h3>4.3 Business Licences</h3>
<p>Track all company business licences, expiry dates, and renewal alerts. The system flags licences due for renewal within 30 days.</p>

<h2 id="field-ops">5. Field Operations</h2>

<h3>5.1 Terminal Deployments</h3>
<p>Log and track POS terminal deployments to client sites. Each deployment records: terminal, client, assigned technician, deployment date, and outcome.</p>

<h3>5.2 Job Assignments</h3>
<p>Create and assign field jobs to technicians with priority levels and deadlines. Jobs can be linked to a client, terminal, or project.</p>

<h3>5.3 Site Visits</h3>
<p>Log technician site visits with notes, photos, and outcome records. Visits are tied to the assigned employee and date/time.</p>

<h2 id="projects">6. Project Management</h2>
<p>Create, track, and close projects across the organisation:</p>
<ol>
    <li>Navigate to <strong>Project Management &rarr; New Project</strong>.</li>
    <li>Enter project name, description, client, assigned manager, and target dates.</li>
    <li>Add team members and milestones.</li>
    <li>Link field operations (jobs, deployments) to the project as work progresses.</li>
    <li>When complete, generate a <strong>Project Closure Report</strong> from the project detail screen.</li>
</ol>
<p>See the <a href="{{ url('/docs/projects') }}">Project Flow Guide</a> for a full step-by-step walkthrough.</p>

<h2 id="clients">7. Client Management</h2>
<p>Manage all client records including:</p>
<ul>
    <li>Contact details and business information</li>
    <li>Assigned POS terminals</li>
    <li>SLA tier settings</li>
    <li>Client-facing dashboard view</li>
    <li>Linked tickets and project history</li>
</ul>

<h2 id="employees">8. Employee Management</h2>

<h3>8.1 Onboarding a New Employee</h3>
<ol>
    <li>Navigate to <strong>Employee Management &rarr; Employees &rarr; Onboard New Employee</strong>.</li>
    <li>Fill in: First Name, Last Name, Email, Password, Primary Role, Department, Hire Date, Status.</li>
    <li>Optionally assign Additional Roles for multi-department access.</li>
    <li>Click <strong>Create Employee</strong>. The employee will receive login credentials.</li>
</ol>
<div class="callout warning">
    <strong>Important:</strong> Passwords are set by the admin at creation. Advise the employee to keep their password secure.
</div>

<h3>8.2 Editing an Employee</h3>
<ol>
    <li>Go to <strong>Employee Management &rarr; Employees</strong> and click the employee&rsquo;s name.</li>
    <li>Click <strong>Edit</strong> to update any details.</li>
    <li>To deactivate, set Status to <strong>Inactive</strong>. The employee will no longer be able to log in.</li>
</ol>

<h3>8.3 Roles &amp; Permissions</h3>
<p>See <a href="#roles">Section 14</a> for the full role breakdown and permission matrix.</p>

<h2 id="technician">9. Technician Portal</h2>
<p>A dedicated view for technicians accessible from the sidebar under <em>Technician Portal</em>. It shows:</p>
<ul>
    <li>Currently assigned jobs and their priority</li>
    <li>Active tickets assigned to the technician</li>
    <li>Field visit history</li>
    <li>Staged resolution steps awaiting action</li>
</ul>
<div class="callout">
    <strong>Tip:</strong> Use the <a href="{{ url('/docs/mobile') }}">Mobile App Guide</a> for instructions on completing work from the mobile app in the field.
</div>

<h2 id="tickets">10. Ticket System</h2>

<h3>10.1 Ticket Types</h3>
<p>The system supports two ticket types:</p>
<ul>
    <li><span class="badge badge-blue">POS Terminal</span> &mdash; Linked to a specific POS terminal at a client site. Creates a traceable chain from terminal &rarr; client &rarr; technician.</li>
    <li><span class="badge badge-purple">Internal</span> &mdash; General internal support requests not tied to a terminal.</li>
</ul>

<h3>10.2 Assignment Types</h3>
<ul>
    <li><span class="badge badge-green">Public</span> &mdash; The ticket is visible to all eligible employees. Any qualified staff member can pick it up.</li>
    <li><span class="badge badge-yellow">Direct</span> &mdash; The ticket is assigned directly to a named employee or technician.</li>
</ul>

<h3>10.3 Creating a Ticket</h3>
<ol>
    <li>Navigate to <strong>Field Operations &rarr; Support Tickets &rarr; Create Ticket</strong>.</li>
    <li>Select <strong>Ticket Type</strong> (POS Terminal or Internal).</li>
    <li>If POS Terminal, select the terminal from the dropdown (auto-populates client details).</li>
    <li>Enter a <strong>Subject</strong> and detailed <strong>Description</strong> of the issue.</li>
    <li>Set <strong>Priority</strong>: Low, Medium, High, or Urgent.</li>
    <li>Choose <strong>Assignment Type</strong>: Public or Direct. If Direct, select the employee.</li>
    <li>Click <strong>Submit Ticket</strong>.</li>
</ol>

<h3>10.4 Ticket Statuses</h3>
<table>
    <thead><tr><th>Status</th><th>Meaning</th></tr></thead>
    <tbody>
        <tr><td><span class="badge badge-gray">Open</span></td><td>Newly created, awaiting pick-up or assignment</td></tr>
        <tr><td><span class="badge badge-yellow">In Progress</span></td><td>Actively being worked on by an assignee</td></tr>
        <tr><td><span class="badge badge-blue">Pending</span></td><td>Awaiting information or a dependency</td></tr>
        <tr><td><span class="badge badge-green">Resolved</span></td><td>Issue fixed, pending confirmation</td></tr>
        <tr><td><span class="badge">Closed</span></td><td>Fully closed and archived</td></tr>
        <tr><td><span class="badge badge-red">Escalated</span></td><td>Flagged for senior or management attention</td></tr>
    </tbody>
</table>

<h3>10.5 Managing Tickets</h3>
<ul>
    <li><strong>Filter</strong> tickets by status, type, priority, date, or assignee using the filter panel.</li>
    <li><strong>Search</strong> by ticket number, subject, or client name.</li>
    <li><strong>Add Comments</strong> on any ticket to log progress notes or communications.</li>
    <li><strong>Reassign</strong> a ticket using the Edit function (Manager/Admin only).</li>
    <li><strong>Escalate</strong> a ticket from the ticket detail screen to flag it for senior review.</li>
</ul>

<h2 id="staged">11. Staged Resolution</h2>

<h3>11.1 Overview</h3>
<p>Staged Resolution allows a ticket to be broken into multiple defined steps, each assigned to an employee. This is used when resolving a single issue requires actions by different people or departments in a specific order.</p>

<h3>11.2 Creating Stages</h3>
<ol>
    <li>Open a ticket and click <strong>Add Staged Resolution</strong>.</li>
    <li>Click <strong>Add Step</strong> for each stage of the resolution.</li>
    <li>For each step, enter: Step Name, Description, Assigned Employee, and Estimated Completion Date.</li>
    <li>Steps are automatically numbered and ordered.</li>
    <li>Click <strong>Save Stages</strong>.</li>
</ol>

<h3>11.3 Completing a Stage</h3>
<ol>
    <li>The assigned employee navigates to the ticket and locates their step.</li>
    <li>They complete the required work.</li>
    <li>Click <strong>Mark Step Complete</strong> and add a resolution note.</li>
    <li>The next step is automatically activated and the next assignee is notified.</li>
</ol>

<h3>11.4 Transfers &amp; Reassignment</h3>
<p>A Manager or Admin can reassign any pending step to a different employee. A transfer reason must be recorded and is stored in the audit trail.</p>

<h3>11.5 Audit Trail</h3>
<p>Every action on a staged resolution &mdash; creation, step completion, transfer, escalation &mdash; is logged with employee name, timestamp, and notes. The full trail is visible at the bottom of the ticket detail page.</p>

<h2 id="reports">12. Reports &amp; Analytics</h2>
<p>The reporting module provides:</p>
<ul>
    <li>Predefined system reports (employee activity, ticket summary, asset audit)</li>
    <li>Custom Report Builder with column selector, grouping, and date filters</li>
    <li>Export to PDF and CSV</li>
</ul>
<p>See the <a href="{{ url('/docs/reports') }}">Reports Manual</a> for detailed step-by-step instructions for each report type.</p>

<h2 id="admin">13. Administration</h2>
<ul>
    <li><strong>Settings</strong> &mdash; System-wide configuration including company name, SLA defaults, and notification preferences</li>
    <li><strong>Roles Management</strong> &mdash; Create, edit, and assign permission roles</li>
    <li><strong>Audit Log</strong> &mdash; View all system-level events with user, action, and timestamp</li>
    <li><strong>Employee Accounts</strong> &mdash; Password resets and account status management</li>
</ul>
<div class="callout warning">
    <strong>Admin Only:</strong> Access to the Administration section is restricted to Administrator and Super Admin roles.
</div>

<h2 id="roles">14. User Roles &amp; Permissions</h2>

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
            <td>All modules, user management, roles, settings, audit log</td>
        </tr>
        <tr>
            <td>Administrator</td>
            <td><span class="badge badge-green">Admin</span></td>
            <td>High</td>
            <td>Most modules, employee management, reports, ticket management</td>
        </tr>
        <tr>
            <td>Manager</td>
            <td><span class="badge badge-blue">Manager</span></td>
            <td>Medium-High</td>
            <td>Dashboard, jobs, visits, projects, tickets, staged resolution</td>
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
            <td>Assigned jobs, tickets, own visits, staged steps</td>
        </tr>
        <tr>
            <td>Employee</td>
            <td><span class="badge badge-gray">Employee</span></td>
            <td>Basic</td>
            <td>Personal dashboard, own assigned work items</td>
        </tr>
    </tbody>
</table>

@endif
@endsection