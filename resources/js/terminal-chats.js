/**
 * Terminal Charts Module
 * All chart functionality for POS Terminal dashboard
 */

class TerminalCharts {
    constructor() {
        this.charts = {};
        this.chartData = {};
    }

    // Initialize all charts
    init(data = {}) {
        this.chartData = data;
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.renderAllCharts());
        } else {
            this.renderAllCharts();
        }
    }

    // Render all available charts
    renderAllCharts() {
        setTimeout(() => {
            this.renderServiceDueChart();
            this.renderLocationChart();
            this.renderClientDistributionChart();
            this.renderTerminalModelsChart();
            this.renderPerformanceChart();
            this.renderInstallationTrendsChart();
        }, 100);
    }

    // Chart 1: Service Due Timeline
    renderServiceDueChart() {
        const ctx = document.getElementById('serviceDueChart')?.getContext('2d');
        if (!ctx) return;

        if (this.charts.serviceDue) {
            this.charts.serviceDue.destroy();
        }

        const data = this.chartData.serviceDue || {
            recentlyServiced: 0,
            serviceDueSoon: 0,
            overdueService: 0,
            neverServiced: 0
        };

        this.charts.serviceDue = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Recently Serviced', 'Due Soon', 'Overdue', 'Never Serviced'],
                datasets: [{
                    label: 'Terminals',
                    data: [
                        data.recentlyServiced,
                        data.serviceDueSoon,
                        data.overdueService,
                        data.neverServiced
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 50
                }]
            },
            options: this.getBarChartOptions('Service Timeline', [
                'Recently Serviced (Last 30 days)',
                'Service Due Soon (60-90 days)',
                'Overdue Service (90+ days)',
                'Never Serviced'
            ])
        });
    }

    // Chart 2: Location Distribution
    renderLocationChart() {
        const ctx = document.getElementById('locationChart')?.getContext('2d');
        if (!ctx) return;

        if (this.charts.location) {
            this.charts.location.destroy();
        }

        const locationData = this.getLocationDataFromTable();

        this.charts.location = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: locationData.labels.length > 0 ? locationData.labels : ['No Data'],
                datasets: [{
                    label: 'Terminals',
                    data: locationData.data.length > 0 ? locationData.data : [0],
                    backgroundColor: [
                        '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8',
                        '#6c757d', '#e83e8c', '#fd7e14', '#6610f2', '#20c997'
                    ].slice(0, locationData.data.length),
                    borderRadius: 4,
                    borderSkipped: false,
                    barThickness: 40
                }]
            },
            options: this.getBarChartOptions('Regional Distribution')
        });
    }

    // Chart 3: Client Distribution
    renderClientDistributionChart() {
        const ctx = document.getElementById('clientChart')?.getContext('2d');
        if (!ctx) return;

        if (this.charts.client) {
            this.charts.client.destroy();
        }

        const clientData = this.chartData.clientDistribution || {};
        const labels = Object.keys(clientData);
        const data = Object.values(clientData);

        this.charts.client = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#007bff', '#28a745', '#dc3545', '#ffc107',
                        '#17a2b8', '#6c757d', '#e83e8c'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: this.getDoughnutChartOptions('Client Distribution')
        });
    }

    // Chart 4: Terminal Models
    renderTerminalModelsChart() {
        const ctx = document.getElementById('modelsChart')?.getContext('2d');
        if (!ctx) return;

        if (this.charts.models) {
            this.charts.models.destroy();
        }

        const modelData = this.chartData.modelDistribution || {};
        const labels = Object.keys(modelData);
        const data = Object.values(modelData);

        this.charts.models = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Terminals',
                    data: data,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545',
                        '#17a2b8', '#6c757d'
                    ],
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 50
                }]
            },
            options: this.getBarChartOptions('Device Models')
        });
    }

    // Chart 5: Performance Overview
    renderPerformanceChart() {
        const ctx = document.getElementById('performanceChart')?.getContext('2d');
        if (!ctx) return;

        if (this.charts.performance) {
            this.charts.performance.destroy();
        }

        const stats = this.chartData.stats || {};

        this.charts.performance = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Active Rate', 'Service Compliance', 'Recent Installs', 'Coverage', 'Response Time'],
                datasets: [{
                    label: 'Performance Metrics',
                    data: [
                        stats.uptime_percentage || 0,
                        this.calculateServiceCompliance(stats),
                        this.calculateInstallationRate(stats),
                        this.calculateCoverage(stats),
                        85 // Sample response time metric
                    ],
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: '#007bff',
                    borderWidth: 2,
                    pointBackgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { stepSize: 20 }
                    }
                }
            }
        });
    }

    // Chart 6: Installation Trends
    renderInstallationTrendsChart() {
        const ctx = document.getElementById('trendsChart')?.getContext('2d');
        if (!ctx) return;

        if (this.charts.trends) {
            this.charts.trends.destroy();
        }

        // Sample monthly data (you'd get this from PHP)
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const installationData = [12, 8, 15, 22, 18, 25];
        const serviceData = [45, 52, 48, 61, 55, 67];

        this.charts.trends = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'New Installations',
                        data: installationData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Services Completed',
                        data: serviceData,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 10 }
                    }
                }
            }
        });
    }

    // Helper: Get bar chart options
    getBarChartOptions(title, customLabels = null) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (customLabels && customLabels[context.dataIndex]) {
                                return `${customLabels[context.dataIndex]}: ${context.parsed.y} terminals`;
                            }
                            return `${context.label}: ${context.parsed.y} terminals`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { display: true, color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            }
        };
    }

    // Helper: Get doughnut chart options
    getDoughnutChartOptions(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} terminals (${percentage}%)`;
                        }
                    }
                }
            }
        };
    }

    // Helper: Extract location data from table
    getLocationDataFromTable() {
        const locationCounts = {};
        const rows = document.querySelectorAll('.terminals-table tbody tr');
        
        rows.forEach((row) => {
            if (row.querySelector('td[colspan]')) return;
            
            const locationCell = row.children[4];
            if (locationCell) {
                const locationDiv = locationCell.querySelector('div:first-child');
                if (locationDiv) {
                    const location = locationDiv.textContent.trim();
                    if (location && location !== 'No region' && location !== '') {
                        locationCounts[location] = (locationCounts[location] || 0) + 1;
                    }
                }
            }
        });

        const sortedLocations = Object.entries(locationCounts)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 10);

        return {
            labels: sortedLocations.map(item => item[0]),
            data: sortedLocations.map(item => item[1])
        };
    }

    // Helper: Calculate service compliance rate
    calculateServiceCompliance(stats) {
        const total = stats.total_terminals || 1;
        const compliant = (stats.recently_serviced || 0);
        return Math.round((compliant / total) * 100);
    }

    // Helper: Calculate installation rate
    calculateInstallationRate(stats) {
        const recent = stats.recent_installations || 0;
        return Math.min(100, recent * 10); // Scale for chart
    }

    // Helper: Calculate coverage
    calculateCoverage(stats) {
        const active = stats.active_terminals || 0;
        const total = stats.total_terminals || 1;
        return Math.round((active / total) * 100);
    }

    // Update charts with new data
    updateCharts(newData) {
        this.chartData = { ...this.chartData, ...newData };
        this.renderAllCharts();
    }

    // Resize all charts
    resizeCharts() {
        Object.values(this.charts).forEach(chart => {
            if (chart && chart.resize) {
                chart.resize();
            }
        });
    }

    // Destroy all charts
    destroyCharts() {
        Object.values(this.charts).forEach(chart => {
            if (chart && chart.destroy) {
                chart.destroy();
            }
        });
        this.charts = {};
    }
}

// Global instance
window.terminalCharts = new TerminalCharts();

// Auto-initialize if Chart.js is loaded
if (typeof Chart !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize with data from PHP (will be injected)
        window.terminalCharts.init(window.chartData || {});
    });
}