@extends('layouts.app')
@section('title', 'Company Dashboard')

@section('content')
<div>
    {{-- Welcome Header --}}
    <div class="mb-5">
    </div>

    {{-- System Alerts --}}
    @if($stats['alerts']->count() > 0)
    <div class="mb-5 flex flex-col gap-1.5">
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
            $alertClass = match($alert['type']) {
                'critical' => 'bg-red-50 border-red-400 text-red-800 hover:bg-red-100',
                'warning'  => 'bg-amber-50 border-amber-400 text-amber-800 hover:bg-amber-100',
                default    => 'bg-blue-50 border-blue-400 text-blue-800 hover:bg-blue-100',
            };
        @endphp
        <a href="{{ $alertUrl }}" class="alert-{{ $alert['type'] }} border rounded-lg px-3 py-2 flex items-center gap-2 text-sm no-underline transition-all hover:-translate-y-px hover:shadow-sm {{ $alertClass }}">
            <span class="text-sm">{{ $alert['icon'] }}</span>
            <span class="flex-1">{{ $alert['message'] }}</span>
            <span class="text-xs opacity-70">&#8594;</span>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Main Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('pos-terminals.index') }}" class="stat-card text-gray-900 no-underline hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="stat-icon stat-icon-blue">&#x1F5A5;&#xFE0F;</div>
            <div>
                <div class="stat-number">{{ number_format($stats['total_terminals']) }}</div>
                <div class="stat-label">Total Terminals</div>
                <div class="stat-sub text-green-600">&#8599; +{{ $stats['new_terminals_this_month'] }} this month</div>
            </div>
        </a>

        <a href="{{ route('pos-terminals.index', ['status' => 'active']) }}" class="stat-card text-gray-900 no-underline hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="stat-icon stat-icon-green">&#x2705;</div>
            <div>
                <div class="stat-number">{{ number_format($stats['active_terminals']) }}</div>
                <div class="stat-label">Active Terminals</div>
                <div class="stat-sub text-green-600">{{ $stats['network_uptime'] }}% uptime</div>
            </div>
        </a>

        <a href="{{ route('business-licenses.index') }}" class="stat-card text-gray-900 no-underline hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="stat-icon stat-icon-teal">&#x1F4CB;</div>
            <div>
                <div class="stat-number">{{ number_format($stats['license_stats']['total_licenses']) }}</div>
                <div class="stat-label">Business Licenses</div>
                <div class="stat-sub text-green-600">{{ $stats['license_stats']['active_licenses'] }} active</div>
            </div>
        </a>

        <a href="{{ route('pos-terminals.index') }}?status=faulty&status=offline&status=maintenance" class="stat-card text-gray-900 no-underline hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="stat-icon stat-icon-red">&#x26A0;&#xFE0F;</div>
            <div>
                <div class="stat-number">{{ number_format($stats['need_attention']) }}</div>
                <div class="stat-label">Need Attention</div>
                <div class="stat-sub text-red-500">{{ $stats['urgent_issues'] }} urgent</div>
            </div>
        </a>

        <a href="{{ route('clients.index') }}" class="stat-card text-gray-900 no-underline hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="stat-icon stat-icon-purple">&#x1F3E2;</div>
            <div>
                <div class="stat-number">{{ number_format($stats['total_clients']) }}</div>
                <div class="stat-label">Active Clients</div>
                <div class="stat-sub">{{ $stats['new_clients_this_month'] }} new this month</div>
            </div>
        </a>

        <a href="{{ route('business-licenses.compliance') }}" class="stat-card text-gray-900 no-underline hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="stat-icon stat-icon-yellow">&#x2705;</div>
            <div>
                <div class="stat-number">{{ number_format($stats['license_stats']['compliance_rate']) }}%</div>
                <div class="stat-label">License Compliance</div>
                <div class="stat-sub">{{ $stats['license_stats']['expiring_soon'] }} expiring soon</div>
            </div>
        </a>
    </div>

    {{-- Main 2-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Main Content (2/3 width) --}}
        <div class="lg:col-span-2 flex flex-col gap-5">

            {{-- License Compliance Alert --}}
            @if($stats['license_stats']['expiring_soon'] > 0 || $stats['license_stats']['expired'] > 0)
            <div class="ui-card border-l-4 border-amber-400 bg-amber-50">
                <div class="px-5 py-4 border-b border-amber-200 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-amber-800 m-0">&#x1F4CB; License Compliance Alert</h4>
                    <a href="{{ route('business-licenses.compliance') }}" class="btn-sm px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-500 text-white hover:bg-amber-600 no-underline transition-colors">View All</a>
                </div>
                <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @if($stats['license_stats']['expired'] > 0)
                    <a href="{{ route('business-licenses.index', ['status' => 'expired']) }}" class="bg-red-50 border border-red-400 p-3 rounded-lg no-underline text-inherit hover:-translate-y-px hover:shadow-sm transition-all block">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-base">&#x26A0;&#xFE0F;</span>
                            <span class="font-semibold text-red-600 text-sm">{{ $stats['license_stats']['expired'] }} Expired</span>
                        </div>
                        <div class="text-xs text-gray-500">Immediate action required</div>
                    </a>
                    @endif

                    @if($stats['license_stats']['expiring_soon'] > 0)
                    <a href="{{ route('business-licenses.expiring') }}" class="bg-amber-50 border border-amber-400 p-3 rounded-lg no-underline text-inherit hover:-translate-y-px hover:shadow-sm transition-all block">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-base">&#x23F0;</span>
                            <span class="font-semibold text-amber-700 text-sm">{{ $stats['license_stats']['expiring_soon'] }} Expiring Soon</span>
                        </div>
                        <div class="text-xs text-gray-500">Within next 30 days</div>
                    </a>
                    @endif

                    @if($stats['license_stats']['critical_expired'] > 0)
                    <a href="{{ route('business-licenses.index', ['priority' => 'critical', 'status' => 'expired']) }}" class="bg-red-50 border border-red-400 p-3 rounded-lg no-underline text-inherit hover:-translate-y-px hover:shadow-sm transition-all block">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-base">&#x1F6A8;</span>
                            <span class="font-semibold text-red-600 text-sm">{{ $stats['license_stats']['critical_expired'] }} Critical Expired</span>
                        </div>
                        <div class="text-xs text-gray-500">High business impact</div>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Terminal Status Chart --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CA; Terminal Status Distribution</h4>
                    <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Active: {{ $stats['active_terminals'] }}</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span> Offline: {{ $stats['offline_terminals'] }}</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span> Maintenance: {{ $stats['maintenance_terminals'] }}</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Faulty: {{ $stats['faulty_terminals'] }}</span>
                    </div>
                </div>
                <div class="ui-card-body">
                    <div class="h-[300px] relative">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- License Status Chart --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CB; License Status Overview</h4>
                    <a href="{{ route('business-licenses.index') }}" class="btn-secondary btn-sm">View All Licenses</a>
                </div>
                <div class="ui-card-body">
                    <div class="h-[250px] relative">
                        <canvas id="licenseChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Monthly Trends Chart --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4C8; Monthly Growth Trends</h4>
                </div>
                <div class="ui-card-body">
                    <div class="h-[250px] relative">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Regional Distribution --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F5FA;&#xFE0F; Regional Distribution</h4>
                </div>
                <div class="ui-card-body">
                    @if($stats['regional_data']->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($stats['regional_data'] as $region => $data)
                        <a href="{{ route('pos-terminals.index', ['region' => $region]) }}" class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500 no-underline text-gray-900 transition-all hover:bg-blue-50 hover:border-blue-700 hover:-translate-y-0.5 hover:shadow-md block">
                            <div class="flex items-center justify-between mb-2.5">
                                <h5 class="text-sm font-semibold text-gray-800 m-0">{{ $region }}</h5>
                                <span class="badge badge-blue">{{ $data['total'] }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mb-2">
                                <span>Active: {{ $data['active'] }}</span>
                                <span>Issues: {{ $data['issues'] }}</span>
                            </div>
                            <div class="bg-gray-200 rounded-full h-1 overflow-hidden">
                                <div class="bg-green-500 h-full rounded-full" style="width: {{ $data['uptime_percentage'] }}%"></div>
                            </div>
                            <div class="text-center mt-1 text-xs text-gray-500">{{ $data['uptime_percentage'] }}% uptime</div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-state-icon">&#x1F5FA;&#xFE0F;</div>
                        <p class="empty-state-msg">No regional data available yet</p>
                        <p class="text-xs text-gray-400 mt-1">Regions will appear here once POS terminals are added with region information</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Recent Activity Feed --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CB; Recent Activity</h4>
                </div>
                <div class="ui-card-body overflow-y-auto max-h-[400px]">
                    @forelse($stats['recent_activity'] as $activity)
                    <div class="flex items-start gap-3 py-3 border-b border-gray-100 last:border-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm flex-shrink-0" style="background: {{ $activity['color'] }};">
                            {{ $activity['icon'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</div>
                            <div class="text-sm text-gray-500 mt-0.5">{{ $activity['description'] }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ $activity['time'] }}</div>
                        </div>
                        @if(isset($activity['action']))
                        <a href="{{ $activity['action']['url'] }}" class="btn-secondary btn-sm flex-shrink-0">{{ $activity['action']['label'] }}</a>
                        @endif
                    </div>
                    @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">&#x1F4DD;</div>
                        <p class="empty-state-msg">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Sidebar (1/3 width) --}}
        <div class="flex flex-col gap-5">

            {{-- Quick Actions --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x26A1; Quick Actions</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-2">
                    <a href="{{ route('pos-terminals.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 bg-white border-2 border-gray-200 rounded-lg text-sm text-gray-700 no-underline transition-all hover:border-[#1a3a5c] hover:text-[#1a3a5c] hover:bg-blue-50 hover:-translate-y-px">
                        <span>&#x1F5A5;&#xFE0F;</span><span>Add New Terminal</span>
                    </a>
                    <a href="{{ route('business-licenses.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 bg-white border-2 border-gray-200 rounded-lg text-sm text-gray-700 no-underline transition-all hover:border-[#1a3a5c] hover:text-[#1a3a5c] hover:bg-blue-50 hover:-translate-y-px">
                        <span>&#x1F4CB;</span><span>Add Business License</span>
                    </a>
                    <a href="{{ route('clients.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 bg-white border-2 border-gray-200 rounded-lg text-sm text-gray-700 no-underline transition-all hover:border-[#1a3a5c] hover:text-[#1a3a5c] hover:bg-blue-50 hover:-translate-y-px">
                        <span>&#x1F3E2;</span><span>Add New Client</span>
                    </a>
                    <a href="{{ route('pos-terminals.index') }}?status=faulty" class="flex items-center gap-2.5 px-3 py-2.5 bg-white border-2 border-gray-200 rounded-lg text-sm text-gray-700 no-underline transition-all hover:border-[#1a3a5c] hover:text-[#1a3a5c] hover:bg-blue-50 hover:-translate-y-px">
                        <span>&#x1F527;</span><span>View Faulty Terminals</span>
                    </a>
                    <a href="{{ route('business-licenses.expiring') }}" class="flex items-center gap-2.5 px-3 py-2.5 bg-white border-2 border-gray-200 rounded-lg text-sm text-gray-700 no-underline transition-all hover:border-[#1a3a5c] hover:text-[#1a3a5c] hover:bg-blue-50 hover:-translate-y-px">
                        <span>&#x23F0;</span><span>Expiring Licenses</span>
                    </a>
                    <a href="{{ route('pos-terminals.column-mapping') }}" class="flex items-center gap-2.5 px-3 py-2.5 bg-white border-2 border-gray-200 rounded-lg text-sm text-gray-700 no-underline transition-all hover:border-[#1a3a5c] hover:text-[#1a3a5c] hover:bg-blue-50 hover:-translate-y-px">
                        <span>&#x2699;&#xFE0F;</span><span>Column Mapping</span>
                    </a>
                </div>
            </div>

            {{-- License Summary --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CB; License Summary</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Total Licenses</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $stats['license_stats']['total_licenses'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Active</span>
                        <span class="text-sm font-semibold text-green-600">{{ $stats['license_stats']['active_licenses'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Expiring Soon</span>
                        <span class="text-sm font-semibold text-amber-600">{{ $stats['license_stats']['expiring_soon'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Expired</span>
                        <span class="text-sm font-semibold text-red-600">{{ $stats['license_stats']['expired'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Critical Priority</span>
                        <span class="text-sm font-semibold text-purple-600">{{ $stats['license_stats']['critical_licenses'] }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Annual Cost</span>
                        <span class="text-sm font-semibold text-blue-600">${{ number_format($stats['license_stats']['annual_cost'], 0) }}</span>
                    </div>
                </div>
            </div>

            {{-- Upcoming Renewals --}}
            @if(isset($stats['upcoming_renewals']) && $stats['upcoming_renewals']->count() > 0)
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F504; Upcoming Renewals</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-2.5">
                    @foreach($stats['upcoming_renewals']->take(5) as $license)
                    <div class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs flex-shrink-0 {{ $license->is_expired ? 'bg-red-500' : 'bg-amber-500' }}">
                            {{ $license->is_expired ? '!' : '~' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ Str::limit($license->license_name, 20) }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $license->is_expired ? 'Expired' : 'Expires' }} {{ $license->expiry_date->format('M d') }}
                            </div>
                        </div>
                        <a href="{{ route('business-licenses.renew', $license) }}" class="btn-sm px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-500 text-white hover:bg-amber-600 no-underline transition-colors flex-shrink-0">Renew</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- System Health --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F49A; System Health</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Network Uptime</span>
                        <span class="text-sm font-semibold text-green-600">{{ $stats['network_uptime'] }}%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-1.5 overflow-hidden -mt-1">
                        <div class="bg-green-500 h-full rounded-full" style="width: {{ $stats['network_uptime'] }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">License Compliance</span>
                        <span class="text-sm font-semibold text-blue-600">{{ $stats['license_stats']['compliance_rate'] }}%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-1.5 overflow-hidden -mt-1">
                        <div class="bg-blue-500 h-full rounded-full" style="width: {{ $stats['license_stats']['compliance_rate'] }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Service Level</span>
                        <span class="text-sm font-semibold text-blue-600">{{ $stats['service_level'] }}%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-1.5 overflow-hidden -mt-1">
                        <div class="bg-blue-500 h-full rounded-full" style="width: {{ $stats['service_level'] }}%"></div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Avg Response Time</span>
                        <span class="text-sm font-semibold text-amber-600">{{ $stats['avg_response_time'] }}h</span>
                    </div>
                </div>
            </div>

            {{-- Top Clients --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F3C6; Top Clients</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-2.5">
                    @foreach($stats['top_clients'] as $client)
                    @php
                        $clientStatusClass = match($client['status']) {
                            'active'   => 'badge-green',
                            'inactive' => 'badge-red',
                            default    => 'badge-gray',
                        };
                    @endphp
                    <div class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg">
                        <div class="w-6 h-6 rounded-full bg-[#1a3a5c] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ $loop->iteration }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $client['name'] }}</div>
                            <div class="text-xs text-gray-500">{{ $client['terminals'] }} terminals</div>
                        </div>
                        <span class="badge {{ $clientStatusClass }} flex-shrink-0">{{ ucfirst($client['status']) }}</span>
                        <a href="{{ route('clients.show', $client['id']) }}" class="btn-secondary btn-sm flex-shrink-0">View</a>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Contract Status --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4C4; Contract Status</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Active Contracts</span>
                        <span class="text-sm font-semibold text-green-600">{{ $stats['contract_stats']['active'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Expiring Soon</span>
                        <span class="text-sm font-semibold text-amber-600">{{ $stats['contract_stats']['expiring_soon'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Expired</span>
                        <span class="text-sm font-semibold text-red-600">{{ $stats['contract_stats']['expired'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Team Overview --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F465; Team Overview</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Total Employees</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $stats['employee_stats']['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Field Technicians</span>
                        <span class="text-sm font-semibold text-blue-600">{{ $stats['employee_stats']['technicians'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Managers</span>
                        <span class="text-sm font-semibold text-purple-600">{{ $stats['employee_stats']['managers'] }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    font: { size: 12 }
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
            backgroundColor: ['#4caf50', '#f44336', '#ff9800', '#9e9e9e', '#607d8b'],
            borderColor: ['#388e3c', '#d32f2f', '#f57c00', '#757575', '#455a64'],
            borderWidth: 2,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { ticks: { font: { size: 11 } } }
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
                labels: { usePointStyle: true, font: { size: 12 } }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
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

// Auto-refresh every 5 minutes
setInterval(() => {
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 300000);

// Real-time clock
function updateClock() {
    const now = new Date();
    document.title = `Revival Technologies - ${now.toLocaleTimeString()}`;
}
setInterval(updateClock, 1000);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    showLicenseNotifications();
});

// Pulse animation for critical alerts
const criticalAlerts = document.querySelectorAll('.alert-critical');
criticalAlerts.forEach(alert => {
    alert.style.animation = 'pulse 2s infinite';
});

const style = document.createElement('style');
style.textContent = `@keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }`;
document.head.appendChild(style);
</script>
@endsection
