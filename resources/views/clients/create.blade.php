@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="page-title">Add New Client</h2>
            <p class="page-subtitle mt-1">Add a new client to your business</p>
        </div>
        <a href="{{ route('clients.index') }}" class="btn-secondary btn-sm">&#8592; Back to Clients</a>
    </div>

    {{-- Flash messages --}}
    @if(session('error'))
        <div class="flash-error mb-4">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="flash-success mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="flash-error mb-4">
            <strong>Validation Errors:</strong>
            <ul class="mt-2 ml-4 list-disc text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clients.store') }}" method="POST" id="clientForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Main form (2/3) --}}
            <div class="lg:col-span-2 flex flex-col gap-5">

                {{-- Company Information --}}
                <div class="ui-card">
                    <div class="ui-card-header">
                        <h4 class="text-sm font-semibold text-gray-800 m-0">Company Information</h4>
                    </div>
                    <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">Company Name <span class="text-red-500">*</span></label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                   placeholder="e.g., Acme Corporation" class="ui-input w-full">
                            @error('company_name')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="ui-label">Contact Person <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}" required
                                   placeholder="e.g., John Smith" class="ui-input w-full">
                            @error('contact_person')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="ui-label">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="john@example.com" class="ui-input w-full">
                            @error('email')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="ui-label">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   placeholder="+1 (555) 123-4567" class="ui-input w-full">
                            @error('phone')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Address Information --}}
                <div class="ui-card">
                    <div class="ui-card-header">
                        <h4 class="text-sm font-semibold text-gray-800 m-0">Address Information</h4>
                    </div>
                    <div class="ui-card-body flex flex-col gap-4">
                        <div>
                            <label class="ui-label">Address</label>
                            <textarea name="address" rows="3" placeholder="123 Main Street, Suite 100"
                                      class="ui-input w-full">{{ old('address') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="ui-label">City</label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                       placeholder="New York" class="ui-input w-full">
                            </div>
                            <div>
                                <label class="ui-label">Region</label>
                                <input type="text" name="region" value="{{ old('region') }}"
                                       placeholder="e.g., North America" class="ui-input w-full">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contract Information --}}
                <div class="ui-card">
                    <div class="ui-card-header">
                        <h4 class="text-sm font-semibold text-gray-800 m-0">Contract Information</h4>
                    </div>
                    <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">Contract Start Date</label>
                            <input type="date" name="contract_start_date" value="{{ old('contract_start_date') }}"
                                   class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label">Contract End Date</label>
                            <input type="date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                                   class="ui-input w-full">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar (1/3) --}}
            <div class="flex flex-col gap-5">

                {{-- Status --}}
                <div class="ui-card">
                    <div class="ui-card-header">
                        <h4 class="text-sm font-semibold text-gray-800 m-0">Client Status</h4>
                    </div>
                    <div class="ui-card-body">
                        <label class="ui-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="ui-select w-full">
                            <option value="prospect" {{ old('status', 'prospect') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                            <option value="active"   {{ old('status') == 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="lost"     {{ old('status') == 'lost'     ? 'selected' : '' }}>Lost</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1.5">Set the current relationship status</p>
                    </div>
                </div>

                {{-- Client Code --}}
                <div class="ui-card">
                    <div class="ui-card-header">
                        <h4 class="text-sm font-semibold text-gray-800 m-0">Client Code</h4>
                    </div>
                    <div class="ui-card-body">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-xs text-gray-500 mb-1">Auto-generated</div>
                            <div class="text-sm font-semibold text-[#1a3a5c]" id="codePreview">Will be created automatically</div>
                            <div class="text-xs text-gray-400 mt-1">Based on company name</div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="ui-card">
                    <div class="ui-card-body flex flex-col gap-2.5">
                        <button type="submit" id="submitBtn" class="btn-primary w-full justify-center">
                            Add Client
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn-secondary w-full text-center">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companyNameInput = document.querySelector('input[name="company_name"]');
    const codePreview = document.getElementById('codePreview');
    if (companyNameInput && codePreview) {
        companyNameInput.addEventListener('input', function() {
            const prefix = this.value.replace(/[^A-Za-z]/g, '').substring(0, 3).toUpperCase();
            codePreview.textContent = prefix ? prefix + 'XXXX' : 'Will be created automatically';
        });
    }
    document.getElementById('clientForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.textContent = 'Creating Client...';
        btn.disabled = true;
    });
});
</script>
@endsection