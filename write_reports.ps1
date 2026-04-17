$f = 'C:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views\reports\index.blade.php'
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
$t = [IO.File]::ReadAllText($f, [Text.Encoding]::UTF8)

# Extract tab section (from <!-- Tabbed Content --> to <!-- Chart.js CDN -->)
$tabStart  = $t.IndexOf('<!-- Tabbed Content -->')
$cdnStart  = $t.IndexOf('<!-- Chart.js CDN -->')
$tabRaw    = $t.Substring($tabStart, $cdnStart - $tabStart).TrimEnd()
# Replace .content-card wrapper with .ui-card
$tabRaw    = $tabRaw.Replace('<div class="content-card">', '<div class="ui-card overflow-hidden mt-5">')
# Fix tab-navigation border
$tabRaw    = $tabRaw.Replace('<div class="tab-navigation">', '<div class="tab-navigation border-b border-gray-200 px-5 pt-2">')

# Extract Chart.js CDN line
$cdnLine   = '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>'

# Extract init JS (from last <script> to @endsection)
$initStart = $t.LastIndexOf('<script>')
$initSection = $t.Substring($initStart).Trim()

$htmlPart = @'
@extends('layouts.app')

@section('title', 'System Analytics')

@section('content')
{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title">System Analytics Dashboard</h1>
        <p class="page-subtitle">Comprehensive system performance and analytics</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="exportFullReport()" class="btn-primary">📊 Export Full Report</button>
        <button onclick="printDashboard()" class="btn-secondary">🖨️ Print</button>
    </div>
</div>

{{-- Key Metrics --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-5">
    <div class="stat-card border-l-4 border-blue-500">
        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 text-xl">🏢</div>
        <div>
            <div class="stat-number text-blue-600">{{ $systemOverview['total_clients'] }}</div>
            <div class="stat-label">Total Clients</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $systemOverview['active_clients'] }} active</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-green-500">
        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 text-xl">💻</div>
        <div>
            <div class="stat-number text-green-600">{{ $systemOverview['total_terminals'] }}</div>
            <div class="stat-label">POS Terminals</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $systemOverview['terminal_uptime'] }}% uptime</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-orange-400">
        <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0 text-xl">👥</div>
        <div>
            <div class="stat-number text-orange-500">{{ $systemOverview['total_employees'] }}</div>
            <div class="stat-label">Employees</div>
            <div class="text-xs text-gray-400 mt-0.5">Active workforce</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-purple-500">
        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0 text-xl">📋</div>
        <div>
            <div class="stat-number text-purple-600">{{ $systemOverview['active_projects'] }}</div>
            <div class="stat-label">Active Projects</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $systemOverview['total_projects'] }} total</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-red-500">
        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 text-xl">🎫</div>
        <div>
            <div class="stat-number text-red-600">{{ $systemOverview['open_tickets'] }}</div>
            <div class="stat-label">Open Tickets</div>
            <div class="text-xs text-gray-400 mt-0.5">Need attention</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-teal-500">
        <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center flex-shrink-0 text-xl">💰</div>
        <div>
            <div class="stat-number text-teal-600">${{ number_format($systemOverview['revenue_impact'] / 1000) }}K</div>
            <div class="stat-label">Revenue Impact</div>
            <div class="text-xs text-gray-400 mt-0.5">Monthly estimate</div>
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

<div>
'@

$minimalCSS = @'

<style>
/* Tab navigation & show/hide — required by switchTab() JS */
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

'@

$newContent = $htmlPart + $tabRaw + "`n`n" + $minimalCSS + "`n" + $cdnLine + "`n" + $initSection
[IO.File]::WriteAllText($f, $newContent, $utf8NoBom)
Write-Host "Reports written. Lines: $($newContent.Split("`n").Length)"
