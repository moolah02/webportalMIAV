{{-- resources/views/roles/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Create Role')

@section('header-actions')
<a href="{{ route('roles.index') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Roles</a>
@endsection

@section('content')
@include('roles.partials.form-styles')

<form action="{{ route('roles.store') }}" method="POST" id="roleForm">
    @csrf

    <div class="rp-layout">
        <div class="rp-main">
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

            @if(session('error'))
            <div class="alert-danger rp-alert">{{ session('error') }}</div>
            @endif

            <div class="ui-card">
                <div class="ui-card-header"><h3>Role Information</h3></div>
                <div class="rp-body">
                    <label class="ui-label" for="role-name">Role Name <span class="rp-req">*</span></label>
                    <input type="text" id="role-name" name="name" value="{{ old('name') }}" required
                           placeholder="e.g., field_technician, office_manager, sales_coordinator"
                           class="ui-input rp-input">
                    <div class="rp-hint">Use lowercase with underscores. Will display as "Field Technician"</div>
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
                    @include('roles.partials.permission-matrix', ['checked' => old('permissions', [])])
                </div>
            </div>
        </div>

        <aside class="rp-side">
            <div class="ui-card">
                <div class="ui-card-header"><h3>Selected Permissions</h3></div>
                <div class="rp-body">
                    <div id="selected-permissions" class="rp-selected">
                        <div class="rp-empty">No permissions selected yet</div>
                    </div>
                </div>
            </div>

            @include('roles.partials.templates')

            <div class="ui-card">
                <div class="ui-card-header"><h3>Role Summary</h3></div>
                <div class="rp-body rp-summary">
                    <div class="summary-item"><span>Total Permissions</span><span id="total-count">0</span></div>
                    <div class="summary-item"><span>Access Level</span><span id="access-level">Safe</span></div>
                    <div class="summary-item"><span>Categories</span><span id="category-count">0</span></div>
                </div>
                <div class="ui-card-footer rp-actions">
                    <a href="{{ route('roles.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg> Create Role</button>
                </div>
            </div>
        </aside>
    </div>
</form>

@include('roles.partials.permission-script', ['mode' => 'create'])
@endsection
