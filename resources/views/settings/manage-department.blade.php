@extends('layouts.app')
@section('title', 'Manage Departments')

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Settings</a>
<button class="btn-primary btn-sm" onclick="showCreateModal()"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add Department</button>
@endsection

@section('content')

<div class="sm-card">
    @if($departments->count() > 0)
    <div style="overflow-x:auto;">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Department Name</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Employees</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                <tr>
                    <td class="sm-name">{{ $department->name }}</td>
                    <td>
                        @if($department->code)
                            <span class="sm-code">{{ $department->code }}</span>
                        @else
                            <span class="sm-muted">N/A</span>
                        @endif
                    </td>
                    <td class="sm-desc">{{ $department->description ?? '—' }}</td>
                    <td class="sm-num">{{ $department->employees_count }} employees</td>
                    <td>
                        <span class="badge {{ $department->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="sm-actions">
                            <button class="sm-btn" onclick='editDepartment(@json($department))'><svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg> Edit</button>
                            <form method="POST" action="{{ route('settings.departments.delete', $department) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="sm-btn sm-btn-danger"
                                        onclick="return confirm('Delete this department?')"><svg class="mv-i" aria-hidden="true"><use href="#i-trash"/></svg> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="sm-empty">
        <svg class="mv-i" aria-hidden="true"><use href="#i-building"/></svg>
        <div>No departments found. Create your first department!</div>
    </div>
    @endif
</div>

{{-- Modal --}}
<div id="deptModal" class="ui-modal">
    <div class="ui-modal-box" style="max-width:520px">
        <div class="ui-modal-header">
            <span class="ui-modal-title" id="modalTitle">Add Department</span>
            <button class="ui-modal-close" onclick="closeModal()" aria-label="Close">&times;</button>
        </div>
        <form id="deptForm" method="POST" action="{{ route('settings.departments.store') }}">
            @csrf
            <input type="hidden" id="dept_method" name="_method" value="POST">
            <input type="hidden" id="dept_id" name="dept_id">
            <div class="ui-modal-body" style="display:grid;gap:14px">
                <div>
                    <label class="ui-label" for="dept_name">Department Name <span class="sm-req">*</span></label>
                    <input type="text" name="name" id="dept_name" required class="ui-input">
                </div>
                <div>
                    <label class="ui-label" for="dept_code">Department Code</label>
                    <input type="text" name="code" id="dept_code" placeholder="e.g. IT, HR, FIN" class="ui-input">
                </div>
                <div>
                    <label class="ui-label" for="dept_description">Description</label>
                    <textarea name="description" id="dept_description" rows="3" class="ui-input" style="resize:vertical;height:auto;padding:8px 11px;"></textarea>
                </div>
                <div id="statusField" style="display:none">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13.5px;color:var(--mv-ink);">
                        <input type="checkbox" name="is_active" id="dept_is_active" value="1" style="width:16px;height:16px;accent-color:var(--mv-accent);">
                        Active
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
.sm-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
.sm-card .ui-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.sm-card .ui-table th { text-align: left; white-space: nowrap; }
.sm-name { color: var(--mv-ink) !important; font-weight: 500; }
.sm-code { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 6px; }
.sm-desc { max-width: 360px; }
.sm-num { font-variant-numeric: tabular-nums; white-space: nowrap; }
.sm-muted { color: var(--mv-muted); }
.sm-req { color: var(--mv-crit); }
.sm-actions { display: flex; gap: 6px; justify-content: flex-end; }
.sm-btn { display: inline-flex; align-items: center; gap: 5px; height: 28px; padding: 0 10px; border-radius: 6px; border: 1px solid var(--mv-line-strong); background: var(--mv-surface); font: inherit; font-size: 12.5px; font-weight: 500; color: var(--mv-ink-2); cursor: pointer; }
.sm-btn .mv-i { width: 13px; height: 13px; }
.sm-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.sm-btn-danger { color: var(--mv-crit); border-color: #EBC3C3; }
.sm-btn-danger:hover { background: var(--mv-crit-soft); color: var(--mv-crit); }
.sm-empty { padding: 48px 16px; text-align: center; color: var(--mv-muted); font-size: 13.5px; }
.sm-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); margin-bottom: 8px; }
.ui-modal { display:none; position:fixed; inset:0; z-index:1100; background:rgba(22,32,44,.45); align-items:center; justify-content:center; padding:16px; }
.ui-modal.show { display:flex; }
.ui-modal-box { background:var(--mv-surface); border:1px solid var(--mv-line); border-radius:12px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 48px rgba(22,32,44,.18); }
.ui-modal-header { padding:14px 20px; border-bottom:1px solid var(--mv-line); display:flex; justify-content:space-between; align-items:center; }
.ui-modal-title { font-size:15px; font-weight:600; color:var(--mv-ink); }
.ui-modal-close { background:none; border:none; font-size:22px; color:var(--mv-muted); cursor:pointer; line-height:1; padding:0 2px; }
.ui-modal-close:hover { color:var(--mv-ink); }
.ui-modal-body { padding:18px 20px; }
.ui-modal-body .ui-input { width:100%; height:36px; padding:0 11px; font-size:13.5px; }
.ui-modal-footer { padding:12px 20px; border-top:1px solid var(--mv-line); display:flex; gap:8px; justify-content:flex-end; background:var(--mv-surface-2); border-radius:0 0 12px 12px; }
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
