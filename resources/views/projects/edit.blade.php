@extends('layouts.app')
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
