
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">🛒 Request Cart</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Review your selected assets before requesting</p>
        </div>
        <a href="{{ route('asset-requests.catalog') }}" class="btn">← Continue Request</a>
    </div>

    @if(count($cartItems) > 0)
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Cart Items -->
        <div class="content-card">
            <h4 style="margin-block-end: 20px; color: #333;">Cart Items</h4>
            
            @foreach($cartItems as $item)
            <div style="display: flex; align-items: center; gap: 15px; padding: 15px 0; border-block-end: 1px solid #f0f0f0;">
                <!-- Asset Image -->
                <div style="inline-size: 80px; height: 80px; border-radius: 8px; overflow: hidden; background: #f5f5f5; flex-shrink: 0;">
                    @if($item['image_url'])
                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" style="inline-size: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="inline-size: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                            📦
                        </div>
                    @endif
                </div>

                <!-- Asset Info -->
                <div style="flex: 1;">
                    <h5 style="margin: 0 0 5px 0; color: #333;">{{ $item['name'] }}</h5>
                    <div style="font-size: 14px; color: #666; margin-block-end: 5px;">
                        {{ $item['asset']->brand }} {{ $item['asset']->model }}
                    </div>
                    <div style="font-size: 14px; color: #2196f3; font-weight: 500;">
                        ${{ number_format($item['unit_price'], 2) }} each
                    </div>
                    <div style="font-size: 12px; color: #666;">
                        Available: {{ $item['asset']->stock_quantity }}
                    </div>
                </div>

                <!-- Quantity Controls -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <form action="{{ route('asset-requests.cart.update', $item['asset_id']) }}" method="POST" style="display: flex; align-items: center; gap: 10px;">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" 
                               min="1" max="{{ $item['asset']->stock_quantity }}"
                               style="inline-size: 70px; padding: 5px; border: 2px solid #ddd; border-radius: 4px;"
                               onchange="this.form.submit()">
                    </form>
                    
                    <form action="{{ route('asset-requests.cart.remove', $item['asset_id']) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #f44336; cursor: pointer; font-size: 18px;" 
                                onclick="return confirm('Remove this item from cart?')">
                            🗑️
                        </button>
                    </form>
                </div>

                <!-- Subtotal -->
                <div style="text-align: right; min-inline-size: 100px;">
                    <div style="font-size: 16px; font-weight: bold; color: #333;">
                        ${{ number_format($item['subtotal'], 2) }}
                    </div>
                    <div style="font-size: 12px; color: #666;">
                        {{ $item['quantity'] }} × ${{ number_format($item['unit_price'], 2) }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Cart Summary -->
        <div class="content-card" style="height: fit-content;">
            <h4 style="margin-block-end: 20px; color: #333;">Order Summary</h4>
            
            <div style="margin-block-end: 20px;">
                <div style="display: flex; justify-content: space-between; margin-block-end: 10px;">
                    <span style="color: #666;">Items ({{ array_sum(array_column($cartItems, 'quantity')) }}):</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-block-end: 10px;">
                    <span style="color: #666;">Estimated Tax:</span>
                    <span>$0.00</span>
                </div>
                <hr style="margin: 15px 0; border: none; border-block-start: 1px solid #eee;">
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold;">
                    <span>Total:</span>
                    <span style="color: #2196f3;">${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <a href="{{ route('asset-requests.checkout') }}" class="btn btn-primary" 
               style="inline-size: 100%; text-align: center; padding: 15px; font-size: 16px; margin-block-end: 10px;">
                Proceed to Checkout
            </a>
            
            <a href="{{ route('asset-requests.catalog') }}" class="btn" 
               style="inline-size: 100%; text-align: center;">
                Continue Reqest
            </a>
        </div>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="content-card" style="text-align: center; padding: 60px;">
        <div style="font-size: 64px; margin-block-end: 20px;">🛒</div>
        <h3>Your cart is empty</h3>
        <p style="color: #666; margin-block-end: 30px;">Browse our asset catalog and add items to your cart.</p>
        <a href="{{ route('asset-requests.catalog') }}" class="btn btn-primary">
            Start Request
        </a>
    </div>
    @endif
</div>

<style>
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

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endsection