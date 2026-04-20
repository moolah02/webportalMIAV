@extends('layouts.app')
@section('title', 'New Project')

@section('content')
<!-- Alpine.js for React-like reactivity -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root {
    --primary-color: #1a3a5c;
    --primary-hover: #152e4a;
    --success-color: #48bb78;
    --warning-color: #ed8936;
    --danger-color: #fc5c65;
    --info-color: #1a3a5c;
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
    box-shadow: 0 4px 12px rgba(26, 58, 92, 0.15);
    transform: translateY(-2px);
}

.project-type-card.selected {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, rgba(26, 58, 92, 0.05) 0%, rgba(26, 58, 92, 0.1) 100%);
}

</style>

<div
     x-data="{
         step: 1,
         selectedClient: null,
         clientInfo: null,
         selectedProjectType: '',
         otherProjectType: '',
         startDate: '{{ date('Y-m-d') }}',
         durationDays: 30,
         budget: null,
         budgetTemplate: '',

         async loadClientInfo(clientId) {
             if (!clientId) return;

             try {
                 const response = await fetch(`/api/clients/${clientId}/info`);
                 this.clientInfo = await response.json();
             } catch (error) {
                 console.error('Failed to load client info:', error);
             }
         },

         selectProjectType(type) {
             this.selectedProjectType = type;
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

         dummy: null
     }">

    <div class="row justify-content-center">
        <div>

            <!-- Workflow Progress -->
            <div class="ui-card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="ui-card-body p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Project Creation Workflow</p>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="workflow-step" :class="step === 1 ? 'active' : 'inactive'">
                            <div class="font-bold text-sm mb-1">STEP 1</div>
                            <div class="text-sm">Basic Setup</div>
                            <div class="text-xs text-gray-400" x-show="step === 1">You are here</div>
                        </div>
                        <div class="workflow-step inactive">
                            <div class="font-bold text-sm mb-1">STEP 2</div>
                            <div class="text-sm">Assign Technicians</div>
                            <div class="text-xs text-gray-400">Deploy to field</div>
                        </div>
                        <div class="workflow-step inactive">
                            <div class="font-bold text-sm mb-1">STEP 3</div>
                            <div class="text-sm">Track Progress</div>
                            <div class="text-xs text-gray-400">Monitor visits</div>
                        </div>
                        <div class="workflow-step inactive">
                            <div class="font-bold text-sm mb-1">STEP 4</div>
                            <div class="text-sm">Close Project</div>
                            <div class="text-xs text-gray-400">Generate reports</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="ui-card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="ui-card-header">
                    <div>
                        <h5 class="m-0 font-semibold text-gray-900">Create New Project</h5>
                        <p class="m-0 mt-1 text-sm text-gray-500">Quick setup — only 3 required fields</p>
                    </div>
                </div>

                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="ui-card-body p-4">
                        @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                            <strong class="font-semibold">Please correct the following errors:</strong>
                            <ul class="mt-2 ml-4 list-disc">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Project Name -->
                        <div class="mb-4">
                            <label class="ui-label">
                                Project Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" class="ui-input ui-input-lg"
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
                            <label class="ui-label">
                                Client <span class="text-red-500">*</span>
                            </label>
                            <select class="ui-select ui-select-lg"
                                    id="client_id"
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
                                    <div>
                                        <div class="fw-bold text-primary" x-text="clientInfo?.total_terminals || 0"></div>
                                        <small class="text-muted">Total Terminals</small>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-success" x-text="clientInfo?.active_terminals || 0"></div>
                                        <small class="text-muted">Active</small>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-info" x-text="clientInfo?.primary_region || '--'"></div>
                                        <small class="text-muted">Primary Region</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Type (Visual Cards) -->
                        <div class="mb-4">
                            <label class="ui-label">
                                Project Type <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div @click="selectProjectType('maintenance')">
                                    <div class="project-type-card" :class="selectedProjectType === 'maintenance' ? 'selected' : ''">
                                        <div class="fs-2 mb-2">🔧</div>
                                        <div class="fw-bold">Maintenance & Repairs</div>
                                        <small class="text-muted">Regular upkeep and servicing</small>
                                        <input type="radio" name="_project_type_radio" value="maintenance"
                                               :checked="selectedProjectType === 'maintenance'" hidden>
                                    </div>
                                </div>
                                <div @click="selectProjectType('installation')">
                                    <div class="project-type-card" :class="selectedProjectType === 'installation' ? 'selected' : ''">
                                        <div class="fs-2 mb-2">📦</div>
                                        <div class="fw-bold">Installation & Setup</div>
                                        <small class="text-muted">New terminal deployment</small>
                                        <input type="radio" name="_project_type_radio" value="installation"
                                               :checked="selectedProjectType === 'installation'" hidden>
                                    </div>
                                </div>
                                <div @click="selectProjectType('support')">
                                    <div class="project-type-card" :class="selectedProjectType === 'support' ? 'selected' : ''">
                                        <div class="fs-2 mb-2">💬</div>
                                        <div class="fw-bold">Support & Troubleshooting</div>
                                        <small class="text-muted">Issue resolution</small>
                                        <input type="radio" name="_project_type_radio" value="support"
                                               :checked="selectedProjectType === 'support'" hidden>
                                    </div>
                                </div>
                                <div @click="selectProjectType('other')">
                                    <div class="project-type-card" :class="selectedProjectType === 'other' ? 'selected' : ''">
                                        <div class="fs-2 mb-2">📝</div>
                                        <div class="fw-bold">Other</div>
                                        <small class="text-muted">Specify project type below</small>
                                        <input type="radio" name="_project_type_radio" value="other"
                                               :checked="selectedProjectType === 'other'" hidden>
                                    </div>
                                </div>
                            </div>
                            <!-- Other type text input -->
                            <div class="mt-3" x-show="selectedProjectType === 'other'" x-cloak>
                                <input type="text"
                                       class="ui-input"
                                       placeholder="Describe the project type (e.g. Audit, Training, Migration…)"
                                       x-model="otherProjectType"
                                       maxlength="100">
                            </div>
                            <!-- Hidden input that carries the final value -->
                            <input type="hidden" name="project_type"
                                   :value="selectedProjectType === 'other' ? otherProjectType : selectedProjectType">
                        </div>

                        <hr class="my-4">

                        <!-- Timeline (Smart Duration Selector) -->
                        <h5 class="mb-3 font-semibold text-gray-800 text-base"><i class="fas fa-calendar-alt me-2"></i>Timeline & Resources</h5>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="ui-input"
                                       name="start_date"
                                       x-model="startDate"
                                       :min="new Date().toISOString().split('T')[0]"
                                       value="{{ date('Y-m-d') }}">
                                <div class="field-hint">
                                    <i class="fas fa-check text-success"></i>
                                    Pre-filled with today's date
                                </div>
                            </div>

                            <div>
                                <label class="form-label fw-bold">Duration (Days)</label>
                                <input type="number" class="ui-input"
                                       name="duration_days"
                                       x-model="durationDays"
                                       min="1"
                                       placeholder="e.g. 30">
                                <input type="hidden" name="end_date" :value="calculateEndDate()">
                            </div>
                            <div>
                                <label class="form-label fw-bold">End Date</label>
                                <input type="text" class="ui-input" readonly
                                       :value="calculateEndDate()"
                                       style="background: #f8fafc;">
                                <div class="field-hint">Auto-calculated</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Optional Fields (Collapsible) -->
                        <details>
                            <summary class="font-semibold mb-3 cursor-pointer" style="color: var(--primary-color);">
                                <i class="fas fa-chevron-down me-2"></i>
                                Optional Fields (Budget, Manager, Notes)
                            </summary>

                            <div class="mt-3">
                                <!-- Budget with Templates -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Budget (USD)</label>
                                    <div class="btn-group mb-2 d-block">
                                        <button type="button" class="btn-sm btn-outline-secondary"
                                                @click="applyBudgetTemplate(5000)">
                                            Small (~$5K)
                                        </button>
                                        <button type="button" class="btn-sm btn-outline-secondary"
                                                @click="applyBudgetTemplate(15000)">
                                            Medium (~$15K)
                                        </button>
                                        <button type="button" class="btn-sm btn-outline-secondary"
                                                @click="applyBudgetTemplate(50000)">
                                            Large (~$50K)
                                        </button>
                                    </div>
                                    <input type="number" class="ui-input"
                                           name="budget"
                                           x-model="budget"
                                           step="0.01" min="0" placeholder="0.00">
                                </div>

                                <!-- Project Manager -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Project Manager</label>
                                    <select class="ui-select" name="project_manager_id">
                                        <option value="">Assign Later (Optional)</option>
                                        @foreach($projectManagers as $manager)
                                        <option value="{{ $manager->id }}">{{ $manager->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Priority -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Priority Level</label>
                                    <select class="ui-select" name="priority">
                                        <option value="normal" selected>🟢 Normal Priority</option>
                                        <option value="high">🔴 High Priority</option>
                                        <option value="low">⚪ Low Priority</option>
                                        <option value="emergency">🚨 Emergency</option>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Project Description</label>
                                    <textarea class="ui-input" name="description" rows="3"
                                              placeholder="Optional: Describe objectives, scope, and deliverables...">{{ old('description') }}</textarea>
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Additional Notes</label>
                                    <textarea class="ui-input" name="notes" rows="2"
                                              placeholder="Optional: Special requirements or constraints...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </details>

                        <hr class="my-4">

                        {{-- Terminal Upload Section --}}
                        @include('projects.partials.terminal-upload-section')

                    </div>

                    <!-- Footer -->
                    <div class="px-5 py-4 border-t border-gray-100 flex justify-between items-center bg-gray-50">
                        <a href="{{ route('projects.index') }}" class="btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn-primary">
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
