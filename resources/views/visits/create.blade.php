@extends('layouts.app')
@section('title', 'Log a Visit')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <p class="text-gray-500 text-sm mt-1">Manually record a technician's field visit from the web</p>
        </div>
        <a href="{{ route('visits.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← Back to Site Visits
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
            <strong>Please fix the following:</strong>
            <ul class="list-disc list-inside mt-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('site_visits.storeManual') }}" class="space-y-6">
        @csrf

        {{-- Who & Where --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Visit Details</h2>
            <div class="grid grid-cols-2 gap-5">

                {{-- Technician --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Technician <span class="text-red-500">*</span>
                    </label>
                    <select name="technician_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select technician —</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('technician_id') == $tech->id ? 'selected' : '' }}>
                                {{ $tech->first_name }} {{ $tech->last_name }}
                                @if($tech->employee_number)({{ $tech->employee_number }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('technician_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- POS Terminal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        POS Terminal <span class="text-red-500">*</span>
                    </label>
                    <select name="pos_terminal_id" id="pos_terminal_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select terminal —</option>
                        @foreach($terminals as $term)
                            <option value="{{ $term->id }}" {{ old('pos_terminal_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->terminal_id }}
                                @if($term->merchant_name) — {{ $term->merchant_name }}@endif
                                @if($term->client) ({{ $term->client->company_name }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('pos_terminal_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Job Assignment (optional) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Job Assignment <span class="text-gray-400 text-xs font-normal">(optional)</span>
                    </label>
                    <select name="job_assignment_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— None —</option>
                        @foreach($assignments as $assn)
                            <option value="{{ $assn->id }}" {{ old('job_assignment_id') == $assn->id ? 'selected' : '' }}>
                                {{ $assn->assignment_id }}
                                <span class="text-gray-400">({{ $assn->status }})</span>
                            </option>
                        @endforeach
                    </select>
                    @error('job_assignment_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Terminal Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Terminal Status <span class="text-red-500">*</span>
                    </label>
                    <select name="terminal_status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select outcome —</option>
                        <option value="working"              {{ old('terminal_status') === 'working' ? 'selected' : '' }}>✅ Working</option>
                        <option value="not_working"          {{ old('terminal_status') === 'not_working' ? 'selected' : '' }}>❌ Not Working</option>
                        <option value="needs_maintenance"    {{ old('terminal_status') === 'needs_maintenance' ? 'selected' : '' }}>🔧 Needs Maintenance</option>
                        <option value="not_found"            {{ old('terminal_status') === 'not_found' ? 'selected' : '' }}>❓ Not Found</option>
                    </select>
                    @error('terminal_status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Timing --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Timing</h2>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Visit Start <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="started_at" required
                           value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('started_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Visit End <span class="text-gray-400 text-xs font-normal">(optional)</span>
                    </label>
                    <input type="datetime-local" name="ended_at"
                           value="{{ old('ended_at') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('ended_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Notes &amp; Observations</h2>
            <div class="grid grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Condition Notes</label>
                    <textarea name="condition_notes" rows="3"
                              placeholder="General condition of the terminal..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('condition_notes') }}</textarea>
                    @error('condition_notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issues Found</label>
                    <textarea name="issues_found" rows="3"
                              placeholder="List any issues discovered..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('issues_found') }}</textarea>
                    @error('issues_found')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Corrective Action Taken</label>
                    <textarea name="corrective_action" rows="3"
                              placeholder="What was done to fix or escalate..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('corrective_action') }}</textarea>
                    @error('corrective_action')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visit Summary</label>
                    <textarea name="visit_summary" rows="3"
                              placeholder="Overall summary of the visit..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('visit_summary') }}</textarea>
                    @error('visit_summary')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('visits.index') }}"
               class="px-5 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 text-sm bg-[#1a3a5c] text-white font-medium rounded-lg hover:bg-[#152e4a] transition-colors">
                Log Site Visit
            </button>
        </div>

    </form>
</div>
@endsection
