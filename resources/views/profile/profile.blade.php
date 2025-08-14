@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Compact Profile Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card compact-card">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-md rounded-circle bg-primary d-flex align-items-center justify-content-center text-white">
                                <i class="fas fa-user fa-lg"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="mb-1">{{ $employee->full_name }}</h4>
                            <p class="text-muted mb-1 small">
                                <i class="fas fa-id-card me-1"></i>{{ $employee->employee_number }}
                                @if($employee->department) • {{ $employee->department->name }}@endif
                                @if($employee->role) • {{ $employee->role->name }}@endif
                            </p>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge badge-sm bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                                @if($employee->isFieldTechnician())
                                    <span class="badge badge-sm bg-warning">
                                        <i class="fas fa-tools me-1"></i>Technician
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('employee.edit-profile') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-1"></i> Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card bg-primary text-white">
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_asset_requests'] }}</div>
                    <div class="stat-label">Total Requests</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card bg-warning text-white">
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card bg-success text-white">
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['assigned_assets_count'] }}</div>
                    <div class="stat-label">Assigned Assets</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card bg-info text-white">
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['subordinates_count'] }}</div>
                    <div class="stat-label">Team Members</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Left Column - Personal & Org Info -->
        <div class="col-lg-6 col-xl-4">
            <!-- Personal Information -->
            <div class="card compact-card mb-3">
                <div class="card-header compact-header">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                </div>
                <div class="card-body compact-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $employee->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $employee->phone ?: 'Not provided' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Hire Date:</span>
                            <span class="info-value">
                                @if($employee->hire_date)
                                    {{ $employee->hire_date->format('M d, Y') }}
                                @else
                                    Not specified
                                @endif
                            </span>
                        </div>
                        @if($employee->position)
                        <div class="info-item">
                            <span class="info-label">Position:</span>
                            <span class="info-value">{{ $employee->position }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">Time Zone:</span>
                            <span class="info-value">{{ $employee->time_zone }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Language:</span>
                            <span class="info-value">{{ strtoupper($employee->language) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organizational Information -->
            <div class="card compact-card mb-3">
                <div class="card-header compact-header">
                    <h6 class="mb-0"><i class="fas fa-sitemap me-2"></i>Organization</h6>
                </div>
                <div class="card-body compact-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Department:</span>
                            <span class="info-value">{{ $employee->department->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Role:</span>
                            <span class="info-value">
                                {{ $employee->role->name ?? 'Not assigned' }}
                                @if($employee->isFieldTechnician())
                                    <small class="text-success"><i class="fas fa-tools"></i></small>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Manager:</span>
                            <span class="info-value">{{ $employee->manager->full_name ?? 'No manager assigned' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">2FA Status:</span>
                            <span class="info-value">
                                <span class="badge badge-sm bg-{{ $employee->two_factor_enabled ? 'success' : 'warning' }}">
                                    {{ $employee->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Last Login:</span>
                            <span class="info-value">
                                @if($employee->last_login_at)
                                    {{ $employee->last_login_at->diffForHumans() }}
                                @else
                                    Never
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Column - Asset Assignments -->
        <div class="col-lg-6 col-xl-4">
            @if($employee->currentAssetAssignments->count() > 0)
            <div class="card compact-card mb-3">
                <div class="card-header compact-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-laptop me-2"></i>Assigned Assets</h6>
                    <span class="badge bg-secondary">{{ $employee->currentAssetAssignments->count() }}</span>
                </div>
                <div class="card-body compact-body">
                    <div class="table-responsive">
                        <table class="table table-sm compact-table">
                            <thead>
                                <tr>
                                    <th>Asset</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->currentAssetAssignments->take(8) as $assignment)
                                <tr>
                                    <td>
                                        <div class="asset-info">
                                            <strong>{{ $assignment->asset->name }}</strong>
                                            @if($assignment->asset->brand)
                                                <small class="text-muted d-block">{{ $assignment->asset->brand }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm {{ $assignment->condition_badge_class }}">
                                            {{ ucfirst($assignment->condition_when_assigned) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm {{ $assignment->status_badge_class }}">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                        @if($assignment->isOverdue())
                                            <small class="text-danger d-block">{{ $assignment->days_overdue }}d overdue</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Team Members (if manager) -->
            @if($employee->subordinates->count() > 0)
            <div class="card compact-card mb-3">
                <div class="card-header compact-header">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Team Members ({{ $employee->subordinates->count() }})</h6>
                </div>
                <div class="card-body compact-body">
                    <div class="team-grid">
                        @foreach($employee->subordinates->take(6) as $subordinate)
                        <div class="team-member">
                            <div class="avatar-sm rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white me-2">
                                {{ substr($subordinate->first_name, 0, 1) }}{{ substr($subordinate->last_name, 0, 1) }}
                            </div>
                            <div class="team-info">
                                <div class="team-name">{{ $subordinate->full_name }}</div>
                                <small class="text-muted">{{ $subordinate->role->name ?? 'No role' }}</small>
                                @if($subordinate->isFieldTechnician())
                                    <small class="text-success"><i class="fas fa-tools"></i></small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Recent Requests -->
        <div class="col-lg-12 col-xl-4">
            @if($employee->assetRequests->count() > 0)
            <div class="card compact-card">
                <div class="card-header compact-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Asset Requests</h6>
                </div>
                <div class="card-body compact-body">
                    <div class="table-responsive">
                        <table class="table table-sm compact-table">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->assetRequests->take(8) as $request)
                                <tr>
                                    <td>
                                        <div class="request-info">
                                            {{ $request->request_number }}
                                            @if($request->business_justification)
                                                <small class="text-muted d-block">{{ Str::limit($request->business_justification, 25) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-{{ 
                                            $request->status === 'approved' ? 'success' : 
                                            ($request->status === 'rejected' ? 'danger' : 
                                            ($request->status === 'pending' ? 'warning' : 
                                            ($request->status === 'fulfilled' ? 'info' : 'secondary'))) 
                                        }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                        @if($request->priority === 'urgent' || $request->priority === 'high')
                                            <small class="text-danger d-block">{{ ucfirst($request->priority) }}</small>
                                        @endif
                                    </td>
                                    <td>${{ number_format($request->total_estimated_cost, 0) }}</td>
                                    <td>{{ $request->created_at->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Compact Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{ route('employee.update-password') }}">
                @csrf
                @method('PATCH')
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="changePasswordModalLabel">Change Password</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control form-control-sm @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" 
                               id="password" name="password" required minlength="8">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control form-control-sm" 
                               id="password_confirmation" name="password_confirmation" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Compact Profile Styles */
.avatar-md {
    width: 50px;
    height: 50px;
}
.avatar-sm {
    width: 28px;
    height: 28px;
    font-size: 0.75rem;
}

.compact-card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.compact-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0 !important;
    padding: 0.75rem 1rem;
}

.compact-body {
    padding: 1rem;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Statistics Cards */
.stat-card {
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 70px;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    line-height: 1;
}

.stat-label {
    font-size: 0.8rem;
    opacity: 0.9;
    margin-top: 0.25rem;
}

.stat-icon {
    font-size: 1.5rem;
    opacity: 0.7;
}

/* Info Grid */
.info-grid {
    display: grid;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.85rem;
    min-width: 80px;
}

.info-value {
    text-align: right;
    font-size: 0.85rem;
    flex: 1;
    margin-left: 1rem;
}

/* Team Grid */
.team-grid {
    display: grid;
    gap: 0.75rem;
}

.team-member {
    display: flex;
    align-items: center;
}

.team-info {
    flex: 1;
}

.team-name {
    font-weight: 600;
    font-size: 0.85rem;
}

/* Compact Table */
.compact-table {
    font-size: 0.8rem;
}

.compact-table th {
    font-weight: 600;
    color: #6c757d;
    border-bottom: 1px solid #dee2e6;
    padding: 0.5rem 0.25rem;
}

.compact-table td {
    padding: 0.5rem 0.25rem;
    border-bottom: 1px solid #f0f0f0;
}

.asset-info strong,
.request-info {
    font-size: 0.85rem;
}

.request-info small,
.asset-info small {
    font-size: 0.7rem;
}

/* Responsive adjustments */
@media (max-width: 1199px) {
    .col-xl-4:last-child {
        margin-top: 1rem;
    }
}

@media (max-width: 991px) {
    .stat-card {
        margin-bottom: 0.5rem;
    }
    
    .col-lg-6:last-child {
        margin-top: 1rem;
    }
}

@media (max-width: 767px) {
    .stat-number {
        font-size: 1.25rem;
    }
    
    .stat-label {
        font-size: 0.75rem;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .info-value {
        text-align: left;
        margin-left: 0;
    }
}
</style>
@endpush