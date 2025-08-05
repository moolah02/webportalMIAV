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

<!-- Current Assignments -->
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
                    <tr class="{{ $assignment->isOverdue() ? 'overdue-row' : '' }}">
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
                                <div style="color: {{ $assignment->isOverdue() ? '#f44336' : '#333' }}; font-weight: {{ $assignment->isOverdue() ? '600' : '500' }};">
                                    {{ $assignment->expected_return_date->format('M d, Y') }}
                                </div>
                                @if($assignment->isOverdue())
                                    <div style="font-size: 12px; color: #f44336; font-weight: 600;">
                                        {{ $assignment->days_overdue }} days overdue
                                    </div>
                                @endif
                            @else
                                <span style="color: #999;">No due date</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #666;">{{ $assignment->days_assigned }} days</span>
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
                                        class="btn-small" title="View Details">
                                    👁️
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

<style>
.overdue-row {
    background-color: #fff5f5 !important;
    border-left: 4px solid #f44336;
}

.overdue-row:hover {
    background-color: #ffebee !important;
}
</style>

<script>
function viewAssignmentDetails(assignmentId) {
    // Fetch assignment details and show in modal or navigate to details page
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            // Show assignment details in a modal or alert
            const assignment = data.assignment;
            const details = `
Assignment Details:
- Employee: ${assignment.employee.full_name}
- Asset: ${assignment.asset.name}
- Assigned: ${new Date(assignment.assignment_date).toLocaleDateString()}
- Quantity: ${assignment.quantity_assigned}
- Condition: ${assignment.condition_when_assigned}
- Days Assigned: ${data.days_assigned}
${data.is_overdue ? '- STATUS: OVERDUE!' : ''}
            `;
            alert(details);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}
</script>