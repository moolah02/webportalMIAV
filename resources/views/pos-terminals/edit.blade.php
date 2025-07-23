@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0;">Edit Terminal {{ $posTerminal->terminal_id }}</h3>
            <p style="color: #666; margin: 5px 0 0 0;">Update terminal information and settings</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('pos-terminals.show', $posTerminal) }}" class="btn">View Details</a>
            <a href="{{ route('pos-terminals.index') }}" class="btn">← Back to List</a>
        </div>
    </div>

    <!-- Form -->
    <div class="content-card">
        <form action="{{ route('pos-terminals.update', $posTerminal) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Terminal Information -->
            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px; color: #333;">🖥️ Terminal Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Terminal ID *</label>
                        <input type="text" name="terminal_id" value="{{ old('terminal_id', $posTerminal->terminal_id) }}" 
                               required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('terminal_id')
                            <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Client/Bank *</label>
                        <select name="client_id" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" 
                                        {{ old('client_id', $posTerminal->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Status</label>
                        <select name="status" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="active" {{ old('status', $posTerminal->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="offline" {{ old('status', $posTerminal->status) == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="maintenance" {{ old('status', $posTerminal->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="faulty" {{ old('status', $posTerminal->status) == 'faulty' ? 'selected' : '' }}>Faulty</option>
                            <option value="decommissioned" {{ old('status', $posTerminal->status) == 'decommissioned' ? 'selected' : '' }}>Decommissioned</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Terminal Model</label>
                        <select name="terminal_model" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="">Select Model</option>
                            <option value="Ingenico iWL220" {{ old('terminal_model', $posTerminal->terminal_model) == 'Ingenico iWL220' ? 'selected' : '' }}>Ingenico iWL220</option>
                            <option value="Verifone VX520" {{ old('terminal_model', $posTerminal->terminal_model) == 'Verifone VX520' ? 'selected' : '' }}>Verifone VX520</option>
                            <option value="PAX A920" {{ old('terminal_model', $posTerminal->terminal_model) == 'PAX A920' ? 'selected' : '' }}>PAX A920</option>
                            <option value="Ingenico Move 5000" {{ old('terminal_model', $posTerminal->terminal_model) == 'Ingenico Move 5000' ? 'selected' : '' }}>Ingenico Move 5000</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $posTerminal->serial_number) }}"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Installation Date</label>
                        <input type="date" name="installation_date" 
                               value="{{ old('installation_date', $posTerminal->installation_date?->format('Y-m-d')) }}"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Last Service Date</label>
                        <input type="date" name="last_service_date" 
                               value="{{ old('last_service_date', $posTerminal->last_service_date?->format('Y-m-d')) }}"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Next Service Due</label>
                        <input type="date" name="next_service_due" 
                               value="{{ old('next_service_due', $posTerminal->next_service_due?->format('Y-m-d')) }}"
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
                        <input type="text" name="merchant_name" value="{{ old('merchant_name', $posTerminal->merchant_name) }}" 
                               required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Contact Person</label>
                        <input type="text" name="merchant_contact_person" value="{{ old('merchant_contact_person', $posTerminal->merchant_contact_person) }}"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Phone Number</label>
                        <input type="text" name="merchant_phone" value="{{ old('merchant_phone', $posTerminal->merchant_phone) }}"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Email Address</label>
                        <input type="email" name="merchant_email" value="{{ old('merchant_email', $posTerminal->merchant_email) }}"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Business Type</label>
                        <select name="business_type" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="">Select Type</option>
                            <option value="Retail" {{ old('business_type', $posTerminal->business_type) == 'Retail' ? 'selected' : '' }}>Retail</option>
                            <option value="Restaurant" {{ old('business_type', $posTerminal->business_type) == 'Restaurant' ? 'selected' : '' }}>Restaurant</option>
                            <option value="Pharmacy" {{ old('business_type', $posTerminal->business_type) == 'Pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                            <option value="Electronics" {{ old('business_type', $posTerminal->business_type) == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                            <option value="Grocery" {{ old('business_type', $posTerminal->business_type) == 'Grocery' ? 'selected' : '' }}>Grocery</option>
                            <option value="Other" {{ old('business_type', $posTerminal->business_type) == 'Other' ? 'selected' : '' }}>Other</option>
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
                                <option value="{{ $region }}" {{ old('region', $posTerminal->region) == $region ? 'selected' : '' }}>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Area/Suburb</label>
                        <input type="text" name="area" value="{{ old('area', $posTerminal->area) }}"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                
                <div style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Physical Address</label>
                    <textarea name="physical_address" rows="3"
                              style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('physical_address', $posTerminal->physical_address) }}</textarea>
                </div>
            </div>

            <!-- Additional Information -->
            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px; color: #333;">📋 Additional Information</h4>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Contract Details</label>
                    <textarea name="contract_details" rows="3"
                              style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('contract_details', $posTerminal->contract_details) }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #eee;">
                <a href="{{ route('pos-terminals.show', $posTerminal) }}" class="btn">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Terminal</button>
            </div>
        </form>
    </div>
</div>
@endsection
