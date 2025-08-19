<!-- Statistics Section Partial -->
<div id="stats-section" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: relative;">
    
    <!-- Header with Toggle -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="margin: 0; color: #333; font-size: 20px;">📊 Terminal Analytics Dashboard</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <!-- Chart View Toggle -->
            <select id="chart-view-selector" onchange="switchChartView()" style="padding: 6px 12px; border: 1px solid #dee2e6; border-radius: 4px; font-size: 12px;">
                <option value="overview">Overview (6 Charts)</option>
                <option value="service">Service Focus</option>
                <option value="distribution">Distribution Focus</option>
                <option value="performance">Performance Focus</option>
            </select>
            
            <button onclick="toggleStats()" id="stats-toggle" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; color: #666;">
                <span id="toggle-text">Hide Charts</span> <span id="toggle-icon">▲</span>
            </button>
        </div>
    </div>
    
    <!-- Quick Stats Cards (Always Visible) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
        <div class="stat-card" style="text-align: center; padding: 15px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px; border: 1px solid #dee2e6; transition: transform 0.2s ease;">
            <div id="total-count" style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 4px;">
                {{ $stats['total_terminals'] ?? 0 }}
            </div>
            <div style="font-size: 12px; color: #666; text-transform: uppercase; font-weight: 500;">Total Terminals</div>
        </div>
        
        <div class="stat-card" style="text-align: center; padding: 15px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-radius: 8px; border: 1px solid #b8dacc; transition: transform 0.2s ease;">
            <div id="active-count" style="font-size: 28px; font-weight: 700; color: #155724; margin-bottom: 4px;">
                {{ $stats['active_terminals'] ?? 0 }}
            </div>
            <div style="font-size: 12px; color: #155724; text-transform: uppercase; font-weight: 500;">Active</div>
            <div style="font-size: 10px; color: #155724; margin-top: 2px;">
                {{ $stats['uptime_percentage'] ?? 0 }}% uptime
            </div>
        </div>
        
        <div class="stat-card" style="text-align: center; padding: 15px; background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%); border-radius: 8px; border: 1px solid #f1b0b7; transition: transform 0.2s ease;">
            <div id="faulty-count" style="font-size: 28px; font-weight: 700; color: #721c24; margin-bottom: 4px;">
                {{ $stats['faulty_terminals'] ?? 0 }}
            </div>
            <div style="font-size: 12px; color: #721c24; text-transform: uppercase; font-weight: 500;">Need Attention</div>
        </div>
        
        <div class="stat-card" style="text-align: center; padding: 15px; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-radius: 8px; border: 1px solid #ffeaa7; transition: transform 0.2s ease;">
            <div id="offline-count" style="font-size: 28px; font-weight: 700; color: #856404; margin-bottom: 4px;">
                {{ $stats['offline_terminals'] ?? 0 }}
            </div>
            <div style="font-size: 12px; color: #856404; text-transform: uppercase; font-weight: 500;">Offline</div>
        </div>
    </div>

    <!-- Charts Section (Collapsible) -->
    <div id="detailed-stats" class="detailed-stats-container">
        
        <!-- Overview View: All 6 Charts (Default) -->
        <div id="overview-charts" class="chart-view active">
            <!-- Row 1: Service & Location -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">🔧 Service Timeline</h3>
                    <div style="position: relative; height: 250px;">
                        <canvas id="serviceDueChart"></canvas>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666; text-align: center;">
                        Maintenance schedule tracking
                    </div>
                </div>

                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">🗺️ Regional Distribution</h3>
                    <div style="position: relative; height: 250px;">
                        <canvas id="locationChart"></canvas>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666; text-align: center;">
                        Terminals by location
                    </div>
                </div>
            </div>

            <!-- Row 2: Client & Models -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">🏦 Client Distribution</h3>
                    <div style="position: relative; height: 250px;">
                        <canvas id="clientChart"></canvas>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666; text-align: center;">
                        Terminals by bank/client
                    </div>
                </div>

                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">📱 Device Models</h3>
                    <div style="position: relative; height: 250px;">
                        <canvas id="modelsChart"></canvas>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666; text-align: center;">
                        Terminal model distribution
                    </div>
                </div>
            </div>

            <!-- Row 3: Performance & Trends -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">⚡ Performance Radar</h3>
                    <div style="position: relative; height: 250px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666; text-align: center;">
                        Key performance indicators
                    </div>
                </div>

                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">📈 Monthly Trends</h3>
                    <div style="position: relative; height: 250px;">
                        <canvas id="trendsChart"></canvas>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666; text-align: center;">
                        Installation & service trends
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Focus View -->
        <div id="service-charts" class="chart-view" style="display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">🔧 Service Timeline</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="serviceDueChart2"></canvas>
                    </div>
                </div>
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">📈 Service Trends</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="trendsChart2"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution Focus View -->
        <div id="distribution-charts" class="chart-view" style="display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">🏦 Clients</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="clientChart2"></canvas>
                    </div>
                </div>
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">🗺️ Locations</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="locationChart2"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Focus View -->
        <div id="performance-charts" class="chart-view" style="display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">⚡ Performance Overview</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="performanceChart2"></canvas>
                    </div>
                </div>
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">📱 Device Models</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="modelsChart2"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics Row -->
        <div style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div style="text-align: center; padding: 12px; background: #e7f3ff; border-radius: 6px; border: 1px solid #b3d9ff;">
                <div style="font-size: 18px; font-weight: 600; color: #0066cc; margin-bottom: 2px;">
                    {{ $stats['recently_serviced'] ?? 0 }}
                </div>
                <div style="font-size: 11px; color: #0066cc; text-transform: uppercase;">Recently Serviced</div>
            </div>
            
            <div style="text-align: center; padding: 12px; background: #fff0e6; border-radius: 6px; border: 1px solid #ffcc99;">
                <div style="font-size: 18px; font-weight: 600; color: #cc6600; margin-bottom: 2px;">
                    {{ $stats['service_due'] ?? 0 }}
                </div>
                <div style="font-size: 11px; color: #cc6600; text-transform: uppercase;">Service Due</div>
            </div>
            
            <div style="text-align: center; padding: 12px; background: #f0f8f0; border-radius: 6px; border: 1px solid #b3e6b3;">
                <div style="font-size: 18px; font-weight: 600; color: #008000; margin-bottom: 2px;">
                    {{ $stats['recent_installations'] ?? 0 }}
                </div>
                <div style="font-size: 11px; color: #008000; text-transform: uppercase;">New Installs</div>
            </div>
            
            <div style="text-align: center; padding: 12px; background: #fdf2f8; border-radius: 6px; border: 1px solid #f8bbd9;">
                <div style="font-size: 18px; font-weight: 600; color: #be185d; margin-bottom: 2px;">
                    {{ count($stats['model_distribution'] ?? []) }}
                </div>
                <div style="font-size: 11px; color: #be185d; text-transform: uppercase;">Device Types</div>
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