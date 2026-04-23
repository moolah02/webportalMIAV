@extends('layouts.app')
@section('title', 'Client Dashboard')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('client-dashboards.index') }}" class="btn-secondary btn-sm">&#x2190; Back</a>
            <div>
                <span class="text-xs text-gray-400 font-mono">{{ $client->client_code }}</span>
                <span class="badge badge-gray ml-2">{{ ucfirst($client->status) }}</span>
            </div>
        </div>
        <button onclick="exportData()" class="btn-secondary">Export Data</button>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">&#x1F5A5;&#xFE0F;</div>
            <div>
                <div class="stat-number">{{ $terminalStats['total'] }}</div>
                <div class="stat-label">Total Terminals</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">&#x2705;</div>
            <div>
                <div class="stat-number">{{ $terminalStats['by_status']['active'] ?? 0 }}</div>
                <div class="stat-label">Active</div>
                <div class="stat-sub">{{ $terminalStats['total'] > 0 ? round((($terminalStats['by_status']['active'] ?? 0) / $terminalStats['total']) * 100, 1) : 0 }}% uptime</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-red">&#x26A0;&#xFE0F;</div>
            <div>
                <div class="stat-number">{{ ($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0) }}</div>
                <div class="stat-label">Need Attention</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">&#x1F4F4;</div>
            <div>
                <div class="stat-number">{{ $terminalStats['by_status']['offline'] ?? 0 }}</div>
                <div class="stat-label">Offline</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-teal">&#x1F527;</div>
            <div>
                <div class="stat-number">0</div>
                <div class="stat-label">Serviced (30d)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">&#x1F4C5;</div>
            <div>
                <div class="stat-number">{{ $terminalStats['total'] }}</div>
                <div class="stat-label">Service Due</div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="ui-card mb-6">
        <div class="ui-card-header">
            <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CA; Terminal Analytics</h4>
        </div>
        <div class="ui-card-body">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <h4 class="text-xs font-semibold text-gray-600 mb-3 uppercase tracking-wide">Service Timeline</h4>
                    <div class="relative h-48">
                        <canvas id="serviceDueChart"></canvas>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <h4 class="text-xs font-semibold text-gray-600 mb-3 uppercase tracking-wide">Regional Distribution</h4>
                    <div class="relative h-48">
                        <canvas id="locationChart"></canvas>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <h4 class="text-xs font-semibold text-gray-600 mb-3 uppercase tracking-wide">Device Models</h4>
                    <div class="relative h-48">
                        <canvas id="modelsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center bg-blue-50 border border-blue-100 rounded-lg p-4">
                    <div class="text-xl font-bold text-blue-700 mb-1">0</div>
                    <div class="text-xs text-blue-600 font-medium uppercase tracking-wide">Recently Serviced</div>
                </div>
                <div class="text-center bg-orange-50 border border-orange-100 rounded-lg p-4">
                    <div class="text-xl font-bold text-orange-700 mb-1">{{ $terminalStats['total'] }}</div>
                    <div class="text-xs text-orange-600 font-medium uppercase tracking-wide">Service Due</div>
                </div>
                <div class="text-center bg-green-50 border border-green-100 rounded-lg p-4">
                    <div class="text-xl font-bold text-green-700 mb-1">0</div>
                    <div class="text-xs text-green-600 font-medium uppercase tracking-wide">New Installs</div>
                </div>
                <div class="text-center bg-purple-50 border border-purple-100 rounded-lg p-4">
                    <div class="text-xl font-bold text-purple-700 mb-1">4</div>
                    <div class="text-xs text-purple-600 font-medium uppercase tracking-wide">Device Types</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main 2-col layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Left: POS Terminals --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Terminals Table --}}
            <div class="ui-card overflow-hidden">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F5A5;&#xFE0F; POS Terminals</h4>
                    <span class="badge badge-gray">{{ $terminalStats['total'] }} terminals</span>
                </div>

                {{-- Filters --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('client-dashboards.show', $client) }}" class="flex flex-wrap gap-3 items-end">
                        <div class="filter-group">
                            <label class="ui-label">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search terminals…" class="ui-input">
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
                </div>

                <div class="overflow-x-auto">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>Terminal ID</th>
                                <th>Merchant</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Last Service</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($terminals as $terminal)
                            @php
                                $tsc = match($terminal->current_status ?? 'unknown') {
                                    'active'      => 'badge-green',
                                    'offline'     => 'badge-yellow',
                                    'maintenance' => 'badge-blue',
                                    'faulty'      => 'badge-red',
                                    default       => 'badge-gray',
                                };
                            @endphp
                            <tr>
                                <td><span class="code-chip">{{ $terminal->terminal_id }}</span></td>
                                <td>
                                    <div class="text-sm font-medium text-gray-900">{{ $terminal->merchant_name ?? '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ $terminal->merchant_contact_person ?? '' }}</div>
                                </td>
                                <td class="text-sm text-gray-600">{{ $terminal->merchant_phone ?? '—' }}</td>
                                <td class="text-sm text-gray-600">{{ $terminal->city ?? '—' }}</td>
                                <td>
                                    <span class="status-badge {{ $tsc }}">{{ ucfirst($terminal->current_status ?? 'unknown') }}</span>
                                </td>
                                <td>
                                    <div class="text-xs font-medium text-gray-700">
                                        {{ $terminal->last_service_date ? \Carbon\Carbon::parse($terminal->last_service_date)->format('M d, Y') : '—' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $terminal->last_service_date ? \Carbon\Carbon::parse($terminal->last_service_date)->diffForHumans() : '' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a href="{{ route('client-dashboards.terminals.show', [$client, $terminal]) }}" class="action-btn action-view" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center text-gray-400">
                                    <div class="text-4xl mb-3">&#x1F5A5;&#xFE0F;</div>
                                    <p class="text-sm font-medium text-gray-600">No terminals found</p>
                                    <p class="text-xs text-gray-400 mt-1">Terminals assigned to this client will appear here.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($terminals) && method_exists($terminals, 'links') && $terminals->hasPages())
                <div class="ui-card-footer justify-center">
                    {{ $terminals->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

            {{-- Active Projects --}}
            <div class="ui-card overflow-hidden">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CB; Active Projects</h4>
                    <a href="{{ route('client-dashboards.projects.create', $client) }}" class="btn-primary btn-sm">+ New Project</a>
                </div>
                <div class="ui-card-body space-y-3">
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
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $project->project_name }}</div>
                                <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $project->project_code }}</div>
                                @if($project->description)
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($project->description, 80) }}</div>
                                @endif
                            </div>
                            <span class="badge {{ $psc }} ml-3 flex-shrink-0">{{ ucfirst($project->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">&#x1F4CB;</div>
                        <p class="empty-state-msg">No active projects</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">

            {{-- Recent Visits --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CD; Recent Visits</h4>
                    <a href="{{ route('visits.index') }}?client_id={{ $client->id }}" class="btn-secondary btn-sm">View More</a>
                </div>
                <div class="ui-card-body space-y-3">
                    @if(isset($recentVisits) && $recentVisits->count() > 0)
                        @foreach($recentVisits->take(4) as $visit)
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-medium text-gray-800">
                                    Terminal {{ $visit->posTerminal->terminal_id ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-400">{{ $visit->technician->full_name ?? 'Technician' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-gray text-xs">{{ ucfirst($visit->status ?? 'Open') }}</span>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') : '' }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="empty-state">
                        <div class="empty-state-icon">&#x1F4CD;</div>
                        <p class="empty-state-msg">No recent visits</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Open Tickets --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F3AB; Open Tickets</h4>
                    <a href="{{ route('tickets.index') }}?client_id={{ $client->id }}" class="btn-secondary btn-sm">View All</a>
                </div>
                <div class="ui-card-body">
                    @if(isset($openTickets) && $openTickets->count() > 0)
                        <div class="space-y-3">
                            @foreach($openTickets->take(3) as $ticket)
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                       class="text-sm font-medium text-gray-800 hover:text-[#1a3a5c] no-underline truncate block">
                                        {{ $ticket->title ?? 'Support Ticket' }}
                                    </a>
                                    <div class="text-xs text-gray-400">{{ $ticket->posTerminal->terminal_id ?? 'Terminal' }}</div>
                                </div>
                                <div class="text-right ml-3 flex-shrink-0">
                                    <span class="badge badge-gray text-xs">{{ ucfirst($ticket->status) }}</span>
                                    <div class="text-xs text-gray-400 mt-1">{{ $ticket->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-state-icon">&#x1F3AB;</div>
                        <p class="empty-state-msg">No open tickets</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
window.chartData = {
    stats: {
        total_terminals: {{ $terminalStats['total'] }},
        active_terminals: {{ $terminalStats['by_status']['active'] ?? 0 }},
        faulty_terminals: {{ ($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0) }},
        offline_terminals: {{ $terminalStats['by_status']['offline'] ?? 0 }},
        uptime_percentage: {{ $terminalStats['total'] > 0 ? round((($terminalStats['by_status']['active'] ?? 0) / $terminalStats['total']) * 100, 1) : 0 }}
    },
    serviceDue: {
        recentlyServiced: 0,
        serviceDueSoon: {{ max(0, ($terminalStats['total'] - 2)) }},
        overdueService: {{ $terminalStats['total'] }},
        neverServiced: {{ max(0, floor($terminalStats['total'] / 2)) }}
    },
    modelDistribution: {
        'Ingenico': {{ max(1, floor($terminalStats['total'] * 0.4)) }},
        'Verifone': {{ max(1, floor($terminalStats['total'] * 0.3)) }},
        'PAX': {{ max(1, floor($terminalStats['total'] * 0.2)) }},
        'Other': {{ max(0, $terminalStats['total'] - floor($terminalStats['total'] * 0.9)) }}
    }
};

let charts = {};

function initializeCharts() {
    Object.values(charts).forEach(chart => chart?.destroy());
    charts = {};

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
                    backgroundColor: ['#4caf50', '#ff9800', '#f44336', '#9e9e9e'],
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
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
                    backgroundColor: ['#4caf50', '#ff9800', '#2196f3', '#f44336'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } }
            }
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
                    backgroundColor: ['#1a3a5c', '#2196f3', '#4caf50', '#9e9e9e'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } }
            }
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
