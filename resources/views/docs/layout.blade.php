<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revival Technologies  {{ $pageTitle ?? 'Documentation Hub' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:         #f6f8fa;
            --surface:    #ffffff;
            --surface2:   #f0f4f8;
            --border:     #d0d7de;
            --text:       #24292f;
            --heading:    #0d1117;
            --muted:      #57606a;
            --accent:     #0969da;
            --accent2:    #1a7f37;
            --brand:      #1a3a5c;
            --sidebar-w:  264px;
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

        /*  TOP NAV  */
        .top-nav {
            background: var(--brand);
            border-bottom: 1px solid rgba(0,0,0,.12);
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
        .top-nav .brand-text { font-weight: 700; font-size: 15px; color: #fff; }
        .top-nav .brand-sub  { font-size: 10px; font-weight: 600; opacity: .65; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,.8); }
        .top-nav .hub-label {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.4px; color: rgba(255,255,255,.75);
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 6px; padding: 4px 10px;
        }
        .top-nav .nav-links { display: flex; gap: 4px; align-items: center; }
        .top-nav .nav-links a {
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 6px;
            transition: background .15s, color .15s;
        }
        .top-nav .nav-links a:hover, .top-nav .nav-links a.active {
            background: rgba(255,255,255,.15);
            color: #fff;
        }
        .top-nav .nav-links a.app-link {
            border: 1px solid rgba(255,255,255,.3);
            margin-left: 6px;
        }

        /*  LAYOUT BODY  */
        .page-body { display: flex; flex: 1; }

        /*  SIDEBAR  */
        .docs-sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 20px 0;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
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
            padding: 8px 18px 4px;
        }
        .docs-sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 18px;
            font-size: 13px;
            color: var(--text);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background .12s, border-color .12s, color .12s;
        }
        .docs-sidebar a:hover { background: var(--surface2); color: var(--heading); }
        .docs-sidebar a.active {
            background: #dbeafe;
            border-left-color: var(--accent);
            color: var(--accent);
            font-weight: 600;
        }
        .docs-sidebar a .icon { font-size: 14px; flex-shrink: 0; }
        .sidebar-divider { height: 1px; background: var(--border); margin: 8px 18px; }

        /*  MAIN CONTENT  */
        .docs-main {
            flex: 1;
            padding: 40px 52px;
            max-width: 980px;
        }

        /*  BREADCRUMB  */
        .breadcrumb {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 20px;
        }
        .breadcrumb a { color: var(--accent); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { margin: 0 5px; }

        /*  TYPOGRAPHY  */
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
            background: #f6f8fa; border: 1px solid var(--border);
            border-radius: 4px; padding: 2px 6px;
            font-family: 'Courier New', monospace; font-size: 12.5px; color: #953800;
        }
        .docs-main pre {
            background: #f6f8fa; border: 1px solid var(--border);
            border-radius: 8px; padding: 20px; overflow-x: auto;
            margin: 14px 0; font-size: 13px; line-height: 1.65;
        }
        .docs-main pre code { background: none; border: none; color: #24292f; padding: 0; }

        /*  CALLOUTS  */
        .docs-main .callout {
            border-left: 4px solid var(--accent);
            background: #dbeafe;
            padding: 13px 16px; border-radius: 0 8px 8px 0;
            margin: 14px 0; font-size: 13.5px; color: var(--text);
        }
        .docs-main .callout.warning  { border-left-color: #d97706; background: #fef3c7; }
        .docs-main .callout.danger   { border-left-color: #dc2626; background: #fee2e2; }
        .docs-main .callout.success  { border-left-color: #16a34a; background: #dcfce7; }
        .docs-main .callout strong { color: var(--heading); }

        /*  TABLES  */
        .docs-main table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13.5px; }
        .docs-main table th {
            background: #f0f4f8; color: var(--heading);
            padding: 9px 14px; text-align: left; font-weight: 600;
            border-bottom: 2px solid var(--border);
        }
        .docs-main table td { padding: 9px 14px; border-bottom: 1px solid var(--border); color: var(--text); }
        .docs-main table tr:hover td { background: #f8fafc; }

        /*  BADGES  */
        .badge { display: inline-block; padding: 2px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; letter-spacing: .3px; }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-green  { background: #dcfce7; color: #16a34a; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .badge-purple { background: #ede9fe; color: #7c3aed; }
        .badge-gray   { background: #f1f5f9; color: #475569; }

        /*  STEP NUMBERS  */
        .step-number {
            display: inline-flex; align-items: center; justify-content: center;
            width: 24px; height: 24px; border-radius: 50%;
            background: var(--accent); color: #fff;
            font-size: 12px; font-weight: 700; flex-shrink: 0; margin-right: 8px;
        }

        /*  FOOTER  */
        footer {
            background: var(--brand);
            color: rgba(255,255,255,.7);
            text-align: center;
            padding: 16px 32px;
            font-size: 12px;
        }
        footer a { color: rgba(255,255,255,.75); text-decoration: none; }
        footer a:hover { color: #fff; }

        /*  RESPONSIVE  */
        @media (max-width: 768px) {
            .docs-sidebar { display: none; }
            .docs-main { padding: 24px 18px; }
            .top-nav .nav-links { display: none; }
            .top-nav .hub-label { display: none; }
        }

        /*  SCROLLBAR  */
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
        <a href="{{ url('/docs/system') }}" class="{{ request()->is('docs/system') ? 'active' : '' }}">System Manual</a>
        <a href="{{ url('/docs/srs') }}" class="{{ request()->is('docs/srs') ? 'active' : '' }}">SRS</a>
        <a href="{{ url('/login') }}" class="app-link">&#8592; App</a>
    </div>
</nav>

<div class="page-body">
    <aside class="docs-sidebar">
        <div class="sidebar-section">
            <div class="sidebar-section-title">User Manuals</div>
            <a href="{{ url('/docs/system') }}" class="{{ request()->is('docs/system') ? 'active' : '' }}">
                <span class="icon">&#9881;&#65039;</span> System Manual
            </a>
            <a href="{{ url('/docs/mobile') }}" class="{{ request()->is('docs/mobile') ? 'active' : '' }}">
                <span class="icon">&#128241;</span> Mobile App Guide
            </a>
            <a href="{{ url('/docs/reports') }}" class="{{ request()->is('docs/reports') ? 'active' : '' }}">
                <span class="icon">&#128202;</span> Reports Manual
            </a>
            <a href="{{ url('/docs/projects') }}" class="{{ request()->is('docs/projects') ? 'active' : '' }}">
                <span class="icon">&#128203;</span> Project Flow Guide
            </a>
        </div>
        <div class="sidebar-divider"></div>
        <div class="sidebar-section">
            <div class="sidebar-section-title">Reference</div>
            <a href="{{ url('/docs/srs') }}" class="{{ request()->is('docs/srs') ? 'active' : '' }}">
                <span class="icon">&#128196;</span> SRS Document
            </a>
            <a href="{{ url('/docs/overview') }}" class="{{ request()->is('docs/overview') ? 'active' : '' }}">
                <span class="icon">&#127962;</span> Business Overview
            </a>
        </div>
    </aside>

    <main class="docs-main">
        @yield('content')
    </main>
</div>

<footer>
    Revival Technologies &mdash; MIAV Dashboard &nbsp;|&nbsp;
    <a href="{{ url('/docs') }}">Docs Hub</a> &nbsp;|&nbsp;
    <a href="{{ url('/login') }}">App Login</a>
    &nbsp;&nbsp;&copy; {{ date('Y') }}
</footer>

</body>
</html>