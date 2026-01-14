@extends('layouts.app')

@section('content')
<div class="profile-management">
    <!-- Success Message -->
    @if(session('success'))
        <div class="success-notification">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div>
                <h4>👤 My Profile</h4>
                <p>Manage your personal information and account settings</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('employee.edit-profile') }}" class="btn btn-primary">✏️ Edit Profile</a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['total_asset_requests'] }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon">⏱️</div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['assigned_assets_count'] }}</div>
                <div class="stat-label">Assigned Assets</div>
            </div>
        </div>
        <div class="stat-card purple">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['subordinates_count'] }}</div>
                <div class="stat-label">Team Members</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-grid">
        <!-- Personal & Organization Info -->
        <div class="content-card">
            <div class="card-header">
                <h3>👤 Personal Information</h3>
            </div>
            <div class="card-body">
                <!-- Profile Summary -->
                <div class="profile-summary">
                    <div class="profile-avatar">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="profile-details">
                        <h3>{{ $employee->full_name }}</h3>
                        <p>{{ $employee->employee_number }} • {{ $employee->department->name ?? 'No Department' }}</p>
                        <div class="profile-badges">
                            <span class="badge badge-{{ $employee->status === 'active' ? 'success' : 'inactive' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                            @if($employee->role)
                                <span class="badge badge-blue">{{ $employee->role->name }}</span>
                            @endif
                            @if($employee->isFieldTechnician())
                                <span class="badge badge-warning">🔧 Technician</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $employee->email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $employee->phone ?: 'Not provided' }}</span>
                    </div>
                    <div class="info-row">
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
                    <div class="info-row">
                        <span class="info-label">Position:</span>
                        <span class="info-value">{{ $employee->position }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Time Zone:</span>
                        <span class="info-value">{{ $employee->time_zone }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Language:</span>
                        <span class="info-value">{{ strtoupper($employee->language) }}</span>
                    </div>
                </div>

                <!-- Organization Section -->
                <div class="section-divider">
                    <h4>🏢 Organization</h4>
                </div>
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">Department:</span>
                        <span class="info-value">{{ $employee->department->name ?? 'Not assigned' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Role:</span>
                        <span class="info-value">{{ $employee->role->name ?? 'Not assigned' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Manager:</span>
                        <span class="info-value">{{ $employee->manager->full_name ?? 'No manager assigned' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">2FA Status:</span>
                        <span class="info-value">
                            <span class="badge badge-{{ $employee->two_factor_enabled ? 'success' : 'warning' }}">
                                {{ $employee->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
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

                <!-- Security Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        🔐 Change Password
                    </button>
                </div>
            </div>
        </div>

        <!-- Assets & Requests -->
        <div class="content-card">
            <div class="card-header">
                <h3>📋 Assets & Requests</h3>
            </div>
            <div class="card-body">
                <!-- Current Asset Assignments -->
                @if($employee->currentAssetAssignments->count() > 0)
                <div class="assignments-section">
                    <h4>🖥️ Assigned Assets ({{ $employee->currentAssetAssignments->count() }})</h4>
                    <div class="assignments-list">
                        @foreach($employee->currentAssetAssignments->take(8) as $assignment)
                        <div class="assignment-item">
                            <div class="assignment-info">
                                <div class="assignment-name">{{ $assignment->asset->name }}</div>
                                <div class="assignment-details">
                                    @if($assignment->asset->brand){{ $assignment->asset->brand }} • @endif
                                    {{ $assignment->asset->category }}
                                    @if($assignment->asset->sku) • SKU: {{ $assignment->asset->sku }}@endif
                                </div>
                            </div>
                            <div class="assignment-badges">
                                <span class="badge badge-{{ $assignment->condition_when_assigned === 'new' ? 'success' : ($assignment->condition_when_assigned === 'good' ? 'info' : 'warning') }}">
                                    {{ ucfirst($assignment->condition_when_assigned) }}
                                </span>
                                @if($assignment->isOverdue())
                                    <span class="badge badge-danger">{{ $assignment->days_overdue }}d overdue</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($employee->currentAssetAssignments->count() > 8)
                        <div class="show-more">
                            And {{ $employee->currentAssetAssignments->count() - 8 }} more assignments...
                        </div>
                    @endif
                </div>
                @endif

                <!-- Recent Asset Requests -->
                @if($employee->assetRequests->count() > 0)
                <div class="requests-section">
                    <h4>📝 Recent Requests ({{ $employee->assetRequests->count() }})</h4>
                    <div class="requests-list">
                        @foreach($employee->assetRequests->take(8) as $request)
                        <div class="request-item">
                            <div class="request-info">
                                <div class="request-name">{{ $request->request_number }}</div>
                                <div class="request-details">
                                    @if($request->business_justification)
                                        {{ Str::limit($request->business_justification, 40) }}
                                    @endif
                                    @if($request->total_estimated_cost)
                                        • ${{ number_format($request->total_estimated_cost, 0) }}
                                    @endif
                                </div>
                            </div>
                            <div class="request-badges">
                                <span class="badge badge-{{ 
                                    $request->status === 'approved' ? 'success' : 
                                    ($request->status === 'rejected' ? 'danger' : 
                                    ($request->status === 'pending' ? 'warning' : 
                                    ($request->status === 'fulfilled' ? 'info' : 'inactive'))) 
                                }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                                @if($request->priority === 'urgent' || $request->priority === 'high')
                                    <span class="badge badge-danger">{{ ucfirst($request->priority) }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Team Members (if manager) -->
                @if($employee->subordinates->count() > 0)
                <div class="team-section">
                    <h4>👥 Team Members ({{ $employee->subordinates->count() }})</h4>
                    <div class="team-list">
                        @foreach($employee->subordinates->take(6) as $subordinate)
                        <div class="team-item">
                            <div class="team-avatar">
                                {{ strtoupper(substr($subordinate->first_name, 0, 1) . substr($subordinate->last_name, 0, 1)) }}
                            </div>
                            <div class="team-info">
                                <div class="team-name">{{ $subordinate->full_name }}</div>
                                <div class="team-role">{{ $subordinate->role->name ?? 'No role assigned' }}</div>
                            </div>
                            @if($subordinate->isFieldTechnician())
                                <span class="badge badge-warning">🔧</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @if($employee->subordinates->count() > 6)
                        <div class="show-more">
                            And {{ $employee->subordinates->count() - 6 }} more team members...
                        </div>
                    @endif
                </div>
                @endif

                @if($employee->currentAssetAssignments->count() === 0 && $employee->assetRequests->count() === 0 && $employee->subordinates->count() === 0)
                <div class="empty-state-large">
                    <div class="empty-icon">📋</div>
                    <h3>No activity yet</h3>
                    <p>Your assets, requests, and team information will appear here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><span>🔐</span><span>Change Password</span></h3>
            <button onclick="closePasswordModal()" class="modal-close">×</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('employee.update-password') }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="current_password" class="form-label required">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-input" required>
                    @error('current_password')
                        <small class="form-error">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password" class="form-label required">New Password</label>
                    <input type="password" name="password" id="password" class="form-input" required minlength="8">
                    @error('password')
                        <small class="form-error">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label required">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" required minlength="8">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">🔐 Update Password</button>
                    <button type="button" onclick="closePasswordModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Clean, Compact Design - Matching Deployment Planning */
.profile-management {
    padding: 0;
    max-width: 100%;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Success Notification */
.success-notification {
    background: #d1fae5;
    color: #065f46;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    border: 1px solid #a7f3d0;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-content h4 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.header-content p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

/* Stats Grid - Compact */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    padding: 1.25rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}

.stat-card.primary::before { background: #3b82f6; }
.stat-card.success::before { background: #10b981; }
.stat-card.warning::before { background: #f59e0b; }
.stat-card.purple::before { background: #8b5cf6; }

.stat-icon {
    font-size: 2rem;
    opacity: 0.8;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

/* Main Grid */
.main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

@media (max-width: 1024px) {
    .main-grid {
        grid-template-columns: 1fr;
    }
}

/* Cards */
.content-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    overflow: hidden;
}

.card-header {
    background: #f8fafc;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
}

.card-body {
    padding: 1.5rem;
}

/* Profile Summary */
.profile-summary {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.profile-avatar {
    flex-shrink: 0;
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.25rem;
}

.profile-details h3 {
    margin: 0 0 0.25rem 0;
    color: #111827;
    font-size: 1.25rem;
    font-weight: 700;
}

.profile-details p {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.profile-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Info Sections */
.section-divider {
    margin: 1.5rem 0 1rem 0;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.section-divider h4 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.info-section {
    margin-bottom: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 0.875rem;
    min-width: 100px;
}

.info-value {
    color: #111827;
    font-size: 0.875rem;
    text-align: right;
    flex: 1;
    margin-left: 1rem;
}

/* Assignments Section */
.assignments-section,
.requests-section,
.team-section {
    margin-bottom: 1.5rem;
}

.assignments-section h4,
.requests-section h4,
.team-section h4 {
    margin: 0 0 0.75rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.assignments-list,
.requests-list {
    max-height: 300px;
    overflow-y: auto;
}

.assignment-item,
.request-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    background: white;
    transition: all 0.2s ease;
}

.assignment-item:hover,
.request-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
}

.assignment-info,
.request-info {
    flex: 1;
    min-width: 0;
}

.assignment-name,
.request-name {
    font-weight: 600;
    color: #111827;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.assignment-details,
.request-details {
    font-size: 0.75rem;
    color: #6b7280;
    line-height: 1.4;
}

.assignment-badges,
.request-badges {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    align-items: flex-end;
}

/* Team Section */
.team-list {
    display: grid;
    gap: 0.75rem;
}

.team-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: white;
    transition: all 0.2s ease;
}

.team-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
}

.team-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.team-info {
    flex: 1;
}

.team-name {
    font-weight: 600;
    color: #111827;
    font-size: 0.875rem;
}

.team-role {
    font-size: 0.75rem;
    color: #6b7280;
}

.show-more {
    text-align: center;
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.5rem;
    font-style: italic;
}

/* Badges - Compact */
.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.625rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.badge-blue {
    background: #e0f2fe;
    color: #0369a1;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.badge-inactive {
    background: #f3f4f6;
    color: #6b7280;
}

/* Form Styles - Compact */
.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.form-label.required::after {
    content: ' *';
    color: #ef4444;
}

.form-input {
    width: 100%;
    padding: 0.625rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s ease;
    background: white;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-error {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
}

.form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

/* Buttons - Compact */
.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-large {
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
}

/* Empty States */
.empty-state-large {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 0.75rem;
    opacity: 0.5;
}

/* Modals */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 1rem;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 400px;
    width: 90%;
    max-height: 80vh;
    overflow: auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    position: relative;
    z-index: 10000;
    pointer-events: auto;
}

.modal-header {
    background: #3b82f6;
    color: white;
    padding: 1rem 1.5rem;
    position: relative;
}

.modal-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
}

.modal-close {
    position: absolute;
    top: 0.75rem;
    right: 1rem;
    background: none;
    border: none;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
}

.modal-body {
    padding: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .profile-summary {
        flex-direction: column;
        align-items: flex-start;
        text-align: center;
    }
    
    .info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .info-value {
        text-align: left;
        margin-left: 0;
    }
    
    .assignment-item,
    .request-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .assignment-badges,
    .request-badges {
        flex-direction: row;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Modal handling
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap modal fallback
    const modal = document.getElementById('changePasswordModal');
    
    // Show modal when bootstrap trigger is clicked
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-bs-toggle="modal"]')) {
            e.preventDefault();
            const targetModal = document.querySelector(e.target.getAttribute('data-bs-target'));
            if (targetModal) {
                targetModal.style.display = 'flex';
            }
        }
    });
    
    // Close modal when clicking outside
    modal?.addEventListener('click', function(e) {
        if (e.target === modal) {
            closePasswordModal();
        }
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePasswordModal();
        }
    });
});

function closePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Success notification auto-hide
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.querySelector('.success-notification');
    if (notification) {
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
});
</script>
@endsection