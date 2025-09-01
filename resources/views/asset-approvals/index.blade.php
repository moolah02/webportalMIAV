{{-- File: resources/views/asset-approvals/index.blade.php --}}
@extends('layouts.app')

@section('content')
    {{-- Ensure BASE is available for JS to build correct form actions --}}
    <meta name="app-base-url" content="{{ url('/') }}">

<div class="container">
    <!-- Header -->
    <div class="header">
        <div>
            <h2 class="title">⚖️ Asset Request Approvals</h2>
            <p class="subtitle">Review and approve employee asset requests</p>
        </div>
        <div class="actions">
            <a href="{{ route('assets.index') }}" class="btn">📦 Manage Assets</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="metric-card pending">
            <div class="metric-icon">⏳</div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['pending_requests'] }}</div>
                <div class="metric-label">Pending Requests</div>
            </div>
        </div>
        <div class="metric-card high-priority">
            <div class="metric-icon">🚨</div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['pending_high_priority'] }}</div>
                <div class="metric-label">High Priority</div>
            </div>
        </div>
        <div class="metric-card total-value">
            <div class="metric-icon">💰</div>
            <div class="metric-content">
                <div class="metric-number">${{ number_format($stats['total_pending_value'], 0) }}</div>
                <div class="metric-label">Pending Value</div>
            </div>
        </div>
        <div class="metric-card requests-month">
            <div class="metric-icon">📈</div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['requests_this_month'] }}</div>
                <div class="metric-label">This Month</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending"   {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved"  {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected"  {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                </select>
            </div>
            <div class="filter-group">
                <select name="priority" class="filter-select">
                    <option value="">All Priority</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high"   {{ request('priority') == 'high'   ? 'selected' : '' }}>High</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low"    {{ request('priority') == 'low'    ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="filter-group">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
            </div>
            <div class="filter-group">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
            </div>
            <button type="submit" class="btn">Filter</button>
            @if(request()->hasAny(['status', 'priority', 'date_from', 'date_to']))
            <a href="{{ route('asset-approvals.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Requests Table -->
    <table class="table">
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
                    <span class="status-badge status-{{ $request->status }}">{{ ucfirst($request->status) }}</span>
                </td>
                <td>
                    <span class="priority-badge priority-{{ $request->priority }}">{{ ucfirst($request->priority) }}</span>
                </td>
                <td>{{ $request->created_at->format('M d, Y \a\t g:i A') }}</td>
                <td>${{ number_format($request->total_estimated_cost, 2) }}</td>
                <td>
                    <a href="{{ route('asset-approvals.show', $request) }}" class="btn btn-small">Review</a>
                    @if($request->status === 'pending')
                        <button onclick="showQuickApprove({{ $request->id }})" class="btn btn-small">✅</button>
                        <button onclick="showQuickReject({{ $request->id }})" class="btn btn-small">❌</button>
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

    <!-- Pagination -->
    @if($requests->hasPages())
    <div class="pagination-wrapper">
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
                <textarea name="approval_notes" rows="3" placeholder="Any notes for the requester..." class="modal-textarea"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Approve Request</button>
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
                <textarea name="rejection_reason" rows="4" required placeholder="Please explain why this request is being rejected..." class="modal-textarea"></textarea>
                <div class="note">This reason will be visible to the employee</div>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject Request</button>
            </div>
        </form>
    </div>
</div>

<style>
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
.title { font-size:32px; font-weight:700; }
.subtitle { color:#666; margin-top:5px; }
.stats-container { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:20px; margin-bottom:30px; }
.metric-card { background:white; padding:20px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1); display:flex; align-items:center; }
.metric-icon { font-size:32px; margin-right:15px; }
.metric-content { flex:1; }
.metric-number { font-size:28px; font-weight:bold; }
.metric-label { font-size:14px; color:#666; }

.filter-card { background:white; padding:20px; border-radius:12px; margin-bottom:20px; }
.filter-form { display:flex; gap:15px; flex-wrap:wrap; }
.filter-group { flex:1; }
.filter-select, .bulk-action-select, .bulk-notes { padding:8px; border:2px solid #ddd; border-radius:4px; width:100%; }

.table { width:100%; border-collapse:collapse; margin-bottom:20px; background:white; }
.table th, .table td { padding:12px; border:1px solid #ddd; text-align:left; }
.table th { background:#f8f9fa; font-weight:bold; }

.status-badge { padding:4px 12px; border-radius:12px; font-size:12px; font-weight:500; }
.status-pending { background:#fff3e0; color:#f57c00; }
.status-approved { background:#e8f5e8; color:#2e7d32; }
.status-rejected { background:#ffebee; color:#d32f2f; }
.status-fulfilled { background:#e3f2fd; color:#1976d2; }

.priority-badge { padding:4px 8px; border-radius:8px; font-size:11px; font-weight:500; }
.priority-low { background:#e8f5e8; color:#2e7d32; }
.priority-normal { background:#e3f2fd; color:#1976d2; }
.priority-high { background:#fff3e0; color:#f57c00; }
.priority-urgent { background:#ffebee; color:#d32f2f; }

.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; }
.modal-content { position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:30px; border-radius:8px; width:90%; max-width:500px; box-shadow:0 4px 20px rgba(0,0,0,0.3); }
.modal-textarea { width:100%; padding:8px; border:2px solid #ddd; border-radius:4px; }
.note { font-size:12px; color:#666; margin-top:5px; }
.modal-actions { display:flex; gap:10px; justify-content:flex-end; }

.btn { padding:8px 16px; border:2px solid #ddd; border-radius:6px; background:white; color:#333; text-decoration:none; cursor:pointer; font-weight:500; transition:all .2s; }
.btn:hover { border-color:#2196f3; color:#2196f3; }
.btn-primary { background:#2196f3; color:white; border-color:#2196f3; }
.btn-danger { background:#f44336; color:white; border-color:#f44336; }
.btn-small { padding:6px 12px; font-size:14px; }
.pagination-wrapper { display:flex; justify-content:center; margin-top:20px; }
.empty-state { text-align:center; padding:20px; }
</style>

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
