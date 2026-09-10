@extends('layouts.app')
@section('title', 'Edit Visit #'.$visit->id)

@push('styles')
<style>
.ve-bar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.ve-stack{display:flex;flex-direction:column;gap:16px}
.ve-head-top{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:14px 18px;border-bottom:1px solid var(--mv-line)}
.ve-head-top h2{margin:0;font-size:15px;font-weight:600;color:var(--mv-ink)}
.ve-dl{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px 24px;margin:0;padding:14px 18px}
.ve-dl dt{margin:0 0 3px;font-size:12px;font-weight:500;color:var(--mv-muted)}
.ve-dl dd{margin:0;font-size:13.5px;color:var(--mv-ink);word-break:break-word}
.ve-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 20px;padding:16px 18px}
.ve-span{grid-column:1/-1}
@media (max-width:700px){.ve-grid{grid-template-columns:1fr}}
.ve-field{min-width:0}
.ve-field .ui-label{margin-bottom:5px}
.ve-opt{font-weight:400;color:var(--mv-muted)}
.ve-hint{margin:6px 0 0;font-size:12px;color:var(--mv-muted)}
.ve-err{margin:4px 0 0;font-size:12px;color:var(--mv-crit)}
.ve-none{color:var(--mv-muted)}
.ve-ev-list{display:flex;flex-direction:column;gap:6px}
.ve-ev{display:flex;align-items:center;gap:10px;padding:8px 12px;font-size:13px;background:var(--mv-surface);border:1px solid var(--mv-line);border-radius:8px}
.ve-ev-name{flex:1;min-width:0;display:flex;align-items:center;gap:6px;color:var(--mv-ink-2)}
a.ve-ev-name{color:var(--mv-accent-ink)}
a.ve-ev-name:hover{text-decoration:underline}
.ve-ev-name .mv-i{width:15px;height:15px;color:var(--mv-muted)}
.ve-ev-name span{min-width:0;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}
.ve-rm{display:inline-flex;align-items:center;gap:6px;margin:0;font-size:12.5px;font-weight:500;color:var(--mv-crit);white-space:nowrap;cursor:pointer}
.ve-file{display:block;width:100%;padding:10px;font-size:13px;color:var(--mv-ink-2);background:var(--mv-surface-2);border:1px dashed var(--mv-line-strong);border-radius:8px;cursor:pointer}
.ve-file::file-selector-button{margin-right:12px;padding:6px 12px;font:inherit;font-size:13px;font-weight:500;color:var(--mv-ink);background:var(--mv-surface);border:1px solid var(--mv-line-strong);border-radius:7px;cursor:pointer}
.ve-file::file-selector-button:hover{background:var(--mv-surface-2)}
.ve-actions{display:flex;justify-content:flex-end;gap:8px}
.ve-errors{align-items:flex-start}
.ve-errors ul{margin:0;padding-left:18px}
</style>
@endpush

@section('content')
{{-- Toolbar --}}
<div class="ve-bar">
    <a href="{{ route('visits.show', $visit) }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Visit</a>
</div>

@if($errors->any())
    <div class="flash-error ve-errors">
        <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
        <ul>
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="ve-stack">
    {{-- Header: read-only context --}}
    <section class="ui-card">
        <div class="ve-head-top">
            <h2>Edit Visit <span class="mv-mono">#{{ $visit->id }}</span></h2>
            <span class="badge {{ $visit->completed_at ? 'badge-green' : 'badge-yellow' }}">
                {{ $visit->completed_at ? 'Completed' : 'In Progress' }}
            </span>
        </div>
        <dl class="ve-dl">
            <div><dt>Merchant</dt><dd>{{ $visit->merchant_name ?? '—' }}</dd></div>
            <div><dt>Employee</dt><dd>{{ optional($visit->employee)->full_name ?? $visit->employee_id }}</dd></div>
            <div>
                <dt>Assignment</dt>
                <dd>
                    @if($visit->assignment_id)
                        <span class="mv-mono">{{ $visit->assignment_id }}</span>
                    @else
                        <span class="ve-none">None</span>
                    @endif
                </dd>
            </div>
            <div><dt>Created</dt><dd>{{ $visit->created_at?->format('M j, Y') }}</dd></div>
        </dl>
    </section>

    <form method="POST" action="{{ route('visits.update', $visit) }}" class="ve-stack" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Outcome --}}
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Visit Outcome</h3>
            </div>
            <div class="ve-grid">
                <div class="ve-field">
                    <label class="ui-label">Corrective Action Taken</label>
                    <select name="action_points" class="ui-select">
                        <option value="">— Select action —</option>
                        @foreach(['Resolved','No action needed','To collect device','Follow-up needed','Replacement needed'] as $opt)
                            <option value="{{ $opt }}" {{ old('action_points', $visit->action_points) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('action_points')<p class="ve-err">{{ $message }}</p>@enderror
                </div>

                <div class="ve-field">
                    <label class="ui-label">
                        Completed At
                        <span class="ve-opt">(clear to reopen visit)</span>
                    </label>
                    <input type="datetime-local" name="completed_at"
                           value="{{ old('completed_at', $visit->completed_at?->format('Y-m-d\TH:i')) }}"
                           class="ui-input">
                    @error('completed_at')<p class="ve-err">{{ $message }}</p>@enderror
                </div>

                <div class="ve-field ve-span">
                    <label class="ui-label">Visit Summary</label>
                    <textarea name="visit_summary" rows="4" class="ui-textarea"
                              placeholder="Overall summary of the visit…">{{ old('visit_summary', $visit->visit_summary) }}</textarea>
                    @error('visit_summary')<p class="ve-err">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- Evidence --}}
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Evidence</h3>
            </div>
            <div class="ve-grid">
                {{-- Existing evidence --}}
                @php $evidence = is_array($visit->evidence) ? $visit->evidence : []; @endphp
                @if(count($evidence))
                <div class="ve-field ve-span">
                    <label class="ui-label">Existing Evidence</label>
                    <div class="ve-ev-list">
                        @foreach($evidence as $i => $url)
                        <div class="ve-ev">
                            @if(\Illuminate\Support\Str::startsWith($url, ['http://','https://','/storage/']))
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="ve-ev-name"><svg class="mv-i" aria-hidden="true"><use href="#i-paperclip"/></svg><span>{{ basename($url) }}</span></a>
                            @else
                                <span class="ve-ev-name"><svg class="mv-i" aria-hidden="true"><use href="#i-paperclip"/></svg><span>{{ $url }}</span></span>
                            @endif
                            <label class="ve-rm">
                                <input type="checkbox" name="remove_evidence[]" value="{{ $i }}">
                                Remove
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <p class="ve-hint">Check "Remove" then save to delete a file.</p>
                </div>
                @endif

                {{-- Upload new evidence --}}
                <div class="ve-field ve-span">
                    <label class="ui-label">Add Evidence <span class="ve-opt">(photos, documents — max 5MB each)</span></label>
                    <input type="file" name="new_evidence[]" multiple accept="image/*,.pdf,.doc,.docx" class="ve-file">
                    @error('new_evidence.*')<p class="ve-err">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="ve-actions">
            <a href="{{ route('visits.show', $visit) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Save Changes</button>
        </div>
    </form>
</div>
@endsection
