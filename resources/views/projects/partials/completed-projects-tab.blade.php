{{-- resources/views/projects/partials/completed-projects-tab.blade.php --}}

@if($projects->count() > 0)
    <div class="mb-4">
        <h5 class="text-dark mb-2">Completed Projects & Reports</h5>
        <p class="text-muted">Manage reports, download files, and review completion details for finished projects.</p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th class="border-0 bg-light rounded-start ps-4">Project</th>
                    <th class="border-0 bg-light">Client</th>
                    <th class="border-0 bg-light">Completed</th>
                    <th class="border-0 bg-light text-center">Quality</th>
                    <th class="border-0 bg-light text-center">Satisfaction</th>
                    <th class="border-0 bg-light rounded-end text-center pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                <tr class="table-row-hover">
                    <td class="ps-4">
                        <div>
                            <div class="fw-semibold text-dark">{{ $project->project_name }}</div>
                            <small class="text-muted">{{ $project->project_code }}</small>
                        </div>
                    </td>
                    <td>
                        <span class="text-dark">{{ $project->client->company_name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $project->completed_at?->format('M j, Y') ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if(isset($project->completion))
                            <div class="d-flex justify-content-center align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $project->completion->quality_score ? 'text-warning' : 'text-muted' }} me-1" style="font-size: 0.8rem;"></i>
                                @endfor
                            </div>
                            <small class="text-muted">{{ $project->completion->quality_score }}/5</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(isset($project->completion))
                            <div class="d-flex justify-content-center align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $project->completion->client_satisfaction ? 'text-warning' : 'text-muted' }} me-1" style="font-size: 0.8rem;"></i>
                                @endfor
                            </div>
                            <small class="text-muted">{{ $project->completion->client_satisfaction }}/5</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('projects.show', $project) }}"
                               class="btn btn-outline-primary rounded-pill me-1"
                               title="View Project">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($project->report_path ?? false)
                                <a href="{{ route('projects.download-report', $project) }}"
                                   class="btn btn-outline-success rounded-pill me-1"
                                   title="Download Report">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                            @if(isset($project->completion))
                                <button type="button"
                                        class="btn btn-outline-info rounded-pill details-btn"
                                        title="View Details"
                                        data-project-id="{{ $project->id }}"
                                        data-project-name="{{ $project->project_name }}"
                                        data-client-name="{{ $project->client->company_name }}"
                                        data-completion="{{ json_encode($project->completion) }}"
                                        data-project-code="{{ $project->project_code }}"
                                        data-completed-at="{{ $project->completed_at?->format('F j, Y g:i A') ?? 'N/A' }}"
                                        data-duration="{{ $project->start_date && $project->completed_at ? $project->start_date->diffInDays($project->completed_at) : 'N/A' }}"
                                        data-terminals="{{ $project->actual_terminals_count ?? 0 }}"
                                        data-completion-rate="{{ $project->completion_percentage ?? 100 }}"
                                        data-report-path="{{ $project->report_path ?? '' }}"
                                        data-client-email="{{ $project->client->email ?? '' }}">
                                    <i class="fas fa-file-alt"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-folder-open text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
            <h4 class="text-muted mb-2">No completed projects</h4>
            <p class="text-muted">Completed projects will appear here once you finish active projects.</p>
        </div>
    </div>
@endif
