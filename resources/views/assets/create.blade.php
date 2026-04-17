@extends('layouts.app')
@section('title', 'Add Asset')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="page-title">Add New Asset</h1>
        <p class="page-subtitle">Add a new asset to the company inventory</p>
    </div>
    <a href="{{ route('assets.index') }}" class="btn-secondary btn-sm">Back to Assets</a>
</div>

{{-- Flash messages --}}
@if(session('error'))
    <div class="flash-error mb-4">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="flash-success mb-4">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="flash-error mb-4">
        <strong>Validation Errors:</strong>
        <ul class="mt-1 ml-4 list-disc">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
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
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Company Vehicle" class="ui-input w-full">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Category <span class="text-red-500">*</span></label>
                            <select name="category" id="categorySelect" required class="ui-select w-full">
                                <option value="">Select Category</option>
                                @foreach($assetCategories as $category)
                                    <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->icon }} {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div id="brandField">
                            <label class="ui-label">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand') }}" placeholder="e.g., Toyota, Dell, Apple" class="ui-input w-full">
                        </div>
                        <div id="modelField">
                            <label class="ui-label">Model</label>
                            <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g., Corolla, Latitude 5420" class="ui-input w-full">
                        </div>
                    </div>
                    <div>
                        <label class="ui-label">Description</label>
                        <textarea name="description" rows="3" placeholder="Detailed description of the asset..." class="ui-input w-full">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Dynamic Category-Specific Fields --}}
            @include('assets.partials.dynamic-fields')

            {{-- Inventory & Status --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Inventory & Status</h3>
                </div>
                <div class="ui-card-body space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">Stock Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 1) }}" required min="0" placeholder="e.g., 1" class="ui-input w-full">
                            @error('stock_quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Min Stock Level <span class="text-red-500">*</span></label>
                            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 0) }}" required min="0" placeholder="e.g., 0" class="ui-input w-full">
                            @error('min_stock_level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="ui-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="ui-select w-full">
                            @foreach($assetStatuses as $status)
                                <option value="{{ $status->slug }}" {{ old('status', 'asset-active') == $status->slug ? 'selected' : '' }}>
                                    {{ $status->icon }} {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <input type="hidden" name="unit_price" value="0">
            <input type="hidden" name="currency" value="USD">
        </div>

        {{-- Settings Sidebar --}}
        <div class="space-y-5">

            {{-- Request Settings --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3 class="text-sm font-semibold text-gray-900">Request Settings</h3>
                </div>
                <div class="ui-card-body space-y-4">
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_requestable" value="1" {{ old('is_requestable', true) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1a3a5c]">
                            <span class="text-sm text-gray-700">Available for Request</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-5">Employees can request this asset</p>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', true) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1a3a5c]">
                            <span class="text-sm text-gray-700">Requires Approval</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-5">Requests need manager approval</p>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="ui-card">
                <div class="ui-card-body flex flex-col gap-2">
                    <button type="submit" class="btn-primary w-full text-center py-3">Create Asset</button>
                    <a href="{{ route('assets.index') }}" class="btn-secondary w-full text-center">Cancel</a>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection