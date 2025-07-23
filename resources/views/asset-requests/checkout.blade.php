@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h2 style="margin: 0; color: #333;">📋 Request Checkout</h2>
        <p style="color: #666; margin: 5px 0 0 0;">Complete your asset request</p>
    </div>

    <form action="{{ route('asset-requests.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Request Details -->
            <div>
                <!-- Business Justification -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333;">📝 Business Justification</h4>
                    <textarea name="business_justification" rows="4" required
                              placeholder="Please explain why you need these assets and how they will be used in your work..."
                              style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;">{{ old('business_justification') }}</textarea>
                    @error('business_justification')
                        <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                        Minimum 20 characters required. Be specific about business needs.
                    </div>
                </div>

                <!-- Request Details -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333;">📅 Request Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Needed By Date</label>
                            <input type="date" name="needed_by_date" value="{{ old('needed_by_date') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Priority Level</label>
                            <select name="priority" required style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Delivery Instructions (Optional)</label>
                        <textarea name="delivery_instructions" rows="2"
                                  placeholder="Special delivery requirements, office location, etc..."
                                  style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">{{ old('delivery_instructions') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="content-card">
                    <h4 style="margin-bottom: 15px; color: #333;">📦 Order Summary</h4>
                    
                    <!-- Cart Items -->
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 15px;">
                        @foreach($cart as $item)
                        @php $asset = \App\Models\Asset::find($item['asset_id']) @endphp
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                            <div style="flex: 1;">
                                <div style="font-weight: 500; font-size: 14px;">{{ $item['name'] }}</div>
                                <div style="font-size: 12px; color: #666;">Qty: {{ $item['quantity'] }}</div>
                            </div>
                            <div style="font-weight: 500;">
                                ${{ number_format($item['quantity'] * $item['unit_price'], 2) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Total -->
                    @php $total = array_sum(array_map(fn($item) => $item['quantity'] * $item['unit_price'], $cart)) @endphp
                    <div style="border-top: 2px solid #eee; padding-top: 15px;">
                        <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold;">
                            <span>Total:</span>
                            <span style="color: #2196f3;">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                    
                    <!-- Approval Notice -->
                    <div style="background: #fff3e0; border: 1px solid #ff9800; border-radius: 6px; padding: 12px; margin: 15px 0;">
                        <div style="color: #f57c00; font-weight: 600; font-size: 14px; margin-bottom: 5px;">⏳ Approval Required</div>
                        <div style="color: #666; font-size: 12px;">
                            This request will be sent to your manager for approval before processing.
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px;">
                            Submit Request
                        </button>
                        <a href="{{ route('asset-requests.cart') }}" class="btn" style="width: 100%; text-align: center;">
                            ← Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection