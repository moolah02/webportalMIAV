<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="{{ asset('css/pos-terminals.css') }}">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Revival Technologies') }} – {{ $title ?? 'Dashboard' }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Add Inter font for modern typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- jQuery (required for some components) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap JS (if needed) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* Reset & Base */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #f8f9fa;
      color: #2d3748;
      line-height: 1.5;
      font-size: 14px;
    }

    /* Sidebar - Solid White */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      height: 100vh;
      background: #ffffff;
      border-right: 1px solid #ffffff;
      overflow-y: auto;
      box-shadow: none; /* Completely remove shadows */
    }

    /* Updated Sidebar Header with Logo Support - Vertical Layout */
    .sidebar-header {
      padding: 24px 16px;
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      color: #1a202c;
      display: flex;
      flex-direction: column; /* Stack logo and text vertically */
      align-items: center; /* Center horizontally */
      gap: 12px;
      text-align: center;
    }

    /* Remove the emoji pseudo-element */
    .sidebar-header::before {
      display: none;
    }

    /* Logo Styling - Larger Size */
    .sidebar-logo {
      height: 64px; /* Increased from 32px to 64px */
      width: auto;
      max-width: 120px; /* Increased max width */
      object-fit: contain;
      flex-shrink: 0; /* Prevent logo from shrinking */
      border-radius: 8px; /* Optional: rounded corners */
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Optional: subtle shadow */
    }

    /* Company Name Styling - Below Logo */
    .company-name {
      font-size: 16px; /* Slightly smaller since logo is now prominent */
      font-weight: 700;
      color: #1a202c;
      white-space: nowrap; /* Prevent text wrapping */
      margin-top: 4px;
      letter-spacing: 0.5px; /* Better spacing */
    }

    /* Alternative: Logo-only header (uncomment if you want logo without text) */
    /*
    .sidebar-header {
      justify-content: center;
      padding: 20px 16px;
    }

    .sidebar-logo {
      height: 40px;
      max-width: 180px;
    }

    .company-name {
      display: none;
    }
    */

    /* Navigation */
    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      color: #4a5568;
      text-decoration: none;
      transition: all 0.15s ease;
      font-weight: 500;
      font-size: 14px;
      border-left: 3px solid transparent;
      background: #ffffff; /* Solid white background */
    }

    .nav-item:hover {
      background: #f7fafc;
      color: #2d3748;
    }

    .nav-item.active {
      background: #ebf8ff;
      color: #2b6cb0;
      border-left-color: #4299e1;
      font-weight: 600;
    }

    /* Menu Icons */
    .nav-icon {
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: inherit;
    }

    /* Section headers - Solid White */
    .nav-section {
      padding: 16px 16px 8px 16px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: all 0.15s ease;
      position: relative;
      color: #718096;
      background: #ffffff; /* Changed to solid white */
      border-top: 1px solid #e2e8f0;
      margin-top: 8px;
      display: flex;
      align-items: center;
    }

    .nav-section:first-child {
      margin-top: 0;
      border-top: none;
    }

    .nav-section:hover {
      color: #4a5568;
      background: #f7fafc;
    }

    /* Simple chevron arrows to match reference */
    .nav-section::after {
      content: '›';
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 14px;
      color: #a0aec0;
      transition: all 0.2s ease;
      font-weight: bold;
    }

    .nav-section.open::after {
      transform: translateY(-50%) rotate(90deg);
    }

    /* Submenus - Solid White */
    .submenu {
      max-height: 0;
      overflow: hidden;
      background: #ffffff; /* Solid white background */
      transition: max-height 0.2s ease;
    }

    .submenu.show {
      max-height: 500px;
    }

    .nav-sub {
      display: flex;
      align-items: center;
      padding: 10px 16px 10px 32px;
      font-size: 14px;
      color: #718096;
      text-decoration: none;
      transition: all 0.15s ease;
      position: relative;
      font-weight: 500;
      border-left: 3px solid transparent;
      background: #ffffff; /* Solid white background */
      gap: 8px;
    }

    .nav-sub:hover {
      background: #f7fafc;
      color: #4a5568;
    }

    .nav-sub.active {
      color: #2b6cb0;
      font-weight: 600;
      background: #ebf8ff;
      border-left-color: #4299e1;
    }

    /* Main content */
    .main-content {
      margin-left: 260px;
      min-height: 100vh;
      background: #f8f9fa;
    }

    .content-header {
      background: #ffffff;
      padding: 20px 24px;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: none; /* Remove any shadows */
    }

    .page-title {
      font-size: 24px;
      font-weight: 700;
      color: #1a202c;
      margin: 0;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #4a5568;
      font-weight: 500;
      font-size: 14px;
    }

    .user-badge {
      background: #edf2f7;
      color: #4a5568;
      padding: 4px 12px;
      border-radius: 16px;
      font-size: 12px;
      font-weight: 600;
      border: 1px solid #e2e8f0;
      box-shadow: none; /* Remove shadows */
    }

    .content-body {
      padding: 24px;
    }

    /* Dashboard cards - Completely Flat */
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px,1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .metric-card {
      background: #ffffff;
      border-radius: 6px;
      padding: 20px;
      text-align: center;
      border: 1px solid #e2e8f0;
      box-shadow: none; /* Explicitly remove all shadows */
    }

    .metric-card:hover {
      border-color: #cbd5e0;
    }

    .metric-number {
      font-size: 32px;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 8px;
    }

    .metric-label {
      color: #718096;
      font-size: 13px;
      font-weight: 500;
    }

    /* Logout styling - Solid White */
    .logout-form {
      border: none;
      margin-top: 16px;
      border-top: 1px solid #e2e8f0;
      background: #ffffff; /* Changed to solid white */
    }

    .logout-btn {
      width: 100%;
      text-align: left;
      padding: 12px 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      background: #ffffff; /* Solid white background */
      border: none;
      color: #e53e3e;
      cursor: pointer;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.15s ease;
      border-left: 3px solid transparent;
    }

    .logout-btn .nav-icon {
      color: #e53e3e;
      font-weight: bold;
    }

    .logout-btn:hover {
      background: #fed7d7;
      border-left-color: #e53e3e;
    }

    /* Responsive */
    @media(max-width: 768px){
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        width: 260px;
        z-index: 1000;
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .main-content {
        margin-left: 0;
      }
      .content-header {
        padding: 16px 20px;
      }
      .content-body {
        padding: 20px;
      }

      /* Responsive logo adjustments */
      .sidebar-header {
        padding: 20px 16px; /* Reduce padding on mobile */
        gap: 8px;
      }

      .sidebar-logo {
        height: 48px; /* Smaller on mobile but still prominent */
        max-width: 100px;
      }

      .company-name {
        font-size: 14px;
      }
    }

    /* Custom scrollbar - Flat */
    .sidebar::-webkit-scrollbar {
      width: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: #ffffff; /* White scrollbar track */
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: #cbd5e0;
      border-radius: 2px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
      background: #a0aec0;
    }

    /* Focus states for accessibility */
    .nav-item:focus,
    .nav-sub:focus,
    .nav-section:focus {
      outline: 2px solid #4299e1;
      outline-offset: -2px;
    }

    /* Remove any remaining shadow effects */
    * {
      box-shadow: none !important;
    }

  </style>

  @stack('styles')

</head>
<body>
  <div class="app-container">
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Updated Header with Logo -->
      <div class="sidebar-header">
        <img src="{{ asset('logo/revival logo.jpeg') }}" alt="Revival Technologies Logo" class="sidebar-logo">
        <span class="company-name">Revival Technologies</span>
      </div>

      <nav>
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
  <span class="nav-icon">📊</span> Company Dashboard
</a>

        <a href="{{ route('employee.dashboard') }}" class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
          <span class="nav-icon">📈</span> Employee Dashboard
        </a>


        <!-- Assets -->
        <div class="nav-section" onclick="toggleMenu(this)">
          <span class="nav-icon">💼</span> Assets Management
        </div>
        <div class="submenu {{ request()->routeIs('assets.*','asset-requests.*','asset-approvals.*','pos-terminals.*','business-licenses.*') ? 'show' : '' }}">
          <a href="{{ route('assets.index') }}" class="nav-sub {{ request()->routeIs('assets.*') ? 'active' : '' }}">
            <span class="nav-icon">🏢</span> Internal Assets
          </a>
          <a href="{{ route('pos-terminals.index') }}" class="nav-sub {{ request()->routeIs('pos-terminals.*') ? 'active' : '' }}">
            <span class="nav-icon">💳</span> POS Terminals
          </a>
          <a href="{{ route('asset-requests.catalog') }}" class="nav-sub {{ request()->routeIs('asset-requests.catalog') ? 'active' : '' }}">
            <span class="nav-icon">🛒</span> Request Assets
          </a>
          <a href="{{ route('asset-requests.index') }}" class="nav-sub {{ request()->routeIs('asset-requests.index') ? 'active' : '' }}">
            <span class="nav-icon">📋</span> My Requests
          </a>
          <a href="{{ route('asset-approvals.index') }}" class="nav-sub {{ request()->routeIs('asset-approvals.*') ? 'active' : '' }}">
            <span class="nav-icon">✅</span> Asset Approvals
          </a>
          <a href="{{ route('business-licenses.index') }}" class="nav-sub {{ request()->routeIs('business-licenses.*') ? 'active' : '' }}">
            <span class="nav-icon">📄</span> Business Licenses
          </a>
        </div>

        <!-- Field Operations -->
        <div class="nav-section" onclick="toggleMenu(this)">
          <span class="nav-icon">🔧</span> Field Operations
        </div>
        <div class="submenu {{ request()->routeIs('deployment.*','jobs.*','reports.technician-visits*','tickets.*','visits.*') ? 'show' : '' }}">
  <a href="{{ route('deployment.hierarchical') }}" class="nav-sub {{ request()->routeIs('deployment.hierarchical') ? 'active' : '' }}">
    <span class="nav-icon">🚀</span> Terminal Deployment
  </a>
  <a href="{{ route('jobs.index') }}" class="nav-sub {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
    <span class="nav-icon">📋</span> All Job Assignment
  </a>



  <a href="{{ route('visits.index') }}"
   class="nav-sub {{ request()->routeIs('visits.*') ? 'active' : '' }}">
  <span class="nav-icon">📝</span> Site Visits
</a>


  <a href="{{ route('tickets.index') }}" class="nav-sub {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
    <span class="nav-icon">🎫</span> Support Tickets
  </a>
</div>

<!-- Project Management -->
<div class="nav-section" onclick="toggleMenu(this)">
  <span class="nav-icon">📋</span> Project Management
</div>
<div class="submenu {{ request()->routeIs('projects.*') ? 'show' : '' }}">
  <a href="{{ route('projects.index') }}" class="nav-sub {{ request()->routeIs('projects.index','projects.show','projects.edit','projects.create') ? 'active' : '' }}">
    <span class="nav-icon">📊</span> Projects
  </a>
  <a href="{{ route('projects.create') }}" class="nav-sub {{ request()->routeIs('projects.create') ? 'active' : '' }}">
    <span class="nav-icon">➕</span> New Project
  </a>
  <a href="{{ route('projects.closure-reports') }}" class="nav-sub {{ request()->routeIs('projects.completion-reports') ? 'active' : '' }}">
    <span class="nav-icon">📄</span> Project Closure and Reports
  </a>
</div>
       <!-- Client Management -->
<div class="nav-section" onclick="toggleMenu(this)">
  <span class="nav-icon">👥</span> Client Management
</div>
<div class="submenu {{ request()->routeIs('clients.*','client-dashboards.*') ? 'show' : '' }}">
  <a href="{{ route('clients.index') }}" class="nav-sub {{ request()->routeIs('clients.index','clients.show','clients.edit','clients.create') ? 'active' : '' }}">
    <span class="nav-icon">🏢</span> Clients
  </a>
  <a href="{{ route('client-dashboards.index') }}" class="nav-sub {{ request()->routeIs('client-dashboards.*') ? 'active' : '' }}">
    <span class="nav-icon">📊</span> Client Dashboards
  </a>
</div>

        <!-- Employee Management -->
        <div class="nav-section" onclick="toggleMenu(this)">
          <span class="nav-icon">👤</span> Employee Management
        </div>
        <div class="submenu {{ request()->routeIs('employees.*','roles.*','technicians.*') ? 'show' : '' }}">
          <a href="{{ route('employees.index') }}" class="nav-sub {{ request()->routeIs('employees.*') ? 'active' : '' }}">
            <span class="nav-icon">👥</span> Employees
          </a>
          <a href="{{ route('roles.index') }}" class="nav-sub {{ request()->routeIs('roles.*') ? 'active' : '' }}">
            <span class="nav-icon">🔐</span> Role Management
          </a>
        </div>

        <!-- Technician -->
        <div class="nav-section" onclick="toggleMenu(this)">
          <span class="nav-icon">🔧</span> Technician Portal
        </div>
        <div class="submenu {{ request()->routeIs('technician.jobs','technician.reports','technician.schedule') ? 'show' : '' }}">
           <a href="{{ route('employee.dashboard') }}" class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
          <span class="nav-icon">📈</span> Employee Dashboard
        </a>
        <a href="{{ route('jobs.mine') }}" class="nav-sub {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
            <span class="nav-icon">📋</span> MyJob Assignment
          </a>

        </div>

        <!-- Administration -->
        <div class="nav-section" onclick="toggleMenu(this)">
          <span class="nav-icon">⚙️</span> Administration
        </div>
        <div class="submenu {{ request()->routeIs('admin.*','settings.*','documents.*') ? 'show' : '' }}">
          <a href="{{ route('settings.index') }}" class="nav-sub {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <span class="nav-icon">🔧</span> System Settings
          </a>

        </div>

       <!-- Reports & Analytics -->
<div class="nav-section" onclick="toggleMenu(this)">
  <span class="nav-icon">📊</span> Reports & Analytics
</div>
<div class="submenu {{ request()->routeIs('reports.*') ? 'show' : '' }}">
  <!-- Main System Dashboard -->
  <a href="{{ route('reports.index') }}" class="nav-sub {{ request()->routeIs('reports.index', 'reports.system') ? 'active' : '' }}">
    <span class="nav-icon">📈</span> Reports Dashboard
  </a>

  <!-- Technician Visit Reports -->
  <a href="{{ route('reports.technician-visits') }}" class="nav-sub {{ request()->routeIs('reports.technician-visits') ? 'active' : '' }}">
    <span class="nav-icon">👨‍🔧</span> Technician Visits
  </a>

  <!-- Report Builder -->
  <a href="{{ route('reports.builder') }}" class="nav-sub {{ request()->routeIs('reports.builder') ? 'active' : '' }}">
    <span class="nav-icon">🏗️</span> Report Builder
  </a>
</div>


        <!-- My Account -->
        <div class="nav-section" onclick="toggleMenu(this)">
          <span class="nav-icon">👤</span> My Account
        </div>
        <div class="submenu {{ request()->routeIs('employee.profile*') ? 'show' : '' }}">
          <a href="{{ route('employee.profile') }}" class="nav-sub {{ request()->routeIs('employee.profile*') ? 'active' : '' }}">
            <span class="nav-icon">👤</span> My Profile
          </a>

          <!-- Logout inside My Account -->
          <form method="POST" action="{{ route('logout') }}" class="logout-form" style="margin: 0; border: none;">
            @csrf
            <button type="submit" class="nav-sub" style="width: 100%; text-align: left; padding: 12px 16px; display: flex; align-items: center; gap: 12px; background: transparent; border: none; color: #e53e3e; cursor: pointer; font-weight: 500; font-size: 14px; border-left: 3px solid transparent;">
              <span class="nav-icon" style="color: #e53e3e;">🚪</span> Sign Out
            </button>
          </form>
        </div>
      </nav>
    </div>

    <!-- Main content -->
    <div class="main-content">
      <div class="content-header">
        <h1 class="page-title">{{ $title ?? 'Dashboard' }}</h1>
        <div class="user-info" style="position: relative;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div style="text-align: right;">
              <div style="font-weight: 500;">{{ auth()->user()->full_name }}</div>
              <span class="user-badge">
                @foreach(auth()->user()->roles as $role)
                  {{ $role->name }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
              </span>
            </div>
            <div style="position: relative;">
              <button type="button" onclick="toggleUserDropdown(event)" style="background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 18px;">
                👤
              </button>
              <div id="userDropdown" style="display: none; position: absolute; top: 50px; right: 0; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); min-width: 180px; z-index: 1000;">
                <a href="{{ route('employee.profile') }}" style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; text-decoration: none; color: #374151; border-bottom: 1px solid #e5e7eb;">
                  <span>👤</span> My Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                  @csrf
                  <button type="submit" style="width: 100%; display: flex; align-items: center; gap: 8px; padding: 12px 16px; background: transparent; border: none; color: #dc2626; cursor: pointer; text-align: left; font-size: 14px;">
                    <span>🚪</span> Sign Out
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="content-body">
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
          <span style="font-size: 24px;">✅</span>
          <span>{{ session('success') }}</span>
          <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; font-size: 20px; cursor: pointer; color: #155724;">&times;</button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
          <span style="font-size: 24px;">❌</span>
          <span>{{ session('error') }}</span>
          <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; font-size: 20px; cursor: pointer; color: #721c24;">&times;</button>
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning" style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
          <span style="font-size: 24px;">⚠️</span>
          <span>{{ session('warning') }}</span>
          <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; font-size: 20px; cursor: pointer; color: #856404;">&times;</button>
        </div>
        @endif

        @yield('content')
      </div>
    </div>
  </div>

  @stack('scripts')

  <script>
    function toggleUserDropdown(event) {
      if (event) event.stopPropagation();
      const dropdown = document.getElementById('userDropdown');
      dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('userDropdown');
      if (!dropdown) return;

      const userInfo = event.target.closest('.user-info');
      if (!userInfo && dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
      }
    });

    function toggleMenu(header) {
      const submenu = header.nextElementSibling;
      const isCurrentlyOpen = header.classList.contains('open');

      // Close all other open menus first
      document.querySelectorAll('.nav-section').forEach(otherHeader => {
        if (otherHeader !== header && otherHeader.classList.contains('open')) {
          const otherSubmenu = otherHeader.nextElementSibling;
          otherSubmenu.classList.remove('show');
          otherHeader.classList.remove('open');
        }
      });

      // Toggle the clicked menu
      if (isCurrentlyOpen) {
        submenu.classList.remove('show');
        header.classList.remove('open');
      } else {
        submenu.classList.add('show');
        header.classList.add('open');
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.nav-section').forEach(header => {
        const submenu = header.nextElementSibling;
        if (submenu.querySelector('.nav-sub.active')) {
          submenu.classList.add('show');
          header.classList.add('open');
        }
      });
    });
  </script>
</body>
</html>
