@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>›</span> Staged Resolution Guide
</div>

<h1>Staged Resolution System</h1>
<p class="subtitle">
    Multi-step ticket resolution with full audit trail — transfer between staff, document all work, track every change.
    <span style="float:right;" class="badge badge-yellow">Technicians &amp; Support</span>
</p>

<h2>Overview</h2>
<p>The Staged Resolution System allows tickets to be solved in multiple steps. Each step can be handled by a different employee. All work, transfers, and resolutions are tracked in a full audit trail.</p>

<h2>Key Features</h2>
<ul>
    <li><strong>Days-Based Estimation</strong> — Uses <code>estimated_resolution_days</code> (more business-friendly than minutes)</li>
    <li><strong>Step Workflow</strong> — Create, complete, and transfer ticket steps</li>
    <li><strong>Full Audit Trail</strong> — Every action logged with timestamp and actor</li>
    <li><strong>Transfer Tracking</strong> — Reason for transfer is recorded</li>
</ul>

<h2>Ticket Steps Workflow</h2>

<div style="display:flex;flex-direction:column;gap:12px;margin:16px 0 28px;">
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="background:#2563a8;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">1</div>
        <div>
            <strong>Create Step</strong> — Record the work being performed on the ticket. Describe what is being done.
        </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="background:#2563a8;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">2</div>
        <div>
            <strong>Work Step</strong> — Employee works on the step. Status: <code>in_progress</code>.
            Add notes as work progresses.
        </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="background:#7c3aed;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">T</div>
        <div>
            <strong>Transfer (Optional)</strong> — If the issue requires another person, transfer the step.
            Enter the reason and select the destination employee. Step becomes <code>transferred</code>, a new step is auto-created.
        </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="background:#059669;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">✓</div>
        <div>
            <strong>Complete Step</strong> — Mark the step as completed. Enter resolution notes for this step.
        </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="background:#e85d04;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">★</div>
        <div>
            <strong>Resolve Ticket</strong> — Once all steps are complete, resolve the entire ticket with final notes.
        </div>
    </div>
</div>

<h2>Database Schema</h2>

<h3>ticket_steps Table</h3>
<pre><code>ticket_steps
├── id                      BIGINT PRIMARY KEY AUTO_INCREMENT
├── ticket_id               FK → tickets.id         [indexed]
├── employee_id             FK → employees.id       [indexed]
├── step_number             INT
├── status                  ENUM('in_progress','completed','transferred','resolved')  [indexed]
├── description             VARCHAR(255)
├── notes                   TEXT NULLABLE
├── resolution_notes        TEXT NULLABLE
├── transferred_reason      VARCHAR(255) NULLABLE
├── transferred_to          FK → employees.id NULLABLE
├── completed_at            TIMESTAMP NULLABLE
├── created_at / updated_at TIMESTAMP</code></pre>

<h3>tickets Table — Modified Columns</h3>
<pre><code>-- replaced estimated_resolution_time (minutes) with:
ALTER TABLE tickets
  ADD COLUMN estimated_resolution_days INT NULLABLE;</code></pre>

<h2>Step Status Reference</h2>

<table>
    <thead>
        <tr><th>Status</th><th>Meaning</th><th>Next Action</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-blue">in_progress</span></td>
            <td>Active step being worked on</td>
            <td>Complete or Transfer</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">completed</span></td>
            <td>Step finished successfully</td>
            <td>Create next step or Resolve ticket</td>
        </tr>
        <tr>
            <td><span class="badge badge-yellow">transferred</span></td>
            <td>Moved to another employee</td>
            <td>New step auto-created for recipient</td>
        </tr>
        <tr>
            <td><span class="badge badge-purple">resolved</span></td>
            <td>Final step — ticket resolution</td>
            <td>Ticket marked resolved</td>
        </tr>
    </tbody>
</table>

<h2>Audit Trail</h2>
<p>Every action on a ticket step is recorded:</p>
<ul>
    <li>Who created the step, when</li>
    <li>Who completed or transferred, when</li>
    <li>Reason for transfer</li>
    <li>Resolution notes at each step</li>
    <li>Full history viewable from the ticket detail screen</li>
</ul>

<div class="callout">
    <strong>SLA Note:</strong> The <code>estimated_resolution_days</code> field on the ticket is used for SLA tracking. Overdue tickets (past their estimated days) are highlighted in the ticket list.
</div>

<h2>Eloquent Model Scopes</h2>
<pre><code>// TicketStep scopes
TicketStep::inProgress()->get();
TicketStep::completed()->get();
TicketStep::transferred()->get();

// Ticket with steps
$ticket->steps;                      // All steps
$ticket->activeStep;                 // Current in-progress step
$ticket->steps()->completed()->get(); // Completed steps only</code></pre>

@endsection
