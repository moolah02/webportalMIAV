@extends('layouts.app')
@section('title', 'Edit Client')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <a href="{{ route('clients.index') }}" class="btn-secondary btn-sm">&#8592; Back to List</a>
    </div>

    <div class="ui-card">
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="ui-card-body flex flex-col gap-6">

                {{-- Company Information --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Company Information</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">Company Name <span class="text-red-500">*</span></label>
                            <input type="text" name="company_name"
                                   value="{{ old('company_name', $client->company_name) }}"
                                   required class="ui-input w-full">
                            @error('company_name')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="ui-label">Client Code <span class="text-red-500">*</span></label>
                            <input type="text" name="client_code"
                                   value="{{ old('client_code', $client->client_code) }}"
                                   required class="ui-input w-full">
                            @error('client_code')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="ui-label">Status</label>
                            <select name="status" class="ui-select w-full">
                                <option value="active"   {{ old('status', $client->status) == 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Contact Information</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">Contact Person</label>
                            <input type="text" name="contact_person"
                                   value="{{ old('contact_person', $client->contact_person) }}"
                                   class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label">Email Address</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $client->email) }}"
                                   class="ui-input w-full">
                            @error('email')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="ui-label">Phone Number</label>
                            <input type="text" name="phone"
                                   value="{{ old('phone', $client->phone) }}"
                                   class="ui-input w-full">
                        </div>
                    </div>
                </div>

                {{-- Location Information --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Location Information</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">City</label>
                            <input type="text" name="city"
                                   value="{{ old('city', $client->city) }}"
                                   class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label">Region</label>
                            <select name="region" class="ui-select w-full">
                                <option value="">Select Region</option>
                                @foreach(['North','South','East','West','Central'] as $r)
                                <option value="{{ $r }}" {{ old('region', $client->region) == $r ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="ui-label">Address</label>
                        <textarea name="address" rows="3" class="ui-input w-full">{{ old('address', $client->address) }}</textarea>
                    </div>
                </div>

                {{-- Contract Information --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Contract Information</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ui-label">Contract Start Date</label>
                            <input type="date" name="contract_start_date"
                                   value="{{ old('contract_start_date', $client->contract_start_date?->format('Y-m-d')) }}"
                                   class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label">Contract End Date</label>
                            <input type="date" name="contract_end_date"
                                   value="{{ old('contract_end_date', $client->contract_end_date?->format('Y-m-d')) }}"
                                   class="ui-input w-full">
                            @error('contract_end_date')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('clients.show', $client) }}" class="btn-secondary btn-sm">Cancel</a>
                <button type="submit" class="btn-primary btn-sm">Update Client</button>
            </div>
        </form>
    </div>
</div>
@endsection