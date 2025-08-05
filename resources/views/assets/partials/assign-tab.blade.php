<!-- Quick Assignment Form -->
<div class="content-card" style="margin-block-end: 30px;">
    <h3 style="margin-block-end: 20px; color: #333; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 24px;">🎯</span>
        Quick Asset Assignment
    </h3>
    
    <form id="quickAssignForm" method="POST" action="{{ route('assets.assign') }}" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 20px; align-items: end;">
        @csrf
        
        <div>
            <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Select Asset</label>
            <select name="asset_id" id="quick_asset_select" required 
                    style="inline-size: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                <option value="">Choose an asset...</option>
                @foreach($availableAssets as $asset)
                    <option value="{{ $asset->id }}" data-available="{{ $asset->available_quantity ?? $asset->stock_quantity }}">
                        {{ $asset->name }} ({{ $asset->available_quantity ?? $asset->stock_quantity }} available)
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Select Employee</label>
            <select name="employee_id" id="quick_employee_select" required 
                    style="inline-size: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                <option value="">Choose an employee...</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">
                        {{ $employee->full_name }} ({{ $employee->employee_number }}) - {{ $employee->department->name ?? 'No Dept' }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Quantity</label>
            <input type="number" name="quantity" id="quick_quantity" min="1" value="1" required 
                   style="inline-size: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
        </div>
        
        <div>
            <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Condition</label>
            <select name="condition_when_assigned" required 
                    style="inline-size: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                @foreach($conditionOptions as $value => $label)
                    <option value="{{ $value }}" {{ $value === 'good' ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="button" onclick="openDetailedAssignModal()" class="btn btn-primary" style="padding: 12px 20px; font-size: 16px;">
            🎯 Assign Asset
        </button>
        
        <input type="hidden" name="assignment_date" value="{{ now()->format('Y-m-d') }}">
    </form>
</div>

<!-- Available Assets for Assignment -->
<div class="content-card">
    <h3 style="margin-block-end: 20px; color: #333; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 24px;">📦</span>
        Available Assets for Assignment
    </h3>
    
    @if($availableAssets->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            @foreach($availableAssets as $asset)
            <div class="asset-card" style="border-left: 4px solid #4caf50;">
                <!-- Asset Header -->
                <div style="display: flex; justify-content: space-between; align-items: start; margin-block-end: 15px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-block-end: 5px;">
                            <h4 style="margin: 0; color: #333;">{{ $asset->name }}</h4>
                            <span class="status-badge status-active">Available</span>
                        </div>
                        <div style="font-size: 14px; color: #666;">
                            @php
                                $categoryObj = $assetCategories->where('name', $asset->category)->first();
                            @endphp
                            {{ $categoryObj->icon ?? '📦' }} {{ $asset->category }}
                            @if($asset->brand || $asset->model)
                                • {{ $asset->brand }} {{ $asset->model }}
                            @endif
                        </div>
                        @if($asset->sku)
                            <div style="font-size: 12px; color: #999;">SKU: {{ $asset->sku }}</div>
                        @endif
                    </div>
                    
                    <div style="text-align: right;">
                        <div style="font-size: 16px; font-weight: bold; color: #333;">
                            {{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Availability Info -->
                <div style="background: #e8f5e8; padding: 12px; border-radius: 6px; margin-block-end: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 8px;">
                        <span style="font-size: 12px; color: #2e7d32; text-transform: uppercase; font-weight: 600;">Availability</span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; text-align: center;">
                        <div>
                            <div style="font-size: 16px; font-weight: bold; color: #333;">{{ $asset->stock_quantity }}</div>
                            <div style="font-size: 12px; color: #666;">Total</div>
                        </div>
                        <div>
                            <div style="font-size: 16px; font-weight: bold; color: #4caf50;">{{ $asset->available_quantity ?? $asset->stock_quantity }}</div>
                            <div style="font-size: 12px; color: #666;">Available</div>
                        </div>
                        @if(isset($asset->assigned_quantity) && $asset->assigned_quantity > 0)
                        <div>
                            <div style="font-size: 16px; font-weight: bold; color: #2196f3;">{{ $asset->assigned_quantity }}</div>
                            <div style="font-size: 12px; color: #666;">Assigned</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Current Assignments Preview -->
                @if($asset->activeAssignments && $asset->activeAssignments->count() > 0)
                <div style="margin-block-end: 15px;">
                    <div style="font-size: 12px; color: #666; margin-block-end: 8px; font-weight: 600;">Currently Assigned To:</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                        @foreach($asset->activeAssignments->take(3) as $assignment)
                        <span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 11px;">
                            {{ $assignment->employee->full_name }} ({{ $assignment->quantity_assigned }})
                        </span>
                        @endforeach
                        @if($asset->activeAssignments->count() > 3)
                        <span style="background: #f5f5f5; color: #666; padding: 4px 8px; border-radius: 12px; font-size: 11px;">
                            +{{ $asset->activeAssignments->count() - 3 }} more
                        </span>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Description -->
                @if($asset->description)
                <div style="margin-block-end: 15px;">
                    <p style="font-size: 14px; color: #666; margin: 0; line-height: 1.4;">
                        {{ Str::limit($asset->description, 80) }}
                    </p>
                </div>
                @endif

                <!-- Actions -->
                <div style="display: flex; gap: 8px;">
                    <button onclick="openAssignModal({{ $asset->id }})" 
                            class="btn-small btn-primary" style="flex: 1;">
                        🎯 Assign Now
                    </button>
                    <button onclick="viewAsset({{ $asset->id }})" 
                            class="btn-small" style="background: #f5f5f5; color: #666;">
                        👁️ View
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($availableAssets->hasPages())
        <div style="margin-top: 30px; display: flex; justify-content: center;">
            {{ $availableAssets->appends(['tab' => 'assign'])->links() }}
        </div>
        @endif
    @else
        <div style="text-align: center; padding: 60px; color: #666;">
            <div style="font-size: 64px; margin-block-end: 20px;">📦</div>
            <h3>No Assets Available for Assignment</h3>
            <p>All requestable assets are either out of stock or fully assigned.</p>
            <a href="{{ route('assets.index', ['tab' => 'assets']) }}" class="btn btn-primary" style="margin-block-start: 15px;">
                View All Assets
            </a>
        </div>
    @endif
</div>

<!-- Detailed Assignment Modal -->
<div id="detailedAssignModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 500px; inline-size: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>🎯</span>
                <span>Asset Assignment Details</span>
            </h3>
            <button onclick="closeDetailedAssignModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <form id="detailedAssignForm" method="POST" action="{{ route('assets.assign') }}">
                @csrf
                
                <div style="display: grid; gap: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600;">Asset</label>
                            <select name="asset_id" id="detailed_asset_id" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select asset...</option>
                                @foreach($availableAssets as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600;">Employee</label>
                            <select name="employee_id" id="detailed_employee_id" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select employee...</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600;">Quantity</label>
                            <input type="number" name="quantity" id="detailed_quantity" min="1" value="1" required 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600;">Condition</label>
                            <select name="condition_when_assigned" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                @foreach($conditionOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $value === 'good' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600;">Assignment Date</label>
                            <input type="date" name="assignment_date" value="{{ now()->format('Y-m-d') }}" required 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600;">Expected Return Date</label>
                            <input type="date" name="expected_return_date" 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600;">Assignment Notes</label>
                        <textarea name="assignment_notes" rows="3" placeholder="Optional notes about this assignment..." 
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        🎯 Assign Asset
                    </button>
                    <button type="button" onclick="closeDetailedAssignModal()" class="btn">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openDetailedAssignModal() {
    // Pre-populate from quick form if values are selected
    const quickAsset = document.getElementById('quick_asset_select').value;
    const quickEmployee = document.getElementById('quick_employee_select').value;
    const quickQuantity = document.getElementById('quick_quantity').value;
    
    if (quickAsset) document.getElementById('detailed_asset_id').value = quickAsset;
    if (quickEmployee) document.getElementById('detailed_employee_id').value = quickEmployee;
    if (quickQuantity) document.getElementById('detailed_quantity').value = quickQuantity;
    
    document.getElementById('detailedAssignModal').style.display = 'flex';
}

function closeDetailedAssignModal() {
    document.getElementById('detailedAssignModal').style.display = 'none';
}

function openAssignModal(assetId) {
    document.getElementById('detailed_asset_id').value = assetId;
    document.getElementById('detailedAssignModal').style.display = 'flex';
}

function viewAsset(assetId) {
    window.location.href = `{{ url('/assets') }}/${assetId}`;
}

// Update quantity max based on selected asset
document.getElementById('quick_asset_select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const available = selectedOption.getAttribute('data-available');
    if (available) {
        document.getElementById('quick_quantity').max = available;
        if (parseInt(document.getElementById('quick_quantity').value) > parseInt(available)) {
            document.getElementById('quick_quantity').value = available;
        }
    }
});

document.getElementById('detailed_asset_id').addEventListener('change', function() {
    const assetId = this.value;
    if (assetId) {
        // Find the asset data to get available quantity
        const assetOption = document.querySelector(`#quick_asset_select option[value="${assetId}"]`);
        if (assetOption) {
            const available = assetOption.getAttribute('data-available');
            document.getElementById('detailed_quantity').max = available;
        }
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('detailedAssignModal');
    if (event.target === modal) {
        closeDetailedAssignModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetailedAssignModal();
    }
});
</script>