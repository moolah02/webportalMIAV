@extends('layouts.app')
@section('title', 'Manage Departments')

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary">← Settings</a>
<button class="btn-primary" onclick="showCreateModal()">+ Add Department</button>
@endsection

@section('content')

@if(session('success'))
<div class="flash-success"><span>✓</span> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-error"><span>✗</span> {{ session('error') }}</div>
@endif

<div class="ui-card overflow-hidden">
    @if($departments->count() > 0)
    <div class="overflow-x-auto">
        <table class="ui-table">
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
                    <td class="font-semibold text-gray-900">{{ $department->name }}</td>
                    <td>
                        @if($department->code)
                            <code class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-700">{{ $department->code }}</code>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="text-gray-600 max-w-xs">{{ $department->description ?? '—' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ $department->employees_count }} employees</span>
                    </td>
                    <td>
                        <span class="badge {{ $department->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex gap-1.5">
                            <button class="btn-secondary btn-sm" onclick='editDepartment(@json($department))'>Edit</button>
                            <form method="POST" action="{{ route('settings.departments.delete', $department) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm"
                                        onclick="return confirm('Delete this department?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">🏢</div>
        <div class="empty-state-msg">No departments found. Create your first department!</div>
    </div>
    @endif
</div>

{{-- Modal --}}
<div id="deptModal" class="ui-modal">
    <div class="ui-modal-box" style="max-width:560px">
        <div class="ui-modal-header">
            <span class="ui-modal-title" id="modalTitle">Add Department</span>
            <button class="ui-modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="deptForm" method="POST" action="{{ route('settings.departments.store') }}">
            @csrf
            <input type="hidden" id="dept_method" name="_method" value="POST">
            <input type="hidden" id="dept_id" name="dept_id">
            <div class="ui-modal-body" style="display:grid;gap:16px">
                <div>
                    <label class="ui-label">Department Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="dept_name" required class="ui-input">
                </div>
                <div>
                    <label class="ui-label">Department Code</label>
                    <input type="text" name="code" id="dept_code" placeholder="e.g. IT, HR, FIN" class="ui-input">
                </div>
                <div>
                    <label class="ui-label">Description</label>
                    <textarea name="description" id="dept_description" rows="3" class="ui-input" style="resize:vertical"></textarea>
                </div>
                <div id="statusField" style="display:none">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="dept_is_active" value="1" class="w-4 h-4">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            <div class="ui-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Department</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.ui-modal { display:none; position:fixed; inset:0; z-index:1000; background:rgba(0,0,0,.5); backdrop-filter:blur(4px); align-items:center; justify-content:center; }
.ui-modal.show { display:flex; }
.ui-modal-box { background:#fff; border-radius:12px; width:90%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.ui-modal-header { padding:20px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; }
.ui-modal-title { font-size:16px; font-weight:700; color:#111827; }
.ui-modal-close { background:none; border:none; font-size:22px; color:#6b7280; cursor:pointer; line-height:1; padding:0; }
.ui-modal-body { padding:24px; }
.ui-modal-footer { padding:16px 24px; border-top:1px solid #e5e7eb; display:flex; gap:10px; justify-content:flex-end; }
</style>
@endpush

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
    document.getElementById('deptModal').classList.add('show');
}
function editDepartment(department) {
    document.getElementById('modalTitle').innerText = 'Edit Department';
    document.getElementById('deptForm').action = '/settings/departments/' + department.id;
    document.getElementById('dept_method').value = 'PUT';
    document.getElementById('dept_name').value = department.name;
    document.getElementById('dept_code').value = department.code || '';
    document.getElementById('dept_description').value = department.description || '';
    document.getElementById('dept_is_active').checked = !!department.is_active;
    document.getElementById('statusField').style.display = 'block';
    document.getElementById('deptModal').classList.add('show');
}
function closeModal() {
    document.getElementById('deptModal').classList.remove('show');
}
document.getElementById('deptModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
