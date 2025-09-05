<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>System Analytics Report - Revival Technologies POS Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }

        .header h1 {
            margin: 0;
            color: #007bff;
            font-size: 28px;
            font-weight: 700;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }

        .section-title {
            color: #333;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f3f4;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            padding: 20px;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #007bff;
        }

        .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .metric-label {
            color: #666;
            font-size: 14px;
            margin: 5px 0 0 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .data-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f3f4;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            color: #fff;
        }

        .status-active { background: #28a745; }
        .status-inactive { background: #6c757d; }
        .status-warning { background: #ffc107; }
        .status-danger { background: #dc3545; }

        .health-score {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            color: #fff;
        }

        .health-excellent { background: #28a745; }
        .health-good { background: #17a2b8; }
        .health-warning { background: #ffc107; }
        .health-poor { background: #dc3545; }

        .recommendations {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .recommendation-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 12px;
            background: #fff;
            border-radius: 6px;
            border-left: 4px solid #17a2b8;
        }

        .recommendation-item.warning {
            border-left-color: #ffc107;
        }

        .recommendation-item.error {
            border-left-color: #dc3545;
        }

        .recommendation-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .recommendation-text {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .section {
                page-break-inside: avoid;
            }

            .metrics-grid {
                display: block;
            }

            .metric-card {
                display: inline-block;
                width: 22%;
                margin: 1%;
                vertical-align: top;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>System Analytics Report</h1>
        <p>Revival Technologies POS Management System</p>
    </div>

    <!-- Report Metadata -->
    <div class="report-meta">
        <div>
            <strong>Report Generated:</strong> {{ now()->format('F j, Y \a\t g:i A') }}<br>
            <strong>Report Period:</strong> {{ $report_period ?? 'Last 30 days' }}
        </div>
        <div>
            <strong>Total Clients:</strong> {{ $overview['total_clients'] ?? 0 }}<br>
            <strong>Total Terminals:</strong> {{ $overview['total_terminals'] ?? 0 }}
        </div>
    </div>

    <!-- System Overview Section -->
    <div class="section">
        <h2 class="section-title">System Overview</h2>

        <div class="metrics-grid">
            <div class="metric-card">
                <p class="metric-value">{{ $overview['total_clients'] ?? 0 }}</p>
                <p class="metric-label">Total Clients</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $overview['active_terminals'] ?? 0 }}</p>
                <p class="metric-label">Active Terminals</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $overview['active_projects'] ?? 0 }}</p>
                <p class="metric-label">Active Projects</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $overview['open_tickets'] ?? 0 }}</p>
                <p class="metric-label">Open Tickets</p>
            </div>
        </div>

        @if(isset($overview['recommendations']) && count($overview['recommendations']) > 0)
        <div class="recommendations">
            <h3 style="margin-top: 0; color: #333;">System Recommendations</h3>
            @foreach($overview['recommendations'] as $recommendation)
            <div class="recommendation-item {{ $recommendation['type'] ?? 'info' }}">
                <div>
                    <div class="recommendation-title">{{ $recommendation['title'] }}</div>
                    <p class="recommendation-text">{{ $recommendation['message'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Client Analytics Section -->
    <div class="section">
        <h2 class="section-title">Client Analytics</h2>

        @if(isset($clientAnalytics['client_details']) && count($clientAnalytics['client_details']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th style="text-align: center;">Terminals</th>
                    <th style="text-align: center;">Active Projects</th>
                    <th style="text-align: center;">Recent Visits</th>
                    <th style="text-align: center;">Open Tickets</th>
                    <th style="text-align: center;">Health Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientAnalytics['client_details'] as $client)
                <tr>
                    <td><strong>{{ $client['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $client['terminals'] }}</td>
                    <td style="text-align: center;">{{ $client['projects'] }}</td>
                    <td style="text-align: center;">{{ $client['visits'] }}</td>
                    <td style="text-align: center;">
                        <span class="status-badge {{ $client['tickets'] > 0 ? 'status-danger' : 'status-active' }}">
                            {{ $client['tickets'] }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="health-score {{ $client['health_score'] >= 80 ? 'health-excellent' : ($client['health_score'] >= 60 ? 'health-good' : 'health-poor') }}">
                            {{ $client['health_score'] }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Terminal Management Section -->
    <div class="section">
        <h2 class="section-title">Terminal Management</h2>

        <div class="metrics-grid">
            <div class="metric-card">
                <p class="metric-value">{{ $terminalAnalytics['status_counts']['active'] ?? 0 }}</p>
                <p class="metric-label">Active Terminals</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $terminalAnalytics['status_counts']['inactive'] ?? 0 }}</p>
                <p class="metric-label">Inactive Terminals</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $terminalAnalytics['status_counts']['maintenance'] ?? 0 }}</p>
                <p class="metric-label">In Maintenance</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $terminalAnalytics['status_counts']['offline'] ?? 0 }}</p>
                <p class="metric-label">Offline</p>
            </div>
        </div>

        @if(isset($terminalAnalytics['locations']) && count($terminalAnalytics['locations']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Location</th>
                    <th style="text-align: center;">Total</th>
                    <th style="text-align: center;">Active</th>
                    <th style="text-align: center;">Inactive</th>
                    <th style="text-align: center;">Maintenance</th>
                    <th style="text-align: center;">Health Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($terminalAnalytics['locations'] as $location)
                <tr>
                    <td><strong>{{ $location['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $location['total'] }}</td>
                    <td style="text-align: center;">
                        <span class="status-badge status-active">{{ $location['active'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge status-inactive">{{ $location['inactive'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge status-warning">{{ $location['maintenance'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="health-score {{ $location['health'] >= 80 ? 'health-excellent' : ($location['health'] >= 60 ? 'health-good' : 'health-poor') }}">
                            {{ $location['health'] }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Service Activity Section -->
    <div class="section">
        <h2 class="section-title">Service Activity</h2>

        <div class="metrics-grid">
            <div class="metric-card">
                <p class="metric-value">{{ $serviceActivity['total_visits'] ?? 0 }}</p>
                <p class="metric-label">Total Visits</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $serviceActivity['avg_response_time'] ?? 0 }}h</p>
                <p class="metric-label">Avg Response Time</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $serviceActivity['resolution_rate'] ?? 0 }}%</p>
                <p class="metric-label">Resolution Rate</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $serviceActivity['customer_satisfaction'] ?? 0 }}%</p>
                <p class="metric-label">Customer Satisfaction</p>
            </div>
        </div>

        @if(isset($serviceActivity['technician_performance']) && count($serviceActivity['technician_performance']) > 0)
        <h3 style="color: #333; margin-bottom: 15px;">Technician Performance</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Technician</th>
                    <th style="text-align: center;">Visits Completed</th>
                    <th style="text-align: center;">Avg Response Time</th>
                    <th style="text-align: center;">Success Rate</th>
                    <th style="text-align: center;">Customer Rating</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceActivity['technician_performance'] as $tech)
                <tr>
                    <td><strong>{{ $tech['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $tech['visits'] }}</td>
                    <td style="text-align: center;">{{ $tech['response_time'] }}h</td>
                    <td style="text-align: center;">
                        <span class="health-score {{ $tech['success_rate'] >= 90 ? 'health-excellent' : ($tech['success_rate'] >= 80 ? 'health-good' : 'health-warning') }}">
                            {{ $tech['success_rate'] }}%
                        </span>
                    </td>
                    <td style="text-align: center;">{{ $tech['rating'] }}/5</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Asset Management Section -->
    <div class="section">
        <h2 class="section-title">Asset Management</h2>

        @if(isset($assetManagement['categories']) && count($assetManagement['categories']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Asset Category</th>
                    <th style="text-align: center;">Total Assets</th>
                    <th style="text-align: center;">Available</th>
                    <th style="text-align: center;">In Use</th>
                    <th style="text-align: center;">Maintenance</th>
                    <th style="text-align: center;">Utilization Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assetManagement['categories'] as $category)
                <tr>
                    <td><strong>{{ $category['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $category['total'] }}</td>
                    <td style="text-align: center;">
                        <span class="status-badge status-active">{{ $category['available'] }}</span>
                    </td>
                    <td style="text-align: center;">{{ $category['in_use'] }}</td>
                    <td style="text-align: center;">
                        <span class="status-badge status-warning">{{ $category['maintenance'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="health-score {{ $category['utilization'] >= 80 ? 'health-excellent' : ($category['utilization'] >= 60 ? 'health-good' : 'health-warning') }}">
                            {{ $category['utilization'] }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Employee Performance Section -->
    <div class="section">
        <h2 class="section-title">Employee Performance</h2>

        @if(isset($employeePerformance['departments']) && count($employeePerformance['departments']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th style="text-align: center;">Employees</th>
                    <th style="text-align: center;">Active Projects</th>
                    <th style="text-align: center;">Completed Tasks</th>
                    <th style="text-align: center;">Performance Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employeePerformance['departments'] as $dept)
                <tr>
                    <td><strong>{{ $dept['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $dept['employee_count'] }}</td>
                    <td style="text-align: center;">{{ $dept['active_projects'] }}</td>
                    <td style="text-align: center;">{{ $dept['completed_tasks'] }}</td>
                    <td style="text-align: center;">
                        <span class="health-score {{ $dept['performance_score'] >= 85 ? 'health-excellent' : ($dept['performance_score'] >= 70 ? 'health-good' : 'health-warning') }}">
                            {{ $dept['performance_score'] }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Project Management Section -->
    <div class="section">
        <h2 class="section-title">Project Management</h2>

        <div class="metrics-grid">
            <div class="metric-card">
                <p class="metric-value">{{ $projectManagement['total_projects'] ?? 0 }}</p>
                <p class="metric-label">Total Projects</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $projectManagement['active_projects'] ?? 0 }}</p>
                <p class="metric-label">Active Projects</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $projectManagement['completed_projects'] ?? 0 }}</p>
                <p class="metric-label">Completed Projects</p>
            </div>
            <div class="metric-card">
                <p class="metric-value">{{ $projectManagement['completion_rate'] ?? 0 }}%</p>
                <p class="metric-label">Completion Rate</p>
            </div>
        </div>

        @if(isset($projectManagement['project_types']) && count($projectManagement['project_types']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Project Type</th>
                    <th style="text-align: center;">Count</th>
                    <th style="text-align: center;">Average Duration</th>
                    <th style="text-align: center;">Success Rate</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projectManagement['project_types'] as $type)
                <tr>
                    <td><strong>{{ $type['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $type['count'] }}</td>
                    <td style="text-align: center;">{{ $type['avg_duration'] }} days</td>
                    <td style="text-align: center;">
                        <span class="health-score {{ $type['success_rate'] >= 90 ? 'health-excellent' : ($type['success_rate'] >= 75 ? 'health-good' : 'health-warning') }}">
                            {{ $type['success_rate'] }}%
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge {{ $type['status'] === 'on_track' ? 'status-active' : ($type['status'] === 'delayed' ? 'status-warning' : 'status-danger') }}">
                            {{ ucfirst(str_replace('_', ' ', $type['status'])) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Regional Analysis Section -->
    <div class="section">
        <h2 class="section-title">Regional Analysis</h2>

        @if(isset($regionalAnalysis['regions']) && count($regionalAnalysis['regions']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Region</th>
                    <th style="text-align: center;">Clients</th>
                    <th style="text-align: center;">Terminals</th>
                    <th style="text-align: center;">Service Coverage</th>
                    <th style="text-align: center;">Response Time</th>
                    <th style="text-align: center;">Regional Health</th>
                </tr>
            </thead>
            <tbody>
                @foreach($regionalAnalysis['regions'] as $region)
                <tr>
                    <td><strong>{{ $region['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $region['clients'] }}</td>
                    <td style="text-align: center;">{{ $region['terminals'] }}</td>
                    <td style="text-align: center;">
                        <span class="health-score {{ $region['coverage'] >= 90 ? 'health-excellent' : ($region['coverage'] >= 75 ? 'health-good' : 'health-warning') }}">
                            {{ $region['coverage'] }}%
                        </span>
                    </td>
                    <td style="text-align: center;">{{ $region['response_time'] }}h</td>
                    <td style="text-align: center;">
                        <span class="health-score {{ $region['health_score'] >= 85 ? 'health-excellent' : ($region['health_score'] >= 70 ? 'health-good' : 'health-warning') }}">
                            {{ $region['health_score'] }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by Revival Technologies POS Management System</p>
        <p>For more information, contact your system administrator</p>
        <p>Report generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
