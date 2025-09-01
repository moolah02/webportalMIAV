
<!-- Complete Assignments Tab Section -->
<!-- Add this at the very top of your blade file or ensure it's in your layout -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="app-base-url" content="{{ url('/') }}">


<!-- Assignment Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
    <div class="metric-card" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="font-size: 32px;">👥</div>
            <div>
                <div style="font-size: 28px; font-weight: bold;">{{ $assignmentStats['active_assignments'] ?? 0 }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Active Assignments</div>
            </div>
        </div>
    </div>

    <div class="metric-card" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="font-size: 32px;">⏰</div>
            <div>
                <div style="font-size: 28px; font-weight: bold;">{{ $assignmentStats['overdue_assignments'] ?? 0 }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Overdue Returns</div>
            </div>
        </div>
    </div>

    <div class="metric-card" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="font-size: 32px;">📈</div>
            <div>
                <div style="font-size: 28px; font-weight: bold;">{{ $assignmentStats['returned_this_month'] ?? 0 }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Returned This Month</div>
            </div>
        </div>
    </div>

    <div class="metric-card" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="font-size: 32px;">📊</div>
            <div>
                <div style="font-size: 28px; font-weight: bold;">{{ $assignmentStats['total_assignments'] ?? 0 }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Total Assignments</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="content-card" style="margin-block-end: 20px;">
    <form method="GET" style="display: grid; grid-template-columns: 2fr 2fr 1fr auto auto auto; gap: 15px; align-items: end;">
        <input type="hidden" name="tab" value="assignments">

        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Search Employee</label>
            <input type="text" name="employee_search" value="{{ request('employee_search') }}"
                   placeholder="Search by employee name or number..."
                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
        </div>

        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Search Asset</label>
            <input type="text" name="asset_search" value="{{ request('asset_search') }}"
                   placeholder="Search by asset name or SKU..."
                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
        </div>

        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Department</label>
            <select name="department" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; align-items: center; gap: 8px; padding: 10px 0;">
            <input type="checkbox" name="overdue_only" value="1" {{ request('overdue_only') ? 'checked' : '' }}
                   id="overdue_filter" style="transform: scale(1.2);">
            <label for="overdue_filter" style="font-weight: 500; color: #f44336;">Overdue Only</label>
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>

        @if(request()->hasAny(['employee_search', 'asset_search', 'department', 'overdue_only']))
        <a href="{{ route('assets.index', ['tab' => 'assignments']) }}" class="btn">Clear</a>
        @endif
    </form>
</div>

<!-- Current Assignments Table -->
@if($assignments->count() > 0)
    <div class="content-card">
        <div style="overflow-x: auto;">
            <table class="assignment-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Asset</th>
                        <th>Quantity</th>
                        <th>Assigned Date</th>
                        <th>Expected Return</th>
                        <th>Days Assigned</th>
                        <th>Condition</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    <tr class="{{ $assignment->is_overdue ? 'overdue-row' : '' }}">
                        <td>
                            <div class="employee-info">
                                <div class="employee-avatar">
                                    {{ strtoupper(substr($assignment->employee->first_name, 0, 1)) }}{{ strtoupper(substr($assignment->employee->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #333;">{{ $assignment->employee->full_name }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ $assignment->employee->employee_number }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ $assignment->employee->department->name ?? 'No Department' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="asset-info">
                                <div class="asset-icon">📦</div>
                                <div>
                                    <div style="font-weight: 600; color: #333;">{{ $assignment->asset->name }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ $assignment->asset->category }}</div>
                                    @if($assignment->asset->sku)
                                        <div style="font-size: 12px; color: #999;">SKU: {{ $assignment->asset->sku }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-weight: 600;">
                                {{ $assignment->quantity_assigned }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 500; color: #333;">{{ $assignment->assignment_date->format('M d, Y') }}</div>
                            <div style="font-size: 12px; color: #666;">by {{ $assignment->assignedBy->full_name ?? 'System' }}</div>
                        </td>
                        <td>
                            @if($assignment->expected_return_date)
                                <div style="color: {{ $assignment->is_overdue ? '#f44336' : '#333' }}; font-weight: {{ $assignment->is_overdue ? '600' : '500' }};">
                                    {{ $assignment->expected_return_date->format('M d, Y') }}
                                </div>
                                @if($assignment->is_overdue)
                                    <div style="font-size: 12px; color: #f44336; font-weight: 600;">
                                        {{ $assignment->days_overdue }} days overdue
                                    </div>
                                @endif
                            @else
                                <span style="color: #999;">No due date</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #666;">{{ (int)$assignment->days_assigned }} days</span>
                        </td>
                        <td>
                            <span class="status-badge" style="background: {{ $assignment->condition_when_assigned == 'new' ? '#e8f5e8' : ($assignment->condition_when_assigned == 'good' ? '#e3f2fd' : ($assignment->condition_when_assigned == 'fair' ? '#fff3e0' : '#ffebee')) }}; color: {{ $assignment->condition_when_assigned == 'new' ? '#2e7d32' : ($assignment->condition_when_assigned == 'good' ? '#1976d2' : ($assignment->condition_when_assigned == 'fair' ? '#f57c00' : '#d32f2f')) }};">
                                {{ ucfirst($assignment->condition_when_assigned) }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button onclick="openReturnModal({{ $assignment->id }})"
                                        class="btn-small btn-success" title="Return Asset">
                                    ↩️ Return
                                </button>
                                <button onclick="openTransferModal({{ $assignment->id }})"
                                        class="btn-small btn-warning" title="Transfer Asset">
                                    🔄 Transfer
                                </button>
                                <button onclick="viewAssignmentDetails({{ $assignment->id }})"
                                        class="btn-small btn-info" title="View Details">
                                    👁️ Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assignments->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $assignments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
@else
    <div class="content-card" style="text-align: center; padding: 60px; color: #666;">
        <div style="font-size: 64px; margin-block-end: 20px;">👥</div>
        <h3>No Active Assignments</h3>
        <p>No assets are currently assigned to employees.</p>
        <a href="{{ route('assets.index', ['tab' => 'assign']) }}" class="btn btn-primary" style="margin-block-start: 15px;">
            Assign First Asset
        </a>
    </div>
@endif

<!-- MODALS SECTION -->

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
                            <div style="font-weight: bold; color: #333;" id="return_asset_name">Loading...</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Employee</label>
                            <div style="font-weight: bold; color: #333;" id="return_employee_name">Loading...</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Assigned Date</label>
                            <div style="color: #333;" id="return_assigned_date">Loading...</div>
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

                <!-- Overdue Warning -->
                <div id="overdue_warning" style="display: none; margin-top: 15px; padding: 10px; background: #ffebee; color: #c62828; border-radius: 6px; font-weight: 500;"></div>
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
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="update_asset_status" value="retired">
                                <span>🚫 Retired</span>
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
<div id="transferAssetModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1004; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 500px; inline-size: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
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
                            <div style="font-weight: bold; color: #333;" id="transfer_asset_name">Loading...</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Current Employee</label>
                            <div style="font-weight: bold; color: #333;" id="transfer_current_employee">Loading...</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Assigned Since</label>
                            <div style="color: #333;" id="transfer_assigned_date">Loading...</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Duration</label>
                            <div style="color: #333;" id="transfer_days_assigned">0 days</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Quantity</label>
                            <div style="color: #333;" id="transfer_quantity">1</div>
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
                            <option value="">Loading employees...</option>
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
                                Transfer Reason <span style="color: #f44336;">*</span>
                            </label>
                            <select name="transfer_reason" id="transfer_reason" required
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select reason...</option>
                                <option value="Employee departure">Employee Departure</option>
                                <option value="Role change">Role Change</option>
                                <option value="Department transfer">Department Transfer</option>
                                <option value="Project completion">Project Completion</option>
                                <option value="Equipment upgrade">Equipment Upgrade</option>
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
<div id="assignmentDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1002; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
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

<!-- STYLES -->
<style>
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.assignment-table {
    width: 100%;
    border-collapse: collapse;
}

.assignment-table th {
    background: #f5f5f5;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #ddd;
}

.assignment-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.assignment-table tr:hover {
    background: #f9f9f9;
}

.employee-info, .asset-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.employee-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #2196f3;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.asset-icon {
    font-size: 24px;
}

.overdue-row {
    background-color: #fff5f5 !important;
    border-left: 4px solid #f44336;
}

.overdue-row:hover {
    background-color: #ffebee !important;
}

.btn-small {
    padding: 5px 10px;
    font-size: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-success {
    background: #4caf50;
    color: white;
}

.btn-success:hover {
    background: #388e3c;
}

.btn-warning {
    background: #ff9800;
    color: white;
}

.btn-warning:hover {
    background: #f57c00;
}

.btn-info {
    background: #2196f3;
    color: white;
}

.btn-info:hover {
    background: #1976d2;
}

input[type="radio"]:checked + span {
    color: #2196f3;
    font-weight: 600;
}

input[type="radio"]:checked {
    accent-color: #2196f3;
}

label:has(input[type="radio"]:checked) {
    border-color: #2196f3 !important;
    background: #f3f8ff;
}

label:has(input[type="radio"]):hover {
    border-color: #2196f3;
    background: #fafbff;
}
</style>

<!-- JAVASCRIPT -->
<script>

    const BASE = document.querySelector('meta[name="app-base-url"]').content.replace(/\/$/, '');
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Global variables
let currentAssignmentForReturn = null;
let currentAssignmentForTransfer = null;
let currentAssignmentForDetails = null;

// Return Asset Modal Functions
function openReturnModal(assignmentId) {
    currentAssignmentForReturn = assignmentId;

    // Reset form
    document.getElementById('returnAssetForm').reset();

    // Show loading state
    document.getElementById('return_asset_name').textContent = 'Loading...';
    document.getElementById('return_employee_name').textContent = 'Loading...';
    document.getElementById('return_assigned_date').textContent = 'Loading...';
    document.getElementById('return_days_assigned').textContent = 'Loading...';
    document.getElementById('return_quantity').textContent = 'Loading...';

    // Show modal
    document.getElementById('returnAssetModal').style.display = 'flex';

    // Fetch assignment details
   fetch(`${BASE}/asset-assignments/${assignmentId}/data`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const assignment = data.assignment;

            // Populate assignment info
            document.getElementById('return_assignment_id').value = assignment.id;
            document.getElementById('return_asset_name').textContent = assignment.asset.name;
            document.getElementById('return_employee_name').textContent =
                `${assignment.employee.first_name} ${assignment.employee.last_name}`;
            document.getElementById('return_assigned_date').textContent =
                new Date(assignment.assignment_date).toLocaleDateString();
            document.getElementById('return_days_assigned').textContent = `${data.days_assigned || 0} days`;
            document.getElementById('return_quantity').textContent = assignment.quantity_assigned;

            // Show overdue warning if applicable
            const overdueWarning = document.getElementById('overdue_warning');
            if (data.is_overdue) {
                overdueWarning.style.display = 'block';
                overdueWarning.innerHTML = `<strong>⚠️ This asset is ${data.days_overdue} days overdue!</strong>`;
            } else {
                overdueWarning.style.display = 'none';
            }
        }
    })
    /*.catch(error => {
        console.error('Error:', error);
        alert('Failed to load assignment details');
        closeReturnModal();
    });*/
}

function closeReturnModal() {
    document.getElementById('returnAssetModal').style.display = 'none';
    document.getElementById('returnAssetForm').reset();
    currentAssignmentForReturn = null;
}

// Transfer Asset Modal Functions
function openTransferModal(assignmentId) {
    currentAssignmentForTransfer = assignmentId;

    // Reset form
    document.getElementById('transferAssetForm').reset();

    // Show loading state
    document.getElementById('transfer_asset_name').textContent = 'Loading...';
    document.getElementById('transfer_current_employee').textContent = 'Loading...';
    document.getElementById('transfer_assigned_date').textContent = 'Loading...';
    document.getElementById('transfer_days_assigned').textContent = 'Loading...';
    document.getElementById('transfer_quantity').textContent = 'Loading...';

    // Show modal
    document.getElementById('transferAssetModal').style.display = 'flex';

    // Fetch assignment details
    fetch(`${BASE}/asset-assignments/${assignmentId}/data`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const assignment = data.assignment;

            // Populate assignment info
            document.getElementById('transfer_assignment_id').value = assignment.id;
            document.getElementById('transfer_asset_name').textContent = assignment.asset.name;
            document.getElementById('transfer_current_employee').textContent =
                `${assignment.employee.first_name} ${assignment.employee.last_name}`;
            document.getElementById('transfer_current_employee').setAttribute('data-employee-id', assignment.employee.id);
            document.getElementById('transfer_assigned_date').textContent =
                new Date(assignment.assignment_date).toLocaleDateString();
            document.getElementById('transfer_days_assigned').textContent = `${data.days_assigned || 0} days`;
            document.getElementById('transfer_quantity').textContent = assignment.quantity_assigned;

            // Load available employees
            loadEmployeesForTransfer(assignment.employee.id);
        }
    })
    /*.catch(error => {
        console.error('Error:', error);
        alert('Failed to load assignment details');
        closeTransferModal();
    });*/
}

function closeTransferModal() {
    document.getElementById('transferAssetModal').style.display = 'none';
    document.getElementById('transferAssetForm').reset();
    currentAssignmentForTransfer = null;
}

function loadEmployeesForTransfer(currentEmployeeId) {
  fetch(`${BASE}/employees/available`, {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
  .then(response => response.json())
  .then(employees => {
    const select = document.getElementById('transfer_new_employee_id');
    select.innerHTML = '<option value="">Choose new employee...</option>';

    employees
      .filter(employee => employee.id != currentEmployeeId)
      .forEach(employee => {
        const option = document.createElement('option');
        option.value = employee.id;
        option.textContent = `${employee.name} (${employee.employee_number}) - ${employee.department}`;
        select.appendChild(option);
      });
  })
  .catch(error => {
    console.error('Error loading employees:', error);
    document.getElementById('transfer_new_employee_id').innerHTML =
      '<option value="">Failed to load employees</option>';
  });
}


// View Assignment Details Functions
function viewAssignmentDetails(assignmentId) {
    currentAssignmentForDetails = assignmentId;

    // Show loading state
    document.getElementById('detailsModalBody').innerHTML = '<p>Loading...</p>';
    document.getElementById('assignmentDetailsModal').style.display = 'flex';

   fetch(`${BASE}/asset-assignments/${assignmentId}/data`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
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
                                <div><strong>Days Assigned:</strong> ${data.days_assigned || 0} days</div>
                            </div>
                        </div>
                    </div>

                    ${assignment.assignment_notes ? `
                    <div>
                        <h4 style="margin-block-end: 10px; color: #333;">📝 Notes</h4>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            ${assignment.assignment_notes}
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
        }
    })
    /*.catch(error => {
        console.error('Error:', error);
        document.getElementById('detailsModalBody').innerHTML =
            '<p style="color: #f44336;">Failed to load assignment details</p>';
    });*/
}

function closeDetailsModal() {
    document.getElementById('assignmentDetailsModal').style.display = 'none';
    currentAssignmentForDetails = null;
}

// Form Submissions
document.getElementById('returnAssetForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!currentAssignmentForReturn) {
        alert('No assignment selected');
        return;
    }

    const formData = new FormData(this);
    const jsonData = {};
    formData.forEach((value, key) => {
        if (key !== '_token' && key !== '_method') {
            jsonData[key] = value;
        }
    });

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '⏳ Processing...';
    submitBtn.disabled = true;

    fetch(`${BASE}/asset-assignments/${currentAssignmentForReturn}/return`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Asset returned successfully!');
            closeReturnModal();
            window.location.reload();
        } else {
            alert('Failed to return asset: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to process return');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

document.getElementById('transferAssetForm').addEventListener('submit', function (e) {
  e.preventDefault();

  if (!currentAssignmentForTransfer) {
    alert('No assignment selected');
    return;
  }

  const select = document.getElementById('transfer_new_employee_id');
  const chosen = select?.value || '';

  if (!chosen) {
    alert('Choose who to transfer to');
    return;
  }

  const formData = new FormData(this);
  // 🔑 Force the value into the payload so Laravel definitely sees it
  formData.set('new_employee_id', chosen);

  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '⏳ Processing...';
  submitBtn.disabled = true;

 fetch(`${BASE}/asset-assignments/${currentAssignmentForTransfer}/transfer`, {
  method: 'POST',
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  body: formData        // keep FormData (do NOT set Content-Type)
})

  .then(async (response) => {
    const raw = await response.text();
    let data = null; try { data = raw ? JSON.parse(raw) : null; } catch {}
    if (!response.ok) {
      const msg = (data && (data.message || JSON.stringify(data.errors || data))) || raw || `HTTP ${response.status}`;
      throw new Error(msg);
    }
    return data || {};
  })
  .then((data) => {
    if (data.success) {
      alert('Asset transferred successfully!');
      closeTransferModal();
      window.location.reload();
    } else {
      alert('Failed to transfer asset: ' + (data.message || 'Unknown error'));
    }
  })
  .catch((err) => {
    console.error(err);
    alert('Failed to process transfer: ' + err.message);
  })
  .finally(() => {
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
  });
});


// Auto-update asset status based on condition
document.getElementById('return_condition')?.addEventListener('change', function() {
    const statusRadios = document.querySelectorAll('input[name="update_asset_status"]');

    switch(this.value) {
        case 'poor':
            statusRadios.forEach(radio => {
                if (radio.value === 'damaged') radio.checked = true;
            });
            break;
        case 'fair':
            statusRadios.forEach(radio => {
                if (radio.value === 'maintenance') radio.checked = true;
            });
            break;
        case 'good':
        case 'new':
            statusRadios.forEach(radio => {
                if (radio.value === 'available') radio.checked = true;
            });
            break;
    }
});

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const returnModal = document.getElementById('returnAssetModal');
    const detailsModal = document.getElementById('assignmentDetailsModal');
    const transferModal = document.getElementById('transferAssetModal');

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
        if (document.getElementById('returnAssetModal').style.display === 'flex') {
            closeReturnModal();
        }
        if (document.getElementById('assignmentDetailsModal').style.display === 'flex') {
            closeDetailsModal();
        }
        if (document.getElementById('transferAssetModal')?.style.display === 'flex') {
            closeTransferModal();
        }
    }
});
</script>
