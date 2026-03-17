{{-- File: resources/views/business-licenses/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">➕ Add New {{ $direction === 'company_held' ? 'Business License' : 'Customer License' }}</h2>
            <p style="color: #666; margin: 5px 0 0 0;">
                {{ $direction === 'company_held' ? 'Register a company-held business license for compliance tracking' : 'Register a license issued to a customer' }}
            </p>
        </div>
        <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn">← Back to Licenses</a>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:6px;padding:16px;margin-bottom:20px;">
            <strong style="color:#991b1b;">Please fix the following errors:</strong>
            <ul style="margin:8px 0 0 0;padding-left:20px;color:#b91c1c;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Main Form -->
        <div>
            <form action="{{ route('business-licenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="license_direction" value="{{ $direction }}">

                <!-- Basic Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📋 Basic Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label class="form-label">License Name *</label>
                            <input type="text" name="license_name" value="{{ old('license_name') }}" required
                                   class="form-input" placeholder="e.g., Business Operating License">
                            @error('license_name') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">License Number *</label>
                            <input type="text" name="license_number" value="{{ old('license_number') }}" required
                                   class="form-input" placeholder="e.g., BL-2024-001234">
                            @error('license_number') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label class="form-label">License Type *</label>
                            <input type="text" name="license_type" value="{{ old('license_type') }}" required
                                   class="form-input" placeholder="e.g., Trading License">
                            @error('license_type') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Status *</label>
                            <select name="status" required class="form-input">
                                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ old('status', 'active') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label class="form-label">Issuing Authority *</label>
                        <input type="text" name="issuing_authority" value="{{ old('issuing_authority') }}" required
                               class="form-input" placeholder="e.g., Department of Commerce">
                        @error('issuing_authority') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-input"
                                  placeholder="Brief description of the license and its purpose...">{{ old('description') }}</textarea>
                        @error('description') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Dates -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📅 Dates</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="form-label">Issue Date *</label>
                            <input type="date" name="issue_date" value="{{ old('issue_date') }}" required class="form-input">
                            @error('issue_date') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-input">
                            <div style="font-size:12px; color:#666; margin-top:5px;">Leave blank if the license does not expire</div>
                            @error('expiry_date') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                @if($direction === 'company_held')
                    <!-- Company-Held Specific Fields -->
                    <div class="content-card" style="margin-block-end: 20px;">
                        <h4 style="margin-block-end: 20px; color: #333;">🏢 Company Details</h4>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                            <div>
                                <label class="form-label">Department *</label>
                                <select name="department_id" required class="form-input">
                                    <option value="">-- Select Department --</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Priority Level *</label>
                                <select name="priority_level" required class="form-input">
                                    <option value="">-- Select Priority --</option>
                                    @foreach(\App\Models\BusinessLicense::PRIORITY_LEVELS as $key => $label)
                                    <option value="{{ $key }}" {{ old('priority_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('priority_level') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                            <div>
                                <label class="form-label">Responsible Employee</label>
                                <select name="responsible_employee_id" class="form-input">
                                    <option value="">-- None --</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('responsible_employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('responsible_employee_id') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Location</label>
                                <input type="text" name="location" value="{{ old('location') }}" class="form-input" placeholder="e.g., Head Office">
                                @error('location') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                            <div>
                                <label class="form-label">Initial Cost ($)</label>
                                <input type="number" name="cost" value="{{ old('cost') }}" step="0.01" min="0" class="form-input" placeholder="0.00">
                                @error('cost') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Renewal Cost ($)</label>
                                <input type="number" name="renewal_cost" value="{{ old('renewal_cost') }}" step="0.01" min="0" class="form-input" placeholder="0.00">
                                @error('renewal_cost') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div style="margin-block-end: 20px;">
                            <label class="form-label">Regulatory Body</label>
                            <input type="text" name="regulatory_body" value="{{ old('regulatory_body') }}" class="form-input" placeholder="e.g., Financial Services Authority">
                            @error('regulatory_body') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div style="margin-block-end: 20px;">
                            <label class="form-label">Business Impact</label>
                            <textarea name="business_impact" rows="2" class="form-input" placeholder="What business operations does this license enable?">{{ old('business_impact') }}</textarea>
                            @error('business_impact') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div style="margin-block-end: 20px;">
                            <label class="form-label">License Conditions</label>
                            <textarea name="license_conditions" rows="2" class="form-input" placeholder="Any conditions or restrictions on this license...">{{ old('license_conditions') }}</textarea>
                            @error('license_conditions') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Compliance Notes</label>
                            <textarea name="compliance_notes" rows="2" class="form-input" placeholder="Internal compliance notes...">{{ old('compliance_notes') }}</textarea>
                            @error('compliance_notes') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @else
                    <!-- Customer-Issued Specific Fields -->
                    <div class="content-card" style="margin-block-end: 20px;">
                        <h4 style="margin-block-end: 20px; color: #333;">👤 Customer Information</h4>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                            <div>
                                <label class="form-label">Customer Name *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="form-input">
                                @error('customer_name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Customer Email *</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="form-input">
                                @error('customer_email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                            <div>
                                <label class="form-label">Customer Company</label>
                                <input type="text" name="customer_company" value="{{ old('customer_company') }}" class="form-input">
                                @error('customer_company') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Customer Phone</label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="form-input">
                                @error('customer_phone') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Customer Address</label>
                            <textarea name="customer_address" rows="2" class="form-input">{{ old('customer_address') }}</textarea>
                            @error('customer_address') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="content-card" style="margin-block-end: 20px;">
                        <h4 style="margin-block-end: 20px; color: #333;">💰 License & Billing</h4>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                            <div>
                                <label class="form-label">Revenue Amount *</label>
                                <input type="number" name="revenue_amount" value="{{ old('revenue_amount') }}" required step="0.01" min="0" class="form-input" placeholder="0.00">
                                @error('revenue_amount') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Billing Cycle *</label>
                                <select name="billing_cycle" required class="form-input">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Models\BusinessLicense::BILLING_CYCLES as $key => $label)
                                    <option value="{{ $key }}" {{ old('billing_cycle') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('billing_cycle') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                            <div>
                                <label class="form-label">Support Level *</label>
                                <select name="support_level" required class="form-input">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Models\BusinessLicense::SUPPORT_LEVELS as $key => $label)
                                    <option value="{{ $key }}" {{ old('support_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('support_level') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">License Quantity</label>
                                <input type="number" name="license_quantity" value="{{ old('license_quantity', 1) }}" min="1" class="form-input">
                                @error('license_quantity') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                            <div>
                                <label class="form-label">Service Start Date</label>
                                <input type="date" name="service_start_date" value="{{ old('service_start_date') }}" class="form-input">
                                @error('service_start_date') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Customer Reference</label>
                                <input type="text" name="customer_reference" value="{{ old('customer_reference') }}" class="form-input">
                                @error('customer_reference') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div style="margin-block-end: 20px;">
                            <label class="form-label">Usage Limit</label>
                            <input type="text" name="usage_limit" value="{{ old('usage_limit') }}" class="form-input" placeholder="e.g., 5 users, unlimited">
                            @error('usage_limit') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">License Terms</label>
                            <textarea name="license_terms" rows="3" class="form-input" placeholder="Terms and conditions for the license...">{{ old('license_terms') }}</textarea>
                            @error('license_terms') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endif

                <!-- Attachments & Reminders -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📂 Attachments & Reminders</h4>

                    <div style="margin-block-end: 20px;">
                        <label class="form-label">License Document</label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="form-input">
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">PDF, DOC, DOCX, JPG, PNG — max 2MB</div>
                        @error('document') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div style="margin-block-end: 20px;">
                        <label class="form-label">Renewal Reminder (Days Before Expiry)</label>
                        <input type="number" name="renewal_reminder_days" value="{{ old('renewal_reminder_days', 15) }}" min="1" max="365" class="form-input">
                        @error('renewal_reminder_days') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    @if($direction === 'company_held')
                    <div>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="auto_renewal" value="1" {{ old('auto_renewal') ? 'checked' : '' }}>
                            <span style="font-weight: 500;">Enable Auto-Renewal Notifications</span>
                        </label>
                        @error('auto_renewal') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    @else
                    <div>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="auto_renewal_customer" value="1" {{ old('auto_renewal_customer') ? 'checked' : '' }}>
                            <span style="font-weight: 500;">Enable Auto-Renewal</span>
                        </label>
                        @error('auto_renewal_customer') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    @endif
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create License</button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="content-card" style="margin-block-end: 20px;">
                <h4 style="margin-block-end: 15px; color: #333;">💡 Tips</h4>
                <ul style="margin: 0; padding-inline-start: 20px; color: #666; line-height: 1.6;">
                    @if($direction === 'company_held')
                        <li>Ensure license numbers are unique and identifiable</li>
                        <li>Assign a department and responsible employee</li>
                        <li>Set priority level for compliance tracking</li>
                        <li>Upload a clear copy of the license document</li>
                        <li>Set renewal reminders to avoid expiry lapses</li>
                    @else
                        <li>Ensure customer details are accurate for communication</li>
                        <li>Set the correct billing cycle and revenue amount</li>
                        <li>Define support level clearly for SLA tracking</li>
                        <li>Document license terms and usage limits</li>
                        <li>Enable auto-renewal to avoid service interruptions</li>
                    @endif
                </ul>
            </div>

            <div class="content-card">
                <h4 style="margin-block-end: 10px; color: #333;">📌 License Type</h4>
                <p style="color:#666; font-size:14px; margin:0;">
                    @if($direction === 'company_held')
                        You are adding a <strong>Company-Held</strong> license owned by the company.
                    @else
                        You are adding a <strong>Customer-Issued</strong> license for a customer.
                    @endif
                </p>
                <a href="{{ route('business-licenses.create', ['direction' => $direction === 'company_held' ? 'customer_issued' : 'company_held']) }}"
                   style="display:inline-block;margin-top:10px;font-size:13px;color:#2196f3;text-decoration:none;">
                    Switch to {{ $direction === 'company_held' ? 'Customer License' : 'Company License' }} →
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.form-label { display:block; margin-bottom:5px; font-weight:500; color:#333; }
.form-input { width:100%; padding:8px; border:2px solid #ddd; border-radius:4px; box-sizing:border-box; }
.form-input:focus { border-color:#2196f3; outline:none; }
.form-error { color:#f44336; font-size:12px; margin-top:5px; }
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
    display: inline-block;
}
.btn-primary { background:#2196f3; color:white; border-color:#2196f3; }
.btn-primary:hover { background:#1976d2; border-color:#1976d2; }
</style>
@endsection
