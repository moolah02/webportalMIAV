{{-- Return Asset + Assignment Details modals. Not currently included by any view;
     styles (.as-*) live in assets/index.blade.php. --}}
<!-- Return Asset Modal -->
<div id="returnAssetModal" class="as-overlay" style="display: none;" role="dialog" aria-modal="true">
    <div class="as-modal">
        <div class="as-modal-head">
            <h3>Return Asset</h3>
            <button type="button" onclick="closeReturnModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="as-modal-body">
            <!-- Assignment Info Display -->
            <div id="returnAssignmentInfo" class="as-summary">
                <h4 class="as-sec">Assignment Details</h4>
                <div class="as-kv">
                    <div><div class="as-k">Asset</div><div class="as-v is-strong" id="return_asset_name">Asset Name</div></div>
                    <div><div class="as-k">Employee</div><div class="as-v is-strong" id="return_employee_name">Employee Name</div></div>
                </div>
                <div class="as-kv is-3" style="margin-top:12px;">
                    <div><div class="as-k">Assigned Date</div><div class="as-v" id="return_assigned_date">Date</div></div>
                    <div><div class="as-k">Days Assigned</div><div class="as-v" id="return_days_assigned">0 days</div></div>
                    <div><div class="as-k">Quantity</div><div class="as-v" id="return_quantity">1</div></div>
                </div>
            </div>

            <form id="returnAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="return_assignment_id" name="assignment_id">

                <div class="as-stack">
                    <!-- Return Details -->
                    <div class="as-grid">
                        <div>
                            <label class="ui-label" for="return_date">Return Date <span class="as-req">*</span></label>
                            <input type="date" name="return_date" id="return_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input w-full">
                        </div>

                        <div>
                            <label class="ui-label" for="return_condition">Condition When Returned <span class="as-req">*</span></label>
                            <select name="condition_when_returned" id="return_condition" required class="ui-select w-full">
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
                        <label class="ui-label">Return Notes</label>
                        <textarea name="return_notes" rows="3" class="ui-textarea w-full"
                                  placeholder="Optional notes about the return (e.g., reason for return, any issues)..."></textarea>
                    </div>

                    <!-- Asset Status Update -->
                    <div>
                        <span class="ui-label">Update Asset Status</span>
                        <div class="as-choices">
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="available" checked>
                                <svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg> Available
                            </label>
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="maintenance">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-wrench"/></svg> Maintenance
                            </label>
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="damaged">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg> Damaged
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="as-modal-actions">
                    <button type="button" onclick="closeReturnModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-undo"/></svg> Process Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assignment Details Modal -->
<div id="assignmentDetailsModal" class="as-overlay" style="display: none;" role="dialog" aria-modal="true">
    <div class="as-modal is-wide">
        <div class="as-modal-head">
            <h3 id="detailsModalTitle">Assignment Details</h3>
            <button type="button" onclick="closeDetailsModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <!-- Modal Body -->
        <div id="detailsModalBody" class="as-modal-body">
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
                <div class="as-detail">
                    <section class="as-kv">
                        <div>
                            <h4 class="as-sec">Employee Details</h4>
                            <div class="as-k">Name</div><div class="as-v is-strong">${assignment.employee.first_name} ${assignment.employee.last_name}</div>
                            <div class="as-k" style="margin-top:8px">Number</div><div class="as-v mv-mono">${assignment.employee.employee_number}</div>
                            <div class="as-k" style="margin-top:8px">Department</div><div class="as-v">${assignment.employee.department?.name || 'Not assigned'}</div>
                        </div>
                        <div>
                            <h4 class="as-sec">Asset Details</h4>
                            <div class="as-k">Name</div><div class="as-v is-strong">${assignment.asset.name}</div>
                            <div class="as-k" style="margin-top:8px">Category</div><div class="as-v">${assignment.asset.category}</div>
                            <div class="as-k" style="margin-top:8px">SKU</div><div class="as-v mv-mono">${assignment.asset.sku || 'Not assigned'}</div>
                        </div>
                    </section>

                    <section>
                        <h4 class="as-sec">Assignment Timeline</h4>
                        <div class="as-kv">
                            <div><div class="as-k">Assigned Date</div><div class="as-v">${new Date(assignment.assignment_date).toLocaleDateString()}</div></div>
                            <div><div class="as-k">Expected Return</div><div class="as-v">${assignment.expected_return_date ? new Date(assignment.expected_return_date).toLocaleDateString() : 'Not set'}</div></div>
                            <div><div class="as-k">Quantity</div><div class="as-v">${assignment.quantity_assigned}</div></div>
                            <div><div class="as-k">Status</div><div class="as-v"><span class="badge badge-green">${assignment.status.charAt(0).toUpperCase() + assignment.status.slice(1)}</span></div></div>
                        </div>
                    </section>

                    <section>
                        <h4 class="as-sec">Condition Tracking</h4>
                        <div class="as-kv">
                            <div><div class="as-k">Condition When Assigned</div><div class="as-v"><span class="badge badge-gray">${assignment.condition_when_assigned.charAt(0).toUpperCase() + assignment.condition_when_assigned.slice(1)}</span></div></div>
                        </div>
                    </section>

                    ${assignment.assignment_notes ? `
                    <section>
                        <h4 class="as-sec">Notes</h4>
                        <div class="as-k">Assignment Notes</div>
                        <p class="as-note">${assignment.assignment_notes}</p>
                    </section>
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
    submitBtn.innerHTML = 'Processing...';
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

                {{-- Leftover fragment from an older version of this modal (kept as-is structurally) --}}
                <!-- Form Actions -->
                <div class="as-modal-actions">
                    <button type="button" onclick="closeReturnModal()" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-undo"/></svg> Process Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                overdueWarning.innerHTML = `<strong>This asset is ${data.days_overdue} days overdue for return!</strong>`;
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
    submitBtn.innerHTML = 'Processing...';
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
