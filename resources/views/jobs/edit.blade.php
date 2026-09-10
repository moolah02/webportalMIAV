{{-- resources/views/jobs/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Assignment')

@section('header-actions')
    <a href="{{ route('jobs.show', $assignment->id) }}" class="btn-secondary btn-sm">View assignment</a>
@endsection

@push('styles')
<style>
    .je-wrap { max-width: 980px; }
    .je-crumb { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--mv-muted); text-decoration: none; margin-bottom: 14px; }
    .je-crumb:hover { color: var(--mv-ink); }
    .je-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 16px; }
    .je-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
    .je-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .je-card-body { padding: 18px; }
    .je-dl { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 14px 24px; margin: 0; }
    .je-dl dt { font-size: 12px; color: var(--mv-muted); margin-bottom: 3px; font-weight: 400; }
    .je-dl dd { margin: 0; font-size: 13.5px; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
    .je-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 18px; }
    .je-full { grid-column: 1 / -1; }
    .je-field .ui-label { display: block; margin-bottom: 6px; }
    .je-field .ui-input, .je-field .ui-select, .je-field .ui-textarea { width: 100%; }
    .je-hint { font-size: 12px; color: var(--mv-muted); margin-top: 5px; }
    .je-err { font-size: 12px; color: var(--mv-crit); margin-top: 5px; }
    .je-req { color: var(--mv-crit); }
    .je-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 18px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); border-radius: 0 0 10px 10px; }
    .je-chip { display: inline-flex; align-items: center; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; line-height: 1.7; background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }
    .je-chip.is-accent { background: var(--mv-accent-soft); color: var(--mv-accent-ink); border-color: transparent; }
    .je-chip.is-good { background: var(--mv-good-soft); color: var(--mv-good); border-color: transparent; }
    .je-chip.is-warn { background: var(--mv-warn-soft); color: var(--mv-warn); border-color: transparent; }
    .je-chip.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); border-color: transparent; }
    @media (max-width: 720px) { .je-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $statusChip = ['assigned' => 'is-accent', 'in_progress' => 'is-warn', 'completed' => 'is-good', 'cancelled' => 'is-crit'][$assignment->status] ?? '';
    $terminalCount = is_array($assignment->pos_terminals) ? count($assignment->pos_terminals) : 0;
@endphp
<div class="je-wrap">
    <a href="{{ route('jobs.assignment') }}" class="je-crumb">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Job Assignment
    </a>

    @if($errors->any())
        <div class="alert-danger" style="border:1px solid; padding:11px 14px; margin-bottom:16px; font-size:13.5px;">
            <strong>Please check the form.</strong>
            <ul style="margin:6px 0 0 18px; padding:0;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="je-card">
        <div class="je-card-head">
            <h2>Assignment <span class="mv-mono" style="font-weight:500;">{{ $assignment->assignment_id }}</span></h2>
            <span class="je-chip {{ $statusChip }}">{{ \Illuminate\Support\Str::headline($assignment->status) }}</span>
        </div>
        <div class="je-card-body">
            <dl class="je-dl">
                <div><dt>Region</dt><dd>{{ $assignment->region->name ?? '—' }}</dd></div>
                <div><dt>Client</dt><dd>{{ $assignment->client->company_name ?? 'All clients' }}</dd></div>
                <div><dt>Terminals</dt><dd>{{ $terminalCount }}</dd></div>
                <div><dt>Created</dt><dd>{{ $assignment->created_at?->format('M j, Y') ?? '—' }}</dd></div>
            </dl>
        </div>
    </div>

    <form method="POST" action="{{ route('jobs.assignment.update', $assignment->id) }}" class="je-card">
        @csrf
        @method('PUT')
        <div class="je-card-head"><h2>Schedule</h2></div>
        <div class="je-card-body">
            <div class="je-grid">
                <div class="je-field">
                    <label class="ui-label" for="technician_id">Technician <span class="je-req">*</span></label>
                    <select id="technician_id" name="technician_id" class="ui-select" required>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ (int) old('technician_id', $assignment->technician_id) === (int) $tech->id ? 'selected' : '' }}>
                                {{ $tech->first_name }} {{ $tech->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('technician_id')<div class="je-err">{{ $message }}</div>@enderror
                </div>

                <div class="je-field">
                    <label class="ui-label" for="scheduled_date">Scheduled Date <span class="je-req">*</span></label>
                    <input type="date" id="scheduled_date" name="scheduled_date" class="ui-input" required
                           value="{{ old('scheduled_date', optional($assignment->scheduled_date)->format('Y-m-d')) }}">
                    @error('scheduled_date')<div class="je-err">{{ $message }}</div>@enderror
                </div>

                <div class="je-field">
                    <label class="ui-label" for="service_type">Service Type <span class="je-req">*</span></label>
                    <select id="service_type" name="service_type" class="ui-select" required>
                        @php $currentService = old('service_type', $assignment->service_type); $serviceListed = false; @endphp
                        @foreach($serviceTypes as $serviceType)
                            @php if ($serviceType->slug === $currentService) $serviceListed = true; @endphp
                            <option value="{{ $serviceType->slug }}" {{ $currentService === $serviceType->slug ? 'selected' : '' }}>{{ $serviceType->name }}</option>
                        @endforeach
                        @if(!$serviceListed && $currentService)
                            <option value="{{ $currentService }}" selected>{{ \Illuminate\Support\Str::headline($currentService) }}</option>
                        @endif
                    </select>
                    @error('service_type')<div class="je-err">{{ $message }}</div>@enderror
                </div>

                <div class="je-field">
                    <label class="ui-label" for="priority">Priority <span class="je-req">*</span></label>
                    <select id="priority" name="priority" class="ui-select" required>
                        @foreach(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'emergency' => 'Emergency'] as $val => $label)
                            <option value="{{ $val }}" {{ old('priority', $assignment->priority) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority')<div class="je-err">{{ $message }}</div>@enderror
                </div>

                <div class="je-field">
                    <label class="ui-label" for="estimated_duration_hours">Est. Duration (hours)</label>
                    <input type="number" id="estimated_duration_hours" name="estimated_duration_hours" class="ui-input"
                           step="0.5" min="0.5" max="8" placeholder="2.0"
                           value="{{ old('estimated_duration_hours', $assignment->estimated_duration_hours) }}">
                    @error('estimated_duration_hours')<div class="je-err">{{ $message }}</div>@enderror
                </div>

                <div class="je-field je-full">
                    <label class="ui-label" for="notes">Notes/Instructions</label>
                    <textarea id="notes" name="notes" rows="4" class="ui-textarea ui-input"
                              placeholder="Special instructions, contact details, or notes for the technician...">{{ old('notes', $assignment->notes) }}</textarea>
                    @error('notes')<div class="je-err">{{ $message }}</div>@enderror
                    <div class="je-hint">Terminals, region and client stay as assigned. To change them, cancel this assignment and create a new one.</div>
                </div>
            </div>
        </div>
        <div class="je-foot">
            <a href="{{ route('jobs.assignment') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
