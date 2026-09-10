@extends('layouts.app')
@section('title', 'Visit Details')

@push('styles')
<style>
.ss-bar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.ss-layout{display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr);gap:16px;align-items:start}
@media (max-width:1100px){.ss-layout{grid-template-columns:minmax(0,1fr)}}
.ss-col{display:flex;flex-direction:column;gap:16px;min-width:0}
.mv-page .badge{display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.ss-chips{display:flex;flex-wrap:wrap;gap:6px}
.ss-dl{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px 24px;margin:0;padding:16px 18px}
.ss-dl .ss-span{grid-column:1/-1}
.ss-dl dt,.ss-item dt,.ss-pair dt{margin:0 0 3px;font-size:12px;font-weight:500;color:var(--mv-muted)}
.ss-dl dd,.ss-item dd,.ss-pair dd{margin:0;font-size:13.5px;color:var(--mv-ink);word-break:break-word}
.ss-body{padding:16px 18px}
.ss-stack{display:flex;flex-direction:column;gap:14px}
.ss-item{margin:0}
.ss-item dd.ss-text{color:var(--mv-ink-2);line-height:1.55}
.ss-pair{display:flex;flex-wrap:wrap;gap:14px 32px;margin:0}
.ss-strong{font-weight:500;color:var(--mv-ink)}
.ss-sub{display:block;margin-top:2px;font-size:12px;color:var(--mv-muted)}
.ss-muted{color:var(--mv-muted)}
.ss-none{margin:0;font-size:13px;color:var(--mv-muted)}
.ss-num{font-variant-numeric:tabular-nums}
.ss-count{font-size:12px;color:var(--mv-muted);font-variant-numeric:tabular-nums}
.ss-issues{display:flex;flex-direction:column;gap:4px;margin:0;padding:0;list-style:none}
.ss-issues li{display:flex;align-items:center;gap:6px;color:var(--mv-ink-2)}
.ss-issues .mv-i{width:15px;height:15px;color:var(--mv-warn)}
.ss-issues .mv-i.is-ok{color:var(--mv-muted)}
.ss-signoff{display:flex;align-items:center;gap:18px;flex-wrap:wrap}
.ss-signoff img{max-height:80px;max-width:200px;background:var(--mv-surface);border:1px solid var(--mv-line);border-radius:8px}
.ss-id{font-size:12.5px;font-weight:500;color:var(--mv-ink)}
.ss-actions{width:1%;text-align:right}
.ss-person{display:flex;align-items:center;gap:12px}
.ss-avatar{width:36px;height:36px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;background:var(--mv-accent-soft);color:var(--mv-accent-ink);font-size:12.5px;font-weight:600;letter-spacing:.02em}
.ss-name{font-size:13.5px;font-weight:600;color:var(--mv-ink)}
.ss-contact{display:flex;flex-direction:column;gap:6px;margin-top:14px;padding-top:12px;border-top:1px solid var(--mv-line);font-size:12.5px;color:var(--mv-ink-2)}
.ss-contact div{display:flex;align-items:center;gap:8px;min-width:0}
.ss-contact span{min-width:0;word-break:break-all}
.ss-contact .mv-i{width:15px;height:15px;color:var(--mv-muted)}
.ss-photos{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding:14px}
.ss-photos a{display:block;overflow:hidden;border:1px solid var(--mv-line);border-radius:8px}
.ss-photos a:hover{border-color:var(--mv-line-strong)}
.ss-photos img{display:block;width:100%;height:100px;object-fit:cover}
</style>
@endpush

@section('content')

{{-- Toolbar --}}
<div class="ss-bar">
    <a href="{{ url()->previous() }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back</a>
</div>

@php
    $statusMap = [
        'open'        => ['badge-blue',   'Open'],
        'in_progress' => ['badge-yellow', 'In Progress'],
        'closed'      => ['badge-green',  'Closed'],
    ];
    [$sCls, $sLbl] = $statusMap[$visit->status ?? 'open'] ?? ['badge-gray', ucfirst($visit->status ?? 'open')];
@endphp

<div class="ss-layout">

    {{-- ====== LEFT / MAIN ====== --}}
    <div class="ss-col">

        {{-- Visit Overview --}}
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Visit Overview</h3>
                <div class="ss-chips">
                    <span class="badge {{ $sCls }}">{{ $sLbl }}</span>
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
                    <span class="badge {{ $oCls }}">{{ $oLbl }}</span>
                    @endif
                </div>
            </div>
            <dl class="ss-dl">
                <div>
                    <dt>Visit ID</dt>
                    <dd><span class="mv-mono ss-strong">{{ $visit->visit_id ?? '#'.$visit->id }}</span></dd>
                </div>
                <div>
                    <dt>Date</dt>
                    <dd>{{ optional($visit->started_at ?? $visit->visit_date)->format('M j, Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt>Time</dt>
                    <dd class="ss-num">
                        {{ optional($visit->started_at)->format('g:i A') ?? '—' }}
                        @if($visit->ended_at)
                            – {{ $visit->ended_at->format('g:i A') }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Duration</dt>
                    <dd class="ss-num">
                        @if($visit->duration_minutes)
                            {{ floor($visit->duration_minutes/60) }}h {{ $visit->duration_minutes % 60 }}m
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Visit Type</dt>
                    <dd>{{ $visit->visit_type ?? 'Site Visit' }}</dd>
                </div>
                <div>
                    <dt>Service Type</dt>
                    <dd>{{ $visit->service_type ?? '—' }}</dd>
                </div>
                @if($visit->jobAssignment)
                <div class="ss-span">
                    <dt>Job Assignment</dt>
                    <dd>
                        <span class="mv-mono">{{ $visit->jobAssignment->assignment_id }}</span>
                        @if($visit->jobAssignment->project)
                            <span class="ss-muted">— {{ $visit->jobAssignment->project->project_name }}</span>
                        @endif
                    </dd>
                </div>
                @endif
            </dl>
        </section>

        {{-- Terminal Status --}}
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Terminal Condition</h3>
            </div>
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
            <div class="ss-body ss-stack">
                <dl class="ss-pair">
                    <div>
                        <dt>State</dt>
                        <dd><span class="badge {{ $tsCls }}">{{ $tsLbl }}</span></dd>
                    </div>
                    @if($tc)
                    <div>
                        <dt>Terminal Condition</dt>
                        <dd><span class="badge {{ $tcCls }}">{{ $tcLbl }}</span></dd>
                    </div>
                    @endif
                </dl>

                @if($visit->condition_notes)
                <dl class="ss-item">
                    <dt>Condition Notes</dt>
                    <dd class="ss-text">{{ $visit->condition_notes }}</dd>
                </dl>
                @endif

                @if($visit->issues_found && count((array)$visit->issues_found))
                <dl class="ss-item">
                    <dt>Issues Found</dt>
                    <dd>
                        <ul class="ss-issues">
                            @foreach((array)$visit->issues_found as $issue)
                            @php $noIssue = strcasecmp(trim((string) $issue), 'No issues') === 0; @endphp
                            <li><svg class="mv-i {{ $noIssue ? 'is-ok' : '' }}" aria-hidden="true"><use href="#i-{{ $noIssue ? 'check' : 'alert-triangle' }}"/></svg>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    </dd>
                </dl>
                @endif

                @if($visit->corrective_action)
                <dl class="ss-item">
                    <dt>Corrective Action</dt>
                    <dd class="ss-text">{{ $visit->corrective_action }}</dd>
                </dl>
                @endif

                @if($visit->recommended_next_action)
                <dl class="ss-item">
                    <dt>Recommended Next Action</dt>
                    <dd class="ss-text">{{ $visit->recommended_next_action }}</dd>
                </dl>
                @endif

                @if(!$visit->condition_notes && !$visit->issues_found && !$visit->corrective_action && !$visit->recommended_next_action)
                <p class="ss-none">No condition notes recorded.</p>
                @endif
            </div>
        </section>

        {{-- Summary --}}
        @if($visit->visit_summary || $visit->comments)
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Visit Summary</h3>
            </div>
            <div class="ss-body ss-stack">
                @if($visit->visit_summary)
                <dl class="ss-item">
                    <dt>Summary</dt>
                    <dd class="ss-text">{{ $visit->visit_summary }}</dd>
                </dl>
                @endif
                @if($visit->comments)
                <dl class="ss-item">
                    <dt>Comments</dt>
                    <dd class="ss-text">{{ $visit->comments }}</dd>
                </dl>
                @endif
            </div>
        </section>
        @endif

        {{-- Sign-off --}}
        @if($visit->merchant_sign_off_name || $visit->merchant_signature_path)
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Merchant Sign-off</h3>
            </div>
            <div class="ss-body ss-signoff">
                @if($visit->merchant_signature_path)
                    <img src="{{ asset($visit->merchant_signature_path) }}" alt="Signature">
                @endif
                @if($visit->merchant_sign_off_name)
                    <dl class="ss-item">
                        <dt>Signed by</dt>
                        <dd class="ss-strong">{{ $visit->merchant_sign_off_name }}</dd>
                    </dl>
                @endif
            </div>
        </section>
        @endif

        {{-- Visit History for this Terminal --}}
        @if($history->count())
        <section class="ui-card overflow-hidden">
            <div class="ui-card-header">
                <h3>
                    Visit History —
                    @if($visit->posTerminal?->terminal_id)
                        <span class="mv-mono">{{ $visit->posTerminal->terminal_id }}</span>
                    @else
                        This Terminal
                    @endif
                </h3>
                <span class="ss-count">{{ $history->count() }} previous visit(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table w-full">
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
                            $hsCls = match($h->status) { 'closed' => 'badge-green', 'in_progress' => 'badge-yellow', default => 'badge-blue' };
                        @endphp
                        <tr>
                            <td><span class="mv-mono ss-id">{{ $h->visit_id ?? '#'.$h->id }}</span></td>
                            <td class="ss-num">{{ optional($h->started_at ?? $h->visit_date)->format('M j, Y') ?? '—' }}</td>
                            <td>{{ $h->technician ? $h->technician->first_name.' '.$h->technician->last_name : '—' }}</td>
                            <td><span class="badge {{ $hsCls }}">{{ ucfirst($h->status ?? 'open') }}</span></td>
                            <td><span class="badge {{ $htsCls }}">{{ $htsLbl }}</span></td>
                            <td class="ss-actions">
                                <a href="{{ route('site_visits.show', $h) }}" class="action-btn" title="View" aria-label="View visit {{ $h->visit_id ?? $h->id }}"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

    </div>

    {{-- ====== RIGHT / SIDEBAR ====== --}}
    <div class="ss-col">

        {{-- Technician Card --}}
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Technician</h3>
            </div>
            <div class="ss-body">
                @if($visit->technician)
                <div class="ss-person">
                    <span class="ss-avatar" aria-hidden="true">{{ substr($visit->technician->first_name,0,1) }}{{ substr($visit->technician->last_name,0,1) }}</span>
                    <div class="min-w-0">
                        <div class="ss-name">{{ $visit->technician->first_name }} {{ $visit->technician->last_name }}</div>
                        @if($visit->technician->employee_number ?? false)
                        <span class="ss-sub mv-mono">{{ $visit->technician->employee_number }}</span>
                        @endif
                    </div>
                </div>
                @if(($visit->technician->phone ?? false) || ($visit->technician->email ?? false))
                <div class="ss-contact">
                    @if($visit->technician->phone ?? false)
                    <div><svg class="mv-i" aria-hidden="true"><use href="#i-phone"/></svg><span>{{ $visit->technician->phone }}</span></div>
                    @endif
                    @if($visit->technician->email ?? false)
                    <div><svg class="mv-i" aria-hidden="true"><use href="#i-mail"/></svg><span>{{ $visit->technician->email }}</span></div>
                    @endif
                </div>
                @endif
                @else
                <p class="ss-none">No technician assigned.</p>
                @endif
            </div>
        </section>

        {{-- Terminal Card --}}
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Terminal</h3>
            </div>
            <div class="ss-body ss-stack">
                @if($visit->posTerminal)
                <dl class="ss-item">
                    <dt>Terminal ID</dt>
                    <dd><span class="mv-mono ss-strong">{{ $visit->posTerminal->terminal_id }}</span></dd>
                </dl>
                @if($visit->posTerminal->merchant_name)
                <dl class="ss-item">
                    <dt>Merchant</dt>
                    <dd>{{ $visit->posTerminal->merchant_name }}</dd>
                </dl>
                @endif
                @if($visit->posTerminal->physical_address ?? false)
                <dl class="ss-item">
                    <dt>Address</dt>
                    <dd>{{ $visit->posTerminal->physical_address }}</dd>
                </dl>
                @endif
                @if($visit->posTerminal->region ?? false)
                <dl class="ss-item">
                    <dt>Region</dt>
                    <dd>{{ $visit->posTerminal->region->name }}</dd>
                </dl>
                @endif
                @if($visit->posTerminal->client)
                <dl class="ss-item">
                    <dt>Client</dt>
                    <dd>{{ $visit->posTerminal->client->company_name }}</dd>
                </dl>
                @endif
                {{-- $visit is the technician_visits report row; contact details live on
                     the linked tablet visit record, so read them from there. --}}
                @php $rec = $visit->visit; @endphp
                @if($rec?->contact_person)
                <dl class="ss-item">
                    <dt>Contact</dt>
                    <dd>
                        {{ $rec->contact_person }}
                        @if($rec->phone_number)
                        <span class="ss-sub">{{ $rec->phone_number }}</span>
                        @endif
                    </dd>
                </dl>
                @endif

                {{-- Updated contact details captured on this visit (from the mobile app) --}}
                @if($rec && ($rec->new_contact_person || $rec->new_phone_number || $rec->new_physical_address))
                <dl class="ss-item">
                    <dt>New / Updated Contact</dt>
                    <dd>
                        @if($rec->new_contact_person)
                        {{ $rec->new_contact_person }}
                        @endif
                        @if($rec->new_phone_number)
                        <span class="ss-sub">{{ $rec->new_phone_number }}</span>
                        @endif
                        @if($rec->new_physical_address)
                        <span class="ss-sub">{{ $rec->new_physical_address }}</span>
                        @endif
                    </dd>
                </dl>
                @endif
                @else
                <p class="ss-none">No terminal linked.</p>
                @endif
            </div>
        </section>

        {{-- Photos taken on the tablet for this visit (stored on the linked visit record).
             This used to read an "attachments" relation whose model/table were never
             built, which crashed the whole page. --}}
        @php
            $photos = collect($visit->visit?->evidence ?? [])
                ->filter(fn ($u) => is_string($u) && str_starts_with($u, 'http'))
                ->values();
        @endphp
        @if($photos->count())
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Photos</h3>
                <span class="ss-count">{{ $photos->count() }}</span>
            </div>
            <div class="ss-photos">
                @foreach($photos as $url)
                <a href="{{ $url }}" target="_blank" rel="noopener" title="Open photo">
                    <img src="{{ $url }}" alt="Visit photo">
                </a>
                @endforeach
            </div>
        </section>
        @endif

    </div>
</div>

@endsection
