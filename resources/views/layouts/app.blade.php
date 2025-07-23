{{-- 
==============================================
IMPROVED LAYOUT FILE
File: resources/views/layouts/app.blade.php
==============================================
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Revival Technologies') }} - {{ $title ?? 'Dashboard' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            font-size: 18px;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item {
            display: block;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            transform: translateX(5px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-right: 4px solid #00d4ff;
            color: white;
            box-shadow: inset 0 0 20px rgba(255,255,255,0.1);
        }

        .nav-sub {
            padding: 12px 20px 12px 55px;
            font-size: 14px;
            color: rgba(255,255,255,0.7);
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
        }

        .nav-sub:hover {
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transform: translateX(3px);
        }

        .nav-section {
            padding: 10px 20px;
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            background: #f5f5f5;
        }

        .content-header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #666;
        }

        .user-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .content-body {
            padding: 30px;
        }

        /* Dashboard Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .metric-number {
            font-size: 36px;
            font-weight: bold;
            color: #2196f3;
            margin-bottom: 8px;
            line-height: 1;
        }

        .metric-label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid #eee;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-item {
            margin-bottom: 12px;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .info-value {
            color: #666;
        }

        /* Activity List */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            font-size: 16px;
            margin-top: 2px;
        }

        .activity-text {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Logout Form */
        .logout-form {
            background: none;
            border: none;
            width: 100%;
        }

        .logout-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.8);
            width: 100%;
            text-align: left;
            padding: 0;
            font-size: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logout-btn:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                🚀 Revival Technologies
            </div>
            
            {{-- 
==============================================
FIXED SIDEBAR NAVIGATION
Update this section in your resources/views/layouts/app.blade.php
==============================================
--}}

{{-- 
==============================================
UNRESTRICTED SIDEBAR NAVIGATION
Replace the <nav> section in your resources/views/layouts/app.blade.php
==============================================
--}}

<nav>
    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        📊 Dashboard
    </a>
    
    {{-- Employee Dashboard for regular employees --}}
    <a href="{{ route('employee.dashboard') }}" class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
        📊 My Dashboard
    </a>
    
    {{-- Technician Dashboard --}}
    <a href="{{ route('technician.dashboard') }}" class="nav-item {{ request()->routeIs('technician.dashboard') ? 'active' : '' }}">
        🔧 Technician Dashboard
    </a>
    
    {{-- Assets Section --}}
    <div class="nav-section">Assets</div>
    
    {{-- Asset Management --}}
    <a href="{{ route('assets.index') }}" class="nav-sub {{ request()->routeIs('assets.*') ? 'active' : '' }}">
        📦 Manage Assets
    </a>
    <a href="{{ route('pos-terminals.index') }}" class="nav-sub {{ request()->routeIs('pos-terminals.*') ? 'active' : '' }}">
        🖥️ POS Terminals
    </a>
    
    {{-- Asset Requests --}}
    <a href="{{ route('asset-requests.catalog') }}" class="nav-sub {{ request()->routeIs('asset-requests.catalog') ? 'active' : '' }}">
        🛒 Request Assets
    </a>
    <a href="{{ route('asset-requests.index') }}" class="nav-sub {{ request()->routeIs('asset-requests.index') ? 'active' : '' }}">
        📝 My Requests
    </a>
    
    {{-- Asset Approvals --}}
    <a href="{{ route('asset-approvals.index') }}" class="nav-sub {{ request()->routeIs('asset-approvals.*') ? 'active' : '' }}">
        ⚖️ Asset Approvals
    </a>
    
    {{-- Licenses --}}
    <a href="{{ route('assets.licenses') }}" class="nav-sub {{ request()->routeIs('assets.licenses') ? 'active' : '' }}">
        🔑 Business Licenses
    </a>
    
    {{-- Client Management Section --}}
    <div class="nav-section">Client Management</div>
    <a href="{{ route('clients.index') }}" class="nav-sub {{ request()->routeIs('clients.*') ? 'active' : '' }}">
        🏢 Clients
    </a>
    
    {{-- Employee & Role Management Section --}}
    <div class="nav-section">Employee Management</div>
    <a href="{{ route('employees.index') }}" class="nav-sub {{ request()->routeIs('employees.*') ? 'active' : '' }}">
        👥 Employees
    </a>
    <a href="{{ route('roles.index') }}" class="nav-sub {{ request()->routeIs('roles.*') ? 'active' : '' }}">
        🔑 Role Management
    </a>
    <a href="{{ route('technicians.index') }}" class="nav-sub {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
        🔧 Technicians
    </a>
    
    {{-- Technician Section --}}
    <div class="nav-section">Technician</div>
    <a href="{{ route('technician.jobs') }}" class="nav-sub {{ request()->routeIs('technician.jobs') ? 'active' : '' }}">
        🔧 My Jobs
    </a>
    <a href="{{ route('technician.reports') }}" class="nav-sub {{ request()->routeIs('technician.reports') ? 'active' : '' }}">
        📋 Service Reports
    </a>
    <a href="{{ route('technician.schedule') }}" class="nav-sub {{ request()->routeIs('technician.schedule') ? 'active' : '' }}">
        📅 My Schedule
    </a>
    
    {{-- Management Section --}}
    <div class="nav-section">Management</div>
    <a href="{{ route('manager.team') }}" class="nav-sub {{ request()->routeIs('manager.team') ? 'active' : '' }}">
        👥 Team Overview
    </a>
    <a href="{{ route('manager.approvals') }}" class="nav-sub {{ request()->routeIs('manager.approvals') ? 'active' : '' }}">
        📋 Pending Approvals
    </a>
    <a href="{{ route('manager.reports') }}" class="nav-sub {{ request()->routeIs('manager.reports') ? 'active' : '' }}">
        📊 Team Reports
    </a>
    
    {{-- Administration Section --}}
    <div class="nav-section">Administration</div>
    <a href="{{ route('admin.employees') }}" class="nav-sub {{ request()->routeIs('admin.employees') ? 'active' : '' }}">
        👤 Employee Admin
    </a>
    <a href="{{ route('admin.roles') }}" class="nav-sub {{ request()->routeIs('admin.roles') ? 'active' : '' }}">
        🔐 Role Admin
    </a>
    <a href="{{ route('admin.departments') }}" class="nav-sub {{ request()->routeIs('admin.departments') ? 'active' : '' }}">
        🏢 Departments
    </a>
    <a href="{{ route('admin.settings') }}" class="nav-sub {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        ⚙️ System Settings
    </a>
    
    {{-- Reports Section --}}
    <div class="nav-section">Reports & Analytics</div>
    <a href="{{ route('reports.index') }}" class="nav-sub {{ request()->routeIs('reports.index') ? 'active' : '' }}">
        📊 Reports Dashboard
    </a>
    <a href="{{ route('reports.builder') }}" class="nav-sub {{ request()->routeIs('reports.builder') ? 'active' : '' }}">
        🔨 Report Builder
    </a>
    
    {{-- General Section --}}
    <div class="nav-section">General</div>
    <a href="{{ route('tickets.index') }}" class="nav-sub {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
        🎫 Support Tickets
    </a>
    <a href="{{ route('documents.index') }}" class="nav-sub {{ request()->routeIs('documents.*') ? 'active' : '' }}">
        📁 Documents
    </a>
    
    {{-- Document Upload --}}
    <a href="{{ route('documents.upload') }}" class="nav-sub {{ request()->routeIs('documents.upload') ? 'active' : '' }}">
        📤 Upload Documents
    </a>
    
    {{-- Profile Section --}}
    <div class="nav-section">My Account</div>
    <a href="{{ route('profile.index') }}" class="nav-sub {{ request()->routeIs('profile.index') ? 'active' : '' }}">
        👤 My Profile
    </a>
    <a href="{{ route('profile.edit') }}" class="nav-sub {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
        ✏️ Edit Profile
    </a>
    
    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" class="nav-item logout-form">
        @csrf
        <button type="submit" class="logout-btn">
            🚪 Logout
        </button>
    </form>
</nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="content-header">
                <h1 class="page-title">{{ $title ?? 'Dashboard' }}</h1>
                <div class="user-info">
                    Welcome, {{ auth()->user()->full_name }}
                    <span class="user-badge">
                        {{ auth()->user()->role->name ?? 'Employee' }}
                    </span>
                </div>
            </div>

            <!-- Content Body -->
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>