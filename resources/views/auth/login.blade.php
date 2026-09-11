<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sign in – MIAV · Revival Technologies</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('logo/revival-logo.jpeg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --ground: #F3F5F8; --pane: #E8EDF3; --surface: #FFFFFF; --paper: #FFFFFF;
      --line: #DCE2E9; --line-strong: #C7D0DB;
      --ink: #16202C; --ink-2: #445162; --muted: #6A7686;
      --accent: #2B64A8; --accent-ink: #1F4F87; --accent-soft: #E9F0F9;
      --good: #1D7F46; --good-soft: #E4F3E9; --crit: #B83232; --crit-soft: #FBE8E7;
      --sans: "IBM Plex Sans", "Segoe UI", system-ui, -apple-system, sans-serif;
      --mono: "IBM Plex Mono", ui-monospace, "Cascadia Mono", Consolas, monospace;
    }
    * { box-sizing: border-box; }
    html { scrollbar-width: thin; scrollbar-color: var(--line-strong) transparent; }
    body { margin: 0; background: var(--ground); color: var(--ink); font: 400 15px/1.5 var(--sans); -webkit-font-smoothing: antialiased; }
    a { color: var(--accent-ink); text-decoration: none; }
    a:hover { text-decoration: underline; }
    :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
    svg.i { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

    .wrap { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); }

    /* Left: what MIAV is */
    .pane { background: var(--pane); border-right: 1px solid var(--line); padding: 40px 56px; display: flex; flex-direction: column; gap: 40px; justify-content: space-between; }
    .product { display: flex; align-items: baseline; gap: 10px; }
    .product strong { font-size: 15px; font-weight: 600; letter-spacing: .02em; }
    .product span { font-size: 13px; color: var(--muted); }
    .pitch { display: flex; flex-direction: column; gap: 28px; max-width: 460px; }
    .pitch h2 { margin: 0; font-size: 34px; line-height: 1.15; font-weight: 600; letter-spacing: -.02em; text-wrap: balance; }
    .pitch p { margin: 0; color: var(--ink-2); font-size: 16px; max-width: 42ch; }
    .slip { width: 320px; filter: drop-shadow(0 1px 1px rgba(22, 32, 44, .06)); }
    .slip-body { background: var(--paper); box-shadow: 0 1px 2px rgba(22, 32, 44, .08), 0 8px 24px rgba(22, 32, 44, .06); padding: 20px 22px 18px; font: 400 12.5px/1.7 var(--mono); color: var(--ink-2); letter-spacing: .02em; border-radius: 4px 4px 0 0; }
    .slip-edge { height: 8px; background: linear-gradient(135deg, var(--paper) 50%, transparent 50%) 0 0 / 12px 8px repeat-x, linear-gradient(225deg, var(--paper) 50%, transparent 50%) 0 0 / 12px 8px repeat-x; }
    .slip .c { text-align: center; }
    .slip .hd { color: var(--ink); font-weight: 500; }
    .slip hr { border: 0; border-top: 1px dashed var(--line-strong); margin: 10px 0; }
    .slip .ln { display: flex; gap: 6px; align-items: baseline; }
    .slip .ln i { flex: 1; border-bottom: 1px dotted var(--line-strong); transform: translateY(-4px); }
    .slip .bars { height: 30px; margin: 12px auto 4px; width: 78%; opacity: .75; background: repeating-linear-gradient(90deg, var(--ink-2) 0 2px, transparent 2px 4px, var(--ink-2) 4px 5px, transparent 5px 8px, var(--ink-2) 8px 11px, transparent 11px 13px); }
    .pane-foot { font-size: 13px; color: var(--muted); max-width: 46ch; margin: 0; }

    /* Right: the form */
    .form-side { display: flex; flex-direction: column; padding: 40px 48px; }
    .form-box { margin: auto 0; width: 100%; max-width: 380px; align-self: center; display: flex; flex-direction: column; gap: 24px; }
    .logo { width: 176px; background: #FFFFFF; border-radius: 8px; padding: 6px 8px; border: 1px solid var(--line); }
    .logo img { display: block; width: 100%; height: auto; }
    .intro h1 { margin: 0 0 4px; font-size: 26px; font-weight: 600; letter-spacing: -.015em; }
    .intro p { margin: 0; color: var(--muted); }
    .msg { display: flex; gap: 8px; align-items: flex-start; font-size: 13.5px; border-radius: 8px; padding: 10px 12px; }
    .msg.ok { color: var(--good); background: var(--good-soft); }
    .msg.err { color: var(--crit); background: var(--crit-soft); }
    .msg div + div { margin-top: 2px; }
    form { display: flex; flex-direction: column; gap: 16px; margin: 0; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    label { font-size: 13.5px; font-weight: 500; color: var(--ink-2); }
    .input { height: 44px; border: 1px solid var(--line-strong); border-radius: 8px; background: var(--surface); display: flex; align-items: center; transition: border-color .12s, box-shadow .12s; }
    .input:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }
    .input.has-error { border-color: var(--crit); }
    .input input { flex: 1; min-width: 0; height: 100%; border: 0; outline: 0; background: transparent; padding: 0 12px; font: inherit; color: var(--ink); }
    .input input::placeholder { color: var(--muted); }
    .reveal { height: 100%; padding: 0 12px; border: 0; background: transparent; color: var(--muted); font: 500 13px var(--sans); cursor: pointer; display: flex; align-items: center; gap: 6px; border-left: 1px solid var(--line); }
    .reveal:hover { color: var(--ink); }
    .row { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .check { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: var(--ink-2); font-weight: 400; cursor: pointer; }
    .check input { width: 16px; height: 16px; accent-color: var(--accent); margin: 0; }
    .submit { height: 46px; border: 0; border-radius: 8px; background: var(--accent); color: #FFFFFF; font: 600 15px var(--sans); cursor: pointer; margin-top: 4px; }
    .submit:hover { background: var(--accent-ink); }
    .note { font-size: 13.5px; color: var(--muted); margin: 0; }
    .form-foot { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; font-size: 12.5px; color: var(--muted); border-top: 1px solid var(--line); padding-top: 18px; margin-top: 40px; }

    @media (max-width: 960px) {
      .wrap { grid-template-columns: minmax(0, 1fr); }
      .pane { display: none; }
      .form-side { padding: 32px 20px; }
    }
    @media (prefers-reduced-motion: reduce) { .input { transition: none; } }
  </style>
</head>
<body>
  <svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="i-eye" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-alert" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
  </svg>

  <div class="wrap">
    <aside class="pane" aria-label="About MIAV">
      <div class="product"><strong>MIAV</strong><span>by Revival Technologies</span></div>

      <div class="pitch">
        <h2>Field operations for POS terminal fleets.</h2>
        <p>Plan field visits, keep every terminal in service and report on the work your technicians do across Zimbabwe.</p>

        <div class="slip" aria-hidden="true">
          <div class="slip-body">
            <div class="c hd">REVIVAL TECHNOLOGIES</div>
            <div class="c">MIAV FIELD OPERATIONS</div>
            <div class="c">HARARE · BULAWAYO</div>
            <hr>
            <div class="ln">TERMINAL FLEET<i></i>TRACKED</div>
            <div class="ln">SITE VISITS<i></i>LOGGED</div>
            <div class="ln">JOB ASSIGNMENTS<i></i>ROUTED</div>
            <div class="ln">SUPPORT TICKETS<i></i>RESOLVED</div>
            <div class="ln">REPORTS<i></i>PDF / CSV</div>
            <hr>
            <div class="c">STAFF ACCESS ONLY</div>
            <div class="bars"></div>
            <div class="c">MIAV-WEB · {{ date('Y.m') }}</div>
          </div>
          <div class="slip-edge"></div>
        </div>
      </div>

      <p class="pane-foot">Technicians log visits from the MIAV tablet app. This portal is for office, operations and management staff.</p>
    </aside>

    <main class="form-side">
      <div class="form-box">
        <div class="logo"><img src="{{ asset('logo/revival-logo.jpeg') }}" alt="Revival Technologies"></div>

        <div class="intro">
          <h1>Sign in</h1>
          <p>Use your MIAV work email and password.</p>
        </div>

        @if (session('status'))
          <div class="msg ok" role="status"><svg class="i"><use href="#i-check"/></svg><div>{{ session('status') }}</div></div>
        @endif

        @if ($errors->any())
          <div class="msg err" role="alert"><svg class="i"><use href="#i-alert"/></svg><div>@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div></div>
        @endif

        <form method="POST" action="{{ route('login') }}" autocomplete="on">
          @csrf
          <div class="field">
            <label for="email">Email address</label>
            <div class="input {{ $errors->has('email') ? 'has-error' : '' }}">
              <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@revival-technologies.com">
            </div>
          </div>
          <div class="field">
            <label for="password">Password</label>
            <div class="input {{ $errors->has('password') ? 'has-error' : '' }}">
              <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Your password">
              <button class="reveal" type="button" id="reveal" aria-controls="password" aria-pressed="false"><svg class="i"><use href="#i-eye"/></svg><span>Show</span></button>
            </div>
          </div>
          <div class="row">
            <label class="check" for="remember_me"><input id="remember_me" name="remember" type="checkbox">Keep me signed in</label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
          </div>
          <button class="submit" type="submit">Sign in</button>
        </form>

        <p class="note">Need an account or locked out? Ask your MIAV administrator.</p>
      </div>

      <div class="form-foot">
        <span>&copy; {{ date('Y') }} Revival Technologies</span>
        <span>MIAV</span>
      </div>
    </main>
  </div>

  <script>
    (function () {
      var pw = document.getElementById('password'), btn = document.getElementById('reveal');
      btn.addEventListener('click', function () {
        var show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        btn.setAttribute('aria-pressed', String(show));
        btn.querySelector('span').textContent = show ? 'Hide' : 'Show';
      });
      // Show that signing in is under way (the server can take a moment to answer).
      var form = document.querySelector('form[action$="/login"]'), submit = form && form.querySelector('.submit');
      if (form && submit) {
        form.addEventListener('submit', function () {
          setTimeout(function () { submit.disabled = true; submit.classList.add('is-busy'); submit.textContent = 'Signing in…'; }, 0);
        });
        window.addEventListener('pageshow', function () { submit.disabled = false; submit.classList.remove('is-busy'); submit.textContent = 'Sign in'; });
      }
    })();
  </script>
</body>
</html>
