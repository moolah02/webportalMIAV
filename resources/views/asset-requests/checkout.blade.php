@extends('layouts.app')
@section('title', 'Asset Checkout')

@push('styles')
<style>
.ar-checkout .rq-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 10px 16px; flex-wrap: wrap; margin-bottom: 16px; }
.ar-checkout .rq-lede { margin: 0; font-size: 13.5px; color: var(--mv-muted); }
.ar-checkout .rq-toolbar .btn-secondary { height: 36px; padding-top: 0; padding-bottom: 0; }
.ar-checkout form.rq-form { margin: 0; }
.ar-checkout .rq-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 20px; align-items: start; }
.ar-checkout .rq-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }

.ar-checkout .rq-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 18px; padding: 18px; }
.ar-checkout .rq-field { min-width: 0; }
.ar-checkout .rq-span-2 { grid-column: 1 / -1; }
.ar-checkout .rq-field .ui-label { margin-bottom: 6px; }
.ar-checkout .rq-field .ui-input, .ar-checkout .rq-field .ui-select { height: 38px; padding-top: 0; padding-bottom: 0; font-size: 13.5px; }
.ar-checkout .rq-field .ui-textarea { font-size: 13.5px; }
.ar-checkout .rq-req { color: var(--mv-crit); }
.ar-checkout .rq-error { margin: 5px 0 0; font-size: 12.5px; color: var(--mv-crit); }

.ar-checkout .ui-table th.num, .ar-checkout .ui-table td.num { text-align: right; }
.ar-checkout .rq-item { display: flex; align-items: center; gap: 12px; min-width: 0; }
.ar-checkout .rq-thumb { width: 36px; height: 36px; flex-shrink: 0; border-radius: 8px; overflow: hidden; display: grid; place-items: center; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-muted); }
.ar-checkout .rq-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ar-checkout .ui-table tfoot td { padding: 12px 14px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); font-size: 13.5px; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.ar-checkout .ui-table tfoot .rq-total { font-size: 15px; font-weight: 600; }

.ar-checkout .rq-person { display: flex; align-items: center; gap: 10px; }
.ar-checkout .rq-avatar { width: 34px; height: 34px; flex-shrink: 0; border-radius: 50%; display: grid; place-items: center; background: var(--mv-accent-soft); color: var(--mv-accent-ink); font-size: 13px; font-weight: 600; }
.ar-checkout .rq-person-name { font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
.ar-checkout .rq-person-sub { font-size: 12.5px; color: var(--mv-muted); }
.ar-checkout .rq-contact { display: flex; align-items: center; gap: 6px; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--mv-line); font-size: 13px; color: var(--mv-ink-2); word-break: break-all; }
.ar-checkout .rq-contact .mv-i { color: var(--mv-muted); }

.ar-checkout .rq-totals { margin: 0; display: flex; flex-direction: column; gap: 8px; }
.ar-checkout .rq-totals > div { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
.ar-checkout .rq-totals dt { margin: 0; font-size: 13px; font-weight: 400; color: var(--mv-muted); }
.ar-checkout .rq-totals dd { margin: 0; font-size: 13.5px; font-weight: 500; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.ar-checkout .rq-note { margin: 14px 0 0; padding-top: 12px; border-top: 1px solid var(--mv-line); font-size: 12.5px; line-height: 1.5; color: var(--mv-muted); }
.ar-checkout .rq-note strong { font-weight: 600; color: var(--mv-ink-2); }
.ar-checkout .rq-actions { flex-direction: column; align-items: stretch; gap: 8px; }
.ar-checkout .rq-actions .btn-primary, .ar-checkout .rq-actions .btn-secondary { justify-content: center; }

@media (max-width: 1100px) { .ar-checkout .rq-layout { grid-template-columns: minmax(0, 1fr); } }
@media (max-width: 640px) { .ar-checkout .rq-fields { grid-template-columns: minmax(0, 1fr); } }
</style>
@endpush

@section('content')
<div class="ar-checkout">

    <div class="rq-toolbar">
        <p class="rq-lede">Provide details for your asset request</p>
        <a href="{{ route('asset-requests.cart') }}" class="btn-secondary">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>
            Back to Cart
        </a>
    </div>

    <form action="{{ route('asset-requests.store') }}" method="POST" class="rq-form">
        @csrf

        <div class="rq-layout">
            {{-- Main Form --}}
            <div class="rq-col">

                {{-- Request Details --}}
                <section class="ui-card">
                    <div class="ui-card-header">
                        <h2>Request Information</h2>
                    </div>
                    <div class="rq-fields">
                        <div class="rq-field rq-span-2">
                            <label for="business_justification" class="ui-label">Business Justification <span class="rq-req">*</span></label>
                            <textarea id="business_justification" name="business_justification" rows="4" required
                                      placeholder="Please explain why you need these assets and how they will be used..."
                                      class="ui-textarea">{{ old('business_justification') }}</textarea>
                            @error('business_justification')
                                <p class="rq-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rq-field">
                            <label for="priority" class="ui-label">Priority <span class="rq-req">*</span></label>
                            <select id="priority" name="priority" required class="ui-select">
                                <option value="">Select Priority</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - Standard processing</option>
                                <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal - Regular business need</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Important for business</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent - Critical business need</option>
                            </select>
                            @error('priority')
                                <p class="rq-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rq-field">
                            <label for="needed_by_date" class="ui-label">Needed By Date</label>
                            <input type="date" id="needed_by_date" name="needed_by_date" value="{{ old('needed_by_date') }}"
                                   min="{{ date('Y-m-d') }}" class="ui-input">
                            @error('needed_by_date')
                                <p class="rq-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rq-field rq-span-2">
                            <label for="delivery_instructions" class="ui-label">Delivery Instructions</label>
                            <textarea id="delivery_instructions" name="delivery_instructions" rows="3"
                                      placeholder="Any special delivery instructions or preferred delivery location..."
                                      class="ui-textarea">{{ old('delivery_instructions') }}</textarea>
                            @error('delivery_instructions')
                                <p class="rq-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- Request Items Review --}}
                <section class="ui-card overflow-hidden">
                    <div class="ui-card-header">
                        <h2>Items in Your Request</h2>
                        <span class="badge badge-gray">{{ count($cartItems) }} {{ count($cartItems) === 1 ? 'asset' : 'assets' }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th>Asset</th>
                                    <th class="num">Quantity</th>
                                    <th class="num">Unit Price</th>
                                    <th class="num">Subtotal</th>
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
                                    <td class="num">{{ $item['quantity'] }}</td>
                                    <td class="num">${{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="num"><span class="cell-primary">${{ number_format($item['subtotal'], 2) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="num">Total Estimated Cost</td>
                                    <td class="num rq-total">${{ number_format($total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>

            {{-- Sidebar --}}
            <aside class="rq-col">

                {{-- Requester Info --}}
                <section class="ui-card">
                    <div class="ui-card-header">
                        <h2>Requester Information</h2>
                    </div>
                    <div class="ui-card-body">
                        <div class="rq-person">
                            <span class="rq-avatar" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()->full_name ?? '', 0, 1)) }}</span>
                            <div class="min-w-0">
                                <div class="rq-person-name">{{ auth()->user()->full_name }}</div>
                                <div class="rq-person-sub">{{ auth()->user()->roles->first()->name ?? 'Employee' }}</div>
                                @if(auth()->user()->department)
                                    <div class="rq-person-sub">{{ is_object(auth()->user()->department) ? auth()->user()->department->name : auth()->user()->department }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="rq-contact">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-mail"/></svg>
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                </section>

                {{-- Request Summary + Submit --}}
                <section class="ui-card">
                    <div class="ui-card-header">
                        <h2>Request Summary</h2>
                    </div>
                    <div class="ui-card-body">
                        <dl class="rq-totals">
                            <div>
                                <dt>Total Items</dt>
                                <dd>{{ array_sum(array_column($cartItems, 'quantity')) }}</dd>
                            </div>
                            <div>
                                <dt>Estimated Cost</dt>
                                <dd>${{ number_format($total, 2) }}</dd>
                            </div>
                        </dl>
                        <p class="rq-note">
                            <strong>Note:</strong> This request will be reviewed by your manager before approval.
                            You'll receive email notifications about status updates.
                        </p>
                    </div>
                    <div class="ui-card-footer rq-actions">
                        <button type="submit" class="btn-primary">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-send"/></svg>
                            Submit Request
                        </button>
                        <a href="{{ route('asset-requests.cart') }}" class="btn-secondary">Back to Cart</a>
                    </div>
                </section>
            </aside>
        </div>
    </form>

</div>
@endsection
