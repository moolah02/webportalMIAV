@extends('layouts.app')

@section('content')
<style>
  .table-container {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
  }

  .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-block-end: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f3f4;
  }

  .table-title {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
  }

  .category-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  .category-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
  }

  .category-table td {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
  }

  .category-table tr:hover {
    background: #f8f9fa;
  }

  .status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
  }

  .status-active {
    background: #d4edda;
    color: #155724;
  }

  .status-inactive {
    background: #f8d7da;
    color: #721c24;
  }

  .btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    margin-right: 5px;
    transition: all 0.3s ease;
  }

  .btn-outline-primary {
    border: 1px solid #667eea;
    color: #667eea;
    background: transparent;
  }

  .btn-outline-primary:hover {
    background: #667eea;
    color: white;
  }

  .btn-outline-danger {
    border: 1px solid #dc3545;
    color: #dc3545;
    background: transparent;
  }

  .btn-outline-danger:hover {
    background: #dc3545;
    color: white;
  }

  .alert {
    padding: 15px;
    border-radius: 8px;
    margin-block-end: 20px;
    border: none;
  }

  .alert-success {
    background: #d4edda;
    color: #155724;
  }

  .alert-danger {
    background: #f8d7da;
    color: #721c24;
  }

  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
  }

  .modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: white;
    border-radius: 12px;
    padding: 30px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
  }

  .form-group {
    margin-block-end: 20px;
  }

  .form-label {
    display: block;
    margin-block-end: 8px;
    font-weight: 600;
    color: #495057;
  }

  .form-control {
    width: 100%;
    padding: 12px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
  }

  .form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .modal-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
  }

  .btn-secondary {
    background: #6c757d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
  }

  .btn-secondary:hover {
    background: #5a6268;
  }

  .category-selector {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-block-end: 25px;
  }

  .category-selector select {
    padding: 12px 20px;
    font-size: 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    min-width: 250px;
    cursor: pointer;
  }

  .field-type-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    background: #e9ecef;
    color: #495057;
  }

  .required-badge {
    background: #fff3cd;
    color: #856404;
  }

  .options-list {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 5px;
  }

  .option-tag {
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
  }

  .options-container {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
  }

  .option-item {
    display: flex;
    gap: 10px;
    margin-block-end: 10px;
    align-items: center;
  }

  .option-item input {
    flex: 1;
  }

  .btn-remove-option {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
  }

  .btn-add-option {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
  }

  .category-settings {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-block-end: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }

  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 26px;
  }

  .toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  input:checked + .toggle-slider {
    background-color: #667eea;
  }

  input:checked + .toggle-slider:before {
    transform: translateX(24px);
  }
</style>

<div class="container-fluid">
  <!-- Breadcrumb -->
  <div style="background: #fff; padding: 20px; border-radius: 12px; margin-block-end: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <nav style="font-size: 14px; color: #666;">
      <a href="{{ route('dashboard') }}" style="color: #667eea; text-decoration: none;">Dashboard</a>
      <span style="margin: 0 8px;">></span>
      <a href="{{ route('settings.index') }}" style="color: #667eea; text-decoration: none;">Settings</a>
      <span style="margin: 0 8px;">></span>
      <span>Category Custom Fields</span>
    </nav>
    <h1 style="margin: 10px 0 0 0; color: #2c3e50; font-weight: 700;">Category Custom Fields</h1>
    <p style="color: #666; margin-top: 5px;">Define custom fields for each asset category</p>
  </div>

  <!-- Category Selector -->
  <div class="category-selector">
    <label style="font-weight: 600; margin-right: 15px;">Select Category:</label>
    <select id="categorySelector" onchange="changeCategory(this.value)">
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ $category->id == $cat->id ? 'selected' : '' }}>
          {{ $cat->name }}
        </option>
      @endforeach
    </select>
  </div>

  <!-- Alerts -->
  @if(session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul style="margin: 0; padding-left: 20px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Category Settings -->
  <div class="category-settings">
    <h3 style="margin-top: 0; margin-block-end: 20px;">{{ $category->name }} Settings</h3>
    <form method="POST" action="{{ route('settings.asset-category-fields.update-category', $category) }}">
      @csrf
      @method('PUT')
      <div style="display: flex; align-items: center; gap: 15px;">
        <label class="toggle-switch">
          <input type="checkbox" name="requires_individual_entry" value="1" {{ $category->requires_individual_entry ? 'checked' : '' }}>
          <span class="toggle-slider"></span>
        </label>
        <div>
          <strong>Requires Individual Entry</strong>
          <p style="margin: 0; color: #666; font-size: 13px;">
            Each item must be entered separately (stock quantity = 1). Enable for vehicles, IT equipment, etc.
          </p>
        </div>
        <button type="submit" class="btn-sm btn-outline-primary" style="margin-left: auto;">Save Setting</button>
      </div>
    </form>
  </div>

  <!-- Fields Table -->
  <div class="table-container">
    <div class="table-header">
      <h2 class="table-title">Custom Fields for "{{ $category->name }}"</h2>
      <button type="button" class="btn-primary" onclick="openAddModal()">
        + Add Field
      </button>
    </div>

    @if($fields->count() > 0)
      <table class="category-table">
        <thead>
          <tr>
            <th style="width: 30px;">#</th>
            <th>Field Name</th>
            <th>Label</th>
            <th>Type</th>
            <th>Required</th>
            <th>Status</th>
            <th style="width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($fields as $index => $field)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td><code>{{ $field->field_name }}</code></td>
              <td>
                <strong>{{ $field->field_label }}</strong>
                @if($field->help_text)
                  <br><small style="color: #666;">{{ $field->help_text }}</small>
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
                  <span class="status-badge required-badge">Required</span>
                @else
                  <span style="color: #999;">Optional</span>
                @endif
              </td>
              <td>
                <span class="status-badge {{ $field->is_active ? 'status-active' : 'status-inactive' }}">
                  {{ $field->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <button type="button" class="btn-sm btn-outline-primary"
                        onclick="openEditModal({{ json_encode($field) }})">
                  Edit
                </button>
                <button type="button" class="btn-sm btn-outline-danger"
                        onclick="deleteField({{ $field->id }}, '{{ $field->field_label }}')">
                  Delete
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div style="text-align: center; padding: 60px; color: #666;">
        <div style="font-size: 48px; margin-block-end: 20px;">+</div>
        <h3>No Custom Fields Defined</h3>
        <p>Add custom fields to capture category-specific information for "{{ $category->name }}" assets.</p>
      </div>
    @endif
  </div>
</div>

<!-- Add Field Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h3 style="margin-top: 0;">Add Custom Field</h3>
    <form id="addForm" method="POST" action="{{ route('settings.asset-category-fields.store', $category) }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Field Name (snake_case) *</label>
        <input type="text" name="field_name" class="form-control" required
               pattern="[a-z][a-z0-9_]*" placeholder="e.g., license_plate">
        <small style="color: #666;">Use lowercase letters, numbers, and underscores only</small>
      </div>
      <div class="form-group">
        <label class="form-label">Display Label *</label>
        <input type="text" name="field_label" class="form-control" required placeholder="e.g., License Plate Number">
      </div>
      <div class="form-group">
        <label class="form-label">Field Type *</label>
        <select name="field_type" id="add_field_type" class="form-control" required onchange="toggleOptions('add')">
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
        <button type="button" class="btn-add-option" onclick="addOption('add')">+ Add Option</button>
      </div>
      <div class="form-group">
        <label class="form-label">Placeholder Text</label>
        <input type="text" name="placeholder_text" class="form-control" placeholder="e.g., Enter license plate...">
      </div>
      <div class="form-group">
        <label class="form-label">Help Text</label>
        <input type="text" name="help_text" class="form-control" placeholder="e.g., Vehicle license plate number">
      </div>
      <div class="form-group">
        <label class="form-label">
          <input type="checkbox" name="is_required" value="1" style="margin-right: 8px;">
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
    <h3 style="margin-top: 0;">Edit Custom Field</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="form-group">
        <label class="form-label">Field Name</label>
        <input type="text" id="edit_field_name" class="form-control" readonly style="background: #f5f5f5;">
        <small style="color: #666;">Field name cannot be changed</small>
      </div>
      <div class="form-group">
        <label class="form-label">Display Label *</label>
        <input type="text" name="field_label" id="edit_field_label" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Field Type *</label>
        <select name="field_type" id="edit_field_type" class="form-control" required onchange="toggleOptions('edit')">
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
        <button type="button" class="btn-add-option" onclick="addOption('edit')">+ Add Option</button>
      </div>
      <div class="form-group">
        <label class="form-label">Placeholder Text</label>
        <input type="text" name="placeholder_text" id="edit_placeholder_text" class="form-control">
      </div>
      <div class="form-group">
        <label class="form-label">Help Text</label>
        <input type="text" name="help_text" id="edit_help_text" class="form-control">
      </div>
      <div class="form-group">
        <label class="form-label">
          <input type="checkbox" name="is_required" id="edit_is_required" value="1" style="margin-right: 8px;">
          Required Field
        </label>
      </div>
      <div class="form-group">
        <label class="form-label">
          <input type="checkbox" name="is_active" id="edit_is_active" value="1" style="margin-right: 8px;">
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
    <input type="text" name="options[]" class="form-control" value="${value}" placeholder="Option ${index + 1}" required>
    <button type="button" class="btn-remove-option" onclick="this.parentElement.remove()">X</button>
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
