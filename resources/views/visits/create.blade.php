@extends('layouts.app')
@section('title', 'Log a Visit')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
/* TomSelect control (input box) */
.ts-wrapper .ts-control{border:1px solid #d1d5db;border-radius:0.375rem;padding:0.4rem 0.625rem;font-size:0.875rem;min-height:2.375rem;box-shadow:none;background:#fff;}
.ts-wrapper.focus .ts-control{border-color:#1a3a5c;box-shadow:0 0 0 2px rgba(26,58,92,.15);}
/* Dropdown rendered on body — position:fixed so viewport coordinates from getBoundingClientRect() map 1-to-1.
   top/left are set by the JS positionDropdown override below (no scroll offset added). */
body > .ts-dropdown{position:fixed !important;z-index:99999 !important;border:1px solid #d1d5db;border-radius:0.375rem;box-shadow:0 4px 16px rgba(0,0,0,.12);font-size:0.875rem;background:#fff;max-height:240px;overflow-y:auto;}
body > .ts-dropdown .option.active,
body > .ts-dropdown .option:hover{background:#1a3a5c;color:#fff;}
body > .ts-dropdown .ts-no-results{padding:0.5rem 0.75rem;color:#9ca3af;font-style:italic;}
/* Template chips */
.template-chip.active-chip{background:#1a3a5c;color:#fff;border-color:#1a3a5c;}
</style>
@endpush

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-5">
        <p class="text-sm text-gray-500">Manually record a technician's field visit from the web</p>
        <a href="{{ route('visits.index') }}" class="btn-secondary btn-sm">← Back to Site Visits</a>
    </div>

    @if($errors->any())
        <div class="flash-error mb-5">
            <span class="text-lg shrink-0">❌</span>
            <div>
                <strong>Please fix the following:</strong>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('site_visits.storeManual') }}" class="space-y-5">
        @csrf

        {{-- Visit Details --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2 class="text-sm font-semibold text-gray-800">Visit Details</h2>
            </div>
            <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Technician: always locked to the logged-in user --}}
                <div>
                    <label class="ui-label">Technician <span class="text-red-500">*</span></label>
                    <div class="ui-input bg-gray-50 text-gray-700 flex items-center gap-2 cursor-default select-none">
                        <span class="w-7 h-7 rounded-full bg-[#1a3a5c] text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($me->first_name,0,1).substr($me->last_name,0,1)) }}
                        </span>
                        {{ $me->first_name }} {{ $me->last_name }}
                        @if($me->employee_number)<span class="text-gray-400 text-xs">({{ $me->employee_number }})</span>@endif
                    </div>
                    <input type="hidden" name="technician_id" value="{{ $me->id }}">
                    <p class="text-xs text-gray-400 mt-1">Logging as yourself</p>
                    @error('technician_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="ui-label">POS Terminal <span class="text-red-500">*</span></label>
                    @if(!$isAdmin && $terminals->isEmpty())
                        <div class="ui-input bg-gray-50 text-gray-400 text-sm">No terminals linked to your active assignments</div>
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
                        @if(!$isAdmin)<p class="text-xs text-gray-400 mt-1">Terminals from your active assignments</p>@endif
                    @endif
                    @error('pos_terminal_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="ui-label">Job Assignment <span class="text-gray-400 normal-case font-normal">(optional)</span></label>
                    <select name="job_assignment_id" class="ui-select">
                        <option value="">— None —</option>
                        @foreach($assignments as $assn)
                            <option value="{{ $assn->id }}" {{ old('job_assignment_id') == $assn->id ? 'selected' : '' }}>
                                {{ $assn->assignment_id }} ({{ $assn->status }})
                            </option>
                        @endforeach
                    </select>
                    @if(!$isAdmin && $assignments->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">You have no active assignments.</p>
                    @endif
                    @error('job_assignment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="ui-label">Terminal Status <span class="text-red-500">*</span></label>
                    <select name="terminal_status" required class="ui-select">
                        <option value="">— Select outcome —</option>
                        <option value="active"    {{ old('terminal_status') === 'active'    ? 'selected' : '' }}>Active</option>
                        <option value="inactive"  {{ old('terminal_status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                        <option value="not_found" {{ old('terminal_status') === 'not_found' ? 'selected' : '' }}>Not Found</option>
                        <option value="relocated" {{ old('terminal_status') === 'relocated' ? 'selected' : '' }}>Relocated</option>
                        <option value="replaced"  {{ old('terminal_status') === 'replaced'  ? 'selected' : '' }}>Replaced</option>
                    </select>
                    @error('terminal_status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="ui-label">Terminal Condition</label>
                    <select name="terminal_condition" class="ui-select">
                        <option value="">— Select condition —</option>
                        <option value="good"    {{ old('terminal_condition') === 'good'    ? 'selected' : '' }}>Good</option>
                        <option value="fair"    {{ old('terminal_condition') === 'fair'    ? 'selected' : '' }}>Fair</option>
                        <option value="poor"    {{ old('terminal_condition') === 'poor'    ? 'selected' : '' }}>Poor</option>
                        <option value="damaged" {{ old('terminal_condition') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                    </select>
                    @error('terminal_condition')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Timing --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2 class="text-sm font-semibold text-gray-800">Timing</h2>
            </div>
            <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">Visit Start <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="started_at" required
                           value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}" class="ui-input">
                    @error('started_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Visit End <span class="text-gray-400 normal-case font-normal">(optional)</span></label>
                    <input type="datetime-local" name="ended_at" value="{{ old('ended_at') }}" class="ui-input">
                    @error('ended_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h2 class="text-sm font-semibold text-gray-800">Notes &amp; Observations</h2>
            </div>
            <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">Condition Notes</label>
                    <textarea name="condition_notes" rows="3" class="ui-textarea"
                              placeholder="General condition of the terminal…">{{ old('condition_notes') }}</textarea>
                    @error('condition_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Issues Found</label>
                    <div class="flex flex-wrap gap-1 mb-1.5" id="issues-templates">
                        @foreach(['No issues','Missing Device','Device relocated','Merchant Closed','Merchant Relocated','Technical Update Failure','Returned to HQ'] as $tpl)
                            <button type="button"
                                    class="template-chip text-xs px-2 py-0.5 rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-[#1a3a5c] hover:text-white hover:border-[#1a3a5c] transition-colors"
                                    data-target="issues_found"
                                    data-value="{{ $tpl }}">{{ $tpl }}</button>
                        @endforeach
                    </div>
                    <textarea name="issues_found" id="issues_found" rows="3" class="ui-textarea"
                              placeholder="List any issues discovered…">{{ old('issues_found') }}</textarea>
                    @error('issues_found')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Corrective Action Taken</label>
                    <div class="flex flex-wrap gap-1 mb-1.5" id="corrective-templates">
                        @foreach(['No Action needed','Follow up needed','Replacement needed','Escalate to Merchant HQ'] as $tpl)
                            <button type="button"
                                    class="template-chip text-xs px-2 py-0.5 rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-[#1a3a5c] hover:text-white hover:border-[#1a3a5c] transition-colors"
                                    data-target="corrective_action"
                                    data-value="{{ $tpl }}">{{ $tpl }}</button>
                        @endforeach
                    </div>
                    <textarea name="corrective_action" id="corrective_action" rows="3" class="ui-textarea"
                              placeholder="What was done to fix or escalate…">{{ old('corrective_action') }}</textarea>
                    @error('corrective_action')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Visit Summary</label>
                    <textarea name="visit_summary" rows="3" class="ui-textarea"
                              placeholder="Overall summary of the visit…">{{ old('visit_summary') }}</textarea>
                    @error('visit_summary')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('visits.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Log Site Visit</button>
        </div>
    </form>
</div>

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
    ['technician_id', 'pos_terminal_id', 'job_assignment_id', 'terminal_status', 'terminal_condition'].forEach(function (name) {
        makeTomSelect(document.querySelector('select[name="' + name + '"]'));
    });

    // Template chip click — append value to target textarea
    document.querySelectorAll('.template-chip').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const textarea = document.getElementById(btn.dataset.target);
            if (!textarea) return;
            const val = btn.dataset.value;
            textarea.value = textarea.value.trim()
                ? textarea.value.trim() + '\n' + val
                : val;
            textarea.dispatchEvent(new Event('input'));
            // Visual feedback: highlight chip briefly
            btn.classList.add('bg-[#1a3a5c]', 'text-white', 'border-[#1a3a5c]');
            setTimeout(() => btn.classList.remove('bg-[#1a3a5c]', 'text-white', 'border-[#1a3a5c]'), 600);
        });
    });
});
</script>
@endpush

@endsection
