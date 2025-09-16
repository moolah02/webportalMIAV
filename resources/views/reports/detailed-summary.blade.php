<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detailed Technical Report - {{ $project->project_name }}</title>
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
            background: linear-gradient(135deg, #2E7D32 0%, #1B5E20 100%);
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
            color: #2E7D32;
            font-size: 20px;
            margin: 0 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #E8F5E8;
        }
        .section h3 {
            color: #4CAF50;
            font-size: 16px;
            margin: 20px 0 10px 0;
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
        .technical-box {
            background: #E8F5E8;
            border-left: 4px solid #4CAF50;
            padding: 20px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .warning-box {
            background: #FFF3E0;
            border-left: 4px solid #FF9800;
            padding: 20px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .metric-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            border-left: 4px solid #2E7D32;
        }
        .metric-number {
            font-size: 24px;
            font-weight: bold;
            color: #2E7D32;
            margin-bottom: 5px;
        }
        .metric-label {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .checklist {
            list-style: none;
            padding: 0;
        }
        .checklist li {
            padding: 5px 0;
            position: relative;
            padding-left: 25px;
        }
        .checklist li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
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
        <div class="report-title">Detailed Technical Report</div>
        <div class="report-subtitle">{{ $project->project_name }}</div>
    </div>

    <div class="section">
        <h2>Project Technical Specifications</h2>
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
                <td>Start Date</td>
                <td>{{ $project->start_date ? $project->start_date->format('F j, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Completion Date</td>
                <td>{{ $project->completed_at ? $project->completed_at->format('F j, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Duration</td>
                <td>
                    @if($project->start_date && $project->completed_at)
                        {{ $project->start_date->diffInDays($project->completed_at) }} days
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <td>Project Manager</td>
                <td>{{ $project->projectManager->full_name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Technical Performance Metrics</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-number">{{ $completion->quality_score ?? 4 }}/5</div>
                <div class="metric-label">Technical Quality</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $project->completion_percentage ?? 100 }}%</div>
                <div class="metric-label">Completion Rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $project->actual_terminals_count ?? 'N/A' }}</div>
                <div class="metric-label">Terminals Processed</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">0</div>
                <div class="metric-label">Critical Issues</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Technical Summary</h2>
        <div class="technical-box">
            <p>{{ $completion->executive_summary ?? 'Project completed successfully with all technical objectives met according to specifications.' }}</p>
        </div>
    </div>

    <div class="section">
        <h2>Technical Achievements</h2>
        <p>{{ $completion->key_achievements ?? 'All technical milestones were achieved successfully with high quality standards maintained throughout the project lifecycle.' }}</p>

        <h3>Completed Activities Checklist</h3>
        <ul class="checklist">
            <li>Initial system assessment and planning</li>
            <li>Terminal configuration and setup</li>
            <li>Software updates and patches applied</li>
            <li>Hardware maintenance performed</li>
            <li>System testing and validation</li>
            <li>Documentation and handover completed</li>
        </ul>
    </div>

    @if($completion->challenges_overcome ?? null)
    <div class="section">
        <h2>Technical Challenges & Solutions</h2>
        <div class="warning-box">
            <h3>Challenges Encountered</h3>
            <p>{{ $completion->challenges_overcome }}</p>
        </div>
    </div>
    @endif

    @if($completion->issues_found ?? null)
    <div class="section">
        <h2>Issues Identified & Resolution</h2>
        <div class="warning-box">
            <h3>Technical Issues</h3>
            <p>{{ $completion->issues_found }}</p>
        </div>
    </div>
    @endif

    @if($completion->recommendations ?? null)
    <div class="section">
        <h2>Technical Recommendations</h2>
        <div class="technical-box">
            <p>{{ $completion->recommendations }}</p>
        </div>
    </div>
    @endif

    @if($completion->lessons_learned ?? null)
    <div class="section">
        <h2>Technical Lessons Learned</h2>
        <p>{{ $completion->lessons_learned }}</p>
    </div>
    @endif

    @if($completion->additional_notes ?? null)
    <div class="section">
        <h2>Additional Technical Notes</h2>
        <p>{{ $completion->additional_notes }}</p>
    </div>
    @endif

    @if($custom_notes ?? null)
    <div class="section">
        <h2>Project-Specific Notes</h2>
        <p>{{ $custom_notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }} by {{ $generated_by->first_name }} {{ $generated_by->last_name }}</p>
        <p>© {{ date('Y') }} Revival Technologies. Technical Documentation - Confidential.</p>
    </div>
</body>
</html>
