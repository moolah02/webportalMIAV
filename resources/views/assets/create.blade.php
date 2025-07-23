@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📦 Add New Asset</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Add a new asset to the company inventory</p>
        </div>
        <a href="{{ route('assets.index') }}" class="btn">← Back to Assets</a>
    </div>

    <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Main Form -->
            <div>
                <!-- Basic Information -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">📋 Basic Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Asset Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g., Dell Laptop"
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('name')
                                <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Category *</label>
                            <select name="category" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Category</option>
                                <option value="Hardware" {{ old('category') == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="Software" {{ old('category') == 'Software' ? 'selected' : '' }}>Software</option>
                                <option value="Mobile Devices" {{ old('category') == 'Mobile Devices' ? 'selected' : '' }}>Mobile Devices</option>
                                <option value="Office Supplies" {{ old('category') == 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                                <option value="Furniture" {{ old('category') == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                                <option value="Networking" {{ old('category') == 'Networking' ? 'selected' : '' }}>Networking</option>
                            </select>
                            @error('category')
                                <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand') }}"
                                   placeholder="e.g., Dell, Apple, Microsoft"
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Model</label>
                            <input type="text" name="model" value="{{ old('model') }}"
                                   placeholder="e.g., Latitude 5420"
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Description</label>
                        <textarea name="description" rows="3" placeholder="Detailed description of the asset..."
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('description') }}</textarea>
                        @error('description')
                            <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">💰 Pricing & Inventory</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Unit Price *</label>
                            <input type="number" name="unit_price" value="{{ old('unit_price') }}" step="0.01" min="0" required
                                   placeholder="0.00"
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('unit_price')
                                <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Currency *</label>
                            <select name="currency" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Stock Quantity *</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('stock_quantity')
                                <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Min Stock Level *</label>
                            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 0) }}" min="0" required
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('min_stock_level')
                                <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku') }}"
                                   placeholder="e.g., DELL-LAT-5420"
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('sku')
                                <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode') }}"
                                   placeholder="Barcode number"
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="content-card">
                    <h4 style="margin-bottom: 20px; color: #333;">📝 Additional Information</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Image URL</label>
                        <input type="url" name="image_url" value="{{ old('image_url') }}"
                               placeholder="https://example.com/image.jpg"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('image_url')
                            <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Any additional notes about this asset..."
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Settings Sidebar -->
            <div>
                <!-- Request Settings -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333;">⚙️ Request Settings</h4>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="is_requestable" value="1" {{ old('is_requestable', true) ? 'checked' : '' }}>
                            <span>Available for Request</span>
                        </label>
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Employees can request this asset</div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', true) ? 'checked' : '' }}>
                            <span>Requires Approval</span>
                        </label>
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Requests need manager approval</div>
                    </div>
                </div>

                <!-- Specifications -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333;">🔧 Specifications</h4>
                    <div id="specifications">
                        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" placeholder="Spec name" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <input type="text" placeholder="Value" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <button type="button" onclick="removeSpec(this)" style="padding: 8px; background: #f44336; color: white; border: none; border-radius: 4px;">×</button>
                        </div>
                    </div>
                    <button type="button" onclick="addSpec()" class="btn-small" style="width: 100%;">+ Add Specification</button>
                </div>

                <!-- Submit Buttons -->
                <div class="content-card">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">
                            Create Asset
                        </button>
                        <a href="{{ route('assets.index') }}" class="btn" style="width: 100%; text-align: center;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function addSpec() {
    const container = document.getElementById('specifications');
    const newSpec = document.createElement('div');
    newSpec.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
    newSpec.innerHTML = `
        <input type="text" name="specifications[key][]" placeholder="Spec name" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        <input type="text" name="specifications[value][]" placeholder="Value" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        <button type="button" onclick="removeSpec(this)" style="padding: 8px; background: #f44336; color: white; border: none; border-radius: 4px;">×</button>
    `;
    container.appendChild(newSpec);
}

function removeSpec(button) {
    button.parentElement.remove();
}
</script>
@endsection