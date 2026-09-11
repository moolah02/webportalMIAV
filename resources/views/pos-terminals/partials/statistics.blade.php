{{-- Statistics Section Partial --}}
@once
@push('styles')
<style>
/* ── POS terminals · statistics partial ────────────────── */
#stats-section.pt-statpanel { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 16px; }
.pt-statpanel .pt-statpanel-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; padding: 12px 18px; border-bottom: 1px solid var(--mv-line); }
.pt-statpanel .pt-statpanel-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.pt-statpanel .pt-statpanel-tools { display: flex; align-items: center; gap: 8px; }
.pt-statpanel .pt-statpanel-tools .ui-select { width: auto; height: 32px; padding: 0 30px 0 10px; font-size: 12.5px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236A7686' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 9px center; background-size: 13px; }
.pt-statpanel .pt-btn-sm { height: 32px; padding: 0 10px; font-size: 12.5px; }
.pt-statpanel #toggle-icon { font-size: 9px; color: var(--mv-muted); }
.pt-statpanel .pt-statpanel-body { padding: 16px 18px 18px; }

.pt-statpanel .pt-tiles { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
.pt-statpanel .pt-tile { padding: 12px 14px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface); }
.pt-statpanel .pt-tile-num { font-size: 22px; font-weight: 600; letter-spacing: -.02em; line-height: 1.15; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.pt-statpanel .pt-tile-label { display: flex; align-items: center; gap: 6px; margin-top: 3px; font-size: 12.5px; color: var(--mv-muted); }
.pt-statpanel .pt-tile-sub { margin-top: 2px; font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.pt-statpanel .pt-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--mv-line-strong); flex-shrink: 0; }
.pt-statpanel .pt-dot.is-good { background: var(--mv-good); }
.pt-statpanel .pt-dot.is-warn { background: #C28A2C; }
.pt-statpanel .pt-dot.is-crit { background: var(--mv-crit); }

.pt-statpanel .detailed-stats-container.collapsed { display: none; }
.pt-statpanel .pt-chart-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
.pt-statpanel .pt-chart { padding: 14px 16px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface); min-width: 0; }
.pt-statpanel .pt-chart h3 { margin: 0 0 2px; font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
.pt-statpanel .pt-chart-note { margin: 0 0 10px; font-size: 12px; color: var(--mv-muted); }
.pt-statpanel .pt-chart-canvas { position: relative; height: 250px; }
.pt-statpanel .pt-chart-canvas.is-tall { height: 300px; }

.pt-statpanel .pt-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 16px; }
.pt-statpanel .pt-metric { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; padding: 10px 14px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); }
.pt-statpanel .pt-metric-label { font-size: 12.5px; color: var(--mv-muted); }
.pt-statpanel .pt-metric-num { font-size: 15px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }

@media (max-width: 900px) {
  .pt-statpanel .pt-tiles, .pt-statpanel .pt-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .pt-statpanel .pt-chart-grid { grid-template-columns: 1fr; }
}
</style>
@endpush
@endonce

<div id="stats-section" class="pt-statpanel">

    <!-- Header with Toggle -->
    <div class="pt-statpanel-head">
        <h2>Terminal Analytics Dashboard</h2>
        <div class="pt-statpanel-tools">
            <!-- Chart View Toggle -->
            <select id="chart-view-selector" onchange="switchChartView()" class="ui-select" aria-label="Chart view">
                <option value="overview">Overview (6 Charts)</option>
                <option value="service">Service Focus</option>
                <option value="distribution">Distribution Focus</option>
                <option value="performance">Performance Focus</option>
            </select>

            <button type="button" onclick="toggleStats()" id="stats-toggle" class="btn-secondary pt-btn-sm">
                <span id="toggle-text">Hide Charts</span> <span id="toggle-icon" aria-hidden="true">▲</span>
            </button>
        </div>
    </div>

    <div class="pt-statpanel-body">
        <!-- Quick Stats (Always Visible) -->
        <div class="pt-tiles">
            <div class="pt-tile">
                <div id="total-count" class="pt-tile-num">{{ $stats['total_terminals'] ?? 0 }}</div>
                <div class="pt-tile-label">Total Terminals</div>
            </div>

            <div class="pt-tile">
                <div id="active-count" class="pt-tile-num">{{ $stats['active_terminals'] ?? 0 }}</div>
                <div class="pt-tile-label"><span class="pt-dot is-good" aria-hidden="true"></span>Active</div>
                <div class="pt-tile-sub">{{ $stats['uptime_percentage'] ?? 0 }}% uptime</div>
            </div>

            <div class="pt-tile">
                <div id="faulty-count" class="pt-tile-num">{{ $stats['faulty_terminals'] ?? 0 }}</div>
                <div class="pt-tile-label"><span class="pt-dot is-crit" aria-hidden="true"></span>Need Attention</div>
            </div>

            <div class="pt-tile">
                <div id="offline-count" class="pt-tile-num">{{ $stats['offline_terminals'] ?? 0 }}</div>
                <div class="pt-tile-label"><span class="pt-dot" aria-hidden="true"></span>Offline</div>
            </div>
        </div>

        <!-- Charts Section (Collapsible) -->
        <div id="detailed-stats" class="detailed-stats-container">

            <!-- Overview View: All 6 Charts (Default) -->
            <div id="overview-charts" class="chart-view active">
                <div class="pt-chart-grid">
                    <div class="chart-container pt-chart">
                        <h3>Service Timeline</h3>
                        <p class="pt-chart-note">Maintenance schedule tracking</p>
                        <div class="pt-chart-canvas"><canvas id="serviceDueChart"></canvas></div>
                    </div>

                    <div class="chart-container pt-chart">
                        <h3>Regional Distribution</h3>
                        <p class="pt-chart-note">Terminals by location</p>
                        <div class="pt-chart-canvas"><canvas id="locationChart"></canvas></div>
                    </div>

                    <div class="chart-container pt-chart">
                        <h3>Client Distribution</h3>
                        <p class="pt-chart-note">Terminals by bank/client</p>
                        <div class="pt-chart-canvas"><canvas id="clientChart"></canvas></div>
                    </div>

                    <div class="chart-container pt-chart">
                        <h3>Device Models</h3>
                        <p class="pt-chart-note">Terminal model distribution</p>
                        <div class="pt-chart-canvas"><canvas id="modelsChart"></canvas></div>
                    </div>

                    <div class="chart-container pt-chart">
                        <h3>Performance Radar</h3>
                        <p class="pt-chart-note">Key performance indicators</p>
                        <div class="pt-chart-canvas"><canvas id="performanceChart"></canvas></div>
                    </div>

                    <div class="chart-container pt-chart">
                        <h3>Monthly Trends</h3>
                        <p class="pt-chart-note">Installation &amp; service trends</p>
                        <div class="pt-chart-canvas"><canvas id="trendsChart"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Service Focus View -->
            <div id="service-charts" class="chart-view" style="display: none;">
                <div class="pt-chart-grid">
                    <div class="chart-container pt-chart">
                        <h3>Service Timeline</h3>
                        <div class="pt-chart-canvas is-tall"><canvas id="serviceDueChart2"></canvas></div>
                    </div>
                    <div class="chart-container pt-chart">
                        <h3>Service Trends</h3>
                        <div class="pt-chart-canvas is-tall"><canvas id="trendsChart2"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Distribution Focus View -->
            <div id="distribution-charts" class="chart-view" style="display: none;">
                <div class="pt-chart-grid">
                    <div class="chart-container pt-chart">
                        <h3>Clients</h3>
                        <div class="pt-chart-canvas is-tall"><canvas id="clientChart2"></canvas></div>
                    </div>
                    <div class="chart-container pt-chart">
                        <h3>Locations</h3>
                        <div class="pt-chart-canvas is-tall"><canvas id="locationChart2"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Performance Focus View -->
            <div id="performance-charts" class="chart-view" style="display: none;">
                <div class="pt-chart-grid">
                    <div class="chart-container pt-chart">
                        <h3>Performance Overview</h3>
                        <div class="pt-chart-canvas is-tall"><canvas id="performanceChart2"></canvas></div>
                    </div>
                    <div class="chart-container pt-chart">
                        <h3>Device Models</h3>
                        <div class="pt-chart-canvas is-tall"><canvas id="modelsChart2"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics Row -->
            <div class="pt-metrics">
                <div class="pt-metric">
                    <span class="pt-metric-label">Recently Serviced</span>
                    <span class="pt-metric-num">{{ $stats['recently_serviced'] ?? 0 }}</span>
                </div>
                <div class="pt-metric">
                    <span class="pt-metric-label">Service Due</span>
                    <span class="pt-metric-num">{{ $stats['service_due'] ?? 0 }}</span>
                </div>
                <div class="pt-metric">
                    <span class="pt-metric-label">New Installs</span>
                    <span class="pt-metric-num">{{ $stats['recent_installations'] ?? 0 }}</span>
                </div>
                <div class="pt-metric">
                    <span class="pt-metric-label">Device Types</span>
                    <span class="pt-metric-num">{{ count($stats['model_distribution'] ?? []) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Data Script (Injected by Controller) -->
<script>
window.chartData = {
    stats: @json($stats ?? []),
    serviceDue: {
        recentlyServiced: {{ $stats['recently_serviced'] ?? 0 }},
        serviceDueSoon: {{ max(0, ($stats['service_due'] ?? 0) - ($stats['overdue_service'] ?? 0)) }},
        overdueService: {{ $stats['overdue_service'] ?? 0 }},
        neverServiced: {{ $stats['never_serviced'] ?? 0 }}
    },
    clientDistribution: @json($stats['client_distribution'] ?? []),
    modelDistribution: @json($stats['model_distribution'] ?? [])
};
</script>
