{{-- File: resources/views/asset-approvals/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Asset Approvals')

@section('content')
    {{-- Ensure BASE is available for JS to build correct form actions --}}
    <meta name="app-base-url" content="{{ url('/') }}">


    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-sm text-gray-500 mt-1">Review and approve employee asset requests</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('assets.index') }}" class="btn-secondary">📦 Manage Assets</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">&#x23F3;</div>
            <div>
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">Pending Requests</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-red">&#x1F6A8;</div>
            <div>
                <div class="stat-number">{{ $stats['pending_high_priority'] }}</div>
                <div class="stat-label">High Priority</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">&#x1F4B0;</div>
            <div>
                <div class="stat-number">${{ number_format($stats['total_pending_value'], 0) }}</div>
                <div class="stat-label">Pending Value</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">&#x1F4C8;</div>
            <div>
                <div class="stat-number">{{ $stats['requests_this_month'] }}</div>
                <div class="stat-label">This Month</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="filter-bar">
        <div class="filter-group">
            <label class="ui-label">Status</label>
            <select name="status" class="ui-select">
                <option value="">All Status</option>
                <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>Approved</option>
                <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>Rejected</option>
                <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label">Priority</label>
            <select name="priority" class="ui-select">
                <option value="">All Priority</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="high"   {{ request('priority') == 'high'   ? 'selected' : '' }}>High</option>
                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="low"    {{ request('priority') == 'low'    ? 'selected' : '' }}>Low</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="ui-input">
        </div>
        <div class="filter-group">
            <label class="ui-label">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="ui-input">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply</button>
            @if(request()->hasAny(['status', 'priority', 'date_from', 'date_to']))
            <a href="{{ route('asset-approvals.index') }}" class="btn-secondary">Clear</a>
            @endif
        </div>
    </form>

    <!-- Requests Table -->
    <div class="ui-card overflow-hidden mt-4">
        <div class="ui-card-header">
            <span class="text-sm font-semibold text-gray-800">Asset Requests</span>
            <span class="badge badge-gray">{{ $requests->total() }} requests</span>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Request #</th>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created At</th>
                        <th>Total Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    @php
                        $statusClass = match($request->status) {
                            'approved'  => 'badge-green',
                            'fulfilled' => 'badge-blue',
                            'rejected'  => 'badge-red',
                            'pending'   => 'badge-yellow',
                            default     => 'badge-gray',
                        };
                        $priorityClass = match($request->priority) {
                            'urgent' => 'badge-red',
                            'high'   => 'badge-orange',
                            'normal' => 'badge-blue',
                            'low'    => 'badge-gray',
                            default  => 'badge-gray',
                        };
                    @endphp
                    <tr>
                        <td>
                            @if($request->status === 'pending')
                                <input type="checkbox" name="request_ids[]" value="{{ $request->id }}" class="request-checkbox">
                            @endif
                        </td>
                        <td><span class="code-chip">{{ $request->request_number }}</span></td>
                        <td class="text-sm font-medium text-gray-800">{{ $request->employee->full_name }}</td>
                        <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($request->status) }}</span></td>
                        <td><span class="status-badge {{ $priorityClass }}">{{ ucfirst($request->priority) }}</span></td>
                        <td class="text-xs text-gray-600">{{ $request->created_at->format('M d, Y') }}<br><span class="text-gray-400">{{ $request->created_at->format('g:i A') }}</span></td>
                        <td class="text-sm font-semibold text-gray-800">${{ number_format($request->total_estimated_cost, 2) }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('asset-approvals.show', $request) }}" class="btn-secondary btn-sm">Review</a>
                                @if($request->status === 'pending')
                                    <button onclick="showQuickApprove({{ $request->id }})" class="action-btn action-view" title="Quick Approve">&#x2705;</button>
                                    <button onclick="showQuickReject({{ $request->id }})" class="action-btn action-delete" title="Quick Reject">&#x274C;</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center text-gray-400">
                            <div class="text-4xl mb-3">&#x1F4CB;</div>
                            <p class="text-sm font-medium text-gray-600">No requests to review</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="ui-card-footer justify-center">
            {{ $requests->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

<!-- Modals for Quick Approve and Reject -->
<div id="quickApproveModal" class="modal">
    <div class="modal-content">
        <h3>✅ Quick Approve Request</h3>
        <form id="quickApproveForm" method="POST">
            @csrf
            <input type="hidden" name="request_id" id="approveRequestId">
            <div>
                <label>Approval Notes (Optional)</label>
                <textarea name="approval_notes" rows="3" placeholder="Any notes for the requester..." class="ui-input"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Approve Request</button>
            </div>
        </form>
    </div>
</div>

<div id="quickRejectModal" class="modal">
    <div class="modal-content">
        <h3>❌ Reject Request</h3>
        <form id="quickRejectForm" method="POST">
            @csrf
            <input type="hidden" name="request_id" id="rejectRequestId">
            <div>
                <label>Rejection Reason *</label>
                <textarea name="rejection_reason" rows="4" required placeholder="Please explain why this request is being rejected..." class="ui-input"></textarea>
                <div class="note">This reason will be visible to the employee</div>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-danger">Reject Request</button>
            </div>
        </form>
    </div>
</div>


<script>
const BASE = (document.querySelector('meta[name="app-base-url"]')?.content || '').replace(/\/$/, '');

// Quick approve/reject with base-aware actions
function showQuickApprove(requestId) {
    document.getElementById('approveRequestId').value = requestId;
    document.getElementById('quickApproveForm').action = `${BASE}/asset-approvals/${requestId}/approve`;
    document.getElementById('quickApproveModal').style.display = 'block';
}

function showQuickReject(requestId) {
    document.getElementById('rejectRequestId').value = requestId;
    document.getElementById('quickRejectForm').action = `${BASE}/asset-approvals/${requestId}/reject`;
    document.getElementById('quickRejectModal').style.display = 'block';
}

// Close modal + reset
function closeModal() {
    document.getElementById('quickApproveModal').style.display = 'none';
    document.getElementById('quickRejectModal').style.display = 'none';
    document.getElementById('quickApproveForm').reset();
    document.getElementById('quickRejectForm').reset();
}

// Backdrop & ESC close
document.addEventListener('click', e => { if (e.target.classList.contains('modal')) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// Optional: prevent double submit
['quickApproveForm','quickRejectForm'].forEach(id => {
    const form = document.getElementById(id);
    form?.addEventListener('submit', () => {
        const btn = form.querySelector('button[type="submit"]');
        btn && (btn.disabled = true);
    });
});
</script>
@endsection
