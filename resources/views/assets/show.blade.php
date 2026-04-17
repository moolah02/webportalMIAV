@extends('layouts.app')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="page-title">{{ $asset->name }}</h1>
        <p class="page-subtitle">Asset Details and Information</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('assets.edit', $asset) }}" class="btn-primary btn-sm">Edit Asset</a>
        <a href="{{ route('assets.index') }}" class="btn-secondary btn-sm">Back to Assets</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Basic Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-900">Basic Information</h3>
            </div>
            <div class="ui-card-body">
                <div class="grid grid-cols-2 gap-5 mb-5">
                    <div>
                        <p class="ui-label">Asset Name</p>
                        <p class="text-sm text-gray-900">{{ $asset->name }}</p>
                    </div>
                    <div>
                        <p class="ui-label">Category</p>
                        @php $categoryObj = App\Models\Category::ofType('asset_category')->where('name', $asset->category)->first(); @endphp
                        <p class="text-sm text-gray-900">{{ $categoryObj->icon ?? '' }} {{ $asset->category }}</p>
                    </div>
                    <div>
                        <p class="ui-label">Brand</p>
                        <p class="text-sm text-gray-900">{{ $asset->brand ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="ui-label">Model</p>
                        <p class="text-sm text-gray-900">{{ $asset->model ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="ui-label">SKU</p>
                        <p class="text-sm text-gray-900 font-mono">{{ $asset->sku ?: 'Not assigned' }}</p>
                    </div>
                    <div>
                        <p class="ui-label">Status</p>
                        @php
                            $statusObj = App\Models\Category::ofType('asset_status')->where('slug', $asset->status)->first();
                            $statusSlug = str_replace('asset-', '', $asset->status);
                        @endphp
                        <span class="badge {{ match($statusSlug) { 'active' => 'badge-green', 'inactive' => 'badge-gray', 'discontinued' => 'badge-red', 'pending' => 'badge-yellow', default => 'badge-gray' } }}">
                            {{ $statusObj->icon ?? '' }} {{ $statusObj->name ?? ucfirst($statusSlug) }}
                        </span>
                    </div>
                </div>
                @if($asset->description)
                    <div>
                        <p class="ui-label">Description</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $asset->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Vehicle-Specific Information --}}
        @if($asset->category === 'Vehicles' && !empty($asset->specifications))
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Vehicle Details</h3>
                </div>
                <div class="ui-card-body">
                    <div class="grid grid-cols-2 gap-5">
                        @if(!empty($asset->specifications['license_plate']))
                            <div>
                                <p class="ui-label">License Plate</p>
                                <p class="text-base font-bold text-gray-900 bg-gray-50 px-3 py-2 rounded-lg text-center font-mono">{{ $asset->specifications['license_plate'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['vin_number']))
                            <div>
                                <p class="ui-label">VIN Number</p>
                                <p class="text-sm text-gray-900 font-mono">{{ $asset->specifications['vin_number'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['engine_number']))
                            <div>
                                <p class="ui-label">Engine Number</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['engine_number'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['vehicle_year']))
                            <div>
                                <p class="ui-label">Year</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['vehicle_year'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['vehicle_color']))
                            <div>
                                <p class="ui-label">Color</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['vehicle_color'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['fuel_type']))
                            <div>
                                <p class="ui-label">Fuel Type</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['fuel_type'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['registration_date']))
                            <div>
                                <p class="ui-label">Registration Date</p>
                                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($asset->specifications['registration_date'])->format('M d, Y') }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['insurance_expiry']))
                            <div>
                                <p class="ui-label">Insurance Expiry</p>
                                @php $insExpiry = \Carbon\Carbon::parse($asset->specifications['insurance_expiry']); @endphp
                                <p class="text-sm {{ $insExpiry->isPast() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                    {{ $insExpiry->format('M d, Y') }}
                                    @if($insExpiry->isPast()) <span class="badge badge-red ml-1">EXPIRED</span> @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- POS Terminal Information --}}
        @if($asset->category === 'POS Terminals' && !empty($asset->specifications))
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">POS Terminal Details</h3>
                </div>
                <div class="ui-card-body">
                    <div class="grid grid-cols-2 gap-5">
                        @if(!empty($asset->specifications['terminal_id']))
                            <div>
                                <p class="ui-label">Terminal ID</p>
                                <p class="text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded-lg">{{ $asset->specifications['terminal_id'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['software_version']))
                            <div>
                                <p class="ui-label">Software Version</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['software_version'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Computer/IT Equipment Information --}}
        @if($asset->category === 'Computer and IT Equipment' && !empty($asset->specifications))
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Computer/IT Specifications</h3>
                </div>
                <div class="ui-card-body">
                    <div class="grid grid-cols-2 gap-5">
                        @if(!empty($asset->specifications['processor']))
                            <div>
                                <p class="ui-label">Processor</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['processor'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['ram']))
                            <div>
                                <p class="ui-label">RAM</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['ram'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['storage']))
                            <div>
                                <p class="ui-label">Storage</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['storage'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['operating_system']))
                            <div>
                                <p class="ui-label">Operating System</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['operating_system'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- License Information --}}
        @if($asset->category === 'Licenses' && !empty($asset->specifications))
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">License Details</h3>
                </div>
                <div class="ui-card-body">
                    <div class="grid grid-cols-2 gap-5">
                        @if(!empty($asset->specifications['license_key']))
                            <div class="col-span-2">
                                <p class="ui-label">License Key</p>
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2 gap-3">
                                    <span class="text-xs font-mono text-gray-900 break-all">{{ $asset->specifications['license_key'] }}</span>
                                    <button onclick="copyToClipboard('{{ $asset->specifications['license_key'] }}')" class="btn-primary btn-sm shrink-0">Copy</button>
                                </div>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['license_expiry']))
                            <div>
                                <p class="ui-label">Expiry Date</p>
                                @php $licExpiry = \Carbon\Carbon::parse($asset->specifications['license_expiry']); @endphp
                                <p class="text-sm {{ $licExpiry->isPast() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                    {{ $licExpiry->format('M d, Y') }}
                                    @if($licExpiry->isPast())
                                        <span class="badge badge-red ml-1">EXPIRED</span>
                                    @elseif($licExpiry->diffInDays() <= 30)
                                        <span class="badge badge-yellow ml-1">EXPIRES SOON</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['max_users']))
                            <div>
                                <p class="ui-label">Max Users</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['max_users'] }} users</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['subscription_type']))
                            <div>
                                <p class="ui-label">Subscription Type</p>
                                <p class="text-sm text-gray-900">{{ $asset->specifications['subscription_type'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Pricing & Inventory --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-900">Pricing & Inventory</h3>
            </div>
            <div class="ui-card-body">
                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <p class="ui-label">Unit Price</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="ui-label">Stock Quantity</p>
                        <p class="text-2xl font-bold {{ $asset->stock_quantity <= $asset->min_stock_level ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $asset->stock_quantity }}
                            @if($asset->stock_quantity <= $asset->min_stock_level)
                                <span class="badge badge-red text-xs ml-1">LOW STOCK</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="ui-label">Min Stock Level</p>
                        <p class="text-xl text-gray-600">{{ $asset->min_stock_level }}</p>
                    </div>
                    @if($asset->barcode)
                        <div>
                            <p class="ui-label">Barcode</p>
                            <p class="text-sm text-gray-900 font-mono">{{ $asset->barcode }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Requests --}}
        @if($recentRequests->count() > 0)
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Recent Requests</h3>
                </div>
                <div class="overflow-y-auto max-h-72">
                    @foreach($recentRequests as $requestItem)
                        @php $reqStatus = $requestItem->assetRequest->status; @endphp
                        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $requestItem->assetRequest->employee->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $requestItem->quantity_requested }} units &bull; {{ $requestItem->assetRequest->created_at->format('M d, Y') }}</p>
                            </div>
                            <span class="badge {{ match($reqStatus) { 'approved' => 'badge-green', 'rejected' => 'badge-red', 'pending' => 'badge-yellow', default => 'badge-gray' } }}">{{ ucfirst($reqStatus) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">

        {{-- Asset Image --}}
        @if($asset->image_url)
            <div class="ui-card overflow-hidden">
                <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" class="w-full h-48 object-cover">
            </div>
        @endif

        {{-- Quick Actions --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="ui-card-body flex flex-col gap-2">
                <a href="{{ route('assets.edit', $asset) }}" class="btn-primary btn-sm text-center">Edit Asset</a>
                @if($asset->is_requestable && $asset->canBeRequested())
                    <button onclick="requestAsset({{ $asset->id }})" class="btn-success btn-sm">Request Asset</button>
                @endif
            </div>
        </div>

        {{-- Asset Settings --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-900">Asset Settings</h3>
            </div>
            <div class="ui-card-body space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Requestable</span>
                    <span class="badge {{ $asset->is_requestable ? 'badge-green' : 'badge-gray' }}">{{ $asset->is_requestable ? 'Yes' : 'No' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Requires Approval</span>
                    <span class="badge {{ $asset->requires_approval ? 'badge-yellow' : 'badge-blue' }}">{{ $asset->requires_approval ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        </div>

        {{-- Asset Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-900">Asset Information</h3>
            </div>
            <div class="ui-card-body space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Created</span>
                    <span class="text-gray-900">{{ $asset->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Last Updated</span>
                    <span class="text-gray-900">{{ $asset->updated_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Value</span>
                    <span class="font-semibold text-gray-900">{{ $asset->currency }} {{ number_format($asset->unit_price * $asset->stock_quantity, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($asset->notes)
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Notes</h3>
                </div>
                <div class="ui-card-body">
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $asset->notes }}</p>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
function requestAsset(assetId) {
    const quantity = prompt('How many units would you like to request?', '1');
    if (quantity !== null && !isNaN(quantity) && quantity > 0) {
        alert(`Request for ${quantity} units submitted!`);
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