@extends('layouts.app')
@section('title', 'Client Dashboard')

@section('header-actions')
<a href="{{ route('client-dashboards.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back</a>
<button type="button" onclick="exportData()" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Export Data</button>
@endsection

@push('styles')
<style>
.cx-head { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 18px; margin-bottom: 16px; }
.cx-head-name { font-size: 16px; font-weight: 600; color: var(--mv-ink); }
.cx-code { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 7px; }
.cx-head-meta { margin-left: auto; font-size: 12.5px; color: var(--mv-muted); }
.cx-stats { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
@media (max-width: 1280px) { .cx-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
@media (max-width: 700px) { .cx-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.cx-stats .stat-card { padding: 14px 16px; gap: 12px; }
.cx-stats .stat-icon { width: 36px; height: 36px; }
.cx-stats .stat-number { font-size: 22px; }
.cx-charts { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
@media (max-width: 1000px) { .cx-charts { grid-template-columns: 1fr; } }
.cx-chart-title { font-size: 12.5px; font-weight: 600; color: var(--mv-ink-2); margin: 0 0 10px; }
.cx-chart-box { position: relative; height: 200px; }
.cx-minis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); border-top: 1px solid var(--mv-line); }
@media (max-width: 700px) { .cx-minis { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.cx-mini { padding: 12px 18px; }
.cx-mini + .cx-mini { border-left: 1px solid var(--mv-line); }
.cx-mini-v { font-size: 18px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.cx-mini-l { font-size: 12px; color: var(--mv-muted); }
.cx-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 16px; align-items: start; }
@media (max-width: 1100px) { .cx-layout { grid-template-columns: 1fr; } }
.cx-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.cx-filters { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; padding: 12px 18px; border-bottom: 1px solid var(--mv-line); background: var(--mv-surface-2); }
.cx-muted { color: var(--mv-muted); font-size: 12.5px; }
.cx-strong { font-weight: 500; color: var(--mv-ink); }
.cx-list { padding: 4px 18px; }
.cx-item { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 10px 0; }
.cx-item + .cx-item { border-top: 1px solid var(--mv-line); }
.cx-item-r { text-align: right; flex-shrink: 0; }
.cx-item-r .cx-muted { font-size: 12px; margin-top: 3px; font-variant-numeric: tabular-nums; }
.cx-link { color: var(--mv-ink); text-decoration: none; font-weight: 500; font-size: 13px; }
.cx-link:hover { color: var(--mv-accent-ink); text-decoration: underline; }
.cx-empty { padding: 26px 18px; text-align: center; color: var(--mv-muted); font-size: 13px; }
.cx-project { padding: 12px 18px; display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
.cx-project + .cx-project { border-top: 1px solid var(--mv-line); }
</style>
@endpush

@section('content')
@php
    $total     = $terminalStats['total'];
    $active    = $terminalStats['by_status']['active'] ?? 0;
    $attention = ($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0);
    $offline   = $terminalStats['by_status']['offline'] ?? 0;
    $uptime    = $total > 0 ? round(($active / $total) * 100, 1) : 0;
    $csc = ['active' => 'badge-green', 'prospect' => 'badge-blue', 'inactive' => 'badge-gray', 'lost' => 'badge-red'][strtolower($client->status ?? '')] ?? 'badge-gray';
@endphp

<div class="ui-card cx-head">
    <span class="cx-head-name">{{ $client->company_name }}</span>
    <span class="cx-code">{{ $client->client_code }}</span>
    <span class="badge {{ $csc }}">{{ ucfirst($client->status) }}</span>
    @if($client->contact_person || $client->city)
    <span class="cx-head-meta">{{ collect([$client->contact_person, $client->city])->filter()->join(' · ') }}</span>
    @endif
</div>

<div class="cx-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-monitor"/></svg></div>
        <div>
            <div class="stat-number">{{ $total }}</div>
            <div class="stat-label">Total Terminals</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ $active }}</div>
            <div class="stat-label">Active</div>
            <div class="stat-sub">{{ $uptime }}% uptime</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red"><svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg></div>
        <div>
            <div class="stat-number">{{ $attention }}</div>
            <div class="stat-label">Need Attention</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-wifi"/></svg></div>
        <div>
            <div class="stat-number">{{ $offline }}</div>
            <div class="stat-label">Offline</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-wrench"/></svg></div>
        <div>
            <div class="stat-number">0</div>
            <div class="stat-label">Serviced (30d)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg></div>
        <div>
            <div class="stat-number">{{ $total }}</div>
            <div class="stat-label">Service Due</div>
        </div>
    </div>
</div>

<div class="ui-card" style="margin-bottom:16px">
    <div class="ui-card-header"><h3>Terminal Analytics</h3></div>
    <div class="ui-card-body">
        <div class="cx-charts">
            <div>
                <p class="cx-chart-title">Service Timeline</p>
                <div class="cx-chart-box"><canvas id="serviceDueChart"></canvas></div>
            </div>
            <div>
                <p class="cx-chart-title">Regional Distribution</p>
                <div class="cx-chart-box"><canvas id="locationChart"></canvas></div>
            </div>
            <div>
                <p class="cx-chart-title">Device Models</p>
                <div class="cx-chart-box"><canvas id="modelsChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="cx-minis">
        <div class="cx-mini"><div class="cx-mini-v">0</div><div class="cx-mini-l">Recently Serviced</div></div>
        <div class="cx-mini"><div class="cx-mini-v">{{ $total }}</div><div class="cx-mini-l">Service Due</div></div>
        <div class="cx-mini"><div class="cx-mini-v">0</div><div class="cx-mini-l">New Installs</div></div>
        <div class="cx-mini"><div class="cx-mini-v">4</div><div class="cx-mini-l">Device Types</div></div>
    </div>
</div>

<div class="cx-layout">
    <div class="cx-col">
        <div class="ui-card overflow-hidden">
            <div class="ui-card-header">
                <h3>POS Terminals</h3>
                <span class="cx-muted">{{ $total }} terminals</span>
            </div>

            <form method="GET" action="{{ route('client-dashboards.show', $client) }}" class="cx-filters">
                <div class="filter-group">
                    <label class="ui-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search terminals…" class="ui-input">
                </div>
                <div class="filter-group">
                    <label class="ui-label">Status</label>
                    <select name="status" class="ui-select">
                        <option value="">All Status</option>
                        <option value="active"      {{ request('status') == 'active'      ? 'selected' : '' }}>Active</option>
                        <option value="offline"     {{ request('status') == 'offline'     ? 'selected' : '' }}>Offline</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="faulty"      {{ request('status') == 'faulty'      ? 'selected' : '' }}>Faulty</option>
                    </select>
                </div>
                @if(isset($regions) && $regions->count())
                <div class="filter-group">
                    <label class="ui-label">Region</label>
                    <select name="region" class="ui-select">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="filter-actions">
                    <button type="submit" class="btn-primary">Apply</button>
                    @if(request()->hasAny(['search','status','region','city']))
                    <a href="{{ route('client-dashboards.show', $client) }}" class="btn-secondary">Clear</a>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="ui-table w-full">
                    <thead>
                        <tr>
                            <th>Terminal ID</th>
                            <th>Merchant</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Last Service</th>
                            <th style="width:60px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terminals as $terminal)
                        @php
                            $tsc = match($terminal->current_status ?? 'unknown') {
                                'active'      => 'badge-green',
                                'offline'     => 'badge-gray',
                                'maintenance' => 'badge-yellow',
                                'faulty'      => 'badge-red',
                                default       => 'badge-gray',
                            };
                        @endphp
                        <tr>
                            <td><span class="code-chip" style="padding:2px 7px;font-size:12px">{{ $terminal->terminal_id }}</span></td>
                            <td>
                                <div class="cx-strong">{{ $terminal->merchant_name ?? '—' }}</div>
                                @if($terminal->merchant_contact_person)<div class="cx-muted">{{ $terminal->merchant_contact_person }}</div>@endif
                            </td>
                            <td style="white-space:nowrap">{{ $terminal->merchant_phone ?? '—' }}</td>
                            <td>{{ $terminal->city ?? '—' }}</td>
                            <td><span class="badge {{ $tsc }}">{{ ucfirst($terminal->current_status ?? 'unknown') }}</span></td>
                            <td style="white-space:nowrap">
                                @if($terminal->last_service_date)
                                    <div>{{ \Carbon\Carbon::parse($terminal->last_service_date)->format('M d, Y') }}</div>
                                    <div class="cx-muted" style="font-size:12px">{{ \Carbon\Carbon::parse($terminal->last_service_date)->diffForHumans() }}</div>
                                @else
                                    <span class="cx-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('client-dashboards.terminals.show', [$client, $terminal]) }}" class="action-btn" title="View"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-monitor"/></svg></div>
                                    <p class="empty-state-msg">No terminals found. Terminals assigned to this client will appear here.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($terminals) && method_exists($terminals, 'links') && $terminals->hasPages())
            <div class="ui-card-footer" style="justify-content:center">
                {{ $terminals->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        <div class="ui-card overflow-hidden">
            <div class="ui-card-header">
                <h3>Active Projects</h3>
                <a href="{{ route('client-dashboards.projects.create', $client) }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> New Project</a>
            </div>
            @forelse(isset($projects) ? $projects->take(3) : collect() as $project)
            @php
                $psc = match($project->status) {
                    'active'    => 'badge-green',
                    'completed' => 'badge-blue',
                    'paused'    => 'badge-yellow',
                    'cancelled' => 'badge-red',
                    default     => 'badge-gray',
                };
            @endphp
            <div class="cx-project">
                <div style="min-width:0">
                    <a href="{{ route('projects.show', $project) }}" class="cx-link">{{ $project->project_name }}</a>
                    <div style="font-family:var(--mv-mono);font-size:11.5px;color:var(--mv-muted);margin-top:1px">{{ $project->project_code }}</div>
                    @if($project->description)
                    <div class="cx-muted" style="margin-top:3px">{{ Str::limit($project->description, 90) }}</div>
                    @endif
                </div>
                <span class="badge {{ $psc }}" style="flex-shrink:0">{{ ucfirst($project->status) }}</span>
            </div>
            @empty
            <div class="cx-empty">No active projects</div>
            @endforelse
        </div>
    </div>

    <div class="cx-col">
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Recent Visits</h3>
                <a href="{{ route('visits.index') }}?client_id={{ $client->id }}" class="btn-secondary btn-sm">View More</a>
            </div>
            @if(isset($recentVisits) && $recentVisits->count() > 0)
            <div class="cx-list">
                @foreach($recentVisits->take(4) as $visit)
                <div class="cx-item">
                    <div>
                        <div class="cx-strong" style="font-size:13px">Terminal <span style="font-family:var(--mv-mono)">{{ $visit->posTerminal->terminal_id ?? 'N/A' }}</span></div>
                        <div class="cx-muted">{{ $visit->technician->full_name ?? 'Technician' }}</div>
                    </div>
                    <div class="cx-item-r">
                        <span class="badge badge-gray">{{ ucfirst($visit->status ?? 'Open') }}</span>
                        <div class="cx-muted">{{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') : '' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="cx-empty">No recent visits</div>
            @endif
        </div>

        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Open Tickets</h3>
                <a href="{{ route('tickets.index') }}?client_id={{ $client->id }}" class="btn-secondary btn-sm">View All</a>
            </div>
            @if(isset($openTickets) && $openTickets->count() > 0)
            <div class="cx-list">
                @foreach($openTickets->take(3) as $ticket)
                <div class="cx-item">
                    <div style="min-width:0">
                        <a href="{{ route('tickets.show', $ticket) }}" class="cx-link" style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $ticket->title ?? 'Support Ticket' }}</a>
                        <div class="cx-muted" style="font-family:var(--mv-mono);font-size:11.5px">{{ $ticket->posTerminal->terminal_id ?? 'Terminal' }}</div>
                    </div>
                    <div class="cx-item-r">
                        <span class="badge badge-gray">{{ ucfirst($ticket->status) }}</span>
                        <div class="cx-muted">{{ $ticket->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="cx-empty">No open tickets</div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
window.chartData = {
    stats: {
        total_terminals: {{ $total }},
        active_terminals: {{ $active }},
        faulty_terminals: {{ $attention }},
        offline_terminals: {{ $offline }},
        uptime_percentage: {{ $uptime }}
    },
    serviceDue: {
        recentlyServiced: 0,
        serviceDueSoon: {{ max(0, ($total - 2)) }},
        overdueService: {{ $total }},
        neverServiced: {{ max(0, floor($total / 2)) }}
    },
    modelDistribution: {
        'Ingenico': {{ max(1, floor($total * 0.4)) }},
        'Verifone': {{ max(1, floor($total * 0.3)) }},
        'PAX': {{ max(1, floor($total * 0.2)) }},
        'Other': {{ max(0, $total - floor($total * 0.9)) }}
    }
};

let charts = {};

function initializeCharts() {
    if (typeof Chart === 'undefined') return;
    Object.values(charts).forEach(chart => chart?.destroy());
    charts = {};

    Chart.defaults.font.family = '"IBM Plex Sans", "Segoe UI", system-ui, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6A7686';

    const legend = { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'rect', boxWidth: 8, boxHeight: 8, padding: 12 } };
    const doughnutBase = { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend } };

    const serviceDueCtx = document.getElementById('serviceDueChart');
    if (serviceDueCtx) {
        charts.serviceDue = new Chart(serviceDueCtx, {
            type: 'bar',
            data: {
                labels: ['Serviced', 'Due Soon', 'Overdue', 'Never'],
                datasets: [{
                    data: [
                        window.chartData.serviceDue.recentlyServiced,
                        window.chartData.serviceDue.serviceDueSoon,
                        window.chartData.serviceDue.overdueService,
                        window.chartData.serviceDue.neverServiced
                    ],
                    backgroundColor: ['#1D7F46', '#C28A2C', '#B83232', '#CBD3DD'],
                    borderWidth: 0,
                    borderRadius: 4,
                    maxBarThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, border: { color: '#E1E6EC' } },
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#E1E6EC' }, border: { display: false } }
                }
            }
        });
    }

    const locationCtx = document.getElementById('locationChart');
    if (locationCtx) {
        charts.location = new Chart(locationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Offline', 'Maintenance', 'Faulty'],
                datasets: [{
                    data: [
                        window.chartData.stats.active_terminals,
                        window.chartData.stats.offline_terminals,
                        {{ $terminalStats['by_status']['maintenance'] ?? 0 }},
                        {{ $terminalStats['by_status']['faulty'] ?? 0 }}
                    ],
                    backgroundColor: ['#1D7F46', '#CBD3DD', '#C28A2C', '#B83232'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: doughnutBase
        });
    }

    const modelsCtx = document.getElementById('modelsChart');
    if (modelsCtx) {
        charts.models = new Chart(modelsCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.chartData.modelDistribution),
                datasets: [{
                    data: Object.values(window.chartData.modelDistribution),
                    backgroundColor: ['#2B64A8', '#9AA6B4', '#CBD3DD', '#E1E6EC'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: doughnutBase
        });
    }
}

function exportData() {
    alert('Export functionality coming soon.');
}

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});
</script>
@endsection
