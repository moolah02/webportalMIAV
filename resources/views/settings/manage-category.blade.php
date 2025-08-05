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
  
  .color-preview {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    display: inline-block;
    border: 2px solid #dee2e6;
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
    max-width: 500px;
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
</style>

<div class="container-fluid">
  <!-- Breadcrumb -->
  <div style="background: #fff; padding: 20px; border-radius: 12px; margin-block-end: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <nav style="font-size: 14px; color: #666;">
      <a href="{{ route('dashboard') }}" style="color: #667eea; text-decoration: none;">🏠 Dashboard</a>
      <span style="margin: 0 8px;">›</span>
      <a href="{{ route('settings.index') }}" style="color: #667eea; text-decoration: none;">⚙️ Settings</a>
      <span style="margin: 0 8px;">›</span>
      <span>{{ $typeLabel }}</span>
    </nav>
    <h1 style="margin: 10px 0 0 0; color: #2c3e50; font-weight: 700;">{{ $typeLabel }}</h1>
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

  <!-- Categories Table -->
  <div class="table-container">
    <div class="table-header">
      <h2 class="table-title">Manage {{ $typeLabel }}</h2>
      <button type="button" class="btn-primary" onclick="openAddModal()">
        ➕ Add {{ str_replace(['Categories', 'Status', 'Types'], ['Category', 'Status', 'Type'], $typeLabel) }}
      </button>
    </div>

    @if($categories->count() > 0)
      <table class="category-table">
        <thead>
          <tr>
            <th style="width: 30px;">📱</th>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Color</th>
            <th style="width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody id="sortable-categories">
          @foreach($categories as $category)
            <tr data-id="{{ $category->id }}">
              <td style="text-align: center;">
                {{ $category->icon ?: '📄' }}
              </td>
              <td>
                <strong>{{ $category->name }}</strong>
              </td>
              <td>
                {{ $category->description ?: 'No description' }}
              </td>
              <td>
                <span class="status-badge {{ $category->is_active ? 'status-active' : 'status-inactive' }}">
                  {{ $category->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                @if($category->color)
                  <span class="color-preview" style="background-color: {{ $category->color }};"></span>
                @else
                  <span style="color: #999;">None</span>
                @endif
              </td>
              <td>
                <button type="button" class="btn-sm btn-outline-primary" 
                        onclick="openEditModal({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', '{{ $category->color }}', '{{ $category->icon }}', {{ $category->is_active ? 'true' : 'false' }})">
                  ✏️ Edit
                </button>
                <button type="button" class="btn-sm btn-outline-danger" 
                        onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')">
                  🗑️ Delete
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div style="text-align: center; padding: 60px; color: #666;">
        <div style="font-size: 48px; margin-block-end: 20px;">📋</div>
        <h3>No {{ $typeLabel }} Found</h3>
        <p>Get started by adding your first {{ strtolower(str_replace(['Categories', 'Status', 'Types'], ['category', 'status', 'type'], $typeLabel)) }}.</p>
      </div>
    @endif
  </div>
</div>

<!-- Add Category Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h3 style="margin-top: 0;">Add {{ str_replace(['Categories', 'Status', 'Types'], ['Category', 'Status', 'Type'], $typeLabel) }}</h3>
    <form id="addForm" method="POST" action="{{ route('settings.category.store', $type) }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Color</label>
        <input type="color" name="color" class="form-control" style="height: 50px;">
      </div>
      <div class="form-group">
        <label class="form-label">Icon (Emoji)</label>
        <input type="text" name="icon" class="form-control" placeholder="📄">
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
    <h3 style="margin-top: 0;">Edit {{ str_replace(['Categories', 'Status', 'Types'], ['Category', 'Status', 'Type'], $typeLabel) }}</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="form-group">
        <label class="form-label">Name *</label>
        <input type="text" name="name" id="edit_name" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Color</label>
        <input type="color" name="color" id="edit_color" class="form-control" style="height: 50px;">
      </div>
      <div class="form-group">
        <label class="form-label">Icon (Emoji)</label>
        <input type="text" name="icon" id="edit_icon" class="form-control" placeholder="📄">
      </div>
      <div class="form-group">
        <label class="form-label">
          <input type="checkbox" name="is_active" id="edit_is_active" value="1" style="margin-right: 8px;">
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