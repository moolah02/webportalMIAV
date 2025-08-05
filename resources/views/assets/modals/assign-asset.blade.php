<!-- Assign Asset Modal -->
<div id="assignAssetModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1002; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 500px; inline-size: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>🎯</span>
                <span>Assign Asset to Employee</span>
            </h3>
            <button onclick="closeAssignModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <!-- Asset Info Display -->
            <div id="assignAssetInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-block-end: 20px; display: none;">
                <h4 style="margin: 0 0 10px 0; color: #333;">Asset Details</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <strong id="assign_asset_name">Asset Name</strong><br>
                        <span style="color: #666; font-size: 14px;" id="assign_asset_category">Category</span>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: #4caf50; font-weight: bold;">
                            <span id="assign_available_quantity">0</span> Available
                        </div>
                        <div style="color: #666; font-size: 14px;">
                            of <span id="assign_total_quantity">0</span> total
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="assignAssetForm" method="POST" action="{{ route('assets.assign') }}">
                @csrf
                <input type="hidden" name="asset_id" id="assign_asset_id">
                
                <div style="display: grid; gap: 20px;">
                    <!-- Employee Selection -->
                    <div>
                        <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                            Select Employee <span style="color: #f44336;">*</span>
                        </label>
                        <div style="position: relative;">
                            <input type="text" id="employee_search" placeholder="Search employees..." 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; margin-block-end: 5px;">
                            <select name="employee_id" id="assign_employee_id" required 
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Choose an employee...</option>
                            </select>
                        </div>
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">
                            Search by name or employee number
                        </div>
                    </div>
                    
                    <!-- Assignment Details -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Quantity <span style="color: #f44336;">*</span>
                            </label>
                            <input type="number" name="quantity" id="assign_quantity" min="1" value="1" required 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Condition <span style="color: #f44336;">*</span>
                            </label>
                            <select name="condition_when_assigned" required 
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="new">New</option>
                                <option value="good" selected>Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Dates -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Assignment Date <span style="color: #f44336;">*</span>
                            </label>
                            <input type="date" name="assignment_date" value="{{ now()->format('Y-m-d') }}" required 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Expected Return Date
                            </label>
                            <input type="date" name="expected_return_date" 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                            <div style="font-size: 12px; color: #666; margin-top: 2px;">Optional</div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Assignment Notes
                        </label>
                        <textarea name="assignment_notes" rows="3" 
                                  placeholder="Optional notes about this assignment (e.g., purpose, special instructions)..." 
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn btn-success" style="flex: 1; background: #4caf50; border-color: #4caf50;">
                        <span style="font-size: 16px; margin-right: 8px;">🎯</span>
                        Assign Asset
                    </button>
                    <button type="button" onclick="closeAssignModal()" class="btn" style="padding: 10px 20px;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentAssetForAssignment = null;

function openAssignModal(assetId) {
    currentAssetForAssignment = assetId;
    
    if (assetId) {
        // Fetch asset details
        fetch(`/assets/${assetId}`)
            .then(response => response.json())
            .then(asset => {
                // Populate asset info
                document.getElementById('assign_asset_id').value = asset.id;
                document.getElementById('assign_asset_name').textContent = asset.name;
                document.getElementById('assign_asset_category').textContent = asset.category;
                document.getElementById('assign_available_quantity').textContent = asset.available_quantity || asset.stock_quantity;
                document.getElementById('assign_total_quantity').textContent = asset.stock_quantity;
                
                // Set quantity max
                document.getElementById('assign_quantity').max = asset.available_quantity || asset.stock_quantity;
                
                // Show asset info
                document.getElementById('assignAssetInfo').style.display = 'block';
                
                // Load employees
                loadEmployeesForAssignment();
                
                // Show modal
                document.getElementById('assignAssetModal').style.display = 'flex';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load asset details');
            });
    } else {
        // Just open modal without pre-selecting asset
        document.getElementById('assignAssetInfo').style.display = 'none';
        loadEmployeesForAssignment();
        document.getElementById('assignAssetModal').style.display = 'flex';
    }
}

function closeAssignModal() {
    document.getElementById('assignAssetModal').style.display = 'none';
    document.getElementById('assignAssetForm').reset();
    document.getElementById('assign_employee_id').innerHTML = '<option value="">Choose an employee...</option>';
}

function loadEmployeesForAssignment() {
    // Load all active employees
    fetch('/employees/available')
        .then(response => response.json())
        .then(employees => {
            const select = document.getElementById('assign_employee_id');
            select.innerHTML = '<option value="">Choose an employee...</option>';
            
            employees.forEach(employee => {
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
document.getElementById('employee_search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const select = document.getElementById('assign_employee_id');
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

// Form submission
document.getElementById('assignAssetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<span style="font-size: 16px; margin-right: 8px;">⏳</span>Assigning...';
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
            closeAssignModal();
            // Reload page or update UI
            window.location.reload();
        } else {
            throw new Error('Assignment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to assign asset. Please try again.');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('assignAssetModal');
    if (event.target === modal) {
        closeAssignModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && document.getElementById('assignAssetModal').style.display === 'flex') {
        closeAssignModal();
    }
});

// Quantity validation
document.getElementById('assign_quantity').addEventListener('input', function() {
    const max = parseInt(this.max);
    const value = parseInt(this.value);
    
    if (value > max) {
        this.value = max;
        alert(`Maximum available quantity is ${max}`);
    }
});
</script>