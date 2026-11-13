@extends('docs.layout')
@section('content')
@if(!empty(trim($page->content ?? '')))
    {!! $page->content !!}
@else

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>&rsaquo;</span> Reporting Feed &amp; Power BI
</div>

<h1>Reporting Feed &amp; Power BI</h1>
<p class="subtitle">
    How an analyst connects Power BI &mdash; or any reporting tool &mdash; to MIAV data, and how to give
    and withdraw that access.
    <span style="float:right;" class="badge badge-yellow">Admins &amp; Analysts</span>
</p>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:20px 24px;margin-bottom:32px;">
    <div style="font-weight:700;font-size:13px;margin-bottom:10px;color:#0c4a6e;">In this guide</div>
    <ol style="columns:2;column-gap:28px;list-style-position:inside;font-size:13.5px;">
        <li><a href="#what" style="color:#0369a1;text-decoration:none;">What the feed is</a></li>
        <li><a href="#issue" style="color:#0369a1;text-decoration:none;">Giving someone access</a></li>
        <li><a href="#datasets" style="color:#0369a1;text-decoration:none;">The six datasets</a></li>
        <li><a href="#powerbi" style="color:#0369a1;text-decoration:none;">Connecting Power BI</a></li>
        <li><a href="#paging" style="color:#0369a1;text-decoration:none;">Paging and refreshing</a></li>
        <li><a href="#copy" style="color:#0369a1;text-decoration:none;">Keeping your own copy</a></li>
        <li><a href="#database" style="color:#0369a1;text-decoration:none;">Why not a database login</a></li>
        <li><a href="#withdraw" style="color:#0369a1;text-decoration:none;">Withdrawing access</a></li>
        <li><a href="#trouble" style="color:#0369a1;text-decoration:none;">When it does not work</a></li>
    </ol>
</div>

<h2 id="what">1. What the feed is</h2>
<p>
    A read-only web address that returns MIAV data as tables, ready for a reporting tool. It is reached
    over the same connection as this portal, so no database port is opened and nothing is installed
    anywhere.
</p>
<p>Access is by <strong>token</strong> &mdash; a long secret string sent with every request. A token:</p>
<ul>
    <li>can <strong>read</strong> the feed and nothing else &mdash; it cannot record a visit, change a terminal or sign in here;</li>
    <li>can be tied to <strong>one client</strong>, so the holder sees only that client&rsquo;s terminals, visits, findings and tickets;</li>
    <li>can be <strong>withdrawn</strong> at any moment, which stops it working immediately.</li>
</ul>
<div class="callout">
    <strong>Base address:</strong> <code>{{ url('/api/bi') }}</code>
</div>

<h2 id="issue">2. Giving someone access</h2>
<ol>
    <li>Go to <strong>System Settings &rarr; Reporting Access</strong>.</li>
    <li>Under <em>Issue a token</em>, type who it is for &mdash; a name you will recognise months from now.</li>
    <li>Choose the client under <strong>Limit to one client</strong>.
        <br><span style="color:#57606a;font-size:13px;">Anyone outside the company should always be limited to the client they work for. Leaving it on
        &ldquo;Every client&rdquo; means that person sees every client MIAV serves.</span></li>
    <li>Press <strong>Issue token</strong>.</li>
    <li><strong>Copy the token immediately.</strong> It is shown once and cannot be read back.</li>
    <li>Send it privately &mdash; not in a group chat.</li>
</ol>
<div class="callout warning">
    <strong>If you lose it:</strong> nothing is broken. Withdraw that token and issue another.
</div>

<h2 id="datasets">3. The six datasets</h2>
<p>
    Each is a flat table, already joined, so the reporting tool does not need to know how MIAV stores
    anything. Call the base address and it lists them with descriptions.
</p>
<table>
    <thead><tr><th>Address</th><th>One row is&hellip;</th></tr></thead>
    <tbody>
        <tr><td><code>{{ url('/api/bi/terminals') }}</code></td><td>a POS terminal, with its client, region and whether it has ever been visited</td></tr>
        <tr><td><code>{{ url('/api/bi/visits') }}</code></td><td>a visit, with the technician, whether the site was reached and why not</td></tr>
        <tr><td><code>{{ url('/api/bi/findings') }}</code></td><td>one device examined on one visit &mdash; status, state, condition</td></tr>
        <tr><td><code>{{ url('/api/bi/tickets') }}</code></td><td>a support ticket</td></tr>
        <tr><td><code>{{ url('/api/bi/clients') }}</code></td><td>a client</td></tr>
        <tr><td><code>{{ url('/api/bi/technicians') }}</code></td><td>a member of field staff &mdash; name, number and role only</td></tr>
    </tbody>
</table>
<div class="callout">
    <strong>Relating them:</strong> <code>findings</code> joins to <code>terminals</code> on <code>terminal_id</code>
    and to <code>visits</code> on <code>visit_id</code>; <code>visits</code> joins to <code>technicians</code>
    by the technician&rsquo;s name or number.
</div>

<h2 id="powerbi">4. Connecting Power BI</h2>
<p>Each dataset becomes its own query. Repeat these steps for each one you want.</p>
<ol>
    <li>In Power BI Desktop choose <strong>Get Data &rarr; Web</strong>.</li>
    <li>Select <strong>Advanced</strong>.</li>
    <li>Under <em>URL parts</em> enter the dataset address, for example <code>{{ url('/api/bi/visits') }}</code></li>
    <li>Under <em>HTTP request header parameters</em> add:
        <ul>
            <li>name: <code>Authorization</code></li>
            <li>value: <code>Bearer &lt;your token&gt;</code> &mdash; the word Bearer, a space, then the token</li>
        </ul>
    </li>
    <li>Press <strong>OK</strong>. If asked how to sign in, choose <strong>Anonymous</strong> &mdash; the token in the header is the credential.</li>
    <li>Power BI shows the response. Expand the <strong>data</strong> field, choose <strong>Convert To Table</strong>, then expand the columns.</li>
    <li><strong>Close &amp; Apply.</strong></li>
</ol>
<div class="callout success">
    <strong>Tip:</strong> name each query after its dataset &mdash; Terminals, Visits, Findings &mdash; then build
    the relationships in the Model view as usual.
</div>

<h2 id="paging">5. Paging and refreshing</h2>
<p>Every response carries the rows plus where to find the rest:</p>
<ul>
    <li><code>data</code> &mdash; the rows</li>
    <li><code>total</code> &mdash; how many there are altogether</li>
    <li><code>next_page</code> &mdash; the address of the next page, or empty when there are no more</li>
</ul>
<p>
    Up to <strong>1,000 rows</strong> come back at once (<code>?per_page=1000</code>). Below that there is
    only one page and <code>next_page</code> is empty, so there is nothing more to do. Above it, follow
    <code>next_page</code> until it is empty.
</p>
<p>
    To refresh only what changed, add <code>?since=2026-01-31</code>. Only rows created or changed on or
    after that date come back, which keeps a daily refresh small.
</p>

<h2 id="copy">6. Keeping your own copy</h2>
<p>
    To hold the data in your own warehouse rather than query it live, add <code>?format=csv</code> to any
    dataset. The whole table comes back as one CSV file, unpaged:
</p>
<div class="callout">
    <code>{{ url('/api/bi/findings') }}?format=csv</code>
</div>
<p>
    Combine it with <code>since</code> to copy only what has changed since the last run &mdash;
    <code>?format=csv&amp;since=2026-01-31</code> &mdash; which is how most nightly copies are built.
    Any warehouse, spreadsheet or load script reads CSV.
</p>
<div class="callout success">
    <strong>Power BI already keeps a copy.</strong> In Import mode it stores the rows inside the report and
    refreshes on a schedule. A separate copy is only needed when other tools must read the same data.
</div>

<h2 id="database">7. Why not a database login</h2>
<p>
    A direct connection to the MIAV database is not offered, and the reason is not inconvenience.
    The database holds every client together in the same tables, alongside employee password hashes,
    access tokens and session records. A database login cannot be limited to one client&rsquo;s rows,
    so it would hand over all of it.
</p>
<p>The feed carries the same information for the client in question, with three differences that matter:</p>
<ul>
    <li>it can be <strong>limited to one client</strong>;</li>
    <li>it can be <strong>withdrawn</strong> in one click, where a database password must be changed everywhere it is used;</li>
    <li>it records <strong>when it was last used</strong>, so unused or unexpected access is visible.</li>
</ul>

<h2 id="withdraw">8. Withdrawing access</h2>
<ol>
    <li>Go to <strong>System Settings &rarr; Reporting Access</strong>.</li>
    <li>Find the token in the list &mdash; it shows what it can see and when it was last used.</li>
    <li>Press <strong>Withdraw</strong> and confirm.</li>
</ol>
<p>It stops working at once. Nothing needs doing on the other side; their next refresh simply fails.</p>

<h2 id="trouble">9. When it does not work</h2>
<table>
    <thead><tr><th>What is seen</th><th>What it means</th></tr></thead>
    <tbody>
        <tr><td><code>401</code>, or Power BI asking for credentials</td><td>The token is wrong, has been withdrawn, or the header is not <code>Bearer &lt;token&gt;</code>.</td></tr>
        <tr><td><code>403</code></td><td>The token is being used on an address outside the feed. Only the six datasets are open to it.</td></tr>
        <tr><td><code>"total": 0</code></td><td>Correct behaviour, no data. Either nothing has been recorded yet, or the token is limited to a client with nothing against it.</td></tr>
        <tr><td>Fewer rows than expected</td><td>Check the <code>scope</code> line at <code>{{ url('/api/bi') }}</code>. It states which client the token is limited to.</td></tr>
        <tr><td>&ldquo;It is not working&rdquo;</td><td>Open Reporting Access. If the token still says <em>never used</em>, the request never arrived and the problem is on their side.</td></tr>
    </tbody>
</table>

@endif
@endsection
