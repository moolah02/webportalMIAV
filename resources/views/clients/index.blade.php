@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">👥 Client Management</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage your clients and business relationships</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('clients.create') }}" class="btn btn-primary">+ Add New Client</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">👥</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['total_clients'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Clients</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">✅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['active_clients'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Active Clients</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">🎯</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['prospects'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Prospects</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">📋</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['under_contract'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Under Contract</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search clients..." 
                   style="flex: 1; min-width: 250px; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
            
            <select name="status" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="prospect" {{ request('status') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
            </select>
            
            <select name="region" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                        {{ $region }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="btn">Filter</button>
            
            @if(request()->hasAny(['search', 'status', 'region']))
            <a href="{{ route('clients.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Clients Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
        @forelse($clients as $client)
        <div class="client-card">
            <!-- Client Header -->
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold;">
                        {{ $client->client_code ?? substr($client->company_name, 0, 2) }}
                    </div>
                    <div>
                        <h4 style="margin: 0; color: #333;">{{ $client->company_name }}</h4>
                        <div style="font-size: 12px; color: #666;">{{ $client->client_code }}</div>
                    </div>
                </div>
                <span class="status-badge status-{{ strtolower($client->status) }}">
                    {{ ucfirst($client->status) }}
                </span>
            </div>

            <!-- Contact Person -->
            <div style="margin-bottom: 15px;">
                <div style="font-weight: 500; margin-bottom: 5px;">{{ $client->contact_person }}</div>
                
                @if($client->email)
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                    <span style="color: #666;">📧</span>
                    <a href="mailto:{{ $client->email }}" style="color: #2196f3; text-decoration: none;">{{ $client->email }}</a>
                </div>
                @endif
                
                @if($client->phone)
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                    <span style="color: #666;">📞</span>
                    <a href="tel:{{ $client->phone }}" style="color: #333;">{{ $client->phone }}</a>
                </div>
                @endif

                @if($client->city || $client->region)
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                    <span style="color: #666;">📍</span>
                    <span style="color: #666;">{{ collect([$client->city, $client->region])->filter()->join(', ') }}</span>
                </div>
                @endif
            </div>

            <!-- Contract Info -->
            @if($client->contract_start_date || $client->contract_end_date)
            <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
                <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px;">Contract Period</div>
                @if($client->contract_start_date)
                <div style="font-size: 14px;">
                    <strong>Start:</strong> {{ \Carbon\Carbon::parse($client->contract_start_date)->format('M d, Y') }}
                </div>
                @endif
                @if($client->contract_end_date)
                <div style="font-size: 14px;">
                    <strong>End:</strong> {{ \Carbon\Carbon::parse($client->contract_end_date)->format('M d, Y') }}
                    @if(\Carbon\Carbon::parse($client->contract_end_date)->isPast())
                        <span style="color: #f44336; margin-left: 5px;">⚠️ Expired</span>
                    @elseif(\Carbon\Carbon::parse($client->contract_end_date)->diffInDays() <= 30)
                        <span style="color: #ff9800; margin-left: 5px;">⚠️ Expiring Soon</span>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Actions -->
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('clients.show', $client) }}" class="btn-small" style="flex: 1; text-align: center;">
                    View Details
                </a>
                <a href="{{ route('clients.edit', $client) }}" class="btn-small">
                    Edit
                </a>
                <button onclick="contactClient('{{ $client->email }}')" class="btn-small" style="background: #4caf50; color: white; border-color: #4caf50;">
                    Contact
                </button>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
            <div style="font-size: 64px; margin-bottom: 20px;">👥</div>
            <h3>No clients yet</h3>
            <p>Start building your client base by adding your first client.</p>
            <a href="{{ route('clients.create') }}" class="btn btn-primary" style="margin-top: 15px;">Add First Client</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $clients->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.client-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.client-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-prospect { background: #fff3e0; color: #f57c00; }
.status-inactive { background: #f5f5f5; color: #666; }
.status-lost { background: #ffebee; color: #d32f2f; }

.btn {
    padding: 8px 16px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
}

.btn-primary {
    background: #2196f3;
    color: white;
    border-color: #2196f3;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}

.btn-small {
    padding: 6px 12px;
    font-size: 14px;
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