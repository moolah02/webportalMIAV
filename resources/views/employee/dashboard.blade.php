{{-- resources/views/employee/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div>
    {{-- Welcome Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div class="flex gap-2">
            @if(Route::has('jobs.mine'))
            <a href="{{ route('jobs.mine') }}" class="btn-primary btn-sm">&#x1F4CB; My Jobs</a>
            @endif
            @if(Route::has('asset-requests.create'))
            <a href="{{ route('asset-requests.create') }}" class="btn-secondary btn-sm">&#x2B; New Request</a>
            @endif
        </div>
    </div>

    {{-- Alert banners --}}
    @if(($stats['jobs']['today'] ?? 0) > 0 || ($stats['pending_approvals'] ?? 0) > 0)
    <div class="space-y-2 mb-6">
        @if(($stats['jobs']['today'] ?? 0) > 0)
        <a href="{{ route('jobs.mine') ?? '#' }}" class="flex items-center gap-3 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm font-medium no-underline hover:bg-blue-100 transition-colors">
            <span>&#x1F4CB;</span>
            <span class="flex-1">You have {{ $stats['jobs']['today'] ?? 0 }} job assignment(s) for today</span>
            <span class="text-xs opacity-60">&#x2192;</span>
        </a>
        @endif
        @if(($stats['pending_approvals'] ?? 0) > 0)
        <a href="{{ route('asset-requests.index') ?? '#' }}" class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg text-sm font-medium no-underline hover:bg-amber-100 transition-colors">
            <span>&#x23F0;</span>
            <span class="flex-1">{{ $stats['pending_approvals'] ?? 0 }} of your asset requests are pending approval</span>
            <span class="text-xs opacity-60">&#x2192;</span>
        </a>
        @endif
    </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">&#x1F4C5;</div>
            <div>
                <div class="stat-number">{{ $stats['jobs']['today'] ?? 0 }}</div>
                <div class="stat-label">Jobs Today</div>
            </div>
        </div>
        @if(Route::has('jobs.mine'))
        <a href="{{ route('jobs.mine') }}" class="stat-card no-underline hover:shadow-md transition-shadow">
            <div class="stat-icon stat-icon-gray">&#x1F4CB;</div>
            <div>
                <div class="stat-number">{{ $stats['jobs']['assigned'] ?? 0 }}</div>
                <div class="stat-label">Assigned</div>
            </div>
        </a>
        @else
        <div class="stat-card">
            <div class="stat-icon stat-icon-gray">&#x1F4CB;</div>
            <div>
                <div class="stat-number">{{ $stats['jobs']['assigned'] ?? 0 }}</div>
                <div class="stat-label">Assigned</div>
            </div>
        </div>
        @endif
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">&#x1F504;</div>
            <div>
                <div class="stat-number">{{ $stats['jobs']['in_progress'] ?? 0 }}</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">&#x2705;</div>
            <div>
                <div class="stat-number">{{ $stats['jobs']['completed'] ?? 0 }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        @if(Route::has('asset-requests.index'))
        <a href="{{ route('asset-requests.index') }}" class="stat-card no-underline hover:shadow-md transition-shadow">
            <div class="stat-icon stat-icon-purple">&#x1F4E6;</div>
            <div>
                <div class="stat-number">{{ $stats['my_requests'] ?? 0 }}</div>
                <div class="stat-label">My Requests</div>
            </div>
        </a>
        @else
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">&#x1F4E6;</div>
            <div>
                <div class="stat-number">{{ $stats['my_requests'] ?? 0 }}</div>
                <div class="stat-label">My Requests</div>
            </div>
        </div>
        @endif
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">&#x23F0;</div>
            <div>
                <div class="stat-number">{{ $stats['pending_approvals'] ?? 0 }}</div>
                <div class="stat-label">Pending Approvals</div>
            </div>
        </div>
    </div>

    {{-- Main 2-col layout --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        {{-- Left: main content --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Upcoming Assignments --}}
            @if(isset($upcomingAssignments) && $upcomingAssignments->isNotEmpty())
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CB; Upcoming Assignments</h4>
                    @if(Route::has('jobs.mine'))
                    <a href="{{ route('jobs.mine') }}" class="btn-secondary btn-sm">View All Jobs</a>
                    @endif
                </div>
                <div class="ui-card-body space-y-3">
                    @foreach($upcomingAssignments->take(5) as $assignment)
                    <div class="flex items-center gap-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg p-4">
                        <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center text-base flex-shrink-0">&#x1F4C5;</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-sm text-gray-800 truncate">{{ $assignment->list_title ?: $assignment->assignment_id }}</div>
                            <div class="text-xs text-gray-500 flex gap-3 flex-wrap mt-1">
                                <span>&#x1F4C5; {{ optional($assignment->scheduled_date)->format('M d, Y') ?: 'Date TBD' }}</span>
                                <span>&#x1F527; {{ \Illuminate\Support\Str::headline($assignment->service_type ?? 'General') }}</span>
                                <span>&#x1F5A5; {{ $assignment->terminal_count ?? 0 }} terminals</span>
                            </div>
                        </div>
                        @if(Route::has('jobs.show'))
                        <a href="{{ route('jobs.show', $assignment->id) }}" class="btn-primary btn-sm flex-shrink-0">Open</a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Activity --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F550; Recent Activity</h4>
                </div>
                <div class="ui-card-body max-h-[400px] overflow-y-auto">
                    @php $recent = $stats['recent_activity'] ?? []; @endphp
                    @if(is_iterable($recent) && count($recent))
                        @foreach($recent as $item)
                        <div class="flex items-start gap-3 py-3 border-b border-gray-100 last:border-0">
                            <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm flex-shrink-0">&#x1F4CB;</div>
                            <div class="flex-1">
                                @if(is_array($item))
                                <div class="text-sm font-medium text-gray-800">{{ $item['label'] ?? $item['title'] ?? 'Activity Update' }}</div>
                                <div class="text-sm text-gray-500 mt-0.5">{{ $item['details'] ?? '' }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $item['date'] ?? 'Recently' }}</div>
                                @else
                                <div class="text-sm text-gray-700">{{ (string)$item }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="empty-state">
                        <div class="empty-state-icon">&#x1F4DD;</div>
                        <p class="empty-state-msg">No recent activity</p>
                        <p class="text-sm text-gray-400 mt-1">Your activity will appear here once you start working on jobs</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Performance --}}
            @if(isset($stats['performance']))
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CA; My Performance</h4>
                </div>
                <div class="ui-card-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center bg-gray-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['performance']['completion_rate'] ?? '0' }}%</div>
                            <div class="text-xs text-gray-500 mt-1">Completion Rate</div>
                        </div>
                        <div class="text-center bg-gray-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['performance']['avg_response_time'] ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 mt-1">Avg Response Time</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">
            {{-- Quick Actions --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x26A1; Quick Actions</h4>
                </div>
                <div class="ui-card-body flex flex-col gap-2">
                    @if(Route::has('jobs.mine'))
                    <a href="{{ route('jobs.mine') }}" class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-colors no-underline">
                        <span>&#x1F4CB;</span><span>View My Jobs</span>
                    </a>
                    @endif
                    @if(Route::has('asset-requests.index'))
                    <a href="{{ route('asset-requests.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-colors no-underline">
                        <span>&#x1F4E6;</span><span>My Asset Requests</span>
                    </a>
                    @endif
                    @if(Route::has('asset-requests.create'))
                    <a href="{{ route('asset-requests.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-colors no-underline">
                        <span>&#x2B;</span><span>New Asset Request</span>
                    </a>
                    @endif
                    <a href="{{ route('employee.profile') }}" class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-colors no-underline">
                        <span>&#x1F464;</span><span>Update Profile</span>
                    </a>
                </div>
            </div>

            {{-- Job Summary --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CA; Job Summary</h4>
                </div>
                <div class="ui-card-body space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Assigned</span>
                        <span class="font-semibold text-gray-800">{{ $stats['jobs']['assigned'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">In Progress</span>
                        <span class="font-semibold text-orange-500">{{ $stats['jobs']['in_progress'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Completed</span>
                        <span class="font-semibold text-green-600">{{ $stats['jobs']['completed'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Due Today</span>
                        <span class="font-semibold text-blue-600">{{ $stats['jobs']['today'] ?? 0 }}</span>
                    </div>
                    @if(($stats['jobs']['assigned'] ?? 0) > 0)
                    @php $pct = round((($stats['jobs']['completed'] ?? 0) / ($stats['jobs']['assigned'] ?? 1)) * 100); @endphp
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                            <span>Overall Progress</span><span>{{ $pct }}%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-green-500 h-full rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Request Status --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4E6; Request Status</h4>
                </div>
                <div class="ui-card-body space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Requests</span>
                        <span class="font-semibold text-gray-800">{{ $stats['my_requests'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Pending Approval</span>
                        <span class="font-semibold text-orange-500">{{ $stats['pending_approvals'] ?? 0 }}</span>
                    </div>
                    @if(isset($stats['approved_requests']))
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Approved</span>
                        <span class="font-semibold text-green-600">{{ $stats['approved_requests'] ?? 0 }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tips --}}
            <div class="ui-card">
                <div class="ui-card-header">
                    <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F4A1; Tips &amp; Reminders</h4>
                </div>
                <div class="ui-card-body space-y-3">
                    <div class="bg-blue-50 border-l-3 border-blue-400 rounded-r-lg p-3">
                        <div class="text-xs font-semibold text-blue-700">Pro Tip</div>
                        <div class="text-xs text-blue-600 mt-0.5">Update job status promptly to keep your completion rate high</div>
                    </div>
                    <div class="bg-purple-50 border-l-3 border-purple-400 rounded-r-lg p-3">
                        <div class="text-xs font-semibold text-purple-700">Remember</div>
                        <div class="text-xs text-purple-600 mt-0.5">Check equipment before starting each job</div>
                    </div>
                    @if(($stats['pending_approvals'] ?? 0) > 0)
                    <div class="bg-amber-50 border-l-3 border-amber-400 rounded-r-lg p-3">
                        <div class="text-xs font-semibold text-amber-700">Action Needed</div>
                        <div class="text-xs text-amber-600 mt-0.5">Follow up on pending asset requests</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection