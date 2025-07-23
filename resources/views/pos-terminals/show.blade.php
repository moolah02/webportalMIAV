@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0;">Terminal {{ $posTerminal->terminal_id }}</h3>
            <p style="color: #666; margin: 5px 0 0 0;">{{ $posTerminal->merchant_name }} • {{ $posTerminal->client->company_name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @permission('update_terminals')
            <a href="{{ route('pos-terminals.edit', $posTerminal) }}" class="btn btn-primary">Edit Terminal</a>
            @endpermission
            <a href="{{ route('pos-terminals.index') }}" class="btn">← Back to List</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Information -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Terminal Details -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">🖥️ Terminal Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Terminal ID</label>
                            <div style="font-weight: 600; font-size: 16px;">{{ $posTerminal->terminal_id }}</div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Model</label>
                            <div style="font-weight: 500;">{{ $posTerminal->terminal_model ?: 'Not specified' }}</div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Serial Number</label>
                            <div style="font-weight: 500;">{{ $posTerminal->serial_number ?: 'Not specified' }}</div>
                        </div>
                    </div>
                    <div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Status</label>
                            <div>
                                <span class="status-badge {{ $posTerminal->status_badge }}">
                                    {{ ucfirst($posTerminal->status) }}
                                </span>
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Installation Date</label>
                            <div style="font-weight: 500;">{{ $posTerminal->installation_date ? $posTerminal->installation_date->format('M d, Y') : 'Not specified' }}</div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Last Service</label>
                            <div style="font-weight: 500;">{{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never serviced' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Merchant Information -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">🏪 Merchant Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Business Name</label>
                            <div style="font-weight: 600; font-size: 16px;">{{ $posTerminal->merchant_name }}</div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Contact Person</label>
                            <div style="font-weight: 500;">{{ $posTerminal->merchant_contact_person ?: 'Not specified' }}</div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Business Type</label>
                            <div style="font-weight: 500;">{{ $posTerminal->business_type ?: 'Not specified' }}</div>
                        </div>
                    </div>
                    <div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Phone Number</label>
                            <div style="font-weight: 500;">
                                @if($posTerminal->merchant_phone)
                                    <a href="tel:{{ $posTerminal->merchant_phone }}" style="color: #2196f3; text-decoration: none;">
                                        {{ $posTerminal->merchant_phone }}
                                    </a>
                                @else
                                    Not specified
                                @endif
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Email Address</label>
                            <div style="font-weight: 500;">
                                @if($posTerminal->merchant_email)
                                    <a href="mailto:{{ $posTerminal->merchant_email }}" style="color: #2196f3; text-decoration: none;">
                                        {{ $posTerminal->merchant_email }}
                                    </a>
                                @else
                                    Not specified
                                @endif
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Location</label>
                            <div style="font-weight: 500;">{{ $posTerminal->area }}, {{ $posTerminal->region }}</div>
                        </div>
                    </div>
                </div>
                
                @if($posTerminal->physical_address)
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Physical Address</label>
                    <div style="font-weight: 500; margin-top: 5px;">{{ $posTerminal->physical_address }}</div>
                </div>
                @endif
            </div>

            <!-- Client Information -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">🏢 Client Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Bank/Client</label>
                            <div style="font-weight: 600; font-size: 16px;">
                                <a href="{{ route('clients.show', $posTerminal->client) }}" style="color: #2196f3; text-decoration: none;">
                                    {{ $posTerminal->client->company_name }}
                                </a>
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Client Code</label>
                            <div style="font-weight: 500;">{{ $posTerminal->client->client_code }}</div>
                        </div>
                    </div>
                    <div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Contact Person</label>
                            <div style="font-weight: 500;">{{ $posTerminal->client->contact_person ?: 'Not specified' }}</div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Client Status</label>
                            <div>
                                <span class="status-badge {{ $posTerminal->client->status == 'active' ? 'status-active' : 'status-offline' }}">
                                    {{ ucfirst($posTerminal->client->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract Details -->
            @if($posTerminal->contract_details)
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">📄 Contract Details</h4>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #2196f3;">
                    {{ $posTerminal->contract_details }}
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Quick Actions -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">⚡ Quick Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @permission('update_terminals')
                    <button onclick="updateStatus('active')" class="btn-small" style="width: 100%; text-align: center; padding: 8px;">
                        Mark as Active
                    </button>
                    <button onclick="updateStatus('maintenance')" class="btn-small" style="width: 100%; text-align: center; padding: 8px;">
                        Mark for Maintenance
                    </button>
                    <button onclick="updateStatus('offline')" class="btn-small" style="width: 100%; text-align: center; padding: 8px;">
                        Mark as Offline
                    </button>
                    @endpermission
                    
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid #eee;">
                    
                    <a href="#" class="btn-small" style="width: 100%; text-align: center; padding: 8px; text-decoration: none;">
                        🎫 Create Ticket
                    </a>
                    <a href="#" class="btn-small" style="width: 100%; text-align: center; padding: 8px; text-decoration: none;">
                        🔧 Schedule Service
                    </a>
                    <a href="#" class="btn-small" style="width: 100%; text-align: center; padding: 8px; text-decoration: none;">
                        📊 View Reports
                    </a>
                </div>
            </div>

            <!-- Service Information -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">🛠️ Service Information</h4>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Last Service</label>
                    <div style="font-weight: 500;">
                        {{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never serviced' }}
                    </div>
                    @if($posTerminal->last_service_date)
                    <div style="font-size: 12px; color: #666;">
                        {{ $posTerminal->last_service_date->diffForHumans() }}
                    </div>
                    @endif
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Next Service Due</label>
                    <div style="font-weight: 500;">
                        @if($posTerminal->next_service_due)
                            <span style="color: {{ $posTerminal->next_service_due <= now() ? '#f44336' : ($posTerminal->next_service_due <= now()->addDays(7) ? '#ff9800' : '#333') }};">
                                {{ $posTerminal->next_service_due->format('M d, Y') }}
                            </span>
                            @if($posTerminal->next_service_due <= now())
                                <div style="font-size: 12px; color: #f44336; font-weight: 600;">⚠️ Overdue</div>
                            @elseif($posTerminal->next_service_due <= now()->addDays(7))
                                <div style="font-size: 12px; color: #ff9800; font-weight: 600;">⏰ Due soon</div>
                            @endif
                        @else
                            <span style="color: #666;">Not scheduled</span>
                        @endif
                    </div>
                </div>

                @if($posTerminal->needsService && $posTerminal->needsService())
                <div style="background: #fff3e0; border: 1px solid #ff9800; border-radius: 6px; padding: 12px; margin-top: 15px;">
                    <div style="color: #f57c00; font-weight: 600; font-size: 14px;">🔔 Service Required</div>
                    <div style="color: #666; font-size: 12px; margin-top: 4px;">This terminal is due for service</div>
                </div>
                @endif
            </div>

            <!-- Statistics -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">📊 Statistics</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #666; font-size: 14px;">Total Jobs</span>
                        <span style="font-weight: 600;">{{ $posTerminal->jobAssignments ? $posTerminal->jobAssignments->count() : 0 }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #666; font-size: 14px;">Service Reports</span>
                        <span style="font-weight: 600;">{{ $posTerminal->serviceReports ? $posTerminal->serviceReports->count() : 0 }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #666; font-size: 14px;">Open Tickets</span>
                        <span style="font-weight: 600;">{{ $posTerminal->tickets ? $posTerminal->tickets->where('status', '!=', 'resolved')->count() : 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Form (Hidden) -->
@permission('update_terminals')
<form id="statusUpdateForm" action="{{ route('pos-terminals.update-status', $posTerminal) }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>
@endpermission

<script>
@permission('update_terminals')
function updateStatus(status) {
    if (confirm('Are you sure you want to update the terminal status to ' + status + '?')) {
        document.getElementById('statusInput').value = status;
        document.getElementById('statusUpdateForm').submit();
    }
}
@endpermission
</script>
@endsection