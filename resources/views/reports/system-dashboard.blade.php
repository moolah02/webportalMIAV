@extends('layouts.app')
@section('title', 'System Dashboard')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <p style="margin: 8px 0 0 0; color: #666; font-size: 16px;">Comprehensive system performance and analytics</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button onclick="exportFullReport()" class="btn-primary">
                📊 Export Full Report
            </button>
            <button onclick="printDashboard()" class="btn-secondary">
                🖨️ Print
            </button>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #1976D2; font-size: 24px;">🏢</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" style="color: #1976D2;">{{ $systemOverview['total_clients'] }}</div>
                <div class="stat-label uppercase tracking-wide">TOTAL CLIENTS</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">{{ $systemOverview['active_clients'] }} active</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #388E3C; font-size: 24px;">💻</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" style="color: #388E3C;">{{ $systemOverview['total_terminals'] }}</div>
                <div class="stat-label uppercase tracking-wide">POS TERMINALS</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">{{ $systemOverview['terminal_uptime'] }}% uptime</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #F57C00; font-size: 24px;">👥</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" style="color: #F57C00;">{{ $systemOverview['total_employees'] }}</div>
                <div class="stat-label uppercase tracking-wide">EMPLOYEES</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">Active workforce</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #7B1FA2; font-size: 24px;">📋</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" style="color: #7B1FA2;">{{ $systemOverview['active_projects'] }}</div>
                <div class="stat-label uppercase tracking-wide">ACTIVE PROJECTS</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">{{ $systemOverview['total_projects'] }} total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #D32F2F; font-size: 24px;">🎫</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" style="color: #D32F2F;">{{ $systemOverview['open_tickets'] }}</div>
                <div class="stat-label uppercase tracking-wide">OPEN TICKETS</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">Need attention</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">
                <span style="color: #00796B; font-size: 24px;">💰</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="stat-number" style="color: #00796B;">${{ number_format($systemOverview['revenue_impact'] / 1000) }}K</div>
                <div class="stat-label uppercase tracking-wide">REVENUE IMPACT</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">Monthly estimate</div>
            </div>
        </div>
    </div>

    <!-- System Health Score -->
    <div class="ui-card p-6">
        <h3 style="margin: 0 0 20px 0; color: #333; font-size: 20px; font-weight: 600;">System Health Overview</h3>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            <div style="text-align: center;">
                <div style="position: relative; width: 150px; height: 150px; margin: 0 auto;">
                    <canvas id="healthScoreChart" width="150" height="150"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div style="font-size: 32px; font-weight: bold; color: #1976D2;">{{ $systemOverview['system_health_score'] }}%</div>
                        <div style="font-size: 12px; color: #666;">HEALTH SCORE</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="health-metrics">
                    <div class="health-item">
                        <span class="health-label">Terminal Uptime</span>
                        <div class="health-bar">
                            <div class="health-progress" style="width: {{ $systemOverview['terminal_uptime'] }}%; background: #4CAF50;"></div>
                        </div>
                        <span class="health-value">{{ $systemOverview['terminal_uptime'] }}%</span>
                    </div>
                    <div class="health-item">
                        <span class="health-label">Asset Utilization</span>
                        <div class="health-bar">
                            <div class="health-progress" style="width: {{ $assetData['asset_utilization'] }}%; background: #2196F3;"></div>
                        </div>
                        <span class="health-value">{{ $assetData['asset_utilization'] }}%</span>
                    </div>
                    <div class="health-item">
                        <span class="health-label">Project Completion</span>
                        <div class="health-bar">
                            <div class="health-progress" style="width: {{ $projectData['project_completion_rate'] }}%; background: #FF9800;"></div>
                        </div>
                        <span class="health-value">{{ $projectData['project_completion_rate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Content -->
    <div class="ui-card p-6">
        <!-- Tab Navigation -->
        <div class="tab-navigation">
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
                <button onclick="exportSection('clients')" class="btn btn-sm">Export Client Data</button>
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
                <button onclick="exportSection('terminals')" class="btn btn-sm">Export Terminal Data</button>
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
                <button onclick="exportSection('service')" class="btn btn-sm">Export Service Data</button>
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
                <button onclick="exportSection('assets')" class="btn btn-sm">Export Asset Data</button>
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
                    <a href="{{ route('assets.low-stock-alerts') }}" class="btn btn-warning">View Low Stock Items</a>
                </div>
                @endif
            </div>
        </div>

        <div id="employees" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Employee Performance & Analytics</h3>
                <button onclick="exportSection('employees')" class="btn btn-sm">Export Employee Data</button>
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
                <button onclick="exportSection('projects')" class="btn btn-sm">Export Project Data</button>
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
                            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">✅</div>
                            <div class="metric-info">
                                <div class="metric-value">{{ $projectData['project_completion_rate'] }}%</div>
                                <div class="stat-label uppercase tracking-wide">Completion Rate</div>
                            </div>
                        </div>

                        @if(isset($projectData['overdue_projects']) && $projectData['overdue_projects'] > 0)
                        <div class="metric-box danger">
                            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">⚠️</div>
                            <div class="metric-info">
                                <div class="metric-value">{{ $projectData['overdue_projects'] }}</div>
                                <div class="stat-label uppercase tracking-wide">Overdue Projects</div>
                            </div>
                        </div>
                        @endif

                        @if(isset($projectData['upcoming_deadlines']) && $projectData['upcoming_deadlines'] > 0)
                        <div class="metric-box warning">
                            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📅</div>
                            <div class="metric-info">
                                <div class="metric-value">{{ $projectData['upcoming_deadlines'] }}</div>
                                <div class="stat-label uppercase tracking-wide">Due in 30 Days</div>
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
                <button onclick="exportSection('regional')" class="btn btn-sm">Export Regional Data</button>
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

<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<!-- Custom Styles -->

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
