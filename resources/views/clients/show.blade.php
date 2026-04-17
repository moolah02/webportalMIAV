{{-- resources/views/clients/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="page-title">{{ $client->company_name }}</h2>
            <p class="page-subtitle mt-1">Client details &amp; history</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('clients.index') }}" class="btn-secondary btn-sm">&#8592; Back to Clients</a>
            <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn-primary btn-sm">Edit</a>
        </div>
    </div>

    {{-- Summary bar --}}
    @php
        $statusClass = match($client->status) {
            'active'   => 'badge-green',
            'prospect' => 'badge-yellow',
            'inactive' => 'badge-gray',
            'lost'     => 'badge-red',
            default    => 'badge-gray',
        };
    @endphp
    <div class="ui-card mb-4">
        <div class="ui-card-body grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <div class="ui-label">Client Code</div>
                <div class="text-sm font-medium text-gray-900">{{ $client->client_code ?: '&#8212;' }}</div>
            </div>
            <div>
                <div class="ui-label">Status</div>
                <span class="badge {{ $statusClass }}">{{ ucfirst($client->status) }}</span>
            </div>
            <div>
                <div class="ui-label">Region</div>
                <div class="text-sm font-medium text-gray-900">{{ $client->region ?: '&#8212;' }}</div>
            </div>
            <div>
                <div class="ui-label">City</div>
                <div class="text-sm font-medium text-gray-900">{{ $client->city ?: '&#8212;' }}</div>
            </div>
        </div>
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Left (2/3) --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Company Information</h4>
                </div>
                <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="ui-label">Company Name</div>
                        <div class="text-sm text-gray-900">{{ $client->company_name }}</div>
                    </div>
                    <div>
                        <div class="ui-label">Contact Person</div>
                        <div class="text-sm text-gray-900">{{ $client->contact_person ?: '&#8212;' }}</div>
                    </div>
                    <div>
                        <div class="ui-label">Email</div>
                        @if($client->email)
                            <a href="mailto:{{ $client->email }}" class="text-sm text-[#1a3a5c] hover:underline">{{ $client->email }}</a>
                        @else
                            <div class="text-sm text-gray-900">&#8212;</div>
                        @endif
                    </div>
                    <div>
                        <div class="ui-label">Phone</div>
                        @if($client->phone)
                            <a href="tel:{{ $client->phone }}" class="text-sm text-[#1a3a5c] hover:underline">{{ $client->phone }}</a>
                        @else
                            <div class="text-sm text-gray-900">&#8212;</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Address</h4>
                </div>
                <div class="ui-card-body">
                    <div class="text-sm text-gray-900 whitespace-pre-line">
                        {{ trim(collect([$client->address, $client->city, $client->region])->filter()->join(', ')) ?: '&#8212;' }}
                    </div>
                </div>
            </div>

            <div class="ui-card">
                @php
                    $start  = $client->contract_start_date;
                    $end    = $client->contract_end_date;
                    $isPast = $end ? $end->isPast() : false;
                    $isSoon = $end ? (!$isPast && $end->diffInDays(now()) <= 30) : false;
                @endphp
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Contract</h4>
                </div>
                <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="ui-label">Start</div>
                        <div class="text-sm text-gray-900">{{ $start?->format('M d, Y') ?: '&#8212;' }}</div>
                    </div>
                    <div>
                        <div class="ui-label">End</div>
                        <div class="text-sm text-gray-900 flex items-center gap-2 flex-wrap">
                            {{ $end?->format('M d, Y') ?: '&#8212;' }}
                            @if($isPast)
                                <span class="badge badge-red">Expired</span>
                            @elseif($isSoon)
                                <span class="badge badge-yellow">Expiring</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar (1/3) --}}
        <div class="flex flex-col gap-4">
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Quick Actions</h4>
                </div>
                <div class="ui-card-body flex flex-wrap gap-2">
                    <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn-primary btn-sm">Edit Client</a>
                    <button type="button" class="btn-secondary btn-sm"
                            onclick="contactClient('{{ $client->email }}')">Contact</button>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Related</h4>
                </div>
                <div class="ui-card-body flex flex-col divide-y divide-gray-100">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">POS Terminals</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $client->posTerminals()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Projects</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $client->projects()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Tickets</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $client->tickets()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function contactClient(email) {
    if (email) { window.location.href = 'mailto:' + email; }
    else { alert('No email address available for this client'); }
}
</script>
@endsection