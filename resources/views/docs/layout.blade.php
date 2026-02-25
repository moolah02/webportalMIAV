<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revival Technologies – {{ $pageTitle ?? 'Documentation Hub' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand:     #1a3a5c;
            --brand-mid: #2563a8;
            --accent:    #e85d04;
            --bg:        #f0f4f8;
            --card-bg:   #ffffff;
            --border:    #dde3ec;
            --text:      #1e293b;
            --muted:     #64748b;
            --sidebar-w: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOP NAV ── */
        .top-nav {
            background: var(--brand);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
        }
        .top-nav .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
        .top-nav .brand img { height: 38px; border-radius: 6px; }
        .top-nav .brand-text { font-weight: 700; font-size: 16px; letter-spacing: .4px; }
        .top-nav .brand-sub { font-size: 11px; font-weight: 400; opacity: .7; letter-spacing: .6px; text-transform: uppercase; }
        .top-nav .nav-links { display: flex; gap: 6px; }
        .top-nav .nav-links a {
            color: rgba(255,255,255,.8);
            text-decoration: none;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 6px;
            transition: background .2s, color .2s;
        }
        .top-nav .nav-links a:hover, .top-nav .nav-links a.active {
            background: rgba(255,255,255,.15);
            color: #fff;
        }

        /* ── LAYOUT BODY ── */
        .page-body {
            display: flex;
            flex: 1;
        }

        /* ── SIDEBAR ── */
        .docs-sidebar {
            width: var(--sidebar-w);
            background: var(--card-bg);
            border-right: 1px solid var(--border);
            padding: 28px 0;
            position: sticky;
            top: 64px;
            height: calc(100vh - 64px);
            overflow-y: auto;
            flex-shrink: 0;
        }
        .sidebar-section { margin-bottom: 8px; }
        .sidebar-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--muted);
            padding: 6px 20px 4px;
        }
        .docs-sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            font-size: 13.5px;
            color: var(--text);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background .15s, border-color .15s, color .15s;
        }
        .docs-sidebar a:hover {
            background: #f1f5fb;
            color: var(--brand-mid);
        }
        .docs-sidebar a.active {
            background: #ebf2fc;
            border-left-color: var(--brand-mid);
            color: var(--brand-mid);
            font-weight: 600;
        }
        .docs-sidebar a .icon { font-size: 16px; flex-shrink: 0; }

        /* ── MAIN CONTENT ── */
        .docs-main {
            flex: 1;
            padding: 40px 48px;
            max-width: 960px;
        }

        /* ── BREADCRUMB ── */
        .breadcrumb {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 24px;
        }
        .breadcrumb a { color: var(--brand-mid); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { margin: 0 6px; }

        /* ── CONTENT ELEMENTS ── */
        .docs-main h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--brand);
            margin-bottom: 8px;
        }
        .docs-main .subtitle {
            font-size: 15px;
            color: var(--muted);
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        .docs-main h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--brand);
            margin: 32px 0 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e8f0fb;
        }
        .docs-main h3 {
            font-size: 15px;
            font-weight: 600;
            color: #1e3a5f;
            margin: 22px 0 8px;
        }
        .docs-main p { margin-bottom: 14px; font-size: 14px; color: #334155; }
        .docs-main ul, .docs-main ol { padding-left: 20px; margin-bottom: 14px; }
        .docs-main li { margin-bottom: 6px; font-size: 14px; }
        .docs-main code {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 2px 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #c7254e;
        }
        .docs-main pre {
            background: #1e293b;
            color: #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            overflow-x: auto;
            margin: 16px 0;
            font-size: 13px;
            line-height: 1.6;
        }
        .docs-main pre code { background: none; border: none; color: inherit; padding: 0; }
        .docs-main .callout {
            border-left: 4px solid var(--brand-mid);
            background: #ebf2fc;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 16px 0;
            font-size: 13.5px;
        }
        .docs-main .callout.warning {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        .docs-main .callout.danger {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        .docs-main .callout.success {
            border-left-color: #10b981;
            background: #ecfdf5;
        }
        .docs-main table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 13.5px;
        }
        .docs-main table th {
            background: var(--brand);
            color: #fff;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
        }
        .docs-main table td {
            padding: 9px 14px;
            border-bottom: 1px solid var(--border);
        }
        .docs-main table tr:nth-child(even) td { background: #f8fafc; }

        /* ── BADGE ── */
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .3px;
        }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-purple { background: #ede9fe; color: #7c3aed; }

        /* ── FOOTER ── */
        footer {
            background: var(--brand);
            color: rgba(255,255,255,.7);
            text-align: center;
            padding: 18px 32px;
            font-size: 12px;
        }
        footer a { color: rgba(255,255,255,.85); text-decoration: none; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .docs-sidebar { display: none; }
            .docs-main { padding: 24px 20px; }
            .top-nav .nav-links { display: none; }
        }
    </style>
</head>
<body>

{{-- TOP NAV --}}
<nav class="top-nav">
    <a href="{{ url('/docs') }}" class="brand">
        <img src="{{ asset('logo/revival logo.jpeg') }}" alt="Revival Technologies Logo">
        <div>
            <div class="brand-text">Revival Technologies</div>
            <div class="brand-sub">Documentation Hub</div>
        </div>
    </a>
    <div class="nav-links">
        <a href="{{ url('/docs') }}" class="{{ request()->is('docs') ? 'active' : '' }}">Home</a>
        <a href="{{ url('/docs/system') }}" class="{{ request()->is('docs/system') ? 'active' : '' }}">System Manual</a>
        <a href="{{ url('/docs/api') }}" class="{{ request()->is('docs/api') ? 'active' : '' }}">API Docs</a>
        <a href="{{ url('/login') }}">← Back to App</a>
    </div>
</nav>

{{-- PAGE BODY --}}
<div class="page-body">

    {{-- SIDEBAR --}}
    <aside class="docs-sidebar">
        <div class="sidebar-section">
            <div class="sidebar-section-title">📘 User Manuals</div>
            <a href="{{ url('/docs/system') }}" class="{{ request()->is('docs/system') ? 'active' : '' }}">
                <span class="icon">⚙️</span> System Manual
            </a>
            <a href="{{ url('/docs/testing') }}" class="{{ request()->is('docs/testing') ? 'active' : '' }}">
                <span class="icon">🧪</span> Testing Guide
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-title">🔧 Technical Docs</div>
            <a href="{{ url('/docs/api') }}" class="{{ request()->is('docs/api') ? 'active' : '' }}">
                <span class="icon">🗂️</span> API Documentation
            </a>
            <a href="{{ url('/docs/tickets') }}" class="{{ request()->is('docs/tickets') ? 'active' : '' }}">
                <span class="icon">🎫</span> Ticket System
            </a>
            <a href="{{ url('/docs/staged-resolution') }}" class="{{ request()->is('docs/staged-resolution') ? 'active' : '' }}">
                <span class="icon">🔄</span> Staged Resolution
            </a>
            <a href="{{ url('/docs/deployment') }}" class="{{ request()->is('docs/deployment') ? 'active' : '' }}">
                <span class="icon">🚀</span> Deployment Guide
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-title">📋 Reference</div>
            <a href="{{ url('/docs/overview') }}" class="{{ request()->is('docs/overview') ? 'active' : '' }}">
                <span class="icon">🏢</span> Business Overview
            </a>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="docs-main">
        @yield('content')
    </main>

</div>

<footer>
    Revival Technologies — MIAV Dashboard &nbsp;|&nbsp;
    <a href="{{ url('/docs') }}">Docs Hub</a> &nbsp;|&nbsp;
    <a href="{{ url('/login') }}">App Login</a>
    &nbsp;&nbsp;© {{ date('Y') }}
</footer>

</body>
</html>
