{{-- resources/views/pos-terminals/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add POS Terminal')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="page-title">Add New POS Terminal</h1>
            <p class="page-subtitle">Register a new terminal for client management</p>
        </div>
        <a href="{{ route('pos-terminals.index') }}" class="btn-secondary">&#x2190; Back to Terminals</a>
    </div>

    @if($errors->any())
    <div class="flash-error mb-5">
        @foreach($errors->all() as $error)<div>&#x26A0; {{ $error }}</div>@endforeach
    </div>
    @endif

    {{-- Form --}}
    <div class="ui-card">
        <div class="ui-card-body">
            <form action="{{ route('pos-terminals.store') }}" method="POST">
                @csrf

                {{-- Terminal Information --}}
                <div class="mb-8 pb-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">&#x1F5A5; Terminal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="ui-label">Terminal ID <span class="text-red-500">*</span></label>
                            <input type="text" name="terminal_id" value="{{ old('terminal_id') }}" placeholder="e.g., POS-001" required class="ui-input">
                            @error('terminal_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Client/Bank <span class="text-red-500">*</span></label>
                            <select name="client_id" required class="ui-select">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                                @endforeach
                            </select>
                            @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Terminal Model</label>
                            <select name="terminal_model" class="ui-select">
                                <option value="">Select Model</option>
                                @foreach(['Ingenico iWL220','Verifone VX520','PAX A920','Ingenico Move 5000'] as $model)
                                    <option value="{{ $model }}" {{ old('terminal_model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="ui-label">Serial Number</label>
                            <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="e.g., SN123456" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Installation Date</label>
                            <input type="date" name="installation_date" value="{{ old('installation_date') }}" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Status</label>
                            <select name="status" class="ui-select">
                                <option value="active"       {{ old('status','active') == 'active'       ? 'selected' : '' }}>Active</option>
                                <option value="offline"      {{ old('status') == 'offline'               ? 'selected' : '' }}>Offline</option>
                                <option value="maintenance"  {{ old('status') == 'maintenance'           ? 'selected' : '' }}>Under Maintenance</option>
                                <option value="faulty"       {{ old('status') == 'faulty'                ? 'selected' : '' }}>Faulty</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Merchant Information --}}
                <div class="mb-8 pb-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">&#x1F3EA; Merchant Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="ui-label">Merchant Name <span class="text-red-500">*</span></label>
                            <input type="text" name="merchant_name" value="{{ old('merchant_name') }}" placeholder="e.g., Green Valley Supermarket" required class="ui-input">
                            @error('merchant_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Contact Person</label>
                            <input type="text" name="merchant_contact_person" value="{{ old('merchant_contact_person') }}" placeholder="e.g., John Doe" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Phone Number</label>
                            <input type="text" name="merchant_phone" value="{{ old('merchant_phone') }}" placeholder="e.g., +254712345678" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Email Address</label>
                            <input type="email" name="merchant_email" value="{{ old('merchant_email') }}" placeholder="e.g., merchant@example.com" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Business Type</label>
                            <select name="business_type" class="ui-select">
                                <option value="">Select Type</option>
                                @foreach(['Retail','Restaurant','Pharmacy','Electronics','Grocery','Other'] as $type)
                                    <option value="{{ $type }}" {{ old('business_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Location Information --}}
                <div class="mb-8 pb-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">&#x1F4CD; Location Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                        <div>
                            <label class="ui-label">Region</label>
                            <select name="region" class="ui-select">
                                <option value="">Select Region</option>
                                @foreach($regions as $reg)
                                    <option value="{{ $reg }}" {{ old('region') == $reg ? 'selected' : '' }}>{{ $reg }}</option>
                                @endforeach
                            </select>
                            @error('region')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">City</label>
                            <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g., Harare" class="ui-input">
                            @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Province</label>
                            <input type="text" name="province" value="{{ old('province') }}" placeholder="e.g., Harare Province" class="ui-input">
                            @error('province')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="ui-label">Physical Address</label>
                        <textarea name="physical_address" rows="3" class="ui-input resize-y" placeholder="Full address...">{{ old('physical_address') }}</textarea>
                        @error('physical_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="mb-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">&#x1F4CB; Additional Information</h3>
                    <div>
                        <label class="ui-label">Contract Details</label>
                        <textarea name="contract_details" rows="4" class="ui-input resize-y" placeholder="Any contract or service agreement details...">{{ old('contract_details') }}</textarea>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('pos-terminals.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Create Terminal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection