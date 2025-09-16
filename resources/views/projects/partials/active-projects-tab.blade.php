{{-- resources/views/projects/partials/active-projects-tab.blade.php --}}

@if($projects->count() > 0)
    <div class="mb-4">
        <h5 class="text-dark mb-2">Projects Ready for Completion</h5>
        <p class="text-muted">Complete these active projects to generate reports and mark them as finished.</p>
    </div>

    <div class="row g-4">
        @foreach($projects as $project)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 project-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="card-title mb-0 fw-semibold">{{ $project->project_name }}</h6>
                        <span class="badge bg-{{ $project->priority === 'high' ? 'danger' : ($project->priority === 'normal' ? 'primary' : 'secondary') }} rounded-pill">
                            {{ ucfirst($project->priority) }}
                        </span>
                    </div>

                    <div class="project-details mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-building text-primary me-2"></i>
                            <span class="text-muted">{{ $project->client->company_name }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-code text-info me-2"></i>
                            <span class="text-muted">{{ $project->project_code }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-tag text-success me-2"></i>
                            <span class="text-muted">{{ ucfirst($project->project_type) }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar text-warning me-2"></i>
                            <span class="text-muted">{{ $project->start_date?->format('M j, Y') ?? 'Not set' }}</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success complete-btn rounded-pill"
                                data-project-id="{{ $project->id }}"
                                data-project-name="{{ $project->project_name }}"
                                data-client-name="{{ $project->client->company_name }}">
                            <i class="fas fa-check-circle me-1"></i>Complete Project
                        </button>

                        <div class="d-flex gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-fill">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                            <a href="{{ route('completion-wizard', $project) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-fill">
                                <i class="fas fa-times-circle me-1"></i>Close Project
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-check-circle text-success mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
            <h4 class="text-muted mb-2">All projects completed!</h4>
            <p class="text-muted">No active projects require completion at this time.</p>
        </div>
    </div>
@endif
