@extends('layouts.app')

@section('content')
<!-- Alpine.js for React-like reactivity -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root {
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #3b82f6;
}

.workflow-step {
    position: relative;
    padding: 1rem;
    text-align: center;
}

.workflow-step.active {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    border-radius: 12px;
}

.workflow-step.inactive {
    background: #f1f5f9;
    color: #64748b;
    border-radius: 12px;
}

.client-info-box {
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    border-left: 4px solid var(--info-color);
    padding: 1.25rem;
    border-radius: 12px;
    margin-top: 1rem;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.field-hint {
    font-size: 0.875rem;
    color: #64748b;
    font-style: italic;
    margin-top: 0.25rem;
}

.validation-warning {
    background: #fef3c7;
    border-left: 4px solid var(--warning-color);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    animation: shake 0.3s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.project-type-card {
    padding: 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
}

.project-type-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
    transform: translateY(-2px);
}

.project-type-card.selected {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(79, 70, 229, 0.1) 100%);
}

.duration-button {
    padding: 0.75rem 1.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 600;
}

.duration-button:hover {
    border-color: var(--success-color);
    background: #f0fdf4;
}

.duration-button.active {
    border-color: var(--success-color);
    background: var(--success-color);
    color: white;
}
</style>

<div class="container-fluid"
     x-data="{
         step: 1,
         selectedClient: null,
         clientInfo: null,
         selectedProjectType: '',
         startDate: '{{ date('Y-m-d') }}',
         durationDays: 30,
         customDuration: false,
         estimatedTerminals: 0,
         budget: null,
         budgetTemplate: '',

         async loadClientInfo(clientId) {
             if (!clientId) return;

             try {
                 const response = await fetch(`/api/clients/${clientId}/info`);
                 this.clientInfo = await response.json();
                 this.estimatedTerminals = Math.min(this.estimatedTerminals || 0, this.clientInfo.total_terminals);
             } catch (error) {
                 console.error('Failed to load client info:', error);
             }
         },

         selectProjectType(type) {
             this.selectedProjectType = type;
         },

         setDuration(days) {
             this.durationDays = days;
             this.customDuration = false;
         },

         applyBudgetTemplate(amount) {
             this.budget = amount;
             this.budgetTemplate = amount;
         },

         calculateEndDate() {
             if (!this.startDate || !this.durationDays) return '';
             const start = new Date(this.startDate);
             start.setDate(start.getDate() + parseInt(this.durationDays));
             return start.toISOString().split('T')[0];
         },

         validateEstimatedTerminals() {
             if (!this.clientInfo) return true;
             return this.estimatedTerminals <= this.clientInfo.total_terminals;
         }
     }">

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">

            <!-- Workflow Progress -->
            <div class="card mb-4" style="border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-3">PROJECT CREATION WORKFLOW</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="workflow-step" :class="step === 1 ? 'active' : 'inactive'">
                                <div class="fw-bold mb-1">STEP 1</div>
                                <div class="small">Basic Setup</div>
                                <div class="tiny text-muted" x-show="step === 1">You are here</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="workflow-step inactive">
                                <div class="fw-bold mb-1">STEP 2</div>
                                <div class="small">Assign Terminals</div>
                                <div class="tiny text-muted">Via Job Assignments</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="workflow-step inactive">
                                <div class="fw-bold mb-1">STEP 3</div>
                                <div class="small">Track Progress</div>
                                <div class="tiny text-muted">Monitor visits</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="workflow-step inactive">
                                <div class="fw-bold mb-1">STEP 4</div>
                                <div class="small">Close Project</div>
                                <div class="tiny text-muted">Generate reports</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="card" style="border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 2.5rem; border-radius: 24px 24px 0 0;">
                    <h3 class="text-white mb-0">
                        <i class="fas fa-plus-circle me-3"></i>
                        Create New Project
                    </h3>
                    <p class="text-white-50 mb-0 mt-2">Quick setup - Only 3 required fields!</p>
                </div>

                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf

                    <div class="card-body p-4">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please correct the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Project Name -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Project Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg"
                                   name="project_name"
                                   value="{{ old('project_name') }}"
                                   placeholder="e.g., Q1 2026 Terminal Maintenance - Harare"
                                   required>
                            <div class="field-hint">
                                <i class="fas fa-lightbulb text-warning"></i>
                                Include period, type, and location for clarity
                            </div>
                        </div>

                        <!-- Client Selection with Live Info -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Client <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg"
                                    name="client_id"
                                    x-model="selectedClient"
                                    @change="loadClientInfo($event.target.value)"
                                    required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }} ({{ $client->pos_terminals_count }} terminals)
                                </option>
                                @endforeach
                            </select>

                            <!-- Live Client Info Box (Alpine.js Reactive) -->
                            <div x-show="clientInfo" x-transition class="client-info-box">
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Client Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="fw-bold text-primary" x-text="clientInfo?.total_terminals || 0"></div>
                                        <small class="text-muted">Total Terminals</small>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fw-bold text-success" x-text="clientInfo?.active_terminals || 0"></div>
                                        <small class="text-muted">Active</small>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fw-bold text-info" x-text="clientInfo?.primary_region || '--'"></div>
                                        <small class="text-muted">Primary Region</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Type (Visual Cards) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Project Type <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                <div class="col-md-4" @click="selectProjectType('maintenance')">
                                    <div class="project-type-card" :class="selectedProjectType === 'maintenance' ? 'selected' : ''">
                                        <div class="fs-2 mb-2">🔧</div>
                                        <div class="fw-bold">Maintenance & Repairs</div>
                                        <small class="text-muted">Regular upkeep and servicing</small>
                                        <input type="radio" name="project_type" value="maintenance"
                                               :checked="selectedProjectType === 'maintenance'" hidden>
                                    </div>
                                </div>
                                <div class="col-md-4" @click="selectProjectType('installation')">
                                    <div class="project-type-card" :class="selectedProjectType === 'installation' ? 'selected' : ''">
                                        <div class="fs-2 mb-2">📦</div>
                                        <div class="fw-bold">Installation & Setup</div>
                                        <small class="text-muted">New terminal deployment</small>
                                        <input type="radio" name="project_type" value="installation"
                                               :checked="selectedProjectType === 'installation'" hidden>
                                    </div>
                                </div>
                                <div class="col-md-4" @click="selectProjectType('support')">
                                    <div class="project-type-card" :class="selectedProjectType === 'support' ? 'selected' : ''">
                                        <div class="fs-2 mb-2">💬</div>
                                        <div class="fw-bold">Support & Troubleshooting</div>
                                        <small class="text-muted">Issue resolution</small>
                                        <input type="radio" name="project_type" value="support"
                                               :checked="selectedProjectType === 'support'" hidden>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Timeline (Smart Duration Selector) -->
                        <h5 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Timeline & Resources</h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control"
                                       name="start_date"
                                       x-model="startDate"
                                       :min="new Date().toISOString().split('T')[0]"
                                       value="{{ date('Y-m-d') }}">
                                <div class="field-hint">
                                    <i class="fas fa-check text-success"></i>
                                    Pre-filled with today's date
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Project Duration</label>
                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" class="duration-button"
                                            :class="durationDays === 7 ? 'active' : ''"
                                            @click="setDuration(7)">
                                        1 Week
                                    </button>
                                    <button type="button" class="duration-button"
                                            :class="durationDays === 30 ? 'active' : ''"
                                            @click="setDuration(30)">
                                        1 Month
                                    </button>
                                    <button type="button" class="duration-button"
                                            :class="durationDays === 90 ? 'active' : ''"
                                            @click="setDuration(90)">
                                        3 Months
                                    </button>
                                </div>
                                <input type="hidden" name="end_date" :value="calculateEndDate()">
                                <input type="number" x-show="customDuration" class="form-control"
                                       x-model="durationDays" placeholder="Custom days">
                                <div class="field-hint" x-text="`End date: ${calculateEndDate()}`"></div>
                            </div>
                        </div>

                        <!-- Estimated Terminals with Validation -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Estimated Terminal Count</label>
                            <input type="number" class="form-control"
                                   name="estimated_terminals_count"
                                   x-model="estimatedTerminals"
                                   min="0"
                                   placeholder="How many terminals will this project cover?">

                            <!-- Real-time Validation Warning -->
                            <div x-show="clientInfo && estimatedTerminals > clientInfo.total_terminals"
                                 x-transition
                                 class="validation-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> You estimated
                                <span x-text="estimatedTerminals"></span> terminals, but this client only has
                                <span x-text="clientInfo?.total_terminals"></span> terminals!
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Optional Fields (Collapsible) -->
                        <details>
                            <summary class="fw-bold mb-3" style="cursor: pointer; color: var(--primary-color);">
                                <i class="fas fa-chevron-down me-2"></i>
                                Optional Fields (Budget, Manager, Notes)
                            </summary>

                            <div class="mt-3">
                                <!-- Budget with Templates -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Budget (USD)</label>
                                    <div class="btn-group mb-2 d-block">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                @click="applyBudgetTemplate(5000)">
                                            Small (~$5K)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                @click="applyBudgetTemplate(15000)">
                                            Medium (~$15K)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                @click="applyBudgetTemplate(50000)">
                                            Large (~$50K)
                                        </button>
                                    </div>
                                    <input type="number" class="form-control"
                                           name="budget"
                                           x-model="budget"
                                           step="0.01" min="0" placeholder="0.00">
                                </div>

                                <!-- Project Manager -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Project Manager</label>
                                    <select class="form-select" name="project_manager_id">
                                        <option value="">Assign Later (Optional)</option>
                                        @foreach($projectManagers as $manager)
                                        <option value="{{ $manager->id }}">{{ $manager->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Priority -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Priority Level</label>
                                    <select class="form-select" name="priority">
                                        <option value="normal" selected>🟢 Normal Priority</option>
                                        <option value="high">🔴 High Priority</option>
                                        <option value="low">⚪ Low Priority</option>
                                        <option value="emergency">🚨 Emergency</option>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Project Description</label>
                                    <textarea class="form-control" name="description" rows="3"
                                              placeholder="Optional: Describe objectives, scope, and deliverables...">{{ old('description') }}</textarea>
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Additional Notes</label>
                                    <textarea class="form-control" name="notes" rows="2"
                                              placeholder="Optional: Special requirements or constraints...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </details>

                    </div>

                    <!-- Footer -->
                    <div class="card-footer d-flex justify-content-between align-items-center p-4"
                         style="background: #f8fafc; border-radius: 0 0 24px 24px;">
                        <a href="{{ route('projects.index') }}" class="btn btn-lg btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-lg btn-primary">
                            <i class="fas fa-rocket me-2"></i> Create Project & Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add API endpoint for client info (temporary mock) -->
<script>
// Mock API until backend is ready
if (!window.fetch.original) {
    window.fetch.original = window.fetch;
    window.fetch = function(url, options) {
        if (url.includes('/api/clients/') && url.includes('/info')) {
            const clientId = url.match(/\/api\/clients\/(\d+)\/info/)[1];
            const client = @json($clients);
            const selectedClient = client.find(c => c.id == clientId);

            return Promise.resolve({
                json: () => Promise.resolve({
                    total_terminals: selectedClient?.pos_terminals_count || 0,
                    active_terminals: Math.floor((selectedClient?.pos_terminals_count || 0) * 0.8),
                    primary_region: 'Harare'  // You can enhance this
                })
            });
        }
        return window.fetch.original(url, options);
    };
}
</script>
@endsection
