@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📦 {{ $asset->name }}</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Asset Details and Information</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">✏️ Edit Asset</a>
            <a href="{{ route('assets.index') }}" class="btn">← Back to Assets</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Content -->
        <div>
            <!-- Basic Information -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 20px; color: #333;">📋 Basic Information</h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Asset Name</label>
                        <div style="font-size: 16px; color: #333;">{{ $asset->name }}</div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Category</label>
                        <div style="font-size: 16px; color: #333;">
                            @php
                                $categoryObj = App\Models\Category::ofType('asset_category')->where('name', $asset->category)->first();
                            @endphp
                            {{ $categoryObj->icon ?? '📦' }} {{ $asset->category }}
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Brand</label>
                        <div style="font-size: 16px; color: #333;">{{ $asset->brand ?: 'Not specified' }}</div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Model</label>
                        <div style="font-size: 16px; color: #333;">{{ $asset->model ?: 'Not specified' }}</div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">SKU</label>
                        <div style="font-size: 16px; color: #333;">{{ $asset->sku ?: 'Not assigned' }}</div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Status</label>
                        <span class="status-badge status-{{ str_replace('asset-', '', $asset->status) }}">
                            @php
                                $statusObj = App\Models\Category::ofType('asset_status')->where('slug', $asset->status)->first();
                            @endphp
                            {{ $statusObj->icon ?? '📊' }} {{ $statusObj->name ?? ucfirst(str_replace('asset-', '', $asset->status)) }}
                        </span>
                    </div>
                </div>

                @if($asset->description)
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Description</label>
                        <div style="font-size: 16px; color: #333; line-height: 1.5;">{{ $asset->description }}</div>
                    </div>
                @endif
            </div>

            <!-- Vehicle-Specific Information -->
            @if($asset->category === 'Vehicles' && !empty($asset->specifications))
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">🚗 Vehicle Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        @if(!empty($asset->specifications['license_plate']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">License Plate</label>
                                <div style="font-size: 18px; color: #333; font-weight: bold; background: #f8f9fa; padding: 10px; border-radius: 6px; text-align: center;">
                                    {{ $asset->specifications['license_plate'] }}
                                </div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['vin_number']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">VIN Number</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['vin_number'] }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['engine_number']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Engine Number</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['engine_number'] }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['vehicle_year']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Year</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['vehicle_year'] }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['vehicle_color']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Color</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['vehicle_color'] }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['fuel_type']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Fuel Type</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['fuel_type'] }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['registration_date']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Registration Date</label>
                                <div style="font-size: 16px; color: #333;">{{ \Carbon\Carbon::parse($asset->specifications['registration_date'])->format('M d, Y') }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['insurance_expiry']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Insurance Expiry</label>
                                <div style="font-size: 16px; color: {{ \Carbon\Carbon::parse($asset->specifications['insurance_expiry'])->isPast() ? '#dc3545' : '#333' }};">
                                    {{ \Carbon\Carbon::parse($asset->specifications['insurance_expiry'])->format('M d, Y') }}
                                    @if(\Carbon\Carbon::parse($asset->specifications['insurance_expiry'])->isPast())
                                        <span style="color: #dc3545; font-weight: bold;"> (EXPIRED)</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- POS Terminal Information -->
            @if($asset->category === 'POS Terminals' && !empty($asset->specifications))
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">🖥️ POS Terminal Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        @if(!empty($asset->specifications['terminal_id']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Terminal ID</label>
                                <div style="font-size: 16px; color: #333; font-family: monospace; background: #f8f9fa; padding: 8px; border-radius: 4px;">
                                    {{ $asset->specifications['terminal_id'] }}
                                </div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['software_version']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Software Version</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['software_version'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Computer/IT Equipment Information -->
            @if($asset->category === 'Computer and IT Equipment' && !empty($asset->specifications))
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">💻 Computer/IT Specifications</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        @if(!empty($asset->specifications['processor']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Processor</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['processor'] }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['ram']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">RAM</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['ram'] }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['storage']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Storage</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['storage'] }}</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['operating_system']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Operating System</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['operating_system'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- License Information -->
            @if($asset->category === 'Licenses' && !empty($asset->specifications))
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">🔑 License Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        @if(!empty($asset->specifications['license_key']))
                            <div style="grid-column: 1 / -1;">
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">License Key</label>
                                <div style="font-size: 14px; color: #333; font-family: monospace; background: #f8f9fa; padding: 12px; border-radius: 6px; word-break: break-all;">
                                    {{ $asset->specifications['license_key'] }}
                                    <button onclick="copyToClipboard('{{ $asset->specifications['license_key'] }}')" 
                                            style="float: right; padding: 4px 8px; font-size: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['license_expiry']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Expiry Date</label>
                                <div style="font-size: 16px; color: {{ \Carbon\Carbon::parse($asset->specifications['license_expiry'])->isPast() ? '#dc3545' : '#333' }};">
                                    {{ \Carbon\Carbon::parse($asset->specifications['license_expiry'])->format('M d, Y') }}
                                    @if(\Carbon\Carbon::parse($asset->specifications['license_expiry'])->isPast())
                                        <span style="color: #dc3545; font-weight: bold;"> (EXPIRED)</span>
                                    @elseif(\Carbon\Carbon::parse($asset->specifications['license_expiry'])->diffInDays() <= 30)
                                        <span style="color: #ff9800; font-weight: bold;"> (EXPIRES SOON)</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['max_users']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Max Users</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['max_users'] }} users</div>
                            </div>
                        @endif

                        @if(!empty($asset->specifications['subscription_type']))
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Subscription Type</label>
                                <div style="font-size: 16px; color: #333;">{{ $asset->specifications['subscription_type'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Pricing & Inventory -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 20px; color: #333;">💰 Pricing & Inventory</h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Unit Price</label>
                        <div style="font-size: 24px; font-weight: bold; color: #333;">
                            {{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Stock Quantity</label>
                        <div style="font-size: 24px; font-weight: bold; color: {{ $asset->stock_quantity <= $asset->min_stock_level ? '#dc3545' : '#333' }};">
                            {{ $asset->stock_quantity }}
                            @if($asset->stock_quantity <= $asset->min_stock_level)
                                <span style="font-size: 12px; color: #dc3545;">LOW STOCK</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Min Stock Level</label>
                        <div style="font-size: 18px; color: #666;">{{ $asset->min_stock_level }}</div>
                    </div>

                    @if($asset->barcode)
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #666;">Barcode</label>
                            <div style="font-size: 16px; color: #333; font-family: monospace;">{{ $asset->barcode }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Requests -->
            @if($recentRequests->count() > 0)
                <div class="content-card">
                    <h4 style="margin-block-end: 20px; color: #333;">📋 Recent Requests</h4>
                    
                    <div style="max-height: 300px; overflow-y: auto;">
                        @foreach($recentRequests as $requestItem)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #eee;">
                                <div>
                                    <div style="font-weight: 500;">{{ $requestItem->assetRequest->employee->full_name }}</div>
                                    <div style="font-size: 14px; color: #666;">
                                        {{ $requestItem->quantity_requested }} units • {{ $requestItem->assetRequest->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                                <span class="status-badge status-{{ $requestItem->assetRequest->status }}">
                                    {{ ucfirst($requestItem->assetRequest->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Asset Image -->
            @if($asset->image_url)
                <div class="content-card" style="margin-block-end: 20px;">
                    <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" 
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">⚡ Quick Actions</h4>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary" style="text-align: center;">
                        ✏️ Edit Asset
                    </a>
                    
                    @if($asset->is_requestable && $asset->canBeRequested())
                        <button onclick="requestAsset({{ $asset->id }})" class="btn" style="background: #28a745; color: white; border-color: #28a745;">
                            🛒 Request Asset
                        </button>
                    @endif
                    
                    <button onclick="updateStock()" class="btn" style="background: #17a2b8; color: white; border-color: #17a2b8;">
                        📊 Update Stock
                    </button>
                </div>
            </div>

            <!-- Asset Settings -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">⚙️ Asset Settings</h4>
                
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Requestable</span>
                        <span class="badge {{ $asset->is_requestable ? 'bg-success' : 'bg-secondary' }}">
                            {{ $asset->is_requestable ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Requires Approval</span>
                        <span class="badge {{ $asset->requires_approval ? 'bg-warning' : 'bg-info' }}">
                            {{ $asset->requires_approval ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Asset Information -->
            <div class="content-card">
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

            @if($asset->notes)
                <div class="content-card" style="margin-top: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">📝 Notes</h4>
                    <div style="font-size: 14px; color: #666; line-height: 1.5;">{{ $asset->notes }}</div>
                </div>
            @endif
        </div>
    </div>
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

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-inactive { background: #f5f5f5; color: #666; }
.status-discontinued { background: #ffebee; color: #d32f2f; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-approved { background: #e8f5e8; color: #2e7d32; }
.status-rejected { background: #ffebee; color: #d32f2f; }

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.bg-success { background: #e8f5e8; color: #2e7d32; }
.bg-secondary { background: #f5f5f5; color: #666; }
.bg-warning { background: #fff3e0; color: #f57c00; }
.bg-info { background: #e3f2fd; color: #1976d2; }
</style>

<script>
function requestAsset(assetId) {
    const quantity = prompt('How many units would you like to request?', '1');
    if (quantity !== null && !isNaN(quantity) && quantity > 0) {
        // Implement asset request functionality
        alert(`Request for ${quantity} units submitted!`);
    }
}

function updateStock() {
    const newStock = prompt('Enter new stock quantity:', '{{ $asset->stock_quantity }}');
    if (newStock !== null && !isNaN(newStock)) {
        // Implement stock update functionality
        alert('Stock update functionality - to be implemented');
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('License key copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection