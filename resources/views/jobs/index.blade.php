{{-- resources/views/jobs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Job Assignments')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <h2 class="page-title mb-4">Technician Assignments</h2>

    {{-- Statistics Cards --}}
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $assignments->total() ?? 0 }}</div>
                    <div class="stat-label">Total Assignments</div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $assignments->where('status', 'assigned')->count() }}</div>
                    <div class="stat-label">Assigned</div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $assignments->where('status', 'completed')->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-body">
                <div class="stat-icon stat-icon-progress">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $assignments->where('status', 'in_progress')->count() }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">Job Assignments</h6>
        </div>
        <div class="card-body">
            <div class="nav-buttons">
                <a href="{{ route('jobs.mine') }}" 
                   class="btn {{ ($scope ?? 'all') === 'mine' ? 'btn-primary' : 'btn-secondary' }}">
                    My Assignments
                </a>
                <a href="{{ route('jobs.index') }}" 
                   class="btn {{ ($scope ?? 'all') === 'all' ? 'btn-primary' : 'btn-secondary' }}">
                    All Assignments
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title">Filters</h6>
        </div>
        <div class="card-body">
            <form method="get">
                <div class="filters-grid">
                    {{-- Status Filter --}}
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            @foreach (['assigned' => 'Assigned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $val => $label)
                                <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Date Filters --}}
                    <div class="form-group">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                    </div>

                    @if(($scope ?? 'all') === 'all')
                        {{-- Priority Filter --}}
                        <div class="form-group">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-control">
                                <option value="">All Priority</option>
                                @foreach (['low'=>'Low','normal'=>'Normal','high'=>'High','emergency'=>'Emergency'] as $pval => $plabel)
                                    <option value="{{ $pval }}" {{ ($filters['priority'] ?? '') === $pval ? 'selected' : '' }}>
                                        {{ $plabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Technician Filter --}}
                        <div class="form-group">
                            <label class="form-label">Technician</label>
                            <select name="technician_id" class="form-control">
                                <option value="">All Technicians</option>
                                @foreach ($technicians as $t)
                                    <option value="{{ $t->id }}" {{ ($filters['technician_id'] ?? '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->first_name }} {{ $t->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Client Filter --}}
                        <div class="form-group">
                            <label class="form-label">Client</label>
                            <select name="client_id" class="form-control">
                                <option value="">All Clients</option>
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}" {{ ($filters['client_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                        {{ $c->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                {{-- Search and Actions --}}
                <div class="search-actions">
                    <div class="form-group search-input">
                        <label class="form-label">Search</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" 
                               class="form-control" placeholder="Assignment ID or notes...">
                    </div>

                    <div class="form-group action-buttons">
                        <label class="form-label">&nbsp;</label>
                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ (($scope ?? 'all') === 'all') ? route('jobs.index') : route('jobs.mine') }}" 
                               class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Assignments List --}}
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">Assignments</h6>
            <span class="badge">{{ $assignments->count() }}</span>
        </div>
        
        @if($assignments->count() > 0)
            <div class="table-responsive">
                <table class="assignments-table">
                    <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Details</th>
                            @if(($scope ?? 'all') === 'all')
                                <th>Technician</th>
                            @endif
                            <th>Scheduled</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($assignments as $a)
                        <tr>
                            <td>
                                <div class="assignment-id">{{ $a->assignment_id }}</div>
                                <div class="assignment-date">Created {{ $a->created_at?->diffForHumans() }}</div>
                            </td>

                            <td>
                                <div class="assignment-title">
                                    {{ $a->list_title ?? implode(' • ', array_filter([
                                        $a->project->project_name ?? null,
                                        $a->client->company_name ?? null,
                                        $a->region->name ?? null,
                                    ])) ?: '—' }}
                                </div>
                                <div class="assignment-details">
                                    Service: {{ \Illuminate\Support\Str::headline($a->service_type) }}
                                    • Terminals: {{ is_array($a->pos_terminals) ? count($a->pos_terminals) : ($a->terminal_count ?? 0) }}
                                    @if(!empty($a->terminal_merchant_preview))
                                        • e.g. {{ $a->terminal_merchant_preview }}@if(($a->terminal_count ?? 0) > 3) + more @endif
                                    @endif
                                </div>
                            </td>

                            @if(($scope ?? 'all') === 'all')
                                <td>
                                    @if($a->technician)
                                        <div class="technician-name">{{ $a->technician->first_name }} {{ $a->technician->last_name }}</div>
                                        <div class="technician-role">Technician</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endif

                            <td>
                                <div class="schedule-date">{{ optional($a->scheduled_date)->format('M j, Y') ?? '—' }}</div>
                                <div class="schedule-relative">{{ optional($a->scheduled_date)?->diffForHumans() }}</div>
                            </td>

                            <td>
                                <span class="badge badge-priority-{{ $a->priority }}">
                                    {{ \Illuminate\Support\Str::headline($a->priority) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge badge-status-{{ $a->status }}">
                                    {{ \Illuminate\Support\Str::headline($a->status) }}
                                </span>
                            </td>

                            <td class="text-end">
                                <a href="{{ route('jobs.show', $a->id) }}" class="btn btn-outline">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($assignments->hasPages())
                <div class="card-footer">
                    {{ $assignments->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <h5>No assignments found</h5>
                <p>Try adjusting your filters or check back later.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
/* Base Styling */
.page-title {
    color: #374151;
    font-weight: 600;
    margin: 0;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.stat-card-body {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    background: #f9fafb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-icon-success { background: #f0fdf4; color: #16a34a; }
.stat-icon-pending { background: #fef3c7; color: #d97706; }
.stat-icon-progress { background: #dbeafe; color: #2563eb; }

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Card Styling */
.card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.card-header {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: between;
    align-items: center;
}

.card-title {
    color: #374151;
    font-weight: 600;
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

.card-footer {
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
}

/* Navigation Buttons */
.nav-buttons {
    display: flex;
    gap: 0.5rem;
}

/* Filters Grid */
.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.search-actions {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 1rem;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-control {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    color: #111827;
    background: #ffffff;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button Group */
.button-group {
    display: flex;
    gap: 0.5rem;
}

/* Button Styling */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 6px;
    border: 1px solid transparent;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-primary {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
}

.btn-primary:hover {
    background: #2563eb;
    border-color: #2563eb;
}

.btn-secondary {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #374151;
}

.btn-secondary:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.btn-outline {
    background: transparent;
    border-color: #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

/* Table Styling */
.assignments-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.assignments-table th {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
}

.assignments-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

.assignments-table tbody tr:hover {
    background: #f9fafb;
}

/* Table Content */
.assignment-id {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.assignment-date,
.assignment-details,
.technician-role,
.schedule-relative {
    color: #6b7280;
    font-size: 0.8125rem;
}

.assignment-title,
.technician-name,
.schedule-date {
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.25rem;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

/* Status Badges */
.badge-status-completed {
    background: #dcfce7;
    color: #166534;
}

.badge-status-in_progress {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-status-assigned {
    background: #f3f4f6;
    color: #374151;
}

.badge-status-cancelled {
    background: #fecaca;
    color: #991b1b;
}

/* Priority Badges */
.badge-priority-emergency {
    background: #fecaca;
    color: #991b1b;
}

.badge-priority-high {
    background: #fed7aa;
    color: #c2410c;
}

.badge-priority-normal {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-priority-low {
    background: #f3f4f6;
    color: #6b7280;
}

/* Empty State */
.empty-state {
    padding: 3rem;
    text-align: center;
}

.empty-state h5 {
    color: #374151;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6b7280;
    margin: 0;
}

/* Utility Classes */
.text-end {
    text-align: right;
}

.text-muted {
    color: #6b7280;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .search-actions {
        grid-template-columns: 1fr;
    }
    
    .nav-buttons {
        flex-direction: column;
    }
    
    .button-group {
        flex-direction: column;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .assignments-table {
        font-size: 0.8125rem;
    }
    
    .assignments-table th,
    .assignments-table td {
        padding: 0.5rem;
    }
}

@media (max-width: 480px) {
    .stat-card-body {
        padding: 1rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}
</style>
@endpush