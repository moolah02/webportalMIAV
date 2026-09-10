@extends('layouts.app')
@section('title', 'Asset Category Fields')

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Settings</a>
<button type="button" class="btn-primary btn-sm" onclick="openAddModal()"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add Field</button>
@endsection

@push('styles')
<style>
/* Top-bar actions sit outside .mv-page, so give the primary button the portal accent here */
.mv-header-actions .btn-primary { background: var(--mv-accent) !important; border-color: var(--mv-accent) !important; color: #fff !important; }
.mv-header-actions .btn-primary:hover { background: var(--mv-accent-ink) !important; border-color: var(--mv-accent-ink) !important; }
.cf { display: grid; gap: 16px; }
.cf-toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.cf-toolbar label { font-size: 13px; font-weight: 500; color: var(--mv-ink-2); }
.cf-toolbar select { width: 280px; max-width: 100%; flex: 0 0 auto; height: 36px; min-width: 240px; padding: 0 30px 0 11px; font: inherit; font-size: 13.5px; color: var(--mv-ink); background-color: var(--mv-surface); border: 1px solid var(--mv-line-strong); border-radius: 8px; cursor: pointer; }
.cf-toolbar select:focus { outline: none; border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43,100,168,.15); }
.cf-toolbar p { margin: 0 0 0 auto; font-size: 13px; color: var(--mv-muted); }

.cf-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
.cf-card-head { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-bottom: 1px solid var(--mv-line); }
.cf-card-head h2, .cf-card-head h3 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.cf-count { font-size: 12px; font-weight: 500; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 6px; padding: 0 7px; }

.cf-setting { display: flex; align-items: center; gap: 14px; padding: 14px 16px; }
.cf-setting strong { display: block; font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
.cf-setting p { margin: 2px 0 0; font-size: 12.5px; color: var(--mv-muted); }
.toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; flex-shrink: 0; }
.toggle-switch input { position: absolute; inset: 0; opacity: 0; margin: 0; cursor: pointer; z-index: 1; }
.toggle-slider { position: absolute; inset: 0; border-radius: 11px; background: var(--mv-line-strong); transition: background .15s; }
.toggle-slider::before { content: ""; position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(22,32,44,.2); transition: transform .15s; }
.toggle-switch input:checked + .toggle-slider { background: var(--mv-accent); }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(16px); }

.category-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.category-table th { text-align: left; white-space: nowrap; }
.cf-num { color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.cf-key { font-family: var(--mv-mono); font-size: 12.5px; color: var(--mv-ink); }
.cf-label { color: var(--mv-ink); font-weight: 500; }
.cf-help { display: block; font-size: 12px; color: var(--mv-muted); margin-top: 2px; }
.options-list { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px; }
.option-tag { font-size: 11.5px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 0 6px; }
.field-type-badge { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 6px; }
.cf-muted { color: var(--mv-muted); }
.cf-actions { display: flex; gap: 6px; justify-content: flex-end; white-space: nowrap; }
.cf-btn { display: inline-flex; align-items: center; gap: 5px; height: 28px; padding: 0 10px; border-radius: 6px; border: 1px solid var(--mv-line-strong); background: var(--mv-surface); font: inherit; font-size: 12.5px; font-weight: 500; color: var(--mv-ink-2); cursor: pointer; }
.cf-btn .mv-i { width: 13px; height: 13px; }
.cf-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.cf-btn-danger { color: var(--mv-crit); border-color: #EBC3C3; }
.cf-btn-danger:hover { background: var(--mv-crit-soft); color: var(--mv-crit); }
.cf-empty { padding: 44px 16px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
.cf-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); display: block; margin: 0 auto 8px; }
.cf-empty h3 { margin: 0 0 4px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.cf-empty p { margin: 0; }

/* Modals (JS toggles .show on .modal) */
#addModal.modal, #editModal.modal { display: none; position: fixed; inset: 0; z-index: 1100; width: auto; height: auto; background: rgba(22,32,44,.45); align-items: center; justify-content: center; padding: 16px; overflow: auto; }
#addModal.modal.show, #editModal.modal.show { display: flex; }
.modal .modal-content { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 20px 48px rgba(22,32,44,.18); width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; padding: 0; display: block; }
.modal .modal-content > h3 { margin: 0; padding: 14px 20px; border-bottom: 1px solid var(--mv-line); font-size: 15px; font-weight: 600; color: var(--mv-ink); }
.modal .modal-content form { padding: 18px 20px 0; }
.modal .mb-4 { margin-bottom: 14px; }
.modal .form-label { display: block; margin-bottom: 5px; }
.modal .ui-input { width: 100%; height: 36px; padding: 0 11px; font-size: 13.5px; }
.modal small { display: block; margin-top: 4px; font-size: 12px; color: var(--mv-muted); }
.modal input[readonly].ui-input { background: var(--mv-surface-2) !important; color: var(--mv-muted); font-family: var(--mv-mono); }
.modal input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--mv-accent); }
.options-container { border: 1px solid var(--mv-line); border-radius: 8px; padding: 12px; margin-bottom: 14px; background: var(--mv-surface-2); }
.option-item { display: flex; gap: 8px; margin-bottom: 8px; align-items: center; }
.option-item input { flex: 1; }
.btn-remove-option { width: 32px; height: 32px; flex-shrink: 0; border-radius: 7px; border: 1px solid #EBC3C3; background: var(--mv-surface); color: var(--mv-crit); font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; }
.btn-remove-option:hover { background: var(--mv-crit-soft); }
.btn-add-option { display: inline-flex; align-items: center; gap: 6px; height: 30px; padding: 0 12px; margin-top: 2px; border-radius: 7px; border: 1px solid var(--mv-line-strong); background: var(--mv-surface); color: var(--mv-ink); font: inherit; font-size: 12.5px; font-weight: 500; cursor: pointer; }
.btn-add-option:hover { background: var(--mv-surface-2); }
.modal-buttons { display: flex; justify-content: flex-end; gap: 8px; margin: 18px -20px 0; padding: 12px 20px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); }
</style>
@endpush

@section('content')
<div class="cf">

  {{-- Category selector --}}
  <div class="cf-toolbar">
    <label for="categorySelector">Select Category:</label>
    <select id="categorySelector" onchange="changeCategory(this.value)">
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ $category->id == $cat->id ? 'selected' : '' }}>
          {{ $cat->name }}
        </option>
      @endforeach
    </select>
    <p>Define custom fields for each asset category</p>
  </div>

  @if($errors->any())
    <div class="alert alert-danger" style="padding:11px 14px;border:1px solid;">
      <ul style="margin: 0; padding-left: 18px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Category settings --}}
  <div class="cf-card">
    <div class="cf-card-head"><h3>{{ $category->name }} Settings</h3></div>
    <form method="POST" action="{{ route('settings.asset-category-fields.update-category', $category) }}">
      @csrf
      @method('PUT')
      <div class="cf-setting">
        <label class="toggle-switch">
          <input type="checkbox" name="requires_individual_entry" value="1" {{ $category->requires_individual_entry ? 'checked' : '' }}>
          <span class="toggle-slider"></span>
        </label>
        <div>
          <strong>Requires Individual Entry</strong>
          <p>Each item must be entered separately (stock quantity = 1). Enable for vehicles, IT equipment, etc.</p>
        </div>
        <button type="submit" class="btn-secondary btn-sm" style="margin-left:auto;white-space:nowrap;">Save Setting</button>
      </div>
    </form>
  </div>

  {{-- Fields --}}
  <div class="cf-card">
    <div class="cf-card-head">
      <h2 class="table-title">Custom Fields for "{{ $category->name }}"</h2>
      <span class="cf-count">{{ $fields->count() }}</span>
    </div>

    @if($fields->count() > 0)
    <div style="overflow-x:auto;">
      <table class="category-table ui-table">
        <thead>
          <tr>
            <th style="width: 40px;">#</th>
            <th>Field Name</th>
            <th>Label</th>
            <th>Type</th>
            <th>Required</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($fields as $index => $field)
            <tr>
              <td class="cf-num">{{ $index + 1 }}</td>
              <td><span class="cf-key">{{ $field->field_name }}</span></td>
              <td>
                <span class="cf-label">{{ $field->field_label }}</span>
                @if($field->help_text)
                  <span class="cf-help">{{ $field->help_text }}</span>
                @endif
                @if($field->field_type === 'select' && $field->options)
                  <div class="options-list">
                    @foreach($field->options as $option)
                      <span class="option-tag">{{ $option }}</span>
                    @endforeach
                  </div>
                @endif
              </td>
              <td><span class="field-type-badge">{{ ucfirst($field->field_type) }}</span></td>
              <td>
                @if($field->is_required)
                  <span class="badge badge-yellow">Required</span>
                @else
                  <span class="cf-muted">Optional</span>
                @endif
              </td>
              <td>
                <span class="badge {{ $field->is_active ? 'badge-green' : 'badge-gray' }}">
                  {{ $field->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <div class="cf-actions">
                  <button type="button" class="cf-btn"
                          onclick="openEditModal({{ json_encode($field) }})">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg> Edit
                  </button>
                  <button type="button" class="cf-btn cf-btn-danger"
                          onclick="deleteField({{ $field->id }}, '{{ $field->field_label }}')">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-trash"/></svg> Delete
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
      <div class="cf-empty">
        <svg class="mv-i" aria-hidden="true"><use href="#i-sliders"/></svg>
        <h3>No Custom Fields Defined</h3>
        <p>Add custom fields to capture category-specific information for "{{ $category->name }}" assets.</p>
      </div>
    @endif
  </div>
</div>

<!-- Add Field Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h3>Add Custom Field</h3>
    <form id="addForm" method="POST" action="{{ route('settings.asset-category-fields.store', $category) }}">
      @csrf
      <div class="mb-4">
        <label class="form-label">Field Name (snake_case) *</label>
        <input type="text" name="field_name" class="ui-input" required
               pattern="[a-z][a-z0-9_]*" placeholder="e.g., license_plate">
        <small>Use lowercase letters, numbers, and underscores only</small>
      </div>
      <div class="mb-4">
        <label class="form-label">Display Label *</label>
        <input type="text" name="field_label" class="ui-input" required placeholder="e.g., License Plate Number">
      </div>
      <div class="mb-4">
        <label class="form-label">Field Type *</label>
        <select name="field_type" id="add_field_type" class="ui-input" required onchange="toggleOptions('add')">
          <option value="text">Text</option>
          <option value="number">Number</option>
          <option value="date">Date</option>
          <option value="select">Dropdown (Select)</option>
          <option value="textarea">Text Area</option>
          <option value="email">Email</option>
          <option value="url">URL</option>
          <option value="tel">Phone Number</option>
        </select>
      </div>
      <div id="add_options_container" class="options-container" style="display: none;">
        <label class="form-label">Dropdown Options</label>
        <div id="add_options_list"></div>
        <button type="button" class="btn-add-option" onclick="addOption('add')"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add Option</button>
      </div>
      <div class="mb-4">
        <label class="form-label">Placeholder Text</label>
        <input type="text" name="placeholder_text" class="ui-input" placeholder="e.g., Enter license plate...">
      </div>
      <div class="mb-4">
        <label class="form-label">Help Text</label>
        <input type="text" name="help_text" class="ui-input" placeholder="e.g., Vehicle license plate number">
      </div>
      <div class="mb-4">
        <label class="form-label" style="display:flex;align-items:center;gap:8px;">
          <input type="checkbox" name="is_required" value="1">
          Required Field
        </label>
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button type="submit" class="btn-primary">Create Field</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Field Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <h3>Edit Custom Field</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-4">
        <label class="form-label">Field Name</label>
        <input type="text" id="edit_field_name" class="ui-input" readonly>
        <small>Field name cannot be changed</small>
      </div>
      <div class="mb-4">
        <label class="form-label">Display Label *</label>
        <input type="text" name="field_label" id="edit_field_label" class="ui-input" required>
      </div>
      <div class="mb-4">
        <label class="form-label">Field Type *</label>
        <select name="field_type" id="edit_field_type" class="ui-input" required onchange="toggleOptions('edit')">
          <option value="text">Text</option>
          <option value="number">Number</option>
          <option value="date">Date</option>
          <option value="select">Dropdown (Select)</option>
          <option value="textarea">Text Area</option>
          <option value="email">Email</option>
          <option value="url">URL</option>
          <option value="tel">Phone Number</option>
        </select>
      </div>
      <div id="edit_options_container" class="options-container" style="display: none;">
        <label class="form-label">Dropdown Options</label>
        <div id="edit_options_list"></div>
        <button type="button" class="btn-add-option" onclick="addOption('edit')"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add Option</button>
      </div>
      <div class="mb-4">
        <label class="form-label">Placeholder Text</label>
        <input type="text" name="placeholder_text" id="edit_placeholder_text" class="ui-input">
      </div>
      <div class="mb-4">
        <label class="form-label">Help Text</label>
        <input type="text" name="help_text" id="edit_help_text" class="ui-input">
      </div>
      <div class="mb-4">
        <label class="form-label" style="display:flex;align-items:center;gap:8px;">
          <input type="checkbox" name="is_required" id="edit_is_required" value="1">
          Required Field
        </label>
      </div>
      <div class="mb-4">
        <label class="form-label" style="display:flex;align-items:center;gap:8px;">
          <input type="checkbox" name="is_active" id="edit_is_active" value="1">
          Active
        </label>
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn-primary">Update Field</button>
      </div>
    </form>
  </div>
</div>

<script>
function changeCategory(categoryId) {
  window.location.href = '/settings/asset-categories/' + categoryId + '/fields';
}

function openAddModal() {
  document.getElementById('addModal').classList.add('show');
  document.getElementById('add_options_list').innerHTML = '';
  toggleOptions('add');
}

function closeAddModal() {
  document.getElementById('addModal').classList.remove('show');
  document.getElementById('addForm').reset();
}

function openEditModal(field) {
  document.getElementById('editForm').action = '/settings/asset-category-fields/' + field.id;
  document.getElementById('edit_field_name').value = field.field_name;
  document.getElementById('edit_field_label').value = field.field_label;
  document.getElementById('edit_field_type').value = field.field_type;
  document.getElementById('edit_placeholder_text').value = field.placeholder_text || '';
  document.getElementById('edit_help_text').value = field.help_text || '';
  document.getElementById('edit_is_required').checked = field.is_required;
  document.getElementById('edit_is_active').checked = field.is_active;

  // Load options for select fields
  const optionsList = document.getElementById('edit_options_list');
  optionsList.innerHTML = '';
  if (field.options && field.options.length > 0) {
    field.options.forEach(option => {
      addOption('edit', option);
    });
  }

  toggleOptions('edit');
  document.getElementById('editModal').classList.add('show');
}

function closeEditModal() {
  document.getElementById('editModal').classList.remove('show');
}

function toggleOptions(prefix) {
  const fieldType = document.getElementById(prefix + '_field_type').value;
  const container = document.getElementById(prefix + '_options_container');
  container.style.display = fieldType === 'select' ? 'block' : 'none';
}

function addOption(prefix, value = '') {
  const list = document.getElementById(prefix + '_options_list');
  const index = list.children.length;
  const div = document.createElement('div');
  div.className = 'option-item';
  div.innerHTML = `
    <input type="text" name="options[]" class="ui-input" value="${value}" placeholder="Option ${index + 1}" required>
    <button type="button" class="btn-remove-option" onclick="this.parentElement.remove()" title="Remove option">X</button>
  `;
  list.appendChild(div);
}

function deleteField(id, label) {
  if (confirm('Are you sure you want to delete the field "' + label + '"?')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/settings/asset-category-fields/' + id;
    form.innerHTML = `@csrf @method('DELETE')`;
    document.body.appendChild(form);
    form.submit();
  }
}

// Close modals when clicking outside
document.querySelectorAll('.modal').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.remove('show');
    }
  });
});
</script>

@endsection
