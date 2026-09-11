@extends('layouts.app')
@section('title', 'Edit Asset')

@section('header-actions')
<a href="{{ route('assets.show', $asset) }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg> View Asset</a>
<a href="{{ route('assets.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Assets</a>
@endsection

@push('styles')
<style>
.mv-header-actions a { text-decoration: none !important; }
.as-form a[class*="btn-"] { text-decoration: none !important; }
.as-form .as-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 16px; align-items: start; }
@media (max-width: 1100px) { .as-form .as-layout { grid-template-columns: minmax(0, 1fr); } }
.as-form .as-main, .as-form .as-side { display: grid; gap: 16px; min-width: 0; align-content: start; }
.as-form .ui-card-body { padding: 16px 18px 18px; }
.as-form .as-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; }
.as-form .as-grid.is-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.as-form .as-grid .is-full { grid-column: 1 / -1; }
.as-form .as-grid + .as-grid { margin-top: 14px; }
@media (max-width: 720px) { .as-form .as-grid, .as-form .as-grid.is-3 { grid-template-columns: minmax(0, 1fr); } }
.as-form .ui-label { margin-bottom: 5px; }
.as-form .ui-input, .as-form .ui-select, .as-form .ui-textarea { font-size: 13.5px; padding: 8px 11px; }
.as-form .as-req { color: var(--mv-crit); }
.as-form .as-err { margin: 4px 0 0; font-size: 12px; color: var(--mv-crit); }
.as-form .as-hint { margin: 4px 0 0; font-size: 12px; color: var(--mv-muted); }
.as-form .as-note { margin-top: 8px; padding: 8px 11px; border-radius: 8px; font-size: 12.5px; background: var(--mv-accent-soft); color: var(--mv-accent-ink); border: 1px solid #C9D9EE; }
.as-form .as-toggles { display: grid; gap: 14px; }
.as-form .as-toggle { display: flex; align-items: flex-start; gap: 10px; margin: 0; cursor: pointer; }
.as-form .as-toggle input { width: 15px; height: 15px; margin: 2px 0 0; flex-shrink: 0; accent-color: var(--mv-accent); }
.as-form .as-toggle strong { display: block; font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
.as-form .as-toggle small { display: block; font-size: 12.5px; color: var(--mv-muted); }
.as-form .as-img { display: block; width: 100%; height: 180px; object-fit: cover; border-radius: 0 0 10px 10px; }
.as-form .as-meta { display: grid; gap: 10px; margin: 0; font-size: 13px; }
.as-form .as-meta > div { display: flex; justify-content: space-between; gap: 12px; }
.as-form .as-meta dt { font-weight: 400; color: var(--mv-muted); }
.as-form .as-meta dd { margin: 0; color: var(--mv-ink); text-align: right; font-variant-numeric: tabular-nums; }
.as-form .as-meta dd.is-strong { font-weight: 600; }
.as-form .as-foot { grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 8px; padding-top: 16px; border-top: 1px solid var(--mv-line); }
</style>
@endpush

@section('content')
<div class="as-form">

<form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data" class="as-layout">
    @csrf
    @method('PUT')

    {{-- Main Form --}}
    <div class="as-main">

        {{-- Basic Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Basic Information</h3>
            </div>
            <div class="ui-card-body">
                <div class="as-grid">
                    <div>
                        <label class="ui-label" for="asset_name">Asset Name <span class="as-req">*</span></label>
                        <input type="text" name="name" id="asset_name" value="{{ old('name', $asset->name) }}" required placeholder="e.g., Company Vehicle" class="ui-input w-full">
                        @error('name')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label" for="categorySelect">Category <span class="as-req">*</span></label>
                        <select name="category" id="categorySelect" required class="ui-select w-full">
                            <option value="">Select Category</option>
                            @foreach($assetCategories as $category)
                                <option value="{{ $category->name }}" {{ old('category', $asset->category) == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div id="brandField">
                        <label class="ui-label">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand', $asset->brand) }}" placeholder="e.g., Toyota, Dell, Apple" class="ui-input w-full">
                    </div>
                    <div id="modelField">
                        <label class="ui-label">Model</label>
                        <input type="text" name="model" value="{{ old('model', $asset->model) }}" placeholder="e.g., Corolla, Latitude 5420" class="ui-input w-full">
                    </div>
                    <div class="is-full">
                        <label class="ui-label">Description</label>
                        <textarea name="description" rows="3" placeholder="Detailed description of the asset..." class="ui-textarea w-full">{{ old('description', $asset->description) }}</textarea>
                        @error('description')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Category-Specific Fields --}}
        @include('assets.partials.dynamic-fields')

        {{-- Pricing & Inventory --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Pricing &amp; Inventory</h3>
            </div>
            <div class="ui-card-body">
                <div class="as-grid is-3">
                    <div>
                        <label class="ui-label">Unit Price <span class="as-req">*</span></label>
                        <input type="number" name="unit_price" value="{{ old('unit_price', $asset->unit_price) }}" step="0.01" min="0" required placeholder="0.00" class="ui-input w-full">
                        @error('unit_price')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Currency <span class="as-req">*</span></label>
                        <select name="currency" required class="ui-select w-full">
                            <option value="USD" {{ old('currency', $asset->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency', $asset->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency', $asset->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="ZWL" {{ old('currency', $asset->currency) == 'ZWL' ? 'selected' : '' }}>ZWL - Zimbabwe Dollar</option>
                        </select>
                    </div>
                    <div>
                        <label class="ui-label">Status <span class="as-req">*</span></label>
                        <select name="status" required class="ui-select w-full">
                            @foreach($assetStatuses as $status)
                                <option value="{{ $status->slug }}" {{ old('status', $asset->status) == $status->slug ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="as-grid">
                    <div>
                        <label class="ui-label">Stock Quantity <span class="as-req">*</span></label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $asset->stock_quantity) }}" min="0" required class="ui-input w-full">
                        @error('stock_quantity')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Min Stock Level <span class="as-req">*</span></label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $asset->min_stock_level) }}" min="0" required class="ui-input w-full">
                        @error('min_stock_level')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $asset->sku) }}" placeholder="e.g., VEH-TOY-COR-001" class="ui-input w-full mv-mono">
                        @error('sku')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $asset->barcode) }}" placeholder="Barcode number" class="ui-input w-full mv-mono">
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Information --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Additional Information</h3>
            </div>
            <div class="ui-card-body">
                <div class="as-grid">
                    <div class="is-full">
                        <label class="ui-label">Image URL</label>
                        <input type="url" name="image_url" value="{{ old('image_url', $asset->image_url) }}" placeholder="https://example.com/image.jpg" class="ui-input w-full">
                        @error('image_url')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="is-full">
                        <label class="ui-label">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Any additional notes about this asset..." class="ui-textarea w-full">{{ old('notes', $asset->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Settings Sidebar --}}
    <div class="as-side">

        {{-- Asset Image Preview --}}
        @if($asset->image_url)
            <div class="ui-card overflow-hidden">
                <div class="ui-card-header">
                    <h3>Current Image</h3>
                </div>
                <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" class="as-img">
            </div>
        @endif

        {{-- Request Settings --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Request Settings</h3>
            </div>
            <div class="ui-card-body as-toggles">
                <label class="as-toggle">
                    <input type="checkbox" name="is_requestable" value="1" {{ old('is_requestable', $asset->is_requestable) ? 'checked' : '' }}>
                    <span><strong>Available for Request</strong><small>Employees can request this asset</small></span>
                </label>
                <label class="as-toggle">
                    <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', $asset->requires_approval) ? 'checked' : '' }}>
                    <span><strong>Requires Approval</strong><small>Requests need manager approval</small></span>
                </label>
            </div>
        </div>

        {{-- Asset Info --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Asset Information</h3>
            </div>
            <div class="ui-card-body">
                <dl class="as-meta">
                    <div><dt>Created</dt><dd>{{ $asset->created_at->format('M d, Y') }}</dd></div>
                    <div><dt>Last Updated</dt><dd>{{ $asset->updated_at->format('M d, Y') }}</dd></div>
                    <div><dt>Total Value</dt><dd class="is-strong">{{ $asset->currency }} {{ number_format($asset->unit_price * $asset->stock_quantity, 2) }}</dd></div>
                </dl>
            </div>
        </div>

    </div>

    {{-- Submit --}}
    <div class="as-foot">
        <a href="{{ route('assets.show', $asset) }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg> Update Asset</button>
    </div>
</form>

</div>
@endsection
