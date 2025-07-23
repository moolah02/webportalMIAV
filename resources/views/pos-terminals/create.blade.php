
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0;">Add New POS Terminal</h3>
            <p style="color: #666; margin: 5px 0 0 0;">Register a new terminal for client management</p>
        </div>
        <a href="{{ route('pos-terminals.index') }}" class="btn">← Back to Terminals</a>
    </div>

    <!-- Form -->
    <div class="content-card">
        <form action="{{ route('pos-terminals.store') }}" method="POST">
            @csrf
            
            <!-- Terminal Information -->
            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px; color: #333;">🖥️ Terminal Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Terminal ID *</label>
                        <input type="text" name="terminal_id" value="{{ old('terminal_id') }}" 
                               placeholder="e.g., POS-001" required
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('terminal_id')
                            <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Client/Bank *</label>
                        <select name="client_id" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Terminal Model</label>
                        <select name="terminal_model" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="">Select Model</option>
                            <option value="Ingenico iWL220" {{ old('terminal_model') == 'Ingenico iWL220' ? 'selected' : '' }}>Ingenico iWL220</option>
                            <option value="Verifone VX520" {{ old('terminal_model') == 'Verifone VX520' ? 'selected' : '' }}>Verifone VX520</option>
                            <option value="PAX A920" {{ old('terminal_model') == 'PAX A920' ? 'selected' : '' }}>PAX A920</option>
                            <option value="Ingenico Move 5000" {{ old('terminal_model') == 'Ingenico Move 5000' ? 'selected' : '' }}>Ingenico Move 5000</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}" 
                               placeholder="e.g., SN123456"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Installation Date</label>
                        <input type="date" name="installation_date" value="{{ old('installation_date') }}"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
            </div>

            <!-- Merchant Information -->
            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px; color: #333;">🏪 Merchant Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Merchant Name *</label>
                        <input type="text" name="merchant_name" value="{{ old('merchant_name') }}" 
                               placeholder="e.g., Green Valley Supermarket" required
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('merchant_name')
                            <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Contact Person</label>
                        <input type="text" name="merchant_contact_person" value="{{ old('merchant_contact_person') }}" 
                               placeholder="e.g., John Doe"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Phone Number</label>
                        <input type="text" name="merchant_phone" value="{{ old('merchant_phone') }}" 
                               placeholder="e.g., +254712345678"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Email Address</label>
                        <input type="email" name="merchant_email" value="{{ old('merchant_email') }}" 
                               placeholder="e.g., merchant@example.com"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Business Type</label>
                        <select name="business_type" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
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
            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px; color: #333;">📍 Location Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Region</label>
                        <select name="region" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="">Select Region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region }}" {{ old('region') == $region ? 'selected' : '' }}>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Area/Suburb</label>
                        <input type="text" name="area" value="{{ old('area') }}" 
                               placeholder="e.g., Westlands, CBD"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                
                <div style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Physical Address</label>
                    <textarea name="physical_address" rows="3" placeholder="Full physical address..."
                              style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('physical_address') }}</textarea>
                </div>
            </div>

            <!-- Additional Information -->
            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px; color: #333;">📋 Additional Information</h4>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Contract Details</label>
                    <textarea name="contract_details" rows="3" placeholder="Any contract or service agreement details..."
                              style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('contract_details') }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #eee;">
                <a href="{{ route('pos-terminals.index') }}" class="btn">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Terminal</button>
            </div>
        </form>
    </div>
</div>
@endsection