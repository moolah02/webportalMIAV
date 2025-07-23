{{-- File: resources/views/asset-approvals/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">Review Request {{ $assetRequest->request_number }}</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Submitted by {{ $assetRequest->employee->full_name }}</p>
        </div>
        <a href="{{ route('asset-approvals.index') }}" class="btn">← Back to Approvals</a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Content -->
        <div>
            <!-- Request Overview -->
            <div class="content-card" style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h4 style="margin: 0; color: #333;">📊 Request Overview</h4>
                    <div style="display: flex; gap: 10px;">
                        <span class="priority-badge priority-{{ $assetRequest->priority }}">
                            {{ ucfirst($assetRequest->priority) }} Priority
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Submitted</label>
                        <div style="font-weight: 500;">{{ $assetRequest->created_at->format('M d, Y \a\t g:i A') }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Needed By</label>
                        <div style="font-weight: 500; {{ $assetRequest->needed_by_date && $assetRequest->needed_by_date->isPast() ? 'color: #f44336;' : '' }}">
                            {{ $assetRequest->needed_by_date ? $assetRequest->needed_by_date->format('M d, Y') : 'ASAP' }}
                            @if($assetRequest->needed_by_date && $assetRequest->needed_by_date->isPast())
                                <span style="font-size: 12px;">⚠️ Overdue</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Total Items</label>
                        <div style="font-weight: 500;">{{ $assetRequest->items->sum('quantity_requested') }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Estimated Cost</label>
                        <div style="font-weight: 500; color: #2196f3;">${{ number_format($assetRequest->total_estimated_cost, 2) }}</div>
                    </div>
                </div>

                <!-- Business Justification -->
                <div>
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px; display: block;">Business Justification</label>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #2196f3;">
                        {{ $assetRequest->business_justification }}
                    </div>
                </div>

                @if($assetRequest->delivery_instructions)
                <div style="margin-top: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase;">Delivery Instructions</label>
                    <div style="color: #666; margin-top: 5px;">{{ $assetRequest->delivery_instructions }}</div>
                </div>
                @endif
            </div>

            <!-- Items for Approval -->
            @if($assetRequest->status === 'pending')
            <div class="content-card" style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 20px; color: #333;">📦 Items for Approval</h4>
                
                <form action="{{ route('asset-approvals.approve', $assetRequest) }}" method="POST" id="approvalForm">
                    @csrf
                    
                    <div style="overflow-x: auto; margin-bottom: 20px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #eee;">Asset</th>
                                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Requested</th>
                                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Available</th>
                                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Approve Qty</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">Unit Price</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assetRequest->items as $item)
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                        <div style="font-weight: 500;">{{ $item->asset->name }}</div>
                                        <div style="font-size: 12px; color: #666;">{{ $item->asset->brand }} {{ $item->asset->model }}</div>
                                        <div style="font-size: 12px; color: #666;">SKU: {{ $item->asset->sku }}</div>
                                    </td>
                                    <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                        <span style="font-weight: 500; font-size: 16px;">{{ $item->quantity_requested }}</span>
                                    </td>
                                    <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                        <span style="color: {{ $item->asset->stock_quantity >= $item->quantity_requested ? '#4caf50' : '#f44336' }};">
                                            {{ $item->asset->stock_quantity }}
                                        </span>
                                        @if($item->asset->stock_quantity < $item->quantity_requested)
                                        <div style="font-size: 11px; color: #f44336;">Insufficient Stock</div>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                        <input type="number" name="item_approvals[{{ $item->id }}][quantity_approved]" 
                                               value="{{ min($item->quantity_requested, $item->asset->stock_quantity) }}" 
                                               min="0" max="{{ min($item->quantity_requested, $item->asset->stock_quantity) }}"
                                               style="width: 80px; padding: 5px; border: 2px solid #ddd; border-radius: 4px; text-align: center;"
                                               onchange="updateTotal({{ $item->id }}, {{ $item->unit_price_at_request }})">
                                    </td>
                                    <td style="padding: 12px; text-align: right; border-bottom: 1px solid #eee;">
                                        ${{ number_format($item->unit_price_at_request, 2) }}
                                    </td>
                                    <td style="padding: 12px; text-align: right; border-bottom: 1px solid #eee;">
                                        <span id="total-{{ $item->id }}" style="font-weight: 500;">
                                            ${{ number_format($item->quantity_requested * $item->unit_price_at_request, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Approval Notes (Optional)</label>
                        <textarea name="approval_notes" rows="3" placeholder="Any notes or conditions for this approval..."
                                  style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"></textarea>
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="approveAll()" class="btn btn-success" style="background: #4caf50; color: white; border-color: #4caf50;">
                            ✅ Approve All Items
                        </button>
                        <button type="submit" class="btn btn-primary">
                            ✅ Approve Selected
                        </button>
                    </div>
                </form>

                <!-- Reject Form -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
                    <h5 style="color: #f44336; margin-bottom: 15px;">❌ Reject This Request</h5>
                    <form action="{{ route('asset-approvals.reject', $assetRequest) }}" method="POST">
                        @csrf
                        <div style="background: #ffebee; padding: 15px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f44336;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #f44336;">Rejection Reason *</label>
                            <textarea name="rejection_reason" rows="4" required placeholder="Please provide a detailed explanation for rejecting this request. This will be visible to the employee."
                                      style="width: 100%; padding: 8px; border: 2px solid #f44336; border-radius: 4px;"></textarea>
                            <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                💡 Be specific about why the request cannot be approved (budget constraints, policy violations, etc.)
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn" style="background: #f44336; color: white; border-color: #f44336;" 
                                    onclick="return confirm('Are you sure you want to reject this entire request? This action cannot be undone.')">
                                ❌ Reject Entire Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <!-- Read-only items view for processed requests -->
            <div class="content-card">
                <h4 style="margin-bottom: 20px; color: #333;">📦 Request Items</h4>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #eee;">Asset</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Requested</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Approved</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Fulfilled</th>
                                <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">Unit Price</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetRequest->items as $item)
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                    <div style="font-weight: 500;">{{ $item->asset->name }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ $item->asset->brand }} {{ $item->asset->model }}</div>
                                </td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                    {{ $item->quantity_requested }}
                                </td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                    <span style="font-weight: 500; color: {{ $item->quantity_approved > 0 ? '#4caf50' : '#f44336' }};">
                                        {{ $item->quantity_approved ?: '0' }}
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                    {{ $item->quantity_fulfilled ?: '0' }}
                                </td>
                                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #eee;">
                                    ${{ number_format($item->unit_price_at_request, 2) }}
                                </td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                    <span class="status-badge status-{{ $item->item_status }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->item_status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Requester Info -->
            <div class="content-card" style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px; color: #333;">👤 Requester Details</h4>
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; margin: 0 auto 10px;">
                        {{ substr($assetRequest->employee->full_name, 0, 1) }}
                    </div>
                    <div style="font-weight: 500; font-size: 16px;">{{ $assetRequest->employee->full_name }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->employee->role->name ?? 'Employee' }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->employee->department->name ?? 'No Department' }}</div>
                </div>
                @if($assetRequest->employee->email)
                <div style="font-size: 14px; margin-bottom: 5px; text-align: center;">
                    📧 <a href="mailto:{{ $assetRequest->employee->email }}" style="color: #2196f3;">{{ $assetRequest->employee->email }}</a>
                </div>
                @endif
                <div style="font-size: 14px; color: #666; text-align: center;">
                    Employee since {{ $assetRequest->employee->created_at->format('M Y') }}
                </div>
            </div>

            <!-- Request History -->
            <div class="content-card" style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px; color: #333;">📈 Employee's Request History</h4>
                @php
                    $employeeStats = [
                        'total_requests' => $assetRequest->employee->assetRequests()->count(),
                        'approved_requests' => $assetRequest->employee->assetRequests()->where('status', 'approved')->count(),
                        'rejected_requests' => $assetRequest->employee->assetRequests()->where('status', 'rejected')->count(),
                        'recent_requests' => $assetRequest->employee->assetRequests()->latest()->limit(3)->get()
                    ];
                    $approvalRate = $employeeStats['total_requests'] > 0 ? 
                        round(($employeeStats['approved_requests'] / $employeeStats['total_requests']) * 100) : 0;
                @endphp
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    <div style="text-align: center; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                        <div style="font-size: 18px; font-weight: bold; color: #2196f3;">{{ $employeeStats['total_requests'] }}</div>
                        <div style="font-size: 12px; color: #666;">Total Requests</div>
                    </div>
                    <div style="text-align: center; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                        <div style="font-size: 18px; font-weight: bold; color: #4caf50;">{{ $approvalRate }}%</div>
                        <div style="font-size: 12px; color: #666;">Approval Rate</div>
                    </div>
                </div>

                @if($employeeStats['recent_requests']->count() > 1)
                <div style="font-size: 14px; color: #666; margin-bottom: 10px;">Recent Requests:</div>
                @foreach($employeeStats['recent_requests']->where('id', '!=', $assetRequest->id)->take(2) as $recent)
                <div style="background: #f8f9fa; padding: 8px; border-radius: 4px; margin-bottom: 5px; font-size: 12px;">
                    <div style="display: flex; justify-content: between; align-items: center;">
                        <div style="font-weight: 500;">{{ $recent->request_number }}</div>
                        <span class="status-badge status-{{ $recent->status }}" style="font-size: 10px; padding: 2px 6px;">
                            {{ ucfirst($recent->status) }}
                        </span>
                    </div>
                    <div style="color: #666; margin-top: 2px;">{{ $recent->created_at->format('M d, Y') }}</div>
                </div>
                @endforeach
                @endif
            </div>

            <!-- Decision History -->
            @if($assetRequest->status !== 'pending')
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">
                    @if($assetRequest->status === 'approved' || $assetRequest->status === 'fulfilled')
                        ✅ Approval Details
                    @else
                        ❌ Rejection Details
                    @endif
                </h4>
                <div style="margin-bottom: 10px;">
                    <div style="font-weight: 500;">{{ $assetRequest->approver->full_name ?? 'System' }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->approved_at ? $assetRequest->approved_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</div>
                </div>
                @if($assetRequest->approval_notes || $assetRequest->rejection_reason)
                <div style="background: {{ $assetRequest->status === 'rejected' ? '#ffebee' : '#e8f5e8' }}; padding: 10px; border-radius: 6px; margin-top: 10px;">
                    <div style="color: {{ $assetRequest->status === 'rejected' ? '#f44336' : '#2e7d32' }}; font-size: 14px;">
                        {{ $assetRequest->approval_notes ?? $assetRequest->rejection_reason }}
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.status-pending { background: #fff3e0; color: #f57c00; }
.status-approved { background: #e8f5e8; color: #2e7d32; }
.status-rejected { background: #ffebee; color: #d32f2f; }
.status-fulfilled { background: #e3f2fd; color: #1976d2; }
.status-partially_approved { background: #fff3e0; color: #f57c00; }

.priority-badge {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 500;
}

.priority-low { background: #e8f5e8; color: #2e7d32; }
.priority-normal { background: #e3f2fd; color: #1976d2; }
.priority-high { background: #fff3e0; color: #f57c00; }
.priority-urgent { background: #ffebee; color: #d32f2f; }

.btn {
    padding: 8px 16px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
}

.btn-primary {
    background: #2196f3;
    color: white;
    border-color: #2196f3;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}
</style>

<script>
function updateTotal(itemId, unitPrice) {
    const quantityInput = document.querySelector(`input[name="item_approvals[${itemId}][quantity_approved]"]`);
    const totalSpan = document.getElementById(`total-${itemId}`);
    const quantity = parseInt(quantityInput.value) || 0;
    const total = quantity * unitPrice;
    totalSpan.textContent = `${total.toFixed(2)}`;
}

function approveAll() {
    // Set all quantities to their maximum available amounts
    document.querySelectorAll('input[name*="quantity_approved"]').forEach(input => {
        const max = parseInt(input.getAttribute('max'));
        input.value = max;
        
        // Update the total display
        const itemId = input.name.match(/\[(\d+)\]/)[1];
        const row = input.closest('tr');
        const unitPriceText = row.cells[4].textContent.replace('status-badge status-{{ $assetRequest->status }}">
                            {{ ucfirst($assetRequest->status) }}
                        </span>
                        <span class=", '').replace(',', '');
        const unitPrice = parseFloat(unitPriceText);
        updateTotal(itemId, unitPrice);
    });
}

// Initialize totals on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name*="quantity_approved"]').forEach(input => {
        const itemId = input.name.match(/\[(\d+)\]/)[1];
        const row = input.closest('tr');
        const unitPriceText = row.cells[4].textContent.replace('status-badge status-{{ $assetRequest->status }}">
                            {{ ucfirst($assetRequest->status) }}
                        </span>
                        <span class=", '').replace(',', '');
        const unitPrice = parseFloat(unitPriceText);
        updateTotal(itemId, unitPrice);
    });
});
</script>
@endsectionstatus-badge status-{{ $assetRequest->status }}">
                            <span class="status-badge status-{{ $assetRequest->status }}">
                            {{ ucfirst($assetRequest->status) }}
                        </span>
                        <span class="priority-badge priority-{{ $assetRequest->priority }}">
                            {{ ucfirst($assetRequest->priority) }} Priority
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Submitted</label>
                        <div style="font-weight: 500;">{{ $assetRequest->created_at->format('M d, Y \a\t g:i A') }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Needed By</label>
                        <div style="font-weight: 500; {{ $assetRequest->needed_by_date && $assetRequest->needed_by_date->isPast() ? 'color: #f44336;' : '' }}">
                            {{ $assetRequest->needed_by_date ? $assetRequest->needed_by_date->format('M d, Y') : 'ASAP' }}
                            @if($assetRequest->needed_by_date && $assetRequest->needed_by_date->isPast())
                                <span style="font-size: 12px;">⚠️ Overdue</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Total Items</label>
                        <div style="font-weight: 500;">{{ $assetRequest->items->sum('quantity_requested') }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Estimated Cost</label>
                        <div style="font-weight: 500; color: #2196f3;">${{ number_format($assetRequest->total_estimated_cost, 2) }}</div>
                    </div>
                </div>

                <!-- Business Justification -->
                <div>
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px; display: block;">Business Justification</label>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #2196f3;">
                        {{ $assetRequest->business_justification }}
                    </div>
                </div>

                @if($assetRequest->delivery_instructions)
                <div style="margin-top: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase;">Delivery Instructions</label>
                    <div style="color: #666; margin-top: 5px;">{{ $assetRequest->delivery_instructions }}</div>
                </div>
                @endif
            </div>

            <!-- Items for Approval -->
            @if($assetRequest->status === 'pending')
            <div class="content-card" style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 20px; color: #333;">📦 Items for Approval</h4>
                
                <form action="{{ route('asset-approvals.approve', $assetRequest) }}" method="POST" id="approvalForm">
                    @csrf
                    
                    <div style="overflow-x: auto; margin-bottom: 20px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #eee;">Asset</th>
                                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Requested</th>
                                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Available</th>
                                    <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Approve Qty</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">Unit Price</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assetRequest->items as $item)
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                        <div style="font-weight: 500;">{{ $item->asset->name }}</div>
                                        <div style="font-size: 12px; color: #666;">{{ $item->asset->brand }} {{ $item->asset->model }}</div>
                                        <div style="font-size: 12px; color: #666;">SKU: {{ $item->asset->sku }}</div>
                                    </td>
                                    <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                        <span style="font-weight: 500; font-size: 16px;">{{ $item->quantity_requested }}</span>
                                    </td>
                                    <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                        <span style="color: {{ $item->asset->stock_quantity >= $item->quantity_requested ? '#4caf50' : '#f44336' }};">
                                            {{ $item->asset->stock_quantity }}
                                        </span>
                                        @if($item->asset->stock_quantity < $item->quantity_requested)
                                        <div style="font-size: 11px; color: #f44336;">Insufficient Stock</div>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                        <input type="number" name="item_approvals[{{ $item->id }}][quantity_approved]" 
                                               value="{{ min($item->quantity_requested, $item->asset->stock_quantity) }}" 
                                               min="0" max="{{ min($item->quantity_requested, $item->asset->stock_quantity) }}"
                                               style="width: 80px; padding: 5px; border: 2px solid #ddd; border-radius: 4px; text-align: center;"
                                               onchange="updateTotal({{ $item->id }}, {{ $item->unit_price_at_request }})">
                                    </td>
                                    <td style="padding: 12px; text-align: right; border-bottom: 1px solid #eee;">
                                        ${{ number_format($item->unit_price_at_request, 2) }}
                                    </td>
                                    <td style="padding: 12px; text-align: right; border-bottom: 1px solid #eee;">
                                        <span id="total-{{ $item->id }}" style="font-weight: 500;">
                                            ${{ number_format($item->quantity_requested * $item->unit_price_at_request, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Approval Notes (Optional)</label>
                        <textarea name="approval_notes" rows="3" placeholder="Any notes or conditions for this approval..."
                                  style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"></textarea>
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="approveAll()" class="btn btn-success" style="background: #4caf50; color: white; border-color: #4caf50;">
                            ✅ Approve All Items
                        </button>
                        <button type="submit" class="btn btn-primary">
                            ✅ Approve Selected
                        </button>
                    </div>
                </form>

                <!-- Reject Form -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
                    <h5 style="color: #f44336; margin-bottom: 15px;">❌ Reject This Request</h5>
                    <form action="{{ route('asset-approvals.reject', $assetRequest) }}" method="POST">
                        @csrf
                        <div style="background: #ffebee; padding: 15px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f44336;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #f44336;">Rejection Reason *</label>
                            <textarea name="rejection_reason" rows="4" required placeholder="Please provide a detailed explanation for rejecting this request. This will be visible to the employee."
                                      style="width: 100%; padding: 8px; border: 2px solid #f44336; border-radius: 4px;"></textarea>
                            <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                💡 Be specific about why the request cannot be approved (budget constraints, policy violations, etc.)
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn" style="background: #f44336; color: white; border-color: #f44336;" 
                                    onclick="return confirm('Are you sure you want to reject this entire request? This action cannot be undone.')">
                                ❌ Reject Entire Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <!-- Read-only items view for processed requests -->
            <div class="content-card">
                <h4 style="margin-bottom: 20px; color: #333;">📦 Request Items</h4>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #eee;">Asset</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Requested</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Approved</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Fulfilled</th>
                                <th style="padding: 12px; text-align: right; border-bottom: 2px solid #eee;">Unit Price</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #eee;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetRequest->items as $item)
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                    <div style="font-weight: 500;">{{ $item->asset->name }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ $item->asset->brand }} {{ $item->asset->model }}</div>
                                </td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                    {{ $item->quantity_requested }}
                                </td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                    <span style="font-weight: 500; color: {{ $item->quantity_approved > 0 ? '#4caf50' : '#f44336' }};">
                                        {{ $item->quantity_approved ?: '0' }}
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                    {{ $item->quantity_fulfilled ?: '0' }}
                                </td>
                                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #eee;">
                                    ${{ number_format($item->unit_price_at_request, 2) }}
                                </td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                                    <span class="status-badge status-{{ $item->item_status }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->item_status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Requester Info -->
            <div class="content-card" style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px; color: #333;">👤 Requester Details</h4>
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; margin: 0 auto 10px;">
                        {{ substr($assetRequest->employee->full_name, 0, 1) }}
                    </div>
                    <div style="font-weight: 500; font-size: 16px;">{{ $assetRequest->employee->full_name }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->employee->role->name ?? 'Employee' }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->employee->department->name ?? 'No Department' }}</div>
                </div>
                @if($assetRequest->employee->email)
                <div style="font-size: 14px; margin-bottom: 5px; text-align: center;">
                    📧 <a href="mailto:{{ $assetRequest->employee->email }}" style="color: #2196f3;">{{ $assetRequest->employee->email }}</a>
                </div>
                @endif
                <div style="font-size: 14px; color: #666; text-align: center;">
                    Employee since {{ $assetRequest->employee->created_at->format('M Y') }}
                </div>
            </div>

            <!-- Request History -->
            <div class="content-card" style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px; color: #333;">📈 Employee's Request History</h4>
                @php
                    $employeeStats = [
                        'total_requests' => $assetRequest->employee->assetRequests()->count(),
                        'approved_requests' => $assetRequest->employee->assetRequests()->where('status', 'approved')->count(),
                        'rejected_requests' => $assetRequest->employee->assetRequests()->where('status', 'rejected')->count(),
                        'recent_requests' => $assetRequest->employee->assetRequests()->latest()->limit(3)->get()
                    ];
                    $approvalRate = $employeeStats['total_requests'] > 0 ? 
                        round(($employeeStats['approved_requests'] / $employeeStats['total_requests']) * 100) : 0;
                @endphp
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    <div style="text-align: center; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                        <div style="font-size: 18px; font-weight: bold; color: #2196f3;">{{ $employeeStats['total_requests'] }}</div>
                        <div style="font-size: 12px; color: #666;">Total Requests</div>
                    </div>
                    <div style="text-align: center; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                        <div style="font-size: 18px; font-weight: bold; color: #4caf50;">{{ $approvalRate }}%</div>
                        <div style="font-size: 12px; color: #666;">Approval Rate</div>
                    </div>
                </div>

                @if($employeeStats['recent_requests']->count() > 1)
                <div style="font-size: 14px; color: #666; margin-bottom: 10px;">Recent Requests:</div>
                @foreach($employeeStats['recent_requests']->where('id', '!=', $assetRequest->id)->take(2) as $recent)
                <div style="background: #f8f9fa; padding: 8px; border-radius: 4px; margin-bottom: 5px; font-size: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-weight: 500;">{{ $recent->request_number }}</div>
                        <span class="status-badge status-{{ $recent->status }}" style="font-size: 10px; padding: 2px 6px;">
                            {{ ucfirst($recent->status) }}
                        </span>
                    </div>
                    <div style="color: #666; margin-top: 2px;">{{ $recent->created_at->format('M d, Y') }}</div>
                </div>
                @endforeach
                @endif
            </div>

            <!-- Decision History -->
            @if($assetRequest->status !== 'pending')
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">
                    @if($assetRequest->status === 'approved' || $assetRequest->status === 'fulfilled')
                        ✅ Approval Details
                    @else
                        ❌ Rejection Details
                    @endif
                </h4>
                <div style="margin-bottom: 10px;">
                    <div style="font-weight: 500;">{{ $assetRequest->approver->full_name ?? 'System' }}</div>
                    <div style="font-size: 14px; color: #666;">{{ $assetRequest->approved_at ? $assetRequest->approved_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</div>
                </div>
                @if($assetRequest->approval_notes || $assetRequest->rejection_reason)
                <div style="background: {{ $assetRequest->status === 'rejected' ? '#ffebee' : '#e8f5e8' }}; padding: 10px; border-radius: 6px; margin-top: 10px;">
                    <div style="color: {{ $assetRequest->status === 'rejected' ? '#f44336' : '#2e7d32' }}; font-size: 14px;">
                        {{ $assetRequest->approval_notes ?? $assetRequest->rejection_reason }}
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.status-pending { background: #fff3e0; color: #f57c00; }
.status-approved { background: #e8f5e8; color: #2e7d32; }
.status-rejected { background: #ffebee; color: #d32f2f; }
.status-fulfilled { background: #e3f2fd; color: #1976d2; }
.status-partially_approved { background: #fff3e0; color: #f57c00; }

.priority-badge {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 500;
}

.priority-low { background: #e8f5e8; color: #2e7d32; }
.priority-normal { background: #e3f2fd; color: #1976d2; }
.priority-high { background: #fff3e0; color: #f57c00; }
.priority-urgent { background: #ffebee; color: #d32f2f; }

.btn {
    padding: 8px 16px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
}

.btn-primary {
    background: #2196f3;
    color: white;
    border-color: #2196f3;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}
</style>

<script>
function updateTotal(itemId, unitPrice) {
    const quantityInput = document.querySelector(`input[name="item_approvals[${itemId}][quantity_approved]"]`);
    const totalSpan = document.getElementById(`total-${itemId}`);
    const quantity = parseInt(quantityInput.value) || 0;
    const total = quantity * unitPrice;
    totalSpan.textContent = `$${total.toFixed(2)}`;
}

function approveAll() {
    // Set all quantities to their maximum available amounts
    document.querySelectorAll('input[name*="quantity_approved"]').forEach(input => {
        const max = parseInt(input.getAttribute('max'));
        input.value = max;
        
        // Update the total display
        const itemId = input.name.match(/\[(\d+)\]/)[1];
        const row = input.closest('tr');
        const unitPriceText = row.cells[4].textContent.replace('$', '').replace(',', '');
        const unitPrice = parseFloat(unitPriceText);
        updateTotal(itemId, unitPrice);
    });
}

// Initialize totals on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name*="quantity_approved"]').forEach(input => {
        const itemId = input.name.match(/\[(\d+)\]/)[1];
        const row = input.closest('tr');
        const unitPriceText = row.cells[4].textContent.replace('$', '').replace(',', '');
        const unitPrice = parseFloat(unitPriceText);
        updateTotal(itemId, unitPrice);
    });
});
</script>
@endsection