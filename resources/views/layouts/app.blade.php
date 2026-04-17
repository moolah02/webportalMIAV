<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Revival Technologies') }} – {{ $title ?? 'Dashboard' }}</title>

  <link rel="icon"       type="image/jpeg" href="{{ asset('logo/revival-logo.jpeg') }}">
  <link rel="shortcut icon" type="image/jpeg" href="{{ asset('logo/revival-logo.jpeg') }}">

  <!-- Inter font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Per-page CSS (pos-terminals, etc.) -->
  <link rel="stylesheet" href="{{ asset('css/pos-terminals.css') }}">

  <!-- jQuery (needed by some inner pages) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap – kept while inner pages still use .btn/.card/etc. -->
  <link  href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Vite (Tailwind + app JS) -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }

    /* Accordion: Tailwind can't animate max-height natively */
    .submenu          { max-height: 0;    overflow: hidden; transition: max-height .25s ease; }
    .submenu.show     { max-height: 600px; }

    /* Chevron arrow on section headers */
    .nav-section { position: relative; }
    .nav-section::after {
      content: '›';
      position: absolute; right: 10px; top: 50%;
      transform: translateY(-50%);
      font-size: 12px; font-weight: 700;
      color: #d1d5db;
      transition: transform .2s ease;
    }
    .nav-section.open::after { transform: translateY(-50%) rotate(90deg); }

    /* Mobile sidebar slide */
    @media (max-width: 767px) {
      .sidebar            { transform: translateX(-100%); transition: transform .3s ease; z-index: 1000; }
      .sidebar.open       { transform: translateX(0); }
      .main-content       { margin-left: 0 !important; }
    }

    /* Thin scrollbar inside sidebar */
    .sidebar::-webkit-scrollbar       { width: 4px; }
    .sidebar::-webkit-scrollbar-track { background: #fff; }
    .sidebar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }
  </style>

  @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

  <!-- ─── Layout shell ──────────────────────────────────────────── -->
  <div class="flex min-h-screen">

    {{-- ═══════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════ --}}
    <aside class="sidebar fixed top-0 left-0 w-52 h-screen bg-white border-r border-gray-200 overflow-y-auto flex-shrink-0">

      {{-- Logo --}}
      <div class="flex items-center gap-2.5 px-4 py-3.5 border-b border-gray-100">
        <img src="{{ asset('logo/revival logo.jpeg') }}"
             alt="Revival Technologies"
             class="h-7 w-7 object-cover rounded-md shadow-sm flex-shrink-0">
        <span class="text-[11px] font-bold text-gray-800 tracking-wide leading-tight">
          Revival Technologies
        </span>
      </div>

      <nav class="pb-4 pt-1">

        {{-- ── Dashboards ────────────────────── --}}
        <div class="px-2 pt-2 pb-1">
          <p class="text-[9px] font-semibold uppercase tracking-widest text-gray-400 px-2 mb-1">Overview</p>
          <a href="{{ route('dashboard') }}"
             class="flex items-center gap-2 px-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                    {{ request()->routeIs('dashboard') ? 'bg-[#1a3a5c] text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <span class="text-[13px] leading-none w-4 text-center">📊</span> Company Dashboard
          </a>
          <a href="{{ route('employee.dashboard') }}"
             class="flex items-center gap-2 px-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                    {{ request()->routeIs('employee.dashboard') ? 'bg-[#1a3a5c] text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <span class="text-[13px] leading-none w-4 text-center">📈</span> Employee Dashboard
          </a>
        </div>

        {{-- ── Assets Management ──────────────── --}}
        <div class="px-2 pt-2">
          <button onclick="toggleMenu(this)"
                  class="nav-section w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[9px] font-semibold uppercase tracking-widest text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all duration-150 cursor-pointer bg-transparent border-none">
            <span class="text-[11px] leading-none w-4 text-center">💼</span> Assets
          </button>
          <div class="submenu {{ request()->routeIs('assets.*','asset-requests.*','asset-approvals.*','pos-terminals.*','business-licenses.*') ? 'show' : '' }}">
            @foreach([
              ['assets.index',           '🏢', 'Internal Assets',   'assets.*'],
              ['pos-terminals.index',    '💳', 'POS Terminals',     'pos-terminals.*'],
              ['asset-requests.catalog', '🛒', 'Request Assets',    'asset-requests.catalog'],
              ['asset-requests.index',   '📋', 'My Requests',       'asset-requests.index'],
              ['asset-approvals.index',  '✅', 'Asset Approvals',   'asset-approvals.*'],
              ['business-licenses.index','📄', 'Business Licenses', 'business-licenses.*'],
            ] as [$route, $icon, $label, $pattern])
            <a href="{{ route($route) }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs($pattern) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">{{ $icon }}</span> {{ $label }}
            </a>
            @endforeach
          </div>
        </div>

        {{-- ── Field Operations ───────────────── --}}
        <div class="px-2 pt-1">
          <button onclick="toggleMenu(this)"
                  class="nav-section w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[9px] font-semibold uppercase tracking-widest text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all duration-150 cursor-pointer bg-transparent border-none">
            <span class="text-[11px] leading-none w-4 text-center">🔧</span> Field Operations
          </button>
          <div class="submenu {{ request()->routeIs('deployment.*','jobs.*','tickets.*','visits.*','site_visits.*') ? 'show' : '' }}">
            @foreach([
              ['deployment.hierarchical', '🚀', 'Terminal Deployment', 'deployment.*'],
              ['jobs.index',              '📋', 'Job Assignments',     'jobs.*'],
              ['visits.index',            '📝', 'Site Visits',         'visits.*'],
              ['site_visits.createManual','➕', 'Log a Visit',         'site_visits.createManual'],
              ['tickets.index',           '🎫', 'Support Tickets',     'tickets.*'],
            ] as [$route, $icon, $label, $pattern])
            <a href="{{ route($route) }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs($pattern) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">{{ $icon }}</span> {{ $label }}
            </a>
            @endforeach
          </div>
        </div>

        {{-- ── Project Management ──────────────── --}}
        <div class="px-2 pt-1">
          <button onclick="toggleMenu(this)"
                  class="nav-section w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[9px] font-semibold uppercase tracking-widest text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all duration-150 cursor-pointer bg-transparent border-none">
            <span class="text-[11px] leading-none w-4 text-center">📋</span> Projects
          </button>
          <div class="submenu {{ request()->routeIs('projects.*') ? 'show' : '' }}">
            @foreach([
              ['projects.index',           '📊', 'All Projects',    'projects.index'],
              ['projects.create',          '➕', 'New Project',     'projects.create'],
              ['projects.closure-reports', '📄', 'Closure Reports', 'projects.completion-reports'],
            ] as [$route, $icon, $label, $pattern])
            <a href="{{ route($route) }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs($pattern) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">{{ $icon }}</span> {{ $label }}
            </a>
            @endforeach
          </div>
        </div>

        {{-- ── Client Management ───────────────── --}}
        <div class="px-2 pt-1">
          <button onclick="toggleMenu(this)"
                  class="nav-section w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[9px] font-semibold uppercase tracking-widest text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all duration-150 cursor-pointer bg-transparent border-none">
            <span class="text-[11px] leading-none w-4 text-center">👥</span> Clients
          </button>
          <div class="submenu {{ request()->routeIs('clients.*','client-dashboards.*') ? 'show' : '' }}">
            @foreach([
              ['clients.index',           '🏢', 'Clients',           'clients.*'],
              ['client-dashboards.index', '📊', 'Client Dashboards', 'client-dashboards.*'],
            ] as [$route, $icon, $label, $pattern])
            <a href="{{ route($route) }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs($pattern) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">{{ $icon }}</span> {{ $label }}
            </a>
            @endforeach
          </div>
        </div>

        {{-- ── Employee Management ─────────────── --}}
        <div class="px-2 pt-1">
          <button onclick="toggleMenu(this)"
                  class="nav-section w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[9px] font-semibold uppercase tracking-widest text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all duration-150 cursor-pointer bg-transparent border-none">
            <span class="text-[11px] leading-none w-4 text-center">👤</span> Employees
          </button>
          <div class="submenu {{ request()->routeIs('employees.*','roles.*') ? 'show' : '' }}">
            @foreach([
              ['employees.index', '👥', 'Employees',       'employees.*'],
              ['roles.index',     '🔐', 'Role Management', 'roles.*'],
            ] as [$route, $icon, $label, $pattern])
            <a href="{{ route($route) }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs($pattern) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">{{ $icon }}</span> {{ $label }}
            </a>
            @endforeach
          </div>
        </div>

        {{-- ── Technician Portal ───────────────── --}}
        <div class="px-2 pt-1">
          <button onclick="toggleMenu(this)"
                  class="nav-section w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[9px] font-semibold uppercase tracking-widest text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all duration-150 cursor-pointer bg-transparent border-none">
            <span class="text-[11px] leading-none w-4 text-center">🔧</span> Technician Portal
          </button>
          <div class="submenu {{ request()->routeIs('technician.*') ? 'show' : '' }}">
            <a href="{{ route('employee.dashboard') }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs('employee.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">📈</span> My Dashboard
            </a>
            <a href="{{ route('jobs.mine') }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150 text-gray-500 hover:bg-gray-100 hover:text-gray-800">
              <span class="text-[11px] leading-none w-3.5 text-center">📋</span> My Assignments
            </a>
          </div>
        </div>

        {{-- ── Reports & Analytics ─────────────── --}}
        <div class="px-2 pt-1">
          <button onclick="toggleMenu(this)"
                  class="nav-section w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[9px] font-semibold uppercase tracking-widest text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all duration-150 cursor-pointer bg-transparent border-none">
            <span class="text-[11px] leading-none w-4 text-center">📊</span> Reports
          </button>
          <div class="submenu {{ request()->routeIs('reports.*') ? 'show' : '' }}">
            @foreach([
              ['reports.index',            '📈', 'Reports Dashboard', 'reports.index'],
              ['reports.technician-visits','🔧', 'Technician Visits', 'reports.technician-visits'],
              ['reports.builder',          '🏗', 'Report Builder',    'reports.builder'],
            ] as [$route, $icon, $label, $pattern])
            <a href="{{ route($route) }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs($pattern) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">{{ $icon }}</span> {{ $label }}
            </a>
            @endforeach
          </div>
        </div>

        {{-- ── Administration ──────────────────── --}}
        <div class="px-2 pt-1">
          <button onclick="toggleMenu(this)"
                  class="nav-section w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[9px] font-semibold uppercase tracking-widest text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all duration-150 cursor-pointer bg-transparent border-none">
            <span class="text-[11px] leading-none w-4 text-center">⚙️</span> Administration
          </button>
          <div class="submenu {{ request()->routeIs('settings.*','admin.*','audit-trail.*') ? 'show' : '' }}">
            <a href="{{ route('settings.index') }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">🔧</span> System Settings
            </a>
            <a href="{{ route('audit-trail.index') }}"
               class="flex items-center gap-2 pl-6 pr-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                      {{ request()->routeIs('audit-trail.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
              <span class="text-[11px] leading-none w-3.5 text-center">🔍</span> Audit Trail
            </a>
          </div>
        </div>

        {{-- ── Bottom links ─────────────────────── --}}
        <div class="px-2 pt-3 mt-2 border-t border-gray-100 space-y-0.5">
          <a href="{{ url('/docs') }}" target="_blank"
             class="flex items-center gap-2 px-2 py-1.5 rounded-md text-[11px] font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition-all duration-150">
            <span class="text-[11px] leading-none w-4 text-center">📚</span> Documentation
          </a>
          <a href="{{ route('employee.profile') }}"
             class="flex items-center gap-2 px-2 py-1.5 rounded-md text-[11px] font-medium transition-all duration-150
                    {{ request()->routeIs('employee.profile*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <span class="text-[11px] leading-none w-4 text-center">👤</span> My Profile
          </a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-[11px] font-medium text-red-500 hover:bg-red-50 hover:text-red-600 transition-all duration-150 bg-transparent border-none cursor-pointer">
              <span class="text-[11px] leading-none w-4 text-center">🚪</span> Sign Out
            </button>
          </form>
        </div>

      </nav>
    </aside>

    {{-- ═══════════════════════════════════
         SIDEBAR OVERLAY (mobile)
    ═══════════════════════════════════ --}}
    <div id="sidebarOverlay"
         onclick="closeSidebar()"
         class="hidden fixed inset-0 bg-black/40 z-[999]">
    </div>

    {{-- ═══════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════ --}}
    <div class="main-content ml-52 flex flex-col flex-1 min-h-screen">

      {{-- Top bar --}}
      <header class="sticky top-0 z-50 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          {{-- Hamburger (mobile only) --}}
          <button onclick="toggleSidebar()"
                  class="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100 transition-colors"
                  aria-label="Open navigation">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
          <h1 class="text-xl font-bold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
        </div>

        {{-- User info --}}
        <div class="relative flex items-center gap-3">

          {{-- ── Bell notification button ── --}}
          <div class="relative" id="notifWrapper">
            <button type="button" id="notifBtn"
                    onclick="toggleNotifDropdown(event)"
                    class="relative w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 border border-gray-200 text-lg hover:bg-gray-200 transition-colors"
                    aria-label="Notifications">
              🔔
              <span id="notifBadge"
                    class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-0.5 leading-none">
                0
              </span>
            </button>

            {{-- Notification dropdown --}}
            <div id="notifDropdown"
                 class="hidden absolute top-14 right-0 w-80 bg-white rounded-xl border border-gray-200 shadow-xl z-50 overflow-hidden">
              <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <span class="text-sm font-semibold text-gray-800">Notifications</span>
                <button onclick="markAllRead()" class="text-xs text-blue-600 hover:underline">Mark all read</button>
              </div>
              <div id="notifList" class="overflow-y-auto max-h-80 divide-y divide-gray-50">
                <div class="px-4 py-8 text-center text-gray-400 text-sm" id="notifEmpty">
                  <div class="text-3xl mb-1">🔔</div>
                  No new notifications
                </div>
              </div>
              <div class="border-t border-gray-100 px-4 py-2.5 text-center">
                <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:underline font-medium">
                  View all notifications →
                </a>
              </div>
            </div>
          </div>
          {{-- ── End bell ── --}}
          <div class="text-right hidden sm:block">
            <p class="text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()->full_name }}</p>
            <span class="inline-block text-xs font-medium text-gray-500 bg-gray-100 border border-gray-200 rounded-full px-3 py-0.5 mt-0.5">
              @foreach(auth()->user()->roles as $role)
                {{ $role->name }}{{ !$loop->last ? ', ' : '' }}
              @endforeach
            </span>
          </div>
          <button type="button"
                  onclick="toggleUserDropdown(event)"
                  class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 border border-gray-200 text-lg hover:bg-gray-200 transition-colors">
            👤
          </button>
          {{-- Dropdown --}}
          <div id="userDropdown"
               class="hidden absolute top-14 right-0 w-48 bg-white rounded-xl border border-gray-200 shadow-lg z-50 overflow-hidden">
            <a href="{{ route('employee.profile') }}"
               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
              <span>👤</span> My Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                      class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 cursor-pointer bg-transparent border-none">
                <span>🚪</span> Sign Out
              </button>
            </form>
          </div>
        </div>
      </header>

      {{-- Flash messages + page content --}}
      <main class="flex-1 p-6">

        @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-5 text-sm">
          <span class="text-lg shrink-0">✅</span>
          <span class="flex-1">{{ session('success') }}</span>
          <button onclick="this.closest('div').remove()" class="shrink-0 text-green-600 hover:text-green-900 text-xl leading-none bg-transparent border-none cursor-pointer">&times;</button>
        </div>
        @endif

        @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-5 text-sm">
          <span class="text-lg shrink-0">❌</span>
          <span class="flex-1">{{ session('error') }}</span>
          <button onclick="this.closest('div').remove()" class="shrink-0 text-red-600 hover:text-red-900 text-xl leading-none bg-transparent border-none cursor-pointer">&times;</button>
        </div>
        @endif

        @if(session('warning'))
        <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg px-4 py-3 mb-5 text-sm">
          <span class="text-lg shrink-0">⚠️</span>
          <span class="flex-1">{{ session('warning') }}</span>
          <button onclick="this.closest('div').remove()" class="shrink-0 text-yellow-600 hover:text-yellow-900 text-xl leading-none bg-transparent border-none cursor-pointer">&times;</button>
        </div>
        @endif

        @yield('content')
      </main>

    </div>{{-- /main-content --}}
  </div>{{-- /flex --}}

  @stack('scripts')

  <script>
    /* ─── User dropdown ─────────────────────────────────── */
    function toggleUserDropdown(e) {
      if (e) e.stopPropagation();
      document.getElementById('userDropdown').classList.toggle('hidden');
    }
    document.addEventListener('click', function (e) {
      const dd = document.getElementById('userDropdown');
      if (!dd || dd.classList.contains('hidden')) return;
      if (!e.target.closest('#userDropdown') && !e.target.closest('[onclick="toggleUserDropdown(event)"]')) {
        dd.classList.add('hidden');
      }
    });

    /* ─── Mobile sidebar ────────────────────────────────── */
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
      document.getElementById('sidebarOverlay').classList.toggle('hidden');
    }
    function closeSidebar() {
      document.querySelector('.sidebar').classList.remove('open');
      document.getElementById('sidebarOverlay').classList.add('hidden');
    }

    /* ─── Accordion nav sections ────────────────────────── */
    function toggleMenu(header) {
      const submenu = header.nextElementSibling;
      const isOpen  = header.classList.contains('open');

      // collapse all others
      document.querySelectorAll('.nav-section').forEach(h => {
        if (h !== header && h.classList.contains('open')) {
          h.nextElementSibling.classList.remove('show');
          h.classList.remove('open');
        }
      });

      if (isOpen) {
        submenu.classList.remove('show');
        header.classList.remove('open');
      } else {
        submenu.classList.add('show');
        header.classList.add('open');
      }
    }

    // On page load: open the section that contains the active link
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.nav-section').forEach(header => {
        const submenu = header.nextElementSibling;
        if (submenu && submenu.querySelector('a.bg-blue-50, button.bg-blue-50')) {
          submenu.classList.add('show');
          header.classList.add('open');
        }
      });
    });

    /* ─── Notification bell ─────────────────────────────── */
    const NOTIF_POLL_MS = 30000; // poll every 30s

    function toggleNotifDropdown(e) {
      if (e) e.stopPropagation();
      const dd = document.getElementById('notifDropdown');
      const isHidden = dd.classList.contains('hidden');
      // close user dropdown if open
      document.getElementById('userDropdown')?.classList.add('hidden');
      dd.classList.toggle('hidden');
      if (isHidden) fetchRecentNotifications();
    }

    document.addEventListener('click', function (e) {
      const dd = document.getElementById('notifDropdown');
      if (!dd || dd.classList.contains('hidden')) return;
      if (!e.target.closest('#notifWrapper')) {
        dd.classList.add('hidden');
      }
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
          const list  = document.getElementById('notifList');
          const empty = document.getElementById('notifEmpty');
          if (!data.notifications || data.notifications.length === 0) {
            list.innerHTML = '<div class="px-4 py-8 text-center text-gray-400 text-sm"><div class="text-3xl mb-1">🔔</div>No new notifications</div>';
            return;
          }
          const typeColors = {
            ticket: 'bg-orange-50 text-orange-600',
            job:    'bg-green-50 text-green-600',
            asset:  'bg-blue-50 text-blue-600',
            visit:  'bg-teal-50 text-teal-600',
            system: 'bg-gray-100 text-gray-500',
          };
          list.innerHTML = data.notifications.map(n => {
            const color = typeColors[n.type] || typeColors.system;
            const href  = n.url ? `href="${n.url}"` : `href="{{ route('notifications.index') }}"`;
            return `
              <a ${href}
                 onclick="markOneRead(event, '${n.id}', '${n.url || ''}')"
                 class="flex gap-3 px-4 py-3 hover:bg-gray-50 transition-colors no-underline cursor-pointer">
                <span class="text-xl mt-0.5">${n.icon}</span>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium text-gray-800 truncate">${n.title}</div>
                  <div class="text-xs text-gray-500 leading-snug mt-0.5 line-clamp-2">${n.body}</div>
                  <div class="text-xs text-gray-400 mt-0.5">${n.created_at}</div>
                </div>
                <span class="shrink-0 mt-1.5 w-2 h-2 rounded-full bg-blue-500"></span>
              </a>`;
          }).join('');
        })
        .catch(() => {});
    }

    function markOneRead(e, id, url) {
      e.preventDefault();
      fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          'Accept': 'application/json',
        },
      }).then(() => {
        fetchUnreadCount();
        if (url) window.location.href = url;
        else document.getElementById('notifDropdown').classList.add('hidden');
      });
    }

    function markAllRead() {
      fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          'Accept': 'application/json',
        },
      }).then(() => {
        fetchUnreadCount();
        fetchRecentNotifications();
      });
    }

    // Initial load + periodic poll
    fetchUnreadCount();
    setInterval(fetchUnreadCount, NOTIF_POLL_MS);
  </script>

</body>
</html>
