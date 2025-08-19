@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 32px; padding-block-end: 16px; border-bottom: 1px solid #e5e7eb;">
        <div>
            <h1 style="margin: 0; color: #111827; font-size: 28px; font-weight: 600; letter-spacing: -0.025em;">Client Management</h1>
            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 15px;">Manage your clients and business relationships</p>
        </div>
        <div>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">Add New Client</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-block-end: 32px;">
        <div class="metric-card">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="metric-icon" style="background-color: #f3f4f6;">
                    <svg style="width: 20px; height: 20px; color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #111827; line-height: 1;">{{ $stats['total_clients'] }}</div>
                    <div style="font-size: 13px; color: #6b7280; font-weight: 500;">Total Clients</div>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="metric-icon" style="background-color: #ecfdf5;">
                    <svg style="width: 20px; height: 20px; color: #059669;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #111827; line-height: 1;">{{ $stats['active_clients'] }}</div>
                    <div style="font-size: 13px; color: #6b7280; font-weight: 500;">Active Clients</div>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="metric-icon" style="background-color: #fef3c7;">
                    <svg style="width: 20px; height: 20px; color: #d97706;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11 9.586z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #111827; line-height: 1;">{{ $stats['prospects'] }}</div>
                    <div style="font-size: 13px; color: #6b7280; font-weight: 500;">Prospects</div>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="metric-icon" style="background-color: #f3e8ff;">
                    <svg style="width: 20px; height: 20px; color: #7c3aed;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h3v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm8 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #111827; line-height: 1;">{{ $stats['under_contract'] }}</div>
                    <div style="font-size: 13px; color: #6b7280; font-weight: 500;">Under Contract</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-block-end: 24px;">
        <form method="GET" style="display: grid; grid-template-columns: 1fr auto auto auto auto; gap: 12px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search clients..." 
                   style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            
            <select name="status" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; min-width: 120px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="prospect" {{ request('status') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
            </select>
            
            <select name="region" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; min-width: 120px;">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                        {{ $region }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="btn btn-secondary">Filter</button>
            
            @if(request()->hasAny(['search', 'status', 'region']))
            <a href="{{ route('clients.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <!-- Clients Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
        @forelse($clients as $client)
        <div class="client-card">
            <!-- Client Header -->
            <div style="display: flex; justify-content: space-between; align-items: start; margin-block-end: 16px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="client-avatar">
                        {{ $client->client_code ?? substr($client->company_name, 0, 2) }}
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <h4 style="margin: 0; color: #111827; font-size: 15px; font-weight: 600; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $client->company_name }}</h4>
                        <div style="font-size: 12px; color: #9ca3af; font-weight: 500;">{{ $client->client_code }}</div>
                    </div>
                </div>
                <span class="status-badge status-{{ strtolower($client->status) }}">
                    {{ ucfirst($client->status) }}
                </span>
            </div>

            <!-- Contact Person -->
            <div style="margin-block-end: 16px;">
                <div style="font-weight: 600; margin-block-end: 8px; color: #374151; font-size: 14px;">{{ $client->contact_person }}</div>
                
                @if($client->email)
                <div style="display: flex; align-items: center; gap: 6px; margin-block-end: 4px;">
                    <svg style="width: 14px; height: 14px; color: #9ca3af;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    <a href="mailto:{{ $client->email }}" style="color: #4f46e5; text-decoration: none; font-size: 13px; font-weight: 500;">{{ $client->email }}</a>
                </div>
                @endif
                
                @if($client->phone)
                <div style="display: flex; align-items: center; gap: 6px; margin-block-end: 4px;">
                    <svg style="width: 14px; height: 14px; color: #9ca3af;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                    <a href="tel:{{ $client->phone }}" style="color: #374151; text-decoration: none; font-size: 13px;">{{ $client->phone }}</a>
                </div>
                @endif

                @if($client->city || $client->region)
                <div style="display: flex; align-items: center; gap: 6px;">
                    <svg style="width: 14px; height: 14px; color: #9ca3af;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <span style="color: #6b7280; font-size: 13px;">{{ collect([$client->city, $client->region])->filter()->join(', ') }}</span>
                </div>
                @endif
            </div>

            <!-- Contract Info -->
            @if($client->contract_start_date || $client->contract_end_date)
            <div class="contract-info">
                <div style="font-size: 11px; color: #9ca3af; text-transform: uppercase; margin-block-end: 6px; font-weight: 600; letter-spacing: 0.05em;">Contract Period</div>
                @if($client->contract_start_date)
                <div style="font-size: 13px; margin-block-end: 2px;">
                    <span style="color: #6b7280;">Start:</span> <strong style="color: #374151;">{{ \Carbon\Carbon::parse($client->contract_start_date)->format('M d, Y') }}</strong>
                </div>
                @endif
                @if($client->contract_end_date)
                <div style="font-size: 13px; display: flex; align-items: center; gap: 6px;">
                    <span style="color: #6b7280;">End:</span> <strong style="color: #374151;">{{ \Carbon\Carbon::parse($client->contract_end_date)->format('M d, Y') }}</strong>
                    @if(\Carbon\Carbon::parse($client->contract_end_date)->isPast())
                        <span style="color: #dc2626; font-size: 12px; font-weight: 600;">Expired</span>
                    @elseif(\Carbon\Carbon::parse($client->contract_end_date)->diffInDays() <= 30)
                        <span style="color: #d97706; font-size: 12px; font-weight: 600;">Expiring</span>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Actions -->
            <div style="display: flex; gap: 6px; margin-block-start: 16px;">
                <a href="{{ route('clients.show', $client) }}" class="btn-small btn-outline" style="flex: 1; text-align: center; font-size: 12px;">
                    View
                </a>
                <a href="{{ route('clients.edit', $client) }}" class="btn-small btn-secondary" style="font-size: 12px;">
                    Edit
                </a>
                <button onclick="contactClient('{{ $client->email }}')" class="btn-small btn-primary" style="font-size: 12px;">
                    Contact
                </button>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: #6b7280;">
            <svg style="width: 48px; height: 48px; margin: 0 auto 16px; color: #d1d5db;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            <h3 style="color: #374151; margin-block-end: 8px;">No clients found</h3>
            <p>Start building your client base by adding your first client.</p>
            <a href="{{ route('clients.create') }}" class="btn btn-primary" style="margin-block-start: 16px;">Add First Client</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div style="margin-block-start: 32px; display: flex; justify-content: center;">
        {{ $clients->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.metric-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.metric-card:hover {
    border-color: #d1d5db;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.metric-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.client-card {
    background: white;
    padding: 18px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.client-card:hover {
    border-color: #d1d5db;
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.05);
}

.client-avatar {
    width: 36px;
    height: 36px;
    border-radius: 6px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    flex-shrink: 0;
}

.contract-info {
    background: #f9fafb;
    padding: 12px;
    border-radius: 6px;
    margin-block-end: 16px;
    border: 1px solid #f3f4f6;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-active { 
    background: #dcfce7; 
    color: #166534; 
    border: 1px solid #bbf7d0;
}
.status-prospect { 
    background: #fef3c7; 
    color: #92400e; 
    border: 1px solid #fde68a;
}
.status-inactive { 
    background: #f3f4f6; 
    color: #6b7280; 
    border: 1px solid #e5e7eb;
}
.status-lost { 
    background: #fecaca; 
    color: #991b1b; 
    border: 1px solid #fca5a5;
}

.btn {
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    color: #374151;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
    display: inline-block;
    line-height: 1;
}

.btn:hover {
    border-color: #9ca3af;
    background: #f9fafb;
}

.btn-primary {
    background: #4f46e5;
    color: white;
    border-color: #4f46e5;
}

.btn-primary:hover {
    background: #4338ca;
    border-color: #4338ca;
    color: white;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border-color: #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
}

.btn-outline {
    background: transparent;
    color: #6b7280;
    border-color: #d1d5db;
}

.btn-outline:hover {
    background: #f9fafb;
    color: #374151;
    border-color: #9ca3af;
}

.btn-small {
    padding: 6px 10px;
    font-size: 13px;
}

input:focus, select:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
</style>

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