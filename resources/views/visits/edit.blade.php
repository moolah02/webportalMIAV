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

            <form method="POST" action="{{ route('visits.update', $visit) }}" class="space-y-5" enctype="multipart/form-data">
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

                {{-- Existing evidence --}}
                @php $evidence = is_array($visit->evidence) ? $visit->evidence : []; @endphp
                @if(count($evidence))
                <div>
                    <label class="ui-label">Existing Evidence</label>
                    <div class="space-y-2">
                        @foreach($evidence as $i => $url)
                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                            @if(\Illuminate\Support\Str::startsWith($url, ['http://','https://','/storage/']))
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline flex-1 truncate">&#128206; {{ basename($url) }}</a>
                            @else
                                <span class="flex-1 truncate text-gray-600">&#128206; {{ $url }}</span>
                            @endif
                            <label class="flex items-center gap-1.5 text-red-600 cursor-pointer whitespace-nowrap">
                                <input type="checkbox" name="remove_evidence[]" value="{{ $i }}" class="rounded border-gray-300">
                                Remove
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Check "Remove" then save to delete a file.</p>
                </div>
                @endif

                {{-- Upload new evidence --}}
                <div>
                    <label class="ui-label">Add Evidence <span class="text-gray-400 normal-case font-normal">(photos, documents — max 5MB each)</span></label>
                    <input type="file" name="new_evidence[]" multiple accept="image/*,.pdf,.doc,.docx"
                           class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-gray-300 file:text-sm file:font-medium file:bg-white file:text-gray-700 hover:file:bg-gray-50 cursor-pointer border border-gray-300 rounded-lg p-1">
                    @error('new_evidence.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
