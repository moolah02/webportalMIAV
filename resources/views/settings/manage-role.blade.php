@extends('layouts.app')
@section('title', 'Manage Roles')

@section('content')

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
      <table class="ui-table">
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
                      <span class="badge badge-gray">{{ str_replace('_', ' ', ucwords($permission)) }}</span>
                    @endforeach
                    @if(count($role->permissions ?? []) > 3)
                      <span class="badge badge-gray">+{{ count($role->permissions) - 3 }} more</span>
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