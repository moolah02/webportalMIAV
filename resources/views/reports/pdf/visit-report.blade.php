<!DOCTYPE html>
<html>
<head>
    <title>Visit Report #{{ $visit->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; }
        .content { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Visit Report #{{ $visit->id }}</h1>
        <p>Generated: {{ now()->format('M d, Y H:i') }}</p>
    </div>

    <div class="content">
        <p><strong>Merchant:</strong> {{ $visit->merchant_name }}</p>
        <p><strong>Technician:</strong> {{ $visit->employee ? $visit->employee->first_name . ' ' . $visit->employee->last_name : 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $visit->completed_at->format('M d, Y H:i') }}</p>

        @if($visit->visit_summary)
        <h3>Summary</h3>
        <p>{{ $visit->visit_summary }}</p>
        @endif
    </div>
</body>
</html>
