<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Audit Trail Analysis</title>
<style>
    * { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:Arial,Helvetica,sans-serif; font-size:11px; color:#1a1a1a; background:#fff; }

    .header { padding:16px 22px 14px; border-bottom:3px solid #1a3a5c; }
    .header table { width:100%; border-collapse:collapse; }
    .header td { vertical-align:bottom; }
    .header td.right { text-align:right; font-size:10px; color:#6b7280; line-height:1.7; }
    .header h1 { font-size:19px; font-weight:700; color:#1a3a5c; margin-bottom:2px; }
    .header p  { font-size:10px; color:#6b7280; }
    .badge { display:inline-block; background:#e0e7ff; color:#3730a3; padding:2px 7px; border-radius:4px; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }

    .body { padding:16px 22px 22px; }

    /* Stat row */
    .stat-row { width:100%; border-collapse:collapse; margin-bottom:18px; }
    .stat-row td { width:25%; padding:10px 14px; background:#f8fafc; border:1px solid #e5e7eb; border-radius:0; text-align:center; }
    .stat-num  { font-size:22px; font-weight:700; color:#1a3a5c; }
    .stat-lbl  { font-size:9px; color:#6b7280; margin-top:3px; text-transform:uppercase; letter-spacing:.05em; }

    /* Section */
    .section-title { font-size:12px; font-weight:700; color:#1a3a5c; padding:6px 0 4px; border-bottom:2px solid #e5e7eb; margin-bottom:10px; margin-top:18px; }

    /* Tables */
    table.data { width:100%; border-collapse:collapse; font-size:10px; margin-bottom:14px; }
    table.data thead tr { background:#1a3a5c; color:#fff; }
    table.data thead th { padding:6px 9px; text-align:left; font-weight:600; white-space:nowrap; }
    table.data tbody tr:nth-child(even) { background:#f8fafc; }
    table.data tbody td { padding:5px 9px; border-bottom:1px solid #f1f5f9; }

    /* Bar chart (CSS only) */
    .bar-row { margin-bottom:5px; }
    .bar-label { font-size:9.5px; color:#374151; margin-bottom:2px; }
    .bar-track { background:#f1f5f9; border-radius:3px; height:10px; width:100%; }
    .bar-fill  { background:#1a3a5c; height:10px; border-radius:3px; }
    .bar-val   { font-size:9px; color:#6b7280; margin-top:1px; }

    /* Two columns */
    .col2 { width:100%; border-collapse:collapse; }
    .col2 td { width:50%; vertical-align:top; padding-right:14px; }
    .col2 td:last-child { padding-right:0; padding-left:14px; }

    /* Footer */
    .footer { padding:8px 22px; border-top:1px solid #e5e7eb; }
    .footer table { width:100%; border-collapse:collapse; }
    .footer td { font-size:9px; color:#9ca3af; }
    .footer td.right { text-align:right; }
</style>
</head>
<body>

<div class="header">
    <table><tr>
        <td>
            <h1>Audit Trail Analysis</h1>
            <p>Period: <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong> — <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong></p>
        </td>
        <td class="right">
            Revival Technologies<br>
            Generated: {{ now()->format('d M Y, H:i') }}<br>
            <span class="badge">Audit Report</span>
        </td>
    </tr></table>
</div>

<div class="body">

    {{-- Summary stats --}}
    <div class="section-title">Overview</div>
    <table class="stat-row">
        <tr>
            <td><div class="stat-num">{{ number_format($total) }}</div><div class="stat-lbl">Total Events</div></td>
            <td><div class="stat-num">{{ $byCategory->sum() }}</div><div class="stat-lbl">Categorised</div></td>
            <td><div class="stat-num">{{ $byAction->count() }}</div><div class="stat-lbl">Action Types</div></td>
            <td><div class="stat-num">{{ $topEmployees->count() }}</div><div class="stat-lbl">Active Users</div></td>
        </tr>
    </table>

    <table class="col2"><tr>

        {{-- Activity by Category --}}
        <td>
            <div class="section-title">Events by Category</div>
            @php $catMax = $byCategory->max() ?: 1; @endphp
            @foreach($byCategory->sortByDesc(fn($v)=>$v) as $cat => $count)
            <div class="bar-row">
                <div class="bar-label">{{ $cat }}</div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:{{ round(($count/$catMax)*100) }}%"></div>
                </div>
                <div class="bar-val">{{ number_format($count) }} event{{ $count != 1 ? 's' : '' }}</div>
            </div>
            @endforeach
        </td>

        {{-- Activity by Action --}}
        <td>
            <div class="section-title">Events by Action</div>
            <table class="data">
                <thead><tr><th>Action</th><th>Count</th><th>% of Total</th></tr></thead>
                <tbody>
                    @foreach($byAction as $row)
                    <tr>
                        <td>{{ ucfirst($row->action) }}</td>
                        <td>{{ number_format($row->total) }}</td>
                        <td>{{ $total ? round(($row->total/$total)*100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </td>

    </tr></table>

    {{-- Top employees --}}
    <div class="section-title">Most Active Users (Top 10)</div>
    <table class="data">
        <thead><tr><th>#</th><th>Employee</th><th>Events in Period</th><th>% of Total</th></tr></thead>
        <tbody>
            @foreach($topEmployees as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ number_format($row->total) }}</td>
                <td>{{ $total ? round(($row->total/$total)*100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Daily activity --}}
    <div class="section-title">Daily Activity</div>
    @php $dayMax = $dailyActivity->max('total') ?: 1; @endphp
    <table class="data">
        <thead><tr><th>Date</th><th>Events</th><th>Activity</th></tr></thead>
        <tbody>
            @foreach($dailyActivity as $day)
            <tr>
                <td>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                <td>{{ number_format($day->total) }}</td>
                <td>
                    <div class="bar-track" style="height:7px;width:180px;display:inline-block;vertical-align:middle;">
                        <div class="bar-fill" style="width:{{ round(($day->total/$dayMax)*100) }}%;height:7px;"></div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Recent events --}}
    <div class="section-title">20 Most Recent Events</div>
    <table class="data">
        <thead><tr><th>When</th><th>Who</th><th>Category</th><th>Action</th><th>Description</th></tr></thead>
        <tbody>
            @foreach($recentEvents as $log)
            <tr>
                <td style="white-space:nowrap;">{{ $log->created_at->format('d M, H:i') }}</td>
                <td style="white-space:nowrap;">{{ $log->employee ? $log->employee->first_name . ' ' . $log->employee->last_name : '—' }}</td>
                <td>{{ $log->category }}</td>
                <td>{{ ucfirst($log->action) }}</td>
                <td>{{ Str::limit($log->description, 60) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<div class="footer">
    <table><tr>
        <td>Revival Technologies — Confidential</td>
        <td class="right">audit-trail-analysis-{{ now()->format('Y-m-d') }}.pdf</td>
    </tr></table>
</div>

</body>
</html>
