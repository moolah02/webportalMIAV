@extends('layouts.app')
@section('title', $existingVisit ? 'Visit Details — '.$terminal?->terminal_id : 'Log Visit — '.$terminal?->terminal_id)

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Back --}}
<div class="flex justify-between items-center mb-5">
    <a href="{{ $assignment ? route('site_visits.index', ['assignment_id' => $assignment->id]) : url()->previous() }}"
       class="btn-secondary btn-sm">&#x2190; Back to Assignment</a>
    @if($existingVisit)
        <a href="{{ route('site_visits.show', $existingVisit) }}" class="btn-secondary btn-sm">&#x1F441; Full Visit Details</a>
    @endif
</div>

{{-- Terminal info banner --}}
<div class="ui-card mb-5">
    <div class="p-5 flex flex-wrap items-start gap-6">
        <div class="flex-1 min-w-0">
            <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Terminal</div>
            <div class="text-lg font-bold text-[#1a3a5c]">{{ $terminal?->terminal_id ?? '—' }}</div>
            @if($terminal?->merchant_name)
                <div class="text-sm text-gray-700 mt-1">{{ $terminal->merchant_name }}</div>
            @endif
            @if($terminal?->physical_address)
                <div class="text-xs text-gray-500 mt-0.5">{{ $terminal->physical_address }}</div>
            @endif
        </div>
        <div>
            <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Model / Serial</div>
            <div class="text-sm text-gray-700">{{ $terminal?->terminal_model ?? '—' }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $terminal?->serial_number ?? '—' }}</div>
        </div>
        @if($assignment)
        <div>
            <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Job Assignment</div>
            <div class="text-sm font-semibold text-gray-800">{{ $assignment->assignment_id }}</div>
            @if($assignment->project)
                <div class="text-xs text-gray-500 mt-0.5">{{ $assignment->project->project_name }}</div>
            @endif
        </div>
        @endif
    </div>
</div>

@if($existingVisit)
{{-- ============================================================
     VISIT ALREADY EXISTS — show captured info + edit form
     ============================================================ --}}

{{-- Status banner --}}
@php
    $visitStatus = $existingVisit->status ?? 'open';
    $bannerMap = [
        'closed'      => ['bg-green-50 border-green-300', 'text-green-800', '&#x2713; Visit Completed'],
        'in_progress' => ['bg-yellow-50 border-yellow-300', 'text-yellow-800', '&#x23F3; Visit In Progress'],
        'open'        => ['bg-blue-50 border-blue-300', 'text-blue-800', '&#x1F4CB; Visit Logged'],
    ];
    [$bannerBg, $bannerText, $bannerLabel] = $bannerMap[$visitStatus] ?? ['bg-gray-50 border-gray-300', 'text-gray-700', 'Visit Recorded'];
@endphp
<div class="rounded-lg border px-5 py-4 mb-5 flex items-center gap-3 {{ $bannerBg }}">
    <span class="{{ $bannerText }} text-xl">{!! $bannerLabel !!}</span>
    <span class="text-sm {{ $bannerText }} font-medium">
        Visit <strong>{{ $existingVisit->visit_id ?? '#'.$existingVisit->id }}</strong>
        has already been recorded for this terminal.
    </span>
    @if($existingVisit->started_at)
        <span class="ml-auto text-xs {{ $bannerText }} opacity-75">
            {{ $existingVisit->started_at->format('M j, Y g:i A') }}
        </span>
    @endif
</div>

{{-- Captured details (read-only summary) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    <div class="ui-card">
        <div class="ui-card-header">
            <span class="text-sm font-semibold">Captured Information</span>
        </div>
        <div class="p-5 space-y-3">
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Terminal Status</div>
                    @php
                        $ts = $existingVisit->terminal_status_during_visit;
                        $tsMap = ['active'=>'badge-green','inactive'=>'badge-red','not_found'=>'badge-gray','relocated'=>'badge-yellow','replaced'=>'badge-yellow'];
                    @endphp
                    <span class="badge {{ $tsMap[$ts] ?? 'badge-gray' }}">{{ $ts ? ucwords(str_replace('_',' ',$ts)) : '—' }}</span>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Condition</div>
                    <span class="text-sm text-gray-700">{{ $existingVisit->terminal_condition ? ucfirst($existingVisit->terminal_condition) : '—' }}</span>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Issues Found</div>
                    <span class="text-sm text-gray-700">{{ $existingVisit->issues_found ?? '—' }}</span>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Corrective Action</div>
                    <span class="text-sm text-gray-700">{{ $existingVisit->corrective_action ?? '—' }}</span>
                </div>
            </div>
            @if($existingVisit->condition_notes)
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Condition Notes</div>
                <p class="text-sm text-gray-700">{{ $existingVisit->condition_notes }}</p>
            </div>
            @endif
            @if($existingVisit->visit_summary)
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Visit Summary</div>
                <p class="text-sm text-gray-700">{{ $existingVisit->visit_summary }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-header">
            <span class="text-sm font-semibold">Update Visit</span>
        </div>
        <div class="p-5">
            <div id="updateMsg" class="hidden mb-3 text-sm rounded px-3 py-2"></div>
            <div class="space-y-4">
                <div>
                    <label class="ui-label">Terminal Status</label>
                    <select id="upd_terminal_status" class="ui-select">
                        <option value="">— No change —</option>
                        @foreach(['active'=>'Active','inactive'=>'Inactive','not_found'=>'Not Found','relocated'=>'Relocated','replaced'=>'Replaced'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $existingVisit->terminal_status_during_visit === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ui-label">Issues Found</label>
                    <select id="upd_issues_found" class="ui-select">
                        <option value="">— No change —</option>
                        @foreach(['No issues','Not In Use','Denied access','Missing Device','Technical Issues','Device relocated','Merchant Closed','Merchant Relocated','Merchant Not Located','Returned to HQ','Returned to Bank'] as $opt)
                            <option value="{{ $opt }}" {{ $existingVisit->issues_found === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ui-label">Corrective Action</label>
                    <select id="upd_corrective_action" class="ui-select">
                        <option value="">— No change —</option>
                        @foreach(['Resolved','No action needed','To collect device','Follow-up needed','Replacement needed'] as $opt)
                            <option value="{{ $opt }}" {{ $existingVisit->corrective_action === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ui-label">Visit Summary</label>
                    <textarea id="upd_visit_summary" rows="3" class="ui-textarea"
                              placeholder="Add or update summary…">{{ $existingVisit->visit_summary }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button id="btnUpdate" onclick="saveUpdate()" class="btn-primary flex-1">Save Changes</button>
                    <a href="{{ route('site_visits.show', $existingVisit) }}" class="btn-secondary">View Full Details</a>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
async function saveUpdate() {
    const btn = document.getElementById('btnUpdate');
    const msg = document.getElementById('updateMsg');
    btn.disabled = true;
    btn.textContent = 'Saving…';
    msg.className = 'hidden mb-3 text-sm rounded px-3 py-2';

    const payload = {};
    const ts = document.getElementById('upd_terminal_status').value;
    const iss = document.getElementById('upd_issues_found').value;
    const ca  = document.getElementById('upd_corrective_action').value;
    const vs  = document.getElementById('upd_visit_summary').value.trim();
    if (ts)  payload.terminal_status_during_visit = ts;
    if (iss) payload.issues_found = [iss];
    if (ca)  payload.corrective_action = ca;
    if (vs)  payload.visit_summary = vs;

    try {
        const res = await fetch("{{ route('site_visits.update', $existingVisit) }}", {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            msg.className = 'mb-3 text-sm rounded px-3 py-2 bg-green-50 border border-green-300 text-green-800';
            msg.textContent = '✓ ' + (data.message || 'Visit updated successfully.');
        } else {
            throw new Error(data.message || 'Update failed.');
        }
    } catch (err) {
        msg.className = 'mb-3 text-sm rounded px-3 py-2 bg-red-50 border border-red-300 text-red-800';
        msg.textContent = '✗ ' + err.message;
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
    }
}
</script>

@else
{{-- ============================================================
     NO VISIT YET — show create form
     ============================================================ --}}

@if(session('success'))
<div class="flash-success mb-5"><span class="text-lg">&#x2713;</span> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="flash-error mb-5">
    <span class="text-lg shrink-0">&#x274C;</span>
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

    {{-- Hidden fields --}}
    <input type="hidden" name="pos_terminal_id" value="{{ $terminal?->id }}">
    @if($assignment)
        <input type="hidden" name="job_assignment_id" value="{{ $assignment->id }}">
    @endif

    {{-- Technician + Timing --}}
    <div class="ui-card">
        <div class="ui-card-header">
            <h2 class="text-sm font-semibold text-gray-800">Visit Details</h2>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="ui-label">Technician <span class="text-red-500">*</span></label>
                @php $me = auth()->user(); @endphp
                <div class="ui-input bg-gray-50 text-gray-700 flex items-center gap-2 cursor-default select-none">
                    <span class="w-7 h-7 rounded-full bg-[#1a3a5c] text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr($me->first_name,0,1).substr($me->last_name,0,1)) }}
                    </span>
                    {{ $me->first_name }} {{ $me->last_name }}
                </div>
                <input type="hidden" name="technician_id" value="{{ $me->id }}">
                <p class="text-xs text-gray-400 mt-1">Logging as yourself</p>
                @error('technician_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="ui-label">Terminal Status <span class="text-red-500">*</span></label>
                <select name="terminal_status" required class="ui-select">
                    <option value="">— Select outcome —</option>
                    @foreach(['active'=>'Active','inactive'=>'Inactive','not_found'=>'Not Found','relocated'=>'Relocated','replaced'=>'Replaced'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('terminal_status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                @error('terminal_status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="ui-label">Terminal Condition</label>
                <select name="terminal_condition" class="ui-select">
                    <option value="">— Select condition —</option>
                    @foreach(['good'=>'Good','fair'=>'Fair','poor'=>'Poor','damaged'=>'Damaged'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('terminal_condition') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                @error('terminal_condition')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

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
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="ui-label">Issues Found</label>
                <select name="issues_found" class="ui-select">
                    <option value="">— Select issue —</option>
                    @foreach(['No issues','Not In Use','Denied access','Missing Device','Technical Issues','Device relocated','Merchant Closed','Merchant Relocated','Merchant Not Located','Returned to HQ','Returned to Bank'] as $opt)
                        <option value="{{ $opt }}" {{ old('issues_found') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('issues_found')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Corrective Action Taken</label>
                <select name="corrective_action" class="ui-select">
                    <option value="">— Select action —</option>
                    @foreach(['Resolved','No action needed','To collect device','Follow-up needed','Replacement needed'] as $opt)
                        <option value="{{ $opt }}" {{ old('corrective_action') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('corrective_action')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Condition Notes</label>
                <textarea name="condition_notes" rows="3" class="ui-textarea"
                          placeholder="General condition of the terminal…">{{ old('condition_notes') }}</textarea>
                @error('condition_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Visit Summary</label>
                <textarea name="visit_summary" rows="3" class="ui-textarea"
                          placeholder="Overall summary of the visit…">{{ old('visit_summary') }}</textarea>
                @error('visit_summary')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ $assignment ? route('site_visits.index', ['assignment_id' => $assignment->id]) : url()->previous() }}"
           class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">Save Visit</button>
    </div>

</form>

@endif

@endsection
