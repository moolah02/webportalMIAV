@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 24px;">Terminal {{ $posTerminal->terminal_id }}</h2>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">{{ $posTerminal->merchant_name }} • {{ $posTerminal->client->company_name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('pos-terminals.edit', $posTerminal) }}" class="btn btn-primary">Edit Terminal</a>
            <a href="{{ route('pos-terminals.index') }}" class="btn">← Back to List</a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
        
        <!-- Left Column - Main Information -->
        <div>
            <!-- Terminal Information -->
            <div class="info-card">
                <h3>🖥️ Terminal Information</h3>
                <div class="info-grid">
                    <div>
                        <label>Terminal ID</label>
                        <div class="info-value">{{ $posTerminal->terminal_id }}</div>
                    </div>
                    <div>
                        <label>Status</label>
                        <div>
                            <span class="status-badge status-{{ $posTerminal->status }}">
                                {{ ucfirst($posTerminal->status) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label>Model</label>
                        <div class="info-value">{{ $posTerminal->terminal_model ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Serial Number</label>
                        <div class="info-value">{{ $posTerminal->serial_number ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Installation Date</label>
                        <div class="info-value">{{ $posTerminal->installation_date ? $posTerminal->installation_date->format('M d, Y') : 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Last Service</label>
                        <div class="info-value">{{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never serviced' }}</div>
                    </div>
                </div>
            </div>

            <!-- Merchant Information -->
            <div class="info-card">
                <h3>🏪 Merchant Information</h3>
                <div class="info-grid">
                    <div>
                        <label>Business Name</label>
                        <div class="info-value">{{ $posTerminal->merchant_name }}</div>
                    </div>
                    <div>
                        <label>Contact Person</label>
                        <div class="info-value">{{ $posTerminal->merchant_contact_person ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Phone Number</label>
                        <div class="info-value">
                            @if($posTerminal->merchant_phone)
                                <a href="tel:{{ $posTerminal->merchant_phone }}" style="color: #007bff; text-decoration: none;">
                                    {{ $posTerminal->merchant_phone }}
                                </a>
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    <div>
                        <label>Email Address</label>
                        <div class="info-value">
                            @if($posTerminal->merchant_email)
                                <a href="mailto:{{ $posTerminal->merchant_email }}" style="color: #007bff; text-decoration: none;">
                                    {{ $posTerminal->merchant_email }}
                                </a>
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    <div>
                        <label>Business Type</label>
                        <div class="info-value">{{ $posTerminal->business_type ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Region</label>
                        <div class="info-value">{{ $posTerminal->region ?: 'Not specified' }}</div>
                    </div>
                </div>
                
                @if($posTerminal->physical_address)
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <label>Physical Address</label>
                    <div class="info-value" style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-top: 5px;">
                        {{ $posTerminal->physical_address }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Client Information -->
            <div class="info-card">
                <h3>🏢 Client Information</h3>
                <div class="info-grid">
                    <div>
                        <label>Bank/Client</label>
                        <div class="info-value">
                            <a href="{{ route('clients.show', $posTerminal->client) }}" style="color: #007bff; text-decoration: none; font-weight: 500;">
                                {{ $posTerminal->client->company_name }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <label>Client Code</label>
                        <div class="info-value">{{ $posTerminal->client->client_code ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <label>Contact Person</label>
                        <div class="info-value">{{ $posTerminal->client->contact_person ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Client Status</label>
                        <div>
                            <span class="status-badge status-{{ $posTerminal->client->status ?? 'active' }}">
                                {{ ucfirst($posTerminal->client->status ?? 'active') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($posTerminal->contract_details)
            <!-- Contract Details -->
            <div class="info-card">
                <h3>📄 Contract Details</h3>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 3px solid #007bff;">
                    {{ $posTerminal->contract_details }}
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div class="info-card">
                <h3>⚡ Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <button onclick="updateStatus('active')" class="action-btn active-btn">
                        Mark as Active
                    </button>
                    <button onclick="updateStatus('maintenance')" class="action-btn maintenance-btn">
                        Mark for Maintenance
                    </button>
                    <button onclick="updateStatus('offline')" class="action-btn offline-btn">
                        Mark as Offline
                    </button>
                    
                    <hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">
                    
                    <a href="#" class="action-btn">🎫 Create Ticket</a>
                    <a href="#" class="action-btn">📅 Schedule Service</a>
                    <a href="#" class="action-btn">📊 View Reports</a>
                </div>
            </div>

            <!-- Service Information -->
            <div class="info-card">
                <h3>🛠️ Service Information</h3>
                <div style="margin-block-end: 15px;">
                    <label>Last Service</label>
                    <div class="info-value">
                        {{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never serviced' }}
                    </div>
                    @if($posTerminal->last_service_date)
                    <div style="font-size: 12px; color: #666; margin-top: 2px;">
                        {{ $posTerminal->last_service_date->diffForHumans() }}
                    </div>
                    @endif
                </div>
                
                <div style="margin-block-end: 15px;">
                    <label>Next Service Due</label>
                    <div class="info-value">
                        @if($posTerminal->next_service_due)
                            <span style="color: {{ $posTerminal->next_service_due <= now() ? '#dc3545' : ($posTerminal->next_service_due <= now()->addDays(7) ? '#ffc107' : '#333') }};">
                                {{ $posTerminal->next_service_due->format('M d, Y') }}
                            </span>
                            @if($posTerminal->next_service_due <= now())
                                <div style="font-size: 12px; color: #dc3545; font-weight: 600; margin-top: 2px;">⚠️ Overdue</div>
                            @elseif($posTerminal->next_service_due <= now()->addDays(7))
                                <div style="font-size: 12px; color: #ffc107; font-weight: 600; margin-top: 2px;">⏰ Due soon</div>
                            @endif
                        @else
                            Not scheduled
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="info-card">
                <h3>📊 Statistics</h3>
                <div class="stats-list">
                    <div class="stat-item">
                        <span>Total Jobs</span>
                        <span>0</span>
                    </div>
                    <div class="stat-item">
                        <span>Service Reports</span>
                        <span>0</span>
                    </div>
                    <div class="stat-item">
                        <span>Open Tickets</span>
                        <span>0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Form (Hidden) -->
<form id="statusUpdateForm" action="{{ route('pos-terminals.update-status', $posTerminal) }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>

<style>
/* Professional, clean styling */
.info-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 20px;
    margin-block-end: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.info-card h3 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 16px;
    font-weight: 600;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.info-grid label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
    margin-block-end: 3px;
    display: block;
}

.info-value {
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

.btn {
    display: inline-block;
    padding: 8px 16px;
    background: #f8f9fa;
    color: #333;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn:hover {
    background: #e9ecef;
    border-color: #007bff;
    text-decoration: none;
}

.btn-primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.action-btn {
    display: block;
    padding: 10px;
    background: white;
    color: #333;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    text-align: center;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
    text-decoration: none;
}

.active-btn {
    background: #d4edda !important;
    color: #155724 !important;
    border-color: #c3e6cb !important;
}

.maintenance-btn {
    background: #d1ecf1 !important;
    color: #0c5460 !important;
    border-color: #bee5eb !important;
}

.offline-btn {
    background: #fff3cd !important;
    color: #856404 !important;
    border-color: #ffeaa7 !important;
}

.status-badge {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-offline {
    background: #fff3cd;
    color: #856404;
}

.status-maintenance {
    background: #d1ecf1;
    color: #0c5460;
}

.status-faulty {
    background: #f8d7da;
    color: #721c24;
}

.stats-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 13px;
}

.stat-item span:last-child {
    font-weight: 600;
    color: #007bff;
}

/* Responsive */
@media (max-width: 768px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    .info-grid {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="display: flex; justify-content: space-between"] {
        flex-direction: column !important;
        gap: 10px !important;
    }
}
</style>

<script>
function updateStatus(status) {
    if (confirm('Are you sure you want to update the terminal status to ' + status + '?')) {
        document.getElementById('statusInput').value = status;
        document.getElementById('statusUpdateForm').submit();
    }
}
</script>
@endsection