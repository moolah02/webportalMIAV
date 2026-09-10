{{-- File: resources/views/asset-approvals/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Asset Approvals')

@push('styles')
<style>
.aa-index .rq-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
.aa-index .rq-stats .stat-card { min-width: 0; }

.aa-index .rq-toolbar { display: flex; align-items: center; gap: 10px 12px; flex-wrap: wrap; margin-bottom: 16px; }
.aa-index .rq-filters { display: flex; align-items: center; gap: 8px 14px; flex-wrap: wrap; margin: 0; }
.aa-index .rq-filter { display: flex; align-items: center; gap: 8px; margin: 0; }
.aa-index .rq-filter > span { font-size: 12.5px; font-weight: 500; color: var(--mv-muted); }
.aa-index .rq-toolbar .ui-select, .aa-index .rq-toolbar .ui-input { width: auto; min-width: 0; height: 36px; padding-top: 0; padding-bottom: 0; font-size: 13.5px; }
.aa-index .rq-toolbar .ui-select { min-width: 132px; padding-right: 32px; }
.aa-index .rq-toolbar .btn-primary, .aa-index .rq-toolbar .btn-secondary { height: 36px; padding-top: 0; padding-bottom: 0; }
.aa-index .rq-filter-actions { display: flex; align-items: center; gap: 8px; }
.aa-index .rq-toolbar-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }

.aa-index .rq-card-title { min-width: 0; }
.aa-index .rq-card-title p { margin: 2px 0 0; font-size: 12.5px; color: var(--mv-muted); }

.aa-index .ui-table th.num, .aa-index .ui-table td.num { text-align: right; }
.aa-index .ui-table th.sel, .aa-index .ui-table td.sel { width: 1%; padding-right: 4px; }
.aa-index .ui-table th.act, .aa-index .ui-table td.act { width: 1%; text-align: right; }
.aa-index .ui-table td.act .action-group { justify-content: flex-end; }
.aa-index .request-checkbox { width: 15px; height: 15px; margin: 0; vertical-align: middle; accent-color: var(--mv-accent); cursor: pointer; }
.aa-index .action-btn.rq-approve:hover { background: var(--mv-good-soft) !important; color: var(--mv-good) !important; border-color: #C6E6D2 !important; }

.aa-index .ui-table tbody tr.rq-empty-row:hover { background: transparent; }
.aa-index .rq-empty { padding: 40px 20px; text-align: center; }
.aa-index .rq-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); margin-bottom: 8px; }
.aa-index .rq-empty-title { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.aa-index .rq-empty .btn-secondary { margin-top: 14px; }

/* Modals (JS toggles .hidden on the overlay) */
.aa-index .rq-modal { position: fixed; inset: 0; z-index: 1100; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(22, 32, 44, .45); }
.aa-index .rq-modal-card { width: 100%; max-width: 440px; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
.aa-index .rq-modal-head { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
.aa-index .rq-modal-head h3 { flex: 1; margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
.aa-index .rq-modal-ic { width: 28px; height: 28px; flex-shrink: 0; border-radius: 8px; display: grid; place-items: center; }
.aa-index .rq-modal-ic.is-good { background: var(--mv-good-soft); color: var(--mv-good); }
.aa-index .rq-modal-ic.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); }
.aa-index .rq-modal-close { width: 30px; height: 30px; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); display: grid; place-items: center; cursor: pointer; }
.aa-index .rq-modal-close:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.aa-index .rq-modal-body { padding: 16px 18px 18px; }
.aa-index .rq-modal-body form { margin: 0; }
.aa-index .rq-modal-body .ui-label { margin-bottom: 6px; }
.aa-index .rq-modal-body .ui-textarea { font-size: 13.5px; }
.aa-index .rq-opt { font-weight: 400; color: var(--mv-muted); }
.aa-index .rq-req { color: var(--mv-crit); }
.aa-index .rq-hint { margin: 5px 0 0; font-size: 12px; color: var(--mv-muted); }
.aa-index .rq-modal-foot { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }

@media (max-width: 1000px) { .aa-index .rq-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
@endpush

@section('content')
<div class="aa-index">
    {{-- Ensure BASE is available for JS to build correct form actions --}}
    <meta name="app-base-url" content="{{ url('/') }}">

    <!-- Statistics Cards -->
    <div class="rq-stats">
        <div class="stat-card">
            <div class="stat-icon {{ $stats['pending_requests'] > 0 ? 'stat-icon-yellow' : 'stat-icon-gray' }}">
                <svg class="mv-i" aria-hidden="true"><use href="#i-hourglass"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">Pending Requests</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $stats['pending_high_priority'] > 0 ? 'stat-icon-red' : 'stat-icon-gray' }}">
                <svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $stats['pending_high_priority'] }}</div>
                <div class="stat-label">High Priority</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-gray">
                <svg class="mv-i" aria-hidden="true"><use href="#i-banknote"/></svg>
            </div>
            <div>
                <div class="stat-number">${{ number_format($stats['total_pending_value'], 0) }}</div>
                <div class="stat-label">Pending Value</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-gray">
                <svg class="mv-i" aria-hidden="true"><use href="#i-calendar"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $stats['requests_this_month'] }}</div>
                <div class="stat-label">This Month</div>
            </div>
        </div>
    </div>

    <!-- Toolbar: filters left, actions right -->
    <div class="rq-toolbar">
        <form method="GET" class="rq-filters">
            <label class="rq-filter">
                <span>Status</span>
                <select name="status" class="ui-select">
                    <option value="">All Status</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>Approved</option>
                    <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>Rejected</option>
                    <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                </select>
            </label>
            <label class="rq-filter">
                <span>Priority</span>
                <select name="priority" class="ui-select">
                    <option value="">All Priority</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high"   {{ request('priority') == 'high'   ? 'selected' : '' }}>High</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low"    {{ request('priority') == 'low'    ? 'selected' : '' }}>Low</option>
                </select>
            </label>
            <label class="rq-filter">
                <span>From</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="ui-input">
            </label>
            <label class="rq-filter">
                <span>To</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="ui-input">
            </label>
            <div class="rq-filter-actions">
                <button type="submit" class="btn-primary">Apply</button>
                @if(request()->hasAny(['status', 'priority', 'date_from', 'date_to']))
                <a href="{{ route('asset-approvals.index') }}" class="btn-secondary">Clear</a>
                @endif
            </div>
        </form>

        <div class="rq-toolbar-right">
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-box"/></svg>
                Manage Assets
            </a>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <div class="rq-card-title">
                <h2>Asset Requests</h2>
                <p>Review and approve employee asset requests</p>
            </div>
            <span class="badge badge-gray">{{ $requests->total() }} requests</span>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th class="sel">Select</th>
                        <th>Request #</th>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created At</th>
                        <th class="num">Total Cost</th>
                        <th class="act">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    @php
                        $statusClass = match($request->status) {
                            'approved'           => 'badge-green',
                            'fulfilled'          => 'badge-blue',
                            'partially_approved' => 'badge-blue',
                            'rejected'           => 'badge-red',
                            'pending'            => 'badge-yellow',
                            default              => 'badge-gray',
                        };
                        $priorityClass = match($request->priority) {
                            'urgent' => 'badge-red',
                            'high'   => 'badge-yellow',
                            default  => 'badge-gray',
                        };
                    @endphp
                    <tr>
                        <td class="sel">
                            @if($request->status === 'pending')
                                <input type="checkbox" name="request_ids[]" value="{{ $request->id }}" class="request-checkbox" aria-label="Select {{ $request->request_number }}">
                            @endif
                        </td>
                        <td><span class="code-chip">{{ $request->request_number }}</span></td>
                        <td>
                            <div class="cell-primary">{{ $request->employee->full_name }}</div>
                            @if($request->employee?->department?->name)
                            <div class="cell-sub">{{ $request->employee->department->name }}</div>
                            @endif
                        </td>
                        <td><span class="status-badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span></td>
                        <td><span class="status-badge {{ $priorityClass }}">{{ ucfirst($request->priority) }}</span></td>
                        <td>
                            <div>{{ $request->created_at->format('M d, Y') }}</div>
                            <div class="cell-sub">{{ $request->created_at->format('g:i A') }}</div>
                        </td>
                        <td class="num"><span class="cell-primary">${{ number_format($request->total_estimated_cost, 2) }}</span></td>
                        <td class="act">
                            <div class="action-group">
                                <a href="{{ route('asset-approvals.show', $request) }}" class="btn-secondary btn-sm">Review</a>
                                @if($request->status === 'pending')
                                    <button type="button" onclick="showQuickApprove({{ $request->id }})" class="action-btn action-view rq-approve" title="Quick Approve" aria-label="Quick approve">
                                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg>
                                    </button>
                                    <button type="button" onclick="showQuickReject({{ $request->id }})" class="action-btn action-delete" title="Quick Reject" aria-label="Quick reject">
                                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="rq-empty-row">
                        <td colspan="8" class="rq-empty">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-inbox"/></svg>
                            <p class="rq-empty-title">No requests to review</p>
                            @if(request()->hasAny(['status', 'priority', 'date_from', 'date_to']))
                            <a href="{{ route('asset-approvals.index') }}" class="btn-secondary btn-sm">Clear</a>
                            @endif
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
    <div id="quickApproveModal" class="hidden rq-modal" role="dialog" aria-modal="true" aria-labelledby="quickApproveTitle">
        <div class="rq-modal-card">
            <div class="rq-modal-head">
                <span class="rq-modal-ic is-good"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-circle"/></svg></span>
                <h3 id="quickApproveTitle">Quick Approve Request</h3>
                <button type="button" onclick="closeModal()" class="rq-modal-close" aria-label="Close"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
            </div>
            <div class="rq-modal-body">
                <form id="quickApproveForm" method="POST">
                    @csrf
                    <input type="hidden" name="request_id" id="approveRequestId">
                    <label class="ui-label" for="quickApproveNotes">Approval Notes <span class="rq-opt">(optional)</span></label>
                    <textarea id="quickApproveNotes" name="approval_notes" rows="3" class="ui-textarea" placeholder="Any notes for the requester..."></textarea>
                    <div class="rq-modal-foot">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-success">Approve Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="quickRejectModal" class="hidden rq-modal" role="dialog" aria-modal="true" aria-labelledby="quickRejectTitle">
        <div class="rq-modal-card">
            <div class="rq-modal-head">
                <span class="rq-modal-ic is-crit"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x-circle"/></svg></span>
                <h3 id="quickRejectTitle">Reject Request</h3>
                <button type="button" onclick="closeModal()" class="rq-modal-close" aria-label="Close"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
            </div>
            <div class="rq-modal-body">
                <form id="quickRejectForm" method="POST">
                    @csrf
                    <input type="hidden" name="request_id" id="rejectRequestId">
                    <label class="ui-label" for="quickRejectReason">Rejection Reason <span class="rq-req">*</span></label>
                    <textarea id="quickRejectReason" name="rejection_reason" rows="4" required class="ui-textarea" placeholder="Please explain why this request is being rejected..."></textarea>
                    <p class="rq-hint">This reason will be visible to the employee.</p>
                    <div class="rq-modal-foot">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-danger">Reject Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const BASE = (document.querySelector('meta[name="app-base-url"]')?.content || '').replace(/\/$/, '');

// Quick approve/reject with base-aware actions
function showQuickApprove(requestId) {
    document.getElementById('approveRequestId').value = requestId;
    document.getElementById('quickApproveForm').action = `${BASE}/asset-approvals/${requestId}/approve`;
    document.getElementById('quickApproveModal').classList.remove('hidden');
}

function showQuickReject(requestId) {
    document.getElementById('rejectRequestId').value = requestId;
    document.getElementById('quickRejectForm').action = `${BASE}/asset-approvals/${requestId}/reject`;
    document.getElementById('quickRejectModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('quickApproveModal').classList.add('hidden');
    document.getElementById('quickRejectModal').classList.add('hidden');
    document.getElementById('quickApproveForm').reset();
    document.getElementById('quickRejectForm').reset();
}

document.addEventListener('click', e => {
    if (e.target.id === 'quickApproveModal' || e.target.id === 'quickRejectModal') closeModal();
});
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
