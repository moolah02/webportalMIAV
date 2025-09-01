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
                        <strong>{{ $visit->employee->name ?? 'Unknown Employee' }}</strong>
                        <br><small class="text-muted">ID: {{ $visit->employee_id }}</small>
                    </td>
                </tr>
                <tr>
                    <td class="field-label">Assignment</td>
                    <td class="field-value">
                        <strong>{{ $visit->assignment->title ?? $visit->assignment->name ?? 'No Assignment' }}</strong>
                        @if($visit->assignment_id)
                            <br><small class="text-muted">ID: {{ $visit->assignment_id }}</small>
                        @endif
                    </td>
                </tr>
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

                <!-- Terminals -->
                <tr>
                    <td class="field-label">Terminals</td>
                    <td class="field-value">
                        @php $terminals = is_array($visit->terminals) ? $visit->terminals : []; @endphp
                        @if(count($terminals))
                            <table class="sub-table">
                                <thead>
                                    <tr>
                                        <th>Terminal ID</th>
                                        <th>Status</th>
                                        <th>Condition</th>
                                        <th>Device Type</th>
                                        <th>Serial Number</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($terminals as $t)
                                        <tr>
                                            <td><strong>{{ $t['terminalId'] ?? '—' }}</strong></td>
                                            <td>{{ $t['status'] ?? '—' }}</td>
                                            <td>{{ $t['condition'] ?? '—' }}</td>
                                            <td>{{ $t['deviceType'] ?? '—' }}</td>
                                            <td>{{ $t['serialNumber'] ?? '—' }}</td>
                                            <td>{{ $t['comments'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <span class="empty-text">No terminals attached.</span>
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

                <!-- Other Terminals Found -->
                <tr>
                    <td class="field-label">Other Terminals</td>
                    <td class="field-value">
                        @php $other = is_array($visit->other_terminals_found) ? $visit->other_terminals_found : []; @endphp
                        @if(count($other))
                            <div class="code-display">{{ json_encode($other, JSON_PRETTY_PRINT) }}</div>
                        @else
                            <span class="empty-text">None found.</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
