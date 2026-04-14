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
      position: absolute; right: 16px; top: 50%;
      transform: translateY(-50%);
      font-size: 16px; font-weight: 700;
      color: #9ca3af;
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
    <aside class="sidebar fixed top-0 left-0 w-64 h-screen bg-white border-r border-gray-200 overflow-y-auto flex-shrink-0">

      {{-- Logo / company name --}}
      <div class="flex flex-col items-center gap-3 px-4 py-6 border-b border-gray-200">
        <img src="{{ asset('logo/revival logo.jpeg') }}"
             alt="Revival Technologies"
             class="h-16 w-auto max-w-[120px] object-contain rounded-lg shadow-sm">
        <span class="text-sm font-bold text-gray-900 tracking-wide whitespace-nowrap">
          Revival Technologies
        </span>
      </div>

      <nav class="pb-6">

        {{-- ── Dashboards ────────────────────── --}}
        <a href="{{ route('dashboard') }}"
           class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium border-l-[3px] transition-all duration-150
                  {{ request()->routeIs('dashboard')
                       ? 'bg-blue-50 text-blue-700 border-blue-500 font-semibold'
                       : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
          <span class="w-5 text-center text-base">📊</span> Company Dashboard
        </a>

        <a href="{{ route('employee.dashboard') }}"
           class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium border-l-[3px] transition-all duration-150
                  {{ request()->routeIs('employee.dashboard')
                       ? 'bg-blue-50 text-blue-700 border-blue-500 font-semibold'
                       : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
          <span class="w-5 text-center text-base">📈</span> Employee Dashboard
        </a>

        {{-- ── Assets Management ──────────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">💼</span> Assets Management
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
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs($pattern)
                         ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold'
                         : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">{{ $icon }}</span> {{ $label }}
          </a>
          @endforeach
        </div>

        {{-- ── Field Operations ───────────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">🔧</span> Field Operations
        </button>
        <div class="submenu {{ request()->routeIs('deployment.*','jobs.*','tickets.*','visits.*') ? 'show' : '' }}">
          @foreach([
            ['deployment.hierarchical', '🚀', 'Terminal Deployment', 'deployment.*'],
            ['jobs.index',              '📋', 'All Job Assignments', 'jobs.*'],
            ['visits.index',            '📝', 'Site Visits',         'visits.*'],
            ['tickets.index',           '🎫', 'Support Tickets',     'tickets.*'],
          ] as [$route, $icon, $label, $pattern])
          <a href="{{ route($route) }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs($pattern)
                         ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold'
                         : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">{{ $icon }}</span> {{ $label }}
          </a>
          @endforeach
        </div>

        {{-- ── Project Management ──────────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">📋</span> Project Management
        </button>
        <div class="submenu {{ request()->routeIs('projects.*') ? 'show' : '' }}">
          @foreach([
            ['projects.index',           '📊', 'Projects',                    'projects.index'],
            ['projects.create',          '➕', 'New Project',                 'projects.create'],
            ['projects.closure-reports', '📄', 'Project Closure and Reports', 'projects.completion-reports'],
          ] as [$route, $icon, $label, $pattern])
          <a href="{{ route($route) }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs($pattern)
                         ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold'
                         : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">{{ $icon }}</span> {{ $label }}
          </a>
          @endforeach
        </div>

        {{-- ── Client Management ───────────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">👥</span> Client Management
        </button>
        <div class="submenu {{ request()->routeIs('clients.*','client-dashboards.*') ? 'show' : '' }}">
          @foreach([
            ['clients.index',           '🏢', 'Clients',           'clients.*'],
            ['client-dashboards.index', '📊', 'Client Dashboards', 'client-dashboards.*'],
          ] as [$route, $icon, $label, $pattern])
          <a href="{{ route($route) }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs($pattern)
                         ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold'
                         : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">{{ $icon }}</span> {{ $label }}
          </a>
          @endforeach
        </div>

        {{-- ── Employee Management ─────────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">👤</span> Employee Management
        </button>
        <div class="submenu {{ request()->routeIs('employees.*','roles.*') ? 'show' : '' }}">
          @foreach([
            ['employees.index', '👥', 'Employees',       'employees.*'],
            ['roles.index',     '🔐', 'Role Management', 'roles.*'],
          ] as [$route, $icon, $label, $pattern])
          <a href="{{ route($route) }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs($pattern)
                         ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold'
                         : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">{{ $icon }}</span> {{ $label }}
          </a>
          @endforeach
        </div>

        {{-- ── Technician Portal ───────────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">🔧</span> Technician Portal
        </button>
        <div class="submenu {{ request()->routeIs('technician.*') ? 'show' : '' }}">
          <a href="{{ route('employee.dashboard') }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs('employee.dashboard') ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold' : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">📈</span> Employee Dashboard
          </a>
          <a href="{{ route('jobs.mine') }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150 text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800">
            <span class="w-4 text-center text-sm">📋</span> My Job Assignments
          </a>
        </div>

        {{-- ── Administration ──────────────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">⚙️</span> Administration
        </button>
        <div class="submenu {{ request()->routeIs('settings.*','admin.*') ? 'show' : '' }}">
          <a href="{{ route('settings.index') }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold' : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">🔧</span> System Settings
          </a>
        </div>

        {{-- ── Reports & Analytics ─────────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">📊</span> Reports &amp; Analytics
        </button>
        <div class="submenu {{ request()->routeIs('reports.*') ? 'show' : '' }}">
          @foreach([
            ['reports.index',            '📈', 'Reports Dashboard',  'reports.index'],
            ['reports.technician-visits','👨‍🔧', 'Technician Visits',  'reports.technician-visits'],
            ['reports.builder',          '🏗️', 'Report Builder',     'reports.builder'],
          ] as [$route, $icon, $label, $pattern])
          <a href="{{ route($route) }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs($pattern)
                         ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold'
                         : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">{{ $icon }}</span> {{ $label }}
          </a>
          @endforeach
        </div>

        {{-- ── Documentation Hub ───────────────── --}}
        <div class="mt-2 border-t border-gray-100">
          <a href="{{ url('/docs') }}" target="_blank"
             class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 border-l-[3px] border-transparent hover:bg-gray-50 hover:text-gray-900 hover:border-gray-400 transition-all duration-150">
            <span class="w-5 text-center text-base">📚</span> Documentation Hub
          </a>
        </div>

        {{-- ── My Account / Sign out ───────────── --}}
        <button onclick="toggleMenu(this)"
                class="nav-section w-full flex items-center gap-3 px-4 py-3 mt-2 border-t border-gray-100
                       text-xs font-semibold uppercase tracking-wider text-gray-500
                       hover:bg-gray-50 hover:text-gray-700 transition-all duration-150 cursor-pointer bg-transparent border-l-0 border-r-0 border-b-0">
          <span class="w-5 text-center text-base">👤</span> My Account
        </button>
        <div class="submenu {{ request()->routeIs('employee.profile*') ? 'show' : '' }}">
          <a href="{{ route('employee.profile') }}"
             class="flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium border-l-[3px] transition-all duration-150
                    {{ request()->routeIs('employee.profile*') ? 'bg-blue-50 text-blue-700 border-blue-400 font-semibold' : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-4 text-center text-sm">👤</span> My Profile
          </a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 pl-8 pr-4 py-2.5 text-sm font-medium text-red-500 border-l-[3px] border-transparent hover:bg-red-50 hover:border-red-400 transition-all duration-150 bg-transparent cursor-pointer">
              <span class="w-4 text-center text-sm">🚪</span> Sign Out
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
    <div class="main-content ml-64 flex flex-col flex-1 min-h-screen">

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
  </script>

</body>
</html>
