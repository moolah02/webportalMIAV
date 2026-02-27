@extends('docs.layout')
@section('content')
@if(!empty(trim($page->content ?? '')))
    {!! $page->content !!}
@else

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>&rsaquo;</span> SRS Document
</div>

<h1>Software Requirements Specification</h1>
<p class="subtitle">
    MIAV Dashboard &mdash; Formal requirements, functional specifications, and system constraints.
    <span style="float:right;" class="badge badge-blue">Version 1.0 &middot; Feb 2026</span>
</p>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:20px 24px;margin-bottom:32px;">
    <div style="font-weight:700;font-size:13px;margin-bottom:12px;color:#0c4a6e;">&#128209; Contents</div>
    <ol style="columns:2;column-gap:32px;list-style-position:inside;font-size:13.5px;">
        <li><a href="#introduction" style="color:#0369a1;text-decoration:none;">Introduction &amp; Purpose</a></li>
        <li><a href="#scope" style="color:#0369a1;text-decoration:none;">Scope</a></li>
        <li><a href="#stakeholders" style="color:#0369a1;text-decoration:none;">Stakeholders &amp; Users</a></li>
        <li><a href="#functional" style="color:#0369a1;text-decoration:none;">Functional Requirements</a></li>
        <li><a href="#non-functional" style="color:#0369a1;text-decoration:none;">Non-Functional Requirements</a></li>
        <li><a href="#user-stories" style="color:#0369a1;text-decoration:none;">User Stories</a></li>
        <li><a href="#data-models" style="color:#0369a1;text-decoration:none;">Data Models</a></li>
        <li><a href="#constraints" style="color:#0369a1;text-decoration:none;">Constraints &amp; Assumptions</a></li>
        <li><a href="#interfaces" style="color:#0369a1;text-decoration:none;">System Interfaces</a></li>
    </ol>
</div>

<h2 id="introduction">1. Introduction &amp; Purpose</h2>
<p>This Software Requirements Specification (SRS) defines the complete functional and non-functional requirements for the <strong>MIAV Dashboard</strong>, a web-based enterprise management system developed for Revival Technologies.</p>
<p>The system provides a centralised platform for managing employees, assets (including POS terminals), field operations, client relationships, project workflows, support ticketing, and organisational reporting. It is intended for internal use by all staff levels, from field technicians to senior management.</p>

<h3>1.1 Document Conventions</h3>
<ul>
    <li><strong>SHALL</strong>  Mandatory requirement</li>
    <li><strong>SHOULD</strong>  Recommended requirement</li>
    <li><strong>MAY</strong>  Optional requirement</li>
</ul>

<h2 id="scope">2. Scope</h2>
<p>The MIAV Dashboard SHALL provide the following core capabilities:</p>
<ul>
    <li>Employee lifecycle management (onboarding, roles, deactivation)</li>
    <li>Asset and POS terminal inventory management</li>
    <li>Field operations including deployments, job assignments, and site visits</li>
    <li>Support ticket creation, assignment, escalation, and resolution</li>
    <li>Multi-stage ticket resolution with audit trail</li>
    <li>Project management with milestone tracking and closure reports</li>
    <li>Client management with SLA tracking</li>
    <li>Role-based access control (RBAC) with granular permissions</li>
    <li>System-wide reporting and analytics with export capabilities</li>
    <li>Mobile-compatible interface for field technicians</li>
</ul>
<p><strong>Out of scope:</strong> Payment processing, external customer-facing portals, and third-party ERP integrations are not included in version 1.0.</p>

<h2 id="stakeholders">3. Stakeholders &amp; Users</h2>

<table>
    <thead>
        <tr><th>Stakeholder</th><th>Role</th><th>Primary Needs</th></tr>
    </thead>
    <tbody>
        <tr><td>Revival Technologies Management</td><td>System Owner</td><td>Oversight, reporting, KPI visibility</td></tr>
        <tr><td>System Administrator</td><td>Super Admin / Admin</td><td>Full system control, user management, configuration</td></tr>
        <tr><td>Operations Manager</td><td>Manager</td><td>Field ops oversight, project management, ticket monitoring</td></tr>
        <tr><td>Field Technician</td><td>Technician</td><td>Job assignments, ticket resolution, visit logging</td></tr>
        <tr><td>Support Staff</td><td>Employee</td><td>Assigned work items, personal dashboard</td></tr>
        <tr><td>Client (indirect)</td><td>External</td><td>Accurate SLA delivery, terminal uptime</td></tr>
    </tbody>
</table>

<h2 id="functional">4. Functional Requirements</h2>

<h3>4.1 Authentication &amp; Access Control</h3>
<ul>
    <li>FR-01: The system SHALL require email and password authentication for all users.</li>
    <li>FR-02: The system SHALL enforce role-based access control using the Spatie Permissions library.</li>
    <li>FR-03: Admins SHALL be able to create, edit, deactivate, and reset passwords for all user accounts.</li>
    <li>FR-04: Inactive users SHALL be denied login access.</li>
    <li>FR-05: Session timeout SHALL occur after a configurable period of inactivity.</li>
</ul>

<h3>4.2 Employee Management</h3>
<ul>
    <li>FR-10: The system SHALL allow admins to onboard new employees with name, email, role, department, and hire date.</li>
    <li>FR-11: Employees SHALL be assignable to one primary role and zero or more supplementary roles.</li>
    <li>FR-12: The system SHALL display employee profiles with assignment history, active tickets, and visit logs.</li>
    <li>FR-13: Employee status (active/inactive) SHALL gate system login.</li>
</ul>

<h3>4.3 Asset &amp; Terminal Management</h3>
<ul>
    <li>FR-20: The system SHALL maintain an inventory of all company assets with serial numbers and status.</li>
    <li>FR-21: POS terminals SHALL be tracked with merchant linkage, deployment history, and associated tickets.</li>
    <li>FR-22: Assets and terminals SHALL be importable via CSV/Excel upload.</li>
    <li>FR-23: Business licences SHALL be tracked with expiry alerts at 30 days.</li>
</ul>

<h3>4.4 Field Operations</h3>
<ul>
    <li>FR-30: The system SHALL support creating terminal deployments linked to clients and technicians.</li>
    <li>FR-31: Job assignments SHALL have priority levels (Low, Medium, High, Urgent) and due dates.</li>
    <li>FR-32: Site visits SHALL be logged with technician, date/time, location, notes, and outcome.</li>
</ul>

<h3>4.5 Ticket System</h3>
<ul>
    <li>FR-40: The system SHALL support two ticket types: POS Terminal and Internal.</li>
    <li>FR-41: Tickets SHALL support two assignment modes: Public (any eligible staff) and Direct (named employee).</li>
    <li>FR-42: Tickets SHALL have statuses: Open, In Progress, Pending, Resolved, Closed, Escalated.</li>
    <li>FR-43: Tickets SHALL support priority levels and SLA timers based on client tier.</li>
    <li>FR-44: All ticket actions (creation, update, comment, reassign) SHALL be logged with timestamp and user.</li>
    <li>FR-45: Managers and Admins SHALL be able to escalate and reassign any ticket.</li>
</ul>

<h3>4.6 Staged Resolution</h3>
<ul>
    <li>FR-50: A ticket MAY have a staged resolution plan with one or more ordered steps.</li>
    <li>FR-51: Each step SHALL have a name, description, assigned employee, and estimated completion date.</li>
    <li>FR-52: Steps SHALL be completed sequentially; the next step activates only after the previous is marked complete.</li>
    <li>FR-53: Step transfers SHALL require a reason and SHALL be recorded in the audit trail.</li>
    <li>FR-54: The full staged resolution audit trail SHALL be visible on the ticket detail page.</li>
</ul>

<h3>4.7 Projects</h3>
<ul>
    <li>FR-60: Projects SHALL be linked to a client and managed by a designated project manager.</li>
    <li>FR-61: Projects SHALL support milestones, team members, and linked field operations.</li>
    <li>FR-62: Completed projects SHALL generate a closure report exportable to PDF.</li>
</ul>

<h3>4.8 Reporting</h3>
<ul>
    <li>FR-70: The system SHALL provide predefined reports: employee activity, ticket summary, asset audit, project status.</li>
    <li>FR-71: A custom report builder SHALL allow column selection, grouping, and date range filtering.</li>
    <li>FR-72: Reports SHALL be exportable as PDF and CSV.</li>
</ul>

<h2 id="non-functional">5. Non-Functional Requirements</h2>

<table>
    <thead><tr><th>ID</th><th>Category</th><th>Requirement</th></tr></thead>
    <tbody>
        <tr><td>NFR-01</td><td>Performance</td><td>Page load time SHALL be under 3 seconds on a standard connection</td></tr>
        <tr><td>NFR-02</td><td>Security</td><td>All passwords SHALL be hashed using bcrypt. HTTPS SHALL be enforced in production.</td></tr>
        <tr><td>NFR-03</td><td>Availability</td><td>System SHOULD target 99.5% uptime during business hours.</td></tr>
        <tr><td>NFR-04</td><td>Scalability</td><td>The system SHOULD support up to 500 concurrent users without degradation.</td></tr>
        <tr><td>NFR-05</td><td>Usability</td><td>The interface SHALL be operable on modern browsers (Chrome, Firefox, Edge, Safari) without plugins.</td></tr>
        <tr><td>NFR-06</td><td>Mobile</td><td>The system SHALL be responsive and usable on mobile devices for technician workflows.</td></tr>
        <tr><td>NFR-07</td><td>Auditability</td><td>All data-modifying actions SHALL be logged with user, action, and timestamp.</td></tr>
        <tr><td>NFR-08</td><td>Backups</td><td>Database backups SHOULD be performed daily and retained for 30 days.</td></tr>
    </tbody>
</table>

<h2 id="user-stories">6. User Stories</h2>

<h3>6.1 Administrator</h3>
<ul>
    <li>As an Admin, I want to create and manage employee accounts so that staff can access the system with appropriate permissions.</li>
    <li>As an Admin, I want to view the audit log so that I can investigate any suspicious or incorrect actions.</li>
    <li>As an Admin, I want to configure system-wide settings so that the system matches company policies.</li>
</ul>

<h3>6.2 Operations Manager</h3>
<ul>
    <li>As a Manager, I want to create and assign job tasks to technicians so that field work is organised and tracked.</li>
    <li>As a Manager, I want to monitor all open tickets so that no issue goes unresolved beyond its SLA.</li>
    <li>As a Manager, I want to generate reports so that I can present operational KPIs to senior management.</li>
    <li>As a Manager, I want to create staged resolution plans so that complex multi-person issues are resolved in an orderly manner.</li>
</ul>

<h3>6.3 Technician</h3>
<ul>
    <li>As a Technician, I want to see my assigned jobs and tickets in one place so that I can prioritise my workday.</li>
    <li>As a Technician, I want to log site visit details from my phone so that records are captured while I&rsquo;m in the field.</li>
    <li>As a Technician, I want to mark my staged resolution step as complete so that the next person in the chain is notified.</li>
</ul>

<h3>6.4 Employee</h3>
<ul>
    <li>As an Employee, I want to see my dashboard so that I know what work is assigned to me today.</li>
    <li>As an Employee, I want to add comments to a ticket so that I can communicate progress without phone calls.</li>
</ul>

<h2 id="data-models">7. Data Models (Key Entities)</h2>

<table>
    <thead><tr><th>Entity</th><th>Key Fields</th><th>Relationships</th></tr></thead>
    <tbody>
        <tr><td><strong>Employee</strong></td><td>id, name, email, role, department, status, hire_date</td><td>Has many: tickets, jobs, visits, staged steps</td></tr>
        <tr><td><strong>Asset</strong></td><td>id, type, serial_number, status, assigned_employee_id</td><td>Belongs to: employee; Has many: deployments</td></tr>
        <tr><td><strong>POS Terminal</strong></td><td>id, serial, model, merchant, deployment_status, client_id</td><td>Belongs to: client; Has many: tickets, deployments</td></tr>
        <tr><td><strong>Ticket</strong></td><td>id, type, subject, description, status, priority, assignment_type, assigned_to</td><td>May have: staged_resolution; belongs to: terminal/client</td></tr>
        <tr><td><strong>StagedResolution</strong></td><td>id, ticket_id, steps (ordered), current_step</td><td>Belongs to: ticket; Has many: steps</td></tr>
        <tr><td><strong>Project</strong></td><td>id, name, client_id, manager_id, status, start_date, end_date</td><td>Has many: milestones, team members, field ops</td></tr>
        <tr><td><strong>Client</strong></td><td>id, name, contact, sla_tier, address</td><td>Has many: terminals, projects, tickets</td></tr>
        <tr><td><strong>Report</strong></td><td>id, type, user_id, parameters, generated_at</td><td>Belongs to: user</td></tr>
    </tbody>
</table>

<h2 id="constraints">8. Constraints &amp; Assumptions</h2>
<ul>
    <li>The system is built on <strong>Laravel 11</strong> with PHP 8.2 and MySQL 8.0.</li>
    <li>Hosting is on AWS EC2 Ubuntu 22.04 LTS.</li>
    <li>Role and permission management uses the <strong>Spatie Laravel Permission</strong> package.</li>
    <li>The admin UI uses <strong>AdminLTE 3</strong> with Bootstrap 4.</li>
    <li>Internet access is required for all users; there is no offline mode in version 1.0.</li>
    <li>It is assumed that all users have a valid email address for account creation.</li>
    <li>The system will be used in English; multi-language support is not in scope for version 1.0.</li>
</ul>

<h2 id="interfaces">9. System Interfaces</h2>

<h3>9.1 User Interface</h3>
<p>Web-based UI built with AdminLTE, Bootstrap 4, and Blade templating. Responsive for desktop and mobile browsers.</p>

<h3>9.2 Mobile Interface</h3>
<p>A dedicated mobile-optimised view is available for technicians. See the <a href="{{ url('/docs/mobile') }}">Mobile App Guide</a> for details.</p>

<h3>9.3 Database Interface</h3>
<p>MySQL 8.0 via Laravel Eloquent ORM. All migrations are version-controlled.</p>

<h3>9.4 Export Interface</h3>
<p>Reports are exported via the <strong>maatwebsite/excel</strong> and <strong>barryvdh/laravel-dompdf</strong> packages for CSV/Excel and PDF respectively.</p>

<h3>9.5 API</h3>
<p>A RESTful API is available for integration purposes. See the <a href="{{ url('/docs/api') }}">API Documentation</a> for endpoint reference.</p>

@endif
@endsection