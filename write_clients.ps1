$utf8NoBom = New-Object System.Text.UTF8Encoding $false
$base = 'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views\clients'

# ─── show.blade.php ─────────────────────────────────────────────────────────
$show = @'
{{-- resources/views/clients/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="page-title">{{ $client->company_name }}</h2>
            <p class="page-subtitle mt-1">Client details &amp; history</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('clients.index') }}" class="btn-secondary btn-sm">&#8592; Back to Clients</a>
            <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn-primary btn-sm">Edit</a>
        </div>
    </div>

    {{-- Summary bar --}}
    @php
        $statusClass = match($client->status) {
            'active'   => 'badge-green',
            'prospect' => 'badge-yellow',
            'inactive' => 'badge-gray',
            'lost'     => 'badge-red',
            default    => 'badge-gray',
        };
    @endphp
    <div class="ui-card mb-4">
        <div class="ui-card-body grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <div class="ui-label">Client Code</div>
                <div class="text-sm font-medium text-gray-900">{{ $client->client_code ?: '&#8212;' }}</div>
            </div>
            <div>
                <div class="ui-label">Status</div>
                <span class="badge {{ $statusClass }}">{{ ucfirst($client->status) }}</span>
            </div>
            <div>
                <div class="ui-label">Region</div>
                <div class="text-sm font-medium text-gray-900">{{ $client->region ?: '&#8212;' }}</div>
            </div>
            <div>
                <div class="ui-label">City</div>
                <div class="text-sm font-medium text-gray-900">{{ $client->city ?: '&#8212;' }}</div>
            </div>
        </div>
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Left (2/3) --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Company Information</h4>
                </div>
                <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="ui-label">Company Name</div>
                        <div class="text-sm text-gray-900">{{ $client->company_name }}</div>
                    </div>
                    <div>
                        <div class="ui-label">Contact Person</div>
                        <div class="text-sm text-gray-900">{{ $client->contact_person ?: '&#8212;' }}</div>
                    </div>
                    <div>
                        <div class="ui-label">Email</div>
                        @if($client->email)
                            <a href="mailto:{{ $client->email }}" class="text-sm text-[#1a3a5c] hover:underline">{{ $client->email }}</a>
                        @else
                            <div class="text-sm text-gray-900">&#8212;</div>
                        @endif
                    </div>
                    <div>
                        <div class="ui-label">Phone</div>
                        @if($client->phone)
                            <a href="tel:{{ $client->phone }}" class="text-sm text-[#1a3a5c] hover:underline">{{ $client->phone }}</a>
                        @else
                            <div class="text-sm text-gray-900">&#8212;</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Address</h4>
                </div>
                <div class="ui-card-body">
                    <div class="text-sm text-gray-900 whitespace-pre-line">
                        {{ trim(collect([$client->address, $client->city, $client->region])->filter()->join(', ')) ?: '&#8212;' }}
                    </div>
                </div>
            </div>

            <div class="ui-card">
                @php
                    $start  = $client->contract_start_date;
                    $end    = $client->contract_end_date;
                    $isPast = $end ? $end->isPast() : false;
                    $isSoon = $end ? (!$isPast && $end->diffInDays(now()) <= 30) : false;
                @endphp
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Contract</h4>
                </div>
                <div class="ui-card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="ui-label">Start</div>
                        <div class="text-sm text-gray-900">{{ $start?->format('M d, Y') ?: '&#8212;' }}</div>
                    </div>
                    <div>
                        <div class="ui-label">End</div>
                        <div class="text-sm text-gray-900 flex items-center gap-2 flex-wrap">
                            {{ $end?->format('M d, Y') ?: '&#8212;' }}
                            @if($isPast)
                                <span class="badge badge-red">Expired</span>
                            @elseif($isSoon)
                                <span class="badge badge-yellow">Expiring</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar (1/3) --}}
        <div class="flex flex-col gap-4">
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Quick Actions</h4>
                </div>
                <div class="ui-card-body flex flex-wrap gap-2">
                    <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="btn-primary btn-sm">Edit Client</a>
                    <button type="button" class="btn-secondary btn-sm"
                            onclick="contactClient('{{ $client->email }}')">Contact</button>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">Related</h4>
                </div>
                <div class="ui-card-body flex flex-col divide-y divide-gray-100">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">POS Terminals</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $client->posTerminals()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Projects</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $client->projects()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Tickets</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $client->tickets()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function contactClient(email) {
    if (email) { window.location.href = 'mailto:' + email; }
    else { alert('No email address available for this client'); }
}
</script>
@endsection
'@

# ─── create.blade.php ────────────────────────────────────────────────────────
$create = @'
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
'@

# ─── edit.blade.php ──────────────────────────────────────────────────────────
$edit = @'
@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="page-title">Edit {{ $client->company_name }}</h2>
            <p class="page-subtitle mt-1">Update client information and settings</p>
        </div>
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
'@

[IO.File]::WriteAllText("$base\show.blade.php",   $show,   $utf8NoBom)
[IO.File]::WriteAllText("$base\create.blade.php", $create, $utf8NoBom)
[IO.File]::WriteAllText("$base\edit.blade.php",   $edit,   $utf8NoBom)

Write-Host "show:   $((Get-Content "$base\show.blade.php").Count) lines"
Write-Host "create: $((Get-Content "$base\create.blade.php").Count) lines"
Write-Host "edit:   $((Get-Content "$base\edit.blade.php").Count) lines"
