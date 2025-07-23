{{-- File: resources/views/asset-approvals/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">⚖️ Asset Request Approvals</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Review and approve employee asset requests</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('assets.index') }}" class="btn">📦 Manage Assets</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⏳</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['pending_requests'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Pending Requests</div>
                </div>
            </div>
        </div>

        <div class="metric-card alert" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">🚨</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['pending_high_priority'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">High Priority</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">💰</div>
                <div>
                    <div style="font-size: 24px; font-weight: bold;">${{ number_format($stats['total_pending_value'], 0) }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Pending Value</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">📈</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['requests_this_month'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">This Month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <select name="status" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
            </select>
            
            <select name="priority" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Priority</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
            </select>
            
            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                   style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
            
            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                   style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
            
            <button type="submit" class="btn">Filter</button>
            
            @if(request()->hasAny(['status', 'priority', 'date_from', 'date_to']))
            <a href="{{ route('asset-approvals.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Bulk Actions -->
    @if($requests->where('status', 'pending')->count() > 0)
    <div class="content-card" style="margin-bottom: 20px; background: #fff3e0; border-left: 4px solid #ff9800;">
        <form action="{{ route('asset-approvals.bulk-action') }}" method="POST" style="display: flex; gap: 15px; align-items: center;">
            @csrf
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="select-all" style="transform: scale(1.2);">
                <label for="select-all" style="font-weight: 500;">Select All Pending</label>
            </div>
            
            <select name="action" required style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">Choose Action</option>
                <option value="approve">Bulk Approve</option>
                <option value="reject">Bulk Reject</option>
            </select>
            
            <input type="text" name="bulk_notes" placeholder="Optional notes..."
                   style="flex: 1; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
            
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    </div>
    @endif

    <!-- Requests List -->
    @forelse($requests as $request)
    <div class="content-card request-card" style="margin-bottom: 15px; {{ $request->priority === 'urgent' ? 'border-left: 4px solid #f44336;' : ($request->priority === 'high' ? 'border-left: 4px solid #ff9800;' : '') }}">
        <div style="display: flex; align-items: start; gap: 20px;">
            <!-- Checkbox for bulk actions -->
            @if($request->status === 'pending')
            <input type="checkbox" name="request_ids[]" value="{{ $request->id }}" class="request-checkbox" style="margin-top: 5px; transform: scale(1.2);">
            @endif

            <!-- Request Details -->
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                    <div>
                        <h4 style="margin: 0; color: #333;">{{ $request->request_number }}</h4>
                        <div style="color: #666; font-size: 14px; margin-top: 2px;">
                            By {{ $request->employee->full_name }} • {{ $request->created_at->format('M d, Y \a\t g:i A') }}
                            @if($request->employee->department)
                            • {{ $request->employee->department->name }}
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <span class="status-badge status-{{ $request->status }}">
                            {{ ucfirst($request->status) }}
                        </span>
                        <span class="priority-badge priority-{{ $request->priority }}">
                            {{ ucfirst($request->priority) }}
                        </span>
                    </div>
                </div>

                <!-- Business Justification -->
                <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                    <div style="font-weight: 500; margin-bottom: 5px; font-size: 14px; color: #333;">Business Justification:</div>
                    <div style="color: #666; font-size: 14px;">{{ Str::limit($request->business_justification, 200) }}</div>
                </div>

                <!-- Items Summary -->
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                    @foreach($request->items->take(4) as $item)
                    <span style="background: white; border: 1px solid #ddd; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                        {{ $item->quantity_requested }}× {{ Str::limit($item->asset->name, 20) }}
                    </span>
                    @endforeach
                    @if($request->items->count() > 4)
                    <span style="color: #666; font-size: 12px; padding: 4px 8px;">
                        +{{ $request->items->count() - 4 }} more items
                    </span>
                    @endif
                </div>

                <!-- Key Info -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; font-size: 14px; color: #666;">
                    <div><strong>Items:</strong> {{ $request->items->sum('quantity_requested') }}</div>
                    <div><strong>Total Cost:</strong> ${{ number_format($request->total_estimated_cost, 2) }}</div>
                    <div><strong>Needed By:</strong> {{ $request->needed_by_date ? $request->needed_by_date->format('M d') : 'ASAP' }}</div>
                    @if($request->needed_by_date && $request->needed_by_date->isPast())
                    <div style="color: #f44336;"><strong>⚠️ Overdue</strong></div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; flex-direction: column; gap: 8px; min-width: 140px;">
                <a href="{{ route('asset-approvals.show', $request) }}" class="btn btn-small" style="text-align: center;">
                    Review Details
                </a>
                
                @if($request->status === 'pending')
                <div style="display: flex; gap: 5px;">
                    <button onclick="showQuickApprove({{ $request->id }})" class="btn btn-small" style="flex: 1; background: #4caf50; color: white; border-color: #4caf50;">
                        ✅
                    </button>
                    <button onclick="showQuickReject({{ $request->id }})" class="btn btn-small" style="flex: 1; background: #f44336; color: white; border-color: #f44336;">
                        ❌
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="content-card" style="text-align: center; padding: 60px;">
        <div style="font-size: 64px; margin-bottom: 20px;">⚖️</div>
        <h3>No requests to review</h3>
        <p style="color: #666;">All requests have been processed or no requests match your filters.</p>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($requests->hasPages())
    <div style="margin-top: 20px;">
        {{ $requests->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Quick Approve Modal -->
<div id="quickApproveModal" class="modal">
    <div class="modal-content">
        <h3 style="margin: 0 0 20px 0;">✅ Quick Approve Request</h3>
        <form id="quickApproveForm" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Approval Notes (Optional)</label>
                <textarea name="approval_notes" rows="3" placeholder="Any notes for the requester..."
                          style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal()" class="btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Approve Request</button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Reject Modal -->
<div id="quickRejectModal" class="modal">
    <div class="modal-content">
        <h3 style="margin: 0 0 20px 0; color: #f44336;">❌ Reject Request</h3>
        <form id="quickRejectForm" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #f44336;">Rejection Reason *</label>
                <textarea name="rejection_reason" rows="4" required placeholder="Please explain why this request is being rejected..."
                          style="width: 100%; padding: 8px; border: 2px solid #f44336; border-radius: 4px;"></textarea>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">This reason will be visible to the employee</div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal()" class="btn">Cancel</button>
                <button type="submit" class="btn" style="background: #f44336; color: white;">Reject Request</button>
            </div>
        </form>
    </div>
</div>

<style>
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.request-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: box-shadow 0.2s ease;
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
}

.btn-small {
    padding: 6px 12px;
    font-size: 14px;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
</style>

<script>
// Bulk selection
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.request-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Quick approve/reject
function showQuickApprove(requestId) {
    document.getElementById('quickApproveForm').action = `/asset-approvals/${requestId}/approve`;
    document.getElementById('quickApproveModal').style.display = 'block';
}

function showQuickReject(requestId) {
    document.getElementById('quickRejectForm').action = `/asset-approvals/${requestId}/reject`;
    document.getElementById('quickRejectModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('quickApproveModal').style.display = 'none';
    document.getElementById('quickRejectModal').style.display = 'none';
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        closeModal();
    }
});

// Update bulk action form with selected requests
document.querySelector('form[action*="bulk-action"]').addEventListener('submit', function(e) {
    const selectedRequests = document.querySelectorAll('.request-checkbox:checked');
    
    if (selectedRequests.length === 0) {
        e.preventDefault();
        alert('Please select at least one request.');
        return;
    }
    
    // Clear existing hidden inputs
    this.querySelectorAll('input[name="request_ids[]"]').forEach(input => input.remove());
    
    // Add selected request IDs as hidden inputs
    selectedRequests.forEach(checkbox => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'request_ids[]';
        hiddenInput.value = checkbox.value;
        this.appendChild(hiddenInput);
    });
});
</script>
@endsection