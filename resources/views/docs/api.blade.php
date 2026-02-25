@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>›</span> API Documentation
</div>

<h1>API Documentation</h1>
<p class="subtitle">
    REST API reference for the MIAV Dashboard — web and future mobile integrations.
    <span style="float:right;" class="badge badge-blue">Technical Reference</span>
</p>

{{-- SWAGGER BANNER --}}
<div style="background:linear-gradient(135deg,#0f172a,#1e3a5c);border-radius:12px;padding:28px 32px;color:#fff;margin-bottom:32px;">
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
        <div style="font-size:40px;">🗂️</div>
        <div style="flex:1;">
            <h2 style="color:#fff;font-size:18px;margin:0 0 6px;border:none;padding:0;">Swagger / OpenAPI UI</h2>
            <p style="opacity:.8;font-size:13.5px;margin:0;">
                The interactive Swagger UI provides a live, testable reference for every API endpoint.
                Once configured, it will be available at <code style="background:rgba(255,255,255,.1);border:none;color:#93c5fd;">/api-docs</code>.
            </p>
        </div>
        <div>
            <span style="background:#e85d04;color:#fff;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;display:inline-block;">
                Coming Soon — See Setup Below ↓
            </span>
        </div>
    </div>
</div>

<div class="callout warning">
    <strong>Setup Required:</strong> Swagger (L5-Swagger) needs to be installed on the server.
    Follow the steps below. Once installed, the full interactive API docs will be live at <code>/api-docs</code>.
</div>

<h2>Installing Swagger (L5-Swagger)</h2>

<h3>Step 1 — Install the Package</h3>
<pre><code>composer require darkaonline/l5-swagger</code></pre>

<h3>Step 2 — Publish the Config</h3>
<pre><code>php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"</code></pre>

<h3>Step 3 — Generate Docs</h3>
<pre><code>php artisan l5-swagger:generate</code></pre>

<h3>Step 4 — Access the UI</h3>
<p>Navigate to <code>http://51.21.252.67/api-docs</code> in your browser.</p>

<div class="callout">
    <strong>Note:</strong> Add annotations to your controllers using <code>@OA\Get</code>, <code>@OA\Post</code>, etc.
    The generator will scan all controllers and build the spec automatically.
</div>

<h2>Current API Endpoints Reference</h2>
<p>These are the main API-style routes in the system. They use standard Laravel conventions.</p>

<h3>Authentication</h3>
<table>
    <thead>
        <tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Auth</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-green">POST</span></td>
            <td><code>/login</code></td>
            <td>Authenticate user, create session</td>
            <td>Guest only</td>
        </tr>
        <tr>
            <td><span class="badge badge-red">POST</span></td>
            <td><code>/logout</code></td>
            <td>Destroy session</td>
            <td>Required</td>
        </tr>
    </tbody>
</table>

<h3>Employees</h3>
<table>
    <thead>
        <tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Permission</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-blue">GET</span></td>
            <td><code>/employees</code></td>
            <td>List all employees</td>
            <td>manage-employees</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">POST</span></td>
            <td><code>/employees</code></td>
            <td>Create new employee</td>
            <td>manage-employees</td>
        </tr>
        <tr>
            <td><span class="badge badge-blue">GET</span></td>
            <td><code>/employees/{id}</code></td>
            <td>View employee details</td>
            <td>manage-employees</td>
        </tr>
        <tr>
            <td><span class="badge badge-yellow">PUT</span></td>
            <td><code>/employees/{id}</code></td>
            <td>Update employee</td>
            <td>manage-employees</td>
        </tr>
        <tr>
            <td><span class="badge badge-red">DELETE</span></td>
            <td><code>/employees/{id}</code></td>
            <td>Deactivate / remove employee</td>
            <td>super_admin</td>
        </tr>
    </tbody>
</table>

<h3>Tickets</h3>
<table>
    <thead>
        <tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Permission</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-blue">GET</span></td>
            <td><code>/tickets</code></td>
            <td>List tickets (filterable)</td>
            <td>view-tickets</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">POST</span></td>
            <td><code>/tickets</code></td>
            <td>Create ticket</td>
            <td>create-tickets</td>
        </tr>
        <tr>
            <td><span class="badge badge-blue">GET</span></td>
            <td><code>/tickets/{id}</code></td>
            <td>View ticket detail</td>
            <td>view-tickets</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">POST</span></td>
            <td><code>/tickets/{id}/steps</code></td>
            <td>Add resolution step</td>
            <td>manage-tickets</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">POST</span></td>
            <td><code>/tickets/{id}/steps/{step}/transfer</code></td>
            <td>Transfer step to another employee</td>
            <td>manage-tickets</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">POST</span></td>
            <td><code>/tickets/{id}/resolve</code></td>
            <td>Resolve the ticket</td>
            <td>manage-tickets</td>
        </tr>
    </tbody>
</table>

<h3>Assets / POS Terminals</h3>
<table>
    <thead>
        <tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Permission</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-blue">GET</span></td>
            <td><code>/pos-terminals</code></td>
            <td>List all POS terminals</td>
            <td>manage-assets</td>
        </tr>
        <tr>
            <td><span class="badge badge-green">POST</span></td>
            <td><code>/pos-terminals/import</code></td>
            <td>Bulk import from CSV/Excel</td>
            <td>manage-assets</td>
        </tr>
    </tbody>
</table>

<h2>API Authentication (Sanctum)</h2>
<p>The system uses <strong>Laravel Sanctum</strong> for API token authentication. To obtain a token:</p>
<pre><code>POST /api/token
Content-Type: application/json

{
    "email": "your@email.com",
    "password": "yourpassword",
    "device_name": "mobile_app"
}

// Response
{
    "token": "3|abc123xyz..."
}</code></pre>

<p>Use the token in subsequent requests:</p>
<pre><code>Authorization: Bearer 3|abc123xyz...</code></pre>

<div class="callout success">
    <strong>Next Step:</strong> Install L5-Swagger (instructions above) to get the full interactive UI with live request testing.
    Run the commands on the production server via SSH.
</div>

@endsection
