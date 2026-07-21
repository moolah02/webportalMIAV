@extends('layouts.app')
@section('title', 'Assignment Visits')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-800">Site Visits</h2>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ $jobAssignment->project?->project_name ?? 'No Project' }}
            &mdash; {{ $jobAssignment->client?->company_name ?? 'No Client' }}
        </p>
    </div>
    <a href="{{ route('site_visits.index') }}" class="btn-secondary">Back</a>
</div>

<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">Visits ({{ $visits->count() }})</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Visit ID</th>
                    <th>Terminal</th>
                    <th>Technician</th>
                    <th>Started</th>
                    <th>Status</th>
                    <th>Terminal Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $visit)
                <tr>
                    <td><span class="font-mono text-xs font-semibold text-navy">{{ $visit->visit_id ?? '#'.$visit->id }}</span></td>
                    <td>
                        @if($visit->posTerminal)
                            <div class="font-medium text-sm">{{ $visit->posTerminal->terminal_id }}</div>
                            <div class="text-xs text-gray-500">{{ $visit->posTerminal->merchant_name }}</div>
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($visit->technician)
                            {{ $visit->technician->first_name }} {{ $visit->technician->last_name }}
                        @else
                            <span class="text-gray-400 text-sm">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @if($visit->started_at)
                            <div class="text-sm">{{ $visit->started_at->format('M j, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $visit->started_at->format('g:i A') }}</div>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match($visit->status) {
                                'closed'      => 'badge-green',
                                'in_progress' => 'badge-yellow',
                                default       => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($visit->status ?? 'open') }}</span>
                    </td>
                    <td>
                        @if($visit->terminal_status_during_visit)
                            @php
                                $tsClass = match($visit->terminal_status_during_visit) {
                                    'active', 'working'                   => 'badge-green',
                                    'inactive', 'not_working'             => 'badge-red',
                                    'replaced', 'needs_maintenance'       => 'badge-yellow',
                                    'relocated'                           => 'badge-blue',
                                    default                               => 'badge-gray',
                                };
                            @endphp
                            <span class="badge {{ $tsClass }}">{{ ucwords(str_replace('_', ' ', $visit->terminal_status_during_visit)) }}</span>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('site_visits.show', $visit) }}" class="btn-secondary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <div class="empty-state-msg">No visits recorded for this assignment</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
