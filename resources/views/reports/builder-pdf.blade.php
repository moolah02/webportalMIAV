<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $filename }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

        /* Header — table layout instead of flex (dompdf flexbox support is poor) */
        .header { padding: 14px 20px 12px; border-bottom: 3px solid #1a3a5c; }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: bottom; }
        .header td.right { text-align: right; font-size: 10px; color: #6b7280; line-height: 1.6; }
        .header h1 { font-size: 18px; font-weight: 700; color: #1a3a5c; margin-bottom: 2px; }
        .header p  { font-size: 10px; color: #6b7280; }
        .badge { display: inline-block; background: #e0e7ff; color: #3730a3; padding: 2px 7px;
                 border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase;
                 letter-spacing: .5px; }

        /* Truncation warning */
        .truncation-notice { margin: 8px 20px 0; padding: 6px 10px; background: #fff7ed;
                             border-left: 3px solid #f59e0b; font-size: 10px; color: #92400e; }

        /* Table */
        .table-wrap { padding: 12px 20px 20px; }
        table.data { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.data thead tr { background: #1a3a5c; color: #fff; }
        table.data thead th { padding: 7px 9px; text-align: left; font-weight: 600;
                              letter-spacing: .3px; white-space: nowrap; }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data tbody td { padding: 5px 9px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }

        /* Footer */
        .footer { padding: 8px 20px; border-top: 1px solid #e5e7eb; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { font-size: 9px; color: #9ca3af; vertical-align: middle; }
        .footer td.right { text-align: right; }

        .no-data { padding: 40px 20px; text-align: center; color: #9ca3af; font-style: italic; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1>Report Builder Export</h1>
                    <p>Data source: <strong>{{ ucwords(str_replace('_', ' ', $baseTable)) }}</strong> &nbsp;&middot;&nbsp; {{ $rowCount }} rows</p>
                </td>
                <td class="right">
                    Revival Technologies<br>
                    Generated: {{ $generatedAt }}<br>
                    <span class="badge">PDF Export</span>
                </td>
            </tr>
        </table>
    </div>

    @if(!empty($truncated))
    <div class="truncation-notice">
        Note: This PDF shows the first {{ $rowCount }} rows. Export as CSV to get the full dataset.
    </div>
    @endif

    <div class="table-wrap">
        @if($results->isEmpty())
            <div class="no-data">No data to display.</div>
        @else
            <table class="data">
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
        <table>
            <tr>
                <td>Revival Technologies &mdash; Confidential</td>
                <td class="right">{{ $filename }}.pdf</td>
            </tr>
        </table>
    </div>

</body>
</html>
