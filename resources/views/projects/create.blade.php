@extends('layouts.app')

@section('content')
<!-- Alpine.js for reactive UI -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
    background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
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
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
}

.modern-header h3 {
    color: white;
    font-weight: 600;
    margin: 0;
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
    align-items: center;
}

.form-section-title i {
    margin-right: 0.5rem;
    color: var(--primary-color);
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
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    background: white;
    transform: translateY(-1px);
}

.form-label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
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

.btn-modern-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    box-shadow: 0 8px 30px rgba(79, 70, 229, 0.3);
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(79, 70, 229, 0.4);
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

.client-info {
    font-size: 0.85rem;
    color: var(--text-secondary);
    font-style: italic;
}

textarea.modern-form-control {
    resize: vertical;
    min-height: 120px;
}

.form-floating {
    position: relative;
}

.priority-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.priority-normal { background: var(--info-color); }
.priority-high { background: var(--danger-color); }
.priority-low { background: var(--secondary-color); }
.priority-emergency { background: var(--warning-color); }

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
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="modern-card">
                <div class="modern-header">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle me-3"></i>
                        Create New Project
                    </h3>
                    <p class="mb-0 mt-2 opacity-75">Set up a new project with all the essential details and requirements.</p>
                </div>

                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                                                   id="project_name" name="project_name" value="{{ old('project_name') }}"
                                                   placeholder="Enter a descriptive project name" required>
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
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->company_name }}
                                                <span class="client-info">({{ $client->pos_terminals_count }} terminals)</span>
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
                                            <option value="discovery" {{ old('project_type') == 'discovery' ? 'selected' : '' }}>🔍 Discovery</option>
                                            <option value="servicing" {{ old('project_type') == 'servicing' ? 'selected' : '' }}>🔧 Servicing</option>
                                            <option value="support" {{ old('project_type') == 'support' ? 'selected' : '' }}>💬 Support</option>
                                            <option value="maintenance" {{ old('project_type') == 'maintenance' ? 'selected' : '' }}>⚙️ Maintenance</option>
                                            <option value="installation" {{ old('project_type') == 'installation' ? 'selected' : '' }}>📦 Installation</option>
                                            <option value="upgrade" {{ old('project_type') == 'upgrade' ? 'selected' : '' }}>⬆️ Upgrade</option>
                                            <option value="decommission" {{ old('project_type') == 'decommission' ? 'selected' : '' }}>📤 Decommission</option>
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
                                            <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>
                                                <span class="priority-indicator priority-normal"></span>🟢 Normal Priority
                                            </option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                                <span class="priority-indicator priority-high"></span>🔴 High Priority
                                            </option>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                                <span class="priority-indicator priority-low"></span>⚪ Low Priority
                                            </option>
                                            <option value="emergency" {{ old('priority') == 'emergency' ? 'selected' : '' }}>
                                                <span class="priority-indicator priority-emergency"></span>🟡 Emergency
                                            </option>
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
                                          placeholder="Describe the project objectives, scope, and key deliverables...">{{ old('description') }}</textarea>
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
                                            Project Start Date
                                        </label>
                                        <div class="input-group-modern">
                                            <i class="fas fa-calendar-plus input-icon"></i>
                                            <input type="date" class="form-control modern-form-control @error('start_date') is-invalid @enderror"
                                                   id="start_date" name="start_date" value="{{ old('start_date') }}">
                                        </div>
                                        @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="end_date" class="form-label">
                                            Expected Completion Date
                                        </label>
                                        <div class="input-group-modern">
                                            <i class="fas fa-calendar-check input-icon"></i>
                                            <input type="date" class="form-control modern-form-control @error('end_date') is-invalid @enderror"
                                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
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
                                            <option value="{{ $manager->id }}" {{ old('project_manager_id') == $manager->id ? 'selected' : '' }}>
                                                👨‍💼 {{ $manager->full_name }}
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
                                                   value="{{ old('estimated_terminals_count') }}" min="0" placeholder="0">
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
                                    Project Budget (USD)
                                </label>
                                <div class="input-group-modern">
                                    <i class="fas fa-money-bill-wave input-icon"></i>
                                    <input type="number" class="form-control modern-form-control @error('budget') is-invalid @enderror"
                                           id="budget" name="budget" value="{{ old('budget') }}" step="0.01" min="0" placeholder="0.00">
                                </div>
                                @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label">
                                    Additional Notes & Requirements
                                </label>
                                <textarea class="form-control modern-form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="3"
                                          placeholder="Any special requirements, constraints, or additional information...">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Terminal Upload Section --}}
                        @include('projects.partials.terminal-upload-section')
                    </div>

                    <div class="modern-footer d-flex justify-content-between align-items-center">
                        <a href="{{ route('projects.index') }}" class="modern-btn btn-modern-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Cancel & Return
                        </a>
                        <button type="submit" class="modern-btn btn-modern-primary">
                            <i class="fas fa-rocket me-2"></i> Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced form interactions
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus first input
    document.getElementById('project_name').focus();

    // Form validation feedback
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.style.borderColor = 'var(--warning-color)';
            } else if (this.value.trim()) {
                this.style.borderColor = 'var(--success-color)';
            }
        });
    });
});
</script>
@endsection
