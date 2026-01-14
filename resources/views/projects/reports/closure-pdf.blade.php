<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Closure Report - {{ $project->project_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24pt;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12pt;
            opacity: 0.9;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #666;
            padding: 8px 15px 8px 0;
            width: 35%;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #333;
            vertical-align: top;
        }

        .metrics-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .metric-row {
            display: table-row;
        }

        .metric-cell {
            display: table-cell;
            width: 50%;
            padding: 15px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .metric-value {
            font-size: 28pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 10pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-box {
            background: #f9fafb;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 15px 0;
        }

        .content-box p {
            margin-bottom: 10px;
            text-align: justify;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .table th {
            background: #1e40af;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        .table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table tr:nth-child(even) {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-completed {
            background: #dcfce7;
            color: #166534;
        }

        .badge-assigned {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-in-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background: #f9fafb;
            border-top: 2px solid #e5e7eb;
            padding: 15px 30px;
            font-size: 9pt;
            color: #666;
        }

        .page-number:after {
            content: counter(page);
        }

        .highlight {
            background: #fef3c7;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }

        .divider {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>PROJECT CLOSURE REPORT</h1>
        <p>{{ $project->project_name }}</p>
    </div>

    {{-- Project Overview --}}
    <div class="section">
        <div class="section-title">Project Overview</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Project Code:</div>
                <div class="info-value"><strong>{{ $project->project_code }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Project Name:</div>
                <div class="info-value">{{ $project->project_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Client:</div>
                <div class="info-value">{{ $client->company_name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Project Type:</div>
                <div class="info-value">{{ ucfirst($project->project_type) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Project Manager:</div>
                <div class="info-value">{{ $projectManager->full_name ?? 'Not assigned' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Priority:</div>
                <div class="info-value">{{ strtoupper($project->priority) }}</div>
            </div>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="section">
        <div class="section-title">Project Timeline</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Start Date:</div>
                <div class="info-value">{{ $project->start_date ? $project->start_date->format('F j, Y') : 'Not set' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">End Date:</div>
                <div class="info-value">{{ $project->end_date ? $project->end_date->format('F j, Y') : 'Not set' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Closure Date:</div>
                <div class="info-value"><strong>{{ $closure_data['closed_at'] }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Duration:</div>
                <div class="info-value"><span class="highlight">{{ $metrics['duration_days'] }} days</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Closure Reason:</div>
                <div class="info-value">{{ $closure_data['closure_reason'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Closed By:</div>
                <div class="info-value">{{ $closure_data['closed_by'] }}</div>
            </div>
        </div>
    </div>

    {{-- Key Metrics --}}
    <div class="section">
        <div class="section-title">Project Metrics</div>
        <div class="metrics-grid">
            <div class="metric-row">
                <div class="metric-cell">
                    <div class="metric-value">{{ $metrics['total_terminals'] }}</div>
                    <div class="metric-label">Total Terminals</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-value">{{ $metrics['completion_rate'] }}%</div>
                    <div class="metric-label">Completion Rate</div>
                </div>
            </div>
            <div class="metric-row">
                <div class="metric-cell">
                    <div class="metric-value">{{ $metrics['completed_jobs'] }}/{{ $metrics['total_jobs'] }}</div>
                    <div class="metric-label">Jobs Completed</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-value">
                        @if($metrics['budget'])
                            ${{ number_format($metrics['budget'], 2) }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="metric-label">Budget</div>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    {{-- Executive Summary --}}
    <div class="section">
        <div class="section-title">Executive Summary</div>
        <div class="content-box">
            <p>{{ $closure_data['executive_summary'] }}</p>
        </div>
    </div>

    {{-- Key Achievements --}}
    <div class="section">
        <div class="section-title">Key Achievements</div>
        <div class="content-box">
            <p>{{ $closure_data['key_achievements'] }}</p>
        </div>
    </div>

    {{-- Challenges Overcome --}}
    @if($closure_data['challenges_overcome'] && $closure_data['challenges_overcome'] !== 'N/A')
    <div class="section">
        <div class="section-title">Challenges Overcome</div>
        <div class="content-box">
            <p>{{ $closure_data['challenges_overcome'] }}</p>
        </div>
    </div>
    @endif

    {{-- Lessons Learned --}}
    @if($closure_data['lessons_learned'] && $closure_data['lessons_learned'] !== 'N/A')
    <div class="section">
        <div class="section-title">Lessons Learned</div>
        <div class="content-box">
            <p>{{ $closure_data['lessons_learned'] }}</p>
        </div>
    </div>
    @endif

    {{-- Issues Found --}}
    @if($closure_data['issues_found'] && $closure_data['issues_found'] !== 'None reported')
    <div class="section">
        <div class="section-title">Issues Found</div>
        <div class="content-box">
            <p>{{ $closure_data['issues_found'] }}</p>
        </div>
    </div>
    @endif

    {{-- Recommendations --}}
    @if($closure_data['recommendations'] && $closure_data['recommendations'] !== 'N/A')
    <div class="section">
        <div class="section-title">Recommendations</div>
        <div class="content-box">
            <p>{{ $closure_data['recommendations'] }}</p>
        </div>
    </div>
    @endif

    {{-- Additional Notes --}}
    @if($closure_data['additional_notes'] && $closure_data['additional_notes'] !== 'N/A')
    <div class="section">
        <div class="section-title">Additional Notes</div>
        <div class="content-box">
            <p>{{ $closure_data['additional_notes'] }}</p>
        </div>
    </div>
    @endif

    {{-- Job Assignments Summary --}}
    @if($job_assignments->count() > 0)
    <div class="section" style="page-break-before: always;">
        <div class="section-title">Job Assignments Summary</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Technician</th>
                    <th>Assignment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($job_assignments->take(20) as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->technician->full_name ?? 'Unassigned' }}</td>
                    <td>{{ $job->created_at->format('M j, Y') }}</td>
                    <td>
                        @if($job->status === 'completed')
                            <span class="badge badge-completed">{{ ucfirst($job->status) }}</span>
                        @elseif($job->status === 'in_progress')
                            <span class="badge badge-in-progress">{{ ucfirst($job->status) }}</span>
                        @else
                            <span class="badge badge-assigned">{{ ucfirst($job->status) }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                @if($job_assignments->count() > 20)
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic; color: #666;">
                        ... and {{ $job_assignments->count() - 20 }} more assignments
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div style="float: left;">
            Generated: {{ now()->format('F j, Y g:i A') }}
        </div>
        <div style="float: right;">
            Page <span class="page-number"></span>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
