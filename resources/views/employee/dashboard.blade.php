{{-- resources/views/employee/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="container-fluid py-3">

  {{-- Welcome Header --}}
  <div style="margin-block-end: 24px;">
    <h2 style="margin: 0; color: #333; font-weight: 600;">Welcome back, {{ auth()->user()->first_name ?? auth()->user()->name ?? 'Employee' }}! 👋</h2>
    <p style="color: #666; margin: 6px 0 0 0; font-size: 15px;">Here's what's happening with your job assignments and requests today</p>
  </div>

  {{-- Quick Action Alerts --}}
  @if(($stats['jobs']['today'] ?? 0) > 0 || ($stats['pending_approvals'] ?? 0) > 0)
  <div style="margin-block-end: 24px;">
    @if(($stats['jobs']['today'] ?? 0) > 0)
    <a href="{{ route('jobs.mine') ?? '#' }}" class="alert-link-compact alert-info" style="margin-block-end: 8px; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px; text-decoration: none; transition: all 0.2s ease; font-size: 14px;">
      <span style="font-size: 16px;">📋</span>
      <span style="flex: 1;">You have {{ $stats['jobs']['today'] ?? 0 }} job assignments for today</span>
      <span style="font-size: 12px; opacity: 0.7;">→</span>
    </a>
    @endif

    @if(($stats['pending_approvals'] ?? 0) > 0)
    <a href="{{ route('asset-requests.index') ?? '#' }}" class="alert-link-compact alert-warning" style="margin-block-end: 8px; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px; text-decoration: none; transition: all 0.2s ease; font-size: 14px;">
      <span style="font-size: 16px;">⏰</span>
      <span style="flex: 1;">{{ $stats['pending_approvals'] ?? 0 }} of your asset requests are pending approval</span>
      <span style="font-size: 12px; opacity: 0.7;">→</span>
    </a>
    @endif
  </div>
  @endif

  {{-- Main Statistics Grid --}}
  <div class="dashboard-grid">
    {{-- Jobs Today --}}
    <div class="metric-card-subtle">
      <div class="metric-icon-subtle">📅</div>
      <div class="metric-content">
        <div class="metric-number-subtle">{{ $stats['jobs']['today'] ?? 0 }}</div>
        <div class="metric-label-subtle">Jobs Today</div>
        <div class="metric-change-subtle">
          <span style="color: #2196f3;">Ready to start</span>
        </div>
      </div>
    </div>

    {{-- Assigned Jobs --}}
    @if(Route::has('jobs.mine'))
    <a href="{{ route('jobs.mine') }}" class="metric-card-subtle clickable-card" style="text-decoration: none;">
    @else
    <div class="metric-card-subtle">
    @endif
      <div class="metric-icon-subtle">📋</div>
      <div class="metric-content">
        <div class="metric-number-subtle">{{ $stats['jobs']['assigned'] ?? 0 }}</div>
        <div class="metric-label-subtle">Assigned Jobs</div>
        <div class="metric-change-subtle">
          <span style="color: #666;">Total assignments</span>
        </div>
      </div>
    @if(Route::has('jobs.mine'))
    </a>
    @else
    </div>
    @endif

    {{-- In Progress --}}
    <div class="metric-card-subtle metric-card-progress">
      <div class="metric-icon-subtle">⚡</div>
      <div class="metric-content">
        <div class="metric-number-subtle">{{ $stats['jobs']['in_progress'] ?? 0 }}</div>
        <div class="metric-label-subtle">In Progress</div>
        <div class="metric-change-subtle">
          <span style="color: #ff9800;">Active work</span>
        </div>
      </div>
    </div>

    {{-- Completed --}}
    <div class="metric-card-subtle">
      <div class="metric-icon-subtle">✅</div>
      <div class="metric-content">
        <div class="metric-number-subtle">{{ $stats['jobs']['completed'] ?? 0 }}</div>
        <div class="metric-label-subtle">Completed</div>
        <div class="metric-change-subtle">
          <span style="color: #4caf50;">Job well done!</span>
        </div>
      </div>
    </div>

    {{-- Asset Requests --}}
    @if(Route::has('asset-requests.index'))
    <a href="{{ route('asset-requests.index') }}" class="metric-card-subtle clickable-card" style="text-decoration: none;">
    @else
    <div class="metric-card-subtle">
    @endif
      <div class="metric-icon-subtle">📦</div>
      <div class="metric-content">
        <div class="metric-number-subtle">{{ $stats['my_requests'] ?? 0 }}</div>
        <div class="metric-label-subtle">My Requests</div>
        <div class="metric-change-subtle">
          <span style="color: #666;">Asset requests</span>
        </div>
      </div>
    @if(Route::has('asset-requests.index'))
    </a>
    @else
    </div>
    @endif

    {{-- Pending Approvals --}}
    <div class="metric-card-subtle {{ ($stats['pending_approvals'] ?? 0) > 0 ? 'metric-card-alert' : '' }}">
      <div class="metric-icon-subtle">⏳</div>
      <div class="metric-content">
        <div class="metric-number-subtle">{{ $stats['pending_approvals'] ?? 0 }}</div>
        <div class="metric-label-subtle">Pending Approvals</div>
        <div class="metric-change-subtle">
          <span style="color: {{ ($stats['pending_approvals'] ?? 0) > 0 ? '#ff9800' : '#666' }};">
            {{ ($stats['pending_approvals'] ?? 0) > 0 ? 'Awaiting approval' : 'All caught up' }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-block-start: 32px;">
    {{-- Main Content Area --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">
      
      {{-- Upcoming Assignments --}}
      @if(isset($upcomingAssignments) && $upcomingAssignments->isNotEmpty())
      <div class="content-card">
        <div style="display: flex; justify-content: between; align-items: center; margin-block-end: 20px;">
          <h4 style="margin: 0; color: #333; font-weight: 600;">📋 Upcoming Assignments</h4>
          @if(Route::has('jobs.mine'))
          <a href="{{ route('jobs.mine') }}" class="btn-small">View All Jobs</a>
          @endif
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
          @foreach($upcomingAssignments->take(5) as $assignment)
          <div style="display: flex; align-items: center; gap: 15px; padding: 16px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #2196f3;">
            <div style="background: #2196f3; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
              📅
            </div>
            <div style="flex: 1;">
              <div style="font-weight: 600; font-size: 15px; margin-block-end: 4px; color: #333;">
                {{ $assignment->list_title ?: $assignment->assignment_id }}
              </div>
              <div style="font-size: 13px; color: #666; display: flex; gap: 12px; flex-wrap: wrap;">
                <span>📅 {{ optional($assignment->scheduled_date)->format('M d, Y') ?: 'Date TBD' }}</span>
                <span>🔧 {{ \Illuminate\Support\Str::headline($assignment->service_type ?? 'General') }}</span>
                <span>🖥️ {{ $assignment->terminal_count ?? 0 }} terminals</span>
              </div>
            </div>
            @if(Route::has('jobs.show'))
            <a href="{{ route('jobs.show', $assignment->id) }}" class="btn-small" style="background: #2196f3; color: white; border-color: #2196f3;">Open</a>
            @endif
          </div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Recent Activity --}}
      <div class="content-card">
        <h4 style="margin-block-end: 20px; color: #333; font-weight: 600;">🕒 Recent Activity</h4>
        <div style="max-height: 400px; overflow-y: auto;">
          @php
            $recent = $stats['recent_activity'] ?? [];
          @endphp

          @if(is_iterable($recent) && count($recent))
            @foreach($recent as $item)
            <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-block-end: 1px solid #f0f0f0;">
              <div style="background: #2196f3; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                📋
              </div>
              <div style="flex: 1;">
                @if(is_array($item))
                <div style="font-weight: 500; color: #333; margin-block-end: 2px;">{{ $item['label'] ?? $item['title'] ?? 'Activity Update' }}</div>
                <div style="font-size: 14px; color: #666; margin-block-end: 4px;">{{ $item['details'] ?? 'No details available' }}</div>
                <div style="font-size: 12px; color: #999;">{{ $item['date'] ?? 'Recently' }}</div>
                @else
                <div style="font-weight: 500; color: #333; margin-block-end: 2px;">Activity Update</div>
                <div style="font-size: 14px; color: #666;">{{ (string)$item }}</div>
                @endif
              </div>
            </div>
            @endforeach
          @else
          <div style="text-align: center; padding: 60px 20px; color: #666;">
            <div style="font-size: 48px; margin-block-end: 16px;">📝</div>
            <h5 style="margin-block-end: 8px; color: #333;">No recent activity</h5>
            <p style="margin: 0;">Your activity will appear here once you start working on jobs</p>
          </div>
          @endif
        </div>
      </div>

      {{-- Performance Overview (if available) --}}
      @if(isset($stats['performance']))
      <div class="content-card">
        <h4 style="margin-block-end: 20px; color: #333; font-weight: 600;">📊 My Performance</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
          <div style="text-align: center; padding: 16px; background: #f8f9fa; border-radius: 8px;">
            <div style="font-size: 24px; font-weight: bold; color: #4caf50;">{{ $stats['performance']['completion_rate'] ?? '0' }}%</div>
            <div style="font-size: 13px; color: #666; margin-top: 4px;">Completion Rate</div>
          </div>
          <div style="text-align: center; padding: 16px; background: #f8f9fa; border-radius: 8px;">
            <div style="font-size: 24px; font-weight: bold; color: #2196f3;">{{ $stats['performance']['avg_response_time'] ?? 'N/A' }}</div>
            <div style="font-size: 13px; color: #666; margin-top: 4px;">Avg Response Time</div>
          </div>
          <div style="text-align: center; padding: 16px; background: #f8f9fa; border-radius: 8px;">
            <div style="font-size: 24px; font-weight: bold; color: #ff9800;">{{ $stats['performance']['quality_score'] ?? 'N/A' }}</div>
            <div style="font-size: 13px; color: #666; margin-top: 4px;">Quality Score</div>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <div style="display: flex; flex-direction: column; gap: 20px;">
      
      {{-- Quick Actions --}}
      <div class="content-card">
        <h4 style="margin-block-end: 16px; color: #333; font-weight: 600;">⚡ Quick Actions</h4>
        <div style="display: flex; flex-direction: column; gap: 8px;">
          @if(Route::has('jobs.mine'))
          <a href="{{ route('jobs.mine') }}" class="quick-action-btn">
            <span>📋</span>
            <span>View My Jobs</span>
          </a>
          @endif
          @if(Route::has('asset-requests.index'))
          <a href="{{ route('asset-requests.index') }}" class="quick-action-btn">
            <span>📦</span>
            <span>My Asset Requests</span>
          </a>
          @endif
          @if(Route::has('asset-requests.create'))
          <a href="{{ route('asset-requests.create') }}" class="quick-action-btn">
            <span>➕</span>
            <span>New Asset Request</span>
          </a>
          @endif
          <a href="#" class="quick-action-btn">
            <span>👤</span>
            <span>Update Profile</span>
          </a>
          <a href="#" class="quick-action-btn">
            <span>📞</span>
            <span>Contact Support</span>
          </a>
        </div>
      </div>

      {{-- Job Status Summary --}}
      <div class="content-card">
        <h4 style="margin-block-end: 16px; color: #333; font-weight: 600;">📊 Job Summary</h4>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #666;">Total Assigned</span>
            <span style="font-weight: 600; color: #333;">{{ $stats['jobs']['assigned'] ?? 0 }}</span>
          </div>
          
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #666;">In Progress</span>
            <span style="font-weight: 600; color: #ff9800;">{{ $stats['jobs']['in_progress'] ?? 0 }}</span>
          </div>
          
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #666;">Completed</span>
            <span style="font-weight: 600; color: #4caf50;">{{ $stats['jobs']['completed'] ?? 0 }}</span>
          </div>
          
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #666;">Due Today</span>
            <span style="font-weight: 600; color: #2196f3;">{{ $stats['jobs']['today'] ?? 0 }}</span>
          </div>
        </div>
        
        {{-- Progress Bar --}}
        @if(($stats['jobs']['assigned'] ?? 0) > 0)
        <div style="margin-top: 16px;">
          <div style="display: flex; justify-content: between; align-items: center; margin-block-end: 6px;">
            <span style="font-size: 12px; color: #666;">Overall Progress</span>
            <span style="font-size: 12px; color: #666;">
              {{ round((($stats['jobs']['completed'] ?? 0) / ($stats['jobs']['assigned'] ?? 1)) * 100) }}%
            </span>
          </div>
          <div style="background: #e0e0e0; height: 6px; border-radius: 3px; overflow: hidden;">
            <div style="background: #4caf50; height: 100%; width: {{ round((($stats['jobs']['completed'] ?? 0) / ($stats['jobs']['assigned'] ?? 1)) * 100) }}%; transition: width 0.3s ease;"></div>
          </div>
        </div>
        @endif
      </div>

      {{-- Request Status --}}
      <div class="content-card">
        <h4 style="margin-block-end: 16px; color: #333; font-weight: 600;">📦 Request Status</h4>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #666;">Total Requests</span>
            <span style="font-weight: 600; color: #333;">{{ $stats['my_requests'] ?? 0 }}</span>
          </div>
          
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #666;">Pending Approval</span>
            <span style="font-weight: 600; color: #ff9800;">{{ $stats['pending_approvals'] ?? 0 }}</span>
          </div>
          
          @if(isset($stats['approved_requests']))
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #666;">Approved</span>
            <span style="font-weight: 600; color: #4caf50;">{{ $stats['approved_requests'] ?? 0 }}</span>
          </div>
          @endif
        </div>
      </div>

      {{-- Helpful Tips --}}
      <div class="content-card">
        <h4 style="margin-block-end: 16px; color: #333; font-weight: 600;">💡 Tips & Reminders</h4>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="padding: 12px; background: #e3f2fd; border-radius: 6px; border-left: 3px solid #2196f3;">
            <div style="font-size: 13px; color: #1976d2; font-weight: 500;">Pro Tip</div>
            <div style="font-size: 12px; color: #1565c0; margin-top: 2px;">Update job status promptly to keep your completion rate high</div>
          </div>
          
          <div style="padding: 12px; background: #f3e5f5; border-radius: 6px; border-left: 3px solid #9c27b0;">
            <div style="font-size: 13px; color: #7b1fa2; font-weight: 500;">Remember</div>
            <div style="font-size: 12px; color: #6a1b9a; margin-top: 2px;">Check equipment before starting each job</div>
          </div>
          
          @if(($stats['pending_approvals'] ?? 0) > 0)
          <div style="padding: 12px; background: #fff3e0; border-radius: 6px; border-left: 3px solid #ff9800;">
            <div style="font-size: 13px; color: #f57c00; font-weight: 500;">Action Needed</div>
            <div style="font-size: 12px; color: #ef6c00; margin-top: 2px;">Follow up on pending asset requests</div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>

<style>
/* Alert Styles */
.alert-link-compact {
  border: 1px solid;
  text-decoration: none !important;
}

.alert-link-compact:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.alert-info {
  background: #e3f2fd;
  border-color: #2196f3;
  color: #1976d2;
}

.alert-info:hover {
  background: #bbdefb;
  color: #1565c0;
}

.alert-warning {
  background: #fff3e0;
  border-color: #ff9800;
  color: #f57c00;
}

.alert-warning:hover {
  background: #ffe0b2;
  color: #ef6c00;
}

/* Card Styles */
.metric-card-subtle {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 24px;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  transition: all 0.2s ease;
  color: inherit;
}

.metric-card-subtle:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border-color: #d0d0d0;
  text-decoration: none;
  color: inherit;
}

.metric-card-alert {
  border-left: 4px solid #ff9800;
}

.metric-card-progress {
  border-left: 4px solid #ff9800;
}

.metric-icon-subtle {
  font-size: 28px;
  opacity: 0.8;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  border-radius: 50%;
}

.metric-number-subtle {
  font-size: 32px;
  font-weight: bold;
  margin-block-end: 4px;
  color: #333;
}

.metric-label-subtle {
  font-size: 14px;
  color: #666;
  margin-block-end: 4px;
  font-weight: 500;
}

.metric-change-subtle {
  font-size: 12px;
  color: #888;
}

.clickable-card {
  cursor: pointer;
  transition: all 0.3s ease;
}

.quick-action-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px;
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  text-decoration: none;
  color: #333;
  transition: all 0.2s ease;
  font-size: 14px;
}

.quick-action-btn:hover {
  border-color: #2196f3;
  background: #f5f9ff;
  color: #2196f3;
  transform: translateY(-1px);
}

.content-card {
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.btn-small {
  padding: 6px 12px;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  font-size: 12px;
  text-decoration: none;
  color: #333;
  transition: all 0.2s ease;
  font-weight: 500;
}

.btn-small:hover {
  background: #f8f9fa;
  border-color: #2196f3;
  color: #2196f3;
  text-decoration: none;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-block-end: 32px;
}

/* Responsive */
@media (max-width: 1200px) {
  div[style*="grid-template-columns: 2fr 1fr"] {
    grid-template-columns: 1fr !important;
  }
}

@media (max-width: 768px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .metric-card-subtle {
    padding: 20px;
  }
  
  .metric-number-subtle {
    font-size: 28px;
  }
  
  .content-card {
    padding: 20px;
  }
}

@media (max-width: 480px) {
  .metric-card-subtle {
    flex-direction: column;
    text-align: center;
    gap: 12px;
  }
  
  .metric-icon-subtle {
    width: 40px;
    height: 40px;
    font-size: 24px;
  }
}
</style>
@endsection