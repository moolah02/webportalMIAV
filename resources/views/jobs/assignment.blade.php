@extends('layouts.app')

@section('content')
<div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">🔧 Technician Job Assignment</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Assign technicians to regions and POS terminals for service visits</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="exportAssignments()" class="btn" style="background: #4caf50; color: white; border-color: #4caf50;">
                📊 Export CSV
            </button>
            <button onclick="refreshData()" class="btn btn-primary">🔄 Refresh</button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">📅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;" id="todayAssignments">{{ $stats['today_assignments'] ?? 5 }}</div>
                    <div style="font-size: 14px opacity: 0.9;">Today's Assignments</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⏳</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;" id="pendingAssignments">{{ $stats['pending_assignments'] ?? 12 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Pending</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">🔄</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;" id="inProgressAssignments">{{ $stats['in_progress_assignments'] ?? 8 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">In Progress</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">✅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;" id="completedToday">{{ $stats['completed_today'] ?? 15 }}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Completed Today</div>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 3fr; gap: 20px;">
        <!-- Assignment Form -->
        <div class="content-card">
            <h4 style="margin-block-end: 20px; color: #333;">📝 Create New Assignment</h4>
            
            <form action="{{ route('jobs.assignment.store') }}" method="POST" id="jobAssignmentForm">
                @csrf
                
                <!-- Hidden field for JSON terminals -->
                <input type="hidden" name="pos_terminals" id="pos_terminals_json" value="">
                
                <div style="margin-block-end: 20px;">
                    <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Technician *</label>
                    <select id="technician" name="technician_id" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        <option value="">Select Technician</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" 
                                    data-specialization="{{ $technician->specialization }}" 
                                    data-phone="{{ $technician->phone }}">
                                {{ $technician->name }} - {{ $technician->specialization }}
                            </option>
                        @endforeach
                    </select>
                    <div id="technicianInfo" style="font-size: 12px; color: #666; margin-block-start: 5px;"></div>
                    @error('technician_id')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-block-end: 20px;">
                    <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Region *</label>
                    <select id="region" name="region_id" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" data-terminals="{{ $region->terminals_count }}">
                                {{ $region->name }} ({{ $region->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-block-end: 20px;">
                    <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Client (Optional)</label>
                    <select id="client" name="client_id" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-block-end: 20px;">
                    <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">POS Terminals *</label>
                    <div style="border: 2px solid #ddd; border-radius: 6px; padding: 15px; max-height: 200px; overflow-y: auto; background: #f8f9fa;">
                        <div id="terminalsContainer">
                            <div style="text-align: center; color: #666; padding: 20px;">
                                <span style="font-size: 32px;">🖥️</span>
                                <div style="margin-block-start: 10px;">Select a region to view terminals</div>
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 12px; color: #666; margin-block-start: 5px; display: flex; justify-content: space-between; align-items: center;">
                        <span id="selectedCount">0 terminals selected</span>
                        <div>
                            <a href="#" onclick="selectAllTerminals()" style="color: #2196f3; text-decoration: none; margin-right: 10px;">Select All</a>
                            <a href="#" onclick="clearAllTerminals()" style="color: #f44336; text-decoration: none;">Clear All</a>
                        </div>
                    </div>
                    @error('pos_terminals')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-block-end: 20px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Scheduled Date *</label>
                        <input type="date" id="scheduledDate" name="scheduled_date" 
                               value="{{ old('scheduled_date', date('Y-m-d')) }}" required 
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        @error('scheduled_date')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Service Type *</label>
                        <select id="serviceType" name="service_type" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                            <option value="">Select Service</option>
                            {{-- UPDATED: Use categories for service types --}}
                            @foreach($serviceTypes as $serviceType)
                                <option value="{{ $serviceType->slug }}" 
                                        {{ old('service_type') == $serviceType->slug ? 'selected' : '' }}
                                        data-icon="{{ $serviceType->icon }}"
                                        data-color="{{ $serviceType->color }}">
                                    @if($serviceType->icon) {{ $serviceType->icon }} @endif {{ $serviceType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_type')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-block-end: 20px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Priority *</label>
                        <select id="priority" name="priority" required style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                            <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>🔵 Normal</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🟡 High</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>⚪ Low</option>
                            <option value="emergency" {{ old('priority') == 'emergency' ? 'selected' : '' }}>🔴 Emergency</option>
                        </select>
                        @error('priority')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Est. Duration (hours)</label>
                        <input type="number" id="estimatedDuration" name="estimated_duration_hours" 
                               value="{{ old('estimated_duration_hours') }}" 
                               step="0.5" min="0.5" max="8" placeholder="2.0"
                               style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        @error('estimated_duration_hours')
                            <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="margin-block-end: 25px;">
                    <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Notes/Instructions</label>
                    <textarea id="notes" name="notes" rows="3" 
                              placeholder="Special instructions, contact details, or notes for the technician..."
                              style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div style="color: #f44336; font-size: 12px; margin-block-start: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        ➕ Create Assignment
                    </button>
                    <button type="button" onclick="resetForm()" class="btn">
                        🔄 Reset Form
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Assignments -->
        <div class="content-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 20px;">
                <h4 style="margin: 0; color: #333;">📋 Current Assignments</h4>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <select id="assignmentFilter" style="padding: 5px 10px; border: 2px solid #ddd; border-radius: 4px;">
                        <option value="">All Assignments</option>
                        {{-- UPDATED: Use status options from controller --}}
                        @foreach($statusOptions as $slug => $name)
                            <option value="{{ $slug }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Assignments List -->
            <div style="max-height: 600px; overflow-y: auto;">
                <div id="assignmentsList">
                    @forelse($assignments as $assignment)
                    <div class="assignment-card" data-status="{{ $assignment->status }}">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-block-end: 10px;">
                            <div>
                                <div style="font-weight: 600; color: #333; margin-block-end: 5px;">#{{ $assignment->assignment_number ?? $assignment->assignment_id }}</div>
                                <div style="font-size: 14px; color: #666;">{{ $assignment->technician->name ?? 'N/A' }} • {{ $assignment->region->name ?? 'N/A' }}</div>
                            </div>
                            <span class="status-badge status-{{ $assignment->status }}">{{ ucfirst(str_replace('_', ' ', $assignment->status)) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="background: #e3f2fd; color: #1976d2; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                                    {{ count($assignment->pos_terminals ?? []) }} terminals
                                </span>
                                <span style="font-size: 14px; color: #666;">{{ $assignment->scheduled_date->format('M d, Y') }}</span>
                            </div>
                            <span class="priority-badge priority-{{ $assignment->priority }}">{{ ucfirst($assignment->priority) }}</span>
                        </div>
                        
                        {{-- UPDATED: Show service type with icon from categories --}}
                        @php
                            $serviceTypeCategory = \App\Models\Category::findBySlugAndType($assignment->service_type, \App\Models\Category::TYPE_SERVICE_TYPE);
                        @endphp
                        @if($serviceTypeCategory)
                        <div style="margin-block-end: 10px;">
                            <span style="background: {{ $serviceTypeCategory->color }}15; color: {{ $serviceTypeCategory->color }}; padding: 2px 6px; border-radius: 8px; font-size: 11px;">
                                @if($serviceTypeCategory->icon) {{ $serviceTypeCategory->icon }} @endif {{ $serviceTypeCategory->name }}
                            </span>
                        </div>
                        @endif
                        
                        <div style="display: flex; gap: 5px;">
                            <button onclick="viewAssignment({{ $assignment->id }})" class="btn-small">👁️ View</button>
                            @if($assignment->status == 'assigned')
                                <button onclick="editAssignment({{ $assignment->id }})" class="btn-small">✏️ Edit</button>
                                <button onclick="cancelAssignment({{ $assignment->id }})" class="btn-small" style="background: #f44336; color: white; border-color: #f44336;">❌ Cancel</button>
                            @elseif($assignment->status == 'in_progress')
                                <button onclick="completeAssignment({{ $assignment->id }})" class="btn-small" style="background: #4caf50; color: white; border-color: #4caf50;">✅ Complete</button>
                            @elseif($assignment->status == 'completed')
                                <button onclick="generateReport({{ $assignment->id }})" class="btn-small" style="background: #9c27b0; color: white; border-color: #9c27b0;">📄 Report</button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; padding: 60px; color: #666;">
                        <div style="font-size: 64px; margin-block-end: 20px;">📋</div>
                        <h3>No assignments found</h3>
                        <p>Create your first technician assignment to get started.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Details Modal -->
<div id="assignmentModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 600px; inline-size: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-height: 80vh; overflow-y: auto;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>🔧</span>
                <span id="modalTitle">Assignment Details</span>
            </h3>
            <button onclick="closeModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        <div id="modalContent" style="padding: 20px;">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<style>
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.assignment-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-block-end: 15px;
    transition: all 0.3s ease;
}

.assignment-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #2196f3;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-assigned { background: #e3f2fd; color: #1976d2; }
.status-in_progress { background: #fff3e0; color: #f57c00; }
.status-completed { background: #e8f5e8; color: #2e7d32; }
.status-cancelled { background: #ffebee; color: #d32f2f; }
.status-in.progress { background: #fff3e0; color: #f57c00; }

.priority-badge {
    padding: 2px 6px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 500;
}

.priority-emergency { background: #ffebee; color: #d32f2f; }
.priority-high { background: #fff3e0; color: #f57c00; }
.priority-normal { background: #e3f2fd; color: #1976d2; }
.priority-low { background: #f5f5f5; color: #666; }

.btn {
    padding: 8px 16px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
}

.btn-primary {
    background: #2196f3;
    color: white;
    border-color: #2196f3;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}

.btn-small {
    padding: 4px 8px;
    font-size: 12px;
    border: 1px solid #ddd;
}

.terminal-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    margin-block-end: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.terminal-checkbox:hover {
    background: #f0f7ff;
    border-color: #2196f3;
}

.terminal-checkbox input[type="checkbox"] {
    cursor: pointer;
}
</style>

// FIXED JavaScript - Replace the JavaScript section in your Blade template

<script>
let selectedTerminals = [];

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Setup event listeners
    setupEventListeners();
    
    // Add CSRF token to meta if not already present
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const metaTag = document.createElement('meta');
        metaTag.name = 'csrf-token';
        metaTag.content = '{{ csrf_token() }}';
        document.head.appendChild(metaTag);
    }
});

function setupEventListeners() {
    // Technician selection
    document.getElementById('technician').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const info = document.getElementById('technicianInfo');
        
        if (option.value) {
            const specialization = option.dataset.specialization;
            const phone = option.dataset.phone;
            info.innerHTML = `<span style="color: #2196f3;">Specialization: ${specialization} | Phone: ${phone}</span>`;
        } else {
            info.innerHTML = '';
        }
    });

    // Region selection - FIXED
    document.getElementById('region').addEventListener('change', loadTerminals);
    document.getElementById('client').addEventListener('change', loadTerminals);

    // Assignment filter
    document.getElementById('assignmentFilter').addEventListener('change', filterAssignments);

    // Form submission handler - FIXED
    document.getElementById('jobAssignmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Form submission started');
        
        // Validate terminals selection
        if (selectedTerminals.length === 0) {
            alert('Please select at least one terminal');
            return false;
        }
        
        // Update hidden JSON field with selected terminals
        document.getElementById('pos_terminals_json').value = JSON.stringify(selectedTerminals);
        
        console.log('Selected terminals:', selectedTerminals);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '⏳ Creating Assignment...';
        submitBtn.disabled = true;
        
        // Create FormData and submit via fetch
        const formData = new FormData(this);
        
        // Debug FormData contents
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', [...response.headers.entries()]);
            
            if (response.ok) {
                return response.json();
            } else {
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP ${response.status}`);
                });
            }
        })
        .then(data => {
            console.log('Success response:', data);
            alert(data.message || 'Assignment created successfully!');
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating assignment: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
}

function loadTerminals() {
    const regionId = document.getElementById('region').value;
    const clientId = document.getElementById('client').value;
    
    console.log('Loading terminals for region:', regionId, 'client:', clientId);
    
    if (!regionId) {
        document.getElementById('terminalsContainer').innerHTML = `
            <div style="text-align: center; color: #666; padding: 20px;">
                <span style="font-size: 32px;">🖥️</span>
                <div style="margin-block-start: 10px;">Select a region to view terminals</div>
            </div>
        `;
        return;
    }

    // Show loading state
    document.getElementById('terminalsContainer').innerHTML = `
        <div style="text-align: center; color: #666; padding: 20px;">
            <span style="font-size: 32px;">⏳</span>
            <div style="margin-block-start: 10px;">Loading terminals...</div>
        </div>
    `;

    // Build API URL - FIXED
    let apiUrl = `/api/regions/${regionId}/terminals`;
    const params = new URLSearchParams();
    
    if (clientId) {
        params.append('client_id', clientId);
    }
    
    if (params.toString()) {
        apiUrl += '?' + params.toString();
    }

    console.log('API URL:', apiUrl);

    // Make AJAX call to load terminals
    fetch(apiUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Terminals response status:', response.status);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Response text:', text);
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Terminals data received:', data);
        
        if (data.success) {
            const terminals = data.terminals || [];
            renderTerminals(terminals);
        } else {
            throw new Error(data.message || 'Failed to load terminals');
        }
    })
    .catch(error => {
        console.error('Error loading terminals:', error);
        
        // Show error state
        document.getElementById('terminalsContainer').innerHTML = `
            <div style="text-align: center; color: #f44336; padding: 20px;">
                <span style="font-size: 32px;">⚠️</span>
                <div style="margin-block-start: 10px;">Error loading terminals</div>
                <div style="font-size: 12px; margin-block-start: 5px; color: #999;">${error.message}</div>
            </div>
        `;
    });
}

function renderTerminals(terminals) {
    const container = document.getElementById('terminalsContainer');
    
    console.log('Rendering terminals:', terminals);
    
    if (!terminals || terminals.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; color: #666; padding: 20px;">
                <span style="font-size: 32px;">🖥️</span>
                <div style="margin-block-start: 10px;">No terminals found in this region</div>
            </div>
        `;
        return;
    }
    
    let html = '';
    terminals.forEach(terminal => {
        const statusColor = getStatusColor(terminal.status || 'unknown');
        const address = terminal.address || terminal.physical_address || 'No address';
        const merchantName = terminal.merchant_name || 'Unknown Merchant';
        
        html += `
            <div class="terminal-checkbox" onclick="toggleTerminal(${terminal.id}, this)">
                <input type="checkbox" id="terminal_${terminal.id}" value="${terminal.id}" onclick="event.stopPropagation();">
                <div style="flex: 1;">
                    <div style="font-weight: 500;">${terminal.terminal_id} - ${merchantName}</div>
                    <div style="font-size: 12px; color: #666;">${address}</div>
                </div>
                <span style="background: ${statusColor}; color: white; padding: 2px 6px; border-radius: 8px; font-size: 10px;">
                    ${(terminal.status || 'UNKNOWN').toUpperCase()}
                </span>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Clear selected terminals when new terminals are loaded
    selectedTerminals = [];
    updateSelectedCount();
}

function getStatusColor(status) {
    const colors = {
        'active': '#4caf50',
        'offline': '#f44336',
        'maintenance': '#ff9800',
        'faulty': '#9e9e9e',
        'unknown': '#9e9e9e'
    };
    return colors[status.toLowerCase()] || '#9e9e9e';
}

function toggleTerminal(terminalId, element) {
    const checkbox = element.querySelector('input[type="checkbox"]');
    
    // Toggle checkbox state
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        if (!selectedTerminals.includes(terminalId)) {
            selectedTerminals.push(terminalId);
        }
        element.style.background = '#e3f2fd';
        element.style.borderColor = '#2196f3';
    } else {
        selectedTerminals = selectedTerminals.filter(id => id !== terminalId);
        element.style.background = '';
        element.style.borderColor = '#e0e0e0';
    }
    
    updateSelectedCount();
    
    // Prevent event bubbling
    if (event) {
        event.stopPropagation();
    }
}

function selectAllTerminals() {
    const checkboxes = document.querySelectorAll('#terminalsContainer input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            const terminalId = parseInt(checkbox.value);
            checkbox.checked = true;
            if (!selectedTerminals.includes(terminalId)) {
                selectedTerminals.push(terminalId);
            }
            checkbox.closest('.terminal-checkbox').style.background = '#e3f2fd';
            checkbox.closest('.terminal-checkbox').style.borderColor = '#2196f3';
        }
    });
    updateSelectedCount();
}

function clearAllTerminals() {
    const checkboxes = document.querySelectorAll('#terminalsContainer input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        checkbox.closest('.terminal-checkbox').style.background = '';
        checkbox.closest('.terminal-checkbox').style.borderColor = '#e0e0e0';
    });
    selectedTerminals = [];
    updateSelectedCount();
}

function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = `${selectedTerminals.length} terminals selected`;
}

function resetForm() {
    document.getElementById('jobAssignmentForm').reset();
    selectedTerminals = [];
    updateSelectedCount();
    document.getElementById('terminalsContainer').innerHTML = `
        <div style="text-align: center; color: #666; padding: 20px;">
            <span style="font-size: 32px;">🖥️</span>
            <div style="margin-block-start: 10px;">Select a region to view terminals</div>
        </div>
    `;
    document.getElementById('technicianInfo').innerHTML = '';
}

// Rest of the functions remain the same...
function filterAssignments() {
    const filter = document.getElementById('assignmentFilter').value;
    const cards = document.querySelectorAll('.assignment-card');
    
    cards.forEach(card => {
        if (!filter || card.dataset.status === filter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function viewAssignment(id) {
    fetch(`/assignments/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(assignment => {
        document.getElementById('modalTitle').textContent = `Assignment #${assignment.assignment_number || assignment.assignment_id}`;
        document.getElementById('modalContent').innerHTML = `
            <div style="display: grid; gap: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h5 style="margin-block-end: 10px; color: #333;">👨‍🔧 Technician</h5>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="font-weight: 500;">${assignment.technician ? assignment.technician.name : 'N/A'}</div>
                            <div style="font-size: 14px; color: #666;">${assignment.technician ? assignment.technician.specialization : 'N/A'}</div>
                            <div style="font-size: 14px; color: #666;">📞 ${assignment.technician ? assignment.technician.phone : 'N/A'}</div>
                        </div>
                    </div>
                    <div>
                        <h5 style="margin-block-end: 10px; color: #333;">📍 Location</h5>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="font-weight: 500;">${assignment.region ? assignment.region.name : 'N/A'}</div>
                            <div style="font-size: 14px; color: #666;">${assignment.terminals_count || (assignment.pos_terminals ? assignment.pos_terminals.length : 0)} terminals assigned</div>
                            <div style="font-size: 14px; color: #666;">📅 ${assignment.scheduled_date}</div>
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button onclick="closeModal()" class="btn">Close</button>
                    ${assignment.status === 'assigned' ? `<button onclick="editAssignment(${id})" class="btn btn-primary">Edit Assignment</button>` : ''}
                </div>
            </div>
        `;
        document.getElementById('assignmentModal').style.display = 'flex';
    })
    .catch(error => {
        console.error('Error loading assignment details:', error);
        alert('Error loading assignment details');
    });
}

function editAssignment(id) {
    window.location.href = `/jobs/assignment/${id}/edit`;
    closeModal();
}

function cancelAssignment(id) {
    if (confirm('Are you sure you want to cancel this assignment?')) {
        fetch(`/jobs/assignment/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment cancelled successfully');
                location.reload();
            } else {
                alert('Error cancelling assignment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling assignment');
        });
    }
}

function completeAssignment(id) {
    if (confirm('Mark this assignment as completed?')) {
        fetch(`/assignments/${id}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment marked as completed');
                location.reload();
            } else {
                alert('Error completing assignment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error completing assignment');
        });
    }
}

function generateReport(id) {
    window.open(`/assignments/${id}/report`, '_blank');
}

function closeModal() {
    document.getElementById('assignmentModal').style.display = 'none';
}

function exportAssignments() {
    window.location.href = '/assignments/export';
}

function refreshData() {
    location.reload();
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('assignmentModal');
    if (event.target === modal) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection