@extends('docs.layout')
@section('content')
@if(!empty(trim($page->content ?? '')))
    {!! $page->content !!}
@else

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>&rsaquo;</span> Mobile App Guide
</div>

<h1>Mobile App Guide</h1>
<p class="subtitle">
    For technicians and field staff &mdash; how to use the MIAV mobile interface to manage jobs, tickets, and visits from anywhere.
    <span style="float:right;" class="badge badge-green">Field Staff</span>
</p>

<div class="callout">
    <strong>About the Mobile Interface:</strong> The MIAV mobile view is a responsive version of the main web system, optimised for smartphones and tablets. No separate app installation is required &mdash; simply open the system URL in your phone browser and log in as normal.
</div>

<h2 id="access">1. Accessing the System on Mobile</h2>
<ol>
    <li>Open your phone&rsquo;s browser (Chrome or Safari recommended).</li>
    <li>Go to: <code>http://51.21.252.67</code></li>
    <li>Enter your email and password, then tap <strong>Login</strong>.</li>
    <li>You will be taken to your personal <strong>Employee Dashboard</strong>.</li>
</ol>
<div class="callout success">
    <strong>Tip:</strong> Add the URL to your phone&rsquo;s home screen for quick access. In Chrome, tap the menu (&#8942;) and select &ldquo;Add to Home Screen&rdquo;.
</div>

<h2 id="dashboard">2. Your Mobile Dashboard</h2>
<p>On login, you will see your personal dashboard showing:</p>
<ul>
    <li><strong>My Active Tickets</strong> &mdash; all tickets currently assigned to you</li>
    <li><strong>My Jobs</strong> &mdash; job assignments for today and upcoming</li>
    <li><strong>Recent Visits</strong> &mdash; your last logged site visits</li>
    <li><strong>Staged Steps Due</strong> &mdash; any staged resolution steps waiting for your action</li>
</ul>
<p>Use the hamburger menu (&#9776;) at the top left to navigate to other sections.</p>

<h2 id="viewing-tickets">3. Viewing &amp; Working Your Tickets</h2>

<h3>3.1 Opening a Ticket</h3>
<ol>
    <li>From the dashboard, tap <strong>My Active Tickets</strong> or navigate to <strong>Field Operations &rarr; Support Tickets</strong>.</li>
    <li>Tap any ticket in the list to open its detail screen.</li>
    <li>Review the <strong>Subject</strong>, <strong>Description</strong>, <strong>Priority</strong>, and any attached client/terminal info.</li>
</ol>

<h3>3.2 Adding a Progress Comment</h3>
<ol>
    <li>On the ticket detail screen, scroll to the <strong>Comments</strong> section.</li>
    <li>Type your update in the comment box (e.g., &ldquo;Arrived at site, diagnosing POS terminal model X2.&rdquo;)</li>
    <li>Tap <strong>Post Comment</strong>. Your name and timestamp will be recorded automatically.</li>
</ol>
<div class="callout warning">
    <strong>Important:</strong> Always add a comment when you arrive on site and when you complete work. This creates an audit trail and keeps managers informed.
</div>

<h3>3.3 Updating Ticket Status</h3>
<ol>
    <li>Open the ticket.</li>
    <li>Tap <strong>Update Status</strong> (or the status dropdown at the top of the ticket).</li>
    <li>Select the new status: In Progress, Pending, Resolved.</li>
    <li>Add a note explaining the change.</li>
    <li>Tap <strong>Save</strong>.</li>
</ol>

<h3>3.4 Resolving a Ticket</h3>
<ol>
    <li>Once the issue is fixed, open the ticket.</li>
    <li>Change status to <strong>Resolved</strong>.</li>
    <li>Add a resolution note describing what was done.</li>
    <li>Tap <strong>Save</strong>. The ticket will be reviewed and closed by a supervisor or admin.</li>
</ol>

<h2 id="visits">4. Logging a Site Visit</h2>
<ol>
    <li>Navigate to <strong>Field Operations &rarr; Site Visits &rarr; Log New Visit</strong>.</li>
    <li>Select the <strong>Client</strong> or site you visited.</li>
    <li>Set the <strong>Visit Date &amp; Time</strong> (defaults to now).</li>
    <li>Enter <strong>Visit Notes</strong>: what was done, what was observed, outcomes.</li>
    <li>Optionally link the visit to a <strong>Ticket</strong> or <strong>Job Assignment</strong>.</li>
    <li>Tap <strong>Save Visit</strong>.</li>
</ol>
<div class="callout">
    <strong>Note:</strong> Visit records feed into management reports and SLA calculations. Log every visit, even brief ones.
</div>

<h2 id="jobs">5. Managing Job Assignments</h2>

<h3>5.1 Viewing Your Jobs</h3>
<ol>
    <li>Navigate to <strong>Field Operations &rarr; Job Assignments</strong>.</li>
    <li>Use the <strong>My Jobs</strong> filter to see only your assigned jobs.</li>
    <li>Jobs are listed with priority (Urgent, High, Medium, Low) and due date.</li>
</ol>

<h3>5.2 Completing a Job</h3>
<ol>
    <li>Open the job from the list.</li>
    <li>Review the job details and any linked ticket or client.</li>
    <li>Tap <strong>Mark as Complete</strong>.</li>
    <li>Enter a completion note describing the outcome.</li>
    <li>Tap <strong>Save</strong>.</li>
</ol>

<h2 id="staged">6. Completing a Staged Resolution Step</h2>
<p>If you have been assigned a step in a <strong>Staged Resolution</strong> plan:</p>
<ol>
    <li>From your dashboard, tap <strong>Staged Steps Due</strong>, or navigate to the relevant ticket.</li>
    <li>On the ticket detail page, scroll to the <strong>Staged Resolution</strong> section.</li>
    <li>Locate your step (highlighted as &ldquo;Your Step&rdquo;).</li>
    <li>Complete the required action in the field.</li>
    <li>Tap <strong>Mark Step Complete</strong>.</li>
    <li>Enter a note describing what you did.</li>
    <li>Tap <strong>Confirm</strong>. The next step in the chain will be activated automatically and the next person notified.</li>
</ol>
<div class="callout warning">
    <strong>Note:</strong> You can only mark your own assigned step as complete. If you cannot complete it, contact your manager to have the step transferred.
</div>

<h2 id="account">7. Your Account &amp; Profile</h2>
<ol>
    <li>Tap your name or avatar in the top-right corner.</li>
    <li>Select <strong>My Profile</strong> to view your employee details.</li>
    <li>Contact your system admin to update your email or reset your password &mdash; you cannot change these yourself.</li>
    <li>To log out, open the top-right menu and tap <strong>Sign Out</strong>.</li>
</ol>

<h2 id="tips">8. Field Tips &amp; Best Practices</h2>
<ul>
    <li><strong>Log in before travelling to site</strong> to review ticket details and client address.</li>
    <li><strong>Add a comment when you arrive</strong> at a client site &mdash; a timestamp note like &ldquo;On site, commencing inspection&rdquo; is sufficient.</li>
    <li><strong>Log your visit before leaving site</strong> to ensure accurate timekeeping.</li>
    <li><strong>If you lose connectivity</strong> mid-visit, note your details manually and log them when back online.</li>
    <li><strong>If a ticket cannot be resolved</strong> in one visit, set status to <strong>Pending</strong> with a note explaining why and the next steps.</li>
    <li><strong>Never close a ticket</strong> yourself unless your supervisor has confirmed this is correct procedure for your team.</li>
</ul>

@endif
@endsection