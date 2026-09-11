@extends('layouts.app')
@section('title', 'Assignment Visits')

@push('styles')
<style>
.av-bar{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:16px}
.av-context{display:flex;align-items:baseline;gap:8px;flex-wrap:wrap;min-width:0}
.av-context h2{margin:0;font-size:14px;font-weight:600;color:var(--mv-ink)}
.av-context span{font-size:13px;color:var(--mv-muted)}
.av-count{font-size:12px;color:var(--mv-muted);font-variant-numeric:tabular-nums}
.mv-page .badge{display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.av-id{font-size:12.5px;font-weight:500;color:var(--mv-ink)}
.av-strong{font-weight:500;color:var(--mv-ink)}
.av-sub{margin-top:2px;font-size:12px;color:var(--mv-muted)}
.av-none{color:var(--mv-muted)}
.av-nowrap{white-space:nowrap}
.av-actions{width:1%;text-align:right}
.av-empty{padding:40px 16px;text-align:center}
.av-empty .empty-state-icon .mv-i{display:block;margin:0 auto}
.av-empty p{margin:0;font-size:13.5px;color:var(--mv-ink-2)}
</style>
@endpush

@section('content')
{{-- Toolbar --}}
<div class="av-bar">
    <a href="{{ route('site_visits.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back</a>
    <div class="av-context">
        <h2>Site Visits</h2>
        <span>
            {{ $jobAssignment->project?->project_name ?? 'No Project' }}
            &mdash; {{ $jobAssignment->client?->company_name ?? 'No Client' }}
        </span>
    </div>
</div>

<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <h3>Visits</h3>
        <span class="av-count">{{ $visits->count() }} {{ $visits->count() === 1 ? 'visit' : 'visits' }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>Visit ID</th>
                    <th>Terminal</th>
                    <th>Technician</th>
                    <th>Started</th>
                    <th>Status</th>
                    <th>State</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $visit)
                <tr>
                    <td><span class="mv-mono av-id">{{ $visit->visit_id ?? '#'.$visit->id }}</span></td>
                    <td>
                        @if($visit->posTerminal)
                            <div class="mv-mono av-strong">{{ $visit->posTerminal->terminal_id }}</div>
                            <div class="av-sub">{{ $visit->posTerminal->merchant_name }}</div>
                        @else
                            <span class="av-none">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($visit->technician)
                            {{ $visit->technician->first_name }} {{ $visit->technician->last_name }}
                        @else
                            <span class="av-none">Unassigned</span>
                        @endif
                    </td>
                    <td class="av-nowrap">
                        @if($visit->started_at)
                            <div>{{ $visit->started_at->format('M j, Y') }}</div>
                            <div class="av-sub">{{ $visit->started_at->format('g:i A') }}</div>
                        @else
                            <span class="av-none">—</span>
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
                            <span class="av-none">—</span>
                        @endif
                    </td>
                    <td class="av-actions">
                        <a href="{{ route('site_visits.show', $visit) }}" class="action-btn" title="View" aria-label="View visit {{ $visit->visit_id ?? $visit->id }}"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="av-empty">
                            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-clipboard"/></svg></div>
                            <p>No visits recorded for this assignment</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
