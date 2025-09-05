<div class="visit-details">
    <h6>Visit #{{ $visit->id }}</h6>
    <p><strong>Merchant:</strong> {{ $visit->merchant_name }}</p>
    <p><strong>Technician:</strong> {{ $visit->employee ? $visit->employee->first_name . ' ' . $visit->employee->last_name : 'N/A' }}</p>
    <p><strong>Date:</strong> {{ $visit->completed_at->format('M d, Y H:i') }}</p>
    <p><strong>Status:</strong> {{ $visit->visit_status ?? 'N/A' }}</p>

    @if($visit->visit_summary)
    <div class="mt-3">
        <strong>Summary:</strong>
        <p>{{ $visit->visit_summary }}</p>
    </div>
    @endif

    @if($visit->visitTerminals->count() > 0)
    <div class="mt-3">
        <strong>Terminals Visited:</strong>
        <ul>
            @foreach($visit->visitTerminals as $terminal)
            <li>{{ $terminal->terminal_id ?? 'Terminal ID N/A' }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
