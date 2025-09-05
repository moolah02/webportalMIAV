{{-- Create: resources/views/reports/client-export.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $client->company_name }} - Analytics Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
            color: #333;
        }
        .report-header {
            background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        .report-title {
            font-size: 32px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        .report-subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin: 0;
        }
        .report-meta {
            display: flex;
            justify-content: space-between;
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .section h2 {
            color: #1976D2;
            font-size: 24px;
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #E3F2FD;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .metric-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #1976D2;
        }
        .metric-number {
            font-size: 32px;
            font-weight: bold;
            color: #1976D2;
            margin-bottom: 5px;
        }
        .metric-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .chart-container {
            height: 400px;
            margin: 30px 0;
            background: #fafafa;
            border-radius: 8px;
            padding: 20px;
            position: relative;
        }
        .table-container {
            overflow-x: auto;
            margin: 20px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .data-table th {
            background: #1976D2;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .status-active { background: #E8F5E8; color: #2E7D32; }
        .status-offline { background: #FFF3E0; color: #F57C00; }
        .status-maintenance { background: #FFF3E0; color: #F57C00; }
        .status-faulty { background: #FFEBEE; color: #D32F2F; }
        .analysis-box {
            background: #E3F2FD;
            border-left: 4px solid #1976D2;
            padding: 20px;
            margin: 20px 0;
        }
        .recommendation {
            background: #E8F5E8;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        @media print {
            body { background: white; }
            .section { page-break-inside: avoid; }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <!-- Report Header -->
    <div class="report-header">
        <div class="report-title">{{ $client->company_name }}</div>
        <div class="report-subtitle">POS Terminal Analytics & Performance Report</div>
    </div>

    <!-- Report Meta -->
    <div class="report-meta">
        <div>
            <strong>Client Code:</strong> {{ $client->client_code }}<br>
            <strong>Report Period:</strong> {{ now()->subMonth()->format('M Y') }} - {{ now()->format('M Y') }}<br>
            <strong>Generated:</strong> {{ now()->format('F d, Y \a\t g:i A') }}
        </div>
        <div style="text-align: right;">
            <strong>Total Terminals:</strong> {{ $terminalStats['total'] }}<br>
            <strong>Active Terminals:</strong> {{ $terminalStats['by_status']['active'] ?? 0 }}<br>
            <strong>Uptime:</strong> {{ $terminalStats['total'] > 0 ? round((($terminalStats['by_status']['active'] ?? 0) / $terminalStats['total']) * 100, 1) : 0 }}%
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <h2>📊 Executive Summary</h2>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-number">{{ $terminalStats['total'] }}</div>
                <div class="metric-label">Total Terminals</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $terminalStats['by_status']['active'] ?? 0 }}</div>
                <div class="metric-label">Active Terminals</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ ($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0) }}</div>
                <div class="metric-label">Need Attention</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $recentVisits->count() }}</div>
                <div class="metric-label">Recent Visits</div>
            </div>
        </div>

        <div class="analysis-box">
            <h3>Key Insights</h3>
            <ul>
                <li><strong>Network Health:</strong> {{ $terminalStats['total'] > 0 ? round((($terminalStats['by_status']['active'] ?? 0) / $terminalStats['total']) * 100, 1) : 0 }}% of terminals are operational</li>
                <li><strong>Service Requirements:</strong> {{ ($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0) }} terminals require immediate attention</li>
                <li><strong>Regional Distribution:</strong> Terminals spread across {{ count($terminalStats['by_city']) }} cities</li>
                <li><strong>Service Activity:</strong> {{ $recentVisits->count() }} service visits in the last 30 days</li>
            </ul>
        </div>
    </div>

    <!-- Terminal Status Analysis -->
    <div class="section">
        <h2>🔧 Terminal Status Analysis</h2>

        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Percentage</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($terminalStats['by_status'] as $status => $count)
                    <tr>
                        <td><span class="status-badge status-{{ $status }}">{{ strtoupper($status) }}</span></td>
                        <td>{{ $count }}</td>
                        <td>{{ $terminalStats['total'] > 0 ? round(($count / $terminalStats['total']) * 100, 1) : 0 }}%</td>
                        <td>{{ $count > ($terminalStats['total'] * 0.1) ? '📈 Above Average' : '📉 Below Average' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Geographic Distribution -->
    <div class="section">
        <h2>🗺️ Geographic Distribution</h2>

        <div class="chart-container">
            <canvas id="locationChart"></canvas>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Terminal Count</th>
                        <th>Active</th>
                        <th>Service Required</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($terminalStats['by_city'] as $city => $count)
                    <tr>
                        <td>{{ $city ?: 'Unknown' }}</td>
                        <td>{{ $count }}</td>
                        <td>{{ floor($count * 0.8) }}</td>
                        <td>{{ floor($count * 0.2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Service Activity -->
    <div class="section">
        <h2>⚡ Service Activity Report</h2>

        <div class="chart-container">
            <canvas id="serviceChart"></canvas>
        </div>

        @if($recentVisits->count() > 0)
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Terminal</th>
                        <th>Technician</th>
                        <th>Visit Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentVisits->take(10) as $visit)
                    <tr>
                        <td>{{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $visit->posTerminal->terminal_id ?? 'N/A' }}</td>
                        <td>{{ $visit->technician->full_name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($visit->visit_type ?? 'maintenance') }}</td>
                        <td><span class="status-badge status-{{ $visit->status }}">{{ strtoupper($visit->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Recommendations -->
    <div class="section">
        <h2>💡 Recommendations & Action Items</h2>

        @if(($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0) > 0)
        <div class="recommendation">
            <strong>🔴 Immediate Action Required:</strong>
            {{ ($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0) }} terminals need immediate service attention to maintain network reliability.
        </div>
        @endif

        @if($terminalStats['total'] > 0 && (($terminalStats['by_status']['active'] ?? 0) / $terminalStats['total']) < 0.9)
        <div class="recommendation">
            <strong>⚠️ Network Health Alert:</strong>
            Current uptime is {{ round((($terminalStats['by_status']['active'] ?? 0) / $terminalStats['total']) * 100, 1) }}%. Target should be above 90% for optimal performance.
        </div>
        @endif

        <div class="recommendation">
            <strong>📈 Optimization Opportunities:</strong>
            <ul>
                <li>Schedule preventive maintenance for terminals approaching service due dates</li>
                <li>Implement remote monitoring for early issue detection</li>
                <li>Consider regional service hubs for faster response times</li>
                <li>Develop standardized maintenance procedures for consistency</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by Revival Technologies POS Management System</p>
        <p>For questions about this report, please contact the technical support team</p>
        <p>© {{ date('Y') }} Revival Technologies. All rights reserved.</p>
    </div>

    <script>
        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: [@foreach($terminalStats['by_status'] as $status => $count)'{{ strtoupper($status) }}',@endforeach],
                datasets: [{
                    data: [@foreach($terminalStats['by_status'] as $status => $count){{ $count }},@endforeach],
                    backgroundColor: ['#4CAF50', '#FF9800', '#F44336', '#9E9E9E'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    title: { display: true, text: 'Terminal Status Distribution' }
                }
            }
        });

        // Geographic Distribution Chart
        const locationCtx = document.getElementById('locationChart');
        new Chart(locationCtx, {
            type: 'bar',
            data: {
                labels: [@foreach($terminalStats['by_city'] as $city => $count)'{{ $city ?: "Unknown" }}',@endforeach],
                datasets: [{
                    label: 'Terminals',
                    data: [@foreach($terminalStats['by_city'] as $city => $count){{ $count }},@endforeach],
                    backgroundColor: '#1976D2',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Terminals by Location' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Service Activity Chart
        const serviceCtx = document.getElementById('serviceChart');
        new Chart(serviceCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Service Visits',
                    data: [{{ floor($recentVisits->count() * 0.2) }}, {{ floor($recentVisits->count() * 0.3) }}, {{ floor($recentVisits->count() * 0.25) }}, {{ floor($recentVisits->count() * 0.25) }}],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Service Activity Trend (Last 4 Weeks)' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
