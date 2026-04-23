@extends('layouts.app')
@section('title', 'Log a Visit')

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

                <div>
                    <label class="ui-label">Technician <span class="text-red-500">*</span></label>
                    <select name="technician_id" required class="ui-select">
                        <option value="">— Select technician —</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('technician_id') == $tech->id ? 'selected' : '' }}>
                                {{ $tech->first_name }} {{ $tech->last_name }}
                                @if($tech->employee_number)({{ $tech->employee_number }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('technician_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="ui-label">POS Terminal <span class="text-red-500">*</span></label>
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
                    @error('job_assignment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="ui-label">Terminal Status <span class="text-red-500">*</span></label>
                    <select name="terminal_status" required class="ui-select">
                        <option value="">— Select outcome —</option>
                        <option value="working"           {{ old('terminal_status') === 'working'           ? 'selected' : '' }}>Working</option>
                        <option value="not_working"       {{ old('terminal_status') === 'not_working'       ? 'selected' : '' }}>Not Working</option>
                        <option value="needs_maintenance" {{ old('terminal_status') === 'needs_maintenance' ? 'selected' : '' }}>Needs Maintenance</option>
                        <option value="not_found"         {{ old('terminal_status') === 'not_found'         ? 'selected' : '' }}>Not Found</option>
                    </select>
                    @error('terminal_status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                    <textarea name="issues_found" rows="3" class="ui-textarea"
                              placeholder="List any issues discovered…">{{ old('issues_found') }}</textarea>
                    @error('issues_found')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="ui-label">Corrective Action Taken</label>
                    <textarea name="corrective_action" rows="3" class="ui-textarea"
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
@endsection
