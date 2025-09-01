{{-- File: resources/views/business-licenses/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">✏️ Edit {{ $businessLicense->isCompanyHeld() ? 'Business License' : 'Customer License' }}</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Update license information for {{ $businessLicense->license_name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('business-licenses.show', $businessLicense) }}" class="btn">👁️ View</a>
            <a href="{{ route('business-licenses.index', ['direction' => $businessLicense->license_direction]) }}" class="btn">← Back to Licenses</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Form -->
        <div>
            <form action="{{ route('business-licenses.update', $businessLicense) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- License Type Display (Read-only) -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">🔄 License Type</h4>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-inline-start: 4px solid #2196f3;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 18px;">{{ $businessLicense->isCompanyHeld() ? '🏢' : '👥' }}</span>
                            <div>
                                <div style="font-weight: 500; color: #333;">{{ $businessLicense->license_direction_name }}</div>
                                <div style="font-size: 12px; color: #666;">
                                    {{ $businessLicense->isCompanyHeld() ? 'License owned by your company' : 'License issued to customer' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📋 Basic Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Name *</label>
                            <input type="text" name="license_name" value="{{ old('license_name', $businessLicense->license_name) }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('license_name')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Number *</label>
                            <input type="text" name="license_number" value="{{ old('license_number', $businessLicense->license_number) }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
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
                                <option value="{{ $key }}" {{ old('license_type', $businessLicense->license_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
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
                                <option value="{{ $key }}" {{ old('status', $businessLicense->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Issuing Authority *</label>
                        <input type="text" name="issuing_authority" value="{{ old('issuing_authority', $businessLicense->issuing_authority) }}" required
                               style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('issuing_authority')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Description</label>
                        <textarea name="description" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">{{ old('description', $businessLicense->description) }}</textarea>
                        @error('description')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Dates Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📅 Dates Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Issue Date *</label>
                            <input type="date" name="issue_date" value="{{ old('issue_date', $businessLicense->issue_date?->format('Y-m-d')) }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('issue_date')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Expiry Date *</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date', $businessLicense->expiry_date?->format('Y-m-d')) }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('expiry_date')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($businessLicense->isCustomerIssued())
                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Service Start Date</label>
                        <input type="date" name="service_start_date" value="{{ old('service_start_date', $businessLicense->service_start_date?->format('Y-m-d')) }}"
                               style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('service_start_date')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>

                @if($businessLicense->isCompanyHeld())
                <!-- Company-Held License Fields -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">💰 Financial Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Initial Cost ($)</label>
                            <input type="number" name="cost" value="{{ old('cost', $businessLicense->cost) }}" step="0.01" min="0"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('cost')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Renewal Cost ($)</label>
                            <input type="number" name="renewal_cost" value="{{ old('renewal_cost', $businessLicense->renewal_cost) }}" step="0.01" min="0"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('renewal_cost')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>


                <!-- Company Additional Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📝 Additional Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Regulatory Body</label>
                            <input type="text" name="regulatory_body" value="{{ old('regulatory_body', $businessLicense->regulatory_body) }}"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('regulatory_body')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Renewal Reminder (Days)</label>
                            <input type="number" name="renewal_reminder_days" value="{{ old('renewal_reminder_days', $businessLicense->renewal_reminder_days) }}" min="1" max="365"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('renewal_reminder_days')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Business Impact</label>
                        <textarea name="business_impact" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">{{ old('business_impact', $businessLicense->business_impact) }}</textarea>
                        @error('business_impact')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Conditions</label>
                        <textarea name="license_conditions" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">{{ old('license_conditions', $businessLicense->license_conditions) }}</textarea>
                        @error('license_conditions')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Compliance Notes</label>
                        <textarea name="compliance_notes" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">{{ old('compliance_notes', $businessLicense->compliance_notes) }}</textarea>
                        @error('compliance_notes')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="auto_renewal" value="1" {{ old('auto_renewal', $businessLicense->auto_renewal) ? 'checked' : '' }}>
                            <span style="font-weight: 500;">Enable Auto-Renewal Notifications</span>
                        </label>
                        @error('auto_renewal')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @else
                <!-- Customer-Issued License Fields -->
                <!-- Customer Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">👤 Customer Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Customer Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $businessLicense->customer_name) }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('customer_name')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Customer Email *</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $businessLicense->customer_email) }}" required
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('customer_email')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Customer Company</label>
                            <input type="text" name="customer_company" value="{{ old('customer_company', $businessLicense->customer_company) }}"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('customer_company')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Customer Phone</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone', $businessLicense->customer_phone) }}"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('customer_phone')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Customer Address</label>
                        <textarea name="customer_address" rows="3" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">{{ old('customer_address', $businessLicense->customer_address) }}</textarea>
                        @error('customer_address')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Customer Reference</label>
                        <input type="text" name="customer_reference" value="{{ old('customer_reference', $businessLicense->customer_reference) }}"
                               style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('customer_reference')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- License & Billing Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">💰 License & Billing Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Revenue Amount ($) *</label>
                            <input type="number" name="revenue_amount" value="{{ old('revenue_amount', $businessLicense->revenue_amount) }}" required step="0.01" min="0"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('revenue_amount')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Billing Cycle *</label>
                            <select name="billing_cycle" required style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Billing Cycle</option>
                                @foreach(\App\Models\BusinessLicense::BILLING_CYCLES as $key => $label)
                                <option value="{{ $key }}" {{ old('billing_cycle', $businessLicense->billing_cycle) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('billing_cycle')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Quantity</label>
                            <input type="number" name="license_quantity" value="{{ old('license_quantity', $businessLicense->license_quantity) }}" min="1"
                                   style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('license_quantity')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Support Level *</label>
                            <select name="support_level" required style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                                <option value="">Select Support Level</option>
                                @foreach(\App\Models\BusinessLicense::SUPPORT_LEVELS as $key => $label)
                                <option value="{{ $key }}" {{ old('support_level', $businessLicense->support_level) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('support_level')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Usage Limit</label>
                        <input type="text" name="usage_limit" value="{{ old('usage_limit', $businessLicense->usage_limit) }}"
                               style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('usage_limit')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Customer License Terms -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📋 License Terms & Conditions</h4>

                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Terms</label>
                        <textarea name="license_terms" rows="4" style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">{{ old('license_terms', $businessLicense->license_terms) }}</textarea>
                        @error('license_terms')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="auto_renewal_customer" value="1" {{ old('auto_renewal_customer', $businessLicense->auto_renewal_customer) ? 'checked' : '' }}>
                            <span style="font-weight: 500;">Enable Auto-Renewal for Customer</span>
                        </label>
                        @error('auto_renewal_customer')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endif

                <!-- Document Upload -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📄 Document</h4>

                    @if($businessLicense->document_path)
                    <div style="background: #e8f5e8; padding: 10px; border-radius: 4px; margin-block-end: 10px; display: flex; align-items: center; gap: 10px;">
                        <span style="color: #4caf50;">📄 Current document: {{ basename($businessLicense->document_path) }}</span>
                        <a href="{{ route('business-licenses.download', $businessLicense) }}" style="color: #2196f3; text-decoration: none; font-size: 14px;">Download</a>
                    </div>
                    @endif

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">License Document</label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               style="inline-size: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">
                            Upload new document to replace existing (PDF, DOC, DOCX, JPG, PNG - Max 2MB)
                        </div>
                        @error('document')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <a href="{{ route('business-licenses.show', $businessLicense) }}" class="btn">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update License</button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div>
            <!-- Current Status -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">📊 Current Status</h4>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 500;">Type:</span>
                        <span style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; background: #e3f2fd; color: #1976d2;">
                            {{ $businessLicense->license_direction_name }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 500;">Status:</span>
                        <span class="status-badge" style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; {{ $businessLicense->getStatusColorClass() }}">
                            {{ $businessLicense->status_name }}
                        </span>
                    </div>
                    @if($businessLicense->isCompanyHeld())
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 500;">Priority:</span>
                        <span class="priority-badge" style="padding: 4px 8px; border-radius: 8px; font-size: 11px; font-weight: 500; {{ $businessLicense->getPriorityColorClass() }}">
                            {{ $businessLicense->priority_level_name }}
                        </span>
                    </div>
                    @else
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 500;">Support:</span>
                        <span style="padding: 4px 8px; border-radius: 8px; font-size: 11px; font-weight: 500; background: #e3f2fd; color: #1976d2;">
                            {{ $businessLicense->support_level_name }}
                        </span>
                    </div>
                    @endif
                    @if($businessLicense->expiry_date)
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 500;">Expires:</span>
                        <span style="{{ $businessLicense->is_expired ? 'color: #f44336; font-weight: bold;' : ($businessLicense->is_expiring_soon ? 'color: #ff9800; font-weight: bold;' : '') }}">
                            {{ $businessLicense->expiry_date->format('M d, Y') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Edit Tips -->
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">💡 Edit Tips</h4>
                <ul style="margin: 0; padding-inline-start: 20px; color: #666; line-height: 1.6;">
                    @if($businessLicense->isCompanyHeld())
                    <li>Update expiry dates when renewals are processed</li>
                    <li>Change status to reflect current license state</li>
                    <li>Upload new documents when available</li>
                    <li>Adjust reminder days for critical licenses</li>
                    <li>Update responsible employees as needed</li>
                    @else
                    <li>Keep customer information up to date</li>
                    <li>Update billing cycle and revenue as needed</li>
                    <li>Modify support levels based on agreements</li>
                    <li>Update license terms when necessary</li>
                    <li>Track usage and adjust limits</li>
                    @endif
                </ul>
            </div>

            <!-- Change History -->
            <div class="content-card">
                <h4 style="margin-block-end: 15px; color: #333;">📝 License History</h4>
                <div style="font-size: 14px; color: #666; line-height: 1.6;">
                    <div style="margin-block-end: 10px;">
                        <strong>Created:</strong> {{ $businessLicense->created_at->format('M d, Y') }}
                        @if($businessLicense->creator)
                        <br><small>by {{ $businessLicense->creator->full_name }}</small>
                        @endif
                    </div>
                    @if($businessLicense->updated_at != $businessLicense->created_at)
                    <div style="margin-block-end: 10px;">
                        <strong>Last Updated:</strong> {{ $businessLicense->updated_at->format('M d, Y') }}
                        @if($businessLicense->updater)
                        <br><small>by {{ $businessLicense->updater->full_name }}</small>
                        @endif
                    </div>
                    @endif
                    @if($businessLicense->renewal_date)
                    <div>
                        <strong>Last Renewed:</strong> {{ $businessLicense->renewal_date->format('M d, Y') }}
                    </div>
                    @endif
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
