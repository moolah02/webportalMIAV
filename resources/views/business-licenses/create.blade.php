{{-- File: resources/views/business-licenses/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">➕ Add New Business License</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Register a new business license for compliance tracking</p>
        </div>
        <a href="{{ route('business-licenses.index') }}" class="btn">← Back to Licenses</a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Form -->
        <div>
            <form action="{{ route('business-licenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📋 Basic Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Name *</label>
                            <input type="text" name="license_name" value="{{ old('license_name') }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                   placeholder="e.g., Business Operating License">
                            @error('license_name')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Number *</label>
                            <input type="text" name="license_number" value="{{ old('license_number') }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                   placeholder="e.g., BL-2024-001234">
                            @error('license_number')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Type *</label>
                            <select name="license_type" required style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select License Type</option>
                                @foreach(\App\Models\BusinessLicense::LICENSE_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('license_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('license_type')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Status *</label>
                            <select name="status" required style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ old('status', 'active') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Issuing Authority *</label>
                        <input type="text" name="issuing_authority" value="{{ old('issuing_authority') }}" required
                               style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                               placeholder="e.g., Department of Commerce">
                        @error('issuing_authority')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Description</label>
                        <textarea name="description" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                  placeholder="Brief description of the license and its purpose...">{{ old('description') }}</textarea>
                        @error('description')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Dates and Financial -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📅 Dates & Financial Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Issue Date *</label>
                            <input type="date" name="issue_date" value="{{ old('issue_date') }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('issue_date')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Expiry Date *</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('expiry_date')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Initial Cost ($)</label>
                            <input type="number" name="cost" value="{{ old('cost') }}" step="0.01" min="0"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                   placeholder="0.00">
                            @error('cost')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Renewal Cost ($)</label>
                            <input type="number" name="renewal_cost" value="{{ old('renewal_cost') }}" step="0.01" min="0"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                   placeholder="0.00">
                            @error('renewal_cost')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Assignment and Management -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">👥 Assignment & Management</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Department *</label>
                            <select name="department_id" required style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('department_id')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Responsible Employee</label>
                            <select name="responsible_employee_id" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('responsible_employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('responsible_employee_id')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Priority Level *</label>
                            <select name="priority_level" required style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                                @foreach(\App\Models\BusinessLicense::PRIORITY_LEVELS as $key => $label)
                                <option value="{{ $key }}" {{ old('priority_level', 'medium') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('priority_level')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Location</label>
                            <input type="text" name="location" value="{{ old('location') }}"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                   placeholder="e.g., Main Office, Branch A">
                            @error('location')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📝 Additional Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Regulatory Body</label>
                            <input type="text" name="regulatory_body" value="{{ old('regulatory_body') }}"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                   placeholder="e.g., State Commerce Commission">
                            @error('regulatory_body')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Renewal Reminder (Days)</label>
                            <input type="number" name="renewal_reminder_days" value="{{ old('renewal_reminder_days', 30) }}" min="1" max="365"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                   placeholder="30">
                            @error('renewal_reminder_days')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Business Impact</label>
                        <textarea name="business_impact" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                  placeholder="Describe the business impact if this license expires...">{{ old('business_impact') }}</textarea>
                        @error('business_impact')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Conditions</label>
                        <textarea name="license_conditions" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                  placeholder="Any specific conditions or requirements for this license...">{{ old('license_conditions') }}</textarea>
                        @error('license_conditions')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Compliance Notes</label>
                        <textarea name="compliance_notes" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;"
                                  placeholder="Any compliance notes or special requirements...">{{ old('compliance_notes') }}</textarea>
                        @error('compliance_notes')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Document</label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">
                            Upload the license document (PDF, DOC, DOCX, JPG, PNG - Max 2MB)
                        </div>
                        @error('document')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="auto_renewal" value="1" {{ old('auto_renewal') ? 'checked' : '' }}>
                            <span style="font-weight: 500;">Enable Auto-Renewal Notifications</span>
                        </label>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">
                            Automatically send renewal reminders based on the reminder days setting
                        </div>
                        @error('auto_renewal')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <a href="{{ route('business-licenses.index') }}" class="btn">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create License</button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div>
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">💡 Tips</h4>
                <ul style="margin: 0; padding-inline-start: 20px; color: #666; line-height: 1.6;">
                    <li>Ensure license numbers are unique and identifiable</li>
                    <li>Set appropriate renewal reminders to avoid expiration</li>
                    <li>Upload clear copies of license documents</li>
                    <li>Assign responsibility to track renewals</li>
                    <li>Mark critical licenses for business operations</li>
                </ul>
            </div>

            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">📋 License Types</h4>
                <div style="color: #666; font-size: 14px; line-height: 1.6;">
                    <strong>Common Types:</strong>
                    <ul style="margin: 10px 0; padding-inline-start: 20px;">
                        <li>Business Operation</li>
                        <li>Professional Licenses</li>
                        <li>Health & Safety</li>
                        <li>Environmental</li>
                        <li>Software Licenses</li>
                        <li>Import/Export</li>
                    </ul>
                </div>
            </div>
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