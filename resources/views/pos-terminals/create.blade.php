@extends('layouts.app')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 24px;">Add New POS Terminal</h2>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">Register a new terminal for client management</p>
        </div>
        <a href="{{ route('pos-terminals.index') }}" class="btn">← Back to Terminals</a>
    </div>

    <!-- Form -->
    <div class="main-card">
        <form action="{{ route('pos-terminals.store') }}" method="POST" class="terminal-form">
            @csrf
            
            <!-- Terminal Information -->
            <div class="form-section">
                <h3 class="section-title">🖥️ Terminal Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">Terminal ID</label>
                        <input type="text" 
                               name="terminal_id" 
                               value="{{ old('terminal_id') }}" 
                               placeholder="e.g., POS-001" 
                               required
                               class="form-input">
                        @error('terminal_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Client/Bank</label>
                        <select name="client_id" required class="form-select">
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Terminal Model</label>
                        <select name="terminal_model" class="form-select">
                            <option value="">Select Model</option>
                            <option value="Ingenico iWL220" {{ old('terminal_model') == 'Ingenico iWL220' ? 'selected' : '' }}>Ingenico iWL220</option>
                            <option value="Verifone VX520" {{ old('terminal_model') == 'Verifone VX520' ? 'selected' : '' }}>Verifone VX520</option>
                            <option value="PAX A920" {{ old('terminal_model') == 'PAX A920' ? 'selected' : '' }}>PAX A920</option>
                            <option value="Ingenico Move 5000" {{ old('terminal_model') == 'Ingenico Move 5000' ? 'selected' : '' }}>Ingenico Move 5000</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Serial Number</label>
                        <input type="text" 
                               name="serial_number" 
                               value="{{ old('serial_number') }}" 
                               placeholder="e.g., SN123456"
                               class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Installation Date</label>
                        <input type="date" 
                               name="installation_date" 
                               value="{{ old('installation_date') }}"
                               class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="faulty" {{ old('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Merchant Information -->
            <div class="form-section">
                <h3 class="section-title">🏪 Merchant Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">Merchant Name</label>
                        <input type="text" 
                               name="merchant_name" 
                               value="{{ old('merchant_name') }}" 
                               placeholder="e.g., Green Valley Supermarket" 
                               required
                               class="form-input">
                        @error('merchant_name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" 
                               name="merchant_contact_person" 
                               value="{{ old('merchant_contact_person') }}" 
                               placeholder="e.g., John Doe"
                               class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" 
                               name="merchant_phone" 
                               value="{{ old('merchant_phone') }}" 
                               placeholder="e.g., +254712345678"
                               class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" 
                               name="merchant_email" 
                               value="{{ old('merchant_email') }}" 
                               placeholder="e.g., merchant@example.com"
                               class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Business Type</label>
                        <select name="business_type" class="form-select">
                            <option value="">Select Type</option>
                            <option value="Retail" {{ old('business_type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                            <option value="Restaurant" {{ old('business_type') == 'Restaurant' ? 'selected' : '' }}>Restaurant</option>
                            <option value="Pharmacy" {{ old('business_type') == 'Pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                            <option value="Electronics" {{ old('business_type') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                            <option value="Grocery" {{ old('business_type') == 'Grocery' ? 'selected' : '' }}>Grocery</option>
                            <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h3 class="section-title">📍 Location Information</h3>
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Region</label>
                        <select name="region" class="form-select">
                            <option value="">Select Region</option>
                            @foreach($regions as $reg)
                                <option value="{{ $reg }}" {{ old('region') == $reg ? 'selected' : '' }}>
                                    {{ $reg }}
                                </option>
                            @endforeach
                        </select>
                        @error('region')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text"
                               name="city"
                               value="{{ old('city') }}"
                               placeholder="e.g., Harare"
                               class="form-input">
                        @error('city')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Province</label>
                        <input type="text"
                               name="province"
                               value="{{ old('province') }}"
                               placeholder="e.g., Harare Province"
                               class="form-input">
                        @error('province')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Physical Address</label>
                    <textarea name="physical_address"
                              rows="3"
                              class="form-textarea"
                              placeholder="Full address...">{{ old('physical_address') }}</textarea>
                    @error('physical_address')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <h3 class="section-title">📋 Additional Information</h3>
                <div class="form-group">
                    <label class="form-label">Contract Details</label>
                    <textarea name="contract_details" 
                              rows="4" 
                              placeholder="Any contract or service agreement details..."
                              class="form-textarea">{{ old('contract_details') }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="form-actions">
                <a href="{{ route('pos-terminals.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Terminal</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Professional Form Styling */
.main-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.terminal-form {
    max-width: none;
}

.form-section {
    margin-block-end: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #f0f0f0;
}

.form-section:last-of-type {
    border-bottom: none;
    margin-block-end: 0;
    padding-bottom: 0;
}

.section-title {
    margin: 0 0 24px 0;
    color: #333;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.form-grid-3 {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    margin-block-end: 8px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.form-input,
.form-select,
.form-textarea {
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: white;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.form-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    gap: 16px;
    justify-content: flex-end;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #f0f0f0;
}

.btn {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid;
    min-width: 120px;
}

.btn-primary {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
    transform: translateY(-1px);
    text-decoration: none;
}

.btn-outline {
    background: white;
    border-color: #dee2e6;
    color: #333;
}

.btn-outline:hover {
    background: #f8f9fa;
    border-color: #007bff;
    text-decoration: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-card {
        padding: 24px;
        margin: 0 16px;
    }
    
    .form-grid,
    .form-grid-3 {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}

/* Focus states for accessibility */
.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* Invalid states */
.form-input:invalid,
.form-select:invalid {
    border-color: #dc3545;
}

/* Placeholder styling */
.form-input::placeholder,
.form-textarea::placeholder {
    color: #6c757d;
    opacity: 1;
}
</style>
@endsection