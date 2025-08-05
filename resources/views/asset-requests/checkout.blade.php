@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">🛒 Submit Asset Request</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Provide details for your asset request</p>
        </div>
        <a href="{{ route('asset-requests.cart') }}" class="btn">← Back to Cart</a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-block-end: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-block-end: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('asset-requests.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Main Form -->
            <div>
                <!-- Request Details -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📋 Request Information</h4>
                    
                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Business Justification *</label>
                        <textarea name="business_justification" rows="4" required
                                  placeholder="Please explain why you need these assets and how they will be used..."
                                  style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">{{ old('business_justification') }}</textarea>
                        @error('business_justification')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Minimum 20 characters required</div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Priority *</label>
                            <select name="priority" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select Priority</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - Standard processing</option>
                                <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal - Regular business need</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Important for business</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent - Critical business need</option>
                            </select>
                            @error('priority')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Needed By Date</label>
                            <input type="date" name="needed_by_date" value="{{ old('needed_by_date') }}" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                            @error('needed_by_date')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Delivery Instructions</label>
                        <textarea name="delivery_instructions" rows="3"
                                  placeholder="Any special delivery instructions or preferred delivery location..."
                                  style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">{{ old('delivery_instructions') }}</textarea>
                        @error('delivery_instructions')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Request Items Review -->
                <div class="content-card">
                    <h4 style="margin-block-end: 20px; color: #333;">📦 Items in Your Request</h4>
                    
                    @foreach($cartItems as $item)
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px 0; border-block-end: 1px solid #f0f0f0;">
                        <!-- Asset Image -->
                        <div style="inline-size: 60px; height: 60px; border-radius: 8px; overflow: hidden; background: #f5f5f5; flex-shrink: 0;">
                            @if($item['image_url'])
                                <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" style="inline-size: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="inline-size: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    📦
                                </div>
                            @endif
                        </div>

                        <!-- Asset Info -->
                        <div style="flex: 1;">
                            <h5 style="margin: 0 0 5px 0; color: #333;">{{ $item['name'] }}</h5>
                            <div style="font-size: 14px; color: #666;">
                                {{ $item['asset']->brand }} {{ $item['asset']->model }}
                            </div>
                        </div>

                        <!-- Quantity and Price -->
                        <div style="text-align: right; min-inline-size: 100px;">
                            <div style="font-size: 16px; font-weight: bold; color: #333;">
                                {{ $item['quantity'] }} × ${{ number_format($item['unit_price'], 2) }}
                            </div>
                            <div style="font-size: 14px; color: #2196f3; font-weight: 500;">
                                ${{ number_format($item['subtotal'], 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div style="text-align: right; padding: 15px 0; font-size: 18px; font-weight: bold; color: #333;">
                        Total Estimated Cost: <span style="color: #2196f3;">${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Requester Info -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">👤 Requester Information</h4>
                    
                    <div style="margin-block-end: 10px;">
                        <div style="font-weight: 500;">{{ auth()->user()->full_name }}</div>
                        <div style="font-size: 14px; color: #666;">{{ auth()->user()->role->name ?? 'Employee' }}</div>
                        @if(auth()->user()->department)
                            <div style="font-size: 14px; color: #666;">{{ auth()->user()->department->name }}</div>
                        @endif
                    </div>
                    
                    <div style="font-size: 14px;">
                        📧 {{ auth()->user()->email }}
                    </div>
                </div>

                <!-- Request Summary -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">📊 Request Summary</h4>
                    
                    <div style="display: flex; justify-content: space-between; margin-block-end: 10px;">
                        <span style="color: #666;">Total Items:</span>
                        <span style="font-weight: 500;">{{ array_sum(array_column($cartItems, 'quantity')) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-block-end: 10px;">
                        <span style="color: #666;">Estimated Cost:</span>
                        <span style="font-weight: 500;">${{ number_format($total, 2) }}</span>
                    </div>
                    
                    <hr style="margin: 15px 0; border: none; border-block-start: 1px solid #eee;">
                    
                    <div style="font-size: 12px; color: #666; line-height: 1.4;">
                        <strong>Note:</strong> This request will be reviewed by your manager before approval. 
                        You'll receive email notifications about status updates.
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="content-card">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="inline-size: 100%; padding: 15px; font-size: 16px;">
                            📤 Submit Request
                        </button>
                        
                        <a href="{{ route('asset-requests.cart') }}" class="btn" style="inline-size: 100%; text-align: center;">
                            ← Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
    text-decoration: none;
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

.alert {
    border-radius: 6px;
    padding: 15px;
    margin-block-end: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
@endsection