{{-- resources/views/roles/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Role')

@section('header-actions')
<a href="{{ route('roles.show', $role) }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg> View Details</a>
<a href="{{ route('roles.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Roles</a>
@endsection

@section('content')
@include('roles.partials.form-styles')

@php
    $currentPermissions = $currentPermissions ?? [];
    $savedPermissions = $currentPermissions;
    if (old('permissions')) {
        $currentPermissions = old('permissions');
    }
    $employeeCount = $role->employees()->count();
    $displayName = ucwords(str_replace('_', ' ', $role->name));
@endphp

<form action="{{ route('roles.update', $role) }}" method="POST" id="roleForm">
    @method('PUT')
    @csrf

    <div class="rp-layout">
        <div class="rp-main">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Current Role Information</h3></div>
                <div class="rp-info">
                    <div class="rp-info-item"><span>Role</span><strong>{{ $displayName }}</strong></div>
                    <div class="rp-info-item"><span>Created</span><strong>{{ $role->created_at ? $role->created_at->format('M d, Y') : '—' }}</strong></div>
                    <div class="rp-info-item"><span>Permissions</span><strong>{{ count($savedPermissions) }}</strong></div>
                    <div class="rp-info-item"><span>Employees</span><strong>{{ $employeeCount }}</strong></div>
                    @if(in_array('all', $savedPermissions))
                        <span class="badge badge-red">Super Admin Role</span>
                    @endif
                </div>
            </div>

            @if($errors->any())
            <div class="alert-danger rp-alert">
                <strong>Could not save role:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="ui-card">
                <div class="ui-card-header"><h3>Role Information</h3></div>
                <div class="rp-body">
                    <label class="ui-label" for="role-name">Role Name <span class="rp-req">*</span></label>
                    <input type="text" id="role-name" name="name" value="{{ old('name', $role->name) }}" required
                           placeholder="e.g., field_technician, office_manager, sales_coordinator"
                           class="ui-input rp-input">
                    <div class="rp-hint">Use lowercase with underscores. Will display as "{{ $displayName }}"</div>
                    @error('name')
                        <div class="rp-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header">
                    <h3>Permissions &amp; Access Control</h3>
                    <div class="rp-toolbar">
                        <button type="button" onclick="expandAllCategories()" class="btn-secondary btn-sm">Expand All</button>
                        <button type="button" onclick="collapseAllCategories()" class="btn-secondary btn-sm">Collapse All</button>
                    </div>
                </div>
                <div class="rp-body">
                    @include('roles.partials.permission-matrix', ['checked' => $currentPermissions])
                </div>
            </div>
        </div>

        <aside class="rp-side">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Selected Permissions</h3></div>
                <div class="rp-body">
                    <div id="selected-permissions" class="rp-selected"></div>
                </div>
            </div>

            @include('roles.partials.templates')

            <div class="ui-card">
                <div class="ui-card-header"><h3>Role Summary</h3></div>
                <div class="rp-body rp-summary">
                    <div class="summary-item"><span>Total Permissions</span><span id="total-count">{{ count($currentPermissions) }}</span></div>
                    <div class="summary-item"><span>Access Level</span><span id="access-level">Safe</span></div>
                    <div class="summary-item"><span>Categories</span><span id="category-count">0</span></div>
                    <div class="summary-item"><span>Employees Affected</span><span>{{ $employeeCount }}</span></div>
                </div>
                @if($employeeCount > 0)
                <div class="rp-note">
                    <svg class="mv-i mv-i-sm" aria-hidden="true" style="flex-shrink:0;margin-top:1px"><use href="#i-alert-triangle"/></svg>
                    <span><strong>Note:</strong> Changes will affect {{ $employeeCount }} employee(s) using this role.</span>
                </div>
                @endif
                <div class="ui-card-footer rp-actions">
                    <a href="{{ route('roles.show', $role) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg> Update Role</button>
                </div>
            </div>
        </aside>
    </div>
</form>

@include('roles.partials.permission-script', ['mode' => 'edit'])
@endsection
