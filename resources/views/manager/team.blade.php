{{-- resources/views/manager/team.blade.php --}}
@extends('layouts.app')
@section('title', 'Team Management')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="page-title">&#x1F465; Team Management</h1>
            <p class="page-subtitle">Manage your team members and performance</p>
        </div>
        <a href="{{ route('employees.create') }}" class="btn-primary">+ Add Team Member</a>
    </div>

    {{-- Team Statistics --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-5 rounded-xl shadow-sm bg-gradient-to-br from-blue-500 to-blue-700 text-white">
            <div class="flex items-center gap-3">
                <span class="text-3xl">&#x1F465;</span>
                <div>
                    <div class="text-2xl font-bold leading-none">{{ auth()->user()->subordinates->count() }}</div>
                    <div class="text-sm opacity-90 mt-1">Team Members</div>
                </div>
            </div>
        </div>
        <div class="p-5 rounded-xl shadow-sm bg-gradient-to-br from-green-500 to-green-700 text-white">
            <div class="flex items-center gap-3">
                <span class="text-3xl">&#x2705;</span>
                <div>
                    <div class="text-2xl font-bold leading-none">{{ auth()->user()->subordinates->where('status', 'active')->count() }}</div>
                    <div class="text-sm opacity-90 mt-1">Active Members</div>
                </div>
            </div>
        </div>
        <div class="p-5 rounded-xl shadow-sm bg-gradient-to-br from-orange-400 to-orange-600 text-white">
            <div class="flex items-center gap-3">
                <span class="text-3xl">&#x1F4DD;</span>
                <div>
                    <div class="text-2xl font-bold leading-none">5</div>
                    <div class="text-sm opacity-90 mt-1">Pending Tasks</div>
                </div>
            </div>
        </div>
        <div class="p-5 rounded-xl shadow-sm bg-gradient-to-br from-purple-500 to-purple-700 text-white">
            <div class="flex items-center gap-3">
                <span class="text-3xl">&#x2B50;</span>
                <div>
                    <div class="text-2xl font-bold leading-none">98%</div>
                    <div class="text-sm opacity-90 mt-1">Team Performance</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Team Members --}}
    <div class="ui-card mb-6">
        <div class="ui-card-header">
            <h4 class="text-sm font-semibold text-gray-800 m-0">&#x1F465; My Team Members</h4>
        </div>
        <div class="ui-card-body">
            @if(auth()->user()->subordinates->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach(auth()->user()->subordinates as $member)
                <div class="border border-gray-200 rounded-lg p-5 bg-gray-50 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                            {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="m-0 text-sm font-semibold text-gray-800">{{ $member->full_name }}</h5>
                            @if($member->role)
                            <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $member->role->name)) }}</div>
                            @endif
                            @if($member->department)
                            <div class="text-xs text-gray-400">{{ $member->department->name }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-1.5 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 text-sm">&#x1F4E7;</span>
                            <a href="mailto:{{ $member->email }}" class="text-blue-600 hover:underline text-sm truncate">{{ $member->email }}</a>
                        </div>
                        @if($member->phone)
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 text-sm">&#x1F4DE;</span>
                            <span class="text-sm text-gray-700">{{ $member->phone }}</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 text-sm">&#x1F4C5;</span>
                            <span class="text-sm text-gray-500">Joined {{ $member->hire_date ? $member->hire_date->format('M Y') : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="badge {{ $member->status === 'active' ? 'badge-green' : ($member->status === 'inactive' ? 'badge-gray' : 'badge-yellow') }}">
                            {{ ucfirst($member->status) }}
                        </span>
                        <div class="flex gap-3">
                            <a href="{{ route('employees.show', $member) }}" class="text-blue-600 hover:underline text-xs font-medium">View</a>
                            <a href="{{ route('employees.edit', $member) }}" class="text-green-600 hover:underline text-xs font-medium">Edit</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">&#x1F465;</div>
                <p class="empty-state-msg">No team members yet</p>
                <p class="text-sm text-gray-400 mt-1">You don't have any direct reports assigned to you.</p>
                <a href="{{ route('employees.create') }}" class="btn-primary btn-sm mt-4 inline-block">+ Add Team Member</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="ui-card">
            <div class="ui-card-header">
                <h5 class="text-sm font-semibold text-gray-800 m-0">&#x1F680; Quick Actions</h5>
            </div>
            <div class="ui-card-body flex flex-col gap-2.5">
                <a href="{{ route('employees.create') }}" class="btn-primary text-center">+ Add Team Member</a>
                <a href="{{ route('manager.approvals') }}" class="btn-secondary text-center">&#x1F4CB; Review Approvals</a>
                <a href="{{ route('manager.reports') }}" class="btn-secondary text-center">&#x1F4CA; View Reports</a>
            </div>
        </div>
        <div class="ui-card">
            <div class="ui-card-header">
                <h5 class="text-sm font-semibold text-gray-800 m-0">&#x1F4C8; Team Performance</h5>
            </div>
            <div class="ui-card-body space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Overall Rating:</span>
                    <span class="font-semibold text-green-600">Excellent</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Tasks Completed:</span>
                    <span class="font-semibold text-gray-800">124/130</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">On-time Delivery:</span>
                    <span class="font-semibold text-green-600">98%</span>
                </div>
            </div>
        </div>
        <div class="ui-card">
            <div class="ui-card-header">
                <h5 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CB; Recent Activity</h5>
            </div>
            <div class="ui-card-body space-y-3 text-sm">
                <div class="pb-3 border-b border-gray-100">
                    <div class="font-medium text-gray-700">John completed Asset Request</div>
                    <div class="text-xs text-gray-400 mt-0.5">2 hours ago</div>
                </div>
                <div class="pb-3 border-b border-gray-100">
                    <div class="font-medium text-gray-700">Sarah joined the team</div>
                    <div class="text-xs text-gray-400 mt-0.5">1 day ago</div>
                </div>
                <div>
                    <div class="font-medium text-gray-700">Team meeting scheduled</div>
                    <div class="text-xs text-gray-400 mt-0.5">3 days ago</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection