{{-- Every error page (403, 404, 419, 500, 503…) uses this layout: Laravel's built-in
     error views extend "errors::minimal", which resolves here first. --}}
@php
    $code = trim($__env->yieldContent('code'));
    [$heading, $explain] = match ($code) {
        '401'   => ['Please sign in', 'You need to be signed in to see this page.'],
        '403'   => ['You don’t have access to this page', 'If you need it for your work, ask your MIAV administrator to give your role access.'],
        '404'   => ['Page not found', 'The page may have moved, or the link is out of date.'],
        '419'   => ['Your session expired', 'The page was open for a while. Refresh it and try again.'],
        '429'   => ['Too many requests', 'Please wait a minute and try again.'],
        '500'   => ['Something went wrong on our side', 'Try again in a moment. If it keeps happening, tell your MIAV administrator what you were doing.'],
        '503'   => ['MIAV is being updated', 'This usually takes a few minutes. Please try again shortly.'],
        default => [trim($__env->yieldContent('title')) ?: 'Something went wrong', trim($__env->yieldContent('message'))],
    };
    // Messages passed to abort() by the app are written for users; framework defaults are not worth repeating.
    $detail = '';
    if (in_array($code, ['403', '404'], true) && isset($exception)) {
        $m = trim((string) $exception->getMessage());
        if ($m !== '' && !in_array(strtolower($m), ['forbidden', 'not found', 'this action is unauthorized.', 'unauthorized', 'user does not have the right permissions.'], true)) {
            $detail = $m;
        }
    }
    try { $signedIn = auth()->check(); } catch (\Throwable $e) { $signedIn = false; }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heading }} – MIAV</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ground: #F3F5F8; --surface: #FFFFFF; --line: #DCE2E9; --line-strong: #C7D0DB;
            --ink: #16202C; --ink-2: #445162; --muted: #6A7686;
            --accent: #2B64A8; --accent-ink: #1F4F87;
            --sans: "IBM Plex Sans", "Segoe UI", system-ui, -apple-system, sans-serif;
            --mono: "IBM Plex Mono", ui-monospace, Consolas, monospace;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: var(--ground); color: var(--ink); font: 400 15px/1.55 var(--sans); -webkit-font-smoothing: antialiased; display: flex; align-items: center; justify-content: center; padding: 32px 20px; }
        :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .box { width: 100%; max-width: 460px; background: var(--surface); border: 1px solid var(--line); border-radius: 12px; padding: 30px 30px 26px; }
        .code { font: 500 13px var(--mono); color: var(--muted); letter-spacing: .06em; }
        h1 { margin: 6px 0 8px; font-size: 22px; font-weight: 600; letter-spacing: -.015em; text-wrap: balance; }
        p { margin: 0; color: var(--ink-2); }
        .detail { margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: #F7F9FB; border: 1px solid var(--line); font-size: 13.5px; color: var(--ink-2); }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 22px; }
        .btn { display: inline-flex; align-items: center; height: 40px; padding: 0 16px; border-radius: 8px; font: 500 14px var(--sans); text-decoration: none; cursor: pointer; border: 1px solid var(--line-strong); background: var(--surface); color: var(--ink); }
        .btn:hover { background: #F7F9FB; }
        .btn.primary { background: var(--accent); border-color: var(--accent); color: #FFFFFF; }
        .btn.primary:hover { background: var(--accent-ink); }
        .brand { margin-top: 22px; padding-top: 16px; border-top: 1px solid var(--line); font-size: 12.5px; color: var(--muted); }
    </style>
</head>
<body>
    <main class="box">
        @if($code !== '')<div class="code">ERROR {{ $code }}</div>@endif
        <h1>{{ $heading }}</h1>
        @if($explain !== '')<p>{{ $explain }}</p>@endif
        @if($detail !== '')<div class="detail">{{ $detail }}</div>@endif
        <div class="actions">
            @if($code === '419')
                <a class="btn primary" href="{{ url()->current() }}">Refresh</a>
            @elseif($signedIn)
                <a class="btn primary" href="{{ url('/dashboard') }}">Go to the dashboard</a>
            @else
                <a class="btn primary" href="{{ url('/login') }}">Sign in</a>
            @endif
            <button type="button" class="btn" onclick="history.length > 1 ? history.back() : location.assign('{{ url('/') }}')">Go back</button>
        </div>
        <div class="brand">MIAV · Revival Technologies</div>
    </main>
</body>
</html>
