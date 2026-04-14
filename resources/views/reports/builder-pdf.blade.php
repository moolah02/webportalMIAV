<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $filename }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

        /* Header */
        .header { padding: 18px 24px 14px; border-bottom: 3px solid #1a3a5c; display: flex; justify-content: space-between; align-items: flex-end; }
        .header-left h1 { font-size: 20px; font-weight: 700; color: #1a3a5c; margin-bottom: 2px; }
        .header-left p  { font-size: 10px; color: #6b7280; }
        .header-right   { text-align: right; font-size: 10px; color: #6b7280; line-height: 1.6; }
        .badge { display: inline-block; background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-top: 4px; }

        /* Table */
        .table-wrap { padding: 16px 24px 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead tr { background: #1a3a5c; color: #fff; }
        thead th { padding: 7px 10px; text-align: left; font-weight: 600; letter-spacing: .3px; white-space: nowrap; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }

        /* Footer */
        .footer { padding: 10px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }

        .no-data { padding: 40px 24px; text-align: center; color: #9ca3af; font-style: italic; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <h1>&#128202; Report Builder Export</h1>
            <p>Data source: <strong>{{ ucwords(str_replace('_', ' ', $baseTable)) }}</strong> &nbsp;&middot;&nbsp; {{ $rowCount }} rows</p>
        </div>
        <div class="header-right">
            <div>Revival Technologies</div>
            <div>Generated: {{ $generatedAt }}</div>
            <div><span class="badge">PDF Export</span></div>
        </div>
    </div>

    <div class="table-wrap">
        @if($results->isEmpty())
            <div class="no-data">No data to display.</div>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $row)
                        <tr>
                            @foreach($columns as $col)
                                <td>{{ $row->$col ?? '—' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="footer">
        <span>Revival Technologies &mdash; Confidential</span>
        <span>{{ $filename }}.pdf</span>
    </div>

</body>
</html>
