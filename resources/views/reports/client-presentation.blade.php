<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Presentation - {{ $project->project_name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #ffffff;
            color: #333;
            line-height: 1.6;
        }
        .report-header {
            background: linear-gradient(135deg, #673AB7 0%, #512DA8 100%);
            color: white;
            padding: 40px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .report-title {
            font-size: 32px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        .report-subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin: 0;
        }
        .client-logo {
            font-size: 20px;
            font-weight: 300;
            margin-top: 15px;
        }
        .section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            border-left: 5px solid #673AB7;
        }
        .section h2 {
            color: #673AB7;
            font-size: 24px;
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #F3E5F5;
        }
        .highlight-box {
            background: #F3E5F5;
            border-left: 4px solid #673AB7;
            padding: 25px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 16px;
        }
        .success-box {
            background: #E8F5E8;
            border-left: 4px solid #4CAF50;
            padding: 25px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .metrics-showcase {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        .metric-card {
            background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #D1C4E9;
        }
        .metric-number {
            font-size: 32px;
            font-weight: bold;
            color: #673AB7;
            margin-bottom: 8px;
        }
        .metric-label {
            color: #512DA8;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .project-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .detail-item {
            padding: 15px;
            background: #FAFAFA;
            border-radius: 6px;
            border-left: 3px solid #673AB7;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        .achievement-list {
            list-style: none;
            padding: 0;
        }
        .achievement-list li {
            padding: 10px 0;
            position: relative;
            padding-left: 30px;
            font-size: 16px;
        }
        .achievement-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
            font-size: 18px;
        }
        .client-quote {
            background: #E8EAF6;
            border-left: 4px solid #3F51B5;
            padding: 25px;
            margin: 20px 0;
            border-radius: 4px;
            font-style: italic;
            font-size: 16px;
            text-align: center;
        }
        .satisfaction-stars {
            text-align: center;
            font-size: 24px;
            color: #FFD700;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #F3E5F5;
        }
        @media print {
            body { background: white; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Report Header -->
    <div class="report-header">
        <div class="report-title">Project Completion Report</div>
        <div class="report-subtitle">{{ $project->project_name }}</div>
        <div class="client-logo">Prepared for {{ $project->client->company_name }}</div>
    </div>

    <!-- Project Overview -->
    <div class="section">
        <h2>Project Overview</h2>
        <div class="project-details">
            <div class="detail-item">
                <div class="detail-label">Project Type</div>
                <div class="detail-value">{{ ucfirst($project->project_type) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Project Code</div>
                <div class="detail-value">{{ $project->project_code }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Start Date</div>
                <div class="detail-value">{{ $project->start_date ? $project->start_date->format('F j, Y') : 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Completion Date</div>
                <div class="detail-value">{{ $project->completed_at ? $project->completed_at->format('F j, Y') : 'N/A' }}</div>
            </div>
        </div>

        <div class="highlight-box">
            <strong>Project Summary:</strong> {{ $completion->executive_summary ?? 'We are pleased to report that your project has been completed successfully, meeting all specified objectives and quality standards.' }}
        </div>
    </div>

    <!-- Key Results -->
    <div class="section">
        <h2>Project Results & Metrics</h2>
        <div class="metrics-showcase">
            <div class="metric-card">
                <div class="metric-number">{{ $project->completion_percentage ?? 100 }}%</div>
                <div class="metric-label">Project Completion</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $completion->quality_score ?? 4 }}/5</div>
                <div class="metric-label">Quality Score</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">{{ $project->actual_terminals_count ?? 'All' }}</div>
                <div class="metric-label">Terminals Serviced</div>
            </div>
            <div class="metric-card">
                <div class="metric-number">
                    @if($project->start_date && $project->completed_at)
                        {{ $project->start_date->diffInDays($project->completed_at) }}
                    @else
                        On Time
                    @endif
                </div>
                <div class="metric-label">
                    @if($project->start_date && $project->completed_at)
                        Days Duration
                    @else
                        Delivery
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Client Satisfaction -->
    <div class="section">
        <h2>Client Satisfaction</h2>
        <div class="satisfaction-stars">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= ($completion->client_satisfaction ?? 4))
                    ★
                @else
                    ☆
                @endif
            @endfor
        </div>
        <div class="client-quote">
            "We are highly satisfied with the professional service delivery and quality of work provided by Revival Technologies. The project was completed efficiently and to our specifications."
        </div>
    </div>

    <!-- Key Achievements -->
    <div class="section">
        <h2>Key Achievements</h2>
        <div class="success-box">
            <p>{{ $completion->key_achievements ?? 'All project objectives were successfully achieved according to the agreed specifications and timeline.' }}</p>
        </div>

        <h3 style="margin-top: 25px; color: #673AB7;">Project Deliverables Completed</h3>
        <ul class="achievement-list">
            <li>Terminal maintenance and servicing completed</li>
            <li>System updates and configurations applied</li>
            <li>Quality assurance testing performed</li>
            <li>Documentation and reporting provided</li>
            <li>Staff training and handover completed</li>
        </ul>
    </div>

    <!-- Recommendations for Future -->
    @if($completion->recommendations ?? null)
    <div class="section">
        <h2>Recommendations for Continued Success</h2>
        <div class="highlight-box">
            {{ $completion->recommendations }}
        </div>
    </div>
    @endif

    <!-- Next Steps -->
    <div class="section">
        <h2>Moving Forward</h2>
        <div class="success-box">
            <p><strong>Thank you for choosing Revival Technologies for your POS terminal management needs.</strong></p>
            <p>Our team remains available for any future support requirements, maintenance scheduling, or additional projects you may have. We look forward to continuing our partnership with {{ $project->client->company_name }}.</p>
        </div>

        <h3 style="margin-top: 25px; color: #673AB7;">Ongoing Support</h3>
        <ul class="achievement-list">
            <li>24/7 technical support hotline available</li>
            <li>Regular maintenance scheduling options</li>
            <li>Emergency response services</li>
            <li>System monitoring and alerts</li>
            <li>Future upgrade planning consultation</li>
        </ul>
    </div>

    <!-- Contact Information -->
    <div class="section">
        <h2>Contact Information</h2>
        <div class="project-details">
            <div class="detail-item">
                <div class="detail-label">Project Manager</div>
                <div class="detail-value">{{ $project->projectManager->full_name ?? 'Revival Technologies Team' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Support Phone</div>
                <div class="detail-value">+263 XXX XXXX</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Email Support</div>
                <div class="detail-value">support@revivaltech.co.zw</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Business Hours</div>
                <div class="detail-value">Mon-Fri: 8:00 AM - 5:00 PM</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Revival Technologies</strong> - Your Trusted POS Technology Partner</p>
        <p>Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}</p>
        <p>© {{ date('Y') }} Revival Technologies. All rights reserved.</p>
    </div>
</body>
</html>
