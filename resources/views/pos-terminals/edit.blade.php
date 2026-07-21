{{-- resources/views/pos-terminals/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit POS Terminal')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div class="flex gap-2">
            <a href="{{ route('pos-terminals.show', $posTerminal) }}" class="btn-secondary">View Details</a>
            <a href="{{ route('pos-terminals.index') }}" class="btn-secondary">&#x2190; Back</a>
        </div>
    </div>

    @if($errors->any())
    <div class="flash-error mb-5">
        @foreach($errors->all() as $error)<div>&#x26A0; {{ $error }}</div>@endforeach
    </div>
    @endif

    <div class="ui-card">
        <div class="ui-card-body">
            <form action="{{ route('pos-terminals.update', $posTerminal) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Terminal Information --}}
                <div class="mb-8 pb-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">&#x1F5A5; Terminal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="ui-label">Terminal ID <span class="text-red-500">*</span></label>
                            <input type="text" name="terminal_id" value="{{ old('terminal_id', $posTerminal->terminal_id) }}" required class="ui-input">
                            @error('terminal_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Client/Bank <span class="text-red-500">*</span></label>
                            <select name="client_id" required class="ui-select">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $posTerminal->client_id) == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="ui-label">Status</label>
                            <select name="status" class="ui-select">
                                @foreach(['active'=>'Active','offline'=>'Offline','maintenance'=>'Under Maintenance','faulty'=>'Faulty','decommissioned'=>'Decommissioned'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('status', $posTerminal->status) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="ui-label">Terminal Model</label>
                            <select name="terminal_model" class="ui-select">
                                <option value="">Select Model</option>
                                @foreach(['Ingenico iWL220','Verifone VX520','PAX A920','Ingenico Move 5000'] as $model)
                                    <option value="{{ $model }}" {{ old('terminal_model', $posTerminal->terminal_model) == $model ? 'selected' : '' }}>{{ $model }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="ui-label">Serial Number</label>
                            <input type="text" name="serial_number" value="{{ old('serial_number', $posTerminal->serial_number) }}" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Installation Date</label>
                            <input type="date" name="installation_date" value="{{ old('installation_date', $posTerminal->installation_date ? $posTerminal->installation_date->format('Y-m-d') : '') }}" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Last Service Date</label>
                            <input type="date" name="last_service_date" value="{{ old('last_service_date', $posTerminal->last_service_date ? $posTerminal->last_service_date->format('Y-m-d') : '') }}" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Next Service Due</label>
                            <input type="date" name="next_service_due" value="{{ old('next_service_due', $posTerminal->next_service_due ? $posTerminal->next_service_due->format('Y-m-d') : '') }}" class="ui-input">
                        </div>
                    </div>
                </div>

                {{-- Merchant Information --}}
                <div class="mb-8 pb-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">&#x1F3EA; Merchant Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="ui-label">Merchant Name <span class="text-red-500">*</span></label>
                            <input type="text" name="merchant_name" value="{{ old('merchant_name', $posTerminal->merchant_name) }}" required class="ui-input">
                            @error('merchant_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ui-label">Contact Person</label>
                            <input type="text" name="merchant_contact_person" value="{{ old('merchant_contact_person', $posTerminal->merchant_contact_person) }}" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Phone Number</label>
                            <input type="text" name="merchant_phone" value="{{ old('merchant_phone', $posTerminal->merchant_phone) }}" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Email Address</label>
                            <input type="email" name="merchant_email" value="{{ old('merchant_email', $posTerminal->merchant_email) }}" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Business Type</label>
                            <select name="business_type" class="ui-select">
                                <option value="">Select Type</option>
                                @foreach(['Retail','Restaurant','Pharmacy','Electronics','Grocery','Other'] as $type)
                                    <option value="{{ $type }}" {{ old('business_type', $posTerminal->business_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
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
                                    <option value="{{ $reg }}" {{ old('region', $posTerminal->region) == $reg ? 'selected' : '' }}>{{ $reg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="ui-label">City</label>
                            <input type="text" name="city" value="{{ old('city', $posTerminal->city) }}" placeholder="e.g., Harare" class="ui-input">
                        </div>
                        <div>
                            <label class="ui-label">Province</label>
                            <input type="text" name="province" value="{{ old('province', $posTerminal->province) }}" placeholder="e.g., Harare Province" class="ui-input">
                        </div>
                    </div>
                    <div>
                        <label class="ui-label">Physical Address</label>
                        <textarea name="physical_address" rows="3" class="ui-input resize-y" placeholder="Full address...">{{ old('physical_address', $posTerminal->physical_address) }}</textarea>
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="mb-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">&#x1F4CB; Additional Information</h3>
                    <div>
                        <label class="ui-label">Contract Details</label>
                        <textarea name="contract_details" rows="3" class="ui-input resize-y">{{ old('contract_details', $posTerminal->contract_details) }}</textarea>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('pos-terminals.show', $posTerminal) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Update Terminal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection