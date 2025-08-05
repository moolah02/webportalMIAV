@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 20px;">
        <div>
            <h3 style="margin: 0;">Edit {{ $client->company_name }}</h3>
            <p style="color: #666; margin: 5px 0 0 0;">Update client information and settings</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('clients.show', $client) }}" class="btn">View Details</a>
            <a href="{{ route('clients.index') }}" class="btn">← Back to List</a>
        </div>
    </div>

    <!-- Form -->
    <div class="content-card">
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Company Information -->
            <div style="margin-block-end: 30px;">
                <h4 style="margin-block-end: 15px; color: #333;">🏢 Company Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Company Name *</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" 
                               required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('company_name')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Client Code *</label>
                        <input type="text" name="client_code" value="{{ old('client_code', $client->client_code) }}" 
                               required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('client_code')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Status</label>
                        <select name="status" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div style="margin-block-end: 30px;">
                <h4 style="margin-block-end: 15px; color: #333;">👤 Contact Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $client->email) }}"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('email')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div style="margin-block-end: 30px;">
                <h4 style="margin-block-end: 15px; color: #333;">📍 Location Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">City</label>
                        <input type="text" name="city" value="{{ old('city', $client->city) }}"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Region</label>
                        <select name="region" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="">Select Region</option>
                            <option value="North" {{ old('region', $client->region) == 'North' ? 'selected' : '' }}>North</option>
                            <option value="South" {{ old('region', $client->region) == 'South' ? 'selected' : '' }}>South</option>
                            <option value="East" {{ old('region', $client->region) == 'East' ? 'selected' : '' }}>East</option>
                            <option value="West" {{ old('region', $client->region) == 'West' ? 'selected' : '' }}>West</option>
                            <option value="Central" {{ old('region', $client->region) == 'Central' ? 'selected' : '' }}>Central</option>
                        </select>
                    </div>
                </div>
                
                <div style="margin-block-start: 15px;">
                    <label style="display: block; margin-block-end: 5px; font-weight: 500;">Address</label>
                    <textarea name="address" rows="3"
                              style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('address', $client->address) }}</textarea>
                </div>
            </div>

            <!-- Contract Information -->
            <div style="margin-block-end: 30px;">
                <h4 style="margin-block-end: 15px; color: #333;">📋 Contract Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Contract Start Date</label>
                        <input type="date" name="contract_start_date" 
                               value="{{ old('contract_start_date', $client->contract_start_date?->format('Y-m-d')) }}"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Contract End Date</label>
                        <input type="date" name="contract_end_date" 
                               value="{{ old('contract_end_date', $client->contract_end_date?->format('Y-m-d')) }}"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        @error('contract_end_date')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 20px; border-block-start: 1px solid #eee;">
                <a href="{{ route('clients.show', $client) }}" class="btn">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Client</button>
            </div>
        </form>
    </div>
</div>
@endsection