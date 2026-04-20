@extends('layouts.app')
@section('title', 'Asset Catalog')

@push('styles')
@endpush

@section('content')

    <div class="page-header">
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('asset-requests.cart') }}" class="btn btn-secondary">
                🛒 Cart 
                @if($cartItemCount > 0)
                <span class="cart-badge">{{ $cartItemCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <div class="filters-section">
        <form method="GET" class="filters-form">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search assets..." 
                   class="form-control search-input">
            <button type="submit" class="btn-primary">🔍 Search</button>
            @if(request()->hasAny(['search']))
            <a href="{{ route('asset-requests.catalog') }}" class="btn btn-secondary">Clear Filters</a>
            @endif
        </form>
    </div>

    <table class="ui-table">
        <thead>
            <tr>
                <th>Asset</th>
                <th>Brand</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td>
                    <div>{{ $asset->name }}</div>
                    <div>{{ $asset->category }}</div>
                </td>
                <td>{{ $asset->brand }} {{ $asset->model }}</td>
                <td>
                    <span class="stock-status {{ $asset->stock_quantity > 0 ? 'stock-available' : 'stock-unavailable' }}">
                        {{ $asset->stock_quantity > 0 ? '✓ AVAILABLE' : '✗ Out of Stock' }}
                    </span>
                </td>
                <td>
                    @if($asset->stock_quantity > 0 && $asset->is_requestable)
                    <form action="{{ route('asset-requests.cart.add', $asset) }}" method="POST">
                        @csrf
                        <input type="number" name="quantity" value="1" min="1" max="{{ $asset->stock_quantity }}" class="quantity-input" style="width: 70px;">
                        <button type="submit" class="add-to-cart-btn">➕ Add to Cart</button>
                    </form>
                    @else
                    <button disabled class="add-to-cart-btn">
                        {{ $asset->stock_quantity == 0 ? '❌ Out of Stock' : '🚫 Not Available' }}
                    </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3>No assets found</h3>
                    <p>Try adjusting your search criteria or check back later.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($assets->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination">
            {{ $assets->appends(request()->query())->links('vendor.pagination.custom') }}
        </div>
    </div>
    @endif
</div>
@endsection