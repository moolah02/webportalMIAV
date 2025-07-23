@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📋 My Asset Requests</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Track the status of your asset requests</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('asset-requests.catalog') }}" class="btn btn-primary">🛒 New Request</a>
            <a href="{{ route('asset-requests.cart') }}" class="btn">View Cart</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center;">
            <select name="status" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
            </select>
            
            <button type="submit" class="btn">Filter</button>
            
            @if(request('status'))
            <a href="{{ route('asset-requests.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Requests List -->
    @forelse($requests as $request)
    <div class="content-card" style="margin-bottom: 15px;">
        <div style="display: flex; justify-content: between; align-items: start; gap: 20px;">
            <!-- Request Info -->
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                    <div>
                        <h4 style="margin: 0; color: #333;">Request {{ $request->request_number }}</h4>
                        <div style="color: #666; font-size: 14px; margin-top: 2px;">
                            Submitted {{ $request->created_at->format('M d, Y \a\t g:i A') }}
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span class="status-badge {{ $request->status_badge }}">
                            {{ ucfirst($request->status) }}
                        </span>
                        <span class="status-badge {{ $request->priority_badge }}">
                            {{ ucfirst($request->priority) }}
                        </span>
                    </div>
                </div>

                <!-- Items Summary -->
                <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <div style="font-weight: 500; margin-bottom: 5px;">Items Requested:</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @foreach($request->items->take(3) as $item)
                        <span style="background: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                            {{ $item->quantity_requested }}× {{ $item->asset->name }}
                        </span>
                        @endforeach
                        @if($request->items->count() > 3)
                        <span style="color: #666; font-size: 12px; padding: 4px 8px;">
                            +{{ $request->items->count() - 3 }} more
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Request Details -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; font-size: 14px;">
                    <div>
                        <strong>Total Items:</strong> {{ $request->total_items }}
                    </div>
                    <div>
                        <strong>Estimated Cost:</strong> ${{ number_format($request->total_estimated_cost, 2) }}
                    </div>
                    <div>
                        <strong>Needed By:</strong> {{ $request->needed_by_date ? $request->needed_by_date->format('M d, Y') : 'Not specified' }}
                    </div>
                </div>

                <!-- Status-specific Info -->
                @if($request->status === 'approved' && $request->approver)
                <div style="background: #e8f5e8; padding: 10px; border-radius: 6px; margin-top: 10px;">
                    <div style="color: #2e7d32; font-weight: 500; font-size: 14px;">✅ Approved by {{ $request->approver->full_name }}</div>
                    <div style="color: #666; font-size: 12px;">{{ $request->approved_at->format('M d, Y \a\t g:i A') }}</div>
                    @if($request->approval_notes)
                    <div style="color: #666; font-size: 12px; margin-top: 5px;">{{ $request->approval_notes }}</div>
                    @endif
                </div>
                @elseif($request->status === 'rejected')
                <div style="background: #ffebee; padding: 10px; border-radius: 6px; margin-top: 10px;">
                    <div style="color: #f44336; font-weight: 500; font-size: 14px;">❌ Request Rejected</div>
                    @if($request->rejection_reason)
                    <div style="color: #666; font-size: 12px; margin-top: 5px;">{{ $request->rejection_reason }}</div>
                    @endif
                </div>
                @elseif($request->status === 'fulfilled')
                <div style="background: #e3f2fd; padding: 10px; border-radius: 6px; margin-top: 10px;">
                    <div style="color: #1976d2; font-weight: 500; font-size: 14px;">📦 Items Delivered</div>
                    <div style="color: #666; font-size: 12px;">{{ $request->fulfilled_at->format('M d, Y \a\t g:i A') }}</div>
                    @if($request->fulfillment_notes)
                    <div style="color: #666; font-size: 12px; margin-top: 5px;">{{ $request->fulfillment_notes }}</div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div style="display: flex; flex-direction: column; gap: 8px; min-width: 120px;">
                <a href="{{ route('asset-requests.show', $request) }}" class="btn-small" style="text-align: center;">
                    View Details
                </a>
                
                @if(in_array($request->status, ['pending', 'draft']))
                <form action="{{ route('asset-requests.cancel', $request) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-small" style="width: 100%; background: #f44336; color: white; border-color: #f44336;">
                        Cancel
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="content-card" style="text-align: center; padding: 60px;">
        <div style="font-size: 64px; margin-bottom: 20px;">📋</div>
        <h3>No requests yet</h3>
        <p style="color: #666; margin-bottom: 30px;">You haven't submitted any asset requests.</p>
        <a href="{{ route('asset-requests.catalog') }}" class="btn btn-primary">
            Browse Assets
        </a>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($requests->hasPages())
    <div style="margin-top: 20px;">
        {{ $requests->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection