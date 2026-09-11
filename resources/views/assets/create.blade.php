@extends('layouts.app')
@section('title', 'Add Asset')

@section('header-actions')
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
.as-form .as-grid .is-full { grid-column: 1 / -1; }
@media (max-width: 720px) { .as-form .as-grid { grid-template-columns: minmax(0, 1fr); } }
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
.as-form .as-foot { grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 8px; padding-top: 16px; border-top: 1px solid var(--mv-line); }
.as-form .as-errors { align-items: flex-start; }
.as-form .as-errors ul { margin: 4px 0 0 18px; padding: 0; }
</style>
@endpush

@section('content')
<div class="as-form">

@if($errors->any())
    <div class="flash-error as-errors">
        <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
        <div>
            <strong>Validation Errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data" class="as-layout">
    @csrf

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
                        <input type="text" name="name" id="asset_name" value="{{ old('name') }}" required placeholder="e.g., Company Vehicle" class="ui-input w-full">
                        @error('name')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label" for="categorySelect">Category <span class="as-req">*</span></label>
                        <select name="category" id="categorySelect" required class="ui-select w-full">
                            <option value="">Select Category</option>
                            @foreach($assetCategories as $category)
                                <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div id="brandField">
                        <label class="ui-label">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" placeholder="e.g., Toyota, Dell, Apple" class="ui-input w-full">
                    </div>
                    <div id="modelField">
                        <label class="ui-label">Model</label>
                        <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g., Corolla, Latitude 5420" class="ui-input w-full">
                    </div>
                    <div class="is-full">
                        <label class="ui-label">Description</label>
                        <textarea name="description" rows="3" placeholder="Detailed description of the asset..." class="ui-textarea w-full">{{ old('description') }}</textarea>
                        @error('description')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Category-Specific Fields --}}
        @include('assets.partials.dynamic-fields')

        {{-- Inventory & Status --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Inventory &amp; Status</h3>
            </div>
            <div class="ui-card-body">
                <div class="as-grid">
                    <div>
                        <label class="ui-label">Stock Quantity <span class="as-req">*</span></label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 1) }}" required min="0" placeholder="e.g., 1" class="ui-input w-full">
                        @error('stock_quantity')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Min Stock Level <span class="as-req">*</span></label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 0) }}" required min="0" placeholder="e.g., 0" class="ui-input w-full">
                        @error('min_stock_level')<p class="as-err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Status <span class="as-req">*</span></label>
                        <select name="status" required class="ui-select w-full">
                            @foreach($assetStatuses as $status)
                                <option value="{{ $status->slug }}" {{ old('status', 'asset-active') == $status->slug ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="unit_price" value="0">
        <input type="hidden" name="currency" value="USD">
    </div>

    {{-- Settings Sidebar --}}
    <div class="as-side">

        {{-- Request Settings --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3>Request Settings</h3>
            </div>
            <div class="ui-card-body as-toggles">
                <label class="as-toggle">
                    <input type="checkbox" name="is_requestable" value="1" {{ old('is_requestable', true) ? 'checked' : '' }}>
                    <span><strong>Available for Request</strong><small>Employees can request this asset</small></span>
                </label>
                <label class="as-toggle">
                    <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', true) ? 'checked' : '' }}>
                    <span><strong>Requires Approval</strong><small>Requests need manager approval</small></span>
                </label>
            </div>
        </div>

    </div>

    {{-- Submit --}}
    <div class="as-foot">
        <a href="{{ route('assets.index') }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg> Create Asset</button>
    </div>
</form>

</div>
@endsection
