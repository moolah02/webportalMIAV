{{-- Transfer Asset Modal. Not currently included by any view; styles (.as-*) live in
     assets/index.blade.php. --}}
<!-- Transfer Asset Modal -->
<div id="transferAssetModal" class="as-overlay" style="display: none;" role="dialog" aria-modal="true">
    <div class="as-modal">
        <div class="as-modal-head">
            <h3>Transfer Asset</h3>
            <button type="button" onclick="closeTransferModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="as-modal-body">
            <!-- Current Assignment Info -->
            <div id="transferAssignmentInfo" class="as-summary">
                <h4 class="as-sec">Current Assignment</h4>
                <div class="as-kv">
                    <div><div class="as-k">Asset</div><div class="as-v is-strong" id="transfer_asset_name">Asset Name</div></div>
                    <div><div class="as-k">Current Employee</div><div class="as-v is-strong" id="transfer_current_employee">Employee Name</div></div>
                </div>
                <div class="as-kv is-3" style="margin-top:12px;">
                    <div><div class="as-k">Assigned Since</div><div class="as-v" id="transfer_assigned_date">Date</div></div>
                    <div><div class="as-k">Duration</div><div class="as-v" id="transfer_days_assigned">0 days</div></div>
                    <div><div class="as-k">Quantity</div><div class="as-v" id="transfer_quantity">1</div></div>
                </div>
            </div>

            <form id="transferAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="transfer_assignment_id" name="assignment_id">

                <div class="as-stack">
                    <!-- New Employee Selection -->
                    <div>
                        <label class="ui-label" for="transfer_new_employee_id">Transfer To <span class="as-req">*</span></label>
                        <input type="text" id="transfer_employee_search" placeholder="Search for new employee..."
                               class="ui-input w-full" style="margin-bottom:6px;">
                        <select name="new_employee_id" id="transfer_new_employee_id" required class="ui-select w-full">
                            <option value="">Choose new employee...</option>
                        </select>
                        <p class="as-hint">Search by name or employee number</p>
                    </div>

                    <!-- Transfer Details -->
                    <div class="as-grid">
                        <div>
                            <label class="ui-label" for="transfer_date">Transfer Date <span class="as-req">*</span></label>
                            <input type="date" name="transfer_date" id="transfer_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input w-full">
                        </div>

                        <div>
                            <label class="ui-label" for="transfer_condition">Current Condition <span class="as-req">*</span></label>
                            <select name="condition_at_transfer" id="transfer_condition" required class="ui-select w-full">
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
                        <label class="ui-label" for="transfer_reason">Reason for Transfer <span class="as-req">*</span></label>
                        <select name="transfer_reason" id="transfer_reason" required class="ui-select w-full">
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
                        <label class="ui-label" for="custom_transfer_reason">Specify Reason <span class="as-req">*</span></label>
                        <input type="text" name="custom_transfer_reason" id="custom_transfer_reason"
                               placeholder="Please specify the reason for transfer..." class="ui-input w-full">
                    </div>

                    <!-- Transfer Notes -->
                    <div>
                        <label class="ui-label">Transfer Notes</label>
                        <textarea name="transfer_notes" rows="3" class="ui-textarea w-full"
                                  placeholder="Additional notes about this transfer (handover instructions, special considerations, etc.)..."></textarea>
                    </div>

                    <!-- New Assignment Details -->
                    <div class="as-panel">
                        <h4 class="as-sec">New Assignment Settings</h4>
                        <div class="as-grid">
                            <div>
                                <label class="ui-label">Expected Return Date</label>
                                <input type="date" name="new_expected_return_date" class="ui-input w-full">
                                <p class="as-hint">Optional for new assignment</p>
                            </div>

                            <div>
                                <label class="ui-label">Priority Level</label>
                                <select name="transfer_priority" class="ui-select w-full">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="immediate">Immediate</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div>
                        <span class="ui-label">Notifications</span>
                        <div class="as-checks">
                            <label class="as-check">
                                <input type="checkbox" name="notify_current_employee" value="1" checked>
                                <span>Notify current employee about transfer</span>
                            </label>
                            <label class="as-check">
                                <input type="checkbox" name="notify_new_employee" value="1" checked>
                                <span>Notify new employee about incoming assignment</span>
                            </label>
                            <label class="as-check">
                                <input type="checkbox" name="notify_managers" value="1">
                                <span>Notify department managers</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="as-modal-actions">
                    <button type="button" onclick="closeTransferModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg> Process Transfer</button>
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
  // Force the value into the payload so Laravel definitely sees it
  formData.set('new_employee_id', chosen);

  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = 'Processing...';
  submitBtn.disabled = true;

  fetch(`${BASE}/asset-assignments/${currentAssignmentForTransfer}/transfer`, {
  method: 'POST',       // use POST; @method('PATCH') stays in the form
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
