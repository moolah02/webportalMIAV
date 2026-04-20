@extends('layouts.app')

@section('title', 'System Report Export')

@section('content')
<div class="ui-card mb-6">
    <div class="ui-card-header flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">System Report</h2>
        <span class="text-sm text-gray-500">Generated: {{ $generatedAt->format('d M Y H:i') }}</span>
    </div>
    <div class="ui-card-body">
        <p class="text-gray-600 mb-4">This is a full system snapshot. Use the CSV export buttons on the System Dashboard to download data for individual sections.</p>

        {{-- System Overview --}}
        @if(!empty($data['systemOverview']))
        <h3 class="text-base font-semibold text-gray-700 mt-6 mb-3">System Overview</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach($data['systemOverview'] as $key => $value)
            @if(!is_array($value))
            <div class="stat-card">
                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                <p class="text-2xl font-bold text-navy">{{ is_numeric($value) ? number_format($value) : $value }}</p>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        {{-- Client Analytics --}}
        @if(!empty($data['clientAnalytics']))
        <h3 class="text-base font-semibold text-gray-700 mt-6 mb-3">Client Analytics</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach($data['clientAnalytics'] as $key => $value)
            @if(!is_array($value))
            <div class="stat-card">
                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                <p class="text-2xl font-bold text-navy">{{ is_numeric($value) ? number_format($value) : $value }}</p>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        {{-- Employee Data --}}
        @if(!empty($data['employeeData']))
        <h3 class="text-base font-semibold text-gray-700 mt-6 mb-3">Employee Data</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach($data['employeeData'] as $key => $value)
            @if(!is_array($value))
            <div class="stat-card">
                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                <p class="text-2xl font-bold text-navy">{{ is_numeric($value) ? number_format($value) : $value }}</p>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        {{-- Asset Data --}}
        @if(!empty($data['assetData']))
        <h3 class="text-base font-semibold text-gray-700 mt-6 mb-3">Asset Data</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach($data['assetData'] as $key => $value)
            @if(!is_array($value))
            <div class="stat-card">
                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                <p class="text-2xl font-bold text-navy">{{ is_numeric($value) ? number_format($value) : $value }}</p>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        <div class="mt-8 flex gap-3">
            <a href="{{ route('reports.system') }}" class="btn-secondary">&#8592; Back to Dashboard</a>
            <a href="{{ route('reports.system.export-csv') }}?section=overview" class="btn-primary">Download Overview CSV</a>
        </div>
    </div>
</div>
@endsection
