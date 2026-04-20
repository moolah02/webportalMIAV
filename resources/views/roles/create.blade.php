{{--
==============================================
COMPLETE ROLE CREATE FORM
File: resources/views/roles/create.blade.php
==============================================
--}}
@extends('layouts.app')
@section('title', 'Create Role')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">Define a new role with specific permissions for your team</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn">← Back to Roles</a>
    </div>

    <form action="{{ route('roles.store') }}" method="POST" id="roleForm">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px;">
            <!-- Main Form -->
            <div>
                <!-- Basic Information -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333; display: flex; align-items: center; gap: 8px;">
                        📋 Role Information
                    </h4>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Role Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., field_technician, office_manager, sales_coordinator"
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">
                            💡 Use lowercase with underscores. Will display as "Field Technician"
                        </div>
                        @error('name')
                            <div style="color: #f44336; font-size: 12px; margin-top: 5px; padding: 8px; background: #ffebee; border-radius: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="content-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h4 style="margin: 0; color: #333; display: flex; align-items: center; gap: 8px;">
                            🔐 Permissions & Access Control
                        </h4>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" onclick="expandAllCategories()" class="btn btn-small">Expand All</button>
                            <button type="button" onclick="collapseAllCategories()" class="btn btn-small">Collapse All</button>
                        </div>
                    </div>

                    @php
                        $groupedPermissions = collect($allPermissions)->groupBy('category');
                        $categoryConfig = [
                            'admin' => ['name' => 'System Administration', 'icon' => '⚡', 'color' => '#f44336'],
                            'dashboard' => ['name' => 'Dashboard Access', 'icon' => '📊', 'color' => '#2196f3'],
                            'assets' => ['name' => 'Asset Management', 'icon' => '📦', 'color' => '#4caf50'],
                            'operations' => ['name' => 'Field Operations', 'icon' => '🔧', 'color' => '#ff9800'],
                            'clients' => ['name' => 'Client Management', 'icon' => '🏢', 'color' => '#9c27b0'],
                            'management' => ['name' => 'Employee Management', 'icon' => '👥', 'color' => '#607d8b'],
                            'technician' => ['name' => 'Technician Portal', 'icon' => '👨‍🔧', 'color' => '#00bcd4'],
                            'reports' => ['name' => 'Reports & Analytics', 'icon' => '📈', 'color' => '#795548'],
                            'special' => ['name' => 'Special Operations', 'icon' => '🎯', 'color' => '#e91e63']
                        ];
                    @endphp

                    @foreach($groupedPermissions as $category => $permissions)
                    @php
                        $config = $categoryConfig[$category] ?? ['name' => ucfirst($category), 'icon' => '📋', 'color' => '#666'];
                    @endphp
                    <div class="permission-category" style="margin-bottom: 20px; border: 2px solid #f0f0f0; border-radius: 12px; overflow: hidden; transition: all 0.3s ease;">
                        <!-- Category Header -->
                        <div class="category-header"
                             onclick="toggleCategory('{{ $category }}')"
                             style="background: linear-gradient(135deg, {{ $config['color'] }}15, {{ $config['color'] }}05);
                                    padding: 16px 20px;
                                    cursor: pointer;
                                    border-bottom: 1px solid #f0f0f0;
                                    transition: all 0.2s ease;
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-size: 24px;">{{ $config['icon'] }}</span>
                                <div>
                                    <h6 style="margin: 0; color: {{ $config['color'] }}; font-weight: 600; font-size: 16px;">
                                        {{ $config['name'] }}
                                    </h6>
                                    <div style="font-size: 12px; color: #666; margin-top: 2px;">
                                        {{ count($permissions) }} permission{{ count($permissions) > 1 ? 's' : '' }} available
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div class="category-selection-info" id="info-{{ $category }}" style="font-size: 12px; color: #666;">
                                    <span id="selected-{{ $category }}">0</span>/{{ count($permissions) }} selected
                                </div>
                                <div class="category-toggle-icon" id="toggle-{{ $category }}"
                                     style="font-size: 18px; color: {{ $config['color'] }}; transition: transform 0.3s ease;">
                                    ▼
                                </div>
                            </div>
                        </div>

                        <!-- Category Content -->
                        <div class="category-content" id="category-{{ $category }}"
                             style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <div style="padding: 20px;">
                                <!-- Category Actions -->
                                <div style="display: flex; gap: 10px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                                    <button type="button" onclick="selectAllInCategory('{{ $category }}')"
                                            class="btn btn-small btn-outline">Select All</button>
                                    <button type="button" onclick="clearAllInCategory('{{ $category }}')"
                                            class="btn btn-small btn-outline">Clear All</button>
                                </div>

                                <!-- Permissions Grid -->
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 12px;">
                                    @foreach($permissions as $key => $permission)
                                    <div class="permission-item"
                                         id="permission-{{ $key }}"
                                         data-category="{{ $category }}"
                                         onclick="togglePermission('{{ $key }}')"
                                         style="border: 1px solid #e0e0e0;
                                                border-radius: 8px;
                                                padding: 16px;
                                                cursor: pointer;
                                                transition: all 0.2s ease;
                                                background: #fff;">
                                        <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; margin: 0;">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $key }}"
                                                   {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}
                                                   onchange="updatePermissionCard(this)"
                                                   style="margin-top: 3px;">
                                            <div style="flex: 1;">
                                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                                    <span style="font-size: 16px;">{{ $permission['icon'] ?? '🔑' }}</span>
                                                    <div style="font-weight: 600; color: #333; font-size: 14px;">
                                                        {{ $permission['name'] }}
                                                    </div>
                                                    @if(isset($permission['danger']) && $permission['danger'])
                                                        <span style="background: #ffebee;
                                                                     color: #d32f2f;
                                                                     padding: 2px 6px;
                                                                     border-radius: 8px;
                                                                     font-size: 10px;
                                                                     font-weight: 600;">
                                                            DANGER
                                                        </span>
                                                    @endif
                                                </div>
                                                <div style="font-size: 12px; color: #666; line-height: 1.4;">
                                                    {{ $permission['description'] }}
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Selected Permissions Preview -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333; display: flex; align-items: center; gap: 8px;">
                        📋 Selected Permissions
                    </h4>

                    <div id="selected-permissions" style="min-height: 120px; max-height: 300px; overflow-y: auto;">
                        <div style="color: #666; font-style: italic; text-align: center; padding: 40px 20px;">
                            No permissions selected yet
                        </div>
                    </div>
                </div>

                <!-- Role Templates -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333; display: flex; align-items: center; gap: 8px;">
                        🎨 Quick Templates
                    </h4>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <button type="button" onclick="applyTemplate('super_admin')" class="template-btn danger">
                            ⚡ Super Administrator
                        </button>
                        <button type="button" onclick="applyTemplate('department_manager')" class="template-btn">
                            👑 Department Manager
                        </button>
                        <button type="button" onclick="applyTemplate('team_lead')" class="template-btn">
                            👥 Team Lead
                        </button>
                        <button type="button" onclick="applyTemplate('field_technician')" class="template-btn">
                            🔧 Field Technician
                        </button>
                        <button type="button" onclick="applyTemplate('office_staff')" class="template-btn">
                            🏢 Office Staff
                        </button>
                        <button type="button" onclick="applyTemplate('basic_employee')" class="template-btn">
                            👤 Basic Employee
                        </button>
                        <hr style="margin: 10px 0; border: none; border-top: 1px solid #e0e0e0;">
                        <button type="button" onclick="clearAll()" class="template-btn clear">
                            🗑️ Clear All
                        </button>
                    </div>
                </div>

                <!-- Permission Summary -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333; display: flex; align-items: center; gap: 8px;">
                        📊 Role Summary
                    </h4>

                    <div class="summary-item">
                        <span>Total Permissions:</span>
                        <span id="total-count" style="font-weight: bold; color: #2196f3;">0</span>
                    </div>

                    <div class="summary-item">
                        <span>Access Level:</span>
                        <span id="access-level" style="font-weight: bold; color: #4caf50;">Safe</span>
                    </div>

                    <div class="summary-item">
                        <span>Categories:</span>
                        <span id="category-count" style="font-weight: bold; color: #ff9800;">0</span>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="content-card">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 16px;">
                            🎉 Create Role
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn" style="width: 100%; text-align: center; padding: 12px;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* Base Styles */
.content-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
}

/* Button Styles */
.btn {
    padding: 10px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
    transform: translateY(-1px);
}

.btn-primary {
    background: #2196f3;
    color: white;
    border-color: #2196f3;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}

.btn-small {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-outline {
    background: transparent;
    border: 1px solid #e0e0e0;
}

/* Template Button Styles */
.template-btn {
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background: #f8f9fa;
    color: #333;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-align: left;
}

.template-btn:hover {
    background: #e9ecef;
    border-color: #2196f3;
    transform: translateY(-1px);
}

.template-btn.danger {
    background: #fff5f5;
    border-color: #fed7d7;
    color: #d32f2f;
}

.template-btn.danger:hover {
    background: #fed7d7;
    border-color: #f44336;
}

.template-btn.clear {
    background: #ffebee;
    border-color: #ffcdd2;
    color: #d32f2f;
}

.template-btn.clear:hover {
    background: #ffcdd2;
    border-color: #f44336;
}

/* Permission Card Styles */
.permission-item {
    transition: all 0.2s ease;
}

.permission-item:hover {
    border-color: #2196f3 !important;
    background: #f8f9ff !important;
    transform: translateY(-1px);
}

.permission-item.selected {
    border-color: #2196f3 !important;
    background: #e3f2fd !important;
}

.permission-item.danger {
    border-color: #f44336 !important;
    background: #ffebee !important;
}

/* Category Styles */
.category-header:hover {
    background: linear-gradient(135deg, rgba(33, 150, 243, 0.1), rgba(33, 150, 243, 0.05)) !important;
}

.category-content.expanded {
    max-height: 1000px !important;
}

/* Summary Styles */
.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.summary-item:last-child {
    border-bottom: none;
}

/* Form Styles */
input[type="text"]:focus {
    border-color: #2196f3 !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .content-card {
        padding: 16px;
    }

    div[style*="grid-template-columns: 1fr 350px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Permission templates with comprehensive coverage
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

// Category management
function toggleCategory(category) {
    const content = document.getElementById(`category-${category}`);
    const toggle = document.getElementById(`toggle-${category}`);

    if (content.style.maxHeight === '0px' || !content.style.maxHeight) {
        content.style.maxHeight = '1000px';
        content.classList.add('expanded');
        toggle.style.transform = 'rotate(180deg)';
    } else {
        content.style.maxHeight = '0px';
        content.classList.remove('expanded');
        toggle.style.transform = 'rotate(0deg)';
    }
}

function expandAllCategories() {
    document.querySelectorAll('.category-content').forEach(content => {
        content.style.maxHeight = '1000px';
        content.classList.add('expanded');
    });
    document.querySelectorAll('.category-toggle-icon').forEach(toggle => {
        toggle.style.transform = 'rotate(180deg)';
    });
}

function collapseAllCategories() {
    document.querySelectorAll('.category-content').forEach(content => {
        content.style.maxHeight = '0px';
        content.classList.remove('expanded');
    });
    document.querySelectorAll('.category-toggle-icon').forEach(toggle => {
        toggle.style.transform = 'rotate(0deg)';
    });
}

// Permission management
function selectAllInCategory(category) {
    document.querySelectorAll(`input[value][data-category="${category}"]`).forEach(checkbox => {
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
    const checkbox = document.querySelector(`input[value="${permissionKey}"]`);
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        updatePermissionCard(checkbox);

        const category = checkbox.closest('.permission-item').dataset.category;
        updateCategoryInfo(category);
        updateSelectedPermissions();
        updateSummary();
    }
}

function updatePermissionCard(checkbox) {
    const card = checkbox.closest('.permission-item');

    if (checkbox.checked) {
        card.classList.add('selected');
        if (checkbox.value === 'all') {
            card.classList.add('danger');
        }
    } else {
        card.classList.remove('selected', 'danger');
    }
}

function updateCategoryInfo(category) {
    const categoryPermissions = document.querySelectorAll(`.permission-item[data-category="${category}"] input`);
    const selectedCount = Array.from(categoryPermissions).filter(cb => cb.checked).length;
    const totalCount = categoryPermissions.length;

    const infoElement = document.getElementById(`selected-${category}`);
    if (infoElement) {
        infoElement.textContent = selectedCount;
    }
}

// Template application
function applyTemplate(templateName) {
    if (!templates[templateName]) return;

    // Show confirmation for super admin
    if (templateName === 'super_admin') {
        if (!confirm('This will create a Super Administrator role with FULL SYSTEM ACCESS. Continue?')) {
            return;
        }
    }

    clearAll();

    templates[templateName].forEach(permission => {
        const checkbox = document.querySelector(`input[value="${permission}"]`);
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
        const category = categoryDiv.querySelector('.category-content').id.replace('category-', '');
        updateCategoryInfo(category);
    });
}

// UI Updates
function updateSelectedPermissions() {
    const selectedDiv = document.getElementById('selected-permissions');
    const checkedBoxes = document.querySelectorAll('input[name="permissions[]"]:checked');

    if (checkedBoxes.length === 0) {
        selectedDiv.innerHTML = `
            <div style="color: #666; font-style: italic; text-align: center; padding: 40px 20px;">
                No permissions selected yet
            </div>
        `;
        return;
    }

    let html = '<div style="display: flex; flex-wrap: wrap; gap: 6px;">';
    checkedBoxes.forEach(checkbox => {
        const permission = checkbox.value;
        const card = checkbox.closest('.permission-item');
        const nameElement = card.querySelector('div[style*="font-weight: 600"]');
        const name = nameElement ? nameElement.textContent.trim() : permission;

        const colors = {
            'all': {bg: '#ffebee', text: '#d32f2f'},
            'view_dashboard': {bg: '#e3f2fd', text: '#1976d2'},
            'manage_assets': {bg: '#e8f5e8', text: '#388e3c'},
            'view_clients': {bg: '#fff3e0', text: '#f57c00'},
            'manage_team': {bg: '#f3e5f5', text: '#7b1fa2'},
            'view_jobs': {bg: '#e0f2f1', text: '#00796b'}
        };

        const color = colors[permission] || {bg: '#f5f5f5', text: '#666'};

        html += `
            <span style="background: ${color.bg};
                         color: ${color.text};
                         padding: 4px 8px;
                         border-radius: 6px;
                         font-size: 11px;
                         font-weight: 500;">
                ${name}
            </span>
        `;
    });
    html += '</div>';

    selectedDiv.innerHTML = html;
}

function updateSummary() {
    const checkedBoxes = document.querySelectorAll('input[name="permissions[]"]:checked');
    const totalCount = checkedBoxes.length;

    // Update total count
    document.getElementById('total-count').textContent = totalCount;

    // Update access level
    const accessLevel = document.getElementById('access-level');
    const hasAll = Array.from(checkedBoxes).some(cb => cb.value === 'all');
    const adminPermissions = ['manage_employees', 'manage_roles', 'manage_settings'];
    const hasAdminPerms = Array.from(checkedBoxes).some(cb => adminPermissions.includes(cb.value));
    const hasManageTeam = Array.from(checkedBoxes).some(cb => cb.value === 'manage_team');

    if (hasAll) {
        accessLevel.textContent = 'Super Admin';
        accessLevel.style.color = '#f44336';
    } else if (hasAdminPerms) {
        accessLevel.textContent = 'Administrator';
        accessLevel.style.color = '#ff9800';
    } else if (hasManageTeam) {
        accessLevel.textContent = 'Manager';
        accessLevel.style.color = '#9c27b0';
    } else if (totalCount > 5) {
        accessLevel.textContent = 'Elevated';
        accessLevel.style.color = '#2196f3';
    } else {
        accessLevel.textContent = 'Safe';
        accessLevel.style.color = '#4caf50';
    }

    // Update category count
    const categories = new Set();
    checkedBoxes.forEach(checkbox => {
        const category = checkbox.closest('.permission-item').dataset.category;
        if (category) categories.add(category);
    });

    document.getElementById('category-count').textContent = categories.size;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all checkboxes
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updatePermissionCard(this);
            const category = this.closest('.permission-item').dataset.category;
            updateCategoryInfo(category);
            updateSelectedPermissions();
            updateSummary();
        });

        // Initialize card state for pre-checked items
        if (checkbox.checked) {
            updatePermissionCard(checkbox);
        }
    });

    // Initialize category info
    updateAllCategories();
    updateSelectedPermissions();
    updateSummary();

    // Form validation
    document.getElementById('roleForm').addEventListener('submit', function(e) {
        const roleName = document.querySelector('input[name="name"]').value;
        const checkedBoxes = document.querySelectorAll('input[name="permissions[]"]:checked');

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

        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '⏳ Creating Role...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection
