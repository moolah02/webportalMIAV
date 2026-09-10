{{-- Assign Assets tab (styles live in assets/index.blade.php) --}}

{{-- Quick Assignment Card --}}
<div class="ui-card mb-4">
    <div class="ui-card-header">
        <h3 class="as-card-title">Quick Asset Assignment</h3>
    </div>
    <div class="ui-card-body">
        @if(isset($fromRequest))
        <div class="flash-info as-banner mb-4">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-info"/></svg>
            <span>Assigning assets from <strong>Approved Request #{{ $fromRequest }}</strong>. Employee has been pre-selected.</span>
        </div>
        @endif

        <form id="quickAssignForm" method="POST" action="{{ route('assets.assign') }}">
            @csrf
            <input type="hidden" name="assignment_date" value="{{ now()->format('Y-m-d') }}">

            <div class="as-grid is-4">
                <div>
                    <label class="ui-label" for="quick_asset_select">Asset <span class="as-req">*</span></label>
                    <select name="asset_id" id="quick_asset_select" required class="ui-select">
                        <option value="">Choose an asset…</option>
                        @foreach($availableAssets as $asset)
                            <option value="{{ $asset->id }}" data-available="{{ $asset->available_quantity ?? $asset->stock_quantity }}">
                                {{ $asset->name }} ({{ $asset->available_quantity ?? $asset->stock_quantity }} avail.)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="ui-label" for="quick_employee_select">Employee <span class="as-req">*</span></label>
                    <select name="employee_id" id="quick_employee_select" required class="ui-select">
                        <option value="">Choose an employee…</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->full_name }} ({{ $emp->employee_number }})
                                @if($emp->department) — {{ $emp->department->name ?? '' }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="ui-label" for="quick_quantity">Qty <span class="as-req">*</span></label>
                    <input type="number" name="quantity" id="quick_quantity" min="1" value="1" required class="ui-input">
                </div>

                <div>
                    <label class="ui-label">Condition <span class="as-req">*</span></label>
                    <select name="condition_when_assigned" required class="ui-select">
                        @foreach($conditionOptions as $value => $label)
                            <option value="{{ $value }}" {{ $value === 'good' ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="as-form-actions">
                <button type="button" onclick="openDetailedAssignModal()" class="btn-primary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-user-plus"/></svg> Assign Asset
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Available Assets Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <div class="as-card-head">
            <h3 class="as-card-title">Available Assets for Assignment</h3>
            <span class="as-card-meta">{{ $availableAssets->count() }} assets</span>
        </div>
    </div>
    @if($availableAssets->count() > 0)
    <div class="overflow-x-auto">
        <table class="ui-table shared-table">
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Category</th>
                    <th class="as-right">Unit Price</th>
                    <th>Stock</th>
                    <th class="as-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($availableAssets as $asset)
                <tr>
                    <td>
                        <div class="cell-primary">{{ $asset->name }}</div>
                        <div class="cell-sub">
                            @if($asset->brand || $asset->model){{ trim($asset->brand . ' ' . $asset->model) }}@endif
                            @if(($asset->brand || $asset->model) && $asset->sku) &middot; @endif
                            @if($asset->sku)SKU <span class="mv-mono">{{ $asset->sku }}</span>@endif
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-gray">{{ $asset->category ?? '—' }}</span>
                    </td>
                    <td class="as-right">
                        <span class="as-num">{{ $asset->currency ?? 'USD' }} {{ number_format($asset->unit_price ?? 0, 2) }}</span>
                    </td>
                    <td>
                        @php $avail = $asset->available_quantity ?? $asset->stock_quantity; @endphp
                        <div class="as-num">{{ $avail }} available</div>
                        <div class="cell-sub">
                            of {{ $asset->stock_quantity }} total
                            @if(($asset->assigned_quantity ?? 0) > 0)
                            &middot; {{ $asset->assigned_quantity }} assigned
                            @endif
                        </div>
                    </td>
                    <td class="as-right">
                        <div class="action-group">
                            <button type="button" onclick="openAssignModal({{ $asset->id }})" class="btn-secondary btn-sm">
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-user-plus"/></svg> Assign Now
                            </button>
                            <a href="{{ route('assets.show', $asset->id) }}" class="action-btn" title="View" aria-label="View"><svg class="mv-i" aria-hidden="true"><use href="#i-eye"/></svg></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($availableAssets->hasPages())
    <div class="ui-card-footer as-pager">
        {{ $availableAssets->links() }}
    </div>
    @endif

    @else
    <div class="empty-state">
        <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg></div>
        <p class="empty-state-msg">No assets available for assignment. All requestable assets are out of stock or fully assigned.</p>
        <a href="{{ route('assets.index', ['tab' => 'assets']) }}" class="btn-secondary btn-sm">View All Assets</a>
    </div>
    @endif
</div>

{{-- Detailed Assignment Modal --}}
<div id="detailedAssignModal" class="hidden as-overlay flex" role="dialog" aria-modal="true">
    <div class="as-modal">
        <div class="as-modal-head">
            <h3>Asset Assignment Details</h3>
            <button type="button" onclick="closeDetailedAssignModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="as-modal-body">
            <form id="detailedAssignForm" method="POST" action="{{ route('assets.assign') }}">
                @csrf

                <div class="as-grid">
                    <div>
                        <label class="ui-label" for="detailed_asset_id">Asset <span class="as-req">*</span></label>
                        <select name="asset_id" id="detailed_asset_id" required class="ui-select">
                            <option value="">Select asset…</option>
                            @foreach($availableAssets as $asset)
                                <option value="{{ $asset->id }}" data-available="{{ $asset->available_quantity ?? $asset->stock_quantity }}">
                                    {{ $asset->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ui-label" for="detailed_employee_id">Employee <span class="as-req">*</span></label>
                        <select name="employee_id" id="detailed_employee_id" required class="ui-select">
                            <option value="">Select employee…</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="ui-label" for="detailed_quantity">Quantity <span class="as-req">*</span></label>
                        <input type="number" name="quantity" id="detailed_quantity" min="1" value="1" required class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Condition <span class="as-req">*</span></label>
                        <select name="condition_when_assigned" required class="ui-select">
                            @foreach($conditionOptions as $value => $label)
                                <option value="{{ $value }}" {{ $value === 'good' ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="ui-label">Assignment Date <span class="as-req">*</span></label>
                        <input type="date" name="assignment_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Expected Return <span class="as-opt">(optional)</span></label>
                        <input type="date" name="expected_return_date" class="ui-input">
                    </div>

                    <div class="is-full">
                        <label class="ui-label">Notes <span class="as-opt">(optional)</span></label>
                        <textarea name="assignment_notes" rows="3" class="ui-textarea"
                                  placeholder="Purpose, special instructions…"></textarea>
                    </div>
                </div>

                <div class="as-modal-actions">
                    <button type="button" onclick="closeDetailedAssignModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg> Confirm Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function makeTomSelectFixed(el) {
        if (!el) return;
        const ts = new TomSelect(el, { allowEmptyOption: true, dropdownParent: 'body' });
        ts.positionDropdown = function () {
            const rect = ts.control.getBoundingClientRect();
            ts.dropdown.style.top   = rect.bottom + 'px';
            ts.dropdown.style.left  = rect.left   + 'px';
            ts.dropdown.style.width = rect.width  + 'px';
        };
    }
    ['quick_asset_select','quick_employee_select','detailed_asset_id','detailed_employee_id'].forEach(function(id) {
        makeTomSelectFixed(document.getElementById(id));
    });

    // Pre-select from URL params (approved request flow)
    const params = new URLSearchParams(window.location.search);
    const employeeId = params.get('employee_id');
    const fromRequest = params.get('from_request');

    if (employeeId) {
        ['quick_employee_select','detailed_employee_id'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el && el.tomselect) el.tomselect.setValue(employeeId);
        });
    }
    if (fromRequest) {
        const banner = document.createElement('div');
        banner.className = 'flash-info as-banner mb-4';
        banner.innerHTML = '<svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-info"/></svg><span>Assigning assets from <strong>Approved Request #' + fromRequest + '</strong>. Employee has been pre-selected.</span>';
        const form = document.getElementById('quickAssignForm');
        if (form) form.parentNode.insertBefore(banner, form);
    }

    // Sync quick-form qty cap when asset changes
    document.getElementById('quick_asset_select').addEventListener('change', function() {
        const avail = this.options[this.selectedIndex]?.getAttribute('data-available');
        if (avail) {
            const qty = document.getElementById('quick_quantity');
            qty.max = avail;
            if (parseInt(qty.value) > parseInt(avail)) qty.value = avail;
        }
    });

    document.getElementById('detailed_asset_id').addEventListener('change', function() {
        const avail = this.options[this.selectedIndex]?.getAttribute('data-available');
        if (avail) {
            const qty = document.getElementById('detailed_quantity');
            qty.max = avail;
        }
    });
});

function openAssignModal(assetId) {
    const sel = document.getElementById('detailed_asset_id');
    if (sel && sel.tomselect) sel.tomselect.setValue(assetId);
    else if (sel) sel.value = assetId;
    openDetailedAssignModal();
}

function openDetailedAssignModal() {
    const quickAsset    = document.getElementById('quick_asset_select')?.value;
    const quickEmployee = document.getElementById('quick_employee_select')?.value;
    const quickQty      = document.getElementById('quick_quantity')?.value;
    const assetSel      = document.getElementById('detailed_asset_id');
    const empSel        = document.getElementById('detailed_employee_id');

    if (quickAsset && assetSel) {
        assetSel.tomselect ? assetSel.tomselect.setValue(quickAsset) : (assetSel.value = quickAsset);
    }
    if (quickEmployee && empSel) {
        empSel.tomselect ? empSel.tomselect.setValue(quickEmployee) : (empSel.value = quickEmployee);
    }
    if (quickQty) document.getElementById('detailed_quantity').value = quickQty;

    document.getElementById('detailedAssignModal').classList.remove('hidden');
}

function closeDetailedAssignModal() {
    document.getElementById('detailedAssignModal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDetailedAssignModal();
});

document.getElementById('detailedAssignModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailedAssignModal();
});
</script>
