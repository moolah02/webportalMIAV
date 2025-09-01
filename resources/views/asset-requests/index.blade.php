@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="header">
        <div>
            <h2 class="title">📋 My Asset Requests</h2>
            <p class="subtitle">Track and manage your asset requests</p>
        </div>
        <div class="actions">
            <a href="{{ route('asset-requests.cart') }}" class="btn btn-outline">
                <span>🛒 View Cart</span>
            </a>
            <a href="{{ route('asset-requests.catalog') }}" class="btn btn-primary">
                <span>➕ New Request</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label>Status</label>
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>✅ Approved</option>
                    <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>❌ Rejected</option>
                    <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>📦 Fulfilled</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>🛑 Cancelled</option>
                    <option value="draft"     {{ request('status') == 'draft'     ? 'selected' : '' }}>📝 Draft</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-filter">Apply Filters</button>
                @if(request('status'))
                <a href="{{ route('asset-requests.index') }}" class="btn btn-clear">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Request Number</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Total Items</th>
                <th>Est. Cost</th>
                <th>Needed By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr data-status="{{ $request->status }}">
                <td>{{ $request->request_number }}</td>
                <td>
                    <span class="status-badge status-{{ $request->status }}">
                        @switch($request->status)
                            @case('pending')
                                ⏳ Pending
                                @break
                            @case('approved')
                                ✅ Approved
                                @break
                            @case('rejected')
                                ❌ Rejected
                                @break
                            @case('fulfilled')
                                📦 Fulfilled
                                @break
                            @case('cancelled')
                                🛑 Cancelled
                                @break
                            @case('draft')
                                📝 Draft
                                @break
                            @default
                                {{ ucfirst($request->status) }}
                        @endswitch
                    </span>
                </td>
                <td>{{ $request->created_at->format('M d, Y') }}</td>
                <td>{{ $request->items->count() }}</td>
                <td>${{ number_format($request->total_estimated_cost, 2) }}</td>
                <td>{{ $request->needed_by_date ? $request->needed_by_date->format('M d, Y') : 'ASAP' }}</td>
                <td>
                    <a href="{{ route('asset-requests.show', $request) }}" class="btn btn-primary"> View Details</a>
                    @if(in_array($request->status, ['pending', 'draft']))
                    <form action="{{ route('asset-requests.cancel', $request) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel this request?')" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="action-btn danger">❌ Cancel</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h3 class="empty-title">No Asset Requests Yet</h3>
                    <p class="empty-description">You haven't submitted any asset requests. Get started by browsing our asset catalog.</p>
                    <a href="{{ route('asset-requests.catalog') }}" class="btn btn-primary empty-action">🛒 Browse Asset Catalog</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($requests->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination">
            {{ $requests->appends(request()->query())->links('vendor.pagination.custom') }}
        </div>
    </div>
    @endif
</div>

<style>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.title { margin: 0; color: #333; font-size: 32px; font-weight: 700; }
.subtitle { color: #666; margin: 8px 0 0 0; font-size: 16px; }
.actions { display: flex; gap: 12px; }

.filter-card {
    background: white; padding: 20px; border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 30px;
}
.filter-form { display: flex; gap: 15px; }
.filter-select { padding: 10px; border: 1px solid #ccc; border-radius: 6px; }

.table {
    width: 100%; border-collapse: collapse; background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.table th, .table td { padding: 12px; border: 1px solid #dee2e6; text-align: left; }
.table th { background-color: #f8f9fa; }

.status-badge {
    padding: 5px 10px; border-radius: 12px; font-weight: 600; display: inline-block;
}
/* Colors for each status */
.status-pending   { background:#fff3cd; color:#856404; }   /* amber */
.status-approved  { background:#e8f5e9; color:#2e7d32; }   /* green */
.status-rejected  { background:#ffebee; color:#c62828; }   /* red */
.status-fulfilled { background:#e3f2fd; color:#1565c0; }   /* blue */
.status-cancelled { background:#eceff1; color:#455a64; }   /* grey */
.status-draft     { background:#f3e5f5; color:#6a1b9a; }   /* purple */

.pagination-wrapper { display: flex; justify-content: center; margin-top: 30px; }

/* Empty State */
.empty-state { text-align: center; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
.empty-icon { font-size: 64px; margin-bottom: 20px; }
.empty-title { font-size: 24px; color: #333; }
.empty-description { color: #666; margin-bottom: 20px; }
.empty-action { padding: 10px 20px; }

/* Responsive */
@media (max-width: 768px) {
    .request-header { flex-direction: column; align-items: flex-start; }
    .request-badges { justify-content: flex-start; }
}
</style>
@endsection
