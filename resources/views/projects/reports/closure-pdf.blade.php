<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Closure Report - {{ $project->project_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1a1a2e;
            background: #fff;
        }

        /* ── COVER HEADER ─────────────────────────────────────────── */
        .cover-header {
            background: #0f172a;
            color: #fff;
            padding: 0;
            margin-bottom: 0;
        }

        .cover-top {
            background: #1e3a5f;
            padding: 18px 36px;
            display: table;
            width: 100%;
        }

        .cover-top-left {
            display: table-cell;
            vertical-align: middle;
        }

        .cover-top-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 15pt;
            font-weight: bold;
            color: #fff;
            letter-spacing: 1px;
        }

        .company-tag {
            font-size: 8pt;
            color: #94a3b8;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .doc-type {
            font-size: 8pt;
            color: #94a3b8;
            text-align: right;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .doc-id {
            font-size: 10pt;
            font-weight: bold;
            color: #60a5fa;
            text-align: right;
            margin-top: 3px;
        }

        .cover-body {
            background: #0f172a;
            padding: 28px 36px 24px;
        }

        .cover-title {
            font-size: 22pt;
            font-weight: bold;
            color: #fff;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .cover-subtitle {
            font-size: 12pt;
            color: #60a5fa;
            margin-bottom: 14px;
        }

        .cover-meta-row {
            display: table;
            width: 100%;
        }

        .cover-meta-cell {
            display: table-cell;
            padding-right: 40px;
            vertical-align: top;
        }

        .cover-meta-label {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .cover-meta-value {
            font-size: 10pt;
            color: #e2e8f0;
            font-weight: bold;
        }

        .cover-accent-bar {
            height: 4px;
            background: #2563eb;
        }

        /* ── PAGE BODY ────────────────────────────────────────────── */
        .page-body {
            padding: 24px 36px;
        }

        /* ── SECTION ──────────────────────────────────────────────── */
        .section {
            margin-bottom: 22px;
            page-break-inside: avoid;
        }

        .section-header {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .section-icon {
            display: table-cell;
            width: 6px;
            background: #2563eb;
            border-radius: 3px;
        }

        .section-title {
            display: table-cell;
            font-size: 12pt;
            font-weight: bold;
            color: #1e3a5f;
            padding: 4px 0 4px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* ── INFO TABLE ───────────────────────────────────────────── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-table tr:nth-child(odd) {
            background: #f8fafc;
        }

        .info-table tr:nth-child(even) {
            background: #fff;
        }

        .info-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #475569;
            font-size: 9pt;
            width: 32%;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-value {
            color: #1a1a2e;
            font-size: 10pt;
        }

        /* ── METRICS STRIP ────────────────────────────────────────── */
        .metrics-strip {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin: 0 -8px 20px;
        }

        .metric-box {
            display: table-cell;
            width: 25%;
            background: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-top: 3px solid #2563eb;
            padding: 14px 12px;
            text-align: center;
            vertical-align: top;
        }

        .metric-number {
            font-size: 22pt;
            font-weight: bold;
            color: #1e3a5f;
            line-height: 1;
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .metric-box.highlight {
            background: #1e3a5f;
            border-color: #1e3a5f;
            border-top-color: #60a5fa;
        }

        .metric-box.highlight .metric-number {
            color: #60a5fa;
        }

        .metric-box.highlight .metric-label {
            color: #94a3b8;
        }

        /* ── CONTENT BOX ──────────────────────────────────────────── */
        .content-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #2563eb;
            padding: 12px 16px;
            margin: 6px 0;
            border-radius: 0 4px 4px 0;
        }

        .content-box p {
            color: #334155;
            font-size: 10pt;
            text-align: justify;
        }

        /* ── TWO-COL LAYOUT ───────────────────────────────────────── */
        .two-col {
            display: table;
            width: 100%;
            border-spacing: 8px;
            margin: 0 -8px;
        }

        .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        /* ── TABLE ────────────────────────────────────────────────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9.5pt;
        }

        .data-table thead tr {
            background: #1e3a5f;
        }

        .data-table thead th {
            color: #fff;
            padding: 9px 12px;
            text-align: left;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table tbody tr:nth-child(odd) {
            background: #fff;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .data-table tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }

        /* ── BADGES ───────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 10px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .badge-completed  { background: #dcfce7; color: #166534; }
        .badge-assigned   { background: #fef3c7; color: #92400e; }
        .badge-in-progress { background: #dbeafe; color: #1d4ed8; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }

        .status-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8pt;
            font-weight: bold;
        }

        /* ── HIGHLIGHT TAG ────────────────────────────────────────── */
        .tag {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
        }

        /* ── FOOTER ───────────────────────────────────────────────── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 36px;
            background: #0f172a;
            padding: 0 36px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            vertical-align: middle;
            font-size: 8pt;
            color: #64748b;
        }

        .footer-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            font-size: 8pt;
            color: #475569;
        }

        .footer-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 8pt;
            color: #64748b;
        }

        .page-number::after { content: counter(page); }

        /* ── DIVIDER ──────────────────────────────────────────────── */
        .divider {
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 18px 0;
        }

        .no-break { page-break-inside: avoid; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════════════
     COVER HEADER
════════════════════════════════════════════════════════════════ --}}
<div class="cover-header">
    <div class="cover-top">
        <div class="cover-top-left">
            <div class="company-name">Revival Technologies</div>
            <div class="company-tag">Field Operations &amp; Project Management</div>
        </div>
        <div class="cover-top-right">
            <div class="doc-type">Project Closure Report</div>
            <div class="doc-id">{{ $project->project_code }}</div>
        </div>
    </div>
    <div class="cover-body">
        <div class="cover-title">{{ strtoupper($project->project_name) }}</div>
        <div class="cover-subtitle">{{ $client->company_name ?? 'Client' }} &mdash; {{ ucfirst($project->project_type) }} Project</div>
        <div class="cover-meta-row">
            <div class="cover-meta-cell">
                <div class="cover-meta-label">Start Date</div>
                <div class="cover-meta-value">{{ $project->start_date ? $project->start_date->format('d M Y') : '—' }}</div>
            </div>
            <div class="cover-meta-cell">
                <div class="cover-meta-label">End Date</div>
                <div class="cover-meta-value">{{ $project->end_date ? $project->end_date->format('d M Y') : '—' }}</div>
            </div>
            <div class="cover-meta-cell">
                <div class="cover-meta-label">Closed On</div>
                <div class="cover-meta-value">{{ $closure_data['closed_at'] }}</div>
            </div>
            <div class="cover-meta-cell">
                <div class="cover-meta-label">Closed By</div>
                <div class="cover-meta-value">{{ $closure_data['closed_by'] }}</div>
            </div>
        </div>
    </div>
    <div class="cover-accent-bar"></div>
</div>

<div class="page-body">

{{-- ═══════════════════════════════════════════════════════════════
     METRICS STRIP
════════════════════════════════════════════════════════════════ --}}
<div class="metrics-strip">
    <div class="metric-box highlight">
        <div class="metric-number">{{ $metrics['completion_rate'] }}%</div>
        <div class="metric-label">Completion Rate</div>
    </div>
    <div class="metric-box">
        <div class="metric-number">{{ $metrics['total_terminals'] }}</div>
        <div class="metric-label">Total Terminals</div>
    </div>
    <div class="metric-box">
        <div class="metric-number">{{ $metrics['completed_jobs'] }}<span style="font-size:14pt; color:#94a3b8;">/{{ $metrics['total_jobs'] }}</span></div>
        <div class="metric-label">Jobs Completed</div>
    </div>
    <div class="metric-box">
        <div class="metric-number">{{ $metrics['duration_days'] }}</div>
        <div class="metric-label">Days Duration</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     PROJECT OVERVIEW + TIMELINE  (side by side)
════════════════════════════════════════════════════════════════ --}}
<div class="two-col no-break">
    <div class="col" style="padding-right: 10px;">
        <div class="section">
            <div class="section-header">
                <div class="section-icon">&nbsp;</div>
                <div class="section-title">Project Overview</div>
            </div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Project Code</td>
                    <td class="info-value"><strong>{{ $project->project_code }}</strong></td>
                </tr>
                <tr>
                    <td class="info-label">Client</td>
                    <td class="info-value">{{ $client->company_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Project Type</td>
                    <td class="info-value">{{ ucfirst($project->project_type) }}</td>
                </tr>
                <tr>
                    <td class="info-label">Project Manager</td>
                    <td class="info-value">{{ $projectManager->full_name ?? 'Not assigned' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Priority</td>
                    <td class="info-value">
                        @php $pri = strtolower($project->priority ?? 'normal'); @endphp
                        @if($pri === 'high' || $pri === 'critical')
                            <span class="status-pill" style="background:#fee2e2; color:#991b1b;">{{ strtoupper($pri) }}</span>
                        @elseif($pri === 'medium')
                            <span class="status-pill" style="background:#fef3c7; color:#92400e;">MEDIUM</span>
                        @else
                            <span class="status-pill" style="background:#dcfce7; color:#166534;">{{ strtoupper($pri) }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Closure Reason</td>
                    <td class="info-value">{{ $closure_data['closure_reason'] }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col" style="padding-left: 10px;">
        <div class="section">
            <div class="section-header">
                <div class="section-icon">&nbsp;</div>
                <div class="section-title">Project Timeline</div>
            </div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Start Date</td>
                    <td class="info-value">{{ $project->start_date ? $project->start_date->format('d M Y') : 'Not set' }}</td>
                </tr>
                <tr>
                    <td class="info-label">End Date</td>
                    <td class="info-value">{{ $project->end_date ? $project->end_date->format('d M Y') : 'Not set' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Closure Date</td>
                    <td class="info-value"><strong>{{ $closure_data['closed_at'] }}</strong></td>
                </tr>
                <tr>
                    <td class="info-label">Duration</td>
                    <td class="info-value"><span class="tag">{{ $metrics['duration_days'] }} days</span></td>
                </tr>
                <tr>
                    <td class="info-label">Budget</td>
                    <td class="info-value">
                        @if($metrics['budget'])
                            <strong>${{ number_format($metrics['budget'], 2) }}</strong>
                        @else
                            <span style="color:#94a3b8;">Not set</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Closed By</td>
                    <td class="info-value">{{ $closure_data['closed_by'] }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<hr class="divider">

{{-- ═══════════════════════════════════════════════════════════════
     NARRATIVE SECTIONS
════════════════════════════════════════════════════════════════ --}}
<div class="section no-break">
    <div class="section-header">
        <div class="section-icon">&nbsp;</div>
        <div class="section-title">Executive Summary</div>
    </div>
    <div class="content-box"><p>{{ $closure_data['executive_summary'] }}</p></div>
</div>

<div class="section no-break">
    <div class="section-header">
        <div class="section-icon">&nbsp;</div>
        <div class="section-title">Key Achievements</div>
    </div>
    <div class="content-box"><p>{{ $closure_data['key_achievements'] }}</p></div>
</div>

@if(!empty($closure_data['challenges_overcome']) && $closure_data['challenges_overcome'] !== 'N/A')
<div class="section no-break">
    <div class="section-header">
        <div class="section-icon">&nbsp;</div>
        <div class="section-title">Challenges Overcome</div>
    </div>
    <div class="content-box"><p>{{ $closure_data['challenges_overcome'] }}</p></div>
</div>
@endif

@if(!empty($closure_data['lessons_learned']) && $closure_data['lessons_learned'] !== 'N/A')
<div class="section no-break">
    <div class="section-header">
        <div class="section-icon">&nbsp;</div>
        <div class="section-title">Lessons Learned</div>
    </div>
    <div class="content-box"><p>{{ $closure_data['lessons_learned'] }}</p></div>
</div>
@endif

@if(!empty($closure_data['issues_found']) && $closure_data['issues_found'] !== 'None reported')
<div class="section no-break">
    <div class="section-header">
        <div class="section-icon" style="background:#dc2626;">&nbsp;</div>
        <div class="section-title">Issues Found</div>
    </div>
    <div class="content-box" style="border-left-color:#dc2626;">
        <p>{{ $closure_data['issues_found'] }}</p>
    </div>
</div>
@endif

@if(!empty($closure_data['recommendations']) && $closure_data['recommendations'] !== 'N/A')
<div class="section no-break">
    <div class="section-header">
        <div class="section-icon" style="background:#d97706;">&nbsp;</div>
        <div class="section-title">Recommendations</div>
    </div>
    <div class="content-box" style="border-left-color:#d97706;">
        <p>{{ $closure_data['recommendations'] }}</p>
    </div>
</div>
@endif

@if(!empty($closure_data['additional_notes']) && $closure_data['additional_notes'] !== 'N/A')
<div class="section no-break">
    <div class="section-header">
        <div class="section-icon">&nbsp;</div>
        <div class="section-title">Additional Notes</div>
    </div>
    <div class="content-box"><p>{{ $closure_data['additional_notes'] }}</p></div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════
     TERMINALS COVERED
════════════════════════════════════════════════════════════════ --}}
@if(!empty($project_terminals) && $project_terminals->count() > 0)
<div class="section page-break">
    <div class="section-header">
        <div class="section-icon">&nbsp;</div>
        <div class="section-title">
            Terminals Covered
            <span style="font-size:9pt; font-weight:normal; color:#64748b; margin-left:8px;">
                {{ $project_terminals->count() }} terminal(s) assigned to this project
            </span>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:16%;">Terminal ID</th>
                <th style="width:28%;">Merchant</th>
                <th style="width:18%;">Location</th>
                <th style="width:13%;">Model</th>
                <th style="width:10%;">Status</th>
                <th style="width:10%;">Included</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project_terminals as $i => $pt)
            @php $pos = $pt->posTerminal; @endphp
            <tr>
                <td style="color:#94a3b8; font-size:8.5pt;">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td><strong>{{ $pos->terminal_id ?? '—' }}</strong></td>
                <td>
                    {{ $pos->merchant_name ?? '—' }}
                    @if($pos && $pos->merchant_contact_person)
                        <br><span style="font-size:8pt; color:#94a3b8;">{{ $pos->merchant_contact_person }}</span>
                    @endif
                </td>
                <td style="font-size:9pt;">
                    {{ $pos->city ?? '' }}
                    @if($pos && $pos->region)
                        @if($pos->city), @endif{{ $pos->region }}
                    @endif
                    @if(!$pos || (!$pos->city && !$pos->region)) — @endif
                </td>
                <td style="font-size:9pt; color:#475569;">{{ $pos->terminal_model ?? '—' }}</td>
                <td>
                    @php $st = $pos->status ?? 'unknown'; @endphp
                    @if($st === 'active')
                        <span class="badge badge-completed">Active</span>
                    @elseif($st === 'offline' || $st === 'faulty')
                        <span class="badge badge-cancelled">{{ ucfirst($st) }}</span>
                    @elseif($st === 'maintenance')
                        <span class="badge badge-assigned">Maint.</span>
                    @else
                        <span class="badge badge-assigned">{{ ucfirst($st) }}</span>
                    @endif
                </td>
                <td style="font-size:8.5pt; color:#475569;">
                    {{ $pt->included_at ? $pt->included_at->format('d M Y') : $pt->created_at->format('d M Y') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════
     VISIT HISTORY
════════════════════════════════════════════════════════════════ --}}
@if(!empty($visit_history) && $visit_history->count() > 0)
<div class="section page-break">
    <div class="section-header">
        <div class="section-icon">&nbsp;</div>
        <div class="section-title">
            Visit History
            <span style="font-size:9pt; font-weight:normal; color:#64748b; margin-left:8px;">
                {{ $visit_history->count() }} visit(s) recorded during this project
            </span>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:15%;">Date</th>
                <th style="width:20%;">Technician</th>
                <th style="width:18%;">Terminal</th>
                <th style="width:12%;">Status</th>
                <th style="width:10%;">Condition</th>
                <th style="width:20%;">Issues Found</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visit_history as $i => $visit)
            @php
                $tStatus = $visit->terminal_status_during_visit;
                $tCond   = $visit->terminal_condition;
            @endphp
            <tr>
                <td style="color:#94a3b8; font-size:8.5pt;">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td style="font-size:9pt;">
                    {{ $visit->started_at ? $visit->started_at->format('d M Y') : '—' }}
                    @if($visit->started_at)
                        <br><span style="color:#94a3b8;">{{ $visit->started_at->format('H:i') }}</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $visit->technician->full_name ?? 'Unknown' }}</strong>
                </td>
                <td style="font-size:9pt;">
                    @if($visit->posTerminal)
                        <strong>{{ $visit->posTerminal->terminal_id }}</strong>
                        <br><span style="color:#64748b; font-size:8.5pt;">{{ $visit->posTerminal->merchant_name }}</span>
                    @else
                        —
                    @endif
                </td>
                <td>
                    @if($tStatus === 'active')
                        <span class="badge badge-completed">Active</span>
                    @elseif($tStatus === 'inactive')
                        <span class="badge badge-cancelled">Inactive</span>
                    @elseif($tStatus === 'not_found')
                        <span class="badge badge-assigned" style="background:#f3f4f6; color:#374151;">Not Found</span>
                    @elseif($tStatus === 'relocated')
                        <span class="badge badge-in-progress">Relocated</span>
                    @elseif($tStatus === 'replaced')
                        <span class="badge badge-assigned">Replaced</span>
                    @else
                        <span style="color:#94a3b8; font-size:8.5pt;">{{ $tStatus ? ucfirst($tStatus) : '—' }}</span>
                    @endif
                </td>
                <td>
                    @if($tCond === 'good')
                        <span class="badge badge-completed">Good</span>
                    @elseif($tCond === 'fair')
                        <span class="badge badge-assigned">Fair</span>
                    @elseif($tCond === 'poor' || $tCond === 'damaged')
                        <span class="badge badge-cancelled">{{ ucfirst($tCond) }}</span>
                    @else
                        <span style="color:#94a3b8; font-size:8.5pt;">{{ $tCond ? ucfirst($tCond) : '—' }}</span>
                    @endif
                </td>
                <td style="font-size:8.5pt; color:#334155;">
                    {{ $visit->issues_found ?: ($visit->visit_summary ? \Illuminate\Support\Str::limit($visit->visit_summary, 60) : '—') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════
     JOB ASSIGNMENTS TABLE
════════════════════════════════════════════════════════════════ --}}
@if($job_assignments->count() > 0)
<div class="section page-break">
    <div class="section-header">
        <div class="section-icon">&nbsp;</div>
        <div class="section-title">
            Job Assignments Summary
            <span style="font-size:9pt; font-weight:normal; color:#64748b; margin-left:8px;">
                {{ $job_assignments->count() }} total
                @if($job_assignments->count() > 20) &mdash; showing first 20 @endif
            </span>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:12%;">#</th>
                <th style="width:35%;">Technician</th>
                <th style="width:25%;">Scheduled Date</th>
                <th style="width:28%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($job_assignments->take(20) as $index => $job)
            <tr>
                <td style="color:#94a3b8; font-size:8.5pt;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>
                    <strong>{{ $job->technician->full_name ?? 'Unassigned' }}</strong>
                    @if($job->assignment_id)
                        <br><span style="font-size:8pt; color:#94a3b8;">{{ $job->assignment_id }}</span>
                    @endif
                </td>
                <td style="color:#475569;">{{ $job->created_at->format('d M Y') }}</td>
                <td>
                    @php $s = $job->status ?? 'assigned'; @endphp
                    @if($s === 'completed')
                        <span class="badge badge-completed">Completed</span>
                    @elseif($s === 'in_progress')
                        <span class="badge badge-in-progress">In Progress</span>
                    @elseif($s === 'cancelled')
                        <span class="badge badge-cancelled">Cancelled</span>
                    @else
                        <span class="badge badge-assigned">{{ ucfirst(str_replace('_', ' ', $s)) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($job_assignments->count() > 20)
    <div style="text-align:center; color:#64748b; font-size:8.5pt; margin-top:8px; font-style:italic;">
        + {{ $job_assignments->count() - 20 }} additional assignments not shown
    </div>
    @endif
</div>
@endif

</div>{{-- end .page-body --}}

{{-- ═══════════════════════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════════════════════ --}}
<div class="footer">
    <div class="footer-left">Revival Technologies &copy; {{ now()->year }}</div>
    <div class="footer-center">{{ $project->project_code }} &mdash; CONFIDENTIAL</div>
    <div class="footer-right">Generated {{ now()->format('d M Y, g:i A') }} &nbsp;|&nbsp; Page <span class="page-number"></span></div>
</div>

</body>
</html>
