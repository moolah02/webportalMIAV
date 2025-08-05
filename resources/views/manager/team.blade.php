{{-- 
==============================================
MANAGER TEAM VIEW
File: resources/views/manager/team.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">👥 Team Management</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage your team members and performance</p>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">+ Add Team Member</a>
    </div>

    <!-- Team Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">👥</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ auth()->user()->subordinates->count() }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Team Members</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">✅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">{{ auth()->user()->subordinates->where('status', 'active')->count() }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Active Members</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">📝</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">5</div>
                    <div style="font-size: 14px; opacity: 0.9;">Pending Tasks</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⭐</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">98%</div>
                    <div style="font-size: 14px; opacity: 0.9;">Team Performance</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members -->
    <div class="content-card">
        <h4 style="margin-block-end: 20px; color: #333;">👥 My Team Members</h4>
        
        @if(auth()->user()->subordinates->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            @foreach(auth()->user()->subordinates as $member)
            <div style="border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: #f8f9fa; transition: all 0.3s ease;">
                <div style="display: flex; align-items: center; gap: 12px; margin-block-end: 15px;">
                    <div style="inline-size: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; font-weight: bold;">
                        {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h5 style="margin: 0; color: #333;">{{ $member->full_name }}</h5>
                        @if($member->role)
                        <div style="font-size: 14px; color: #666;">{{ ucfirst(str_replace('_', ' ', $member->role->name)) }}</div>
                        @endif
                        @if($member->department)
                        <div style="font-size: 12px; color: #999;">{{ $member->department->name }}</div>
                        @endif
                    </div>
                </div>

                <div style="margin-block-end: 15px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-block-end: 5px;">
                        <span style="color: #666;">📧</span>
                        <a href="mailto:{{ $member->email }}" style="color: #2196f3; text-decoration: none; font-size: 14px;">{{ $member->email }}</a>
                    </div>
                    
                    @if($member->phone)
                    <div style="display: flex; align-items: center; gap: 8px; margin-block-end: 5px;">
                        <span style="color: #666;">📞</span>
                        <span style="color: #333; font-size: 14px;">{{ $member->phone }}</span>
                    </div>
                    @endif

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: #666;">📅</span>
                        <span style="color: #666; font-size: 14px;">Joined {{ $member->hire_date ? $member->hire_date->format('M Y') : 'N/A' }}</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="status-badge status-{{ $member->status }}">
                        {{ ucfirst($member->status) }}
                    </span>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('employees.show', $member) }}" style="color: #2196f3; text-decoration: none; font-size: 12px;">View</a>
                        <a href="{{ route('employees.edit', $member) }}" style="color: #4caf50; text-decoration: none; font-size: 12px;">Edit</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align: center; padding: 60px; color: #666;">
            <div style="font-size: 64px; margin-block-end: 20px;">👥</div>
            <h3>No team members yet</h3>
            <p>You don't have any direct reports assigned to you.</p>
            <p style="font-size: 14px; color: #999;">Contact your administrator to assign team members.</p>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-block-start: 30px;">
        <div class="content-card">
            <h5 style="margin-block-end: 15px; color: #333;">🚀 Quick Actions</h5>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('employees.create') }}" class="btn btn-primary" style="text-align: center;">+ Add Team Member</a>
                <a href="{{ route('manager.approvals') }}" class="btn" style="text-align: center;">📋 Review Approvals</a>
                <a href="{{ route('manager.reports') }}" class="btn" style="text-align: center;">📊 View Reports</a>
            </div>
        </div>

        <div class="content-card">
            <h5 style="margin-block-end: 15px; color: #333;">📈 Team Performance</h5>
            <div style="color: #666;">
                <div style="margin-block-end: 10px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Overall Rating:</span>
                        <span style="font-weight: bold; color: #4caf50;">Excellent</span>
                    </div>
                </div>
                <div style="margin-block-end: 10px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Tasks Completed:</span>
                        <span style="font-weight: bold;">124/130</span>
                    </div>
                </div>
                <div style="margin-block-end: 10px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>On-time Delivery:</span>
                        <span style="font-weight: bold; color: #4caf50;">98%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h5 style="margin-block-end: 15px; color: #333;">📋 Recent Activity</h5>
            <div style="color: #666; font-size: 14px;">
                <div style="margin-block-end: 10px; padding-bottom: 10px; border-block-end: 1px solid #eee;">
                    <div style="font-weight: 500;">John completed Asset Request</div>
                    <div style="font-size: 12px; color: #999;">2 hours ago</div>
                </div>
                <div style="margin-block-end: 10px; padding-bottom: 10px; border-block-end: 1px solid #eee;">
                    <div style="font-weight: 500;">Sarah joined the team</div>
                    <div style="font-size: 12px; color: #999;">1 day ago</div>
                </div>
                <div style="margin-block-end: 10px;">
                    <div style="font-weight: 500;">Team meeting scheduled</div>
                    <div style="font-size: 12px; color: #999;">3 days ago</div>
                </div>
            </div>
        </div>
    </div>
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
    color: white;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-inactive { background: #f5f5f5; color: #666; }
</style>
@endsection