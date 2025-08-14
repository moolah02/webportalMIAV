@extends('layouts.app')

@section('content')
<div>
    <!-- Welcome Header -->
    <div style="margin-block-end: 20px;">
        <h2 style="margin: 0; color: #333;">Welcome back, {{ auth()->user()->first_name }}! 👋</h2>
        <p style="color: #666; margin: 5px 0 0 0;">Here's what's happening with your POS terminal network and business licenses today</p>
    </div>

    <!-- System Alerts (Compact Design) -->
    @if($stats['alerts']->count() > 0)
    <div style="margin-block-end: 20px;">
        @foreach($stats['alerts'] as $alert)
        @php
            $alertUrl = match(true) {
                str_contains($alert['message'], 'licenses expiring') => route('business-licenses.expiring'),
                str_contains($alert['message'], 'licenses expired') => route('business-licenses.index', ['status' => 'expired']),
                str_contains($alert['message'], 'critical licenses') => route('business-licenses.index', ['priority' => 'critical']),
                str_contains($alert['message'], 'license renewals') => route('business-licenses.compliance'),
                str_contains($alert['message'], 'faulty') => route('pos-terminals.index', ['status' => 'faulty']),
                str_contains($alert['message'], 'offline') => route('pos-terminals.index', ['status' => 'offline']),
                str_contains($alert['message'], 'contracts expiring') => route('clients.index', ['expiring' => true]),
                str_contains($alert['message'], 'asset requests') => route('asset-approvals.index'),
                str_contains($alert['message'], 'job assignments') => route('jobs.assignment'),
                default => '#'
            };
        @endphp
        <a href="{{ $alertUrl }}" class="alert-link-compact alert-{{ $alert['type'] }}" style="margin-block-end: 6px; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s ease; font-size: 14px;">
            <span style="font-size: 14px;">{{ $alert['icon'] }}</span>
            <span style="flex: 1;">{{ $alert['message'] }}</span>
            <span style="font-size: 12px; opacity: 0.7;">→</span>
        </a>
        @endforeach
    </div>
    @endif

    <!-- Main Statistics Grid (Subtle Design) -->
    <div class="dashboard-grid">
        <!-- Total Terminals -->
        <a href="{{ route('pos-terminals.index') }}" class="metric-card-subtle clickable-card" style="text-decoration: none;">
            <div class="metric-icon-subtle">🖥️</div>
            <div class="metric-content">
                <div class="metric-number-subtle">{{ number_format($stats['total_terminals']) }}</div>
                <div class="metric-label-subtle">Total Terminals</div>
                <div class="metric-change-subtle">
                    <span style="color: #4caf50;">↗ +{{ $stats['new_terminals_this_month'] }} this month</span>
                </div>
            </div>
        </a>

        <!-- Active Terminals -->
        <a href="{{ route('pos-terminals.index', ['status' => 'active']) }}" class="metric-card-subtle clickable-card" style="text-decoration: none;">
            <div class="metric-icon-subtle">✅</div>
            <div class="metric-content">
                <div class="metric-number-subtle">{{ number_format($stats['active_terminals']) }}</div>
                <div class="metric-label-subtle">Active Terminals</div>
                <div class="metric-change-subtle">
                    <span style="color: #4caf50;">{{ $stats['network_uptime'] }}% uptime</span>
                </div>
            </div>
        </a>

        <!-- Business Licenses -->
        <a href="{{ route('business-licenses.index') }}" class="metric-card-subtle clickable-card" style="text-decoration: none;">
            <div class="metric-icon-subtle">📋</div>
            <div class="metric-content">
                <div class="metric-number-subtle">{{ number_format($stats['license_stats']['total_licenses']) }}</div>
                <div class="metric-label-subtle">Business Licenses</div>
                <div class="metric-change-subtle">
                    <span style="color: #4caf50;">{{ $stats['license_stats']['active_licenses'] }} active</span>
                </div>
            </div>
        </a>

        <!-- Need Attention -->
        <a href="{{ route('pos-terminals.index') }}?status=faulty&status=offline&status=maintenance" class="metric-card-subtle metric-card-alert clickable-card" style="text-decoration: none;">
            <div class="metric-icon-subtle">⚠️</div>
            <div class="metric-content">
                <div class="metric-number-subtle">{{ number_format($stats['need_attention']) }}</div>
                <div class="metric-label-subtle">Need Attention</div>
                <div class="metric-change-subtle">
                    <span style="color: #f44336;">{{ $stats['urgent_issues'] }} urgent</span>
                </div>
            </div>
        </a>

        <!-- Total Clients -->
        <a href="{{ route('clients.index') }}" class="metric-card-subtle clickable-card" style="text-decoration: none;">
            <div class="metric-icon-subtle">🏢</div>
            <div class="metric-content">
                <div class="metric-number-subtle">{{ number_format($stats['total_clients']) }}</div>
                <div class="metric-label-subtle">Active Clients</div>
                <div class="metric-change-subtle">
                    <span style="color: #666;">{{ $stats['new_clients_this_month'] }} new this month</span>
                </div>
            </div>
        </a>

        <!-- License Compliance -->
        <a href="{{ route('business-licenses.compliance') }}" class="metric-card-subtle clickable-card" style="text-decoration: none;">
            <div class="metric-icon-subtle">✅</div>
            <div class="metric-content">
                <div class="metric-number-subtle">{{ number_format($stats['license_stats']['compliance_rate']) }}%</div>
                <div class="metric-label-subtle">License Compliance</div>
                <div class="metric-change-subtle">
                    <span style="color: #666;">{{ $stats['license_stats']['expiring_soon'] }} expiring soon</span>
                </div>
            </div>
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-block-start: 30px;">
        <!-- Main Content Area -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- License Compliance Alert (Compact) -->
            @if($stats['license_stats']['expiring_soon'] > 0 || $stats['license_stats']['expired'] > 0)
            <div class="content-card" style="background: #fff8f0; border-left: 4px solid #ff9800;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 12px;">
                    <h4 style="margin: 0; color: #f57c00; font-size: 16px;">📋 License Compliance Alert</h4>
                    <a href="{{ route('business-licenses.compliance') }}" class="btn-small" style="background: #ff9800; color: white; border-color: #ff9800;">View All</a>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                    @if($stats['license_stats']['expired'] > 0)
                    <a href="{{ route('business-licenses.index', ['status' => 'expired']) }}" class="license-alert-card-compact" style="background: #fef7f7; border: 1px solid #f44336; padding: 12px; border-radius: 6px; text-decoration: none; color: inherit;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-block-end: 6px;">
                            <span style="font-size: 16px;">⚠️</span>
                            <span style="font-weight: 600; color: #f44336; font-size: 14px;">{{ $stats['license_stats']['expired'] }} Expired</span>
                        </div>
                        <div style="font-size: 11px; color: #666;">Immediate action required</div>
                    </a>
                    @endif

                    @if($stats['license_stats']['expiring_soon'] > 0)
                    <a href="{{ route('business-licenses.expiring') }}" class="license-alert-card-compact" style="background: #fff9f0; border: 1px solid #ff9800; padding: 12px; border-radius: 6px; text-decoration: none; color: inherit;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-block-end: 6px;">
                            <span style="font-size: 16px;">⏰</span>
                            <span style="font-weight: 600; color: #f57c00; font-size: 14px;">{{ $stats['license_stats']['expiring_soon'] }} Expiring Soon</span>
                        </div>
                        <div style="font-size: 11px; color: #666;">Within next 30 days</div>
                    </a>
                    @endif

                    @if($stats['license_stats']['critical_expired'] > 0)
                    <a href="{{ route('business-licenses.index', ['priority' => 'critical', 'status' => 'expired']) }}" class="license-alert-card-compact" style="background: #fef7f7; border: 1px solid #f44336; padding: 12px; border-radius: 6px; text-decoration: none; color: inherit;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-block-end: 6px;">
                            <span style="font-size: 16px;">🚨</span>
                            <span style="font-weight: 600; color: #f44336; font-size: 14px;">{{ $stats['license_stats']['critical_expired'] }} Critical Expired</span>
                        </div>
                        <div style="font-size: 11px; color: #666;">High business impact</div>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Terminal Status Chart -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 20px;">
                    <h4 style="margin: 0; color: #333;">📊 Terminal Status Distribution</h4>
                    <div style="display: flex; gap: 15px; font-size: 12px;">
                        <span><span style="color: #4caf50;">●</span> Active: {{ $stats['active_terminals'] }}</span>
                        <span><span style="color: #ff9800;">●</span> Offline: {{ $stats['offline_terminals'] }}</span>
                        <span><span style="color: #2196f3;">●</span> Maintenance: {{ $stats['maintenance_terminals'] }}</span>
                        <span><span style="color: #f44336;">●</span> Faulty: {{ $stats['faulty_terminals'] }}</span>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div style="height: 300px; position: relative;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- License Status Chart -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 20px;">
                    <h4 style="margin: 0; color: #333;">📋 License Status Overview</h4>
                    <a href="{{ route('business-licenses.index') }}" class="btn-small">View All Licenses</a>
                </div>
                
                <div style="height: 250px; position: relative;">
                    <canvas id="licenseChart"></canvas>
                </div>
            </div>

            <!-- Monthly Trends Chart -->
            <div class="content-card">
                <h4 style="margin-block-end: 20px; color: #333;">📈 Monthly Growth Trends</h4>
                <div style="height: 250px; position: relative;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>

            <!-- Regional Distribution -->
            <div class="content-card">
                <h4 style="margin-block-end: 20px; color: #333;">🗺️ Regional Distribution</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    @foreach($stats['regional_data']->take(6) as $region => $data)
                    <a href="{{ route('pos-terminals.index', ['region' => $region]) }}" class="regional-card" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #2196f3; text-decoration: none; color: inherit; transition: all 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 10px;">
                            <h5 style="margin: 0; color: #333; font-size: 14px;">{{ $region }}</h5>
                            <span style="background: #2196f3; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                {{ $data['total'] }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #666; margin-block-end: 8px;">
                            <span>Active: {{ $data['active'] }}</span>
                            <span>Issues: {{ $data['issues'] }}</span>
                        </div>
                        <!-- Progress bar -->
                        <div style="background: #e0e0e0; height: 4px; border-radius: 2px; overflow: hidden;">
                            <div style="background: #4caf50; height: 100%; width: {{ $data['uptime_percentage'] }}%; transition: width 0.3s ease;"></div>
                        </div>
                        <div style="text-align: center; margin-top: 4px; font-size: 11px; color: #666;">
                            {{ $data['uptime_percentage'] }}% uptime
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activity Feed -->
            <div class="content-card">
                <h4 style="margin-block-end: 20px; color: #333;">📋 Recent Activity</h4>
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($stats['recent_activity'] as $activity)
                    <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-block-end: 1px solid #f0f0f0;">
                        <div style="background: {{ $activity['color'] }}; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                            {{ $activity['icon'] }}
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; color: #333; margin-block-end: 2px;">{{ $activity['title'] }}</div>
                            <div style="font-size: 14px; color: #666; margin-block-end: 4px;">{{ $activity['description'] }}</div>
                            <div style="font-size: 12px; color: #999;">{{ $activity['time'] }}</div>
                        </div>
                        @if(isset($activity['action']))
                        <a href="{{ $activity['action']['url'] }}" class="btn-small">{{ $activity['action']['label'] }}</a>
                        @endif
                    </div>
                    @empty
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <div style="font-size: 48px; margin-block-end: 15px;">📝</div>
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
                <h4 style="margin-block-end: 15px; color: #333;">⚡ Quick Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('pos-terminals.create') }}" class="quick-action-btn">
                        <span>🖥️</span>
                        <span>Add New Terminal</span>
                    </a>
                    <a href="{{ route('business-licenses.create') }}" class="quick-action-btn">
                        <span>📋</span>
                        <span>Add Business License</span>
                    </a>
                    <a href="{{ route('clients.create') }}" class="quick-action-btn">
                        <span>🏢</span>
                        <span>Add New Client</span>
                    </a>
                    <a href="{{ route('pos-terminals.index') }}?status=faulty" class="quick-action-btn">
                        <span>🔧</span>
                        <span>View Faulty Terminals</span>
                    </a>
                    <a href="{{ route('business-licenses.expiring') }}" class="quick-action-btn">
                        <span>⏰</span>
                        <span>Expiring Licenses</span>
                    </a>
                    <a href="{{ route('pos-terminals.column-mapping') }}" class="quick-action-btn">
                        <span>⚙️</span>
                        <span>Column Mapping</span>
                    </a>
                </div>
            </div>

            <!-- License Status Summary -->
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">📋 License Summary</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Total Licenses</span>
                        <span style="font-weight: 600; color: #333;">{{ $stats['license_stats']['total_licenses'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Active</span>
                        <span style="font-weight: 600; color: #4caf50;">{{ $stats['license_stats']['active_licenses'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Expiring Soon</span>
                        <span style="font-weight: 600; color: #ff9800;">{{ $stats['license_stats']['expiring_soon'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Expired</span>
                        <span style="font-weight: 600; color: #f44336;">{{ $stats['license_stats']['expired'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Critical Priority</span>
                        <span style="font-weight: 600; color: #9c27b0;">{{ $stats['license_stats']['critical_licenses'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Annual Cost</span>
                        <span style="font-weight: 600; color: #2196f3;">${{ number_format($stats['license_stats']['annual_cost'], 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Upcoming License Renewals -->
            @if(isset($stats['upcoming_renewals']) && $stats['upcoming_renewals']->count() > 0)
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">🔄 Upcoming Renewals</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($stats['upcoming_renewals']->take(5) as $license)
                    <div style="display: flex; align-items: center; gap: 10px; padding: 8px; background: #f8f9fa; border-radius: 6px;">
                        <div style="background: {{ $license->is_expired ? '#f44336' : '#ff9800' }}; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                            {{ $license->is_expired ? '⚠️' : '⏰' }}
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; font-size: 14px;">{{ Str::limit($license->license_name, 20) }}</div>
                            <div style="font-size: 12px; color: #666;">
                                {{ $license->is_expired ? 'Expired' : 'Expires' }} {{ $license->expiry_date->format('M d') }}
                            </div>
                        </div>
                        <a href="{{ route('business-licenses.renew', $license) }}" class="btn-small" style="background: #ff9800; color: white; border-color: #ff9800;">Renew</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- System Health -->
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">💚 System Health</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Network Uptime</span>
                        <span style="font-weight: 600; color: #4caf50;">{{ $stats['network_uptime'] }}%</span>
                    </div>
                    <div style="background: #e0e0e0; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: #4caf50; height: 100%; width: {{ $stats['network_uptime'] }}%; transition: width 0.3s ease;"></div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">License Compliance</span>
                        <span style="font-weight: 600; color: #2196f3;">{{ $stats['license_stats']['compliance_rate'] }}%</span>
                    </div>
                    <div style="background: #e0e0e0; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: #2196f3; height: 100%; width: {{ $stats['license_stats']['compliance_rate'] }}%; transition: width 0.3s ease;"></div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Service Level</span>
                        <span style="font-weight: 600; color: #2196f3;">{{ $stats['service_level'] }}%</span>
                    </div>
                    <div style="background: #e0e0e0; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: #2196f3; height: 100%; width: {{ $stats['service_level'] }}%; transition: width 0.3s ease;"></div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Avg Response Time</span>
                        <span style="font-weight: 600; color: #ff9800;">{{ $stats['avg_response_time'] }}h</span>
                    </div>
                </div>
            </div>

            <!-- Top Clients -->
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">🏆 Top Clients</h4>
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
                        <span class="status-badge-mini status-{{ $client['status'] }}">{{ ucfirst($client['status']) }}</span>
                        <a href="{{ route('clients.show', $client['id']) }}" class="btn-small">View</a>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Contract Status -->
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">📄 Contract Status</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Active Contracts</span>
                        <span style="font-weight: 600; color: #4caf50;">{{ $stats['contract_stats']['active'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Expiring Soon</span>
                        <span style="font-weight: 600; color: #ff9800;">{{ $stats['contract_stats']['expiring_soon'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Expired</span>
                        <span style="font-weight: 600; color: #f44336;">{{ $stats['contract_stats']['expired'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Team Overview -->
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">👥 Team Overview</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Total Employees</span>
                        <span style="font-weight: 600; color: #333;">{{ $stats['employee_stats']['total'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Field Technicians</span>
                        <span style="font-weight: 600; color: #2196f3;">{{ $stats['employee_stats']['technicians'] }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #666;">Managers</span>
                        <span style="font-weight: 600; color: #9c27b0;">{{ $stats['employee_stats']['managers'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Compact Alert Styles */
.alert-link-compact {
    border: 1px solid;
    text-decoration: none !important;
}

.alert-link-compact:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.alert-critical {
    background: #fef7f7;
    border-color: #f44336;
    color: #c62828;
}

.alert-critical:hover {
    background: #ffebee;
    color: #b71c1c;
}

.alert-warning {
    background: #fff9f0;
    border-color: #ff9800;
    color: #f57c00;
}

.alert-warning:hover {
    background: #fff3e0;
    color: #ef6c00;
}

.alert-info {
    background: #f0f8ff;
    border-color: #2196f3;
    color: #1976d2;
}

.alert-info:hover {
    background: #e3f2fd;
    color: #1565c0;
}

/* Subtle Card Styles */
.metric-card-subtle {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
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
    border-left: 4px solid #f44336;
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

.license-alert-card-compact {
    transition: all 0.2s ease;
}

.license-alert-card-compact:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    text-decoration: none;
    color: inherit;
}

.regional-card:hover {
    background: #e3f2fd !important;
    border-left-color: #1976d2 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

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

.content-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.btn-small {
    padding: 4px 8px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 11px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease;
}

.btn-small:hover {
    background: #f8f9fa;
    border-color: #007bff;
    text-decoration: none;
}

.status-badge-mini {
    padding: 2px 6px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-block-end: 30px;
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
    
    div[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Terminal Status Chart
const ctx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Offline', 'Maintenance', 'Faulty', 'Decommissioned'],
        datasets: [{
            data: [
                {{ $stats['active_terminals'] }},
                {{ $stats['offline_terminals'] }},
                {{ $stats['maintenance_terminals'] }},
                {{ $stats['faulty_terminals'] }},
                {{ $stats['decommissioned_terminals'] ?? 0 }}
            ],
            backgroundColor: [
                '#4caf50',
                '#ff9800', 
                '#2196f3',
                '#f44336',
                '#9e9e9e'
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
                    usePointStyle: true,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// License Status Chart
const licenseCtx = document.getElementById('licenseChart').getContext('2d');
const licenseChart = new Chart(licenseCtx, {
    type: 'bar',
    data: {
        labels: ['Active', 'Expired', 'Expiring Soon', 'Suspended', 'Cancelled'],
        datasets: [{
            label: 'License Count',
            data: [
                {{ $stats['license_stats']['active_licenses'] }},
                {{ $stats['license_stats']['expired'] }},
                {{ $stats['license_stats']['expiring_soon'] }},
                {{ $stats['license_stats']['suspended'] ?? 0 }},
                {{ $stats['license_stats']['cancelled'] ?? 0 }}
            ],
            backgroundColor: [
                '#4caf50',
                '#f44336',
                '#ff9800',
                '#9e9e9e',
                '#607d8b'
            ],
            borderColor: [
                '#388e3c',
                '#d32f2f',
                '#f57c00',
                '#757575',
                '#455a64'
            ],
            borderWidth: 2,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Monthly Trends Chart
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
const trendsChart = new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($stats['monthly_trends']['months']) !!},
        datasets: [{
            label: 'New Terminals',
            data: {!! json_encode($stats['monthly_trends']['terminals']) !!},
            borderColor: '#2196f3',
            backgroundColor: 'rgba(33, 150, 243, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'New Clients',
            data: {!! json_encode($stats['monthly_trends']['clients']) !!},
            borderColor: '#4caf50',
            backgroundColor: 'rgba(76, 175, 80, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'New Licenses',
            data: {!! json_encode($stats['monthly_trends']['licenses']) !!},
            borderColor: '#ff9800',
            backgroundColor: 'rgba(255, 152, 0, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    font: {
                        size: 12
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// License expiration notifications
function showLicenseNotifications() {
    const expiredCount = {{ $stats['license_stats']['expired'] }};
    const expiringCount = {{ $stats['license_stats']['expiring_soon'] }};
    
    if (expiredCount > 0) {
        setTimeout(() => {
            if (confirm(`You have ${expiredCount} expired license(s). Would you like to view them now?`)) {
                window.open('{{ route("business-licenses.index", ["status" => "expired"]) }}', '_blank');
            }
        }, 2000);
    } else if (expiringCount > 0) {
        setTimeout(() => {
            if (confirm(`You have ${expiringCount} license(s) expiring soon. Would you like to review them?`)) {
                window.open('{{ route("business-licenses.expiring") }}', '_blank');
            }
        }, 5000);
    }
}

// Auto-refresh data every 5 minutes
setInterval(() => {
    // Reload page to refresh data
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 300000); // 5 minutes

// Real-time clock
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    document.title = `Revival Technologies - ${timeString}`;
}

setInterval(updateClock, 1000);

// Initialize license notifications
document.addEventListener('DOMContentLoaded', function() {
    showLicenseNotifications();
});

// Pulse animation for critical alerts
const criticalAlerts = document.querySelectorAll('.alert-critical');
criticalAlerts.forEach(alert => {
    alert.style.animation = 'pulse 2s infinite';
});

// Add CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
`;
document.head.appendChild(style);
</script>
@endsection