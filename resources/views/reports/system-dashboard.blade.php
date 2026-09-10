@extends('layouts.app')
@section('title', 'System Dashboard')

@push('styles')
<style>
    .sd { display: grid; gap: 20px; }

    /* Toolbar: context left, actions right */
    .sd-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .sd-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 14px; font-size: 13px; color: var(--mv-muted); }
    .sd-meta strong { color: var(--mv-ink); font-weight: 600; font-variant-numeric: tabular-nums; }
    .sd-meta .sd-dot { width: 3px; height: 3px; border-radius: 50%; background: var(--mv-line-strong); }
    .sd-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .sd-actions button { display: inline-flex; align-items: center; gap: 8px; }
    .sd-actions .mv-i { width: 16px; height: 16px; }

    /* KPI row */
    .sd-kpis { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; }
    .sd-kpi { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 14px 16px; min-width: 0; }
    .sd-kpi-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12.5px; font-weight: 500; color: var(--mv-muted); }
    .sd-kpi-top .mv-i { width: 16px; height: 16px; color: var(--mv-muted); }
    .sd-kpi-value { margin-top: 6px; font-size: 24px; font-weight: 600; letter-spacing: -.02em; line-height: 1.15; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
    .sd-kpi-sub { margin-top: 2px; font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }

    /* Cards */
    .sd-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; min-width: 0; }
    .sd-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 13px 18px; border-bottom: 1px solid var(--mv-line); }
    .sd-card-head h3 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .sd-card-head .sd-note { font-size: 12.5px; color: var(--mv-muted); }
    .sd-card-head a { display: inline-flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; }
    .sd-card-head a:hover { text-decoration: underline; }
    .sd-card-body { padding: 16px 18px; }
    .sd-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }

    /* Health */
    .sd-health { display: grid; grid-template-columns: 200px minmax(0, 1fr); gap: 28px; align-items: center; }
    .sd-ring { position: relative; width: 148px; height: 148px; margin: 0 auto; }
    .sd-ring canvas { width: 148px !important; height: 148px !important; }
    .sd-ring-copy { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; }
    .sd-ring-value { font-size: 26px; font-weight: 600; letter-spacing: -.02em; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
    .sd-ring-label { font-size: 12px; color: var(--mv-muted); }
    .sd-bars { display: grid; gap: 14px; }
    .sd-bar-row { display: grid; grid-template-columns: minmax(110px, 170px) minmax(0, 1fr) 64px; gap: 14px; align-items: center; font-size: 13.5px; }
    .sd-bar-label { color: var(--mv-ink-2); min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sd-track { height: 8px; border-radius: 4px; background: #EDF0F4; overflow: hidden; }
    .sd-fill { height: 100%; border-radius: 4px; background: var(--mv-accent); }
    .sd-fill.is-good { background: var(--mv-good); }
    .sd-fill.is-warn { background: #C28A2C; }
    .sd-fill.is-crit { background: var(--mv-crit); }
    .sd-bar-value { text-align: right; font-weight: 500; color: var(--mv-ink); font-variant-numeric: tabular-nums; white-space: nowrap; }

    /* Action items */
    .sd-alert { display: flex; align-items: center; gap: 12px; padding: 12px 18px; }
    .sd-alert + .sd-alert { border-top: 1px solid var(--mv-line); }
    .sd-alert-ic { width: 30px; height: 30px; border-radius: 8px; display: grid; place-items: center; flex-shrink: 0; }
    .sd-alert-ic .mv-i { width: 16px; height: 16px; }
    .sd-alert-ic.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); }
    .sd-alert-ic.is-warn { background: var(--mv-warn-soft); color: var(--mv-warn); }
    .sd-alert-text { flex: 1; min-width: 0; }
    .sd-alert-title { font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
    .sd-alert-desc { font-size: 12.5px; color: var(--mv-muted); }
    .sd-alert-link { display: inline-flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; white-space: nowrap; }
    .sd-alert-link:hover { text-decoration: underline; }

    /* Charts */
    .sd-chart { position: relative; height: 240px; }
    .sd-chart canvas { display: block; }
    .sd-empty { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; font-size: 13px; color: var(--mv-muted); text-align: center; }
    .sd-empty .mv-i { width: 26px; height: 26px; color: var(--mv-line-strong); }
    .sd-empty-inline { padding: 24px 0; display: flex; flex-direction: column; align-items: center; gap: 6px; font-size: 13px; color: var(--mv-muted); }
    .sd-empty-inline .mv-i { width: 24px; height: 24px; color: var(--mv-line-strong); }

    /* Table */
    .sd .ui-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .sd .ui-table th { text-align: left; white-space: nowrap; }
    .sd .ui-table td.sd-title { max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sd .ui-table td.sd-title a { color: var(--mv-ink); font-weight: 500; text-decoration: none; }
    .sd .ui-table td.sd-title a:hover { color: var(--mv-accent-ink); text-decoration: underline; }
    .sd .ui-table .sd-muted { color: var(--mv-muted); white-space: nowrap; }

    /* Tabs (class names are used by switchTab) */
    .tab-navigation { display: flex; gap: 2px; padding: 0 10px; border-bottom: 1px solid var(--mv-line); overflow-x: auto; }
    .tab-button {
        display: inline-flex; align-items: center; gap: 7px; padding: 12px 10px; margin-bottom: -1px;
        border: 0; border-bottom: 2px solid transparent; background: transparent; cursor: pointer;
        font: inherit; font-size: 13.5px; font-weight: 500; color: var(--mv-muted); white-space: nowrap;
    }
    .tab-button .mv-i { width: 16px; height: 16px; }
    .tab-button:hover { color: var(--mv-ink); }
    .tab-button.active { color: var(--mv-accent-ink); border-bottom-color: var(--mv-accent); }
    .tab-content { display: none; padding: 18px; }
    .tab-content.active { display: block; }
    .sd-sec-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .sd-sec-head h3 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
    .sd-sec-head .btn-secondary { display: inline-flex; align-items: center; gap: 6px; }
    .sd-sec-head .btn-secondary .mv-i { width: 15px; height: 15px; }
    .sd-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(360px, 100%), 1fr)); gap: 16px; }
    .sd-panel { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 14px 16px; min-width: 0; }
    .sd-panel h4 { margin: 0 0 12px; font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
    .sd-panel .sd-chart { height: 260px; }
    .sd-panel-wide { grid-column: 1 / -1; }

    /* Figures (small numbers with labels) */
    .sd-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1px; background: var(--mv-line); border: 1px solid var(--mv-line); border-radius: 8px; overflow: hidden; }
    .sd-fig { background: var(--mv-surface); padding: 12px 14px; }
    .sd-fig-value { font-size: 20px; font-weight: 600; letter-spacing: -.01em; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
    .sd-fig-label { font-size: 12.5px; color: var(--mv-muted); }
    .sd-fig-sub { margin-top: 2px; font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
    .sd-fig-sub.is-up { color: var(--mv-good); }
    .sd-fig-sub.is-down { color: var(--mv-crit); }

    /* Row lists */
    .sd-rows { display: flex; flex-direction: column; }
    .sd-rows.is-scroll { max-height: 272px; overflow-y: auto; padding-right: 4px; }
    .sd-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 9px 0; border-top: 1px solid var(--mv-line); font-size: 13.5px; }
    .sd-row:first-child { border-top: 0; padding-top: 2px; }
    .sd-row-main { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .sd-row-name { color: var(--mv-ink); font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sd-row-meta { color: var(--mv-muted); font-size: 12.5px; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .sd-row-meta strong { color: var(--mv-ink); font-weight: 600; }
    .sd-panel .sd-bar-row { grid-template-columns: minmax(90px, 150px) minmax(0, 1fr) auto; font-size: 13px; }

    /* Notes / recommendations */
    .sd-notes { margin: 0; padding: 0; list-style: none; display: grid; gap: 8px; }
    .sd-notes li { display: flex; gap: 10px; font-size: 13.5px; color: var(--mv-ink-2); line-height: 1.5; }
    .sd-notes li::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: var(--mv-muted); margin-top: 8px; flex-shrink: 0; }
    .sd-notes strong { color: var(--mv-ink); font-weight: 600; }

    /* Status rows (service requirements, project health, stock alerts) */
    .sd-status { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-top: 1px solid var(--mv-line); }
    .sd-status:first-of-type { border-top: 0; padding-top: 0; }
    .sd-status .sd-alert-ic.is-good { background: var(--mv-good-soft); color: var(--mv-good); }
    .sd-status-value { font-size: 18px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; min-width: 48px; }
    .sd-status-text { font-size: 13px; color: var(--mv-ink-2); }
    .sd-status-text span { display: block; font-size: 12.5px; color: var(--mv-muted); }

    @media (max-width: 1280px) { .sd-kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 1100px) { .sd-2 { grid-template-columns: minmax(0, 1fr); } .sd-health { grid-template-columns: minmax(0, 1fr); } }
    @media (max-width: 640px) { .sd-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } .sd-bar-row { grid-template-columns: minmax(0, 1fr) 56px; } .sd-bar-row .sd-track { grid-column: 1 / -1; grid-row: 2; } }
    @media print { .sd-actions, .tab-navigation { display: none !important; } .tab-content { display: block !important; } }
</style>
@endpush

@section('content')
@php
    $score = $systemOverview['system_health_score'];
    $tone = fn ($v) => $v >= 80 ? 'is-good' : ($v >= 60 ? 'is-warn' : 'is-crit');
    $visitsNow  = $systemOverview['total_visits_this_month'];
    $visitsPrev = $systemOverview['total_visits_last_month'];
@endphp
<div class="sd">

    {{-- Toolbar --}}
    <div class="sd-toolbar">
        <div class="sd-meta">
            <span>Generated {{ $generatedAt->format('M d, Y \a\t h:i A') }}</span>
            <span class="sd-dot" aria-hidden="true"></span>
            <span>Health score <strong>{{ $score }}%</strong></span>
            <span class="sd-dot" aria-hidden="true"></span>
            <span><strong>{{ $systemOverview['open_tickets'] }}</strong> open support tickets</span>
            <span class="sd-dot" aria-hidden="true"></span>
            <span><strong>{{ $assetData['low_stock_alerts'] }}</strong> low stock alerts</span>
        </div>
        <div class="sd-actions">
            <button onclick="exportFullReport()" class="btn-primary">
                <svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Full Report
            </button>
            <button onclick="printDashboard()" class="btn-secondary">
                <svg class="mv-i" aria-hidden="true"><use href="#i-printer"/></svg> Print
            </button>
        </div>
    </div>

    {{-- Key metrics --}}
    <div class="sd-kpis">
        <div class="sd-kpi">
            <div class="sd-kpi-top">Total Clients <svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg></div>
            <div class="sd-kpi-value">{{ number_format($systemOverview['total_clients']) }}</div>
            <div class="sd-kpi-sub">{{ $systemOverview['active_clients'] }} active</div>
        </div>
        <div class="sd-kpi">
            <div class="sd-kpi-top">POS Terminals <svg class="mv-i" aria-hidden="true"><use href="#i-monitor"/></svg></div>
            <div class="sd-kpi-value">{{ number_format($systemOverview['total_terminals']) }}</div>
            <div class="sd-kpi-sub">{{ $systemOverview['terminal_uptime'] }}% uptime</div>
        </div>
        <div class="sd-kpi">
            <div class="sd-kpi-top">Employees <svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg></div>
            <div class="sd-kpi-value">{{ number_format($systemOverview['total_employees']) }}</div>
            <div class="sd-kpi-sub">Active workforce</div>
        </div>
        <div class="sd-kpi">
            <div class="sd-kpi-top">Active Projects <svg class="mv-i" aria-hidden="true"><use href="#i-folder"/></svg></div>
            <div class="sd-kpi-value">{{ number_format($systemOverview['active_projects']) }}</div>
            <div class="sd-kpi-sub">{{ $systemOverview['total_projects'] }} total</div>
        </div>
        <div class="sd-kpi">
            <div class="sd-kpi-top">Open Tickets <svg class="mv-i" aria-hidden="true"><use href="#i-ticket"/></svg></div>
            <div class="sd-kpi-value">{{ number_format($systemOverview['open_tickets']) }}</div>
            <div class="sd-kpi-sub">Need attention</div>
        </div>
        <div class="sd-kpi">
            <div class="sd-kpi-top">Revenue Impact <svg class="mv-i" aria-hidden="true"><use href="#i-banknote"/></svg></div>
            <div class="sd-kpi-value">${{ number_format($systemOverview['revenue_impact'] / 1000) }}K</div>
            <div class="sd-kpi-sub">Monthly estimate</div>
        </div>
    </div>

    {{-- System health --}}
    <div class="sd-card">
        <div class="sd-card-head">
            <h3>System Health Overview</h3>
            <span class="badge {{ $score >= 80 ? 'badge-green' : ($score >= 60 ? 'badge-yellow' : 'badge-red') }}">{{ $score }}% Health</span>
        </div>
        <div class="sd-card-body">
            <div class="sd-health">
                <div class="sd-ring">
                    <canvas id="healthScoreChart" width="148" height="148"></canvas>
                    <div class="sd-ring-copy">
                        <div class="sd-ring-value">{{ $score }}%</div>
                        <div class="sd-ring-label">Health score</div>
                    </div>
                </div>
                <div class="sd-bars">
                    @foreach([
                        ['Terminal Uptime', $systemOverview['terminal_uptime']],
                        ['Asset Utilization', $assetData['asset_utilization']],
                        ['Project Completion', $projectData['project_completion_rate']],
                    ] as [$label, $value])
                    <div class="sd-bar-row">
                        <span class="sd-bar-label">{{ $label }}</span>
                        <div class="sd-track"><div class="sd-fill {{ $tone($value) }}" style="width: {{ min(100, max(0, $value)) }}%;"></div></div>
                        <span class="sd-bar-value">{{ $value }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Action items --}}
    @if(count($alerts) > 0)
    @php
        $alertIcons = ['View Terminals' => 'monitor', 'View Tickets' => 'ticket', 'View Assets' => 'box', 'View Licenses' => 'file-check', 'View Projects' => 'folder'];
    @endphp
    <div class="sd-card">
        <div class="sd-card-head">
            <h3>Action Items</h3>
            <span class="sd-note">{{ count($alerts) }} item(s) need attention</span>
        </div>
        <div>
            @foreach($alerts as $alert)
            @php
                $alertIcon = preg_match('/^[a-z][a-z-]*$/', (string) ($alert['icon'] ?? ''))
                    ? $alert['icon']
                    : ($alertIcons[$alert['label'] ?? ''] ?? 'alert-triangle');
            @endphp
            <div class="sd-alert">
                <span class="sd-alert-ic {{ $alert['type'] === 'danger' ? 'is-crit' : 'is-warn' }}"><svg class="mv-i" aria-hidden="true"><use href="#i-{{ $alertIcon }}"/></svg></span>
                <div class="sd-alert-text">
                    <div class="sd-alert-title">{{ $alert['title'] }}</div>
                    <div class="sd-alert-desc">{{ $alert['desc'] }}</div>
                </div>
                <a href="{{ $alert['link'] }}" class="sd-alert-link">{{ $alert['label'] }} <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-right"/></svg></a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Ticket trend + visits trend --}}
    <div class="sd-2">
        <div class="sd-card">
            <div class="sd-card-head"><h3>Ticket Trend (6 Months)</h3></div>
            <div class="sd-card-body"><div class="sd-chart"><canvas id="ticketTrendChart"></canvas></div></div>
        </div>
        <div class="sd-card">
            <div class="sd-card-head"><h3>Service Visits (6 Months)</h3></div>
            <div class="sd-card-body"><div class="sd-chart"><canvas id="visitsTrendChart"></canvas></div></div>
        </div>
    </div>

    {{-- Recent tickets --}}
    <div class="sd-card">
        <div class="sd-card-head">
            <h3>Recent Tickets</h3>
            <a href="{{ route('tickets.index') }}">View All <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-right"/></svg></a>
        </div>
        <div style="overflow-x:auto;">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Assigned To</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTickets as $ticket)
                @php
                    $statusBadge = ['open' => 'badge-blue', 'in_progress' => 'badge-yellow', 'on_hold' => 'badge-gray', 'resolved' => 'badge-green', 'closed' => 'badge-gray', 'cancelled' => 'badge-red'][$ticket->status] ?? 'badge-gray';
                    $priorityBadge = ['low' => 'badge-gray', 'medium' => 'badge-blue', 'high' => 'badge-yellow', 'urgent' => 'badge-red', 'critical' => 'badge-red'][$ticket->priority] ?? 'badge-gray';
                @endphp
                <tr>
                    <td class="sd-muted mv-mono">#{{ $ticket->id }}</td>
                    <td class="sd-title"><a href="{{ route('tickets.show', $ticket->id) }}">{{ Str::limit($ticket->title, 60) }}</a></td>
                    <td>{{ optional($ticket->client)->company_name ?? '—' }}</td>
                    <td>{{ $ticket->assignedTo ? $ticket->assignedTo->first_name . ' ' . $ticket->assignedTo->last_name : '—' }}</td>
                    <td><span class="badge {{ $priorityBadge }}">{{ ucfirst($ticket->priority ?? 'normal') }}</span></td>
                    <td><span class="badge {{ $statusBadge }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
                    <td class="sd-muted">{{ $ticket->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="sd-empty-inline"><svg class="mv-i" aria-hidden="true"><use href="#i-ticket"/></svg>No tickets found.</div></td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Tabbed analytics --}}
    <div class="sd-card">
        <div class="tab-navigation" role="tablist">
            <button class="tab-button active" onclick="switchTab(event, 'overview')"><svg class="mv-i" aria-hidden="true"><use href="#i-chart"/></svg> System Overview</button>
            <button class="tab-button" onclick="switchTab(event, 'clients')"><svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg> Client Analytics</button>
            <button class="tab-button" onclick="switchTab(event, 'terminals')"><svg class="mv-i" aria-hidden="true"><use href="#i-monitor"/></svg> Terminal Management</button>
            <button class="tab-button" onclick="switchTab(event, 'service')"><svg class="mv-i" aria-hidden="true"><use href="#i-wrench"/></svg> Service Activity</button>
            <button class="tab-button" onclick="switchTab(event, 'assets')"><svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg> Asset Management</button>
            <button class="tab-button" onclick="switchTab(event, 'employees')"><svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg> Employee Performance</button>
            <button class="tab-button" onclick="switchTab(event, 'projects')"><svg class="mv-i" aria-hidden="true"><use href="#i-folder"/></svg> Project Management</button>
            <button class="tab-button" onclick="switchTab(event, 'regional')"><svg class="mv-i" aria-hidden="true"><use href="#i-map"/></svg> Regional Analysis</button>
        </div>

        {{-- Overview --}}
        <div id="overview" class="tab-content active">
            <div class="sd-sec-head"><h3>System Overview &amp; Key Metrics</h3></div>
            <div class="sd-grid">
                <div class="sd-panel">
                    <h4>Monthly Visit Trends</h4>
                    <div class="sd-chart"><canvas id="visitTrendsChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>System Performance Indicators</h4>
                    <div class="sd-figures">
                        <div class="sd-fig">
                            <div class="sd-fig-value">{{ $visitsNow }}</div>
                            <div class="sd-fig-label">Visits This Month</div>
                            <div class="sd-fig-sub {{ $visitsNow > $visitsPrev ? 'is-up' : ($visitsNow < $visitsPrev ? 'is-down' : '') }}">
                                {{ $visitsNow >= $visitsPrev ? '+' : '−' }}{{ abs($visitsNow - $visitsPrev) }} vs last month
                            </div>
                        </div>
                        <div class="sd-fig">
                            <div class="sd-fig-value">{{ $assetData['total_assets'] }}</div>
                            <div class="sd-fig-label">Total Assets</div>
                            <div class="sd-fig-sub">{{ $assetData['assignment_status']['assigned'] }} assigned</div>
                        </div>
                        <div class="sd-fig">
                            <div class="sd-fig-value">{{ number_format($serviceActivity['average_resolution_time'], 1) }}h</div>
                            <div class="sd-fig-label">Avg Resolution Time</div>
                            <div class="sd-fig-sub">Ticket resolution</div>
                        </div>
                    </div>

                    <h4 style="margin-top:18px;">Key Recommendations</h4>
                    <ul class="sd-notes">
                        @if($systemOverview['terminal_uptime'] < 90)
                        <li><span><strong>Terminal Health:</strong> System uptime is {{ $systemOverview['terminal_uptime'] }}%. Consider increasing maintenance frequency for terminals in maintenance/faulty status.</span></li>
                        @endif
                        @if($systemOverview['open_tickets'] > 5)
                        <li><span><strong>Support Queue:</strong> {{ $systemOverview['open_tickets'] }} open tickets require attention to maintain service quality.</span></li>
                        @endif
                        @if($assetData['low_stock_alerts'] > 0)
                        <li><span><strong>Inventory Alert:</strong> {{ $assetData['low_stock_alerts'] }} assets are below minimum stock levels and need replenishment.</span></li>
                        @endif
                        <li><span><strong>Growth Opportunity:</strong> Consider expanding service coverage to improve regional distribution and reduce technician workload.</span></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Clients --}}
        <div id="clients" class="tab-content">
            <div class="sd-sec-head">
                <h3>Client Analytics &amp; Performance</h3>
                <button onclick="exportSection('clients')" class="btn-secondary btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Client Data</button>
            </div>
            <div class="sd-grid">
                <div class="sd-panel">
                    <h4>Client Distribution by Status</h4>
                    <div class="sd-chart"><canvas id="clientStatusChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Top Clients by Terminal Count</h4>
                    @php $clientBadge = ['active' => 'badge-green', 'prospect' => 'badge-yellow', 'lost' => 'badge-red']; @endphp
                    <div class="sd-rows is-scroll">
                        @forelse($clientAnalytics['client_terminal_counts'] as $client)
                        <div class="sd-row">
                            <div class="sd-row-main">
                                <span class="sd-row-name">{{ $client['name'] }}</span>
                                <span class="badge {{ $clientBadge[$client['status']] ?? 'badge-gray' }}">{{ ucfirst($client['status']) }}</span>
                            </div>
                            <span class="sd-row-meta"><strong>{{ $client['terminal_count'] }}</strong> terminals ({{ $client['active_terminals'] }} active)</span>
                        </div>
                        @empty
                        <div class="sd-empty-inline"><svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg>No clients yet.</div>
                        @endforelse
                    </div>
                </div>
                <div class="sd-panel sd-panel-wide">
                    <h4>Client Activity Analysis</h4>
                    <div class="sd-chart"><canvas id="clientActivityChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Terminals --}}
        <div id="terminals" class="tab-content">
            <div class="sd-sec-head">
                <h3>Terminal Management &amp; Health</h3>
                <button onclick="exportSection('terminals')" class="btn-secondary btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Terminal Data</button>
            </div>
            <div class="sd-grid">
                <div class="sd-panel">
                    <h4>Terminal Status Distribution</h4>
                    <div class="sd-chart"><canvas id="terminalStatusChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Device Model Distribution</h4>
                    <div class="sd-chart"><canvas id="terminalModelsChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Geographic Distribution</h4>
                    <div class="sd-chart"><canvas id="terminalRegionChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Service Requirements</h4>
                    <div class="sd-status">
                        <span class="sd-alert-ic is-crit"><svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg></span>
                        <span class="sd-status-value">{{ $terminalData['terminals_needing_service'] }}</span>
                        <div class="sd-status-text">Terminals Need Service<span>Immediate attention required for optimal performance</span></div>
                    </div>
                    @if(isset($terminalData['service_due_analysis']))
                    <div class="sd-status">
                        <span class="sd-alert-ic is-warn"><svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg></span>
                        <span class="sd-status-value">{{ $terminalData['service_due_analysis']['due_this_week'] ?? 0 }}</span>
                        <div class="sd-status-text">Due This Week<span>Schedule maintenance to prevent issues</span></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Service --}}
        <div id="service" class="tab-content">
            <div class="sd-sec-head">
                <h3>Service Activity &amp; Performance</h3>
                <button onclick="exportSection('service')" class="btn-secondary btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Service Data</button>
            </div>
            <div class="sd-grid">
                <div class="sd-panel">
                    <h4>Visits by Technician (Last 30 Days)</h4>
                    <div class="sd-chart"><canvas id="technicianVisitsChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Ticket Priority Distribution</h4>
                    <div class="sd-chart"><canvas id="ticketPriorityChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Job Assignment Status</h4>
                    <div class="sd-chart"><canvas id="jobStatusChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Technician Productivity</h4>
                    <div class="sd-bars">
                        @forelse($serviceActivity['technician_productivity'] as $tech)
                        <div class="sd-bar-row">
                            <span class="sd-bar-label">{{ $tech['name'] }}</span>
                            <div class="sd-track"><div class="sd-fill" style="width: {{ min(100, max(0, $tech['productivity_score'])) }}%;"></div></div>
                            <span class="sd-bar-value">{{ $tech['visits'] }} visits</span>
                        </div>
                        @empty
                        <div class="sd-empty-inline"><svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg>No technician activity yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Assets --}}
        <div id="assets" class="tab-content">
            <div class="sd-sec-head">
                <h3>Asset Management &amp; Utilization</h3>
                <button onclick="exportSection('assets')" class="btn-secondary btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Asset Data</button>
            </div>
            <div class="sd-grid">
                <div class="sd-panel">
                    <h4>Assets by Category</h4>
                    <div class="sd-chart"><canvas id="assetCategoryChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Asset Utilization Overview</h4>
                    <div class="sd-figures">
                        <div class="sd-fig"><div class="sd-fig-value">{{ $assetData['assignment_status']['total_stock'] }}</div><div class="sd-fig-label">Total Stock</div></div>
                        <div class="sd-fig"><div class="sd-fig-value">{{ $assetData['assignment_status']['assigned'] }}</div><div class="sd-fig-label">Currently Assigned</div></div>
                        <div class="sd-fig"><div class="sd-fig-value">{{ $assetData['assignment_status']['available'] }}</div><div class="sd-fig-label">Available</div></div>
                        <div class="sd-fig"><div class="sd-fig-value">{{ $assetData['asset_utilization'] }}%</div><div class="sd-fig-label">Utilization Rate</div></div>
                    </div>
                    @if($assetData['low_stock_alerts'] > 0)
                    <div class="sd-status" style="margin-top:14px;">
                        <span class="sd-alert-ic is-warn"><svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg></span>
                        <div class="sd-status-text" style="flex:1;">Stock Alerts<span>{{ $assetData['low_stock_alerts'] }} assets are below minimum stock levels and require immediate attention.</span></div>
                        <a href="{{ route('assets.low-stock-alerts') }}" class="btn-secondary btn-sm">View Low Stock Items</a>
                    </div>
                    @endif
                </div>
                <div class="sd-panel">
                    <h4>Most Requested Assets</h4>
                    <div class="sd-rows">
                        @forelse($assetData['top_requested_assets'] as $asset => $count)
                        <div class="sd-row">
                            <span class="sd-row-name">{{ $asset }}</span>
                            <span class="sd-row-meta"><strong>{{ $count }}</strong> requests</span>
                        </div>
                        @empty
                        <div class="sd-empty-inline"><svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg>No asset requests yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Employees --}}
        <div id="employees" class="tab-content">
            <div class="sd-sec-head">
                <h3>Employee Performance &amp; Analytics</h3>
                <button onclick="exportSection('employees')" class="btn-secondary btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Employee Data</button>
            </div>
            <div class="sd-grid">
                <div class="sd-panel">
                    <h4>Employees by Department</h4>
                    <div class="sd-chart"><canvas id="employeeDeptChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Employee Roles Distribution</h4>
                    <div class="sd-chart"><canvas id="employeeRoleChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Current Technician Workload</h4>
                    <div class="sd-bars">
                        @forelse($employeeData['technician_workload'] as $tech => $assignments)
                        <div class="sd-bar-row">
                            <span class="sd-bar-label">{{ $tech }}</span>
                            <div class="sd-track"><div class="sd-fill" style="width: {{ min(100, ($assignments / 5) * 100) }}%;"></div></div>
                            <span class="sd-bar-value">{{ $assignments }} assignments</span>
                        </div>
                        @empty
                        <div class="sd-empty-inline"><svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg>No open assignments.</div>
                        @endforelse
                    </div>
                </div>
                <div class="sd-panel">
                    <h4>Workforce Overview</h4>
                    <div class="sd-figures">
                        <div class="sd-fig"><div class="sd-fig-value">{{ $employeeData['total_employees'] }}</div><div class="sd-fig-label">Active Employees</div></div>
                        <div class="sd-fig"><div class="sd-fig-value">{{ $employeeData['employee_asset_assignments'] }}</div><div class="sd-fig-label">Asset Assignments</div></div>
                        <div class="sd-fig"><div class="sd-fig-value">{{ $employeeData['recent_hires'] }}</div><div class="sd-fig-label">Recent Hires (3mo)</div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Projects --}}
        <div id="projects" class="tab-content">
            <div class="sd-sec-head">
                <h3>Project Management &amp; Progress</h3>
                <button onclick="exportSection('projects')" class="btn-secondary btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Project Data</button>
            </div>
            <div class="sd-grid">
                <div class="sd-panel">
                    <h4>Projects by Status</h4>
                    <div class="sd-chart"><canvas id="projectStatusChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Project Types Distribution</h4>
                    <div class="sd-chart"><canvas id="projectTypeChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Project Health Indicators</h4>
                    <div class="sd-status">
                        <span class="sd-alert-ic is-good"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></span>
                        <span class="sd-status-value">{{ $projectData['project_completion_rate'] }}%</span>
                        <div class="sd-status-text">Completion Rate</div>
                    </div>
                    @if(isset($projectData['overdue_projects']) && $projectData['overdue_projects'] > 0)
                    <div class="sd-status">
                        <span class="sd-alert-ic is-crit"><svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg></span>
                        <span class="sd-status-value">{{ $projectData['overdue_projects'] }}</span>
                        <div class="sd-status-text">Overdue Projects</div>
                    </div>
                    @endif
                    @if(isset($projectData['upcoming_deadlines']) && $projectData['upcoming_deadlines'] > 0)
                    <div class="sd-status">
                        <span class="sd-alert-ic is-warn"><svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg></span>
                        <span class="sd-status-value">{{ $projectData['upcoming_deadlines'] }}</span>
                        <div class="sd-status-text">Due in 30 Days</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Regional --}}
        <div id="regional" class="tab-content">
            <div class="sd-sec-head">
                <h3>Regional Analysis &amp; Coverage</h3>
                <button onclick="exportSection('regional')" class="btn-secondary btn-sm"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export Regional Data</button>
            </div>
            <div class="sd-grid">
                <div class="sd-panel">
                    <h4>Terminal Distribution by Region</h4>
                    <div class="sd-chart"><canvas id="regionalTerminalsChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Service Activity by Region</h4>
                    <div class="sd-chart"><canvas id="regionalServiceChart"></canvas></div>
                </div>
                <div class="sd-panel">
                    <h4>Regional Health Scores</h4>
                    <div class="sd-bars">
                        @forelse($regionalData['regional_health_scores'] as $region => $regionScore)
                        <div class="sd-bar-row">
                            <span class="sd-bar-label">{{ $region }}</span>
                            <div class="sd-track"><div class="sd-fill {{ $tone($regionScore) }}" style="width: {{ min(100, max(0, $regionScore)) }}%;"></div></div>
                            <span class="sd-bar-value">{{ $regionScore }}%</span>
                        </div>
                        @empty
                        <div class="sd-empty-inline"><svg class="mv-i" aria-hidden="true"><use href="#i-map"/></svg>No regional data yet.</div>
                        @endforelse
                    </div>
                </div>
                <div class="sd-panel">
                    <h4>Coverage Analysis</h4>
                    <div class="sd-figures">
                        <div class="sd-fig"><div class="sd-fig-value">{{ $regionalData['coverage_analysis']['total_cities'] }}</div><div class="sd-fig-label">Cities Covered</div></div>
                        <div class="sd-fig"><div class="sd-fig-value">{{ $regionalData['coverage_analysis']['covered_regions'] }}</div><div class="sd-fig-label">Active Regions</div></div>
                        <div class="sd-fig"><div class="sd-fig-value">{{ $regionalData['coverage_analysis']['terminals_per_technician'] }}</div><div class="sd-fig-label">Terminals/Technician</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// ── Chart styling shared by every chart on this page ──────────────────────
const MV = {
    accent: '#2B64A8', neutral: '#9AA6B4', light: '#CBD3DD',
    good: '#1D7F46', warn: '#C28A2C', crit: '#B83232',
    grid: '#E1E6EC', tick: '#6A7686', ink: '#16202C'
};

if (window.Chart) {
    Chart.defaults.font.family = '"IBM Plex Sans", "Segoe UI", system-ui, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = MV.tick;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyle = 'rect';
    Chart.defaults.plugins.legend.labels.boxWidth = 8;
    Chart.defaults.plugins.legend.labels.boxHeight = 8;
    Chart.defaults.plugins.legend.labels.padding = 14;
    Chart.defaults.plugins.tooltip.backgroundColor = MV.ink;
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 6;
    Chart.defaults.plugins.tooltip.boxPadding = 4;
}

const mvCatAxis   = { grid: { display: false, drawBorder: false }, ticks: { color: MV.tick, autoSkip: true, maxRotation: 0 } };
const mvValueAxis = { beginAtZero: true, grid: { color: MV.grid, drawBorder: false }, ticks: { color: MV.tick, precision: 0 } };

const pretty = s => String(s ?? '—').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

function mvHasData(values) {
    return Array.isArray(values) && values.some(v => Number(v) > 0);
}

function mvEmpty(canvas, text) {
    if (!canvas) return;
    canvas.style.display = 'none';
    const box = canvas.parentElement;
    if (box.querySelector('.sd-empty')) return;
    const el = document.createElement('div');
    el.className = 'sd-empty';
    el.innerHTML = '<svg class="mv-i" aria-hidden="true"><use href="#i-chart"/></svg><span></span>';
    el.querySelector('span').textContent = text || 'No data for this period yet';
    box.appendChild(el);
}

// Colour by meaning for status-like keys; unknown keys stay neutral.
function mvStatusColor(key, map) {
    const k = String(key ?? '').toLowerCase().replace(/[\s-]+/g, '_');
    return map[k] || MV.light;
}
const TERMINAL_STATUS = { active: MV.good, online: MV.good, working: MV.good, maintenance: MV.warn, needs_attention: MV.warn, pending: MV.warn, faulty: MV.crit, offline: MV.crit, not_working: MV.crit, decommissioned: MV.neutral, inactive: MV.light };
const CLIENT_STATUS   = { active: MV.good, prospect: MV.neutral, inactive: MV.light, lost: MV.crit };
const PRIORITY        = { urgent: MV.crit, critical: MV.crit, high: MV.warn, medium: MV.neutral, low: MV.light };
const JOB_STATUS      = { completed: MV.good, assigned: MV.accent, in_progress: MV.accent, scheduled: MV.accent, pending: MV.warn, on_hold: MV.warn, cancelled: MV.crit, failed: MV.crit };
const PROJECT_STATUS  = { active: MV.accent, completed: MV.good, on_hold: MV.warn, paused: MV.warn, cancelled: MV.crit, planning: MV.neutral, closed: MV.light };

function mvBar(canvas, labels, values, opts = {}) {
    if (!canvas) return null;
    if (!labels.length || !mvHasData(values)) { mvEmpty(canvas, opts.empty); return null; }
    const horizontal = !!opts.horizontal;
    return new Chart(canvas, {
        type: 'bar',
        data: { labels, datasets: [{ label: opts.label || 'Count', data: values, backgroundColor: opts.colors || MV.accent, borderRadius: 4, maxBarThickness: 28 }] },
        options: {
            responsive: true, maintainAspectRatio: false, indexAxis: horizontal ? 'y' : 'x',
            plugins: { legend: { display: false } },
            scales: horizontal ? { x: mvValueAxis, y: mvCatAxis } : { x: mvCatAxis, y: mvValueAxis }
        }
    });
}

function mvDoughnut(canvas, labels, values, colors, opts = {}) {
    if (!canvas) return null;
    if (!labels.length || !mvHasData(values)) { mvEmpty(canvas, opts.empty); return null; }
    return new Chart(canvas, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: values, backgroundColor: colors, borderColor: '#fff', borderWidth: 2, hoverOffset: 2 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom' } } }
    });
}

function mvLine(canvas, labels, values, label, opts = {}) {
    if (!canvas) return null;
    if (!labels.length || !mvHasData(values)) { mvEmpty(canvas, opts.empty); return null; }
    return new Chart(canvas, {
        type: 'line',
        data: { labels, datasets: [{ label, data: values, borderColor: MV.accent, backgroundColor: 'rgba(43, 100, 168, .08)', fill: true, tension: 0.3, borderWidth: 2, pointRadius: 3, pointBackgroundColor: MV.accent }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: mvCatAxis, y: mvValueAxis } }
    });
}

const entries = obj => Object.entries(obj || {});

// Global chart instances
let charts = {};

// Tab switching functionality
function switchTab(evt, tabName) {
    const tabContents = document.getElementsByClassName('tab-content');
    const tabButtons = document.getElementsByClassName('tab-button');
    for (let i = 0; i < tabContents.length; i++) tabContents[i].classList.remove('active');
    for (let i = 0; i < tabButtons.length; i++) tabButtons[i].classList.remove('active');
    document.getElementById(tabName).classList.add('active');
    evt.currentTarget.classList.add('active');
    setTimeout(() => initializeChartsForTab(tabName), 100);
}

function initializeChartsForTab(tabName) {
    switch (tabName) {
        case 'overview':  initOverviewCharts(); break;
        case 'clients':   initClientCharts(); break;
        case 'terminals': initTerminalCharts(); break;
        case 'service':   initServiceCharts(); break;
        case 'assets':    initAssetCharts(); break;
        case 'employees': initEmployeeCharts(); break;
        case 'projects':  initProjectCharts(); break;
        case 'regional':  initRegionalCharts(); break;
    }
}

function initOverviewCharts() {
    // Health score ring
    const healthCtx = document.getElementById('healthScoreChart');
    if (healthCtx && !charts.healthScore) {
        const score = {{ $systemOverview['system_health_score'] }};
        const tone = score >= 80 ? MV.good : (score >= 60 ? MV.warn : MV.crit);
        charts.healthScore = new Chart(healthCtx, {
            type: 'doughnut',
            data: { datasets: [{ data: [score, Math.max(0, 100 - score)], backgroundColor: [tone, '#EDF0F4'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '78%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }

    // Monthly visit trends (real visit counts for the last 6 months)
    const visitCtx = document.getElementById('visitTrendsChart');
    if (visitCtx && !charts.visitTrends) {
        const v = @json($visitsTrend);
        charts.visitTrends = mvLine(visitCtx, v.labels, v.visits, 'Visits', { empty: 'No visits in the last 6 months' }) || true;
    }
}

function initClientCharts() {
    const clientStatusCtx = document.getElementById('clientStatusChart');
    if (clientStatusCtx && !charts.clientStatus) {
        const d = entries(@json($clientAnalytics['client_distribution']));
        charts.clientStatus = mvDoughnut(clientStatusCtx, d.map(e => pretty(e[0])), d.map(e => e[1]), d.map(e => mvStatusColor(e[0], CLIENT_STATUS))) || true;
    }

    const activityCtx = document.getElementById('clientActivityChart');
    if (activityCtx && !charts.clientActivity) {
        const d = entries(@json($clientAnalytics['top_clients_by_activity']));
        charts.clientActivity = mvBar(activityCtx, d.map(e => e[0]), d.map(e => e[1]), { label: 'Visits', empty: 'No client visits yet' }) || true;
    }
}

function initTerminalCharts() {
    const terminalStatusCtx = document.getElementById('terminalStatusChart');
    if (terminalStatusCtx && !charts.terminalStatus) {
        const d = entries(@json($terminalData['status_distribution']));
        charts.terminalStatus = mvDoughnut(terminalStatusCtx, d.map(e => pretty(e[0])), d.map(e => e[1]), d.map(e => mvStatusColor(e[0], TERMINAL_STATUS))) || true;
    }

    const modelsCtx = document.getElementById('terminalModelsChart');
    if (modelsCtx && !charts.terminalModels) {
        const d = entries(@json($terminalData['model_distribution'])).sort((a, b) => b[1] - a[1]);
        charts.terminalModels = mvBar(modelsCtx, d.map(e => e[0] || 'Unknown'), d.map(e => e[1]), { label: 'Terminals', horizontal: true }) || true;
    }

    const regionCtx = document.getElementById('terminalRegionChart');
    if (regionCtx && !charts.terminalRegion) {
        const d = entries(@json($terminalData['regional_distribution'])).sort((a, b) => b[1] - a[1]);
        charts.terminalRegion = mvBar(regionCtx, d.map(e => e[0] || 'Unknown'), d.map(e => e[1]), { label: 'Terminals', horizontal: true }) || true;
    }
}

function initServiceCharts() {
    const techVisitsCtx = document.getElementById('technicianVisitsChart');
    if (techVisitsCtx && !charts.technicianVisits) {
        const d = entries(@json($serviceActivity['visits_by_technician'])).sort((a, b) => b[1] - a[1]);
        charts.technicianVisits = mvBar(techVisitsCtx, d.map(e => e[0]), d.map(e => e[1]), { label: 'Visits', horizontal: true, empty: 'No visits in the last 30 days' }) || true;
    }

    const ticketPriorityCtx = document.getElementById('ticketPriorityChart');
    if (ticketPriorityCtx && !charts.ticketPriority) {
        const d = entries(@json($serviceActivity['tickets_by_priority']));
        charts.ticketPriority = mvDoughnut(ticketPriorityCtx, d.map(e => pretty(e[0])), d.map(e => e[1]), d.map(e => mvStatusColor(e[0], PRIORITY))) || true;
    }

    const jobStatusCtx = document.getElementById('jobStatusChart');
    if (jobStatusCtx && !charts.jobStatus) {
        const d = entries(@json($serviceActivity['job_assignments_by_status']));
        charts.jobStatus = mvBar(jobStatusCtx, d.map(e => pretty(e[0])), d.map(e => e[1]), { label: 'Assignments', colors: d.map(e => mvStatusColor(e[0], JOB_STATUS)) }) || true;
    }
}

function initAssetCharts() {
    const assetCategoryCtx = document.getElementById('assetCategoryChart');
    if (assetCategoryCtx && !charts.assetCategory) {
        const d = entries(@json($assetData['assets_by_category'])).sort((a, b) => b[1] - a[1]);
        charts.assetCategory = mvBar(assetCategoryCtx, d.map(e => e[0] || 'Uncategorised'), d.map(e => e[1]), { label: 'Assets', horizontal: true }) || true;
    }
}

function initEmployeeCharts() {
    const deptCtx = document.getElementById('employeeDeptChart');
    if (deptCtx && !charts.employeeDept) {
        const d = entries(@json($employeeData['employees_by_department'])).sort((a, b) => b[1] - a[1]);
        charts.employeeDept = mvBar(deptCtx, d.map(e => e[0] || 'No department'), d.map(e => e[1]), { label: 'Employees', horizontal: true }) || true;
    }

    const roleCtx = document.getElementById('employeeRoleChart');
    if (roleCtx && !charts.employeeRole) {
        const d = entries(@json($employeeData['employees_by_role'])).sort((a, b) => b[1] - a[1]);
        charts.employeeRole = mvBar(roleCtx, d.map(e => pretty(e[0])), d.map(e => e[1]), { label: 'Employees', horizontal: true }) || true;
    }
}

function initProjectCharts() {
    const projectStatusCtx = document.getElementById('projectStatusChart');
    if (projectStatusCtx && !charts.projectStatus) {
        const d = entries(@json($projectData['projects_by_status']));
        charts.projectStatus = mvDoughnut(projectStatusCtx, d.map(e => pretty(e[0])), d.map(e => e[1]), d.map(e => mvStatusColor(e[0], PROJECT_STATUS))) || true;
    }

    const projectTypeCtx = document.getElementById('projectTypeChart');
    if (projectTypeCtx && !charts.projectType) {
        const d = entries(@json($projectData['projects_by_type'])).sort((a, b) => b[1] - a[1]);
        charts.projectType = mvBar(projectTypeCtx, d.map(e => pretty(e[0])), d.map(e => e[1]), { label: 'Projects', horizontal: true }) || true;
    }
}

function initRegionalCharts() {
    const regionalTerminalsCtx = document.getElementById('regionalTerminalsChart');
    if (regionalTerminalsCtx && !charts.regionalTerminals) {
        const d = entries(@json($regionalData['terminals_by_region'])).sort((a, b) => b[1] - a[1]);
        charts.regionalTerminals = mvBar(regionalTerminalsCtx, d.map(e => e[0] || 'Unknown'), d.map(e => e[1]), { label: 'Terminals', horizontal: true }) || true;
    }

    const regionalServiceCtx = document.getElementById('regionalServiceChart');
    if (regionalServiceCtx && !charts.regionalService) {
        const d = entries(@json($regionalData['service_activity_by_region'])).sort((a, b) => b[1] - a[1]);
        charts.regionalService = mvBar(regionalServiceCtx, d.map(e => e[0] || 'Unknown'), d.map(e => e[1]), { label: 'Service Visits', horizontal: true, empty: 'No service visits recorded by region' }) || true;
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

function initTrendCharts() {
    const ticketCtx = document.getElementById('ticketTrendChart');
    if (ticketCtx) {
        const t = @json($ticketTrend);
        if (!mvHasData(t.created) && !mvHasData(t.resolved)) {
            mvEmpty(ticketCtx, 'No tickets in the last 6 months');
        } else {
            new Chart(ticketCtx, {
                type: 'bar',
                data: {
                    labels: t.labels,
                    datasets: [
                        { label: 'Created',  data: t.created,  backgroundColor: MV.accent, borderRadius: 4, maxBarThickness: 28 },
                        { label: 'Resolved', data: t.resolved, backgroundColor: MV.good,   borderRadius: 4, maxBarThickness: 28 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { x: mvCatAxis, y: mvValueAxis } }
            });
        }
    }

    const visitsCtx = document.getElementById('visitsTrendChart');
    if (visitsCtx) {
        const v = @json($visitsTrend);
        mvLine(visitsCtx, v.labels, v.visits, 'Visits', { empty: 'No service visits in the last 6 months' });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (!window.Chart) return;
    initOverviewCharts();
    initTrendCharts();
});
</script>
@endpush
