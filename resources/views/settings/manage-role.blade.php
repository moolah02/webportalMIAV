@extends('layouts.app')
@section('title', 'Manage Roles')

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Settings</a>
<button type="button" class="btn-primary btn-sm" onclick="openAddModal()"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add Role</button>
@endsection

@push('styles')
<style>
.sm-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
.sm-card-head { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-bottom: 1px solid var(--mv-line); }
.sm-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.sm-count { font-size: 12px; font-weight: 500; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 6px; padding: 0 7px; }
.sm-card .ui-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.sm-card .ui-table th { text-align: left; white-space: nowrap; }
.sm-key { font-family: var(--mv-mono); font-size: 12.5px; color: var(--mv-ink); }
.sm-name { color: var(--mv-ink) !important; font-weight: 500; }
.sm-desc { max-width: 280px; }
.sm-num { font-variant-numeric: tabular-nums; font-weight: 500; color: var(--mv-ink); }
.permission-tags { display: flex; flex-wrap: wrap; gap: 4px; max-width: 320px; }
.permission-tags .badge { font-size: 11.5px; }
.sm-actions { display: flex; gap: 6px; justify-content: flex-end; white-space: nowrap; }
.sm-btn { display: inline-flex; align-items: center; gap: 5px; height: 28px; padding: 0 10px; border-radius: 6px; border: 1px solid var(--mv-line-strong); background: var(--mv-surface); font: inherit; font-size: 12.5px; font-weight: 500; color: var(--mv-ink-2); cursor: pointer; }
.sm-btn .mv-i { width: 13px; height: 13px; }
.sm-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.sm-btn-danger { color: var(--mv-crit); border-color: #EBC3C3; }
.sm-btn-danger:hover { background: var(--mv-crit-soft); color: var(--mv-crit); }
.sm-empty { padding: 48px 16px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
.sm-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); margin-bottom: 8px; }
.sm-empty h3 { margin: 0 0 4px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.sm-empty p { margin: 0; }

/* Modals (JS toggles .show on .modal) */
#addModal.modal, #editModal.modal { display: none; position: fixed; inset: 0; z-index: 1100; width: auto; height: auto; background: rgba(22,32,44,.45); align-items: center; justify-content: center; padding: 16px; overflow: auto; }
#addModal.modal.show, #editModal.modal.show { display: flex; }
.modal .modal-content { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 20px 48px rgba(22,32,44,.18); width: 100%; max-width: 760px; max-height: 90vh; overflow-y: auto; padding: 0; display: block; }
.modal .modal-content > h3 { margin: 0; padding: 14px 20px; border-bottom: 1px solid var(--mv-line); font-size: 15px; font-weight: 600; color: var(--mv-ink); }
.modal .modal-content form { padding: 18px 20px 0; }
.modal .mb-4 { margin-bottom: 14px; }
.modal .form-label { display: block; margin-bottom: 5px; }
.modal .ui-input { width: 100%; height: 36px; padding: 0 11px; font-size: 13.5px; }
.modal textarea.ui-input { height: auto; padding: 8px 11px; }
.modal input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--mv-accent); vertical-align: -2px; }
.permissions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }
.permission-group { border: 1px solid var(--mv-line); border-radius: 8px; padding: 10px 12px; }
.permission-group h4 { margin: 0 0 6px; font-size: 12px; font-weight: 600; color: var(--mv-muted); letter-spacing: .03em; }
.permission-item { display: flex; align-items: center; gap: 8px; padding: 3px 0; }
.permission-item label { font-size: 13px; color: var(--mv-ink-2); cursor: pointer; }
.modal-buttons { display: flex; justify-content: flex-end; gap: 8px; margin: 18px -20px 0; padding: 12px 20px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); position: sticky; bottom: 0; }
</style>
@endpush

@section('content')

<div class="sm-card">
    <div class="sm-card-head">
        <h2 class="table-title">System Roles</h2>
        <span class="sm-count">{{ $roles->count() }}</span>
    </div>

    @if($roles->count() > 0)
    <div style="overflow-x:auto;">
      <table class="ui-table">
        <thead>
          <tr>
            <th>Role Name</th>
            <th>Display Name</th>
            <th>Description</th>
            <th>Permissions</th>
            <th>Status</th>
            <th>Employees</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($roles as $role)
            <tr>
              <td><span class="sm-key">{{ $role->name }}</span></td>
              <td class="sm-name">{{ $role->display_name ?: $role->name }}</td>
              <td class="sm-desc">{{ $role->description ?: 'No description' }}</td>
              <td>
                <div class="permission-tags">
                  @if(in_array('all', $role->permissions ?? []))
                    <span class="badge badge-blue">All Permissions</span>
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
                <span class="badge {{ $role->is_active ? 'badge-green' : 'badge-gray' }}">
                  {{ $role->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td><span class="sm-num">{{ $role->employees->count() }}</span></td>
              <td>
                <div class="sm-actions">
                  <button type="button" class="sm-btn"
                          onclick="openEditModal({{ $role->id }}, '{{ $role->name }}', '{{ $role->display_name }}', '{{ $role->description }}', {{ json_encode($role->permissions ?? []) }}, {{ $role->is_active ? 'true' : 'false' }})">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg> Edit
                  </button>
                  @if($role->employees->count() == 0)
                    <button type="button" class="sm-btn sm-btn-danger"
                            onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')">
                      <svg class="mv-i" aria-hidden="true"><use href="#i-trash"/></svg> Delete
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
      <div class="sm-empty">
        <svg class="mv-i" aria-hidden="true"><use href="#i-shield"/></svg>
        <h3>No Roles Found</h3>
        <p>Get started by creating your first user role.</p>
      </div>
    @endif
</div>

<!-- Add Role Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h3>Add New Role</h3>
    <form id="addForm" method="POST" action="{{ route('settings.roles.store') }}">
      @csrf
      <div class="mb-4">
        <label class="form-label">Role Name *</label>
        <input type="text" name="name" class="ui-input" required placeholder="e.g., field_technician">
      </div>
      <div class="mb-4">
        <label class="form-label">Display Name</label>
        <input type="text" name="display_name" class="ui-input" placeholder="e.g., Field Technician">
      </div>
      <div class="mb-4">
        <label class="form-label">Description</label>
        <textarea name="description" class="ui-input" rows="3" placeholder="Brief description of this role..."></textarea>
      </div>
      <div class="mb-4">
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
    <h3>Edit Role</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-4">
        <label class="form-label">Role Name *</label>
        <input type="text" name="name" id="edit_name" class="ui-input" required>
      </div>
      <div class="mb-4">
        <label class="form-label">Display Name</label>
        <input type="text" name="display_name" id="edit_display_name" class="ui-input">
      </div>
      <div class="mb-4">
        <label class="form-label">Description</label>
        <textarea name="description" id="edit_description" class="ui-input" rows="3"></textarea>
      </div>
      <div class="mb-4">
        <label class="form-label" style="display:flex;align-items:center;gap:8px;">
          <input type="checkbox" name="is_active" id="edit_is_active" value="1">
          Active
        </label>
      </div>
      <div class="mb-4">
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
