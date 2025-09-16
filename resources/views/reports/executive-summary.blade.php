<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Summary - {{ $project->project_name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        .report-header {
            background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .report-title {
            font-size: 28px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        .report-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
        }
        .section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section h2 {
            color: #1976D2;
            font-size: 20px;
            margin: 0 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #E3F2FD;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .metric-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            border-left: 4px solid #1976D2;
        }
        .metric-number {
            font-size: 24px;
            font-weight: bold;
            color: #1976D2;
            margin-bottom: 5px;
        }
        .metric-label {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .info-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }
        .info-table td:first-child {
            font-weight: 600;
            color: #666;
            width: 30%;
        }
        .summary-box {
            background: #E3F2FD;
            border-left: 4px solid #1976D2;
            padding: 20px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 11px;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        @media print {
            body { background: white; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="report-title">Executive Summary</div>
        <div class="report-subtitle">{{ $project->project_name }}</div>
    </div>

    <div class="section">
        <h2>Project Overview</h2>
        <table class="info-table">
            <tr>
                <td>Project Name</td>
                <td>{{ $project->project_name }}</td>
            </tr>
            <tr>
                <td>Client</td>
                <td>{{ $project->client->company_name }}</td>
            </tr>
            <tr>
                <td>Project Code</td>
                <td>{{ $project->project_code }}</td>
            </tr>
            <tr>
                <td>Project Type</td>
                <td>{{ ucfirst($project->project_type) }}</td>
            </tr>
            <tr>
                <td>Completed Date</td>
                <td>{{ $project->completed_at ? $project->completed_at->format('F j, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Project Manager</td>
                <td>{{ $project->projectManager->full_name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Performance Summary</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-number">{{ $completion->quality_score ?? 4 }}</div>
                <div class="metric-label">Quality Score (out of 5)</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $completion->client_satisfaction ?? 4 }}</div>
                <div class="metric-label">Client Satisfaction</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $project->completion_percentage ?? 100 }}%</div>
                <div class="metric-label">Completion Rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $project->actual_terminals_count ?? 'N/A' }}</div>
                <div class="metric-label">Terminals Serviced</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Executive Summary</h2>
        <div class="summary-box">
            <p>{{ $completion->executive_summary ?? 'Project completed successfully with all objectives met.' }}</p>
        </div>
    </div>

    <div class="section">
        <h2>Key Achievements</h2>
        <p>{{ $completion->key_achievements ?? 'All project milestones were achieved on schedule.' }}</p>

        @if($completion->challenges_overcome ?? null)
        <h3 style="margin-top: 20px; color: #666;">Challenges Overcome</h3>
        <p>{{ $completion->challenges_overcome }}</p>
        @endif
    </div>

    @if($completion->recommendations ?? null)
    <div class="section">
        <h2>Recommendations</h2>
        <p>{{ $completion->recommendations }}</p>
    </div>
    @endif

    @if($custom_notes ?? null)
    <div class="section">
        <h2>Additional Notes</h2>
        <p>{{ $custom_notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }} by {{ $generated_by->first_name }} {{ $generated_by->last_name }}</p>
        <p>© {{ date('Y') }} Revival Technologies. Confidential.</p>
    </div>
</body>
</html>
