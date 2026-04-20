"""
UI Uniformity Fix Script
Rewrites pages to use the shared design system:
  btn-primary / btn-secondary / btn-danger / btn-success / btn-sm
  ui-card / ui-card-header / ui-card-body
  ui-table
  ui-input / ui-select / ui-label
  stat-card / stat-number / stat-label
  badge / badge-green / badge-blue / badge-yellow / badge-red / badge-gray
  flash-success / flash-error / flash-warning
  filter-bar / empty-state
"""
import os

BASE = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'

files = {}

# ── 1. projects/create.blade.php ────────────────────────────────────────────
files['projects/create.blade.php'] = r"""@extends('layouts.app')
@section('title', 'New Project')

@section('header-actions')
<a href="{{ route('projects.index') }}" class="btn-secondary">← Back to Projects</a>
@endsection

@section('content')

@if($errors->any())
<div class="flash-error">
    <div>
        <strong>Please fix the following errors:</strong>
        <ul class="mt-1 list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" id="createProjectForm">
@csrf

{{-- Basic Information --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Basic Information</span>
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Project Name <span class="text-red-500">*</span></label>
                <input type="text" name="project_name" id="project_name"
                       value="{{ old('project_name') }}"
                       class="ui-input @error('project_name') border-red-400 @enderror"
                       placeholder="Enter project name" required autofocus>
                @error('project_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Client <span class="text-red-500">*</span></label>
                <select name="client_id" class="ui-select @error('client_id') border-red-400 @enderror" required>
                    <option value="">Select Client</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->company_name }}
                    </option>
                    @endforeach
                </select>
                @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Project Type <span class="text-red-500">*</span></label>
                <select name="project_type" class="ui-select @error('project_type') border-red-400 @enderror" required>
                    <option value="">Select Type</option>
                    <option value="discovery"    {{ old('project_type') == 'discovery'    ? 'selected' : '' }}>Discovery</option>
                    <option value="servicing"    {{ old('project_type') == 'servicing'    ? 'selected' : '' }}>Servicing</option>
                    <option value="support"      {{ old('project_type') == 'support'      ? 'selected' : '' }}>Support</option>
                    <option value="maintenance"  {{ old('project_type') == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
                    <option value="installation" {{ old('project_type') == 'installation' ? 'selected' : '' }}>Installation</option>
                    <option value="upgrade"      {{ old('project_type') == 'upgrade'      ? 'selected' : '' }}>Upgrade</option>
                    <option value="decommission" {{ old('project_type') == 'decommission' ? 'selected' : '' }}>Decommission</option>
                </select>
                @error('project_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Priority Level <span class="text-red-500">*</span></label>
                <select name="priority" class="ui-select @error('priority') border-red-400 @enderror" required>
                    <option value="normal"    {{ old('priority', 'normal') == 'normal'    ? 'selected' : '' }}>Normal</option>
                    <option value="high"      {{ old('priority') == 'high'      ? 'selected' : '' }}>High</option>
                    <option value="low"       {{ old('priority') == 'low'       ? 'selected' : '' }}>Low</option>
                    <option value="emergency" {{ old('priority') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
                @error('priority')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-4">
            <label class="ui-label">Project Description</label>
            <textarea name="description" rows="4"
                      class="ui-input @error('description') border-red-400 @enderror"
                      style="min-height:100px;resize:vertical"
                      placeholder="Describe the project objectives, scope, and key deliverables...">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- Timeline & Resources --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Timeline &amp; Resources</span>
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Project Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                       class="ui-input @error('start_date') border-red-400 @enderror">
                @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Expected Completion Date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}"
                       class="ui-input @error('end_date') border-red-400 @enderror">
                @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Project Manager</label>
                <select name="project_manager_id" class="ui-select @error('project_manager_id') border-red-400 @enderror">
                    <option value="">Select Manager (Optional)</option>
                    @foreach($projectManagers as $manager)
                    <option value="{{ $manager->id }}" {{ old('project_manager_id') == $manager->id ? 'selected' : '' }}>
                        {{ $manager->full_name }}
                    </option>
                    @endforeach
                </select>
                @error('project_manager_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Estimated Terminal Count</label>
                <input type="number" name="estimated_terminals_count"
                       value="{{ old('estimated_terminals_count') }}" min="0" placeholder="0"
                       class="ui-input @error('estimated_terminals_count') border-red-400 @enderror">
                @error('estimated_terminals_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Budget & Notes --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Budget &amp; Additional Information</span>
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Project Budget (USD)</label>
                <input type="number" name="budget" value="{{ old('budget') }}"
                       step="0.01" min="0" placeholder="0.00"
                       class="ui-input @error('budget') border-red-400 @enderror">
                @error('budget')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-4">
            <label class="ui-label">Additional Notes &amp; Requirements</label>
            <textarea name="notes" rows="3"
                      class="ui-input @error('notes') border-red-400 @enderror"
                      style="min-height:80px;resize:vertical"
                      placeholder="Any special requirements, constraints, or additional information...">{{ old('notes') }}</textarea>
            @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- Terminal Upload Section --}}
@include('projects.partials.terminal-upload-section')

{{-- Actions --}}
<div class="flex justify-between items-center mt-6">
    <a href="{{ route('projects.index') }}" class="btn-secondary">← Cancel</a>
    <button type="submit" class="btn-primary">Create Project</button>
</div>

</form>
@endsection
"""

# ── 2. projects/edit.blade.php ─────────────────────────────────────────────
files['projects/edit.blade.php'] = r"""@extends('layouts.app')
@section('title', 'Edit Project')

@section('header-actions')
<a href="{{ route('projects.show', $project) }}" class="btn-secondary">← Back to Project</a>
@endsection

@section('content')

@if($errors->any())
<div class="flash-error">
    <div>
        <strong>Please fix the following errors:</strong>
        <ul class="mt-1 list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="flash-warning" style="margin-bottom:20px">
    <span>ℹ️</span>
    <span>Editing <strong>{{ $project->project_name }}</strong> ({{ $project->project_code }}) — changes are saved immediately on submission.</span>
</div>

<form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- Basic Information --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Basic Information</span>
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Project Name <span class="text-red-500">*</span></label>
                <input type="text" name="project_name" id="project_name"
                       value="{{ old('project_name', $project->project_name) }}"
                       class="ui-input @error('project_name') border-red-400 @enderror"
                       placeholder="Enter project name" required>
                @error('project_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Client <span class="text-red-500">*</span></label>
                <select name="client_id" class="ui-select @error('client_id') border-red-400 @enderror" required>
                    <option value="">Select Client</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                        {{ $client->company_name }}
                    </option>
                    @endforeach
                </select>
                @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Project Type <span class="text-red-500">*</span></label>
                <select name="project_type" class="ui-select @error('project_type') border-red-400 @enderror" required>
                    <option value="">Select Type</option>
                    <option value="discovery"    {{ old('project_type', $project->project_type) == 'discovery'    ? 'selected' : '' }}>Discovery</option>
                    <option value="servicing"    {{ old('project_type', $project->project_type) == 'servicing'    ? 'selected' : '' }}>Servicing</option>
                    <option value="support"      {{ old('project_type', $project->project_type) == 'support'      ? 'selected' : '' }}>Support</option>
                    <option value="maintenance"  {{ old('project_type', $project->project_type) == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
                    <option value="installation" {{ old('project_type', $project->project_type) == 'installation' ? 'selected' : '' }}>Installation</option>
                    <option value="upgrade"      {{ old('project_type', $project->project_type) == 'upgrade'      ? 'selected' : '' }}>Upgrade</option>
                    <option value="decommission" {{ old('project_type', $project->project_type) == 'decommission' ? 'selected' : '' }}>Decommission</option>
                </select>
                @error('project_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Priority Level <span class="text-red-500">*</span></label>
                <select name="priority" class="ui-select @error('priority') border-red-400 @enderror" required>
                    <option value="normal"    {{ old('priority', $project->priority) == 'normal'    ? 'selected' : '' }}>Normal</option>
                    <option value="high"      {{ old('priority', $project->priority) == 'high'      ? 'selected' : '' }}>High</option>
                    <option value="low"       {{ old('priority', $project->priority) == 'low'       ? 'selected' : '' }}>Low</option>
                    <option value="emergency" {{ old('priority', $project->priority) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
                @error('priority')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-4">
            <label class="ui-label">Project Description</label>
            <textarea name="description" rows="4"
                      class="ui-input @error('description') border-red-400 @enderror"
                      style="min-height:100px;resize:vertical"
                      placeholder="Describe the project objectives, scope, and key deliverables...">{{ old('description', $project->description) }}</textarea>
            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- Timeline & Resources --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Timeline &amp; Resources</span>
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Project Start Date</label>
                <input type="date" name="start_date"
                       value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                       class="ui-input @error('start_date') border-red-400 @enderror">
                @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Expected Completion Date</label>
                <input type="date" name="end_date"
                       value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                       class="ui-input @error('end_date') border-red-400 @enderror">
                @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Project Manager</label>
                <select name="project_manager_id" class="ui-select @error('project_manager_id') border-red-400 @enderror">
                    <option value="">Select Manager (Optional)</option>
                    @foreach($projectManagers as $manager)
                    <option value="{{ $manager->id }}" {{ old('project_manager_id', $project->project_manager_id) == $manager->id ? 'selected' : '' }}>
                        {{ $manager->full_name }}
                    </option>
                    @endforeach
                </select>
                @error('project_manager_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="ui-label">Estimated Terminal Count</label>
                <input type="number" name="estimated_terminals_count"
                       value="{{ old('estimated_terminals_count', $project->estimated_terminals_count) }}"
                       min="0" class="ui-input @error('estimated_terminals_count') border-red-400 @enderror">
                @error('estimated_terminals_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Budget & Notes --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-800">Budget &amp; Additional Information</span>
    </div>
    <div class="ui-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Budget (USD)</label>
                <input type="number" name="budget"
                       value="{{ old('budget', $project->budget) }}"
                       step="0.01" min="0"
                       class="ui-input @error('budget') border-red-400 @enderror">
                @error('budget')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-4">
            <label class="ui-label">Additional Notes</label>
            <textarea name="notes" rows="3"
                      class="ui-input @error('notes') border-red-400 @enderror"
                      style="min-height:80px;resize:vertical"
                      placeholder="Any additional information or requirements">{{ old('notes', $project->notes) }}</textarea>
            @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- Terminal Upload Section --}}
@include('projects.partials.terminal-upload-section')

{{-- Actions --}}
<div class="flex justify-between items-center mt-6">
    <a href="{{ route('projects.show', $project) }}" class="btn-secondary">← Cancel Changes</a>
    <button type="submit" class="btn-primary">💾 Update Project</button>
</div>

</form>
@endsection
"""

# ── 3. projects/index.blade.php ────────────────────────────────────────────
files['projects/index.blade.php'] = r"""@extends('layouts.app')
@section('title', 'Projects')

@section('header-actions')
<a href="{{ route('projects.create') }}" class="btn-primary">➕ New Project</a>
@endsection

@section('content')

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div class="flex flex-col gap-1">
        <label class="ui-label">Client</label>
        <select name="client_id" class="ui-select" style="min-width:160px">
            <option value="">All Clients</option>
            @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                {{ $client->company_name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-col gap-1">
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select" style="min-width:140px">
            <option value="">All Status</option>
            <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="paused"    {{ request('status') == 'paused'    ? 'selected' : '' }}>Paused</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    <div class="flex flex-col gap-1">
        <label class="ui-label">Type</label>
        <select name="project_type" class="ui-select" style="min-width:140px">
            <option value="">All Types</option>
            <option value="discovery"    {{ request('project_type') == 'discovery'    ? 'selected' : '' }}>Discovery</option>
            <option value="servicing"    {{ request('project_type') == 'servicing'    ? 'selected' : '' }}>Servicing</option>
            <option value="support"      {{ request('project_type') == 'support'      ? 'selected' : '' }}>Support</option>
            <option value="maintenance"  {{ request('project_type') == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
            <option value="installation" {{ request('project_type') == 'installation' ? 'selected' : '' }}>Installation</option>
        </select>
    </div>

    <div class="flex flex-col gap-1">
        <label class="ui-label">Search</label>
        <input type="text" name="search" placeholder="Search projects…"
               value="{{ request('search') }}" class="ui-input" style="min-width:200px">
    </div>

    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['client_id','status','project_type','search']))
        <a href="{{ route('projects.index') }}" class="btn-secondary">Clear</a>
        @endif
    </div>
</form>

{{-- Table --}}
<div class="ui-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Timeline</th>
                    <th>Manager</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr>
                    <td>
                        <div class="font-semibold text-gray-900">{{ $project->project_name }}</div>
                        <code class="text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                            {{ $project->project_code }}
                        </code>
                    </td>
                    <td class="text-gray-700">{{ $project->client->company_name }}</td>
                    <td>
                        <span class="badge badge-gray capitalize">{{ $project->project_type }}</span>
                    </td>
                    <td>
                        @php
                            $sc = match($project->status) {
                                'active'    => 'badge-green',
                                'completed' => 'badge-blue',
                                'paused'    => 'badge-yellow',
                                'cancelled' => 'badge-red',
                                default     => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $sc }} capitalize">{{ $project->status }}</span>
                    </td>
                    <td class="text-gray-500 text-xs leading-relaxed">
                        @if($project->job_assignments_count > 0)
                            <div>{{ $project->terminals_count ?? 0 }} terminals</div>
                            <div>{{ number_format($project->completion_percentage ?? 0, 1) }}% complete</div>
                        @else
                            <span class="italic text-gray-400">No assignments</span>
                        @endif
                    </td>
                    <td class="text-xs leading-relaxed text-gray-700">
                        <div><span class="font-medium">Start:</span> {{ $project->start_date ? $project->start_date->format('M j, Y') : 'Not set' }}</div>
                        <div><span class="font-medium">End:</span> {{ $project->end_date ? $project->end_date->format('M j, Y') : 'Not set' }}</div>
                    </td>
                    <td>
                        @if($project->projectManager)
                            <span class="font-medium text-gray-700">{{ $project->projectManager->full_name }}</span>
                        @else
                            <span class="text-gray-400 italic text-xs">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('projects.show', $project) }}" class="btn-secondary btn-sm">👁 View</a>
                            <a href="{{ route('projects.edit', $project) }}" class="btn-secondary btn-sm">✏️ Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <div class="empty-state-msg">No projects match your current filters.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($projects->hasPages())
<div class="mt-5">
    {{ $projects->appends(request()->query())->links() }}
</div>
@endif

@endsection
"""

# ── 4. settings/manage-department.blade.php ────────────────────────────────
files['settings/manage-department.blade.php'] = r"""@extends('layouts.app')
@section('title', 'Manage Departments')

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary">← Settings</a>
<button class="btn-primary" onclick="showCreateModal()">+ Add Department</button>
@endsection

@section('content')

@if(session('success'))
<div class="flash-success"><span>✓</span> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-error"><span>✗</span> {{ session('error') }}</div>
@endif

<div class="ui-card overflow-hidden">
    @if($departments->count() > 0)
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Department Name</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Employees</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                <tr>
                    <td class="font-semibold text-gray-900">{{ $department->name }}</td>
                    <td>
                        @if($department->code)
                            <code class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-700">{{ $department->code }}</code>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="text-gray-600 max-w-xs">{{ $department->description ?? '—' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ $department->employees_count }} employees</span>
                    </td>
                    <td>
                        <span class="badge {{ $department->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex gap-1.5">
                            <button class="btn-secondary btn-sm" onclick='editDepartment(@json($department))'>Edit</button>
                            <form method="POST" action="{{ route('settings.departments.delete', $department) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm"
                                        onclick="return confirm('Delete this department?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">🏢</div>
        <div class="empty-state-msg">No departments found. Create your first department!</div>
    </div>
    @endif
</div>

{{-- Modal --}}
<div id="deptModal" class="ui-modal">
    <div class="ui-modal-box" style="max-width:560px">
        <div class="ui-modal-header">
            <span class="ui-modal-title" id="modalTitle">Add Department</span>
            <button class="ui-modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="deptForm" method="POST" action="{{ route('settings.departments.store') }}">
            @csrf
            <input type="hidden" id="dept_method" name="_method" value="POST">
            <input type="hidden" id="dept_id" name="dept_id">
            <div class="ui-modal-body" style="display:grid;gap:16px">
                <div>
                    <label class="ui-label">Department Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="dept_name" required class="ui-input">
                </div>
                <div>
                    <label class="ui-label">Department Code</label>
                    <input type="text" name="code" id="dept_code" placeholder="e.g. IT, HR, FIN" class="ui-input">
                </div>
                <div>
                    <label class="ui-label">Description</label>
                    <textarea name="description" id="dept_description" rows="3" class="ui-input" style="resize:vertical"></textarea>
                </div>
                <div id="statusField" style="display:none">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="dept_is_active" value="1" class="w-4 h-4">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            <div class="ui-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Department</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.ui-modal { display:none; position:fixed; inset:0; z-index:1000; background:rgba(0,0,0,.5); backdrop-filter:blur(4px); align-items:center; justify-content:center; }
.ui-modal.show { display:flex; }
.ui-modal-box { background:#fff; border-radius:12px; width:90%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.ui-modal-header { padding:20px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; }
.ui-modal-title { font-size:16px; font-weight:700; color:#111827; }
.ui-modal-close { background:none; border:none; font-size:22px; color:#6b7280; cursor:pointer; line-height:1; padding:0; }
.ui-modal-body { padding:24px; }
.ui-modal-footer { padding:16px 24px; border-top:1px solid #e5e7eb; display:flex; gap:10px; justify-content:flex-end; }
</style>
@endpush

<script>
function showCreateModal() {
    document.getElementById('modalTitle').innerText = 'Add Department';
    document.getElementById('deptForm').action = '{{ route('settings.departments.store') }}';
    document.getElementById('dept_method').value = 'POST';
    document.getElementById('dept_name').value = '';
    document.getElementById('dept_code').value = '';
    document.getElementById('dept_description').value = '';
    document.getElementById('dept_is_active').checked = true;
    document.getElementById('statusField').style.display = 'none';
    document.getElementById('deptModal').classList.add('show');
}
function editDepartment(department) {
    document.getElementById('modalTitle').innerText = 'Edit Department';
    document.getElementById('deptForm').action = '/settings/departments/' + department.id;
    document.getElementById('dept_method').value = 'PUT';
    document.getElementById('dept_name').value = department.name;
    document.getElementById('dept_code').value = department.code || '';
    document.getElementById('dept_description').value = department.description || '';
    document.getElementById('dept_is_active').checked = !!department.is_active;
    document.getElementById('statusField').style.display = 'block';
    document.getElementById('deptModal').classList.add('show');
}
function closeModal() {
    document.getElementById('deptModal').classList.remove('show');
}
document.getElementById('deptModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
"""

# ── 5. admin/docs/index.blade.php ──────────────────────────────────────────
files['admin/docs/index.blade.php'] = r"""@extends('layouts.app')
@section('title', 'Documentation Manager')

@section('header-actions')
<a href="{{ url('/docs') }}" target="_blank" class="btn-secondary">🔗 View Live Docs</a>
@endsection

@section('content')

@if(session('success'))
<div class="flash-success"><span>✓</span> {{ session('success') }}</div>
@endif

<div class="ui-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Slug</th>
                    <th>Last Edited</th>
                    <th>Edited By</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td>
                        <div class="font-semibold text-gray-900">{{ $page->title }}</div>
                        @if($page->subtitle)
                            <div class="text-gray-500 text-xs mt-0.5">{{ Str::limit($page->subtitle, 80) }}</div>
                        @endif
                    </td>
                    <td>
                        <code class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-700">{{ $page->slug }}</code>
                    </td>
                    <td class="text-gray-500 text-sm">
                        {{ $page->updated_at ? $page->updated_at->format('d M Y, H:i') : '—' }}
                    </td>
                    <td class="text-gray-500 text-sm">
                        {{ $page->editor?->name ?? '—' }}
                    </td>
                    <td class="text-right">
                        <div class="inline-flex gap-2">
                            <a href="{{ url('/docs/' . $page->slug) }}" target="_blank" class="btn-secondary btn-sm">👁 View</a>
                            <a href="{{ route('admin.docs.edit', $page->slug) }}" class="btn-primary btn-sm">✏️ Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon">📄</div>
                            <div class="empty-state-msg">
                                No documentation pages found.<br>
                                Run <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">php artisan db:seed --class=DocPageSeeder</code> to populate them.
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
"""

# ── Write all files ─────────────────────────────────────────────────────────
for rel_path, content in files.items():
    full_path = os.path.join(BASE, rel_path.replace('/', os.sep))
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f'  WRITTEN: {rel_path}')

print('\nDone. All files written.')
