{{-- File: resources/views/business-licenses/show.blade.php --}}
@extends('layouts.app')
@section('title', 'License Details')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">
                {{ $businessLicense->isCompanyHeld() ? '🏢' : '👥' }} {{ $businessLicense->license_name }}
            </h2>
            <p style="color: #666; margin: 5px 0 0 0;">
                License #{{ $businessLicense->license_number }} • {{ $businessLicense->license_direction_name }}
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($businessLicense->document_path)
            <a href="{{ route('business-licenses.download', $businessLicense) }}" class="btn-secondary">📄 Download</a>
            @endif
            @if($businessLicense->canRenew())
            <a href="{{ route('business-licenses.renew', $businessLicense) }}" class="btn-secondary" style="background: #ff9800; color: white; border-color: #ff9800;">🔄 Renew</a>
            @endif
            <a href="{{ route('business-licenses.edit', $businessLicense) }}" class="btn-secondary">✏️ Edit</a>
            <a href="{{ route('business-licenses.index', ['direction' => $businessLicense->license_direction]) }}" class="btn-secondary">← Back</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Content -->
        <div>
            <!-- License Status & Overview -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 20px;">
                    <h4 style="margin: 0; color: #333;">📊 License Overview</h4>
                    <div style="display: flex; gap: 10px;">
                        <span class="direction-badge" style="padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background: #e3f2fd; color: #1976d2;">
                            {{ $businessLicense->license_direction_name }}
                        </span>
                        <span class="status-badge" style="padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; {{ $businessLicense->getStatusColorClass() }}">
                            {{ $businessLicense->status_name }}
                        </span>
                        @if($businessLicense->isCompanyHeld())
                        <span class="priority-badge" style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; {{ $businessLicense->getPriorityColorClass() }}">
                            {{ $businessLicense->priority_level_name }} Priority
                        </span>
                        @else
                        <span class="support-badge" style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; background: #e3f2fd; color: #1976d2;">
                            {{ $businessLicense->support_level_name }}
                        </span>
                        @endif
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

            @if($businessLicense->isCustomerIssued())
            <!-- Customer Information -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">👤 Customer Information</h4>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-block-end: 15px;">
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Customer Name</label>
                        <div style="font-weight: 500;">{{ $businessLicense->customer_name }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Email</label>
                        <div style="font-weight: 500;">
                            <a href="mailto:{{ $businessLicense->customer_email }}" style="color: #2196f3; text-decoration: none;">
                                {{ $businessLicense->customer_email }}
                            </a>
                        </div>
                    </div>
                    @if($businessLicense->customer_company)
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Company</label>
                        <div style="font-weight: 500;">{{ $businessLicense->customer_company }}</div>
                    </div>
                    @endif
                    @if($businessLicense->customer_phone)
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Phone</label>
                        <div style="font-weight: 500;">
                            <a href="tel:{{ $businessLicense->customer_phone }}" style="color: #2196f3; text-decoration: none;">
                                {{ $businessLicense->customer_phone }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                @if($businessLicense->customer_address)
                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Address</label>
                    <div style="background: #f8f9fa; padding: 10px; border-radius: 4px;">{{ $businessLicense->customer_address }}</div>
                </div>
                @endif

                @if($businessLicense->customer_reference)
                <div>
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Customer Reference</label>
                    <div style="font-weight: 500;">{{ $businessLicense->customer_reference }}</div>
                </div>
                @endif
            </div>
            @endif

            <!-- Description & Details -->
            @if($businessLicense->description)
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">📝 Description</h4>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #2196f3;">
                    {{ $businessLicense->description }}
                </div>
            </div>
            @endif

            <!-- Financial Information -->
            @if($businessLicense->isCompanyHeld() && ($businessLicense->cost || $businessLicense->renewal_cost))
            <div class="ui-card p-6" style="margin-block-end: 20px;">
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
            @elseif($businessLicense->isCustomerIssued())
            <!-- Revenue Information -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">💰 Revenue Information</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Revenue Amount</label>
                        <div style="font-weight: 500; color: #4caf50; font-size: 18px;">${{ number_format($businessLicense->revenue_amount, 2) }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Billing Cycle</label>
                        <div style="font-weight: 500;">{{ $businessLicense->billing_cycle_name }}</div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Annual Revenue</label>
                        <div style="font-weight: 500; color: #4caf50; font-size: 18px;">${{ number_format($businessLicense->annual_revenue, 2) }}</div>
                    </div>
                    @if($businessLicense->license_quantity)
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">License Quantity</label>
                        <div style="font-weight: 500;">{{ $businessLicense->license_quantity }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($businessLicense->isCustomerIssued() && ($businessLicense->usage_limit || $businessLicense->service_start_date))
            <!-- License Details -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">📋 License Details</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    @if($businessLicense->usage_limit)
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Usage Limit</label>
                        <div style="font-weight: 500;">{{ $businessLicense->usage_limit }}</div>
                    </div>
                    @endif
                    @if($businessLicense->service_start_date)
                    <div>
                        <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Service Start Date</label>
                        <div style="font-weight: 500;">{{ $businessLicense->service_start_date->format('M d, Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($businessLicense->isCompanyHeld() && $businessLicense->business_impact)
            <!-- Business Impact -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">🏢 Business Impact</h4>
                <div style="background: #fff3e0; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #ff9800;">
                    {{ $businessLicense->business_impact }}
                </div>
            </div>
            @endif

            <!-- License Conditions / Terms -->
            @if($businessLicense->license_conditions || $businessLicense->license_terms)
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">
                    📋 {{ $businessLicense->isCompanyHeld() ? 'License Conditions' : 'License Terms' }}
                </h4>
                <div style="background: #e3f2fd; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #2196f3;">
                    {{ $businessLicense->isCompanyHeld() ? $businessLicense->license_conditions : $businessLicense->license_terms }}
                </div>
            </div>
            @endif

            @if($businessLicense->isCompanyHeld() && $businessLicense->compliance_notes)
            <!-- Compliance Notes -->
            <div class="ui-card p-6">
                <h4 style="margin-block-end: 15px; color: #333;">✅ Compliance Notes</h4>
                <div style="background: #e8f5e8; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #4caf50;">
                    {{ $businessLicense->compliance_notes }}
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            @if($businessLicense->isCompanyHeld())
            <!-- Assignment Information -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
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
                        <div style="inline-size: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #1a3a5c 0%, #152e4a 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold;">
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
            <div class="ui-card p-6" style="margin-block-end: 20px;">
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
            @else
            <!-- Customer License Settings -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">⚙️ License Settings</h4>
                
                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Support Level</label>
                    <div style="font-weight: 500;">{{ $businessLicense->support_level_name }}</div>
                </div>

                <div style="margin-block-end: 15px;">
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Auto Renewal</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 16px;">{{ $businessLicense->auto_renewal_customer ? '✅' : '❌' }}</span>
                        <span style="font-weight: 500;">{{ $businessLicense->auto_renewal_customer ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                </div>

                @if($businessLicense->renewal_date)
                <div>
                    <label style="font-size: 12px; color: #666; text-transform: uppercase; margin-block-end: 5px; display: block;">Last Renewed</label>
                    <div style="font-weight: 500;">{{ $businessLicense->renewal_date->format('M d, Y') }}</div>
                </div>
                @endif
            </div>
            @endif

            <!-- Additional Information -->
            <div class="ui-card p-6" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">ℹ️ Additional Info</h4>
                
                @if($businessLicense->isCompanyHeld() && $businessLicense->regulatory_body)
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
            <div class="ui-card p-6">
                <h4 style="margin-block-end: 15px; color: #333;">⚡ Quick Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @if($businessLicense->canRenew() && ($businessLicense->is_expired || $businessLicense->is_expiring_soon))
                    <a href="{{ route('business-licenses.renew', $businessLicense) }}" class="btn-secondary" style="background: #ff9800; color: white; border-color: #ff9800; text-align: center;">
                        🔄 Renew License
                    </a>
                    @endif
                    
                    <a href="{{ route('business-licenses.edit', $businessLicense) }}" class="btn-secondary" style="text-align: center;">
                        ✏️ Edit License
                    </a>
                    
                    @if($businessLicense->document_path)
                    <a href="{{ route('business-licenses.download', $businessLicense) }}" class="btn-secondary" style="background: #2196f3; color: white; border-color: #2196f3; text-align: center;">
                        📄 Download Document
                    </a>
                    @endif
                    
                    @if($businessLicense->isCustomerIssued() && $businessLicense->customer_email)
                    <a href="mailto:{{ $businessLicense->customer_email }}?subject=Regarding License {{ $businessLicense->license_number }}" class="btn-secondary" style="background: #4caf50; color: white; border-color: #4caf50; text-align: center;">
                        ✉️ Email Customer
                    </a>
                    @endif
                    
                    <button onclick="if(confirm('Are you sure you want to delete this license?')) { document.getElementById('delete-form').submit(); }" 
                            class="btn-secondary" style="background: #f44336; color: white; border-color: #f44336;">
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

@endsection