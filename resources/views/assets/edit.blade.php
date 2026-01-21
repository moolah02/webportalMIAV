@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📦 Edit Asset</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Update asset information and settings</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('assets.show', $asset) }}" class="btn">👁️ View Asset</a>
            <a href="{{ route('assets.index') }}" class="btn">← Back to Assets</a>
        </div>
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

    <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Main Form -->
            <div>
                <!-- Basic Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📋 Basic Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Asset Name *</label>
                            <input type="text" name="name" value="{{ old('name', $asset->name) }}" required
                                   placeholder="e.g., Company Vehicle"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('name')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Category *</label>
                            <select name="category" id="categorySelect" required onchange="toggleCategoryFields()"
                                    style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Category</option>
                                @foreach($assetCategories as $category)
                                    <option value="{{ $category->name }}" {{ old('category', $asset->category) == $category->name ? 'selected' : '' }}>
                                        {{ $category->icon }} {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="brandField">
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand', $asset->brand) }}"
                                   placeholder="e.g., Toyota, Dell, Apple"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div id="modelField">
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Model</label>
                            <input type="text" name="model" value="{{ old('model', $asset->model) }}"
                                   placeholder="e.g., Corolla, Latitude 5420"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Description</label>
                        <textarea name="description" rows="3" placeholder="Detailed description of the asset..."
                                  style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('description', $asset->description) }}</textarea>
                        @error('description')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Vehicle-Specific Fields -->
                <div id="vehicleFields" class="content-card" style="margin-block-end: 20px; display: none;">
                    <h4 style="margin-block-end: 20px; color: #333;">🚗 Vehicle Details</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Plate *</label>
                            <input type="text" name="license_plate" value="{{ old('license_plate', $asset->specifications['license_plate'] ?? '') }}"
                                   placeholder="e.g., ABC-123D"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('license_plate')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">VIN Number</label>
                            <input type="text" name="vin_number" value="{{ old('vin_number', $asset->specifications['vin_number'] ?? '') }}"
                                   placeholder="Vehicle Identification Number"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Engine Number</label>
                            <input type="text" name="engine_number" value="{{ old('engine_number', $asset->specifications['engine_number'] ?? '') }}"
                                   placeholder="Engine serial number"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Year</label>
                            <input type="number" name="vehicle_year" value="{{ old('vehicle_year', $asset->specifications['vehicle_year'] ?? '') }}" min="1900" max="{{ date('Y') + 1 }}"
                                   placeholder="Manufacturing year"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Color</label>
                            <input type="text" name="vehicle_color" value="{{ old('vehicle_color', $asset->specifications['vehicle_color'] ?? '') }}"
                                   placeholder="e.g., White, Blue"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Fuel Type</label>
                            <select name="fuel_type" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Fuel Type</option>
                                <option value="Petrol" {{ old('fuel_type', $asset->specifications['fuel_type'] ?? '') == 'Petrol' ? 'selected' : '' }}>Petrol</option>
                                <option value="Diesel" {{ old('fuel_type', $asset->specifications['fuel_type'] ?? '') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="Electric" {{ old('fuel_type', $asset->specifications['fuel_type'] ?? '') == 'Electric' ? 'selected' : '' }}>Electric</option>
                                <option value="Hybrid" {{ old('fuel_type', $asset->specifications['fuel_type'] ?? '') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Registration Date</label>
                            <input type="date" name="registration_date" value="{{ old('registration_date', $asset->specifications['registration_date'] ?? '') }}"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Insurance Expiry</label>
                            <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry', $asset->specifications['insurance_expiry'] ?? '') }}"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <!-- Dynamic Category-Specific Fields -->
                @include('assets.partials.dynamic-fields')

                <!-- POS Terminal-Specific Fields -->
                <div id="posFields" class="content-card" style="margin-block-end: 20px; display: none;">
                    <h4 style="margin-block-end: 20px; color: #333;">🖥️ POS Terminal Details</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Terminal ID</label>
                            <input type="text" name="terminal_id" value="{{ old('terminal_id', $asset->specifications['terminal_id'] ?? '') }}"
                                   placeholder="Unique terminal identifier"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Software Version</label>
                            <input type="text" name="software_version" value="{{ old('software_version', $asset->specifications['software_version'] ?? '') }}"
                                   placeholder="e.g., v2.1.3"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <!-- Computer/IT Equipment Fields -->
                <div id="computerFields" class="content-card" style="margin-block-end: 20px; display: none;">
                    <h4 style="margin-block-end: 20px; color: #333;">💻 Computer/IT Details</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Processor</label>
                            <input type="text" name="processor" value="{{ old('processor', $asset->specifications['processor'] ?? '') }}"
                                   placeholder="e.g., Intel i5-11400H"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">RAM</label>
                            <input type="text" name="ram" value="{{ old('ram', $asset->specifications['ram'] ?? '') }}"
                                   placeholder="e.g., 8GB DDR4"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Storage</label>
                            <input type="text" name="storage" value="{{ old('storage', $asset->specifications['storage'] ?? '') }}"
                                   placeholder="e.g., 256GB SSD"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Operating System</label>
                            <input type="text" name="operating_system" value="{{ old('operating_system', $asset->specifications['operating_system'] ?? '') }}"
                                   placeholder="e.g., Windows 11 Pro"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <!-- Licenses Fields -->
                <div id="licenseFields" class="content-card" style="margin-block-end: 20px; display: none;">
                    <h4 style="margin-block-end: 20px; color: #333;">🔑 License Details</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Key</label>
                            <input type="text" name="license_key" value="{{ old('license_key', $asset->specifications['license_key'] ?? '') }}"
                                   placeholder="Software license key"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Expiry Date</label>
                            <input type="date" name="license_expiry" value="{{ old('license_expiry', $asset->specifications['license_expiry'] ?? '') }}"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Max Users</label>
                            <input type="number" name="max_users" value="{{ old('max_users', $asset->specifications['max_users'] ?? '') }}" min="1"
                                   placeholder="Maximum concurrent users"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Subscription Type</label>
                            <select name="subscription_type" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Type</option>
                                <option value="Monthly" {{ old('subscription_type', $asset->specifications['subscription_type'] ?? '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="Annual" {{ old('subscription_type', $asset->specifications['subscription_type'] ?? '') == 'Annual' ? 'selected' : '' }}>Annual</option>
                                <option value="Perpetual" {{ old('subscription_type', $asset->specifications['subscription_type'] ?? '') == 'Perpetual' ? 'selected' : '' }}>Perpetual</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">💰 Pricing & Inventory</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Unit Price *</label>
                            <input type="number" name="unit_price" value="{{ old('unit_price', $asset->unit_price) }}" step="0.01" min="0" required
                                   placeholder="0.00"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('unit_price')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Currency *</label>
                            <select name="currency" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="USD" {{ old('currency', $asset->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency', $asset->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency', $asset->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="ZWL" {{ old('currency', $asset->currency) == 'ZWL' ? 'selected' : '' }}>ZWL - Zimbabwe Dollar</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Status *</label>
                            <select name="status" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                                @foreach($assetStatuses as $status)
                                    <option value="{{ $status->slug }}" {{ old('status', $asset->status) == $status->slug ? 'selected' : '' }}>
                                        {{ $status->icon }} {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Stock Quantity *</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $asset->stock_quantity) }}" min="0" required
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('stock_quantity')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Min Stock Level *</label>
                            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $asset->min_stock_level) }}" min="0" required
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('min_stock_level')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $asset->sku) }}"
                                   placeholder="e.g., VEH-TOY-COR-001"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('sku')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $asset->barcode) }}"
                                   placeholder="Barcode number"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="content-card">
                    <h4 style="margin-block-end: 20px; color: #333;">📝 Additional Information</h4>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Image URL</label>
                        <input type="url" name="image_url" value="{{ old('image_url', $asset->image_url) }}"
                               placeholder="https://example.com/image.jpg"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('image_url')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Any additional notes about this asset..."
                                  style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('notes', $asset->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Settings Sidebar -->
            <div>
                <!-- Asset Image Preview -->
                @if($asset->image_url)
                    <div class="content-card" style="margin-block-end: 20px;">
                        <h4 style="margin-block-end: 15px; color: #333;">🖼️ Current Image</h4>
                        <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}"
                             style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                    </div>
                @endif

                <!-- Request Settings -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">⚙️ Request Settings</h4>

                    <div style="margin-block-end: 15px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="is_requestable" value="1" {{ old('is_requestable', $asset->is_requestable) ? 'checked' : '' }}>
                            <span>Available for Request</span>
                        </label>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Employees can request this asset</div>
                    </div>

                    <div style="margin-block-end: 15px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', $asset->requires_approval) ? 'checked' : '' }}>
                            <span>Requires Approval</span>
                        </label>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Requests need manager approval</div>
                    </div>
                </div>

                <!-- Asset Info -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">📊 Asset Information</h4>

                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #666;">Created</span>
                            <span>{{ $asset->created_at->format('M d, Y') }}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #666;">Last Updated</span>
                            <span>{{ $asset->updated_at->format('M d, Y') }}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #666;">Total Value</span>
                            <span style="font-weight: bold;">{{ $asset->currency }} {{ number_format($asset->unit_price * $asset->stock_quantity, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="content-card">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="inline-size: 100%; padding: 15px;">
                            💾 Update Asset
                        </button>
                        <a href="{{ route('assets.show', $asset) }}" class="btn" style="inline-size: 100%; text-align: center;">
                            Cancel
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
    transition: all 0.3s ease;
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

/* Animation for showing/hiding category fields */
.category-fields {
    animation: slideIn 0.3s ease-in-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
function toggleCategoryFields() {
    const categorySelect = document.getElementById('categorySelect');
    const selectedCategory = categorySelect.value;

    // Hide all category-specific fields first
    document.getElementById('vehicleFields').style.display = 'none';
    document.getElementById('posFields').style.display = 'none';
    document.getElementById('computerFields').style.display = 'none';
    document.getElementById('licenseFields').style.display = 'none';

    // Show relevant fields based on category
    if (selectedCategory === 'Vehicles') {
        document.getElementById('vehicleFields').style.display = 'block';
        document.getElementById('vehicleFields').classList.add('category-fields');

        // Make license plate required for vehicles
        document.querySelector('input[name="license_plate"]').required = true;
    } else if (selectedCategory === 'POS Terminals') {
        document.getElementById('posFields').style.display = 'block';
        document.getElementById('posFields').classList.add('category-fields');
    } else if (selectedCategory === 'Computer and IT Equipment') {
        document.getElementById('computerFields').style.display = 'block';
        document.getElementById('computerFields').classList.add('category-fields');
    } else if (selectedCategory === 'Licenses') {
        document.getElementById('licenseFields').style.display = 'block';
        document.getElementById('licenseFields').classList.add('category-fields');
    } else {
        // Remove required attribute from license plate when not vehicles
        const licensePlateInput = document.querySelector('input[name="license_plate"]');
        if (licensePlateInput) {
            licensePlateInput.required = false;
        }
    }
}

// Initialize form state on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleCategoryFields();
});
</script>
@endsection
