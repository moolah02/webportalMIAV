@extends('layouts.app')
@section('title', 'Add Client')

@section('header-actions')
<a href="{{ route('clients.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Clients</a>
@endsection

@push('styles')
<style>
.cf-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 16px; align-items: start; }
@media (max-width: 1000px) { .cf-layout { grid-template-columns: 1fr; } }
.cf-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.cf-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; padding: 16px 18px; }
.cf-grid .cf-full { grid-column: 1 / -1; }
@media (max-width: 640px) { .cf-grid { grid-template-columns: 1fr; } }
.cf-grid .ui-input, .cf-grid .ui-select, .cf-body .ui-select { width: 100%; }
.cf-body { padding: 16px 18px; }
.cf-req { color: var(--mv-crit); }
.cf-hint { font-size: 12px; color: var(--mv-muted); margin-top: 5px; }
.cf-error { font-size: 12px; color: var(--mv-crit); margin-top: 5px; }
.cf-alert { padding: 11px 14px; font-size: 13px; border: 1px solid; margin-bottom: 16px; }
.cf-alert ul { margin: 6px 0 0 18px; list-style: disc; }
.cf-code { border: 1px dashed var(--mv-line-strong); border-radius: 8px; padding: 12px 14px; background: var(--mv-surface-2); }
.cf-code-v { font-family: var(--mv-mono); font-size: 14px; font-weight: 500; color: var(--mv-ink); margin: 2px 0; }
.cf-actions { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; }
</style>
@endpush

@section('content')
@if(session('error'))
    <div class="alert-danger cf-alert">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert-success cf-alert">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert-danger cf-alert">
        <strong>Validation Errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('clients.store') }}" method="POST" id="clientForm">
    @csrf
    <div class="cf-layout">
        <div class="cf-col">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Company Information</h3></div>
                <div class="cf-grid">
                    <div>
                        <label class="ui-label">Company Name <span class="cf-req">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required placeholder="e.g., Acme Corporation" class="ui-input">
                        @error('company_name')<div class="cf-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Contact Person <span class="cf-req">*</span></label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" required placeholder="e.g., John Smith" class="ui-input">
                        @error('contact_person')<div class="cf-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Email Address <span class="cf-req">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com" class="ui-input">
                        @error('email')<div class="cf-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="ui-label">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 123-4567" class="ui-input">
                        @error('phone')<div class="cf-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Address Information</h3></div>
                <div class="cf-grid">
                    <div class="cf-full">
                        <label class="ui-label">Address</label>
                        <textarea name="address" rows="3" placeholder="123 Main Street, Suite 100" class="ui-input">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="ui-label">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="New York" class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Region</label>
                        <input type="text" name="region" value="{{ old('region') }}" placeholder="e.g., North America" class="ui-input">
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Contract Information</h3></div>
                <div class="cf-grid">
                    <div>
                        <label class="ui-label">Contract Start Date</label>
                        <input type="date" name="contract_start_date" value="{{ old('contract_start_date') }}" class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Contract End Date</label>
                        <input type="date" name="contract_end_date" value="{{ old('contract_end_date') }}" class="ui-input">
                    </div>
                </div>
            </div>
        </div>

        <div class="cf-col">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Client Status</h3></div>
                <div class="cf-body">
                    <label class="ui-label">Status <span class="cf-req">*</span></label>
                    <select name="status" required class="ui-select">
                        <option value="prospect" {{ old('status', 'prospect') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                        <option value="active"   {{ old('status') == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="lost"     {{ old('status') == 'lost'     ? 'selected' : '' }}>Lost</option>
                    </select>
                    <div class="cf-hint">Set the current relationship status</div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header"><h3>Client Code</h3></div>
                <div class="cf-body">
                    <div class="cf-code">
                        <div class="cf-hint" style="margin:0">Auto-generated</div>
                        <div class="cf-code-v" id="codePreview">Will be created automatically</div>
                        <div class="cf-hint" style="margin:0">Based on company name</div>
                    </div>
                </div>
                <div class="ui-card-footer cf-actions">
                    <a href="{{ route('clients.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" id="submitBtn" class="btn-primary">Add Client</button>
                </div>
            </div>
        </div>
    </div>
</form>

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
