{{-- All Assets as cards. Not currently included by any view; if used, include it from
     assets/index.blade.php, whose styles block defines the .as-* classes below. --}}
@php
    $cardBadge = ['active' => 'badge-green', 'available' => 'badge-green', 'inactive' => 'badge-gray', 'retired' => 'badge-gray', 'maintenance' => 'badge-yellow', 'pending' => 'badge-yellow', 'damaged' => 'badge-red', 'discontinued' => 'badge-red'];
    $stockBadge = ['in_stock' => 'badge-green', 'low_stock' => 'badge-yellow'];
@endphp

{{-- Statistics --}}
<div class="as-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg></div>
        <div class="min-w-0">
            <div class="stat-number">{{ $stats['total_assets'] ?? 0 }}</div>
            <div class="stat-label">Total Assets</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div class="min-w-0">
            <div class="stat-number">{{ $stats['active_assets'] ?? 0 }}</div>
            <div class="stat-label">Active Assets</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ ($stats['low_stock'] ?? 0) > 0 ? 'stat-icon-yellow' : '' }}"><svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg></div>
        <div class="min-w-0">
            <div class="stat-number">{{ $stats['low_stock'] ?? 0 }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-banknote"/></svg></div>
        <div class="min-w-0">
            <div class="stat-number">${{ number_format($stats['total_value'] ?? 0, 0) }}</div>
            <div class="stat-label">Total Value</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <input type="hidden" name="tab" value="assets">

    <div class="filter-group as-grow">
        <label class="ui-label">Search Assets</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name, model, SKU..." class="ui-input">
    </div>

    <div class="filter-group">
        <label class="ui-label">Category</label>
        <select name="category" class="ui-select">
            <option value="">All Categories</option>
            @foreach($assetCategories as $category)
                <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select">
            <option value="">All Status</option>
            @foreach($assetStatuses as $status)
                <option value="{{ $status->slug }}" {{ request('status') == $status->slug ? 'selected' : '' }}>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="ui-label">Stock</label>
        <select name="stock_status" class="ui-select">
            <option value="">All Stock</option>
            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
    </div>

    <div class="filter-actions">
        @if(request()->hasAny(['search', 'category', 'status', 'stock_status']))
        <a href="{{ route('assets.index', ['tab' => 'assets']) }}" class="btn-secondary">Clear</a>
        @endif
        <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-filter"/></svg> Filter</button>
    </div>
</form>

{{-- Assets Grid --}}
<div class="as-cards">
    @forelse($assets as $asset)
    @php $cardSlug = str_replace('asset-', '', $asset->status); @endphp
    <div class="ui-card asset-card">
        <div class="ui-card-body">
            {{-- Asset Header --}}
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h4 class="as-card-title">{{ $asset->name }}</h4>
                        <span class="badge {{ $cardBadge[$cardSlug] ?? 'badge-gray' }}">
                            {{ $assetStatuses->where('slug', $asset->status)->first()->name ?? ucfirst($cardSlug) }}
                        </span>
                    </div>
                    <div class="cell-sub">
                        {{ $asset->category }}
                        @if($asset->brand || $asset->model)
                            &middot; {{ $asset->brand }} {{ $asset->model }}
                        @endif
                    </div>
                    @if($asset->sku)
                        <div class="cell-sub">SKU <span class="mv-mono">{{ $asset->sku }}</span></div>
                    @endif
                </div>

                <div class="text-right">
                    <div class="as-price">{{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}</div>
                    @if($asset->is_requestable)
                        <span class="badge badge-blue mt-1">Requestable</span>
                    @endif
                </div>
            </div>

            {{-- Stock & Assignment Info --}}
            <div class="as-summary" style="margin:0;">
                <div class="flex items-center justify-between" style="margin-bottom:10px;">
                    <span class="as-sec" style="margin:0;">Stock &amp; Assignment</span>
                    <span class="badge stock-badge stock-{{ $asset->stock_status }} {{ $stockBadge[$asset->stock_status] ?? 'badge-red' }}">
                        @if($asset->stock_status == 'in_stock')
                            In Stock
                        @elseif($asset->stock_status == 'low_stock')
                            Low Stock
                        @else
                            Out of Stock
                        @endif
                    </span>
                </div>
                <div class="as-kv">
                    <div>
                        <div class="as-k">Total Stock</div>
                        <div class="as-v is-strong">{{ $asset->stock_quantity }}</div>
                    </div>
                    <div>
                        <div class="as-k">Available</div>
                        <div class="as-v is-strong {{ ($asset->available_quantity ?? $asset->stock_quantity) <= 0 ? 'as-late' : '' }}">
                            {{ $asset->available_quantity ?? $asset->stock_quantity }}
                        </div>
                    </div>
                    @if(isset($asset->assigned_quantity) && $asset->assigned_quantity > 0)
                    <div>
                        <div class="as-k">Assigned</div>
                        <div class="as-v is-strong">{{ $asset->assigned_quantity }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="as-k">Min Level</div>
                        <div class="as-v">{{ $asset->min_stock_level }}</div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if($asset->description)
                <p class="cell-sub" style="margin:0;">{{ Str::limit($asset->description, 80) }}</p>
            @endif

            {{-- Actions --}}
            <div class="flex gap-2">
                <button type="button" onclick="assetQuickActions({{ $asset->id }}, '{{ $asset->name }}')"
                        class="btn-secondary btn-sm flex-1 justify-center">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-more"/></svg> Quick Actions
                </button>

                @if(isset($asset->available_quantity) && $asset->available_quantity > 0)
                <button type="button" onclick="openAssignModal({{ $asset->id }})" class="btn-secondary btn-sm">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-user-plus"/></svg> Assign
                </button>
                @endif

                @if($asset->is_requestable && method_exists($asset, 'canBeRequested') && $asset->canBeRequested())
                <button type="button" onclick="requestAsset({{ $asset->id }})" class="btn-secondary btn-sm">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-cart"/></svg> Request
                </button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="ui-card" style="grid-column: 1 / -1;">
        <div class="empty-state">
            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg></div>
            <h3>No assets found</h3>
            <p class="empty-state-msg">Start by adding your first asset to the inventory.</p>
            <a href="{{ route('assets.create') }}" class="btn-primary btn-sm">Add First Asset</a>
        </div>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if(method_exists($assets, 'hasPages') && $assets->hasPages())
<div class="as-pager flex" style="margin-top:16px;">
    {{ $assets->appends(request()->query())->links() }}
</div>
@endif
