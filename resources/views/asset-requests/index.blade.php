@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 32px; font-weight: 700;">📋 My Asset Requests</h2>
            <p style="color: #666; margin: 8px 0 0 0; font-size: 16px;">Track and manage your asset requests</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('asset-requests.cart') }}" class="btn btn-outline">
                <span style="font-size: 18px;">🛒</span>
                <span>View Cart</span>
            </a>
            <a href="{{ route('asset-requests.catalog') }}" class="btn btn-primary">
                <span style="font-size: 18px;">➕</span>
                <span>New Request</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-block-end: 30px;">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <div class="stat-number">{{ $requests->total() }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
                <div class="stat-number">{{ $requests->where('status', 'pending')->count() }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <div class="stat-number">{{ $requests->where('status', 'approved')->count() }}</div>
                <div class="stat-label">Approved</div>
            </div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-number">{{ $requests->where('status', 'fulfilled')->count() }}</div>
                <div class="stat-label">Fulfilled</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <div class="filter-group">
                <label style="font-size: 14px; color: #666; margin-block-end: 5px; display: block;">Status</label>
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                    <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>📦 Fulfilled</option>
                </select>
            </div>
            
            <div class="filter-actions" style="margin-block-start: 20px;">
                <button type="submit" class="btn btn-filter">Apply Filters</button>
                @if(request('status'))
                <a href="{{ route('asset-requests.index') }}" class="btn btn-clear">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Requests List -->
    <div class="requests-container">
        @forelse($requests as $request)
        <div class="request-card" data-status="{{ $request->status }}">
            <!-- Card Header -->
            <div class="request-header">
                <div class="request-title-section">
                    <h4 class="request-title">{{ $request->request_number }}</h4>
                    <div class="request-meta">
                        <span class="meta-item">
                            <span style="color: #666;">📅</span>
                            {{ $request->created_at->format('M d, Y') }}
                        </span>
                        <span class="meta-item">
                            <span style="color: #666;">⏰</span>
                            {{ $request->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
                
                <div class="request-badges">
                    <span class="status-badge status-{{ $request->status }}">
                        @switch($request->status)
                            @case('pending')
                                <span>⏳</span> Pending
                                @break
                            @case('approved')
                                <span>✅</span> Approved
                                @break
                            @case('rejected')
                                <span>❌</span> Rejected
                                @break
                            @case('fulfilled')
                                <span>📦</span> Fulfilled
                                @break
                            @default
                                {{ ucfirst($request->status) }}
                        @endswitch
                    </span>
                    <span class="priority-badge priority-{{ $request->priority }}">
                        @switch($request->priority)
                            @case('urgent')
                                <span>🚨</span> Urgent
                                @break
                            @case('high')
                                <span>🔴</span> High
                                @break
                            @case('normal')
                                <span>🟡</span> Normal
                                @break
                            @case('low')
                                <span>🟢</span> Low
                                @break
                            @default
                                {{ ucfirst($request->priority) }}
                        @endswitch
                    </span>
                </div>
            </div>

            <!-- Items Preview -->
            <div class="items-preview">
                <div class="preview-header">
                    <span style="font-weight: 600; color: #333;">📦 Requested Items</span>
                    <span class="item-count">{{ $request->items->count() }} item{{ $request->items->count() > 1 ? 's' : '' }}</span>
                </div>
                <div class="items-grid">
                    @foreach($request->items->take(4) as $item)
                    <div class="item-chip">
                        <span class="item-quantity">{{ $item->quantity_requested }}×</span>
                        <span class="item-name">{{ Str::limit($item->asset->name, 15) }}</span>
                    </div>
                    @endforeach
                    @if($request->items->count() > 4)
                    <div class="item-chip more-items">
                        +{{ $request->items->count() - 4 }} more
                    </div>
                    @endif
                </div>
            </div>

            <!-- Request Summary -->
            <div class="request-summary">
                <div class="summary-item">
                    <span class="summary-icon">📊</span>
                    <div>
                        <div class="summary-label">Total Items</div>
                        <div class="summary-value">{{ $request->total_items }}</div>
                    </div>
                </div>
                <div class="summary-item">
                    <span class="summary-icon">💰</span>
                    <div>
                        <div class="summary-label">Est. Cost</div>
                        <div class="summary-value">${{ number_format($request->total_estimated_cost, 2) }}</div>
                    </div>
                </div>
                <div class="summary-item">
                    <span class="summary-icon">⏰</span>
                    <div>
                        <div class="summary-label">Needed By</div>
                        <div class="summary-value">{{ $request->needed_by_date ? $request->needed_by_date->format('M d') : 'ASAP' }}</div>
                    </div>
                </div>
            </div>

            <!-- Status Messages -->
            @if($request->status === 'approved' && $request->approver)
            <div class="status-message success">
                <div class="status-header">
                    <span style="font-size: 18px;">✅</span>
                    <span>Approved by {{ $request->approver->full_name }}</span>
                </div>
                <div class="status-time">{{ $request->approved_at->format('M d, Y \a\t g:i A') }}</div>
                @if($request->approval_notes)
                <div class="status-notes">{{ $request->approval_notes }}</div>
                @endif
            </div>
            @elseif($request->status === 'rejected')
            <div class="status-message error">
                <div class="status-header">
                    <span style="font-size: 18px;">❌</span>
                    <span>Request Rejected</span>
                </div>
                @if($request->rejection_reason)
                <div class="status-notes">{{ $request->rejection_reason }}</div>
                @endif
            </div>
            @elseif($request->status === 'fulfilled')
            <div class="status-message info">
                <div class="status-header">
                    <span style="font-size: 18px;">📦</span>
                    <span>Items Delivered</span>
                </div>
                <div class="status-time">{{ $request->fulfilled_at->format('M d, Y \a\t g:i A') }}</div>
                @if($request->fulfillment_notes)
                <div class="status-notes">{{ $request->fulfillment_notes }}</div>
                @endif
            </div>
            @endif

            <!-- Actions -->
            <div class="request-actions">
                <a href="{{ route('asset-requests.show', $request) }}" class="action-btn primary">
                    <span>👁️</span>
                    View Details
                </a>
                
                @if(in_array($request->status, ['pending', 'draft']))
                <form action="{{ route('asset-requests.cancel', $request) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to cancel this request?')" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="action-btn danger">
                        <span>❌</span>
                        Cancel
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <h3 class="empty-title">No Asset Requests Yet</h3>
            <p class="empty-description">You haven't submitted any asset requests. Get started by browsing our asset catalog.</p>
            <a href="{{ route('asset-requests.catalog') }}" class="btn btn-primary empty-action">
                <span>🛒</span>
                Browse Asset Catalog
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($requests->hasPages())
    <div class="pagination-wrapper">
        {{ $requests->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
/* Stat Cards */
.stat-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.stat-icon {
    font-size: 32px;
    opacity: 0.9;
}

.stat-number {
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    margin-block-end: 4px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
    font-weight: 500;
}

/* Filter Card */
.filter-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-block-end: 25px;
    border: 1px solid #f0f0f0;
}

.filter-select {
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    font-size: 14px;
    min-width: 180px;
    transition: border-color 0.2s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #2196f3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

/* Request Cards */
.requests-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.request-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.request-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: var(--status-color);
}

.request-card[data-status="pending"]::before { background: #ff9800; }
.request-card[data-status="approved"]::before { background: #4caf50; }
.request-card[data-status="rejected"]::before { background: #f44336; }
.request-card[data-status="fulfilled"]::before { background: #2196f3; }

.request-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.request-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-block-end: 20px;
}

.request-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin: 0 0 8px 0;
}

.request-meta {
    display: flex;
    gap: 15px;
    font-size: 14px;
    color: #666;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.request-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.status-badge, .priority-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.status-pending { background: #fff3e0; color: #f57c00; }
.status-approved { background: #e8f5e8; color: #2e7d32; }
.status-rejected { background: #ffebee; color: #d32f2f; }
.status-fulfilled { background: #e3f2fd; color: #1976d2; }

.priority-urgent { background: #ffebee; color: #d32f2f; }
.priority-high { background: #fff3e0; color: #f57c00; }
.priority-normal { background: #e3f2fd; color: #1976d2; }
.priority-low { background: #e8f5e8; color: #2e7d32; }

/* Items Preview */
.items-preview {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-block-end: 20px;
}

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-block-end: 15px;
}

.item-count {
    background: #2196f3;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.items-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.item-chip {
    background: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 13px;
    border: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.item-quantity {
    font-weight: 700;
    color: #2196f3;
}

.more-items {
    background: #e0e0e0;
    color: #666;
    font-weight: 600;
}

/* Request Summary */
.request-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-block-end: 20px;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.summary-icon {
    font-size: 20px;
    background: #f0f0f0;
    padding: 8px;
    border-radius: 8px;
}

.summary-label {
    font-size: 12px;
    color: #666;
    margin-block-end: 2px;
}

.summary-value {
    font-weight: 700;
    color: #333;
    font-size: 16px;
}

/* Status Messages */
.status-message {
    padding: 15px;
    border-radius: 10px;
    margin-block-end: 20px;
}

.status-message.success {
    background: #e8f5e8;
    border: 1px solid #4caf50;
}

.status-message.error {
    background: #ffebee;
    border: 1px solid #f44336;
}

.status-message.info {
    background: #e3f2fd;
    border: 1px solid #2196f3;
}

.status-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    margin-block-end: 5px;
}

.status-time {
    font-size: 12px;
    color: #666;
    margin-block-end: 8px;
}

.status-notes {
    font-size: 14px;
    color: #555;
    font-style: italic;
}

/* Actions */
.request-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.action-btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.action-btn.primary {
    background: #2196f3;
    color: white;
}

.action-btn.primary:hover {
    background: #1976d2;
    transform: translateY(-1px);
}

.action-btn.danger {
    background: #f44336;
    color: white;
}

.action-btn.danger:hover {
    background: #d32f2f;
    transform: translateY(-1px);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.empty-icon {
    font-size: 80px;
    margin-block-end: 20px;
    opacity: 0.5;
}

.empty-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-block-end: 10px;
}

.empty-description {
    color: #666;
    font-size: 16px;
    margin-block-end: 30px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.empty-action {
    padding: 12px 24px;
    font-size: 16px;
}

/* Buttons */
.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 2px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background: #2196f3;
    color: white;
}

.btn-primary:hover {
    background: #1976d2;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
}

.btn-outline {
    background: white;
    color: #333;
    border-color: #e0e0e0;
}

.btn-outline:hover {
    border-color: #2196f3;
    color: #2196f3;
    transform: translateY(-1px);
}

.btn-filter {
    background: #4caf50;
    color: white;
}

.btn-filter:hover {
    background: #388e3c;
}

.btn-clear {
    background: #f44336;
    color: white;
}

.btn-clear:hover {
    background: #d32f2f;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .request-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .request-badges {
        justify-content: flex-start;
    }
    
    .request-summary {
        grid-template-columns: 1fr;
    }
    
    .request-actions {
        justify-content: stretch;
    }
    
    .action-btn {
        flex: 1;
        justify-content: center;
    }
}
</style>
@endsection