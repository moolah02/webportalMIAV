{{-- resources/views/projects/partials/analytics-tab.blade.php - DEBUGGED VERSION --}}

<div class="mb-4">
    <h5 class="text-dark mb-2">Analytics & Overview</h5>
    <p class="text-muted">High-level statistics and insights across all projects.</p>
</div>

{{-- Debug info - remove this after testing --}}
<div class="alert alert-info mb-4" style="font-family: monospace; font-size: 12px;">
    <strong>DEBUG INFO:</strong><br>
    Active Projects: {{ $activeProjects->count() }}<br>
    Completed Projects: {{ $completedProjects->count() }}<br>
    @php
        $completedWithCompletions = $completedProjects->filter(function($project) {
            return isset($project->completion) && $project->completion->quality_score;
        });
    @endphp
    Projects with completion data: {{ $completedWithCompletions->count() }}
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="stats-card bg-primary text-white">
            <div class="stats-icon">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number">{{ $activeProjects->count() }}</h3>
                <p class="stats-label mb-0">Active Projects</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card bg-success text-white">
            <div class="stats-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number">{{ $completedProjects->count() }}</h3>
                <p class="stats-label mb-0">Completed Projects</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card bg-info text-white">
            <div class="stats-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number">
                    @php
                        try {
                            $completedWithCompletions = $completedProjects->filter(function($project) {
                                return isset($project->completion) && isset($project->completion->quality_score) && $project->completion->quality_score > 0;
                            });

                            if ($completedWithCompletions->count() > 0) {
                                $avgQuality = $completedWithCompletions->avg(function($project) {
                                    return (float) $project->completion->quality_score;
                                });
                                echo number_format($avgQuality, 1);
                            } else {
                                echo '0.0';
                            }
                        } catch (Exception $e) {
                            echo '0.0';
                        }
                    @endphp
                </h3>
                <p class="stats-label mb-0">Avg Quality Score</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card bg-warning text-white">
            <div class="stats-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number">
                    @php
                        try {
                            if ($completedWithCompletions->count() > 0) {
                                $avgSatisfaction = $completedWithCompletions->avg(function($project) {
                                    return (float) $project->completion->client_satisfaction;
                                });
                                echo number_format($avgSatisfaction, 1);
                            } else {
                                echo '0.0';
                            }
                        } catch (Exception $e) {
                            echo '0.0';
                        }
                    @endphp
                </h3>
                <p class="stats-label mb-0">Avg Satisfaction</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Completions Section -->
<div class="row g-4">
    <div class="col-12">
        <div class="analytics-card">
            <div class="analytics-header">
                <h6 class="mb-0 text-dark">
                    <i class="fas fa-clock text-primary me-2"></i>Recent Completions
                </h6>
            </div>
            <div class="analytics-body">
                @if($completedProjects->take(5)->count() > 0)
                    @foreach($completedProjects->take(5) as $project)
                    <div class="completion-item {{ !$loop->last ? 'mb-3' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold text-dark">{{ $project->project_name ?? 'Unnamed Project' }}</div>
                                <small class="text-muted">{{ $project->client->company_name ?? 'No Client' }}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">
                                    @if($project->completed_at)
                                        {{ $project->completed_at->diffForHumans() }}
                                    @else
                                        Recently
                                    @endif
                                </small>
                                @if(isset($project->completion) && isset($project->completion->quality_score))
                                    <div class="small mt-1">
                                        <span class="badge bg-primary">{{ $project->completion->quality_score }}/5</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line text-muted mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                        <p class="text-muted mb-0">No completed projects yet</p>
                        <small class="text-muted">Analytics will appear here once projects are completed</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Additional CSS to ensure cards are visible --}}
<style>
/* Force stats cards to be visible */
.stats-card {
    border-radius: 20px;
    padding: 2rem;
    display: flex !important;
    align-items: center;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255,255,255,0.1);
    min-height: 120px;
    visibility: visible !important;
    opacity: 1 !important;
}

.stats-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(0,0,0,0.18);
}

.stats-icon {
    font-size: 3rem;
    margin-right: 1.5rem;
    opacity: 0.9;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}

.stats-label {
    font-size: 1rem;
    opacity: 0.95;
    font-weight: 500;
}

/* Analytics cards */
.analytics-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    visibility: visible !important;
    display: block !important;
}

.analytics-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.analytics-body {
    padding: 2rem;
}

.completion-item {
    padding: 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
    border-radius: 8px;
    margin-bottom: 0.75rem;
}

.completion-item:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.completion-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}
</style>
