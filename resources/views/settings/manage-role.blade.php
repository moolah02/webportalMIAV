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
  
  .roles-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  .roles-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
  }
  
  .roles-table td {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
  }
  
  .roles-table tr:hover {
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
  
  .permission-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
  }
  
  .permission-tag {
    background: #e9ecef;
    color: #495057;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
  }
  
  .permission-tag.all {
    background: #fff3cd;
    color: #856404;
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
  }
  
  .form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }
  
  .permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 15px;
  }
  
  .permission-group {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
  }
  
  .permission-group h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
    color: #495057;
  }
  
  .permission-item {
    display: flex;
    align-items: center;
    margin-block-end: 8px;
  }
  
  .permission-item input {
    margin-right: 8px;
  }
  
  .permission-item label {
    font-size: 13px;
    color: #495057;
    cursor: pointer;
    margin: 0;
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
</style>

<div class="container-fluid">
  <!-- Breadcrumb -->
  <div style="background: #fff; padding: 20px; border-radius: 12px; margin-block-end: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <nav style="font-size: 14px; color: #666;">
      <a href="{{ route('dashboard') }}" style="color: #667eea; text-decoration: none;">🏠 Dashboard</a>
      <span style="margin: 0 8px;">›</span>
      <a href="{{ route('settings.index') }}" style="color: #667eea; text-decoration: none;">⚙️ Settings</a>
      <span style="margin: 0 8px;">›</span>
      <span>Role Management</span>
    </nav>
    <h1 style="margin: 10px 0 0 0; color: #2c3e50; font-weight: 700;">👥 Role Management</h1>
  </div>

  <!-- Alerts -->
  @if(session('success'))
    <div class="alert alert-success">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">
      ❌ {{ session('error') }}
    </div>
  @endif

  <!-- Roles Table -->
  <div class="table-container">
    <div class="table-header">
      <h2 class="table-title">System Roles</h2>
      <button type="button" class="btn-primary" onclick="openAddModal()">
        ➕ Add Role
      </button>
    </div>

    @if($roles->count() > 0)
      <table class="roles-table">
        <thead>
          <tr>
            <th>Role Name</th>
            <th>Display Name</th>
            <th>Description</th>
            <th>Permissions</th>
            <th>Status</th>
            <th>Employees</th>
            <th style="width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($roles as $role)
            <tr>
              <td>
                <strong>{{ $role->name }}</strong>
              </td>
              <td>
                {{ $role->display_name ?: $role->name }}
              </td>
              <td>
                {{ $role->description ?: 'No description' }}
              </td>
              <td>
                <div class="permission-tags">
                  @if(in_array('all', $role->permissions ?? []))
                    <span class="permission-tag all">All Permissions</span>
                  @else
                    @foreach(array_slice($role->permissions ?? [], 0, 3) as $permission)
                      <span class="permission-tag">{{ str_replace('_', ' ', ucwords($permission)) }}</span>
                    @endforeach
                    @if(count($role->permissions ?? []) > 3)
                      <span class="permission-tag">+{{ count($role->permissions) - 3 }} more</span>
                    @endif
                  @endif
                </div>
              </td>
              <td>
                <span class="status-badge {{ $role->is_active ? 'status-active' : 'status-inactive' }}">
                  {{ $role->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <strong>{{ $role->employees->count() }}</strong>
              </td>
              <td>
                <button type="button" class="btn-sm btn-outline-primary" 
                        onclick="openEditModal({{ $role->id }}, '{{ $role->name }}', '{{ $role->display_name }}', '{{ $role->description }}', {{ json_encode($role->permissions ?? []) }}, {{ $role->is_active ? 'true' : 'false' }})">
                  ✏️ Edit
                </button>
                @if($role->employees->count() == 0)
                  <button type="button" class="btn-sm btn-outline-danger" 
                          onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')">
                    🗑️ Delete
                  </button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div style="text-align: center; padding: 60px; color: #666;">
        <div style="font-size: 48px; margin-block-end: 20px;">👥</div>
        <h3>No Roles Found</h3>
        <p>Get started by creating your first user role.</p>
      </div>
    @endif
  </div>
</div>

<!-- Add Role Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h3 style="margin-top: 0;">Add New Role</h3>
    <form id="addForm" method="POST" action="{{ route('settings.roles.store') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Role Name *</label>
        <input type="text" name="name" class="form-control" required placeholder="e.g., field_technician">
      </div>
      <div class="form-group">
        <label class="form-label">Display Name</label>
        <input type="text" name="display_name" class="form-control" placeholder="e.g., Field Technician">
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this role..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Permissions</label>
        <div class="permissions-grid">
          @foreach($availablePermissions as $group => $permissions)
            <div class="permission-group">
              <h4>{{ $group }}</h4>
              @foreach($permissions as $key => $label)
                <div class="permission-item">
                  <input type="checkbox" name="permissions[]" value="{{ $key }}" id="add_perm_{{ $key }}">
                  <label for="add_perm_{{ $key }}">{{ $label }}</label>
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button type="submit" class="btn-primary">Create Role</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Role Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <h3 style="margin-top: 0;">Edit Role</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="form-group">
        <label class="form-label">Role Name *</label>
        <input type="text" name="name" id="edit_name" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Display Name</label>
        <input type="text" name="display_name" id="edit_display_name" class="form-control">
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">
          <input type="checkbox" name="is_active" id="edit_is_active" value="1" style="margin-right: 8px;">
          Active
        </label>
      </div>
      <div class="form-group">
        <label class="form-label">Permissions</label>
        <div class="permissions-grid">
          @foreach($availablePermissions as $group => $permissions)
            <div class="permission-group">
              <h4>{{ $group }}</h4>
              @foreach($permissions as $key => $label)
                <div class="permission-item">
                  <input type="checkbox" name="permissions[]" value="{{ $key }}" id="edit_perm_{{ $key }}">
                  <label for="edit_perm_{{ $key }}">{{ $label }}</label>
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn-primary">Update Role</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddModal() {
  document.getElementById('addModal').classList.add('show');
}

function closeAddModal() {
  document.getElementById('addModal').classList.remove('show');
  document.getElementById('addForm').reset();
  // Uncheck all permissions
  document.querySelectorAll('#addModal input[type="checkbox"]').forEach(cb => cb.checked = false);
}

function openEditModal(id, name, displayName, description, permissions, isActive) {
  document.getElementById('editForm').action = `/settings/roles/${id}`;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_display_name').value = displayName || '';
  document.getElementById('edit_description').value = description || '';
  document.getElementById('edit_is_active').checked = isActive;
  
  // Clear all permissions first
  document.querySelectorAll('#editModal input[name="permissions[]"]').forEach(cb => cb.checked = false);
  
  // Check the permissions this role has
  permissions.forEach(permission => {
    const checkbox = document.getElementById(`edit_perm_${permission}`);
    if (checkbox) {
      checkbox.checked = true;
    }
  });
  
  document.getElementById('editModal').classList.add('show');
}

function closeEditModal() {
  document.getElementById('editModal').classList.remove('show');
}

function deleteRole(id, name) {
  if (confirm(`Are you sure you want to delete the role "${name}"?\n\nThis action cannot be undone.`)) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/settings/roles/${id}`;
    form.innerHTML = `
      @csrf
      @method('DELETE')
    `;
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

// Handle "All Permissions" checkbox
document.addEventListener('change', function(e) {
  if (e.target.value === 'all' && e.target.type === 'checkbox') {
    const modal = e.target.closest('.modal');
    const allCheckboxes = modal.querySelectorAll('input[name="permissions[]"]');
    
    if (e.target.checked) {
      // If "all" is checked, uncheck others
      allCheckboxes.forEach(cb => {
        if (cb.value !== 'all') {
          cb.checked = false;
        }
      });
    }
  } else if (e.target.name === 'permissions[]' && e.target.value !== 'all') {
    // If any other permission is checked, uncheck "all"
    const modal = e.target.closest('.modal');
    const allCheckbox = modal.querySelector('input[value="all"]');
    if (allCheckbox && e.target.checked) {
      allCheckbox.checked = false;
    }
  }
});
</script>

@endsection