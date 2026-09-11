@extends('layouts.app')
@section('title', 'Asset Details')

@push('styles')
<style>
.as-show a[class*="btn-"] { text-decoration: none !important; }
.as-show .as-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 16px; margin-bottom: 16px; }
.as-show .as-head-main { display: flex; align-items: center; gap: 12px; min-width: 0; }
.as-show .as-thumb {
  width: 40px; height: 40px; border-radius: 9px; flex-shrink: 0; display: grid; place-items: center;
  background: var(--mv-surface); border: 1px solid var(--mv-line); color: var(--mv-ink-2);
}
.as-show .as-name { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.as-show .as-name h2 { margin: 0; font-size: 17px; font-weight: 600; color: var(--mv-ink); }
.as-show .as-sub { margin-top: 2px; font-size: 12.5px; color: var(--mv-muted); }
.as-show .as-head-actions { display: flex; flex-wrap: wrap; gap: 8px; }
.as-show .as-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 16px; align-items: start; }
@media (max-width: 1100px) { .as-show .as-layout { grid-template-columns: minmax(0, 1fr); } }
.as-show .as-main, .as-show .as-side { display: grid; gap: 16px; min-width: 0; align-content: start; }
.as-show .ui-card-body { padding: 16px 18px 18px; }
.as-show .as-kv { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 20px; margin: 0; }
.as-show .as-kv.is-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.as-show .as-kv .is-full { grid-column: 1 / -1; }
@media (max-width: 720px) { .as-show .as-kv, .as-show .as-kv.is-3 { grid-template-columns: minmax(0, 1fr); } }
.as-show .as-k { margin: 0 0 3px; font-size: 12px; color: var(--mv-muted); }
.as-show .as-v { margin: 0; font-size: 13.5px; color: var(--mv-ink); overflow-wrap: anywhere; font-variant-numeric: tabular-nums; }
.as-show .as-v.is-lg { font-size: 18px; font-weight: 600; letter-spacing: -.01em; }
.as-show .as-v.is-crit { color: var(--mv-crit); }
.as-show .as-v.is-none { color: var(--mv-muted); }
.as-show .as-v .badge { margin-left: 6px; vertical-align: 2px; }
.as-show .as-desc { margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--mv-line); }
.as-show .as-desc .as-v { color: var(--mv-ink-2); line-height: 1.55; white-space: pre-line; }
.as-show .as-plate {
  display: inline-block; padding: 3px 10px; border-radius: 6px; letter-spacing: .04em;
  font-family: var(--mv-mono); font-size: 14px; font-weight: 500; color: var(--mv-ink);
  background: var(--mv-surface-2); border: 1px solid var(--mv-line-strong);
}
.as-show .as-keybox {
  display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 7px 7px 7px 12px;
  border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2);
}
.as-show .as-keybox .mv-mono { font-size: 12.5px; color: var(--mv-ink); word-break: break-all; }
.as-show .as-list > div { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 11px 18px; }
.as-show .as-list > div + div { border-top: 1px solid var(--mv-line); }
.as-show .as-list .cell-primary { font-size: 13.5px; }
.as-show .as-scroll { max-height: 18rem; overflow-y: auto; }
.as-show .as-rows { display: grid; gap: 10px; margin: 0; font-size: 13px; }
.as-show .as-rows > div { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.as-show .as-rows dt { font-weight: 400; color: var(--mv-muted); }
.as-show .as-rows dd { margin: 0; color: var(--mv-ink); text-align: right; font-variant-numeric: tabular-nums; }
.as-show .as-rows dd.is-strong { font-weight: 600; }
.as-show .as-btns { display: grid; gap: 8px; }
.as-show .as-btns [class*="btn-"] { justify-content: center; }
.as-show .as-img { display: block; width: 100%; height: 190px; object-fit: cover; }
.as-show .as-notes { margin: 0; font-size: 13.5px; line-height: 1.55; color: var(--mv-ink-2); white-space: pre-line; }
</style>
@endpush

@section('content')
@php
    $statusObj = App\Models\Category::ofType('asset_status')->where('slug', $asset->status)->first();
    $statusSlug = str_replace('asset-', '', $asset->status);
    $statusBadge = match($statusSlug) { 'active', 'available' => 'badge-green', 'inactive', 'retired' => 'badge-gray', 'discontinued', 'damaged' => 'badge-red', 'pending', 'maintenance' => 'badge-yellow', default => 'badge-gray' };
@endphp
<div class="as-show">

{{-- Header --}}
<div class="as-head">
    <div class="as-head-main">
        <span class="as-thumb" aria-hidden="true"><svg class="mv-i"><use href="#i-box"/></svg></span>
        <div class="min-w-0">
            <div class="as-name">
                <h2>{{ $asset->name }}</h2>
                @if($asset->sku)<span class="code-chip">{{ $asset->sku }}</span>@endif
                <span class="badge {{ $statusBadge }}">{{ $statusObj->name ?? ucfirst($statusSlug) }}</span>
            </div>
            <div class="as-sub">
                {{ $asset->category }}@if($asset->brand || $asset->model) &middot; {{ trim($asset->brand . ' ' . $asset->model) }}@endif
            </div>
        </div>
    </div>
    <div class="as-head-actions">
        <a href="{{ route('assets.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Assets</a>
        <a href="{{ route('assets.edit', $asset) }}" class="btn-primary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit Asset</a>
    </div>
</div>

<div class="as-layout">
    {{-- Main Content --}}
    <div class="as-main">

        {{-- Basic Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Basic Information</h3>
            </div>
            <div class="ui-card-body">
                <div class="as-kv">
                    <div>
                        <p class="as-k">Asset Name</p>
                        <p class="as-v">{{ $asset->name }}</p>
                    </div>
                    <div>
                        <p class="as-k">Category</p>
                        <p class="as-v">{{ $asset->category }}</p>
                    </div>
                    <div>
                        <p class="as-k">Brand</p>
                        <p class="as-v {{ $asset->brand ? '' : 'is-none' }}">{{ $asset->brand ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="as-k">Model</p>
                        <p class="as-v {{ $asset->model ? '' : 'is-none' }}">{{ $asset->model ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="as-k">SKU</p>
                        <p class="as-v {{ $asset->sku ? 'mv-mono' : 'is-none' }}">{{ $asset->sku ?: 'Not assigned' }}</p>
                    </div>
                    <div>
                        <p class="as-k">Status</p>
                        <p class="as-v"><span class="badge {{ $statusBadge }}" style="margin-left:0">{{ $statusObj->name ?? ucfirst($statusSlug) }}</span></p>
                    </div>
                </div>
                @if($asset->description)
                    <div class="as-desc">
                        <p class="as-k">Description</p>
                        <p class="as-v">{{ $asset->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Vehicle-Specific Information --}}
        @if($asset->category === 'Vehicles' && !empty($asset->specifications))
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3>Vehicle Details</h3>
                </div>
                <div class="ui-card-body">
                    <div class="as-kv">
                        @if(!empty($asset->specifications['license_plate']))
                            <div>
                                <p class="as-k">License Plate</p>
                                <p class="as-v"><span class="as-plate">{{ $asset->specifications['license_plate'] }}</span></p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['vin_number']))
                            <div>
                                <p class="as-k">VIN Number</p>
                                <p class="as-v mv-mono">{{ $asset->specifications['vin_number'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['engine_number']))
                            <div>
                                <p class="as-k">Engine Number</p>
                                <p class="as-v mv-mono">{{ $asset->specifications['engine_number'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['vehicle_year']))
                            <div>
                                <p class="as-k">Year</p>
                                <p class="as-v">{{ $asset->specifications['vehicle_year'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['vehicle_color']))
                            <div>
                                <p class="as-k">Color</p>
                                <p class="as-v">{{ $asset->specifications['vehicle_color'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['fuel_type']))
                            <div>
                                <p class="as-k">Fuel Type</p>
                                <p class="as-v">{{ $asset->specifications['fuel_type'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['registration_date']))
                            <div>
                                <p class="as-k">Registration Date</p>
                                <p class="as-v">{{ \Carbon\Carbon::parse($asset->specifications['registration_date'])->format('M d, Y') }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['insurance_expiry']))
                            <div>
                                <p class="as-k">Insurance Expiry</p>
                                @php $insExpiry = \Carbon\Carbon::parse($asset->specifications['insurance_expiry']); @endphp
                                <p class="as-v {{ $insExpiry->isPast() ? 'is-crit' : '' }}">
                                    {{ $insExpiry->format('M d, Y') }}
                                    @if($insExpiry->isPast()) <span class="badge badge-red">EXPIRED</span> @endif
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
                    <h3>POS Terminal Details</h3>
                </div>
                <div class="ui-card-body">
                    <div class="as-kv">
                        @if(!empty($asset->specifications['terminal_id']))
                            <div>
                                <p class="as-k">Terminal ID</p>
                                <p class="as-v"><span class="id-chip">{{ $asset->specifications['terminal_id'] }}</span></p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['software_version']))
                            <div>
                                <p class="as-k">Software Version</p>
                                <p class="as-v mv-mono">{{ $asset->specifications['software_version'] }}</p>
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
                    <h3>Computer/IT Specifications</h3>
                </div>
                <div class="ui-card-body">
                    <div class="as-kv">
                        @if(!empty($asset->specifications['processor']))
                            <div>
                                <p class="as-k">Processor</p>
                                <p class="as-v">{{ $asset->specifications['processor'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['ram']))
                            <div>
                                <p class="as-k">RAM</p>
                                <p class="as-v">{{ $asset->specifications['ram'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['storage']))
                            <div>
                                <p class="as-k">Storage</p>
                                <p class="as-v">{{ $asset->specifications['storage'] }}</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['operating_system']))
                            <div>
                                <p class="as-k">Operating System</p>
                                <p class="as-v">{{ $asset->specifications['operating_system'] }}</p>
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
                    <h3>License Details</h3>
                </div>
                <div class="ui-card-body">
                    <div class="as-kv">
                        @if(!empty($asset->specifications['license_key']))
                            <div class="is-full">
                                <p class="as-k">License Key</p>
                                <div class="as-keybox">
                                    <span class="mv-mono">{{ $asset->specifications['license_key'] }}</span>
                                    <button type="button" onclick="copyToClipboard('{{ $asset->specifications['license_key'] }}')" class="btn-secondary btn-sm shrink-0"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-copy"/></svg> Copy</button>
                                </div>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['license_expiry']))
                            <div>
                                <p class="as-k">Expiry Date</p>
                                @php $licExpiry = \Carbon\Carbon::parse($asset->specifications['license_expiry']); @endphp
                                <p class="as-v {{ $licExpiry->isPast() ? 'is-crit' : '' }}">
                                    {{ $licExpiry->format('M d, Y') }}
                                    @if($licExpiry->isPast())
                                        <span class="badge badge-red">EXPIRED</span>
                                    @elseif($licExpiry->diffInDays() <= 30)
                                        <span class="badge badge-yellow">EXPIRES SOON</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['max_users']))
                            <div>
                                <p class="as-k">Max Users</p>
                                <p class="as-v">{{ $asset->specifications['max_users'] }} users</p>
                            </div>
                        @endif
                        @if(!empty($asset->specifications['subscription_type']))
                            <div>
                                <p class="as-k">Subscription Type</p>
                                <p class="as-v">{{ $asset->specifications['subscription_type'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Pricing & Inventory --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Pricing &amp; Inventory</h3>
            </div>
            <div class="ui-card-body">
                <div class="as-kv is-3">
                    <div>
                        <p class="as-k">Unit Price</p>
                        <p class="as-v is-lg">{{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="as-k">Stock Quantity</p>
                        <p class="as-v is-lg {{ $asset->stock_quantity <= $asset->min_stock_level ? 'is-crit' : '' }}">
                            {{ $asset->stock_quantity }}
                            @if($asset->stock_quantity <= $asset->min_stock_level)
                                <span class="badge badge-red">LOW STOCK</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="as-k">Min Stock Level</p>
                        <p class="as-v is-lg">{{ $asset->min_stock_level }}</p>
                    </div>
                    @if($asset->barcode)
                        <div>
                            <p class="as-k">Barcode</p>
                            <p class="as-v mv-mono">{{ $asset->barcode }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Requests --}}
        @if($recentRequests->count() > 0)
            <div class="ui-card overflow-hidden">
                <div class="ui-card-header">
                    <h3>Recent Requests</h3>
                </div>
                <div class="as-list as-scroll">
                    @foreach($recentRequests as $requestItem)
                        @php $reqStatus = $requestItem->assetRequest->status; @endphp
                        <div>
                            <div class="min-w-0">
                                <div class="cell-primary">{{ $requestItem->assetRequest->employee->full_name }}</div>
                                <div class="cell-sub tabular">{{ $requestItem->quantity_requested }} units &middot; {{ $requestItem->assetRequest->created_at->format('M d, Y') }}</div>
                            </div>
                            <span class="badge {{ match($reqStatus) { 'approved' => 'badge-green', 'rejected' => 'badge-red', 'pending' => 'badge-yellow', default => 'badge-gray' } }}">{{ ucfirst($reqStatus) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="as-side">

        {{-- Asset Image --}}
        @if($asset->image_url)
            <div class="ui-card overflow-hidden">
                <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" class="as-img">
            </div>
        @endif

        {{-- Quick Actions --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="ui-card-body as-btns">
                <a href="{{ route('assets.edit', $asset) }}" class="btn-primary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit Asset</a>
                @if($asset->is_requestable && $asset->canBeRequested())
                    <button type="button" onclick="requestAsset({{ $asset->id }})" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-cart"/></svg> Request Asset</button>
                @endif
            </div>
        </div>

        {{-- Asset Settings --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Asset Settings</h3>
            </div>
            <div class="ui-card-body">
                <dl class="as-rows">
                    <div>
                        <dt>Requestable</dt>
                        <dd><span class="badge {{ $asset->is_requestable ? 'badge-green' : 'badge-gray' }}">{{ $asset->is_requestable ? 'Yes' : 'No' }}</span></dd>
                    </div>
                    <div>
                        <dt>Requires Approval</dt>
                        <dd><span class="badge {{ $asset->requires_approval ? 'badge-blue' : 'badge-gray' }}">{{ $asset->requires_approval ? 'Yes' : 'No' }}</span></dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Asset Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Asset Information</h3>
            </div>
            <div class="ui-card-body">
                <dl class="as-rows">
                    <div><dt>Created</dt><dd>{{ $asset->created_at->format('M d, Y') }}</dd></div>
                    <div><dt>Last Updated</dt><dd>{{ $asset->updated_at->format('M d, Y') }}</dd></div>
                    <div><dt>Total Value</dt><dd class="is-strong">{{ $asset->currency }} {{ number_format($asset->unit_price * $asset->stock_quantity, 2) }}</dd></div>
                </dl>
            </div>
        </div>

        {{-- Notes --}}
        @if($asset->notes)
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3>Notes</h3>
                </div>
                <div class="ui-card-body">
                    <p class="as-notes">{{ $asset->notes }}</p>
                </div>
            </div>
        @endif

    </div>
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
