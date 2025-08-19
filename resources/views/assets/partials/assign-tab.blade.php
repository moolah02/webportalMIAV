<!-- Quick Assignment Form -->
<div class="content-card" style="margin-block-end: 30px;">
    <h3 style="margin-block-end: 20px; color: #333; font-size: 20px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 20px;">🎯</span>
        Quick Asset Assignment
    </h3>

    <form id="quickAssignForm" method="POST" action="{{ route('assets.assign') }}" style="margin-bottom: 20px;">
        @csrf

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>
                    <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Select Asset</label>
                    <select name="asset_id" id="quick_asset_select" required 
                            style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                        <option value="">Choose an asset...</option>
                        @foreach($availableAssets as $asset)
                            <option value="{{ $asset->id }}" data-available="{{ $asset->available_quantity ?? $asset->stock_quantity }}">
                                {{ $asset->name }} ({{ $asset->available_quantity ?? $asset->stock_quantity }} available)
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Select Employee</label>
                    <select name="employee_id" id="quick_employee_select" required 
                            style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                        <option value="">Choose an employee...</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->full_name }} ({{ $employee->employee_number }}) - {{ $employee->department->name ?? 'No Dept' }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Quantity</label>
                    <input type="number" name="quantity" id="quick_quantity" min="1" value="1" required 
                           style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                </td>
                <td>
                    <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">Condition</label>
                    <select name="condition_when_assigned" required 
                            style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                        @foreach($conditionOptions as $value => $label)
                            <option value="{{ $value }}" {{ $value === 'good' ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" onclick="openDetailedAssignModal()" class="btn btn-primary" style="padding: 12px 20px; font-size: 16px;">
                        🎯 Assign Asset
                    </button>
                    <input type="hidden" name="assignment_date" value="{{ now()->format('Y-m-d') }}">
                </td>
            </tr>
        </table>
    </form>
</div>

<!-- Available Assets for Assignment -->
<div class="content-card">
    <h3 style="margin-block-end: 20px; color: #333; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 24px;">📦</span>
        Available Assets for Assignment
    </h3>

    @if($availableAssets->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Asset Name</th>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Category</th>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Price</th>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Availability</th>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($availableAssets as $asset)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                        <strong>{{ $asset->name }}</strong>
                        <div style="font-size: 14px; color: #666;">
                            @php
                                $categoryObj = $assetCategories->where('name', $asset->category)->first();
                            @endphp
                            {{ $categoryObj->icon ?? '📦' }} {{ $asset->category }}
                            @if($asset->brand || $asset->model)
                                • {{ $asset->brand }} {{ $asset->model }}
                            @endif
                        </div>
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $asset->category }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                        {{ $asset->currency }} {{ number_format($asset->unit_price, 2) }}
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                        Total: {{ $asset->stock_quantity }}<br>
                        Available: {{ $asset->available_quantity ?? $asset->stock_quantity }}<br>
                        @if(isset($asset->assigned_quantity) && $asset->assigned_quantity > 0)
                        Assigned: {{ $asset->assigned_quantity }}
                        @endif
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                        <button onclick="openAssignModal({{ $asset->id }})" class="btn-small btn-primary">
                            🎯 Assign Now
                        </button>
                        <button onclick="viewAsset({{ $asset->id }})" class="btn-small" style="background: #f5f5f5; color: #666;">
                            👁️ View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        @if($availableAssets->hasPages())
        <div style="margin-top: 30px; display: flex; justify-content: center;">
            <div>
                {{ $availableAssets->links('pagination::simple-bootstrap-4') }}
            </div>
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