{{-- 
==============================================
1. ASSET MANAGEMENT INDEX
File: resources/views/assets/index.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📦 Internal Assets</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage company assets and inventory</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('asset-approvals.index') }}" class="btn">⚖️ Approvals</a>
            <a href="{{ route('assets.create') }}" class="btn btn-primary">+ Add Asset</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">📦</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $assets->count() }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Assets</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">✅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $assets->where('is_requestable', true)->count() }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Available for Request</div>
                </div>
            </div>
        </div>

        <div class="metric-card alert" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⚠️</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $assets->where('stock_quantity', '<=', 5)->count() }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Low Stock</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">💰</div>
                <div>
                    <div style="font-size: 24px; font-weight: bold;">${{ number_format($assets->sum(function($asset) { return $asset->unit_price * $asset->stock_quantity; }), 0) }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search assets..." 
                   style="flex: 1; min-width: 250px; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
            
            <select name="category" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
            
            <select name="status" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
            </select>

            <select name="stock_status" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Stock Levels</option>
                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
            
            <button type="submit" class="btn">Filter</button>
            
            @if(request()->hasAny(['search', 'category', 'status', 'stock_status']))
            <a href="{{ route('assets.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Assets Table -->
    <div class="content-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Asset</th>
                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Category</th>
                        <th style="padding: 15px; text-align: right; border-bottom: 2px solid #eee; font-weight: 600;">Price</th>
                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid #eee; font-weight: 600;">Stock</th>
                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid #eee; font-weight: 600;">Requests</th>
                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid #eee; font-weight: 600;">Status</th>
                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid #eee; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($asset->image_url)
                                    <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                        📦
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight: 500; margin-bottom: 2px;">{{ $asset->name }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ $asset->brand }} {{ $asset->model }}</div>
                                    @if($asset->sku)
                                    <div style="font-size: 11px; color: #999;">SKU: {{ $asset->sku }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">
                            <span class="category-badge" style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                {{ $asset->category }}
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: right; border-bottom: 1px solid #eee;">
                            <div style="font-weight: 500;">${{ number_format($asset->unit_price, 2) }}</div>
                        </td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">
                            <div style="font-weight: 500; margin-bottom: 2px;">{{ $asset->stock_quantity }}</div>
                            <span class="status-badge {{ $asset->stock_quantity > 10 ? 'status-active' : ($asset->stock_quantity > 0 ? 'status-pending' : 'status-offline') }}" style="font-size: 10px;">
                                @if($asset->stock_quantity > 10) In Stock
                                @elseif($asset->stock_quantity > 0) Low Stock
                                @else Out of Stock
                                @endif
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">
                            {{ $asset->requestItems()->count() }}
                        </td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">
                            <span class="status-badge {{ $asset->status === 'active' ? 'status-active' : 'status-offline' }}">
                                {{ ucfirst($asset->status) }}
                            </span>
                            @if(!$asset->is_requestable)
                            <div style="font-size: 10px; color: #666; margin-top: 2px;">Not Requestable</div>
                            @endif
                        </td>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <a href="{{ route('assets.show', $asset) }}" class="btn-small">View</a>
                                <a href="{{ route('assets.edit', $asset) }}" class="btn-small">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 60px; text-align: center; color: #666;">
                            <div style="font-size: 48px; margin-bottom: 15px;">📦</div>
                            <h3>No assets found</h3>
                            <p>Start by adding your first asset to the system.</p>
                            <a href="{{ route('assets.create') }}" class="btn btn-primary" style="margin-top: 15px;">Add First Asset</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($assets->hasPages())
        <div style="margin-top: 20px;">
            {{ $assets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-offline { background: #ffebee; color: #d32f2f; }

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

.btn-small {
    padding: 6px 12px;
    font-size: 14px;
}
</style>
@endsection

{{-- 
==============================================
2. CREATE ASSET FORM
File: resources/views/assets/create.blade.php
==============================================
--}}
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

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Status</label>
                        <select name="status" style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                    </div>
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
</style>
@endsection