@extends('layouts.app')

@section('content')
<div>
    <!-- Header with Cart -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">🛒 Asset Catalog</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Browse and request company assets</p>
        </div>
        <div style="display: flex; gap: 15px; align-items: center;">
            <a href="{{ route('asset-requests.cart') }}" class="cart-button">
                🛒 Cart 
                @if($cartItemCount > 0)
                <span class="cart-badge">{{ $cartItemCount }}</span>
                @endif
            </a>
            <a href="{{ route('asset-requests.index') }}" class="btn">My Requests</a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="content-card" style="margin-block-end: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search assets..." 
                   style="flex: 1; min-inline-size: 300px; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
            
            <select name="category" style="padding: 10px; border: 2px solid #ddd; border-radius: 6px; min-inline-size: 150px;">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="btn btn-primary">Search</button>
            
            @if(request()->hasAny(['search', 'category']))
            <a href="{{ route('asset-requests.catalog') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Asset Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($assets as $asset)
        <div class="asset-card">
            <!-- Asset Image -->
            <div class="asset-image">
                @if($asset->image_url)
                    <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" style="inline-size: 100%; height: 200px; object-fit: cover;">
                @else
                    <div style="inline-size: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                        @switch($asset->category)
                            @case('Hardware') 💻 @break
                            @case('Software') ⚙️ @break
                            @case('Mobile Devices') 📱 @break
                            @case('Office Supplies') 📝 @break
                            @case('Furniture') 🪑 @break
                            @case('Networking') 🌐 @break
                            @default 📦
                        @endswitch
                    </div>
                @endif
            </div>

            <!-- Asset Info -->
            <div style="padding: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-block-end: 10px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #333;">{{ $asset->name }}</h4>
                        <div style="font-size: 12px; color: #666;">{{ $asset->brand }} {{ $asset->model }}</div>
                    </div>
                    <span class="category-badge" style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 11px;">
                        {{ $asset->category }}
                    </span>
                </div>

                <p style="color: #666; font-size: 14px; margin: 10px 0; line-height: 1.4;">
                    {{ Str::limit($asset->description, 80) }}
                </p>

                <!-- Price and Stock -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0;">
                    <div>
                        <div style="font-size: 18px; font-weight: bold; color: #2196f3;">{{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}</div>
                        <div style="font-size: 12px; color: #666;">
                            Stock: {{ $asset->stock_quantity }}
                            <span style="color: {{ $asset->stock_quantity > 0 ? '#4caf50' : '#f44336' }}; margin-left: 5px; font-size: 10px;">
                                {{ $asset->stock_quantity > 0 ? '✓ Available' : '✗ Out of Stock' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                @if($asset->stock_quantity > 0 && $asset->is_requestable)
                <form action="{{ route('asset-requests.cart.add', $asset) }}" method="POST" style="display: flex; gap: 10px;">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" max="{{ $asset->stock_quantity }}" 
                           style="inline-size: 70px; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Add to Cart
                    </button>
                </form>
                @else
                <button disabled class="btn" style="inline-size: 100%; opacity: 0.5;">
                    {{ $asset->stock_quantity == 0 ? 'Out of Stock' : 'Not Available' }}
                </button>
                @endif
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
            <div style="font-size: 64px; margin-block-end: 20px;">📦</div>
            <h3>No assets found</h3>
            <p>Try adjusting your search criteria or check back later.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($assets->hasPages())
    <div style="margin-block-start: 30px; display: flex; justify-content: center;">
        {{ $assets->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.cart-button {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.2s ease;
}

.cart-button:hover {
    transform: translateY(-1px);
    color: white;
}

.cart-badge {
    background: #f44336;
    color: white;
    border-radius: 50%;
    inline-size: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.asset-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.asset-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.asset-image {
    position: relative;
    overflow: hidden;
}

.asset-image img {
    transition: transform 0.3s ease;
}

.asset-card:hover .asset-image img {
    transform: scale(1.05);
}

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

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endsection