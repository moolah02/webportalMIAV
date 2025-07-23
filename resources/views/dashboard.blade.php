{{--
==============================================
ENHANCED DASHBOARD VIEW
File: resources/views/dashboard.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Welcome Header -->
    <div style="margin-bottom: 30px;">
        <h2 style="margin: 0; color: #333;">Welcome back, {{ auth()->user()->first_name }}! 👋</h2>
        <p style="color: #666; margin: 5px 0 0 0;">Here's what's happening with your POS terminal network today</p>
    </div>

    <!-- Main Statistics Grid -->
    <div class="dashboard-grid">
        <!-- Total Terminals -->
        <div class="metric-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="metric-icon">🖥️</div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['total_terminals'] }}</div>
                <div class="metric-label">Total Terminals</div>
                <div class="metric-change">
                    <span style="color: #a8d8a8;">↗ +{{ $stats['new_terminals_this_month'] }} this month</span>
                </div>
            </div>
        </div>

        <!-- Active Terminals -->
        <div class="metric-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <div class="metric-icon">✅</div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['active_terminals'] }}</div>
                <div class="metric-label">Active Terminals</div>
                <div class="metric-change">
                    <span style="color: #a8d8a8;">{{ number_format(($stats['active_terminals'] / max($stats['total_terminals'], 1)) * 100, 1) }}% uptime</span>
                </div>
            </div>
        </div>

        <!-- Need Attention -->
        <div class="metric-card alert" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
            <div class="metric-icon">⚠️</div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['need_attention'] }}</div>
                <div class="metric-label">Need Attention</div>
                <div class="metric-change">
                    <span style="color: #fff3cd;">{{ $stats['urgent_issues'] }} urgent</span>
                </div>
            </div>
        </div>

        <!-- Total Clients -->
        <div class="metric-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
            <div class="metric-icon">🏢</div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['total_clients'] }}</div>
                <div class="metric-label">Active Clients</div>
                <div class="metric-change">
                    <span style="color: #666;">{{ $stats['new_clients_this_month'] }} new this month</span>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 30px;">
        <!-- Main Content Area -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Terminal Status Chart -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h4 style="margin: 0; color: #333;">📊 Terminal Status Overview</h4>
                    <select id="chartTimeframe" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 3 months</option>
                    </select>
                </div>
                
                <!-- Chart Container -->
                <div style="height: 300px; position: relative;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Regional Distribution -->
            <div class="content-card">
                <h4 style="margin-bottom: 20px; color: #333;">🗺️ Regional Distribution</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    @foreach($stats['regional_data'] as $region => $data)
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #2196f3;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h5 style="margin: 0; color: #333;">{{ $region }}</h5>
                            <span style="background: #2196f3; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                {{ $data['total'] }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #666;">
                            <span>Active: {{ $data['active'] }}</span>
                            <span>Issues: {{ $data['issues'] }}</span>
                        </div>
                        <!-- Progress bar -->
                        <div style="background: #e0e0e0; height: 4px; border-radius: 2px; margin-top: 8px; overflow: hidden;">
                            <div style="background: #4caf50; height: 100%; width: {{ $data['total'] > 0 ? ($data['active'] / $data['total']) * 100 : 0 }}%; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activity Feed -->
            <div class="content-card">
                <h4 style="margin-bottom: 20px; color: #333;">📋 Recent Activity</h4>
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($stats['recent_activity'] as $activity)
                    <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                        <div style="background: {{ $activity['color'] }}; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                            {{ $activity['icon'] }}
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; color: #333; margin-bottom: 2px;">{{ $activity['title'] }}</div>
                            <div style="font-size: 14px; color: #666; margin-bottom: 4px;">{{ $activity['description'] }}</div>
                            <div style="font-size: 12px; color: #999;">{{ $activity['time'] }}</div>
                        </div>
                        @if(isset($activity['action']))
                        <a href="{{ $activity['action']['url'] }}" class="btn-small">{{ $activity['action']['label'] }}</a>
                        @endif
                    </div>
                    @empty
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <div style="font-size: 48px; margin-bottom: 15px;">📝</div>
                        <p>No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Quick Actions -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">⚡ Quick Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('pos-terminals.create') }}" class="quick-action-btn">
                        <span>🖥️</span>
                        <span>Add New Terminal</span>
                    </a>
                    <a href="{{ route('clients.create') }}" class="quick-action-btn">
                        <span>🏢</span>
                        <span>Add New Client</span>
                    </a>
                    <a href="{{ route('pos-terminals.index') }}?status=faulty" class="quick-action-btn">
                        <span>🔧</span>
                        <span>View Issues</span>
                    </a>
                    <a href="#" class="quick-action-btn">
                        <span>📊</span>
                        <span>Generate Report</span>
                    </a>
                </div>
            </div>

            <!-- System Health -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">💚 System Health</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Network Uptime</span>
                        <span style="font-weight: 600; color: #4caf50;">{{ $stats['network_uptime'] }}%</span>
                    </div>
                    <div style="background: #e0e0e0; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: #4caf50; height: 100%; width: {{ $stats['network_uptime'] }}%; transition: width 0.3s ease;"></div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Service Level</span>
                        <span style="font-weight: 600; color: #2196f3;">{{ $stats['service_level'] }}%</span>
                    </div>
                    <div style="background: #e0e0e0; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: #2196f3; height: 100%; width: {{ $stats['service_level'] }}%; transition: width 0.3s ease;"></div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Response Time</span>
                        <span style="font-weight: 600; color: #ff9800;">{{ $stats['avg_response_time'] }}h</span>
                    </div>
                </div>
            </div>

            <!-- Top Clients -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">🏆 Top Clients</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($stats['top_clients'] as $client)
                    <div style="display: flex; align-items: center; gap: 10px; padding: 8px; background: #f8f9fa; border-radius: 6px;">
                        <div style="background: #2196f3; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">
                            {{ $loop->iteration }}
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; font-size: 14px;">{{ $client['name'] }}</div>
                            <div style="font-size: 12px; color: #666;">{{ $client['terminals'] }} terminals</div>
                        </div>
                        <a href="{{ route('clients.show', $client['id']) }}" class="btn-small">View</a>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Alerts & Notifications -->
            @if($stats['alerts']->count() > 0)
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">🔔 Alerts</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach($stats['alerts'] as $alert)
                    <div style="background: {{ $alert['type'] === 'critical' ? '#ffebee' : '#fff3e0' }}; border: 1px solid {{ $alert['type'] === 'critical' ? '#f44336' : '#ff9800' }}; border-radius: 6px; padding: 10px;">
                        <div style="font-weight: 500; font-size: 12px; color: {{ $alert['type'] === 'critical' ? '#f44336' : '#f57c00' }};">
                            {{ strtoupper($alert['type']) }}
                        </div>
                        <div style="font-size: 14px; color: #333; margin-top: 2px;">{{ $alert['message'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease;
}

.quick-action-btn:hover {
    border-color: #2196f3;
    background: #f5f9ff;
    color: #2196f3;
    transform: translateY(-1px);
}

.metric-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
}

.metric-icon {
    font-size: 32px;
    opacity: 0.9;
}

.metric-content {
    flex: 1;
}

.metric-number {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 5px;
}

.metric-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 5px;
}

.metric-change {
    font-size: 12px;
    opacity: 0.8;
}
</style>

<script>
// Terminal Status Chart
const ctx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Offline', 'Maintenance', 'Faulty'],
        datasets: [{
            data: [
                {{ $stats['active_terminals'] }},
                {{ $stats['offline_terminals'] }},
                {{ $stats['maintenance_terminals'] }},
                {{ $stats['faulty_terminals'] }}
            ],
            backgroundColor: [
                '#4caf50',
                '#ff9800',
                '#2196f3',
                '#f44336'
            ],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        }
    }
});

// Auto-refresh data every 30 seconds
setInterval(() => {
    // You can add AJAX call here to refresh dashboard data
    console.log('Dashboard data refresh...');
}, 30000);
</script>
@endsection