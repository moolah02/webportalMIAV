@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>›</span> Testing Guide
</div>

<h1>Testing Guide</h1>
<p class="subtitle">
    QA checklist for all major system features. Follow these test cases to verify functionality end-to-end.
    <span style="float:right;" class="badge badge-green">QA Document</span>
</p>

<div class="callout">
    <strong>Prerequisites:</strong>
    Production URL: <code>http://51.21.252.67</code> &nbsp;|&nbsp;
    Dev URL: <code>http://51.21.252.67:8080</code> &nbsp;|&nbsp;
    Use your assigned employee credentials. Browser: Chrome, Firefox, or Edge (latest).
</div>

<h2>1. Authentication &amp; Login Tests</h2>

<h3>Test 1.1 — Valid Login</h3>
<ol>
    <li>Navigate to <code>/login</code>.</li>
    <li>Enter a valid email and password.</li>
    <li>Click <strong>Login</strong>.</li>
</ol>
<p><strong>Expected:</strong> Logged in, redirected to dashboard, user name shown in header.</p>

<h3>Test 1.2 — Invalid Login</h3>
<ol>
    <li>Navigate to <code>/login</code>.</li>
    <li>Enter an incorrect password.</li>
</ol>
<p><strong>Expected:</strong> Error message displayed, not logged in.</p>

<h3>Test 1.3 — Inactive Account</h3>
<ol>
    <li>Deactivate an employee in the admin panel.</li>
    <li>Attempt to log in as that employee.</li>
</ol>
<p><strong>Expected:</strong> Login blocked, "Your account has been deactivated" message shown.</p>

<h2>2. Employee Management Tests</h2>

<h3>Test 2.1 — View Employees List</h3>
<ol>
    <li>Log in as Admin or Manager.</li>
    <li>Navigate to <strong>Employee Management → Employees</strong>.</li>
</ol>
<p><strong>Expected:</strong> List shown with Name, Email, Department, Status, and Role badges. Multiple roles displayed for multi-role employees.</p>

<h3>Test 2.2 — Create New Employee</h3>
<ol>
    <li>Click <strong>+ Onboard New Employee</strong>.</li>
    <li>Fill in all required fields, select Primary Role.</li>
    <li>Click <strong>Create Employee</strong>.</li>
</ol>
<p><strong>Expected:</strong> Success message, new employee visible in list.</p>

<h3>Test 2.3 — Multi-Role Assignment</h3>
<ol>
    <li>Create or edit an employee.</li>
    <li>Assign both a Primary Role and at least one Additional Role.</li>
</ol>
<p><strong>Expected:</strong> All roles shown as badges in the employee list. Employee has combined permissions.</p>

<h3>Test 2.4 — Deactivate Employee</h3>
<ol>
    <li>Edit an employee and set Status to <strong>Inactive</strong>.</li>
    <li>Attempt login as that employee.</li>
</ol>
<p><strong>Expected:</strong> Login blocked. Employee no longer appears in active assignment lists.</p>

<h2>3. Asset Management Tests</h2>

<h3>Test 3.1 — POS Terminal Import</h3>
<ol>
    <li>Navigate to <strong>Assets → POS Terminals → Import</strong>.</li>
    <li>Upload a valid CSV/Excel file with terminal data.</li>
</ol>
<p><strong>Expected:</strong> Terminals imported, success count shown, terminals visible in list.</p>

<h3>Test 3.2 — Asset Category Fields</h3>
<ol>
    <li>Navigate to <strong>Assets → Asset Categories</strong>.</li>
    <li>Create or edit a category with custom fields.</li>
</ol>
<p><strong>Expected:</strong> Custom fields saved and visible when creating assets of that category.</p>

<h2>4. Ticket System Tests</h2>

<h3>Test 4.1 — Create POS Terminal Ticket</h3>
<ol>
    <li>Navigate to <strong>Field Ops → Tickets → New Ticket</strong>.</li>
    <li>Set Ticket Type: <code>POS Terminal</code>, Assignment Type: <code>Public</code>.</li>
    <li>Submit ticket.</li>
</ol>
<p><strong>Expected:</strong> Ticket created, visible in POS Terminal ticket list, open to all eligible staff.</p>

<h3>Test 4.2 — Create Direct Assignment Ticket</h3>
<ol>
    <li>Create a new ticket, set Assignment Type: <code>Direct</code>, select an employee.</li>
</ol>
<p><strong>Expected:</strong> Ticket assigned to specific employee, appears in their dashboard.</p>

<h3>Test 4.3 — Staged Resolution — Add Step</h3>
<ol>
    <li>Open any ticket.</li>
    <li>Click <strong>Add Step</strong>, fill in description, submit.</li>
</ol>
<p><strong>Expected:</strong> Step created with status <code>in_progress</code>, step number incremented.</p>

<h3>Test 4.4 — Transfer Ticket</h3>
<ol>
    <li>Open a ticket step.</li>
    <li>Click <strong>Transfer</strong>, select another employee, enter reason.</li>
</ol>
<p><strong>Expected:</strong> Step marked as <code>transferred</code>, new step created for destination employee, audit trail updated.</p>

<h3>Test 4.5 — Resolve Ticket</h3>
<ol>
    <li>Complete all steps.</li>
    <li>Click <strong>Resolve Ticket</strong>, enter resolution notes.</li>
</ol>
<p><strong>Expected:</strong> Ticket status changes to <code>resolved</code>, resolution notes saved.</p>

<h2>5. Field Operations Tests</h2>

<h3>Test 5.1 — Create Job Assignment</h3>
<p>Navigate to <strong>Field Ops → Job Assignments → New Job</strong>. Assign to a technician.</p>
<p><strong>Expected:</strong> Job visible in technician's portal and job list.</p>

<h3>Test 5.2 — Log Site Visit</h3>
<p>Navigate to <strong>Field Ops → Visits → Log Visit</strong>. Fill in client, technician, and notes.</p>
<p><strong>Expected:</strong> Visit saved, visible in visit history.</p>

<h3>Test 5.3 — Terminal Deployment</h3>
<p>Create a deployment record linking a terminal to a client site.</p>
<p><strong>Expected:</strong> Deployment logged, terminal status updated.</p>

<h2>6. Reports Tests</h2>

<h3>Test 6.1 — System Reports</h3>
<p>Navigate to <strong>Reports → System Reports</strong>. Run each available report.</p>
<p><strong>Expected:</strong> Data loaded within 5 seconds, export to PDF/CSV works.</p>

<h3>Test 6.2 — Custom Report Builder</h3>
<p>Navigate to <strong>Reports → Report Builder</strong>. Select a table, choose columns, apply filters.</p>
<p><strong>Expected:</strong> Report generated with selected columns only, filters applied correctly.</p>

<h2>7. Role-Based Access Tests</h2>

<table>
    <thead>
        <tr><th>Role</th><th>Expected Access</th><th>Expected Restrictions</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-green">Admin</span></td>
            <td>All modules</td>
            <td>None (full access)</td>
        </tr>
        <tr>
            <td><span class="badge badge-blue">Manager</span></td>
            <td>Dashboard, Ops, Projects, Clients</td>
            <td>No system settings, no role management</td>
        </tr>
        <tr>
            <td><span class="badge badge-yellow">Supervisor</span></td>
            <td>Field Ops, Tickets, Reports (view)</td>
            <td>Cannot create employees, no admin settings</td>
        </tr>
        <tr>
            <td><span class="badge badge-blue">Technician</span></td>
            <td>Own jobs, assigned tickets, technician portal</td>
            <td>No management views</td>
        </tr>
    </tbody>
</table>

<div class="callout warning">
    <strong>Tip:</strong> Use separate browser profiles or incognito windows to test different roles simultaneously.
</div>

@endsection
