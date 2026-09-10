@extends('layouts.app')
@section('title', 'Asset Cart')

@push('styles')
<style>
.ar-cart .rq-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 10px 16px; flex-wrap: wrap; margin-bottom: 16px; }
.ar-cart .rq-lede { margin: 0; font-size: 13.5px; color: var(--mv-muted); }
.ar-cart .rq-toolbar .btn-secondary { height: 36px; padding-top: 0; padding-bottom: 0; }
.ar-cart .rq-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 20px; align-items: start; }

.ar-cart .ui-table th.num, .ar-cart .ui-table td.num { text-align: right; }
.ar-cart .ui-table th.act, .ar-cart .ui-table td.act { width: 1%; text-align: right; }
.ar-cart .rq-item { display: flex; align-items: center; gap: 12px; min-width: 0; }
.ar-cart .rq-thumb { width: 40px; height: 40px; flex-shrink: 0; border-radius: 8px; overflow: hidden; display: grid; place-items: center; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-muted); }
.ar-cart .rq-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ar-cart .rq-qty { margin: 0; }
.ar-cart .rq-qty .ui-input { width: 72px; height: 32px; padding: 0 8px; text-align: center; font-size: 13px; font-variant-numeric: tabular-nums; }
.ar-cart .rq-strong { font-weight: 600; color: var(--mv-ink); }
.ar-cart td form { margin: 0; }

.ar-cart .rq-totals { margin: 0; display: flex; flex-direction: column; gap: 8px; }
.ar-cart .rq-totals > div { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
.ar-cart .rq-totals dt { margin: 0; font-size: 13px; font-weight: 400; color: var(--mv-muted); }
.ar-cart .rq-totals dd { margin: 0; font-size: 13.5px; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.ar-cart .rq-totals .rq-grand { margin-top: 4px; padding-top: 12px; border-top: 1px solid var(--mv-line); }
.ar-cart .rq-totals .rq-grand dt { font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
.ar-cart .rq-totals .rq-grand dd { font-size: 18px; font-weight: 600; letter-spacing: -.01em; }
.ar-cart .rq-actions { flex-direction: column; align-items: stretch; gap: 8px; }
.ar-cart .rq-actions a { justify-content: center; }

.ar-cart .rq-empty { padding: 44px 20px; text-align: center; }
.ar-cart .rq-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); margin-bottom: 8px; }
.ar-cart .rq-empty-title { margin: 0 0 2px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.ar-cart .rq-empty-text { margin: 0 0 14px; font-size: 13px; color: var(--mv-muted); }

@media (max-width: 1100px) { .ar-cart .rq-layout { grid-template-columns: minmax(0, 1fr); } }
</style>
@endpush

@section('content')
<div class="ar-cart">

    <div class="rq-toolbar">
        <p class="rq-lede">Review your selected assets before requesting</p>
        <a href="{{ route('asset-requests.catalog') }}" class="btn-secondary">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>
            Continue Request
        </a>
    </div>

    @if(count($cartItems) > 0)
    <div class="rq-layout">

        {{-- Cart Items --}}
        <section class="ui-card overflow-hidden">
            <div class="ui-card-header">
                <h2>Cart Items</h2>
                <span class="badge badge-gray">{{ count($cartItems) }} {{ count($cartItems) === 1 ? 'asset' : 'assets' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th class="num">Unit Price</th>
                            <th>Quantity</th>
                            <th class="num">Subtotal</th>
                            <th class="act"><span class="sr-only">Remove</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        <tr>
                            <td>
                                <div class="rq-item">
                                    <div class="rq-thumb">
                                        @if($item['image_url'])
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                                        @else
                                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-box"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="cell-primary">{{ $item['name'] }}</div>
                                        <div class="cell-sub">{{ $item['asset']->brand }} {{ $item['asset']->model }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="num">${{ number_format($item['unit_price'], 2) }}</td>
                            <td>
                                <form action="{{ route('asset-requests.cart.update', $item['asset_id']) }}" method="POST" class="rq-qty">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                           min="1" max="{{ $item['asset']->stock_quantity }}"
                                           class="ui-input" aria-label="Quantity for {{ $item['name'] }}"
                                           onchange="this.form.submit()">
                                </form>
                                <div class="cell-sub">Available: {{ $item['asset']->stock_quantity }}</div>
                            </td>
                            <td class="num">
                                <div class="rq-strong">${{ number_format($item['subtotal'], 2) }}</div>
                                <div class="cell-sub">{{ $item['quantity'] }} &times; ${{ number_format($item['unit_price'], 2) }}</div>
                            </td>
                            <td class="act">
                                <form action="{{ route('asset-requests.cart.remove', $item['asset_id']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete" title="Remove from cart" aria-label="Remove {{ $item['name'] }} from cart"
                                            onclick="return confirm('Remove this item from cart?')">
                                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Order Summary --}}
        <aside class="ui-card">
            <div class="ui-card-header">
                <h2>Order Summary</h2>
            </div>
            <div class="ui-card-body">
                <dl class="rq-totals">
                    <div>
                        <dt>Items ({{ array_sum(array_column($cartItems, 'quantity')) }})</dt>
                        <dd>${{ number_format($total, 2) }}</dd>
                    </div>
                    <div>
                        <dt>Estimated Tax</dt>
                        <dd>$0.00</dd>
                    </div>
                    <div class="rq-grand">
                        <dt>Total</dt>
                        <dd>${{ number_format($total, 2) }}</dd>
                    </div>
                </dl>
            </div>
            <div class="ui-card-footer rq-actions">
                <a href="{{ route('asset-requests.checkout') }}" class="btn-primary">
                    Proceed to Checkout
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                </a>
                <a href="{{ route('asset-requests.catalog') }}" class="btn-secondary">Continue Request</a>
            </div>
        </aside>

    </div>
    @else
    {{-- Empty Cart --}}
    <div class="ui-card rq-empty">
        <svg class="mv-i" aria-hidden="true"><use href="#i-cart"/></svg>
        <p class="rq-empty-title">Your cart is empty</p>
        <p class="rq-empty-text">Browse our asset catalog and add items to your cart.</p>
        <a href="{{ route('asset-requests.catalog') }}" class="btn-primary btn-sm">Start Request</a>
    </div>
    @endif

</div>
@endsection
