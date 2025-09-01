{{-- resources/views/clients/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-block-end:24px;">
        <div>
            <h2 style="margin:0;color:#111827;font-weight:700;letter-spacing:-.02em;">
                {{ $client->company_name }}
            </h2>
            <p style="margin:6px 0 0;color:#6b7280;">Client details & history</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('clients.index') }}" class="btn btn-outline">← Back to Clients</a>
            <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn btn-secondary">Edit</a>
        </div>
    </div>

    <!-- Summary -->
    <div class="content-card" style="margin-block-end:16px;">
        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;">
            <div>
                <div class="label">Client Code</div>
                <div class="value">{{ $client->client_code ?: '—' }}</div>
            </div>
            <div>
                <div class="label">Status</div>
                <span class="status-badge status-{{ strtolower($client->status) }}">{{ ucfirst($client->status) }}</span>
            </div>
            <div>
                <div class="label">Region</div>
                <div class="value">{{ $client->region ?: '—' }}</div>
            </div>
            <div>
                <div class="label">City</div>
                <div class="value">{{ $client->city ?: '—' }}</div>
            </div>
        </div>
    </div>

    <!-- Two-column layout -->
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">
        <!-- Left -->
        <div style="display:flex;flex-direction:column;gap:16px;">

            <div class="content-card">
                <h4 class="card-title">🏢 Company Information</h4>
                <div class="grid-2">
                    <div>
                        <div class="label">Company Name</div>
                        <div class="value">{{ $client->company_name }}</div>
                    </div>
                    <div>
                        <div class="label">Contact Person</div>
                        <div class="value">{{ $client->contact_person ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="label">Email</div>
                        @if($client->email)
                            <a href="mailto:{{ $client->email }}" class="link">{{ $client->email }}</a>
                        @else
                            <div class="value">—</div>
                        @endif
                    </div>
                    <div>
                        <div class="label">Phone</div>
                        @if($client->phone)
                            <a href="tel:{{ $client->phone }}" class="link">{{ $client->phone }}</a>
                        @else
                            <div class="value">—</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-card">
                <h4 class="card-title">📍 Address</h4>
                <div class="value" style="white-space:pre-line;">
                    {{ trim(collect([$client->address, $client->city, $client->region])->filter()->join(', ')) ?: '—' }}
                </div>
            </div>

            <div class="content-card">
                @php
                  $start = $client->contract_start_date;
                  $end   = $client->contract_end_date;
                  $isPast = $end ? $end->isPast() : false;
                  $isSoon = $end ? (!$isPast && $end->diffInDays(now()) <= 30) : false;
                @endphp
                <h4 class="card-title">📋 Contract</h4>
                <div class="grid-2">
                    <div>
                        <div class="label">Start</div>
                        <div class="value">{{ $start?->format('M d, Y') ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="label">End</div>
                        <div class="value">
                            {{ $end?->format('M d, Y') ?: '—' }}
                            @if($isPast)
                                <span class="pill pill-danger">Expired</span>
                            @elseif($isSoon)
                                <span class="pill pill-warn">Expiring</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="content-card">
                <h4 class="card-title">⚡ Quick Actions</h4>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn btn-secondary btn-small">Edit Client</a>
                    <button type="button" class="btn btn-outline btn-small"
                            onclick="contactClient('{{ $client->email }}')">Contact</button>
                </div>
            </div>

            <div class="content-card">
                <h4 class="card-title">📈 Related</h4>
                <ul class="related">
                    <li><span class="label">POS Terminals:</span> <span class="value">{{ $client->posTerminals()->count() }}</span></li>
                    <li><span class="label">Projects:</span> <span class="value">{{ $client->projects()->count() }}</span></li>
                    <li><span class="label">Tickets:</span> <span class="value">{{ $client->tickets()->count() }}</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.content-card{background:#fff;padding:20px;border-radius:10px;border:1px solid #e5e7eb}
.card-title{margin:0 0 12px;color:#111827;font-weight:700}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.label{font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px}
.value{color:#111827}
.link{color:#4f46e5;text-decoration:none}
.link:hover{text-decoration:underline}

.btn{padding:10px 16px;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#374151;text-decoration:none;cursor:pointer;font-weight:500;font-size:14px;transition:all .2s ease;display:inline-block;line-height:1}
.btn:hover{border-color:#9ca3af;background:#f9fafb}
.btn-secondary{background:#f3f4f6;color:#374151;border-color:#d1d5db}
.btn-secondary:hover{background:#e5e7eb;border-color:#9ca3af}
.btn-outline{background:transparent;color:#374151;border-color:#d1d5db}
.btn-outline:hover{background:#f9fafb;color:#111827;border-color:#9ca3af}
.btn-small{padding:6px 10px;font-size:13px}

.status-badge{padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em}
.status-active{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
.status-prospect{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
.status-inactive{background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb}
.status-lost{background:#fecaca;color:#991b1b;border:1px solid #fca5a5}

.pill{margin-left:8px;padding:2px 6px;border-radius:999px;font-size:11px;font-weight:700;border:1px solid transparent}
.pill-danger{background:#fee2e2;color:#991b1b;border-color:#fecaca}
.pill-warn{background:#fef3c7;color:#92400e;border-color:#fde68a}

.related{list-style:none;padding:0;margin:0}
.related li{display:flex;justify-content:space-between;padding:8px 0;border-top:1px dashed #eef1f4}
.related li:first-child{border-top:none}
</style>

<script>
function contactClient(email){
    if(email){ window.location.href='mailto:'+email; }
    else{ alert('No email address available for this client'); }
}
</script>
@endsection
