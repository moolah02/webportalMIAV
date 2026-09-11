{{-- Assign Asset Modal (opened from asset list "Assign Now" button).
     Not currently included by any view; styles (.as-*) live in assets/index.blade.php. --}}
<div id="assignAssetModal" class="hidden as-overlay flex" role="dialog" aria-modal="true">
    <div class="as-modal">

        <div class="as-modal-head">
            <h3>Assign Asset to Employee</h3>
            <button type="button" onclick="closeAssignModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="as-modal-body">

            {{-- Asset summary (populated by JS) --}}
            <div id="assignAssetInfo" class="hidden as-summary">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="as-v is-strong" id="assign_asset_name">—</div>
                        <div class="cell-sub" id="assign_asset_category">—</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="as-num">
                            <span id="assign_available_quantity">0</span> available
                        </div>
                        <div class="cell-sub">of <span id="assign_total_quantity">0</span> total</div>
                    </div>
                </div>
            </div>

            <form id="assignAssetForm" method="POST" action="{{ route('assets.assign') }}">
                @csrf
                <input type="hidden" name="asset_id" id="assign_asset_id">

                <div class="as-grid">
                    <div class="is-full">
                        <label class="ui-label" for="assign_employee_id">Employee <span class="as-req">*</span></label>
                        <select name="employee_id" id="assign_employee_id" required class="ui-select">
                            <option value="">Choose an employee…</option>
                        </select>
                        <p class="as-hint">Search by name or employee number</p>
                    </div>

                    <div>
                        <label class="ui-label" for="assign_quantity">Quantity <span class="as-req">*</span></label>
                        <input type="number" name="quantity" id="assign_quantity" min="1" value="1" required class="ui-input">
                    </div>
                    <div>
                        <label class="ui-label">Condition <span class="as-req">*</span></label>
                        <select name="condition_when_assigned" required class="ui-select">
                            <option value="new">New</option>
                            <option value="good" selected>Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
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
                        <textarea name="assignment_notes" rows="2" class="ui-textarea"
                                  placeholder="Purpose or special instructions…"></textarea>
                    </div>
                </div>

                <div class="as-modal-actions">
                    <button type="button" onclick="closeAssignModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" id="assignSubmitBtn" class="btn-primary">Assign Asset</button>
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
    .finally(() => { btn.textContent = 'Assign Asset'; btn.disabled = false; });
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
