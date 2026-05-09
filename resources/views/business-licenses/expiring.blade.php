@extends('layouts.app')
@section('title', 'Expiring Licenses')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">
            &#x26A0; Expiring {{ $direction === 'company_held' ? 'Internal' : 'Customer' }} Licenses
        </h2>
        <p class="text-sm text-gray-500 mt-0.5">Licenses expiring within {{ $days }} days</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('business-licenses.compliance', ['direction' => $direction]) }}" class="btn-secondary">Compliance Report</a>
        <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary">&#x2190; Back to Licenses</a>
    </div>
</div>

{{-- Days filter --}}
<div class="flex gap-2 mb-5">
    @foreach([15, 30, 60, 90] as $d)
    <a href="{{ route('business-licenses.expiring', ['direction' => $direction, 'days' => $d]) }}"
       class="{{ $days == $d ? 'btn-primary btn-sm' : 'btn-secondary btn-sm' }}">{{ $d }} days</a>
    @endforeach
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">&#x1F525;</div>
        <div>
            <div class="stat-number">{{ $licenses->where('is_expired', true)->count() }}</div>
            <div class="stat-label">Already Expired</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">&#x26A0;</div>
        <div>
            <div class="stat-number">{{ $licenses->where('is_expired', false)->count() }}</div>
            <div class="stat-label">Expiring Soon</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">&#x1F4CB;</div>
        <div>
            <div class="stat-number">{{ $licenses->total() }}</div>
            <div class="stat-label">Total Affected</div>
        </div>
    </div>
</div>

<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">Expiring Licenses</span>
        <span class="badge badge-yellow">{{ $licenses->total() }} licenses</span>
    </div>

    @if($licenses->count() > 0)
    <div class="overflow-x-auto">
        <table class="shared-table">
            <thead>
                <tr>
                    <th>License</th>
                    @if($direction === 'company_held')
                    <th>Department</th>
                    <th>Priority</th>
                    @else
                    <th>Customer</th>
                    <th>Revenue</th>
                    @endif
                    <th>Expiry Date</th>
                    <th>Days Left</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($licenses as $license)
                @php $days_left = (int) $license->days_until_expiry; @endphp
                <tr>
                    <td>
                        <div class="font-semibold text-sm text-gray-800">{{ $license->license_name }}</div>
                        <div class="text-xs text-gray-400">{{ $license->license_number }}</div>
                        <div class="text-xs text-gray-400">{{ $license->license_type_name }}</div>
                    </td>
                    @if($direction === 'company_held')
                    <td>
                        <div class="text-sm text-gray-700">{{ $license->department->name ?? '—' }}</div>
                        <div class="text-xs text-gray-400">{{ $license->responsibleEmployee->full_name ?? '' }}</div>
                    </td>
                    <td>
                        @php $pc = match($license->priority_level) { 'critical'=>'badge-red','high'=>'badge-orange','medium'=>'badge-blue', default=>'badge-gray' }; @endphp
                        <span class="badge {{ $pc }}">{{ $license->priority_level_name }}</span>
                    </td>
                    @else
                    <td>
                        <div class="text-sm text-gray-700">{{ $license->customer_display_name }}</div>
                        <div class="text-xs text-gray-400">{{ $license->customer_email }}</div>
                    </td>
                    <td class="text-sm font-semibold text-gray-800">
                        ${{ number_format($license->revenue_amount ?? 0, 0) }}
                        <div class="text-xs text-gray-400">{{ $license->billing_cycle_name }}</div>
                    </td>
                    @endif
                    <td class="text-sm text-gray-700">
                        {{ $license->expiry_date ? $license->expiry_date->format('M d, Y') : 'N/A' }}
                    </td>
                    <td>
                        @if($license->is_expired)
                            <span class="badge badge-red">{{ abs($days_left) }}d overdue</span>
                        @elseif($days_left <= 7)
                            <span class="badge badge-red">{{ $days_left }}d left</span>
                        @elseif($days_left <= 30)
                            <span class="badge badge-yellow">{{ $days_left }}d left</span>
                        @else
                            <span class="badge badge-blue">{{ $days_left }}d left</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-gray">{{ $license->status_name }}</span>
                    </td>
                    <td class="text-center">
                        <div class="flex gap-1 justify-center">
                            <a href="{{ route('business-licenses.show', $license) }}" class="btn-secondary btn-sm">View</a>
                            @if($license->canRenew())
                            <a href="{{ route('business-licenses.renew', $license) }}" class="btn-primary btn-sm">Renew</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($licenses->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $licenses->appends(request()->query())->links() }}
    </div>
    @endif

    @else
    <div class="empty-state py-16">
        <div class="empty-state-icon">&#x2705;</div>
        <div class="empty-state-msg">No licenses expiring within {{ $days }} days.</div>
        <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-primary mt-4">Back to Licenses</a>
    </div>
    @endif
</div>

@endsection
