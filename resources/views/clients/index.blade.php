{{-- resources/views/clients/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Client Management')

@section('content')
{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title">Client Management</h1>
        <p class="page-subtitle">Manage your clients and business relationships</p>
    </div>
    <a href="{{ route('clients.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add New Client
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card border-l-4 border-gray-400">
        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
        </div>
        <div>
            <div class="stat-number">{{ $stats['total_clients'] }}</div>
            <div class="stat-label">Total Clients</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-green-500">
        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        </div>
        <div>
            <div class="stat-number text-green-600">{{ $stats['active_clients'] }}</div>
            <div class="stat-label">Active Clients</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-yellow-400">
        <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414z" clip-rule="evenodd"/></svg>
        </div>
        <div>
            <div class="stat-number text-yellow-600">{{ $stats['prospects'] }}</div>
            <div class="stat-label">Prospects</div>
        </div>
    </div>
    <div class="stat-card border-l-4 border-purple-500">
        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h3v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm8 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
        </div>
        <div>
            <div class="stat-number text-purple-600">{{ $stats['under_contract'] }}</div>
            <div class="stat-label">Under Contract</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div>
        <label class="ui-label">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search clients..." class="ui-input w-48">
    </div>
    <div>
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select w-36">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="prospect" {{ request('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="lost"     {{ request('status') === 'lost'     ? 'selected' : '' }}>Lost</option>
        </select>
    </div>
    <div>
        <label class="ui-label">Region</label>
        <select name="region" class="ui-select w-36">
            <option value="">All Regions</option>
            @foreach($regions as $region)
                <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','status','region']))
            <a href="{{ route('clients.index') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

{{-- Table Card --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-gray-800">Clients</span>
            <span class="text-xs text-gray-400">•</span>
            <span class="text-xs text-gray-500">{{ number_format(method_exists($clients,'total') ? $clients->total() : $clients->count()) }} total</span>
        </div>
        <a href="{{ route('clients.create') }}" class="btn-primary btn-sm">+ Add Client</a>
    </div>

    @if($clients->count())
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Contract</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    @php
                        $end    = $client->contract_end_date;
                        $isPast = $end ? $end->isPast() : false;
                        $isSoon = $end ? (!$isPast && $end->diffInDays(now()) <= 30) : false;
                        $sc     = ['active'=>'badge badge-green','prospect'=>'badge badge-yellow','inactive'=>'badge badge-gray','lost'=>'badge badge-red'];
                    @endphp
                    <tr>
                        <td>
                            <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-xs font-semibold text-gray-700">{{ $client->client_code ?: '—' }}</span>
                        </td>
                        <td>
                            <div class="text-sm font-semibold text-gray-900">{{ $client->company_name }}</div>
                            <div class="text-xs text-gray-500">{{ $client->address ? \Illuminate\Support\Str::limit($client->address, 50) : '—' }}</div>
                        </td>
                        <td>
                            <span class="{{ $sc[strtolower($client->status)] ?? 'badge badge-gray' }}">{{ ucfirst($client->status) }}</span>
                        </td>
                        <td class="text-sm text-gray-700">{{ $client->contact_person ?: '—' }}</td>
                        <td>
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}" class="text-sm text-[#1a3a5c] hover:underline">{{ $client->email }}</a>
                            @else <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td>
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}" class="text-sm text-[#1a3a5c] hover:underline">{{ $client->phone }}</a>
                            @else <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-500">{{ collect([$client->city, $client->region])->filter()->join(', ') ?: '—' }}</td>
                        <td>
                            @if($client->contract_start_date || $client->contract_end_date)
                                <div class="text-xs text-gray-500">Start: <span class="font-medium text-gray-700">{{ $client->contract_start_date?->format('M d, Y') ?: '—' }}</span></div>
                                <div class="text-xs text-gray-500 mt-0.5">End: <span class="font-medium text-gray-700">{{ $client->contract_end_date?->format('M d, Y') ?: '—' }}</span>
                                    @if($isPast) <span class="badge badge-red ml-1">Expired</span>
                                    @elseif($isSoon) <span class="badge badge-yellow ml-1">Expiring</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('clients.show', ['client' => $client->id]) }}" class="btn-secondary btn-sm">View</a>
                                <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn-secondary btn-sm">Edit</a>
                                <button type="button" onclick="contactClient('{{ $client->email }}')" class="btn-secondary btn-sm">Contact</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
        </div>
        <p class="empty-state-msg">No clients found. <a href="{{ route('clients.create') }}" class="text-[#1a3a5c] underline">Add your first client</a>.</p>
    </div>
    @endif
</div>

{{-- Pagination --}}
@if(method_exists($clients,'hasPages') && $clients->hasPages())
<div class="mt-5 flex justify-center">
    {{ $clients->appends(request()->query())->links() }}
</div>
@endif
@endsection