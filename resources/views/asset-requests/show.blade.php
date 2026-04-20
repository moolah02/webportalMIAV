@extends('layouts.app')
@section('title', 'Request Details')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <p style="color: #666; margin: 5px 0 0 0;">Submitted by {{ $assetRequest->employee->full_name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($assetRequest->employee_id === auth()->id())
                @if(in_array($assetRequest->status, ['pending', 'draft']))
                <form action="{{ route('asset-requests.cancel', $assetRequest) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this request?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-secondary" style="background: #f44336; color: white;">Cancel Request</button>
                </form>
                @endif
                <a href="{{ route('asset-requests.index') }}" class="btn-secondary">← My Requests</a>
            @else
                <a href="{{ route('asset-approvals.index') }}" class="btn-secondary">← Back to Approvals</a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Content -->
        <div>
            <!-- Request Status -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 15px;">
                    <h4 style="margin: 0; color: #333;">📊 Request Status</h4>
                    <div style="display: flex; gap: 10px;">
                        <span class="status-badge {{ $assetRequest->status_badge }}">
                            {{ ucfirst($assetRequest->status) }}
                        </span>
                        <span class="status-badge {{ $assetRequest->priority_badge }}">
                            {{ ucfirst($assetRequest->priority) }} Priority
                        </span>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div style="display: flex; align-items: center; gap: 15px; margin-block-end: 15px;">
                    <div class="timeline-step {{ in_array($assetRequest->status, ['pending', 'approved', 'fulfilled', 'rejected']) ? 'completed' : '' }}">
                        <div class="timeline-circle">📝</div>
                        <div class="timeline-label">Submitted</div>
                    </div>
                    <div class="timeline-line {{ in_array($assetRequest->status, ['approved', 'fulfilled']) ? 'completed' : '' }}"></div>
                    <div class="timeline-step {{ in_array($assetRequest->status, ['approved', 'fulfilled']) ? 'completed' : ($assetRequest->status === 'rejected' ? 'rejected' : '') }}">
                        <div class="timeline-circle">{{ $assetRequest->status === 'rejected' ? '❌' : '✅' }}</div>
                        <div class="timeline-label">{{ $assetRequest->status === 'rejected' ? 'Rejected' : 'Approved' }}</div>
                    </div>
                    <div class="timeline-line {{ $assetRequest->status === 'fulfilled' ? 'completed' : '' }}"></div>
                    <div class="timeline-step {{ $assetRequest->status === 'fulfilled' ? 'completed' : '' }}">
                        <div class="timeline-circle">📦</div>
                        <div class="timeline-label">Fulfilled</div>
                    </div>
                </div>

                <!-- Request Details -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Submitted</label>
                        <div style="font-weight: 500;">{{ $assetRequest->created_at->format('M d, Y \a\t g:i A') }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Needed By</label>
                        <div style="font-weight: 500;">{{ $assetRequest->needed_by_date ? $assetRequest->needed_by_date->format('M d, Y') : 'Not specified' }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Total Cost</label>
                        <div style="font-weight: 500;">${{ number_format($assetRequest->total_estimated_cost, 2) }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Items</label>
                        <div style="font-weight: 500;">{{ $assetRequest->total_items }} items</div>
                    </div>
                </div>
            </div>

            <!-- Business Justification -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">📝 Business Justification</h4>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #2196f3;">
                    {{ $assetRequest->business_justification }}
                </div>
                @if($assetRequest->delivery_instructions)
                <div style="margin-block-start: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase;">Delivery Instructions</label>
                    <div style="color: #666; margin-block-start: 5px;">{{ $assetRequest->delivery_instructions }}</div>
                </div>
                @endif
            </div>

            <!-- Requested Items -->
            <div class="ui-card p-6">
                <h4 style="margin-block-end: 20px; color: #333;">📦 Requested Items</h4>
                <div style="overflow-x: auto;">
                    <table style="inline-size: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 12px; text-align: start; border-block-end: 2px solid #eee;">Asset</th>
                                <th style="padding: 12px; text-align: center; border-block-end: 2px solid #eee;">Requested</th>
                                <th style="padding: 12px; text-align: center; border-block-end: 2px solid #eee;">Approved</th>
                                <th style="padding: 12px; text-align: center; border-block-end: 2px solid #eee;">Fulfilled</th>
                                <th style="padding: 12px; text-align: right; border-block-end: 2px solid #eee;">Unit Price</th>
                                <th style="padding: 12px; text-align: right; border-block-end: 2px solid #eee;">Total</th>
                                <th style="padding: 12px; text-align: center; border-block-end: 2px solid #eee;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetRequest->items as $item)
                            <tr>
                                <td style="padding: 12px; border-block-end: 1px solid #eee;">
                                    <div style="font-weight: 500;">{{ $item->asset->name }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ $item->asset->brand }} {{ $item->asset->model }}</div>
                                </td>
                                <td style="padding: 12px; text-align: center; border-block-end: 1px solid #eee;">
                                    {{ $item->quantity_requested }}
                                </td>
                                <td style="padding: 12px; text-align: center; border-block-end: 1px solid #eee;">
                                    {{ $item->quantity_approved ?: '-' }}
                                </td>
                                <td style="padding: 12px; text-align: center; border-block-end: 1px solid #eee;">
                                    {{ $item->quantity_fulfilled ?: '-' }}
                                </td>
                                <td style="padding: 12px; text-align: right; border-block-end: 1px solid #eee;">
                                    ${{ number_format($item->unit_price_at_request, 2) }}
                                </td>
                                <td style="padding: 12px; text-align: right; border-block-end: 1px solid #eee;">
                                    ${{ number_format($item->total_price, 2) }}
                                </td>
                                <td style="padding: 12px; text-align: center; border-block-end: 1px solid #eee;">
                                    <span class="status-badge {{ $item->status_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->item_status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Requester Info -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">👤 Requester</h4>
                <div style="margin-block-end: 10px;">
                    <div style="font-weight: 500;">{{ $assetRequest->employee->full_name }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->employee->role->name ?? 'Employee' }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->employee->department->name ?? 'No Department' }}</div>
                </div>
                @if($assetRequest->employee->email)
                <div style="font-size: 14px;">
                    📧 <a href="mailto:{{ $assetRequest->employee->email }}" style="color: #2196f3;">{{ $assetRequest->employee->email }}</a>
                </div>
                @endif
            </div>

            <!-- Approval Info -->
            @if($assetRequest->status === 'approved' || $assetRequest->status === 'fulfilled')
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">✅ Approval Details</h4>
                <div style="margin-block-end: 10px;">
                    <div style="font-weight: 500;">{{ $assetRequest->approver->full_name }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->approved_at->format('M d, Y \a\t g:i A') }}</div>
                </div>
                @if($assetRequest->approval_notes)
                <div style="background: #e8f5e8; padding: 10px; border-radius: 6px; margin-block-start: 10px;">
                    <div style="color: #2e7d32; font-size: 14px;">{{ $assetRequest->approval_notes }}</div>
                </div>
                @endif
            </div>
            @elseif($assetRequest->status === 'rejected')
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">❌ Rejection Details</h4>
                <div style="margin-block-end: 10px;">
                    <div style="font-weight: 500;">{{ $assetRequest->approver->full_name ?? 'System' }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->approved_at ? $assetRequest->approved_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</div>
                </div>
                @if($assetRequest->rejection_reason)
                <div style="background: #ffebee; padding: 10px; border-radius: 6px; margin-block-start: 10px;">
                    <div style="color: #f44336; font-size: 14px;">{{ $assetRequest->rejection_reason }}</div>
                </div>
                @endif
            </div>
            @endif

            <!-- Fulfillment Info -->
            @if($assetRequest->status === 'fulfilled' && $assetRequest->fulfiller)
            <div class="ui-card p-6">
                <h4 style="margin-block-end: 15px; color: #333;">📦 Fulfillment Details</h4>
                <div style="margin-block-end: 10px;">
                    <div style="font-weight: 500;">{{ $assetRequest->fulfiller->full_name }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->fulfilled_at->format('M d, Y \a\t g:i A') }}</div>
                </div>
                @if($assetRequest->fulfillment_notes)
                <div style="background: #e3f2fd; padding: 10px; border-radius: 6px; margin-block-start: 10px;">
                    <div style="color: #1976d2; font-size: 14px;">{{ $assetRequest->fulfillment_notes }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@endsection