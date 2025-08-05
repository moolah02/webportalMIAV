<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
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
<div class="content-card" style="margin-block-end: 20px;">
    <form method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto auto; gap: 15px; align-items: end;">
        <input type="hidden" name="tab" value="assets">
        
        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Search Assets</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by name, model, SKU..." 
                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Category</label>
            <select name="category" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                <option value="">All Categories</option>
                @foreach($assetCategories as $category)
                    <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                        {{ $category->icon }} {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Status</label>
            <select name="status" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                <option value="">All Status</option>
                @foreach($assetStatuses as $status)
                    <option value="{{ $status->slug }}" {{ request('status') == $status->slug ? 'selected' : '' }}>
                        {{ $status->icon }} {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Stock</label>
            <select name="stock_status" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                <option value="">All Stock</option>
                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Filter</button>
        
        @if(request()->hasAny(['search', 'category', 'status', 'stock_status']))
        <a href="{{ route('assets.index', ['tab' => 'assets']) }}" class="btn">Clear</a>
        @endif
    </form>
</div>

<!-- Assets Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px;">
    @forelse($assets as $asset)
    <div class="asset-card">
        <!-- Asset Header -->
        <div style="display: flex; justify-content: space-between; align-items: start; margin-block-end: 15px;">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 10px; margin-block-end: 5px;">
                    <h4 style="margin: 0; color: #333;">{{ $asset->name }}</h4>
                    <span class="status-badge status-{{ str_replace('asset-', '', $asset->status) }}">
                        {{ $assetStatuses->where('slug', $asset->status)->first()->name ?? ucfirst(str_replace('asset-', '', $asset->status)) }}
                    </span>
                </div>
                <div style="font-size: 14px; color: #666;">
                    @php
                        $categoryObj = $assetCategories->where('name', $asset->category)->first();
                    @endphp
                    {{ $categoryObj->icon ?? '📦' }} {{ $asset->category }}
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

        <!-- Stock & Assignment Info -->
        <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-block-end: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 8px;">
                <span style="font-size: 12px; color: #666; text-transform: uppercase;">Stock & Assignment</span>
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
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <div style="font-size: 12px; color: #666;">Total Stock</div>
                    <div style="font-size: 18px; font-weight: bold; color: #333;">{{ $asset->stock_quantity }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #666;">Available</div>
                    <div style="font-size: 18px; font-weight: bold; color: {{ ($asset->available_quantity ?? $asset->stock_quantity) <= 0 ? '#f44336' : '#333' }};">
                        {{ $asset->available_quantity ?? $asset->stock_quantity }}
                    </div>
                </div>
                @if(isset($asset->assigned_quantity) && $asset->assigned_quantity > 0)
                <div>
                    <div style="font-size: 12px; color: #666;">Assigned</div>
                    <div style="font-size: 18px; font-weight: bold; color: #2196f3;">{{ $asset->assigned_quantity }}</div>
                </div>
                @endif
                <div>
                    <div style="font-size: 12px; color: #666;">Min Level</div>
                    <div style="font-size: 14px; color: #999;">{{ $asset->min_stock_level }}</div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($asset->description)
        <div style="margin-block-end: 15px;">
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
            
            @if(isset($asset->available_quantity) && $asset->available_quantity > 0)
            <button onclick="openAssignModal({{ $asset->id }})" 
                    class="btn-small" style="background: #4caf50; color: white; border-color: #4caf50;">
                👥 Assign
            </button>
            @endif
            
            @if($asset->is_requestable && method_exists($asset, 'canBeRequested') && $asset->canBeRequested())
            <button onclick="requestAsset({{ $asset->id }})" 
                    class="btn-small" style="background: #ff9800; color: white; border-color: #ff9800;">
                🛒 Request
            </button>
            @endif
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
        <div style="font-size: 64px; margin-block-end: 20px;">📦</div>
        <h3>No assets found</h3>
        <p>Start by adding your first asset to the inventory.</p>
        <a href="{{ route('assets.create') }}" class="btn btn-primary" style="margin-block-start: 15px;">Add First Asset</a>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if(method_exists($assets, 'hasPages') && $assets->hasPages())
<div style="margin-block-start: 30px; display: flex; justify-content: center;">
    {{ $assets->appends(request()->query())->links() }}
</div>
@endif