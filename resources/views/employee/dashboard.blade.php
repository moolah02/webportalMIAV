?>
@extends('layouts.app')

@section('content')
<div>
    <div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Employee Dashboard</h3>
        <p>Welcome, {{ $employee->full_name }}!</p>
        
        <div style="margin-block-start: 20px;">
            <h4>My Assets</h4>
            @if($assets && $assets->count() > 0)
                @foreach($assets as $asset)
                    <p>{{ $asset->asset_number }} - {{ $asset->brand }} {{ $asset->model }}</p>
                @endforeach
            @else
                <p style="color: #666;">No assets assigned to you.</p>
            @endif
        </div>
        
        <div style="margin-block-start: 20px;">
            <h4>My Requests</h4>
            @if($requests && $requests->count() > 0)
                @foreach($requests as $request)
                    <p>{{ $request->request_number }} - {{ $request->asset_type }} ({{ $request->status }})</p>
                @endforeach
            @else
                <p style="color: #666;">No pending requests.</p>
            @endif
        </div>
    </div>
</div>
@endsection