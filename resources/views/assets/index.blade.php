{{-- 
==============================================
ASSETS INDEX VIEW
File: resources/views/assets/index.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📦 Asset Management</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage company assets and inventory</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('assets.export') }}" class="btn" style="background: #4caf50; color: white; border-color: #4caf50;">
                📊 Export CSV
            </a>
            <a href="{{ route('assets.create') }}" class="btn btn-primary">+ Add New Asset</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">📦</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['total_assets'] ?? 0 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Assets</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">✅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['active_assets'] ?? 0 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Active Assets</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⚠️</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['low_stock'] ?? 0 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Low Stock Items</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">💰</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">${{ number_format($stats['total_value'] ?? 0, 0) }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-bottom: 20px;">
        <form method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto auto; gap: 15px; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Search Assets</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name, model, SKU..." 
                       style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Category</label>
                <select name="category" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Status</label>
                <select name="status" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Stock</label>
                <select name="stock_status" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                    <option value="">All Stock</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            
            @if(request()->hasAny(['search', 'category', 'status', 'stock_status']))
            <a href="{{ route('assets.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Assets Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px;">
        @forelse($assets as $asset)
        <div class="asset-card">
            <!-- Asset Header -->
            <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                        <h4 style="margin: 0; color: #333;">{{ $asset->name }}</h4>
                        <span class="status-badge status-{{ $asset->status }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </div>
                    <div style="font-size: 14px; color: #666;">
                        {{ $asset->category }}
                        @if($asset->brand || $asset->model)
                            • {{ $asset->brand }} {{ $asset->model }}
                        @endif
                    </div>
                    @if($asset->sku)
                        <div style="font-size: 12px; color: #999;">SKU: {{ $asset->sku }}</div>
                    @endif
                </div>
                
                <div style="text-align: right;">
                    <div style="font-size: 18px; font-weight: bold; color: #333;">
                        {{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}
                    </div>
                    @if($asset->is_requestable)
                        <span style="background: #e8f5e8; color: #2e7d32; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                            Requestable
                        </span>
                    @endif
                </div>
            </div>

            <!-- Stock Info -->
            <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 12px; color: #666; text-transform: uppercase;">Stock Status</span>
                    <span class="stock-badge stock-{{ $asset->stock_status }}">
                        @if($asset->stock_status == 'in_stock')
                            ✅ In Stock
                        @elseif($asset->stock_status == 'low_stock')
                            ⚠️ Low Stock
                        @else
                            ❌ Out of Stock
                        @endif
                    </span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 20px; font-weight: bold; color: #333;">{{ $asset->stock_quantity }}</span>
                        <span style="font-size: 14px; color: #666;">available</span>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; color: #666;">Min Level: {{ $asset->min_stock_level }}</div>
                        @if($asset->isLowStock())
                            <div style="font-size: 12px; color: #f57c00;">⚠️ Needs Restock</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($asset->description)
            <div style="margin-bottom: 15px;">
                <p style="font-size: 14px; color: #666; margin: 0; line-height: 1.4;">
                    {{ Str::limit($asset->description, 80) }}
                </p>
            </div>
            @endif

            <!-- Actions -->
            <div style="display: flex; gap: 8px;">
                <button onclick="assetQuickActions({{ $asset->id }}, '{{ $asset->name }}')" 
                        class="btn-small" style="flex: 1; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    ⚡ Quick Actions
                </button>
                
                @if($asset->canBeRequested())
                <button onclick="requestAsset({{ $asset->id }})" 
                        class="btn-small" style="background: #4caf50; color: white; border-color: #4caf50;">
                    🛒 Request
                </button>
                @endif
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
            <div style="font-size: 64px; margin-bottom: 20px;">📦</div>
            <h3>No assets found</h3>
            <p>Start by adding your first asset to the inventory.</p>
            <a href="{{ route('assets.create') }}" class="btn btn-primary" style="margin-top: 15px;">Add First Asset</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($assets, 'hasPages') && $assets->hasPages())
    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $assets->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Quick Actions Modal -->
<div id="assetQuickActionsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 400px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>📦</span>
                <span id="modalAssetName">Asset Actions</span>
            </h3>
            <button onclick="closeAssetActions()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <div style="display: grid; gap: 12px;">
                <button onclick="viewAsset()" class="modal-action-btn" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
                    <span style="font-size: 20px;">👁️</span>
                    <div>
                        <div style="font-weight: bold;">View Details</div>
                        <div style="font-size: 12px; opacity: 0.9;">See complete asset information</div>
                    </div>
                </button>
                
                <button onclick="editAsset()" class="modal-action-btn" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
                    <span style="font-size: 20px;">✏️</span>
                    <div>
                        <div style="font-weight: bold;">Edit Asset</div>
                        <div style="font-size: 12px; opacity: 0.9;">Update asset information</div>
                    </div>
                </button>
                
                <button onclick="updateStock()" class="modal-action-btn" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
                    <span style="font-size: 20px;">📊</span>
                    <div>
                        <div style="font-weight: bold;">Update Stock</div>
                        <div style="font-size: 12px; opacity: 0.9;">Adjust stock quantities</div>
                    </div>
                </button>
                
                <button onclick="viewRequests()" class="modal-action-btn" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
                    <span style="font-size: 20px;">📋</span>
                    <div>
                        <div style="font-weight: bold;">View Requests</div>
                        <div style="font-size: 12px; opacity: 0.9;">See request history</div>
                    </div>
                </button>
            </div>
        </div>
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

.asset-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.asset-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-inactive { background: #f5f5f5; color: #666; }
.status-discontinued { background: #ffebee; color: #d32f2f; }

.stock-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.stock-in_stock { background: #e8f5e8; color: #2e7d32; }
.stock-low_stock { background: #fff3e0; color: #f57c00; }
.stock-out_of_stock { background: #ffebee; color: #d32f2f; }

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

.btn-small {
    padding: 8px 12px;
    font-size: 14px;
}

.modal-action-btn {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
    width: 100%;
}

.modal-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>

<script>
let currentAssetId = null;

function assetQuickActions(assetId, assetName) {
    currentAssetId = assetId;
    document.getElementById('modalAssetName').textContent = `Actions for ${assetName}`;
    document.getElementById('assetQuickActionsModal').style.display = 'flex';
}

function closeAssetActions() {
    document.getElementById('assetQuickActionsModal').style.display = 'none';
}

function viewAsset() {
    closeAssetActions();
    window.location.href = `/assets/${currentAssetId}`;
}

function editAsset() {
    closeAssetActions();
    window.location.href = `/assets/${currentAssetId}/edit`;
}

function updateStock() {
    closeAssetActions();
    const newStock = prompt('Enter new stock quantity:');
    if (newStock !== null && !isNaN(newStock)) {
        // You can implement stock update AJAX here
        alert('Stock update functionality - to be implemented');
    }
}

function viewRequests() {
    closeAssetActions();
    alert('Request history - to be implemented');
}

function requestAsset(assetId) {
    const quantity = prompt('How many units would you like to request?', '1');
    if (quantity !== null && !isNaN(quantity) && quantity > 0) {
        // You can implement asset request functionality here
        alert(`Request for ${quantity} units submitted!`);
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('assetQuickActionsModal');
    if (event.target === modal) {
        closeAssetActions();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAssetActions();
    }
});
</script>
@endsection