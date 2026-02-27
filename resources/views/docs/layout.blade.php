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
            --bg:         #0d1117;
            --surface:    #161b22;
            --surface2:   #1c2128;
            --border:     #30363d;
            --text:       #c9d1d9;
            --heading:    #f0f6fc;
            --muted:      #8b949e;
            --accent:     #1f6feb;
            --accent2:    #388bfd;
            --sidebar-w:  260px;
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
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .top-nav .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
        .top-nav .brand img { height: 34px; border-radius: 6px; }
        .top-nav .brand-text { font-weight: 700; font-size: 15px; color: var(--heading); }
        .top-nav .brand-sub { font-size: 10px; font-weight: 600; opacity: .55; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); }
        .top-nav .hub-label {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.4px; color: var(--muted); background: var(--surface2);
            border: 1px solid var(--border); border-radius: 6px; padding: 4px 10px;
        }
        .top-nav .nav-links { display: flex; gap: 4px; align-items: center; }
        .top-nav .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 6px;
            transition: background .15s, color .15s;
        }
        .top-nav .nav-links a:hover, .top-nav .nav-links a.active {
            background: var(--surface2);
            color: var(--heading);
        }

        /* ── LAYOUT BODY ── */
        .page-body { display: flex; flex: 1; }

        /* ── SIDEBAR ── */
        .docs-sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 24px 0;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
            flex-shrink: 0;
        }
        .sidebar-section { margin-bottom: 6px; }
        .sidebar-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--muted);
            padding: 6px 18px 4px;
        }
        .docs-sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 18px;
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background .12s, border-color .12s, color .12s;
        }
        .docs-sidebar a:hover { background: var(--surface2); color: var(--heading); }
        .docs-sidebar a.active {
            background: rgba(31,111,235,.1);
            border-left-color: var(--accent);
            color: var(--accent2);
            font-weight: 600;
        }
        .docs-sidebar a .icon { font-size: 14px; flex-shrink: 0; }

        /* ── MAIN CONTENT ── */
        .docs-main {
            flex: 1;
            padding: 40px 52px;
            max-width: 980px;
        }

        /* ── BREADCRUMB ── */
        .breadcrumb {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 20px;
        }
        .breadcrumb a { color: var(--accent2); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { margin: 0 5px; }

        /* ── TYPOGRAPHY ── */
        .docs-main h1 { font-size: 26px; font-weight: 800; color: var(--heading); margin-bottom: 8px; }
        .docs-main .subtitle {
            font-size: 14.5px; color: var(--muted); margin-bottom: 28px;
            padding-bottom: 20px; border-bottom: 1px solid var(--border);
        }
        .docs-main h2 {
            font-size: 17px; font-weight: 700; color: var(--heading);
            margin: 32px 0 12px; padding-bottom: 6px; border-bottom: 1px solid var(--border);
        }
        .docs-main h3 { font-size: 14.5px; font-weight: 600; color: var(--heading); margin: 22px 0 8px; }
        .docs-main p  { margin-bottom: 14px; font-size: 14px; color: var(--text); }
        .docs-main ul, .docs-main ol { padding-left: 20px; margin-bottom: 14px; }
        .docs-main li { margin-bottom: 6px; font-size: 14px; color: var(--text); }
        .docs-main strong { color: var(--heading); }
        .docs-main code {
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 4px; padding: 2px 6px;
            font-family: 'Courier New', monospace; font-size: 12.5px; color: #e3b341;
        }
        .docs-main pre {
            background: #010409; border: 1px solid var(--border);
            border-radius: 8px; padding: 20px; overflow-x: auto;
            margin: 14px 0; font-size: 13px; line-height: 1.65;
        }
        .docs-main pre code { background: none; border: none; color: #c9d1d9; padding: 0; }

        /* ── CALLOUTS ── */
        .docs-main .callout {
            border-left: 4px solid var(--accent);
            background: rgba(31,111,235,.08);
            padding: 13px 16px; border-radius: 0 8px 8px 0;
            margin: 14px 0; font-size: 13.5px; color: var(--text);
        }
        .docs-main .callout.warning  { border-left-color: #e3b341; background: rgba(227,179,65,.08); }
        .docs-main .callout.danger   { border-left-color: #f85149; background: rgba(248,81,73,.08); }
        .docs-main .callout.success  { border-left-color: #3fb950; background: rgba(63,185,80,.08); }
        .docs-main .callout strong { color: var(--heading); }

        /* ── TABLES ── */
        .docs-main table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13.5px; }
        .docs-main table th {
            background: var(--surface2); color: var(--heading);
            padding: 9px 14px; text-align: left; font-weight: 600;
            border-bottom: 1px solid var(--border);
        }
        .docs-main table td { padding: 9px 14px; border-bottom: 1px solid var(--border); color: var(--text); }
        .docs-main table tr:hover td { background: var(--surface2); }

        /* ── BADGES ── */
        .badge { display: inline-block; padding: 2px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; letter-spacing: .3px; }
        .badge-blue   { background: rgba(31,111,235,.2);  color: #79b8ff; }
        .badge-green  { background: rgba(63,185,80,.2);   color: #56d364; }
        .badge-yellow { background: rgba(227,179,65,.2);  color: #e3b341; }
        .badge-red    { background: rgba(248,81,73,.2);   color: #f85149; }
        .badge-purple { background: rgba(188,140,255,.2); color: #bc8cff; }

        /* ── FOOTER ── */
        footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            color: var(--muted);
            text-align: center;
            padding: 16px 32px;
            font-size: 12px;
        }
        footer a { color: var(--muted); text-decoration: none; }
        footer a:hover { color: var(--heading); }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .docs-sidebar { display: none; }
            .docs-main { padding: 24px 18px; }
            .top-nav .nav-links { display: none; }
            .top-nav .hub-label { display: none; }
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--muted); }
    </style>
</head>
<body>

<nav class="top-nav">
    <a href="{{ url('/docs') }}" class="brand">
        <img src="{{ asset('logo/revival logo.jpeg') }}" alt="Revival Technologies">
        <div>
            <div class="brand-text">Revival Technologies</div>
            <div class="brand-sub">MIAV Dashboard</div>
        </div>
    </a>
    <span class="hub-label">DOCUMENTATION HUB</span>
    <div class="nav-links">
        <a href="{{ url('/docs') }}" class="{{ request()->is('docs') ? 'active' : '' }}">Home</a>
        <a href="{{ url('/docs/system') }}" class="{{ request()->is('docs/system') ? 'active' : '' }}">System</a>
        <a href="{{ url('/docs/api') }}" class="{{ request()->is('docs/api') ? 'active' : '' }}">API</a>
        <a href="{{ url('/docs/deployment') }}" class="{{ request()->is('docs/deployment') ? 'active' : '' }}">Deploy</a>
        <a href="{{ url('/login') }}" style="border:1px solid var(--border);margin-left:6px;">← App</a>
    </div>
</nav>

<div class="page-body">
    <aside class="docs-sidebar">
        <div class="sidebar-section">
            <div class="sidebar-section-title">User Manuals</div>
            <a href="{{ url('/docs/system') }}" class="{{ request()->is('docs/system') ? 'active' : '' }}">
                <span class="icon">⚙️</span> System Manual
            </a>
            <a href="{{ url('/docs/testing') }}" class="{{ request()->is('docs/testing') ? 'active' : '' }}">
                <span class="icon">🧪</span> Testing Guide
            </a>
            <a href="{{ url('/docs/tickets') }}" class="{{ request()->is('docs/tickets') ? 'active' : '' }}">
                <span class="icon">🎫</span> Ticket System
            </a>
            <a href="{{ url('/docs/staged-resolution') }}" class="{{ request()->is('docs/staged-resolution') ? 'active' : '' }}">
                <span class="icon">🔄</span> Staged Resolution
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-title">Technical Docs</div>
            <a href="{{ url('/docs/api') }}" class="{{ request()->is('docs/api') ? 'active' : '' }}">
                <span class="icon">🗂️</span> API Documentation
            </a>
            <a href="{{ url('/docs/deployment') }}" class="{{ request()->is('docs/deployment') ? 'active' : '' }}">
                <span class="icon">🚀</span> Deployment Guide
            </a>
            <a href="{{ url('/docs/overview') }}" class="{{ request()->is('docs/overview') ? 'active' : '' }}">
                <span class="icon">🏢</span> Business Overview
            </a>
        </div>
    </aside>

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
