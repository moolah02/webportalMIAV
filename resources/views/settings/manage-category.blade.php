@extends('layouts.app')
@section('title', 'Manage Categories')

@php
  $singular = str_replace(['Categories', 'Status', 'Types'], ['Category', 'Status', 'Type'], $typeLabel);
@endphp

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Settings</a>
<button type="button" class="btn-primary btn-sm" onclick="openAddModal()"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add {{ $singular }}</button>
@endsection

@push('styles')
<style>
.sm-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
.sm-card-head { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-bottom: 1px solid var(--mv-line); }
.sm-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.sm-count { font-size: 12px; font-weight: 500; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 6px; padding: 0 7px; }
.category-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.category-table th { text-align: left; white-space: nowrap; }
.sm-name { color: var(--mv-ink) !important; font-weight: 500; }
.sm-desc { max-width: 420px; }
.sm-muted { color: var(--mv-muted); }
.color-preview { width: 16px; height: 16px; border-radius: 4px; display: inline-block; border: 1px solid var(--mv-line-strong); vertical-align: -3px; }
.sm-hex { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-muted); margin-left: 6px; }
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
.modal .modal-content { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 20px 48px rgba(22,32,44,.18); width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto; padding: 0; display: block; }
.modal .modal-content > h3 { margin: 0; padding: 14px 20px; border-bottom: 1px solid var(--mv-line); font-size: 15px; font-weight: 600; color: var(--mv-ink); }
.modal .modal-content form { padding: 18px 20px 0; }
.modal .mb-4 { margin-bottom: 14px; }
.modal .form-label { display: block; margin-bottom: 5px; }
.modal .ui-input { width: 100%; height: 36px; padding: 0 11px; font-size: 13.5px; }
.modal textarea.ui-input { height: auto; padding: 8px 11px; }
.modal input[type="color"].ui-input { width: 64px; padding: 3px; cursor: pointer; }
.modal input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--mv-accent); }
.modal-buttons { display: flex; justify-content: flex-end; gap: 8px; margin: 18px -20px 0; padding: 12px 20px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); }
</style>
@endpush

@section('content')

<div class="sm-card">
    <div class="sm-card-head">
        <h2 class="table-title">Manage {{ $typeLabel }}</h2>
        <span class="sm-count">{{ $categories->count() }}</span>
    </div>

    @if($categories->count() > 0)
    <div style="overflow-x:auto;">
      <table class="category-table ui-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Color</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody id="sortable-categories">
          @foreach($categories as $category)
            <tr data-id="{{ $category->id }}">
              <td class="sm-name">{{ $category->name }}</td>
              <td class="sm-desc">{{ $category->description ?: 'No description' }}</td>
              <td>
                <span class="badge {{ $category->is_active ? 'badge-green' : 'badge-gray' }}">
                  {{ $category->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                @if($category->color)
                  <span class="color-preview" style="background-color: {{ $category->color }};"></span><span class="sm-hex">{{ $category->color }}</span>
                @else
                  <span class="sm-muted">None</span>
                @endif
              </td>
              <td>
                <div class="sm-actions">
                  <button type="button" class="sm-btn"
                          onclick="openEditModal({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', '{{ $category->color }}', '{{ $category->icon }}', {{ $category->is_active ? 'true' : 'false' }})">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg> Edit
                  </button>
                  <button type="button" class="sm-btn sm-btn-danger"
                          onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')">
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
      <div class="sm-empty">
        <svg class="mv-i" aria-hidden="true"><use href="#i-list"/></svg>
        <h3>No {{ $typeLabel }} Found</h3>
        <p>Get started by adding your first {{ strtolower(str_replace(['Categories', 'Status', 'Types'], ['category', 'status', 'type'], $typeLabel)) }}.</p>
      </div>
    @endif
</div>

<!-- Add Category Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h3>Add {{ $singular }}</h3>
    <form id="addForm" method="POST" action="{{ route('settings.category.store', $type) }}">
      @csrf
      <div class="mb-4">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="ui-input" required>
      </div>
      <div class="mb-4">
        <label class="form-label">Description</label>
        <textarea name="description" class="ui-input" rows="3"></textarea>
      </div>
      <div class="mb-4">
        <label class="form-label">Color</label>
        <input type="color" name="color" class="ui-input">
      </div>
      <div class="mb-4">
        <label class="form-label">Icon</label>
        <input type="text" name="icon" class="ui-input" placeholder="">
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button type="submit" class="btn-primary">Create</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Category Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <h3>Edit {{ $singular }}</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-4">
        <label class="form-label">Name *</label>
        <input type="text" name="name" id="edit_name" class="ui-input" required>
      </div>
      <div class="mb-4">
        <label class="form-label">Description</label>
        <textarea name="description" id="edit_description" class="ui-input" rows="3"></textarea>
      </div>
      <div class="mb-4">
        <label class="form-label">Color</label>
        <input type="color" name="color" id="edit_color" class="ui-input">
      </div>
      <div class="mb-4">
        <label class="form-label">Icon</label>
        <input type="text" name="icon" id="edit_icon" class="ui-input" placeholder="">
      </div>
      <div class="mb-4">
        <label class="form-label" style="display:flex;align-items:center;gap:8px;">
          <input type="checkbox" name="is_active" id="edit_is_active" value="1">
          Active
        </label>
      </div>
      <div class="modal-buttons">
        <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn-primary">Update</button>
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
}

function openEditModal(id, name, description, color, icon, isActive) {
  document.getElementById('editForm').action = `/settings/categories/${id}`;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_description').value = description || '';
  document.getElementById('edit_color').value = color || '#007bff';
  document.getElementById('edit_icon').value = icon || '';
  document.getElementById('edit_is_active').checked = isActive;
  document.getElementById('editModal').classList.add('show');
}

function closeEditModal() {
  document.getElementById('editModal').classList.remove('show');
}

function deleteCategory(id, name) {
  if (confirm(`Are you sure you want to delete "${name}"?`)) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/settings/categories/${id}`;
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
</script>

@endsection
