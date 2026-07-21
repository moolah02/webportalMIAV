@extends('layouts.app')
@section('title', 'Visit Details')

@section('content')

{{-- Back nav --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ url()->previous() }}" class="btn-secondary btn-sm">← Back</a>
    <div>
        <h2 class="text-lg font-bold text-gray-800">Visit Details</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ $visit->visit_id ?? 'Visit #'.$visit->id }}</p>
    </div>
    <div class="ml-auto flex gap-2">
        @php
            $statusMap = [
                'open'        => ['badge-blue',   'Open'],
                'in_progress' => ['badge-yellow', 'In Progress'],
                'closed'      => ['badge-green',  'Closed'],
            ];
            [$sCls, $sLbl] = $statusMap[$visit->status ?? 'open'] ?? ['badge-gray', ucfirst($visit->status ?? 'open')];
        @endphp
        <span class="badge {{ $sCls }} text-sm px-3 py-1">{{ $sLbl }}</span>
        @if($visit->outcome)
        @php
            $outMap = [
                'completed'             => ['badge-green',  'Completed'],
                'could_not_access_site' => ['badge-red',    'Could Not Access Site'],
                'parts_required'        => ['badge-yellow', 'Parts Required'],
                'reschedule'            => ['badge-yellow', 'Rescheduled'],
                'terminal_not_found'    => ['badge-gray',   'Terminal Not Found'],
                'terminal_relocated'    => ['badge-blue',   'Terminal Relocated'],
            ];
            [$oCls, $oLbl] = $outMap[$visit->outcome] ?? ['badge-gray', ucwords(str_replace('_',' ',$visit->outcome))];
        @endphp
        <span class="badge {{ $oCls }} text-sm px-3 py-1">{{ $oLbl }}</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ====== LEFT / MAIN ====== --}}
    <div class="lg:col-span-2 flex flex-col gap-6">

        {{-- Visit Overview --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold">Visit Overview</span>
            </div>
            <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Visit ID</div>
                    <div class="text-sm font-mono font-semibold text-navy">{{ $visit->visit_id ?? '#'.$visit->id }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Date</div>
                    <div class="text-sm font-medium">
                        {{ optional($visit->started_at ?? $visit->visit_date)->format('M j, Y') ?? '—' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Time</div>
                    <div class="text-sm">
                        {{ optional($visit->started_at)->format('g:i A') ?? '—' }}
                        @if($visit->ended_at)
                            → {{ $visit->ended_at->format('g:i A') }}
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Duration</div>
                    <div class="text-sm font-medium">
                        @if($visit->duration_minutes)
                            {{ floor($visit->duration_minutes/60) }}h {{ $visit->duration_minutes % 60 }}m
                        @else
                            —
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Visit Type</div>
                    <div class="text-sm">{{ $visit->visit_type ?? 'Site Visit' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Service Type</div>
                    <div class="text-sm">{{ $visit->service_type ?? '—' }}</div>
                </div>
                @if($visit->jobAssignment)
                <div class="col-span-2 sm:col-span-3">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Job Assignment</div>
                    <div class="text-sm font-medium">
                        {{ $visit->jobAssignment->assignment_id }}
                        @if($visit->jobAssignment->project)
                            — {{ $visit->jobAssignment->project->project_name }}
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Terminal Status --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold">Terminal Condition</span>
            </div>
            <div class="p-5">
                @php
                    $ts = $visit->terminal_status_during_visit ?? $visit->terminal_status;
                    $tsMap = [
                        'active'            => ['badge-green',  'Active'],
                        'inactive'          => ['badge-red',    'Inactive'],
                        'not_found'         => ['badge-gray',   'Not Found'],
                        'relocated'         => ['badge-blue',   'Relocated'],
                        'replaced'          => ['badge-yellow', 'Replaced'],
                        'working'           => ['badge-green',  'Working'],
                        'not_working'       => ['badge-red',    'Not Working'],
                        'needs_maintenance' => ['badge-yellow', 'Needs Maintenance'],
                    ];
                    [$tsCls, $tsLbl] = $tsMap[$ts] ?? ['badge-gray', ucwords(str_replace('_',' ',$ts ?? 'Unknown'))];

                    $tcMap = [
                        'good'    => ['badge-green',  'Good'],
                        'fair'    => ['badge-yellow', 'Fair'],
                        'poor'    => ['badge-red',    'Poor'],
                        'damaged' => ['badge-red',    'Damaged'],
                    ];
                    $tc = $visit->terminal_condition;
                    [$tcCls, $tcLbl] = $tc ? ($tcMap[$tc] ?? ['badge-gray', ucwords($tc)]) : [null, null];
                @endphp
                <div class="mb-4 flex gap-6 flex-wrap">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Status During Visit</div>
                        <span class="badge {{ $tsCls }} text-sm px-3 py-1">{{ $tsLbl }}</span>
                    </div>
                    @if($tc)
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Terminal Condition</div>
                        <span class="badge {{ $tcCls }} text-sm px-3 py-1">{{ $tcLbl }}</span>
                    </div>
                    @endif
                </div>

                @if($visit->condition_notes)
                <div class="mb-4">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Condition Notes</div>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $visit->condition_notes }}</p>
                </div>
                @endif

                @if($visit->issues_found && count((array)$visit->issues_found))
                <div class="mb-4">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-2">Issues Found</div>
                    <ul class="space-y-1">
                        @foreach((array)$visit->issues_found as $issue)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-red-400 mt-0.5">⚠</span> {{ $issue }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($visit->corrective_action)
                <div class="mb-4">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Corrective Action</div>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $visit->corrective_action }}</p>
                </div>
                @endif

                @if($visit->recommended_next_action)
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Recommended Next Action</div>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $visit->recommended_next_action }}</p>
                </div>
                @endif

                @if(!$visit->condition_notes && !$visit->issues_found && !$visit->corrective_action && !$visit->recommended_next_action)
                <p class="text-sm text-gray-400 italic">No condition notes recorded.</p>
                @endif
            </div>
        </div>

        {{-- Summary --}}
        @if($visit->visit_summary || $visit->comments)
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold">Visit Summary</span>
            </div>
            <div class="p-5 space-y-4">
                @if($visit->visit_summary)
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Summary</div>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $visit->visit_summary }}</p>
                </div>
                @endif
                @if($visit->comments)
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Comments</div>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $visit->comments }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Sign-off --}}
        @if($visit->merchant_sign_off_name || $visit->merchant_signature_path)
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold">Merchant Sign-off</span>
            </div>
            <div class="p-5 flex items-center gap-5">
                @if($visit->merchant_signature_path)
                    <img src="{{ asset($visit->merchant_signature_path) }}" alt="Signature"
                         class="border border-gray-200 rounded" style="max-height:80px; max-width:200px">
                @endif
                @if($visit->merchant_sign_off_name)
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Signed by</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $visit->merchant_sign_off_name }}</div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Visit History for this Terminal --}}
        @if($history->count())
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold">
                    Visit History — {{ $visit->posTerminal?->terminal_id ?? 'This Terminal' }}
                </span>
                <span class="text-xs text-gray-400">{{ $history->count() }} previous visit(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Visit ID</th>
                            <th>Date</th>
                            <th>Technician</th>
                            <th>Status</th>
                            <th>Terminal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $h)
                        @php
                            $hts = $h->terminal_status_during_visit ?? $h->terminal_status;
                            $htsMap = [
                                'active'            => ['badge-green',  'Active'],
                                'inactive'          => ['badge-red',    'Inactive'],
                                'not_found'         => ['badge-gray',   'Not Found'],
                                'relocated'         => ['badge-blue',   'Relocated'],
                                'replaced'          => ['badge-yellow', 'Replaced'],
                                'working'           => ['badge-green',  'Working'],
                                'not_working'       => ['badge-red',    'Not Working'],
                                'needs_maintenance' => ['badge-yellow', 'Needs Maint.'],
                            ];
                            [$htsCls, $htsLbl] = $htsMap[$hts] ?? ['badge-gray', '—'];
                        @endphp
                        <tr>
                            <td><span class="font-mono text-xs font-semibold" style="color:#1a3a5c">{{ $h->visit_id ?? '#'.$h->id }}</span></td>
                            <td>
                                <div class="text-sm">{{ optional($h->started_at ?? $h->visit_date)->format('M j, Y') ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="text-sm">
                                    {{ $h->technician ? $h->technician->first_name.' '.$h->technician->last_name : '—' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $hsCls = match($h->status) { 'closed' => 'badge-green', 'in_progress' => 'badge-yellow', default => 'badge-blue' };
                                @endphp
                                <span class="badge {{ $hsCls }}">{{ ucfirst($h->status ?? 'open') }}</span>
                            </td>
                            <td><span class="badge {{ $htsCls }}">{{ $htsLbl }}</span></td>
                            <td>
                                <a href="{{ route('site_visits.show', $h) }}" class="btn-secondary btn-sm">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    {{-- ====== RIGHT / SIDEBAR ====== --}}
    <div class="flex flex-col gap-6">

        {{-- Technician Card --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold">Technician</span>
            </div>
            <div class="p-5">
                @if($visit->technician)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:#1a3a5c">
                        {{ substr($visit->technician->first_name,0,1) }}{{ substr($visit->technician->last_name,0,1) }}
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-800">
                            {{ $visit->technician->first_name }} {{ $visit->technician->last_name }}
                        </div>
                        <div class="text-xs text-gray-500">{{ $visit->technician->employee_number ?? '' }}</div>
                    </div>
                </div>
                @if($visit->technician->phone ?? false)
                <div class="text-xs text-gray-500 mb-1">📞 {{ $visit->technician->phone }}</div>
                @endif
                @if($visit->technician->email ?? false)
                <div class="text-xs text-gray-500">✉ {{ $visit->technician->email }}</div>
                @endif
                @else
                <p class="text-sm text-gray-400 italic">No technician assigned.</p>
                @endif
            </div>
        </div>

        {{-- Terminal Card --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold">Terminal</span>
            </div>
            <div class="p-5 space-y-3">
                @if($visit->posTerminal)
                <div>
                    <div class="text-xs text-gray-400 mb-0.5">Terminal ID</div>
                    <div class="text-sm font-semibold text-navy">{{ $visit->posTerminal->terminal_id }}</div>
                </div>
                @if($visit->posTerminal->merchant_name)
                <div>
                    <div class="text-xs text-gray-400 mb-0.5">Merchant</div>
                    <div class="text-sm font-medium">{{ $visit->posTerminal->merchant_name }}</div>
                </div>
                @endif
                @if($visit->posTerminal->physical_address ?? false)
                <div>
                    <div class="text-xs text-gray-400 mb-0.5">Address</div>
                    <div class="text-sm text-gray-700">{{ $visit->posTerminal->physical_address }}</div>
                </div>
                @endif
                @if($visit->posTerminal->region ?? false)
                <div>
                    <div class="text-xs text-gray-400 mb-0.5">Region</div>
                    <div class="text-sm text-gray-700">{{ $visit->posTerminal->region->name }}</div>
                </div>
                @endif
                @if($visit->posTerminal->client)
                <div>
                    <div class="text-xs text-gray-400 mb-0.5">Client</div>
                    <div class="text-sm text-gray-700">{{ $visit->posTerminal->client->company_name }}</div>
                </div>
                @endif
                @if($visit->contact_person ?? false)
                <div>
                    <div class="text-xs text-gray-400 mb-0.5">Contact</div>
                    <div class="text-sm">{{ $visit->contact_person }}</div>
                    @if($visit->phone_number ?? false)
                    <div class="text-xs text-gray-500">{{ $visit->phone_number }}</div>
                    @endif
                </div>
                @endif
                @else
                <p class="text-sm text-gray-400 italic">No terminal linked.</p>
                @endif
            </div>
        </div>

        {{-- Attachments --}}
        @if($visit->attachments && $visit->attachments->count())
        <div class="ui-card">
            <div class="ui-card-header">
                <span class="text-sm font-semibold">Attachments ({{ $visit->attachments->count() }})</span>
            </div>
            <div class="p-4 grid grid-cols-2 gap-3">
                @foreach($visit->attachments as $att)
                    @if($att->type === 'photo')
                    <a href="{{ asset($att->path) }}" target="_blank" class="block rounded overflow-hidden border border-gray-200 hover:opacity-80 transition">
                        <img src="{{ asset($att->path) }}" alt="{{ $att->caption ?? 'Photo' }}" class="w-full object-cover" style="height:100px">
                        @if($att->caption)
                        <div class="text-xs text-gray-500 px-2 py-1 truncate">{{ $att->caption }}</div>
                        @endif
                    </a>
                    @else
                    <a href="{{ asset($att->path) }}" target="_blank"
                       class="flex items-center gap-2 p-2 rounded border border-gray-200 hover:bg-gray-50 transition text-sm text-gray-700">
                        📎 {{ $att->caption ?? basename($att->path) }}
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
