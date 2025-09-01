@extends('layouts.app')

@section('content')
<div class="p-6 space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold text-gray-900">Client Dashboards</h1>
        <div class="bg-blue-100 text-blue-800 text-sm font-medium px-4 py-2 rounded-full border border-blue-300 shadow-md">
            {{ $clients->count() }} total clients
        </div>
    </div>

    <!-- Clients Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($clients as $client)
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-lg transition-transform transform hover:scale-105">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $client->company_name }}</h3>
                        <p class="text-sm text-gray-500 font-mono">{{ $client->client_code }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $client->status === 'active' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-gray-100 text-gray-700 border border-gray-300' }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </div>

                <!-- Terminal Count -->
                <div class="mb-4">
                    <div class="text-4xl font-bold text-blue-600">{{ $client->pos_terminals_count }}</div>
                    <div class="text-sm text-gray-500">POS Terminals</div>
                </div>

                <!-- Status Breakdown -->
                @if(count($client->status_breakdown) > 0)
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-700 mb-2">Terminal Status</div>
                        <div class="space-y-1">
                            @foreach($client->status_breakdown as $status => $count)
                                <div class="flex justify-between text-xs">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 rounded-full {{ $status === 'online' ? 'bg-emerald-500' : ($status === 'offline' ? 'bg-red-500' : 'bg-yellow-500') }}"></div>
                                        <span class="capitalize text-gray-600">{{ str_replace('_', ' ', $status) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Activity -->
                <div class="mb-4">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium text-gray-900">{{ $client->recent_activity_count }}</span> visits (last 30 days)
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4 border-t border-gray-200">
                    <a href="{{ route('client-dashboards.show', $client) }}"
                       class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors px-4 py-2 rounded-md bg-blue-50 hover:bg-blue-100 shadow-md">
                        View Dashboard
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    @if($clients->count() === 0)
        <div class="text-center py-12">
            <div class="text-gray-400 text-lg mb-2">No clients found</div>
            <a href="{{ route('clients.create') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                Create your first client
            </a>
        </div>
    @endif
</div>
@endsection
