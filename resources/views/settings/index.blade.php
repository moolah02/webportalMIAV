@extends('layouts.app')

@section('content')
<style>
  .settings-card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
  }

  .settings-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    transform: translateY(-3px);
  }

  .settings-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    margin: -25px -25px 20px -25px;
    font-weight: 600;
    font-size: 16px;
  }

  .settings-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .settings-list li {
    margin-block-end: 12px;
  }

  .settings-list a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .settings-list a:hover {
    background: #e9ecef;
    color: #000;
    transform: translateX(5px);
  }

  .badge {
    background: #6c757d;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-block-end: 30px;
  }

  .stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
  }

  .stat-number {
    font-size: 36px;
    font-weight: 800;
    color: #2196f3;
    margin-block-end: 10px;
  }

  .stat-label {
    color: #666;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
  }
</style>

<div class="container-fluid">
  <!-- Page Header -->
  <div style="background: #fff; padding: 25px; border-radius: 12px; margin-block-end: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <h1 style="margin: 0; color: #2c3e50; font-weight: 700;">⚙️ Settings & Configuration</h1>
    <p style="margin: 8px 0 0 0; color: #666;">Manage system categories and configurations</p>
  </div>

  <!-- Stats Overview -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-number">{{ $stats['total_categories'] }}</div>
      <div class="stat-label">Total Categories</div>
    </div>
    <div class="stat-card">
      <div class="stat-number">{{ $stats['asset_categories'] }}</div>
      <div class="stat-label">Asset Categories</div>
    </div>
    <div class="stat-card">
      <div class="stat-number">{{ $stats['total_departments'] }}</div>
      <div class="stat-label">Departments</div>
    </div>
    <div class="stat-card">
      <div class="stat-number">{{ $stats['total_roles'] }}</div>
      <div class="stat-label">Roles</div>
    </div>
  </div>

  <!-- Settings Cards -->
  <div class="settings-grid">
    <!-- System Categories -->
    <div class="settings-card">
      <div class="settings-header">📋 System Categories</div>
      <p style="color: #666; margin-block-end: 20px;">Manage all system categories and classifications</p>
      <ul class="settings-list">
        @foreach($categoryTypes as $type => $label)
          <li>
            <a href="{{ route('settings.category.manage', $type) }}">
              <span>{{ $label }}</span>
              <span class="badge">{{ $categories->get($type, collect())->count() }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <!-- Asset Configuration -->
    <div class="settings-card">
      <div class="settings-header">📦 Asset Configuration</div>
      <p style="color: #666; margin-block-end: 20px;">Configure asset-related settings</p>
      <ul class="settings-list">
        <li>
          <a href="{{ route('settings.category.manage', 'asset_category') }}">
            <span>🏷️ Asset Categories</span>
            <span class="badge">{{ $categories->get('asset_category', collect())->count() }}</span>
          </a>
        </li>
        <li>
          <a href="{{ route('settings.category.manage', 'asset_status') }}">
            <span>📊 Asset Status</span>
            <span class="badge">{{ $categories->get('asset_status', collect())->count() }}</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Terminal Configuration -->
    <div class="settings-card">
      <div class="settings-header">🖥️ Terminal Configuration</div>
      <p style="color: #666; margin-block-end: 20px;">Configure POS terminal settings</p>
      <ul class="settings-list">
        <li>
          <a href="{{ route('settings.category.manage', 'terminal_status') }}">
            <span>📶 Terminal Status</span>
            <span class="badge">{{ $categories->get('terminal_status', collect())->count() }}</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Service Configuration -->
    <div class="settings-card">
      <div class="settings-header">🔧 Service Configuration</div>
      <p style="color: #666; margin-block-end: 20px;">Configure service and job settings</p>
      <ul class="settings-list">
        <li>
          <a href="{{ route('settings.category.manage', 'service_type') }}">
            <span>⚙️ Service Types</span>
            <span class="badge">{{ $categories->get('service_type', collect())->count() }}</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Department Management -->
    <div class="settings-card">
      <div class="settings-header">🏢 Department Management</div>
      <p style="color: #666; margin-block-end: 20px;">Manage company departments and teams</p>
      <ul class="settings-list">
        <li>
          <a href="{{ route('settings.departments.manage') }}">
            <span>📂 All Departments</span>
            <span class="badge">{{ $stats['total_departments'] }}</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- System Settings -->
    <div class="settings-card">
      <div class="settings-header">⚙️ System Settings</div>
      <p style="color: #666; margin-block-end: 20px;">General system configuration</p>
      <ul class="settings-list">
        <li>
          <a href="#" onclick="alert('Coming Soon!')">
            <span>📧 Email Settings</span>
            <span class="badge">Soon</span>
          </a>
        </li>
        <li>
          <a href="#" onclick="alert('Coming Soon!')">
            <span>🔔 Notifications</span>
            <span class="badge">Soon</span>
          </a>
        </li>
        <li>
          <a href="#" onclick="alert('Coming Soon!')">
            <span>💾 Backup Settings</span>
            <span class="badge">Soon</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
@endsection
