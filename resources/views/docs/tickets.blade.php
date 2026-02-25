@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>›</span> Ticket System Guide
</div>

<h1>Ticket System Guide</h1>
<p class="subtitle">
    Enhanced ticket system with ticket types, assignment types, status filtering, and staged resolution.
    <span style="float:right;" class="badge badge-purple">Support Team</span>
</p>

<h2>Overview</h2>
<p>The support ticket system is divided into:</p>
<ol>
    <li><strong>Ticket Type Division</strong> — POS Terminal vs Internal tickets</li>
    <li><strong>Assignment Type</strong> — Public (open to any employee) vs Direct (assigned employee)</li>
    <li><strong>Status Filtering</strong> — View pending, in-progress, resolved, etc.</li>
</ol>

<h2>Ticket Types</h2>

<table>
    <thead>
        <tr><th>Type</th><th>Use Case</th><th>Linked To</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-blue">POS Terminal</span></td>
            <td>Issues with a specific POS terminal at a client site</td>
            <td>Terminal serial / client record</td>
        </tr>
        <tr>
            <td><span class="badge badge-purple">Internal</span></td>
            <td>Internal support, IT requests, HR issues</td>
            <td>Department / employee</td>
        </tr>
    </tbody>
</table>

<h2>Assignment Types</h2>

<table>
    <thead>
        <tr><th>Type</th><th>Description</th><th>Who Can Attend</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-green">Public</span></td>
            <td>Open ticket — any eligible employee can pick it up</td>
            <td>All employees with ticket permissions</td>
        </tr>
        <tr>
            <td><span class="badge badge-yellow">Direct</span></td>
            <td>Assigned directly to a specific employee</td>
            <td>Only the assigned employee</td>
        </tr>
    </tbody>
</table>

<h2>Ticket Lifecycle</h2>
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:16px 0 24px;">
    <span style="background:#dbeafe;color:#1d4ed8;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:600;">Open</span>
    <span style="color:#64748b;">→</span>
    <span style="background:#fef9c3;color:#a16207;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:600;">In Progress</span>
    <span style="color:#64748b;">→</span>
    <span style="background:#ede9fe;color:#7c3aed;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:600;">Pending</span>
    <span style="color:#64748b;">→</span>
    <span style="background:#dcfce7;color:#15803d;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:600;">Resolved</span>
    <span style="color:#64748b;">or</span>
    <span style="background:#fee2e2;color:#b91c1c;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:600;">Closed</span>
</div>

<h2>Database Schema</h2>

<h3>Tickets Table — Relevant Columns</h3>
<pre><code>tickets
├── id                          BIGINT PRIMARY KEY
├── title                       VARCHAR(255)
├── description                 TEXT
├── status                      ENUM('open','in_progress','pending','resolved','closed')
├── priority                    ENUM('low','medium','high','urgent')
├── ticket_type                 ENUM('pos_terminal','internal')   [indexed]
├── assignment_type             ENUM('public','direct')           [indexed]
├── assigned_to                 FK → employees.id
├── created_by                  FK → employees.id
├── estimated_resolution_days   INT NULLABLE
├── created_at / updated_at     TIMESTAMP</code></pre>

<h3>Scope Methods (Eloquent)</h3>
<pre><code>// Filter by type
Ticket::byTicketType('pos_terminal')->get();
Ticket::byTicketType('internal')->get();

// Filter by assignment
Ticket::byAssignmentType('public')->get();
Ticket::byAssignmentType('direct')->get();

// Named scopes
Ticket::pending()->get();
Ticket::posTerminalTickets()->get();
Ticket::internalTickets()->get();
Ticket::publicTickets()->get();
Ticket::directTickets()->get();</code></pre>

<h2>Creating a Ticket</h2>
<ol>
    <li>Navigate to <strong>Field Ops → Tickets → New Ticket</strong>.</li>
    <li>Fill in <em>Title</em>, <em>Description</em>, <em>Priority</em>.</li>
    <li>Select <strong>Ticket Type</strong>: POS Terminal or Internal.</li>
    <li>Select <strong>Assignment Type</strong>: Public or Direct.</li>
    <li>If Direct, select the <strong>Assigned Employee</strong>.</li>
    <li>Optionally set <strong>Estimated Resolution Days</strong>.</li>
    <li>Click <strong>Submit Ticket</strong>.</li>
</ol>

<h2>Filtering Tickets</h2>
<p>Use the filter bar on the Tickets list to filter by:</p>
<ul>
    <li>Status (Open / Pending / Resolved / All)</li>
    <li>Ticket Type (POS Terminal / Internal)</li>
    <li>Assignment Type (Public / Direct)</li>
    <li>Priority level</li>
    <li>Assigned employee</li>
    <li>Date range</li>
</ul>

<div class="callout success">
    <strong>Pro Tip:</strong> Use the <em>Pending Tickets</em> quick-filter to see all tickets that require action — open, in-progress, and pending combined.
</div>

@endsection
