@extends('layouts.app')
@php
    $activeTab = request()->get('tab', 'assets'); 
@endphp
@section('content')
<div>
    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📦 Asset Management</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage company assets and track assignments</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($activeTab === 'assets')
                <a href="{{ route('assets.export') }}" class="btn" style="background: #4caf50; color: white; border-color: #4caf50;">
                    📊 Export CSV
                </a>
                <a href="{{ route('assets.create') }}" class="btn btn-primary">+ Add New Asset</a>
            @endif
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

    <!-- Tab Navigation -->
    <div class="tab-navigation" style="margin-block-end: 30px;">
        <div style="display: flex; border-bottom: 2px solid #e0e0e0; margin-block-end: 20px;">
            <a href="{{ route('assets.index', ['tab' => 'assets']) }}" 
               class="tab-link {{ $activeTab === 'assets' ? 'active' : '' }}"
               style="padding: 15px 25px; text-decoration: none; color: {{ $activeTab === 'assets' ? '#2196f3' : '#666' }}; border-bottom: 3px solid {{ $activeTab === 'assets' ? '#2196f3' : 'transparent' }}; font-weight: 500; transition: all 0.3s ease;">
                <span style="font-size: 18px; margin-right: 8px;">📦</span>
                All Assets
                @if(isset($stats))
                    <span style="background: {{ $activeTab === 'assets' ? '#2196f3' : '#ddd' }}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 8px;">
                        {{ $stats['total_assets'] }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('assets.index', ['tab' => 'assignments']) }}" 
               class="tab-link {{ $activeTab === 'assignments' ? 'active' : '' }}"
               style="padding: 15px 25px; text-decoration: none; color: {{ $activeTab === 'assignments' ? '#2196f3' : '#666' }}; border-bottom: 3px solid {{ $activeTab === 'assignments' ? '#2196f3' : 'transparent' }}; font-weight: 500; transition: all 0.3s ease;">
                <span style="font-size: 18px; margin-right: 8px;">👥</span>
                Current Assignments
                @if(isset($assignmentStats))
                    <span style="background: {{ $activeTab === 'assignments' ? '#2196f3' : '#ddd' }}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 8px;">
                        {{ $assignmentStats['active_assignments'] }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('assets.index', ['tab' => 'history']) }}" 
               class="tab-link {{ $activeTab === 'history' ? 'active' : '' }}"
               style="padding: 15px 25px; text-decoration: none; color: {{ $activeTab === 'history' ? '#2196f3' : '#666' }}; border-bottom: 3px solid {{ $activeTab === 'history' ? '#2196f3' : 'transparent' }}; font-weight: 500; transition: all 0.3s ease;">
                <span style="font-size: 18px; margin-right: 8px;">📋</span>
                Assignment History
            </a>
            
            <a href="{{ route('assets.index', ['tab' => 'assign']) }}" 
               class="tab-link {{ $activeTab === 'assign' ? 'active' : '' }}"
               style="padding: 15px 25px; text-decoration: none; color: {{ $activeTab === 'assign' ? '#2196f3' : '#666' }}; border-bottom: 3px solid {{ $activeTab === 'assign' ? '#2196f3' : 'transparent' }}; font-weight: 500; transition: all 0.3s ease;">
                <span style="font-size: 18px; margin-right: 8px;">🎯</span>
                Assign Assets
            </a>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'assets')
            @include('assets.partials.assets-tab')
        @elseif($activeTab === 'assignments')
            @include('assets.partials.assignments-tab')
        @elseif($activeTab === 'history')
            @include('assets.partials.history-tab')
        @elseif($activeTab === 'assign')
            @include('assets.partials.assign-tab')
        @endif
    </div>
</div>

<!-- Return Asset Modal -->
<div id="returnAssetModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1003; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>↩️</span>
                <span>Return Asset</span>
            </h3>
            <button onclick="closeReturnModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <!-- Assignment Info Display -->
            <div id="returnAssignmentInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-block-end: 20px;">
                <h4 style="margin: 0 0 15px 0; color: #333;">Assignment Details</h4>
                
                <div style="display: grid; gap: 12px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Asset</label>
                            <div style="font-weight: bold; color: #333;" id="return_asset_name">Asset Name</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Employee</label>
                            <div style="font-weight: bold; color: #333;" id="return_employee_name">Employee Name</div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Assigned Date</label>
                            <div style="color: #333;" id="return_assigned_date">Date</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Days Assigned</label>
                            <div style="color: #333;" id="return_days_assigned">0 days</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Quantity</label>
                            <div style="color: #333;" id="return_quantity">1</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="returnAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="return_assignment_id" name="assignment_id">
                
                <div style="display: grid; gap: 20px;">
                    <!-- Return Details -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Return Date <span style="color: #f44336;">*</span>
                            </label>
                            <input type="date" name="return_date" id="return_date" value="{{ now()->format('Y-m-d') }}" required 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Condition When Returned <span style="color: #f44336;">*</span>
                            </label>
                            <select name="condition_when_returned" id="return_condition" required 
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select condition...</option>
                                <option value="new">New - Like brand new</option>
                                <option value="good">Good - Minor wear, fully functional</option>
                                <option value="fair">Fair - Noticeable wear, some issues</option>
                                <option value="poor">Poor - Significant damage/issues</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Return Notes -->
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Return Notes
                        </label>
                        <textarea name="return_notes" rows="3" 
                                  placeholder="Optional notes about the return (e.g., reason for return, any issues)..." 
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                    
                    <!-- Asset Status Update -->
                    <div>
                        <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                            Update Asset Status
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="update_asset_status" value="available" checked>
                                <span>📦 Available</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="update_asset_status" value="maintenance">
                                <span>🔧 Maintenance</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="update_asset_status" value="damaged">
                                <span>⚠️ Damaged</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; background: #2196f3; border-color: #2196f3; color: white; padding: 12px; border-radius: 6px; border: none; cursor: pointer;">
                        <span style="font-size: 16px; margin-right: 8px;">↩️</span>
                        Process Return
                    </button>
                    <button type="button" onclick="closeReturnModal()" style="padding: 10px 20px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Asset Modal -->
<div id="transferAssetModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1004; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>🔄</span>
                <span>Transfer Asset</span>
            </h3>
            <button onclick="closeTransferModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <!-- Current Assignment Info -->
            <div id="transferAssignmentInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-block-end: 20px;">
                <h4 style="margin: 0 0 15px 0; color: #333;">Current Assignment</h4>
                
                <div style="display: grid; gap: 12px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Asset</label>
                            <div style="font-weight: bold; color: #333;" id="transfer_asset_name">Asset Name</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Current Employee</label>
                            <div style="font-weight: bold; color: #333;" id="transfer_current_employee">Employee Name</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="transferAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="transfer_assignment_id" name="assignment_id">
                
                <div style="display: grid; gap: 20px;">
                    <!-- New Employee Selection -->
                    <div>
                        <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                            Transfer To <span style="color: #f44336;">*</span>
                        </label>
                        <select name="new_employee_id" id="transfer_new_employee_id" required 
                                style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                            <option value="">Choose new employee...</option>
                            @if(isset($employees))
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_number }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <!-- Transfer Details -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Transfer Date <span style="color: #f44336;">*</span>
                            </label>
                            <input type="date" name="transfer_date" id="transfer_date" value="{{ now()->format('Y-m-d') }}" required 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Reason <span style="color: #f44336;">*</span>
                            </label>
                            <select name="transfer_reason" required 
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select reason...</option>
                                <option value="employee_departure">Employee Departure</option>
                                <option value="role_change">Role Change</option>
                                <option value="department_transfer">Department Transfer</option>
                                <option value="project_completion">Project Completion</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Transfer Notes -->
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Transfer Notes
                        </label>
                        <textarea name="transfer_notes" rows="3" 
                                  placeholder="Additional notes about this transfer..." 
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn btn-warning" style="flex: 1; background: #ff9800; border-color: #ff9800; color: white; padding: 12px; border-radius: 6px; border: none; cursor: pointer;">
                        <span style="font-size: 16px; margin-right: 8px;">🔄</span>
                        Process Transfer
                    </button>
                    <button type="button" onclick="closeTransferModal()" style="padding: 10px 20px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assignment Details Modal -->
<div id="assignmentDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>📋</span>
                <span id="detailsModalTitle">Assignment Details</span>
            </h3>
            <button onclick="closeDetailsModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div id="detailsModalBody" style="padding: 20px;">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<style>
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.asset-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.asset-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.assignment-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #2196f3;
}

.assignment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.assignment-card.overdue {
    border-left-color: #f44336;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-inactive { background: #f5f5f5; color: #666; }
.status-discontinued { background: #ffebee; color: #d32f2f; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-offline { background: #ffebee; color: #d32f2f; }

.stock-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.stock-in_stock { background: #e8f5e8; color: #2e7d32; }
.stock-low_stock { background: #fff3e0; color: #f57c00; }
.stock-out_of_stock { background: #ffebee; color: #d32f2f; }

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
    text-decoration: none;
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
    padding: 8px 12px;
    font-size: 14px;
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

.btn-warning {
    background: #ff9800;
    color: white;
    border-color: #ff9800;
}

.btn-warning:hover {
    background: #f57c00;
    border-color: #f57c00;
    color: white;
}

.btn-danger {
    background: #f44336;
    color: white;
    border-color: #f44336;
}

.btn-danger:hover {
    background: #d32f2f;
    border-color: #d32f2f;
    color: white;
}

.tab-link:hover {
    color: #2196f3 !important;
    border-bottom-color: #2196f3 !important;
}

.modal-action-btn {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: start;
    inline-size: 100%;
}

.modal-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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

/* Table styles for assignment tabs */
.assignment-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.assignment-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
}

.assignment-table td {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.assignment-table tbody tr:hover {
    background: #f8f9fa;
}

.employee-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.employee-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.asset-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.asset-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}
</style>

<script>
// CSRF token setup for AJAX requests
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Global variables for modal management
let currentAssetId = null;
let currentAssignmentForReturn = null;
let currentAssignmentForDetails = null;
let currentAssignmentForTransfer = null;

// Asset Quick Actions Functions (existing functionality)
function assetQuickActions(assetId, assetName) {
    currentAssetId = assetId;
    if (document.getElementById('modalAssetName')) {
        document.getElementById('modalAssetName').textContent = `Actions for ${assetName}`;
        document.getElementById('assetQuickActionsModal').style.display = 'flex';
    }
}

function closeAssetActions() {
    if (document.getElementById('assetQuickActionsModal')) {
        document.getElementById('assetQuickActionsModal').style.display = 'none';
    }
}

function viewAsset() {
    closeAssetActions();
    window.location.href = `{{ url('/assets') }}/${currentAssetId}`;
}

function editAsset() {
    closeAssetActions();
    window.location.href = `{{ url('/assets') }}/${currentAssetId}/edit`;
}

function updateStock() {
    closeAssetActions();
    if (document.getElementById('stockUpdateModal')) {
        document.getElementById('stockUpdateModal').style.display = 'flex';
    }
}

function closeStockModal() {
    if (document.getElementById('stockUpdateModal')) {
        document.getElementById('stockUpdateModal').style.display = 'none';
    }
}

function deleteAsset() {
    closeAssetActions();
    if (confirm('Are you sure you want to delete this asset? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('/assets') }}/${currentAssetId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Return Asset Modal Functions
function openReturnModal(assignmentId) {
    currentAssignmentForReturn = assignmentId;
    
    // Fetch assignment details and populate modal
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignment = data.assignment;
                
                // Populate assignment info in return modal
                document.getElementById('return_assignment_id').value = assignment.id;
                document.getElementById('return_asset_name').textContent = assignment.asset.name;
                document.getElementById('return_employee_name').textContent = assignment.employee.first_name + ' ' + assignment.employee.last_name;
                document.getElementById('return_assigned_date').textContent = new Date(assignment.assignment_date).toLocaleDateString();
                document.getElementById('return_days_assigned').textContent = (assignment.days_assigned || 0) + ' days';
                document.getElementById('return_quantity').textContent = assignment.quantity_assigned;
                
                // Show modal
                document.getElementById('returnAssetModal').style.display = 'flex';
            } else {
                alert('Failed to load assignment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeReturnModal() {
    document.getElementById('returnAssetModal').style.display = 'none';
    document.getElementById('returnAssetForm').reset();
}

// Transfer Asset Modal Functions
function openTransferModal(assignmentId) {
    currentAssignmentForTransfer = assignmentId;
    
    // Fetch assignment details and populate modal
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignment = data.assignment;
                
                // Populate assignment info in transfer modal  
                document.getElementById('transfer_assignment_id').value = assignment.id;
                document.getElementById('transfer_asset_name').textContent = assignment.asset.name;
                document.getElementById('transfer_current_employee').textContent = assignment.employee.first_name + ' ' + assignment.employee.last_name;
                
                // Show modal
                document.getElementById('transferAssetModal').style.display = 'flex';
            } else {
                alert('Failed to load assignment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeTransferModal() {
    document.getElementById('transferAssetModal').style.display = 'none';
    document.getElementById('transferAssetForm').reset();
}

// Assignment Details Modal Functions  
function viewAssignmentDetails(assignmentId) {
    currentAssignmentForDetails = assignmentId;
    
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignment = data.assignment;
                
                document.getElementById('detailsModalTitle').textContent = 
                    `${assignment.asset.name} → ${assignment.employee.first_name} ${assignment.employee.last_name}`;
                
                const modalBody = document.getElementById('detailsModalBody');
                modalBody.innerHTML = `
                    <div style="display: grid; gap: 20px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <h4 style="margin-block-end: 10px; color: #333;">👤 Employee Details</h4>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div><strong>Name:</strong> ${assignment.employee.first_name} ${assignment.employee.last_name}</div>
                                    <div><strong>Number:</strong> ${assignment.employee.employee_number}</div>
                                    <div><strong>Department:</strong> ${assignment.employee.department?.name || 'Not assigned'}</div>
                                </div>
                            </div>
                            <div>
                                <h4 style="margin-block-end: 10px; color: #333;">📦 Asset Details</h4>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div><strong>Name:</strong> ${assignment.asset.name}</div>
                                    <div><strong>Category:</strong> ${assignment.asset.category}</div>
                                    <div><strong>SKU:</strong> ${assignment.asset.sku || 'Not assigned'}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 style="margin-block-end: 10px; color: #333;">📋 Assignment Timeline</h4>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div><strong>Assigned Date:</strong> ${new Date(assignment.assignment_date).toLocaleDateString()}</div>
                                    <div><strong>Expected Return:</strong> ${assignment.expected_return_date ? new Date(assignment.expected_return_date).toLocaleDateString() : 'Not set'}</div>
                                    <div><strong>Quantity:</strong> ${assignment.quantity_assigned}</div>
                                    <div><strong>Status:</strong> <span style="padding: 4px 8px; background: #e8f5e8; color: #2e7d32; border-radius: 12px; font-size: 12px;">${assignment.status.charAt(0).toUpperCase() + assignment.status.slice(1)}</span></div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 style="margin-block-end: 10px; color: #333;">🔧 Condition Tracking</h4>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                <div><strong>Condition When Assigned:</strong> <span style="padding: 4px 8px; background: #e3f2fd; color: #1976d2; border-radius: 12px; font-size: 12px;">${assignment.condition_when_assigned.charAt(0).toUpperCase() + assignment.condition_when_assigned.slice(1)}</span></div>
                            </div>
                        </div>
                        
                        ${assignment.assignment_notes ? `
                        <div>
                            <h4 style="margin-block-end: 10px; color: #333;">📝 Notes</h4>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                <div><strong>Assignment Notes:</strong><br>${assignment.assignment_notes}</div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                document.getElementById('assignmentDetailsModal').style.display = 'flex';
            } else {
                alert('Failed to load assignment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeDetailsModal() {
    document.getElementById('assignmentDetailsModal').style.display = 'none';
}

function requestAsset(assetId) {
    const quantity = prompt('How many units would you like to request?', '1');
    if (quantity !== null && !isNaN(quantity) && quantity > 0) {
        alert(`Request for ${quantity} units submitted!`);
    }
}

// Document Ready Functions
document.addEventListener('DOMContentLoaded', function() {
    // Handle stock update form submission
    const stockForm = document.getElementById('stockUpdateForm');
    if (stockForm) {
        stockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newStock = document.getElementById('newStockQuantity').value;
            if (!newStock || newStock < 0) {
                alert('Please enter a valid stock quantity.');
                return;
            }
            
            fetch(`{{ url('/assets') }}/${currentAssetId}/update-stock`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({
                    stock_quantity: parseInt(newStock)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeStockModal();
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update stock.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating stock.');
            });
        });
    }

    // Handle return form submission
    const returnForm = document.getElementById('returnAssetForm');
    if (returnForm) {
        returnForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentAssignmentForReturn) {
                alert('No assignment selected');
                return;
            }
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span style="font-size: 16px; margin-right: 8px;">⏳</span>Processing...';
            submitBtn.disabled = true;
            
            fetch(`/asset-assignments/${currentAssignmentForReturn}/return`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeReturnModal();
                    alert('Asset returned successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to return asset: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to process return');
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Handle transfer form submission
    const transferForm = document.getElementById('transferAssetForm');
    if (transferForm) {
        transferForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentAssignmentForTransfer) {
                alert('No assignment selected');
                return;
            }
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span style="font-size: 16px; margin-right: 8px;">⏳</span>Processing...';
            submitBtn.disabled = true;
            
            fetch(`/asset-assignments/${currentAssignmentForTransfer}/transfer`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeTransferModal();
                    alert('Asset transferred successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to transfer asset: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to process transfer');
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// Close modals when clicking outside or pressing Escape
document.addEventListener('click', function(event) {
    const quickActionsModal = document.getElementById('assetQuickActionsModal');
    const stockModal = document.getElementById('stockUpdateModal');
    const returnModal = document.getElementById('returnAssetModal');
    const detailsModal = document.getElementById('assignmentDetailsModal');
    const transferModal = document.getElementById('transferAssetModal');
    
    if (quickActionsModal && event.target === quickActionsModal) {
        closeAssetActions();
    }
    
    if (stockModal && event.target === stockModal) {
        closeStockModal();
    }
    
    if (event.target === returnModal) {
        closeReturnModal();
    }
    
    if (event.target === detailsModal) {
        closeDetailsModal();
    }
    
    if (event.target === transferModal) {
        closeTransferModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAssetActions();
        closeStockModal();
        
        if (document.getElementById('returnAssetModal').style.display === 'flex') {
            closeReturnModal();
        }
        if (document.getElementById('assignmentDetailsModal').style.display === 'flex') {
            closeDetailsModal();
        }
        if (document.getElementById('transferAssetModal').style.display === 'flex') {
            closeTransferModal();
        }
    }
});
</script>

@endsection