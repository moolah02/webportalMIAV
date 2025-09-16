@extends('layouts.app')

@section('content')
<style>
:root {
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #3b82f6;
    --light-bg: #f8fafc;
    --card-bg: #ffffff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border-color: #e2e8f0;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

body {
    background: var(--gradient-primary);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    min-height: 100vh;
}

.modern-card {
    background: white;
    border: none;
    border-radius: 24px;
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    overflow: hidden;
}

.modern-header {
    background: linear-gradient(135deg, var(--warning-color) 0%, #bae5ef 100%);
    padding: 2.5rem;
    color: white;
    border: none;
    position: relative;
}

.modern-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M20 20c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10zm10 0c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10z'/%3E%3C/g%3E%3C/svg%3E") repeat;
}

.modern-header h3 {
    color: white;
    font-weight: 600;
    margin: 0;
    position: relative;
    z-index: 1;
}

.project-info-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    margin-top: 1rem;
    display: inline-block;
    backdrop-filter: blur(5px);
    position: relative;
    z-index: 1;
}

.form-section {
    background: rgba(248, 250, 252, 0.5);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-color);
}

.form-section-title {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.1rem;
    display: flex;
    align-items-center;
}

.form-section-title i {
    margin-right: 0.5rem;
    color: var(--warning-color);
}

.modern-form-control {
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
    font-size: 0.95rem;
}

.modern-form-control:focus {
    border-color: var(--warning-color);
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
    background: white;
    transform: translateY(-1px);
}

.form-label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    display: flex;
    align-items-center;
}

.form-label .text-danger {
    margin-left: 0.25rem;
}

.modern-btn {
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    font-size: 0.85rem;
}

.modern-btn:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.modern-btn:hover:before {
    left: 100%;
}

.btn-modern-warning {
    background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
    color: white;
    box-shadow: 0 8px 30px rgba(245, 158, 11, 0.3);
}

.btn-modern-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(245, 158, 11, 0.4);
    color: white;
}

.btn-modern-secondary {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #475569 100%);
    color: white;
    box-shadow: 0 8px 30px rgba(100, 116, 139, 0.3);
}

.btn-modern-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(100, 116, 139, 0.4);
    color: white;
}

.error-alert {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%);
    border: 2px solid rgba(239, 68, 68, 0.2);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.error-alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.error-alert li {
    color: var(--danger-color);
    font-weight: 500;
}

.invalid-feedback {
    font-weight: 500;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: var(--danger-color);
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.modern-footer {
    background: var(--light-bg);
    border-radius: 0 0 24px 24px;
    padding: 2rem;
    border-top: 1px solid var(--border-color);
}

.input-group-modern {
    position: relative;
}

.input-group-modern .form-control {
    padding-left: 3rem;
}

.input-group-modern .input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    z-index: 3;
}

.change-indicator {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.05) 100%);
    border-left: 4px solid var(--warning-color);
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 0 8px 8px 0;
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="modern-card">
                <div class="modern-header">
                    <h3 class="mb-0">
                        <i class="fas fa-edit me-3"></i>
                        Edit Project
                    </h3>
                    <div class="project-info-badge">
                        <i class="fas fa-project-diagram me-2"></i>
                        {{ $project->project_name }} • {{ $project->project_code }}
                    </div>
                </div>

                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4">
                        @if ($errors->any())
                        <div class="error-alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                <strong class="text-danger">Please correct the following errors:</strong>
                            </div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="change-indicator">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-warning me-2"></i>
                                <span class="text-warning fw-medium">Editing existing project - changes will be saved immediately upon submission.</span>
                            </div>
                        </div>

                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i>
                                Basic Information
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="project_name" class="form-label">
                                            Project Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group-modern">
                                            <i class="fas fa-project-diagram input-icon"></i>
                                            <input type="text" class="form-control modern-form-control @error('project_name') is-invalid @enderror"
                                                   id="project_name" name="project_name" value="{{ old('project_name', $project->project_name) }}"
                                                   placeholder="Enter project name" required>
                                        </div>
                                        @error('project_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="client_id" class="form-label">
                                            Client <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select modern-form-control @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->company_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Configuration Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-cogs"></i>
                                Project Configuration
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="project_type" class="form-label">
                                            Project Type <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select modern-form-control @error('project_type') is-invalid @enderror" id="project_type" name="project_type" required>
                                            <option value="">Select Type</option>
                                            <option value="discovery" {{ old('project_type', $project->project_type) == 'discovery' ? 'selected' : '' }}>Discovery</option>
                                            <option value="servicing" {{ old('project_type', $project->project_type) == 'servicing' ? 'selected' : '' }}>Servicing</option>
                                            <option value="support" {{ old('project_type', $project->project_type) == 'support' ? 'selected' : '' }}>Support</option>
                                            <option value="maintenance" {{ old('project_type', $project->project_type) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                            <option value="installation" {{ old('project_type', $project->project_type) == 'installation' ? 'selected' : '' }}>Installation</option>
                                            <option value="upgrade" {{ old('project_type', $project->project_type) == 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                                            <option value="decommission" {{ old('project_type', $project->project_type) == 'decommission' ? 'selected' : '' }}>Decommission</option>
                                        </select>
                                        @error('project_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="priority" class="form-label">
                                            Priority Level <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select modern-form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                            <option value="normal" {{ old('priority', $project->priority) == 'normal' ? 'selected' : '' }}>Normal</option>
                                            <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="emergency" {{ old('priority', $project->priority) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                        </select>
                                        @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    Project Description
                                </label>
                                <textarea class="form-control modern-form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4"
                                          placeholder="Describe the project objectives and scope">{{ old('description', $project->description) }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Timeline & Resources Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-calendar-alt"></i>
                                Timeline & Resources
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="start_date" class="form-label">
                                            Start Date
                                        </label>
                                        <div class="input-group-modern">
                                            <i class="fas fa-calendar-plus input-icon"></i>
                                            <input type="date" class="form-control modern-form-control @error('start_date') is-invalid @enderror"
                                                   id="start_date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                                        </div>
                                        @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="end_date" class="form-label">
                                            Expected End Date
                                        </label>
                                        <div class="input-group-modern">
                                            <i class="fas fa-calendar-check input-icon"></i>
                                            <input type="date" class="form-control modern-form-control @error('end_date') is-invalid @enderror"
                                                   id="end_date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}">
                                        </div>
                                        @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="project_manager_id" class="form-label">
                                            Project Manager
                                        </label>
                                        <select class="form-select modern-form-control @error('project_manager_id') is-invalid @enderror"
                                                id="project_manager_id" name="project_manager_id">
                                            <option value="">Select Manager (Optional)</option>
                                            @foreach($projectManagers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('project_manager_id', $project->project_manager_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('project_manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="estimated_terminals_count" class="form-label">
                                            Estimated Terminal Count
                                        </label>
                                        <div class="input-group-modern">
                                            <i class="fas fa-desktop input-icon"></i>
                                            <input type="number" class="form-control modern-form-control @error('estimated_terminals_count') is-invalid @enderror"
                                                   id="estimated_terminals_count" name="estimated_terminals_count"
                                                   value="{{ old('estimated_terminals_count', $project->estimated_terminals_count) }}" min="0">
                                        </div>
                                        @error('estimated_terminals_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Budget & Additional Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-dollar-sign"></i>
                                Budget & Additional Information
                            </div>

                            <div class="mb-4">
                                <label for="budget" class="form-label">
                                    Budget (USD)
                                </label>
                                <div class="input-group-modern">
                                    <i class="fas fa-money-bill-wave input-icon"></i>
                                    <input type="number" class="form-control modern-form-control @error('budget') is-invalid @enderror"
                                           id="budget" name="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0">
                                </div>
                                @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label">
                                    Additional Notes
                                </label>
                                <textarea class="form-control modern-form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="3"
                                          placeholder="Any additional information or requirements">{{ old('notes', $project->notes) }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modern-footer d-flex justify-content-between align-items-center">
                        <a href="{{ route('projects.show', $project) }}" class="modern-btn btn-modern-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Cancel Changes
                        </a>
                        <button type="submit" class="modern-btn btn-modern-warning">
                            <i class="fas fa-save me-2"></i> Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
