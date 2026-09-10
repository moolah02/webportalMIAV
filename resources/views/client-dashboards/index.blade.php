{{-- resources/views/client-dashboards/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Client Dashboards')

@section('header-actions')
<a href="{{ route('clients.create') }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add New Client</a>
@endsection

@push('styles')
<style>
.cd-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
@media (max-width: 900px) { .cd-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.cd-muted { color: var(--mv-muted); font-size: 12.5px; }
.cd-company { font-weight: 500; color: var(--mv-accent-ink); font-size: 13.5px; text-decoration: none; }
.cd-company:hover { text-decoration: underline; }
.cd-sub { font-size: 12px; color: var(--mv-muted); margin-top: 1px; }
.cd-link { color: var(--mv-accent-ink); text-decoration: none; }
.cd-link:hover { text-decoration: underline; }
.cd-num { text-align: right; font-variant-numeric: tabular-nums; font-weight: 500; color: var(--mv-ink); }
.cd-contract { font-size: 12px; color: var(--mv-muted); line-height: 1.55; white-space: nowrap; }
.cd-contract b { font-weight: 500; color: var(--mv-ink-2); font-variant-numeric: tabular-nums; }
.cd-contract .badge { margin-left: 4px; font-size: 11px; padding: 1px 6px; }
</style>
@endpush

@section('content')
<div class="cd-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['total_clients'] }}</div>
            <div class="stat-label">Total Clients</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green"><svg class="mv-i" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['active_clients'] }}</div>
            <div class="stat-label">Active Clients</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-target"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['prospects'] }}</div>
            <div class="stat-label">Prospects</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-file-check"/></svg></div>
        <div>
            <div class="stat-number">{{ $stats['under_contract'] }}</div>
            <div class="stat-label">Under Contract</div>
        </div>
    </div>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group" style="flex:1;min-width:200px">
        <label class="ui-label">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search clients..." class="ui-input" style="width:100%">
    </div>
    <div class="filter-group">
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="prospect" {{ request('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="lost"     {{ request('status') === 'lost'     ? 'selected' : '' }}>Lost</option>
        </select>
    </div>
    <div class="filter-group">
        <label class="ui-label">Region</label>
        <select name="region" class="ui-select">
            <option value="">All Regions</option>
            @foreach($regions as $region)
                @php $regionName = is_object($region) ? $region->name : $region; @endphp
                <option value="{{ $regionName }}" {{ request('region') == $regionName ? 'selected' : '' }}>{{ $regionName }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','status','region']))
        <a href="{{ route('client-dashboards.index') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <h3>Clients</h3>
        <span class="cd-muted">{{ number_format(method_exists($clients,'total') ? $clients->total() : $clients->count()) }} total</span>
    </div>

    @if($clients->count())
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th style="text-align:right">Terminals</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Contract</th>
                    <th style="width:150px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    @php
                        $end    = $client->contract_end_date;
                        $isPast = $end ? $end->isPast() : false;
                        $isSoon = $end ? (!$isPast && $end->diffInDays(now()) <= 30) : false;
                        $sc     = ['active' => 'badge-green', 'prospect' => 'badge-blue', 'inactive' => 'badge-gray', 'lost' => 'badge-red'];
                    @endphp
                    <tr>
                        <td><span class="id-chip" style="padding:2px 7px;font-size:12px">{{ $client->client_code ?: '—' }}</span></td>
                        <td>
                            <a href="{{ route('client-dashboards.show', $client) }}" class="cd-company">{{ $client->company_name }}</a>
                            <div class="cd-sub">{{ $client->address ? \Illuminate\Support\Str::limit($client->address, 50) : '—' }}</div>
                        </td>
                        <td><span class="badge {{ $sc[strtolower($client->status)] ?? 'badge-gray' }}">{{ ucfirst($client->status) }}</span></td>
                        <td class="cd-num">{{ number_format($client->pos_terminals_count ?? 0) }}</td>
                        <td>{{ $client->contact_person ?: '—' }}</td>
                        <td>
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}" class="cd-link">{{ $client->email }}</a>
                            @else
                                <span class="cd-muted">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}" class="cd-link">{{ $client->phone }}</a>
                            @else
                                <span class="cd-muted">—</span>
                            @endif
                        </td>
                        <td>{{ collect([$client->city, $client->region])->filter()->join(', ') ?: '—' }}</td>
                        <td>
                            @if($client->contract_start_date || $client->contract_end_date)
                                <div class="cd-contract">
                                    <div>Start <b>{{ $client->contract_start_date?->format('M d, Y') ?: '—' }}</b></div>
                                    <div>End <b>{{ $client->contract_end_date?->format('M d, Y') ?: '—' }}</b>
                                        @if($isPast)
                                            <span class="badge badge-red">Expired</span>
                                        @elseif($isSoon)
                                            <span class="badge badge-yellow">Expiring</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="cd-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('client-dashboards.show', $client) }}" class="action-btn" title="Open Dashboard"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-chart"/></svg></a>
                                <a href="{{ route('clients.show', ['client' => $client->id]) }}" class="action-btn" title="View Details"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a>
                                <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="action-btn" title="Edit Client"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg></a>
                                <button type="button" onclick="contactClient('{{ $client->email }}')" class="action-btn" title="Send Email"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-mail"/></svg></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg></div>
        <p class="empty-state-msg">No clients found. Start building your client base by adding your first client.</p>
        <a href="{{ route('clients.create') }}" class="btn-primary" style="margin-top:12px">Add First Client</a>
    </div>
    @endif
</div>

@if(method_exists($clients,'hasPages') && $clients->hasPages())
<div class="mt-5 flex justify-center">
    {{ $clients->appends(request()->query())->links() }}
</div>
@endif

<script>
function contactClient(email) {
    if (email) {
        window.location.href = 'mailto:' + email;
    } else {
        alert('No email address available for this client');
    }
}
</script>
@endsection
