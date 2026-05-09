@extends('layouts.app')
@section('title', 'Compliance Report')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">
            &#x1F4CB; {{ $direction === 'company_held' ? 'Internal Licenses' : 'Customer Licenses' }} — Compliance Report
        </h2>
        <p class="text-sm text-gray-500 mt-0.5">Licenses requiring attention (expiring within 30 days or already expired)</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('business-licenses.expiring', ['direction' => $direction]) }}" class="btn-secondary">Expiring Soon</a>
        <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary">&#x2190; Back to Licenses</a>
    </div>
</div>

{{-- Compliance Summary --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">&#x2705;</div>
        <div>
            <div class="stat-number">{{ $stats['compliant'] }}</div>
            <div class="stat-label">Compliant</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">&#x26A0;</div>
        <div>
            <div class="stat-number">{{ $stats['warning'] }}</div>
            <div class="stat-label">Warning (expiring soon)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">&#x274C;</div>
        <div>
            <div class="stat-number">{{ $stats['nonCompliant'] }}</div>
            <div class="stat-label">Non-Compliant (expired)</div>
        </div>
    </div>
</div>

{{-- Compliance Score --}}
@php
    $total = $stats['compliant'] + $stats['warning'] + $stats['nonCompliant'];
    $score = $total > 0 ? round(($stats['compliant'] / $total) * 100) : 100;
    $scoreColor = $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-amber-600' : 'text-red-600');
@endphp
<div class="ui-card mb-5">
    <div class="ui-card-body">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Overall Compliance Score</div>
                <div class="text-3xl font-bold {{ $scoreColor }}">{{ $score }}%</div>
                <div class="text-xs text-gray-400 mt-1">{{ $stats['compliant'] }} of {{ $total }} licenses fully compliant</div>
            </div>
            <div class="w-32 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-2 rounded-full {{ $score >= 80 ? 'bg-green-500' : ($score >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                     style="width:{{ $score }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- Licenses Needing Attention --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">Licenses Requiring Action</span>
        <span class="badge badge-red">{{ $licenses->total() }} items</span>
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
                    <th>Expiry</th>
                    <th>Compliance</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($licenses as $license)
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
                        @if($license->compliance_status === 'non_compliant')
                            <span class="badge badge-red">&#x274C; Non-Compliant</span>
                            <div class="text-xs text-red-500 mt-0.5">{{ abs((int)$license->days_until_expiry) }}d overdue</div>
                        @elseif($license->compliance_status === 'warning')
                            <span class="badge badge-yellow">&#x26A0; Warning</span>
                            <div class="text-xs text-amber-600 mt-0.5">{{ (int)$license->days_until_expiry }}d remaining</div>
                        @else
                            <span class="badge badge-green">&#x2705; Compliant</span>
                        @endif
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
        <div class="empty-state-msg">All licenses are compliant! No immediate action required.</div>
        <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-primary mt-4">View All Licenses</a>
    </div>
    @endif
</div>

@endsection
