@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="{{ route('client-dashboards.index') }}"
               style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; background: #F8F9FA; color: #666; text-decoration: none; transition: all 0.2s ease; font-size: 18px;"
               onmouseover="this.style.background='#E0E0E0'"
               onmouseout="this.style.background='#F8F9FA'">
                ←
            </a>
            <div>
                <h2 style="margin: 0; color: #333; font-size: 24px; font-weight: 600;">{{ $client->company_name }}</h2>
                <div style="display: flex; align-items: center; gap: 12px; margin-top: 4px;">
                    <span style="color: #666; font-size: 14px; font-family: monospace;">{{ $client->client_code }}</span>
                    <span class="status-badge status-{{ $client->status }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </div>
            </div>
        </div>
        <button onclick="exportData()" class="btn btn-primary">Export Data</button>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-block-end: 30px;">
        <div class="metric-card">
            <div class="metric-icon" style="background: #F5F5F5;">
                <span style="color: #333; font-size: 24px;">💻</span>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $terminalStats['total'] }}</div>
                <div class="metric-label">TOTAL TERMINALS</div>
                <div style="font-size: 11px; color: #999; margin-top: 2px;">Active network size</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #E8F5E8;">
                <span style="color: #388E3C; font-size: 24px;">✅</span>
            </div>
            <div class="metric-content">
                <div class="metric-number" style="color: #388E3C;">{{ $terminalStats['by_status']['active'] ?? 0 }}</div>
                <div class="metric-label">ACTIVE</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">
                    {{ $terminalStats['total'] > 0 ? round((($terminalStats['by_status']['active'] ?? 0) / $terminalStats['total']) * 100, 1) : 0 }}% uptime
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #FFEBEE;">
                <span style="color: #D32F2F; font-size: 24px;">⚠️</span>
            </div>
            <div class="metric-content">
                <div class="metric-number" style="color: #D32F2F;">
                    {{ ($terminalStats['by_status']['maintenance'] ?? 0) + ($terminalStats['by_status']['faulty'] ?? 0) }}
                </div>
                <div class="metric-label">NEED ATTENTION</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">Require service</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #FFF3E0;">
                <span style="color: #F57C00; font-size: 24px;">📴</span>
            </div>
            <div class="metric-content">
                <div class="metric-number" style="color: #F57C00;">{{ $terminalStats['by_status']['offline'] ?? 0 }}</div>
                <div class="metric-label">OFFLINE</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">Not responding</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #E3F2FD;">
                <span style="color: #1976D2; font-size: 24px;">🔧</span>
            </div>
            <div class="metric-content">
                <div class="metric-number" style="color: #1976D2;">0</div>
                <div class="metric-label">RECENTLY SERVICED</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">Last 30 days</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #FFF0E6;">
                <span style="color: #CC6600; font-size: 24px;">📅</span>
            </div>
            <div class="metric-content">
                <div class="metric-number" style="color: #CC6600;">{{ $terminalStats['total'] }}</div>
                <div class="metric-label">SERVICE DUE</div>
                <div style="font-size: 11px; color: #666; margin-top: 2px;">Maintenance needed</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="content-card" style="margin-block-end: 30px;">
        <h3 style="margin: 0 0 24px 0; color: #333; font-size: 18px; font-weight: 600;">Terminal Analytics</h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-block-end: 24px;">
            <!-- Service Timeline -->
            <div style="background: #FAFAFA; padding: 20px; border-radius: 8px; border: 1px solid #F0F0F0;">
                <h4 style="margin: 0 0 16px 0; color: #333; font-size: 14px; font-weight: 600;">Service Timeline</h4>
                <div style="position: relative; height: 200px;">
                    <canvas id="serviceDueChart"></canvas>
                </div>
                <div style="margin-top: 12px; font-size: 12px; color: #666; text-align: center;">
                    Maintenance schedule tracking
                </div>
            </div>

            <!-- Regional Distribution -->
            <div style="background: #FAFAFA; padding: 20px; border-radius: 8px; border: 1px solid #F0F0F0;">
                <h4 style="margin: 0 0 16px 0; color: #333; font-size: 14px; font-weight: 600;">Regional Distribution</h4>
                <div style="position: relative; height: 200px;">
                    <canvas id="locationChart"></canvas>
                </div>
                <div style="margin-top: 12px; font-size: 12px; color: #666; text-align: center;">
                    Terminals by location
                </div>
            </div>

            <!-- Device Models -->
            <div style="background: #FAFAFA; padding: 20px; border-radius: 8px; border: 1px solid #F0F0F0;">
                <h4 style="margin: 0 0 16px 0; color: #333; font-size: 14px; font-weight: 600;">Device Models</h4>
                <div style="position: relative; height: 200px;">
                    <canvas id="modelsChart"></canvas>
                </div>
                <div style="margin-top: 12px; font-size: 12px; color: #666; text-align: center;">
                    Terminal model distribution
                </div>
            </div>
        </div>

        <!-- Additional Metrics Row -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div style="text-align: center; padding: 16px; border-radius: 8px; background: #E3F2FD; border: 1px solid #BBDEFB;">
                <div style="font-size: 20px; font-weight: 600; color: #1976D2; margin-bottom: 4px;">0</div>
                <div style="font-size: 12px; color: #1976D2; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Recently Serviced</div>
            </div>

            <div style="text-align: center; padding: 16px; border-radius: 8px; background: #FFF0E6; border: 1px solid #FFE0CC;">
                <div style="font-size: 20px; font-weight: 600; color: #CC6600; margin-bottom: 4px;">{{ $terminalStats['total'] }}</div>
                <div style="font-size: 12px; color: #CC6600; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Service Due</div>
            </div>

            <div style="text-align: center; padding: 16px; border-radius: 8px; background: #E8F5E8; border: 1px solid #C8E6C9;">
                <div style="font-size: 20px; font-weight: 600; color: #388E3C; margin-bottom: 4px;">0</div>
                <div style="font-size: 12px; color: #388E3C; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">New Installs</div>
            </div>

            <div style="text-align: center; padding: 16px; border-radius: 8px; background: #F3E5F5; border: 1px solid #E1BEE7;">
                <div style="font-size: 20px; font-weight: 600; color: #7B1FA2; margin-bottom: 4px;">4</div>
                <div style="font-size: 12px; color: #7B1FA2; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Device Types</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 30px;">
        <!-- POS Terminals Table -->
        <div class="content-card">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 20px; padding-bottom: 16px; border-bottom: 1px solid #F0F0F0;">
                <div>
                    <h3 style="margin: 0; color: #333; font-size: 18px; font-weight: 600;">POS Terminals</h3>
                    <p style="margin: 4px 0 0 0; color: #666; font-size: 14px;">{{ $terminalStats['total'] }} total terminals</p>
                </div>
                <button onclick="exportTable()" class="btn">Export</button>
            </div>

            <!-- Filters -->
            <div class="filters-section" style="background: #F8F9FA; padding: 20px; border-radius: 8px; margin-block-end: 20px; border: 1px solid #F0F0F0;">
                <div style="display: flex; align-items: center; gap: 12px; margin-block-end: 16px;">
                    <span style="color: #666; font-size: 20px;">🔍</span>
                    <h4 style="margin: 0; color: #333; font-size: 14px; font-weight: 600;">Filters & Search</h4>
                    <button type="button" onclick="clearFilters()" style="background: none; border: none; color: #1976D2; font-size: 12px; cursor: pointer; text-decoration: underline;">Clear All Filters</button>
                </div>

                <form method="GET" action="{{ route('client-dashboards.show', $client) }}">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap: 12px; margin-block-end: 12px;">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search terminals..."
                               style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <button type="button" onclick="clearFilters()" class="btn">Clear</button>
                        <button type="button" onclick="addTerminal()" class="btn btn-primary">+ Add Terminal</button>
                        <button type="button" onclick="exportTable()" class="btn">Export</button>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px;">
                        <select name="client" style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                            <option value="">All Clients</option>
                            <option value="{{ $client->id }}" selected>{{ $client->company_name }}</option>
                        </select>
                        <select name="status" style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="faulty" {{ request('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                        </select>
                        <select name="region" style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                            <option value="">All Regions</option>
                            @if(isset($regions))
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <select name="city" style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                            <option value="">All Cities</option>
                            @if(isset($cities))
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <select name="province" style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;">
                            <option value="">All Provinces</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="terminals-table">
                    <thead>
                        <tr>
                            <th>Terminal ID</th>
                            <th>Client/Bank</th>
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
                        <tr>
                            <td>
                                <div style="font-weight: 500; color: #333;">{{ $terminal->terminal_id }}</div>
                            </td>
                            <td>
                                <div style="color: #333;">{{ $client->company_name }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: #333;">{{ $terminal->merchant_name ?? 'City Electronics' }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $terminal->merchant_contact_person ?? 'Mike Smith' }}</div>
                            </td>
                            <td>
                                <div style="color: #333;">{{ $terminal->merchant_contact_person ?? 'Jane Doe' }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $terminal->merchant_phone ?? '+254734567890' }}</div>
                            </td>
                            <td>
                                <div style="color: #333;">{{ $terminal->city ?? 'Unknown' }}</div>
                            </td>
                            <td>
                                <span class="terminal-status-badge status-{{ $terminal->current_status ?? 'maintenance' }}">
                                    {{ strtoupper($terminal->current_status ?? 'MAINTENANCE') }}
                                </span>
                            </td>
                            <td>
                                <div style="color: #333; font-size: 14px;">
                                    {{ $terminal->last_service_date ? \Carbon\Carbon::parse($terminal->last_service_date)->format('M d, Y') : 'Jul 15, 2024' }}
                                </div>
                                <div style="font-size: 12px; color: #999;">
                                    {{ $terminal->last_service_date ? \Carbon\Carbon::parse($terminal->last_service_date)->diffForHumans() : '1 year ago' }}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px;">
                                    <button onclick="viewTerminal('{{ $terminal->id }}')" class="action-btn" title="View">👁️</button>
                                    <button onclick="editTerminal('{{ $terminal->id }}')" class="action-btn" title="Edit">✏️</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <!-- Sample Data Rows -->
                        <tr>
                            <td>
                                <div style="font-weight: 500; color: #333;">POS-002</div>
                            </td>
                            <td>
                                <div style="color: #333;">{{ $client->company_name }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: #333;">City Electronics</div>
                                <div style="font-size: 12px; color: #666;">Mike Smith</div>
                            </td>
                            <td>
                                <div style="color: #333;">Mike Smith</div>
                                <div style="font-size: 12px; color: #666;">+254734567890</div>
                            </td>
                            <td>
                                <div style="color: #333;">Unknown</div>
                            </td>
                            <td>
                                <span class="terminal-status-badge status-maintenance">
                                    MAINTENANCE
                                </span>
                            </td>
                            <td>
                                <div style="color: #333; font-size: 14px;">Jul 15, 2024</div>
                                <div style="font-size: 12px; color: #999;">1 year ago</div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px;">
                                    <button onclick="viewTerminal('sample')" class="action-btn" title="View">👁️</button>
                                    <button onclick="editTerminal('sample')" class="action-btn" title="Edit">✏️</button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($terminals) && method_exists($terminals, 'links'))
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $terminals->appends(request()->query())->links() }}
            </div>
            @endif

            <!-- Projects Section -->
            <div style="border-top: 1px solid #F0F0F0; margin-top: 24px; padding-top: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 16px;">
                    <div>
                        <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Active Projects</h4>
                        <p style="margin: 4px 0 0 0; color: #666; font-size: 14px;">{{ $projects->count() ?? 3 }} ongoing projects</p>
                    </div>
                    <button onclick="createProject()" class="btn">+ Create Project</button>
                </div>

                @if(isset($projects) && $projects->count() > 0)
                    @foreach($projects->take(3) as $project)
                    <div style="background: #FAFAFA; border: 1px solid #F0F0F0; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h5 style="margin: 0; color: #333; font-size: 14px; font-weight: 600;">{{ $project->project_name }}</h5>
                                <p style="margin: 4px 0; color: #666; font-size: 12px; font-family: monospace;">{{ $project->project_code }}</p>
                                <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">{{ $project->description }}</p>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="status-badge status-{{ $project->status }}">{{ ucfirst($project->status) }}</span>
                                <span style="font-size: 12px; color: #666;">{{ ucfirst($project->priority ?? 'normal') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div style="background: #FAFAFA; border: 1px solid #F0F0F0; border-radius: 8px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <h5 style="margin: 0; color: #333; font-size: 14px; font-weight: 600;">Terminal Discovery Phase 1</h5>
                            <p style="margin: 4px 0; color: #666; font-size: 12px; font-family: monospace;">CLI-DIS-202508-01</p>
                            <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">Initial discovery and assessment of all POS terminals...</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span class="status-badge status-active">Active</span>
                            <span style="font-size: 12px; color: #666;">Discovery</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Recent Visits -->
    <div class="content-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 16px; padding-bottom: 12px; border-bottom: 1px solid #F0F0F0;">
            <h4 style="margin: 0; color: #333; font-size: 14px; font-weight: 600;">Recent Visits</h4>
            <a href="{{ route('visits.index') }}?client_id={{ $client->id }}"
               style="background: none; border: none; color: #1976D2; font-size: 12px; cursor: pointer; text-decoration: underline;">
               View More
            </a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @if(isset($recentVisits) && $recentVisits->count() > 0)
                @foreach($recentVisits->take(4) as $visit)
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <div style="font-weight: 500; color: #333; font-size: 14px;">
                            <a href="{{ route('client-dashboards.terminals.show', [$client, $visit->posTerminal]) }}"
                               style="color: #333; text-decoration: none;">
                               Terminal {{ $visit->posTerminal->terminal_id ?? 'N/A' }}
                            </a>
                        </div>
                        <div style="color: #666; font-size: 12px;">{{ $visit->technician->full_name ?? 'Technician' }}</div>
                    </div>
                    <div style="text-align: right;">
                        <span class="status-badge status-{{ $visit->status ?? 'active' }}" style="font-size: 10px; padding: 2px 6px;">
                            {{ ucfirst($visit->status ?? 'Open') }}
                        </span>
                        <div style="font-size: 11px; color: #999; margin-top: 2px;">
                            {{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') : 'Aug 26, 2025' }}
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                {{-- Sample data when no visits exist --}}
                @for($i = 1; $i <= 4; $i++)
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <div style="font-weight: 500; color: #333; font-size: 14px;">Terminal {{ $i }}</div>
                        <div style="color: #666; font-size: 12px;">Monah Chimwa</div>
                    </div>
                    <div style="text-align: right;">
                        <span class="status-badge status-active" style="font-size: 10px; padding: 2px 6px;">Open</span>
                        <div style="font-size: 11px; color: #999; margin-top: 2px;">Aug 26, 2025</div>
                    </div>
                </div>
                @endfor
            @endif
        </div>
    </div>

    <!-- Open Tickets -->
    <div class="content-card">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #F0F0F0; margin-block-end: 16px;">
            <h4 style="margin: 0; color: #333; font-size: 14px; font-weight: 600;">Open Tickets</h4>
            <a href="{{ route('tickets.index') }}?client_id={{ $client->id }}"
               style="background: none; border: none; color: #1976D2; font-size: 12px; cursor: pointer; text-decoration: underline;">
               View All
            </a>
        </div>
        @if(isset($openTickets) && $openTickets->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($openTickets->take(3) as $ticket)
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <div style="font-weight: 500; color: #333; font-size: 14px;">
                            <a href="{{ route('tickets.show', $ticket) }}"
                               style="color: #333; text-decoration: none;">
                               {{ $ticket->title ?? 'Support Ticket' }}
                            </a>
                        </div>
                        <div style="color: #666; font-size: 12px;">{{ $ticket->posTerminal->terminal_id ?? 'Terminal' }}</div>
                    </div>
                    <div style="text-align: right;">
                        <span class="status-badge status-{{ $ticket->status }}" style="font-size: 10px; padding: 2px 6px;">
                            {{ ucfirst($ticket->status) }}
                        </span>
                        <div style="font-size: 11px; color: #999; margin-top: 2px;">{{ $ticket->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
        <div style="text-align: center; padding: 20px; color: #666;">
            <div style="font-size: 32px; margin-bottom: 8px;">🎫</div>
            <p style="margin: 0; font-size: 14px;">No open tickets</p>
        </div>
        @endif
    </div>

    <!-- Active Projects -->
    <div class="content-card">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #F0F0F0; margin-block-end: 16px;">
            <h4 style="margin: 0; color: #333; font-size: 14px; font-weight: 600;">Active Projects</h4>
            <a href="{{ route('client-dashboards.projects.create', $client) }}"
               style="background: none; border: none; color: #1976D2; font-size: 12px; cursor: pointer; text-decoration: underline;">
               + New Project
            </a>
        </div>
        @if(isset($projects) && $projects->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($projects->take(2) as $project)
                <div style="background: #FAFAFA; border: 1px solid #F0F0F0; border-radius: 8px; padding: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                        <div style="font-weight: 500; color: #333; font-size: 13px;">{{ $project->project_name }}</div>
                        <span class="status-badge status-{{ $project->status }}" style="font-size: 10px; padding: 2px 6px;">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                    <div style="font-size: 11px; color: #666; font-family: monospace; margin-bottom: 4px;">{{ $project->project_code }}</div>
                    <div style="font-size: 12px; color: #666;">{{ Str::limit($project->description, 60) }}</div>
                </div>
                @endforeach
            </div>
        @else
        <div style="text-align: center; padding: 20px; color: #666;">
            <div style="font-size: 32px; margin-bottom: 8px;">📋</div>
            <p style="margin: 0; font-size: 14px;">No active projects</p>
        </div>
        @endif
    </div>
</div>

{{-- Add this JavaScript to the bottom of your view to fix the terminal view links --}}
<script>
// Function to view terminal details
function viewTerminalDetails(terminalId) {
    if (terminalId && terminalId !== 'sample') {
        window.location.href = `/client-dashboards/{{ $client->id }}/terminals/${terminalId}`;
    }
}

// Update the table action buttons to use the correct terminal IDs
document.addEventListener('DOMContentLoaded', function() {
    // Fix any sample terminal links
    document.querySelectorAll('a[href*="/terminals/sample"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            alert('This is sample data. No terminal details available.');
        });
    });
});
</script>
/* Metric Cards */
.metric-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #F0F0F0;
    display: flex;
    align-items: center;
    gap: 16px;
}

.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.metric-content {
    flex: 1;
}

.metric-number {
    font-size: 28px;
    font-weight: bold;
    color: #333;
    line-height: 1;
    margin-bottom: 4px;
}

.metric-label {
    font-size: 12px;
    color: #666;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* Content Card */
.content-card {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #F0F0F0;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
}

.terminals-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.terminals-table th {
    background: #F8F9FA;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #E0E0E0;
}

.terminals-table td {
    padding: 12px;
    border-bottom: 1px solid #F0F0F0;
    vertical-align: top;
}

.terminals-table tr:hover {
    background: #FAFAFA;
}

/* Status Badges */
.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    text-transform: capitalize;
}

.status-active { background: #E8F5E8; color: #2E7D32; }
.status-pending { background: #FFF3E0; color: #F57C00; }
.status-inactive { background: #F5F5F5; color: #666; }
.status-planning { background: #E3F2FD; color: #1976D2; }
.status-completed { background: #E8F5E8; color: #2E7D32; }
.status-on_hold { background: #FFF3E0; color: #F57C00; }
.status-cancelled { background: #FFEBEE; color: #D32F2F; }

.terminal-status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.terminal-status-badge.status-active { background: #E8F5E8; color: #2E7D32; }
.terminal-status-badge.status-offline { background: #FFF3E0; color: #F57C00; }
.terminal-status-badge.status-maintenance { background: #FFF3E0; color: #F57C00; }
.terminal-status-badge.status-faulty { background: #FFEBEE; color: #D32F2F; }

/* Action Buttons */
.action-btn {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s ease;
    background: #F8F9FA;
    color: #666;
}

.action-btn:hover {
    background: #E0E0E0;
    transform: translateY(-1px);
}

/* Buttons */
.btn {
    padding: 10px 16px;
    border: 1px solid #E0E0E0;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #1976D2;
    color: #1976D2;
}

.btn-primary {
    background: #1976D2;
    color: white;
    border-color: #1976D2;
}

.btn-primary:hover {
    background: #1565C0;
    border-color: #1565C0;
    color: white;
}
</style>

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

// Button Functions
function exportData() {
    window.open(`/client-dashboards/{{ $client->id }}/export-data`, '_blank');
}

function exportTable() {
    const params = new URLSearchParams(window.location.search);
    const queryString = params.toString();
    const url = `/client-dashboards/{{ $client->id }}/export-table${queryString ? '?' + queryString : ''}`;
    window.open(url, '_blank');
}

function clearFilters() {
    window.location.href = '{{ route("client-dashboards.show", $client) }}';
}

function addTerminal() {
    window.location.href = '/client-dashboards/{{ $client->id }}/terminals/create';
}

function viewTerminal(terminalId) {
    if (terminalId !== 'sample') {
        window.location.href = `/client-dashboards/{{ $client->id }}/terminals/${terminalId}`;
    }
}

function editTerminal(terminalId) {
    if (terminalId !== 'sample') {
        window.location.href = `/client-dashboards/{{ $client->id }}/terminals/${terminalId}/edit`;
    }
}

function createProject() {
    window.location.href = '/client-dashboards/{{ $client->id }}/projects/create';
}

function generateReport() {
    const reportType = prompt('Generate report:\n\n1. Monthly Summary\n2. Terminal Performance\n3. Activity Report\n\nEnter 1, 2, or 3:');

    if (reportType) {
        const reportTypes = {
            '1': 'monthly',
            '2': 'terminals',
            '3': 'activity'
        };

        if (reportTypes[reportType]) {
            window.open(`/client-dashboards/{{ $client->id }}/reports/${reportTypes[reportType]}`, '_blank');
        } else {
            alert('Invalid selection. Please enter 1, 2, or 3.');
        }
    }
}

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    console.log('Client Dashboard initialized for client {{ $client->id }}');
});
</script>
@endsection
