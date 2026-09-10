{{-- Behaviour for the permission matrix. Expects $mode = 'create' | 'edit'. --}}
@php $isEdit = ($mode ?? 'create') === 'edit'; @endphp
<script>
const templates = {
    'super_admin': ['all'],
    'department_manager': [
        'view_dashboard', 'manage_team', 'manage_employees', 'view_employees',
        'manage_clients', 'view_clients', 'view_client_dashboards',
        'approve_requests', 'view_reports', 'use_report_builder', 'export_reports',
        'manage_visits', 'view_visits', 'assign_jobs', 'manage_jobs',
        'view_own_data'
    ],
    'team_lead': [
        'view_dashboard', 'manage_team', 'view_employees',
        'view_clients', 'approve_requests', 'view_reports',
        'assign_jobs', 'view_jobs', 'manage_visits', 'view_visits',
        'view_own_data'
    ],
    'field_technician': [
        'view_own_data', 'view_jobs', 'view_schedule', 'create_reports',
        'view_own_reports', 'view_terminals', 'view_visits',
        'view_tickets', 'view_clients'
    ],
    'office_staff': [
        'view_own_data', 'view_dashboard', 'view_clients', 'view_assets',
        'request_assets', 'view_own_requests', 'view_documents',
        'view_reports'
    ],
    'basic_employee': [
        'view_own_data', 'request_assets', 'view_own_requests', 'view_documents'
    ]
};

const ROLE_FORM_TEXT = {
    empty: @json($isEdit ? 'No permissions selected' : 'No permissions selected yet'),
    superAdminConfirm: @json($isEdit ? 'This will give this role FULL SYSTEM ACCESS (Super Admin). Continue?' : 'This will create a Super Administrator role with FULL SYSTEM ACCESS. Continue?'),
    saving: @json($isEdit ? 'Updating Role...' : 'Creating Role...'),
    confirmAllOnSubmit: {{ $isEdit ? 'true' : 'false' }}
};

// Group expand / collapse
function setCategoryOpen(content, toggle, open) {
    content.style.maxHeight = open ? 'none' : '0px';
    content.classList.toggle('expanded', open);
    if (toggle) toggle.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
}

function toggleCategory(category) {
    const content = document.getElementById(`category-${category}`);
    const toggle = document.getElementById(`toggle-${category}`);
    if (content) setCategoryOpen(content, toggle, content.style.maxHeight === '0px');
}

function expandAllCategories() {
    document.querySelectorAll('.category-content').forEach(content => {
        setCategoryOpen(content, document.getElementById(content.id.replace('category-', 'toggle-')), true);
    });
}

function collapseAllCategories() {
    document.querySelectorAll('.category-content').forEach(content => {
        setCategoryOpen(content, document.getElementById(content.id.replace('category-', 'toggle-')), false);
    });
}

// Permission selection
function selectAllInCategory(category) {
    document.querySelectorAll(`.permission-item[data-category="${category}"] input[name="permissions[]"]`).forEach(checkbox => {
        checkbox.checked = true;
        updatePermissionCard(checkbox);
    });
    updateCategoryInfo(category);
    updateSelectedPermissions();
    updateSummary();
}

function clearAllInCategory(category) {
    document.querySelectorAll(`.permission-item[data-category="${category}"] input`).forEach(checkbox => {
        checkbox.checked = false;
        updatePermissionCard(checkbox);
    });
    updateCategoryInfo(category);
    updateSelectedPermissions();
    updateSummary();
}

function togglePermission(permissionKey) {
    const checkbox = document.querySelector(`input[name="permissions[]"][value="${permissionKey}"]`);
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        updatePermissionCard(checkbox);
        updateCategoryInfo(checkbox.closest('.permission-item').dataset.category);
        updateSelectedPermissions();
        updateSummary();
    }
}

function updatePermissionCard(checkbox) {
    const card = checkbox.closest('.permission-item');
    if (!card) return;
    if (checkbox.checked) {
        card.classList.add('selected');
        if (checkbox.value === 'all') card.classList.add('danger');
    } else {
        card.classList.remove('selected', 'danger');
    }
}

function updateCategoryInfo(category) {
    const inputs = document.querySelectorAll(`.permission-item[data-category="${category}"] input`);
    const selectedCount = Array.from(inputs).filter(cb => cb.checked).length;
    const infoElement = document.getElementById(`selected-${category}`);
    if (infoElement) infoElement.textContent = selectedCount;
}

// Templates
function applyTemplate(templateName) {
    if (!templates[templateName]) return;
    if (templateName === 'super_admin' && !confirm(ROLE_FORM_TEXT.superAdminConfirm)) return;

    clearAll();
    templates[templateName].forEach(permission => {
        const checkbox = document.querySelector(`input[name="permissions[]"][value="${permission}"]`);
        if (checkbox) {
            checkbox.checked = true;
            updatePermissionCard(checkbox);
        }
    });
    updateAllCategories();
    updateSelectedPermissions();
    updateSummary();
}

function clearAll() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
        updatePermissionCard(checkbox);
    });
    updateAllCategories();
    updateSelectedPermissions();
    updateSummary();
}

function updateAllCategories() {
    document.querySelectorAll('.permission-category').forEach(categoryDiv => {
        updateCategoryInfo(categoryDiv.querySelector('.category-content').id.replace('category-', ''));
    });
}

// Sidebar
function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function updateSelectedPermissions() {
    const selectedDiv = document.getElementById('selected-permissions');
    const checkedBoxes = document.querySelectorAll('input[name="permissions[]"]:checked');

    if (checkedBoxes.length === 0) {
        selectedDiv.innerHTML = `<div class="rp-empty">${ROLE_FORM_TEXT.empty}</div>`;
        return;
    }

    let html = '<div class="rp-chips">';
    checkedBoxes.forEach(checkbox => {
        const nameElement = checkbox.closest('.permission-item').querySelector('.perm-name');
        const name = nameElement ? nameElement.textContent.trim() : checkbox.value;
        html += `<span class="rp-chip${checkbox.value === 'all' ? ' is-crit' : ''}">${escapeHtml(name)}</span>`;
    });
    selectedDiv.innerHTML = html + '</div>';
}

function updateSummary() {
    const checkedBoxes = Array.from(document.querySelectorAll('input[name="permissions[]"]:checked'));
    const totalCount = checkedBoxes.length;
    document.getElementById('total-count').textContent = totalCount;

    const accessLevel = document.getElementById('access-level');
    const values = checkedBoxes.map(cb => cb.value);
    const hasAll = values.includes('all');
    const hasAdminPerms = ['manage_employees', 'manage_roles', 'manage_settings'].some(p => values.includes(p));
    const hasManageTeam = values.includes('manage_team');

    let level = ['Safe', '#1D7F46'];
    if (hasAll) level = ['Super Admin', '#B83232'];
    else if (hasAdminPerms) level = ['Administrator', '#9A6412'];
    else if (hasManageTeam) level = ['Manager', '#1F4F87'];
    else if (totalCount > 5) level = ['Elevated', '#2B64A8'];
    accessLevel.textContent = level[0];
    accessLevel.style.color = level[1];

    const categories = new Set();
    checkedBoxes.forEach(cb => {
        const category = cb.closest('.permission-item').dataset.category;
        if (category) categories.add(category);
    });
    document.getElementById('category-count').textContent = categories.size;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updatePermissionCard(this);
            updateCategoryInfo(this.closest('.permission-item').dataset.category);
            updateSelectedPermissions();
            updateSummary();
        });
        if (checkbox.checked) updatePermissionCard(checkbox);
    });

    updateAllCategories();
    updateSelectedPermissions();
    updateSummary();

    const form = document.getElementById('roleForm');
    form.addEventListener('submit', function(e) {
        const roleName = form.querySelector('input[name="name"]').value;
        const checkedBoxes = form.querySelectorAll('input[name="permissions[]"]:checked');

        if (!roleName.trim()) {
            e.preventDefault();
            alert('Please enter a role name');
            return;
        }
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one permission');
            return;
        }
        if (ROLE_FORM_TEXT.confirmAllOnSubmit) {
            const hasAll = Array.from(checkedBoxes).some(cb => cb.value === 'all');
            if (hasAll && !confirm('You are updating this role to have FULL SYSTEM ACCESS. This will affect all employees with this role. Continue?')) {
                e.preventDefault();
                return;
            }
        }
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.textContent = ROLE_FORM_TEXT.saving;
        submitBtn.disabled = true;
    });
});
</script>
