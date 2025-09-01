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
                            <label class="form-label">License Name *</label>
                            <input type="text" name="license_name" value="{{ old('license_name') }}" required
                                   class="form-input"
                                   placeholder="e.g., Business Operating License">
                            @error('license_name') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">License Number *</label>
                            <input type="text" name="license_number" value="{{ old('license_number') }}" required
                                   class="form-input"
                                   placeholder="e.g., BL-2024-001234">
                            @error('license_number') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label class="form-label">License Type *</label>
                            <input type="text" name="license_type" value="{{ old('license_type') }}" required
                                   class="form-input"
                                   placeholder="e.g., Trading License">
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
                               class="form-input"
                               placeholder="e.g., Department of Commerce">
                        @error('issuing_authority') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-input"
                                  placeholder="Brief description of the license and its purpose...">{{ old('description') }}</textarea>
                        @error('description') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Dates and Financial -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📅 Dates & Financial Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="form-label">Initial Cost ($)</label>
                            <input type="number" name="cost" value="{{ old('cost') }}" step="0.01" min="0"
                                   class="form-input" placeholder="0.00">
                            @error('cost') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Renewal Cost ($)</label>
                            <input type="number" name="renewal_cost" value="{{ old('renewal_cost') }}" step="0.01" min="0"
                                   class="form-input" placeholder="0.00">
                            @error('renewal_cost') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">👤 Customer Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label class="form-label">Customer Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="form-input" required>
                            @error('customer_name') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Customer Email *</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="form-input" required>
                            @error('customer_email') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label class="form-label">Customer Phone</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="form-input">
                            @error('customer_phone') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Customer Address</label>
                            <textarea name="customer_address" rows="3" class="form-input">{{ old('customer_address') }}</textarea>
                            @error('customer_address') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Upload Document -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📂 Attachments</h4>

                    <div style="margin-block-end: 20px;">
                        <label class="form-label">License Document</label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="form-input">
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">
                            Upload the license document (PDF, DOC, DOCX, JPG, PNG - Max 2MB)
                        </div>
                        @error('document') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div style="margin-block-end:20px;">
                        <label class="form-label">Renewal Reminder (Days)</label>
                        <input type="number" name="renewal_reminder_days" value="{{ old('renewal_reminder_days', 15) }}" min="1" max="365"
                               class="form-input">
                        <div style="font-size:12px; color:#666; margin-top:5px;">Default is 15 days before expiry</div>
                        @error('renewal_reminder_days') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="auto_renewal" value="1" {{ old('auto_renewal') ? 'checked' : '' }}>
                            <span style="font-weight: 500;">Enable Auto-Renewal Notifications</span>
                        </label>
                        @error('auto_renewal') <div class="form-error">{{ $message }}</div> @enderror
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
                    <li>Use descriptive license types for easier reporting</li>
                    <li>Upload clear copies of license documents</li>
                    <li>Keep customer details accurate for communication</li>
                    <li>Enable auto-renewal notifications to avoid expiry</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.form-label { display:block; margin-bottom:5px; font-weight:500; color:#333; }
.form-input { width:100%; padding:8px; border:2px solid #ddd; border-radius:4px; }
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
    transition: all 0.2s ease;
    display: inline-block;
}
.btn:hover { border-color:#2196f3; color:#2196f3; }
.btn-primary { background:#2196f3; color:white; border-color:#2196f3; }
.btn-primary:hover { background:#1976d2; border-color:#1976d2; }
</style>
@endsection
