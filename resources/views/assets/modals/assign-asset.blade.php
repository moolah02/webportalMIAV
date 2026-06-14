{{-- Assign Asset Modal (opened from asset list "Assign Now" button) --}}
<div id="assignAssetModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="ui-card w-full max-w-lg max-h-[90vh] overflow-y-auto">

        <div class="ui-card-header" style="background:#1a3a5c;">
            <h3 class="text-sm font-semibold text-white m-0">&#x1F3AF; Assign Asset to Employee</h3>
            <button onclick="closeAssignModal()" class="text-white/70 hover:text-white text-xl leading-none border-0 bg-transparent cursor-pointer">&times;</button>
        </div>

        <div class="ui-card-body space-y-4">

            {{-- Asset summary (populated by JS) --}}
            <div id="assignAssetInfo" class="hidden rounded-lg border border-blue-100 bg-blue-50 p-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-gray-800" id="assign_asset_name">—</div>
                        <div class="text-xs text-gray-500 mt-0.5" id="assign_asset_category">—</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-sm font-bold text-[#1a3a5c]">
                            <span id="assign_available_quantity">0</span> available
                        </div>
                        <div class="text-xs text-gray-400">of <span id="assign_total_quantity">0</span> total</div>
                    </div>
                </div>
            </div>

            <form id="assignAssetForm" method="POST" action="{{ route('assets.assign') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="asset_id" id="assign_asset_id">

                <div>
                    <label class="ui-label">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" id="assign_employee_id" required class="ui-select">
                        <option value="">Choose an employee…</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Search by name or employee number</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ui-label">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" id="assign_quantity" min="1" value="1" required class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Condition <span class="text-red-500">*</span></label>
                        <select name="condition_when_assigned" required class="ui-select">
                            <option value="new">New</option>
                            <option value="good" selected>Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
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
                    <textarea name="assignment_notes" rows="2" class="ui-textarea"
                              placeholder="Purpose or special instructions…"></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="submit" id="assignSubmitBtn" class="btn-primary flex-1">&#x1F3AF; Assign Asset</button>
                    <button type="button" onclick="closeAssignModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let assignEmployeeTomSelect = null;

function openAssignModal(assetId) {
    const modal = document.getElementById('assignAssetModal');

    if (assetId) {
        fetch('/assets/' + assetId, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(asset => {
                document.getElementById('assign_asset_id').value = asset.id;
                document.getElementById('assign_asset_name').textContent = asset.name;
                document.getElementById('assign_asset_category').textContent = asset.category ?? '';
                document.getElementById('assign_available_quantity').textContent = asset.available_quantity ?? asset.stock_quantity;
                document.getElementById('assign_total_quantity').textContent = asset.stock_quantity;
                document.getElementById('assign_quantity').max = asset.available_quantity ?? asset.stock_quantity;
                document.getElementById('assignAssetInfo').classList.remove('hidden');
                loadEmployeesForAssignment();
                modal.classList.remove('hidden');
            })
            .catch(() => alert('Failed to load asset details'));
    } else {
        document.getElementById('assignAssetInfo').classList.add('hidden');
        loadEmployeesForAssignment();
        modal.classList.remove('hidden');
    }
}

function closeAssignModal() {
    document.getElementById('assignAssetModal').classList.add('hidden');
    document.getElementById('assignAssetForm').reset();
    if (assignEmployeeTomSelect) {
        assignEmployeeTomSelect.clear();
        assignEmployeeTomSelect.clearOptions();
    }
    document.getElementById('assignAssetInfo').classList.add('hidden');
}

function loadEmployeesForAssignment() {
    fetch('/employees/available', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(employees => {
            const sel = document.getElementById('assign_employee_id');
            if (assignEmployeeTomSelect) {
                assignEmployeeTomSelect.clearOptions();
                employees.forEach(e => assignEmployeeTomSelect.addOption({ value: e.id, text: e.name + ' (' + e.employee_number + ') — ' + (e.department || '') }));
                assignEmployeeTomSelect.refreshOptions(false);
            } else {
                sel.innerHTML = '<option value="">Choose an employee…</option>';
                employees.forEach(e => {
                    const opt = document.createElement('option');
                    opt.value = e.id;
                    opt.textContent = e.name + ' (' + e.employee_number + ') — ' + (e.department || '');
                    sel.appendChild(opt);
                });
                if (window.TomSelect) {
                    assignEmployeeTomSelect = new TomSelect(sel, { allowEmptyOption: true, dropdownParent: 'body' });
                    assignEmployeeTomSelect.positionDropdown = function () {
                        const rect = assignEmployeeTomSelect.control.getBoundingClientRect();
                        assignEmployeeTomSelect.dropdown.style.top   = rect.bottom + 'px';
                        assignEmployeeTomSelect.dropdown.style.left  = rect.left   + 'px';
                        assignEmployeeTomSelect.dropdown.style.width = rect.width  + 'px';
                    };
                }
            }
        })
        .catch(err => console.error('Error loading employees:', err));
}

document.getElementById('assignAssetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('assignSubmitBtn');
    btn.textContent = 'Assigning…';
    btn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => { if (r.ok) { closeAssignModal(); window.location.reload(); } else throw new Error(); })
    .catch(() => alert('Failed to assign asset. Please try again.'))
    .finally(() => { btn.textContent = '🎯 Assign Asset'; btn.disabled = false; });
});

document.getElementById('assign_quantity').addEventListener('input', function() {
    if (parseInt(this.value) > parseInt(this.max)) {
        this.value = this.max;
    }
});

document.getElementById('assignAssetModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});

document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('assignAssetModal');
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeAssignModal();
});
</script>
