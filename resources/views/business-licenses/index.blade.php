{{-- File: resources/views/business-licenses/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📋 Business Licenses</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage and track business licenses and compliance</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('business-licenses.compliance') }}" class="btn">📊 Compliance</a>
            <a href="{{ route('business-licenses.expiring') }}" class="btn">⚠️ Expiring</a>
            <a href="{{ route('business-licenses.create') }}" class="btn btn-primary">➕ Add License</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">✅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['active_licenses'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Active Licenses</div>
                </div>
            </div>
        </div>

        <div class="metric-card alert" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⏰</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['expiring_soon'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Expiring Soon</div>
                </div>
            </div>
        </div>

        <div class="metric-card alert" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⚠️</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['expired_licenses'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Expired</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">💰</div>
                <div>
                    <div style="font-size: 24px; font-weight: bold;">${{ number_format($stats['total_annual_cost'], 0) }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Annual Cost</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">🚨</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['critical_licenses'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Critical Priority</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #607d8b 0%, #455a64 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">📊</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $stats['total_licenses'] }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Licenses</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-block-end: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search licenses..."
                   style="flex: 1; min-width: 200px; padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
            
            <select name="status" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Status</option>
                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            
            <select name="license_type" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Types</option>
                @foreach(\App\Models\BusinessLicense::LICENSE_TYPES as $key => $label)
                <option value="{{ $key }}" {{ request('license_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="priority" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Priority</option>
                @foreach(\App\Models\BusinessLicense::PRIORITY_LEVELS as $key => $label)
                <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="department" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
                @endforeach
            </select>
            
            <button type="submit" class="btn">Filter</button>
            
            @if(request()->hasAny(['search', 'status', 'license_type', 'priority', 'department']))
            <a href="{{ route('business-licenses.index') }}" class="btn">Clear</a>
            @endif
        </form>
    </div>

    <!-- Licenses List -->
    @forelse($licenses as $license)
    <div class="content-card license-card" style="margin-block-end: 15px; {{ $license->is_expired ? 'border-inline-start: 4px solid #f44336;' : ($license->is_expiring_soon ? 'border-inline-start: 4px solid #ff9800;' : '') }}">
        <div style="display: flex; align-items: start; gap: 20px;">
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-block-end: 10px;">
                    <div>
                        <h4 style="margin: 0; color: #333;">{{ $license->license_name }}</h4>
                        <div style="color: #666; font-size: 14px; margin-block-start: 2px;">
                            {{ $license->license_number }} • {{ $license->license_type_name }}
                        </div>
                        <div style="color: #666; font-size: 12px; margin-block-start: 2px;">
                            Issued by {{ $license->issuing_authority }}
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <span class="status-badge" style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; {{ $license->getStatusColorClass() }}">
                            {{ $license->status_name }}
                        </span>
                        <span class="priority-badge" style="padding: 4px 8px; border-radius: 8px; font-size: 11px; font-weight: 500; {{ $license->getPriorityColorClass() }}">
                            {{ $license->priority_level_name }}
                        </span>
                    </div>
                </div>

                <!-- Key Information -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-block-end: 10px; font-size: 14px; color: #666;">
                    <div>
                        <strong>Department:</strong> {{ $license->department->name ?? 'N/A' }}
                    </div>
                    <div>
                        <strong>Responsible:</strong> {{ $license->responsibleEmployee->full_name ?? 'Unassigned' }}
                    </div>
                    <div>
                        <strong>Issue Date:</strong> {{ $license->issue_date ? $license->issue_date->format('M d, Y') : 'N/A' }}
                    </div>
                    <div style="{{ $license->is_expired ? 'color: #f44336; font-weight: bold;' : ($license->is_expiring_soon ? 'color: #ff9800; font-weight: bold;' : '') }}">
                        <strong>Expiry:</strong> {{ $license->expiry_date ? $license->expiry_date->format('M d, Y') : 'N/A' }}
                        @if($license->days_until_expiry !== null)
                            @if($license->days_until_expiry < 0)
                                ({{ abs($license->days_until_expiry) }} days overdue)
                            @elseif($license->days_until_expiry <= 30)
                                ({{ $license->days_until_expiry }} days left)
                            @endif
                        @endif
                    </div>
                    @if($license->renewal_cost)
                    <div>
                        <strong>Renewal Cost:</strong> ${{ number_format($license->renewal_cost, 2) }}
                    </div>
                    @endif
                    @if($license->location)
                    <div>
                        <strong>Location:</strong> {{ $license->location }}
                    </div>
                    @endif
                </div>

                @if($license->description)
                <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-block-end: 10px;">
                    <div style="font-size: 12px; color: #666; margin-block-end: 5px;">Description:</div>
                    <div style="font-size: 14px; color: #333;">{{ Str::limit($license->description, 150) }}</div>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div style="display: flex; flex-direction: column; gap: 8px; min-inline-size: 140px;">
                <a href="{{ route('business-licenses.show', $license) }}" class="btn btn-small" style="text-align: center;">
                    View Details
                </a>
                
                @if($license->canRenew() && ($license->is_expired || $license->is_expiring_soon))
                <a href="{{ route('business-licenses.renew', $license) }}" class="btn btn-small" style="background: #ff9800; color: white; border-color: #ff9800; text-align: center;">
                    🔄 Renew
                </a>
                @endif
                
                <a href="{{ route('business-licenses.edit', $license) }}" class="btn btn-small" style="text-align: center;">
                    ✏️ Edit
                </a>
                
                @if($license->document_path)
                <a href="{{ route('business-licenses.download', $license) }}" class="btn btn-small" style="background: #2196f3; color: white; border-color: #2196f3; text-align: center;">
                    📄 Document
                </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="content-card" style="text-align: center; padding: 60px;">
        <div style="font-size: 64px; margin-block-end: 20px;">📋</div>
        <h3>No business licenses found</h3>
        <p style="color: #666;">Get started by adding your first business license.</p>
        <div style="margin-block-start: 20px;">
            <a href="{{ route('business-licenses.create') }}" class="btn btn-primary">Add First License</a>
        </div>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($licenses->hasPages())
    <div style="margin-block-start: 20px;">
        {{ $licenses->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.license-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: box-shadow 0.2s ease;
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
}

.btn-small {
    padding: 6px 12px;
    font-size: 14px;
}

.alert {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}
</style>
@endsection