@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>›</span> API Documentation
</div>

<h1>API Documentation</h1>
<p class="subtitle">
    REST API reference for the MIAV Dashboard — web and mobile integrations.
    <span style="float:right;" class="badge badge-blue">Technical Reference</span>
</p>

{{-- SWAGGER BANNER --}}
<div style="background:linear-gradient(135deg,#0f172a,#1e3a5c);border-radius:12px;padding:28px 32px;color:#fff;margin-bottom:32px;">
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
        <div style="font-size:40px;">🗂️</div>
        <div style="flex:1;">
            <h2 style="color:#fff;font-size:18px;margin:0 0 6px;border:none;padding:0;">Swagger / OpenAPI Interactive UI</h2>
            <p style="opacity:.8;font-size:13.5px;margin:0;">
                Full OpenAPI 3.0 spec — authentication, jobs, tickets, POS terminals, assets, reports, visits, sync, uploads and more.
                Browse endpoints, inspect request/response schemas, and copy example payloads. Password-protected for external sharing.
            </p>
        </div>
        <div>
            <a href="/docs/swagger.html" target="_blank"
               style="background:#10b981;color:#fff;padding:12px 22px;border-radius:8px;font-weight:600;font-size:13px;display:inline-block;text-decoration:none;">
                Open Swagger UI ↗
            </a>
        </div>
    </div>
</div>

<div class="callout success">
    <strong>Live:</strong> The interactive Swagger UI is at
    <a href="/docs/swagger.html" target="_blank"><code>/docs/swagger.html</code></a>.
    A password is required to open it (ask your system administrator).
</div>

<h2>Quick Reference — Key Endpoint Groups</h2>
<p>All API endpoints are under <code>/api/</code> and require a Bearer token except where noted.</p>

<h3>Authentication</h3>
<table>
    <thead><tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Auth</th></tr></thead>
    <tbody>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/auth/login</code></td><td>Obtain a Sanctum Bearer token</td><td>None</td></tr>
        <tr><td><span class="badge badge-red">POST</span></td><td><code>/api/auth/logout</code></td><td>Revoke current token</td><td>Required</td></tr>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/auth/profile</code></td><td>Get authenticated user profile</td><td>Required</td></tr>
        <tr><td><span class="badge badge-yellow">PUT</span></td><td><code>/api/auth/profile</code></td><td>Update name, phone, region</td><td>Required</td></tr>
        <tr><td><span class="badge badge-yellow">PATCH</span></td><td><code>/api/auth/profile/password</code></td><td>Change own password</td><td>Required</td></tr>
    </tbody>
</table>

<h3>Jobs &amp; Assignments</h3>
<table>
    <thead><tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Permission</th></tr></thead>
    <tbody>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/jobs</code></td><td>List all assignments</td><td>view-jobs</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/jobs</code></td><td>Create assignment</td><td>manage-jobs</td></tr>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/jobs/mine</code></td><td>Assignments for the logged-in technician</td><td>Required</td></tr>
        <tr><td><span class="badge badge-yellow">PATCH</span></td><td><code>/api/jobs/{id}/status</code></td><td>Update assignment status</td><td>manage-jobs</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/jobs/{id}/checkin</code></td><td>Technician check-in on arrival</td><td>Required</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/jobs/{id}/complete</code></td><td>Mark assignment complete</td><td>Required</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/jobs/{id}/cancel</code></td><td>Cancel an assignment</td><td>manage-jobs</td></tr>
    </tbody>
</table>

<h3>Tickets</h3>
<table>
    <thead><tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Permission</th></tr></thead>
    <tbody>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/tickets</code></td><td>List tickets (filterable by status, priority)</td><td>view-tickets</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/tickets</code></td><td>Create ticket</td><td>create-tickets</td></tr>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/tickets/{id}</code></td><td>Ticket detail with steps &amp; audit trail</td><td>view-tickets</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/tickets/{id}/resolve</code></td><td>Resolve ticket</td><td>manage-tickets</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/tickets/{id}/steps</code></td><td>Add resolution step</td><td>manage-tickets</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/tickets/{id}/steps/{step}/transfer</code></td><td>Transfer step to another employee</td><td>manage-tickets</td></tr>
    </tbody>
</table>

<h3>POS Terminals</h3>
<table>
    <thead><tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Permission</th></tr></thead>
    <tbody>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/pos-terminals</code></td><td>List all terminals</td><td>manage-assets</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/pos-terminals/import</code></td><td>Bulk import from CSV/Excel</td><td>manage-assets</td></tr>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/pos-terminals/{terminal}</code></td><td>Terminal detail</td><td>manage-assets</td></tr>
        <tr><td><span class="badge badge-yellow">PATCH</span></td><td><code>/api/pos-terminals/{terminal}/status</code></td><td>Update terminal status</td><td>manage-assets</td></tr>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/pos-terminals/{terminal}/history</code></td><td>Service history</td><td>manage-assets</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/pos-terminals/{terminal}/service-report</code></td><td>Create service report</td><td>manage-assets</td></tr>
    </tbody>
</table>

<h3>Report Builder</h3>
<table>
    <thead><tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Permission</th></tr></thead>
    <tbody>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/report/fields</code></td><td>All available tables and columns for query building</td><td>Required</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/report/preview</code></td><td>Run a query and return rows as JSON (up to limit)</td><td>preview-reports</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/report/export</code></td><td>Export as CSV or PDF (same payload + format field)</td><td>export-reports</td></tr>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/report/templates</code></td><td>List saved templates visible to current user</td><td>Required</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/report/templates</code></td><td>Save current report config as a named template</td><td>Required</td></tr>
        <tr><td><span class="badge badge-yellow">PUT</span></td><td><code>/api/report/templates/{id}</code></td><td>Update template name / payload</td><td>Owner or manager</td></tr>
        <tr><td><span class="badge badge-red">DELETE</span></td><td><code>/api/report/templates/{id}</code></td><td>Delete a template</td><td>Owner or manager</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/report/templates/{id}/duplicate</code></td><td>Create a private copy of any visible template</td><td>Required</td></tr>
    </tbody>
</table>

<h3>Visits</h3>
<table>
    <thead><tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Permission</th></tr></thead>
    <tbody>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/visits</code></td><td>List site visits</td><td>Required</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/visits</code></td><td>Log a new visit</td><td>Required</td></tr>
        <tr><td><span class="badge badge-blue">GET</span></td><td><code>/api/visits/{id}</code></td><td>Visit detail with evidence</td><td>Required</td></tr>
        <tr><td><span class="badge badge-green">POST</span></td><td><code>/api/visits/{id}/evidence</code></td><td>Upload photo/document evidence</td><td>Required</td></tr>
    </tbody>
</table>

<h2>API Authentication (Sanctum)</h2>
<p>The system uses <strong>Laravel Sanctum</strong> for Bearer token authentication. Obtain a token from the login endpoint:</p>
<pre><code>POST /api/auth/login
Content-Type: application/json

{
    "email": "tech@example.com",
    "password": "yourpassword"
}

// Response
{
    "success": true,
    "token": "3|abc123xyz...",
    "user": { ... }
}</code></pre>

<p>Include the token in every subsequent request:</p>
<pre><code>Authorization: Bearer 3|abc123xyz...</code></pre>

<div class="callout">
    <strong>Full spec:</strong> The Swagger UI at <a href="/docs/swagger.html" target="_blank"><code>/docs/swagger.html</code></a>
    documents every endpoint with request body schemas, parameter definitions, and response examples.
</div>

@endsection
