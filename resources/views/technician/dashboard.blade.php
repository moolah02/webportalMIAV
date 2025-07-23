?>
@extends('layouts.app')

@section('content')
<div>
    <div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Technician Dashboard</h3>
        <p>Welcome to your technician dashboard, {{ $employee->full_name }}!</p>
        
        <div style="margin-top: 20px;">
            <h4>Today's Jobs</h4>
            <p style="color: #666;">No jobs assigned for today.</p>
        </div>
        
        <div style="margin-top: 20px;">
            <h4>Recent Service Reports</h4>
            <p style="color: #666;">No recent reports.</p>
        </div>
    </div>
</div>
@endsection