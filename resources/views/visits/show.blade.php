@extends('layouts.app')

@section('content')
<style>
/* Clean Neutral Styling */
.container-fluid {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 2rem 1rem;
}

/* Enhanced Back Button */
.btn-light {
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 0.6rem 1.2rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    color: #495057;
}

.btn-light:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background: #f8f9fa;
    color: #495057;
}

/* Single Table Container */
.table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    background: white;
    border-bottom: 2px solid #e9ecef;
    padding: 1.5rem;
    font-size: 1.3rem;
    font-weight: 600;
    color: #495057;
    text-align: center;
}

/* Enhanced Table */
.table {
    margin-bottom: 0;
    font-size: 0.9rem;
}

.table tbody td {
    padding: 1rem 1.5rem;
    border-color: #f1f3f4;
    vertical-align: top;
    color: #495057;
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

/* Field Labels */
.field-label {
    width: 180px;
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Field Values */
.field-value {
    color: #495057;
    font-weight: 500;
}

/* Sub-tables for complex data */
.sub-table {
    margin: 0;
    font-size: 0.85rem;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
}

.sub-table thead th {
    background: #f8f9fa;
    border: none;
    padding: 0.75rem;
    font-weight: 600;
    color: #495057;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sub-table tbody td {
    padding: 0.75rem;
    border-color: #f1f3f4;
}

/* Evidence Links */
.evidence-link {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.evidence-link:hover {
    color: #0056b3;
    text-decoration: underline;
}

/* Code Display */
.code-display {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 1rem;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    color: #495057;
    white-space: pre-wrap;
    max-height: 200px;
    overflow-y: auto;
}

/* Empty States */
.empty-text {
    color: #6c757d;
    font-style: italic;
}

/* Status Indicators */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

/* Technical Details Styles */
.technical-details {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #f8f9fa;
    overflow: hidden;
}

.technical-summary {
    padding: 12px 16px;
    background: #f1f3f4;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 600;
    color: #495057;
    transition: background-color 0.2s ease;
    list-style: none;
    outline: none;
}

.technical-summary:hover {
    background: #e9ecef;
}

.technical-summary::-webkit-details-marker {
    display: none;
}

.technical-summary::after {
    content: '▼';
    font-size: 0.75em;
    transition: transform 0.2s;
    color: #6c757d;
}

.technical-details[open] .technical-summary::after {
    transform: rotate(180deg);
}

.technical-icon {
    margin-right: 8px;
    font-size: 1.1em;
}

.data-status {
    margin-left: auto;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.technical-content {
    padding: 20px;
    background: white;
}

.detail-group {
    margin-bottom: 20px;
}

.detail-heading {
    font-size: 0.9rem;
    font-weight: 700;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
    padding-bottom: 6px;
    border-bottom: 2px solid #e9ecef;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f1f3f4;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.85rem;
    flex: 0 0 140px;
}

.detail-value {
    color: #495057;
    font-weight: 500;
    text-align: right;
    flex: 1;
}

.status-value {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.status-value.active, .status-value.found {
    background: #d4edda;
    color: #155724;
}

.status-value.working {
    background: #cce7ff;
    color: #0056b3;
}

.status-value.offline, .status-value.faulty {
    background: #f8d7da;
    color: #721c24;
}

.status-value.maintenance {
    background: #fff3cd;
    color: #856404;
}

.issue-value {
    color: #dc3545;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .technical-content .row {
        margin: 0;
    }

    .technical-content .col-md-6 {
        padding: 0;
        margin-bottom: 15px;
    }

    .detail-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .detail-label {
        flex: none;
        margin-bottom: 4px;
    }

    .detail-value {
        text-align: left;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem 0.5rem;
    }

    .field-label {
        width: 120px;
        font-size: 0.8rem;
    }

    .table {
        font-size: 0.8rem;
    }

    .sub-table {
        font-size: 0.75rem;
    }
}
</style>

<div class="container-fluid py-4">
    <a href="{{ route('visits.index') }}" class="btn btn-light mb-3">← All Visits</a>

    <div class="table-container">
        <div class="table-header">
            Visit #{{ $visit->id }} Details
        </div>
        <table class="table">
            <tbody>
                <!-- Basic Information -->
                <tr>
                    <td class="field-label">Status</td>
                    <td class="field-value">
                        @if($visit->completed_at)
                            <span class="status-badge status-completed">Completed</span>
                            {{ $visit->completed_at->format('F j, Y \a\t g:i A') }}
                        @else
                            <span class="status-badge status-pending">In Progress</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="field-label">Merchant</td>
                    <td class="field-value">
                        <strong>{{ $visit->merchant_name ?? 'Unknown Merchant' }}</strong>
                        <br><small class="text-muted">ID: {{ $visit->merchant_id }}</small>
                    </td>
                </tr>
                <tr>
                    <td class="field-label">Employee</td>
                    <td class="field-value">
                        <strong>{{ optional($visit->employee)->full_name ?? 'Unknown Employee' }}</strong>
                        <br><small class="text-muted">ID: {{ $visit->employee_id }}</small>
                    </td>
                </tr>
                <tr>
                    <td class="field-label">Assignment</td>
                    <td class="field-value">
                        <strong>{{ $visit->assignment_id ?? 'No Assignment' }}</strong>
                    </td>
                </tr>
                @if(!empty($visit->contact_person))
                <tr>
                    <td class="field-label">Contact Person</td>
                    <td class="field-value">
                        {{ $visit->contact_person }}
                        @if(!empty($visit->phone_number))
                            <br><small class="text-muted">{{ $visit->phone_number }}</small>
                        @endif
                    </td>
                </tr>
                @endif
                <tr>
                    <td class="field-label">Visit Summary</td>
                    <td class="field-value">{{ $visit->visit_summary ?: 'No summary provided.' }}</td>
                </tr>
                @if(!empty($visit->action_points))
                <tr>
                    <td class="field-label">Action Points</td>
                    <td class="field-value">{{ $visit->action_points }}</td>
                </tr>
                @endif

                <!-- Primary Terminal -->
                <tr>
                    <td class="field-label">Primary Terminal</td>
                    <td class="field-value">
                        @php $terminal = $visit->getCompleteTerminalInfo(); @endphp
                        @if(!empty($terminal))
                            {{-- Debug info --}}
                            @if(isset($terminal['found_in_pos_terminals']))
                                <div class="small text-muted mb-2">
                                    @if($terminal['found_in_pos_terminals'])
                                        ✓ Terminal data
                                    @else
                                        ⚠️ Terminal not found  (showing basic data only)
                                    @endif
                                </div>
                            @endif

                            <table class="sub-table">
                                <thead>
                                    <tr>
                                        <th>Terminal ID</th>
                                        <th>Status</th>
                                        <th>Condition</th>
                                        <th>Model</th>
                                        <th>Serial Number</th>
                                        @if(!empty($terminal['issues']))
                                        <th>Issues</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>{{ $terminal['terminal_id'] ?? '—' }}</strong></td>
                                        <td>{{ $terminal['status'] ?? ($terminal['current_status'] ?? '—') }}</td>
                                        <td>{{ $terminal['condition_status'] ?? ($terminal['condition'] ?? '—') }}</td>
                                        <td>{{ $terminal['terminal_model'] ?? '—' }}</td>
                                        <td>{{ $terminal['serial_number'] ?? '—' }}</td>
                                        @if(!empty($terminal['issues']))
                                        <td>{{ $terminal['issues'] }}</td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>

                            @if(!empty($terminal['last_service_date']) || !empty($terminal['next_service_due']))
                            <div class="mt-2 small text-muted">
                                @if(!empty($terminal['last_service_date']))
                                <div>Last Service: {{ $terminal['last_service_date'] }}</div>
                                @endif
                                @if(!empty($terminal['next_service_due']))
                                <div>Next Service Due: {{ $terminal['next_service_due'] }}</div>
                                @endif
                            </div>
                            @endif

                            {{-- Debug: Show raw terminal data --}}
                            <details class="mt-2">
                                <summary class="small text-muted" style="cursor: pointer;">View Extra Terminal details</summary>
                                <div class="code-display mt-2">{{ json_encode($terminal, JSON_PRETTY_PRINT) }}</div>
                            </details>
                        @else
                            <span class="empty-text">No primary terminal data.</span>
                        @endif
                    </td>
                </tr>

                <!-- Other Terminals Found -->
                <tr>
                    <td class="field-label">Other Terminals</td>
                    <td class="field-value">
                        @php $otherTerminals = is_array($visit->other_terminals_found) ? $visit->other_terminals_found : []; @endphp
                        @if(count($otherTerminals))
                            <div class="small text-muted mb-2">Found {{ count($otherTerminals) }} additional terminal(s)</div>
                            <div class="code-display">{{ json_encode($otherTerminals, JSON_PRETTY_PRINT) }}</div>
                        @else
                            <span class="empty-text">None found.</span>
                        @endif
                    </td>
                </tr>

                <!-- Evidence -->
                <tr>
                    <td class="field-label">Evidence</td>
                    <td class="field-value">
                        @php $evidence = is_array($visit->evidence) ? $visit->evidence : []; @endphp
                        @if(count($evidence))
                            @foreach($evidence as $idx => $e)
                                <div class="mb-2">
                                    <strong>Evidence {{ $idx + 1 }}:</strong>
                                    @if(\Illuminate\Support\Str::startsWith($e, ['http://','https://','/storage/']))
                                        <a href="{{ $e }}" target="_blank" rel="noopener" class="evidence-link">View Evidence</a>
                                    @else
                                        {{ $e }}
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <span class="empty-text">No evidence.</span>
                        @endif
                    </td>
                </tr>

                <!-- Signature -->
                @if(!empty($visit->signature))
                <tr>
                    <td class="field-label">Signature</td>
                    <td class="field-value">
                        @if(\Illuminate\Support\Str::startsWith($visit->signature, ['data:image/', 'data:application/']))
                            <div class="small text-muted mb-2">Digital signature captured</div>
                            <div class="code-display" style="max-height: 100px;">
                                {{ \Illuminate\Support\Str::limit($visit->signature, 200) }}...
                            </div>
                        @else
                            {{ $visit->signature }}
                        @endif
                    </td>
                </tr>
                @endif

                <!-- Timestamps -->
                <tr>
                    <td class="field-label">Created</td>
                    <td class="field-value">
                        {{ $visit->created_at ? $visit->created_at->format('F j, Y \a\t g:i A') : '—' }}
                    </td>
                </tr>
                <tr>
                    <td class="field-label">Updated</td>
                    <td class="field-value">
                        {{ $visit->updated_at ? $visit->updated_at->format('F j, Y \a\t g:i A') : '—' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
