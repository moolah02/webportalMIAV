@extends('docs.layout')
@section('content')
@if(!empty(trim($page->content ?? '')))
    {!! $page->content !!}
@else

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>&rsaquo;</span> Reports Manual
</div>

<h1>Reports Manual</h1>
<p class="subtitle">
    Step-by-step guide to generating, filtering, and exporting every report available in the MIAV Dashboard.
    <span style="float:right;" class="badge badge-yellow">Managers &amp; Admins</span>
</p>

<div style="background:#fff8f0;border:1px solid #fed7aa;border-radius:10px;padding:16px 20px;margin-bottom:28px;">
    <strong>&#128202; Who uses reports?</strong> The reporting module is available to Managers, Supervisors, Administrators, and Super Admins.
    Standard employees can view their own personal work summaries only.
</div>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:20px 24px;margin-bottom:32px;">
    <div style="font-weight:700;font-size:13px;margin-bottom:10px;color:#0c4a6e;">&#128209; Report Types In This Manual</div>
    <ol style="columns:2;column-gap:28px;list-style-position:inside;font-size:13.5px;">
        <li><a href="#accessing" style="color:#0369a1;text-decoration:none;">Accessing Reports</a></li>
        <li><a href="#employee-report" style="color:#0369a1;text-decoration:none;">Employee Activity Report</a></li>
        <li><a href="#ticket-report" style="color:#0369a1;text-decoration:none;">Ticket Summary Report</a></li>
        <li><a href="#asset-report" style="color:#0369a1;text-decoration:none;">Asset Audit Report</a></li>
        <li><a href="#project-report" style="color:#0369a1;text-decoration:none;">Project Status Report</a></li>
        <li><a href="#visit-report" style="color:#0369a1;text-decoration:none;">Site Visit Report</a></li>
        <li><a href="#custom-report" style="color:#0369a1;text-decoration:none;">Custom Report Builder</a></li>
        <li><a href="#export" style="color:#0369a1;text-decoration:none;">Exporting Reports</a></li>
        <li><a href="#scheduled" style="color:#0369a1;text-decoration:none;">Scheduled &amp; Recurring Reports</a></li>
    </ol>
</div>

<h2 id="accessing">1. Accessing the Reports Module</h2>
<ol>
    <li>Log in to the MIAV Dashboard.</li>
    <li>In the left sidebar, click <strong>Reports &amp; Analytics</strong> to expand the section.</li>
    <li>You will see the available report categories: Predefined Reports and Custom Report Builder.</li>
</ol>
<div class="callout">
    <strong>Note:</strong> If you do not see the Reports section in your sidebar, your account does not have the required permission. Contact your administrator.
</div>

<h2 id="employee-report">2. Employee Activity Report</h2>
<p>Shows a summary of work completed by each employee over a selected date range, including tickets handled, jobs completed, and visits logged.</p>

<h3>Step-by-step:</h3>
<ol>
    <li>Navigate to <strong>Reports &amp; Analytics &rarr; Employee Activity</strong>.</li>
    <li>Set the <strong>Date Range</strong>: choose a start and end date, or use a quick preset (This Week, This Month, Last Month, Custom).</li>
    <li>Optionally filter by <strong>Department</strong> or select a specific employee from the dropdown.</li>
    <li>Click <strong>Generate Report</strong>.</li>
    <li>Review the output table showing per-employee: tickets resolved, jobs completed, visits logged, average response time.</li>
    <li>To export, click <strong>Export PDF</strong> or <strong>Export CSV</strong> at the top right of the results.</li>
</ol>
<div class="callout success">
    <strong>Use case:</strong> Monthly performance reviews, identifying high-performing staff, and spotting workload imbalances.
</div>

<h2 id="ticket-report">3. Ticket Summary Report</h2>
<p>Provides an overview of all tickets in the system within a period, grouped by status, type, or priority.</p>

<h3>Step-by-step:</h3>
<ol>
    <li>Navigate to <strong>Reports &amp; Analytics &rarr; Ticket Summary</strong>.</li>
    <li>Set the <strong>Date Range</strong> (creation date or resolution date).</li>
    <li>Apply optional filters:
        <ul>
            <li><strong>Ticket Type</strong>: POS Terminal, Internal, or All</li>
            <li><strong>Status</strong>: Open, In Progress, Resolved, Closed, Escalated, or All</li>
            <li><strong>Priority</strong>: Urgent, High, Medium, Low, or All</li>
            <li><strong>Assigned To</strong>: specific employee or All</li>
        </ul>
    </li>
    <li>Select <strong>Group By</strong>: Status, Type, Priority, or Assignee.</li>
    <li>Click <strong>Generate Report</strong>.</li>
    <li>The results show counts per group and a summary timeline chart.</li>
    <li>Export as needed.</li>
</ol>
<div class="callout warning">
    <strong>SLA Check:</strong> Look for the <span class="badge badge-red">SLA Breached</span> indicator on tickets where resolution exceeded the client SLA threshold.
</div>

<h2 id="asset-report">4. Asset Audit Report</h2>
<p>Lists all assets, their current status, assigned employees, and last update date. Useful for physical audits and reconciliation.</p>

<h3>Step-by-step:</h3>
<ol>
    <li>Navigate to <strong>Reports &amp; Analytics &rarr; Asset Audit</strong>.</li>
    <li>Select <strong>Asset Type</strong>: All Assets, POS Terminals Only, or General Assets.</li>
    <li>Optionally filter by <strong>Status</strong> (Active, Inactive, Under Repair, Disposed).</li>
    <li>Optionally filter by <strong>Assigned Employee</strong> or <strong>Client</strong>.</li>
    <li>Click <strong>Generate Report</strong>.</li>
    <li>Review the table: Asset ID, Name/Serial, Type, Status, Assigned To, Location, Last Updated.</li>
    <li>Export to CSV for spreadsheet reconciliation or PDF for a formal printable audit.</li>
</ol>
<div class="callout success">
    <strong>Tip:</strong> Run this report quarterly for physical stock reconciliation. Compare the CSV against physical inventory counts.
</div>

<h2 id="project-report">5. Project Status Report</h2>
<p>Overview of all projects &mdash; active, completed, or overdue &mdash; with milestone summaries and team assignments.</p>

<h3>Step-by-step:</h3>
<ol>
    <li>Navigate to <strong>Reports &amp; Analytics &rarr; Project Status</strong>.</li>
    <li>Filter by <strong>Status</strong>: Active, Completed, On Hold, Overdue, or All.</li>
    <li>Optionally filter by <strong>Client</strong> or <strong>Project Manager</strong>.</li>
    <li>Set a date range for the <strong>Expected End Date</strong> if needed.</li>
    <li>Click <strong>Generate Report</strong>.</li>
    <li>Each row shows: Project Name, Client, Manager, Status, % Complete, Start Date, Target End, Actual End (if closed).</li>
    <li>Click any project row to view its full detail and milestone breakdown within the report.</li>
    <li>Export to PDF for client-facing project summaries.</li>
</ol>

<h2 id="visit-report">6. Site Visit Report</h2>
<p>Records all field visit activity, useful for verifying SLA compliance and technician attendance.</p>

<h3>Step-by-step:</h3>
<ol>
    <li>Navigate to <strong>Reports &amp; Analytics &rarr; Site Visits</strong>.</li>
    <li>Set the <strong>Date Range</strong>.</li>
    <li>Optionally filter by <strong>Technician</strong>, <strong>Client</strong>, or <strong>Linked Ticket</strong>.</li>
    <li>Click <strong>Generate Report</strong>.</li>
    <li>Results show: Visit ID, Date, Technician, Client/Site, Duration, Notes Summary, Linked Ticket (if any).</li>
    <li>Export as needed.</li>
</ol>

<h2 id="custom-report">7. Custom Report Builder</h2>
<p>Build a fully customised report by choosing which data source, columns, filters, and grouping you need.</p>

<h3>Step-by-step:</h3>
<ol>
    <li>Navigate to <strong>Reports &amp; Analytics &rarr; Custom Report Builder</strong>.</li>
    <li><strong>Choose Data Source:</strong> select the primary entity (Tickets, Employees, Assets, Projects, Visits, or Jobs).</li>
    <li><strong>Select Columns:</strong> tick the fields you want in your report. Available columns depend on the chosen data source.</li>
    <li><strong>Add Filters:</strong>
        <ul>
            <li>Click <strong>+ Add Filter</strong>.</li>
            <li>Choose a field, operator (equals, contains, greater than, between, etc.), and value.</li>
            <li>Add multiple filters to narrow the results.</li>
        </ul>
    </li>
    <li><strong>Group By:</strong> optionally select a field to group results (e.g., group tickets by Status).</li>
    <li><strong>Date Range:</strong> set the date range for the primary record&rsquo;s date field.</li>
    <li>Click <strong>Preview Report</strong> to see the first 20 rows.</li>
    <li>If satisfied, click <strong>Generate Full Report</strong>.</li>
    <li>Export to PDF or CSV.</li>
    <li>To reuse this configuration, click <strong>Save Report Template</strong> and give it a name.</li>
</ol>
<div class="callout">
    <strong>Example:</strong> To see all Urgent tickets logged last month by a specific technician: Data Source = Tickets, Filters = Priority is Urgent + Assigned To = [Name] + Created At between [start] and [end], Group By = Status.
</div>

<h2 id="export">8. Exporting Reports</h2>

<h3>8.1 Export to PDF</h3>
<ol>
    <li>After generating any report, click <strong>Export PDF</strong> (top right of results).</li>
    <li>A formatted PDF document will download to your device.</li>
    <li>PDFs include the Revival Technologies logo, report title, date range, and applied filters at the top.</li>
    <li>Suitable for emailing to management or presenting in meetings.</li>
</ol>

<h3>8.2 Export to CSV / Excel</h3>
<ol>
    <li>Click <strong>Export CSV</strong> to download a comma-separated file.</li>
    <li>Open in Microsoft Excel or Google Sheets for further analysis.</li>
    <li>The first row contains column headers. Dates are exported in DD/MM/YYYY format.</li>
</ol>
<div class="callout warning">
    <strong>Large exports:</strong> Reports with more than 5,000 rows may take a few seconds to generate. Do not close the tab while the download is in progress.
</div>

<h2 id="scheduled">9. Scheduled &amp; Recurring Reports</h2>
<p>Managers can configure reports to run automatically on a schedule and be emailed to recipients.</p>
<ol>
    <li>Open any predefined report or a saved custom report template.</li>
    <li>Click <strong>Schedule This Report</strong>.</li>
    <li>Set the <strong>Frequency</strong>: Daily, Weekly (select day), or Monthly (select date).</li>
    <li>Enter one or more <strong>Email Recipients</strong>.</li>
    <li>Choose <strong>Format</strong>: PDF, CSV, or Both.</li>
    <li>Click <strong>Save Schedule</strong>.</li>
    <li>The report will run automatically at midnight on the scheduled day and be emailed to recipients.</li>
</ol>
<div class="callout">
    <strong>Note:</strong> Scheduled reports require the system mail configuration to be active. Contact your admin if emails are not arriving.
</div>

@endif
@endsection