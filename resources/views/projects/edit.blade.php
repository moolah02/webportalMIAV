@extends('layouts.app')
@section('title', 'Edit Project')

@section('header-actions')
<a href="{{ route('projects.show', $project) }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Project</a>
@endsection

@push('styles')
<style>
.pe-wrap { display: flex; flex-direction: column; gap: 16px; }
.pe-head { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 18px; }
.pe-name { font-size: 16px; font-weight: 600; color: var(--mv-ink); }
.pe-code { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 7px; }
.pe-muted { font-size: 12.5px; color: var(--mv-muted); }
.pe-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; padding: 16px 18px; }
.pe-full { grid-column: 1 / -1; }
@media (max-width: 760px) { .pe-grid { grid-template-columns: 1fr; } }
.pe-grid .ui-input, .pe-grid .ui-select { width: 100%; }
.pe-req { color: var(--mv-crit); }
.pe-error { font-size: 12px; color: var(--mv-crit); margin-top: 5px; }
.pe-invalid { border-color: var(--mv-crit) !important; }
.pe-alert { padding: 11px 14px; font-size: 13px; border: 1px solid; }
.pe-alert ul { margin: 6px 0 0 18px; list-style: disc; }
.pe-actions { display: flex; justify-content: flex-end; gap: 8px; }
</style>
@endpush

@section('content')
@php
    $editStatusBadge = match($project->status) {
        'active'    => 'badge-green',
        'completed' => 'badge-blue',
        'paused'    => 'badge-yellow',
        'cancelled' => 'badge-red',
        default     => 'badge-gray',
    };
@endphp

<div class="pe-wrap">
    @if($errors->any())
    <div class="alert-danger pe-alert">
        <strong>Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="ui-card pe-head">
        <span class="pe-name">{{ $project->project_name }}</span>
        <span class="pe-code">{{ $project->project_code }}</span>
        <span class="badge {{ $editStatusBadge }}" style="text-transform:capitalize">{{ $project->status }}</span>
        <span class="pe-muted">Last updated {{ $project->updated_at->diffForHumans() }}</span>
        <a href="{{ route('projects.show', $project) }}" class="btn-secondary btn-sm" style="margin-left:auto">View Project <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-right"/></svg></a>
    </div>

    <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="pe-wrap">
        @csrf
        @method('PUT')

        <div class="ui-card">
            <div class="ui-card-header"><h3>Basic Information</h3></div>
            <div class="pe-grid">
                <div>
                    <label class="ui-label">Project Name <span class="pe-req">*</span></label>
                    <input type="text" name="project_name" id="project_name"
                           value="{{ old('project_name', $project->project_name) }}"
                           class="ui-input @error('project_name') pe-invalid @enderror"
                           placeholder="Enter project name" required>
                    @error('project_name')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ui-label">Client <span class="pe-req">*</span></label>
                    <select name="client_id" class="ui-select @error('client_id') pe-invalid @enderror" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ui-label">Project Type <span class="pe-req">*</span></label>
                    <select name="project_type" class="ui-select @error('project_type') pe-invalid @enderror" required>
                        <option value="">Select Type</option>
                        <option value="discovery"    {{ old('project_type', $project->project_type) == 'discovery'    ? 'selected' : '' }}>Discovery</option>
                        <option value="servicing"    {{ old('project_type', $project->project_type) == 'servicing'    ? 'selected' : '' }}>Servicing</option>
                        <option value="support"      {{ old('project_type', $project->project_type) == 'support'      ? 'selected' : '' }}>Support</option>
                        <option value="maintenance"  {{ old('project_type', $project->project_type) == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
                        <option value="installation" {{ old('project_type', $project->project_type) == 'installation' ? 'selected' : '' }}>Installation</option>
                        <option value="upgrade"      {{ old('project_type', $project->project_type) == 'upgrade'      ? 'selected' : '' }}>Upgrade</option>
                        <option value="decommission" {{ old('project_type', $project->project_type) == 'decommission' ? 'selected' : '' }}>Decommission</option>
                    </select>
                    @error('project_type')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ui-label">Priority Level <span class="pe-req">*</span></label>
                    <select name="priority" class="ui-select @error('priority') pe-invalid @enderror" required>
                        <option value="normal"    {{ old('priority', $project->priority) == 'normal'    ? 'selected' : '' }}>Normal</option>
                        <option value="high"      {{ old('priority', $project->priority) == 'high'      ? 'selected' : '' }}>High</option>
                        <option value="low"       {{ old('priority', $project->priority) == 'low'       ? 'selected' : '' }}>Low</option>
                        <option value="emergency" {{ old('priority', $project->priority) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    @error('priority')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
                <div class="pe-full">
                    <label class="ui-label">Project Description</label>
                    <textarea name="description" rows="3" class="ui-input @error('description') pe-invalid @enderror"
                              style="min-height:90px;resize:vertical"
                              placeholder="Describe the project objectives, scope, and key deliverables...">{{ old('description', $project->description) }}</textarea>
                    @error('description')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Timeline &amp; Resources</h3></div>
            <div class="pe-grid">
                <div>
                    <label class="ui-label">Project Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                           class="ui-input @error('start_date') pe-invalid @enderror">
                    @error('start_date')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ui-label">Expected Completion Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                           class="ui-input @error('end_date') pe-invalid @enderror">
                    @error('end_date')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ui-label">Project Manager</label>
                    <select name="project_manager_id" class="ui-select @error('project_manager_id') pe-invalid @enderror">
                        <option value="">Select Manager (Optional)</option>
                        @foreach($projectManagers as $manager)
                        <option value="{{ $manager->id }}" {{ old('project_manager_id', $project->project_manager_id) == $manager->id ? 'selected' : '' }}>{{ $manager->full_name }}</option>
                        @endforeach
                    </select>
                    @error('project_manager_id')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ui-label">Estimated Terminal Count</label>
                    <input type="number" name="estimated_terminals_count" min="0"
                           value="{{ old('estimated_terminals_count', $project->estimated_terminals_count) }}"
                           class="ui-input @error('estimated_terminals_count') pe-invalid @enderror">
                    @error('estimated_terminals_count')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-header"><h3>Budget &amp; Notes</h3></div>
            <div class="pe-grid">
                <div>
                    <label class="ui-label">Budget (USD)</label>
                    <input type="number" name="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0"
                           class="ui-input @error('budget') pe-invalid @enderror" placeholder="0.00">
                    @error('budget')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
                <div class="pe-full">
                    <label class="ui-label">Additional Notes</label>
                    <textarea name="notes" rows="3" class="ui-input @error('notes') pe-invalid @enderror"
                              style="min-height:80px;resize:vertical"
                              placeholder="Any additional information or requirements">{{ old('notes', $project->notes) }}</textarea>
                    @error('notes')<div class="pe-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        @include('projects.partials.terminal-upload-section')

        <div class="pe-actions">
            <a href="{{ route('projects.show', $project) }}" class="btn-secondary">Cancel Changes</a>
            <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-save"/></svg> Update Project</button>
        </div>
    </form>
</div>
@endsection
