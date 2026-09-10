@extends('layouts.app')
@section('title', $existingVisit ? 'Visit Details — '.$terminal?->terminal_id : 'Log Visit — '.$terminal?->terminal_id)

@push('styles')
<style>
.et-bar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.et-stack{display:flex;flex-direction:column;gap:16px}
.et-stack > .flash-error{margin-bottom:0}
.et-term{display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr) minmax(0,1fr);gap:14px 24px;margin:0;padding:16px 18px}
@media (max-width:800px){.et-term{grid-template-columns:1fr}}
.et-term dt,.et-dl dt{margin:0 0 3px;font-size:12px;font-weight:500;color:var(--mv-muted)}
.et-term dd,.et-dl dd{margin:0;font-size:13.5px;color:var(--mv-ink);word-break:break-word}
.et-tid{display:block;font-family:var(--mv-mono);font-size:16px;font-weight:600;letter-spacing:-.01em;color:var(--mv-ink)}
.et-merchant{display:block;margin-top:4px;font-size:13.5px;color:var(--mv-ink-2)}
.et-sub{display:block;margin-top:2px;font-size:12px;color:var(--mv-muted)}
.et-status{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:11px 16px;font-size:13.5px;color:var(--mv-ink-2);background:var(--mv-surface);border:1px solid var(--mv-line);border-radius:10px}
.et-status strong{font-weight:600;color:var(--mv-ink)}
.et-status-time{margin-left:auto;font-size:12.5px;color:var(--mv-muted)}
.et-cols{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;align-items:start}
@media (max-width:1000px){.et-cols{grid-template-columns:1fr}}
.et-dl{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 20px;margin:0;padding:16px 18px}
.et-dl .et-span{grid-column:1/-1}
.et-dl .et-text{color:var(--mv-ink-2);line-height:1.55}
.et-none{color:var(--mv-muted)}
.et-form{display:flex;flex-direction:column;gap:14px;padding:16px 18px}
.et-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 20px;padding:16px 18px}
@media (max-width:700px){.et-grid,.et-dl{grid-template-columns:1fr}}
.et-field{min-width:0}
.et-field .ui-label{margin-bottom:5px}
.et-req{color:var(--mv-crit)}
.et-opt{font-weight:400;color:var(--mv-muted)}
.et-hint{margin:4px 0 0;font-size:12px;color:var(--mv-muted)}
.et-err{margin:4px 0 0;font-size:12px;color:var(--mv-crit)}
.et-static{display:flex;align-items:center;gap:8px;min-height:38px;padding-top:6px;padding-bottom:6px;background-color:var(--mv-surface-2) !important;color:var(--mv-ink);cursor:default;user-select:none}
.et-avatar{width:24px;height:24px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;background:var(--mv-accent-soft);color:var(--mv-accent-ink);font-size:10.5px;font-weight:600;letter-spacing:.02em}
.et-actions,.et-card-actions{display:flex;justify-content:flex-end;gap:8px}
.et-card-actions{padding-top:2px}
.mv-page .badge{display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.upd-msg{display:flex;align-items:center;gap:8px;padding:9px 12px;font-size:13px;border:1px solid transparent;border-radius:8px}
.upd-msg.is-good{background:var(--mv-good-soft);color:var(--mv-good);border-color:#C6E6D2}
.upd-msg.is-bad{background:var(--mv-crit-soft);color:var(--mv-crit);border-color:#F2CACA}
.et-errors{align-items:flex-start}
.et-errors ul{margin:4px 0 0;padding-left:18px}
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Toolbar --}}
<div class="et-bar">
    <a href="{{ $assignment ? route('site_visits.index', ['assignment_id' => $assignment->id]) : url()->previous() }}"
       class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Assignment</a>
    @if($existingVisit)
        <a href="{{ route('site_visits.show', $existingVisit) }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>Full Visit Details</a>
    @endif
</div>

<div class="et-stack">

{{-- Terminal --}}
<section class="ui-card">
    <dl class="et-term">
        <div>
            <dt>Terminal</dt>
            <dd>
                <span class="et-tid">{{ $terminal?->terminal_id ?? '—' }}</span>
                @if($terminal?->merchant_name)
                    <span class="et-merchant">{{ $terminal->merchant_name }}</span>
                @endif
                @if($terminal?->physical_address)
                    <span class="et-sub">{{ $terminal->physical_address }}</span>
                @endif
            </dd>
        </div>
        <div>
            <dt>Model / Serial</dt>
            <dd>
                {{ $terminal?->terminal_model ?? '—' }}
                <span class="et-sub mv-mono">{{ $terminal?->serial_number ?? '—' }}</span>
            </dd>
        </div>
        @if($assignment)
        <div>
            <dt>Job Assignment</dt>
            <dd>
                <span class="mv-mono">{{ $assignment->assignment_id }}</span>
                @if($assignment->project)
                    <span class="et-sub">{{ $assignment->project->project_name }}</span>
                @endif
            </dd>
        </div>
        @endif
    </dl>
</section>

@if($existingVisit)
{{-- ============================================================
     VISIT ALREADY EXISTS — show captured info + edit form
     ============================================================ --}}

{{-- Status --}}
@php
    $visitStatus = $existingVisit->status ?? 'open';
    $bannerMap = [
        'closed'      => ['badge-green',  'check-circle', 'Visit Completed'],
        'in_progress' => ['badge-yellow', 'hourglass',    'Visit In Progress'],
        'open'        => ['badge-blue',   'clipboard',    'Visit Logged'],
    ];
    [$bannerCls, $bannerIcon, $bannerLabel] = $bannerMap[$visitStatus] ?? ['badge-gray', 'info', 'Visit Recorded'];
@endphp
<div class="et-status">
    <span class="badge {{ $bannerCls }}"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-{{ $bannerIcon }}"/></svg>{{ $bannerLabel }}</span>
    <span>
        Visit <strong class="mv-mono">{{ $existingVisit->visit_id ?? '#'.$existingVisit->id }}</strong>
        has already been recorded for this terminal.
    </span>
    @if($existingVisit->started_at)
        <span class="et-status-time">{{ $existingVisit->started_at->format('M j, Y g:i A') }}</span>
    @endif
</div>

<div class="et-cols">

    {{-- Captured details (read-only summary) --}}
    <section class="ui-card">
        <div class="ui-card-header">
            <h3>Captured Information</h3>
        </div>
        @php
            $ts = $existingVisit->terminal_status_during_visit;
            $tsMap = ['active'=>'badge-green','inactive'=>'badge-red','not_found'=>'badge-gray','relocated'=>'badge-yellow','replaced'=>'badge-yellow'];
        @endphp
        <dl class="et-dl">
            <div>
                <dt>State</dt>
                <dd>
                    @if($ts)
                        <span class="badge {{ $tsMap[$ts] ?? 'badge-gray' }}">{{ ucwords(str_replace('_',' ',$ts)) }}</span>
                    @else
                        <span class="et-none">—</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt>Condition</dt>
                <dd>{{ $existingVisit->terminal_condition ? ucfirst($existingVisit->terminal_condition) : '—' }}</dd>
            </div>
            <div>
                <dt>Issues Found</dt>
                <dd>{{ $existingVisit->issues_found ?? '—' }}</dd>
            </div>
            <div>
                <dt>Corrective Action</dt>
                <dd>{{ $existingVisit->corrective_action ?? '—' }}</dd>
            </div>
            @if($existingVisit->condition_notes)
            <div class="et-span">
                <dt>Condition Notes</dt>
                <dd class="et-text">{{ $existingVisit->condition_notes }}</dd>
            </div>
            @endif
            @if($existingVisit->visit_summary)
            <div class="et-span">
                <dt>Visit Summary</dt>
                <dd class="et-text">{{ $existingVisit->visit_summary }}</dd>
            </div>
            @endif
        </dl>
    </section>

    {{-- Update --}}
    <section class="ui-card">
        <div class="ui-card-header">
            <h3>Update Visit</h3>
        </div>
        <div class="et-form">
            <div id="updateMsg" class="upd-msg hidden" role="status" aria-live="polite"></div>
            <div class="et-field">
                <label class="ui-label" for="upd_terminal_status">State</label>
                <select id="upd_terminal_status" class="ui-select">
                    <option value="">— No change —</option>
                    @foreach(['active'=>'Active','inactive'=>'Inactive','not_found'=>'Not Found','relocated'=>'Relocated','replaced'=>'Replaced'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $existingVisit->terminal_status_during_visit === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="et-field">
                <label class="ui-label" for="upd_issues_found">Issues Found</label>
                <select id="upd_issues_found" class="ui-select">
                    <option value="">— No change —</option>
                    @foreach(['No issues','Not In Use','Denied access','Missing Device','Technical Issues','Device relocated','Merchant Closed','Merchant Relocated','Merchant Not Located','Returned to HQ','Returned to Bank'] as $opt)
                        <option value="{{ $opt }}" {{ $existingVisit->issues_found === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="et-field">
                <label class="ui-label" for="upd_corrective_action">Corrective Action</label>
                <select id="upd_corrective_action" class="ui-select">
                    <option value="">— No change —</option>
                    @foreach(['Resolved','No action needed','To collect device','Follow-up needed','Replacement needed'] as $opt)
                        <option value="{{ $opt }}" {{ $existingVisit->corrective_action === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="et-field">
                <label class="ui-label" for="upd_visit_summary">Visit Summary</label>
                <textarea id="upd_visit_summary" rows="3" class="ui-textarea"
                          placeholder="Add or update summary…">{{ $existingVisit->visit_summary }}</textarea>
            </div>
            <div class="et-field">
                <label class="ui-label" for="upd_condition_notes">Condition Notes</label>
                <textarea id="upd_condition_notes" rows="3" class="ui-textarea"
                          placeholder="General condition of the terminal…">{{ $existingVisit->condition_notes }}</textarea>
            </div>
            <div class="et-card-actions">
                <a href="{{ route('site_visits.show', $existingVisit) }}" class="btn-secondary">View Full Details</a>
                <button id="btnUpdate" onclick="saveUpdate()" class="btn-primary">Save Changes</button>
            </div>
        </div>
    </section>

</div>

<script>
function showUpdateMsg(el, kind, text) {
    el.className = 'upd-msg is-' + kind;
    el.innerHTML = '<svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-' + (kind === 'good' ? 'check-circle' : 'alert-circle') + '"/></svg>';
    const span = document.createElement('span');
    span.textContent = text;
    el.appendChild(span);
}

async function saveUpdate() {
    const btn = document.getElementById('btnUpdate');
    const msg = document.getElementById('updateMsg');
    btn.disabled = true;
    btn.textContent = 'Saving…';
    msg.className = 'upd-msg hidden';

    const payload = {};
    const ts  = document.getElementById('upd_terminal_status').value;
    const iss = document.getElementById('upd_issues_found').value;
    const ca  = document.getElementById('upd_corrective_action').value;
    const vs  = document.getElementById('upd_visit_summary').value.trim();
    const cn  = document.getElementById('upd_condition_notes').value.trim();
    if (ts)  payload.terminal_status_during_visit = ts;
    if (iss) payload.issues_found = [iss];
    if (ca)  payload.corrective_action = ca;
    if (vs)  payload.visit_summary = vs;
    payload.condition_notes = cn.length > 0 ? cn : null;

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
            showUpdateMsg(msg, 'good', data.message || 'Visit updated successfully.');
        } else {
            throw new Error(data.message || 'Update failed.');
        }
    } catch (err) {
        showUpdateMsg(msg, 'bad', err.message);
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

@if($errors->any())
<div class="flash-error et-errors">
    <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
    <div>
        <strong>Please fix the following:</strong>
        <ul>
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" action="{{ route('site_visits.storeManual') }}" class="et-stack">
    @csrf

    {{-- Hidden fields --}}
    <input type="hidden" name="pos_terminal_id" value="{{ $terminal?->id }}">
    @if($assignment)
        <input type="hidden" name="job_assignment_id" value="{{ $assignment->id }}">
    @endif

    {{-- Technician + Timing --}}
    <section class="ui-card">
        <div class="ui-card-header">
            <h2>Visit Details</h2>
        </div>
        <div class="et-grid">

            <div class="et-field">
                <label class="ui-label">Technician <span class="et-req">*</span></label>
                @php $me = auth()->user(); @endphp
                <div class="ui-input et-static">
                    <span class="et-avatar" aria-hidden="true">{{ strtoupper(substr($me->first_name,0,1).substr($me->last_name,0,1)) }}</span>
                    <span>{{ $me->first_name }} {{ $me->last_name }}</span>
                </div>
                <input type="hidden" name="technician_id" value="{{ $me->id }}">
                <p class="et-hint">Logging as yourself</p>
                @error('technician_id')<p class="et-err">{{ $message }}</p>@enderror
            </div>

            <div class="et-field">
                <label class="ui-label">Terminal Status <span class="et-req">*</span></label>
                <select name="terminal_status" required class="ui-select">
                    <option value="">— Select outcome —</option>
                    @foreach(['active'=>'Active','inactive'=>'Inactive','not_found'=>'Not Found','relocated'=>'Relocated','replaced'=>'Replaced'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('terminal_status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                @error('terminal_status')<p class="et-err">{{ $message }}</p>@enderror
            </div>

            <div class="et-field">
                <label class="ui-label">Terminal Condition</label>
                <select name="terminal_condition" class="ui-select">
                    <option value="">— Select condition —</option>
                    @foreach(['good'=>'Good','fair'=>'Fair','poor'=>'Poor','damaged'=>'Damaged'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('terminal_condition') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                @error('terminal_condition')<p class="et-err">{{ $message }}</p>@enderror
            </div>

            <div class="et-field">
                <label class="ui-label">Visit Start <span class="et-req">*</span></label>
                <input type="datetime-local" name="started_at" required
                       value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}" class="ui-input">
                @error('started_at')<p class="et-err">{{ $message }}</p>@enderror
            </div>

            <div class="et-field">
                <label class="ui-label">Visit End <span class="et-opt">(optional)</span></label>
                <input type="datetime-local" name="ended_at" value="{{ old('ended_at') }}" class="ui-input">
                @error('ended_at')<p class="et-err">{{ $message }}</p>@enderror
            </div>

        </div>
    </section>

    {{-- Notes --}}
    <section class="ui-card">
        <div class="ui-card-header">
            <h2>Notes &amp; Observations</h2>
        </div>
        <div class="et-grid">
            <div class="et-field">
                <label class="ui-label">Issues Found</label>
                <select name="issues_found" class="ui-select">
                    <option value="">— Select issue —</option>
                    @foreach(['No issues','Not In Use','Denied access','Missing Device','Technical Issues','Device relocated','Merchant Closed','Merchant Relocated','Merchant Not Located','Returned to HQ','Returned to Bank'] as $opt)
                        <option value="{{ $opt }}" {{ old('issues_found') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('issues_found')<p class="et-err">{{ $message }}</p>@enderror
            </div>
            <div class="et-field">
                <label class="ui-label">Corrective Action Taken</label>
                <select name="corrective_action" class="ui-select">
                    <option value="">— Select action —</option>
                    @foreach(['Resolved','No action needed','To collect device','Follow-up needed','Replacement needed'] as $opt)
                        <option value="{{ $opt }}" {{ old('corrective_action') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('corrective_action')<p class="et-err">{{ $message }}</p>@enderror
            </div>
            <div class="et-field">
                <label class="ui-label">Condition Notes</label>
                <textarea name="condition_notes" rows="3" class="ui-textarea"
                          placeholder="General condition of the terminal…">{{ old('condition_notes') }}</textarea>
                @error('condition_notes')<p class="et-err">{{ $message }}</p>@enderror
            </div>
            <div class="et-field">
                <label class="ui-label">Visit Summary</label>
                <textarea name="visit_summary" rows="3" class="ui-textarea"
                          placeholder="Overall summary of the visit…">{{ old('visit_summary') }}</textarea>
                @error('visit_summary')<p class="et-err">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <div class="et-actions">
        <a href="{{ $assignment ? route('site_visits.index', ['assignment_id' => $assignment->id]) : url()->previous() }}"
           class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">Save Visit</button>
    </div>

</form>

@endif

</div>

@endsection
