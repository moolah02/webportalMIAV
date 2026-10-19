<!DOCTYPE html>
@php
    $isMonthly = $type === 'monthly';
    $meta = $report['meta'];
    $sum = $report['summary'];
    $txn = $report['transactions'];
    $title = $isMonthly ? 'Monthly Executive Report' : 'Weekly Operational Report';
@endphp
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} — {{ $meta['period_label'] }}</title>
    <style>
        /* dompdf: tables for layout, no flexbox. */
        @page { margin: 14mm 10mm 16mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #1f2937; }
        .head { width: 100%; border-bottom: 3px solid #1a3a5c; padding-bottom: 8px; margin-bottom: 12px; }
        .head td { vertical-align: top; }
        h1 { font-size: 17px; color: #1a3a5c; margin: 0 0 3px; }
        .scope { font-size: 10px; color: #4b5563; }
        .meta { text-align: right; font-size: 9px; color: #6b7280; }
        .badge { display: inline-block; background: #e0e7ff; color: #3730a3; border-radius: 3px;
                 padding: 2px 6px; font-size: 8px; font-weight: bold; letter-spacing: .04em; }
        h2 { font-size: 11.5px; color: #1a3a5c; margin: 14px 0 6px; padding-bottom: 3px; border-bottom: 2px solid #e5e7eb; }
        .stat-row { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-bottom: 4px; }
        .stat { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 4px; padding: 6px 8px; width: 16%; }
        .stat-num { font-size: 15px; font-weight: bold; color: #1a3a5c; }
        .stat-lbl { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; }
        .stat-sub { font-size: 8px; color: #6b7280; margin-top: 1px; }
        table.data { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 6px; }
        table.data th { background: #1a3a5c; color: #fff; font-size: 8px; text-align: left; padding: 4px 5px;
                        text-transform: uppercase; letter-spacing: .03em; }
        table.data td { font-size: 8.5px; padding: 4px 5px; border-bottom: 1px solid #e5e7eb; vertical-align: top;
                        word-wrap: break-word; overflow-wrap: break-word; }
        table.data tr:nth-child(even) td { background: #f8fafc; }
        .mono { font-family: "DejaVu Sans Mono", monospace; font-size: 8px; }
        .sub { color: #6b7280; font-size: 7.5px; }
        .none { color: #9ca3af; }
        .warn { color: #9a6412; font-weight: bold; }
        .crit { color: #b83232; font-weight: bold; }
        .good { color: #1d7f46; font-weight: bold; }
        .notice { background: #fbf1de; border: 1px solid #e8d5a8; color: #6b4708; padding: 6px 8px;
                  border-radius: 4px; font-size: 9px; margin-bottom: 10px; }
        .narrative { border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px 10px; margin-bottom: 8px; }
        .narrative .lbl { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 3px; }
        .narrative p { margin: 0; font-size: 9.5px; line-height: 1.5; white-space: pre-line; }
        .sign { width: 100%; margin-top: 14px; font-size: 9px; }
        .sign td { padding-top: 22px; border-top: 1px solid #9ca3af; width: 45%; color: #4b5563; }
        .foot { position: fixed; bottom: -8mm; left: 0; right: 0; font-size: 7.5px; color: #9ca3af;
                border-top: 1px solid #e5e7eb; padding-top: 3px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

<table class="head">
    <tr>
        <td>
            <h1>{{ $title }}</h1>
            <div class="scope">
                {{ $meta['period_label'] }} ({{ $meta['period_start'] }} to {{ $meta['period_end'] }})<br>
                Client: {{ $meta['client_name'] }} &middot; Region: {{ $meta['region'] }} &middot; {{ $meta['days'] }} days
            </div>
        </td>
        <td class="meta">
            Revival Technologies<br>
            Prepared: {{ $meta['generated_at'] }}<br>
            @if($record->exists && $record->isIssued())
                Issued: {{ $record->issued_at?->format('Y-m-d H:i') }}<br>
            @endif
            <span class="badge">{{ $isMonthly ? 'EXECUTIVE' : 'OPERATIONAL' }}</span>
        </td>
    </tr>
</table>

@if(! $txn['supplied'])
    <div class="notice">
        <strong>Transaction activity not supplied.</strong>
        No transaction figures were provided for this period, so activity against target cannot be reported.
        Every other section is complete.
    </div>
@endif

<h2>Performance at a glance</h2>
<table class="stat-row">
    <tr>
        <td class="stat">
            <div class="stat-num">{{ $report['merchants']['visited'] }}/{{ $report['merchants']['total'] }}</div>
            <div class="stat-lbl">Merchants visited</div>
            <div class="stat-sub">{{ $report['merchants']['coverage_pct'] ?? 0 }}% of merchants</div>
        </td>
        <td class="stat">
            <div class="stat-num">{{ $sum['devices_visited'] }}/{{ $sum['devices_total'] }}</div>
            <div class="stat-lbl">Devices visited</div>
            <div class="stat-sub">{{ $sum['coverage_pct'] ?? 0 }}% coverage</div>
        </td>
        <td class="stat">
            <div class="stat-num">{{ $sum['devices_active'] }}</div>
            <div class="stat-lbl">Active on last visit</div>
            <div class="stat-sub">{{ $sum['activation_pct'] ?? 0 }}% of visited</div>
        </td>
        <td class="stat">
            <div class="stat-num">{{ $report['faults']['total'] }}</div>
            <div class="stat-lbl">Faults identified</div>
            <div class="stat-sub">{{ $report['faults']['devices_affected'] }} devices</div>
        </td>
        <td class="stat">
            <div class="stat-num">{{ $report['open_issues']['total'] }}</div>
            <div class="stat-lbl">Open issues</div>
            <div class="stat-sub">{{ $sum['open_issues_over_30'] }} over 30 days</div>
        </td>
        <td class="stat">
            <div class="stat-num">{{ $txn['supplied'] ? ($txn['achievement'] !== null ? $txn['achievement'].'%' : number_format($txn['actual'])) : '—' }}</div>
            <div class="stat-lbl">Txns vs target</div>
            <div class="stat-sub">{{ $txn['supplied'] ? number_format($txn['actual']).' of '.($txn['target'] ? number_format($txn['target']) : 'no target') : 'not supplied' }}</div>
        </td>
    </tr>
</table>

@if($comparison)
    @php $prev = $comparison['summary']; @endphp
    <h2>Movement against {{ $comparison['period_label'] }}</h2>
    <table class="data">
        <colgroup><col style="width:40%"><col style="width:20%"><col style="width:20%"><col style="width:20%"></colgroup>
        <thead><tr><th>Measure</th><th>This period</th><th>{{ $comparison['period_label'] }}</th><th>Change</th></tr></thead>
        <tbody>
        @foreach([
            ['Devices visited', $sum['devices_visited'], $prev['devices_visited'], 'up'],
            ['Coverage %', $sum['coverage_pct'] ?? 0, $prev['coverage_pct'] ?? 0, 'up'],
            ['Devices active', $sum['devices_active'], $prev['devices_active'], 'up'],
            ['Open issues', $sum['open_issues'], $prev['open_issues'], 'down'],
            ['Open issues over 30 days', $sum['open_issues_over_30'], $prev['open_issues_over_30'], 'down'],
            ['Tickets resolved', $sum['tickets_resolved'], $prev['tickets_resolved'], 'up'],
        ] as [$label, $now, $before, $direction])
            @php
                $delta = (int) $now - (int) $before;
                $class = $delta === 0 ? '' : (($direction === 'up' ? $delta > 0 : $delta < 0) ? 'good' : 'crit');
            @endphp
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $now }}</td>
                <td>{{ $before }}</td>
                <td class="{{ $class }}">{{ $delta > 0 ? '+' : '' }}{{ $delta }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<h2>Device-by-device status ({{ count($report['devices']) }} devices)</h2>
<table class="data">
    <colgroup>
        <col style="width:11%"><col style="width:14%"><col style="width:7%"><col style="width:9%">
        <col style="width:12%"><col style="width:12%"><col style="width:16%"><col style="width:10%"><col style="width:9%">
    </colgroup>
    <thead>
        <tr>
            <th>Terminal</th>
            <th>Merchant</th>
            <th>Visited</th>
            <th>Status</th>
            <th>Faults identified</th>
            <th>Corrective action</th>
            <th>Reason not visited / not active</th>
            <th>Open issues</th>
            <th>Txns / target</th>
        </tr>
    </thead>
    <tbody>
    @forelse($report['devices'] as $device)
        @php $reason = $device['non_visit_reason'] ?? $device['non_activation_reason']; @endphp
        <tr>
            <td><span class="mono">{{ $device['terminal_code'] }}</span>
                @if($device['region'])<div class="sub">{{ $device['region'] }}</div>@endif
            </td>
            <td>{{ $device['merchant_name'] ?: '—' }}
                @if($device['city'])<div class="sub">{{ $device['city'] }}</div>@endif
            </td>
            <td>
                @if($device['visited'])
                    <span class="good">Yes</span>
                    <div class="sub">{{ $device['last_visit_date'] }}</div>
                @else
                    <span class="crit">No</span>
                @endif
            </td>
            <td>{{ $device['device_status'] }}
                @if($device['condition'])<div class="sub">Condition: {{ $device['condition'] }}</div>@endif
            </td>
            <td>
                @if($device['faults']) {{ implode('; ', $device['faults']) }}
                @else <span class="none">{{ $device['visited'] ? 'None recorded' : '—' }}</span> @endif
            </td>
            <td>
                @if($device['corrective_actions']) {{ implode('; ', $device['corrective_actions']) }}
                @elseif($device['faults']) <span class="warn">Not recorded</span>
                @else <span class="none">—</span> @endif
            </td>
            <td>
                @if($reason)
                    {{ $reason['reason'] }}
                    @if(!empty($reason['note']))<div class="sub">{{ $reason['note'] }}</div>@endif
                @else
                    <span class="none">—</span>
                @endif
            </td>
            <td>
                @if($device['open_issue_count'] > 0)
                    {{ $device['open_issue_count'] }} open
                    <div class="sub">oldest {{ $device['oldest_issue_days'] }}d &middot; {{ $device['escalation_owner'] ?: 'Unassigned' }}</div>
                @else
                    <span class="none">None</span>
                @endif
            </td>
            <td>
                @if($device['transactions'] && $device['transactions']['actual'] !== null)
                    {{ number_format($device['transactions']['actual']) }}
                    @if($device['transactions']['target'])
                        <div class="sub">of {{ number_format($device['transactions']['target']) }} ({{ $device['transactions']['achievement'] }}%)</div>
                    @endif
                @else
                    <span class="none">Not supplied</span>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="9" class="none">No devices match this period and filter.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="page-break"></div>

<h2>Faults identified and corrective action</h2>
<table class="data">
    <colgroup><col style="width:35%"><col style="width:15%"><col style="width:35%"><col style="width:15%"></colgroup>
    <thead><tr><th>Fault</th><th>Devices</th><th>Corrective action taken</th><th>Devices</th></tr></thead>
    <tbody>
    @php
        $faultList = array_slice($report['faults']['by_type'], 0, 20, true);
        $actionList = array_slice($report['corrective']['by_type'], 0, 20, true);
        $faultKeys = array_keys($faultList);
        $actionKeys = array_keys($actionList);
        $rowCount = max(count($faultKeys), count($actionKeys));
    @endphp
    @if($rowCount === 0)
        <tr><td colspan="4" class="none">No faults or corrective actions were recorded in this period.</td></tr>
    @else
        @for($i = 0; $i < $rowCount; $i++)
            <tr>
                <td>{{ $faultKeys[$i] ?? '' }}</td>
                <td>{{ isset($faultKeys[$i]) ? $faultList[$faultKeys[$i]] : '' }}</td>
                <td>{{ $actionKeys[$i] ?? '' }}</td>
                <td>{{ isset($actionKeys[$i]) ? $actionList[$actionKeys[$i]] : '' }}</td>
            </tr>
        @endfor
    @endif
    </tbody>
</table>

@if($report['corrective']['outstanding'])
    <h2>Faults with no corrective action recorded ({{ $report['corrective']['outstanding_count'] }})</h2>
    <table class="data">
        <colgroup><col style="width:15%"><col style="width:25%"><col style="width:40%"><col style="width:20%"></colgroup>
        <thead><tr><th>Terminal</th><th>Merchant</th><th>Fault</th><th>Owner</th></tr></thead>
        <tbody>
        @foreach($report['corrective']['outstanding'] as $item)
            <tr>
                <td><span class="mono">{{ $item['terminal_code'] }}</span></td>
                <td>{{ $item['merchant_name'] }}</td>
                <td>{{ implode('; ', $item['faults']) }}</td>
                <td>{{ $item['owner'] ?: 'Unassigned' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<h2>Open issues, ageing and escalation (as at {{ $report['open_issues']['as_at'] }})</h2>
<table class="stat-row">
    <tr>
        @foreach($report['open_issues']['by_age'] as $band => $count)
            <td class="stat" style="width:25%">
                <div class="stat-num {{ $band === 'Over 30 days' && $count > 0 ? 'crit' : '' }}">{{ $count }}</div>
                <div class="stat-lbl">{{ $band }}</div>
            </td>
        @endforeach
    </tr>
</table>

@if($report['open_issues']['items'])
    <table class="data">
        <colgroup>
            <col style="width:11%"><col style="width:11%"><col style="width:18%"><col style="width:22%">
            <col style="width:9%"><col style="width:10%"><col style="width:19%">
        </colgroup>
        <thead><tr><th>Ticket</th><th>Terminal</th><th>Merchant</th><th>Issue</th><th>Priority</th><th>Age</th><th>Escalation owner</th></tr></thead>
        <tbody>
        @foreach($report['open_issues']['items'] as $item)
            <tr>
                <td><span class="mono">{{ $item['ticket_id'] }}</span></td>
                <td><span class="mono">{{ $item['terminal_code'] ?: '—' }}</span></td>
                <td>{{ $item['merchant_name'] ?: '—' }}</td>
                <td>{{ $item['title'] ?: $item['issue_type'] }}
                    <div class="sub">raised {{ $item['raised_on'] }}</div>
                </td>
                <td>{{ $item['priority'] }}</td>
                <td class="{{ $item['age_days'] > 30 ? 'crit' : ($item['age_days'] > 14 ? 'warn' : '') }}">{{ $item['age_days'] }}d</td>
                <td>{{ $item['owner'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <table class="data"><tbody><tr><td class="none">No issues were open at the end of this period.</td></tr></tbody></table>
@endif

<h2>Transaction activity against target</h2>
@if($txn['supplied'])
    <table class="stat-row">
        <tr>
            <td class="stat" style="width:25%"><div class="stat-num">{{ number_format($txn['actual']) }}</div><div class="stat-lbl">Transactions</div></td>
            <td class="stat" style="width:25%"><div class="stat-num">{{ $txn['target'] ? number_format($txn['target']) : '—' }}</div><div class="stat-lbl">Target</div><div class="stat-sub">{{ $txn['target_basis'] }}</div></td>
            <td class="stat" style="width:25%">
                <div class="stat-num {{ $txn['achievement'] !== null ? ($txn['achievement'] >= 100 ? 'good' : 'warn') : '' }}">{{ $txn['achievement'] !== null ? $txn['achievement'].'%' : '—' }}</div>
                <div class="stat-lbl">Achievement</div>
            </td>
            <td class="stat" style="width:25%"><div class="stat-num">{{ $txn['devices_with_data'] }}/{{ $txn['devices_total'] }}</div><div class="stat-lbl">Devices supplied</div></td>
        </tr>
    </table>
    @if(count($txn['missing_devices']) > 0)
        <div class="sub" style="margin-top:4px">
            No figures supplied for: {{ implode(', ', array_slice($txn['missing_devices'], 0, 40)) }}@if(count($txn['missing_devices']) > 40) and {{ count($txn['missing_devices']) - 40 }} more @endif.
        </div>
    @endif
@else
    <table class="data"><tbody><tr><td class="none">No transaction figures were supplied for this period.</td></tr></tbody></table>
@endif

@if($report['new_devices'])
    <h2>Devices added in the period ({{ count($report['new_devices']) }})</h2>
    <table class="data">
        <colgroup><col style="width:15%"><col style="width:35%"><col style="width:20%"><col style="width:15%"><col style="width:15%"></colgroup>
        <thead><tr><th>Terminal</th><th>Merchant</th><th>Region</th><th>How</th><th>Added</th></tr></thead>
        <tbody>
        @foreach($report['new_devices'] as $device)
            <tr>
                <td><span class="mono">{{ $device['terminal_code'] }}</span></td>
                <td>{{ $device['merchant_name'] }}</td>
                <td>{{ $device['region'] ?: '—' }}</td>
                <td>{{ $device['source'] }}</td>
                <td>{{ $device['added_on'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

@if($report['technicians'])
    <h2>Technician activity</h2>
    <table class="data">
        <colgroup><col style="width:40%"><col style="width:20%"><col style="width:20%"><col style="width:20%"></colgroup>
        <thead><tr><th>Technician</th><th>Visits</th><th>Devices</th><th>Faults found</th></tr></thead>
        <tbody>
        @foreach($report['technicians'] as $tech)
            <tr><td>{{ $tech['name'] }}</td><td>{{ $tech['visits'] }}</td><td>{{ $tech['devices'] }}</td><td>{{ $tech['faults'] }}</td></tr>
        @endforeach
        </tbody>
    </table>
@endif

<h2>Summary, recommendations and action required from the client</h2>
<div class="narrative">
    <div class="lbl">Summary</div>
    <p>{{ $record->executive_summary ?: 'Not provided.' }}</p>
</div>
<div class="narrative">
    <div class="lbl">Recommendations</div>
    <p>{{ $record->recommendations ?: 'Not provided.' }}</p>
</div>
<div class="narrative">
    <div class="lbl">Action required from the client</div>
    <p>{{ $record->client_actions ?: 'None outstanding.' }}</p>
</div>

<table class="sign">
    <tr>
        <td>
            Prepared by: {{ $record->preparedBy?->full_name ?? auth()->user()?->full_name }}<br>
            Revival Technologies &middot; {{ $meta['generated_at'] }}
        </td>
        <td style="width:10%"></td>
        <td>
            Approved by: {{ $record->approved_by_name ?: '' }}<br>
            Date: {{ $record->approved_at?->format('Y-m-d') ?: '' }}
        </td>
    </tr>
</table>

<div class="foot">
    Revival Technologies — Confidential &middot; {{ $title }} &middot; {{ $meta['period_label'] }}
    &middot; {{ $meta['client_name'] }}
</div>

</body>
</html>
