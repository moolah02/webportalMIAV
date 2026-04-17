{{-- resources/views/visits/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="page-title">Visit #{{ $visit->id }}</h1>
            <p class="page-subtitle">Full visit details and terminal information</p>
        </div>
        <a href="{{ route('visits.index') }}" class="btn-secondary">&#x2190; All Visits</a>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 py-3 px-4 text-center font-semibold text-gray-600 text-sm">
            Visit #{{ $visit->id }} Details
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                {{-- Status --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Status</td>
                    <td class="text-gray-700 py-3 px-4">
                        @if($visit->completed_at)
                            <span class="badge badge-green">Completed</span>
                            <span class="ml-2 text-gray-400 text-xs">{{ $visit->completed_at->format('F j, Y \a\t g:i A') }}</span>
                        @else
                            <span class="badge badge-yellow">In Progress</span>
                        @endif
                    </td>
                </tr>
                {{-- Merchant --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Merchant</td>
                    <td class="text-gray-700 py-3 px-4">
                        <div class="font-medium">{{ $visit->merchant_name ?? 'Unknown Merchant' }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">ID: {{ $visit->merchant_id }}</div>
                    </td>
                </tr>
                {{-- Employee --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Employee</td>
                    <td class="text-gray-700 py-3 px-4">
                        <div class="font-medium">{{ optional($visit->employee)->full_name ?? 'Unknown Employee' }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">ID: {{ $visit->employee_id }}</div>
                    </td>
                </tr>
                {{-- Assignment --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Assignment</td>
                    <td class="text-gray-700 py-3 px-4 font-medium">{{ $visit->assignment_id ?? 'No Assignment' }}</td>
                </tr>
                @if(!empty($visit->contact_person))
                {{-- Contact Person --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Contact Person</td>
                    <td class="text-gray-700 py-3 px-4">
                        <div>{{ $visit->contact_person }}</div>
                        @if(!empty($visit->phone_number))
                        <div class="text-xs text-gray-400 mt-0.5">{{ $visit->phone_number }}</div>
                        @endif
                    </td>
                </tr>
                @endif
                {{-- Visit Summary --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Visit Summary</td>
                    <td class="text-gray-700 py-3 px-4">{{ $visit->visit_summary ?: 'No summary provided.' }}</td>
                </tr>
                @if(!empty($visit->action_points))
                {{-- Action Points --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Action Points</td>
                    <td class="text-gray-700 py-3 px-4">{{ $visit->action_points }}</td>
                </tr>
                @endif
                {{-- Primary Terminal --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Primary Terminal</td>
                    <td class="text-gray-700 py-3 px-4">
                        @php $terminal = $visit->getCompleteTerminalInfo(); @endphp
                        @if(!empty($terminal))
                            @if(isset($terminal['found_in_pos_terminals']))
                            <div class="text-xs text-gray-400 mb-2">
                                @if($terminal['found_in_pos_terminals'])
                                    &#x2713; Terminal data
                                @else
                                    &#x26A0; Terminal not found (showing basic data only)
                                @endif
                            </div>
                            @endif
                            <table class="w-full text-xs border border-gray-200 rounded-md overflow-hidden">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Terminal ID</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Status</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Condition</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Model</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Serial No.</th>
                                        @if(!empty($terminal['issues']))
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Issues</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 font-semibold">{{ $terminal['terminal_id'] ?? '&#x2014;' }}</td>
                                        <td class="px-3 py-2">{{ $terminal['status'] ?? ($terminal['current_status'] ?? '&#x2014;') }}</td>
                                        <td class="px-3 py-2">{{ $terminal['condition_status'] ?? ($terminal['condition'] ?? '&#x2014;') }}</td>
                                        <td class="px-3 py-2">{{ $terminal['terminal_model'] ?? '&#x2014;' }}</td>
                                        <td class="px-3 py-2">{{ $terminal['serial_number'] ?? '&#x2014;' }}</td>
                                        @if(!empty($terminal['issues']))
                                        <td class="px-3 py-2 text-red-600">{{ $terminal['issues'] }}</td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                            @if(!empty($terminal['last_service_date']) || !empty($terminal['next_service_due']))
                            <div class="mt-2 text-xs text-gray-400 space-y-0.5">
                                @if(!empty($terminal['last_service_date']))
                                <div>Last Service: {{ $terminal['last_service_date'] }}</div>
                                @endif
                                @if(!empty($terminal['next_service_due']))
                                <div>Next Service Due: {{ $terminal['next_service_due'] }}</div>
                                @endif
                            </div>
                            @endif
                            <details class="mt-2">
                                <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600">View extra terminal details</summary>
                                <div class="bg-gray-50 border border-gray-200 rounded-md p-3 font-mono text-xs text-gray-600 whitespace-pre-wrap max-h-48 overflow-y-auto mt-1.5">{{ json_encode($terminal, JSON_PRETTY_PRINT) }}</div>
                            </details>
                        @else
                            <span class="text-gray-400 italic">No primary terminal data.</span>
                        @endif
                    </td>
                </tr>
                {{-- Other Terminals --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Other Terminals</td>
                    <td class="text-gray-700 py-3 px-4">
                        @php $otherTerminals = is_array($visit->other_terminals_found) ? $visit->other_terminals_found : []; @endphp
                        @if(count($otherTerminals))
                            <div class="text-xs text-gray-400 mb-1.5">Found {{ count($otherTerminals) }} additional terminal(s)</div>
                            <div class="bg-gray-50 border border-gray-200 rounded-md p-3 font-mono text-xs text-gray-600 whitespace-pre-wrap max-h-48 overflow-y-auto">{{ json_encode($otherTerminals, JSON_PRETTY_PRINT) }}</div>
                        @else
                            <span class="text-gray-400 italic">None found.</span>
                        @endif
                    </td>
                </tr>
                {{-- Evidence --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Evidence</td>
                    <td class="text-gray-700 py-3 px-4">
                        @php $evidence = is_array($visit->evidence) ? $visit->evidence : []; @endphp
                        @if(count($evidence))
                            @foreach($evidence as $idx => $e)
                            <div class="mb-2">
                                <span class="font-medium">Evidence {{ $idx + 1 }}:</span>
                                @if(\Illuminate\Support\Str::startsWith($e, ['http://','https://','/storage/']))
                                    <a href="{{ $e }}" target="_blank" rel="noopener" class="text-blue-600 font-medium hover:underline ml-1">View Evidence</a>
                                @else
                                    <span class="ml-1">{{ $e }}</span>
                                @endif
                            </div>
                            @endforeach
                        @else
                            <span class="text-gray-400 italic">No evidence.</span>
                        @endif
                    </td>
                </tr>
                @if(!empty($visit->signature))
                {{-- Signature --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Signature</td>
                    <td class="text-gray-700 py-3 px-4">
                        @if(\Illuminate\Support\Str::startsWith($visit->signature, ['data:image/', 'data:application/']))
                            <div class="text-xs text-gray-400 mb-1.5">Digital signature captured</div>
                            <div class="bg-gray-50 border border-gray-200 rounded-md p-3 font-mono text-xs text-gray-600 whitespace-pre-wrap max-h-24 overflow-y-auto">
                                {{ \Illuminate\Support\Str::limit($visit->signature, 200) }}...
                            </div>
                        @else
                            {{ $visit->signature }}
                        @endif
                    </td>
                </tr>
                @endif
                {{-- Created --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Created</td>
                    <td class="text-gray-700 py-3 px-4">{{ $visit->created_at ? $visit->created_at->format('F j, Y \a\t g:i A') : '&#x2014;' }}</td>
                </tr>
                {{-- Updated --}}
                <tr class="hover:bg-gray-50">
                    <td class="w-44 bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wide py-3 px-4 align-top whitespace-nowrap">Updated</td>
                    <td class="text-gray-700 py-3 px-4">{{ $visit->updated_at ? $visit->updated_at->format('F j, Y \a\t g:i A') : '&#x2014;' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection