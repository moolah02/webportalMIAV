@extends('layouts.app')
@section('title', 'Replace device — '.$posTerminal->terminal_id)

@push('styles')
<style>
.tm-page { max-width: 820px; }
.tm-lead { margin:4px 0 0; font-size:13.5px; color:var(--mv-ink-2); }
.tm-bar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.tm-stack { display:flex; flex-direction:column; gap:16px; }
.tm-body { padding:16px 18px; display:flex; flex-direction:column; gap:16px; }
.tm-from { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px 20px; margin:0; padding:16px 18px; }
@media (max-width:760px){ .tm-from { grid-template-columns:1fr; } }
.tm-from dt { margin:0 0 3px; font-size:12px; font-weight:500; color:var(--mv-muted); }
.tm-from dd { margin:0; font-size:13.5px; color:var(--mv-ink); word-break:break-word; }
.tm-tid { font-family:var(--mv-mono); font-size:15px; font-weight:600; color:var(--mv-ink); }
.tm-label { font-size:12px; font-weight:500; color:var(--mv-muted); margin-bottom:5px; display:block; }
.tm-opt { display:flex; gap:12px; align-items:flex-start; padding:14px 16px; border:1px solid var(--mv-line); border-radius:10px; cursor:pointer; background:var(--mv-surface); }
.tm-opt + .tm-opt { margin-top:10px; }
.tm-opt:has(input:checked) { border-color:var(--mv-accent); box-shadow:inset 0 0 0 1px var(--mv-accent); }
.tm-opt input { margin-top:3px; flex-shrink:0; }
.tm-opt-t { font-size:14px; font-weight:600; color:var(--mv-ink); }
.tm-opt-d { margin:3px 0 0; font-size:12.5px; color:var(--mv-ink-2); line-height:1.5; }
.tm-panel { margin-top:12px; padding-left:28px; display:none; }
.tm-opt:has(input:checked) .tm-panel { display:block; }
.tm-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px 16px; }
@media (max-width:620px){ .tm-grid { grid-template-columns:1fr; } }
.tm-note { padding:11px 14px; font-size:13px; color:var(--mv-ink-2); background:var(--mv-surface-2); border:1px solid var(--mv-line); border-radius:9px; line-height:1.55; }
.tm-err { margin:4px 0 0; font-size:12px; color:var(--mv-crit); }
.tm-actions { display:flex; justify-content:flex-end; gap:8px; }
.tm-empty { font-size:13px; color:var(--mv-muted); }
</style>
@endpush

@section('content')
<div class="mv-page tm-page">

    <div class="tm-bar">
        <div>
            <h1 class="page-title">Replace this device</h1>
            <p class="tm-lead">The site stays where it is. The device standing there changes.</p>
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

    <div class="tm-stack">

        {{-- What is coming off --}}
        <section class="ui-card">
            <div class="ui-card-header"><h2>Coming off site</h2></div>
            <dl class="tm-from">
                <div>
                    <dt>Device</dt>
                    <dd><span class="tm-tid">{{ $posTerminal->terminal_id }}</span></dd>
                </div>
                <div>
                    <dt>Site</dt>
                    <dd>{{ $posTerminal->merchant_name ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Where</dt>
                    <dd>{{ collect([$posTerminal->city, $posTerminal->region])->filter()->implode(' · ') ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Model</dt>
                    <dd>{{ $posTerminal->terminal_model ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Serial</dt>
                    <dd class="mv-mono">{{ $posTerminal->serial_number ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Visits on record</dt>
                    <dd>{{ $posTerminal->visits()->count() }}</dd>
                </div>
            </dl>
        </section>

        <form method="POST" action="{{ route('pos-terminals.migrate', $posTerminal) }}" class="tm-stack">
            @csrf

            <section class="ui-card">
                <div class="ui-card-header"><h2>What takes its place</h2></div>
                <div class="tm-body">

                    <label class="tm-opt">
                        <input type="radio" name="replacement" value="stock"
                               {{ old('replacement', $stock->isNotEmpty() ? 'stock' : 'new') === 'stock' ? 'checked' : '' }}
                               {{ $stock->isEmpty() ? 'disabled' : '' }}>
                        <div style="flex:1; min-width:0;">
                            <span class="tm-opt-t">A device already in stock</span>
                            <p class="tm-opt-d">
                                One that was collected from another site. It keeps the visit history
                                from everywhere it has been.
                            </p>
                            <div class="tm-panel">
                                @if($stock->isEmpty())
                                    <p class="tm-empty">Nothing is in stock. Collect a device from a site first, or register a new one below.</p>
                                @else
                                    <label class="tm-label" for="replacement_terminal_id">Which device</label>
                                    <select name="replacement_terminal_id" id="replacement_terminal_id" class="ui-select">
                                        <option value="">— Choose a device —</option>
                                        @foreach($stock as $device)
                                            <option value="{{ $device->id }}" {{ (string) old('replacement_terminal_id') === (string) $device->id ? 'selected' : '' }}>
                                                {{ $device->terminal_id }}@if($device->terminal_model) — {{ $device->terminal_model }}@endif
                                                @if($device->merchant_name) (last at {{ $device->merchant_name }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('replacement_terminal_id')<p class="tm-err">{{ $message }}</p>@enderror
                                @endif
                            </div>
                        </div>
                    </label>

                    <label class="tm-opt">
                        <input type="radio" name="replacement" value="new"
                               {{ old('replacement', $stock->isNotEmpty() ? 'stock' : 'new') === 'new' ? 'checked' : '' }}>
                        <div style="flex:1; min-width:0;">
                            <span class="tm-opt-t">A brand-new device</span>
                            <p class="tm-opt-d">
                                One that has never been deployed. It is added to the system and put on
                                this site in one step.
                            </p>
                            <div class="tm-panel">
                                <div class="tm-grid">
                                    <div>
                                        <label class="tm-label" for="new_terminal_id">Terminal ID <span style="color:var(--mv-crit)">*</span></label>
                                        <input type="text" name="new_terminal_id" id="new_terminal_id"
                                               value="{{ old('new_terminal_id') }}" class="ui-input mv-mono" placeholder="As printed on the device">
                                        @error('new_terminal_id')<p class="tm-err">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="tm-label" for="new_serial_number">Serial number</label>
                                        <input type="text" name="new_serial_number" id="new_serial_number"
                                               value="{{ old('new_serial_number') }}" class="ui-input mv-mono">
                                        @error('new_serial_number')<p class="tm-err">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="tm-label" for="new_terminal_model">Model</label>
                                        <select name="new_terminal_model" id="new_terminal_model" class="ui-select">
                                            <option value="">— Choose a model —</option>
                                            @foreach(\App\Support\VisitOptions::TERMINAL_MODELS as $model)
                                                <option value="{{ $model }}" {{ old('new_terminal_model') === $model ? 'selected' : '' }}>{{ $model }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_terminal_model')<p class="tm-err">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>

                    @error('replacement')<p class="tm-err">{{ $message }}</p>@enderror

                    <div>
                        <label class="tm-label" for="notes">Why the swap <span style="font-weight:400">(optional)</span></label>
                        <textarea name="notes" id="notes" rows="2" class="ui-textarea"
                                  placeholder="e.g. screen cracked, device would not take a card">{{ old('notes') }}</textarea>
                        @error('notes')<p class="tm-err">{{ $message }}</p>@enderror
                    </div>

                    <p class="tm-note">
                        <strong>What this does.</strong> The replacement takes on
                        {{ $posTerminal->merchant_name ?: 'this site' }} and everything recorded about the site.
                        <strong>{{ $posTerminal->terminal_id }}</strong> goes into stock, ready to be sent out
                        again. Neither device is duplicated or deleted, and each keeps its own visits — so
                        the fault history of {{ $posTerminal->terminal_id }} travels with the device, not with
                        the site.
                    </p>

                </div>
            </section>

            <div class="tm-actions">
                <a href="{{ route('pos-terminals.show', $posTerminal) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Swap the device</button>
            </div>
        </form>

    </div>
</div>
@endsection
