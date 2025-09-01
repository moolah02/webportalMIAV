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
                            <div style="font-weight: bold; color: #333;" id="transfer_asset_name">Asset Name</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Current Employee</label>
                            <div style="font-weight: bold; color: #333;" id="transfer_current_employee">Employee Name</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Assigned Since</label>
                            <div style="color: #333;" id="transfer_assigned_date">Date</div>
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
                        <div style="position: relative;">
                            <input type="text" id="transfer_employee_search" placeholder="Search for new employee..."
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; margin-block-end: 5px;">
                            <select name="new_employee_id" id="transfer_new_employee_id" required
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Choose new employee...</option>
                            </select>
                        </div>
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">
                            Search by name or employee number
                        </div>
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
                                Current Condition <span style="color: #f44336;">*</span>
                            </label>
                            <select name="condition_at_transfer" id="transfer_condition" required
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Assess current condition...</option>
                                <option value="new">New - Like brand new</option>
                                <option value="good">Good - Minor wear, fully functional</option>
                                <option value="fair">Fair - Noticeable wear, some issues</option>
                                <option value="poor">Poor - Significant damage/issues</option>
                            </select>
                        </div>
                    </div>

                    <!-- Transfer Reason -->
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Reason for Transfer <span style="color: #f44336;">*</span>
                        </label>
                        <select name="transfer_reason" id="transfer_reason" required
                                style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                            <option value="">Select reason...</option>
                            <option value="employee_departure">Employee Departure</option>
                            <option value="role_change">Role Change</option>
                            <option value="department_transfer">Department Transfer</option>
                            <option value="project_completion">Project Completion</option>
                            <option value="equipment_upgrade">Equipment Upgrade</option>
                            <option value="performance_issues">Performance Issues</option>
                            <option value="maintenance_request">Maintenance Request</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Custom Reason (shown when "Other" is selected) -->
                    <div id="custom_reason_section" style="display: none;">
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Specify Reason <span style="color: #f44336;">*</span>
                        </label>
                        <input type="text" name="custom_transfer_reason" id="custom_transfer_reason"
                               placeholder="Please specify the reason for transfer..."
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                    </div>

                    <!-- Transfer Notes -->
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Transfer Notes
                        </label>
                        <textarea name="transfer_notes" rows="3"
                                  placeholder="Additional notes about this transfer (handover instructions, special considerations, etc.)..."
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>

                    <!-- New Assignment Details -->
                    <div style="background: #fff3e0; padding: 15px; border-radius: 8px; border: 1px solid #ffcc02;">
                        <h5 style="margin: 0 0 15px 0; color: #e65100; display: flex; align-items: center; gap: 8px;">
                            <span>🎯</span>
                            New Assignment Settings
                        </h5>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                    Expected Return Date
                                </label>
                                <input type="date" name="new_expected_return_date"
                                       style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <div style="font-size: 12px; color: #666; margin-top: 2px;">Optional for new assignment</div>
                            </div>

                            <div>
                                <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                    Priority Level
                                </label>
                                <select name="transfer_priority"
                                        style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="immediate">Immediate</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div>
                        <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                            Notifications
                        </label>
                        <div style="display: grid; gap: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="notify_current_employee" value="1" checked>
                                <span>Notify current employee about transfer</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="notify_new_employee" value="1" checked>
                                <span>Notify new employee about incoming assignment</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="notify_managers" value="1">
                                <span>Notify department managers</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn btn-warning" style="flex: 1; background: #ff9800; border-color: #ff9800; color: white;">
                        <span style="font-size: 16px; margin-right: 8px;">🔄</span>
                        Process Transfer
                    </button>
                    <button type="button" onclick="closeTransferModal()" class="btn" style="padding: 10px 20px;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentAssignmentForTransfer = null;

function openTransferModal(assignmentId) {
    currentAssignmentForTransfer = assignmentId;

    // Fetch assignment details
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            const assignment = data.assignment;

            // Populate assignment info
            document.getElementById('transfer_assignment_id').value = assignment.id;
            document.getElementById('transfer_asset_name').textContent = assignment.asset.name;
            document.getElementById('transfer_current_employee').textContent = assignment.employee.full_name;
            document.getElementById('transfer_current_employee').setAttribute('data-employee-id', assignment.employee.id);
            document.getElementById('transfer_assigned_date').textContent = new Date(assignment.assignment_date).toLocaleDateString();
            document.getElementById('transfer_days_assigned').textContent = `${data.days_assigned} days`;
            document.getElementById('transfer_quantity').textContent = assignment.quantity_assigned;

            // Set form action
            document.getElementById('transferAssetForm').action = `/asset-assignments/${assignment.id}/transfer`;

            // Set default condition based on original condition
            document.getElementById('transfer_condition').value = assignment.condition_when_assigned;

            // Load available employees (excluding current employee)
            loadEmployeesForTransfer();

            // Show modal
            document.getElementById('transferAssetModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeTransferModal() {
    document.getElementById('transferAssetModal').style.display = 'none';
    document.getElementById('transferAssetForm').reset();
    document.getElementById('transfer_new_employee_id').innerHTML = '<option value="">Choose new employee...</option>';
    document.getElementById('custom_reason_section').style.display = 'none';
}

function loadEmployeesForTransfer() {
    const currentEmployeeId = document.getElementById('transfer_current_employee').getAttribute('data-employee-id');

    fetch(${BASE}'/employees/available')
        .then(response => response.json())
        .then(employees => {
            const select = document.getElementById('transfer_new_employee_id');
            select.innerHTML = '<option value="">Choose new employee...</option>';

            // Filter out current employee
            employees
                .filter(employee => employee.id != currentEmployeeId)
                .forEach(employee => {
                    const option = document.createElement('option');
                    option.value = employee.id;
                    option.textContent = `${employee.name} (${employee.employee_number}) - ${employee.department}`;
                    option.setAttribute('data-assigned-count', employee.assigned_assets_count);
                    select.appendChild(option);
                });
        })
        .catch(error => {
            console.error('Error loading employees:', error);
        });
}

// Employee search functionality
document.getElementById('transfer_employee_search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const select = document.getElementById('transfer_new_employee_id');
    const options = select.getElementsByTagName('option');

    for (let i = 1; i < options.length; i++) { // Skip first option
        const option = options[i];
        const text = option.textContent.toLowerCase();

        if (text.includes(searchTerm)) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }
});

// Show/hide custom reason field
document.getElementById('transfer_reason').addEventListener('change', function() {
    const customReasonSection = document.getElementById('custom_reason_section');
    const customReasonInput = document.getElementById('custom_transfer_reason');

    if (this.value === 'other') {
        customReasonSection.style.display = 'block';
        customReasonInput.required = true;
    } else {
        customReasonSection.style.display = 'none';
        customReasonInput.required = false;
        customReasonInput.value = '';
    }
});

// Form submission
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
  method: 'POST',       // ✅ use POST; @method('PATCH') stays in the form
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


// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('transferAssetModal');
    if (event.target === modal) {
        closeTransferModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && document.getElementById('transferAssetModal').style.display === 'flex') {
        closeTransferModal();
    }
});

// Validate transfer date (cannot be before assignment date)
document.getElementById('transfer_date').addEventListener('change', function() {
    const assignmentDate = document.getElementById('transfer_assigned_date').textContent;
    const transferDate = new Date(this.value);
    const assignedDate = new Date(assignmentDate);

    if (transferDate < assignedDate) {
        alert('Transfer date cannot be before the assignment date.');
        this.value = new Date().toISOString().split('T')[0]; // Reset to today
    }
});

// Auto-set expected return date based on transfer priority
document.querySelector('select[name="transfer_priority"]').addEventListener('change', function() {
    const returnDateInput = document.querySelector('input[name="new_expected_return_date"]');
    const today = new Date();
    let suggestedDate;

    switch(this.value) {
        case 'immediate':
            suggestedDate = new Date(today.getTime() + (7 * 24 * 60 * 60 * 1000)); // 1 week
            break;
        case 'urgent':
            suggestedDate = new Date(today.getTime() + (30 * 24 * 60 * 60 * 1000)); // 1 month
            break;
        case 'normal':
        default:
            suggestedDate = new Date(today.getTime() + (90 * 24 * 60 * 60 * 1000)); // 3 months
            break;
    }

    if (!returnDateInput.value) { // Only set if not already set
        returnDateInput.value = suggestedDate.toISOString().split('T')[0];
    }
});
</script>
