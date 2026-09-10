<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Revival Technologies') }} – @yield('title', 'Dashboard')</title>

  <link rel="icon"       type="image/jpeg" href="{{ asset('logo/revival-logo.jpeg') }}">
  <link rel="shortcut icon" type="image/jpeg" href="{{ asset('logo/revival-logo.jpeg') }}">

  <!-- IBM Plex: interface + codes/IDs -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Per-page CSS (pos-terminals, etc.) -->
  <link rel="stylesheet" href="{{ asset('css/pos-terminals.css') }}">

  <!-- jQuery (needed by some inner pages) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap – kept while inner pages still use .btn/.card/etc. -->
  <link  href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Vite (Tailwind + app JS) -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- MIAV shell (sidebar, top bar, alerts) — loaded last so it wins -->
  <link rel="stylesheet" href="{{ asset('css/miav-shell.css') }}?v={{ @filemtime(public_path('css/miav-shell.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/miav-ui.css') }}?v={{ @filemtime(public_path('css/miav-ui.css')) }}">

  @stack('styles')
</head>

<body class="mv-body">
  @include('layouts.partials.icons')

  @php
    $me = auth()->user();
    $initials = strtoupper(mb_substr($me->first_name ?? '', 0, 1) . mb_substr($me->last_name ?? '', 0, 1)) ?: 'U';
    $roleNames = $me->roles->pluck('name')->map(fn ($r) => ucwords(str_replace('_', ' ', $r)))->implode(', ');
    $miavApp = \App\Services\MobileAppRelease::latest();
    $onDiscoveries = request()->routeIs('pos-terminals.index') && request('tab') === 'discoveries';

    // [route, icon, label, active-patterns, query, not-active-on]
    // Same sections, order and names as the previous menu, so nobody has to
    // relearn it. Only addition: Assets › Discovered Terminals.
    // [label, section icon, items, 'flat'?] — sections fold like the old menu;
    // the one holding the current page opens by itself.
    $nav = [
      ['Overview', 'grid', [
        ['dashboard',          'grid',       'Company Dashboard',  ['dashboard']],
        ['employee.dashboard', 'user-check', 'Employee Dashboard', ['employee.dashboard']],
      ], 'flat'],
      ['Assets', 'box', [
        ['assets.index',            'box',          'Internal Assets',      ['assets.*']],
        ['pos-terminals.index',     'card',         'POS Terminals',        ['pos-terminals.*'], []],
        ['pos-terminals.index',     'compass',      'Discovered Terminals', [],                  ['tab' => 'discoveries']],
        ['asset-requests.catalog',  'cart',         'Request Assets',       ['asset-requests.catalog', 'asset-requests.cart']],
        ['asset-requests.index',    'list',         'My Requests',          ['asset-requests.index', 'asset-requests.show']],
        ['asset-approvals.index',   'check-square', 'Asset Approvals',      ['asset-approvals.*']],
        ['business-licenses.index', 'file',         'Business Licenses',    ['business-licenses.*']],
      ]],
      ['Field Operations', 'route', [
        ['deployment.hierarchical',  'route',       'Terminal Deployment', ['deployment.*']],
        ['jobs.index',               'clipboard',   'Job Assignments',     ['jobs.*'], [], ['jobs.mine']],
        ['visits.index',             'pin',         'Site Visits',         ['visits.*', 'site_visits.show', 'site_visits.completed']],
        ['site_visits.createManual', 'plus-circle', 'Log a Visit',         ['site_visits.createManual']],
        ['tickets.index',            'ticket',      'Support Tickets',     ['tickets.*']],
      ]],
      ['Projects', 'folder', [
        ['projects.index',           'folder',      'All Projects',    ['projects.index', 'projects.show', 'projects.edit']],
        ['projects.create',          'plus-circle', 'New Project',     ['projects.create']],
        ['projects.closure-reports', 'file-check',  'Closure Reports', ['projects.closure-reports', 'projects.completion-reports']],
      ]],
      ['Clients', 'building', [
        ['clients.index',           'building', 'Clients',           ['clients.*']],
        ['client-dashboards.index', 'layout',   'Client Dashboards', ['client-dashboards.*']],
      ]],
      ['Employees', 'users', [
        ['employees.index', 'users',  'Employees',       ['employees.*']],
        ['roles.index',     'shield', 'Role Management', ['roles.*']],
      ]],
      ['Technician Portal', 'wrench', [
        // Same page as Overview › Employee Dashboard (as before); highlighted there only.
        ['employee.dashboard', 'user-check', 'My Dashboard',   []],
        ['jobs.mine',          'list-todo',  'My Assignments', ['jobs.mine']],
      ]],
      ['Reports', 'chart', [
        ['reports.index',             'chart', 'Reports Dashboard', ['reports.index', 'reports.system']],
        ['reports.technician-visits', 'pin',   'Technician Visits', ['reports.technician-visits*']],
        ['reports.builder',           'table', 'Report Builder',    ['reports.builder', 'reports.history']],
      ]],
      ['Administration', 'settings', [
        ['settings.index',    'sliders', 'System Settings', ['settings.*']],
        ['audit-trail.index', 'history', 'Audit Trail',     ['audit-trail.*']],
      ]],
    ];
  @endphp

  {{-- ── Sidebar ─────────────────────────────── --}}
  <aside class="mv-side sidebar" id="mvSide" aria-label="Main navigation">
    <a class="mv-brand" href="{{ route('dashboard') }}">
      <span class="mv-brand-mark"><img src="{{ asset('logo/revival-logo.jpeg') }}" alt="Revival Technologies"></span>
      <span class="mv-brand-name"><strong>MIAV</strong><span>Revival Technologies</span></span>
    </a>

    <nav class="mv-nav">
      @foreach($nav as $group)
        @php
          [$groupLabel, $groupIcon, $items] = $group;
          $isFlat = ($group[3] ?? null) === 'flat';
          $rows = [];
          foreach ($items as $item) {
              [$route, $icon, $label, $patterns] = $item;
              $query = $item[4] ?? [];
              $notOn = $item[5] ?? [];
              if (!empty($query['tab']) && $query['tab'] === 'discoveries') {
                  $isOn = $onDiscoveries;
              } elseif ($route === 'pos-terminals.index') {
                  $isOn = request()->routeIs('pos-terminals.*') && !$onDiscoveries;
              } else {
                  $isOn = $patterns && request()->routeIs(...$patterns) && !($notOn && request()->routeIs(...$notOn));
              }
              $rows[] = ['href' => route($route, $query), 'icon' => $icon, 'label' => $label, 'on' => $isOn];
          }
          $groupOn = collect($rows)->contains('on', true);
        @endphp
        @if($isFlat)
          <div class="mv-nav-group">
            <p class="mv-nav-label">{{ $groupLabel }}</p>
            @foreach($rows as $row)
              <a href="{{ $row['href'] }}" class="{{ $row['on'] ? 'is-on' : '' }}" @if($row['on']) aria-current="page" @endif>
                <svg class="mv-i"><use href="#i-{{ $row['icon'] }}"/></svg>{{ $row['label'] }}
              </a>
            @endforeach
          </div>
        @else
          <div class="mv-sec{{ $groupOn ? ' is-open is-current' : '' }}" data-sec="{{ \Illuminate\Support\Str::slug($groupLabel) }}">
            <button type="button" class="mv-sec-btn" aria-expanded="{{ $groupOn ? 'true' : 'false' }}" onclick="mvToggleSec(this)">
              <svg class="mv-i"><use href="#i-{{ $groupIcon }}"/></svg><span>{{ $groupLabel }}</span>
              <svg class="mv-i mv-chev"><use href="#i-chevron-down"/></svg>
            </button>
            <div class="mv-sec-items">
              @foreach($rows as $row)
                <a href="{{ $row['href'] }}" class="{{ $row['on'] ? 'is-on' : '' }}" @if($row['on']) aria-current="page" @endif>{{ $row['label'] }}</a>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    </nav>
    <script>
      // Sections fold like the old menu. The current page's section is open;
      // anything else the user opened stays open on the next page.
      function mvToggleSec(btn) {
        var sec = btn.closest('.mv-sec'), open = !sec.classList.contains('is-open');
        sec.classList.toggle('is-open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        try { var s = JSON.parse(localStorage.getItem('mvNavOpen') || '{}'); s[sec.dataset.sec] = open; localStorage.setItem('mvNavOpen', JSON.stringify(s)); } catch (e) {}
      }
      (function () {
        try {
          var s = JSON.parse(localStorage.getItem('mvNavOpen') || '{}');
          document.querySelectorAll('.mv-sec:not(.is-current)').forEach(function (sec) {
            if (s[sec.dataset.sec]) { sec.classList.add('is-open'); sec.querySelector('.mv-sec-btn').setAttribute('aria-expanded', 'true'); }
          });
        } catch (e) {}
      })();
    </script>

    <div class="mv-side-foot">
      <div class="mv-nav">
        <div class="mv-nav-group">
          <a href="{{ route('mobile-app.index') }}" class="{{ request()->routeIs('mobile-app.*') ? 'is-on' : '' }}">
            <svg class="mv-i"><use href="#i-phone"/></svg>Mobile App
            @if($miavApp)<span class="mv-ver">{{ $miavApp['version'] }}</span>@endif
          </a>
          <a href="{{ url('/docs') }}" target="_blank" rel="noopener">
            <svg class="mv-i"><use href="#i-book"/></svg>Documentation
            <svg class="mv-i mv-ext"><use href="#i-external"/></svg>
          </a>
          <a href="{{ route('employee.profile') }}" class="{{ request()->routeIs('employee.profile*') ? 'is-on' : '' }}">
            <svg class="mv-i"><use href="#i-user"/></svg>My Profile
          </a>
        </div>
      </div>
      <div class="mv-me">
        <span class="mv-avatar" aria-hidden="true">{{ $initials }}</span>
        <a class="mv-me-text" href="{{ route('employee.profile') }}" style="text-decoration:none;color:inherit">
          <strong>{{ $me->full_name }}</strong>
          <span>{{ $roleNames ?: $me->email }}</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="mv-signout" title="Sign Out" aria-label="Sign Out"><svg class="mv-i"><use href="#i-logout"/></svg></button>
        </form>
      </div>
    </div>
  </aside>

  <div id="sidebarOverlay" onclick="closeSidebar()" class="mv-overlay hidden"></div>

  {{-- ── Main ─────────────────────────────────── --}}
  <div class="mv-main main-content">
    <header class="mv-top">
      <button type="button" onclick="toggleSidebar()" class="mv-icon-btn mv-burger" aria-label="Open navigation">
        <svg class="mv-i"><use href="#i-menu"/></svg>
      </button>
      <div class="mv-title">
        <h1>@yield('title', 'Dashboard')</h1>
        @hasSection('header-actions')
        <div class="mv-header-actions">@yield('header-actions')</div>
        @endif
      </div>

      <div class="mv-top-right">
        {{-- Notifications --}}
        <div style="position:relative" id="notifWrapper">
          <button type="button" id="notifBtn" onclick="toggleNotifDropdown(event)" class="mv-icon-btn" aria-label="Notifications">
            <svg class="mv-i"><use href="#i-bell"/></svg>
            <span id="notifBadge" class="mv-badge hidden">0</span>
          </button>
          <div id="notifDropdown" class="mv-dropdown mv-notif hidden">
            <div class="mv-notif-head">
              <strong>Notifications</strong>
              <button type="button" onclick="markAllRead()" class="mv-link-btn">Mark all read</button>
            </div>
            <div id="notifList" class="mv-notif-list">
              <div class="mv-notif-empty" id="notifEmpty"><svg class="mv-i"><use href="#i-bell"/></svg>No new notifications</div>
            </div>
            <div class="mv-notif-foot"><a href="{{ route('notifications.index') }}">View all notifications</a></div>
          </div>
        </div>

        {{-- User menu --}}
        <button type="button" onclick="toggleUserDropdown(event)" class="mv-user-btn" aria-haspopup="menu">
          <span class="mv-user-text"><strong>{{ $me->full_name }}</strong><span class="mv-roles">{{ $roleNames ?: $me->email }}</span></span>
          <span class="mv-avatar" aria-hidden="true">{{ $initials }}</span>
        </button>
        <div id="userDropdown" class="mv-dropdown mv-menu hidden" role="menu">
          <a href="{{ route('employee.profile') }}"><svg class="mv-i"><use href="#i-user"/></svg>My Profile</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="mv-danger"><svg class="mv-i"><use href="#i-logout"/></svg>Sign Out</button>
          </form>
        </div>
      </div>
    </header>

    <main class="mv-page">
      @foreach(['success' => 'check-circle', 'error' => 'alert-circle', 'warning' => 'alert-triangle'] as $flashType => $flashIcon)
        @if(session($flashType))
        <div class="mv-flash is-{{ $flashType }}" role="{{ $flashType === 'success' ? 'status' : 'alert' }}">
          <svg class="mv-i"><use href="#i-{{ $flashIcon }}"/></svg>
          <span class="mv-flash-text">{{ session($flashType) }}</span>
          <button type="button" onclick="this.closest('.mv-flash').remove()" aria-label="Dismiss"><svg class="mv-i mv-i-sm"><use href="#i-x"/></svg></button>
        </div>
        @endif
      @endforeach

      @yield('content')
    </main>
  </div>

  @stack('scripts')

  <script>
    /* ── User dropdown ─────────────────────────── */
    function toggleUserDropdown(e) {
      if (e) e.stopPropagation();
      document.getElementById('notifDropdown')?.classList.add('hidden');
      document.getElementById('userDropdown').classList.toggle('hidden');
    }
    document.addEventListener('click', function (e) {
      const dd = document.getElementById('userDropdown');
      if (!dd || dd.classList.contains('hidden')) return;
      if (!e.target.closest('#userDropdown') && !e.target.closest('[onclick="toggleUserDropdown(event)"]')) {
        dd.classList.add('hidden');
      }
    });

    /* ── Small-screen sidebar ──────────────────── */
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
      document.getElementById('sidebarOverlay').classList.toggle('hidden');
    }
    function closeSidebar() {
      document.querySelector('.sidebar').classList.remove('open');
      document.getElementById('sidebarOverlay').classList.add('hidden');
    }
    // Kept for older pages that still call it; the new sidebar has no accordions.
    function toggleMenu() {}

    /* ── Toasts (used by pages via showNotification) ── */
    const MV_TOAST_ICON = { success: 'check-circle', error: 'alert-circle', info: 'info' };
    function createSystemToastContainer() {
      const existing = document.getElementById('systemToastContainer');
      if (existing) return existing;
      const container = document.createElement('div');
      container.id = 'systemToastContainer';
      document.body.appendChild(container);
      return container;
    }
    function showNotification(type, message, duration = 5000) {
      const container = createSystemToastContainer();
      const toast = document.createElement('div');
      toast.className = `system-toast ${type}`;
      toast.innerHTML = `
        <div class="system-toast-icon"><svg class="mv-i"><use href="#i-${MV_TOAST_ICON[type] || 'info'}"/></svg></div>
        <div class="system-toast-message"></div>
        <button type="button" class="system-toast-close" aria-label="Dismiss"><svg class="mv-i mv-i-sm"><use href="#i-x"/></svg></button>
      `;
      toast.querySelector('.system-toast-message').innerHTML = message;
      toast.querySelector('.system-toast-close').addEventListener('click', () => removeNotification(toast));
      container.appendChild(toast);
      requestAnimationFrame(() => toast.classList.add('visible'));
      let timeoutId = setTimeout(() => removeNotification(toast), duration);
      toast.addEventListener('mouseenter', () => clearTimeout(timeoutId));
      toast.addEventListener('mouseleave', () => { timeoutId = setTimeout(() => removeNotification(toast), 2000); });
    }
    function removeNotification(toast) {
      if (!toast || toast.dataset.closing) return;
      toast.dataset.closing = 'true';
      toast.classList.remove('visible');
      setTimeout(() => toast.remove(), 220);
    }

    /* ── Notification bell ─────────────────────── */
    const NOTIF_POLL_MS = 30000;
    const MV_NOTIF_ICON = { ticket: 'ticket', job: 'clipboard', asset: 'box', visit: 'pin', system: 'bell' };
    const mvEsc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function toggleNotifDropdown(e) {
      if (e) e.stopPropagation();
      const dd = document.getElementById('notifDropdown');
      const isHidden = dd.classList.contains('hidden');
      document.getElementById('userDropdown')?.classList.add('hidden');
      dd.classList.toggle('hidden');
      if (isHidden) fetchRecentNotifications();
    }
    document.addEventListener('click', function (e) {
      const dd = document.getElementById('notifDropdown');
      if (!dd || dd.classList.contains('hidden')) return;
      if (!e.target.closest('#notifWrapper')) dd.classList.add('hidden');
    });

    function fetchUnreadCount() {
      fetch('{{ route("notifications.unread-count") }}')
        .then(r => r.json())
        .then(data => {
          const badge = document.getElementById('notifBadge');
          if (!badge) return;
          if (data.count > 0) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.classList.remove('hidden');
          } else {
            badge.classList.add('hidden');
          }
        })
        .catch(() => {});
    }

    function fetchRecentNotifications() {
      fetch('{{ route("notifications.recent") }}')
        .then(r => r.json())
        .then(data => {
          const list = document.getElementById('notifList');
          if (!data.notifications || data.notifications.length === 0) {
            list.innerHTML = '<div class="mv-notif-empty"><svg class="mv-i"><use href="#i-bell"/></svg>No new notifications</div>';
            return;
          }
          list.innerHTML = data.notifications.map(n => {
            const type = MV_NOTIF_ICON[n.type] ? n.type : 'system';
            const href = n.url ? mvEsc(n.url) : '{{ route("notifications.index") }}';
            return `
              <a href="${href}" onclick="markOneRead(event, '${mvEsc(n.id)}', '${mvEsc(n.url || '')}')" class="mv-notif-item">
                <span class="mv-notif-ic t-${type}"><svg class="mv-i mv-i-sm"><use href="#i-${MV_NOTIF_ICON[type]}"/></svg></span>
                <span class="mv-notif-body">
                  <span class="mv-notif-title" style="display:block">${mvEsc(n.title)}</span>
                  <span class="mv-notif-text">${mvEsc(n.body)}</span>
                  <span class="mv-notif-time" style="display:block">${mvEsc(n.created_at)}</span>
                </span>
                <span class="mv-notif-dot"></span>
              </a>`;
          }).join('');
        })
        .catch(() => {});
    }

    function markOneRead(e, id, url) {
      e.preventDefault();
      fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
      }).then(() => {
        fetchUnreadCount();
        if (url) window.location.href = url;
        else document.getElementById('notifDropdown').classList.add('hidden');
      });
    }

    function markAllRead() {
      fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
      }).then(() => { fetchUnreadCount(); fetchRecentNotifications(); });
    }

    fetchUnreadCount();
    setInterval(fetchUnreadCount, NOTIF_POLL_MS);
  </script>
</body>
</html>
