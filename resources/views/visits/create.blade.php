@extends('layouts.app')
@section('title', 'Log a Visit')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
.lv-bar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.lv-bar-note{margin:0;font-size:13px;color:var(--mv-muted)}
.lv-form{display:flex;flex-direction:column;gap:16px}
.lv-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 20px;padding:16px 18px}
@media (max-width:700px){.lv-grid{grid-template-columns:1fr}}
.lv-field{min-width:0}
.lv-field .ui-label{margin-bottom:5px}
.lv-req{color:var(--mv-crit)}
.lv-opt{font-weight:400;color:var(--mv-muted)}
.lv-hint{margin:4px 0 0;font-size:12px;color:var(--mv-muted)}
.lv-hint.is-warn{color:var(--mv-warn)}
.lv-err{margin:4px 0 0;font-size:12px;color:var(--mv-crit)}
.lv-static{display:flex;align-items:center;gap:8px;min-height:38px;padding-top:6px;padding-bottom:6px;background-color:var(--mv-surface-2) !important;color:var(--mv-ink);cursor:default;user-select:none}
.lv-static.is-empty{color:var(--mv-muted);font-size:13px}
.lv-static .mv-mono{font-size:12px;color:var(--mv-muted)}
.lv-avatar{width:24px;height:24px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;background:var(--mv-accent-soft);color:var(--mv-accent-ink);font-size:10.5px;font-weight:600;letter-spacing:.02em}
.lv-actions{display:flex;justify-content:flex-end;gap:8px}
.lv-errors{align-items:flex-start}
.lv-errors ul{margin:4px 0 0;padding-left:18px}

/* TomSelect copies .ui-select onto its wrapper; keep a single border on the inner control */
.mv-page .ts-wrapper.ui-select{border:0 !important;padding:0 !important;background:transparent !important;box-shadow:none !important;min-height:0}
.ts-wrapper .ts-control{border:1px solid var(--mv-line-strong);border-radius:8px;padding:8px 10px;font-size:13.5px;min-height:38px;box-shadow:none;background-color:var(--mv-surface);color:var(--mv-ink)}
.ts-wrapper.focus .ts-control{border-color:var(--mv-accent);box-shadow:0 0 0 3px rgba(43,100,168,.15)}
/* Dropdown rendered on body — position:fixed so viewport coordinates from getBoundingClientRect() map 1-to-1.
   top/left are set by the JS positionDropdown override below (no scroll offset added). */
body > .ts-dropdown{position:fixed !important;z-index:99999 !important;margin-top:4px;border:1px solid var(--mv-line);border-radius:8px;box-shadow:0 12px 32px rgba(22,32,44,.12);font-size:13.5px;background:var(--mv-surface);color:var(--mv-ink);max-height:240px;overflow-y:auto}
body > .ts-dropdown .option{padding:7px 10px}
body > .ts-dropdown .option.active,
body > .ts-dropdown .option:hover{background:var(--mv-accent-soft);color:var(--mv-accent-ink)}
body > .ts-dropdown .ts-no-results{padding:8px 10px;color:var(--mv-muted)}
</style>
@endpush

@section('content')
{{-- Toolbar --}}
<div class="lv-bar">
    <a href="{{ route('visits.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Site Visits</a>
    <p class="lv-bar-note">Manually record a technician's field visit from the web</p>
</div>

@if($errors->any())
    <div class="flash-error lv-errors">
        <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
        <div>
            <strong>Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('site_visits.storeManual') }}" class="lv-form">
    @csrf

    {{-- Visit Details --}}
    <section class="ui-card">
        <div class="ui-card-header">
            <h2>Visit Details</h2>
        </div>
        <div class="lv-grid">

            {{-- Technician: always locked to the logged-in user --}}
            <div class="lv-field">
                <label class="ui-label">Technician <span class="lv-req">*</span></label>
                <div class="ui-input lv-static">
                    <span class="lv-avatar" aria-hidden="true">{{ strtoupper(substr($me->first_name,0,1).substr($me->last_name,0,1)) }}</span>
                    <span>{{ $me->first_name }} {{ $me->last_name }}</span>
                    @if($me->employee_number)<span class="mv-mono">{{ $me->employee_number }}</span>@endif
                </div>
                <input type="hidden" name="technician_id" value="{{ $me->id }}">
                <p class="lv-hint">Logging as yourself</p>
                @error('technician_id')<p class="lv-err">{{ $message }}</p>@enderror
            </div>

            <div class="lv-field">
                <label class="ui-label">POS Terminal <span class="lv-req">*</span></label>
                @if(!$isAdmin && $terminals->isEmpty())
                    <div class="ui-input lv-static is-empty">No terminals linked to your active assignments</div>
                    <input type="hidden" name="pos_terminal_id" value="">
                @else
                    <select name="pos_terminal_id" id="pos_terminal_id" required class="ui-select">
                        <option value="">— Select terminal —</option>
                        @foreach($terminals as $term)
                            <option value="{{ $term->id }}" {{ old('pos_terminal_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->terminal_id }}
                                @if($term->merchant_name) — {{ $term->merchant_name }}@endif
                                @if($term->client) ({{ $term->client->company_name }})@endif
                            </option>
                        @endforeach
                    </select>
                    @if(!$isAdmin)<p class="lv-hint">Terminals from your active assignments</p>@endif
                @endif
                @error('pos_terminal_id')<p class="lv-err">{{ $message }}</p>@enderror
            </div>

            <div class="lv-field">
                <label class="ui-label">Job Assignment <span class="lv-opt">(optional)</span></label>
                <select name="job_assignment_id" class="ui-select">
                    <option value="">— None —</option>
                    @foreach($assignments as $assn)
                        <option value="{{ $assn->id }}" {{ old('job_assignment_id') == $assn->id ? 'selected' : '' }}>
                            {{ $assn->assignment_id }} ({{ $assn->status }})
                        </option>
                    @endforeach
                </select>
                @if(!$isAdmin && $assignments->isEmpty())
                    <p class="lv-hint is-warn">You have no active assignments.</p>
                @endif
                @error('job_assignment_id')<p class="lv-err">{{ $message }}</p>@enderror
            </div>

            <div class="lv-field">
                <label class="ui-label">Terminal Status <span class="lv-req">*</span></label>
                <select name="terminal_status" required class="ui-select">
                    <option value="">— Select outcome —</option>
                    <option value="active"    {{ old('terminal_status') === 'active'    ? 'selected' : '' }}>Active</option>
                    <option value="inactive"  {{ old('terminal_status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                    <option value="not_found" {{ old('terminal_status') === 'not_found' ? 'selected' : '' }}>Not Found</option>
                    <option value="relocated" {{ old('terminal_status') === 'relocated' ? 'selected' : '' }}>Relocated</option>
                    <option value="replaced"  {{ old('terminal_status') === 'replaced'  ? 'selected' : '' }}>Replaced</option>
                </select>
                @error('terminal_status')<p class="lv-err">{{ $message }}</p>@enderror
            </div>

            <div class="lv-field">
                <label class="ui-label">Terminal Condition</label>
                <select name="terminal_condition" class="ui-select">
                    <option value="">— Select condition —</option>
                    <option value="good"    {{ old('terminal_condition') === 'good'    ? 'selected' : '' }}>Good</option>
                    <option value="fair"    {{ old('terminal_condition') === 'fair'    ? 'selected' : '' }}>Fair</option>
                    <option value="poor"    {{ old('terminal_condition') === 'poor'    ? 'selected' : '' }}>Poor</option>
                    <option value="damaged" {{ old('terminal_condition') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                </select>
                @error('terminal_condition')<p class="lv-err">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    {{-- Timing --}}
    <section class="ui-card">
        <div class="ui-card-header">
            <h2>Timing</h2>
        </div>
        <div class="lv-grid">
            <div class="lv-field">
                <label class="ui-label">Visit Start <span class="lv-req">*</span></label>
                <input type="datetime-local" name="started_at" required
                       value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}" class="ui-input">
                @error('started_at')<p class="lv-err">{{ $message }}</p>@enderror
            </div>
            <div class="lv-field">
                <label class="ui-label">Visit End <span class="lv-opt">(optional)</span></label>
                <input type="datetime-local" name="ended_at" value="{{ old('ended_at') }}" class="ui-input">
                @error('ended_at')<p class="lv-err">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    {{-- Notes --}}
    <section class="ui-card">
        <div class="ui-card-header">
            <h2>Notes &amp; Observations</h2>
        </div>
        <div class="lv-grid">
            <div class="lv-field">
                <label class="ui-label">Condition Notes</label>
                <textarea name="condition_notes" rows="3" class="ui-textarea"
                          placeholder="General condition of the terminal…">{{ old('condition_notes') }}</textarea>
                @error('condition_notes')<p class="lv-err">{{ $message }}</p>@enderror
            </div>
            <div class="lv-field">
                <label class="ui-label">Issues Found</label>
                <select name="issues_found" id="issues_found" class="ui-select">
                    <option value="">— Select issue —</option>
                    @foreach(['No issues','Not In Use','Denied access','Missing Device','Technical Issues','Device relocated','Merchant Closed','Merchant Relocated','Merchant Not Located','Returned to HQ','Returned to Bank'] as $opt)
                        <option value="{{ $opt }}" {{ old('issues_found') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('issues_found')<p class="lv-err">{{ $message }}</p>@enderror
            </div>
            <div class="lv-field">
                <label class="ui-label">Corrective Action Taken</label>
                <select name="corrective_action" id="corrective_action" class="ui-select">
                    <option value="">— Select action —</option>
                    @foreach(['Resolved','No action needed','To collect device','Follow-up needed','Replacement needed'] as $opt)
                        <option value="{{ $opt }}" {{ old('corrective_action') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('corrective_action')<p class="lv-err">{{ $message }}</p>@enderror
            </div>
            <div class="lv-field">
                <label class="ui-label">Visit Summary</label>
                <textarea name="visit_summary" rows="3" class="ui-textarea"
                          placeholder="Overall summary of the visit…">{{ old('visit_summary') }}</textarea>
                @error('visit_summary')<p class="lv-err">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    {{-- Submit --}}
    <div class="lv-actions">
        <a href="{{ route('visits.index') }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">Log Site Visit</button>
    </div>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Searchable selects — dropdown appended to body to avoid all stacking/overflow issues.
    // positionDropdown is overridden to use position:fixed + getBoundingClientRect() which
    // gives viewport-relative coordinates — correct at any scroll depth or screen resolution.
    function makeTomSelect(el) {
        if (!el) return;
        const ts = new TomSelect(el, {
            allowEmptyOption: true,
            dropdownParent: 'body',
            plugins: ['no_backspace_delete'],
        });
        // Override the built-in positionDropdown which uses scrollTop offset (wrong for fixed).
        ts.positionDropdown = function () {
            const rect = ts.control.getBoundingClientRect();
            const dd   = ts.dropdown;
            dd.style.top   = rect.bottom + 'px';
            dd.style.left  = rect.left   + 'px';
            dd.style.width = rect.width  + 'px';
        };
    }
    ['technician_id', 'pos_terminal_id', 'job_assignment_id', 'terminal_status', 'terminal_condition', 'issues_found', 'corrective_action'].forEach(function (name) {
        makeTomSelect(document.querySelector('select[name="' + name + '"]'));
    });
});
</script>
@endpush

@endsection
