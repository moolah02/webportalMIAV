@extends('layouts.app')
@section('title', 'Edit Client')

@section('header-actions')
<a href="{{ route('clients.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to List</a>
@endsection

@push('styles')
<style>
.ce-wrap { display: flex; flex-direction: column; gap: 16px; max-width: 980px; }
.ce-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; padding: 16px 18px; }
.ce-grid .ce-full { grid-column: 1 / -1; }
@media (max-width: 640px) { .ce-grid { grid-template-columns: 1fr; } }
.ce-grid .ui-input, .ce-grid .ui-select { width: 100%; }
.ce-req { color: var(--mv-crit); }
.ce-error { font-size: 12px; color: var(--mv-crit); margin-top: 5px; }
.ce-mono { font-family: var(--mv-mono); }
.ce-actions { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 18px; }
</style>
@endpush

@section('content')
<form action="{{ route('clients.update', $client) }}" method="POST" class="ce-wrap">
    @csrf
    @method('PUT')

    <div class="ui-card">
        <div class="ui-card-header"><h3>Company Information</h3></div>
        <div class="ce-grid">
            <div>
                <label class="ui-label">Company Name <span class="ce-req">*</span></label>
                <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" required class="ui-input">
                @error('company_name')<div class="ce-error">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="ui-label">Client Code <span class="ce-req">*</span></label>
                <input type="text" name="client_code" value="{{ old('client_code', $client->client_code) }}" required class="ui-input ce-mono">
                @error('client_code')<div class="ce-error">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="ui-label">Status</label>
                <select name="status" class="ui-select">
                    <option value="active"   {{ old('status', $client->status) == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-header"><h3>Contact Information</h3></div>
        <div class="ce-grid">
            <div>
                <label class="ui-label">Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}" class="ui-input">
            </div>
            <div>
                <label class="ui-label">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" class="ui-input">
                @error('email')<div class="ce-error">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="ui-label">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="ui-input">
            </div>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-header"><h3>Location Information</h3></div>
        <div class="ce-grid">
            <div>
                <label class="ui-label">City</label>
                <input type="text" name="city" value="{{ old('city', $client->city) }}" class="ui-input">
            </div>
            <div>
                <label class="ui-label">Region</label>
                <select name="region" class="ui-select">
                    <option value="">Select Region</option>
                    @foreach(['North','South','East','West','Central'] as $r)
                    <option value="{{ $r }}" {{ old('region', $client->region) == $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ce-full">
                <label class="ui-label">Address</label>
                <textarea name="address" rows="3" class="ui-input">{{ old('address', $client->address) }}</textarea>
            </div>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-header"><h3>Contract Information</h3></div>
        <div class="ce-grid">
            <div>
                <label class="ui-label">Contract Start Date</label>
                <input type="date" name="contract_start_date" value="{{ old('contract_start_date', $client->contract_start_date?->format('Y-m-d')) }}" class="ui-input">
            </div>
            <div>
                <label class="ui-label">Contract End Date</label>
                <input type="date" name="contract_end_date" value="{{ old('contract_end_date', $client->contract_end_date?->format('Y-m-d')) }}" class="ui-input">
                @error('contract_end_date')<div class="ce-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="ui-card-footer ce-actions">
            <a href="{{ route('clients.show', $client) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Client</button>
        </div>
    </div>
</form>
@endsection
