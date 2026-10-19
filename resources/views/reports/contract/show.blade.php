@extends('layouts.app')

@php
    $isMonthly = $type === 'monthly';
    $meta = $report['meta'];
    $sum = $report['summary'];
    $txn = $report['transactions'];
@endphp

@section('title', $isMonthly ? 'Monthly Executive Report' : 'Weekly Operational Report')

@section('header-actions')
    <div class="cr-header-actions">
        <a class="btn-secondary" href="{{ route('reports.contract.export', array_merge(request()->query(), ['type' => $type, 'format' => 'xlsx'])) }}">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-table"/></svg> Excel
        </a>
        <a class="btn-secondary" href="{{ route('reports.contract.export', array_merge(request()->query(), ['type' => $type, 'format' => 'csv'])) }}">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> CSV
        </a>
        <a class="btn-primary" href="{{ route('reports.contract.export', array_merge(request()->query(), ['type' => $type, 'format' => 'pdf'])) }}">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-file"/></svg> Download report
        </a>
    </div>
@endsection

@push('styles')
<style>
.mv-page .cr-header-actions { display: flex; gap: 8px; }
.mv-page .cr-sub { color: var(--mv-muted); font-size: 13px; margin: -4px 0 18px; }
.mv-page .cr-filters { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; background: var(--mv-surface);
    border: 1px solid var(--mv-line); border-radius: 10px; padding: 14px 16px; margin-bottom: 18px; }
.mv-page .cr-field { display: flex; flex-direction: column; gap: 4px; }
.mv-page .cr-field label { font-size: 12px; color: var(--mv-muted); font-weight: 500; }
.mv-page .cr-field select, .mv-page .cr-field input { height: 36px; min-width: 150px; border: 1px solid var(--mv-line-strong);
    border-radius: 7px; padding: 0 10px; font: inherit; font-size: 13px; color: var(--mv-ink); background: var(--mv-surface); }
.mv-page .cr-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(168px, 1fr)); gap: 12px; margin-bottom: 20px; }
.mv-page .cr-kpi { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 14px 16px; }
.mv-page .cr-kpi-label { font-size: 12.5px; color: var(--mv-muted); font-weight: 500; }
.mv-page .cr-kpi-value { font-size: 24px; font-weight: 600; letter-spacing: -.02em; font-variant-numeric: tabular-nums; margin-top: 4px; }
.mv-page .cr-kpi-sub { font-size: 12px; color: var(--mv-muted); margin-top: 2px; }
.mv-page .cr-kpi-sub.is-good { color: var(--mv-good); }
.mv-page .cr-kpi-sub.is-warn { color: var(--mv-warn); }
.mv-page .cr-kpi-sub.is-crit { color: var(--mv-crit); }
.mv-page .cr-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 18px; }
.mv-page .cr-card-head { display: flex; justify-content: space-between; align-items: center; gap: 12px;
    padding: 13px 16px; border-bottom: 1px solid var(--mv-line); }
.mv-page .cr-card-head h2 { font-size: 15px; font-weight: 600; margin: 0; }
.mv-page .cr-card-head .cr-note { font-size: 12.5px; color: var(--mv-muted); }
.mv-page .cr-card-body { padding: 16px; }
.mv-page .cr-scroll { overflow-x: auto; }
.mv-page table.cr-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.mv-page table.cr-table th { text-align: left; font-size: 11.5px; text-transform: uppercase; letter-spacing: .04em;
    color: var(--mv-muted); font-weight: 600; padding: 9px 10px; border-bottom: 1px solid var(--mv-line); white-space: nowrap; }
.mv-page table.cr-table td { padding: 9px 10px; border-bottom: 1px solid var(--mv-line); vertical-align: top; }
.mv-page table.cr-table tbody tr:last-child td { border-bottom: 0; }
.mv-page table.cr-table td.num { text-align: right; font-variant-numeric: tabular-nums; }
.mv-page .cr-code { font-family: var(--mv-mono); font-size: 12px; }
.mv-page .cr-muted { color: var(--mv-muted); }
.mv-page .cr-pill { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
.mv-page .cr-pill.is-good { background: var(--mv-good-soft); color: var(--mv-good); }
.mv-page .cr-pill.is-warn { background: var(--mv-warn-soft); color: var(--mv-warn); }
.mv-page .cr-pill.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); }
.mv-page .cr-pill.is-flat { background: var(--mv-surface-2); color: var(--mv-ink-2); }
.mv-page .cr-reason { font-size: 12.5px; }
.mv-page .cr-reason .cr-src { font-size: 11px; color: var(--mv-muted); }
.mv-page details.cr-edit { margin-top: 6px; }
.mv-page details.cr-edit > summary { cursor: pointer; font-size: 12px; color: var(--mv-accent); list-style: none; }
.mv-page details.cr-edit > summary::-webkit-details-marker { display: none; }
.mv-page details.cr-edit form { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; padding: 10px;
    background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 8px; }
.mv-page details.cr-edit select, .mv-page details.cr-edit input { height: 32px; border: 1px solid var(--mv-line-strong);
    border-radius: 6px; padding: 0 8px; font: inherit; font-size: 12.5px; background: var(--mv-surface); }
.mv-page .cr-split { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.mv-page .cr-bar { height: 6px; border-radius: 3px; background: var(--mv-surface-2); overflow: hidden; margin-top: 6px; }
.mv-page .cr-bar span { display: block; height: 100%; background: var(--mv-accent); }
.mv-page .cr-empty { padding: 22px; text-align: center; color: var(--mv-muted); font-size: 13px; }
.mv-page .cr-notice { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border-radius: 9px;
    background: var(--mv-warn-soft); color: #6b4708; font-size: 13px; margin-bottom: 16px; }
.mv-page .cr-notice a { color: inherit; font-weight: 600; text-decoration: underline; }
.mv-page .cr-narrative textarea { width: 100%; min-height: 92px; border: 1px solid var(--mv-line-strong); border-radius: 8px;
    padding: 10px 12px; font: inherit; font-size: 13.5px; line-height: 1.55; color: var(--mv-ink); resize: vertical; }
.mv-page .cr-narrative label { display: block; font-size: 12.5px; font-weight: 600; margin: 0 0 6px; }
.mv-page .cr-narrative .cr-hint { font-weight: 400; color: var(--mv-muted); }
.mv-page .cr-narrative .cr-row { margin-bottom: 14px; }
.mv-page .cr-signoff { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; justify-content: space-between;
    border-top: 1px solid var(--mv-line); padding-top: 14px; margin-top: 4px; }
.mv-page .cr-issued { font-size: 12.5px; color: var(--mv-good); font-weight: 600; }
@media (max-width: 900px) { .mv-page .cr-split { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="cr-sub">
    {{ $meta['period_label'] }} &middot; {{ $meta['client_name'] }} &middot; {{ $meta['region'] }}
    &middot; prepared {{ $meta['generated_at'] }}
    @if($record->exists && $record->isIssued())
        <span class="cr-issued">&middot; Issued {{ $record->issued_at?->format('d M Y H:i') }}</span>
    @endif
</div>

<form method="GET" class="cr-filters">
    <div class="cr-field">
        <label for="cr-period">Period</label>
        <select id="cr-period" name="period" onchange="crApplyPeriod(this)">
            @php $matched = false; @endphp
            @foreach($periodOptions as $option)
                @php $selected = $option['start'] === $scope['start'] && $option['end'] === $scope['end']; $matched = $matched || $selected; @endphp
                <option value="{{ $option['start'] }}|{{ $option['end'] }}" @selected($selected)>{{ $option['label'] }}</option>
            @endforeach
            <option value="custom" @selected(! $matched)>Custom dates</option>
        </select>
    </div>
    <div class="cr-field">
        <label for="cr-from">From</label>
        <input type="date" id="cr-from" name="period_start" value="{{ $scope['start'] }}">
    </div>
    <div class="cr-field">
        <label for="cr-to">To</label>
        <input type="date" id="cr-to" name="period_end" value="{{ $scope['end'] }}" @if($isMonthly) readonly @endif>
    </div>
    <div class="cr-field">
        <label for="cr-client">Client</label>
        <select id="cr-client" name="client_id">
            <option value="">All clients</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" @selected((string) $scope['clientId'] === (string) $client->id)>
                    {{ $client->company_name ?: $client->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="cr-field">
        <label for="cr-region">Region</label>
        <select id="cr-region" name="region">
            <option value="">All regions</option>
            @foreach($regions as $regionName)
                <option value="{{ $regionName }}" @selected($scope['region'] === $regionName)>{{ $regionName }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-primary">Apply</button>
</form>

@if(! $txn['supplied'])
    <div class="cr-notice">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-alert-triangle"/></svg>
        <span>
            No transaction figures have been supplied for this period, so transaction activity against target
            cannot be reported. <a href="{{ route('reports.transactions') }}">Upload the client's transaction file</a>
            to complete this section.
        </span>
    </div>
@endif

<div class="cr-kpis">
    <div class="cr-kpi">
        <div class="cr-kpi-label">Merchants visited</div>
        <div class="cr-kpi-value">{{ $report['merchants']['visited'] }}<span class="cr-muted" style="font-size:15px"> / {{ $report['merchants']['total'] }}</span></div>
        <div class="cr-kpi-sub">{{ $report['merchants']['coverage_pct'] ?? 0 }}% of merchants</div>
    </div>
    <div class="cr-kpi">
        <div class="cr-kpi-label">Devices visited</div>
        <div class="cr-kpi-value">{{ $sum['devices_visited'] }}<span class="cr-muted" style="font-size:15px"> / {{ $sum['devices_total'] }}</span></div>
        <div class="cr-kpi-sub {{ ($sum['coverage_pct'] ?? 0) >= 90 ? 'is-good' : (($sum['coverage_pct'] ?? 0) >= 70 ? 'is-warn' : 'is-crit') }}">
            {{ $sum['coverage_pct'] ?? 0 }}% coverage
        </div>
    </div>
    <div class="cr-kpi">
        <div class="cr-kpi-label">Active on last visit</div>
        <div class="cr-kpi-value">{{ $sum['devices_active'] }}</div>
        <div class="cr-kpi-sub">{{ $sum['activation_pct'] ?? 0 }}% of devices visited</div>
    </div>
    <div class="cr-kpi">
        <div class="cr-kpi-label">Faults identified</div>
        <div class="cr-kpi-value">{{ $report['faults']['total'] }}</div>
        <div class="cr-kpi-sub">on {{ $report['faults']['devices_affected'] }} device{{ $report['faults']['devices_affected'] === 1 ? '' : 's' }}</div>
    </div>
    <div class="cr-kpi">
        <div class="cr-kpi-label">Open issues</div>
        <div class="cr-kpi-value">{{ $report['open_issues']['total'] }}</div>
        <div class="cr-kpi-sub {{ $sum['open_issues_over_30'] > 0 ? 'is-crit' : '' }}">
            {{ $sum['open_issues_over_30'] }} over 30 days
        </div>
    </div>
    <div class="cr-kpi">
        <div class="cr-kpi-label">Transactions vs target</div>
        <div class="cr-kpi-value">{{ $txn['supplied'] ? number_format($txn['actual']) : '—' }}</div>
        <div class="cr-kpi-sub">
            @if($txn['supplied'] && $txn['target'])
                {{ $txn['achievement'] }}% of {{ number_format($txn['target']) }}
            @elseif($txn['supplied'])
                No target set
            @else
                Not supplied by client
            @endif
        </div>
    </div>
</div>

@if($comparison)
    <div class="cr-card">
        <div class="cr-card-head">
            <h2>Movement against {{ $comparison['period_label'] }}</h2>
            <span class="cr-note">This month compared with the month before</span>
        </div>
        <div class="cr-card-body cr-scroll">
            @php
                $prev = $comparison['summary'];
                $rows = [
                    ['Devices visited', $sum['devices_visited'], $prev['devices_visited'], 'up'],
                    ['Coverage %', $sum['coverage_pct'] ?? 0, $prev['coverage_pct'] ?? 0, 'up'],
                    ['Devices active', $sum['devices_active'], $prev['devices_active'], 'up'],
                    ['Faults identified', $report['faults']['total'], $prev['devices_with_faults'], 'down'],
                    ['Open issues', $sum['open_issues'], $prev['open_issues'], 'down'],
                    ['Open issues over 30 days', $sum['open_issues_over_30'], $prev['open_issues_over_30'], 'down'],
                    ['Tickets resolved', $sum['tickets_resolved'], $prev['tickets_resolved'], 'up'],
                ];
            @endphp
            <table class="cr-table">
                <thead><tr><th>Measure</th><th class="num">This period</th><th class="num">{{ $comparison['period_label'] }}</th><th class="num">Change</th></tr></thead>
                <tbody>
                @foreach($rows as [$label, $now, $before, $goodDirection])
                    @php
                        $delta = (int) $now - (int) $before;
                        $good = $goodDirection === 'up' ? $delta > 0 : $delta < 0;
                        $class = $delta === 0 ? 'is-flat' : ($good ? 'is-good' : 'is-crit');
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="num">{{ $now }}</td>
                        <td class="num">{{ $before }}</td>
                        <td class="num"><span class="cr-pill {{ $class }}">{{ $delta > 0 ? '+' : '' }}{{ $delta }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="cr-card">
    <div class="cr-card-head">
        <h2>Device-by-device status</h2>
        <span class="cr-note">{{ count($report['devices']) }} device{{ count($report['devices']) === 1 ? '' : 's' }} in scope &middot; every device accounted for</span>
    </div>
    <div class="cr-scroll">
        <table class="cr-table">
            <thead>
                <tr>
                    <th>Terminal</th>
                    <th>Merchant</th>
                    <th>Region</th>
                    <th>Visited</th>
                    <th>Last visit</th>
                    <th>Status on visit</th>
                    <th>Condition</th>
                    <th>Faults identified</th>
                    <th>Corrective action taken</th>
                    <th>Reason not visited / not active</th>
                    <th class="num">Open</th>
                    <th class="num">Oldest</th>
                    <th>Escalation owner</th>
                    <th class="num">Txns</th>
                    <th class="num">Target</th>
                </tr>
            </thead>
            <tbody>
            @forelse($report['devices'] as $device)
                @php $reason = $device['non_visit_reason'] ?? $device['non_activation_reason']; @endphp
                <tr>
                    <td><span class="cr-code">{{ $device['terminal_code'] }}</span></td>
                    <td>
                        {{ $device['merchant_name'] ?: '—' }}
                        @if($device['city'])<div class="cr-muted" style="font-size:12px">{{ $device['city'] }}</div>@endif
                    </td>
                    <td>{{ $device['region'] ?: '—' }}</td>
                    <td>
                        @if($device['visited'])
                            <span class="cr-pill is-good">Visited</span>
                            @if($device['visit_count'] > 1)<div class="cr-muted" style="font-size:12px">{{ $device['visit_count'] }} visits</div>@endif
                        @else
                            <span class="cr-pill is-crit">Not visited</span>
                        @endif
                    </td>
                    <td>{{ $device['last_visit_date'] ?: '—' }}
                        @if($device['technicians'])<div class="cr-muted" style="font-size:12px">{{ implode(', ', $device['technicians']) }}</div>@endif
                    </td>
                    <td>
                        {{ $device['device_status'] }}
                        @if($device['device_state'])
                            <div class="cr-muted" style="font-size:12px">State: {{ $device['device_state'] }}</div>
                        @endif
                    </td>
                    <td>{{ $device['condition'] ?: '—' }}</td>
                    <td>
                        @if($device['faults'])
                            {{ implode('; ', $device['faults']) }}
                        @else
                            <span class="cr-muted">{{ $device['visited'] ? 'None recorded' : '—' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($device['corrective_actions'])
                            {{ implode('; ', $device['corrective_actions']) }}
                        @elseif($device['faults'])
                            <span class="cr-pill is-warn">Not recorded</span>
                        @else
                            <span class="cr-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($reason)
                            <div class="cr-reason">
                                {{ $reason['reason'] }}
                                @if(!empty($reason['note']))<div class="cr-muted">{{ $reason['note'] }}</div>@endif
                                <div class="cr-src">{{ $reason['source'] === 'recorded' ? 'Recorded by the office' : 'From the job and visit records' }}</div>
                            </div>
                        @else
                            <span class="cr-muted">—</span>
                        @endif

                        @if(! $device['visited'] || $device['active'] === false)
                            <details class="cr-edit">
                                <summary>{{ ($reason['source'] ?? '') === 'recorded' ? 'Change reason' : 'Record reason' }}</summary>
                                <form method="POST" action="{{ route('reports.contract.exception') }}">
                                    @csrf
                                    <input type="hidden" name="pos_terminal_id" value="{{ $device['terminal_id'] }}">
                                    <input type="hidden" name="period_start" value="{{ $scope['start'] }}">
                                    <input type="hidden" name="period_end" value="{{ $scope['end'] }}">
                                    <input type="hidden" name="type" value="{{ $device['visited'] ? 'non_activation' : 'non_visit' }}">
                                    <select name="reason_code" aria-label="Reason">
                                        <option value="">Use the system explanation</option>
                                        @foreach($reasonCodes as $code => $label)
                                            <option value="{{ $code }}" @selected(($reason['code'] ?? null) === $code)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="reason_note" placeholder="Note (optional)" value="{{ $reason['note'] ?? '' }}" maxlength="1000">
                                    <select name="escalation_owner_id" aria-label="Escalation owner">
                                        <option value="">Owner (optional)</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->first_name }} {{ $owner->last_name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-secondary">Save</button>
                                </form>
                            </details>
                        @endif
                    </td>
                    <td class="num">{{ $device['open_issue_count'] ?: '' }}</td>
                    <td class="num">{{ $device['oldest_issue_days'] ?: '' }}</td>
                    <td>{{ $device['escalation_owner'] ?: '—' }}</td>
                    <td class="num">
                        @if($device['transactions'] && $device['transactions']['actual'] !== null)
                            {{ number_format($device['transactions']['actual']) }}
                        @else
                            <span class="cr-muted">n/s</span>
                        @endif
                    </td>
                    <td class="num">
                        @if($device['transactions'] && $device['transactions']['target'])
                            {{ number_format($device['transactions']['target']) }}
                            @if($device['transactions']['achievement'] !== null)
                                <div class="cr-muted" style="font-size:12px">{{ $device['transactions']['achievement'] }}%</div>
                            @endif
                        @else
                            <span class="cr-muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="15" class="cr-empty">No devices match this period and filter.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="cr-split">
    <div class="cr-card">
        <div class="cr-card-head"><h2>Faults identified</h2><span class="cr-note">{{ $report['faults']['distinct'] }} type{{ $report['faults']['distinct'] === 1 ? '' : 's' }}</span></div>
        <div class="cr-card-body">
            @if($report['faults']['by_type'])
                <table class="cr-table">
                    <thead><tr><th>Fault</th><th class="num">Devices</th></tr></thead>
                    <tbody>
                    @foreach($report['faults']['by_type'] as $fault => $count)
                        <tr><td>{{ $fault }}</td><td class="num">{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="cr-empty">No faults were recorded in this period.</div>
            @endif
        </div>
    </div>

    <div class="cr-card">
        <div class="cr-card-head">
            <h2>Corrective action taken</h2>
            <span class="cr-note">{{ $report['corrective']['outstanding_count'] }} fault{{ $report['corrective']['outstanding_count'] === 1 ? '' : 's' }} with no action recorded</span>
        </div>
        <div class="cr-card-body">
            @if($report['corrective']['by_type'])
                <table class="cr-table">
                    <thead><tr><th>Action</th><th class="num">Devices</th></tr></thead>
                    <tbody>
                    @foreach($report['corrective']['by_type'] as $action => $count)
                        <tr><td>{{ $action }}</td><td class="num">{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="cr-empty">No corrective action was recorded in this period.</div>
            @endif

            @if($report['corrective']['outstanding'])
                <div style="margin-top:14px">
                    <div style="font-size:12.5px;font-weight:600;margin-bottom:6px">Faults awaiting an action</div>
                    <table class="cr-table">
                        <thead><tr><th>Terminal</th><th>Merchant</th><th>Fault</th><th>Owner</th></tr></thead>
                        <tbody>
                        @foreach($report['corrective']['outstanding'] as $item)
                            <tr>
                                <td><span class="cr-code">{{ $item['terminal_code'] }}</span></td>
                                <td>{{ $item['merchant_name'] }}</td>
                                <td>{{ implode('; ', $item['faults']) }}</td>
                                <td>{{ $item['owner'] ?: 'Unassigned' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="cr-card">
    <div class="cr-card-head">
        <h2>Open issues, ageing and escalation</h2>
        <span class="cr-note">Unresolved as at {{ $report['open_issues']['as_at'] }}
            @if($report['open_issues']['unassigned'] > 0) &middot; {{ $report['open_issues']['unassigned'] }} with no owner @endif
        </span>
    </div>
    <div class="cr-card-body">
        <div class="cr-kpis" style="margin-bottom:14px">
            @foreach($report['open_issues']['by_age'] as $band => $count)
                <div class="cr-kpi">
                    <div class="cr-kpi-label">{{ $band }}</div>
                    <div class="cr-kpi-value">{{ $count }}</div>
                    @if($report['open_issues']['total'] > 0)
                        <div class="cr-bar"><span style="width: {{ round($count / max($report['open_issues']['total'], 1) * 100) }}%"></span></div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($report['open_issues']['items'])
            <div class="cr-scroll">
                <table class="cr-table">
                    <thead><tr><th>Ticket</th><th>Terminal</th><th>Merchant</th><th>Issue</th><th>Priority</th><th>Raised</th><th class="num">Age</th><th>Escalation owner</th></tr></thead>
                    <tbody>
                    @foreach($report['open_issues']['items'] as $item)
                        <tr>
                            <td><span class="cr-code">{{ $item['ticket_id'] }}</span></td>
                            <td><span class="cr-code">{{ $item['terminal_code'] ?: '—' }}</span></td>
                            <td>{{ $item['merchant_name'] ?: '—' }}</td>
                            <td>{{ $item['title'] ?: $item['issue_type'] }}</td>
                            <td><span class="cr-pill {{ strtolower($item['priority']) === 'high' || strtolower($item['priority']) === 'critical' ? 'is-crit' : 'is-flat' }}">{{ $item['priority'] }}</span></td>
                            <td>{{ $item['raised_on'] }}</td>
                            <td class="num"><span class="cr-pill {{ $item['age_days'] > 30 ? 'is-crit' : ($item['age_days'] > 14 ? 'is-warn' : 'is-flat') }}">{{ $item['age_days'] }}d</span></td>
                            <td>{{ $item['owner'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="cr-empty">No issues were open at the end of this period.</div>
        @endif
    </div>
</div>

<div class="cr-card">
    <div class="cr-card-head">
        <h2>Transaction activity against target</h2>
        <span class="cr-note">
            {{ $txn['devices_with_data'] }} of {{ $txn['devices_total'] }} devices supplied &middot; {{ $txn['target_basis'] }}
        </span>
    </div>
    <div class="cr-card-body">
        @if($txn['supplied'])
            <div class="cr-kpis" style="margin-bottom:14px">
                <div class="cr-kpi"><div class="cr-kpi-label">Transactions</div><div class="cr-kpi-value">{{ number_format($txn['actual']) }}</div></div>
                <div class="cr-kpi"><div class="cr-kpi-label">Target</div><div class="cr-kpi-value">{{ $txn['target'] ? number_format($txn['target']) : '—' }}</div></div>
                <div class="cr-kpi">
                    <div class="cr-kpi-label">Achievement</div>
                    <div class="cr-kpi-value">{{ $txn['achievement'] !== null ? $txn['achievement'].'%' : '—' }}</div>
                    @if($txn['achievement'] !== null)
                        <div class="cr-kpi-sub {{ $txn['achievement'] >= 100 ? 'is-good' : ($txn['achievement'] >= 75 ? 'is-warn' : 'is-crit') }}">
                            {{ $txn['achievement'] >= 100 ? 'Target met' : 'Below target' }}
                        </div>
                    @endif
                </div>
                <div class="cr-kpi">
                    <div class="cr-kpi-label">Devices with no figures</div>
                    <div class="cr-kpi-value">{{ count($txn['missing_devices']) }}</div>
                    <div class="cr-kpi-sub">reported as not supplied</div>
                </div>
            </div>
            @if(count($txn['missing_devices']) > 0)
                <div class="cr-muted" style="font-size:12.5px">
                    No figures supplied for: {{ implode(', ', array_slice($txn['missing_devices'], 0, 25)) }}@if(count($txn['missing_devices']) > 25) and {{ count($txn['missing_devices']) - 25 }} more @endif.
                </div>
            @endif
        @else
            <div class="cr-empty">
                No transaction figures have been supplied for this period.<br>
                <a href="{{ route('reports.transactions') }}">Upload the client's transaction file</a> to complete this section.
            </div>
        @endif
    </div>
</div>

<div class="cr-split">
    <div class="cr-card">
        <div class="cr-card-head"><h2>Technician activity</h2></div>
        <div class="cr-card-body">
            @if($report['technicians'])
                <table class="cr-table">
                    <thead><tr><th>Technician</th><th class="num">Visits</th><th class="num">Devices</th><th class="num">Faults found</th></tr></thead>
                    <tbody>
                    @foreach($report['technicians'] as $tech)
                        <tr><td>{{ $tech['name'] }}</td><td class="num">{{ $tech['visits'] }}</td><td class="num">{{ $tech['devices'] }}</td><td class="num">{{ $tech['faults'] }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="cr-empty">No visits were recorded in this period.</div>
            @endif
        </div>
    </div>

    <div class="cr-card">
        <div class="cr-card-head"><h2>Devices added in the period</h2><span class="cr-note">{{ count($report['new_devices']) }} added</span></div>
        <div class="cr-card-body">
            @if($report['new_devices'])
                <table class="cr-table">
                    <thead><tr><th>Terminal</th><th>Merchant</th><th>Region</th><th>How</th><th>Added</th></tr></thead>
                    <tbody>
                    @foreach($report['new_devices'] as $device)
                        <tr>
                            <td><span class="cr-code">{{ $device['terminal_code'] }}</span></td>
                            <td>{{ $device['merchant_name'] }}</td>
                            <td>{{ $device['region'] ?: '—' }}</td>
                            <td>{{ $device['source'] }}</td>
                            <td>{{ $device['added_on'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="cr-empty">No devices were added in this period.</div>
            @endif
        </div>
    </div>
</div>

<div class="cr-card cr-narrative">
    <div class="cr-card-head">
        <h2>Summary, recommendations and action required from the client</h2>
        <span class="cr-note">This wording appears in the report sent to the client</span>
    </div>
    <div class="cr-card-body">
        <form method="POST" action="{{ route('reports.contract.notes') }}">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="period_start" value="{{ $scope['start'] }}">
            <input type="hidden" name="period_end" value="{{ $scope['end'] }}">
            <input type="hidden" name="client_id" value="{{ $scope['clientId'] }}">

            <div class="cr-row">
                <label for="cr-exec">Summary <span class="cr-hint">— what the period amounted to, in plain words</span></label>
                <textarea id="cr-exec" name="executive_summary" maxlength="5000">{{ old('executive_summary', $record->executive_summary) }}</textarea>
            </div>
            <div class="cr-row">
                <label for="cr-rec">Recommendations <span class="cr-hint">— what we advise</span></label>
                <textarea id="cr-rec" name="recommendations" maxlength="5000">{{ old('recommendations', $record->recommendations) }}</textarea>
            </div>
            <div class="cr-row">
                <label for="cr-act">Action required from the client <span class="cr-hint">— decisions or approvals we are waiting on</span></label>
                <textarea id="cr-act" name="client_actions" maxlength="5000">{{ old('client_actions', $record->client_actions) }}</textarea>
            </div>

            <div class="cr-signoff">
                <div class="cr-muted" style="font-size:12.5px">
                    Prepared by {{ $record->preparedBy?->full_name ?? auth()->user()?->full_name }}
                    @if($record->exists && $record->isIssued())
                        &middot; issued {{ $record->issued_at?->format('d M Y H:i') }}
                        @if($record->approved_by_name) &middot; approved by {{ $record->approved_by_name }} @endif
                    @endif
                </div>
                <button type="submit" class="btn-secondary">Save wording</button>
            </div>
        </form>

        <form method="POST" action="{{ route('reports.contract.issue') }}" class="cr-signoff" style="border-top:0;padding-top:0">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="period_start" value="{{ $scope['start'] }}">
            <input type="hidden" name="period_end" value="{{ $scope['end'] }}">
            <input type="hidden" name="client_id" value="{{ $scope['clientId'] }}">
            <input type="hidden" name="region" value="{{ $scope['region'] }}">
            <div class="cr-field">
                <label for="cr-approved">Approved by (optional)</label>
                <input type="text" id="cr-approved" name="approved_by_name" value="{{ $record->approved_by_name }}" maxlength="120" placeholder="Name of approver">
            </div>
            <button type="submit" class="btn-primary">
                {{ $record->exists && $record->isIssued() ? 'Re-issue with current figures' : 'Issue report' }}
            </button>
        </form>
        <div class="cr-muted" style="font-size:12.5px;margin-top:8px">
            Issuing stores the figures exactly as they stand, so this report can be reproduced later.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function crApplyPeriod(select) {
    if (select.value === 'custom') return;
    var parts = select.value.split('|');
    document.getElementById('cr-from').value = parts[0];
    document.getElementById('cr-to').value = parts[1];
}
</script>
@endpush
