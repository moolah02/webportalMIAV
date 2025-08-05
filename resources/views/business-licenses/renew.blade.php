{{-- File: resources/views/business-licenses/renew.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">🔄 Renew Business License</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Process renewal for {{ $businessLicense->license_name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('business-licenses.show', $businessLicense) }}" class="btn">👁️ View License</a>
            <a href="{{ route('business-licenses.index') }}" class="btn">← Back to Licenses</a>
        </div>
    </div>

    <!-- License Status Alert -->
    @if($businessLicense->is_expired || $businessLicense->is_expiring_soon)
    <div style="background: {{ $businessLicense->is_expired ? '#ffebee' : '#fff3e0' }}; border: 1px solid {{ $businessLicense->is_expired ? '#f44336' : '#ff9800' }}; padding: 20px; border-radius: 8px; margin-block-end: 30px;">
        <div style="display: flex; align-items: center; gap: 15px; color: {{ $businessLicense->is_expired ? '#f44336' : '#f57c00' }};">
            <div style="font-size: 32px;">{{ $businessLicense->is_expired ? '⚠️' : '⏰' }}</div>
            <div>
                <div style="font-weight: bold; font-size: 18px; margin-block-end: 5px;">
                    {{ $businessLicense->is_expired ? 'License Has Expired' : 'License Expiring Soon' }}
                </div>
                <div style="font-size: 14px;">
                    @if($businessLicense->is_expired)
                        This license expired {{ abs($businessLicense->days_until_expiry) }} days ago on {{ $businessLicense->expiry_date->format('M d, Y') }}
                    @else
                        This license expires in {{ $businessLicense->days_until_expiry }} days on {{ $businessLicense->expiry_date->format('M d, Y') }}
                    @endif
                </div>
                @if($businessLicense->business_impact)
                <div style="font-size: 13px; margin-block-start: 8px; padding: 10px; background: rgba(255,255,255,0.3); border-radius: 4px;">
                    <strong>Business Impact:</strong> {{ $businessLicense->business_impact }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Renewal Form -->
        <div>
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 20px; color: #333;">🔄 Renewal Information</h4>
                
                <form action="{{ route('business-licenses.process-renewal', $businessLicense) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- New Expiry Date -->
                    <div style="margin-block-end: 25px;">
                        <label style="display: block; margin-block-end: 8px; font-weight: 500; color: #333;">New Expiry Date *</label>
                        <input type="date" name="new_expiry_date" value="{{ old('new_expiry_date') }}" required
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               style="inline-size: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px;">
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">
                            Current expiry: {{ $businessLicense->expiry_date ? $businessLicense->expiry_date->format('M d, Y') : 'Not set' }}
                        </div>
                        @error('new_expiry_date')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Renewal Cost -->
                    <div style="margin-block-end: 25px;">
                        <label style="display: block; margin-block-end: 8px; font-weight: 500; color: #333;">Renewal Cost ($)</label>
                        <input type="number" name="renewal_cost" value="{{ old('renewal_cost', $businessLicense->renewal_cost) }}" 
                               step="0.01" min="0" placeholder="0.00"
                               style="inline-size: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px;">
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">
                            Previous renewal cost: ${{ number_format($businessLicense->renewal_cost ?? 0, 2) }}
                        </div>
                        @error('renewal_cost')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Compliance Notes -->
                    <div style="margin-block-end: 25px;">
                        <label style="display: block; margin-block-end: 8px; font-weight: 500; color: #333;">Renewal Notes</label>
                        <textarea name="compliance_notes" rows="4" placeholder="Any notes about this renewal process, compliance updates, or changes..."
                                  style="inline-size: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; resize: vertical;">{{ old('compliance_notes') }}</textarea>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">
                            Document any changes, conditions, or important notes about this renewal
                        </div>
                        @error('compliance_notes')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Document Upload -->
                    <div style="margin-block-end: 25px;">
                        <label style="display: block; margin-block-end: 8px; font-weight: 500; color: #333;">Renewed License Document</label>
                        @if($businessLicense->document_path)
                        <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-block-end: 10px; display: flex; align-items: center; justify-content: between; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: #666;">📄 Current:</span>
                                <span style="font-weight: 500;">{{ basename($businessLicense->document_path) }}</span>
                            </div>
                            <a href="{{ route('business-licenses.download', $businessLicense) }}" 
                               style="color: #2196f3; text-decoration: none; font-size: 14px;">View Current</a>
                        </div>
                        @endif
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               style="inline-size: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px;">
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">
                            Upload the new renewed license document (PDF, DOC, DOCX, JPG, PNG - Max 2MB)
                        </div>
                        @error('document')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Renewal Confirmation -->
                    <div style="background: #e3f2fd; border: 1px solid #2196f3; padding: 20px; border-radius: 8px; margin-block-end: 25px;">
                        <h5 style="margin: 0 0 10px 0; color: #1976d2;">📋 Renewal Summary</h5>
                        <div style="font-size: 14px; color: #333; line-height: 1.6;">
                            <div><strong>License:</strong> {{ $businessLicense->license_name }}</div>
                            <div><strong>License Number:</strong> {{ $businessLicense->license_number }}</div>
                            <div><strong>Current Status:</strong> 
                                <span class="status-badge" style="padding: 2px 6px; border-radius: 8px; font-size: 11px; {{ $businessLicense->getStatusColorClass() }}">
                                    {{ $businessLicense->status_name }}
                                </span>
                            </div>
                            <div><strong>Current Expiry:</strong> {{ $businessLicense->expiry_date ? $businessLicense->expiry_date->format('M d, Y') : 'Not set' }}</div>
                            <div><strong>Issuing Authority:</strong> {{ $businessLicense->issuing_authority }}</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 15px; justify-content: flex-end;">
                        <a href="{{ route('business-licenses.show', $businessLicense) }}" class="btn">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="background: #4caf50; border-color: #4caf50;">
                            🔄 Process Renewal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div>
            <!-- Current License Info -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">📋 Current License Details</h4>
                
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Type:</span>
                        <span style="font-weight: 500;">{{ $businessLicense->license_type_name }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Department:</span>
                        <span style="font-weight: 500;">{{ $businessLicense->department->name ?? 'N/A' }}</span>
                    </div>
                    
                    @if($businessLicense->responsibleEmployee)
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Responsible:</span>
                        <span style="font-weight: 500;">{{ $businessLicense->responsibleEmployee->full_name }}</span>
                    </div>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Priority:</span>
                        <span class="priority-badge" style="padding: 2px 6px; border-radius: 6px; font-size: 11px; {{ $businessLicense->getPriorityColorClass() }}">
                            {{ $businessLicense->priority_level_name }}
                        </span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Issue Date:</span>
                        <span style="font-weight: 500;">{{ $businessLicense->issue_date ? $businessLicense->issue_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    
                    @if($businessLicense->renewal_reminder_days)
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Reminder Days:</span>
                        <span style="font-weight: 500;">{{ $businessLicense->renewal_reminder_days }} days</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Renewal History -->
            @if($businessLicense->renewal_date)
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">🔄 Last Renewal</h4>
                <div style="font-size: 14px; color: #666;">
                    <div style="margin-block-end: 8px;">
                        <strong>Date:</strong> {{ $businessLicense->renewal_date->format('M d, Y') }}
                    </div>
                    @if($businessLicense->renewal_cost)
                    <div style="margin-block-end: 8px;">
                        <strong>Cost:</strong> ${{ number_format($businessLicense->renewal_cost, 2) }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Renewal Tips -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">💡 Renewal Tips</h4>
                <ul style="margin: 0; padding-inline-start: 20px; color: #666; line-height: 1.6; font-size: 14px;">
                    <li>Set new expiry date based on renewal period</li>
                    <li>Upload the new license document if available</li>
                    <li>Update renewal costs for budget tracking</li>
                    <li>Add notes about any condition changes</li>
                    <li>Verify all information before processing</li>
                </ul>
            </div>

            <!-- Important Notes -->
            @if($businessLicense->license_conditions || $businessLicense->business_impact)
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">⚠️ Important Notes</h4>
                
                @if($businessLicense->business_impact)
                <div style="background: #fff3e0; padding: 12px; border-radius: 6px; margin-block-end: 15px; border-inline-start: 4px solid #ff9800;">
                    <div style="font-weight: 500; color: #f57c00; margin-block-end: 5px;">Business Impact:</div>
                    <div style="font-size: 14px; color: #666;">{{ $businessLicense->business_impact }}</div>
                </div>
                @endif
                
                @if($businessLicense->license_conditions)
                <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; border-inline-start: 4px solid #2196f3;">
                    <div style="font-weight: 500; color: #1976d2; margin-block-end: 5px;">License Conditions:</div>
                    <div style="font-size: 14px; color: #666;">{{ $businessLicense->license_conditions }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn {
    padding: 10px 20px;
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

/* Form styling */
input[type="date"], input[type="number"], textarea {
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

input[type="date"]:focus, input[type="number"]:focus, textarea:focus {
    outline: none;
    border-color: #2196f3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

/* File upload styling */
input[type="file"] {
    transition: border-color 0.2s ease;
}

input[type="file"]:hover {
    border-color: #2196f3;
}
</style>

<script>
// Auto-calculate common renewal periods
document.addEventListener('DOMContentLoaded', function() {
    const expiryInput = document.querySelector('input[name="new_expiry_date"]');
    const currentExpiry = new Date('{{ $businessLicense->expiry_date ? $businessLicense->expiry_date->format("Y-m-d") : "" }}');
    
    // Suggest 1 year from current expiry as default
    if (currentExpiry && !isNaN(currentExpiry.getTime())) {
        const oneYearLater = new Date(currentExpiry);
        oneYearLater.setFullYear(oneYearLater.getFullYear() + 1);
        
        if (!expiryInput.value) {
            expiryInput.value = oneYearLater.toISOString().split('T')[0];
        }
    }
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const newExpiryDate = new Date(expiryInput.value);
        const currentDate = new Date();
        
        if (newExpiryDate <= currentDate) {
            e.preventDefault();
            alert('New expiry date must be in the future.');
            expiryInput.focus();
            return false;
        }
        
        // Confirm renewal
        const confirmMessage = `Are you sure you want to renew this license?\n\nNew expiry date: ${newExpiryDate.toLocaleDateString()}\n\nThis will update the license status to "active".`;
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection