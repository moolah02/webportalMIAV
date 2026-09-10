@extends('layouts.app')
@section('title', 'My Asset Requests')

@push('styles')
<style>
.ar-index .rq-toolbar { display: flex; align-items: center; gap: 10px 12px; flex-wrap: wrap; margin-bottom: 16px; }
.ar-index .rq-filters { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin: 0; }
.ar-index .rq-filter { display: flex; align-items: center; gap: 8px; margin: 0; }
.ar-index .rq-filter > span { font-size: 12.5px; font-weight: 500; color: var(--mv-muted); }
.ar-index .rq-toolbar .ui-select { width: auto; min-width: 140px; height: 36px; padding-top: 0; padding-bottom: 0; padding-right: 32px; font-size: 13.5px; }
.ar-index .rq-toolbar .btn-primary, .ar-index .rq-toolbar .btn-secondary { height: 36px; padding-top: 0; padding-bottom: 0; }
.ar-index .rq-toolbar-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }

.ar-index .ui-table th.num, .ar-index .ui-table td.num { text-align: right; }
.ar-index .ui-table th.act, .ar-index .ui-table td.act { width: 1%; text-align: right; }
.ar-index .ui-table td.act .action-group { justify-content: flex-end; }
.ar-index .ui-table td.act form { margin: 0; }
.ar-index .rq-muted { color: var(--mv-muted); }

.ar-index .ui-table tbody tr.rq-empty-row:hover { background: transparent; }
.ar-index .rq-empty { padding: 40px 20px; text-align: center; }
.ar-index .rq-empty .mv-i { display: block; width: 28px; height: 28px; color: var(--mv-line-strong); margin: 0 auto 8px; }
.ar-index .rq-empty-title { margin: 0 0 2px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.ar-index .rq-empty-text { margin: 0 0 14px; font-size: 13px; color: var(--mv-muted); }
</style>
@endpush

@section('content')
<div class="ar-index">

    {{-- Toolbar: filters left, actions right --}}
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
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="draft"     {{ request('status') == 'draft'     ? 'selected' : '' }}>Draft</option>
                </select>
            </label>
            <button type="submit" class="btn-primary">Apply</button>
            @if(request('status'))
            <a href="{{ route('asset-requests.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>

        <div class="rq-toolbar-right">
            <a href="{{ route('asset-requests.cart') }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-cart"/></svg>
                View Cart
            </a>
            <a href="{{ route('asset-requests.catalog') }}" class="btn-primary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg>
                New Request
            </a>
        </div>
    </div>

    {{-- Requests Table --}}
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <h2>My Requests</h2>
            <span class="badge badge-gray">{{ $requests->total() }} requests</span>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="num">Items</th>
                        <th class="num">Est. Cost</th>
                        <th>Needed By</th>
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
                            'cancelled'          => 'badge-gray',
                            'draft'              => 'badge-gray',
                            default              => 'badge-gray',
                        };
                    @endphp
                    <tr>
                        <td><span class="code-chip">{{ $request->request_number }}</span></td>
                        <td><span class="status-badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span></td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td class="num">{{ $request->items->count() }}</td>
                        <td class="num"><span class="cell-primary">${{ number_format($request->total_estimated_cost, 2) }}</span></td>
                        <td>
                            @if($request->needed_by_date)
                                {{ $request->needed_by_date->format('M d, Y') }}
                            @else
                                <span class="rq-muted">ASAP</span>
                            @endif
                        </td>
                        <td class="act">
                            <div class="action-group">
                                <a href="{{ route('asset-requests.show', $request) }}" class="action-btn action-view" title="View Details" aria-label="View details">
                                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>
                                </a>
                                @if(in_array($request->status, ['pending', 'draft']))
                                <form action="{{ route('asset-requests.cancel', $request) }}" method="POST"
                                      onsubmit="return confirm('Cancel this request?')" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn action-delete" title="Cancel Request" aria-label="Cancel request">
                                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="rq-empty-row">
                        <td colspan="7" class="rq-empty">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-clipboard"/></svg>
                            <p class="rq-empty-title">No Asset Requests Yet</p>
                            <p class="rq-empty-text">Browse the catalog to submit your first request.</p>
                            <a href="{{ route('asset-requests.catalog') }}" class="btn-primary btn-sm">Browse Asset Catalog</a>
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

</div>
@endsection
