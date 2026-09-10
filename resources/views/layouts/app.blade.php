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

    // [route, icon, label, active-patterns, query]
    $nav = [
      [null, [
        ['dashboard',          'grid',       'Operations overview', ['dashboard']],
        ['employee.dashboard', 'user-check', 'My work',             ['employee.dashboard']],
      ]],
      ['Field work', [
        ['jobs.index',               'clipboard',   'Job assignments',    ['jobs.*']],
        ['visits.index',             'pin',         'Site visits',        ['visits.*', 'site_visits.show', 'site_visits.completed']],
        ['site_visits.createManual', 'plus-circle', 'Log a visit',        ['site_visits.createManual']],
        ['deployment.hierarchical',  'route',       'Deployment planner', ['deployment.*']],
        ['tickets.index',            'ticket',      'Support tickets',    ['tickets.*']],
      ]],
      ['Fleet', [
        ['pos-terminals.index',     'card',         'POS terminals',           ['pos-terminals.*'], []],
        ['pos-terminals.index',     'compass',      'Discovered in the field', [],                  ['tab' => 'discoveries']],
        ['assets.index',            'box',          'Internal assets',         ['assets.*']],
        ['asset-requests.catalog',  'cart',         'Request assets',          ['asset-requests.catalog', 'asset-requests.cart']],
        ['asset-requests.index',    'list',         'My requests',             ['asset-requests.index', 'asset-requests.show']],
        ['asset-approvals.index',   'check-square', 'Asset approvals',         ['asset-approvals.*']],
        ['business-licenses.index', 'file',         'Business licences',       ['business-licenses.*']],
      ]],
      ['Customers', [
        ['clients.index',            'building',   'Clients',           ['clients.*']],
        ['client-dashboards.index',  'layout',     'Client dashboards', ['client-dashboards.*']],
        ['projects.index',           'folder',     'Projects',          ['projects.index', 'projects.show', 'projects.edit', 'projects.create']],
        ['projects.closure-reports', 'file-check', 'Closure reports',   ['projects.closure-reports', 'projects.completion-reports']],
      ]],
      ['Team', [
        ['employees.index', 'users',  'Employees',      ['employees.*']],
        ['roles.index',     'shield', 'Roles & access', ['roles.*']],
      ]],
      ['Insights', [
        ['reports.index',             'chart', 'Reports',           ['reports.index', 'reports.system']],
        ['reports.technician-visits', 'pin',   'Technician visits', ['reports.technician-visits*']],
        ['reports.builder',           'table', 'Report builder',    ['reports.builder', 'reports.history']],
      ]],
      ['Administration', [
        ['settings.index',    'sliders', 'Settings',    ['settings.*']],
        ['audit-trail.index', 'history', 'Audit trail', ['audit-trail.*']],
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
      @foreach($nav as [$groupLabel, $items])
      <div class="mv-nav-group">
        @if($groupLabel)<p class="mv-nav-label">{{ $groupLabel }}</p>@endif
        @foreach($items as $item)
          @php
            [$route, $icon, $label, $patterns] = $item;
            $query = $item[4] ?? [];
            if (!empty($query['tab']) && $query['tab'] === 'discoveries') {
                $isOn = $onDiscoveries;
            } elseif ($route === 'pos-terminals.index') {
                $isOn = request()->routeIs('pos-terminals.*') && !$onDiscoveries;
            } else {
                $isOn = $patterns && request()->routeIs(...$patterns);
            }
          @endphp
          <a href="{{ route($route, $query) }}" class="{{ $isOn ? 'is-on' : '' }}" @if($isOn) aria-current="page" @endif>
            <svg class="mv-i"><use href="#i-{{ $icon }}"/></svg>{{ $label }}
          </a>
        @endforeach
      </div>
      @endforeach
    </nav>

    <div class="mv-side-foot">
      <div class="mv-nav">
        <div class="mv-nav-group">
          <a href="{{ route('mobile-app.index') }}" class="{{ request()->routeIs('mobile-app.*') ? 'is-on' : '' }}">
            <svg class="mv-i"><use href="#i-phone"/></svg>Mobile app
            @if($miavApp)<span class="mv-ver">{{ $miavApp['version'] }}</span>@endif
          </a>
          <a href="{{ url('/docs') }}" target="_blank" rel="noopener">
            <svg class="mv-i"><use href="#i-book"/></svg>Help &amp; documentation
            <svg class="mv-i mv-ext"><use href="#i-external"/></svg>
          </a>
        </div>
      </div>
      <div class="mv-me">
        <span class="mv-avatar" aria-hidden="true">{{ $initials }}</span>
        <a class="mv-me-text" href="{{ route('employee.profile') }}" style="text-decoration:none;color:inherit">
          <strong>{{ $me->full_name }}</strong>
          <span>{{ $roleNames ?: 'Employee' }}</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="mv-signout" title="Sign out" aria-label="Sign out"><svg class="mv-i"><use href="#i-logout"/></svg></button>
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
          <span class="mv-user-text"><strong>{{ $me->full_name }}</strong><span class="mv-roles">{{ $roleNames ?: 'Employee' }}</span></span>
          <span class="mv-avatar" aria-hidden="true">{{ $initials }}</span>
        </button>
        <div id="userDropdown" class="mv-dropdown mv-menu hidden" role="menu">
          <a href="{{ route('employee.profile') }}"><svg class="mv-i"><use href="#i-user"/></svg>My profile</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="mv-danger"><svg class="mv-i"><use href="#i-logout"/></svg>Sign out</button>
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
