<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Documentation' }} · MIAV</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo/revival-logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ground: #F3F5F8; --surface: #FFFFFF; --surface-2: #F7F9FB;
            --line: #E1E6EC; --line-strong: #CBD3DD;
            --ink: #16202C; --ink-2: #445162; --muted: #6A7686;
            --accent: #2B64A8; --accent-ink: #1F4F87; --accent-soft: #E9F0F9;
            --good: #1D7F46; --good-soft: #E4F3E9; --warn: #9A6412; --warn-soft: #FBF1DE; --crit: #B83232; --crit-soft: #FBE8E7;
            --sans: "IBM Plex Sans", "Segoe UI", system-ui, -apple-system, sans-serif;
            --mono: "IBM Plex Mono", ui-monospace, "Cascadia Mono", Consolas, monospace;
            --side-w: 256px;
        }

        body { font-family: var(--sans); background: var(--ground); color: var(--ink); line-height: 1.6; min-height: 100vh; display: flex; flex-direction: column; -webkit-font-smoothing: antialiased; }
        .i { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
        :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        html { scrollbar-width: thin; scrollbar-color: var(--line-strong) transparent; }

        /* Top bar */
        .top { position: sticky; top: 0; z-index: 100; height: 60px; display: flex; align-items: center; gap: 18px; padding: 0 28px; background: var(--surface); border-bottom: 1px solid var(--line); }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--ink); }
        .brand-mark { width: 34px; height: 34px; border-radius: 7px; overflow: hidden; border: 1px solid var(--line); display: flex; align-items: center; padding-left: 2px; background: #fff; }
        .brand-mark img { height: 30px; width: auto; max-width: none; display: block; }
        .brand strong { display: block; font-size: 14px; font-weight: 600; line-height: 1.2; }
        .brand span { display: block; font-size: 11.5px; color: var(--muted); line-height: 1.2; }
        .top-nav { display: flex; gap: 2px; margin-left: 12px; }
        .top-nav a { font-size: 13.5px; color: var(--ink-2); text-decoration: none; padding: 6px 10px; border-radius: 6px; }
        .top-nav a:hover { background: var(--surface-2); color: var(--ink); }
        .top-nav a.active { background: var(--accent-soft); color: var(--accent-ink); font-weight: 500; }
        .top-actions { margin-left: auto; display: flex; gap: 8px; align-items: center; }
        .btn { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 12px; border-radius: 8px; border: 1px solid var(--line-strong); background: var(--surface); color: var(--ink); font: 500 13px var(--sans); text-decoration: none; cursor: pointer; }
        .btn:hover { background: var(--surface-2); }
        .btn .i { width: 15px; height: 15px; }

        /* Body */
        .page-body { display: flex; flex: 1; }
        .side { width: var(--side-w); flex-shrink: 0; background: var(--surface); border-right: 1px solid var(--line); padding: 18px 10px; position: sticky; top: 60px; height: calc(100vh - 60px); overflow-y: auto; }
        .side-sec + .side-sec { margin-top: 14px; }
        .side-title { font-size: 11px; font-weight: 500; letter-spacing: .04em; color: var(--muted); padding: 0 10px 4px; }
        .side a { display: flex; align-items: center; gap: 10px; padding: 6px 10px; border-radius: 6px; font-size: 13.5px; color: var(--ink-2); text-decoration: none; }
        .side a .i { color: var(--muted); }
        .side a:hover { background: var(--surface-2); color: var(--ink); }
        .side a.active { background: var(--accent-soft); color: var(--accent-ink); font-weight: 500; }
        .side a.active .i { color: var(--accent-ink); }

        .main { flex: 1; min-width: 0; padding: 36px 48px 64px; }
        .doc { max-width: 860px; background: var(--surface); border: 1px solid var(--line); border-radius: 10px; padding: 36px 44px; }

        /* Content typography */
        .breadcrumb { font-size: 12.5px; color: var(--muted); margin-bottom: 18px; }
        .breadcrumb a { color: var(--accent-ink); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { margin: 0 5px; }
        .doc h1 { font-size: 24px; font-weight: 600; letter-spacing: -.015em; color: var(--ink); margin-bottom: 6px; text-wrap: balance; }
        .doc .subtitle { font-size: 14.5px; color: var(--muted); margin-bottom: 26px; padding-bottom: 20px; border-bottom: 1px solid var(--line); }
        .doc h2 { font-size: 17px; font-weight: 600; color: var(--ink); margin: 32px 0 10px; padding-bottom: 6px; border-bottom: 1px solid var(--line); }
        .doc h3 { font-size: 14.5px; font-weight: 600; color: var(--ink); margin: 22px 0 8px; }
        .doc p { margin-bottom: 14px; font-size: 14px; color: var(--ink-2); max-width: 72ch; }
        .doc ul, .doc ol { padding-left: 20px; margin-bottom: 14px; }
        .doc li { margin-bottom: 6px; font-size: 14px; color: var(--ink-2); }
        .doc strong { color: var(--ink); font-weight: 600; }
        .doc a { color: var(--accent-ink); }
        .doc code { background: var(--surface-2); border: 1px solid var(--line); border-radius: 4px; padding: 1px 5px; font-family: var(--mono); font-size: 12.5px; color: var(--ink); }
        .doc pre { background: var(--surface-2); border: 1px solid var(--line); border-radius: 8px; padding: 18px; overflow-x: auto; margin: 14px 0; font-size: 13px; line-height: 1.65; }
        .doc pre code { background: none; border: none; padding: 0; }

        .doc .callout { border: 1px solid #C9D9EE; background: var(--accent-soft); padding: 12px 16px; border-radius: 8px; margin: 14px 0; font-size: 13.5px; color: var(--ink-2); }
        .doc .callout.warning { border-color: #F0DDB6; background: var(--warn-soft); }
        .doc .callout.danger  { border-color: #F2CACA; background: var(--crit-soft); }
        .doc .callout.success { border-color: #C6E6D2; background: var(--good-soft); }

        .doc table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13.5px; display: block; overflow-x: auto; }
        .doc table th { background: var(--surface-2); color: var(--muted); font-size: 11.5px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; padding: 9px 14px; text-align: left; border-bottom: 1px solid var(--line); }
        .doc table td { padding: 9px 14px; border-bottom: 1px solid var(--line); color: var(--ink-2); vertical-align: top; }

        .badge { display: inline-block; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        .badge-blue, .badge-purple { background: var(--accent-soft); color: var(--accent-ink); }
        .badge-green  { background: var(--good-soft); color: var(--good); }
        .badge-yellow { background: var(--warn-soft); color: var(--warn); }
        .badge-red    { background: var(--crit-soft); color: var(--crit); }
        .badge-gray   { background: var(--surface-2); color: var(--ink-2); border: 1px solid var(--line); }

        .step-number { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: var(--accent-soft); color: var(--accent-ink); font-size: 12px; font-weight: 600; flex-shrink: 0; margin-right: 8px; }

        footer { border-top: 1px solid var(--line); background: var(--surface); color: var(--muted); padding: 14px 28px; font-size: 12.5px; display: flex; gap: 16px; flex-wrap: wrap; }
        footer a { color: var(--ink-2); text-decoration: none; }
        footer a:hover { color: var(--accent-ink); }
        footer span:last-child { margin-left: auto; }

        @media (max-width: 900px) {
            .side { display: none; }
            .top-nav { display: none; }
            .main { padding: 18px 16px 40px; }
            .doc { padding: 22px 18px; }
            .top { padding: 0 16px; }
        }

        @media print {
            .top, .side, footer { display: none !important; }
            .page-body { display: block; }
            .main { padding: 0; }
            .doc { max-width: 100%; border: 0; padding: 0; }
            body { background: #fff; }
            a { color: inherit; text-decoration: none; }
            .doc pre { white-space: pre-wrap; word-break: break-all; }
        }
    </style>
</head>
<body>
@include('layouts.partials.icons')

<header class="top">
    <a href="{{ url('/docs') }}" class="brand">
        <span class="brand-mark"><img src="{{ asset('logo/revival-logo.jpeg') }}" alt="Revival Technologies"></span>
        <span><strong>MIAV</strong><span>Documentation</span></span>
    </a>
    <nav class="top-nav" aria-label="Documentation">
        <a href="{{ url('/docs') }}" class="{{ request()->is('docs') ? 'active' : '' }}">Home</a>
        <a href="{{ url('/docs/system') }}" class="{{ request()->is('docs/system') ? 'active' : '' }}">System Manual</a>
        <a href="{{ url('/docs/srs') }}" class="{{ request()->is('docs/srs') ? 'active' : '' }}">SRS</a>
    </nav>
    <div class="top-actions">
        @auth
            @if(auth()->user()->roles->whereIn('name', ['super_admin', 'administrator'])->isNotEmpty())
                @if(!request()->is('docs'))
                    <a href="{{ url('/admin/docs/' . basename(request()->path()) . '/edit') }}" class="btn"><svg class="i"><use href="#i-edit"/></svg>Edit Page</a>
                @endif
                <a href="{{ url('/admin/docs') }}" class="btn"><svg class="i"><use href="#i-settings"/></svg>Manage Docs</a>
            @endif
        @endauth
        <button type="button" onclick="window.print()" class="btn"><svg class="i"><use href="#i-download"/></svg>Download PDF</button>
        <a href="{{ url('/login') }}" class="btn"><svg class="i"><use href="#i-arrow-left"/></svg>Back to MIAV</a>
    </div>
</header>

<div class="page-body">
    <aside class="side">
        <div class="side-sec">
            <div class="side-title">User Manuals</div>
            <a href="{{ url('/docs/system') }}" class="{{ request()->is('docs/system') ? 'active' : '' }}"><svg class="i"><use href="#i-book"/></svg>System Manual</a>
            <a href="{{ url('/docs/mobile') }}" class="{{ request()->is('docs/mobile') ? 'active' : '' }}"><svg class="i"><use href="#i-phone"/></svg>Mobile App Guide</a>
            <a href="{{ url('/docs/reports') }}" class="{{ request()->is('docs/reports') ? 'active' : '' }}"><svg class="i"><use href="#i-chart"/></svg>Reports Manual</a>
            <a href="{{ url('/docs/projects') }}" class="{{ request()->is('docs/projects') ? 'active' : '' }}"><svg class="i"><use href="#i-folder"/></svg>Project Flow Guide</a>
        </div>
        <div class="side-sec">
            <div class="side-title">Reference</div>
            <a href="{{ url('/docs/srs') }}" class="{{ request()->is('docs/srs') ? 'active' : '' }}"><svg class="i"><use href="#i-file"/></svg>SRS Document</a>
            <a href="{{ url('/docs/overview') }}" class="{{ request()->is('docs/overview') ? 'active' : '' }}"><svg class="i"><use href="#i-briefcase"/></svg>Business Overview</a>
        </div>
    </aside>

    <main class="main">
        <div class="doc">
            {{-- Doc pages can be edited in the app (Manage Docs) and older content carries
                 emoji; drop them on the way out so the docs keep the portal's look. --}}
            @php ob_start(); @endphp
            @yield('content')
            @php
                echo preg_replace('/(?:[\x{1F000}-\x{1FAFF}]|[\x{2600}-\x{2604}\x{2607}-\x{2712}\x{2714}\x{2716}\x{2718}-\x{27BF}]|[\x{2B00}-\x{2BFF}]|[\x{23E9}-\x{23FA}])[\x{FE0F}\x{200D}]*\s?/u', '', ob_get_clean());
            @endphp
        </div>
    </main>
</div>

<footer>
    <span>Revival Technologies · MIAV</span>
    <a href="{{ url('/docs') }}">Documentation home</a>
    <a href="{{ url('/login') }}">Sign in to MIAV</a>
    <span>&copy; {{ date('Y') }}</span>
</footer>

</body>
</html>
