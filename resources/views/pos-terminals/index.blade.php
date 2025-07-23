@extends('layouts.app')

@section('content')
<div>
    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <div class="metric-card">
            <div class="metric-number">{{ $stats['total_terminals'] }}</div>
            <div class="metric-label">Total Terminals</div>
        </div>
        <div class="metric-card">
            <div class="metric-number">{{ $stats['active_terminals'] }}</div>
            <div class="metric-label">Active Terminals</div>
        </div>
        <div class="metric-card alert">
            <div class="metric-number">{{ $stats['faulty_terminals'] }}</div>
            <div class="metric-label">Need Attention</div>
        </div>
        <div class="metric-card">
            <div class="metric-number">{{ $stats['offline_terminals'] }}</div>
            <div class="metric-label">Offline</div>
        </div>
    </div>

    <!-- POS Terminal Tabs -->
    <div class="pos-tabs" style="display: flex; margin-bottom: 20px; border-bottom: 2px solid #eee;">
        <button class="tab-btn active" onclick="switchTab('overview')" style="padding: 12px 24px; background: none; border: none; cursor: pointer; font-weight: 500; color: #2196f3; border-bottom: 2px solid #2196f3;">
            Terminal Overview
        </button>
        <button class="tab-btn" onclick="switchTab('import')" style="padding: 12px 24px; background: none; border: none; cursor: pointer; font-weight: 500; color: #666; border-bottom: 2px solid transparent;">
            Import Bank Data
        </button>
        <button class="tab-btn" onclick="switchTab('field')" style="padding: 12px 24px; background: none; border: none; cursor: pointer; font-weight: 500; color: #666; border-bottom: 2px solid transparent;">
            Field Updates
        </button>
    </div>

    <!-- Terminal Overview Tab -->
    <div id="overview-tab" class="tab-content" style="display: block;">
        <div class="content-card">
            <!-- Filters and Search -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" placeholder="Search terminals..." style="padding: 8px 12px; border: 2px solid #ddd; border-radius: 4px; width: 300px;" value="{{ request('search') }}">
                    
                    <select style="padding: 8px 12px; border: 2px solid #ddd; border-radius: 4px;">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select style="padding: 8px 12px; border: 2px solid #ddd; border-radius: 4px;">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="faulty" {{ request('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                    </select>
                    
                    <select style="padding: 8px 12px; border: 2px solid #ddd; border-radius: 4px;">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                                {{ $region }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                @permission('update_terminals')
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('pos-terminals.create') }}" class="btn btn-primary">Add Terminal</a>
                    <button class="btn">Export Report</button>
                </div>
                @endpermission
            </div>
            
            <!-- Terminals Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Terminal ID</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Client/Bank</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Merchant Name</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Contact Person</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Location</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Status</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Last Service</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terminals as $terminal)
                        <tr>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                <strong>{{ $terminal->terminal_id }}</strong>
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                {{ $terminal->client->company_name }}
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                <div style="font-weight: 500;">{{ $terminal->merchant_name }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $terminal->business_type }}</div>
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                <div style="font-weight: 500;">{{ $terminal->merchant_contact_person }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $terminal->merchant_phone }}</div>
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                <div>{{ $terminal->area }}, {{ $terminal->region }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $terminal->region }} Region</div>
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                <span class="status-badge {{ $terminal->status_badge }}">
                                    {{ ucfirst($terminal->status) }}
                                </span>
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                <div>{{ $terminal->last_service_date ? $terminal->last_service_date->format('M d, Y') : 'Never' }}</div>
                                <div style="font-size: 12px; color: #666;">
                                    {{ $terminal->last_service_info }}
                                </div>
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                <div style="display: flex; gap: 5px;">
                                    <a href="{{ route('pos-terminals.show', $terminal) }}" class="btn-small">View</a>
                                    @permission('update_terminals')
                                    <a href="{{ route('pos-terminals.edit', $terminal) }}" class="btn-small">Edit</a>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="padding: 40px; text-align: center; color: #666;">
                                No POS terminals found. <a href="{{ route('pos-terminals.create') }}">Add your first terminal</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                <div style="color: #666; font-size: 14px;">
                    Showing {{ $terminals->firstItem() ?? 0 }} to {{ $terminals->lastItem() ?? 0 }} of {{ $terminals->total() }} terminals
                </div>
                {{ $terminals->links() }}
            </div>
        </div>
    </div>

    <!-- Import Tab -->
    <div id="import-tab" class="tab-content" style="display: none;">
        <div class="content-card">
            <h3 style="margin-bottom: 20px;">📄 Import Bank/Client Terminal Data</h3>
            <p style="color: #666; margin-bottom: 20px;">Upload the terminal dataset provided by the bank/client. This includes static information about POS terminals and merchant details.</p>
            
            <form action="{{ route('pos-terminals.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div style="border: 3px dashed #ddd; border-radius: 8px; padding: 40px; text-align: center; background: #fafafa; margin-bottom: 20px;">
                    <div style="font-size: 48px; margin-bottom: 15px;">📁</div>
                    <h4>Drag & Drop CSV/Excel File Here</h4>
                    <p>or click to browse files</p>
                    <input type="file" name="file" accept=".csv,.xlsx,.xls" style="margin-top: 10px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label>Select Client:</label>
                    <select name="client_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                        <option value="">Choose a client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Process Import</button>
                <a href="#" class="btn">Download Template</a>
            </form>
        </div>
    </div>

    <!-- Field Updates Tab -->
    <div id="field-tab" class="tab-content" style="display: none;">
        <div class="content-card">
            <h3 style="margin-bottom: 20px;">🔧 Technician Field Updates</h3>
            <p style="color: #666; margin-bottom: 20px;">Update terminal status and service information after field visits.</p>
            
            <form>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label>Terminal ID:</label>
                        <input type="text" placeholder="Enter Terminal ID" style="width: 100%; padding: 8px; margin-top: 5px;">
                    </div>
                    <div>
                        <label>Service Type:</label>
                        <select style="width: 100%; padding: 8px; margin-top: 5px;">
                            <option>Routine Maintenance</option>
                            <option>Emergency Repair</option>
                            <option>Software Update</option>
                            <option>Hardware Replacement</option>
                        </select>
                    </div>
                    <div>
                        <label>Current Status:</label>
                        <select style="width: 100%; padding: 8px; margin-top: 5px;">
                            <option>Active</option>
                            <option>Offline</option>
                            <option>Under Maintenance</option>
                            <option>Faulty</option>
                        </select>
                    </div>
                    <div>
                        <label>Visit Date:</label>
                        <input type="datetime-local" style="width: 100%; padding: 8px; margin-top: 5px;">
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label>Service Notes:</label>
                    <textarea placeholder="Describe the work performed..." rows="4" style="width: 100%; padding: 8px; margin-top: 5px;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Terminal</button>
            </form>
        </div>
    </div>
</div>

<style>
.btn-small {
    padding: 4px 8px;
    font-size: 12px;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
    border-radius: 3px;
    text-decoration: none;
    color: #333;
}

.btn-small:hover {
    background: #f0f0f0;
    border-color: #2196f3;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    background: #e8f5e8;
    color: #2e7d32;
}

.status-offline {
    background: #fff3e0;
    color: #f57c00;
}

.status-pending {
    background: #e3f2fd;
    color: #1976d2;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.style.color = '#666';
        btn.style.borderBottomColor = 'transparent';
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    // Style active button
    event.target.style.color = '#2196f3';
    event.target.style.borderBottomColor = '#2196f3';
}
</script>
@endsection