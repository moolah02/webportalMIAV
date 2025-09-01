@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('client-dashboards.index') }}"
                       class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $client->company_name }}</h1>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="text-sm text-gray-500">{{ $client->client_code }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $client->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard -->
    <div class="max-w-full mx-auto px-6 py-8">
        <!-- Statistics Cards - now horizontal -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-6">
            <div class="stat-card h-full text-center p-6 rounded-lg border transition-all duration-200 hover:transform hover:scale-105"
                 style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #dee2e6;">
                <div id="total-count" class="text-3xl font-bold text-gray-900 mb-1">
                    {{ $terminalStats['total'] }}
                </div>
                <div class="text-xs text-gray-600 uppercase font-medium tracking-wide">Total Terminals</div>
                <div class="text-xs text-gray-400 mt-1">Active network size</div>
            </div>

            <div class="stat-card h-full text-center p-6 rounded-lg border transition-all duration-200 hover:transform hover:scale-105"
                 style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-color: #b8dacc;">
                <div id="active-count" class="text-3xl font-bold mb-1" style="color: #155724;">
                    {{ $terminalStats['by_status']['active'] ?? 0 }}
                </div>
                <div class="text-xs uppercase font-medium tracking-wide" style="color: #155724;">Active</div>
                <div class="text-xs mt-1" style="color: #155724;">
                    {{ $terminalStats['total'] > 0 ? round((($terminalStats['by_status']['active'] ?? 0) / $terminalStats['total']) * 100, 1) : 0 }}% uptime
                </div>
            </div>

            <div class="stat-card h-full text-center p-6 rounded-lg border transition-all duration-200 hover:transform hover:scale-105"
                 style="background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%); border-color: #f1b0b7;">
                <div id="faulty-count" class="text-3xl font-bold mb-1" style="color: #721c24;">
                    {{ ($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0) }}
                </div>
                <div class="text-xs uppercase font-medium tracking-wide" style="color: #721c24;">Need Attention</div>
                <div class="text-xs text-gray-500 mt-1">Require service</div>
            </div>

            <div class="stat-card h-full text-center p-6 rounded-lg border transition-all duration-200 hover:transform hover:scale-105"
                 style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-color: #ffeaa7;">
                <div id="offline-count" class="text-3xl font-bold mb-1" style="color: #856404;">
                    {{ $terminalStats['by_status']['offline'] ?? 0 }}
                </div>
                <div class="text-xs uppercase font-medium tracking-wide" style="color: #856404;">Offline</div>
                <div class="text-xs text-gray-500 mt-1">Not responding</div>
            </div>

            <div class="stat-card h-full text-center p-6 rounded-lg border transition-all duration-200 hover:transform hover:scale-105"
                 style="background: linear-gradient(135deg, #e7f3ff 0%, #b3d9ff 100%); border-color: #b3d9ff;">
                <div class="text-3xl font-bold mb-1" style="color: #0066cc;">
                    0
                </div>
                <div class="text-xs uppercase font-medium tracking-wide" style="color: #0066cc;">Recently Serviced</div>
                <div class="text-xs text-gray-500 mt-1">Last 30 days</div>
            </div>
        </div>

        <!-- Second Row: Service Due Card -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
            <div class="stat-card text-center p-6 rounded-lg border transition-all duration-200 hover:transform hover:scale-105"
                 style="background: linear-gradient(135deg, #fff0e6 0%, #ffcc99 100%); border-color: #ffcc99;">
                <div class="text-3xl font-bold mb-1" style="color: #cc6600;">
                    {{ $terminalStats['total'] }}
                </div>
                <div class="text-xs uppercase font-medium tracking-wide" style="color: #cc6600;">Service Due</div>
                <div class="text-xs text-gray-500 mt-1">Maintenance needed</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8 shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Terminal Analytics</h2>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Service Timeline -->
                <div class="chart-container bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Service Timeline</h3>
                    <div class="relative" style="height: 250px;">
                        <canvas id="serviceDueChart"></canvas>
                    </div>
                    <div class="mt-3 text-xs text-gray-600 text-center">
                        Maintenance schedule tracking
                    </div>
                </div>

                <!-- Regional Distribution -->
                <div class="chart-container bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Regional Distribution</h3>
                    <div class="relative" style="height: 250px;">
                        <canvas id="locationChart"></canvas>
                    </div>
                    <div class="mt-3 text-xs text-gray-600 text-center">
                        Terminals by location
                    </div>
                </div>

                <!-- Device Models -->
                <div class="chart-container bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Device Models</h3>
                    <div class="relative" style="height: 250px;">
                        <canvas id="modelsChart"></canvas>
                    </div>
                    <div class="mt-3 text-xs text-gray-600 text-center">
                        Terminal model distribution
                    </div>
                </div>
            </div>

            <!-- Additional Metrics Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 rounded-md border" style="background: #e7f3ff; border-color: #b3d9ff;">
                    <div class="text-xl font-semibold mb-1" style="color: #0066cc;">
                        0
                    </div>
                    <div class="text-xs uppercase tracking-wide" style="color: #0066cc;">Recently Serviced</div>
                </div>

                <div class="text-center p-4 rounded-md border" style="background: #fff0e6; border-color: #ffcc99;">
                    <div class="text-xl font-semibold mb-1" style="color: #cc6600;">
                        {{ $terminalStats['total'] }}
                    </div>
                    <div class="text-xs uppercase tracking-wide" style="color: #cc6600;">Service Due</div>
                </div>

                <div class="text-center p-4 rounded-md border" style="background: #f0f8f0; border-color: #b3e6b3;">
                    <div class="text-xl font-semibold mb-1" style="color: #008000;">
                        0
                    </div>
                    <div class="text-xs uppercase tracking-wide" style="color: #008000;">New Installs</div>
                </div>

                <div class="text-center p-4 rounded-md border" style="background: #fdf2f8; border-color: #f8bbd9;">
                    <div class="text-xl font-semibold mb-1" style="color: #be185d;">
                        4
                    </div>
                    <div class="text-xs uppercase tracking-wide" style="color: #be185d;">Device Types</div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- POS Terminals Table - Takes up 3/4 of the width -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">POS Terminals</h2>
                                <p class="text-sm text-gray-500">{{ $terminalStats['total'] }} total terminals</p>
                            </div>
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Export
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center space-x-4 mb-4">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.586V4z"/>
                            </svg>
                            <h3 class="text-sm font-medium text-gray-900">Filters & Search</h3>
                            <button class="text-sm text-blue-600 hover:text-blue-700">Clear All Filters</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-3">
                            <input type="text" placeholder="Search terminals..."
                                   class="rounded-md border-gray-300 shadow-sm text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button class="px-4 py-2 text-sm bg-gray-800 text-white rounded-md hover:bg-gray-700">Search</button>
                            <button class="px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Clear</button>
                            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Terminal</button>
                            <button class="px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Export</button>
                        </div>
                        <div class="grid grid-cols-5 gap-3">
                            <select class="rounded-md border-gray-300 shadow-sm text-sm">
                                <option>All Clients</option>
                            </select>
                            <select class="rounded-md border-gray-300 shadow-sm text-sm">
                                <option>All Status</option>
                            </select>
                            <select class="rounded-md border-gray-300 shadow-sm text-sm">
                                <option>All Regions</option>
                            </select>
                            <select class="rounded-md border-gray-300 shadow-sm text-sm">
                                <option>All Cities</option>
                            </select>
                            <select class="rounded-md border-gray-300 shadow-sm text-sm">
                                <option>All Provinces</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table - Full Width Without Horizontal Scroll -->
                    <div class="w-full">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terminal ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client/Bank</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merchant</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Service</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($terminals as $terminal)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $terminal->terminal_id }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $client->company_name }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $terminal->merchant_name ?? 'City Electronics' }}</div>
                                        <div class="text-sm text-gray-500">{{ $terminal->merchant_contact_person ?? 'Mike Smith' }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">{{ $terminal->merchant_contact_person ?? 'Jane Doe' }}</div>
                                        <div class="text-sm text-gray-500">+254734567890</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">{{ $terminal->city ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @switch($terminal->current_status ?? 'maintenance')
                                                @case('active') bg-green-100 text-green-800 @break
                                                @case('offline') bg-orange-100 text-orange-800 @break
                                                @case('maintenance') bg-yellow-100 text-yellow-800 @break
                                                @case('faulty') bg-red-100 text-red-800 @break
                                                @default bg-yellow-100 text-yellow-800
                                            @endswitch">
                                            {{ strtoupper($terminal->current_status ?? 'MAINTENANCE') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Jul 15, 2024
                                        <div class="text-xs text-gray-400">1 year ago</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="#" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    </td>
                                </tr>
                                @empty
                                <!-- Sample Data Rows -->
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">POS-002</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">ABC Bank</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900">City Electronics</div>
                                        <div class="text-sm text-gray-500">Mike Smith</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">Mike Smith</div>
                                        <div class="text-sm text-gray-500">+254734567890</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">Unknown</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            MAINTENANCE
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Jul 15, 2024
                                        <div class="text-xs text-gray-400">1 year ago</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="#" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Projects Section -->
                    <div class="border-t border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Active Projects</h3>
                                <p class="text-sm text-gray-500">3 ongoing projects</p>
                            </div>
                            <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                                + Create Project
                            </button>
                        </div>

                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Terminal Discovery Phase 1</h4>
                                    <p class="text-sm text-gray-600">CLI-DIS-202508-01</p>
                                    <p class="text-sm text-gray-500 mt-1">Initial discovery and assessment of all POS terminals...</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span class="text-xs text-gray-500">Discovery</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Recent Visits -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-gray-900">Recent Visits</h3>
                            <button class="text-xs text-blue-600 hover:text-blue-700 font-medium">View More</button>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @for($i = 1; $i <= 4; $i++)
                        <div class="px-4 py-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Terminal {{ $i }}</p>
                                    <p class="text-sm text-gray-600">Monah Chimwa</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800">Open</span>
                                    <p class="text-xs text-gray-500 mt-1">Aug 26, 2025</p>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Open Tickets -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Open Tickets</h3>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-500">No open tickets</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<!-- Chart Data & JavaScript -->
<script>
// Chart Data
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

// Chart instances
let charts = {};

// Initialize Charts
function initializeCharts() {
    // Destroy existing charts
    Object.values(charts).forEach(chart => chart?.destroy());
    charts = {};

    // Service Due Chart
    const serviceDueCtx = document.getElementById('serviceDueChart');
    if (serviceDueCtx) {
        charts.serviceDue = new Chart(serviceDueCtx, {
            type: 'bar',
            data: {
                labels: ['Recently Serviced', 'Due Soon', 'Overdue', 'Never Serviced'],
                datasets: [{
                    data: [
                        window.chartData.serviceDue.recentlyServiced,
                        window.chartData.serviceDue.serviceDueSoon,
                        window.chartData.serviceDue.overdueService,
                        window.chartData.serviceDue.neverServiced
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                    borderColor: ['#1e7e34', '#e0a800', '#bd2130', '#545b62'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Regional Distribution Chart
    const locationCtx = document.getElementById('locationChart');
    if (locationCtx) {
        charts.location = new Chart(locationCtx, {
            type: 'bar',
            data: {
                labels: ['HARARE', 'BULAWAYO', 'GWERU', 'KWEKWE', 'MUTARE'],
                datasets: [{
                    data: [25, 15, 8, 5, 0],
                    backgroundColor: ['#007bff', '#28a745', '#dc3545', '#ffc107', '#6f42c1'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 5 } }
                }
            }
        });
    }

    // Device Models Chart
    const modelsCtx = document.getElementById('modelsChart');
    if (modelsCtx) {
        charts.models = new Chart(modelsCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(window.chartData.modelDistribution),
                datasets: [{
                    data: Object.values(window.chartData.modelDistribution),
                    backgroundColor: ['#17a2b8', '#fd7e14', '#20c997', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
}

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});
</script>
@endsection
