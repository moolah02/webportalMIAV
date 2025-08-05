{{-- File: resources/views/business-licenses/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📋 {{ $businessLicense->license_name }}</h2>
            <p style="color: #666; margin: 5px 0 0 0;">License #{{ $businessLicense->license_number }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($businessLicense->document_path)
            <a href="{{ route('business-licenses.download', $businessLicense) }}" class="btn">📄 Download</a>
            @endif
            @if($businessLicense->canRenew())
            <a href="{{ route('business-licenses.renew', $businessLicense) }}" class="btn" style="background: #ff9800; color: white; border-color: #ff9800;">🔄 Renew</a>
            @endif
            <a href="{{ route('business-licenses.edit', $businessLicense) }}" class="btn">✏️ Edit</a>
            <a href="{{ route('business-licenses.index') }}" class="btn">← Back</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Content -->
        <div>
            <!-- License Status & Overview -->
            <div class="content-card" style="margin-block-end: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 20px;">
                    <h4 style="margin: 0; color: #333;">📊 License Overview</h4>
                    <div style="display: flex; gap: 10px;">
                        <span class="status-badge" style="padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; {{ $businessLicense->getStatusColorClass() }}">
                            {{ $businessLicense->status_name }}
                        </span>
                        <span class="priority-badge" style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; {{ $businessLicense->getPriorityColorClass() }}">
                            {{ $businessLicense->priority_level_name }} Priority
                        </span>
                    </div>
                </div>

                @if($businessLicense->is_expired || $businessLicense->is_expiring_soon)
                <div style="background: {{ $businessLicense->is_expired ? '#ffebee' : '#fff3e0' }}; border: 1px solid {{ $businessLicense->is_expired ? '#f44336' : '#ff9800' }}; padding: 15px; border-radius: 6px; margin-block-end: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px; color: {{ $businessLicense->is_expired ? '#f44336' : '#f57c00' }};">
                        <div style="font-size: 20px;">{{ $businessLicense->is_expired ? '⚠️' : '⏰' }}</div>
                        <div>
                            <div style="font-weight: bold;">
                                {{ $businessLicense->is_expired ? 'License Expired' : 'License Expiring Soon' }}
                            </div>
                            <div style="font-size: 14px;">
                                @if($businessLicense->is_expired)
                                    Expired {{ abs($businessLicense->days_until_expiry) }} days ago
                                @else
                                    Expires in {{ $businessLicense->days_until_expiry }} days
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">License Type</label>
                        <div style="font-weight: 500;">{{ $businessLicense->license_type_name }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Issuing Authority</label>
                        <div style="font-weight: 500;">{{ $businessLicense->issuing_authority }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Issue Date</label>
                        <div style="font-weight: 500;">{{ $businessLicense->issue_date ? $businessLicense->issue_date->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Expiry Date</label>
                        <div style="font-weight: 500; {{ $businessLicense->is_expired ? 'color: #f44336;' : ($businessLicense->is_expiring_soon ? 'color: #ff9800;' : '') }}">
                            {{ $businessLicense->expiry_date ? $businessLicense->expiry_date->format('M d, Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Details -->
            @if($businessLicense->description)
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">📝 Description</h4>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #2196f3;">
                    {{ $businessLicense->description }}
                </div>
            </div>
            @endif

            <!-- Financial Information -->
            @if($businessLicense->cost || $businessLicense->renewal_cost)
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">💰 Financial Information</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    @if($businessLicense->cost)
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Initial Cost</label>
                        <div style="font-weight: 500; color: #2196f3; font-size: 18px;">${{ number_format($businessLicense->cost, 2) }}</div>
                    </div>
                    @endif
                    @if($businessLicense->renewal_cost)
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Renewal Cost</label>
                        <div style="font-weight: 500; color: #2196f3; font-size: 18px;">${{ number_format($businessLicense->renewal_cost, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Business Impact -->
            @if($businessLicense->business_impact)
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">🏢 Business Impact</h4>
                <div style="background: #fff3e0; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #ff9800;">
                    {{ $businessLicense->business_impact }}
                </div>
            </div>
            @endif

            <!-- License Conditions -->
            @if($businessLicense->license_conditions)
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">📋 License Conditions</h4>
                <div style="background: #e3f2fd; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #2196f3;">
                    {{ $businessLicense->license_conditions }}
                </div>
            </div>
            @endif

            <!-- Compliance Notes -->
            @if($businessLicense->compliance_notes)
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">✅ Compliance Notes</h4>
                <div style="background: #e8f5e8; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #4caf50;">
                    {{ $businessLicense->compliance_notes }}
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Assignment Information -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">👥 Assignment</h4>
                
                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Department</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="inline-size: 32px; height: 32px; border-radius: 50%; background: #2196f3; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold;">
                            {{ substr($businessLicense->department->name ?? 'N/A', 0, 1) }}
                        </div>
                        <div style="font-weight: 500;">{{ $businessLicense->department->name ?? 'Not Assigned' }}</div>
                    </div>
                </div>

                @if($businessLicense->responsibleEmployee)
                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Responsible Employee</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="inline-size: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold;">
                            {{ substr($businessLicense->responsibleEmployee->full_name, 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight: 500;">{{ $businessLicense->responsibleEmployee->full_name }}</div>
                            @if($businessLicense->responsibleEmployee->email)
                            <div style="font-size: 12px; color: #666;">{{ $businessLicense->responsibleEmployee->email }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($businessLicense->location)
                <div>
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Location</label>
                    <div style="font-weight: 500;">📍 {{ $businessLicense->location }}</div>
                </div>
                @endif
            </div>

            <!-- Renewal Settings -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">🔄 Renewal Settings</h4>
                
                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Reminder Days</label>
                    <div style="font-weight: 500;">{{ $businessLicense->renewal_reminder_days }} days before expiry</div>
                </div>

                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Auto Renewal</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 16px;">{{ $businessLicense->auto_renewal ? '✅' : '❌' }}</span>
                        <span style="font-weight: 500;">{{ $businessLicense->auto_renewal ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                </div>

                @if($businessLicense->renewal_date)
                <div>
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Last Renewed</label>
                    <div style="font-weight: 500;">{{ $businessLicense->renewal_date->format('M d, Y') }}</div>
                </div>
                @endif
            </div>

            <!-- Additional Information -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">ℹ️ Additional Info</h4>
                
                @if($businessLicense->regulatory_body)
                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Regulatory Body</label>
                    <div style="font-weight: 500;">{{ $businessLicense->regulatory_body }}</div>
                </div>
                @endif

                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Created</label>
                    <div style="font-weight: 500;">{{ $businessLicense->created_at->format('M d, Y') }}</div>
                    @if($businessLicense->creator)
                    <div style="font-size: 12px; color: #666;">by {{ $businessLicense->creator->full_name }}</div>
                    @endif
                </div>

                @if($businessLicense->updated_at != $businessLicense->created_at)
                <div>
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Last Updated</label>
                    <div style="font-weight: 500;">{{ $businessLicense->updated_at->format('M d, Y') }}</div>
                    @if($businessLicense->updater)
                    <div style="font-size: 12px; color: #666;">by {{ $businessLicense->updater->full_name }}</div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">⚡ Quick Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @if($businessLicense->canRenew() && ($businessLicense->is_expired || $businessLicense->is_expiring_soon))
                    <a href="{{ route('business-licenses.renew', $businessLicense) }}" class="btn" style="background: #ff9800; color: white; border-color: #ff9800; text-align: center;">
                        🔄 Renew License
                    </a>
                    @endif
                    
                    <a href="{{ route('business-licenses.edit', $businessLicense) }}" class="btn" style="text-align: center;">
                        ✏️ Edit License
                    </a>
                    
                    @if($businessLicense->document_path)
                    <a href="{{ route('business-licenses.download', $businessLicense) }}" class="btn" style="background: #2196f3; color: white; border-color: #2196f3; text-align: center;">
                        📄 Download Document
                    </a>
                    @endif
                    
                    <button onclick="if(confirm('Are you sure you want to delete this license?')) { document.getElementById('delete-form').submit(); }" 
                            class="btn" style="background: #f44336; color: white; border-color: #f44336;">
                        🗑️ Delete License
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" action="{{ route('business-licenses.destroy', $businessLicense) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

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
}
</style>
@endsection