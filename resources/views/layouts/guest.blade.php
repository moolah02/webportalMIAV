<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $guestTitle = match (true) {
            request()->routeIs('password.request') => 'Reset your password',
            request()->routeIs('password.reset')   => 'Choose a new password',
            request()->routeIs('password.confirm') => 'Confirm your password',
            request()->routeIs('register')         => 'Create an account',
            request()->routeIs('verification.*')   => 'Verify your email',
            default                                => 'MIAV',
        };
    @endphp
    <title>{{ $guestTitle }} – MIAV · Revival Technologies</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo/revival-logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind utilities used by the auth form components --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ground: #F3F5F8; --surface: #FFFFFF; --line: #DCE2E9; --line-strong: #C7D0DB;
            --ink: #16202C; --ink-2: #445162; --muted: #6A7686;
            --accent: #2B64A8; --accent-ink: #1F4F87; --accent-soft: #E9F0F9;
            --good: #1D7F46; --good-soft: #E4F3E9; --crit: #B83232; --crit-soft: #FBE8E7;
            --sans: "IBM Plex Sans", "Segoe UI", system-ui, -apple-system, sans-serif;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: var(--ground); color: var(--ink); font: 400 15px/1.5 var(--sans); -webkit-font-smoothing: antialiased; display: flex; flex-direction: column; }
        :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .g-wrap { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .g-box { width: 100%; max-width: 400px; display: flex; flex-direction: column; gap: 22px; }
        .g-logo { width: 164px; background: #FFFFFF; border-radius: 8px; padding: 6px 8px; border: 1px solid var(--line); }
        .g-logo img { display: block; width: 100%; height: auto; }
        .g-card { background: var(--surface); border: 1px solid var(--line); border-radius: 12px; padding: 26px 26px 24px; }
        .g-card h1 { margin: 0 0 14px; font-size: 22px; font-weight: 600; letter-spacing: -.015em; }
        .g-back { font-size: 13.5px; color: var(--accent-ink); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .g-back:hover { text-decoration: underline; }
        .g-back svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; }
        .g-foot { display: flex; justify-content: space-between; gap: 12px; padding: 16px 24px; font-size: 12.5px; color: var(--muted); border-top: 1px solid var(--line); background: var(--surface); }

        /* The auth forms use the stock form components — give them the portal look */
        .g-form { font-family: var(--sans); }
        .g-form form > * + * { margin-top: 16px !important; }
        .g-form .text-sm.text-gray-600 { color: var(--ink-2) !important; font-size: 14px; line-height: 1.55; }
        .g-form label { display: block; font-size: 13.5px !important; font-weight: 500 !important; color: var(--ink-2) !important; margin-bottom: 6px; }
        .g-form input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]) {
            display: block; width: 100%; height: 44px; margin-top: 0 !important; padding: 0 12px;
            border: 1px solid var(--line-strong) !important; border-radius: 8px !important; background: var(--surface);
            font: inherit; color: var(--ink); box-shadow: none !important;
        }
        .g-form input:focus { border-color: var(--accent) !important; box-shadow: 0 0 0 3px var(--accent-soft) !important; outline: 0; }
        .g-form input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--accent); }
        .g-form button[type="submit"], .g-form .g-submit {
            display: flex; width: 100%; height: 46px; align-items: center; justify-content: center; margin-left: 0 !important;
            border: 0; border-radius: 8px; background: var(--accent) !important; color: #FFFFFF !important;
            font: 600 15px var(--sans) !important; text-transform: none !important; letter-spacing: 0 !important; cursor: pointer;
        }
        .g-form button[type="submit"]:hover { background: var(--accent-ink) !important; }
        .g-form .flex.items-center.justify-end, .g-form .flex.justify-end { display: flex; flex-direction: column-reverse; align-items: stretch !important; gap: 12px; }
        .g-form a { color: var(--accent-ink) !important; font-size: 13.5px; text-decoration: none !important; }
        .g-form a:hover { text-decoration: underline !important; }
        .g-form ul.text-red-600, .g-form .text-red-600 { color: var(--crit) !important; font-size: 13px !important; margin-top: 6px; list-style: none; padding: 0; }
        .g-form .text-green-600 { display: block; color: var(--good) !important; background: var(--good-soft); padding: 10px 12px; border-radius: 8px; font-size: 13.5px !important; }
    </style>
</head>
<body>
    <main class="g-wrap">
        <div class="g-box">
            <div class="g-logo"><img src="{{ asset('logo/revival-logo.jpeg') }}" alt="Revival Technologies"></div>
            <div class="g-card">
                @if($guestTitle !== 'MIAV')<h1>{{ $guestTitle }}</h1>@endif
                <div class="g-form">
                    {{ $slot }}
                </div>
            </div>
            @if(Route::has('login') && !request()->routeIs('login'))
            <a class="g-back" href="{{ route('login') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 19-7-7 7-7M19 12H5"/></svg>Back to sign in</a>
            @endif
        </div>
    </main>
    <footer class="g-foot">
        <span>&copy; {{ date('Y') }} Revival Technologies</span>
        <span>MIAV</span>
    </footer>
</body>
</html>
