{{-- resources/views/employee/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'My Dashboard')

@push('styles')
<style>
  .ed { display: flex; flex-direction: column; gap: 20px; }
  .ed-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
  .ed-hello { font-size: 20px; font-weight: 600; letter-spacing: -.01em; color: var(--mv-ink); margin: 0; }
  .ed-date { font-size: 13.5px; color: var(--mv-muted); margin-top: 2px; }
  .ed-actions { display: flex; gap: 8px; flex-wrap: wrap; }

  .ed-kpis { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 1px; background: var(--mv-line); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
  .ed-kpi { background: var(--mv-surface); padding: 16px 18px; display: flex; flex-direction: column; gap: 3px; text-decoration: none !important; color: inherit; min-width: 0; }
  a.ed-kpi:hover { background: var(--mv-surface-2); }
  .ed-kpi-label { display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 500; color: var(--mv-muted); }
  .ed-kpi-label .mv-i { width: 15px; height: 15px; }
  .ed-kpi-value { font-size: 26px; font-weight: 600; letter-spacing: -.02em; line-height: 1.15; color: var(--mv-ink); font-variant-numeric: tabular-nums; }

  .ed-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr); gap: 20px; align-items: start; }
  .ed-col { display: flex; flex-direction: column; gap: 20px; min-width: 0; }
  .ed-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; min-width: 0; }
  .ed-card-head { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
  .ed-card-head h2 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
  .ed-card-head .ed-link { margin-left: auto; font-size: 13px; font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
  .ed-card-head .ed-link:hover { text-decoration: underline; }
  .ed-card-body { padding: 16px 18px; }

  .ed-note { display: flex; align-items: center; gap: 12px; padding: 11px 16px; border-radius: 10px; border: 1px solid var(--mv-line); background: var(--mv-surface); text-decoration: none !important; color: var(--mv-ink-2); font-size: 13.5px; }
  .ed-note:hover { border-color: var(--mv-line-strong); }
  .ed-note .ed-note-ic { width: 30px; height: 30px; border-radius: 8px; display: grid; place-items: center; flex-shrink: 0; }
  .ed-note.info .ed-note-ic { background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
  .ed-note.warn .ed-note-ic { background: var(--mv-warn-soft); color: var(--mv-warn); }
  .ed-note span.grow { flex: 1; }
  .ed-note .go { font-weight: 500; color: var(--mv-accent-ink); display: inline-flex; align-items: center; gap: 4px; }

  .ed-list { display: flex; flex-direction: column; }
  .ed-row { display: flex; align-items: center; gap: 14px; padding: 12px 18px; text-decoration: none !important; color: var(--mv-ink-2); font-size: 13.5px; }
  .ed-row + .ed-row { border-top: 1px solid var(--mv-line); }
  a.ed-row:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
  .ed-row .grow { flex: 1; min-width: 0; }
  .ed-row .title { color: var(--mv-ink); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .ed-row .sub { font-size: 12.5px; color: var(--mv-muted); display: flex; gap: 12px; flex-wrap: wrap; margin-top: 2px; }
  .ed-row .sub span { display: inline-flex; align-items: center; gap: 5px; }
  .ed-row .sub .mv-i { width: 13px; height: 13px; }
  .ed-row .ic { width: 32px; height: 32px; border-radius: 8px; background: var(--mv-surface-2); color: var(--mv-ink-2); display: grid; place-items: center; flex-shrink: 0; }
  .ed-row .ic .mv-i { width: 17px; height: 17px; }
  .ed-row .chev { color: var(--mv-line-strong); width: 16px; height: 16px; }
  .ed-cal { width: 46px; flex-shrink: 0; border: 1px solid var(--mv-line); border-radius: 8px; text-align: center; overflow: hidden; background: var(--mv-surface); }
  .ed-cal b { display: block; font-size: 17px; font-weight: 600; color: var(--mv-ink); line-height: 1.5; font-variant-numeric: tabular-nums; }
  .ed-cal span { display: block; font-size: 10.5px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--mv-accent-ink); background: var(--mv-accent-soft); padding: 1px 0; }
  .ed-mono { font-family: var(--mv-mono); font-size: 12.5px; }

  .chip { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 500; padding: 2px 8px; border-radius: 6px; white-space: nowrap; }
  .chip.good { background: var(--mv-good-soft); color: var(--mv-good); }
  .chip.warn { background: var(--mv-warn-soft); color: var(--mv-warn); }
  .chip.crit { background: var(--mv-crit-soft); color: var(--mv-crit); }
  .chip.info { background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
  .chip.neutral { background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }

  .ed-dl { display: flex; flex-direction: column; }
  .ed-dl div { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 8px 0; font-size: 13.5px; color: var(--mv-ink-2); }
  .ed-dl div + div { border-top: 1px solid var(--mv-line); }
  .ed-dl b { font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
  .ed-meter { height: 6px; border-radius: 3px; background: var(--mv-surface-2); overflow: hidden; border: 1px solid var(--mv-line); }
  .ed-meter i { display: block; height: 100%; border-radius: 3px; background: var(--mv-good); }
  .ed-progress { padding-top: 12px; margin-top: 4px; border-top: 1px solid var(--mv-line); display: flex; flex-direction: column; gap: 6px; }
  .ed-progress div:first-child { display: flex; justify-content: space-between; font-size: 12.5px; color: var(--mv-muted); }

  .ed-empty { display: flex; flex-direction: column; align-items: flex-start; gap: 6px; padding: 18px; color: var(--mv-muted); font-size: 13.5px; }
  .ed-empty strong { color: var(--mv-ink); font-weight: 500; }
  .ed-empty a { color: var(--mv-accent-ink); font-weight: 500; text-decoration: none; }

  @media (max-width: 1280px) { .ed-kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
  @media (max-width: 1100px) { .ed-grid { grid-template-columns: minmax(0, 1fr); } }
  @media (max-width: 640px) { .ed-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
@endpush

@section('content')
@php
    $jobs = $stats['jobs'] ?? [];
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $hasMine = Route::has('jobs.mine');
    $hasRequests = Route::has('asset-requests.index');
    $statusChip = fn ($st) => match (strtolower((string) $st)) {
        'completed', 'approved', 'fulfilled' => 'good',
        'in_progress', 'pending'             => 'warn',
        'rejected', 'cancelled', 'overdue'   => 'crit',
        'assigned'                           => 'info',
        default                              => 'neutral',
    };
@endphp

<div class="ed">

    <div class="ed-head">
        <div>
            <p class="ed-hello">{{ $greeting }}, {{ $me->first_name ?? auth()->user()->first_name }}</p>
            <div class="ed-date">{{ now()->format('l j F Y') }}</div>
        </div>
        <div class="ed-actions">
            @if($hasMine)
            <a href="{{ route('jobs.mine') }}" class="btn-primary btn-sm"><svg class="mv-i mv-i-sm"><use href="#i-list-todo"/></svg>My Jobs</a>
            @endif
            @if(Route::has('asset-requests.create'))
            <a href="{{ route('asset-requests.create') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm"><use href="#i-plus"/></svg>New Request</a>
            @endif
        </div>
    </div>

    @if(($jobs['today'] ?? 0) > 0 || ($stats['pending_approvals'] ?? 0) > 0)
    <div style="display:flex;flex-direction:column;gap:8px">
        @if(($jobs['today'] ?? 0) > 0)
        <a href="{{ $hasMine ? route('jobs.mine') : '#' }}" class="ed-note info">
            <span class="ed-note-ic"><svg class="mv-i"><use href="#i-calendar"/></svg></span>
            <span class="grow">You have {{ $jobs['today'] }} job {{ \Illuminate\Support\Str::plural('assignment', $jobs['today']) }} for today</span>
            <span class="go">Open<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></span>
        </a>
        @endif
        @if(($stats['pending_approvals'] ?? 0) > 0)
        <a href="{{ $hasRequests ? route('asset-requests.index') : '#' }}" class="ed-note warn">
            <span class="ed-note-ic"><svg class="mv-i"><use href="#i-hourglass"/></svg></span>
            <span class="grow">{{ $stats['pending_approvals'] }} of your asset requests {{ $stats['pending_approvals'] == 1 ? 'is' : 'are' }} pending approval</span>
            <span class="go">Review<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></span>
        </a>
        @endif
    </div>
    @endif

    <div class="ed-kpis">
        <div class="ed-kpi">
            <span class="ed-kpi-label"><svg class="mv-i"><use href="#i-calendar"/></svg>Jobs Today</span>
            <span class="ed-kpi-value">{{ $jobs['today'] ?? 0 }}</span>
        </div>
        <a href="{{ $hasMine ? route('jobs.mine') : '#' }}" class="ed-kpi">
            <span class="ed-kpi-label"><svg class="mv-i"><use href="#i-clipboard"/></svg>Assigned</span>
            <span class="ed-kpi-value">{{ $jobs['assigned'] ?? 0 }}</span>
        </a>
        <div class="ed-kpi">
            <span class="ed-kpi-label"><svg class="mv-i"><use href="#i-refresh"/></svg>In Progress</span>
            <span class="ed-kpi-value">{{ $jobs['in_progress'] ?? 0 }}</span>
        </div>
        <div class="ed-kpi">
            <span class="ed-kpi-label"><svg class="mv-i"><use href="#i-check-circle"/></svg>Completed</span>
            <span class="ed-kpi-value">{{ $jobs['completed'] ?? 0 }}</span>
        </div>
        <a href="{{ $hasRequests ? route('asset-requests.index') : '#' }}" class="ed-kpi">
            <span class="ed-kpi-label"><svg class="mv-i"><use href="#i-box"/></svg>My Requests</span>
            <span class="ed-kpi-value">{{ $stats['my_requests'] ?? 0 }}</span>
        </a>
        <div class="ed-kpi">
            <span class="ed-kpi-label"><svg class="mv-i"><use href="#i-hourglass"/></svg>Pending Approvals</span>
            <span class="ed-kpi-value">{{ $stats['pending_approvals'] ?? 0 }}</span>
        </div>
    </div>

    <div class="ed-grid">
        <div class="ed-col">

            <div class="ed-card">
                <div class="ed-card-head">
                    <h2>Upcoming Assignments</h2>
                    @if($hasMine)<a href="{{ route('jobs.mine') }}" class="ed-link">View All Jobs<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></a>@endif
                </div>
                <div class="ed-list">
                    @forelse(($upcomingAssignments ?? collect())->take(6) as $assignment)
                    @php $when = $assignment->scheduled_date ? \Illuminate\Support\Carbon::parse($assignment->scheduled_date) : null; @endphp
                    <a href="{{ Route::has('jobs.show') ? route('jobs.show', $assignment->id) : '#' }}" class="ed-row">
                        <div class="ed-cal">
                            <span>{{ $when ? $when->format('M') : 'TBD' }}</span>
                            <b>{{ $when ? $when->format('j') : '–' }}</b>
                        </div>
                        <div class="grow">
                            <div class="title">{{ $assignment->list_title ?: $assignment->assignment_id }}</div>
                            <div class="sub">
                                <span class="ed-mono">{{ $assignment->assignment_id }}</span>
                                <span><svg class="mv-i"><use href="#i-wrench"/></svg>{{ \Illuminate\Support\Str::headline($assignment->service_type ?? 'General') }}</span>
                                <span><svg class="mv-i"><use href="#i-card"/></svg>{{ $assignment->terminal_count ?? 0 }} {{ \Illuminate\Support\Str::plural('terminal', $assignment->terminal_count ?? 0) }}</span>
                            </div>
                        </div>
                        <span class="chip {{ $statusChip($assignment->status) }}">{{ \Illuminate\Support\Str::headline($assignment->status ?? 'assigned') }}</span>
                        <svg class="mv-i chev"><use href="#i-chevron-right"/></svg>
                    </a>
                    @empty
                    <div class="ed-empty">
                        <strong>No upcoming assignments</strong>
                        New jobs assigned to you will show here.
                        @if($hasMine)<a href="{{ route('jobs.mine') }}">My Assignments</a>@endif
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="ed-card">
                <div class="ed-card-head"><h2>Recent Activity</h2></div>
                <div class="ed-list" style="max-height:400px;overflow-y:auto">
                    @php $recent = $stats['recent_activity'] ?? []; @endphp
                    @forelse($recent as $item)
                        @if(is_array($item))
                        @php
                            $d = $item['date'] ?? null;
                            $dText = $d instanceof \DateTimeInterface ? \Illuminate\Support\Carbon::instance($d)->diffForHumans() : ($d ?: 'Recently');
                        @endphp
                        <div class="ed-row">
                            <span class="ic"><svg class="mv-i"><use href="#i-{{ ($item['type'] ?? '') === 'asset_request' ? 'box' : 'activity' }}"/></svg></span>
                            <div class="grow">
                                <div class="title">{{ $item['label'] ?? $item['title'] ?? 'Activity Update' }}</div>
                                @if(!empty($item['details']))<div class="sub">{{ $item['details'] }}</div>@endif
                            </div>
                            @if(!empty($item['status']))<span class="chip {{ $statusChip($item['status']) }}">{{ \Illuminate\Support\Str::headline($item['status']) }}</span>@endif
                            <span class="sub" style="flex-shrink:0;margin:0">{{ $dText }}</span>
                        </div>
                        @else
                        <div class="ed-row"><span class="grow">{{ (string) $item }}</span></div>
                        @endif
                    @empty
                    <div class="ed-empty"><strong>No recent activity</strong>Your activity will appear here once you start working on jobs.</div>
                    @endforelse
                </div>
            </div>

            @if(isset($stats['performance']))
            <div class="ed-card">
                <div class="ed-card-head"><h2>My Performance</h2></div>
                <div class="ed-card-body">
                    <div class="ed-dl">
                        <div><span>Completion Rate</span><b>{{ $stats['performance']['completion_rate'] ?? '0' }}%</b></div>
                        <div><span>Avg Response Time</span><b>{{ $stats['performance']['avg_response_time'] ?? 'N/A' }}</b></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="ed-col">
            <div class="ed-card">
                <div class="ed-card-head"><h2>Quick Actions</h2></div>
                <div class="ed-list">
                    @php
                        $actions = [];
                        if ($hasMine) $actions[] = [route('jobs.mine'), 'list-todo', 'View My Jobs'];
                        if ($hasRequests) $actions[] = [route('asset-requests.index'), 'box', 'My Asset Requests'];
                        if (Route::has('asset-requests.create')) $actions[] = [route('asset-requests.create'), 'plus-circle', 'New Asset Request'];
                        $actions[] = [route('employee.profile'), 'user', 'Update Profile'];
                    @endphp
                    @foreach($actions as [$aUrl, $aIcon, $aLabel])
                    <a href="{{ $aUrl }}" class="ed-row">
                        <span class="ic"><svg class="mv-i"><use href="#i-{{ $aIcon }}"/></svg></span>
                        <span class="grow title">{{ $aLabel }}</span>
                        <svg class="mv-i chev"><use href="#i-chevron-right"/></svg>
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="ed-card">
                <div class="ed-card-head"><h2>Job Summary</h2></div>
                <div class="ed-card-body" style="padding-top:8px">
                    <div class="ed-dl">
                        <div><span>Total Assigned</span><b>{{ $jobs['assigned'] ?? 0 }}</b></div>
                        <div><span>In Progress</span><b>{{ $jobs['in_progress'] ?? 0 }}</b></div>
                        <div><span>Completed</span><b>{{ $jobs['completed'] ?? 0 }}</b></div>
                        <div><span>Due Today</span><b>{{ $jobs['today'] ?? 0 }}</b></div>
                    </div>
                    @php $allJobs = ($jobs['assigned'] ?? 0) + ($jobs['in_progress'] ?? 0) + ($jobs['completed'] ?? 0); @endphp
                    @if($allJobs > 0)
                    @php $pct = round((($jobs['completed'] ?? 0) / $allJobs) * 100); @endphp
                    <div class="ed-progress">
                        <div><span>Overall Progress</span><span>{{ $pct }}%</span></div>
                        <div class="ed-meter"><i style="width: {{ $pct }}%"></i></div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="ed-card">
                <div class="ed-card-head"><h2>Request Status</h2></div>
                <div class="ed-card-body" style="padding-top:8px;padding-bottom:8px">
                    <div class="ed-dl">
                        <div><span>Total Requests</span><b>{{ $stats['my_requests'] ?? 0 }}</b></div>
                        <div><span>Pending Approval</span><b>{{ $stats['pending_approvals'] ?? 0 }}</b></div>
                        @if(isset($stats['approved_requests']))
                        <div><span>Approved</span><b>{{ $stats['approved_requests'] ?? 0 }}</b></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
