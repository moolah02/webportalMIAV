/**
 * Chart Controls and Integration
 * Handles chart view switching and stats toggling
 */

// Toggle stats section visibility
function toggleStats() {
    const detailedStats = document.getElementById('detailed-stats');
    const toggleText = document.getElementById('toggle-text');
    const toggleIcon = document.getElementById('toggle-icon');
    
    if (detailedStats.classList.contains('collapsed')) {
        detailedStats.classList.remove('collapsed');
        toggleText.textContent = 'Hide Charts';
        toggleIcon.textContent = '▲';
        
        // Re-render charts when showing
        setTimeout(() => {
            if (window.terminalCharts) {
                window.terminalCharts.resizeCharts();
            }
        }, 350);
    } else {
        detailedStats.classList.add('collapsed');
        toggleText.textContent = 'Show Charts';
        toggleIcon.textContent = '▼';
    }
}

// Switch between different chart views
function switchChartView() {
    const selector = document.getElementById('chart-view-selector');
    const selectedView = selector.value;
    
    // Hide all chart views
    document.querySelectorAll('.chart-view').forEach(view => {
        view.style.display = 'none';
        view.classList.remove('active');
    });
    
    // Show selected view
    const targetView = document.getElementById(selectedView + '-charts');
    if (targetView) {
        targetView.style.display = 'block';
        targetView.classList.add('active');
        
        // Re-render charts for the new view
        setTimeout(() => {
            if (window.terminalCharts) {
                window.terminalCharts.resizeCharts();
            }
        }, 100);
    }
}

// Update charts when filters change
function updateChartsWithFilters() {
    const form = document.getElementById('filter-form');
    if (!form) return;
    
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Show loading state
    const statsSection = document.getElementById('stats-section');
    if (statsSection) {
        statsSection.classList.add('loading');
    }
    
    // Fetch updated chart data
    fetch(`${window.location.pathname}/chart-data?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update stat cards
            updateStatCards(data.stats);
            
            // Update charts
            if (window.terminalCharts) {
                window.terminalCharts.updateCharts(data.chartData);
            }
        }
    })
    .catch(error => {
        console.error('Error updating charts:', error);
    })
    .finally(() => {
        // Remove loading state
        if (statsSection) {
            statsSection.classList.remove('loading');
        }
    });
}

// Update the stat cards with new data
function updateStatCards(stats) {
    const elements = {
        'total-count': stats.total_terminals || 0,
        'active-count': stats.active_terminals || 0,
        'faulty-count': stats.faulty_terminals || 0,
        'offline-count': stats.offline_terminals || 0
    };
    
    Object.entries(elements).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            // Animate the number change
            const currentValue = parseInt(element.textContent) || 0;
            animateNumber(element, currentValue, value);
        }
    });
}

// Animate number changes in stat cards
function animateNumber(element, start, end) {
    const duration = 500;
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Enhanced filter application with chart updates
function applyFilters() {
    updateChartsWithFilters();
    
    // Also submit the form for table updates
    const form = document.getElementById('filter-form');
    if (form) {
        form.submit();
    }
}

// Auto-update charts when search is performed
function handleSearch(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        updateChartsWithFilters();
        applyFilters();
    }
}

// Clear filters and reset charts
function clearAllFilters() {
    const form = document.getElementById('filter-form');
    if (!form) return;
    
    const inputs = form.querySelectorAll('select, input[type="text"]');
    
    inputs.forEach(input => {
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else {
            input.value = '';
        }
    });
    
    // Update charts with cleared filters
    updateChartsWithFilters();
    
    // Redirect to clear URL parameters
    setTimeout(() => {
        window.location.href = window.location.pathname;
    }, 500);
}

// Export chart data
function exportChartData() {
    if (!window.terminalCharts || !window.chartData) {
        alert('Chart data not available for export');
        return;
    }
    
    const data = {
        timestamp: new Date().toISOString(),
        statistics: window.chartData.stats,
        distributions: {
            clients: window.chartData.clientDistribution,
            models: window.chartData.modelDistribution,
            regions: window.chartData.stats.regional_distribution
        }
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `terminal-analytics-${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    URL.revokeObjectURL(url);
}

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chart view selector
    const viewSelector = document.getElementById('chart-view-selector');
    if (viewSelector) {
        viewSelector.addEventListener('change', switchChartView);
    }
    
    // Initialize filters with chart updates
    const filterSelects = document.querySelectorAll('#filter-form select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Debounce the chart updates
            clearTimeout(this.updateTimeout);
            this.updateTimeout = setTimeout(() => {
                updateChartsWithFilters();
            }, 300);
        });
    });
    
    // Initialize search with chart updates
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                updateChartsWithFilters();
            }, 500);
        });
    }
    
    // Add export button to stats header if not exists
    const statsHeader = document.querySelector('#stats-section > div:first-child');
    if (statsHeader && !document.getElementById('export-chart-btn')) {
        const exportBtn = document.createElement('button');
        exportBtn.id = 'export-chart-btn';
        exportBtn.innerHTML = '📊 Export Data';
        exportBtn.style.cssText = 'background: #007bff; color: white; border: 1px solid #007bff; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer; margin-left: 10px;';
        exportBtn.onclick = exportChartData;
        
        const toggleContainer = statsHeader.querySelector('div:last-child');
        if (toggleContainer) {
            toggleContainer.insertBefore(exportBtn, toggleContainer.firstChild);
        }
    }
});

// Window resize handler for charts
window.addEventListener('resize', function() {
    if (window.terminalCharts) {
        clearTimeout(window.resizeTimeout);
        window.resizeTimeout = setTimeout(() => {
            window.terminalCharts.resizeCharts();
        }, 250);
    }
});