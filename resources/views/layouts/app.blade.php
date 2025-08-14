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

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* Reset & Base */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #333; line-height: 1.6; }

    /* Sidebar */
    .sidebar { 
      position: fixed; 
      top: 0; 
      left: 0; 
      inline-size: 280px; 
      height: 100vh; 
      background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%); 
      color: #fff; 
      overflow-y: auto; 
      box-shadow: 2px 0 20px rgba(0,0,0,0.15);
      border-right: 1px solid rgba(255,255,255,0.1);
    }
    
    .sidebar-header { 
      padding: 25px 20px; 
      font-size: 20px; 
      font-weight: 700; 
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
      border-block-end: 2px solid rgba(255,255,255,0.1);
      text-align: center;
      letter-spacing: 0.5px;
    }

    /* Nav items */
    .nav-item { 
      display: flex; 
      align-items: center; 
      gap: 15px; 
      padding: 16px 20px; 
      color: rgba(255,255,255,0.85); 
      text-decoration: none; 
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
      font-weight: 500;
      position: relative;
    }
    
    .nav-item:hover { 
      background: rgba(255,255,255,0.12); 
      color: #fff; 
      padding-left: 25px;
    }
    
    .nav-item.active { 
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%); 
      color: #fff; 
      font-weight: 600;
      border-inline-start: 4px solid #00d4ff;
      box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1);
    }

    /* Section headers - improved design */
    .nav-section { 
      padding: 18px 20px; 
      font-size: 13px; 
      font-weight: 600; 
      text-transform: uppercase; 
      letter-spacing: 1px;
      cursor: pointer; 
      transition: all 0.3s ease; 
      position: relative;
      background: rgba(0,0,0,0.15);
      border-block-start: 1px solid rgba(255,255,255,0.05);
      border-block-end: 1px solid rgba(255,255,255,0.05);
      color: rgba(255,255,255,0.9);
    }
    
    .nav-section:hover { 
      background: rgba(255,255,255,0.08);
      color: #fff;
    }
    
    /* Modern chevron indicator */
    .nav-section::after { 
      content: '';
      position: absolute; 
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      inline-size: 8px;
      height: 8px;
      border-right: 2px solid rgba(255,255,255,0.7);
      border-block-end: 2px solid rgba(255,255,255,0.7);
      transform: translateY(-50%) rotate(-45deg);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .nav-section.open::after { 
      transform: translateY(-50%) rotate(45deg);
      border-color: rgba(255,255,255,0.9);
    }

    /* Submenus - enhanced styling */
    .submenu { 
      max-height: 0;
      overflow: hidden;
      background: rgba(0,0,0,0.2);
      border-block-end: 1px solid rgba(255,255,255,0.05);
      transition: max-height 0.3s ease-out;
    }
    
    .submenu.show { 
      max-height: 500px;
    }
    
    .nav-sub { 
      display: flex;
      align-items: center;
      padding: 14px 20px 14px 45px; 
      font-size: 14px; 
      color: rgba(255,255,255,0.75); 
      text-decoration: none;
      transition: all 0.3s ease; 
      position: relative;
      font-weight: 500;
    }
    
    /* Connector line for nested items */
    .nav-sub::before {
      content: '';
      position: absolute;
      left: 30px;
      top: 50%;
      inline-size: 8px;
      height: 1px;
      background: rgba(255,255,255,0.3);
      transform: translateY(-50%);
    }
    
    .nav-sub:hover { 
      background: rgba(255,255,255,0.08); 
      color: rgba(255,255,255,0.95); 
      padding-left: 50px;
    }
    
    .nav-sub.active { 
      color: #fff; 
      font-weight: 600;
      background: rgba(255,255,255,0.1);
      border-inline-start: 3px solid #00d4ff;
    }
    
    .nav-sub.active::before {
      background: #00d4ff;
      inline-size: 12px;
    }

    /* Main content */
    .main-content { 
      margin-left: 280px; 
      min-height: 100vh; 
      background: #f5f5f5; 
    }
    
    .content-header { 
      background: #fff; 
      padding: 25px 35px; 
      border-block-end: 1px solid #eee; 
      display: flex; 
      justify-content: space-between; 
      align-items: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .page-title { 
      font-size: 28px; 
      font-weight: 700; 
      color: #2c3e50;
      margin: 0;
    }
    
    .user-info { 
      display: flex; 
      align-items: center; 
      gap: 15px; 
      color: #666;
      font-weight: 500;
    }
    
    .user-badge { 
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
      color: #fff; 
      padding: 6px 14px; 
      border-radius: 20px; 
      font-size: 12px; 
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .content-body { 
      padding: 35px; 
    }

    /* Dashboard cards */
    .dashboard-grid { 
      display: grid; 
      grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); 
      gap: 25px; 
      margin-block-end: 35px; 
    }
    
    .metric-card { 
      background: #fff; 
      border-radius: 12px; 
      padding: 30px; 
      text-align: center; 
      box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
      border: 1px solid rgba(0,0,0,0.05); 
      transition: all 0.3s ease; 
    }
    
    .metric-card:hover { 
      box-shadow: 0 8px 30px rgba(0,0,0,0.12); 
      transform: translateY(-3px); 
    }
    
    .metric-number { 
      font-size: 42px; 
      font-weight: 800; 
      color: #2196f3; 
      margin-block-end: 12px; 
    }
    
    .metric-label { 
      color: #666; 
      font-size: 14px; 
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Logout styling */
    .logout-form { 
      border: none; 
      margin-block-start: 20px;
      border-top: 2px solid rgba(255,255,255,0.1);
    }
    
    .logout-btn { 
      inline-size: 100%; 
      text-align: start; 
      padding: 18px 20px; 
      display: flex; 
      align-items: center; 
      gap: 15px; 
      background: none; 
      border: none; 
      color: rgba(255,255,255,0.8); 
      cursor: pointer; 
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .logout-btn:hover { 
      color: #ff6b6b;
      background: rgba(255,107,107,0.1);
      padding-left: 25px;
    }

    /* Responsive */
    @media(max-inline-size:768px){
      .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; inline-size: 280px; }
      .sidebar.open { transform: translateX(0); }
      .main-content { margin-left: 0; }
      .content-header { padding: 20px; }
      .content-body { padding: 20px; }
    }

    /* Custom scrollbar for sidebar */
    .sidebar::-webkit-scrollbar {
      inline-size: 6px;
    }
    
    .sidebar::-webkit-scrollbar-track {
      background: rgba(255,255,255,0.1);
    }
    
    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(255,255,255,0.3);
      border-radius: 3px;
    }
    
    .sidebar::-webkit-scrollbar-thumb:hover {
      background: rgba(255,255,255,0.5);
    }

  </style>
  
  <!-- ADD THIS LINE: Support for pushed styles from individual pages -->
  @stack('styles')
  
</head>
<body>
  <div class="app-container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">🚀 Revival Technologies</div>
      <nav>
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard</a>
        <a href="{{ route('employee.dashboard') }}" class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">📊 My Dashboard</a>
        

        <!-- Assets -->
        <div class="nav-section" onclick="toggleMenu(this)">📦 Assets</div>
        <div class="submenu {{ request()->routeIs('assets.*','asset-requests.*','asset-approvals.*','pos-terminals.*','business-licenses.*') ? 'show' : '' }}">
          <a href="{{ route('assets.index') }}" class="nav-sub {{ request()->routeIs('assets.*') ? 'active' : '' }}">📦 Internal Assets</a>
          <a href="{{ route('pos-terminals.index') }}" class="nav-sub {{ request()->routeIs('pos-terminals.*') ? 'active' : '' }}">🖥️ POS Terminals</a>
          <a href="{{ route('asset-requests.catalog') }}" class="nav-sub {{ request()->routeIs('asset-requests.catalog') ? 'active' : '' }}">🛒 Request Assets</a>
          <a href="{{ route('asset-requests.index') }}" class="nav-sub {{ request()->routeIs('asset-requests.index') ? 'active' : '' }}">📝 My Requests</a>
          <a href="{{ route('asset-approvals.index') }}" class="nav-sub {{ request()->routeIs('asset-approvals.*') ? 'active' : '' }}">⚖️ Asset Approvals</a>
          <a href="{{ route('business-licenses.index') }}" class="nav-sub {{ request()->routeIs('business-licenses.*') ? 'active' : '' }}">📋 Business Licenses</a>
        </div>

      <!-- Field Operations -->
<div class="nav-section" onclick="toggleMenu(this)">🗺️ Field Operations</div>
<div class="submenu {{ request()->routeIs('deployment.*','jobs.*','reports.technician-visits*') ? 'show' : '' }}">
  
  <!-- REPLACE the old deployment planning with the new hierarchical system -->
  <a href="{{ route('deployment.hierarchical') }}"
     class="nav-sub {{ request()->routeIs('deployment.hierarchical') ? 'active' : '' }}">
    🗺️ Terminal Deployment
  </a>
  
  <!-- Keep Job Assignment separate -->
  <a href="{{ route('jobs.assignment') }}"
     class="nav-sub {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
    📋 Job Assignment
  </a>

  <!-- Optional: Add Technician Visits if you have this -->
  <!-- 
  <a href="{{ route('reports.technician-visits') }}"
     class="nav-sub {{ request()->routeIs('reports.technician-visits*') ? 'active' : '' }}">
    📊 Technician Reports
  </a>
  -->


  <a href="{{ route('reports.technician-visits') }}"
     class="nav-sub {{ request()->routeIs('reports.technician-visits*') ? 'active' : '' }}">
    📄 Technician Reports
  </a>
  <a href="{{ route('tickets.index') }}"
   class="nav-sub {{ request()->routeIs('tickets.*') ? 'active' : '' }}">🎫 Support Tickets</a>
</div>


        <!-- Client Management -->
        <div class="nav-section" onclick="toggleMenu(this)">🏢 Client Management</div>
        <div class="submenu {{ request()->routeIs('clients.*') ? 'show' : '' }}">
          <a href="{{ route('clients.index') }}" class="nav-sub {{ request()->routeIs('clients.*') ? 'active' : '' }}">Clients</a>
        </div>

        <!-- Employee Management -->
        <div class="nav-section" onclick="toggleMenu(this)">👥 Employee Management</div>
        <div class="submenu {{ request()->routeIs('employees.*','roles.*','technicians.*') ? 'show' : '' }}">
          <a href="{{ route('employees.index') }}" class="nav-sub {{ request()->routeIs('employees.*') ? 'active' : '' }}">Employees</a>
          <a href="{{ route('roles.index') }}" class="nav-sub {{ request()->routeIs('roles.*') ? 'active' : '' }}">Role Management</a>
        </div>

        <!-- Technician -->
        <div class="nav-section" onclick="toggleMenu(this)">🔧 Technician</div>
        <div class="submenu {{ request()->routeIs('technician.jobs','technician.reports','technician.schedule') ? 'show' : '' }}">
          <a href="{{ route('technician.jobs') }}" class="nav-sub {{ request()->routeIs('technician.jobs') ? 'active' : '' }}">🔧 My Jobs</a>
          <a href="{{ route('technician.reports') }}" class="nav-sub {{ request()->routeIs('technician.reports') ? 'active' : '' }}">📋 Service Reports</a>
          <a href="{{ route('technician.schedule') }}" class="nav-sub {{ request()->routeIs('technician.schedule') ? 'active' : '' }}">📅 My Schedule</a>
        </div>

      
        <!-- Administration -->
        <div class="nav-section" onclick="toggleMenu(this)">🏛️ Administration</div>
        <div class="submenu {{ request()->routeIs('admin.*') ? 'show' : '' }}">
          
          <a href="{{ route('settings.index') }}" class="nav-sub {{ request()->routeIs('settings.*') ? 'active' : '' }}">⚙️ System Settings</a>
          <a href="{{ route('tickets.index') }}" class="nav-sub {{ request()->routeIs('tickets.*') ? 'active' : '' }}">🎫 Support Tickets</a>
          <a href="{{ route('documents.index') }}" class="nav-sub {{ request()->routeIs('documents.*') ? 'active' : '' }}">📁 Documents</a>
        
        </div>




        <!-- Reports & Analytics -->
        <div class="nav-section" onclick="toggleMenu(this)">📊 Reports & Analytics</div>
        <div class="submenu {{ request()->routeIs('reports.*') ? 'show' : '' }}">
          <a href="{{ route('reports.index') }}" class="nav-sub {{ request()->routeIs('reports.index') ? 'active' : '' }}">📊 Reports Dashboard</a>
          <a href="{{ route('reports.builder') }}" class="nav-sub {{ request()->routeIs('reports.builder') ? 'active' : '' }}">🔨 Report Builder</a>
        </div>

        <!-- My Account -->
<div class="nav-section" onclick="toggleMenu(this)">👤 My Account</div>
<div class="submenu {{ request()->routeIs('employee.profile*') ? 'show' : '' }}">
  <a href="{{ route('employee.profile') }}" class="nav-sub {{ request()->routeIs('employee.profile*') ? 'active' : '' }}">👤 My Profile</a>
</div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
          @csrf
          <button type="submit" class="logout-btn">🚪 Logout</button>
        </form>
      </nav>
    </div>

    <!-- Main content -->
    <div class="main-content">
      <div class="content-header">
        <h1 class="page-title">{{ $title ?? 'Dashboard' }}</h1>
        <div class="user-info">
          Welcome, {{ auth()->user()->full_name }}
          <span class="user-badge">{{ auth()->user()->role->name ?? 'Employee' }}</span>
        </div>
      </div>
      <div class="content-body">
        @yield('content')
      </div>
    </div>
  </div>

  <!-- ADD THIS LINE: Support for pushed scripts from individual pages -->
  @stack('scripts')

  <script>
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
        // Close this menu
        submenu.classList.remove('show');
        header.classList.remove('open');
      } else {
        // Open this menu
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