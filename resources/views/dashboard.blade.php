@extends('layouts.app')
@section('title', 'Company Dashboard')

@push('styles')
<style>
  .db { display: flex; flex-direction: column; gap: 20px; }
  .db-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
  .db-hello { font-size: 20px; font-weight: 600; letter-spacing: -.01em; color: var(--mv-ink); margin: 0; }
  .db-date { font-size: 13.5px; color: var(--mv-muted); margin-top: 2px; }
  .db-actions { display: flex; gap: 8px; flex-wrap: wrap; }

  /* KPI strip: one object, cells divided by hairlines */
  .db-kpis { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 1px; background: var(--mv-line); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
  .db-kpi { background: var(--mv-surface); padding: 16px 18px; display: flex; flex-direction: column; gap: 3px; text-decoration: none !important; color: inherit; min-width: 0; }
  a.db-kpi:hover { background: var(--mv-surface-2); }
  .db-kpi-label { display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 500; color: var(--mv-muted); }
  .db-kpi-label .mv-i { width: 15px; height: 15px; }
  .db-kpi-value { font-size: 26px; font-weight: 600; letter-spacing: -.02em; line-height: 1.15; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
  .db-kpi-sub { font-size: 12.5px; color: var(--mv-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .t-good { color: var(--mv-good) !important; } .t-warn { color: var(--mv-warn) !important; } .t-crit { color: var(--mv-crit) !important; } .t-accent { color: var(--mv-accent-ink) !important; }

  .db-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr); gap: 20px; align-items: start; }
  .db-col { display: flex; flex-direction: column; gap: 20px; min-width: 0; }

  .db-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; min-width: 0; }
  .db-card-head { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
  .db-card-head h2 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
  .db-card-head .db-meta { font-size: 12.5px; color: var(--mv-muted); }
  .db-card-head .db-link { margin-left: auto; font-size: 13px; font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
  .db-card-head .db-link:hover { text-decoration: underline; }
  .db-card-body { padding: 16px 18px; }

  /* Needs attention */
  .db-alert { display: grid; grid-template-columns: 84px minmax(0, 1fr) auto; gap: 12px; align-items: center; padding: 11px 18px; text-decoration: none !important; color: var(--mv-ink-2); }
  .db-alert + .db-alert { border-top: 1px solid var(--mv-line); }
  .db-alert:hover { background: var(--mv-surface-2); }
  .db-alert .db-go { font-size: 13px; font-weight: 500; color: var(--mv-accent-ink); display: inline-flex; align-items: center; gap: 4px; }
  .chip { display: inline-flex; align-items: center; gap: 5px; justify-self: start; font-size: 12px; font-weight: 500; padding: 2px 8px; border-radius: 6px; white-space: nowrap; }
  .chip.crit { background: var(--mv-crit-soft); color: var(--mv-crit); }
  .chip.warn { background: var(--mv-warn-soft); color: var(--mv-warn); }
  .chip.good { background: var(--mv-good-soft); color: var(--mv-good); }
  .chip.info { background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
  .chip.neutral { background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }

  /* Fleet status: one stacked bar + legend table */
  .db-fleet-total { display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px; }
  .db-fleet-total b { font-size: 22px; font-weight: 600; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
  .db-fleet-total span { font-size: 13px; color: var(--mv-muted); }
  .db-stack { display: flex; gap: 2px; height: 12px; border-radius: 6px; overflow: hidden; background: var(--mv-surface-2); }
  .db-stack span { display: block; min-width: 3px; }
  .db-legend { margin-top: 14px; display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 2px 18px; }
  .db-legend a { display: grid; grid-template-columns: 10px minmax(0, 1fr) auto auto; gap: 10px; align-items: center; padding: 7px 0; text-decoration: none !important; color: var(--mv-ink-2); font-size: 13.5px; border-bottom: 1px solid var(--mv-line); }
  .db-legend a:hover { color: var(--mv-ink); }
  .db-legend i { width: 10px; height: 10px; border-radius: 3px; display: block; }
  .db-legend b { font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
  .db-legend em { font-style: normal; font-size: 12.5px; color: var(--mv-muted); min-width: 40px; text-align: right; font-variant-numeric: tabular-nums; }

  /* Tables and meters */
  .db-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
  .db-table th { text-align: left; font-size: 11.5px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: var(--mv-muted); padding: 9px 18px; background: var(--mv-surface-2); border-bottom: 1px solid var(--mv-line); }
  .db-table td { padding: 10px 18px; border-top: 1px solid var(--mv-line); color: var(--mv-ink-2); font-variant-numeric: tabular-nums; }
  .db-table tr:first-child td { border-top: 0; }
  .db-table td.num, .db-table th.num { text-align: right; }
  .db-table a { color: var(--mv-ink); font-weight: 500; text-decoration: none; }
  .db-table a:hover { color: var(--mv-accent-ink); text-decoration: underline; }
  .db-meter { height: 6px; border-radius: 3px; background: var(--mv-surface-2); overflow: hidden; border: 1px solid var(--mv-line); }
  .db-meter i { display: block; height: 100%; border-radius: 3px; background: var(--mv-accent); }
  .db-meter.good i { background: var(--mv-good); } .db-meter.warn i { background: #C28A2C; } .db-meter.crit i { background: var(--mv-crit); }
  .db-upt { display: grid; grid-template-columns: minmax(60px, 1fr) 48px; gap: 10px; align-items: center; }

  .db-chart { position: relative; height: 240px; }

  /* Lists in the side column */
  .db-list { display: flex; flex-direction: column; }
  .db-row { display: flex; align-items: center; gap: 12px; padding: 10px 18px; text-decoration: none !important; color: var(--mv-ink-2); font-size: 13.5px; }
  .db-row + .db-row { border-top: 1px solid var(--mv-line); }
  a.db-row:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
  .db-row .db-ic { width: 32px; height: 32px; border-radius: 8px; background: var(--mv-surface-2); color: var(--mv-ink-2); display: grid; place-items: center; flex-shrink: 0; }
  .db-row .db-ic .mv-i { width: 17px; height: 17px; }
  .db-row .db-grow { flex: 1; min-width: 0; }
  .db-row .db-title { color: var(--mv-ink); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .db-row .db-sub { font-size: 12.5px; color: var(--mv-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .db-row .db-chev { color: var(--mv-line-strong); width: 16px; height: 16px; }
  .db-rank { width: 22px; font-size: 12.5px; color: var(--mv-muted); font-variant-numeric: tabular-nums; text-align: right; flex-shrink: 0; }

  .db-dl { display: flex; flex-direction: column; }
  .db-dl div { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 8px 0; font-size: 13.5px; color: var(--mv-ink-2); }
  .db-dl div + div { border-top: 1px solid var(--mv-line); }
  .db-dl b { font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
  .db-sec + .db-sec { border-top: 1px solid var(--mv-line); }
  .db-sec-title { font-size: 12px; font-weight: 600; color: var(--mv-muted); letter-spacing: .04em; text-transform: uppercase; margin: 0 0 4px; }

  .db-health { display: flex; flex-direction: column; gap: 14px; }
  .db-health-row { display: flex; flex-direction: column; gap: 6px; }
  .db-health-row div { display: flex; justify-content: space-between; font-size: 13.5px; color: var(--mv-ink-2); }
  .db-health-row b { font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }

  .db-empty { display: flex; flex-direction: column; align-items: flex-start; gap: 6px; padding: 18px; color: var(--mv-muted); font-size: 13.5px; }
  .db-empty strong { color: var(--mv-ink); font-weight: 500; }
  .db-empty a { color: var(--mv-accent-ink); font-weight: 500; text-decoration: none; }

  .db-activity { max-height: 420px; overflow-y: auto; }

  @media (max-width: 1280px) { .db-kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
  @media (max-width: 1100px) { .db-grid { grid-template-columns: minmax(0, 1fr); } }
  @media (max-width: 640px) { .db-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } .db-alert { grid-template-columns: minmax(0, 1fr); } }
</style>
@endpush

@section('content')
@php
    $s = $stats;
    $total = (int) $s['total_terminals'];
    $segments = [
        ['label' => 'Active',         'count' => (int) $s['active_terminals'],               'color' => '#1D7F46', 'status' => 'active'],
        ['label' => 'Offline',        'count' => (int) $s['offline_terminals'],              'color' => '#C28A2C', 'status' => 'offline'],
        ['label' => 'Maintenance',    'count' => (int) $s['maintenance_terminals'],          'color' => '#2B64A8', 'status' => 'maintenance'],
        ['label' => 'Faulty',         'count' => (int) $s['faulty_terminals'],               'color' => '#B83232', 'status' => 'faulty'],
        ['label' => 'Decommissioned', 'count' => (int) ($s['decommissioned_terminals'] ?? 0), 'color' => '#9AA6B4', 'status' => 'decommissioned'],
    ];
    $other = max(0, $total - array_sum(array_column($segments, 'count')));
    if ($other > 0) {
        $segments[] = ['label' => 'Status not set', 'count' => $other, 'color' => '#CBD3DD', 'status' => null];
    }
    $lic = $s['license_stats'];
    $uptime = (float) $s['network_uptime'];
    $tone = fn ($v) => $v >= 90 ? 'good' : ($v >= 70 ? 'warn' : 'crit');
    $trend = $s['monthly_trends'];
    $hasTrend = (array_sum($trend['terminals']) + array_sum($trend['clients']) + array_sum($trend['licenses'])) > 0;
    $maxClient = max(1, (int) collect($s['top_clients'])->max('terminals'));
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $activityIcon = function (string $title): array {
        return match (true) {
            str_contains($title, 'Expired')  => ['alert-triangle', 't-crit'],
            str_contains($title, 'Renewed')  => ['refresh', ''],
            str_contains($title, 'License')  => ['file-check', ''],
            str_contains($title, 'Terminal') => ['card', ''],
            str_contains($title, 'Client')   => ['building', ''],
            str_contains($title, 'Job')      => ['clipboard', ''],
            str_contains($title, 'Visit')    => ['pin', ''],
            default                          => ['activity', ''],
        };
    };
@endphp

<div class="db">

    <div class="db-head">
        <div>
            <p class="db-hello">{{ $greeting }}, {{ auth()->user()->first_name }}</p>
            <div class="db-date">{{ now()->format('l j F Y') }} · {{ number_format($total) }} terminals across {{ number_format($s['total_clients']) }} active {{ \Illuminate\Support\Str::plural('client', $s['total_clients']) }}</div>
        </div>
        <div class="db-actions">
            <a href="{{ route('pos-terminals.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm"><use href="#i-card"/></svg>POS Terminals</a>
            <a href="{{ route('reports.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm"><use href="#i-chart"/></svg>Reports Dashboard</a>
        </div>
    </div>

    {{-- Key figures --}}
    <div class="db-kpis">
        <a href="{{ route('pos-terminals.index') }}" class="db-kpi">
            <span class="db-kpi-label"><svg class="mv-i"><use href="#i-card"/></svg>Total Terminals</span>
            <span class="db-kpi-value">{{ number_format($total) }}</span>
            <span class="db-kpi-sub">+{{ $s['new_terminals_this_month'] }} this month</span>
        </a>
        <a href="{{ route('pos-terminals.index', ['status' => 'active']) }}" class="db-kpi">
            <span class="db-kpi-label"><svg class="mv-i"><use href="#i-check-circle"/></svg>Active Terminals</span>
            <span class="db-kpi-value">{{ number_format($s['active_terminals']) }}</span>
            <span class="db-kpi-sub t-{{ $tone($uptime) }}">{{ $uptime }}% uptime</span>
        </a>
        <a href="{{ route('pos-terminals.index', ['status' => 'faulty']) }}" class="db-kpi">
            <span class="db-kpi-label"><svg class="mv-i"><use href="#i-alert-triangle"/></svg>Need Attention</span>
            <span class="db-kpi-value">{{ number_format($s['need_attention']) }}</span>
            <span class="db-kpi-sub {{ $s['urgent_issues'] > 0 ? 't-crit' : '' }}">{{ $s['urgent_issues'] }} urgent</span>
        </a>
        <a href="{{ route('clients.index') }}" class="db-kpi">
            <span class="db-kpi-label"><svg class="mv-i"><use href="#i-building"/></svg>Active Clients</span>
            <span class="db-kpi-value">{{ number_format($s['total_clients']) }}</span>
            <span class="db-kpi-sub">{{ $s['new_clients_this_month'] }} new this month</span>
        </a>
        <a href="{{ route('business-licenses.index') }}" class="db-kpi">
            <span class="db-kpi-label"><svg class="mv-i"><use href="#i-file-check"/></svg>Business Licenses</span>
            <span class="db-kpi-value">{{ number_format($lic['total_licenses']) }}</span>
            <span class="db-kpi-sub">{{ $lic['active_licenses'] }} active</span>
        </a>
        <a href="{{ route('business-licenses.compliance') }}" class="db-kpi">
            <span class="db-kpi-label"><svg class="mv-i"><use href="#i-shield"/></svg>License Compliance</span>
            <span class="db-kpi-value">{{ number_format($lic['compliance_rate']) }}%</span>
            <span class="db-kpi-sub {{ $lic['expiring_soon'] > 0 ? 't-warn' : '' }}">{{ $lic['expiring_soon'] }} expiring soon</span>
        </a>
    </div>

    {{-- Needs attention --}}
    @if($s['alerts']->count() > 0)
    <div class="db-card">
        <div class="db-card-head">
            <h2>Needs attention</h2>
            <span class="db-meta">{{ $s['alerts']->count() }} {{ \Illuminate\Support\Str::plural('item', $s['alerts']->count()) }}</span>
        </div>
        <div>
            @foreach($s['alerts'] as $alert)
            @php
                $alertUrl = match(true) {
                    str_contains($alert['message'], 'licenses expiring') => route('business-licenses.expiring'),
                    str_contains($alert['message'], 'licenses expired') => route('business-licenses.index', ['status' => 'expired']),
                    str_contains($alert['message'], 'critical licenses') => route('business-licenses.index', ['priority' => 'critical']),
                    str_contains($alert['message'], 'license renewals') => route('business-licenses.compliance'),
                    str_contains($alert['message'], 'faulty') => route('pos-terminals.index', ['status' => 'faulty']),
                    str_contains($alert['message'], 'offline') => route('pos-terminals.index', ['status' => 'offline']),
                    str_contains($alert['message'], 'contracts expiring') => route('clients.index', ['expiring' => true]),
                    str_contains($alert['message'], 'asset requests') => route('asset-approvals.index'),
                    str_contains($alert['message'], 'job assignments') => route('jobs.index'),
                    default => '#'
                };
                [$chipClass, $chipText] = match($alert['type']) {
                    'critical' => ['crit', 'Critical'],
                    'warning'  => ['warn', 'Warning'],
                    default    => ['info', 'Info'],
                };
            @endphp
            <a href="{{ $alertUrl }}" class="db-alert">
                <span class="chip {{ $chipClass }}">{{ $chipText }}</span>
                <span>{{ $alert['message'] }}</span>
                <span class="db-go">Review<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="db-grid">
        <div class="db-col">

            {{-- Terminal fleet --}}
            <div class="db-card">
                <div class="db-card-head">
                    <h2>Terminal Status Distribution</h2>
                    <a href="{{ route('pos-terminals.index') }}" class="db-link">All terminals<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></a>
                </div>
                <div class="db-card-body">
                    @if($total > 0)
                    <div class="db-fleet-total"><b>{{ number_format($total) }}</b><span>terminals in the fleet</span></div>
                    <div class="db-stack" role="img" aria-label="Terminal status breakdown">
                        @foreach($segments as $seg)
                            @if($seg['count'] > 0)
                            <span style="flex: {{ $seg['count'] }}; background: {{ $seg['color'] }}" title="{{ $seg['label'] }}: {{ $seg['count'] }}"></span>
                            @endif
                        @endforeach
                    </div>
                    <div class="db-legend">
                        @foreach($segments as $seg)
                        <a href="{{ $seg['status'] ? route('pos-terminals.index', ['status' => $seg['status']]) : route('pos-terminals.index') }}">
                            <i style="background: {{ $seg['color'] }}"></i>
                            <span>{{ $seg['label'] }}</span>
                            <b>{{ number_format($seg['count']) }}</b>
                            <em>{{ $total > 0 ? round($seg['count'] / $total * 100) : 0 }}%</em>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="db-empty"><strong>No terminals yet</strong>Terminals appear here once they are imported or added.<a href="{{ route('pos-terminals.create') }}">Add New Terminal</a></div>
                    @endif
                </div>
            </div>

            {{-- Regional distribution --}}
            <div class="db-card">
                <div class="db-card-head">
                    <h2>Regional Distribution</h2>
                    @if($s['regional_data']->isNotEmpty())<span class="db-meta">{{ $s['regional_data']->count() }} {{ \Illuminate\Support\Str::plural('region', $s['regional_data']->count()) }}</span>@endif
                </div>
                @if($s['regional_data']->isNotEmpty())
                <div style="overflow-x:auto">
                    <table class="db-table">
                        <thead><tr><th>Region</th><th class="num">Terminals</th><th class="num">Active</th><th class="num">Issues</th><th style="width:34%">Uptime</th></tr></thead>
                        <tbody>
                        @foreach($s['regional_data'] as $region => $data)
                            <tr>
                                <td><a href="{{ route('pos-terminals.index', ['region' => $region]) }}">{{ $region }}</a></td>
                                <td class="num">{{ number_format($data['total']) }}</td>
                                <td class="num">{{ number_format($data['active']) }}</td>
                                <td class="num {{ $data['issues'] > 0 ? 't-warn' : '' }}">{{ number_format($data['issues']) }}</td>
                                <td><div class="db-upt"><div class="db-meter {{ $tone($data['uptime_percentage']) }}"><i style="width: {{ $data['uptime_percentage'] }}%"></i></div><span>{{ $data['uptime_percentage'] }}%</span></div></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="db-empty"><strong>No regional data yet</strong>Regions appear here once terminals have a region set.</div>
                @endif
            </div>

            {{-- Growth --}}
            <div class="db-card">
                <div class="db-card-head">
                    <h2>Monthly Growth Trends</h2>
                    <span class="db-meta">Last 6 months</span>
                </div>
                <div class="db-card-body">
                    @if($hasTrend)
                    <div class="db-chart"><canvas id="trendsChart" aria-label="New terminals, clients and licenses per month"></canvas></div>
                    @else
                    <div class="db-empty"><strong>Nothing new in the last 6 months</strong>New terminals, clients and licenses will be charted here.</div>
                    @endif
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="db-card">
                <div class="db-card-head"><h2>Recent Activity</h2></div>
                <div class="db-list db-activity">
                    @forelse($s['recent_activity'] as $activity)
                    @php [$aIcon, $aTone] = $activityIcon($activity['title']); @endphp
                    <div class="db-row">
                        <span class="db-ic {{ $aTone }}"><svg class="mv-i"><use href="#i-{{ $aIcon }}"/></svg></span>
                        <div class="db-grow">
                            <div class="db-title">{{ $activity['title'] }}</div>
                            <div class="db-sub">{{ $activity['description'] }}</div>
                        </div>
                        <span class="db-sub" style="flex-shrink:0">{{ $activity['time'] }}</span>
                        @if(isset($activity['action']))
                        <a href="{{ $activity['action']['url'] }}" class="btn-secondary btn-sm" style="flex-shrink:0">{{ $activity['action']['label'] }}</a>
                        @endif
                    </div>
                    @empty
                    <div class="db-empty"><strong>No recent activity</strong></div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="db-col">

            {{-- Quick actions --}}
            <div class="db-card">
                <div class="db-card-head"><h2>Quick Actions</h2></div>
                <div class="db-list">
                    @foreach([
                        [route('pos-terminals.create'), 'plus-circle', 'Add New Terminal'],
                        [route('business-licenses.create'), 'file-check', 'Add Business License'],
                        [route('clients.create'), 'building', 'Add New Client'],
                        [route('pos-terminals.index', ['status' => 'faulty']), 'wrench', 'View Faulty Terminals'],
                        [route('business-licenses.expiring'), 'clock', 'Expiring Licenses'],
                        [route('pos-terminals.column-mapping'), 'table', 'Column Mapping'],
                    ] as [$qaUrl, $qaIcon, $qaLabel])
                    <a href="{{ $qaUrl }}" class="db-row">
                        <span class="db-ic"><svg class="mv-i"><use href="#i-{{ $qaIcon }}"/></svg></span>
                        <span class="db-grow db-title">{{ $qaLabel }}</span>
                        <svg class="mv-i db-chev"><use href="#i-chevron-right"/></svg>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Top clients --}}
            <div class="db-card">
                <div class="db-card-head">
                    <h2>Top Clients</h2>
                    <a href="{{ route('clients.index') }}" class="db-link">All clients<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></a>
                </div>
                <div class="db-list">
                    @forelse($s['top_clients'] as $client)
                    <a href="{{ route('clients.show', $client['id']) }}" class="db-row">
                        <span class="db-rank">{{ $loop->iteration }}</span>
                        <div class="db-grow">
                            <div class="db-title">{{ $client['name'] }}</div>
                            <div class="db-meter" style="margin-top:6px"><i style="width: {{ round($client['terminals'] / $maxClient * 100) }}%"></i></div>
                        </div>
                        <span style="text-align:right;flex-shrink:0">
                            <span class="db-title" style="display:block;font-variant-numeric:tabular-nums">{{ number_format($client['terminals']) }}</span>
                            <span class="db-sub">terminals</span>
                        </span>
                    </a>
                    @empty
                    <div class="db-empty"><strong>No clients yet</strong><a href="{{ route('clients.create') }}">Add New Client</a></div>
                    @endforelse
                </div>
            </div>

            {{-- System health --}}
            <div class="db-card">
                <div class="db-card-head"><h2>System Health</h2></div>
                <div class="db-card-body db-health">
                    <div class="db-health-row">
                        <div><span>Network Uptime</span><b>{{ $uptime }}%</b></div>
                        <div class="db-meter {{ $tone($uptime) }}"><i style="width: {{ $uptime }}%"></i></div>
                    </div>
                    <div class="db-health-row">
                        <div><span>License Compliance</span><b>{{ $lic['compliance_rate'] }}%</b></div>
                        <div class="db-meter"><i style="width: {{ $lic['compliance_rate'] }}%"></i></div>
                    </div>
                    <div class="db-health-row">
                        <div><span>Service Level</span><b>{{ $s['service_level'] }}%</b></div>
                        <div class="db-meter"><i style="width: {{ $s['service_level'] }}%"></i></div>
                    </div>
                    <div class="db-dl"><div><span>Avg Response Time</span><b>{{ $s['avg_response_time'] }} h</b></div></div>
                </div>
            </div>

            {{-- Licenses --}}
            <div class="db-card">
                <div class="db-card-head">
                    <h2>License Summary</h2>
                    <a href="{{ route('business-licenses.index') }}" class="db-link">View All<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></a>
                </div>
                @if($lic['total_licenses'] > 0)
                <div class="db-card-body" style="padding-top:8px;padding-bottom:8px">
                    <div class="db-dl">
                        <div><span>Total Licenses</span><b>{{ $lic['total_licenses'] }}</b></div>
                        <div><span>Active</span><b>{{ $lic['active_licenses'] }}</b></div>
                        <div><span>Expiring Soon</span><b class="{{ $lic['expiring_soon'] > 0 ? 't-warn' : '' }}">{{ $lic['expiring_soon'] }}</b></div>
                        <div><span>Expired</span><b class="{{ $lic['expired'] > 0 ? 't-crit' : '' }}">{{ $lic['expired'] }}</b></div>
                        <div><span>Critical Priority</span><b>{{ $lic['critical_licenses'] }}</b></div>
                        <div><span>Annual Cost</span><b>${{ number_format($lic['annual_cost'], 0) }}</b></div>
                    </div>
                </div>
                @if(isset($s['upcoming_renewals']) && $s['upcoming_renewals']->count() > 0)
                <div class="db-list" style="border-top:1px solid var(--mv-line)">
                    @foreach($s['upcoming_renewals']->take(5) as $license)
                    <div class="db-row">
                        <span class="chip {{ $license->is_expired ? 'crit' : 'warn' }}">{{ $license->is_expired ? 'Expired' : 'Due' }}</span>
                        <div class="db-grow">
                            <div class="db-title">{{ $license->license_name }}</div>
                            <div class="db-sub">{{ $license->is_expired ? 'Expired' : 'Expires' }} {{ optional($license->expiry_date)->format('j M Y') }}</div>
                        </div>
                        <a href="{{ route('business-licenses.renew', $license) }}" class="btn-secondary btn-sm" style="flex-shrink:0">Renew</a>
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <div class="db-empty"><strong>No business licenses recorded</strong>Track licenses and renewal dates here.<a href="{{ route('business-licenses.create') }}">Add Business License</a></div>
                @endif
            </div>

            {{-- Contracts and team --}}
            <div class="db-card">
                <div class="db-card-body db-sec" style="padding-bottom:10px">
                    <p class="db-sec-title">Contract Status</p>
                    <div class="db-dl">
                        <div><span>Active Contracts</span><b>{{ $s['contract_stats']['active'] }}</b></div>
                        <div><span>Expiring Soon</span><b class="{{ $s['contract_stats']['expiring_soon'] > 0 ? 't-warn' : '' }}">{{ $s['contract_stats']['expiring_soon'] }}</b></div>
                        <div><span>Expired</span><b class="{{ $s['contract_stats']['expired'] > 0 ? 't-crit' : '' }}">{{ $s['contract_stats']['expired'] }}</b></div>
                    </div>
                </div>
                <div class="db-card-body db-sec" style="padding-bottom:10px">
                    <p class="db-sec-title">Team Overview</p>
                    <div class="db-dl">
                        <div><span>Total Employees</span><b>{{ $s['employee_stats']['total'] }}</b></div>
                        <div><span>Field Technicians</span><b>{{ $s['employee_stats']['technicians'] }}</b></div>
                        <div><span>Managers</span><b>{{ $s['employee_stats']['managers'] }}</b></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if($hasTrend)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    var el = document.getElementById('trendsChart');
    if (!el || typeof Chart === 'undefined') return;
    Chart.defaults.font.family = '"IBM Plex Sans", "Segoe UI", system-ui, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6A7686';
    var bar = { borderRadius: 4, maxBarThickness: 18, borderSkipped: 'bottom' };
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json($trend['months']),
            datasets: [
                Object.assign({ label: 'New Terminals', data: @json($trend['terminals']), backgroundColor: '#2B64A8' }, bar),
                Object.assign({ label: 'New Clients',   data: @json($trend['clients']),   backgroundColor: '#8DB3E2' }, bar),
                Object.assign({ label: 'New Licenses',  data: @json($trend['licenses']),  backgroundColor: '#5B6B7F' }, bar)
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, useBorderRadius: true, borderRadius: 2, padding: 16 } },
                tooltip: { backgroundColor: '#16202C', padding: 10, cornerRadius: 6, boxPadding: 4, titleFont: { weight: '600' } }
            },
            scales: {
                x: { grid: { display: false }, border: { color: '#E1E6EC' } },
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#EEF1F4' }, border: { display: false } }
            }
        }
    });
})();
</script>
@endif

<script>
// Keep the figures fresh while the dashboard is on screen.
setInterval(function () { if (document.visibilityState === 'visible') location.reload(); }, 300000);
</script>
@endsection
