<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $filename }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #16202C; background: #fff; }

        /* dompdf: tables for layout, no flexbox */
        .header { padding: 14px 20px 12px; border-bottom: 3px solid #1F4F87; }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: bottom; }
        .header td.right { text-align: right; font-size: 9.5px; color: #6A7686; line-height: 1.6; }
        .header h1 { font-size: 17px; font-weight: 700; color: #1F4F87; margin-bottom: 3px; }
        .header p { font-size: 10px; color: #445162; line-height: 1.5; }

        .summary { padding: 10px 20px 0; }
        .summary table { border-collapse: collapse; font-size: 9.5px; }
        .summary th { text-align: left; color: #6A7686; font-weight: 600; padding: 3px 14px 3px 0; border-bottom: 1px solid #E1E6EC; }
        .summary td { padding: 3px 14px 3px 0; border-bottom: 1px solid #EEF1F4; }
        .summary td.num { text-align: right; font-weight: 700; }

        .table-wrap { padding: 12px 20px 16px; }
        table.data { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 8.5px; }
        table.data thead tr { background: #1F4F87; color: #fff; }
        table.data thead th { padding: 6px 6px; text-align: left; font-weight: 600; vertical-align: bottom; word-wrap: break-word; }
        table.data tbody tr:nth-child(even) { background: #F7F9FB; }
        table.data tbody td { padding: 5px 6px; border-bottom: 1px solid #E1E6EC; vertical-align: top; word-wrap: break-word; }
        .mono { font-family: "DejaVu Sans Mono", Consolas, monospace; font-size: 8.5px; }
        .sub { color: #6A7686; margin-top: 2px; }
        .none { color: #9AA6B4; }

        .footer { padding: 8px 20px; border-top: 1px solid #E1E6EC; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { font-size: 8.5px; color: #9AA6B4; }
        .footer td.right { text-align: right; }
        .no-data { padding: 40px 20px; text-align: center; color: #6A7686; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1>Discovered Terminals (Extra Work)</h1>
                    <p>
                        <strong>{{ $rows->count() }}</strong> {{ $rows->count() === 1 ? 'terminal' : 'terminals' }} found on site by technicians
                        &middot; {{ $period }}
                        @if($search) &middot; Search: <strong>{{ $search }}</strong> @endif
                    </p>
                    <p>Terminals that were not on the original list: registered from the tablet during field visits.</p>
                </td>
                <td class="right">
                    Revival Technologies<br>
                    Generated: {{ $generatedAt }} (Harare time)
                </td>
            </tr>
        </table>
    </div>

    @if($rows->isNotEmpty())
    <div class="summary">
        <table>
            <thead><tr><th>Found by</th><th>Terminals</th></tr></thead>
            <tbody>
                @foreach($byTechnician as $name => $count)
                <tr><td>{{ $name }}</td><td class="num">{{ $count }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="table-wrap">
        @if($rows->isEmpty())
            <div class="no-data">No discovered terminals match these filters.</div>
        @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width:3%">#</th>
                    <th style="width:9%">Found on</th>
                    <th style="width:10%">Found by</th>
                    <th style="width:9%">Terminal ID</th>
                    <th style="width:13%">Merchant / Client</th>
                    <th style="width:11%">Contact</th>
                    <th style="width:13%">Location</th>
                    <th style="width:11%">Model / Serial</th>
                    <th style="width:7%">Status</th>
                    <th style="width:14%">Found during / Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r['found_on'] }}</td>
                    <td>{!! $r['found_by'] !== '' && $r['found_by'] !== null ? e($r['found_by']) : '<span class="none">Not recorded</span>' !!}</td>
                    <td class="mono">{{ $r['terminal'] }}</td>
                    <td>
                        {{ $r['merchant'] ?: '—' }}
                        @if($r['client'])<div class="sub">{{ $r['client'] }}</div>@endif
                        @if($r['business'])<div class="sub">{{ $r['business'] }}</div>@endif
                    </td>
                    <td>
                        {{ $r['contact'] ?: '—' }}
                        @if($r['phone'])<div class="sub">{{ $r['phone'] }}</div>@endif
                    </td>
                    <td>
                        {{ $r['address'] ?: '—' }}
                        @if($r['city'] || $r['region'])<div class="sub">{{ implode(', ', array_filter([$r['city'], $r['region']])) }}</div>@endif
                    </td>
                    <td>
                        {{ $r['model'] ?: '—' }}
                        @if($r['serial'])<div class="sub mono">{{ $r['serial'] }}</div>@endif
                    </td>
                    <td>{{ $r['status'] ? ucfirst($r['status']) : '—' }}</td>
                    <td>
                        {{ $r['found_visit'] ?: '—' }}
                        @if($r['note'])<div class="sub">{{ $r['note'] }}</div>@endif
                        @if($r['visits'] > 0)<div class="sub">{{ $r['visits'] }} {{ $r['visits'] === 1 ? 'visit' : 'visits' }} recorded since</div>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="footer">
        <table>
            <tr>
                <td>Revival Technologies &mdash; Confidential</td>
                <td class="right">{{ $filename }}.pdf</td>
            </tr>
        </table>
    </div>

</body>
</html>
