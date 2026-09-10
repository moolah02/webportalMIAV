{{-- resources/views/clients/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Client Details')

@section('header-actions')
<a href="{{ route('clients.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Clients</a>
<a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit</a>
@endsection

@push('styles')
<style>
.cs-head { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 18px; margin-bottom: 16px; }
.cs-name { font-size: 16px; font-weight: 600; color: var(--mv-ink); }
.cs-code { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 7px; }
.cs-facts { display: flex; gap: 28px; margin-left: auto; flex-wrap: wrap; }
.cs-fact span { display: block; font-size: 11.5px; color: var(--mv-muted); }
.cs-fact strong { font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
.cs-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 16px; align-items: start; }
@media (max-width: 1000px) { .cs-layout { grid-template-columns: 1fr; } }
.cs-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.cs-dl { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 20px; padding: 16px 18px; }
@media (max-width: 600px) { .cs-dl { grid-template-columns: 1fr; } }
.cs-dl .v { font-size: 13.5px; color: var(--mv-ink); margin-top: 2px; }
.cs-link { color: var(--mv-accent-ink); text-decoration: none; }
.cs-link:hover { text-decoration: underline; }
.cs-rows { padding: 4px 18px; }
.cs-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; font-size: 13px; color: var(--mv-ink-2); }
.cs-row + .cs-row { border-top: 1px solid var(--mv-line); }
.cs-row strong { font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.cs-actions { display: flex; flex-direction: column; gap: 8px; padding: 14px 18px; }
.cs-actions > * { width: 100%; justify-content: center; }
</style>
@endpush

@section('content')
@php
    $statusClass = match($client->status) {
        'active'   => 'badge-green',
        'prospect' => 'badge-blue',
        'inactive' => 'badge-gray',
        'lost'     => 'badge-red',
        default    => 'badge-gray',
    };
    $start  = $client->contract_start_date;
    $end    = $client->contract_end_date;
    $isPast = $end ? $end->isPast() : false;
    $isSoon = $end ? (!$isPast && $end->diffInDays(now()) <= 30) : false;
@endphp

<div class="ui-card cs-head">
    <span class="cs-name">{{ $client->company_name }}</span>
    <span class="cs-code">{{ $client->client_code ?: '—' }}</span>
    <span class="badge {{ $statusClass }}">{{ ucfirst($client->status) }}</span>
    <div class="cs-facts">
        <div class="cs-fact"><span>Region</span><strong>{{ $client->region ?: '—' }}</strong></div>
        <div class="cs-fact"><span>City</span><strong>{{ $client->city ?: '—' }}</strong></div>
    </div>
</div>

<div class="cs-layout">
    <div class="cs-col">
        <div class="ui-card">
            <div class="ui-card-header"><h3>Company Information</h3></div>
            <div class="cs-dl">
                <div><div class="ui-label">Company Name</div><div class="v">{{ $client->company_name }}</div></div>
                <div><div class="ui-label">Contact Person</div><div class="v">{{ $client->contact_person ?: '—' }}</div></div>
                <div>
                    <div class="ui-label">Email</div>
                    <div class="v">@if($client->email)<a href="mailto:{{ $client->email }}" class="cs-link">{{ $client->email }}</a>@else — @endif</div>
                </div>
                <div>
                    <div class="ui-label">Phone</div>
                    <div class="v">@if($client->phone)<a href="tel:{{ $client->phone }}" class="cs-link">{{ $client->phone }}</a>@else — @endif</div>
                </div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Address</h3></div>
            <div class="cs-dl" style="grid-template-columns:1fr">
                <div class="v" style="white-space:pre-line">{{ trim(collect([$client->address, $client->city, $client->region])->filter()->join(', ')) ?: '—' }}</div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Contract</h3></div>
            <div class="cs-dl">
                <div><div class="ui-label">Start</div><div class="v">{{ $start?->format('M d, Y') ?: '—' }}</div></div>
                <div>
                    <div class="ui-label">End</div>
                    <div class="v" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                        {{ $end?->format('M d, Y') ?: '—' }}
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

    <div class="cs-col">
        <div class="ui-card">
            <div class="ui-card-header"><h3>Quick Actions</h3></div>
            <div class="cs-actions">
                <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit Client</a>
                <button type="button" class="btn-secondary" onclick="contactClient('{{ $client->email }}')"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-mail"/></svg> Contact</button>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Related</h3></div>
            <div class="cs-rows">
                <div class="cs-row"><span>POS Terminals</span><strong>{{ $client->posTerminals()->count() }}</strong></div>
                <div class="cs-row"><span>Projects</span><strong>{{ $client->projects()->count() }}</strong></div>
                <div class="cs-row"><span>Tickets</span><strong>{{ $client->tickets()->count() }}</strong></div>
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
