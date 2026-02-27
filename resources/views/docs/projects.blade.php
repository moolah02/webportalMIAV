@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>&rsaquo;</span> Project Flow Guide
</div>

<h1>Project Flow Guide</h1>
<p class="subtitle">
    End-to-end walkthrough of the project lifecycle in MIAV &mdash; from creation through to closure and reporting.
    <span style="float:right;" class="badge badge-purple">Project Managers</span>
</p>

<div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:10px;padding:16px 20px;margin-bottom:28px;">
    <strong>&#128203; Project Lifecycle at a Glance:</strong>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;align-items:center;">
        <span style="background:#7c3aed;color:#fff;padding:5px 14px;border-radius:99px;font-size:12px;font-weight:600;">1. Create</span>
        <span style="color:#7c3aed;font-size:16px;">&#8594;</span>
        <span style="background:#1a3a5c;color:#fff;padding:5px 14px;border-radius:99px;font-size:12px;font-weight:600;">2. Plan &amp; Assign</span>
        <span style="color:#7c3aed;font-size:16px;">&#8594;</span>
        <span style="background:#1a7f37;color:#fff;padding:5px 14px;border-radius:99px;font-size:12px;font-weight:600;">3. Execute</span>
        <span style="color:#7c3aed;font-size:16px;">&#8594;</span>
        <span style="background:#9a3412;color:#fff;padding:5px 14px;border-radius:99px;font-size:12px;font-weight:600;">4. Monitor</span>
        <span style="color:#7c3aed;font-size:16px;">&#8594;</span>
        <span style="background:#0969da;color:#fff;padding:5px 14px;border-radius:99px;font-size:12px;font-weight:600;">5. Close &amp; Report</span>
    </div>
</div>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:20px 24px;margin-bottom:32px;">
    <div style="font-weight:700;font-size:13px;margin-bottom:10px;color:#0c4a6e;">&#128209; Contents</div>
    <ol style="columns:2;column-gap:28px;list-style-position:inside;font-size:13.5px;">
        <li><a href="#creating" style="color:#0369a1;text-decoration:none;">Creating a Project</a></li>
        <li><a href="#team" style="color:#0369a1;text-decoration:none;">Assigning Team Members</a></li>
        <li><a href="#milestones" style="color:#0369a1;text-decoration:none;">Setting Milestones</a></li>
        <li><a href="#field-ops" style="color:#0369a1;text-decoration:none;">Linking Field Operations</a></li>
        <li><a href="#monitoring" style="color:#0369a1;text-decoration:none;">Monitoring Progress</a></li>
        <li><a href="#updates" style="color:#0369a1;text-decoration:none;">Updating &amp; Communicating</a></li>
        <li><a href="#closure" style="color:#0369a1;text-decoration:none;">Project Closure</a></li>
        <li><a href="#reports" style="color:#0369a1;text-decoration:none;">Closure Report</a></li>
        <li><a href="#statuses" style="color:#0369a1;text-decoration:none;">Project Statuses Reference</a></li>
    </ol>
</div>

<h2 id="creating">1. Creating a Project</h2>

<h3>Step-by-step:</h3>
<ol>
    <li>Navigate to <strong>Project Management &rarr; Projects &rarr; Create New Project</strong>.</li>
    <li>Fill in the required fields:
        <table>
            <thead><tr><th>Field</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td><strong>Project Name *</strong></td><td>A clear, descriptive name (e.g., &ldquo;FNB Merchant Terminal Rollout &mdash; March 2026&rdquo;)</td></tr>
                <tr><td><strong>Client *</strong></td><td>Select the client from the dropdown</td></tr>
                <tr><td><strong>Project Manager *</strong></td><td>The manager responsible for this project</td></tr>
                <tr><td><strong>Start Date *</strong></td><td>When the project begins</td></tr>
                <tr><td><strong>Target End Date *</strong></td><td>Expected completion date</td></tr>
                <tr><td><strong>Description</strong></td><td>Scope summary, objectives, and any relevant background</td></tr>
                <tr><td><strong>Status</strong></td><td>Set to <span class="badge badge-blue">Active</span> on creation</td></tr>
                <tr><td><strong>Priority</strong></td><td>Low / Medium / High / Critical</td></tr>
            </tbody>
        </table>
    </li>
    <li>Click <strong>Create Project</strong>. You will be redirected to the project detail page.</li>
</ol>
<div class="callout success">
    <strong>Tip:</strong> Write a detailed description at creation. This becomes the reference point for the team throughout the project lifecycle.
</div>

<h2 id="team">2. Assigning Team Members</h2>
<ol>
    <li>On the project detail page, locate the <strong>Team Members</strong> section.</li>
    <li>Click <strong>Add Team Member</strong>.</li>
    <li>Search for and select an employee.</li>
    <li>Assign their <strong>Role on Project</strong> (e.g., Lead Technician, Support Technician, Coordinator).</li>
    <li>Click <strong>Add</strong>. Repeat for each team member.</li>
</ol>
<div class="callout">
    <strong>Note:</strong> Team members receive a notification when added to a project. They will see the project in their personal dashboard.
</div>

<h2 id="milestones">3. Setting Milestones</h2>
<p>Milestones are key checkpoints in the project timeline. Use them to track major deliverables.</p>
<ol>
    <li>On the project detail page, scroll to the <strong>Milestones</strong> section.</li>
    <li>Click <strong>Add Milestone</strong>.</li>
    <li>Enter:
        <ul>
            <li><strong>Milestone Name</strong> &mdash; e.g., &ldquo;Initial site survey complete&rdquo;</li>
            <li><strong>Target Date</strong></li>
            <li><strong>Description</strong> (optional)</li>
            <li><strong>Responsible Person</strong> &mdash; assign a team member</li>
        </ul>
    </li>
    <li>Click <strong>Save Milestone</strong>.</li>
    <li>Add as many milestones as needed to reflect the project&rsquo;s key stages.</li>
</ol>
<div class="callout warning">
    <strong>Important:</strong> When a milestone passes its target date without being marked complete, it will turn <span class="badge badge-red">Overdue</span> and flag in the project status dashboard.
</div>

<h2 id="field-ops">4. Linking Field Operations</h2>
<p>Tie real-world work to the project by linking jobs, deployments, and visits.</p>

<h3>4.1 Linking Job Assignments</h3>
<ol>
    <li>From the project page, go to the <strong>Linked Operations</strong> tab.</li>
    <li>Click <strong>Link Job Assignment</strong>.</li>
    <li>Search for an existing job by employee or reference number, or click <strong>Create New Job</strong> (it will automatically link to this project).</li>
</ol>

<h3>4.2 Linking Terminal Deployments</h3>
<ol>
    <li>In the <strong>Linked Operations</strong> tab, click <strong>Link Deployment</strong>.</li>
    <li>Select the terminal deployment from the list or create a new one.</li>
    <li>Linked deployments will appear in the project timeline and count towards project KPIs.</li>
</ol>

<h3>4.3 Linking Support Tickets</h3>
<ol>
    <li>Navigate to any existing ticket and open it.</li>
    <li>In the ticket detail, click <strong>Link to Project</strong>.</li>
    <li>Select this project from the dropdown and save.</li>
    <li>Alternatively, create a new ticket directly from the project page using the <strong>Create Ticket for this Project</strong> button.</li>
</ol>

<h2 id="monitoring">5. Monitoring Progress</h2>
<p>The project detail page provides a live overview of progress:</p>
<ul>
    <li><strong>Progress Bar</strong> &mdash; calculated based on completed vs total milestones</li>
    <li><strong>Milestone Status Table</strong> &mdash; each milestone with status (On Track, Overdue, Complete)</li>
    <li><strong>Linked Operations Tab</strong> &mdash; all jobs, deployments, visits, and tickets linked to this project</li>
    <li><strong>Activity Timeline</strong> &mdash; chronological log of all updates, comments, and status changes</li>
</ul>
<div class="callout">
    <strong>Tip:</strong> Run the <a href="{{ url('/docs/reports') }}#project-report">Project Status Report</a> for a formal snapshot to share with stakeholders.
</div>

<h2 id="updates">6. Updating &amp; Communicating</h2>

<h3>6.1 Posting a Project Update</h3>
<ol>
    <li>On the project detail page, scroll to the <strong>Updates / Comments</strong> section.</li>
    <li>Type your update in the text field.</li>
    <li>Click <strong>Post Update</strong>.</li>
    <li>All team members will see the update in their dashboard notifications and on the project timeline.</li>
</ol>

<h3>6.2 Updating a Milestone</h3>
<ol>
    <li>Find the milestone in the Milestones section.</li>
    <li>Click <strong>Edit</strong> to update the date, description, or responsible person, or click <strong>Mark Complete</strong> when the milestone is achieved.</li>
    <li>Always add a completion note when marking a milestone done.</li>
</ol>

<h3>6.3 Placing a Project on Hold</h3>
<ol>
    <li>On the project detail page, click <strong>Edit Project</strong>.</li>
    <li>Change <strong>Status</strong> to <span class="badge badge-yellow">On Hold</span>.</li>
    <li>Add a hold reason in the description or as a project update.</li>
    <li>Save. The project will be visible in the &ldquo;On Hold&rdquo; filter on the projects list.</li>
</ol>

<h2 id="closure">7. Project Closure</h2>
<p>When all milestones are complete and deliverables confirmed:</p>
<ol>
    <li>Ensure all linked tickets are <strong>Resolved</strong> or <strong>Closed</strong>.</li>
    <li>Ensure all linked job assignments are <strong>Complete</strong>.</li>
    <li>Ensure all milestones are marked <strong>Complete</strong>.</li>
    <li>On the project detail page, click <strong>Close Project</strong>.</li>
    <li>Enter the <strong>Actual End Date</strong> and a <strong>Closure Summary</strong> (key outcomes, deliverables completed, issues resolved).</li>
    <li>Click <strong>Confirm Closure</strong>. The project status changes to <span class="badge badge-green">Completed</span>.</li>
</ol>
<div class="callout warning">
    <strong>Note:</strong> Closing a project is final. If there are open linked tickets, the system will warn you but will not block closure. Ensure all work is genuinely complete before closing.
</div>

<h2 id="reports">8. Closure Report</h2>
<p>After closing a project, generate the formal closure report to share with management and clients.</p>
<ol>
    <li>On the closed project&rsquo;s detail page, click <strong>Generate Closure Report</strong>.</li>
    <li>Select the <strong>Report Format</strong>: PDF (for distribution) or CSV (for data analysis).</li>
    <li>The PDF closure report includes:
        <ul>
            <li>Project summary (name, client, manager, dates)</li>
            <li>Milestone completion table with actual vs target dates</li>
            <li>Summary of linked operations (jobs completed, terminals deployed, tickets resolved)</li>
            <li>Total duration and team members</li>
            <li>Closure notes</li>
            <li>Revival Technologies branding and date stamp</li>
        </ul>
    </li>
    <li>Click <strong>Download</strong> or <strong>Email Report</strong> to send directly to a recipient from the system.</li>
</ol>

<h2 id="statuses">9. Project Status Reference</h2>

<table>
    <thead>
        <tr><th>Status</th><th>Meaning</th><th>Who Can Set</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-blue">Active</span></td>
            <td>In progress, work ongoing</td>
            <td>Manager, Admin</td>
        </tr>
        <tr>
            <td><span class="badge badge-yellow">On Hold</span></td>
            <td>Paused &mdash; awaiting approval, resources, or client decision</td>
            <td>Manager, Admin</td>
        </tr>
        <tr>
            <td><span class="badge badge-red">Overdue</span></td>
            <td>Past target end date with open milestones (set automatically)</td>
            <td>System (automatic)</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">Completed</span></td>
            <td>All work done, project formally closed</td>
            <td>Manager, Admin</td>
        </tr>
        <tr>
            <td><span class="badge badge-gray">Cancelled</span></td>
            <td>Project discontinued before completion</td>
            <td>Admin only</td>
        </tr>
    </tbody>
</table>

@endsection