@extends('layouts.app')
@section('title', 'Asset Catalog')

@push('styles')
<style>
.ar-catalog .rq-toolbar { display: flex; align-items: center; gap: 10px 12px; flex-wrap: wrap; margin-bottom: 18px; }
.ar-catalog .rq-search { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 260px; max-width: 620px; margin: 0; }
.ar-catalog .rq-search-field { position: relative; flex: 1; min-width: 0; }
.ar-catalog .rq-search-field .mv-i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--mv-muted); pointer-events: none; }
.ar-catalog .rq-search-field .ui-input { height: 36px; padding: 0 12px 0 34px; font-size: 13.5px; }
.ar-catalog .rq-toolbar .btn-primary, .ar-catalog .rq-toolbar .btn-secondary { height: 36px; padding-top: 0; padding-bottom: 0; }
.ar-catalog .rq-toolbar-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }
.ar-catalog .rq-count { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; margin-left: 2px; border-radius: 10px; background: var(--mv-accent); color: #FFFFFF; font-size: 11.5px; font-weight: 600; font-variant-numeric: tabular-nums; }

.ar-catalog .rq-meta { display: flex; align-items: baseline; justify-content: space-between; gap: 6px 16px; flex-wrap: wrap; margin-bottom: 12px; }
.ar-catalog .rq-meta h2 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
.ar-catalog .rq-meta h2 span { margin-left: 6px; font-size: 13px; font-weight: 400; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.ar-catalog .rq-meta p { margin: 0; font-size: 13px; color: var(--mv-muted); }

.ar-catalog .rq-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(236px, 1fr)); gap: 14px; }
.ar-catalog .rq-product { display: flex; flex-direction: column; min-width: 0; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; transition: border-color .15s ease; }
.ar-catalog .rq-product:hover { border-color: var(--mv-line-strong); }
.ar-catalog .rq-media { height: 124px; display: grid; place-items: center; background: var(--mv-surface-2); border-bottom: 1px solid var(--mv-line); color: var(--mv-line-strong); }
.ar-catalog .rq-media img { width: 100%; height: 100%; object-fit: contain; padding: 10px; }
.ar-catalog .rq-media .mv-i { width: 26px; height: 26px; }
.ar-catalog .rq-body { flex: 1; display: flex; flex-direction: column; gap: 4px; padding: 12px 14px 12px; }
.ar-catalog .rq-body .badge { align-self: flex-start; margin-bottom: 4px; }
.ar-catalog .rq-name { margin: 0; font-size: 14px; font-weight: 600; line-height: 1.35; color: var(--mv-ink); }
.ar-catalog .rq-sub { font-size: 12.5px; color: var(--mv-muted); }
.ar-catalog .rq-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: auto; padding-top: 10px; }
.ar-catalog .rq-price { font-size: 15px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.ar-catalog .rq-stock { font-size: 12.5px; color: var(--mv-ink-2); font-variant-numeric: tabular-nums; white-space: nowrap; }
.ar-catalog .rq-foot { padding: 10px 14px; border-top: 1px solid var(--mv-line); }
.ar-catalog .rq-add { display: flex; align-items: center; gap: 8px; margin: 0; }
.ar-catalog .rq-add .ui-input { width: 62px; height: 32px; padding: 0 6px; text-align: center; font-size: 13px; font-variant-numeric: tabular-nums; }
.ar-catalog .rq-add .btn-primary { flex: 1; justify-content: center; height: 32px; padding-top: 0; padding-bottom: 0; font-size: 13px; }
.ar-catalog .rq-unavailable { display: flex; align-items: center; gap: 6px; height: 32px; font-size: 12.5px; color: var(--mv-muted); }

.ar-catalog .rq-empty { padding: 44px 20px; text-align: center; }
.ar-catalog .rq-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); margin-bottom: 8px; }
.ar-catalog .rq-empty-title { margin: 0 0 2px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.ar-catalog .rq-empty-text { margin: 0 0 14px; font-size: 13px; color: var(--mv-muted); }
.ar-catalog .rq-pager { margin-top: 18px; }
</style>
@endpush

@section('content')
<div class="ar-catalog">

    {{-- Toolbar: search left, cart right --}}
    <div class="rq-toolbar">
        <form method="GET" class="rq-search" role="search">
            <label for="catalogSearch" class="sr-only">Search Assets</label>
            <div class="rq-search-field">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-search"/></svg>
                <input type="text" id="catalogSearch" name="search" value="{{ request('search') }}"
                       placeholder="Search by name, brand, category…" class="ui-input">
            </div>
            <button type="submit" class="btn-primary">Search</button>
            @if(request()->hasAny(['search']))
            <a href="{{ route('asset-requests.catalog') }}" class="btn-secondary">Clear</a>
            @endif
        </form>

        <div class="rq-toolbar-right">
            <a href="{{ route('asset-requests.cart') }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-cart"/></svg>
                Cart
                @if($cartItemCount > 0)
                    <span class="rq-count">{{ $cartItemCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <div class="rq-meta">
        <h2>Available Assets<span>{{ $assets->total() }} assets</span></h2>
        <p>Browse available assets and add them to your request cart</p>
    </div>

    @if($assets->count() > 0)
    <div class="rq-grid">
        @foreach($assets as $asset)
        <article class="rq-product">
            <div class="rq-media">
                @if($asset->image_url)
                    <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" loading="lazy">
                @else
                    <svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg>
                @endif
            </div>

            <div class="rq-body">
                @if($asset->category)
                <span class="badge badge-gray">{{ $asset->category }}</span>
                @endif
                <h3 class="rq-name">{{ $asset->name }}</h3>
                @if($asset->brand || $asset->model)
                <div class="rq-sub">{{ trim($asset->brand . ' ' . $asset->model) }}</div>
                @endif

                <div class="rq-row">
                    <span class="rq-price">${{ number_format((float) $asset->unit_price, 2) }}</span>
                    @if($asset->stock_quantity <= 0 || $asset->isLowStock())
                        <span class="badge badge-yellow">{{ $asset->stock_quantity }} in stock</span>
                    @else
                        <span class="rq-stock">{{ $asset->stock_quantity }} in stock</span>
                    @endif
                </div>
            </div>

            <div class="rq-foot">
                @if($asset->stock_quantity > 0 && $asset->is_requestable)
                <form action="{{ route('asset-requests.cart.add', $asset) }}" method="POST" class="rq-add">
                    @csrf
                    <label for="qty-{{ $asset->id }}" class="sr-only">Quantity</label>
                    <input type="number" id="qty-{{ $asset->id }}" name="quantity" value="1" min="1" max="{{ $asset->stock_quantity }}"
                           class="ui-input">
                    <button type="submit" class="btn-primary btn-sm">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg>
                        Add to Cart
                    </button>
                </form>
                @else
                <span class="rq-unavailable">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-ban"/></svg>
                    {{ $asset->stock_quantity == 0 ? 'Out of Stock' : 'Not Available' }}
                </span>
                @endif
            </div>
        </article>
        @endforeach
    </div>
    @else
    <div class="ui-card rq-empty">
        <svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg>
        <p class="rq-empty-title">No Assets Found</p>
        <p class="rq-empty-text">Try adjusting your search criteria or check back later.</p>
        @if(request()->filled('search'))
            <a href="{{ route('asset-requests.catalog') }}" class="btn-secondary btn-sm">Clear search</a>
        @else
            <a href="{{ route('asset-requests.index') }}" class="btn-secondary btn-sm">My Requests</a>
        @endif
    </div>
    @endif

    @if($assets->hasPages())
    <div class="rq-pager">
        {{ $assets->appends(request()->query())->links() }}
    </div>
    @endif

</div>
@endsection
