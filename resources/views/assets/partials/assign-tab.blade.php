{{-- Quick Assignment Card --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F3AF; Quick Asset Assignment</h3>
    </div>
    <div class="ui-card-body">
        @if(isset($fromRequest))
        <div class="flash-info mb-4">
            &#x1F4E6; Assigning assets from <strong>Approved Request #{{ $fromRequest }}</strong>. Employee has been pre-selected.
        </div>
        @endif

        <form id="quickAssignForm" method="POST" action="{{ route('assets.assign') }}">
            @csrf
            <input type="hidden" name="assignment_date" value="{{ now()->format('Y-m-d') }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="ui-label">Asset <span class="text-red-500">*</span></label>
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
                    <label class="ui-label">Employee <span class="text-red-500">*</span></label>
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
                    <label class="ui-label">Qty <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="quick_quantity" min="1" value="1" required class="ui-input">
                </div>

                <div>
                    <label class="ui-label">Condition <span class="text-red-500">*</span></label>
                    <select name="condition_when_assigned" required class="ui-select">
                        @foreach($conditionOptions as $value => $label)
                            <option value="{{ $value }}" {{ $value === 'good' ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="openDetailedAssignModal()" class="btn-primary">
                    &#x1F3AF; Assign Asset
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Available Assets Table --}}
<div class="ui-card">
    <div class="ui-card-header">
        <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F4E6; Available Assets for Assignment</h3>
        <span class="badge badge-blue">{{ $availableAssets->count() }} assets</span>
    </div>
    <div class="ui-card-body p-0">
        @if($availableAssets->count() > 0)
        <div class="overflow-x-auto">
            <table class="shared-table">
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($availableAssets as $asset)
                    <tr>
                        <td>
                            <div class="font-semibold text-gray-800 text-sm">{{ $asset->name }}</div>
                            @if($asset->brand || $asset->model)
                            <div class="text-xs text-gray-400">{{ trim($asset->brand . ' ' . $asset->model) }}</div>
                            @endif
                            @if($asset->sku)
                            <div class="text-xs text-gray-400">SKU: {{ $asset->sku }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $asset->category ?? '—' }}</span>
                        </td>
                        <td class="text-sm text-gray-700">
                            {{ $asset->currency ?? 'USD' }} {{ number_format($asset->unit_price ?? 0, 2) }}
                        </td>
                        <td>
                            @php $avail = $asset->available_quantity ?? $asset->stock_quantity; @endphp
                            <div class="text-sm font-semibold text-gray-800">{{ $avail }} available</div>
                            <div class="text-xs text-gray-400">of {{ $asset->stock_quantity }} total</div>
                            @if(($asset->assigned_quantity ?? 0) > 0)
                            <div class="text-xs text-amber-600">{{ $asset->assigned_quantity }} assigned</div>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <button onclick="openAssignModal({{ $asset->id }})" class="btn-primary btn-sm">
                                    &#x1F3AF; Assign Now
                                </button>
                                <a href="{{ route('assets.show', $asset->id) }}" class="btn-secondary btn-sm">View</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($availableAssets->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $availableAssets->links() }}
        </div>
        @endif

        @else
        <div class="empty-state py-16">
            <div class="empty-state-icon">&#x1F4E6;</div>
            <div class="empty-state-msg">No assets available for assignment. All requestable assets are out of stock or fully assigned.</div>
            <a href="{{ route('assets.index', ['tab' => 'assets']) }}" class="btn-primary mt-4">View All Assets</a>
        </div>
        @endif
    </div>
</div>

{{-- Detailed Assignment Modal --}}
<div id="detailedAssignModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="ui-card w-full max-w-lg">
        <div class="ui-card-header" style="background:#1a3a5c;">
            <h3 class="text-sm font-semibold text-white m-0">&#x1F3AF; Asset Assignment Details</h3>
            <button onclick="closeDetailedAssignModal()" class="text-white/70 hover:text-white text-xl leading-none border-0 bg-transparent cursor-pointer">&times;</button>
        </div>
        <div class="ui-card-body">
            <form id="detailedAssignForm" method="POST" action="{{ route('assets.assign') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ui-label">Asset <span class="text-red-500">*</span></label>
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
                        <label class="ui-label">Employee <span class="text-red-500">*</span></label>
                        <select name="employee_id" id="detailed_employee_id" required class="ui-select">
                            <option value="">Select employee…</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ui-label">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" id="detailed_quantity" min="1" value="1" required class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Condition <span class="text-red-500">*</span></label>
                        <select name="condition_when_assigned" required class="ui-select">
                            @foreach($conditionOptions as $value => $label)
                                <option value="{{ $value }}" {{ $value === 'good' ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ui-label">Assignment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="assignment_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Expected Return <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                        <input type="date" name="expected_return_date" class="ui-input">
                    </div>
                </div>

                <div>
                    <label class="ui-label">Notes <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                    <textarea name="assignment_notes" rows="3" class="ui-textarea"
                              placeholder="Purpose, special instructions…"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1">&#x1F3AF; Confirm Assignment</button>
                    <button type="button" onclick="closeDetailedAssignModal()" class="btn-secondary">Cancel</button>
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
        banner.className = 'flash-info mb-4';
        banner.innerHTML = '&#x1F4E6; Assigning assets from <strong>Approved Request #' + fromRequest + '</strong>. Employee has been pre-selected.';
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
