@extends('layouts.app')

@section('content')
<div>
    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">🚀 POS Terminal Deployment</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Deploy technicians to terminals across regions • Hierarchical assignment management</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" class="btn" style="background: #ff9800; color: white; border-color: #ff9800;" onclick="generateWorkOrders()">
                📋 Generate Work Orders
            </button>
            <button type="button" class="btn" style="background: #4caf50; color: white; border-color: #4caf50;" onclick="exportDeployment()">
                📊 Export Deployment
            </button>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-block-end: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-block-end: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Page Setup - Filters Section -->
    <div class="content-card" style="margin-block-end: 20px;">
        <!-- Step Indicators -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2196f3;">
            <h6 style="margin: 0 0 10px 0; color: #333; display: flex; align-items: center; gap: 8px;">
                📋 Deployment Progress
            </h6>
            <div id="stepProgress" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; font-size: 12px;">
                <div class="step-item active" id="step1">
                    <div class="step-circle">1</div>
                    <div>Select Clients & Projects</div>
                </div>
                <div class="step-item" id="step2">
                    <div class="step-circle">2</div>
                    <div>Load Hierarchy</div>
                </div>
                <div class="step-item" id="step3">
                    <div class="step-circle">3</div>
                    <div>Choose Technicians</div>
                </div>
                <div class="step-item" id="step4">
                    <div class="step-circle">4</div>
                    <div>Assign & Deploy</div>
                </div>
            </div>
        </div>

        <h4 style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px; color: #333;">
            🔧 Deployment Setup
        </h4>

        <div style="display: grid; grid-template-columns: 2fr 1fr 2fr 1fr; gap: 20px; align-items: end;">
            <!-- Client Selection -->
            <div>
                <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                    Select Clients <span style="color: #f44336;">*</span>
                </label>
                <div style="position: relative;">
                    <div class="custom-dropdown" id="clientDropdown">
                        <div class="dropdown-selected" onclick="toggleDropdown('clientDropdown')">
                            <span id="clientSelectedText">Choose clients...</span>
                            <i class="dropdown-arrow">▼</i>
                        </div>
                        <div class="dropdown-options" id="clientOptions">
                            <div class="dropdown-search">
                                <input type="text" placeholder="Search clients..." onkeyup="filterOptions('clientOptions', this.value)">
                            </div>
                            @foreach($clients as $client)
                                <label class="dropdown-option">
                                    <input type="checkbox" value="{{ $client['id'] }}" data-terminals="{{ $client['terminal_count'] }}" data-name="{{ $client['name'] }}" onchange="updateClientSelection()">
                                    <span>{{ $client['name'] }} ({{ $client['terminal_count'] }} terminals)</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <small style="color: #666;">Select multiple clients</small>
                </div>
            </div>

            <!-- Total Terminals Display -->
            <div>
                <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Total Terminals</label>
                <div style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 32px; font-weight: bold;" id="totalTerminalCount">0</div>
                    <div style="font-size: 12px; opacity: 0.9;">Selected</div>
                </div>
            </div>

            <!-- Project Selection -->
            <div>
                <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                    Projects <span style="color: #f44336;">*</span>
                </label>
                <div style="position: relative;">
                    <div class="custom-dropdown" id="projectDropdown">
                        <div class="dropdown-selected" onclick="toggleDropdown('projectDropdown')">
                            <span id="projectSelectedText">Select clients first...</span>
                            <i class="dropdown-arrow">▼</i>
                        </div>
                        <div class="dropdown-options" id="projectOptions">
                            <div class="dropdown-search">
                                <input type="text" placeholder="Search projects..." onkeyup="filterOptions('projectOptions', this.value)">
                            </div>
                            <div id="projectOptionsList">
                                <div class="dropdown-option disabled">Select clients first...</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="createNewProject()" style="margin-top: 8px; width: 100%; padding: 8px;">
                        ➕ Create New Project
                    </button>
                </div>
            </div>

            <!-- Start Date -->
            <div>
                <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Deployment Date</label>
                <input type="date" id="deploymentDate" value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px;">
                <button type="button" class="btn btn-success" onclick="loadHierarchy()" id="loadHierarchyBtn" disabled style="margin-top: 8px; width: 100%;">
                    🔒 Select Clients & Projects First
                </button>
            </div>
        </div>
    </div>

    <!-- Progress Stats - Hidden Initially -->
    <div style="display: none; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-block-end: 20px;" id="progressStats" class="progress-section">
        <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 24px;">🎯</div>
                <div>
                    <div style="font-size: 20px; font-weight: bold;" id="totalTerminals">0</div>
                    <div style="font-size: 12px; opacity: 0.9;">Total Terminals</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 24px;">✅</div>
                <div>
                    <div style="font-size: 20px; font-weight: bold;" id="assignedTerminals">0</div>
                    <div style="font-size: 12px; opacity: 0.9;">Assigned</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 24px;">⏳</div>
                <div>
                    <div style="font-size: 20px; font-weight: bold;" id="unassignedTerminals">0</div>
                    <div style="font-size: 12px; opacity: 0.9;">Unassigned</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 24px;">👥</div>
                <div>
                    <div style="font-size: 20px; font-weight: bold;" id="selectedTerminals">0</div>
                    <div style="font-size: 12px; opacity: 0.9;">Selected</div>
                </div>
            </div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 24px;">🔧</div>
                <div>
                    <div style="font-size: 20px; font-weight: bold;" id="technicianCount">0</div>
                    <div style="font-size: 12px; opacity: 0.9;">Technicians</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area - Hidden Initially -->
    <div style="display: none; grid-template-columns: 3fr 2fr; gap: 20px;" id="mainContentArea" class="main-content-section">

        <!-- Left Side - Terminal Table -->
        <div class="content-card" style="padding: 0;">
            <!-- Table Header -->
            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 20px; border-radius: 8px 8px 0 0; border-bottom: 2px solid #dee2e6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h4 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #333;">
                        📊 Terminal List
                        <span style="background: #667eea; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;" id="terminalCount">0</span>
                    </h4>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-small" onclick="selectAllVisible()" disabled id="selectAllBtn" title="Select All Visible">
                            ☑️ Select All
                        </button>
                        <button class="btn btn-small" onclick="clearSelections()" disabled id="clearAllBtn" title="Clear Selections">
                            ❌ Clear
                        </button>
                        <button class="btn btn-small" onclick="exportTableData()" disabled id="exportBtn" title="Export Data">
                            📊 Export
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 12px;">Province</label>
                        <select id="provinceFilter" onchange="applyFilters()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All Provinces</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 12px;">City</label>
                        <select id="cityFilter" onchange="applyFilters()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All Cities</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 12px;">Region</label>
                        <select id="regionFilter" onchange="applyFilters()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All Regions</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 12px;">Assignment Status</label>
                        <select id="assignmentFilter" onchange="applyFilters()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All Terminals</option>
                            <option value="assigned">✅ Assigned</option>
                            <option value="unassigned">⏳ Unassigned</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 12px;">Status</label>
                        <select id="statusFilter" onchange="applyFilters()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="offline">Offline</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="faulty">Faulty</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 12px;">Search</label>
                        <input type="text" id="searchFilter" placeholder="Search terminals..." onkeyup="applyFilters()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Terminal Table -->
            <div style="max-height: 600px; overflow: auto; background: white;">
                <table id="terminalTable" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                        <tr>
                            <th style="padding: 12px 8px; text-align: left; border-bottom: 2px solid #dee2e6; width: 40px;">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" style="cursor: pointer;">
                            </th>
                            <th style="padding: 12px 8px; text-align: left; border-bottom: 2px solid #dee2e6; min-width: 100px; font-weight: 600;">Terminal ID</th>
                            <th style="padding: 12px 8px; text-align: left; border-bottom: 2px solid #dee2e6; min-width: 200px; font-weight: 600;">Merchant Name</th>
                            <th style="padding: 12px 8px; text-align: left; border-bottom: 2px solid #dee2e6; min-width: 120px; font-weight: 600;">Province</th>
                            <th style="padding: 12px 8px; text-align: left; border-bottom: 2px solid #dee2e6; min-width: 120px; font-weight: 600;">City</th>
                            <th style="padding: 12px 8px; text-align: left; border-bottom: 2px solid #dee2e6; min-width: 120px; font-weight: 600;">Region</th>
                            <th style="padding: 12px 8px; text-align: center; border-bottom: 2px solid #dee2e6; min-width: 100px; font-weight: 600;">Assignment</th>
                            <th style="padding: 12px 8px; text-align: center; border-bottom: 2px solid #dee2e6; min-width: 80px; font-weight: 600;">Status</th>
                            <th style="padding: 12px 8px; text-align: center; border-bottom: 2px solid #dee2e6; min-width: 100px; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="terminalTableBody">
                        <tr>
                            <td colspan="9" style="padding: 60px 20px; text-align: center; color: #666;">
                                <div style="font-size: 48px; margin-bottom: 15px;">👈</div>
                                <h5>Step 1: Configure Deployment Setup</h5>
                                <p>Select clients and projects to load terminals</p>
                                <div style="margin-top: 20px; font-size: 14px; color: #999;">
                                    • Choose one or more clients<br>
                                    • Select associated projects
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div id="tablePagination" style="display: none; padding: 15px 20px; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 14px; color: #666;">
                        Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalTerminals">0</span> terminals
                    </div>
                    <div style="display: flex; gap: 5px;" id="paginationButtons">
                        <!-- Pagination buttons will be inserted here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Assignment & Tracking -->
        <div style="display: grid; gap: 20px;">

            <!-- Assignment Section -->
            <div class="content-card" id="assignmentSection">
                <h4 style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px; color: #333;">
                    👥 Technician Assignment
                </h4>

                <!-- Technician Selection -->
                <div style="margin-block-end: 15px;">
                    <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                        Select Technicians <span style="color: #f44336;">*</span>
                    </label>
                    <div class="custom-dropdown" id="technicianDropdown">
                        <div class="dropdown-selected" onclick="toggleDropdown('technicianDropdown')">
                            <span id="technicianSelectedText">Choose technicians...</span>
                            <i class="dropdown-arrow">▼</i>
                        </div>
                        <div class="dropdown-options" id="technicianOptions">
                            <div class="dropdown-search">
                                <input type="text" placeholder="Search technicians..." onkeyup="filterOptions('technicianOptions', this.value)">
                            </div>
                            @foreach($technicians as $tech)
                                <label class="dropdown-option">
                                    <input type="checkbox"
                                           value="{{ $tech['id'] }}"
                                           data-name="{{ $tech['name'] }}"
                                           data-spec="{{ $tech['specialization'] }}"
                                           data-availability="{{ $tech['availability_status'] }}"
                                           data-workload="{{ $tech['current_workload'] }}"
                                           onchange="updateTechnicianSelection()">
                                    <span>
                                        {{ $tech['name'] }} - {{ $tech['specialization'] }}
                                        @if($tech['availability_status'] !== 'available')
                                            <small style="color: #ff9800;">({{ ucfirst($tech['availability_status']) }})</small>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <small style="color: #666;">Select multiple technicians</small>
                </div>

                <!-- Assignment Mode -->
                <div style="margin-block-end: 15px;">
                    <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Assignment Mode</label>
                    <div style="display: grid; gap: 8px;">
                        <label style="display: flex; align-items: center; gap: 8px; padding: 8px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;" onclick="selectAssignmentMode('individual')">
                            <input type="radio" name="assignmentMode" value="individual" checked>
                            <div>
                                <strong>Individual Assignment</strong>
                                <div style="font-size: 12px; color: #666;">Distribute terminals among technicians</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 8px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;" onclick="selectAssignmentMode('team')">
                            <input type="radio" name="assignmentMode" value="team">
                            <div>
                                <strong>Team Assignment</strong>
                                <div style="font-size: 12px; color: #666;">All technicians work together</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Assignment Options -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-block-end: 15px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">Priority</label>
                        <select id="assignmentPriority" style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 6px;">
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">Service Type</label>
                        <select id="serviceType" style="width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 6px;">
                            <option value="routine_maintenance">Routine Maintenance</option>
                            <option value="emergency_repair">Emergency Repair</option>
                        </select>
                    </div>
                </div>

                <!-- Assignment Actions -->
                <div style="display: grid; gap: 8px;">
                    <button type="button" class="btn btn-success" onclick="assignSelected()" id="assignSelectedBtn" disabled style="width: 100%;">
                        ➕ Assign Selected Terminals
                    </button>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <button type="button" class="btn" onclick="assignAll()" id="assignAllBtn" disabled>
                            📋 Assign All
                        </button>
                        <button type="button" class="btn" onclick="clearAssignments()" id="clearAssignmentsBtn" disabled>
                            🗑️ Clear Assignments
                        </button>
                    </div>
                </div>
            </div>

            <!-- Technician Workload Display -->
            <div class="content-card">
                <h4 style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px; color: #333;">
                    📊 Technician Workload
                </h4>

                <div id="technicianWorkload">
                    <div style="text-align: center; color: #666; padding: 20px;">
                        <div style="font-size: 32px; margin-block-end: 10px;">👥</div>
                        <h6>Step 3: Select Technicians</h6>
                        <p>Choose technicians to see workload distribution</p>
                    </div>
                </div>
            </div>

            <!-- Unassigned Terminals -->
            <div class="content-card">
                <h4 style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px; color: #333;">
                    ⏳ Unassigned Terminals
                    <span style="background: #ff9800; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;" id="unassignedCount">0</span>
                </h4>

                <div id="unassignedList" style="max-height: 200px; overflow-y: auto;">
                    <div style="text-align: center; color: #666; padding: 20px;">
                        <div style="font-size: 32px; margin-block-end: 10px;">⏳</div>
                        <p>Load hierarchy to see unassigned terminals</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review & Deploy Section - Hidden Initially -->
    <div class="content-card review-section" style="margin-top: 20px; display: none;" id="reviewSection">
        <h4 style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px; color: #333;">
            📋 Review & Deploy
        </h4>

        <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 20px;">
            <!-- Assignment Summary Table -->
            <div>
                <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <table class="assignment-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Technician</th>
                                <th style="padding: 12px; text-align: center; font-weight: 600;">Terminals</th>
                                <th style="padding: 12px; text-align: center; font-weight: 600;">Regions</th>
                                <th style="padding: 12px; text-align: center; font-weight: 600;">Priority</th>
                                <th style="padding: 12px; text-align: center; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="assignmentSummaryTable">
                            <tr>
                                <td colspan="5" style="padding: 40px; text-align: center; color: #666;">
                                    No assignments yet
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Deploy Actions -->
            <div>
                <div style="display: grid; gap: 12px;">
                    <button type="button" class="btn btn-primary" onclick="generateWorkOrders()" id="generateWorkOrdersBtn" disabled style="width: 100%;">
                        📋 Generate Work Orders
                    </button>
                    <button type="button" class="btn btn-success" onclick="deployAll()" id="deployAllBtn" disabled style="width: 100%;">
                        🚀 Deploy All Assignments
                    </button>
                    <button type="button" class="btn" onclick="saveAsDraft()" id="saveDraftBtn" disabled style="width: 100%;">
                        💾 Save as Draft
                    </button>
                </div>

                <!-- Deployment Stats -->
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <h6 style="margin: 0 0 10px 0; color: #333;">Deployment Summary</h6>
                    <div style="display: grid; gap: 5px; font-size: 14px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Total Technicians:</span>
                            <strong id="summaryTechnicians">0</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Total Terminals:</span>
                            <strong id="summaryTerminals">0</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Estimated Time:</span>
                            <strong id="summaryTime">0 hours</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create New Project Modal -->
<div id="createProjectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 500px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>➕</span>
                <span>Create New Project</span>
            </h3>
            <button onclick="closeProjectModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 20px;">
            <form id="createProjectForm">
                <div style="display: grid; gap: 15px;">
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Project Name <span style="color: #f44336;">*</span>
                        </label>
                        <input type="text" id="newProjectName" required
                               placeholder="e.g., Q1 2025 Terminal Maintenance"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Project Type <span style="color: #f44336;">*</span>
                            </label>
                            <select id="newProjectType" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select type...</option>
                                <option value="discovery">Discovery</option>
                                <option value="servicing">Servicing</option>
                                <option value="support">Support</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="installation">Installation</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Expected Duration
                            </label>
                            <select id="newProjectDuration" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="1">1 Month</option>
                                <option value="3" selected>3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="12">1 Year</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Project Description
                        </label>
                        <textarea id="newProjectDescription" rows="3"
                                  placeholder="Describe the project objectives and scope..."
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        ➕ Create Project
                    </button>
                    <button type="button" onclick="closeProjectModal()" class="btn">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assignment Success Modal -->
<div id="assignmentSuccessModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 600px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>🎉</span>
                <span>Deployment Successful</span>
            </h3>
            <button onclick="closeSuccessModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 20px;">
            <div id="successModalContent">
                <!-- Content will be populated here -->
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-primary" onclick="viewAssignments()" style="flex: 1;">
                    👁️ View All Assignments
                </button>
                <button type="button" class="btn" onclick="createNewDeployment()">
                    🆕 New Deployment
                </button>
                <button type="button" class="btn" onclick="closeSuccessModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.metric-card {
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

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
    border: none;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

.btn-success {
    background: #4caf50;
    color: white;
    border-color: #4caf50;
}

.btn-success:hover {
    background: #388e3c;
    border-color: #388e3c;
    color: white;
}

.btn-small {
    padding: 6px 12px;
    font-size: 12px;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Progressive Disclosure */
.progress-section, .main-content-section, .assignment-section, .review-section {
    transition: all 0.3s ease;
}

.step-item {
    text-align: center;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.step-item.active {
    opacity: 1;
    color: #2196f3;
}

.step-item.completed {
    opacity: 1;
    color: #4caf50;
}

.step-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #ddd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
    font-weight: bold;
    font-size: 12px;
}

.step-item.active .step-circle {
    background: #2196f3;
}

.step-item.completed .step-circle {
    background: #4caf50;
}

/* Terminal Table Styles */
#terminalTable {
    font-size: 14px;
    border-collapse: collapse;
}

#terminalTable th {
    background: #f8f9fa;
    position: sticky;
    top: 0;
    z-index: 10;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 8px;
}

#terminalTable td {
    padding: 8px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.terminal-row {
    transition: background-color 0.2s ease;
    cursor: pointer;
}

.terminal-row:hover {
    background-color: #f8f9fa;
}

.terminal-row.selected {
    background-color: #e3f2fd;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #e8f5e8;
    color: #2e7d32;
}

.status-offline {
    background: #ffebee;
    color: #c62828;
}

.status-maintenance {
    background: #fff3e0;
    color: #f57c00;
}

.status-faulty {
    background: #fce4ec;
    color: #ad1457;
}

.status-unknown {
    background: #f5f5f5;
    color: #666;
}

.assignment-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.assignment-assigned {
    background: #e8f5e8;
    color: #2e7d32;
}

.assignment-unassigned {
    background: #fff3e0;
    color: #f57c00;
}

/* Filter Styles */
.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

/* Pagination Styles */
#tablePagination {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
}

#paginationButtons {
    display: flex;
    gap: 5px;
}

#paginationButtons .btn {
    padding: 6px 12px;
    font-size: 12px;
    min-width: auto;
}

/* Table Container */
.table-container {
    max-height: 600px;
    overflow: auto;
    background: white;
    border-radius: 0 0 8px 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    #terminalTable th,
    #terminalTable td {
        padding: 6px 4px;
        font-size: 12px;
    }

    .status-badge {
        padding: 2px 6px;
        font-size: 10px;
    }
}

.workload-item {
    background: white;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 8px;
    border-left: 4px solid #007bff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.technician-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
}

.workload-count {
    color: #666;
    font-size: 14px;
}

.unassigned-item {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 4px;
    padding: 8px;
    margin-bottom: 4px;
    font-size: 13px;
}

.assignment-table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
}

.assignment-table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.assignment-table tbody tr:hover {
    background: #f8f9fa;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.alert {
    border-radius: 6px;
    padding: 15px;
    margin-block-end: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Custom Dropdown Styles */
.custom-dropdown {
    position: relative;
    width: 100%;
}

.dropdown-selected {
    background: white;
    border: 2px solid #ddd;
    border-radius: 6px;
    padding: 10px 12px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: border-color 0.2s;
}

.dropdown-selected:hover {
    border-color: #2196f3;
}

.dropdown-selected.active {
    border-color: #2196f3;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

.dropdown-arrow {
    transition: transform 0.2s;
    color: #666;
    font-size: 12px;
}

.dropdown-selected.active .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #2196f3;
    border-top: none;
    border-radius: 0 0 6px 6px;
    max-height: 250px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.dropdown-options.show {
    display: block;
    animation: dropdownFadeIn 0.2s ease-out;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.dropdown-search {
    padding: 8px;
    border-bottom: 1px solid #eee;
    background: #f9f9f9;
}

.dropdown-search input {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.dropdown-search input:focus {
    outline: none;
    border-color: #2196f3;
}

.dropdown-option {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
    transition: background-color 0.2s;
    border-bottom: 1px solid #f5f5f5;
}

.dropdown-option:hover {
    background-color: #f8f9fa;
}

.dropdown-option.disabled {
    color: #999;
    cursor: not-allowed;
}

.dropdown-option input[type="checkbox"] {
    margin-right: 8px;
    cursor: pointer;
}

.dropdown-option span {
    flex: 1;
    font-size: 14px;
}

.dropdown-options::-webkit-scrollbar {
    width: 6px;
}

.dropdown-options::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.dropdown-options::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.dropdown-options::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

@media (max-width: 1200px) {
    div[style*="grid-template-columns: 3fr 2fr"] {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }

    div[style*="grid-template-columns: 2fr 1fr 2fr 1fr"] {
        grid-template-columns: 1fr 1fr !important;
        gap: 15px;
    }
}
</style>

<script>
// CSRF token setup for AJAX requests
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// =====================
// GLOBAL STATE (Updated for Table)
// =====================
let deploymentState = {
    selectedClients: new Set(),
    selectedProjects: new Set(),
    selectedTechnicians: new Set(),
    selectedTerminals: new Set(),
    hierarchyData: [],
    allTerminals: new Map(),
    filteredTerminals: [],
    assignedTerminalIds: new Set(), // Track which terminals are assigned
    assignments: {},
    deploymentDate: null,
    isLoading: false,
    filters: {
        province: '',
        city: '',
        region: '',
        status: '',
        assignment: '', // New assignment filter
        search: ''
    },
    pagination: {
        currentPage: 1,
        itemsPerPage: 50,
        totalPages: 1
    }
};

// =====================
// INITIALIZATION
// =====================
document.addEventListener('DOMContentLoaded', function() {
    console.log('Terminal Deployment initialized');
    console.log('Available clients:', @json($clients));
    console.log('Available technicians:', @json($technicians));

    setupEventListeners();
    updateLoadButton();
    updateProgressiveVisibility();
});

function setupEventListeners() {
    // Deployment date
    document.getElementById('deploymentDate').addEventListener('change', function() {
        deploymentState.deploymentDate = this.value;
    });

    // Assignment mode
    document.querySelectorAll('input[name="assignmentMode"]').forEach(radio => {
        radio.addEventListener('change', updateAssignmentMode);
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.custom-dropdown')) {
            closeAllDropdowns();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeProjectModal();
            closeSuccessModal();
        }
    });
}

// =====================
// PROGRESSIVE DISCLOSURE FUNCTIONS
// =====================
function updateProgressiveVisibility() {
    const hasClients = deploymentState.selectedClients.size > 0;
    const hasProjects = deploymentState.selectedProjects.size > 0;
    const hasHierarchy = deploymentState.hierarchyData.length > 0;
    const hasTechnicians = deploymentState.selectedTechnicians.size > 0;
    const hasAssignments = Object.keys(deploymentState.assignments).length > 0;
    const hasSelections = deploymentState.selectedTerminals.size > 0;

    // Show/hide sections progressively
    toggleSection('progressStats', hasHierarchy);
    toggleSection('mainContentArea', hasClients && hasProjects);
    toggleSection('reviewSection', hasAssignments);

    // Update button states
    updateButtonStates(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments, hasSelections);

    // Update step indicators
    updateStepIndicators(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments);

    // Show helpful hints
    updateHelpfulHints(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments);
}

function toggleSection(sectionId, shouldShow) {
    const section = document.getElementById(sectionId);
    if (section) {
        if (shouldShow && section.style.display === 'none') {
            section.style.display = sectionId === 'progressStats' ? 'grid' : 'block';

            // Add smooth animation
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            setTimeout(() => {
                section.style.transition = 'all 0.3s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 10);
        } else if (!shouldShow) {
            section.style.display = 'none';
        }
    }
}

function updateButtonStates(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments, hasSelections) {
    // Load Hierarchy Button
    const loadBtn = document.getElementById('loadHierarchyBtn');
    loadBtn.disabled = !(hasClients && hasProjects);
    loadBtn.textContent = hasClients && hasProjects ? '🗺️ Load Client Terminals' : '🔒 Select Clients & Projects First';

    // Assignment Buttons
    document.getElementById('assignSelectedBtn').disabled = !(hasSelections && hasTechnicians);
    document.getElementById('assignAllBtn').disabled = !(hasHierarchy && hasTechnicians);
    document.getElementById('clearAssignmentsBtn').disabled = !hasAssignments;

    // Hierarchy Control Buttons
    ['expandAllBtn', 'collapseAllBtn', 'selectAllBtn', 'clearAllBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.disabled = !hasHierarchy;
    });

    // Review Section Buttons
    ['generateWorkOrdersBtn', 'deployAllBtn', 'saveDraftBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.disabled = !hasAssignments;
    });
}

function updateStepIndicators(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments) {
    // Step 1: Clients & Projects
    const step1 = document.getElementById('step1');
    if (hasClients && hasProjects) {
        step1.classList.remove('active');
        step1.classList.add('completed');
    } else if (hasClients || hasProjects) {
        step1.classList.add('active');
        step1.classList.remove('completed');
    }

    // Step 2: Hierarchy
    const step2 = document.getElementById('step2');
    if (hasHierarchy) {
        step2.classList.remove('active');
        step2.classList.add('completed');
    } else if (hasClients && hasProjects) {
        step2.classList.add('active');
        step2.classList.remove('completed');
    }

    // Step 3: Technicians
    const step3 = document.getElementById('step3');
    if (hasTechnicians && hasHierarchy) {
        step3.classList.remove('active');
        step3.classList.add('completed');
    } else if (hasHierarchy) {
        step3.classList.add('active');
        step3.classList.remove('completed');
    }

    // Step 4: Deploy
    const step4 = document.getElementById('step4');
    if (hasAssignments) {
        step4.classList.add('active');
    }
}

function updateHelpfulHints(hasClients, hasProjects, hasHierarchy, hasTechnicians, hasAssignments) {
    // Update the hierarchy tree placeholder
    const treeContainer = document.getElementById('hierarchyTree');

    if (!hasClients || !hasProjects) {
        treeContainer.innerHTML = `
            <div style="text-align: center; padding: 60px 20px; color: #666;">
                <div style="font-size: 64px; margin-block-end: 20px;">👈</div>
                <h5>Step 1: Configure Deployment Setup</h5>
                <p>Select clients and projects to continue</p>
                <div style="margin-top: 20px; font-size: 14px; color: #999;">
                    ${!hasClients ? '• Choose one or more clients' : '✅ Clients selected'}<br>
                    ${!hasProjects ? '• Select associated projects' : '✅ Projects selected'}
                </div>
            </div>
        `;
    }

    // Update technician workload placeholder
    if (!hasTechnicians && hasHierarchy) {
        document.getElementById('technicianWorkload').innerHTML = `
            <div style="text-align: center; color: #666; padding: 20px;">
                <div style="font-size: 32px; margin-block-end: 10px;">👥</div>
                <h6>Step 3: Select Technicians</h6>
                <p>Choose technicians to see workload distribution</p>
            </div>
        `;
    }
}

// =====================
// CUSTOM DROPDOWN FUNCTIONS (FIXED)
// =====================
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const selected = dropdown.querySelector('.dropdown-selected');
    const options = dropdown.querySelector('.dropdown-options');

    // Close other dropdowns first
    closeAllDropdowns();

    // Toggle current dropdown
    if (options.classList.contains('show')) {
        closeDropdown(dropdownId);
    } else {
        openDropdown(dropdownId);
    }
}

function openDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const selected = dropdown.querySelector('.dropdown-selected');
    const options = dropdown.querySelector('.dropdown-options');

    selected.classList.add('active');
    options.classList.add('show');

    // Focus search input if it exists
    const searchInput = options.querySelector('.dropdown-search input');
    if (searchInput) {
        setTimeout(() => searchInput.focus(), 100);
    }
}

function closeDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const selected = dropdown.querySelector('.dropdown-selected');
    const options = dropdown.querySelector('.dropdown-options');

    selected.classList.remove('active');
    options.classList.remove('show');
}

function closeAllDropdowns() {
    document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
        const selected = dropdown.querySelector('.dropdown-selected');
        const options = dropdown.querySelector('.dropdown-options');
        if (selected && options) {
            selected.classList.remove('active');
            options.classList.remove('show');
        }
    });
}

function filterOptions(optionsId, searchValue) {
    const options = document.getElementById(optionsId);
    const dropdownOptions = options.querySelectorAll('.dropdown-option');

    searchValue = searchValue.toLowerCase();

    dropdownOptions.forEach(option => {
        if (option.classList.contains('disabled')) return;

        const text = option.textContent.toLowerCase();
        if (text.includes(searchValue)) {
            option.style.display = 'flex';
        } else {
            option.style.display = 'none';
        }
    });
}

function updateClientSelection() {
    deploymentState.selectedClients.clear();

    const checkboxes = document.querySelectorAll('#clientOptions input[type="checkbox"]:checked');
    const selectedText = document.getElementById('clientSelectedText');

    if (checkboxes.length === 0) {
        selectedText.textContent = 'Choose clients...';
    } else if (checkboxes.length === 1) {
        selectedText.textContent = checkboxes[0].dataset.name;
    } else {
        selectedText.textContent = `${checkboxes.length} clients selected`;
    }

    checkboxes.forEach(checkbox => {
        deploymentState.selectedClients.add(checkbox.value);
    });

    console.log('Selected clients:', Array.from(deploymentState.selectedClients));
    updateTotalTerminalCount();
    loadProjectsForClients();
    updateLoadButton();
    updateProgressiveVisibility();

    // Auto-close dropdown after selection
    setTimeout(() => closeDropdown('clientDropdown'), 300);
}

function updateTechnicianSelection() {
    deploymentState.selectedTechnicians.clear();

    const checkboxes = document.querySelectorAll('#technicianOptions input[type="checkbox"]:checked');
    const selectedText = document.getElementById('technicianSelectedText');

    if (checkboxes.length === 0) {
        selectedText.textContent = 'Choose technicians...';
    } else if (checkboxes.length === 1) {
        selectedText.textContent = checkboxes[0].dataset.name;
    } else {
        selectedText.textContent = `${checkboxes.length} technicians selected`;
    }

    checkboxes.forEach(checkbox => {
        deploymentState.selectedTechnicians.add(checkbox.value);
    });

    updateTechnicianWorkload();
    updateAssignmentButtons();
    updateProgressiveVisibility();

    if (deploymentState.selectedTechnicians.size > 0) {
        showAlert('✅ Step 3 Complete! Select terminals from the hierarchy to assign them.');
    }

    // Auto-close dropdown after selection
    setTimeout(() => closeDropdown('technicianDropdown'), 300);
}

// =====================
// CLIENT & PROJECT MANAGEMENT
// =====================
function updateTotalTerminalCount() {
    const checkboxes = document.querySelectorAll('#clientOptions input[type="checkbox"]:checked');
    let totalTerminals = 0;

    checkboxes.forEach(checkbox => {
        totalTerminals += parseInt(checkbox.dataset.terminals) || 0;
    });

    document.getElementById('totalTerminalCount').textContent = totalTerminals;
}

function loadProjectsForClients() {
    const projectDropdown = document.getElementById('projectDropdown');
    const projectSelectedText = document.getElementById('projectSelectedText');
    const projectOptionsList = document.getElementById('projectOptionsList');

    if (deploymentState.selectedClients.size === 0) {
        projectSelectedText.textContent = 'Select clients first...';
        projectOptionsList.innerHTML = '<div class="dropdown-option disabled">Select clients first...</div>';
        return;
    }

    // Show loading state
    projectSelectedText.textContent = 'Loading projects...';
    projectOptionsList.innerHTML = '<div class="dropdown-option disabled">Loading projects...</div>';

    const clientIds = Array.from(deploymentState.selectedClients);

    fetch('{{ route("deployment.projects") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({
            client_ids: clientIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            projectSelectedText.textContent = 'Choose projects...';
            projectOptionsList.innerHTML = '';

            if (!data.projects || data.projects.length === 0) {
                projectOptionsList.innerHTML = '<div class="dropdown-option disabled">No projects found for selected clients</div>';
            } else {
                data.projects.forEach(project => {
                    const projectOption = document.createElement('label');
                    projectOption.className = 'dropdown-option';
                    projectOption.innerHTML = `
                        <input type="checkbox" value="${project.id}" data-name="${project.display_name}" data-type="${project.project_type}" onchange="updateProjectSelection()">
                        <span>${project.display_name} (${project.project_type})</span>
                    `;
                    projectOptionsList.appendChild(projectOption);
                });
            }
        } else {
            projectSelectedText.textContent = 'Error loading projects';
            projectOptionsList.innerHTML = '<div class="dropdown-option disabled">Error loading projects</div>';
            showErrorModal(
                'Failed to Load Projects',
                'Unable to load projects for the selected clients. Please try again.',
                data.message || 'Unknown error'
            );
        }
    })
    .catch(error => {
        console.error('Error loading projects:', error);
        projectSelectedText.textContent = 'Error loading projects';
        projectOptionsList.innerHTML = '<div class="dropdown-option disabled">Error loading projects</div>';
        showErrorModal(
            'Network Error',
            'Failed to connect to the server while loading projects. Please check your connection and try again.',
            error.toString()
        );
    });
}

function updateProjectSelection() {
    deploymentState.selectedProjects.clear();

    const checkboxes = document.querySelectorAll('#projectOptionsList input[type="checkbox"]:checked');
    const selectedText = document.getElementById('projectSelectedText');

    if (checkboxes.length === 0) {
        selectedText.textContent = 'Choose projects...';
    } else if (checkboxes.length === 1) {
        selectedText.textContent = checkboxes[0].dataset.name;
    } else {
        selectedText.textContent = `${checkboxes.length} projects selected`;
    }

    checkboxes.forEach(checkbox => {
        deploymentState.selectedProjects.add(checkbox.value);
    });

    console.log('Selected projects:', Array.from(deploymentState.selectedProjects));
    updateLoadButton();
    updateProgressiveVisibility();

    // Auto-close dropdown after selection
    setTimeout(() => closeDropdown('projectDropdown'), 300);
}

function updateLoadButton() {
    const hasClients = deploymentState.selectedClients.size > 0;
    const hasProjects = deploymentState.selectedProjects.size > 0;

    document.getElementById('loadHierarchyBtn').disabled = !(hasClients && hasProjects);
}

// =====================
// HIERARCHY MANAGEMENT
// =====================
function loadHierarchy() {
    if (deploymentState.selectedClients.size === 0) {
        showAlert('Please select at least one client', 'danger');
        return;
    }

    if (deploymentState.selectedProjects.size === 0) {
        showAlert('Please select at least one project', 'danger');
        return;
    }

    const tableBody = document.getElementById('terminalTableBody');
    setLoading(tableBody, true);

    tableBody.innerHTML = `
        <tr>
            <td colspan="8" style="padding: 40px 20px; text-align: center; color: #666;">
                <div style="font-size: 32px; margin-bottom: 15px;">⏳</div>
                <p>Loading terminals...</p>
            </td>
        </tr>
    `;

    const requestData = {
        client_ids: Array.from(deploymentState.selectedClients),
        project_ids: Array.from(deploymentState.selectedProjects)
    };

    fetch('{{ route("deployment.terminals") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Hierarchy loaded successfully:', data);
            deploymentState.hierarchyData = data.hierarchy;
            buildTerminalMap(data.hierarchy);

            setLoading(tableBody, false);
            renderHierarchy();
            updateProgressStats();

            updateProgressiveVisibility();
            showAlert('✅ Step 2 Complete! Now select technicians to start assigning terminals.');
        } else {
            setLoading(tableBody, false);
            showErrorModal(
                'Failed to Load Terminals',
                'Unable to load terminal hierarchy. Please check your selections and try again.',
                data.message || 'Unknown error'
            );
        }
    })
    .catch(error => {
        console.error('Error loading terminals:', error);
        setLoading(tableBody, false);
        showErrorModal(
            'Network Error',
            'Failed to connect to the server while loading terminals. Please check your connection and try again.',
            error.toString()
        );
    });
}

function buildTerminalMap(hierarchy) {
    console.log('Building terminal map from hierarchy:', hierarchy);
    deploymentState.allTerminals.clear();

    const terminals = [];

    const processNode = (node) => {
        if (node.type === 'terminal') {
            const terminalId = node.id.replace('terminal-', '');
            deploymentState.allTerminals.set(terminalId, node);
            terminals.push(node);
            return;
        }

        if (node.children) {
            node.children.forEach(processNode);
        }

        if (node.terminals) {
            node.terminals.forEach(processNode);
        }
    };

    hierarchy.forEach(processNode);

    // Get assigned terminal IDs
    refreshAssignedTerminals().then(() => {
        // Populate filter options
        populateFilterOptions(terminals);

        // Initialize filtered terminals
        deploymentState.filteredTerminals = terminals;

        console.log('Final terminal map size:', deploymentState.allTerminals.size);
    });
}

function refreshAssignedTerminals() {
    return fetch('{{ route("deployment.assigned-terminals") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            deploymentState.assignedTerminalIds = new Set(data.assigned_terminal_ids || []);
        }
    })
    .catch(error => {
        console.error('Error loading assigned terminals:', error);
        deploymentState.assignedTerminalIds = new Set();
    });
}

function populateFilterOptions(terminals) {
    const provinces = new Set();
    const cities = new Set();
    const regions = new Set();

    terminals.forEach(terminal => {
        if (terminal.province) provinces.add(terminal.province);
        if (terminal.city) cities.add(terminal.city);
        if (terminal.area) regions.add(terminal.area);
    });

    // Populate Province filter
    const provinceFilter = document.getElementById('provinceFilter');
    provinceFilter.innerHTML = '<option value="">All Provinces</option>';
    Array.from(provinces).sort().forEach(province => {
        provinceFilter.innerHTML += `<option value="${province}">${province}</option>`;
    });

    // Populate City filter
    const cityFilter = document.getElementById('cityFilter');
    cityFilter.innerHTML = '<option value="">All Cities</option>';
    Array.from(cities).sort().forEach(city => {
        cityFilter.innerHTML += `<option value="${city}">${city}</option>`;
    });

    // Populate Region filter
    const regionFilter = document.getElementById('regionFilter');
    regionFilter.innerHTML = '<option value="">All Regions</option>';
    Array.from(regions).sort().forEach(region => {
        regionFilter.innerHTML += `<option value="${region}">${region}</option>`;
    });
}

function applyFilters() {
    // Get filter values
    deploymentState.filters.province = document.getElementById('provinceFilter').value;
    deploymentState.filters.city = document.getElementById('cityFilter').value;
    deploymentState.filters.region = document.getElementById('regionFilter').value;
    deploymentState.filters.status = document.getElementById('statusFilter').value;
    deploymentState.filters.assignment = document.getElementById('assignmentFilter').value;
    deploymentState.filters.search = document.getElementById('searchFilter').value.toLowerCase();

    // Update dependent filters
    updateDependentFilters();

    // Filter terminals
    const allTerminals = Array.from(deploymentState.allTerminals.values());
    deploymentState.filteredTerminals = allTerminals.filter(terminal => {
        const terminalId = terminal.id.replace('terminal-', '');

        // Province filter
        if (deploymentState.filters.province && terminal.province !== deploymentState.filters.province) {
            return false;
        }

        // City filter
        if (deploymentState.filters.city && terminal.city !== deploymentState.filters.city) {
            return false;
        }

        // Region filter
        if (deploymentState.filters.region && terminal.area !== deploymentState.filters.region) {
            return false;
        }

        // Status filter
        if (deploymentState.filters.status && terminal.status !== deploymentState.filters.status) {
            return false;
        }

        // Assignment filter
        if (deploymentState.filters.assignment) {
            const isAssigned = deploymentState.assignedTerminalIds.has(terminalId);
            if (deploymentState.filters.assignment === 'assigned' && !isAssigned) {
                return false;
            }
            if (deploymentState.filters.assignment === 'unassigned' && isAssigned) {
                return false;
            }
        }

        // Search filter
        if (deploymentState.filters.search) {
            const searchText = (
                terminal.terminal_id + ' ' +
                terminal.merchant_name + ' ' +
                (terminal.province || '') + ' ' +
                (terminal.city || '') + ' ' +
                (terminal.area || '')
            ).toLowerCase();

            if (!searchText.includes(deploymentState.filters.search)) {
                return false;
            }
        }

        return true;
    });

    // Reset pagination
    deploymentState.pagination.currentPage = 1;
    deploymentState.pagination.totalPages = Math.ceil(deploymentState.filteredTerminals.length / deploymentState.pagination.itemsPerPage);

    // Render table
    renderTerminalTable();
    updatePagination();
}

function updateDependentFilters() {
    const selectedProvince = deploymentState.filters.province;
    const selectedCity = deploymentState.filters.city;

    // Update City filter based on Province selection
    if (selectedProvince) {
        const cityFilter = document.getElementById('cityFilter');
        const citiesInProvince = new Set();

        Array.from(deploymentState.allTerminals.values()).forEach(terminal => {
            if (terminal.province === selectedProvince && terminal.city) {
                citiesInProvince.add(terminal.city);
            }
        });

        const currentCity = cityFilter.value;
        cityFilter.innerHTML = '<option value="">All Cities</option>';
        Array.from(citiesInProvince).sort().forEach(city => {
            const selected = city === currentCity ? 'selected' : '';
            cityFilter.innerHTML += `<option value="${city}" ${selected}>${city}</option>`;
        });

        // Reset city if it's not in the new list
        if (currentCity && !citiesInProvince.has(currentCity)) {
            deploymentState.filters.city = '';
        }
    }

    // Update Region filter based on Province and City selection
    const regionFilter = document.getElementById('regionFilter');
    const regionsFiltered = new Set();

    Array.from(deploymentState.allTerminals.values()).forEach(terminal => {
        const matchesProvince = !selectedProvince || terminal.province === selectedProvince;
        const matchesCity = !deploymentState.filters.city || terminal.city === deploymentState.filters.city;

        if (matchesProvince && matchesCity && terminal.area) {
            regionsFiltered.add(terminal.area);
        }
    });

    const currentRegion = regionFilter.value;
    regionFilter.innerHTML = '<option value="">All Regions</option>';
    Array.from(regionsFiltered).sort().forEach(region => {
        const selected = region === currentRegion ? 'selected' : '';
        regionFilter.innerHTML += `<option value="${region}" ${selected}>${region}</option>`;
    });

    // Reset region if it's not in the new list
    if (currentRegion && !regionsFiltered.has(currentRegion)) {
        deploymentState.filters.region = '';
    }
}

function renderTerminalTable() {
    const tableBody = document.getElementById('terminalTableBody');
    const startIndex = (deploymentState.pagination.currentPage - 1) * deploymentState.pagination.itemsPerPage;
    const endIndex = Math.min(startIndex + deploymentState.pagination.itemsPerPage, deploymentState.filteredTerminals.length);
    const pageTerminals = deploymentState.filteredTerminals.slice(startIndex, endIndex);

    if (pageTerminals.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" style="padding: 40px 20px; text-align: center; color: #666;">
                    <div style="font-size: 32px; margin-bottom: 10px;">🔍</div>
                    <h5>No terminals found</h5>
                    <p>Try adjusting your filters or search criteria</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    pageTerminals.forEach(terminal => {
        const terminalId = terminal.id.replace('terminal-', '');
        const isSelected = deploymentState.selectedTerminals.has(terminalId);
        const isAssigned = deploymentState.assignedTerminalIds.has(terminalId);
        const statusClass = getStatusClass(terminal.status);
        const assignmentClass = isAssigned ? 'assignment-assigned' : 'assignment-unassigned';

        html += `
            <tr class="terminal-row ${isSelected ? 'selected' : ''}" data-terminal-id="${terminalId}">
                <td style="padding: 8px; text-align: center; border-bottom: 1px solid #eee;">
                    <input type="checkbox" ${isSelected ? 'checked' : ''}
                           onchange="toggleTerminalSelection('${terminalId}')"
                           style="cursor: pointer;">
                </td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; font-family: monospace; font-weight: 600;">
                    ${terminal.terminal_id}
                </td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: 500;">
                    ${terminal.merchant_name}
                </td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">
                    ${terminal.province || 'Unknown'}
                </td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">
                    ${terminal.city || 'Unknown'}
                </td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">
                    ${terminal.area || 'Unknown'}
                </td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;">
                    <span class="assignment-badge ${assignmentClass}">
                        ${isAssigned ? '✅ Assigned' : '⏳ Unassigned'}
                    </span>
                </td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;">
                    <span class="status-badge ${statusClass}">${terminal.status || 'unknown'}</span>
                </td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;">
                    <button class="btn btn-small" onclick="assignSingleTerminal('${terminalId}')"
                            style="background: #2196f3; color: white; padding: 4px 8px; font-size: 11px;"
                            ${isAssigned ? 'disabled title="Already assigned"' : ''}>
                        ${isAssigned ? 'Assigned' : 'Assign'}
                    </button>
                </td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;

    // Update counter
    document.getElementById('terminalCount').textContent = deploymentState.filteredTerminals.length;

    // Enable/disable controls
    const hasTerminals = deploymentState.filteredTerminals.length > 0;
    ['selectAllBtn', 'clearAllBtn', 'exportBtn'].forEach(id => {
        document.getElementById(id).disabled = !hasTerminals;
    });
}

function getStatusClass(status) {
    const statusClasses = {
        'active': 'status-active',
        'offline': 'status-offline',
        'maintenance': 'status-maintenance',
        'faulty': 'status-faulty'
    };
    return statusClasses[status] || 'status-unknown';
}

function renderHierarchy() {
    console.log('renderHierarchy called with data:', deploymentState.hierarchyData);

    if (!deploymentState.hierarchyData || deploymentState.hierarchyData.length === 0) {
        console.log('No hierarchy data, showing empty table');
        showEmptyTable();
        return;
    }

    // Apply initial filters (no filters)
    applyFilters();
}

function showEmptyTable() {
    const tableBody = document.getElementById('terminalTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="9" style="padding: 60px 20px; text-align: center; color: #666;">
                <div style="font-size: 48px; margin-bottom: 15px;">👈</div>
                <h5>Step 1: Configure Deployment Setup</h5>
                <p>Select clients and projects to load terminals</p>
                <div style="margin-top: 20px; font-size: 14px; color: #999;">
                    • Choose one or more clients<br>
                    • Select associated projects
                </div>
            </td>
        </tr>
    `;
}

function renderNode(node, level) {
    const isTerminal = node.type === 'terminal';
    const hasChildren = node.children && node.children.length > 0;
    const hasTerminals = node.terminals && node.terminals.length > 0;

    if (isTerminal) {
        return renderTerminalNode(node);
    } else {
        return renderContainerNode(node, hasChildren, hasTerminals, level);
    }
}

function renderContainerNode(node, hasChildren, hasTerminals, level) {
    const terminalIds = getTerminalIdsInNode(node.id);
    const allSelected = terminalIds.length > 0 && terminalIds.every(id => deploymentState.selectedTerminals.has(id));
    const someSelected = terminalIds.some(id => deploymentState.selectedTerminals.has(id));

    const expandIcon = (hasChildren || hasTerminals) ?
        (node.collapsed ? '▶️' : '▼️') : '📁';

    let html = `
        <div class="tree-node" data-id="${node.id}" data-type="${node.type}">
            <div class="node-header ${allSelected ? 'selected' : ''}" onclick="toggleNodeSelection('${node.id}')">
                <input type="checkbox"
                       ${allSelected ? 'checked' : ''}
                       ${someSelected && !allSelected ? 'data-indeterminate="true"' : ''}
                       onchange="toggleNodeSelection('${node.id}')" onclick="event.stopPropagation()">
                <div onclick="toggleNodeExpansion('${node.id}'); event.stopPropagation();" style="cursor: pointer; margin-right: 8px;">
                    ${expandIcon}
                </div>
                <div class="node-content">
                    <div>
                        <span class="node-name">${node.name}</span>
                        <span class="node-count">${node.terminal_count || 0}</span>
                    </div>
                    <div class="node-actions">
                        <button class="btn btn-small" onclick="assignAllInNode('${node.id}'); event.stopPropagation();" style="background: #4caf50; color: white; padding: 3px 6px;">
                            Assign All
                        </button>
                    </div>
                </div>
            </div>
    `;

    if (!node.collapsed && (hasChildren || hasTerminals)) {
        html += '<div class="node-children">';

        if (hasChildren) {
            node.children.forEach(child => {
                html += renderNode(child, level + 1);
            });
        }

        if (hasTerminals) {
            node.terminals.forEach(terminal => {
                html += renderNode(terminal, level + 1);
            });
        }

        html += '</div>';
    }

    html += '</div>';
    return html;
}

function renderTerminalNode(node) {
    const terminalId = node.id.replace('terminal-', '');
    const isSelected = deploymentState.selectedTerminals.has(terminalId);

    // Get project badges
    let projectBadges = '';
    if (node.projects && node.projects.length > 0) {
        node.projects.forEach(project => {
            projectBadges += `<span class="project-badge project-${project.type}">${project.name}</span>`;
        });
    }

    return `
        <div class="tree-node" data-id="${node.id}" data-type="terminal">
            <div class="terminal-node ${isSelected ? 'selected' : ''}" onclick="toggleTerminalSelection('${terminalId}')">
                <input type="checkbox" ${isSelected ? 'checked' : ''}
                       onchange="toggleTerminalSelection('${terminalId}')" onclick="event.stopPropagation()">
                <div class="terminal-info">
                    <div class="terminal-name" title="${node.merchant_name}">🏢 ${node.merchant_name}</div>
                    <div class="terminal-id">${node.terminal_id}</div>
                    ${projectBadges}
                    <div class="terminal-actions">
                        <button class="btn btn-small" onclick="assignSingleTerminal('${terminalId}'); event.stopPropagation();" style="background: #2196f3; color: white; padding: 2px 6px;">
                            Assign
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Remove old hierarchical functions - no longer needed
// These functions were for the tree view: renderNode, renderContainerNode, renderTerminalNode,
// toggleNodeSelection, toggleNodeExpansion, getTerminalIdsInNode, findNodeById,
// expandAll, collapseAll, assignAllInNode

function assignSingleTerminal(terminalId) {
    // Quick assign single terminal
    if (deploymentState.selectedTechnicians.size === 0) {
        showAlert('Please select a technician first', 'danger');
        return;
    }

    deploymentState.selectedTerminals.clear();
    deploymentState.selectedTerminals.add(terminalId);
    assignSelected();
}

function assignAll() {
    deploymentState.allTerminals.forEach((terminal, id) => {
        deploymentState.selectedTerminals.add(id);
    });
    assignSelected();
}

// =====================
// TECHNICIAN MANAGEMENT
// =====================
function updateTechnicianWorkload() {
    const container = document.getElementById('technicianWorkload');

    if (deploymentState.selectedTechnicians.size === 0) {
        container.innerHTML = `
            <div style="text-align: center; color: #666; padding: 20px;">
                <div style="font-size: 32px; margin-block-end: 10px;">👥</div>
                <h6>Step 3: Select Technicians</h6>
                <p>Choose technicians to see workload distribution</p>
            </div>
        `;
        return;
    }

    let html = '';
    const checkboxes = document.querySelectorAll('#technicianOptions input[type="checkbox"]:checked');

    checkboxes.forEach(checkbox => {
        const workload = checkbox.dataset.workload || 0;
        const availability = checkbox.dataset.availability || 'available';
        const spec = checkbox.dataset.spec || 'General';

        const availabilityColor = {
            'available': '#4caf50',
            'busy': '#ff9800',
            'very_busy': '#f44336',
            'overloaded': '#d32f2f'
        }[availability] || '#666';

        html += `
            <div class="workload-item">
                <div class="technician-name">${checkbox.dataset.name}</div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                    <div style="font-size: 12px; color: #666;">${spec}</div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 12px;">Current: ${workload} jobs</span>
                        <span style="background: ${availabilityColor}; color: white; padding: 2px 6px; border-radius: 8px; font-size: 10px;">
                            ${availability.replace('_', ' ').toUpperCase()}
                        </span>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// =====================
// ASSIGNMENT MANAGEMENT
// =====================
function assignSelected() {
    if (deploymentState.selectedTechnicians.size === 0) {
        showAlert('Please select at least one technician', 'danger');
        return;
    }

    if (deploymentState.selectedTerminals.size === 0) {
        showAlert('Please select terminals to assign', 'danger');
        return;
    }

    const assignmentMode = document.querySelector('input[name="assignmentMode"]:checked').value;
    const selectedTerminalIds = Array.from(deploymentState.selectedTerminals);
    const selectedTechnicians = getSelectedTechnicianData();

    const assignmentData = {
        selected_terminals: selectedTerminalIds,
        scheduled_date: deploymentState.deploymentDate || document.getElementById('deploymentDate').value,
        service_type: document.getElementById('serviceType').value,
        priority: document.getElementById('assignmentPriority').value,
        assignment_type: assignmentMode,
        notes: `Deployment assignment - ${assignmentMode} mode`
    };

    if (deploymentState.selectedProjects.size > 0) {
        assignmentData.project_id = Array.from(deploymentState.selectedProjects)[0];
    }

    if (assignmentMode === 'team') {
        selectedTechnicians.forEach(tech => {
            createAssignment({...assignmentData, technician_id: tech.id}, tech);
        });
    } else {
        distributeTerminalsAmongTechnicians(selectedTechnicians, selectedTerminalIds, assignmentData);
    }

    deploymentState.selectedTerminals.clear();
    renderHierarchy();
    updateProgressStats();
}

function getSelectedTechnicianData() {
    const checkboxes = document.querySelectorAll('#technicianOptions input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(checkbox => ({
        id: checkbox.value,
        name: checkbox.dataset.name,
        specialization: checkbox.dataset.spec,
        availability: checkbox.dataset.availability,
        workload: parseInt(checkbox.dataset.workload) || 0
    }));
}

function distributeTerminalsAmongTechnicians(technicians, terminalIds, baseAssignmentData) {
    const terminalsPerTech = Math.ceil(terminalIds.length / technicians.length);

    technicians.forEach((tech, index) => {
        const startIndex = index * terminalsPerTech;
        const endIndex = Math.min(startIndex + terminalsPerTech, terminalIds.length);
        const techTerminals = terminalIds.slice(startIndex, endIndex);

        if (techTerminals.length > 0) {
            createAssignment({
                ...baseAssignmentData,
                technician_id: tech.id,
                selected_terminals: techTerminals
            }, tech);
        }
    });
}

function createAssignment(assignmentData, technician) {
    fetch('{{ route("deployment.assign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(assignmentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addToLocalAssignments(technician.id, technician, assignmentData.selected_terminals);
            showAlert(`Assignment created for ${technician.name}!`);
            updateAssignmentSummary();
            updateUnassignedList();
            updateProgressiveVisibility();

            // Refresh assigned terminals and re-render table
            refreshAssignedTerminals().then(() => {
                renderTerminalTable();
            });
        } else {
            showAlert('Error creating assignment: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error creating assignment:', error);
        showAlert('Error creating assignment', 'danger');
    });
}

function addToLocalAssignments(technicianId, technician, terminalIds) {
    if (!deploymentState.assignments[technicianId]) {
        deploymentState.assignments[technicianId] = {
            technician: technician,
            terminals: [],
            regions: new Set(),
            priority: document.getElementById('assignmentPriority').value
        };
    }

    terminalIds.forEach(terminalId => {
        if (!deploymentState.assignments[technicianId].terminals.includes(terminalId)) {
            deploymentState.assignments[technicianId].terminals.push(terminalId);

            // Add region info
            const terminal = deploymentState.allTerminals.get(terminalId);
            if (terminal) {
                deploymentState.assignments[technicianId].regions.add(terminal.city || 'Unknown');
            }
        }
    });

    updateProgressStats();
    showReviewSection();
}

// =====================
// UI UPDATES
// =====================
function updateProgressStats() {
    const totalTerminals = deploymentState.allTerminals.size;
    let assignedCount = 0;

    Object.values(deploymentState.assignments).forEach(assignment => {
        assignedCount += assignment.terminals.length;
    });

    const selectedCount = deploymentState.selectedTerminals.size;
    const unassignedCount = totalTerminals - assignedCount;
    const technicianCount = Object.keys(deploymentState.assignments).length;

    document.getElementById('totalTerminals').textContent = totalTerminals;
    document.getElementById('assignedTerminals').textContent = assignedCount;
    document.getElementById('unassignedTerminals').textContent = unassignedCount;
    document.getElementById('selectedTerminals').textContent = selectedCount;
    document.getElementById('technicianCount').textContent = technicianCount;
    document.getElementById('unassignedCount').textContent = unassignedCount;

    updateUnassignedList();
}

function updateAssignmentButtons() {
    const hasSelections = deploymentState.selectedTerminals.size > 0;
    const hasTechnicians = deploymentState.selectedTechnicians.size > 0;
    const hasAssignments = Object.keys(deploymentState.assignments).length > 0;

    document.getElementById('assignSelectedBtn').disabled = !(hasSelections && hasTechnicians);
    document.getElementById('assignAllBtn').disabled = !(deploymentState.allTerminals.size > 0 && hasTechnicians);
    document.getElementById('clearAssignmentsBtn').disabled = !hasAssignments;
}

function updateAssignmentSummary() {
    const tableBody = document.getElementById('assignmentSummaryTable');

    if (Object.keys(deploymentState.assignments).length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" style="padding: 40px; text-align: center; color: #666;">
                    No assignments yet
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    Object.values(deploymentState.assignments).forEach((assignment, index) => {
        const regionList = Array.from(assignment.regions).join(', ');
        const priorityClass = {
            'normal': 'bg-primary',
            'high': 'bg-warning',
            'emergency': 'bg-danger'
        }[assignment.priority] || 'bg-primary';

        html += `
            <tr>
                <td>
                    <strong>${assignment.technician.name}</strong>
                    <br><small style="color: #666;">${assignment.technician.specialization}</small>
                </td>
                <td style="text-align: center;">
                    <strong>${assignment.terminals.length}</strong>
                </td>
                <td style="text-align: center;">
                    ${regionList || 'Various'}
                </td>
                <td style="text-align: center;">
                    <span style="padding: 4px 8px; border-radius: 12px; font-size: 11px; color: white;" class="${priorityClass}">
                        ${assignment.priority.toUpperCase()}
                    </span>
                </td>
                <td style="text-align: center;">
                    <button class="btn btn-small" onclick="removeAssignment('${assignment.technician.id}')" style="background: #f44336; color: white;">
                        Remove
                    </button>
                </td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;

    // Update summary stats
    const totalTechnicians = Object.keys(deploymentState.assignments).length;
    const totalTerminals = Object.values(deploymentState.assignments).reduce((sum, a) => sum + a.terminals.length, 0);
    const estimatedTime = totalTerminals * 1.5; // 1.5 hours per terminal

    document.getElementById('summaryTechnicians').textContent = totalTechnicians;
    document.getElementById('summaryTerminals').textContent = totalTerminals;
    document.getElementById('summaryTime').textContent = `${estimatedTime} hours`;

    // Enable deploy buttons
    ['generateWorkOrdersBtn', 'deployAllBtn', 'saveDraftBtn'].forEach(id => {
        document.getElementById(id).disabled = totalTechnicians === 0;
    });
}

function updateUnassignedList() {
    const container = document.getElementById('unassignedList');
    const assignedTerminalIds = new Set();

    Object.values(deploymentState.assignments).forEach(assignment => {
        assignment.terminals.forEach(terminalId => {
            assignedTerminalIds.add(terminalId);
        });
    });

    const unassignedTerminals = [];
    deploymentState.allTerminals.forEach((terminal, id) => {
        if (!assignedTerminalIds.has(id)) {
            unassignedTerminals.push(terminal);
        }
    });

    if (unassignedTerminals.length === 0 && deploymentState.allTerminals.size > 0) {
        container.innerHTML = `
            <div style="text-align: center; color: #666; padding: 20px;">
                <div style="font-size: 32px; margin-block-end: 10px;">✅</div>
                <p>All terminals assigned!</p>
            </div>
        `;
        return;
    }

    if (deploymentState.allTerminals.size === 0) {
        container.innerHTML = `
            <div style="text-align: center; color: #666; padding: 20px;">
                <div style="font-size: 32px; margin-block-end: 10px;">⏳</div>
                <p>Load hierarchy to see unassigned terminals</p>
            </div>
        `;
        return;
    }

    let html = '';
    unassignedTerminals.slice(0, 10).forEach(terminal => { // Show max 10
        html += `
            <div class="unassigned-item" onclick="selectUnassignedTerminal('${terminal.id.replace('terminal-', '')}')">
                <strong>${terminal.merchant_name}</strong> - ${terminal.terminal_id}
                <br><small>${terminal.city || 'Unknown City'}</small>
            </div>
        `;
    });

    if (unassignedTerminals.length > 10) {
        html += `<div style="text-align: center; padding: 10px; color: #666;">
            ... and ${unassignedTerminals.length - 10} more
        </div>`;
    }

    container.innerHTML = html;
}

function showReviewSection() {
    updateProgressiveVisibility();
}

// =====================
// PROJECT MODAL
// =====================
function createNewProject() {
    if (deploymentState.selectedClients.size === 0) {
        showAlert('Please select clients first before creating a project', 'danger');
        return;
    }
    document.getElementById('createProjectModal').style.display = 'flex';
}

function closeProjectModal() {
    document.getElementById('createProjectModal').style.display = 'none';
    document.getElementById('createProjectForm').reset();
}

// Create project form submission
document.addEventListener('DOMContentLoaded', function() {
    const createProjectForm = document.getElementById('createProjectForm');
    if (createProjectForm) {
        createProjectForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (deploymentState.selectedClients.size === 0) {
                showAlert('Please select clients first', 'danger');
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '⏳ Creating Project...';
            submitBtn.disabled = true;

            const formData = {
                name: document.getElementById('newProjectName').value.trim(),
                type: document.getElementById('newProjectType').value,
                duration: document.getElementById('newProjectDuration').value,
                description: document.getElementById('newProjectDescription').value.trim(),
                client_ids: Array.from(deploymentState.selectedClients)
            };

            // Validate required fields
            if (!formData.name || !formData.type) {
                showAlert('Please fill in all required fields', 'danger');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }

            // Make API call to create project
            fetch('{{ route("deployment.projects.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeProjectModal();
                    showAlert(`Project "${formData.name}" created successfully!`);

                    // Add the new project to the dropdown
                    const projectOptionsList = document.getElementById('projectOptionsList');
                    const newProjectOption = document.createElement('label');
                    newProjectOption.className = 'dropdown-option';
                    newProjectOption.innerHTML = `
                        <input type="checkbox" value="${data.project.id}" data-name="${data.project.name}" data-type="${data.project.type}" onchange="updateProjectSelection()">
                        <span>${data.project.name} (${data.project.type})</span>
                    `;

                    // Remove "no projects" message if it exists
                    const noProjectsMsg = projectOptionsList.querySelector('.disabled');
                    if (noProjectsMsg) {
                        noProjectsMsg.remove();
                    }

                    projectOptionsList.appendChild(newProjectOption);

                    // Auto-select the new project
                    const newCheckbox = newProjectOption.querySelector('input[type="checkbox"]');
                    newCheckbox.checked = true;
                    updateProjectSelection();
                } else {
                    showAlert('Error creating project: ' + (data.message || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error creating project:', error);
                showErrorModal(
                    'Project Creation Failed',
                    'There was an error creating your project. Please check the details below and try again.',
                    error.toString()
                );
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// =====================
// UTILITY FUNCTIONS
// =====================
function updatePagination() {
    const pagination = document.getElementById('tablePagination');
    const startIndex = (deploymentState.pagination.currentPage - 1) * deploymentState.pagination.itemsPerPage + 1;
    const endIndex = Math.min(deploymentState.pagination.currentPage * deploymentState.pagination.itemsPerPage, deploymentState.filteredTerminals.length);

    if (deploymentState.filteredTerminals.length === 0) {
        pagination.style.display = 'none';
        return;
    }

    pagination.style.display = 'flex';

    // Update showing text
    document.getElementById('showingFrom').textContent = startIndex;
    document.getElementById('showingTo').textContent = endIndex;
    document.getElementById('totalTerminals').textContent = deploymentState.filteredTerminals.length;

    // Generate pagination buttons
    const buttonsContainer = document.getElementById('paginationButtons');
    let buttonsHtml = '';

    // Previous button
    if (deploymentState.pagination.currentPage > 1) {
        buttonsHtml += `<button class="btn btn-small" onclick="changePage(${deploymentState.pagination.currentPage - 1})">← Previous</button>`;
    }

    // Page numbers
    const totalPages = deploymentState.pagination.totalPages;
    const currentPage = deploymentState.pagination.currentPage;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (endPage - startPage < 4) {
        if (startPage === 1) {
            endPage = Math.min(totalPages, startPage + 4);
        } else {
            startPage = Math.max(1, endPage - 4);
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const active = i === currentPage ? 'btn-primary' : '';
        buttonsHtml += `<button class="btn btn-small ${active}" onclick="changePage(${i})">${i}</button>`;
    }

    // Next button
    if (deploymentState.pagination.currentPage < deploymentState.pagination.totalPages) {
        buttonsHtml += `<button class="btn btn-small" onclick="changePage(${deploymentState.pagination.currentPage + 1})">Next →</button>`;
    }

    buttonsContainer.innerHTML = buttonsHtml;
}

function changePage(page) {
    deploymentState.pagination.currentPage = page;
    renderTerminalTable();
    updatePagination();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const isChecked = selectAllCheckbox.checked;

    if (isChecked) {
        selectAllVisible();
    } else {
        clearSelections();
    }
}

function selectAllVisible() {
    const startIndex = (deploymentState.pagination.currentPage - 1) * deploymentState.pagination.itemsPerPage;
    const endIndex = Math.min(startIndex + deploymentState.pagination.itemsPerPage, deploymentState.filteredTerminals.length);
    const pageTerminals = deploymentState.filteredTerminals.slice(startIndex, endIndex);

    pageTerminals.forEach(terminal => {
        const terminalId = terminal.id.replace('terminal-', '');
        deploymentState.selectedTerminals.add(terminalId);
    });

    renderTerminalTable();
    updateProgressStats();
    updateAssignmentButtons();
}

function selectAll() {
    deploymentState.filteredTerminals.forEach(terminal => {
        const terminalId = terminal.id.replace('terminal-', '');
        deploymentState.selectedTerminals.add(terminalId);
    });

    renderTerminalTable();
    updateProgressStats();
    updateAssignmentButtons();
}

function clearSelections() {
    deploymentState.selectedTerminals.clear();
    document.getElementById('selectAllCheckbox').checked = false;
    renderTerminalTable();
    updateProgressStats();
    updateAssignmentButtons();
}

function exportTableData() {
    const data = deploymentState.filteredTerminals.map(terminal => ({
        'Terminal ID': terminal.terminal_id,
        'Merchant Name': terminal.merchant_name,
        'Province': terminal.province || 'Unknown',
        'City': terminal.city || 'Unknown',
        'Region': terminal.area || 'Unknown',
        'Status': terminal.status || 'unknown',
        'Address': terminal.address || '',
        'Phone': terminal.phone || ''
    }));

    // Convert to CSV
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header]}"`).join(','))
    ].join('\\n');

    // Download
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `terminals_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Update existing functions to work with table
function toggleTerminalSelection(terminalId) {
    if (deploymentState.selectedTerminals.has(terminalId)) {
        deploymentState.selectedTerminals.delete(terminalId);
    } else {
        deploymentState.selectedTerminals.add(terminalId);
    }
    updateProgressStats();
    updateAssignmentButtons();
    renderTerminalTable();
}

function showEmptyHierarchy() {
    showEmptyTable();
}

function showAlert(message, type = 'success') {
    // Create better alert/toast notification
    const alertId = 'alert-' + Date.now();
    const alertClass = type === 'success' ? 'alert-success' : type === 'danger' ? 'alert-danger' : 'alert-info';
    const icon = type === 'success' ? '✅' : type === 'danger' ? '❌' : 'ℹ️';

    const alertHtml = `
        <div id="${alertId}" class="alert ${alertClass}" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; animation: slideIn 0.3s ease;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">${icon}</span>
                <span style="flex: 1;">${message}</span>
                <button type="button" onclick="removeAlert('${alertId}')" style="background: none; border: none; font-size: 18px; cursor: pointer; padding: 0; margin-left: 10px;">×</button>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', alertHtml);

    // Auto-remove after 5 seconds
    setTimeout(() => removeAlert(alertId), 5000);
}

function removeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }
}

function showErrorModal(title, message, details = null) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background: rgba(0,0,0,0.5); z-index: 10000;
        display: flex; justify-content: center; align-items: center;
    `;

    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; padding: 0; max-width: 500px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span>❌</span>
                    <span>${title}</span>
                </h3>
                <button onclick="this.closest('[style*=\"position: fixed\"]').remove()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer;">×</button>
            </div>
            <div style="padding: 20px;">
                <p style="margin: 0 0 15px 0; color: #333; line-height: 1.5;">${message}</p>
                ${details ? `<details style="margin-top: 15px; padding: 10px; background: #f5f5f5; border-radius: 4px;">
                    <summary style="cursor: pointer; font-weight: 600;">Technical Details</summary>
                    <pre style="margin: 10px 0 0 0; font-size: 12px; color: #666; white-space: pre-wrap;">${details}</pre>
                </details>` : ''}
                <div style="text-align: right; margin-top: 20px;">
                    <button onclick="this.closest('[style*=\"position: fixed\"]').remove()" class="btn btn-primary">Close</button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function setLoading(element, isLoading) {
    if (isLoading) {
        element.classList.add('loading');
    } else {
        element.classList.remove('loading');
    }
}

// No longer needed for table view
// function setIndeterminateCheckboxes() {
//     document.querySelectorAll('input[data-indeterminate="true"]').forEach(checkbox => {
//         checkbox.indeterminate = true;
//     });
// }

// Remove old functions that are no longer needed for table view
// The following functions have been removed as they were specific to the hierarchical tree view:
// - renderNode, renderContainerNode, renderTerminalNode
// - toggleNodeSelection, toggleNodeExpansion, getTerminalIdsInNode, findNodeById
// - expandAll, collapseAll, assignAllInNode
// - setIndeterminateCheckboxes

// =====================
// ACTION FUNCTIONS (Updated for Table)
// =====================

function assignSingleTerminal(terminalId) {
    // Quick assign single terminal
    if (deploymentState.selectedTechnicians.size === 0) {
        showAlert('Please select a technician first', 'danger');
        return;
    }

    deploymentState.selectedTerminals.clear();
    deploymentState.selectedTerminals.add(terminalId);
    assignSelected();
}

function assignAll() {
    deploymentState.allTerminals.forEach((terminal, id) => {
        deploymentState.selectedTerminals.add(id);
    });
    assignSelected();
}

function clearAssignments() {
    deploymentState.assignments = {};
    updateProgressStats();
    updateAssignmentSummary();
    updateUnassignedList();
    updateAssignmentButtons();
    updateProgressiveVisibility();
}

function removeAssignment(technicianId) {
    delete deploymentState.assignments[technicianId];
    updateProgressStats();
    updateAssignmentSummary();
    updateUnassignedList();
    updateAssignmentButtons();
    updateProgressiveVisibility();
}

function selectUnassignedTerminal(terminalId) {
    deploymentState.selectedTerminals.add(terminalId);
    updateProgressStats();
    updateAssignmentButtons();
    renderHierarchy();
}

function selectAssignmentMode(mode) {
    document.querySelector(`input[name="assignmentMode"][value="${mode}"]`).checked = true;
}

function updateAssignmentMode() {
    // Handle assignment mode changes
    updateAssignmentButtons();
}

function generateWorkOrders() {
    if (Object.keys(deploymentState.assignments).length === 0) {
        showAlert('No assignments to generate work orders for', 'danger');
        return;
    }

    showAlert('Generating work orders...', 'info');

    // Simulate work order generation
    setTimeout(() => {
        showAlert('Work orders generated successfully!');
    }, 2000);
}

function deployAll() {
    if (Object.keys(deploymentState.assignments).length === 0) {
        showAlert('No assignments to deploy', 'danger');
        return;
    }

    const assignmentCount = Object.keys(deploymentState.assignments).length;
    const terminalCount = Object.values(deploymentState.assignments).reduce((sum, a) => sum + a.terminals.length, 0);

    // Show success modal
    document.getElementById('successModalContent').innerHTML = `
        <div style="text-align: center; margin: 20px 0;">
            <div style="font-size: 64px; margin-block-end: 20px;">🎉</div>
            <h4>Deployment Successful!</h4>
            <p style="color: #666; margin: 15px 0;">
                Successfully deployed <strong>${terminalCount} terminals</strong> to <strong>${assignmentCount} technician(s)</strong>
            </p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <div style="display: grid; gap: 8px; font-size: 14px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Deployment Date:</span>
                        <strong>${deploymentState.deploymentDate || 'Today'}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Total Assignments:</span>
                        <strong>${assignmentCount}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Estimated Completion:</span>
                        <strong>${terminalCount * 1.5} hours</strong>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('assignmentSuccessModal').style.display = 'flex';

    // Clear state after successful deployment
    setTimeout(() => {
        clearAssignments();
    }, 500);
}

function saveAsDraft() {
    showAlert('Deployment saved as draft');
}

function exportDeployment() {
    if (Object.keys(deploymentState.assignments).length === 0) {
        showAlert('No assignments to export', 'danger');
        return;
    }

    // Export logic here
    showAlert('Export completed!');
}

function closeSuccessModal() {
    document.getElementById('assignmentSuccessModal').style.display = 'none';
}

function viewAssignments() {
    closeSuccessModal();
    window.location.href = '/job-assignments';
}

function createNewDeployment() {
    closeSuccessModal();
    window.location.reload();
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const projectModal = document.getElementById('createProjectModal');
    const successModal = document.getElementById('assignmentSuccessModal');

    if (event.target === projectModal) {
        closeProjectModal();
    }
    if (event.target === successModal) {
        closeSuccessModal();
    }
});
</script>

@endsection
