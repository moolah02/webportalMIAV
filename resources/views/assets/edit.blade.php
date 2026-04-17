@extends('layouts.app')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="page-title">Edit Asset</h1>
        <p class="page-subtitle">Update asset information and settings</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('assets.show', $asset) }}" class="btn-secondary btn-sm">View Asset</a>
        <a href="{{ route('assets.index') }}" class="btn-secondary btn-sm">Back to Assets</a>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="flash-success mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-error mb-4">{{ session('error') }}</div>
@endif

<form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Form --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Basic Information --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Basic Information</h3>
                </div>
                <div class="ui-card-body">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="ui-label">Asset Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $asset->name) }}" required placeholder="e.g., Company Vehicle" class="ui-input w-full">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Category <span class="text-red-500">*</span></label>
                            <select name="category" id="categorySelect" required class="ui-select w-full">
                                <option value="">Select Category</option>
                                @foreach($assetCategories as $category)
                                    <option value="{{ $category->name }}" {{ old('category', $asset->category) == $category->name ? 'selected' : '' }}>
                                        {{ $category->icon }} {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div id="brandField">
                            <label class="ui-label">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand', $asset->brand) }}" placeholder="e.g., Toyota, Dell, Apple" class="ui-input w-full">
                        </div>
                        <div id="modelField">
                            <label class="ui-label">Model</label>
                            <input type="text" name="model" value="{{ old('model', $asset->model) }}" placeholder="e.g., Corolla, Latitude 5420" class="ui-input w-full">
                        </div>
                    </div>
                    <div>
                        <label class="ui-label">Description</label>
                        <textarea name="description" rows="3" placeholder="Detailed description of the asset..." class="ui-input w-full">{{ old('description', $asset->description) }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Dynamic Category-Specific Fields --}}
            @include('assets.partials.dynamic-fields')

            {{-- Pricing & Inventory --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Pricing & Inventory</h3>
                </div>
                <div class="ui-card-body space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="ui-label">Unit Price <span class="text-red-500">*</span></label>
                            <input type="number" name="unit_price" value="{{ old('unit_price', $asset->unit_price) }}" step="0.01" min="0" required placeholder="0.00" class="ui-input w-full">
                            @error('unit_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Currency <span class="text-red-500">*</span></label>
                            <select name="currency" required class="ui-select w-full">
                                <option value="USD" {{ old('currency', $asset->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency', $asset->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency', $asset->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="ZWL" {{ old('currency', $asset->currency) == 'ZWL' ? 'selected' : '' }}>ZWL - Zimbabwe Dollar</option>
                            </select>
                        </div>
                        <div>
                            <label class="ui-label">Status <span class="text-red-500">*</span></label>
                            <select name="status" required class="ui-select w-full">
                                @foreach($assetStatuses as $status)
                                    <option value="{{ $status->slug }}" {{ old('status', $asset->status) == $status->slug ? 'selected' : '' }}>
                                        {{ $status->icon }} {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">Stock Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $asset->stock_quantity) }}" min="0" required class="ui-input w-full">
                            @error('stock_quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Min Stock Level <span class="text-red-500">*</span></label>
                            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $asset->min_stock_level) }}" min="0" required class="ui-input w-full">
                            @error('min_stock_level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $asset->sku) }}" placeholder="e.g., VEH-TOY-COR-001" class="ui-input w-full">
                            @error('sku')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $asset->barcode) }}" placeholder="Barcode number" class="ui-input w-full">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Information --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Additional Information</h3>
                </div>
                <div class="ui-card-body space-y-4">
                    <div>
                        <label class="ui-label">Image URL</label>
                        <input type="url" name="image_url" value="{{ old('image_url', $asset->image_url) }}" placeholder="https://example.com/image.jpg" class="ui-input w-full">
                        @error('image_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Any additional notes about this asset..." class="ui-input w-full">{{ old('notes', $asset->notes) }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- Settings Sidebar --}}
        <div class="space-y-5">

            {{-- Asset Image Preview --}}
            @if($asset->image_url)
                <div class="ui-card overflow-hidden">
                    <div class="ui-card-header">
                        <h3 class="text-sm font-semibold text-gray-900">Current Image</h3>
                    </div>
                    <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" class="w-full h-48 object-cover">
                </div>
            @endif

            {{-- Request Settings --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Request Settings</h3>
                </div>
                <div class="ui-card-body space-y-4">
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_requestable" value="1" {{ old('is_requestable', $asset->is_requestable) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1a3a5c]">
                            <span class="text-sm text-gray-700">Available for Request</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-5">Employees can request this asset</p>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', $asset->requires_approval) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1a3a5c]">
                            <span class="text-sm text-gray-700">Requires Approval</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-5">Requests need manager approval</p>
                    </div>
                </div>
            </div>

            {{-- Asset Info --}}
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

            {{-- Submit --}}
            <div class="ui-card">
                <div class="ui-card-body flex flex-col gap-2">
                    <button type="submit" class="btn-primary w-full text-center py-3">Update Asset</button>
                    <a href="{{ route('assets.show', $asset) }}" class="btn-secondary w-full text-center">Cancel</a>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection