@extends('layouts.app')
@section('title', 'My Asset Requests')

@section('content')

{{-- Header actions --}}
<div class="flex justify-end items-center gap-2 mb-5">
    <a href="{{ route('asset-requests.cart') }}" class="btn-secondary">🛒 View Cart</a>
    <a href="{{ route('asset-requests.catalog') }}" class="btn-primary">+ New Request</a>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div class="filter-group">
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select">
            <option value="">All Status</option>
            <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>Approved</option>
            <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>Rejected</option>
            <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="draft"     {{ request('status') == 'draft'     ? 'selected' : '' }}>Draft</option>
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn-primary">Apply</button>
        @if(request('status'))
        <a href="{{ route('asset-requests.index') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

{{-- Requests Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">My Requests</span>
        <span class="badge badge-gray">{{ $requests->total() }} requests</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Items</th>
                    <th>Est. Cost</th>
                    <th>Needed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td><span class="code-chip">{{ $request->request_number }}</span></td>
                    <td>
                        @php
                            $statusClass = match($request->status) {
                                'approved'  => 'badge-green',
                                'fulfilled' => 'badge-blue',
                                'rejected'  => 'badge-red',
                                'cancelled' => 'badge-red',
                                'pending'   => 'badge-yellow',
                                'draft'     => 'badge-gray',
                                default     => 'badge-gray',
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                    </td>
                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                    <td>{{ $request->items->count() }}</td>
                    <td>${{ number_format($request->total_estimated_cost, 2) }}</td>
                    <td>{{ $request->needed_by_date ? $request->needed_by_date->format('M d, Y') : 'ASAP' }}</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('asset-requests.show', $request) }}" class="action-btn action-view" title="View Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if(in_array($request->status, ['pending', 'draft']))
                            <form action="{{ route('asset-requests.cancel', $request) }}" method="POST"
                                  onsubmit="return confirm('Cancel this request?')" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="action-btn action-delete" title="Cancel Request">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400">
                        <div class="text-4xl mb-3">📋</div>
                        <p class="text-sm font-medium text-gray-600 mb-1">No Asset Requests Yet</p>
                        <p class="text-xs text-gray-400 mb-4">Browse the catalog to submit your first request.</p>
                        <a href="{{ route('asset-requests.catalog') }}" class="btn-primary btn-sm">🛒 Browse Asset Catalog</a>
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

@endsection
