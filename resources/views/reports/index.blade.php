@extends('layouts.app')

@section('title', 'System Analytics')

@section('content')
{{-- Actions --}}
<div class="flex justify-end items-center mb-6">
    <div class="flex items-center gap-2">
        <button onclick="exportFullReport()" class="btn-primary">&#x1F4CA; Export Full Report</button>
        <button onclick="printDashboard()" class="btn-secondary">&#x1F5A8;&#xFE0F; Print</button>
    </div>
</div>

{{-- Key Metrics --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-5">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">🏢</div>
        <div>
            <div class="stat-number">{{ $systemOverview['total_clients'] }}</div>
            <div class="stat-label">Total Clients</div>
            <div class="stat-sub">{{ $systemOverview['active_clients'] }} active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">💻</div>
        <div>
            <div class="stat-number">{{ $systemOverview['total_terminals'] }}</div>
            <div class="stat-label">POS Terminals</div>
            <div class="stat-sub">{{ $systemOverview['terminal_uptime'] }}% uptime</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-orange">👥</div>
        <div>
            <div class="stat-number">{{ $systemOverview['total_employees'] }}</div>
            <div class="stat-label">Employees</div>
            <div class="stat-sub">Active workforce</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">📋</div>
        <div>
            <div class="stat-number">{{ $systemOverview['active_projects'] }}</div>
            <div class="stat-label">Active Projects</div>
            <div class="stat-sub">{{ $systemOverview['total_projects'] }} total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">🎫</div>
        <div>
            <div class="stat-number">{{ $systemOverview['open_tickets'] }}</div>
            <div class="stat-label">Open Tickets</div>
            <div class="stat-sub">Need attention</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-teal">💰</div>
        <div>
            <div class="stat-number">${{ number_format($systemOverview['revenue_impact'] / 1000) }}K</div>
            <div class="stat-label">Revenue Impact</div>
            <div class="stat-sub">Monthly estimate</div>
        </div>
    </div>
</div>

{{-- System Health Card --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">System Health Overview</span>
        <span class="badge {{ $systemOverview['system_health_score'] >= 80 ? 'badge-green' : ($systemOverview['system_health_score'] >= 60 ? 'badge-yellow' : 'badge-red') }}">{{ $systemOverview['system_health_score'] }}% Health</span>
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
            <div class="flex flex-col items-center">
                <div class="relative w-36 h-36">
                    <canvas id="healthScoreChart" width="144" height="144"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-3xl font-bold text-[#1a3a5c]">{{ $systemOverview['system_health_score'] }}%</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Health Score</div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2 health-metrics">
                <div class="health-item">
                    <span class="health-label">Terminal Uptime</span>
                    <div class="health-bar"><div class="health-progress" style="width: {{ $systemOverview['terminal_uptime'] }}%; background: #22c55e;"></div></div>
                    <span class="health-value">{{ $systemOverview['terminal_uptime'] }}%</span>
                </div>
                <div class="health-item">
                    <span class="health-label">Asset Utilization</span>
                    <div class="health-bar"><div class="health-progress" style="width: {{ $assetData['asset_utilization'] }}%; background: #3b82f6;"></div></div>
                    <span class="health-value">{{ $assetData['asset_utilization'] }}%</span>
                </div>
                <div class="health-item">
                    <span class="health-label">Project Completion</span>
                    <div class="health-bar"><div class="health-progress" style="width: {{ $projectData['project_completion_rate'] }}%; background: #f59e0b;"></div></div>
                    <span class="health-value">{{ $projectData['project_completion_rate'] }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div><!-- Tabbed Content -->
    <div class="ui-card overflow-hidden mt-5">
        <!-- Tab Navigation -->
        <div class="tab-navigation border-b border-gray-200 px-5 pt-2">
            <button class="tab-button active" onclick="switchTab(event, 'overview')">📊 System Overview</button>
            <button class="tab-button" onclick="switchTab(event, 'clients')">🏢 Client Analytics</button>
            <button class="tab-button" onclick="switchTab(event, 'terminals')">💻 Terminal Management</button>
            <button class="tab-button" onclick="switchTab(event, 'service')">🔧 Service Activity</button>
            <button class="tab-button" onclick="switchTab(event, 'assets')">📦 Asset Management</button>
            <button class="tab-button" onclick="switchTab(event, 'employees')">👥 Employee Performance</button>
            <button class="tab-button" onclick="switchTab(event, 'projects')">📋 Project Management</button>
            <button class="tab-button" onclick="switchTab(event, 'regional')">🗺️ Regional Analysis</button>
        </div>

        <!-- Tab Content -->
        <div id="overview" class="tab-content active">
            <h3>System Overview & Key Metrics</h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin: 24px 0;">
                <div class="chart-container">
                    <h4>Monthly Visit Trends</h4>
                    <canvas id="visitTrendsChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>System Performance Indicators</h4>
                    <div class="kpi-grid">
                        <div class="kpi-item">
                            <div class="kpi-value">{{ $systemOverview['total_visits_this_month'] }}</div>
                            <div class="kpi-label">Visits This Month</div>
                            <div class="kpi-change {{ $systemOverview['total_visits_this_month'] > $systemOverview['total_visits_last_month'] ? 'positive' : 'negative' }}">
                                {{ $systemOverview['total_visits_this_month'] > $systemOverview['total_visits_last_month'] ? '↗' : '↘' }}
                                {{ abs($systemOverview['total_visits_this_month'] - $systemOverview['total_visits_last_month']) }} vs last month
                            </div>
                        </div>

                        <div class="kpi-item">
                            <div class="kpi-value">{{ $assetData['total_assets'] }}</div>
                            <div class="kpi-label">Total Assets</div>
                            <div class="kpi-change">{{ $assetData['assignment_status']['assigned'] }} assigned</div>
                        </div>

                        <div class="kpi-item">
                            <div class="kpi-value">{{ number_format($serviceActivity['average_resolution_time'], 1) }}h</div>
                            <div class="kpi-label">Avg Resolution Time</div>
                            <div class="kpi-change">Ticket resolution</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="recommendation-box">
                <h4>🎯 Key Recommendations</h4>
                <ul>
                    @if($systemOverview['terminal_uptime'] < 90)
                    <li><strong>Terminal Health:</strong> System uptime is {{ $systemOverview['terminal_uptime'] }}%. Consider increasing maintenance frequency for terminals in maintenance/faulty status.</li>
                    @endif
                    @if($systemOverview['open_tickets'] > 5)
                    <li><strong>Support Queue:</strong> {{ $systemOverview['open_tickets'] }} open tickets require attention to maintain service quality.</li>
                    @endif
                    @if($assetData['low_stock_alerts'] > 0)
                    <li><strong>Inventory Alert:</strong> {{ $assetData['low_stock_alerts'] }} assets are below minimum stock levels and need replenishment.</li>
                    @endif
                    <li><strong>Growth Opportunity:</strong> Consider expanding service coverage to improve regional distribution and reduce technician workload.</li>
                </ul>
            </div>
        </div>

        <div id="clients" class="tab-content">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 20px;">
                <h3>Client Analytics & Performance</h3>
                <button onclick="exportSection('clients')" class="btn-sm">Export Client Data</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="chart-container">
                    <h4>Client Distribution by Status</h4>
                    <canvas id="clientStatusChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Top Clients by Terminal Count</h4>
                    <div class="client-list">
                        @foreach($clientAnalytics['client_terminal_counts'] as $client)
                        <div class="client-item">
                            <div class="client-info">
                                <span class="client-name">{{ $client['name'] }}</span>
                                <span class="client-status status-{{ $client['status'] }}">{{ strtoupper($client['status']) }}</span>
                            </div>
                            <div class="client-stats">
                                <span class="terminal-count">{{ $client['terminal_count'] }} terminals</span>
                                <span class="active-count">({{ $client['active_terminals'] }} active)</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <h4>Client Activity Analysis</h4>
                <div class="chart-container">
                    <canvas id="clientActivityChart"></canvas>
                </div>
            </div>
        </div>

        <div id="terminals" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Terminal Management & Health</h3>
                <button onclick="exportSection('terminals')" class="btn-sm">Export Terminal Data</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="chart-container">
                    <h4>Terminal Status Distribution</h4>
                    <canvas id="terminalStatusChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Device Model Distribution</h4>
                    <canvas id="terminalModelsChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Geographic Distribution</h4>
                    <canvas id="terminalRegionChart"></canvas>
                </div>

                <div class="service-alerts">
                    <h4>Service Requirements</h4>
                    <div class="alert-item urgent">
                        <span class="alert-icon">🚨</span>
                        <div class="alert-content">
                            <div class="alert-title">{{ $terminalData['terminals_needing_service'] }} Terminals Need Service</div>
                            <div class="alert-desc">Immediate attention required for optimal performance</div>
                        </div>
                    </div>

                    @if(isset($terminalData['service_due_analysis']))
                    <div class="alert-item warning">
                        <span class="alert-icon">⚠️</span>
                        <div class="alert-content">
                            <div class="alert-title">{{ $terminalData['service_due_analysis']['due_this_week'] ?? 0 }} Due This Week</div>
                            <div class="alert-desc">Schedule maintenance to prevent issues</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div id="service" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Service Activity & Performance</h3>
                <button onclick="exportSection('service')" class="btn-sm">Export Service Data</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="chart-container">
                    <h4>Visits by Technician (Last 30 Days)</h4>
                    <canvas id="technicianVisitsChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Ticket Priority Distribution</h4>
                    <canvas id="ticketPriorityChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Job Assignment Status</h4>
                    <canvas id="jobStatusChart"></canvas>
                </div>

                <div class="productivity-metrics">
                    <h4>Technician Productivity</h4>
                    @foreach($serviceActivity['technician_productivity'] as $tech)
                    <div class="productivity-item">
                        <div class="tech-name">{{ $tech['name'] }}</div>
                        <div class="productivity-bar">
                            <div class="progress-fill" style="width: {{ $tech['productivity_score'] }}%; background: linear-gradient(90deg, #4CAF50 0%, #8BC34A 100%);"></div>
                        </div>
                        <div class="tech-stats">{{ $tech['visits'] }} visits</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="assets" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Asset Management & Utilization</h3>
                <button onclick="exportSection('assets')" class="btn-sm">Export Asset Data</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="chart-container">
                    <h4>Assets by Category</h4>
                    <canvas id="assetCategoryChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Asset Utilization Overview</h4>
                    <div class="utilization-stats">
                        <div class="stat-item">
                            <div class="stat-value">{{ $assetData['assignment_status']['total_stock'] }}</div>
                            <div class="stat-label">Total Stock</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $assetData['assignment_status']['assigned'] }}</div>
                            <div class="stat-label">Currently Assigned</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $assetData['assignment_status']['available'] }}</div>
                            <div class="stat-label">Available</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $assetData['asset_utilization'] }}%</div>
                            <div class="stat-label">Utilization Rate</div>
                        </div>
                    </div>
                </div>

                <div class="chart-container">
                    <h4>Most Requested Assets</h4>
                    <div class="requested-assets">
                        @foreach($assetData['top_requested_assets'] as $asset => $count)
                        <div class="asset-request-item">
                            <span class="asset-name">{{ $asset }}</span>
                            <span class="request-count">{{ $count }} requests</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($assetData['low_stock_alerts'] > 0)
                <div class="alert-box">
                    <h4>⚠️ Stock Alerts</h4>
                    <p>{{ $assetData['low_stock_alerts'] }} assets are below minimum stock levels and require immediate attention.</p>
                    <a href="{{ route('assets.low-stock-alerts') }}" class="btn-secondary">View Low Stock Items</a>
                </div>
                @endif
            </div>
        </div>

        <div id="employees" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Employee Performance & Analytics</h3>
                <button onclick="exportSection('employees')" class="btn-sm">Export Employee Data</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="chart-container">
                    <h4>Employees by Department</h4>
                    <canvas id="employeeDeptChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Employee Roles Distribution</h4>
                    <canvas id="employeeRoleChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Current Technician Workload</h4>
                    <div class="workload-list">
                        @foreach($employeeData['technician_workload'] as $tech => $assignments)
                        <div class="workload-item">
                            <span class="tech-name">{{ $tech }}</span>
                            <div class="workload-bar">
                                <div class="workload-fill" style="width: {{ min(100, ($assignments / 5) * 100) }}%;"></div>
                            </div>
                            <span class="assignment-count">{{ $assignments }} assignments</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="employee-stats">
                    <h4>Workforce Overview</h4>
                    <div class="stat-grid">
                        <div class="stat-card">
                            <div class="stat-number">{{ $employeeData['total_employees'] }}</div>
                            <div class="stat-title">Active Employees</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $employeeData['employee_asset_assignments'] }}</div>
                            <div class="stat-title">Asset Assignments</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $employeeData['recent_hires'] }}</div>
                            <div class="stat-title">Recent Hires (3mo)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="projects" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Project Management & Progress</h3>
                <button onclick="exportSection('projects')" class="btn-sm">Export Project Data</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="chart-container">
                    <h4>Projects by Status</h4>
                    <canvas id="projectStatusChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Project Types Distribution</h4>
                    <canvas id="projectTypeChart"></canvas>
                </div>

                <div class="project-alerts">
                    <h4>Project Health Indicators</h4>
                    <div class="project-metrics">
                        <div class="metric-box success">
                            <div class="metric-icon">✅</div>
                            <div class="metric-info">
                                <div class="metric-value">{{ $projectData['project_completion_rate'] }}%</div>
                                <div class="metric-label">Completion Rate</div>
                            </div>
                        </div>

                        @if(isset($projectData['overdue_projects']) && $projectData['overdue_projects'] > 0)
                        <div class="metric-box danger">
                            <div class="metric-icon">⚠️</div>
                            <div class="metric-info">
                                <div class="metric-value">{{ $projectData['overdue_projects'] }}</div>
                                <div class="metric-label">Overdue Projects</div>
                            </div>
                        </div>
                        @endif

                        @if(isset($projectData['upcoming_deadlines']) && $projectData['upcoming_deadlines'] > 0)
                        <div class="metric-box warning">
                            <div class="metric-icon">📅</div>
                            <div class="metric-info">
                                <div class="metric-value">{{ $projectData['upcoming_deadlines'] }}</div>
                                <div class="metric-label">Due in 30 Days</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="regional" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Regional Analysis & Coverage</h3>
                <button onclick="exportSection('regional')" class="btn-sm">Export Regional Data</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <div class="chart-container">
                    <h4>Terminal Distribution by Region</h4>
                    <canvas id="regionalTerminalsChart"></canvas>
                </div>

                <div class="chart-container">
                    <h4>Service Activity by Region</h4>
                    <canvas id="regionalServiceChart"></canvas>
                </div>

                <div class="regional-health">
                    <h4>Regional Health Scores</h4>
                    @foreach($regionalData['regional_health_scores'] as $region => $score)
                    <div class="region-item">
                        <div class="region-name">{{ $region }}</div>
                        <div class="health-score-bar">
                            <div class="score-fill" style="width: {{ $score }}%; background: {{ $score >= 80 ? '#4CAF50' : ($score >= 60 ? '#FF9800' : '#F44336') }};"></div>
                        </div>
                        <div class="score-value">{{ $score }}%</div>
                    </div>
                    @endforeach
                </div>

                <div class="coverage-stats">
                    <h4>Coverage Analysis</h4>
                    <div class="coverage-grid">
                        <div class="coverage-item">
                            <div class="coverage-number">{{ $regionalData['coverage_analysis']['total_cities'] }}</div>
                            <div class="coverage-label">Cities Covered</div>
                        </div>
                        <div class="coverage-item">
                            <div class="coverage-number">{{ $regionalData['coverage_analysis']['covered_regions'] }}</div>
                            <div class="coverage-label">Active Regions</div>
                        </div>
                        <div class="coverage-item">
                            <div class="coverage-number">{{ $regionalData['coverage_analysis']['terminals_per_technician'] }}</div>
                            <div class="coverage-label">Terminals/Technician</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Tab navigation & show/hide &#x2014; required by switchTab() JS */
.tab-navigation { display: flex; flex-wrap: wrap; gap: 2px; }
.tab-button {
    padding: 10px 16px; border: none; background: none; cursor: pointer;
    font-size: 13px; font-weight: 500; color: #6b7280;
    border-bottom: 2px solid transparent; transition: all 0.2s; white-space: nowrap;
}
.tab-button:hover { color: #1a3a5c; }
.tab-button.active { color: #1a3a5c; border-bottom-color: #1a3a5c; font-weight: 600; }
.tab-content { display: none; padding: 24px; }
.tab-content.active { display: block; animation: fadeIn 0.25s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* System health bars */
.health-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
.health-item:last-child { border-bottom: none; }
.health-label { min-width: 160px; font-size: 14px; color: #374151; }
.health-bar { flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
.health-progress { height: 100%; border-radius: 4px; transition: width 0.4s ease; }
.health-value { min-width: 40px; text-align: right; font-size: 14px; font-weight: 600; color: #111827; }
.health-metrics { display: flex; flex-direction: column; justify-content: center; }

/* Chart containers within tabs */
.chart-container { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
.chart-container h4 { margin: 0 0 12px 0; font-size: 14px; font-weight: 600; color: #374151; }
.chart-container canvas { max-height: 280px !important; }

/* KPI grid */
.kpi-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.kpi-item { background: #f9fafb; border-radius: 8px; padding: 16px; text-align: center; }
.kpi-value { font-size: 26px; font-weight: 700; color: #111827; line-height: 1; }
.kpi-label { font-size: 12px; color: #6b7280; margin-top: 4px; }
.kpi-change { color: #6b7280; font-size: 11px; margin-top: 2px; }
.kpi-change.positive { color: #16a34a; }
.kpi-change.negative { color: #dc2626; }

/* Recommendations */
.recommendation-box { background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 0 8px 8px 0; padding: 16px; margin-top: 16px; }
.recommendation-box h4 { margin: 0 0 8px 0; color: #1d4ed8; font-size: 14px; }
.recommendation-box ul { margin: 0; padding-left: 20px; color: #374151; font-size: 13px; line-height: 1.6; }

/* Alert items */
.alert-item { padding: 12px 16px; border-radius: 8px; border-left: 4px solid #e5e7eb; background: #f9fafb; margin-bottom: 8px; }
.alert-item.urgent { border-left-color: #dc2626; background: #fef2f2; }
.alert-item.warning { border-left-color: #f59e0b; background: #fffbeb; }

/* Tab headings */
.tab-content h3 { font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; }
.tab-content h4 { font-size: 13px; font-weight: 600; color: #374151; }

/* Productivity bars */
.productivity-item { display: flex; align-items: center; gap: 10px; padding: 6px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; }

/* Regional */
.region-item { display: flex; align-items: center; gap: 10px; padding: 6px 0; }
.region-name { min-width: 120px; font-size: 13px; color: #374151; }
.health-score-bar { flex: 1; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; }
.score-fill { height: 100%; border-radius: 3px; }
.score-value { min-width: 32px; text-align: right; font-size: 12px; font-weight: 600; color: #111827; }
.coverage-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.coverage-item { background: #f9fafb; border-radius: 8px; padding: 14px; text-align: center; }
.coverage-number { font-size: 22px; font-weight: 700; color: #111827; }
.coverage-label { font-size: 11px; color: #6b7280; margin-top: 2px; }

/* Metric boxes within tabs */
.metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; margin: 16px 0; }
.metric-box { background: #f9fafb; border-radius: 8px; padding: 14px; display: flex; align-items: center; gap: 10px; }
.metric-box.danger { background: #fef2f2; }
.metric-box.warning { background: #fffbeb; }
.metric-icon { font-size: 20px; flex-shrink: 0; }
.metric-info .metric-value { font-size: 20px; font-weight: 700; color: #111827; line-height: 1; }
.metric-info .metric-label { font-size: 11px; color: #6b7280; margin-top: 2px; }

/* Client / service list items */
.client-list, .service-list { display: flex; flex-direction: column; gap: 8px; }
.client-item { display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f9fafb; border-radius: 6px; }
.technician-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
.regional-health, .coverage-stats { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
.regional-health h4, .coverage-stats h4 { margin: 0 0 12px 0; font-size: 14px; font-weight: 600; color: #374151; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Global chart instances
let charts = {};

// Tab switching functionality
function switchTab(evt, tabName) {
    const tabContents = document.getElementsByClassName('tab-content');
    const tabButtons = document.getElementsByClassName('tab-button');

    // Hide all tab contents
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove('active');
    }

    // Remove active class from all buttons
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }

    // Show selected tab and mark button as active
    document.getElementById(tabName).classList.add('active');
    evt.currentTarget.classList.add('active');

    // Initialize charts for the active tab
    setTimeout(() => initializeChartsForTab(tabName), 100);
}

// Chart initialization
function initializeChartsForTab(tabName) {
    switch(tabName) {
        case 'overview':
            initOverviewCharts();
            break;
        case 'clients':
            initClientCharts();
            break;
        case 'terminals':
            initTerminalCharts();
            break;
        case 'service':
            initServiceCharts();
            break;
        case 'assets':
            initAssetCharts();
            break;
        case 'employees':
            initEmployeeCharts();
            break;
        case 'projects':
            initProjectCharts();
            break;
        case 'regional':
            initRegionalCharts();
            break;
    }
}

function initOverviewCharts() {
    // Health Score Chart (Doughnut)
    const healthCtx = document.getElementById('healthScoreChart');
    if (healthCtx && !charts.healthScore) {
        const score = {{ $systemOverview['system_health_score'] }};
        charts.healthScore = new Chart(healthCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: ['#1976D2', '#F0F0F0'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    // Visit Trends Chart
    const visitCtx = document.getElementById('visitTrendsChart');
    if (visitCtx && !charts.visitTrends) {
        charts.visitTrends = new Chart(visitCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Visits',
                    data: [120, 150, 180, 200, 170, 220],
                    borderColor: '#1976D2',
                    backgroundColor: 'rgba(25, 118, 210, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
}

function initClientCharts() {
    // Client Status Distribution
    const clientStatusCtx = document.getElementById('clientStatusChart');
    if (clientStatusCtx && !charts.clientStatus) {
        const statusData = @json($clientAnalytics['client_distribution']);
        charts.clientStatus = new Chart(clientStatusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(s => s.toUpperCase()),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: ['#4CAF50', '#FF9800', '#F44336', '#9E9E9E']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Client Activity Chart
    const activityCtx = document.getElementById('clientActivityChart');
    if (activityCtx && !charts.clientActivity) {
        const activityData = @json($clientAnalytics['top_clients_by_activity']);
        charts.clientActivity = new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(activityData),
                datasets: [{
                    label: 'Visits',
                    data: Object.values(activityData),
                    backgroundColor: '#1976D2'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
}

function initTerminalCharts() {
    // Terminal Status Distribution
    const terminalStatusCtx = document.getElementById('terminalStatusChart');
    if (terminalStatusCtx && !charts.terminalStatus) {
        const statusData = @json($terminalData['status_distribution']);
        charts.terminalStatus = new Chart(terminalStatusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(s => s.toUpperCase()),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: ['#4CAF50', '#FF9800', '#F44336', '#9E9E9E']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Terminal Models Chart
    const modelsCtx = document.getElementById('terminalModelsChart');
    if (modelsCtx && !charts.terminalModels) {
        const modelData = @json($terminalData['model_distribution']);
        charts.terminalModels = new Chart(modelsCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(modelData),
                datasets: [{
                    data: Object.values(modelData),
                    backgroundColor: '#2196F3'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Regional Distribution
    const regionCtx = document.getElementById('terminalRegionChart');
    if (regionCtx && !charts.terminalRegion) {
        const regionData = @json($terminalData['regional_distribution']);
        charts.terminalRegion = new Chart(regionCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(regionData),
                datasets: [{
                    data: Object.values(regionData),
                    backgroundColor: ['#4CAF50', '#FF9800', '#F44336', '#2196F3', '#9C27B0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}

function initServiceCharts() {
    // Technician Visits Chart
    const techVisitsCtx = document.getElementById('technicianVisitsChart');
    if (techVisitsCtx && !charts.technicianVisits) {
        const visitsData = @json($serviceActivity['visits_by_technician']);
        charts.technicianVisits = new Chart(techVisitsCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(visitsData),
                datasets: [{
                    label: 'Visits',
                    data: Object.values(visitsData),
                    backgroundColor: '#4CAF50'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Ticket Priority Chart
    const ticketPriorityCtx = document.getElementById('ticketPriorityChart');
    if (ticketPriorityCtx && !charts.ticketPriority) {
        const priorityData = @json($serviceActivity['tickets_by_priority']);
        charts.ticketPriority = new Chart(ticketPriorityCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(priorityData).map(p => p.toUpperCase()),
                datasets: [{
                    data: Object.values(priorityData),
                    backgroundColor: ['#F44336', '#FF9800', '#2196F3', '#4CAF50']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Job Status Chart
    const jobStatusCtx = document.getElementById('jobStatusChart');
    if (jobStatusCtx && !charts.jobStatus) {
        const jobData = @json($serviceActivity['job_assignments_by_status']);
        charts.jobStatus = new Chart(jobStatusCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(jobData).map(s => s.replace('_', ' ').toUpperCase()),
                datasets: [{
                    data: Object.values(jobData),
                    backgroundColor: '#FF9800'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
}

function initAssetCharts() {
    // Asset Category Chart
    const assetCategoryCtx = document.getElementById('assetCategoryChart');
    if (assetCategoryCtx && !charts.assetCategory) {
        const categoryData = @json($assetData['assets_by_category']);
        charts.assetCategory = new Chart(assetCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(categoryData),
                datasets: [{
                    data: Object.values(categoryData),
                    backgroundColor: ['#2196F3', '#4CAF50', '#FF9800', '#F44336', '#9C27B0', '#00BCD4']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}

function initEmployeeCharts() {
    // Employee Department Chart
    const deptCtx = document.getElementById('employeeDeptChart');
    if (deptCtx && !charts.employeeDept) {
        const deptData = @json($employeeData['employees_by_department']);
        charts.employeeDept = new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(deptData),
                datasets: [{
                    data: Object.values(deptData),
                    backgroundColor: '#1976D2'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Employee Role Chart
    const roleCtx = document.getElementById('employeeRoleChart');
    if (roleCtx && !charts.employeeRole) {
        const roleData = @json($employeeData['employees_by_role']);
        charts.employeeRole = new Chart(roleCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(roleData),
                datasets: [{
                    data: Object.values(roleData),
                    backgroundColor: ['#4CAF50', '#FF9800', '#F44336', '#2196F3']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}

function initProjectCharts() {
    // Project Status Chart
    const projectStatusCtx = document.getElementById('projectStatusChart');
    if (projectStatusCtx && !charts.projectStatus) {
        const statusData = @json($projectData['projects_by_status']);
        charts.projectStatus = new Chart(projectStatusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(s => s.toUpperCase()),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#F44336']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Project Type Chart
    const projectTypeCtx = document.getElementById('projectTypeChart');
    if (projectTypeCtx && !charts.projectType) {
        const typeData = @json($projectData['projects_by_type']);
        charts.projectType = new Chart(projectTypeCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(typeData).map(t => t.replace('_', ' ').toUpperCase()),
                datasets: [{
                    data: Object.values(typeData),
                    backgroundColor: '#9C27B0'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
}

function initRegionalCharts() {
    // Regional Terminals Chart
    const regionalTerminalsCtx = document.getElementById('regionalTerminalsChart');
    if (regionalTerminalsCtx && !charts.regionalTerminals) {
        const terminalData = @json($regionalData['terminals_by_region']);
        charts.regionalTerminals = new Chart(regionalTerminalsCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(terminalData),
                datasets: [{
                    label: 'Terminals',
                    data: Object.values(terminalData),
                    backgroundColor: '#4CAF50'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Regional Service Chart
    const regionalServiceCtx = document.getElementById('regionalServiceChart');
    if (regionalServiceCtx && !charts.regionalService) {
        const serviceData = @json($regionalData['service_activity_by_region']);
        charts.regionalService = new Chart(regionalServiceCtx, {
            type: 'line',
            data: {
                labels: Object.keys(serviceData),
                datasets: [{
                    label: 'Service Visits',
                    data: Object.values(serviceData),
                    borderColor: '#FF9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
}

// Export functions
function exportFullReport() {
    window.open('/reports/system/export', '_blank');
}

function exportSection(section) {
    window.open(`/reports/system/export-csv?section=${section}`, '_blank');
}

function printDashboard() {
    window.print();
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Initialize overview charts by default
    initOverviewCharts();
    console.log('System dashboard initialized');
});
</script>
@endsection