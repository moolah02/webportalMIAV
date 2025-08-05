<!-- History Filters -->
<div class="content-card" style="margin-block-end: 20px;">
    <form method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto auto; gap: 15px; align-items: end;">
        <input type="hidden" name="tab" value="history">
        
        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Search Employee</label>
            <input type="text" name="employee_search" value="{{ request('employee_search') }}" 
                   placeholder="Search by employee name or number..." 
                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">Status</label>
            <select name="status_filter" style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                <option value="">All Status</option>
                @foreach($statusOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('status_filter') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">From Date</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 5px; font-weight: 500; color: #333;">To Date</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                   style="inline-size: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
        </div>
        
        <button type="submit" class="btn btn-primary">Filter</button>
        
        @if(request()->hasAny(['employee_search', 'status_filter', 'date_from', 'date_to']))
        <a href="{{ route('assets.index', ['tab' => 'history']) }}" class="btn">Clear</a>
        @endif
    </form>
</div>

<!-- Assignment History -->
@if($history->count() > 0)
    <div class="content-card">
        <div style="overflow-x: auto;">
            <table class="assignment-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Asset</th>
                        <th>Quantity</th>
                        <th>Assignment Period</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Condition</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $assignment)
                    <tr>
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
                            <div>
                                <div style="font-weight: 500; color: #333;">{{ $assignment->assignment_date->format('M d, Y') }}</div>
                                <div style="font-size: 12px; color: #666;">
                                    to {{ $assignment->actual_return_date ? $assignment->actual_return_date->format('M d, Y') : 'Present' }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #666;">{{ $assignment->days_assigned }} days</span>
                        </td>
                        <td>
                            <span class="status-badge {{ $assignment->status_badge }}">
                                @switch($assignment->status)
                                    @case('assigned')
                                        🟢 Active
                                        @break
                                    @case('returned')
                                        ✅ Returned
                                        @break
                                    @case('transferred')
                                        🔄 Transferred
                                        @break
                                    @case('lost')
                                        ❌ Lost
                                        @break
                                    @case('damaged')
                                        ⚠️ Damaged
                                        @break
                                    @default
                                        {{ ucfirst($assignment->status) }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <div>
                                <div style="font-size: 12px; color: #666;">When Assigned:</div>
                                <span class="status-badge" style="background: {{ $assignment->condition_when_assigned == 'new' ? '#e8f5e8' : ($assignment->condition_when_assigned == 'good' ? '#e3f2fd' : ($assignment->condition_when_assigned == 'fair' ? '#fff3e0' : '#ffebee')) }}; color: {{ $assignment->condition_when_assigned == 'new' ? '#2e7d32' : ($assignment->condition_when_assigned == 'good' ? '#1976d2' : ($assignment->condition_when_assigned == 'fair' ? '#f57c00' : '#d32f2f')) }};">
                                    {{ ucfirst($assignment->condition_when_assigned) }}
                                </span>
                                
                                @if($assignment->condition_when_returned)
                                    <div style="font-size: 12px; color: #666; margin-top: 4px;">When Returned:</div>
                                    <span class="status-badge" style="background: {{ $assignment->condition_when_returned == 'new' ? '#e8f5e8' : ($assignment->condition_when_returned == 'good' ? '#e3f2fd' : ($assignment->condition_when_returned == 'fair' ? '#fff3e0' : '#ffebee')) }}; color: {{ $assignment->condition_when_returned == 'new' ? '#2e7d32' : ($assignment->condition_when_returned == 'good' ? '#1976d2' : ($assignment->condition_when_returned == 'fair' ? '#f57c00' : '#d32f2f')) }};">
                                        {{ ucfirst($assignment->condition_when_returned) }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <!-- In your assignment history table, replace the Actions column buttons with: -->
<td>
    <div style="display: flex; gap: 5px;">
        <button onclick="viewAssignmentDetails({{ $assignment->id }})" 
                class="btn-small" 
                style="background: #f0f8ff; color: #1976d2; border: 1px solid #1976d2; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
            📋 Details
        </button>
        
        @if($assignment->status === 'assigned')
            <button onclick="openReturnModal({{ $assignment->id }})" 
                    class="btn-small btn-success" 
                    style="background: #e8f5e8; color: #2e7d32; border: 1px solid #2e7d32; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                ↩️ Return
            </button>
            
            <button onclick="openTransferModal({{ $assignment->id }})" 
                    class="btn-small btn-warning" 
                    style="background: #fff3e0; color: #f57c00; border: 1px solid #f57c00; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                🔄 Transfer
            </button>
        @endif
    </div>
</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($history->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $history->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
@else
    <div class="content-card" style="text-align: center; padding: 60px; color: #666;">
        <div style="font-size: 64px; margin-block-end: 20px;">📋</div>
        <h3>No Assignment History</h3>
        <p>No assignment history found matching your criteria.</p>
        @if(request()->hasAny(['employee_search', 'status_filter', 'date_from', 'date_to']))
            <a href="{{ route('assets.index', ['tab' => 'history']) }}" class="btn btn-primary" style="margin-block-start: 15px;">
                View All History
            </a>
        @else
            <a href="{{ route('assets.index', ['tab' => 'assign']) }}" class="btn btn-primary" style="margin-block-start: 15px;">
                Make First Assignment
            </a>
        @endif
    </div>
@endif

<!-- Assignment History Detail Modal -->
<div id="assignmentHistoryModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 600px; inline-size: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>📋</span>
                <span id="historyModalTitle">Assignment History</span>
            </h3>
            <button onclick="closeHistoryModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div id="historyModalBody" style="padding: 20px;">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
function viewAssignmentHistory(assignmentId) {
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            const assignment = data.assignment;
            
            document.getElementById('historyModalTitle').textContent = 
                `${assignment.asset.name} → ${assignment.employee.full_name}`;
            
            const modalBody = document.getElementById('historyModalBody');
            modalBody.innerHTML = `
                <div style="display: grid; gap: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <h4 style="margin-block-end: 10px; color: #333;">👤 Employee Details</h4>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                <div><strong>Name:</strong> ${assignment.employee.full_name}</div>
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
                                <div><strong>Actual Return:</strong> ${assignment.actual_return_date ? new Date(assignment.actual_return_date).toLocaleDateString() : 'Not returned'}</div>
                                <div><strong>Duration:</strong> ${data.days_assigned} days</div>
                                <div><strong>Quantity:</strong> ${assignment.quantity_assigned}</div>
                                <div><strong>Status:</strong> <span class="status-badge ${data.assignment.status === 'assigned' ? 'status-active' : 'status-pending'}">${assignment.status.charAt(0).toUpperCase() + assignment.status.slice(1)}</span></div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="margin-block-end: 10px; color: #333;">🔧 Condition Tracking</h4>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div><strong>Condition When Assigned:</strong> <span class="status-badge">${assignment.condition_when_assigned.charAt(0).toUpperCase() + assignment.condition_when_assigned.slice(1)}</span></div>
                                <div><strong>Condition When Returned:</strong> ${assignment.condition_when_returned ? '<span class="status-badge">' + assignment.condition_when_returned.charAt(0).toUpperCase() + assignment.condition_when_returned.slice(1) + '</span>' : 'Not returned yet'}</div>
                            </div>
                        </div>
                    </div>
                    
                    ${assignment.assignment_notes || assignment.return_notes ? `
                    <div>
                        <h4 style="margin-block-end: 10px; color: #333;">📝 Notes</h4>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            ${assignment.assignment_notes ? `<div style="margin-block-end: 10px;"><strong>Assignment Notes:</strong><br>${assignment.assignment_notes}</div>` : ''}
                            ${assignment.return_notes ? `<div><strong>Return Notes:</strong><br>${assignment.return_notes}</div>` : ''}
                        </div>
                    </div>
                    ` : ''}
                    
                    <div>
                        <h4 style="margin-block-end: 10px; color: #333;">👥 People Involved</h4>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div><strong>Assigned By:</strong> ${assignment.assigned_by ? (data.assignment.assigned_by_user?.full_name || 'System') : 'System'}</div>
                                <div><strong>Returned To:</strong> ${assignment.returned_to ? (data.assignment.returned_to_user?.full_name || 'Unknown') : 'Not returned'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('assignmentHistoryModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment history');
        });
}

function closeHistoryModal() {
    document.getElementById('assignmentHistoryModal').style.display = 'none';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('assignmentHistoryModal');
    if (event.target === modal) {
        closeHistoryModal();
    }
});
</script>