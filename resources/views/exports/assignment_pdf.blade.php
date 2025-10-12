<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Terminal Assignment Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .header .subtitle {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #7f8c8d;
        }

        .summary {
            display: inline-block;
            width: 100%;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .summary-item {
            display: inline-block;
            width: 23%;
            text-align: center;
            vertical-align: top;
        }

        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            color: #3498db;
            display: block;
        }

        .summary-item .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .assignments-section {
            margin-top: 30px;
        }

        .assignments-section h2 {
            font-size: 18px;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .technician-block {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .technician-header {
            background: #3498db;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
        }

        .technician-info {
            background: #ecf0f1;
            padding: 8px 15px;
            font-size: 11px;
            color: #555;
        }

        .terminals-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .terminals-table th {
            background: #f1f2f6;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
            font-weight: bold;
        }

        .terminals-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }

        .terminals-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .priority-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .priority-normal { background: #3498db; color: white; }
        .priority-high { background: #f39c12; color: white; }
        .priority-emergency { background: #e74c3c; color: white; }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Terminal Assignment Export</h1>
        <div class="subtitle">Generated on {{ $export_date }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="number">{{ $total_assignments }}</span>
            <span class="label">Technicians</span>
        </div>
        <div class="summary-item">
            <span class="number">{{ $total_terminals }}</span>
            <span class="label">Terminals</span>
        </div>
        <div class="summary-item">
            <span class="number">{{ $assignments->pluck('regions')->flatten()->unique()->count() }}</span>
            <span class="label">Regions</span>
        </div>
        <div class="summary-item">
            <span class="number">{{ round($total_terminals * 1.5, 1) }}h</span>
            <span class="label">Est. Time</span>
        </div>
    </div>

    <div class="assignments-section">
        <h2>Assignment Details</h2>

        @php
            $groupedAssignments = $assignments->groupBy('technician_name');
        @endphp

        @foreach($groupedAssignments as $technicianName => $techAssignments)
            <div class="technician-block">
                <div class="technician-header">
                    {{ $technicianName }}
                </div>
                <div class="technician-info">
                    Terminals: {{ $techAssignments->count() }} |
                    Regions: {{ $techAssignments->pluck('city')->filter()->unique()->implode(', ') ?: 'Various' }} |
                    Priority: <span class="priority-badge priority-{{ strtolower($techAssignments->first()['priority'] ?? 'normal') }}">{{ strtoupper($techAssignments->first()['priority'] ?? 'Normal') }}</span>
                </div>

                <table class="terminals-table">
                    <thead>
                        <tr>
                            <th style="width: 15%">Terminal ID</th>
                            <th style="width: 25%">Merchant Name</th>
                            <th style="width: 15%">City</th>
                            <th style="width: 15%">Region</th>
                            <th style="width: 30%">Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($techAssignments as $assignment)
                            <tr>
                                <td><strong>{{ $assignment['terminal_id'] }}</strong></td>
                                <td>{{ $assignment['merchant_name'] }}</td>
                                <td>{{ $assignment['city'] ?: 'N/A' }}</td>
                                <td>{{ $assignment['area'] ?: 'N/A' }}</td>
                                <td>{{ $assignment['address'] ?: 'No address provided' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <div class="footer">
        <div>POS Terminal Management System - Assignment Export</div>
        <div>Report generated automatically on {{ date('Y-m-d H:i:s') }}</div>
    </div>
</body>
</html>
