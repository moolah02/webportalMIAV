{{-- resources/views/jobs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Assignment '.$assignment->assignment_id)

@section('content')
<div class="container-fluid py-4">
    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-back">
            ← Back to Assignments
        </a>
    </div>

    {{-- Assignment Header --}}
    <div class="card mb-4">
        <div class="card-body">
            {{-- Title and Status Row --}}
            <div class="header-row mb-4">
                <div class="title-section">
                    <h3 class="assignment-title">Assignment {{ $assignment->assignment_id }}</h3>
                    <div class="status-group">
                        <span class="badge badge-status-{{ $assignment->status }}">
                            {{ \Illuminate\Support\Str::headline($assignment->status) }}
                        </span>
                        <span class="badge badge-priority-{{ $assignment->priority }}">
                            {{ \Illuminate\Support\Str::headline($assignment->priority) }}
                        </span>
                    </div>
                </div>

                {{-- Status Update Actions --}}
                <div class="action-section">
                    @if(auth()->user()->can('update', $assignment) || (auth()->user()->id == $assignment->technician_id))
                        <div class="status-actions">
                            @if($assignment->status === 'assigned')
                                <button type="button" class="btn btn-primary" onclick="updateStatus({{ $assignment->id }}, 'in_progress', this)">
                                    <i class="fas fa-play"></i> Start Assignment
                                </button>
                            @elseif($assignment->status === 'in_progress')
                                <button type="button" class="btn btn-success" onclick="updateStatus({{ $assignment->id }}, 'completed', this)">
                                    <i class="fas fa-check"></i> Mark Complete
                                </button>
                            @elseif($assignment->status === 'completed')
                                <span class="completed-indicator">
                                    <i class="fas fa-check-circle"></i> Assignment Completed
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="details-grid">
                {{-- Assignment Details Column --}}
                <div class="detail-section">
                    <h6 class="section-title">Assignment Details</h6>

                    <div class="detail-item">
                        <span class="detail-label">Technician</span>
                        <div class="detail-value">
                            @if($assignment->technician)
                                <div class="technician-info">
                                    <div class="avatar">
                                        {{ substr($assignment->technician->first_name, 0, 1) }}{{ substr($assignment->technician->last_name, 0, 1) }}
                                    </div>
                                    <span>{{ $assignment->technician->first_name }} {{ $assignment->technician->last_name }}</span>
                                </div>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </div>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Client</span>
                        <span class="detail-value">{{ $assignment->client->company_name ?? '—' }}</span>
                    </div>

                    @if($assignment->project)
                    <div class="detail-item">
                        <span class="detail-label">Project</span>
                        <span class="detail-value">{{ $assignment->project->project_name }}</span>
                    </div>
                    @endif
                </div>

                {{-- Service Information Column --}}
                <div class="detail-section">
                    <h6 class="section-title">Service Information</h6>

                    <div class="detail-item">
                        <span class="detail-label">Scheduled Date</span>
                        <div class="detail-value">
                            @if($assignment->scheduled_date)
                                <div>{{ $assignment->scheduled_date->format('M j, Y') }}</div>
                                <small class="text-muted">{{ $assignment->scheduled_date->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Not scheduled</span>
                            @endif
                        </div>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Service Type</span>
                        <span class="detail-value">{{ \Illuminate\Support\Str::headline($assignment->service_type) }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Created</span>
                        <span class="detail-value">{{ $assignment->created_at?->format('M j, Y') ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Terminals Section --}}
    @if($terminals->isEmpty())
        <div class="card mb-4">
            <div class="empty-terminals">
                <div class="empty-icon">🖥️</div>
                <h5 class="empty-title">No terminals assigned</h5>
                <p class="empty-text">This assignment doesn't have any terminals associated with it.</p>
            </div>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title">Terminals</h6>
                <div class="header-actions">
                    <span class="terminal-count">
                        {{ $terminals->count() }} {{ \Illuminate\Support\Str::plural('terminal', $terminals->count()) }}
                    </span>
                    <div class="search-box">
                        <input type="text" id="terminalSearch" placeholder="Search terminals..." class="form-control">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="terminals-table">
                    <thead>
                        <tr>
                            <th>Merchant</th>
                            <th>Terminal ID</th>
                            <th>Address</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="terminalsTableBody">
                    @foreach($terminals as $t)
                        <tr class="terminal-row" data-searchable="{{ strtolower($t->merchant_name . ' ' . $t->terminal_id . ' ' . ($t->physical_address ?? $t->address ?? '') . ' ' . ($t->city ?? '') . ' ' . ($t->province ?? '')) }}">
                            <td>
                                <div class="merchant-name">{{ $t->merchant_name ?? '—' }}</div>
                                <div class="client-name">{{ $t->client->company_name ?? '—' }}</div>
                            </td>
                            <td>
                                <code class="terminal-id">{{ $t->terminal_id }}</code>
                            </td>
                            <td>
                                <div class="address">{{ $t->physical_address ?? $t->address ?? '—' }}</div>
                            </td>
                            <td>
                                <div class="location">
                                    {{ $t->city ?? '—' }}
                                    @if($t->province)
                                        <span class="province">, {{ $t->province }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-terminal-{{ strtolower($t->current_status ?? $t->status ?? 'unknown') === 'active' ? 'active' : (strtolower($t->current_status ?? $t->status ?? 'unknown') === 'inactive' ? 'inactive' : 'unknown') }}">
                                    {{ \Illuminate\Support\Str::headline($t->current_status ?? $t->status ?? 'unknown') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- No Results Message --}}
            <div id="noResults" class="no-results" style="display: none;">
                <div class="text-center py-4">
                    <i class="fas fa-search text-muted mb-2"></i>
                    <p class="text-muted mb-0">No terminals match your search criteria</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Live Site Visits (always visible) --}}
    <div class="card mb-4" id="liveSiteVisitsCard">
        <div class="card-header">
            <h6 class="card-title">Live Site Visits</h6>
            <span class="terminal-count" id="liveVisitCount">0</span>
        </div>
        <div class="card-body">
            <div id="liveVisitsList" class="list-group" style="max-height: 420px; overflow-y:auto;">
                <div class="text-muted">Waiting for updates…</div>
            </div>
        </div>
    </div>

    {{-- Notes Section --}}
    @if($assignment->notes)
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Notes</h6>
            </div>
            <div class="card-body">
                <div class="notes-content">{{ $assignment->notes }}</div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Base Card Styling */
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
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.card-title {
    color: #374151;
    font-weight: 600;
    margin: 0;
    font-size: 0.875rem;
}

.card-body {
    padding: 1.5rem;
}

/* Back Button */
.btn-back {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #374151;
    text-decoration: none;
    transition: all 0.15s ease;
}

.btn-back:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #111827;
}

/* Header Section */
.header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.title-section {
    flex: 1;
    min-width: 300px;
}

.action-section {
    flex-shrink: 0;
}

.assignment-title {
    color: #111827;
    font-weight: 700;
    margin: 0 0 1rem 0;
    font-size: 1.5rem;
}

.status-group {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Status Actions */
.status-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.status-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    border-radius: 6px;
    border: none;
    text-decoration: none;
    transition: all 0.15s ease;
    font-size: 0.875rem;
}

.btn-primary {
    background: #3b82f6;
    color: #ffffff;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-success {
    background: #10b981;
    color: #ffffff;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.completed-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #059669;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.detail-section {
    display: flex;
    flex-direction: column;
}

.section-title {
    color: #6b7280;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
    padding-bottom: 0.5rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f9fafb;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: #6b7280;
    font-size: 0.875rem;
    min-width: 120px;
    flex-shrink: 0;
}

.detail-value {
    text-align: right;
    font-size: 0.875rem;
    color: #111827;
    flex: 1;
}

/* Technician Info */
.technician-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: flex-end;
}

.avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #4f46e5;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
}

/* Badge Styling */
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

.badge-status-approved {
    background: #dcfce7;
    color: #166534;
}

.badge-status-pending {
    background: #fef3c7;
    color: #d97706;
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

/* Terminal Badges */
.badge-terminal-active {
    background: #dcfce7;
    color: #166534;
}

.badge-terminal-inactive {
    background: #fecaca;
    color: #991b1b;
}

.badge-terminal-unknown {
    background: #f3f4f6;
    color: #6b7280;
}

/* Terminal Search */
.search-box {
    position: relative;
    min-width: 200px;
}

.search-box .form-control {
    padding-right: 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
}

.search-box .form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-icon {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 0.875rem;
}

/* Terminal Count */
.terminal-count {
    background: #f3f4f6;
    color: #374151;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
}

/* Empty Terminals State */
.empty-terminals {
    padding: 3rem;
    text-align: center;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-title {
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 1.125rem;
    font-weight: 600;
}

.empty-text {
    color: #6b7280;
    margin: 0;
}

/* Table Styling */
.terminals-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.terminals-table th {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.terminals-table td {
    padding: 1rem;
    border-bottom: 1px solid #f9fafb;
    vertical-align: top;
}

.terminals-table tbody tr:hover {
    background: #f8fafc;
}

/* Table Content */
.merchant-name {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.client-name,
.province {
    color: #6b7280;
    font-size: 0.8125rem;
}

.terminal-id {
    background: #f1f5f9;
    color: #475569;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
    font-size: 0.8125rem;
    border: 1px solid #e2e8f0;
    font-weight: 500;
}

.address,
.location {
    color: #374151;
    line-height: 1.4;
}

/* No Results Message */
.no-results {
    padding: 2rem;
    border-top: 1px solid #f3f4f6;
}

/* Notes Section */
.notes-content {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 1rem;
    line-height: 1.6;
    color: #374151;
    white-space: pre-wrap;
    font-size: 0.875rem;
}

/* Utility Classes */
.text-muted {
    color: #6b7280;
}

/* Notifications */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border: 1px solid #e5e7eb;
    z-index: 1000;
    transform: translateX(400px);
    opacity: 0;
    transition: all 0.3s ease;
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

.notification-content {
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.notification-success {
    border-left: 4px solid #10b981;
}

.notification-success .fa-check-circle {
    color: #10b981;
}

.notification-error {
    border-left: 4px solid #ef4444;
}

.notification-error .fa-exclamation-triangle {
    color: #ef4444;
}

/* Responsive Design */
@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .header-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .detail-item {
        flex-direction: column;
        gap: 0.5rem;
    }

    .detail-value {
        text-align: left;
    }

    .technician-info {
        justify-content: flex-start;
    }

    .card-body {
        padding: 1rem;
    }

    .terminals-table th,
    .terminals-table td {
        padding: 0.5rem;
    }

    .assignment-title {
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .empty-terminals {
        padding: 2rem 1rem;
    }

    .status-group {
        width: 100%;
    }

    .badge {
        flex: 1;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
// ==============================
// Terminal Search Functionality
// ==============================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('terminalSearch');
    const tableBody = document.getElementById('terminalsTableBody');
    const noResults = document.getElementById('noResults');

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('.terminal-row');
            let visibleCount = 0;

            rows.forEach(function(row) {
                const searchableText = row.getAttribute('data-searchable');

                if (searchableText.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (noResults) {
                if (visibleCount === 0 && searchTerm !== '') {
                    noResults.style.display = 'block';
                } else {
                    noResults.style.display = 'none';
                }
            }
        });
    }
});

// ==============================
// Status Update Function
// ==============================
function updateStatus(assignmentId, newStatus, buttonElement) {
    // Disable button and show loading
    const originalText = buttonElement.innerHTML;
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

    // Make AJAX request
    fetch(`/api/assignments/${assignmentId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('success', data.message || 'Status updated successfully');

            // Reload page after short delay to reflect changes
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', error.message || 'Failed to update status');

        // Re-enable button
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalText;
    });
}

// ==============================
// Show notification function
// ==============================
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
            <span>${message}</span>
        </div>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Remove notification after 4 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 4000);
}

// ==============================
// Live Site Visits Polling (3b)
// ==============================
(function(){
    const assignmentId = {{ (int)$assignment->id }};
    const listEl = document.getElementById('liveVisitsList');
    const countEl = document.getElementById('liveVisitCount');

    async function fetchVisits() {
        try {
            const res = await fetch(`{{ route('api.jobs.assignments.visits', $assignment->id) }}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) throw new Error('Feed error');

            countEl.textContent = data.count;

            if (data.count === 0) {
                listEl.innerHTML = '<div class="text-muted">No visits yet for this assignment.</div>';
                return;
            }

            listEl.innerHTML = data.visits.map(v => `
                <div class="list-group-item" style="padding:12px 0; border-bottom:1px solid #f1f5f9;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-weight:600;">
                            ${v.merchant_name ?? '—'} <span style="color:#6b7280;">(${v.terminal_id ?? '—'})</span>
                        </div>
                        <span class="badge" style="background:#f3f4f6;color:#374151;">
                            ${v.status ? v.status.replace('_',' ') : 'open'}
                        </span>
                    </div>
                    <div style="color:#6b7280; font-size:12px; margin-top:4px;">
                        Tech: ${v.technician ?? '—'} • Started: ${v.started_at ?? '—'} ${v.ended_at ? '• Ended: '+v.ended_at : ''}
                    </div>
                    <div style="margin-top:6px; font-size:13px;">
                        <strong>Terminal Status:</strong> ${v.terminal_status ?? '—'}
                        ${v.comments ? `<div style="color:#374151; margin-top:4px;">${v.comments}</div>` : ''}
                    </div>
                </div>
            `).join('');
        } catch (e) {
            console.error(e);
            listEl.innerHTML = '<div class="text-danger">Failed to load live visits.</div>';
        }
    }

    // initial + poll every 10s
    fetchVisits();
    setInterval(fetchVisits, 10000);
})();
</script>
@endpush
