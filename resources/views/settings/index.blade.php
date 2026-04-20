@extends('layouts.app')
@section('title', 'System Settings')

@section('content')

<div class="">
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
              <span class="badge badge-gray">{{ $categories->get($type, collect())->count() }}</span>
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
            <span class="badge badge-gray">{{ $categories->get('asset_category', collect())->count() }}</span>
          </a>
        </li>
        <li>
          <a href="{{ route('settings.category.manage', 'asset_status') }}">
            <span>📊 Asset Status</span>
            <span class="badge badge-gray">{{ $categories->get('asset_status', collect())->count() }}</span>
          </a>
        </li>
        <li>
          <a href="{{ route('settings.asset-category-fields.index', $categories->get('asset_category', collect())->first()?->id ?? 1) }}">
            <span>⚙️ Category Custom Fields</span>
            <span class="badge badge-gray">Manage</span>
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
            <span class="badge badge-gray">{{ $categories->get('terminal_status', collect())->count() }}</span>
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
            <span class="badge badge-gray">{{ $categories->get('service_type', collect())->count() }}</span>
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
            <span class="badge badge-gray">{{ $stats['total_departments'] }}</span>
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
            <span class="badge badge-gray">Soon</span>
          </a>
        </li>
        <li>
          <a href="#" onclick="alert('Coming Soon!')">
            <span>🔔 Notifications</span>
            <span class="badge badge-gray">Soon</span>
          </a>
        </li>
        <li>
          <a href="#" onclick="alert('Coming Soon!')">
            <span>💾 Backup Settings</span>
            <span class="badge badge-gray">Soon</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
@endsection
