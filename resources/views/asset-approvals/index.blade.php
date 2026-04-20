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
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">⏳</div>
            <div class="flex-1 min-w-0">
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label uppercase tracking-wide">Pending Requests</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">🚨</div>
            <div class="flex-1 min-w-0">
                <div class="stat-number">{{ $stats['pending_high_priority'] }}</div>
                <div class="stat-label uppercase tracking-wide">High Priority</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">💰</div>
            <div class="flex-1 min-w-0">
                <div class="stat-number">${{ number_format($stats['total_pending_value'], 0) }}</div>
                <div class="stat-label uppercase tracking-wide">Pending Value</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📈</div>
            <div class="flex-1 min-w-0">
                <div class="stat-number">{{ $stats['requests_this_month'] }}</div>
                <div class="stat-label uppercase tracking-wide">This Month</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
        <form method="GET" class="flex flex-wrap gap-3 items-center w-full">
            <select name="status" class="ui-select w-auto">
                    <option value="">All Status</option>
                    <option value="pending"   {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved"  {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected"  {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                </select>
            <select name="priority" class="ui-select">
                    <option value="">All Priority</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high"   {{ request('priority') == 'high'   ? 'selected' : '' }}>High</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low"    {{ request('priority') == 'low'    ? 'selected' : '' }}>Low</option>
                </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="ui-input">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="ui-input">
            <button type="submit" class="btn-secondary">Filter</button>
            @if(request()->hasAny(['status', 'priority', 'date_from', 'date_to']))
            <a href="{{ route('asset-approvals.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    <!-- Requests Table -->
    <div class="ui-card overflow-hidden mt-4">
        <div class="overflow-x-auto">
    <table class="ui-table">
        <thead>
            <tr>
                <th>Select</th>
                <th>Request Number</th>
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
            <tr class="{{ $request->priority }}">
                <td>
                    @if($request->status === 'pending')
                        <input type="checkbox" name="request_ids[]" value="{{ $request->id }}" class="request-checkbox">
                    @endif
                </td>
                <td>{{ $request->request_number }}</td>
                <td>{{ $request->employee->full_name }}</td>
                <td>
                    <span class="badge badge-gray">{{ ucfirst($request->status) }}</span>
                </td>
                <td>
                    <span class="badge badge-gray">{{ ucfirst($request->priority) }}</span>
                </td>
                <td>{{ $request->created_at->format('M d, Y \a\t g:i A') }}</td>
                <td>${{ number_format($request->total_estimated_cost, 2) }}</td>
                <td>
                    <a href="{{ route('asset-approvals.show', $request) }}" class="btn-secondary btn-sm">Review</a>
                    @if($request->status === 'pending')
                        <button onclick="showQuickApprove({{ $request->id }})" class="btn-secondary btn-sm">✅</button>
                        <button onclick="showQuickReject({{ $request->id }})" class="btn-secondary btn-sm">❌</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="empty-state">No requests to review.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($requests->hasPages())
    <div class="pagination-wrapper">
        {{ $requests->appends(request()->query())->links() }}
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
