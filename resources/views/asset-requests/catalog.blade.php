@extends('layouts.app')

@push('styles')
<style>
    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        background: white;
        padding: 24px 32px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .filters-section {
        margin-bottom: 32px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .table th, .table td {
        padding: 12px;
        border: 1px solid #dee2e6;
        text-align: left;
    }

    .table th {
        background-color: #f8f9fa;
    }

    .stock-status {
        padding: 2px 6px;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stock-available {
        background: #d4edda;
        color: #155724;
    }

    .stock-unavailable {
        background: #f8d7da;
        color: #721c24;
    }

    .add-to-cart-btn {
        padding: 8px 16px;
        background: #2c3e50;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .add-to-cart-btn:hover {
        background: #34495e;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination {
        display: flex;
        gap: 10px;
    }

    .pagination a, .pagination span {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-decoration: none;
        color: #2c3e50;
        transition: background 0.2s, color 0.2s;
    }

    .pagination a:hover {
        background: #2c3e50;
        color: white;
    }

    .pagination .active {
        background: #2196f3;
        color: white;
        border: 1px solid #2196f3;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .table {
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">🛒 Asset Catalog</h1>
            <p class="page-subtitle">Browse and request company assets</p>
        </div>
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
            <button type="submit" class="btn btn-primary">🔍 Search</button>
            @if(request()->hasAny(['search']))
            <a href="{{ route('asset-requests.catalog') }}" class="btn btn-secondary">Clear Filters</a>
            @endif
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Asset</th>
                <th>Brand</th>
                <th>Price</th>
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
                <td>{{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}</td>
                <td>
                    <span class="stock-status {{ $asset->stock_quantity > 0 ? 'stock-available' : 'stock-unavailable' }}">
                        {{ $asset->stock_quantity > 0 ? '✓ Available' : '✗ Out of Stock' }}
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
                <td colspan="5" class="empty-state">
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