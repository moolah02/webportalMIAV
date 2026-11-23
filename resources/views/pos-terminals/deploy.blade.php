@extends('layouts.app')
@section('title', 'Send out — '.$posTerminal->terminal_id)

@push('styles')
<style>
.td-page { max-width: 820px; }
.td-lead { margin:4px 0 0; font-size:13.5px; color:var(--mv-ink-2); }
.td-bar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.td-stack { display:flex; flex-direction:column; gap:16px; }
.td-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px 20px; padding:16px 18px; }
@media (max-width:700px){ .td-grid { grid-template-columns:1fr; } }
.td-span { grid-column:1/-1; }
.td-label { font-size:12px; font-weight:500; color:var(--mv-muted); margin-bottom:5px; display:block; }
.td-req { color:var(--mv-crit); }
.td-err { margin:4px 0 0; font-size:12px; color:var(--mv-crit); }
.td-note { margin:0 18px 16px; padding:11px 14px; font-size:13px; color:var(--mv-ink-2); background:var(--mv-surface-2); border:1px solid var(--mv-line); border-radius:9px; line-height:1.55; }
.td-prev { display:flex; align-items:center; gap:10px; flex-wrap:wrap; padding:11px 16px; margin:0 18px 0; font-size:13px; color:var(--mv-ink-2); background:var(--mv-surface); border:1px solid var(--mv-line); border-radius:9px; }
.td-actions { display:flex; justify-content:flex-end; gap:8px; }
.td-tid { font-family:var(--mv-mono); font-weight:600; color:var(--mv-ink); }
</style>
@endpush

@section('content')
<div class="mv-page td-page">

    <div class="td-bar">
        <div>
            <h1 class="page-title">Send <span class="td-tid">{{ $posTerminal->terminal_id }}</span> out to a site</h1>
            <p class="td-lead">The same device record goes out again — nothing is duplicated.</p>
        </div>
        <a href="{{ route('pos-terminals.show', $posTerminal) }}" class="btn-secondary">Cancel</a>
    </div>

    @if(session('error'))
        <div class="flash-error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
    <div class="flash-error">
        <div>
            <strong>Please fix the following:</strong>
            <ul>@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('pos-terminals.deploy', $posTerminal) }}" class="td-stack">
        @csrf

        <section class="ui-card">
            <div class="ui-card-header"><h2>Where it is going</h2></div>

            @if($previous)
            <div class="td-prev">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-clock"/></svg>
                <span>
                    Last at <strong>{{ $previous->siteLabel() }}</strong>
                    @if($previous->ended_at)
                        until {{ $previous->ended_at->format('d M Y') }}
                    @endif
                    @if($previous->endedReasonLabel()) — {{ strtolower($previous->endedReasonLabel()) }} @endif
                </span>
            </div>
            @endif

            <div class="td-grid">
                <div class="td-span">
                    <label class="td-label" for="merchant_name">Merchant / site name <span class="td-req">*</span></label>
                    <input type="text" name="merchant_name" id="merchant_name"
                           value="{{ old('merchant_name') }}" class="ui-input" required>
                    @error('merchant_name')<p class="td-err">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="td-label" for="client_id">Client</label>
                    <select name="client_id" id="client_id" class="ui-select">
                        <option value="">— None —</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (string) old('client_id') === (string) $client->id ? 'selected' : '' }}>
                                {{ $client->company_name ?: $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="td-err">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="td-label" for="merchant_id">Merchant ID</label>
                    <input type="text" name="merchant_id" id="merchant_id"
                           value="{{ old('merchant_id') }}" class="ui-input mv-mono">
                    @error('merchant_id')<p class="td-err">{{ $message }}</p>@enderror
                </div>

                <div class="td-span">
                    <label class="td-label" for="physical_address">Physical address</label>
                    <input type="text" name="physical_address" id="physical_address"
                           value="{{ old('physical_address') }}" class="ui-input" placeholder="Street, suburb">
                    @error('physical_address')<p class="td-err">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="td-label" for="city">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}" class="ui-input">
                    @error('city')<p class="td-err">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="td-label" for="area">Area / suburb</label>
                    <input type="text" name="area" id="area" value="{{ old('area') }}" class="ui-input">
                    @error('area')<p class="td-err">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="td-label" for="region">Region</label>
                    <input type="text" name="region" id="region" value="{{ old('region') }}" class="ui-input">
                    @error('region')<p class="td-err">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="td-label" for="province">Province</label>
                    <input type="text" name="province" id="province" value="{{ old('province') }}" class="ui-input">
                    @error('province')<p class="td-err">{{ $message }}</p>@enderror
                </div>

                <div class="td-span">
                    <label class="td-label" for="notes">Note <span style="font-weight:400">(optional)</span></label>
                    <textarea name="notes" id="notes" rows="2" class="ui-textarea"
                              placeholder="e.g. replacing a device returned to the bank">{{ old('notes') }}</textarea>
                    @error('notes')<p class="td-err">{{ $message }}</p>@enderror
                </div>
            </div>

            <p class="td-note">
                Filling in the city, area and region here is worth the minute it takes — they are what
                makes it possible to answer "is this suburb covered?" later.
            </p>
        </section>

        <div class="td-actions">
            <a href="{{ route('pos-terminals.show', $posTerminal) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Send it out</button>
        </div>
    </form>

</div>
@endsection
