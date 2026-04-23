@extends('layouts.app')
@section('title', 'Asset Catalog')

@section('content')

{{-- Header --}}
<div class="flex justify-between items-center mb-5">
    <p class="text-sm text-gray-500">Browse available assets and add them to your request cart</p>
    <a href="{{ route('asset-requests.cart') }}" class="btn-secondary">
        &#x1F6D2; Cart
        @if($cartItemCount > 0)
            <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#1a3a5c] text-white text-xs font-bold">{{ $cartItemCount }}</span>
        @endif
    </a>
</div>

{{-- Search Filter --}}
<form method="GET" class="filter-bar">
    <div class="filter-group" style="flex:1;min-width:200px">
        <label class="ui-label">Search Assets</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name, brand, category…" class="ui-input">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn-primary">&#x1F50D; Search</button>
        @if(request()->hasAny(['search']))
        <a href="{{ route('asset-requests.catalog') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

{{-- Assets Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">Available Assets</span>
        <span class="badge badge-gray">{{ $assets->total() }} assets</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Brand</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td>
                        <div class="text-sm font-semibold text-gray-900">{{ $asset->name }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $asset->category }}</div>
                    </td>
                    <td class="text-sm text-gray-700">{{ $asset->brand }} {{ $asset->model }}</td>
                    <td>
                        @if($asset->stock_quantity > 0)
                            <span class="status-badge badge-green">&#x2713; Available</span>
                        @else
                            <span class="status-badge badge-red">&#x2717; Out of Stock</span>
                        @endif
                    </td>
                    <td>
                        @if($asset->stock_quantity > 0 && $asset->is_requestable)
                        <form action="{{ route('asset-requests.cart.add', $asset) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="quantity" value="1" min="1" max="{{ $asset->stock_quantity }}"
                                   class="ui-input w-20 text-center">
                            <button type="submit" class="btn-primary btn-sm">+ Add to Cart</button>
                        </form>
                        @else
                        <span class="status-badge badge-gray">
                            {{ $asset->stock_quantity == 0 ? 'Out of Stock' : 'Not Available' }}
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-16 text-center text-gray-400">
                        <div class="text-4xl mb-3">&#x1F4E6;</div>
                        <p class="text-sm font-medium text-gray-600 mb-1">No Assets Found</p>
                        <p class="text-xs text-gray-400">Try adjusting your search criteria or check back later.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($assets->hasPages())
    <div class="ui-card-footer justify-center">
        {{ $assets->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection
