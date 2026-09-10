@extends('layouts.app')
@section('title', 'Technician Dashboard')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="m-0 text-gray-900 text-2xl font-semibold"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-wrench"/></svg> Technician Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Welcome back, {{ auth()->user()->full_name ?? auth()->user()->name ?? 'Technician' }}</p>
        </div>
        <div class="flex gap-2">
            @if(Route::has('jobs.mine'))
            <a href="{{ route('jobs.mine') }}" class="btn-primary btn-sm"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-clipboard"/></svg> My Jobs</a>
            @endif
            @if(Route::has('visits.create'))
            <a href="{{ route('visits.create') }}" class="btn-secondary btn-sm">+ Log Visit</a>
            @endif
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-calendar"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['jobs_today'] ?? 0 }}</div>
                <div class="stat-label">Jobs Today</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-refresh"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['jobs_in_progress'] ?? 0 }}</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-check-circle"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['jobs_completed'] ?? 0 }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-teal"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-pin"/></svg></div>
            <div>
                <div class="stat-number">{{ $stats['visits_today'] ?? 0 }}</div>
                <div class="stat-label">Visits Today</div>
            </div>
        </div>
    </div>

    {{-- Main 2-col layout --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        {{-- Left: main content --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Today's Assignments --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-clipboard"/></svg> Today's Jobs</h4>
                    @if(Route::has('jobs.mine'))
                    <a href="{{ route('jobs.mine') }}" class="btn-secondary btn-sm">View All</a>
                    @endif
                </div>
                <div class="ui-card-body">
                    @if(isset($todaysJobs) && $todaysJobs->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($todaysJobs as $job)
                            <div class="flex items-center gap-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg p-4">
                                <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center text-base flex-shrink-0"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-wrench"/></svg></div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-sm text-gray-800 truncate">{{ $job->list_title ?: $job->assignment_id }}</div>
                                    <div class="text-xs text-gray-500 flex gap-3 flex-wrap mt-1">
                                        <span><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-monitor"/></svg> {{ $job->terminal_count ?? 0 }} terminals</span>
                                        <span><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-wrench"/></svg> {{ \Illuminate\Support\Str::headline($job->service_type ?? 'General') }}</span>
                                    </div>
                                </div>
                                @if(Route::has('jobs.show'))
                                <a href="{{ route('jobs.show', $job->id) }}" class="btn-primary btn-sm flex-shrink-0">Open</a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-clipboard"/></svg></div>
                            <p class="empty-state-msg">No jobs assigned for today</p>
                            <p class="text-sm text-gray-400 mt-1">Check back later or view all assignments</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Site Visits --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-pin"/></svg> Recent Site Visits</h4>
                    @if(Route::has('visits.index'))
                    <a href="{{ route('visits.index') }}" class="btn-secondary btn-sm">View All</a>
                    @endif
                </div>
                <div class="ui-card-body">
                    @if(isset($recentVisits) && $recentVisits->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($recentVisits as $visit)
                            <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                                <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-sm flex-shrink-0"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-pin"/></svg></div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-800 truncate">
                                        {{ $visit->terminal->terminal_id ?? 'Terminal' }}
                                        @if($visit->terminal->merchant_name) — {{ $visit->terminal->merchant_name }}@endif
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $visit->started_at?->diffForHumans() }}</div>
                                </div>
                                @php
                                    $vsClass = match($visit->terminal_status ?? '') {
                                        'working'           => 'badge-green',
                                        'not_working'       => 'badge-red',
                                        'needs_maintenance' => 'badge-yellow',
                                        'not_found'         => 'badge-gray',
                                        default             => 'badge-gray',
                                    };
                                @endphp
                                <span class="status-badge {{ $vsClass }}">{{ ucfirst(str_replace('_', ' ', $visit->terminal_status ?? 'unknown')) }}</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-edit"/></svg></div>
                            <p class="empty-state-msg">No recent site visits</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">

            {{-- Quick Actions --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-zap"/></svg> Quick Actions</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-2">
                    @if(Route::has('jobs.mine'))
                    <a href="{{ route('jobs.mine') }}" class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-[#1a3a5c] hover:bg-blue-50 hover:text-[#1a3a5c] transition-colors no-underline">
                        <span><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-clipboard"/></svg></span><span>My Job Assignments</span>
                    </a>
                    @endif
                    @if(Route::has('visits.create'))
                    <a href="{{ route('visits.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-[#1a3a5c] hover:bg-blue-50 hover:text-[#1a3a5c] transition-colors no-underline">
                        <span><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-pin"/></svg></span><span>Log a Site Visit</span>
                    </a>
                    @endif
                    @if(Route::has('visits.index'))
                    <a href="{{ route('visits.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-[#1a3a5c] hover:bg-blue-50 hover:text-[#1a3a5c] transition-colors no-underline">
                        <span><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-file"/></svg></span><span>Visit History</span>
                    </a>
                    @endif
                    @if(Route::has('asset-requests.index'))
                    <a href="{{ route('asset-requests.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-[#1a3a5c] hover:bg-blue-50 hover:text-[#1a3a5c] transition-colors no-underline">
                        <span><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-box"/></svg></span><span>Asset Requests</span>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Job Summary --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-chart"/></svg> Job Summary</h4>
                </div>
                <div class="ui-card-body space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Due Today</span>
                        <span class="font-semibold text-blue-600">{{ $stats['jobs_today'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">In Progress</span>
                        <span class="font-semibold text-orange-500">{{ $stats['jobs_in_progress'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Completed</span>
                        <span class="font-semibold text-green-600">{{ $stats['jobs_completed'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Site Visits Today</span>
                        <span class="font-semibold text-teal-600">{{ $stats['visits_today'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
