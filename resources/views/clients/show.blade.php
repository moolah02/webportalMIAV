@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">👥 Add New Client</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Add a new client to your business</p>
        </div>
        <a href="{{ route('clients.index') }}" class="btn">← Back to Clients</a>
    </div>

    <form action="{{ route('clients.store') }}" method="POST" id="clientForm">
        @csrf
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Main Form -->
            <div>
                <!-- Basic Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">🏢 Company Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-block-end: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Company Name *</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                   placeholder="e.g., Acme Corporation"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('company_name')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Contact Person *</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}" required
                                   placeholder="e.g., John Smith"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('contact_person')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="john@example.com"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('email')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   placeholder="+1 (555) 123-4567"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            @error('phone')
                                <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 20px; color: #333;">📍 Address Information</h4>
                    
                    <div style="margin-block-end: 20px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Address</label>
                        <textarea name="address" rows="3" placeholder="123 Main Street, Suite 100"
                                  style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">{{ old('address') }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">City</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                   placeholder="New York"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Region</label>
                            <input type="text" name="region" value="{{ old('region') }}"
                                   placeholder="e.g., North America, Europe"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <!-- Contract Information -->
                <div class="content-card">
                    <h4 style="margin-block-end: 20px; color: #333;">📋 Contract Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Contract Start Date</label>
                            <input type="date" name="contract_start_date" value="{{ old('contract_start_date') }}"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 500;">Contract End Date</label>
                            <input type="date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Sidebar -->
            <div>
                <!-- Client Status -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">⚙️ Client Status</h4>
                    
                    <div style="margin-block-end: 15px;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 500;">Status *</label>
                        <select name="status" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                            <option value="prospect" {{ old('status', 'prospect') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Set the current relationship status</div>
                    </div>
                </div>

                <!-- Client Code Info -->
                <div class="content-card" style="margin-block-end: 20px;">
                    <h4 style="margin-block-end: 15px; color: #333;">🔖 Client Code</h4>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; text-align: center;">
                        <div style="font-size: 12px; color: #666; margin-block-end: 5px;">Auto-generated</div>
                        <div style="font-weight: bold; color: #2196f3;">Will be created automatically</div>
                        <div style="font-size: 12px; color: #666; margin-block-start: 5px;">Based on company name</div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="content-card">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="inline-size: 100%; padding: 15px;">
                            🎉 Add Client
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn" style="inline-size: 100%; text-align: center;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
    color: white;
}
</style>

<script>
// Form validation and UX improvements
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('clientForm');
    
    // Auto-generate preview of client code based on company name
    const companyNameInput = document.querySelector('input[name="company_name"]');
    if (companyNameInput) {
        companyNameInput.addEventListener('input', function() {
            const companyName = this.value;
            const prefix = companyName.replace(/[^A-Za-z]/g, '').substring(0, 3).toUpperCase();
            const preview = prefix ? prefix + 'XXXX' : 'Will be created automatically';
            
            const codeDisplay = document.querySelector('.content-card:nth-child(2) .font-weight-bold');
            if (codeDisplay) {
                codeDisplay.textContent = preview;
            }
        });
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const companyName = document.querySelector('input[name="company_name"]').value;
        const contactPerson = document.querySelector('input[name="contact_person"]').value;
        const email = document.querySelector('input[name="email"]').value;
        
        if (!companyName || !contactPerson || !email) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return;
        }
        
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '⏳ Creating Client...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection
