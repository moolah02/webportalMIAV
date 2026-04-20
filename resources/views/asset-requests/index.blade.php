@extends('layouts.app')
@section('title', 'My Asset Requests')

@section('content')

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-sm text-gray-500 mt-1">Track and manage your asset requests</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('asset-requests.cart') }}" class="btn-secondary">
                <span>🛒 View Cart</span>
            </a>
            <a href="{{ route('asset-requests.catalog') }}" class="btn-primary">
                <span>➕ New Request</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label>Status</label>
                <select name="status" class="ui-select">
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
                <button type="submit" class="btn-filter">Apply Filters</button>
                @if(request('status'))
                <a href="{{ route('asset-requests.index') }}" class="btn-clear">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <table class="ui-table">
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
                    <span class="badge badge-gray">
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
                    <a href="{{ route('asset-requests.show', $request) }}" class="btn-primary"> View Details</a>
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
                    <a href="{{ route('asset-requests.catalog') }}" class="btn-primary empty-action">🛒 Browse Asset Catalog</a>
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

@endsection
