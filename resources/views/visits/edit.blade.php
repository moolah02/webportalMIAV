@extends('layouts.app')
@section('title', 'Edit Visit #'.$visit->id)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-5">
        <a href="{{ route('visits.show', $visit) }}" class="btn-secondary">&#x2190; Back to Visit</a>
    </div>

    @if(session('success'))
        <div class="flash-success mb-5">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="flash-error mb-5">
            @foreach($errors->all() as $err)<div>&#x26A0; {{ $err }}</div>@endforeach
        </div>
    @endif

    <div class="ui-card">
        <div class="ui-card-header">
            <h2 class="text-sm font-semibold text-gray-800">Edit Visit #{{ $visit->id }}</h2>
            <span class="badge {{ $visit->completed_at ? 'badge-green' : 'badge-yellow' }}">
                {{ $visit->completed_at ? 'Completed' : 'In Progress' }}
            </span>
        </div>
        <div class="ui-card-body">
            {{-- Read-only context --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-5 grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-500">Merchant:</span> <span class="font-medium">{{ $visit->merchant_name ?? '—' }}</span></div>
                <div><span class="text-gray-500">Employee:</span> <span class="font-medium">{{ optional($visit->employee)->full_name ?? $visit->employee_id }}</span></div>
                <div><span class="text-gray-500">Assignment:</span> <span class="font-medium">{{ $visit->assignment_id ?? 'None' }}</span></div>
                <div><span class="text-gray-500">Created:</span> <span class="font-medium">{{ $visit->created_at?->format('M j, Y') }}</span></div>
            </div>

            <form method="POST" action="{{ route('visits.update', $visit) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="ui-label">Corrective Action Taken</label>
                    <select name="action_points" class="ui-select">
                        <option value="">— Select action —</option>
                        @foreach(['Resolved','No action needed','To collect device','Follow-up needed','Replacement needed'] as $opt)
                            <option value="{{ $opt }}" {{ old('action_points', $visit->action_points) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('action_points')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="ui-label">Visit Summary</label>
                    <textarea name="visit_summary" rows="4" class="ui-textarea resize-y"
                              placeholder="Overall summary of the visit…">{{ old('visit_summary', $visit->visit_summary) }}</textarea>
                    @error('visit_summary')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="ui-label">
                        Completed At
                        <span class="text-gray-400 normal-case font-normal text-xs">(clear to reopen visit)</span>
                    </label>
                    <input type="datetime-local" name="completed_at"
                           value="{{ old('completed_at', $visit->completed_at?->format('Y-m-d\TH:i')) }}"
                           class="ui-input">
                    @error('completed_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('visits.show', $visit) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
