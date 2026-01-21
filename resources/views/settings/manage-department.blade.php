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

  .dept-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  .dept-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
  }

  .dept-table td {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
  }

  .dept-table tr:hover {
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

  .employee-count {
    background: #e7f1ff;
    color: #0c5460;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }
</style>

<div class="container-fluid">
  <!-- Back Button -->
  <div style="margin-block-end: 20px;">
    <a href="{{ route('settings.index') }}" style="text-decoration: none; color: #667eea; font-weight: 600;">
      ← Back to Settings
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="table-container">
    <div class="table-header">
      <h2 class="table-title">🏢 Department Management</h2>
      <button class="btn-primary" onclick="showCreateModal()">
        + Add Department
      </button>
    </div>

    @if($departments->count() > 0)
      <table class="dept-table">
        <thead>
          <tr>
            <th>Department Name</th>
            <th>Code</th>
            <th>Description</th>
            <th>Employees</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($departments as $department)
            <tr>
              <td style="font-weight: 600;">{{ $department->name }}</td>
              <td>
                @if($department->code)
                  <code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">{{ $department->code }}</code>
                @else
                  <span style="color: #999;">N/A</span>
                @endif
              </td>
              <td style="max-width: 300px;">{{ $department->description ?? '-' }}</td>
              <td>
                <span class="employee-count">{{ $department->employees_count }} employees</span>
              </td>
              <td>
                <span class="status-badge {{ $department->is_active ? 'status-active' : 'status-inactive' }}">
                  {{ $department->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <button class="btn-sm btn-outline-primary" onclick='editDepartment(@json($department))'>
                  Edit
                </button>
                <form method="POST" action="{{ route('settings.departments.delete', $department) }}" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this department?')">
                    Delete
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div style="text-align: center; padding: 50px; color: #999;">
        <p style="font-size: 18px;">No departments found. Create your first department!</p>
      </div>
    @endif
  </div>
</div>

<!-- Create/Edit Modal -->
<div id="deptModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto;">
  <div style="background: white; max-width: 600px; margin: 50px auto; border-radius: 12px; padding: 30px;">
    <h3 style="margin-top: 0;" id="modalTitle">Add Department</h3>

    <form id="deptForm" method="POST" action="{{ route('settings.departments.store') }}">
      @csrf
      <input type="hidden" id="dept_method" name="_method" value="POST">
      <input type="hidden" id="dept_id" name="dept_id">

      <div style="margin-block-end: 20px;">
        <label style="display: block; margin-block-end: 5px; font-weight: 600;">Department Name *</label>
        <input type="text" name="name" id="dept_name" required
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
      </div>

      <div style="margin-block-end: 20px;">
        <label style="display: block; margin-block-end: 5px; font-weight: 600;">Department Code</label>
        <input type="text" name="code" id="dept_code"
               placeholder="e.g., IT, HR, FIN"
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
      </div>

      <div style="margin-block-end: 20px;">
        <label style="display: block; margin-block-end: 5px; font-weight: 600;">Description</label>
        <textarea name="description" id="dept_description" rows="3"
                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
      </div>

      <div style="margin-block-end: 20px;" id="statusField" style="display: none;">
        <label style="display: block; margin-block-end: 5px; font-weight: 600;">
          <input type="checkbox" name="is_active" id="dept_is_active" value="1">
          Active
        </label>
      </div>

      <div style="display: flex; gap: 10px; justify-content: flex-end;">
        <button type="button" onclick="closeModal()"
                style="padding: 10px 20px; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer;">
          Cancel
        </button>
        <button type="submit" class="btn-primary">
          Save Department
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function showCreateModal() {
  document.getElementById('modalTitle').innerText = 'Add Department';
  document.getElementById('deptForm').action = '{{ route('settings.departments.store') }}';
  document.getElementById('dept_method').value = 'POST';
  document.getElementById('dept_name').value = '';
  document.getElementById('dept_code').value = '';
  document.getElementById('dept_description').value = '';
  document.getElementById('dept_is_active').checked = true;
  document.getElementById('statusField').style.display = 'none';
  document.getElementById('deptModal').style.display = 'block';
}

function editDepartment(department) {
  document.getElementById('modalTitle').innerText = 'Edit Department';
  document.getElementById('deptForm').action = '/settings/departments/' + department.id;
  document.getElementById('dept_method').value = 'PUT';
  document.getElementById('dept_name').value = department.name;
  document.getElementById('dept_code').value = department.code || '';
  document.getElementById('dept_description').value = department.description || '';
  document.getElementById('dept_is_active').checked = department.is_active;
  document.getElementById('statusField').style.display = 'block';
  document.getElementById('deptModal').style.display = 'block';
}

function closeModal() {
  document.getElementById('deptModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('deptModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeModal();
  }
});
</script>
@endsection
