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
                        <th style="width:240px;">Actions</th>
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
                                    <div class="action-group">
                                        <a href="{{ route('clients.show', ['client' => $client->id]) }}"
                                           class="action-btn action-view"
                                           title="View Details">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('clients.edit', ['client' => $client->id]) }}"
                                           class="action-btn action-edit"
                                           title="Edit Client">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button type="button"
                                                onclick="contactClient('{{ $client->email }}')"
                                                class="action-btn action-contact"
                                                title="Send Email">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="action-menu">
                                        <button type="button" class="action-menu-btn" onclick="toggleActionMenu(this)">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                            </svg>
                                        </button>
                                        <div class="action-menu-dropdown">
                                            <a href="{{ route('clients.show', ['client' => $client->id]) }}" class="action-menu-item">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                View Details
                                            </a>
                                            <a href="#" class="action-menu-item">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                Duplicate
                                            </a>
                                            <div class="action-menu-divider"></div>
                                            <a href="#" class="action-menu-item text-red-600">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </a>
                                        </div>
                                    </div>
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

/* Enhanced Actions Column */
.actions{display:flex;align-items:center;justify-content:space-between;gap:8px;position:relative}

.action-group{display:flex;align-items:center;gap:4px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:2px}

.action-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:none;background:transparent;color:#64748b;cursor:pointer;transition:all 0.15s ease;text-decoration:none;position:relative}

.action-btn:hover{background:white;color:#374151;box-shadow:0 1px 2px rgba(0,0,0,0.05)}

.action-view:hover{color:#059669;background:#f0fdf4}
.action-edit:hover{color:#dc2626;background:#fef2f2}
.action-contact:hover{color:#2563eb;background:#eff6ff}

/* Action Menu */
.action-menu{position:relative}

.action-menu-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid #e2e8f0;background:white;color:#64748b;cursor:pointer;transition:all 0.15s ease}

.action-menu-btn:hover{background:#f8fafc;color:#374151;border-color:#cbd5e1}

.action-menu-dropdown{position:absolute;top:100%;right:0;z-index:50;min-width:180px;background:white;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);opacity:0;visibility:hidden;transform:translateY(-4px);transition:all 0.15s ease;margin-top:4px}

.action-menu.active .action-menu-dropdown{opacity:1;visibility:visible;transform:translateY(0)}

.action-menu-item{display:flex;align-items:center;gap:8px;padding:10px 12px;color:#374151;text-decoration:none;font-size:14px;font-weight:500;transition:all 0.15s ease}

.action-menu-item:hover{background:#f8fafc;color:#111827}

.action-menu-item.text-red-600{color:#dc2626}
.action-menu-item.text-red-600:hover{background:#fef2f2;color:#b91c1c}

.action-menu-divider{height:1px;background:#e2e8f0;margin:4px 0}

/* Inputs focus */
input:focus,select:focus{outline:none;border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.1)}

/* Responsive adjustments */
@media (max-width: 1200px){
    .action-group{gap:2px;padding:1px}
    .action-btn{width:28px;height:28px}
}

@media (max-width: 900px){
    .rt-table thead th:nth-child(5),
    .rt-table tbody td:nth-child(5), /* Email */
    .rt-table thead th:nth-child(7),
    .rt-table tbody td:nth-child(7), /* Location */
    .rt-table thead th:nth-child(8),
    .rt-table tbody td:nth-child(8)  /* Contract */ { display:none; }

    .actions{justify-content:center}
    .action-group{flex-wrap:wrap;max-width:none}
}

@media (max-width: 640px){
    .rt-table thead th:nth-child(6),
    .rt-table tbody td:nth-child(6)  /* Phone */ { display:none; }

    .action-group{display:none}
    .action-menu{flex:1}
    .action-menu-btn{width:100%;border-radius:6px}
}
</style>

<script>
function contactClient(email){
    if(email){
        window.location.href='mailto:'+email;
    } else{
        alert('No email address available for this client');
    }
}

function toggleActionMenu(button) {
    // Close all other open menus
    document.querySelectorAll('.action-menu.active').forEach(menu => {
        if (!menu.contains(button)) {
            menu.classList.remove('active');
        }
    });

    // Toggle current menu
    const menu = button.closest('.action-menu');
    menu.classList.toggle('active');
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.action-menu')) {
        document.querySelectorAll('.action-menu.active').forEach(menu => {
            menu.classList.remove('active');
        });
    }
});

// Close menus on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.action-menu.active').forEach(menu => {
            menu.classList.remove('active');
        });
    }
});
</script>
@endsection
