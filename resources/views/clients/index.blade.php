{{-- resources/views/clients/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-block-end:32px;padding-block-end:16px;border-bottom:1px solid #e5e7eb;">
        <div>
            <h1 style="margin:0;color:#111827;font-size:28px;font-weight:600;letter-spacing:-0.025em;">Client Management</h1>
            <p style="color:#6b7280;margin:4px 0 0 0;font-size:15px;">Manage your clients and business relationships</p>
        </div>
        <div>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">Add New Client</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-block-end:32px;">
        <div class="metric-card">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="metric-icon" style="background-color:#f3f4f6;">
                    <svg style="width:20px;height:20px;color:#6b7280;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:24px;font-weight:700;color:#111827;line-height:1;">{{ $stats['total_clients'] }}</div>
                    <div style="font-size:13px;color:#6b7280;font-weight:500;">Total Clients</div>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="metric-icon" style="background-color:#ecfdf5;">
                    <svg style="width:20px;height:20px;color:#059669;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:24px;font-weight:700;color:#111827;line-height:1;">{{ $stats['active_clients'] }}</div>
                    <div style="font-size:13px;color:#6b7280;font-weight:500;">Active Clients</div>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="metric-icon" style="background-color:#fef3c7;">
                    <svg style="width:20px;height:20px;color:#d97706;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11 9.586z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:24px;font-weight:700;color:#111827;line-height:1;">{{ $stats['prospects'] }}</div>
                    <div style="font-size:13px;color:#6b7280;font-weight:500;">Prospects</div>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="metric-icon" style="background-color:#f3e8ff;">
                    <svg style="width:20px;height:20px;color:#7c3aed;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h3v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm8 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:24px;font-weight:700;color:#111827;line-height:1;">{{ $stats['under_contract'] }}</div>
                    <div style="font-size:13px;color:#6b7280;font-weight:500;">Under Contract</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-block-end:24px;">
        <form method="GET" style="display:grid;grid-template-columns:1fr auto auto auto auto;gap:12px;align-items:center;">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search clients..."
                   style="padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;">

            <select name="status" style="padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;min-width:120px;">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="prospect" {{ request('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="lost"     {{ request('status') === 'lost'     ? 'selected' : '' }}>Lost</option>
            </select>

            <select name="region" style="padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;min-width:120px;">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                        {{ $region }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-secondary">Filter</button>

            @if(request()->hasAny(['search','status','region']))
            <a href="{{ route('clients.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <!-- Clients Table -->
    <div class="content-card" style="padding:0;">
        @if($clients->count())
        <div class="table-toolbar">
            <div class="toolbar-left">
                <strong style="color:#111827;">Clients</strong>
                <span class="muted">•</span>
                <span class="muted">{{ number_format(method_exists($clients,'total') ? $clients->total() : $clients->count()) }} total</span>
            </div>
            <div class="toolbar-right">
                <a href="{{ route('clients.create') }}" class="btn btn-primary btn-small">+ Add Client</a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="rt-table">
                <thead>
                    <tr>
                        <th style="width:120px;">Code</th>
                        <th>Company</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:190px;">Contact</th>
                        <th style="width:220px;">Email</th>
                        <th style="width:150px;">Phone</th>
                        <th style="width:180px;">Location</th>
                        <th style="width:200px;">Contract</th>
                        <th style="width:200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        @php
                            $end    = $client->contract_end_date;
                            $isPast = $end ? $end->isPast() : false;
                            $isSoon = $end ? (!$isPast && $end->diffInDays(now()) <= 30) : false;
                        @endphp
                        <tr>
                            <td>
                                <div class="code-chip" title="{{ $client->client_code ?: '—' }}">
                                    {{ $client->client_code ?: '—' }}
                                </div>
                            </td>
                            <td>
                                <div class="cell-main">
                                    <div class="company">{{ $client->company_name }}</div>
                                    <div class="subtle">
                                        {{ $client->address ? \Illuminate\Support\Str::limit($client->address, 50) : '—' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($client->status) }}">{{ ucfirst($client->status) }}</span>
                            </td>
                            <td>
                                <div class="cell-main">
                                    <div class="company">{{ $client->contact_person ?: '—' }}</div>
                                </div>
                            </td>
                            <td>
                                @if($client->email)
                                    <a href="mailto:{{ $client->email }}" class="link">{{ $client->email }}</a>
                                @else
                                    <span class="subtle">—</span>
                                @endif
                            </td>
                            <td>
                                @if($client->phone)
                                    <a href="tel:{{ $client->phone }}" class="link">{{ $client->phone }}</a>
                                @else
                                    <span class="subtle">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="subtle">{{ collect([$client->city, $client->region])->filter()->join(', ') ?: '—' }}</div>
                            </td>
                            <td>
                                @if($client->contract_start_date || $client->contract_end_date)
                                    <div class="contract">
                                        <div class="subtle">Start: <strong>{{ $client->contract_start_date?->format('M d, Y') ?: '—' }}</strong></div>
                                        <div class="subtle">End: <strong>{{ $client->contract_end_date?->format('M d, Y') ?: '—' }}</strong>
                                            @if($isPast)
                                                <span class="pill pill-danger">Expired</span>
                                            @elseif($isSoon)
                                                <span class="pill pill-warn">Expiring</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="subtle">—</span>
                                @endif
                            </td>
                            <td>
    <div class="actions">
        <a href="{{ route('clients.show', ['client' => $client->id]) }}" class="btn btn-outline btn-xs">View</a>
        <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn btn-secondary btn-xs">Edit</a>
        <button type="button" onclick="contactClient('{{ $client->email }}')" class="btn btn-outline btn-xs">Contact</button>
    </div>
</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @else
            <div style="padding:48px;text-align:center;color:#6b7280;">
                <svg style="width:48px;height:48px;margin:0 auto 16px;color:#d1d5db;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <h3 style="color:#374151;margin-block-end:8px;">No clients found</h3>
                <p>Start building your client base by adding your first client.</p>
                <a href="{{ route('clients.create') }}" class="btn btn-primary" style="margin-block-start:16px;">Add First Client</a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(method_exists($clients,'hasPages') && $clients->hasPages())
        <div style="margin-block-start:32px;display:flex;justify-content:center;">
            {{ $clients->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<style>
/* Cards */
.metric-card{background:white;padding:20px;border-radius:8px;border:1px solid #e5e7eb;transition:all .2s ease}
.metric-card:hover{border-color:#d1d5db;box-shadow:0 1px 3px rgba(0,0,0,.1)}
.metric-icon{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center}
.content-card{background:white;padding:20px;border-radius:8px;border:1px solid #e5e7eb}

/* Buttons */
.btn{padding:10px 16px;border:1px solid #d1d5db;border-radius:6px;background:white;color:#374151;text-decoration:none;cursor:pointer;font-weight:500;font-size:14px;transition:all .2s ease;display:inline-block;line-height:1}
.btn:hover{border-color:#9ca3af;background:#f9fafb}
.btn-primary{background:#4f46e5;color:white;border-color:#4f46e5}
.btn-primary:hover{background:#4338ca;border-color:#4338ca;color:white}
.btn-secondary{background:#f3f4f6;color:#374151;border-color:#d1d5db}
.btn-secondary:hover{background:#e5e7eb;border-color:#9ca3af}
.btn-outline{background:transparent;color:#374151;border-color:#d1d5db}
.btn-outline:hover{background:#f9fafb;color:#111827;border-color:#9ca3af}
.btn-small{padding:6px 10px;font-size:13px}

/* Extra small button size for table actions */
.btn-xs{padding:5px 10px;font-size:12px;line-height:1.2;border-radius:5px}

/* Table */
.table-toolbar{display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid #e5e7eb}
.table-toolbar .muted{color:#9ca3af;font-size:12px}

.table-wrap{width:100%;overflow:auto;border-radius:8px}
.rt-table{width:100%;border-collapse:separate;border-spacing:0}
.rt-table thead th{position:sticky;top:0;background:#f9fafb;color:#374151;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e5e7eb;padding:12px 14px;text-align:left;z-index:1}
.rt-table tbody td{border-bottom:1px solid #f3f4f6;padding:12px 14px;vertical-align:top;font-size:14px;color:#111827}
.rt-table tbody tr:hover{background:#fafafa}
.rt-table tbody tr:nth-child(even){background:#fcfcfd}

/* Cells */
.cell-main .company{font-weight:600;color:#111827}
.subtle{color:#6b7280;font-size:12px}
.link{color:#4f46e5;text-decoration:none}
.link:hover{text-decoration:underline}
.code-chip{display:inline-flex;align-items:center;justify-content:center;min-width:54px;padding:4px 8px;border-radius:6px;background:#f3f4f6;color:#374151;font-weight:600;font-size:12px}

.contract{display:flex;flex-direction:column;gap:2px}
.pill{margin-left:8px;padding:2px 6px;border-radius:999px;font-size:11px;font-weight:700;vertical-align:middle;border:1px solid transparent}
.pill-danger{background:#fee2e2;color:#991b1b;border-color:#fecaca}
.pill-warn{background:#fef3c7;color:#92400e;border-color:#fde68a}

/* Status badges */
.status-badge{padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em}
.status-active{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
.status-prospect{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
.status-inactive{background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb}
.status-lost{background:#fecaca;color:#991b1b;border:1px solid #fca5a5}

/* Actions column */
.actions{display:flex;gap:6px;flex-wrap:wrap}

/* Inputs focus */
input:focus,select:focus{outline:none;border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.1)}

/* Responsive: hide less-critical columns on small screens */
@media (max-width: 900px){
    .rt-table thead th:nth-child(5),
    .rt-table tbody td:nth-child(5), /* Email */
    .rt-table thead th:nth-child(7),
    .rt-table tbody td:nth-child(7), /* Location */
    .rt-table thead th:nth-child(8),
    .rt-table tbody td:nth-child(8)  /* Contract */ { display:none; }
}
@media (max-width: 640px){
    .rt-table thead th:nth-child(6),
    .rt-table tbody td:nth-child(6)  /* Phone */ { display:none; }
}
</style>

<script>
function contactClient(email){
    if(email){ window.location.href='mailto:'+email; }
    else{ alert('No email address available for this client'); }
}
</script>
@endsection
