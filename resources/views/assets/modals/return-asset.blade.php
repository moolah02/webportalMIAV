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

<script>
let currentAssignmentForReturn = null;
let currentAssignmentForDetails = null;

// Return Asset Modal Functions
function openReturnModal(assignmentId) {
    currentAssignmentForReturn = assignmentId;

    // For now, show modal with basic data - you can enhance this to fetch actual data
    document.getElementById('return_assignment_id').value = assignmentId;
    document.getElementById('returnAssetModal').style.display = 'flex';
}

function closeReturnModal() {
    document.getElementById('returnAssetModal').style.display = 'none';
    document.getElementById('returnAssetForm').reset();
}

// Assignment Details Modal Functions
function viewAssignmentDetails(assignmentId) {
    currentAssignmentForDetails = assignmentId;

    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
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
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeDetailsModal() {
    document.getElementById('assignmentDetailsModal').style.display = 'none';
}

// Form submission for return
document.getElementById('returnAssetForm').addEventListener('submit', function(e) {
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const returnModal = document.getElementById('returnAssetModal');
    const detailsModal = document.getElementById('assignmentDetailsModal');

    if (event.target === returnModal) {
        closeReturnModal();
    }
    if (event.target === detailsModal) {
        closeDetailsModal();
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
    }
});
</script>

                <!-- Form Actions -->
                <div style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn btn-success" style="flex: 1; background: #2196f3; border-color: #2196f3;">
                        <span style="font-size: 16px; margin-right: 8px;">↩️</span>
                        Process Return
                    </button>
                    <button type="button" onclick="closeReturnModal()" class="btn" style="padding: 10px 20px;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Radio button styling */
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

<script>
let currentAssignmentForReturn = null;

function openReturnModal(assignmentId) {
    currentAssignmentForReturn = assignmentId;

    // Fetch assignment details
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            const assignment = data.assignment;

            // Populate assignment info
            document.getElementById('return_assignment_id').value = assignment.id;
            document.getElementById('return_asset_name').textContent = assignment.asset.name;
            document.getElementById('return_employee_name').textContent = assignment.employee.full_name;
            document.getElementById('return_assigned_date').textContent = new Date(assignment.assignment_date).toLocaleDateString();
            document.getElementById('return_days_assigned').textContent = `${data.days_assigned} days`;
            document.getElementById('return_quantity').textContent = assignment.quantity_assigned;

            // Set form action
            document.getElementById('returnAssetForm').action = `/asset-assignments/${assignment.id}/return`;

            // Show overdue warning if applicable
            const overdueWarning = document.getElementById('overdue_warning');
            if (data.is_overdue) {
                overdueWarning.style.display = 'block';
                overdueWarning.innerHTML = `<strong>⚠️ This asset is ${data.days_overdue} days overdue for return!</strong>`;
            } else {
                overdueWarning.style.display = 'none';
            }

            // Set default condition based on original condition
            const returnCondition = document.getElementById('return_condition');
            returnCondition.value = assignment.condition_when_assigned;

            // Show modal
            document.getElementById('returnAssetModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeReturnModal() {
    document.getElementById('returnAssetModal').style.display = 'none';
    document.getElementById('returnAssetForm').reset();
    document.getElementById('condition_details_section').style.display = 'none';
}

// Show/hide condition details based on selected condition
document.getElementById('return_condition').addEventListener('change', function() {
    const conditionDetailsSection = document.getElementById('condition_details_section');
    const conditionDescription = document.getElementById('condition_description');

    if (this.value === 'fair' || this.value === 'poor') {
        conditionDetailsSection.style.display = 'block';
        conditionDescription.required = true;
    } else {
        conditionDetailsSection.style.display = 'none';
        conditionDescription.required = false;
    }
});

// Form submission
document.getElementById('returnAssetForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.innerHTML = '<span style="font-size: 16px; margin-right: 8px;">⏳</span>Processing...';
    submitBtn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            closeReturnModal();
            // Show success message and reload
            alert('Asset returned successfully!');
            window.location.reload();
        } else {
            return response.json().then(data => {
                throw new Error(data.message || 'Return failed');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to process return: ' + error.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Auto-update asset status based on condition
document.addEventListener('change', function(e) {
    if (e.target.name === 'condition_when_returned') {
        const statusRadios = document.querySelectorAll('input[name="update_asset_status"]');

        switch(e.target.value) {
            case 'poor':
                // Auto-select damaged for poor condition
                statusRadios.forEach(radio => {
                    if (radio.value === 'damaged') radio.checked = true;
                });
                break;
            case 'fair':
                // Auto-select maintenance for fair condition
                statusRadios.forEach(radio => {
                    if (radio.value === 'maintenance') radio.checked = true;
                });
                break;
            case 'good':
            case 'new':
                // Auto-select available for good/new condition
                statusRadios.forEach(radio => {
                    if (radio.value === 'available') radio.checked = true;
                });
                break;
        }
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('returnAssetModal');
    if (event.target === modal) {
        closeReturnModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && document.getElementById('returnAssetModal').style.display === 'flex') {
        closeReturnModal();
    }
});

// Validate return date (cannot be before assignment date)
document.getElementById('return_date').addEventListener('change', function() {
    const assignmentDate = document.getElementById('return_assigned_date').textContent;
    const returnDate = new Date(this.value);
    const assignedDate = new Date(assignmentDate);

    if (returnDate < assignedDate) {
        alert('Return date cannot be before the assignment date.');
        this.value = new Date().toISOString().split('T')[0]; // Reset to today
    }
});
</script>
